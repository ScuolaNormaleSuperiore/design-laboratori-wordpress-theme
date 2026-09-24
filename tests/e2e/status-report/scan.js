#!/usr/bin/env node

/**
 * DLI Site Status Scanner
 *
 * Usage:
 *   node scan.js <baseUrl> [options]
 *
 * Options:
 *   --sitemap <path>      Path to sitemap page (default: /mappa-sito/)
 *   --timeout <ms>        Page timeout in ms (default: 15000)
 *   --concurrency <n>     Max parallel tabs (default: 3)
 *   --delay <ms>          Wait between page requests in ms (default: 300, 0 = no delay)
 *   --out <path>          Output file path without extension (default: ./report)
 *
 * Example:
 *   node scan.js https://laboratorio1.local/
 *   node scan.js https://laboratorio1.local/ --sitemap /mappa-sito/ --concurrency 2
 */

'use strict';

const { chromium } = require('playwright');
const { generateReport } = require('./report');

// ---------------------------------------------------------------------------
// CLI argument parsing
// ---------------------------------------------------------------------------

function buildTimestamp(date) {
  const pad = (n) => String(n).padStart(2, '0');
  return (
    date.getFullYear() +
    pad(date.getMonth() + 1) +
    pad(date.getDate()) +
    '_' +
    pad(date.getHours()) +
    pad(date.getMinutes())
  );
}

function parseArgs(argv) {
  const args = argv.slice(2);
  const opts = {
    baseUrl: null,
    sitemap: null,
    sitemapExplicit: false,
    timeout: 15000,
    concurrency: 3,
    delay: 1000, // ms to wait before starting each page request; 0 = no delay
    out: null, // resolved in main after timestamp is built
    outExplicit: false,
  };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg.startsWith('http')) {
      opts.baseUrl = arg.replace(/\/$/, '');
    } else if (arg === '--sitemap' && args[i + 1]) {
      opts.sitemap = args[++i];
      opts.sitemapExplicit = true;
    } else if (arg === '--timeout' && args[i + 1]) {
      opts.timeout = parseInt(args[++i], 10);
    } else if (arg === '--concurrency' && args[i + 1]) {
      opts.concurrency = parseInt(args[++i], 10);
    } else if (arg === '--delay' && args[i + 1]) {
      opts.delay = parseInt(args[++i], 10);
    } else if (arg === '--out' && args[i + 1]) {
      opts.out = args[++i];
      opts.outExplicit = true;
    }
  }

  return opts;
}

// ---------------------------------------------------------------------------
// URL extraction from sitemap page
// ---------------------------------------------------------------------------

const DEFAULT_SITEMAPS = [
  '/mappa-sito/',
  '/en/site-map/',
];

function normalizeUrl(value) {
  const url = new URL(value);
  url.hash = '';
  url.search = '';
  return url.href.replace(/\/$/, '');
}

async function extractUrls(page, baseUrl, sitemapPath, timeout) {
  const sitemapUrl = new URL(sitemapPath, baseUrl).href;
  console.log(`\nReading sitemap: ${sitemapUrl}`);

  let response;
  try {
    response = await page.goto(sitemapUrl, { waitUntil: 'load', timeout });
  } catch (err) {
    console.log(`Skipping unavailable sitemap: ${err.message}`);
    return null;
  }

  const renderedUrl = page.url();
  const renderedOrigin = new URL(renderedUrl).origin;
  const hasSitemap = await page.locator('#dli-sitemap').count() > 0;

  if (!response || response.status() !== 200 || !hasSitemap) {
    const status = response ? response.status() : 'no response';
    console.log(`Skipping unavailable sitemap: HTTP ${status}, selector #dli-sitemap not found.`);
    return null;
  }

  const urls = await page.evaluate((origin) => {
    const anchors = Array.from(document.querySelectorAll('#dli-sitemap a[href]'));
    const found = new Set();
    anchors.forEach((a) => {
      const href = a.getAttribute('href');
      if (!href) return;
      let url;
      try {
        url = new URL(href, origin);
      } catch {
        return;
      }
      // Keep only internal URLs, strip hash and query
      if (url.origin === origin) {
        url.hash = '';
        url.search = '';
        found.add(url.href);
      }
    });
    return Array.from(found);
  }, renderedOrigin);

  return {
    sitemapUrl: normalizeUrl(renderedUrl),
    urls: Array.from(new Set([
      normalizeUrl(renderedOrigin),
      ...urls.map(normalizeUrl),
    ])).sort(),
  };
}

// ---------------------------------------------------------------------------
// PHP error detection in page source
// ---------------------------------------------------------------------------

const PHP_PATTERNS = [
  /Fatal error:/i,
  /Warning:/i,
  /Notice:/i,
  /Deprecated:/i,
  /Parse error:/i,
];

function extractPhpErrors(html) {
  const errors = [];
  const lines = html.split('\n');
  lines.forEach((line) => {
    PHP_PATTERNS.forEach((pattern) => {
      if (pattern.test(line)) {
        errors.push(line.trim().substring(0, 300));
      }
    });
  });
  // Deduplicate
  return Array.from(new Set(errors));
}

// ---------------------------------------------------------------------------
// Single page scan
// ---------------------------------------------------------------------------

async function scanPage(browser, url, timeout) {
  const result = {
    url,
    status: null,
    ttfbMs: null,       // Time To First Byte — server-only, not affected by concurrency
    responseTimeMs: null, // Total load time — includes all resources, affected by concurrency
    errors: [],
    timedOut: false,
  };

  let page;
  try {
    page = await browser.newPage();
  } catch (err) {
    result.errors.push({ type: 'INTERNAL', message: `Cannot open tab: ${err.message}` });
    return result;
  }

  const resourceErrors = [];
  const jsErrors = [];

  // Intercept failed resource loads
  page.on('response', (response) => {
    const status = response.status();
    const url = response.url();
    const type = response.request().resourceType();
    if (
      status >= 400 &&
      ['stylesheet', 'script', 'image', 'font', 'media'].includes(type)
    ) {
      resourceErrors.push({ status, url });
    }
  });

  // Capture JS console errors — skip transient network-level noise
  const IGNORE_CONSOLE_PATTERNS = [
    'ERR_NETWORK_CHANGED',
    'ERR_INTERNET_DISCONNECTED',
    'ERR_NAME_NOT_RESOLVED',
  ];
  page.on('console', (msg) => {
    if (msg.type() === 'error') {
      const text = msg.text();
      if (IGNORE_CONSOLE_PATTERNS.some((p) => text.includes(p))) return;
      jsErrors.push(text.substring(0, 300));
    }
  });

  // Capture uncaught JS exceptions
  page.on('pageerror', (err) => {
    jsErrors.push(err.message.substring(0, 300));
  });

  try {
    const t0 = Date.now();
    const response = await page.goto(url, {
      waitUntil: 'load',
      timeout,
    });
    result.responseTimeMs = Date.now() - t0;

    result.status = response ? response.status() : null;

    // TTFB via Navigation Timing API — server-only, not affected by concurrency
    const ttfb = await page.evaluate(() => {
      const nav = performance.getEntriesByType('navigation')[0];
      return nav ? Math.round(nav.responseStart) : null;
    });
    result.ttfbMs = ttfb;

    if (result.status && result.status !== 200) {
      result.errors.push({ type: 'HTTP', message: `HTTP ${result.status}` });
    }

    // Check body content length — use textContent (layout-independent)
    const bodyText = await page.evaluate(() => document.body ? document.body.textContent : '');
    if (bodyText.trim().length < 200) {
      result.errors.push({ type: 'CONTENT', message: `Page body too short (${bodyText.trim().length} chars) — possibly empty or broken` });
    }

    // PHP errors in full page HTML
    const html = await page.content();
    const phpErrors = extractPhpErrors(html);
    phpErrors.forEach((msg) => result.errors.push({ type: 'PHP', message: msg }));

  } catch (err) {
    if (err.name === 'TimeoutError' || err.message.includes('Timeout')) {
      result.timedOut = true;
      result.errors.push({ type: 'TIMEOUT', message: `Page timed out after ${timeout}ms` });
    } else {
      result.errors.push({ type: 'NET', message: err.message.substring(0, 300) });
    }
  } finally {
    // Attach resource and JS errors collected via listeners
    resourceErrors.forEach(({ status, url }) => {
      result.errors.push({ type: 'NET', status, message: `${status} ${url}` });
    });
    const seenJs = new Set();
    jsErrors.forEach((msg) => {
      if (!seenJs.has(msg)) {
        seenJs.add(msg);
        result.errors.push({ type: 'JS', message: msg });
      }
    });

    await page.close().catch(() => {});
  }

  return result;
}

// ---------------------------------------------------------------------------
// Concurrency pool
// ---------------------------------------------------------------------------

async function runWithConcurrency(tasks, concurrency, delay) {
  const results = [];
  const queue = [...tasks];
  const active = new Set();

  return new Promise((resolve) => {
    function next() {
      while (active.size < concurrency && queue.length > 0) {
        const task = queue.shift();
        const run = delay > 0
          ? new Promise((res) => setTimeout(res, delay)).then(() => task())
          : task();
        const p = run.then((result) => {
          active.delete(p);
          results.push(result);
          printProgress(result);
          next();
          if (active.size === 0 && queue.length === 0) resolve(results);
        });
        active.add(p);
      }
    }
    next();
    if (active.size === 0) resolve(results);
  });
}

function printProgress(result) {
  const icon = result.errors.length === 0 ? '✓' : '✗';
  const errCount = result.errors.length > 0 ? ` (${result.errors.length} error${result.errors.length > 1 ? 's' : ''})` : '';
  const status = result.timedOut ? ' [TIMEOUT]' : result.status ? ` [${result.status}]` : '';
  console.log(`  ${icon}  ${result.url}${status}${errCount}`);
  result.errors.forEach((e) => {
    console.log(`       [${e.type}] ${e.message}`);
  });
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

async function main() {
  const opts = parseArgs(process.argv);

  if (!opts.baseUrl) {
    console.error('Usage: node scan.js <baseUrl> [--sitemap /path/] [--timeout ms] [--concurrency n] [--out ./report]');
    process.exit(1);
  }

  // Build timestamped default output path unless --out was explicitly provided
  if (!opts.outExplicit) {
    const ts = buildTimestamp(new Date());
    opts.out = `./tests/e2e/status-report/reports/status_report_${ts}`;
  }

  console.log('='.repeat(60));
  console.log('DLI Site Status Scanner');
  console.log('='.repeat(60));
  console.log(`Base URL    : ${opts.baseUrl}`);
  console.log(`Sitemaps    : ${opts.sitemapExplicit ? opts.sitemap : DEFAULT_SITEMAPS.join(', ')}`);
  console.log(`Timeout     : ${opts.timeout}ms`);
  console.log(`Concurrency : ${opts.concurrency}`);
  console.log(`Delay       : ${opts.delay > 0 ? opts.delay + 'ms' : 'none (--delay 0)'}`);
  console.log(`Output      : ${opts.out}.html / ${opts.out}.json`);
  console.log('='.repeat(60));

  const browser = await chromium.launch({ headless: true });

  // Step 1: extract URLs from the available language sitemaps.
  const sitemapPaths = opts.sitemapExplicit ? [opts.sitemap] : DEFAULT_SITEMAPS;
  const urls = new Set();
  const readSitemaps = new Set();

  for (const sitemapPath of sitemapPaths) {
    const sitemapPage = await browser.newPage();
    const extracted = await extractUrls(sitemapPage, opts.baseUrl, sitemapPath, opts.timeout);
    await sitemapPage.close();

    if (!extracted || readSitemaps.has(extracted.sitemapUrl)) {
      continue;
    }

    readSitemaps.add(extracted.sitemapUrl);
    urls.add(extracted.sitemapUrl);
    extracted.urls.forEach((url) => urls.add(url));
  }

  const scanUrls = Array.from(urls).sort();
  console.log(`\nRead ${readSitemaps.size} sitemap(s); found ${scanUrls.length} unique internal URLs.\n`);

  // Step 2: scan all pages
  console.log(`Scanning ${scanUrls.length} pages (concurrency: ${opts.concurrency})...\n`);
  const tasks = scanUrls.map((url) => () => scanPage(browser, url, opts.timeout));
  const results = await runWithConcurrency(tasks, opts.concurrency, opts.delay);

  await browser.close();

  // Step 3: generate reports
  await generateReport(results, opts);
}

main().catch((err) => {
  console.error('Fatal error:', err);
  process.exit(1);
});
