#!/usr/bin/env node

/**
 * DLI Accessibility Scanner (pa11y-ci)
 *
 * Checks the accessibility (WCAG 2 AA) of the site pages with pa11y-ci, using
 * the axe and HTML CodeSniffer runners. Every page is checked twice: on a
 * desktop viewport (1280 px) and on a narrow one (320 px, WCAG reflow).
 * The page list comes from the XML sitemap of the theme, with a fixed list as
 * fallback and an optional local file with extra URLs.
 *
 * Usage:
 *   node scan.js <baseUrl> [options]
 *
 * The base URL can also be given with the PA11Y_BASE_URL environment variable.
 *
 * Options:
 *   --gate              Count only WCAG errors and exit with code 2 if any page
 *                       has more errors than the threshold (default threshold: 0)
 *   --threshold <n>     Number of issues tolerated before failing (default: 0)
 *   --limit <n>         Check only the first n pages (default: all)
 *   --timeout <ms>      Page timeout in ms (default: 60000)
 *   --concurrency <n>   Pages checked in parallel (default: 2)
 *   --insecure          Accept invalid HTTPS certificates (local development only)
 *   --out <path>        Output path without extension
 *                       (default: ./reports/pa11y_report_<YYYYMMDD_HHMM>)
 *
 * Extra URLs: add them (paths or absolute URLs) to pa11yci.urls.local.json, a
 * JSON array ignored by Git.
 *
 * Prerequisites:
 *   npm install            (installs pa11y-ci and pa11y-ci-reporter-html)
 *   npm run status:scan:install-browser   (Chromium from Playwright, one time)
 *
 * Example:
 *   node scan.js https://laboratorio1.local
 *   node scan.js https://laboratorio1.local --gate --threshold 5
 */

'use strict';

const fs = require('fs');
const os = require('os');
const path = require('path');
const tls = require('tls');
const { spawn } = require('child_process');

const REPORTS_DIR = path.join(__dirname, 'reports');
const LOCAL_URLS_FILE = path.join(__dirname, 'pa11yci.urls.local.json');
const FALLBACK_PATHS = ['/', '/mappa-sito/', '/en/site-map/'];
const VIEWPORTS = [
  { label: 'desktop', width: 1280, height: 800 },
  { label: 'mobile', width: 320, height: 800 },
];

function usage() {
  console.error(
    'Usage: node scan.js <baseUrl> [--gate] [--threshold n] [--limit n] [--timeout ms] ' +
      '[--concurrency n] [--insecure] [--out ./report]'
  );
}

function parseArgs(argv) {
  const opts = {
    baseUrl: process.env.PA11Y_BASE_URL || '',
    gate: false,
    threshold: 0,
    limit: 0,
    timeout: 60000,
    concurrency: 2,
    insecure: false,
    out: '',
  };
  const numeric = (value, fallback) => {
    const n = parseInt(value, 10);
    return Number.isFinite(n) && n >= 0 ? n : fallback;
  };
  for (let i = 0; i < argv.length; i += 1) {
    const arg = argv[i];
    if (arg === '--gate') opts.gate = true;
    else if (arg === '--insecure') opts.insecure = true;
    else if (arg === '--threshold' && argv[i + 1]) opts.threshold = numeric(argv[(i += 1)], 0);
    else if (arg === '--limit' && argv[i + 1]) opts.limit = numeric(argv[(i += 1)], 0);
    else if (arg === '--timeout' && argv[i + 1]) opts.timeout = numeric(argv[(i += 1)], 60000);
    else if (arg === '--concurrency' && argv[i + 1]) opts.concurrency = Math.max(1, numeric(argv[(i += 1)], 2));
    else if (arg === '--out' && argv[i + 1]) opts.out = argv[(i += 1)];
    else if (arg === '--help' || arg === '-h') {
      usage();
      process.exit(0);
    } else if (!arg.startsWith('--') && !opts.baseUrl) opts.baseUrl = arg;
    else if (!arg.startsWith('--')) opts.baseUrl = arg;
  }
  opts.baseUrl = opts.baseUrl.replace(/\/+$/, '');
  return opts;
}

function timestamp() {
  const d = new Date();
  const p = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}${p(d.getMonth() + 1)}${p(d.getDate())}_${p(d.getHours())}${p(d.getMinutes())}`;
}

/**
 * Local development sites (e.g. Local) use a certificate trusted by the operating
 * system but unknown to Node.js: add the system certificates to the ones Node trusts,
 * so the sitemap can be read. Silently skipped on Node versions without the API.
 */
function trustSystemCertificates() {
  try {
    tls.setDefaultCACertificates([...tls.getCACertificates('default'), ...tls.getCACertificates('system')]);
  } catch {
    // Older Node.js: --insecure remains available.
  }
}

async function getText(url) {
  const response = await fetch(url, { redirect: 'follow' });
  if (!response.ok) {
    throw new Error(`HTTP ${response.status} for ${url}`);
  }
  return response.text();
}

function extractLocs(xml) {
  const locs = [];
  const pattern = /<loc>\s*([^<\s]+)\s*<\/loc>/gi;
  let match = pattern.exec(xml);
  while (match) {
    locs.push(match[1].replace(/&amp;/g, '&'));
    match = pattern.exec(xml);
  }
  return locs;
}

/** Reads the sitemap index (or a plain urlset) and returns the page URLs. */
async function readSitemap(baseUrl) {
  const entry = `${baseUrl}/sitemap.xml`;
  const xml = await getText(entry);
  if (!/<sitemapindex/i.test(xml)) {
    return extractLocs(xml);
  }
  const urls = [];
  for (const child of extractLocs(xml)) {
    urls.push(...extractLocs(await getText(child)));
  }
  return urls;
}

function readLocalUrls() {
  if (!fs.existsSync(LOCAL_URLS_FILE)) return [];
  try {
    const data = JSON.parse(fs.readFileSync(LOCAL_URLS_FILE, 'utf8'));
    return Array.isArray(data) ? data.filter((item) => typeof item === 'string') : [];
  } catch (error) {
    console.warn(`Warning: cannot read ${LOCAL_URLS_FILE}: ${error.message}`);
    return [];
  }
}

/** Keeps the path of every URL and moves it to the base URL host. */
function normalizeUrls(rawUrls, baseUrl) {
  const base = new URL(baseUrl);
  const seen = new Set();
  const result = [];
  for (const raw of rawUrls) {
    let parsed;
    try {
      parsed = new URL(raw, `${base.origin}/`);
    } catch {
      continue;
    }
    parsed.hash = '';
    const url = parsed.origin === base.origin ? parsed.href : `${base.origin}${parsed.pathname}${parsed.search}`;
    if (!seen.has(url)) {
      seen.add(url);
      result.push(url);
    }
  }
  return result;
}

async function collectPages(opts) {
  let fromSitemap = [];
  let source = 'sitemap.xml';
  try {
    fromSitemap = await readSitemap(opts.baseUrl);
  } catch (error) {
    const cause = error.cause && error.cause.code ? `, ${error.cause.code}` : '';
    console.warn(`Warning: sitemap not available (${error.message}${cause}); using the fixed list.`);
  }
  let pages = normalizeUrls([...fromSitemap, ...readLocalUrls()], opts.baseUrl);
  if (fromSitemap.length === 0) {
    source = 'fixed list';
    pages = normalizeUrls([...FALLBACK_PATHS, ...readLocalUrls()], opts.baseUrl);
  }
  if (opts.limit > 0) pages = pages.slice(0, opts.limit);
  return { pages, source };
}

function chromiumPath() {
  let executablePath = '';
  try {
    // eslint-disable-next-line global-require
    executablePath = require('playwright').chromium.executablePath();
  } catch {
    executablePath = '';
  }
  if (!executablePath || !fs.existsSync(executablePath)) {
    console.error('Chromium not found. Install it once with: npm run status:scan:install-browser');
    process.exit(1);
  }
  return executablePath;
}

function buildConfig(opts, pages, htmlDir, jsonFile) {
  const urls = [];
  for (const page of pages) {
    for (const viewport of VIEWPORTS) {
      // The fragment keeps desktop and mobile results apart in the report.
      urls.push({
        url: `${page}#${viewport.label}-${viewport.width}`,
        viewport: { width: viewport.width, height: viewport.height },
      });
    }
  }

  const reporters = opts.gate
    ? ['cli']
    : ['cli', ['json', { fileName: jsonFile }], ['pa11y-ci-reporter-html', { destination: htmlDir }]];

  return {
    defaults: {
      standard: 'WCAG2AA',
      runners: ['axe', 'htmlcs'],
      timeout: opts.timeout,
      wait: 500,
      includeNotices: !opts.gate,
      includeWarnings: !opts.gate,
      chromeLaunchConfig: {
        executablePath: chromiumPath(),
        acceptInsecureCerts: opts.insecure,
        args: [],
      },
      reporters,
    },
    concurrency: opts.concurrency,
    urls,
  };
}

function findFirstHtml(dir) {
  if (!fs.existsSync(dir)) return null;
  const files = fs.readdirSync(dir).filter((name) => name.endsWith('.html'));
  if (files.length === 0) return null;
  const index = files.find((name) => name.toLowerCase().startsWith('index')) || files[0];
  return path.join(dir, index);
}

function runPa11yCi(configFile, opts) {
  return new Promise((resolve) => {
    const bin = require.resolve('pa11y-ci/bin/pa11y-ci.js');
    const args = [bin, '--config', configFile];
    if (opts.threshold > 0) args.push('--threshold', String(opts.threshold));
    const child = spawn(process.execPath, args, { cwd: process.cwd(), stdio: 'inherit' });
    child.on('error', (error) => {
      console.error(`Cannot start pa11y-ci: ${error.message}`);
      resolve(1);
    });
    child.on('close', (code) => resolve(typeof code === 'number' ? code : 1));
  });
}

async function main() {
  const opts = parseArgs(process.argv.slice(2));
  if (!opts.baseUrl) {
    usage();
    process.exit(1);
  }
  trustSystemCertificates();
  if (opts.insecure) {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
  }

  const ts = timestamp();
  const outBase = opts.out ? path.resolve(opts.out) : path.join(REPORTS_DIR, `pa11y_report_${ts}`);
  const jsonFile = `${outBase}.json`;
  const htmlFinal = `${outBase}.html`;
  const workDir = fs.mkdtempSync(path.join(os.tmpdir(), 'dli-pa11y-'));
  const htmlDir = path.join(workDir, 'html');

  const { pages, source } = await collectPages(opts);
  if (pages.length === 0) {
    console.error('No page to check.');
    process.exit(1);
  }

  console.log('====================================================================');
  console.log('DLI Accessibility Scanner (pa11y-ci, WCAG2AA)');
  console.log('====================================================================');
  console.log(`Base URL : ${opts.baseUrl}`);
  console.log(`Pages    : ${pages.length} (from ${source}), ${VIEWPORTS.length} viewports each`);
  console.log(`Mode     : ${opts.gate ? `gate (errors only, threshold ${opts.threshold})` : 'report (errors, warnings and notices)'}`);
  console.log('====================================================================');

  const configFile = path.join(workDir, 'pa11yci.config.json');
  fs.writeFileSync(configFile, JSON.stringify(buildConfig(opts, pages, htmlDir, jsonFile), null, 2));

  if (!opts.gate) fs.mkdirSync(path.dirname(jsonFile), { recursive: true });
  const code = await runPa11yCi(configFile, opts);

  if (!opts.gate) {
    const html = findFirstHtml(htmlDir);
    if (html) {
      fs.copyFileSync(html, htmlFinal);
      console.log(`HTML report: ${path.relative(process.cwd(), htmlFinal)}`);
    }
    if (fs.existsSync(jsonFile)) {
      console.log(`JSON report: ${path.relative(process.cwd(), jsonFile)}`);
    }
  }
  fs.rmSync(workDir, { recursive: true, force: true });

  // pa11y-ci exits with 2 when the threshold is exceeded and with 1 on a runtime error.
  if (code === 1) process.exit(1);
  process.exit(opts.gate ? code : 0);
}

main().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
