#!/usr/bin/env node
'use strict';

const fs = require('fs');
const path = require('path');
const { spawnSync } = require('child_process');

const ROOT = process.cwd();
const REPORTS_DIR = path.join(__dirname, 'reports');
const PHPSTAN_CONFIG = 'phpstan.neon';

function pad(n) {
  return String(n).padStart(2, '0');
}

function buildTimestamp(date) {
  return `${date.getFullYear()}${pad(date.getMonth() + 1)}${pad(date.getDate())}_${pad(date.getHours())}${pad(date.getMinutes())}`;
}

function parseArgs(argv) {
  const args = argv.slice(2);
  const opts = {
    out: null,
    timeout: 120000,
    gate: false,
  };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg === '--out' && args[i + 1]) {
      opts.out = args[++i];
    } else if (arg === '--timeout' && args[i + 1]) {
      opts.timeout = parseInt(args[++i], 10);
    } else if (arg === '--gate') {
      opts.gate = true;
    }
  }

  if (!opts.out) {
    const ts = buildTimestamp(new Date());
    opts.out = path.join('tests', 'e2e', 'quality-report', 'reports', `quality_report_${ts}`);
  }

  return opts;
}

function runCommand(command, timeoutMs = 120000) {
  const t0 = Date.now();
  const res = spawnSync(command, {
    shell: true,
    cwd: ROOT,
    encoding: 'utf8',
    timeout: timeoutMs,
    maxBuffer: 10 * 1024 * 1024,
  });
  const durationMs = Date.now() - t0;
  const stdout = res.stdout || '';
  const stderr = res.stderr || '';
  const combined = `${stdout}\n${stderr}`.trim();

  const commandMissing = [
    /is not recognized as an internal or external command/i,
    /not recognized as the name of a cmdlet/i,
    /command not found/i,
    /\bnot found\b/i,
    /no such file or directory/i,
    /non riconosciuto come nome/i,
    /non [èe] riconosciuto come comando interno o esterno/i,
  ].some((pattern) => pattern.test(combined));

  return {
    command,
    durationMs,
    exitCode: typeof res.status === 'number' ? res.status : 1,
    timedOut: !!res.error && res.error.code === 'ETIMEDOUT',
    stdout,
    stderr,
    combined,
    commandMissing,
  };
}

function runWithWindowsFallback(command, timeoutMs = 120000) {
  const first = runCommand(command, timeoutMs);
  if (!first.commandMissing) {
    return first;
  }

  const wrapped = `cmd.exe /c "${command.replace(/"/g, '\\"')}"`;
  const second = runCommand(wrapped, timeoutMs);
  if (!second.commandMissing) {
    return {
      ...second,
      command: wrapped,
    };
  }
  return first;
}

function extractJsonPayload(text) {
  if (!text) return null;
  const trimmed = text.trim();
  if ((trimmed.startsWith('{') && trimmed.endsWith('}')) || (trimmed.startsWith('[') && trimmed.endsWith(']'))) {
    try {
      return JSON.parse(trimmed);
    } catch {
      // continue
    }
  }

  const firstCurly = text.indexOf('{');
  const firstSquare = text.indexOf('[');
  let start = -1;
  if (firstCurly === -1) start = firstSquare;
  else if (firstSquare === -1) start = firstCurly;
  else start = Math.min(firstCurly, firstSquare);
  if (start === -1) return null;

  const candidate = text.slice(start).trim();
  try {
    return JSON.parse(candidate);
  } catch {
    const lastCurly = text.lastIndexOf('}');
    const lastSquare = text.lastIndexOf(']');
    let end = -1;
    if (lastCurly === -1) end = lastSquare;
    else if (lastSquare === -1) end = lastCurly;
    else end = Math.max(lastCurly, lastSquare);
    if (end > start) {
      const bounded = text.slice(start, end + 1).trim();
      try {
        return JSON.parse(bounded);
      } catch {
        return null;
      }
    }
    return null;
  }
}

function normalizeRelPath(value) {
  return String(value || '').replace(/\\/g, '/').replace(/^\.\/+/, '').replace(/^\/+/, '');
}

function quoteArg(arg) {
  return `"${String(arg).replace(/"/g, '\\"')}"`;
}

function wildcardToRegex(pattern) {
  const escaped = pattern
    .replace(/[.+?^${}()|[\]\\]/g, '\\$&')
    .replace(/\*/g, '.*');
  return new RegExp(`^${escaped}$`);
}

function parseNeonList(content, key) {
  const lines = content.split(/\r?\n/);
  const values = [];
  let inList = false;

  for (const rawLine of lines) {
    const line = rawLine.replace(/\t/g, '  ');
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) continue;

    if (!inList && new RegExp(`^${key}:\\s*$`).test(trimmed)) {
      inList = true;
      continue;
    }

    if (inList) {
      if (/^\s*-\s+/.test(line)) {
        const item = trimmed.replace(/^-+\s+/, '').replace(/^["']|["']$/g, '');
        values.push(normalizeRelPath(item));
        continue;
      }

      if (/^\S/.test(line)) {
        break;
      }
    }
  }

  return values;
}

function loadPhpStanScope() {
  const fallback = {
    paths: ['inc', 'page-templates', 'template-parts', 'archive.php', 'page.php', 'header.php', 'footer.php', 'functions.php'],
    excludePaths: ['vendor/*', 'inc/vendor/*', 'node_modules/*', 'tests/*', 'assets/*', 'languages/*', 'DEV/*', 'SETUP/*', 'DOC/*', 'AGENTS/*'],
  };

  const configFile = path.join(ROOT, PHPSTAN_CONFIG);
  if (!fs.existsSync(configFile)) {
    return fallback;
  }

  try {
    const content = fs.readFileSync(configFile, 'utf8');
    const paths = parseNeonList(content, 'paths');
    const excludePaths = parseNeonList(content, 'excludePaths');
    if (paths.length === 0) return fallback;
    return { paths, excludePaths };
  } catch {
    return fallback;
  }
}

function isExcludedPath(relPath, scope) {
  const rel = normalizeRelPath(relPath);
  const excludes = Array.isArray(scope.excludePaths) ? scope.excludePaths : [];
  return excludes.some((pattern) => {
    const normalizedPattern = normalizeRelPath(pattern);
    if (!normalizedPattern) return false;
    if (!normalizedPattern.includes('*')) {
      return rel === normalizedPattern || rel.startsWith(`${normalizedPattern}/`);
    }
    return wildcardToRegex(normalizedPattern).test(rel);
  });
}

function countPhpFiles(dir, out = [], scope = null) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    const rel = normalizeRelPath(path.relative(ROOT, full));
    if (entry.isDirectory()) {
      const defaultIgnored = ['.git', 'node_modules', 'vendor', 'assets/bootstrap-italia'];
      const isDefaultIgnored = defaultIgnored.some((p) => rel === p || rel.startsWith(`${p}/`));
      const isScopeExcluded = scope ? isExcludedPath(rel, scope) : false;
      if (isDefaultIgnored || isScopeExcluded) {
        continue;
      }
      countPhpFiles(full, out, scope);
    } else if (entry.isFile() && entry.name.endsWith('.php')) {
      if (scope && isExcludedPath(rel, scope)) continue;
      out.push(full);
    }
  }
  return out;
}

function getScopedPhpTargets(scope) {
  const targets = [];
  const seen = new Set();

  for (const item of scope.paths) {
    const rel = normalizeRelPath(item);
    if (!rel || seen.has(rel)) continue;
    const abs = path.join(ROOT, rel);
    if (!fs.existsSync(abs)) continue;
    if (isExcludedPath(rel, scope)) continue;
    targets.push(rel);
    seen.add(rel);
  }

  if (targets.length === 0) {
    return ['inc', 'page-templates', 'template-parts'].filter((p) => fs.existsSync(path.join(ROOT, p)));
  }

  return targets;
}

function collectPhpFilesForScope(scope) {
  const files = [];
  const targets = getScopedPhpTargets(scope);
  for (const target of targets) {
    const abs = path.join(ROOT, target);
    if (!fs.existsSync(abs)) continue;
    const stat = fs.statSync(abs);
    if (stat.isFile() && target.endsWith('.php')) {
      files.push(abs);
    } else if (stat.isDirectory()) {
      countPhpFiles(abs, files, scope);
    }
  }
  return files;
}

function checkPhpSyntax(timeout, scope) {
  const check = {
    id: 'php_syntax',
    label: 'PHP syntax (php -l)',
    severity: 'critical',
    status: 'SKIP',
    command: `php -l <PHP files in ${PHPSTAN_CONFIG} paths>`,
    durationMs: 0,
    metrics: {
      filesChecked: 0,
      syntaxErrors: 0,
    },
    source: 'phpstan.neon (paths)',
    details: [],
  };

  const phpProbe = runWithWindowsFallback('php -v', Math.min(timeout, 20000));
  if (phpProbe.commandMissing || phpProbe.exitCode !== 0) {
    const reason = (phpProbe.combined || '').split('\n').map((x) => x.trim()).find(Boolean);
    if (reason) check.details.push(`php non eseguibile: ${reason}`);
    return check;
  }

  const files = collectPhpFilesForScope(scope);
  check.metrics.filesChecked = files.length;

  const t0 = Date.now();
  const errors = [];
  for (const file of files) {
    const rel = path.relative(ROOT, file).replace(/\\/g, '/');
    const cmd = `php -l "${file}"`;
    const res = runWithWindowsFallback(cmd, Math.min(timeout, 30000));
    if (res.exitCode !== 0) {
      errors.push({ file: rel, message: res.combined.split('\n').slice(0, 3).join(' | ') });
    }
  }
  check.durationMs = Date.now() - t0;
  check.metrics.syntaxErrors = errors.length;
  if (errors.length > 0) {
    check.status = 'FAIL';
    check.details = errors.slice(0, 20).map((e) => `${e.file}: ${e.message}`);
    if (errors.length > 20) check.details.push(`... altri ${errors.length - 20} errori`);
  } else {
    check.status = 'PASS';
  }

  return check;
}

function checkPhpcs(timeout, scope) {
  const targets = getScopedPhpTargets(scope);
  if (targets.length === 0) {
    return {
      id: 'phpcs',
      label: 'PHPCS (WordPress coding standard)',
      severity: 'major',
      status: 'SKIP',
      command: 'vendor/bin/phpcs --report=json --standard=phpcs.xml.dist --extensions=php <paths-from-phpstan.neon>',
      durationMs: 0,
      metrics: {
        errors: 0,
        warnings: 0,
        filesWithIssues: 0,
        filesChecked: 0,
      },
      source: 'altro',
      details: [],
    };
  }

  const scopedTargets = targets.map((t) => quoteArg(t)).join(' ');
  const baseUnix = `vendor/bin/phpcs --report=json --standard=phpcs.xml.dist --extensions=php ${scopedTargets}`;
  const baseWin = `vendor\\bin\\phpcs.bat --report=json --standard=phpcs.xml.dist --extensions=php ${scopedTargets}`;

  const check = {
    id: 'phpcs',
    label: 'PHPCS (WordPress coding standard)',
    severity: 'major',
    status: 'SKIP',
    command: baseUnix,
    durationMs: 0,
    metrics: {
      errors: 0,
      warnings: 0,
      filesWithIssues: 0,
      filesChecked: 0,
    },
    source: 'phpstan.neon (paths)',
    details: [],
  };

  check.metrics.filesChecked = collectPhpFilesForScope(scope).length;

  const candidates = [baseUnix, baseWin];

  let res = null;
  let parsedJson = null;
  for (const cmd of candidates) {
    const attempt = runWithWindowsFallback(cmd, timeout);
    if (attempt.commandMissing) continue;
    const parsed = extractJsonPayload(attempt.stdout) || extractJsonPayload(attempt.combined);
    if (parsed && typeof parsed === 'object') {
      res = attempt;
      parsedJson = parsed;
      check.command = cmd;
      break;
    }
    if (!res) {
      res = attempt;
      check.command = cmd;
    }
  }

  if (!res) {
    check.details.push('phpcs non eseguibile nel contesto corrente.');
    return check;
  }

  check.durationMs = res.durationMs;
  const json = parsedJson || extractJsonPayload(res.stdout) || extractJsonPayload(res.combined);
  if (!json || typeof json !== 'object') {
    check.status = res.exitCode === 0 ? 'PASS' : 'WARN';
    check.details.push('Output PHPCS non parsabile.');
    return check;
  }

  const totals = json.totals || {};
  const files = (json.files && typeof json.files === 'object') ? json.files : {};
  const fileNames = Object.keys(files);

  check.metrics.errors = Number.isFinite(Number(totals.errors)) ? Number(totals.errors) : 0;
  check.metrics.warnings = Number.isFinite(Number(totals.warnings)) ? Number(totals.warnings) : 0;

  if (Number.isFinite(Number(totals.files))) {
    check.metrics.filesWithIssues = Number(totals.files);
  } else {
    check.metrics.filesWithIssues = fileNames.filter((name) => {
      const entry = files[name] || {};
      const entryErrors = Number(entry.errors || 0);
      const entryWarnings = Number(entry.warnings || 0);
      const messages = Array.isArray(entry.messages) ? entry.messages.length : 0;
      return entryErrors > 0 || entryWarnings > 0 || messages > 0;
    }).length;
  }

  if (check.metrics.errors > 0) {
    check.status = 'FAIL';
  } else if (check.metrics.warnings > 0) {
    check.status = 'WARN';
  } else {
    check.status = 'PASS';
  }

  return check;
}

function readPhpStanLevel() {
  const configFile = path.join(ROOT, PHPSTAN_CONFIG);
  if (!fs.existsSync(configFile)) return null;
  try {
    const content = fs.readFileSync(configFile, 'utf8');
    const m = content.match(/^\s*level\s*:\s*(\d+)/m);
    return m ? parseInt(m[1], 10) : null;
  } catch { return null; }
}

function checkPhpStan(timeout) {
  const check = {
    id: 'phpstan',
    label: 'PHPStan static analysis',
    severity: 'critical',
    status: 'SKIP',
    command: 'vendor/bin/phpstan analyse --configuration=phpstan.neon --error-format=json --no-progress --memory-limit=512M',
    durationMs: 0,
    metrics: {
      errors: 0,
      filesWithErrors: 0,
      level: null,
    },
    source: 'phpstan.neon (paths)',
    details: [],
  };

  check.metrics.level = readPhpStanLevel();

  const candidates = [
    'vendor/bin/phpstan analyse --configuration=phpstan.neon --error-format=json --no-progress --memory-limit=512M',
    'vendor\\bin\\phpstan.bat analyse --configuration=phpstan.neon --error-format=json --no-progress --memory-limit=512M',
  ];

  let res = null;
  let parsedJson = null;
  for (const cmd of candidates) {
    const attempt = runWithWindowsFallback(cmd, timeout);
    if (attempt.commandMissing) continue;
    const parsed = extractJsonPayload(attempt.stdout) || extractJsonPayload(attempt.combined);
    if (parsed && typeof parsed === 'object') {
      res = attempt;
      parsedJson = parsed;
      check.command = cmd;
      break;
    }
    if (!res) {
      res = attempt;
      check.command = cmd;
    }
  }

  if (!res) {
    check.details.push('phpstan non eseguibile nel contesto corrente.');
    return check;
  }

  check.durationMs = res.durationMs;
  const json = parsedJson || extractJsonPayload(res.stdout) || extractJsonPayload(res.combined);

  if (!json || typeof json !== 'object') {
    check.status = res.exitCode === 0 ? 'PASS' : 'WARN';
    check.details.push('Output PHPStan non parsabile.');
    return check;
  }

  const files = json.files || {};
  const fileNames = Object.keys(files);
  const totalErrors = (json.totals && typeof json.totals.errors === 'number')
    ? json.totals.errors
    : fileNames.reduce((acc, name) => acc + ((files[name].messages || []).length), 0);

  check.metrics.errors = totalErrors;
  check.metrics.filesWithErrors = fileNames.length;

  check.status = totalErrors > 0 ? 'FAIL' : 'PASS';
  return check;
}

function checkComposerAudit(timeout) {
  const check = {
    id: 'composer_audit',
    label: 'Composer security audit',
    severity: 'critical',
    status: 'SKIP',
    command: 'composer audit --format=json',
    durationMs: 0,
    metrics: {
      advisories: 0,
      critical: 0,
      high: 0,
      medium: 0,
      low: 0,
      abandoned: 0,
    },
    source: 'tutto il progetto',
    details: [],
  };

  const res = runWithWindowsFallback('composer audit --format=json', timeout);
  if (res.commandMissing) {
    check.details.push('composer non eseguibile nel contesto corrente.');
    return check;
  }

  check.durationMs = res.durationMs;
  const json = extractJsonPayload(res.stdout) || extractJsonPayload(res.combined);

  if (!json || typeof json !== 'object') {
    check.status = res.exitCode === 0 ? 'PASS' : 'WARN';
    check.details.push('Output composer audit non parsabile.');
    return check;
  }

  const advisories = json.advisories || {};
  let total = 0;
  const sev = { critical: 0, high: 0, medium: 0, low: 0 };

  Object.values(advisories).forEach((list) => {
    if (!Array.isArray(list)) return;
    total += list.length;
    list.forEach((a) => {
      const level = String(a.severity || '').toLowerCase();
      if (level in sev) sev[level] += 1;
    });
  });

  check.metrics.advisories = total;
  check.metrics.critical = sev.critical;
  check.metrics.high = sev.high;
  check.metrics.medium = sev.medium;
  check.metrics.low = sev.low;

  if (sev.critical > 0 || sev.high > 0) check.status = 'FAIL';
  else if (total > 0) check.status = 'WARN';
  else check.status = 'PASS';

  check.metrics.abandoned = Object.keys(json.abandoned || {}).length;
  if (check.metrics.abandoned > 0 && check.status === 'PASS') check.status = 'WARN';

  return check;
}

function checkNpmAudit(timeout) {
  const check = {
    id: 'npm_audit',
    label: 'NPM security audit',
    severity: 'critical',
    status: 'SKIP',
    command: 'npm audit --json --omit=dev',
    durationMs: 0,
    metrics: {
      total: 0,
      critical: 0,
      high: 0,
      moderate: 0,
      low: 0,
      info: 0,
    },
    source: 'tutto il progetto',
    details: [],
  };

  const res = runWithWindowsFallback('npm audit --json --omit=dev', timeout);
  if (res.commandMissing) {
    check.details.push('npm non eseguibile nel contesto corrente.');
    return check;
  }

  check.durationMs = res.durationMs;
  const json = extractJsonPayload(res.stdout) || extractJsonPayload(res.combined);
  if (!json || typeof json !== 'object') {
    check.status = res.exitCode === 0 ? 'PASS' : 'WARN';
    check.details.push('Output npm audit non parsabile.');
    return check;
  }

  const v = (json.metadata && json.metadata.vulnerabilities) || {};
  check.metrics.total = v.total || 0;
  check.metrics.critical = v.critical || 0;
  check.metrics.high = v.high || 0;
  check.metrics.moderate = v.moderate || 0;
  check.metrics.low = v.low || 0;
  check.metrics.info = v.info || 0;

  if (check.metrics.critical > 0 || check.metrics.high > 0) check.status = 'FAIL';
  else if (check.metrics.total > 0) check.status = 'WARN';
  else check.status = 'PASS';

  return check;
}

function checkLargePhpFiles(scope) {
  const check = {
    id: 'php_large_files',
    label: 'Large PHP files (>1000 LOC)',
    severity: 'minor',
    status: 'PASS',
    command: 'internal file scan',
    durationMs: 0,
    metrics: {
      filesOver1000: 0,
      maxLoc: 0,
    },
    source: 'phpstan.neon (paths)',
    details: [],
  };

  const t0 = Date.now();
  const files = collectPhpFilesForScope(scope);
  let maxLoc = 0;
  const offenders = [];

  for (const file of files) {
    const content = fs.readFileSync(file, 'utf8');
    const loc = content.split('\n').length;
    if (loc > maxLoc) maxLoc = loc;
    if (loc > 1000) {
      offenders.push({ file: path.relative(ROOT, file).replace(/\\/g, '/'), loc });
    }
  }

  check.durationMs = Date.now() - t0;
  check.metrics.filesOver1000 = offenders.length;
  check.metrics.maxLoc = maxLoc;

  if (offenders.length > 0) {
    check.status = 'WARN';
    check.details = offenders.slice(0, 20).map((o) => `${o.file} (${o.loc} LOC)`);
    if (offenders.length > 20) check.details.push(`... altri ${offenders.length - 20} file`);
  }

  return check;
}

function checkTodoFixme(scope) {
  const check = {
    id: 'todo_fixme',
    label: 'TODO / FIXME in PHP files',
    severity: 'minor',
    status: 'PASS',
    command: 'internal file scan',
    durationMs: 0,
    metrics: { total: 0, todo: 0, fixme: 0, filesWithMarkers: 0 },
    source: 'phpstan.neon (paths)',
    details: [],
  };

  const t0 = Date.now();
  const files = collectPhpFilesForScope(scope);
  const TODO_RE = /\bTODO\b/g;
  const FIXME_RE = /\bFIXME\b/g;
  let totalTodo = 0;
  let totalFixme = 0;
  const offenders = [];

  for (const file of files) {
    const content = fs.readFileSync(file, 'utf8');
    const todoCount = (content.match(TODO_RE) || []).length;
    const fixmeCount = (content.match(FIXME_RE) || []).length;
    if (todoCount > 0 || fixmeCount > 0) {
      totalTodo += todoCount;
      totalFixme += fixmeCount;
      offenders.push({ file: path.relative(ROOT, file).replace(/\\/g, '/'), todo: todoCount, fixme: fixmeCount });
    }
  }

  check.durationMs = Date.now() - t0;
  check.metrics.todo = totalTodo;
  check.metrics.fixme = totalFixme;
  check.metrics.total = totalTodo + totalFixme;
  check.metrics.filesWithMarkers = offenders.length;
  // FIXME implies something broken/urgent; TODO alone is neutral
  if (totalFixme > 0) check.status = 'WARN';
  check.details = offenders.slice(0, 20).map((o) => `${o.file} (TODO: ${o.todo}, FIXME: ${o.fixme})`);
  if (offenders.length > 20) check.details.push(`... altri ${offenders.length - 20} file`);

  return check;
}

function getGitInfo() {
  const branch = runWithWindowsFallback('git rev-parse --abbrev-ref HEAD', 10000);
  const commit = runWithWindowsFallback('git rev-parse --short HEAD', 10000);
  return {
    branch: branch.exitCode === 0 ? branch.stdout.trim() : null,
    commit: commit.exitCode === 0 ? commit.stdout.trim() : null,
  };
}

function computeSummary(checks) {
  const summary = {
    totalChecks: checks.length,
    pass: 0,
    warn: 0,
    fail: 0,
    skip: 0,
    verdict: 'PASS',
    criticalFailures: 0,
    majorFailures: 0,
  };

  checks.forEach((c) => {
    const key = c.status.toLowerCase();
    if (summary[key] !== undefined) summary[key] += 1;
    if (c.status === 'FAIL' && c.severity === 'critical') summary.criticalFailures += 1;
    if (c.status === 'FAIL' && c.severity === 'major') summary.majorFailures += 1;
  });

  if (summary.criticalFailures > 0) summary.verdict = 'FAIL';
  else if (summary.fail > 0 || summary.warn > 0) summary.verdict = 'WARN';

  return summary;
}

function escHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function writeJson(report, outBase) {
  const file = `${outBase}.json`;
  fs.mkdirSync(path.dirname(file), { recursive: true });
  fs.writeFileSync(file, JSON.stringify(report, null, 2), 'utf8');
  return file;
}

function writeHtml(report, outBase) {
  const file = `${outBase}.html`;
  const summary = report.summary;
  const color = summary.verdict === 'PASS' ? '#27ae60' : summary.verdict === 'WARN' ? '#e67e22' : '#c0392b';

  const rows = report.checks.map((c) => {
    const statusColor = c.status === 'PASS' ? '#27ae60' : c.status === 'WARN' ? '#e67e22' : c.status === 'FAIL' ? '#c0392b' : '#7f8c8d';
    const metrics = Object.entries(c.metrics || {})
      .map(([k, v]) => {
        const value = (v === null || v === undefined || v === '') ? 0 : v;
        return `<div><strong>${escHtml(k)}</strong>: ${escHtml(value)}</div>`;
      })
      .join('');
    const source = c.source ? `<div><code>Sorgente: ${escHtml(c.source)}</code></div>` : '';
    const details = (c.details || []).length > 0
      ? `<details><summary>Dettagli</summary><ul>${c.details.map((n) => `<li><code>${escHtml(n)}</code></li>`).join('')}</ul></details>`
      : '';

    return `
      <tr>
        <td><span style="color:${statusColor};font-weight:bold">${c.status}</span></td>
        <td>${escHtml(c.id)}</td>
        <td>${escHtml(c.label)}</td>
        <td>${escHtml(c.severity)}</td>
        <td>${metrics}</td>
        <td>${c.durationMs} ms</td>
        <td><code>${escHtml(c.command)}</code>${source}${details}</td>
      </tr>`;
  }).join('');

  const html = `<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DLI Static Quality Report</title>
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
summary{cursor:pointer}
</style>
</head>
<body>
  <h1>DLI Static Quality Report</h1>
  <div class="meta">
    Scansione: <strong>${new Date(report.scannedAt).toLocaleString('it-IT')}</strong>
    &nbsp;|&nbsp; Branch: <strong>${escHtml(report.git.branch || 'n/a')}</strong>
    &nbsp;|&nbsp; Commit: <strong>${escHtml(report.git.commit || 'n/a')}</strong>
    &nbsp;|&nbsp; Verdict: <strong style="color:${color}">${summary.verdict}</strong>
  </div>
  <div class="cards">
    <div class="card"><div class="num">${summary.totalChecks}</div><div>Checks</div></div>
    <div class="card"><div class="num" style="color:#27ae60">${summary.pass}</div><div>Pass</div></div>
    <div class="card"><div class="num" style="color:#e67e22">${summary.warn}</div><div>Warn</div></div>
    <div class="card"><div class="num" style="color:#c0392b">${summary.fail}</div><div>Fail</div></div>
    <div class="card"><div class="num" style="color:#7f8c8d">${summary.skip}</div><div>Skip</div></div>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Status</th>
        <th>ID</th>
        <th>Check</th>
        <th>Severity</th>
        <th>Metrics</th>
        <th>Duration</th>
        <th>Command / Notes</th>
      </tr>
    </thead>
    <tbody>${rows}</tbody>
  </table>
</body>
</html>`;

  fs.mkdirSync(path.dirname(file), { recursive: true });
  fs.writeFileSync(file, html, 'utf8');
  return file;
}

function printConsole(report) {
  const s = report.summary;
  const sep = '='.repeat(68);
  console.log('\n' + sep);
  console.log('DLI Static Quality Scanner');
  console.log(sep);
  console.log(`Branch: ${report.git.branch || 'n/a'} | Commit: ${report.git.commit || 'n/a'}`);
  console.log(`Scanned at: ${new Date(report.scannedAt).toLocaleString('it-IT')}`);
  console.log(sep);
  report.checks.forEach((c) => {
    const marker = c.status === 'PASS' ? '✓' : c.status === 'WARN' ? '!' : c.status === 'FAIL' ? '✗' : '-';
    console.log(`${marker} [${c.status}] ${c.id} (${c.durationMs}ms)`);
  });
  console.log(sep);
  console.log(`PASS ${s.pass} | WARN ${s.warn} | FAIL ${s.fail} | SKIP ${s.skip}`);
  console.log(`VERDICT: ${s.verdict}`);
  console.log(sep + '\n');
}

function main() {
  const opts = parseArgs(process.argv);
  const scope = loadPhpStanScope();

  const checks = [
    checkPhpSyntax(opts.timeout, scope),
    checkPhpcs(opts.timeout, scope),
    checkPhpStan(opts.timeout),
    checkComposerAudit(opts.timeout),
    checkNpmAudit(opts.timeout),
    checkLargePhpFiles(scope),
    checkTodoFixme(scope),
  ];

  const report = {
    scannedAt: new Date().toISOString(),
    git: getGitInfo(),
    summary: computeSummary(checks),
    checks,
  };

  const jsonFile = writeJson(report, opts.out);
  const htmlFile = writeHtml(report, opts.out);

  printConsole(report);
  console.log(`JSON report: ${jsonFile}`);
  console.log(`HTML report: ${htmlFile}`);

  if (opts.gate && report.summary.verdict === 'FAIL') {
    process.exit(1);
  }
}

main();
