'use strict';

const fs = require('fs');
const path = require('path');

const REPORTS_DIR = path.join(__dirname, 'reports');

// ---------------------------------------------------------------------------
// CLI argument parsing
// ---------------------------------------------------------------------------

function parseArgs(argv) {
  const args = argv.slice(2);
  const opts = {
    fileNew: null,
    fileOld: null,
    timingThreshold: 500,
    out: null,
  };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg === '--timing-threshold' && args[i + 1]) {
      opts.timingThreshold = parseInt(args[++i], 10);
    } else if (arg === '--out' && args[i + 1]) {
      opts.out = args[++i];
    } else if (!arg.startsWith('--')) {
      if (!opts.fileNew) opts.fileNew = arg;
      else if (!opts.fileOld) opts.fileOld = arg;
    }
  }

  return opts;
}

// ---------------------------------------------------------------------------
// Auto-detect two most recent report JSON files
// ---------------------------------------------------------------------------

function tsFromFilename(filename) {
  const m = path.basename(filename).match(/^status_report_(\d{8}_\d{4})\.json$/);
  return m ? m[1] : null;
}

function findTwoMostRecentReports() {
  if (!fs.existsSync(REPORTS_DIR)) {
    console.error('ERROR: reports/ directory not found.');
    process.exit(1);
  }

  const files = fs.readdirSync(REPORTS_DIR)
    .filter((f) => tsFromFilename(f) !== null)
    .sort((a, b) => tsFromFilename(b).localeCompare(tsFromFilename(a)));

  if (files.length < 2) {
    console.error(`ERROR: Need at least 2 status_report_*.json files in reports/, found ${files.length}.`);
    process.exit(1);
  }

  return [
    path.join(REPORTS_DIR, files[0]),
    path.join(REPORTS_DIR, files[1]),
  ];
}

// ---------------------------------------------------------------------------
// Load and validate a report JSON
// ---------------------------------------------------------------------------

function loadReport(filePath) {
  const abs = path.isAbsolute(filePath) ? filePath : path.resolve(filePath);
  if (!fs.existsSync(abs)) {
    console.error(`ERROR: File not found: ${abs}`);
    process.exit(1);
  }
  try {
    return JSON.parse(fs.readFileSync(abs, 'utf8'));
  } catch (e) {
    console.error(`ERROR: Cannot parse ${abs}: ${e.message}`);
    process.exit(1);
  }
}

// ---------------------------------------------------------------------------
// Compare logic
// ---------------------------------------------------------------------------

function errorKey(e) {
  return `${e.type}::${e.message}`;
}

function errorsEqual(a, b) {
  if (a.length !== b.length) return false;
  const ka = new Set(a.map(errorKey));
  return b.every((e) => ka.has(errorKey(e)));
}

function classifyPage(oldPage, newPage) {
  if (!oldPage) return 'NEW_PAGE';
  if (!newPage) return 'REMOVED_PAGE';
  const hadErrors = oldPage.errors.length > 0;
  const hasErrors = newPage.errors.length > 0;
  if (!hadErrors && !hasErrors) return 'UNCHANGED_OK';
  if (hadErrors && !hasErrors) return 'FIXED';
  if (!hadErrors && hasErrors) return 'REGRESSION';
  if (errorsEqual(oldPage.errors, newPage.errors)) return 'UNCHANGED_ERROR';
  return 'CHANGED_ERROR';
}

function buildDelta(reportNew, reportOld, timingThreshold) {
  const mapNew = new Map(reportNew.pages.map((p) => [p.url, p]));
  const mapOld = new Map(reportOld.pages.map((p) => [p.url, p]));
  const allUrls = new Set([...mapNew.keys(), ...mapOld.keys()]);

  const pages = [];
  allUrls.forEach((url) => {
    const pNew = mapNew.get(url) || null;
    const pOld = mapOld.get(url) || null;
    const delta = classifyPage(pOld, pNew);

    const ttfbDelta = (pNew && pNew.ttfbMs !== null && pOld && pOld.ttfbMs !== null)
      ? pNew.ttfbMs - pOld.ttfbMs
      : null;
    const loadDelta = (pNew && pNew.responseTimeMs !== null && pOld && pOld.responseTimeMs !== null)
      ? pNew.responseTimeMs - pOld.responseTimeMs
      : null;

    let timingStatus = null;
    if (ttfbDelta !== null) {
      timingStatus = ttfbDelta > timingThreshold ? 'SLOWER' : ttfbDelta < -timingThreshold ? 'FASTER' : 'STABLE';
    }

    pages.push({ url, delta, old: pOld, new: pNew, ttfbDelta, loadDelta, timingStatus });
  });

  return pages;
}

function computeSummary(pages) {
  const counts = {
    fixed: 0, regressions: 0, changedErrors: 0,
    persistentErrors: 0, unchangedOk: 0, newPages: 0, removedPages: 0,
    fasterPages: 0, slowerPages: 0,
  };
  const ttfbDeltas = [];
  const loadDeltas = [];

  pages.forEach((p) => {
    counts[{
      FIXED: 'fixed', REGRESSION: 'regressions', CHANGED_ERROR: 'changedErrors',
      UNCHANGED_ERROR: 'persistentErrors', UNCHANGED_OK: 'unchangedOk',
      NEW_PAGE: 'newPages', REMOVED_PAGE: 'removedPages',
    }[p.delta]]++;
    if (p.timingStatus === 'FASTER') counts.fasterPages++;
    if (p.timingStatus === 'SLOWER') counts.slowerPages++;
    if (p.ttfbDelta !== null) ttfbDeltas.push(p.ttfbDelta);
    if (p.loadDelta !== null) loadDeltas.push(p.loadDelta);
  });

  const avg = (arr) => arr.length > 0
    ? Math.round(arr.reduce((a, b) => a + b, 0) / arr.length)
    : null;

  let verdict;
  if (counts.regressions === 0 && counts.fixed === 0 && counts.changedErrors === 0) {
    verdict = 'UNCHANGED';
  } else if (counts.regressions === 0 || counts.fixed > counts.regressions) {
    verdict = 'IMPROVED';
  } else if (counts.fixed === 0 || counts.regressions > counts.fixed) {
    verdict = 'DEGRADED';
  } else {
    verdict = 'MIXED';
  }

  return { ...counts, avgTtfbDeltaMs: avg(ttfbDeltas), avgLoadDeltaMs: avg(loadDeltas), verdict };
}

// ---------------------------------------------------------------------------
// Console output
// ---------------------------------------------------------------------------

function printConsole(pages, summary, opts, fileNew, fileOld, reportNew, reportOld) {
  const SEP = '='.repeat(60);
  const LIN = '-'.repeat(60);
  const pad = (n) => String(n).padStart(3);
  const signFmt = (n) => n === null ? '—' : (n > 0 ? '+' : '') + n + 'ms';
  const VERDICT_LABEL = { IMPROVED: 'MIGLIORATO', DEGRADED: 'PEGGIORATO', UNCHANGED: 'INVARIATO', MIXED: 'MISTO' };

  console.log('\n' + SEP);
  console.log('DLI Site Status Comparator');
  console.log(SEP);
  console.log(`Nuovo  : ${path.basename(fileNew)}  (${new Date(reportNew.scannedAt).toLocaleString('it-IT')})`);
  console.log(`Vecchio: ${path.basename(fileOld)}  (${new Date(reportOld.scannedAt).toLocaleString('it-IT')})`);
  console.log(SEP);
  console.log('\nRIEPILOGO DELTA');
  console.log(LIN);
  const compared = pages.filter((p) => p.delta !== 'NEW_PAGE' && p.delta !== 'REMOVED_PAGE').length;
  console.log(`Pagine confrontate : ${pad(compared)}`);
  console.log(`Pagine fisse       : ${pad(summary.fixed)}  (errori risolti)`);
  console.log(`Regressioni        : ${pad(summary.regressions)}  (errori nuovi)`);
  console.log(`Errori modificati  : ${pad(summary.changedErrors)}`);
  console.log(`Errori persistenti : ${pad(summary.persistentErrors)}`);
  console.log(`Pagine OK stabili  : ${pad(summary.unchangedOk)}`);
  console.log(`Pagine nuove       : ${pad(summary.newPages)}`);
  console.log(`Pagine rimosse     : ${pad(summary.removedPages)}`);
  console.log(LIN);
  console.log(`Pagine più veloci  : ${pad(summary.fasterPages)}  (TTFB migliorato > ${opts.timingThreshold}ms)`);
  console.log(`Pagine più lente   : ${pad(summary.slowerPages)}  (TTFB peggiorato > ${opts.timingThreshold}ms)`);
  console.log(`Delta TTFB medio   : ${signFmt(summary.avgTtfbDeltaMs)}`);
  console.log(`Delta Load medio   : ${signFmt(summary.avgLoadDeltaMs)}`);
  console.log(LIN);
  console.log(`Risultato          : ${VERDICT_LABEL[summary.verdict] || summary.verdict}`);
  console.log(SEP);

  const regressions = pages.filter((p) => p.delta === 'REGRESSION');
  if (regressions.length > 0) {
    console.log(`\nREGRESSIONI (${regressions.length})`);
    regressions.forEach((p) => {
      p.new.errors.forEach((e) => console.log(`  ✗  ${p.url}  [${e.type}] ${e.message}`));
    });
  }

  const fixed = pages.filter((p) => p.delta === 'FIXED');
  if (fixed.length > 0) {
    console.log(`\nPAGINE FISSE (${fixed.length})`);
    fixed.forEach((p) => {
      const types = p.old.errors.map((e) => e.type).join(', ');
      console.log(`  ✓  ${p.url}  (rimosso: ${types})`);
    });
  }

  console.log(SEP + '\n');
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

function errorBadge(type) {
  const colors = {
    HTTP: '#c0392b', PHP: '#e67e22', JS: '#8e44ad',
    NET: '#2980b9', CONTENT: '#7f8c8d', TIMEOUT: '#7f8c8d', INTERNAL: '#c0392b',
  };
  const bg = colors[type] || '#555';
  return `<span style="background:${bg};color:#fff;padding:1px 6px;border-radius:3px;font-size:0.75em;font-weight:bold;white-space:nowrap">${type}</span>`;
}

function deltaBadge(delta) {
  const map = {
    FIXED:           { bg: '#27ae60', label: 'FIXED' },
    REGRESSION:      { bg: '#c0392b', label: 'REGRESSION' },
    CHANGED_ERROR:   { bg: '#e67e22', label: 'CHANGED' },
    UNCHANGED_ERROR: { bg: '#7f8c8d', label: 'PERSIST' },
    UNCHANGED_OK:    { bg: '#bdc3c7', label: 'OK' },
    NEW_PAGE:        { bg: '#2980b9', label: 'NEW' },
    REMOVED_PAGE:    { bg: '#95a5a6', label: 'REMOVED' },
  };
  const { bg, label } = map[delta] || { bg: '#555', label: delta };
  return `<span style="background:${bg};color:#fff;padding:1px 6px;border-radius:3px;font-size:0.75em;font-weight:bold;white-space:nowrap">${label}</span>`;
}

function signCell(delta) {
  if (delta === null) return '<span style="color:#bbb">—</span>';
  const sign = delta > 0 ? '+' : '';
  const color = delta > 0 ? '#c0392b' : delta < 0 ? '#27ae60' : '#888';
  return `<span style="color:${color};font-weight:bold">${sign}${delta} ms</span>`;
}

function errList(errors) {
  return errors.map((e) => `<li>${errorBadge(e.type)} <code>${escHtml(e.message)}</code></li>`).join('');
}

function renderSection(pages, title, filterFn) {
  const rows = pages.filter(filterFn);
  if (rows.length === 0) return '';

  const trs = rows.map((p) => {
    let detail = '';
    if (p.delta === 'FIXED' && p.old.errors.length > 0) {
      detail = `<div style="font-size:0.8em;margin-top:4px"><span style="color:#27ae60">&#10003; Risolti:</span><ul style="margin:2px 0;padding-left:16px;line-height:1.8">${errList(p.old.errors)}</ul></div>`;
    } else if (p.delta === 'REGRESSION' && p.new.errors.length > 0) {
      detail = `<div style="font-size:0.8em;margin-top:4px"><span style="color:#c0392b">&#10007; Nuovi:</span><ul style="margin:2px 0;padding-left:16px;line-height:1.8">${errList(p.new.errors)}</ul></div>`;
    } else if (p.delta === 'CHANGED_ERROR') {
      detail = `<div style="font-size:0.8em;margin-top:4px">
        <div><span style="color:#c0392b">Ora:</span><ul style="margin:2px 0;padding-left:16px;line-height:1.8">${errList(p.new.errors)}</ul></div>
        <div style="margin-top:2px"><span style="color:#888">Prima:</span><ul style="margin:2px 0;padding-left:16px;line-height:1.8">${errList(p.old.errors)}</ul></div>
      </div>`;
    } else if (p.delta === 'UNCHANGED_ERROR' && p.new && p.new.errors.length > 0) {
      detail = `<div style="font-size:0.8em;margin-top:4px"><ul style="margin:2px 0;padding-left:16px;line-height:1.8">${errList(p.new.errors)}</ul></div>`;
    }

    return `<tr>
      <td style="padding:8px 12px;white-space:nowrap">${deltaBadge(p.delta)}</td>
      <td style="padding:8px 12px"><a href="${escHtml(p.url)}" target="_blank" rel="noopener">${escHtml(p.url)}</a>${detail}</td>
      <td style="padding:8px 12px;text-align:right;white-space:nowrap">${signCell(p.ttfbDelta)}</td>
      <td style="padding:8px 12px;text-align:right;white-space:nowrap">${signCell(p.loadDelta)}</td>
    </tr>`;
  }).join('');

  return `
<div class="section-title">${title} (${rows.length})</div>
<table>
  <thead><tr>
    <th style="width:100px">Stato</th>
    <th>URL</th>
    <th style="width:100px;text-align:right">&#916; TTFB</th>
    <th style="width:100px;text-align:right">&#916; Load</th>
  </tr></thead>
  <tbody>${trs}</tbody>
</table>`;
}

function renderTimingSection(pages, threshold) {
  const rows = pages
    .filter((p) => p.timingStatus === 'SLOWER' || p.timingStatus === 'FASTER')
    .sort((a, b) => Math.abs(b.ttfbDelta) - Math.abs(a.ttfbDelta));
  if (rows.length === 0) return '';

  const trs = rows.map((p) => {
    const icon = p.timingStatus === 'SLOWER' ? '&#9650;' : '&#9660;';
    const color = p.timingStatus === 'SLOWER' ? '#c0392b' : '#27ae60';
    const oldTtfb = p.old && p.old.ttfbMs !== null ? p.old.ttfbMs + ' ms' : '—';
    const newTtfb = p.new && p.new.ttfbMs !== null ? p.new.ttfbMs + ' ms' : '—';
    return `<tr>
      <td style="padding:8px 12px;text-align:center;color:${color};font-size:1.1em">${icon}</td>
      <td style="padding:8px 12px"><a href="${escHtml(p.url)}" target="_blank" rel="noopener">${escHtml(p.url)}</a></td>
      <td style="padding:8px 12px;text-align:right;color:#888">${oldTtfb}</td>
      <td style="padding:8px 12px;text-align:right">${newTtfb}</td>
      <td style="padding:8px 12px;text-align:right">${signCell(p.ttfbDelta)}</td>
    </tr>`;
  }).join('');

  return `
<div class="section-title">Variazioni TTFB significative (&gt; &pm;${threshold} ms)</div>
<p style="font-size:0.8em;color:#666;margin:-4px 0 8px">TTFB &egrave; il tempo server, indipendente dalla concorrenza dello scanner.</p>
<table>
  <thead><tr>
    <th style="width:36px"></th>
    <th>URL</th>
    <th style="width:120px;text-align:right">TTFB prima</th>
    <th style="width:120px;text-align:right">TTFB dopo</th>
    <th style="width:110px;text-align:right">Delta</th>
  </tr></thead>
  <tbody>${trs}</tbody>
</table>`;
}

function writeHtml(pages, summary, opts, fileNew, fileOld, reportNew, reportOld) {
  const VERDICT_LABEL = { IMPROVED: 'MIGLIORATO', DEGRADED: 'PEGGIORATO', UNCHANGED: 'INVARIATO', MIXED: 'MISTO' };
  const VERDICT_COLOR = { IMPROVED: '#27ae60', DEGRADED: '#c0392b', UNCHANGED: '#7f8c8d', MIXED: '#e67e22' };
  const verdictLabel = VERDICT_LABEL[summary.verdict] || summary.verdict;
  const verdictColor = VERDICT_COLOR[summary.verdict] || '#555';
  const scannedAt = new Date().toLocaleString('it-IT');
  const signFmt = (n) => n === null ? '—' : (n > 0 ? '+' : '') + n + 'ms';
  const deltaColor = (n) => n === null ? '#16a085' : n > 0 ? '#c0392b' : '#27ae60';

  const persistentSection = summary.persistentErrors > 0
    ? `<details>
  <summary>Errori persistenti (${summary.persistentErrors}) &mdash; clicca per espandere</summary>
  ${renderSection(pages, '', (p) => p.delta === 'UNCHANGED_ERROR').replace(/^[\s\S]*?<table/, '<table')}
</details>`
    : '';

  const html = `<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DLI Site Status &mdash; Confronto report</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: system-ui, sans-serif; margin: 0; padding: 24px; background: #f5f5f5; color: #222; }
  h1 { font-size: 1.4em; margin: 0 0 4px; }
  .meta { color: #666; font-size: 0.85em; margin-bottom: 24px; }
  .files-box { background: #fff; border-radius: 6px; padding: 14px 20px; box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 24px; font-size: 0.88em; }
  .files-box .lbl { color: #888; font-size: 0.82em; text-transform: uppercase; letter-spacing: .04em; display: inline-block; width: 56px; }
  .summary { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 28px; }
  .card { background: #fff; border-radius: 6px; padding: 16px 24px; min-width: 110px; box-shadow: 0 1px 4px rgba(0,0,0,.1); text-align: center; }
  .card .num { font-size: 2em; font-weight: bold; line-height: 1; }
  .card .lbl { font-size: 0.75em; color: #666; margin-top: 4px; text-transform: uppercase; letter-spacing: .04em; }
  .verdict { font-size: 1.6em; font-weight: bold; color: ${verdictColor}; border: 2px solid ${verdictColor}; border-radius: 6px; padding: 12px 24px; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 24px; }
  th { background: #2c3e50; color: #fff; padding: 10px 12px; text-align: left; font-size: 0.8em; text-transform: uppercase; letter-spacing: .05em; }
  tr:hover { background: #f0f4f8; }
  .section-title { font-size: 1em; font-weight: bold; color: #444; margin: 24px 0 8px; }
  details summary { cursor: pointer; font-size: 0.9em; color: #666; padding: 6px 0; }
  code { font-size: 0.82em; word-break: break-all; }
</style>
</head>
<body>

<h1>DLI Site Status &mdash; Confronto report</h1>
<p class="meta">Elaborato il <strong>${scannedAt}</strong></p>

<div class="files-box">
  <div><span class="lbl">Nuovo</span> <strong>${escHtml(path.basename(fileNew))}</strong> &nbsp;&bull;&nbsp; ${escHtml(new Date(reportNew.scannedAt).toLocaleString('it-IT'))} &nbsp;&bull;&nbsp; ${escHtml(reportNew.baseUrl)}</div>
  <div style="margin-top:6px"><span class="lbl">Vecchio</span> <strong>${escHtml(path.basename(fileOld))}</strong> &nbsp;&bull;&nbsp; ${escHtml(new Date(reportOld.scannedAt).toLocaleString('it-IT'))} &nbsp;&bull;&nbsp; ${escHtml(reportOld.baseUrl)}</div>
</div>

<div class="summary">
  <div class="card verdict">${verdictLabel}</div>
  <div class="card"><div class="num" style="color:#27ae60">${summary.fixed}</div><div class="lbl">Errori risolti</div></div>
  <div class="card"><div class="num" style="color:#c0392b">${summary.regressions}</div><div class="lbl">Regressioni</div></div>
  <div class="card"><div class="num" style="color:#e67e22">${summary.changedErrors}</div><div class="lbl">Cambiati</div></div>
  <div class="card"><div class="num" style="color:#7f8c8d">${summary.persistentErrors}</div><div class="lbl">Persistenti</div></div>
  <div class="card"><div class="num">${summary.unchangedOk}</div><div class="lbl">OK stabili</div></div>
  <div class="card"><div class="num" style="color:#27ae60">${summary.fasterPages}</div><div class="lbl">Più veloci</div></div>
  <div class="card"><div class="num" style="color:#c0392b">${summary.slowerPages}</div><div class="lbl">Più lente</div></div>
  <div class="card"><div class="num" style="color:${deltaColor(summary.avgTtfbDeltaMs)}">${signFmt(summary.avgTtfbDeltaMs)}</div><div class="lbl">&#916; TTFB medio</div></div>
  <div class="card"><div class="num" style="color:${deltaColor(summary.avgLoadDeltaMs)}">${signFmt(summary.avgLoadDeltaMs)}</div><div class="lbl">&#916; Load medio</div></div>
</div>

${renderSection(pages, 'Regressioni', (p) => p.delta === 'REGRESSION')}
${renderSection(pages, 'Pagine fisse', (p) => p.delta === 'FIXED')}
${renderSection(pages, 'Errori modificati', (p) => p.delta === 'CHANGED_ERROR')}
${renderTimingSection(pages, opts.timingThreshold)}
${persistentSection}
${renderSection(pages, 'Pagine nuove', (p) => p.delta === 'NEW_PAGE')}
${renderSection(pages, 'Pagine rimosse', (p) => p.delta === 'REMOVED_PAGE')}

</body>
</html>`;

  const filePath = opts.out + '.html';
  fs.writeFileSync(filePath, html, 'utf8');
  console.log(`HTML report written: ${filePath}`);
}

// ---------------------------------------------------------------------------
// JSON output
// ---------------------------------------------------------------------------

function writeJson(pages, summary, opts, fileNew, fileOld, reportNew, reportOld) {
  const output = {
    comparedAt: new Date().toISOString(),
    newReport:  { file: path.basename(fileNew), scannedAt: reportNew.scannedAt, baseUrl: reportNew.baseUrl },
    oldReport:  { file: path.basename(fileOld), scannedAt: reportOld.scannedAt, baseUrl: reportOld.baseUrl },
    summary,
    pages: pages.map((p) => ({
      url: p.url,
      delta: p.delta,
      ttfbDelta: p.ttfbDelta,
      loadDelta: p.loadDelta,
      timingStatus: p.timingStatus,
      old: p.old ? { errors: p.old.errors, ttfbMs: p.old.ttfbMs, responseTimeMs: p.old.responseTimeMs } : null,
      new: p.new ? { errors: p.new.errors, ttfbMs: p.new.ttfbMs, responseTimeMs: p.new.responseTimeMs } : null,
    })),
  };

  const filePath = opts.out + '.json';
  fs.writeFileSync(filePath, JSON.stringify(output, null, 2), 'utf8');
  console.log(`JSON report written: ${filePath}`);
}

// ---------------------------------------------------------------------------
// Timestamp helper
// ---------------------------------------------------------------------------

function buildTimestamp(date) {
  const pad = (n) => String(n).padStart(2, '0');
  return date.getFullYear() + pad(date.getMonth() + 1) + pad(date.getDate()) + '_' + pad(date.getHours()) + pad(date.getMinutes());
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

function main() {
  const opts = parseArgs(process.argv);
  let fileNew = opts.fileNew;
  let fileOld = opts.fileOld;

  if (!fileNew || !fileOld) {
    const auto = findTwoMostRecentReports();
    if (!fileNew) fileNew = auto[0];
    if (!fileOld) fileOld = auto[1];
    console.log(`Auto-selected reports:`);
    console.log(`  Nuovo  : ${path.basename(fileNew)}`);
    console.log(`  Vecchio: ${path.basename(fileOld)}`);
  }

  const reportNew = loadReport(fileNew);
  const reportOld = loadReport(fileOld);

  if (!opts.out) {
    const tsNew = tsFromFilename(fileNew) || buildTimestamp(new Date());
    const tsOld = tsFromFilename(fileOld) || 'prev';
    opts.out = path.join(REPORTS_DIR, `compare_${tsNew}_vs_${tsOld}`);
  }

  const outDir = path.dirname(opts.out);
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

  const pages = buildDelta(reportNew, reportOld, opts.timingThreshold);
  const summary = computeSummary(pages);

  printConsole(pages, summary, opts, fileNew, fileOld, reportNew, reportOld);
  writeJson(pages, summary, opts, fileNew, fileOld, reportNew, reportOld);
  writeHtml(pages, summary, opts, fileNew, fileOld, reportNew, reportOld);
}

main();
