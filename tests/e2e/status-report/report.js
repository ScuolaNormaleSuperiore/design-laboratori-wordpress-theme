'use strict';

const fs = require('fs');
const path = require('path');

// ---------------------------------------------------------------------------
// Summary computation
// ---------------------------------------------------------------------------

function computeSummary(results) {
  const summary = {
    total: results.length,
    ok: 0,
    withErrors: 0,
    httpErrors: 0,
    phpErrors: 0,
    jsErrors: 0,
    resourceErrors: 0,
    contentErrors: 0,
    timeouts: 0,
    avgResponseTimeMs: null,
    maxResponseTimeMs: null,
    slowPages: [], // pages with responseTimeMs > 3000
  };

  const times = results.map((r) => r.responseTimeMs).filter((t) => t !== null);
  if (times.length > 0) {
    summary.avgResponseTimeMs = Math.round(times.reduce((a, b) => a + b, 0) / times.length);
    summary.maxResponseTimeMs = Math.max(...times);
    summary.slowPages = results
      .filter((r) => r.responseTimeMs > 3000)
      .map((r) => ({ url: r.url, responseTimeMs: r.responseTimeMs }))
      .sort((a, b) => b.responseTimeMs - a.responseTimeMs);
  }

  results.forEach((r) => {
    if (r.errors.length === 0) {
      summary.ok++;
    } else {
      summary.withErrors++;
    }
    r.errors.forEach((e) => {
      if (e.type === 'HTTP') summary.httpErrors++;
      if (e.type === 'PHP') summary.phpErrors++;
      if (e.type === 'JS') summary.jsErrors++;
      if (e.type === 'NET') summary.resourceErrors++;
      if (e.type === 'CONTENT') summary.contentErrors++;
      if (e.type === 'TIMEOUT') summary.timeouts++;
    });
  });

  return summary;
}

// ---------------------------------------------------------------------------
// JSON report
// ---------------------------------------------------------------------------

function writeJson(results, summary, opts) {
  const output = {
    baseUrl: opts.baseUrl,
    scannedAt: new Date().toISOString(),
    totalPages: summary.total,
    pagesWithErrors: summary.withErrors,
    summary: {
      httpErrors: summary.httpErrors,
      phpErrors: summary.phpErrors,
      jsErrors: summary.jsErrors,
      resourceErrors: summary.resourceErrors,
      contentErrors: summary.contentErrors,
      timeouts: summary.timeouts,
      avgResponseTimeMs: summary.avgResponseTimeMs,
      maxResponseTimeMs: summary.maxResponseTimeMs,
      slowPages: summary.slowPages,
    },
    pages: results.map((r) => ({
      url: r.url,
      status: r.status,
      responseTimeMs: r.responseTimeMs,
      timedOut: r.timedOut,
      errors: r.errors,
    })),
  };

  const filePath = opts.out + '.json';
  fs.writeFileSync(filePath, JSON.stringify(output, null, 2), 'utf8');
  console.log(`\nJSON report written: ${filePath}`);
}

// ---------------------------------------------------------------------------
// HTML report
// ---------------------------------------------------------------------------

function errorBadge(type) {
  const colors = {
    HTTP: '#c0392b',
    PHP: '#e67e22',
    JS: '#8e44ad',
    NET: '#2980b9',
    CONTENT: '#7f8c8d',
    TIMEOUT: '#7f8c8d',
    INTERNAL: '#c0392b',
  };
  const bg = colors[type] || '#555';
  return `<span style="background:${bg};color:#fff;padding:1px 6px;border-radius:3px;font-size:0.75em;font-weight:bold;white-space:nowrap">${type}</span>`;
}

function renderPageRow(r, index) {
  const hasErrors = r.errors.length > 0;
  const rowClass = hasErrors ? 'row-error' : 'row-ok';
  const icon = hasErrors ? '&#10007;' : '&#10003;';
  const statusStr = r.timedOut ? 'TIMEOUT' : (r.status || '—');
  const detailId = `detail-${index}`;

  const timeMs = r.responseTimeMs !== null ? r.responseTimeMs : null;
  const timeStr = timeMs !== null ? timeMs + ' ms' : '—';
  const timeStyle = timeMs === null
    ? ''
    : timeMs > 3000
      ? 'color:#c0392b;font-weight:bold'
      : timeMs > 1500
        ? 'color:#e67e22'
        : 'color:#27ae60';

  let errHtml = '';
  if (hasErrors) {
    const errItems = r.errors
      .map((e) => `<li>${errorBadge(e.type)} <code>${escHtml(e.message)}</code></li>`)
      .join('');
    errHtml = `
      <tr class="detail-row" id="${detailId}" style="display:none">
        <td colspan="5" style="padding:8px 16px 12px 40px;background:#fafafa;border-bottom:1px solid #eee">
          <ul style="margin:0;padding:0 0 0 16px;line-height:1.8">${errItems}</ul>
        </td>
      </tr>`;
  }

  const toggle = hasErrors
    ? `onclick="toggle('${detailId}')" style="cursor:pointer"`
    : '';

  return `
    <tr class="${rowClass}" ${toggle}>
      <td style="padding:8px 12px;font-size:1.1em;color:${hasErrors ? '#c0392b' : '#27ae60'}">${icon}</td>
      <td style="padding:8px 12px"><a href="${escHtml(r.url)}" target="_blank" rel="noopener">${escHtml(r.url)}</a></td>
      <td style="padding:8px 12px;text-align:center">${statusStr}</td>
      <td style="padding:8px 12px;text-align:right;${timeStyle}">${timeStr}</td>
      <td style="padding:8px 12px;text-align:center">${r.errors.length === 0 ? '—' : r.errors.length}</td>
    </tr>${errHtml}`;
}

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function writeHtml(results, summary, opts) {
  const pass = summary.withErrors === 0;
  const passLabel = pass ? 'PASS' : 'FAIL';
  const passColor = pass ? '#27ae60' : '#c0392b';
  const scannedAt = new Date().toLocaleString('it-IT');

  // Sort: errors first
  const sorted = [...results].sort((a, b) => b.errors.length - a.errors.length);
  const errorPages = sorted.filter((r) => r.errors.length > 0);
  const okPages = sorted.filter((r) => r.errors.length === 0);

  const errorRows = errorPages.map((r, i) => renderPageRow(r, i)).join('');
  const okRows = okPages.map((r, i) => renderPageRow(r, i + errorPages.length)).join('');

  const html = `<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DLI Site Status Report</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: system-ui, sans-serif; margin: 0; padding: 24px; background: #f5f5f5; color: #222; }
  h1 { font-size: 1.4em; margin: 0 0 4px; }
  .meta { color: #666; font-size: 0.85em; margin-bottom: 24px; }
  .summary { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 28px; }
  .card { background: #fff; border-radius: 6px; padding: 16px 24px; min-width: 120px; box-shadow: 0 1px 4px rgba(0,0,0,.1); text-align: center; }
  .card .num { font-size: 2em; font-weight: bold; line-height: 1; }
  .card .lbl { font-size: 0.75em; color: #666; margin-top: 4px; text-transform: uppercase; letter-spacing: .04em; }
  .verdict { font-size: 1.6em; font-weight: bold; color: ${passColor}; border: 2px solid ${passColor}; border-radius: 6px; padding: 12px 24px; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 24px; }
  th { background: #2c3e50; color: #fff; padding: 10px 12px; text-align: left; font-size: 0.8em; text-transform: uppercase; letter-spacing: .05em; }
  tr.row-error:hover, tr.row-ok:hover { background: #f0f4f8; }
  tr.row-error { border-left: 3px solid #c0392b; }
  .section-title { font-size: 1em; font-weight: bold; color: #444; margin: 24px 0 8px; }
  details summary { cursor: pointer; font-size: 0.9em; color: #666; padding: 6px 0; }
  code { font-size: 0.82em; word-break: break-all; }
</style>
</head>
<body>

<h1>DLI Site Status Report</h1>
<p class="meta">
  Sito: <strong><a href="${escHtml(opts.baseUrl)}" target="_blank" rel="noopener">${escHtml(opts.baseUrl)}</a></strong>
  &nbsp;&bull;&nbsp; Scansione: <strong>${scannedAt}</strong>
  &nbsp;&bull;&nbsp; Pagine controllate: <strong>${summary.total}</strong>
</p>

<!-- RIEPILOGO NUMERICO -->
<div class="summary">
  <div class="card verdict">${passLabel}</div>
  <div class="card"><div class="num" style="color:#27ae60">${summary.ok}</div><div class="lbl">Pagine OK</div></div>
  <div class="card"><div class="num" style="color:#c0392b">${summary.withErrors}</div><div class="lbl">Con errori</div></div>
  <div class="card"><div class="num">${summary.total}</div><div class="lbl">Totale</div></div>
  <div class="card"><div class="num" style="color:#c0392b">${summary.httpErrors}</div><div class="lbl">HTTP errors</div></div>
  <div class="card"><div class="num" style="color:#e67e22">${summary.phpErrors}</div><div class="lbl">PHP errors</div></div>
  <div class="card"><div class="num" style="color:#8e44ad">${summary.jsErrors}</div><div class="lbl">JS errors</div></div>
  <div class="card"><div class="num" style="color:#2980b9">${summary.resourceErrors}</div><div class="lbl">Risorse 404</div></div>
  <div class="card"><div class="num" style="color:#7f8c8d">${summary.timeouts}</div><div class="lbl">Timeout</div></div>
  <div class="card"><div class="num" style="color:#16a085">${summary.avgResponseTimeMs !== null ? summary.avgResponseTimeMs + 'ms' : '—'}</div><div class="lbl">Tempo medio</div></div>
  <div class="card"><div class="num" style="color:${summary.maxResponseTimeMs > 3000 ? '#c0392b' : '#16a085'}">${summary.maxResponseTimeMs !== null ? summary.maxResponseTimeMs + 'ms' : '—'}</div><div class="lbl">Tempo max</div></div>
</div>

${errorPages.length > 0 ? `
<div class="section-title">Pagine con errori (${errorPages.length})</div>
<table>
  <thead>
    <tr>
      <th style="width:36px"></th>
      <th>URL</th>
      <th style="width:90px;text-align:center">Status</th>
      <th style="width:120px;text-align:right">Tempo</th>
      <th style="width:80px;text-align:center">Errori</th>
    </tr>
  </thead>
  <tbody>${errorRows}</tbody>
</table>` : ''}

${okPages.length > 0 ? `
<details>
  <summary>Pagine senza errori (${okPages.length}) &mdash; clicca per espandere</summary>
  <table style="margin-top:8px">
    <thead>
      <tr>
        <th style="width:36px"></th>
        <th>URL</th>
        <th style="width:90px;text-align:center">Status</th>
        <th style="width:120px;text-align:right">Tempo</th>
        <th style="width:80px;text-align:center">Errori</th>
      </tr>
    </thead>
    <tbody>${okRows}</tbody>
  </table>
</details>` : ''}

${summary.slowPages.length > 0 ? (() => {
  const top10 = summary.slowPages.slice(0, 10);
  const aboveThreshold = summary.slowPages.filter((p) => p.responseTimeMs > 3000).length;
  return `
<div class="section-title">Top 10 pagine più lente${aboveThreshold > 0 ? ` &mdash; <span style="color:#c0392b">${aboveThreshold} superano i 3s</span>` : ''}</div>
<table>
  <thead><tr><th style="width:36px;text-align:center">#</th><th>URL</th><th style="width:160px;text-align:right">Tempo risposta</th></tr></thead>
  <tbody>${top10.map((p, i) => {
    const slow = p.responseTimeMs > 3000;
    return `<tr>
      <td style="padding:8px 12px;text-align:center;color:#888;font-size:0.85em">${i + 1}</td>
      <td style="padding:8px 12px"><a href="${escHtml(p.url)}" target="_blank" rel="noopener">${escHtml(p.url)}</a>${slow ? ' <span style="background:#c0392b;color:#fff;padding:1px 5px;border-radius:3px;font-size:0.72em;font-weight:bold;vertical-align:middle">&gt;3s</span>' : ''}</td>
      <td style="padding:8px 12px;text-align:right;${slow ? 'color:#c0392b;font-weight:bold' : 'color:#e67e22'}">${p.responseTimeMs} ms</td>
    </tr>`;
  }).join('')}</tbody>
</table>`;
})() : ''}

<script>
function toggle(id) {
  var el = document.getElementById(id);
  if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
}
</script>
</body>
</html>`;

  const filePath = opts.out + '.html';
  fs.writeFileSync(filePath, html, 'utf8');
  console.log(`HTML report written: ${filePath}`);
}

// ---------------------------------------------------------------------------
// Entry point
// ---------------------------------------------------------------------------

async function generateReport(results, opts) {
  // Ensure output directory exists
  const outDir = path.dirname(opts.out);
  if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
  }

  const summary = computeSummary(results);

  console.log('\n' + '='.repeat(60));
  console.log('SCAN COMPLETE');
  console.log('='.repeat(60));
  console.log(`Total pages : ${summary.total}`);
  console.log(`Pages OK    : ${summary.ok}`);
  console.log(`With errors : ${summary.withErrors}`);
  console.log(`HTTP errors : ${summary.httpErrors}`);
  console.log(`PHP errors  : ${summary.phpErrors}`);
  console.log(`JS errors   : ${summary.jsErrors}`);
  console.log(`Res. errors : ${summary.resourceErrors}`);
  console.log(`Timeouts    : ${summary.timeouts}`);
  console.log(`Avg time    : ${summary.avgResponseTimeMs !== null ? summary.avgResponseTimeMs + 'ms' : '—'}`);
  console.log(`Max time    : ${summary.maxResponseTimeMs !== null ? summary.maxResponseTimeMs + 'ms' : '—'}`);
  if (summary.slowPages.length > 0) {
    console.log(`Slow pages  : ${summary.slowPages.length} (> 3s)`);
    summary.slowPages.forEach((p) => console.log(`  ${p.responseTimeMs}ms  ${p.url}`));
  }
  console.log('='.repeat(60));
  console.log(`Result      : ${summary.withErrors === 0 ? 'PASS' : 'FAIL'}`);
  console.log('='.repeat(60));

  writeJson(results, summary, opts);
  writeHtml(results, summary, opts);
}

module.exports = { generateReport };
