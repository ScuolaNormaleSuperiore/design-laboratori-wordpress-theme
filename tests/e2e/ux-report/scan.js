#!/usr/bin/env node

/**
 * DLI UX Report Scanner
 *
 * Scans all site pages for accessibility violations (Axe/WCAG) and responsive
 * overflow issues. Pages are visited sequentially (no concurrency) because
 * Axe analysis is CPU-intensive.
 *
 * Usage:
 *   node scan.js <baseUrl> [options]
 *
 * Options:
 *   --sitemap <path>   Path to sitemap page (default: /mappa-sito/)
 *   --timeout <ms>     Page timeout in ms (default: 30000)
 *   --delay <ms>       Wait between pages in ms (default: 500; 0 = no delay)
 *   --out <path>       Output path without extension (default: ./reports/ux_report_<ts>)
 *   --gate             Exit with code 1 if verdict is FAIL
 *
 * Prerequisites:
 *   npm install --save-dev @axe-core/playwright
 *   npx playwright install chromium
 *
 * Example:
 *   node scan.js https://laboratorio1.local
 *   node scan.js https://laboratorio1.local --gate
 */

'use strict';

const path = require('path');
const fs = require('fs');
const { chromium } = require('playwright');

// ---------------------------------------------------------------------------
// Axe — loaded lazily so the script fails gracefully if not installed
// ---------------------------------------------------------------------------

function requireAxe() {
  try {
    return require('@axe-core/playwright').AxeBuilder;
  } catch {
    console.error(
      'ERROR: @axe-core/playwright is not installed.\n' +
      'Run: npm install --save-dev @axe-core/playwright'
    );
    process.exit(1);
  }
}

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
    sitemap: '/mappa-sito/',
    timeout: 30000,
    delay: 500,
    out: null,
    outExplicit: false,
    gate: false,
  };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg.startsWith('http')) {
      opts.baseUrl = arg.replace(/\/$/, '');
    } else if (arg === '--sitemap' && args[i + 1]) {
      opts.sitemap = args[++i];
    } else if (arg === '--timeout' && args[i + 1]) {
      opts.timeout = parseInt(args[++i], 10);
    } else if (arg === '--delay' && args[i + 1]) {
      opts.delay = parseInt(args[++i], 10);
    } else if (arg === '--out' && args[i + 1]) {
      opts.out = args[++i];
      opts.outExplicit = true;
    } else if (arg === '--gate') {
      opts.gate = true;
    }
  }

  return opts;
}

// ---------------------------------------------------------------------------
// URL extraction from sitemap page (same logic as status-report)
// ---------------------------------------------------------------------------

async function extractUrls(page, baseUrl, sitemapPath, timeout) {
  const sitemapUrl = baseUrl + sitemapPath;
  console.log(`\nReading sitemap: ${sitemapUrl}`);

  try {
    await page.goto(sitemapUrl, { waitUntil: 'load', timeout });
  } catch (err) {
    console.error(`ERROR: Cannot reach sitemap page: ${err.message}`);
    process.exit(1);
  }

  const urls = await page.evaluate((base) => {
    const anchors = Array.from(document.querySelectorAll('a[href]'));
    const found = new Set();
    anchors.forEach((a) => {
      const href = a.getAttribute('href');
      if (!href) return;
      let url;
      try {
        url = new URL(href, base);
      } catch {
        return;
      }
      if (url.origin === new URL(base).origin) {
        url.hash = '';
        url.search = '';
        found.add(url.href.replace(/\/$/, '') || '/');
      }
    });
    return Array.from(found);
  }, baseUrl);

  const homepage = baseUrl + '/';
  const unique = Array.from(new Set([homepage, ...urls])).sort();
  console.log(`Found ${unique.length} unique internal URLs.\n`);
  return unique;
}

// ---------------------------------------------------------------------------
// Accessibility check via Axe
// ---------------------------------------------------------------------------

async function checkAxe(page, AxeBuilder) {
  try {
    const results = await new AxeBuilder({ page }).analyze();
    const violations = results.violations || [];

    const counts = { critical: 0, serious: 0, moderate: 0, minor: 0, total: 0 };
    const details = [];

    violations.forEach((v) => {
      const impact = v.impact || 'minor';
      if (impact in counts) counts[impact] += 1;
      counts.total += 1;

      details.push({
        id: v.id,
        impact,
        description: v.description,
        nodes: (v.nodes || []).length,
        wcag: v.tags.filter((t) => t.startsWith('wcag') || t.startsWith('best-practice')),
      });
    });

    return { ...counts, violations: details, error: null };
  } catch (err) {
    return { critical: 0, serious: 0, moderate: 0, minor: 0, total: 0, violations: [], error: err.message };
  }
}

// ---------------------------------------------------------------------------
// Responsive overflow check across viewports
// ---------------------------------------------------------------------------

const VIEWPORTS = [
  { name: 'mobile', width: 375, height: 812 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'desktop', width: 1280, height: 800 },
];

async function checkResponsive(page) {
  const result = {};
  let overflowCount = 0;

  for (const vp of VIEWPORTS) {
    await page.setViewportSize({ width: vp.width, height: vp.height });
    const data = await page.evaluate((clientWidth) => {
      const overflow = document.documentElement.scrollWidth > document.documentElement.clientWidth;
      if (!overflow) return { overflow: false, elements: [] };

      const offenders = Array.from(document.querySelectorAll('*'))
        .filter((el) => {
          try {
            return el.getBoundingClientRect().right > clientWidth;
          } catch { return false; }
        })
        .slice(0, 10) // cap at 10 elements per viewport
        .map((el) => {
          const rect = el.getBoundingClientRect();
          const tag = el.tagName.toLowerCase();
          const id = el.id ? `#${el.id}` : '';
          const cls = el.className && typeof el.className === 'string'
            ? '.' + el.className.trim().split(/\s+/).slice(0, 3).join('.')
            : '';
          return {
            selector: `${tag}${id}${cls}`.substring(0, 80),
            right: Math.round(rect.right),
            width: Math.round(rect.width),
          };
        });

      return { overflow: true, elements: offenders };
    }, vp.width);

    result[vp.width] = data;
    if (data.overflow) overflowCount += 1;
  }

  return { viewports: result, overflowCount };
}

// ---------------------------------------------------------------------------
// Single page scan — expandable: add new check functions here
// ---------------------------------------------------------------------------

async function scanPage(browser, url, timeout, AxeBuilder) {
  const result = {
    url,
    axe: null,
    responsive: null,
    overflowCount: 0,
    error: null,
  };

  let page;
  try {
    page = await browser.newPage();
    // Use desktop viewport as default for Axe analysis
    await page.setViewportSize({ width: 1280, height: 800 });

    await page.goto(url, { waitUntil: 'load', timeout });

    // --- Accessibility (Axe) ---
    result.axe = await checkAxe(page, AxeBuilder);

    // --- Responsive overflow ---
    const responsive = await checkResponsive(page);
    result.responsive = responsive.viewports;
    result.overflowCount = responsive.overflowCount;

    // Future checks can be added here as additional async functions:
    // result.performance = await checkPerformance(page);
    // result.seo = await checkSeo(page);

  } catch (err) {
    result.error = err.message.substring(0, 300);
  } finally {
    if (page) await page.close().catch(() => {});
  }

  return result;
}

// ---------------------------------------------------------------------------
// Summary computation
// ---------------------------------------------------------------------------

function computeSummary(results) {
  const summary = {
    pagesScanned: results.length,
    pagesWithAxeViolations: 0,
    pagesWithOverflow: 0,
    axeCriticalTotal: 0,
    axeSeriousTotal: 0,
    axeModerateTotal: 0,
    axeMinorTotal: 0,
    axeTotal: 0,
    verdict: 'PASS',
  };

  results.forEach((r) => {
    if (r.axe) {
      if (r.axe.total > 0) summary.pagesWithAxeViolations += 1;
      summary.axeCriticalTotal += r.axe.critical;
      summary.axeSeriousTotal += r.axe.serious;
      summary.axeModerateTotal += r.axe.moderate;
      summary.axeMinorTotal += r.axe.minor;
      summary.axeTotal += r.axe.total;
    }
    if (r.overflowCount > 0) summary.pagesWithOverflow += 1;
  });

  if (summary.axeCriticalTotal > 0) {
    summary.verdict = 'FAIL';
  } else if (summary.axeSeriousTotal > 0 || summary.pagesWithOverflow > 0) {
    summary.verdict = 'WARN';
  }

  return summary;
}

// ---------------------------------------------------------------------------
// Git info
// ---------------------------------------------------------------------------

function getGitInfo() {
  try {
    const { execSync } = require('child_process');
    const branch = execSync('git rev-parse --abbrev-ref HEAD', { encoding: 'utf8' }).trim();
    const commit = execSync('git rev-parse --short HEAD', { encoding: 'utf8' }).trim();
    return { branch, commit };
  } catch {
    return { branch: null, commit: null };
  }
}

// ---------------------------------------------------------------------------
// Report output
// ---------------------------------------------------------------------------

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function verdictColor(v) {
  return v === 'PASS' ? '#27ae60' : v === 'WARN' ? '#e67e22' : '#c0392b';
}

function writeJson(data, filePath) {
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
  fs.writeFileSync(filePath + '.json', JSON.stringify(data, null, 2), 'utf8');
}

function writeHtml(data, filePath) {
  const { summary, pages, scannedAt, baseUrl, git } = data;
  const color = verdictColor(summary.verdict);

  const pageRows = pages.map((p) => {
    const axe = p.axe || {};
    const verdictPage = axe.critical > 0 ? 'FAIL' : axe.serious > 0 ? 'WARN' : 'PASS';
    const vpColor = (v) => v === 'PASS' ? '#27ae60' : v === 'WARN' ? '#e67e22' : '#c0392b';

    const overflowBadges = p.responsive
      ? VIEWPORTS.map((vp) => {
          const vpData = p.responsive[vp.width];
          const ov = vpData && vpData.overflow;
          const elements = (vpData && vpData.elements) || [];
          const elemList = elements.length
            ? `<ul style="margin:4px 0 0;padding-left:14px">${elements.map((e) =>
                `<li><code>${escHtml(e.selector)}</code> (right:${e.right}px, w:${e.width}px)</li>`
              ).join('')}</ul>`
            : '';
          return `<div style="margin-bottom:2px"><span style="color:${ov ? '#c0392b' : '#27ae60'};font-size:11px;font-weight:bold">${vp.name}:${ov ? 'OVF' : 'OK'}</span>${elemList}</div>`;
        }).join('')
      : '—';

    const violationList = (axe.violations || []).length
      ? `<ul>${(axe.violations || []).map((v) =>
          `<li><code>${escHtml(v.id)}</code> [${escHtml(v.impact)}] — ${escHtml(v.description)} (${v.nodes} node${v.nodes !== 1 ? 's' : ''})</li>`
        ).join('')}</ul>`
      : '—';

    return `<tr>
      <td style="word-break:break-all;font-size:12px">${escHtml(p.url)}</td>
      <td style="color:${vpColor(verdictPage)};font-weight:bold">${verdictPage}</td>
      <td>${axe.critical || 0}</td>
      <td>${axe.serious || 0}</td>
      <td>${axe.moderate || 0}</td>
      <td>${axe.minor || 0}</td>
      <td>${overflowBadges}</td>
      <td style="font-size:12px">${violationList}</td>
    </tr>`;
  }).join('');

  const html = `<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DLI UX Report</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;margin:0;padding:24px;background:#f6f7f9;color:#1f2937}
h1{margin:0 0 8px}
.meta{color:#4b5563;font-size:14px;margin-bottom:18px}
.cards{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.card{background:#fff;border-radius:8px;padding:12px 16px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.card .num{font-size:24px;font-weight:700}
.table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.table th,.table td{border-bottom:1px solid #e5e7eb;padding:8px 10px;vertical-align:top;text-align:left;font-size:13px}
.table th{background:#111827;color:#fff;font-size:11px;text-transform:uppercase;letter-spacing:.04em}
code{font-size:11px;background:#f3f4f6;padding:1px 3px;border-radius:3px}
ul{margin:4px 0;padding-left:16px}
</style>
</head>
<body>
  <h1>DLI UX Report</h1>
  <div class="meta">
    Scanned: <strong>${new Date(scannedAt).toLocaleString('it-IT')}</strong>
    &nbsp;|&nbsp;
    Base URL: <strong>${escHtml(baseUrl)}</strong>
    ${git && git.branch ? `&nbsp;|&nbsp; Branch: <strong>${escHtml(git.branch)}</strong> @ <code>${escHtml(git.commit)}</code>` : ''}
    &nbsp;|&nbsp;
    Verdict: <strong style="color:${color}">${summary.verdict}</strong>
  </div>

  <div class="cards">
    <div class="card"><div class="num">${summary.pagesScanned}</div><div>Pages scanned</div></div>
    <div class="card"><div class="num" style="color:${summary.pagesWithAxeViolations > 0 ? '#c0392b' : '#27ae60'}">${summary.pagesWithAxeViolations}</div><div>Pages with Axe violations</div></div>
    <div class="card"><div class="num" style="color:${summary.pagesWithOverflow > 0 ? '#e67e22' : '#27ae60'}">${summary.pagesWithOverflow}</div><div>Pages with overflow</div></div>
    <div class="card"><div class="num" style="color:${summary.axeCriticalTotal > 0 ? '#c0392b' : '#27ae60'}">${summary.axeCriticalTotal}</div><div>Critical violations</div></div>
    <div class="card"><div class="num" style="color:${summary.axeSeriousTotal > 0 ? '#e67e22' : '#27ae60'}">${summary.axeSeriousTotal}</div><div>Serious violations</div></div>
    <div class="card"><div class="num">${summary.axeModerateTotal}</div><div>Moderate violations</div></div>
    <div class="card"><div class="num">${summary.axeMinorTotal}</div><div>Minor violations</div></div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>URL</th>
        <th>Verdict</th>
        <th>Critical</th>
        <th>Serious</th>
        <th>Moderate</th>
        <th>Minor</th>
        <th>Responsive</th>
        <th>Axe violations</th>
      </tr>
    </thead>
    <tbody>${pageRows}</tbody>
  </table>
</body>
</html>`;

  fs.mkdirSync(path.dirname(filePath), { recursive: true });
  fs.writeFileSync(filePath + '.html', html, 'utf8');
}

// ---------------------------------------------------------------------------
// Progress output
// ---------------------------------------------------------------------------

function printProgress(result, index, total) {
  const axe = result.axe || {};
  const verdictPage = axe.critical > 0 ? 'FAIL' : axe.serious > 0 ? 'WARN' : result.error ? 'ERROR' : 'PASS';
  const icon = verdictPage === 'PASS' ? '✓' : verdictPage === 'WARN' ? '!' : '✗';
  const overflow = result.overflowCount > 0 ? ` [overflow:${result.overflowCount}vp]` : '';
  const axeInfo = axe.total > 0 ? ` [axe:${axe.total}]` : '';
  console.log(`  ${icon}  [${index}/${total}] ${result.url}${axeInfo}${overflow}`);
  if (result.error) {
    console.log(`       [ERROR] ${result.error}`);
  }
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

async function main() {
  const opts = parseArgs(process.argv);

  if (!opts.baseUrl) {
    console.error('Usage: node scan.js <baseUrl> [--sitemap /path/] [--timeout ms] [--delay ms] [--out ./path] [--gate]');
    process.exit(1);
  }

  if (!opts.outExplicit) {
    const ts = buildTimestamp(new Date());
    opts.out = `./tests/e2e/ux-report/reports/ux_report_${ts}`;
  }

  const AxeBuilder = requireAxe();

  console.log('='.repeat(60));
  console.log('DLI UX Report Scanner');
  console.log('='.repeat(60));
  console.log(`Base URL : ${opts.baseUrl}`);
  console.log(`Sitemap  : ${opts.sitemap}`);
  console.log(`Timeout  : ${opts.timeout}ms`);
  console.log(`Delay    : ${opts.delay > 0 ? opts.delay + 'ms' : 'none (--delay 0)'}`);
  console.log(`Output   : ${opts.out}.html / ${opts.out}.json`);
  console.log(`Gate     : ${opts.gate}`);
  console.log('='.repeat(60));

  const browser = await chromium.launch({ headless: true, args: ['--disable-web-security'] });

  // Step 1: extract URLs
  const sitemapPage = await browser.newPage();
  const urls = await extractUrls(sitemapPage, opts.baseUrl, opts.sitemap, opts.timeout);
  await sitemapPage.close();

  // Step 2: scan pages sequentially
  console.log(`Scanning ${urls.length} pages (sequential)...\n`);
  const results = [];
  for (let i = 0; i < urls.length; i++) {
    if (opts.delay > 0 && i > 0) {
      await new Promise((res) => setTimeout(res, opts.delay));
    }
    const result = await scanPage(browser, urls[i], opts.timeout, AxeBuilder);
    results.push(result);
    printProgress(result, i + 1, urls.length);
  }

  await browser.close();

  // Step 3: compute summary and write reports
  const summary = computeSummary(results);
  const git = getGitInfo();

  const data = {
    scannedAt: new Date().toISOString(),
    baseUrl: opts.baseUrl,
    git,
    summary,
    pages: results,
  };

  writeJson(data, opts.out);
  writeHtml(data, opts.out);

  console.log('\n' + '='.repeat(60));
  console.log(`Pages scanned          : ${summary.pagesScanned}`);
  console.log(`Pages with violations  : ${summary.pagesWithAxeViolations}`);
  console.log(`Pages with overflow    : ${summary.pagesWithOverflow}`);
  console.log(`Axe critical           : ${summary.axeCriticalTotal}`);
  console.log(`Axe serious            : ${summary.axeSeriousTotal}`);
  console.log(`Axe moderate           : ${summary.axeModerateTotal}`);
  console.log(`Axe minor              : ${summary.axeMinorTotal}`);
  console.log(`Verdict                : ${summary.verdict}`);
  console.log('='.repeat(60));
  console.log(`JSON : ${opts.out}.json`);
  console.log(`HTML : ${opts.out}.html`);

  if (opts.gate && summary.verdict === 'FAIL') {
    console.error('\nGate: FAIL — exiting with code 1');
    process.exit(1);
  }
}

main().catch((err) => {
  console.error('Fatal error:', err);
  process.exit(1);
});
