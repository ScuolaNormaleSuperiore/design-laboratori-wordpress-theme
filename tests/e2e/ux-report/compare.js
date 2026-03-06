#!/usr/bin/env node

/**
 * DLI UX Report Comparator
 *
 * Compares two ux_report JSON files and shows metric deltas.
 * If no files are specified, picks the two most recent in reports/.
 *
 * Usage:
 *   node compare.js [<new.json> <old.json>] [--out <file.html>]
 *
 * Example:
 *   node compare.js
 *   node compare.js reports/ux_report_20260310_1400.json reports/ux_report_20260309_0900.json
 */

'use strict';

const fs = require('fs');
const path = require('path');

const REPORTS_DIR = path.join(__dirname, 'reports');

// ---------------------------------------------------------------------------
// CLI
// ---------------------------------------------------------------------------

function parseArgs(argv) {
  const args = argv.slice(2);
  const opts = { fileNew: null, fileOld: null, out: null };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg === '--out' && args[i + 1]) {
      opts.out = args[++i];
    } else if (!arg.startsWith('--')) {
      if (!opts.fileNew) opts.fileNew = arg;
      else if (!opts.fileOld) opts.fileOld = arg;
    }
  }

  return opts;
}

function tsFromFilename(filename) {
  const m = path.basename(filename).match(/^ux_report_(\d{8}_\d{4})\.json$/);
  return m ? m[1] : null;
}

function findTwoMostRecentReports() {
  if (!fs.existsSync(REPORTS_DIR)) {
    throw new Error('reports/ directory not found');
  }

  const files = fs.readdirSync(REPORTS_DIR)
    .filter((f) => tsFromFilename(f) !== null)
    .sort((a, b) => tsFromFilename(b).localeCompare(tsFromFilename(a)));

  if (files.length < 2) {
    throw new Error(`Need at least 2 ux_report_*.json files in reports/, found ${files.length}`);
  }

  return [
    path.join(REPORTS_DIR, files[0]),
    path.join(REPORTS_DIR, files[1]),
  ];
}

function loadJson(filePath) {
  const abs = path.isAbsolute(filePath) ? filePath : path.resolve(filePath);
  if (!fs.existsSync(abs)) throw new Error(`File not found: ${abs}`);
  try {
    return JSON.parse(fs.readFileSync(abs, 'utf8'));
  } catch (e) {
    throw new Error(`Cannot parse ${abs}: ${e.message}`);
  }
}

// ---------------------------------------------------------------------------
// Comparison logic
// ---------------------------------------------------------------------------

// All metrics are "lower is better" except pagesScanned
const LOWER_IS_BETTER = new Set([
  'pagesWithAxeViolations',
  'pagesWithOverflow',
  'axeCriticalTotal',
  'axeSeriousTotal',
  'axeModerateTotal',
  'axeMinorTotal',
  'axeTotal',
]);

const METRIC_LABELS = {
  pagesScanned: 'Pages scanned',
  pagesWithAxeViolations: 'Pages with violations',
  pagesWithOverflow: 'Pages with overflow',
  axeCriticalTotal: 'Axe Critical',
  axeSeriousTotal: 'Axe Serious',
  axeModerateTotal: 'Axe Moderate',
  axeMinorTotal: 'Axe Minor',
  axeTotal: 'Axe Total',
};

function compareMetrics(newReport, oldReport) {
  const keys = Object.keys(METRIC_LABELS);
  return keys.map((key) => {
    const oldV = (oldReport.summary && oldReport.summary[key] != null) ? oldReport.summary[key] : null;
    const newV = (newReport.summary && newReport.summary[key] != null) ? newReport.summary[key] : null;
    const delta = (oldV !== null && newV !== null) ? newV - oldV : null;
    return { key, label: METRIC_LABELS[key], old: oldV, new: newV, delta };
  });
}

function buildSummary(metrics, newReport, oldReport) {
  const verdictOld = (oldReport.summary && oldReport.summary.verdict) || '?';
  const verdictNew = (newReport.summary && newReport.summary.verdict) || '?';

  let overallVerdict = 'UNCHANGED';
  const criticalDelta = metrics.find((m) => m.key === 'axeCriticalTotal');
  const totalDelta = metrics.find((m) => m.key === 'axeTotal');

  if (criticalDelta && criticalDelta.delta !== null) {
    if (criticalDelta.delta > 0) overallVerdict = 'DEGRADED';
    else if (criticalDelta.delta < 0) overallVerdict = 'IMPROVED';
  }
  if (overallVerdict === 'UNCHANGED' && totalDelta && totalDelta.delta !== null) {
    if (totalDelta.delta > 0) overallVerdict = 'DEGRADED';
    else if (totalDelta.delta < 0) overallVerdict = 'IMPROVED';
  }

  return { verdictOld, verdictNew, overallVerdict };
}

function comparePagesDetail(newReport, oldReport) {
  const mapNew = new Map((newReport.pages || []).map((p) => [p.url, p]));
  const mapOld = new Map((oldReport.pages || []).map((p) => [p.url, p]));
  const allUrls = Array.from(new Set([...mapNew.keys(), ...mapOld.keys()])).sort();

  return allUrls.map((url) => {
    const n = mapNew.get(url) || null;
    const o = mapOld.get(url) || null;

    const axeNew = n && n.axe ? n.axe.total : null;
    const axeOld = o && o.axe ? o.axe.total : null;
    const axeDelta = axeNew !== null && axeOld !== null ? axeNew - axeOld : null;

    const overflowNew = n ? n.overflowCount : null;
    const overflowOld = o ? o.overflowCount : null;

    let status = 'UNCHANGED';
    if (!o && n) status = 'NEW';
    else if (o && !n) status = 'REMOVED';
    else if (axeDelta !== null && axeDelta > 0) status = 'WORSE';
    else if (axeDelta !== null && axeDelta < 0) status = 'BETTER';

    return { url, status, axeOld, axeNew, axeDelta, overflowOld, overflowNew };
  });
}

// ---------------------------------------------------------------------------
// HTML output
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
  return v === 'IMPROVED' ? '#27ae60'
    : v === 'DEGRADED' ? '#c0392b'
    : '#6b7280';
}

function deltaColor(key, delta) {
  if (delta === null || delta === 0) return '#6b7280';
  if (!LOWER_IS_BETTER.has(key)) return '#6b7280';
  return delta > 0 ? '#c0392b' : '#27ae60';
}

function writeHtml(result, outFile) {
  const { summary, metrics, pages, newReport, oldReport, newFile, oldFile } = result;
  const color = verdictColor(summary.overallVerdict);

  const metricRows = metrics.map((m) => {
    const dc = deltaColor(m.key, m.delta);
    const deltaStr = m.delta !== null
      ? `<span style="color:${dc};font-weight:bold">${m.delta > 0 ? '+' : ''}${m.delta}</span>`
      : '—';
    return `<tr>
      <td>${escHtml(m.label)}</td>
      <td>${m.old !== null ? m.old : '—'}</td>
      <td>${m.new !== null ? m.new : '—'}</td>
      <td>${deltaStr}</td>
    </tr>`;
  }).join('');

  const pageRows = pages.map((p) => {
    const statusColor = p.status === 'WORSE' ? '#c0392b'
      : p.status === 'BETTER' ? '#27ae60'
      : p.status === 'NEW' || p.status === 'REMOVED' ? '#e67e22'
      : '#6b7280';

    const axeDeltaStr = p.axeDelta !== null
      ? `<span style="color:${p.axeDelta > 0 ? '#c0392b' : p.axeDelta < 0 ? '#27ae60' : '#6b7280'};font-weight:bold">${p.axeDelta > 0 ? '+' : ''}${p.axeDelta}</span>`
      : '—';

    const overflowOldStr = p.overflowOld !== null ? p.overflowOld : '—';
    const overflowNewStr = p.overflowNew !== null ? p.overflowNew : '—';

    return `<tr>
      <td style="word-break:break-all;font-size:12px">${escHtml(p.url)}</td>
      <td style="color:${statusColor};font-weight:bold">${p.status}</td>
      <td>${p.axeOld !== null ? p.axeOld : '—'} → ${p.axeNew !== null ? p.axeNew : '—'} ${axeDeltaStr}</td>
      <td>${overflowOldStr} → ${overflowNewStr}</td>
    </tr>`;
  }).join('');

  const verdictBadge = (v) => {
    const c = v === 'PASS' ? '#27ae60' : v === 'WARN' ? '#e67e22' : v === 'FAIL' ? '#c0392b' : '#6b7280';
    return `<span style="color:${c};font-weight:bold">${escHtml(v)}</span>`;
  };

  const html = `<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DLI UX Compare</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;margin:0;padding:24px;background:#f6f7f9;color:#1f2937}
h1,h2{margin:0 0 8px}
h2{font-size:16px;margin-top:24px;margin-bottom:8px}
.meta{color:#4b5563;font-size:14px;margin-bottom:18px}
.table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:18px}
.table th,.table td{border-bottom:1px solid #e5e7eb;padding:8px 10px;vertical-align:top;text-align:left;font-size:13px}
.table th{background:#111827;color:#fff;font-size:11px;text-transform:uppercase;letter-spacing:.04em}
</style>
</head>
<body>
  <h1>DLI UX Comparator</h1>
  <div class="meta">
    New: <strong>${escHtml(newFile)}</strong> (${new Date(newReport.scannedAt).toLocaleString('it-IT')}) — verdict: ${verdictBadge(summary.verdictNew)}
    &nbsp;|&nbsp;
    Old: <strong>${escHtml(oldFile)}</strong> (${new Date(oldReport.scannedAt).toLocaleString('it-IT')}) — verdict: ${verdictBadge(summary.verdictOld)}
    &nbsp;|&nbsp;
    Overall: <strong style="color:${color}">${summary.overallVerdict}</strong>
  </div>

  <h2>Summary metrics</h2>
  <table class="table">
    <thead><tr><th>Metric</th><th>Old</th><th>New</th><th>Delta</th></tr></thead>
    <tbody>${metricRows}</tbody>
  </table>

  <h2>Per-page details</h2>
  <table class="table">
    <thead>
      <tr>
        <th>URL</th>
        <th>Status</th>
        <th>Axe violations (old → new)</th>
        <th>Overflow viewports (old → new)</th>
      </tr>
    </thead>
    <tbody>${pageRows}</tbody>
  </table>
</body>
</html>`;

  fs.mkdirSync(path.dirname(outFile), { recursive: true });
  fs.writeFileSync(outFile, html, 'utf8');
}

function printConsole(result) {
  const sep = '='.repeat(60);
  console.log('\n' + sep);
  console.log('DLI UX Comparator');
  console.log(sep);
  console.log(`New: ${result.newFile}`);
  console.log(`Old: ${result.oldFile}`);
  console.log(sep);
  result.metrics.forEach((m) => {
    const delta = m.delta !== null ? ` (${m.delta > 0 ? '+' : ''}${m.delta})` : '';
    console.log(`${m.label.padEnd(30)}: ${m.old !== null ? m.old : '?'} → ${m.new !== null ? m.new : '?'}${delta}`);
  });
  console.log(`${'Overall verdict'.padEnd(30)}: ${result.summary.overallVerdict}`);
  console.log(sep + '\n');
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

function main() {
  const opts = parseArgs(process.argv);

  let fileNew = opts.fileNew;
  let fileOld = opts.fileOld;
  if (!fileNew || !fileOld) {
    [fileNew, fileOld] = findTwoMostRecentReports();
  }

  const newReport = loadJson(fileNew);
  const oldReport = loadJson(fileOld);

  const metrics = compareMetrics(newReport, oldReport);
  const summary = buildSummary(metrics, newReport, oldReport);
  const pages = comparePagesDetail(newReport, oldReport);

  const tsNew = tsFromFilename(path.basename(fileNew)) || String(Date.now());
  const tsOld = tsFromFilename(path.basename(fileOld)) || String(Date.now());
  const outFile = opts.out
    ? (opts.out.endsWith('.html') ? opts.out : `${opts.out}.html`)
    : path.join(REPORTS_DIR, `ux_compare_${tsNew}_vs_${tsOld}.html`);

  const result = {
    newFile: path.basename(fileNew),
    oldFile: path.basename(fileOld),
    newReport,
    oldReport,
    metrics,
    summary,
    pages,
  };

  writeHtml(result, outFile);
  printConsole(result);
  console.log(`HTML report: ${outFile}`);
}

try {
  main();
} catch (e) {
  console.error(`ERROR: ${e.message}`);
  process.exit(1);
}
