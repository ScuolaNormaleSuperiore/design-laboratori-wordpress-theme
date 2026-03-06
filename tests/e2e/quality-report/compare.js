#!/usr/bin/env node
'use strict';

const fs = require('fs');
const path = require('path');

const REPORTS_DIR = path.join(__dirname, 'reports');

function parseArgs(argv) {
  const args = argv.slice(2);
  const opts = {
    fileNew: null,
    fileOld: null,
    out: null,
  };

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
  const m = path.basename(filename).match(/^quality_report_(\d{8}_\d{4})\.json$/);
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
    throw new Error(`Need at least 2 quality_report_*.json files in reports/, found ${files.length}`);
  }

  return [
    path.join(REPORTS_DIR, files[0]),
    path.join(REPORTS_DIR, files[1]),
  ];
}

function loadJson(filePath) {
  const abs = path.isAbsolute(filePath) ? filePath : path.resolve(filePath);
  if (!fs.existsSync(abs)) {
    throw new Error(`File not found: ${abs}`);
  }

  try {
    return JSON.parse(fs.readFileSync(abs, 'utf8'));
  } catch (e) {
    throw new Error(`Cannot parse ${abs}: ${e.message}`);
  }
}

function normalizeMetric(v) {
  return typeof v === 'number' ? v : null;
}

function compareChecks(newReport, oldReport) {
  const mapNew = new Map((newReport.checks || []).map((c) => [c.id, c]));
  const mapOld = new Map((oldReport.checks || []).map((c) => [c.id, c]));
  const ids = Array.from(new Set([...mapNew.keys(), ...mapOld.keys()])).sort();

  const deltas = ids.map((id) => {
    const n = mapNew.get(id) || null;
    const o = mapOld.get(id) || null;
    const statusNew = n ? n.status : 'MISSING';
    const statusOld = o ? o.status : 'MISSING';

    let change = 'UNCHANGED';
    if (!o && n) change = 'NEW_CHECK';
    else if (o && !n) change = 'REMOVED_CHECK';
    else if (statusOld !== statusNew) {
      if (statusOld !== 'FAIL' && statusNew === 'FAIL') change = 'REGRESSION';
      else if (statusOld === 'FAIL' && statusNew !== 'FAIL') change = 'FIXED';
      else change = 'CHANGED';
    }

    const metrics = [];
    const metricKeys = new Set([
      ...Object.keys((o && o.metrics) || {}),
      ...Object.keys((n && n.metrics) || {}),
    ]);

    for (const key of metricKeys) {
      const oldV = normalizeMetric(o && o.metrics ? o.metrics[key] : null);
      const newV = normalizeMetric(n && n.metrics ? n.metrics[key] : null);
      if (oldV === null || newV === null) continue;
      if (oldV !== newV) metrics.push({ key, old: oldV, new: newV, delta: newV - oldV });
    }

    return {
      id,
      label: (n && n.label) || (o && o.label) || id,
      severity: (n && n.severity) || (o && o.severity) || null,
      statusOld,
      statusNew,
      change,
      metrics,
    };
  });

  return deltas;
}

function buildSummary(deltas, newReport, oldReport) {
  const summary = {
    verdict: 'UNCHANGED',
    fixed: 0,
    regressions: 0,
    changed: 0,
    unchanged: 0,
    newChecks: 0,
    removedChecks: 0,
    failOld: (oldReport.summary && oldReport.summary.fail) || 0,
    failNew: (newReport.summary && newReport.summary.fail) || 0,
    warnOld: (oldReport.summary && oldReport.summary.warn) || 0,
    warnNew: (newReport.summary && newReport.summary.warn) || 0,
  };

  deltas.forEach((d) => {
    if (d.change === 'FIXED') summary.fixed += 1;
    else if (d.change === 'REGRESSION') summary.regressions += 1;
    else if (d.change === 'CHANGED') summary.changed += 1;
    else if (d.change === 'UNCHANGED') summary.unchanged += 1;
    else if (d.change === 'NEW_CHECK') summary.newChecks += 1;
    else if (d.change === 'REMOVED_CHECK') summary.removedChecks += 1;
  });

  if (summary.regressions > summary.fixed) summary.verdict = 'DEGRADED';
  else if (summary.fixed > summary.regressions) summary.verdict = 'IMPROVED';
  else if (summary.changed > 0) summary.verdict = 'MIXED';

  if (summary.failNew > summary.failOld) summary.verdict = 'DEGRADED';
  if (summary.failNew < summary.failOld && summary.regressions === 0) summary.verdict = 'IMPROVED';

  return summary;
}

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function writeHtml(result, outFile) {
  const color = result.summary.verdict === 'IMPROVED' ? '#27ae60'
    : result.summary.verdict === 'DEGRADED' ? '#c0392b'
      : result.summary.verdict === 'MIXED' ? '#e67e22'
        : '#6b7280';

  const LOWER_IS_BETTER = new Set([
    'errors', 'warnings', 'filesWithIssues', 'syntaxErrors',
    'advisories', 'critical', 'high', 'medium', 'low', 'total',
    'fixme', 'todo', 'filesWithMarkers', 'filesWithErrors',
    'abandoned', 'filesOver1000',
  ]);

  const statusBadge = (s) => {
    const c = s === 'PASS' ? '#27ae60' : s === 'WARN' ? '#e67e22' : s === 'FAIL' ? '#c0392b' : '#6b7280';
    return `<span style="color:${c};font-weight:bold">${escHtml(s)}</span>`;
  };

  const rows = result.deltas.map((d) => {
    const changeColor = d.change === 'REGRESSION' ? '#c0392b'
      : d.change === 'FIXED' ? '#27ae60'
        : d.change === 'CHANGED' ? '#e67e22'
          : '#6b7280';

    const statusCell = `${statusBadge(d.statusOld)} → ${statusBadge(d.statusNew)}`;

    const metrics = d.metrics.length
      ? `<ul>${d.metrics.map((m) => {
          const isLower = LOWER_IS_BETTER.has(m.key);
          const deltaColor = isLower
            ? (m.delta > 0 ? '#c0392b' : m.delta < 0 ? '#27ae60' : '#6b7280')
            : '#6b7280';
          return `<li><code>${escHtml(m.key)}</code>: ${m.old} → ${m.new} <span style="color:${deltaColor};font-weight:bold">(${m.delta > 0 ? '+' : ''}${m.delta})</span></li>`;
        }).join('')}</ul>`
      : '—';

    return `<tr>
      <td><span style="color:${changeColor};font-weight:bold">${d.change}</span></td>
      <td>${escHtml(d.id)}</td>
      <td>${escHtml(d.label)}</td>
      <td>${escHtml(d.severity || '—')}</td>
      <td>${statusCell}</td>
      <td>${metrics}</td>
    </tr>`;
  }).join('');

  const html = `<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DLI Static Quality Compare</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;margin:0;padding:24px;background:#f6f7f9;color:#1f2937}
h1{margin:0 0 8px}
.meta{color:#4b5563;font-size:14px;margin-bottom:18px}
.cards{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.card{background:#fff;border-radius:8px;padding:12px 16px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.card .num{font-size:24px;font-weight:700}
.table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.table th,.table td{border-bottom:1px solid #e5e7eb;padding:10px;vertical-align:top;text-align:left;font-size:13px}
.table th{background:#111827;color:#fff;font-size:12px;text-transform:uppercase;letter-spacing:.04em}
code{font-size:12px}
ul{margin:0;padding-left:18px}
</style>
</head>
<body>
  <h1>DLI Static Quality Comparator</h1>
  <div class="meta">
    New: <strong>${escHtml(result.newFile)}</strong> (${new Date(result.newReport.scannedAt).toLocaleString('it-IT')})
    &nbsp;|&nbsp;
    Old: <strong>${escHtml(result.oldFile)}</strong> (${new Date(result.oldReport.scannedAt).toLocaleString('it-IT')})
    &nbsp;|&nbsp;
    Verdict: <strong style="color:${color}">${result.summary.verdict}</strong>
  </div>

  <div class="cards">
    <div class="card"><div class="num" style="color:#27ae60">${result.summary.fixed}</div><div>Fixed</div></div>
    <div class="card"><div class="num" style="color:#c0392b">${result.summary.regressions}</div><div>Regressions</div></div>
    <div class="card"><div class="num" style="color:#e67e22">${result.summary.changed}</div><div>Changed</div></div>
    <div class="card"><div class="num">${result.summary.unchanged}</div><div>Unchanged</div></div>
    <div class="card">
      <div style="display:flex;align-items:baseline;gap:6px">
        <span class="num" style="color:#7f8c8d">${result.summary.failOld}</span>
        <span style="color:#aaa">→</span>
        <span class="num" style="color:${result.summary.failNew > result.summary.failOld ? '#c0392b' : result.summary.failNew < result.summary.failOld ? '#27ae60' : '#7f8c8d'}">${result.summary.failNew}</span>
      </div>
      <div>FAIL checks</div>
    </div>
    <div class="card">
      <div style="display:flex;align-items:baseline;gap:6px">
        <span class="num" style="color:#7f8c8d">${result.summary.warnOld}</span>
        <span style="color:#aaa">→</span>
        <span class="num" style="color:${result.summary.warnNew > result.summary.warnOld ? '#e67e22' : result.summary.warnNew < result.summary.warnOld ? '#27ae60' : '#7f8c8d'}">${result.summary.warnNew}</span>
      </div>
      <div>WARN checks</div>
    </div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Delta</th>
        <th>ID</th>
        <th>Check</th>
        <th>Severity</th>
        <th>Status</th>
        <th>Metric changes</th>
      </tr>
    </thead>
    <tbody>${rows}</tbody>
  </table>
</body>
</html>`;

  fs.writeFileSync(outFile, html, 'utf8');
}

function printConsole(result) {
  const sep = '='.repeat(68);
  console.log('\n' + sep);
  console.log('DLI Static Quality Comparator');
  console.log(sep);
  console.log(`New: ${result.newFile}`);
  console.log(`Old: ${result.oldFile}`);
  console.log(sep);
  console.log(`Fixed: ${result.summary.fixed}`);
  console.log(`Regressions: ${result.summary.regressions}`);
  console.log(`Changed: ${result.summary.changed}`);
  console.log(`Unchanged: ${result.summary.unchanged}`);
  console.log(`FAIL checks: ${result.summary.failOld} -> ${result.summary.failNew}`);
  console.log(`WARN checks: ${result.summary.warnOld} -> ${result.summary.warnNew}`);
  console.log(`Verdict: ${result.summary.verdict}`);
  console.log(sep + '\n');
}

function main() {
  const opts = parseArgs(process.argv);

  let fileNew = opts.fileNew;
  let fileOld = opts.fileOld;
  if (!fileNew || !fileOld) {
    [fileNew, fileOld] = findTwoMostRecentReports();
  }

  const newReport = loadJson(fileNew);
  const oldReport = loadJson(fileOld);

  const deltas = compareChecks(newReport, oldReport);
  const summary = buildSummary(deltas, newReport, oldReport);

  const tsNew = tsFromFilename(path.basename(fileNew)) || String(Date.now());
  const tsOld = tsFromFilename(path.basename(fileOld)) || String(Date.now());
  const outFile = opts.out
    ? (opts.out.endsWith('.html') ? opts.out : `${opts.out}.html`)
    : path.join(REPORTS_DIR, `quality_compare_${tsNew}_vs_${tsOld}.html`);

  const result = {
    newFile: path.basename(fileNew),
    oldFile: path.basename(fileOld),
    newReport,
    oldReport,
    deltas,
    summary,
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
