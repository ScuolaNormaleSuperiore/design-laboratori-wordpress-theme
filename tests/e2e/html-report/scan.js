#!/usr/bin/env node

/**
 * DLI HTML Validation Scanner
 *
 * Scans all site pages and validates their rendered HTML against the
 * Nu Html Checker (VNU). Requires Java 8+ and the vnu-jar npm package.
 *
 * Usage:
 *   node scan.js <baseUrl> [options]
 *
 * Options:
 *   --sitemap <path>   Path to sitemap page (default: /mappa-sito/)
 *   --timeout <ms>     Page timeout in ms (default: 30000)
 *   --delay <ms>       Wait between pages in ms (default: 500; 0 = no delay)
 *   --out <path>       Output path without extension (default: ./reports/html_report_<ts>)
 *   --gate             Exit with code 1 if verdict is FAIL
 *
 * Prerequisites:
 *   npm install --save-dev vnu-jar
 *   npx playwright install chromium
 *   Java 8+ must be installed and available on PATH
 *
 * Example:
 *   node scan.js https://laboratorio1.local
 *   node scan.js https://laboratorio1.local --gate
 */

'use strict';

const path = require('path');
const fs = require('fs');
const { spawnSync } = require('child_process');
const { chromium } = require('playwright');

// ---------------------------------------------------------------------------
// vnu-jar — loaded lazily so the script fails gracefully if not installed
// ---------------------------------------------------------------------------

function requireVnu() {
  try {
    return require('vnu-jar');
  } catch {
    console.error(
      'ERROR: vnu-jar is not installed.\n' +
      'Run: npm install --save-dev vnu-jar'
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
    delay: 1000,
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
// URL extraction from sitemap page (same logic as status-report and ux-report)
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
// HTML validation via VNU
// ---------------------------------------------------------------------------

function validateHtml(html, vnuJarPath) {
  const proc = spawnSync(
    'java',
    ['-jar', vnuJarPath, '--format', 'json', '--stdout', '-'],
    {
      input: html,
      encoding: 'utf8',
      maxBuffer: 10 * 1024 * 1024,
    }
  );

  if (proc.error) {
    return { errorCount: 0, warningCount: 0, errors: [], warnings: [], error: `java: ${proc.error.message}` };
  }

  const output = (proc.stdout || '').trim();

  // VNU outputs nothing (or empty messages array) when the page is clean
  if (!output) {
    return { errorCount: 0, warningCount: 0, errors: [], warnings: [], error: null };
  }

  let json;
  try {
    json = JSON.parse(output);
  } catch (e) {
    return { errorCount: 0, warningCount: 0, errors: [], warnings: [], error: `VNU parse error: ${e.message}` };
  }

  const messages = json.messages || [];

  const errors = messages
    .filter((m) => m.type === 'error')
    .map((m) => ({
      message: m.message,
      line: m.lastLine || null,
      col: m.lastColumn || null,
      extract: (m.extract || '').substring(0, 120),
    }));

  const warnings = messages
    .filter((m) => m.type === 'info' && m.subType === 'warning')
    .map((m) => ({
      message: m.message,
      line: m.lastLine || null,
      col: m.lastColumn || null,
      extract: (m.extract || '').substring(0, 120),
    }));

  return { errorCount: errors.length, warningCount: warnings.length, errors, warnings, error: null };
}

// ---------------------------------------------------------------------------
// Single page scan
// ---------------------------------------------------------------------------

async function scanPage(browser, url, timeout, vnuJarPath) {
  const result = {
    url,
    errorCount: 0,
    warningCount: 0,
    errors: [],
    warnings: [],
    vnuError: null,
    pageError: null,
  };

  let page;
  try {
    page = await browser.newPage();
    await page.goto(url, { waitUntil: 'load', timeout });

    const html = await page.content();
    const validation = validateHtml(html, vnuJarPath);

    result.errorCount = validation.errorCount;
    result.warningCount = validation.warningCount;
    result.errors = validation.errors;
    result.warnings = validation.warnings;
    result.vnuError = validation.error;

  } catch (err) {
    result.pageError = err.message.substring(0, 300);
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
    pagesWithErrors: 0,
    pagesWithWarnings: 0,
    totalErrors: 0,
    totalWarnings: 0,
    verdict: 'PASS',
  };

  results.forEach((r) => {
    if (r.errorCount > 0) summary.pagesWithErrors += 1;
    if (r.warningCount > 0) summary.pagesWithWarnings += 1;
    summary.totalErrors += r.errorCount;
    summary.totalWarnings += r.warningCount;
  });

  if (summary.totalErrors > 0) {
    summary.verdict = 'FAIL';
  } else if (summary.totalWarnings > 0) {
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
    const verdictPage = p.pageError ? 'ERROR' : p.errorCount > 0 ? 'FAIL' : p.warningCount > 0 ? 'WARN' : 'PASS';
    const vpColor = verdictColor(verdictPage === 'ERROR' ? 'FAIL' : verdictPage);

    const errorList = p.errors.length
      ? `<ul>${p.errors.map((e) =>
          `<li><span style="color:#c0392b">Error</span> [L${e.line || '?'}:C${e.col || '?'}] ${escHtml(e.message)}` +
          (e.extract ? `<br><code>${escHtml(e.extract)}</code>` : '') +
          '</li>'
        ).join('')}</ul>`
      : '';

    const warnList = p.warnings.length
      ? `<ul>${p.warnings.map((w) =>
          `<li><span style="color:#e67e22">Warning</span> [L${w.line || '?'}:C${w.col || '?'}] ${escHtml(w.message)}` +
          (w.extract ? `<br><code>${escHtml(w.extract)}</code>` : '') +
          '</li>'
        ).join('')}</ul>`
      : '';

    const details = errorList || warnList
      ? errorList + warnList
      : p.pageError
        ? `<span style="color:#6b7280">${escHtml(p.pageError)}</span>`
        : '—';

    return `<tr>
      <td style="word-break:break-all;font-size:12px">${escHtml(p.url)}</td>
      <td style="color:${vpColor};font-weight:bold">${verdictPage}</td>
      <td>${p.errorCount}</td>
      <td>${p.warningCount}</td>
      <td style="font-size:12px">${details}</td>
    </tr>`;
  }).join('');

  const html = `<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DLI HTML Validation Report</title>
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
  <h1>DLI HTML Validation Report</h1>
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
    <div class="card"><div class="num" style="color:${summary.pagesWithErrors > 0 ? '#c0392b' : '#27ae60'}">${summary.pagesWithErrors}</div><div>Pages with errors</div></div>
    <div class="card"><div class="num" style="color:${summary.pagesWithWarnings > 0 ? '#e67e22' : '#27ae60'}">${summary.pagesWithWarnings}</div><div>Pages with warnings</div></div>
    <div class="card"><div class="num" style="color:${summary.totalErrors > 0 ? '#c0392b' : '#27ae60'}">${summary.totalErrors}</div><div>Total errors</div></div>
    <div class="card"><div class="num" style="color:${summary.totalWarnings > 0 ? '#e67e22' : '#27ae60'}">${summary.totalWarnings}</div><div>Total warnings</div></div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>URL</th>
        <th>Verdict</th>
        <th>Errors</th>
        <th>Warnings</th>
        <th>Details</th>
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
  const verdictPage = result.pageError ? 'ERROR' : result.errorCount > 0 ? 'FAIL' : result.warningCount > 0 ? 'WARN' : 'PASS';
  const icon = verdictPage === 'PASS' ? '✓' : verdictPage === 'WARN' ? '!' : '✗';
  const counts = (result.errorCount > 0 || result.warningCount > 0)
    ? ` [errors:${result.errorCount} warnings:${result.warningCount}]`
    : '';
  console.log(`  ${icon}  [${index}/${total}] ${result.url}${counts}`);
  if (result.pageError) {
    console.log(`       [PAGE] ${result.pageError}`);
  }
  if (result.vnuError) {
    console.log(`       [VNU]  ${result.vnuError}`);
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
    opts.out = `./tests/e2e/html-report/reports/html_report_${ts}`;
  }

  const vnuJarPath = requireVnu();

  console.log('='.repeat(60));
  console.log('DLI HTML Validation Scanner');
  console.log('='.repeat(60));
  console.log(`Base URL : ${opts.baseUrl}`);
  console.log(`Sitemap  : ${opts.sitemap}`);
  console.log(`Timeout  : ${opts.timeout}ms`);
  console.log(`Delay    : ${opts.delay > 0 ? opts.delay + 'ms' : 'none (--delay 0)'}`);
  console.log(`Output   : ${opts.out}.html / ${opts.out}.json`);
  console.log(`Gate     : ${opts.gate}`);
  console.log(`VNU jar  : ${vnuJarPath}`);
  console.log('='.repeat(60));

  const browser = await chromium.launch({ headless: true });

  // Step 1: extract URLs from sitemap
  const sitemapPage = await browser.newPage();
  const urls = await extractUrls(sitemapPage, opts.baseUrl, opts.sitemap, opts.timeout);
  await sitemapPage.close();

  // Step 2: scan pages sequentially (VNU is CPU-intensive)
  console.log(`Scanning ${urls.length} pages (sequential)...\n`);
  const results = [];
  for (let i = 0; i < urls.length; i++) {
    if (opts.delay > 0 && i > 0) {
      await new Promise((res) => setTimeout(res, opts.delay));
    }
    const result = await scanPage(browser, urls[i], opts.timeout, vnuJarPath);
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
  console.log(`Pages with errors      : ${summary.pagesWithErrors}`);
  console.log(`Pages with warnings    : ${summary.pagesWithWarnings}`);
  console.log(`Total errors           : ${summary.totalErrors}`);
  console.log(`Total warnings         : ${summary.totalWarnings}`);
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
