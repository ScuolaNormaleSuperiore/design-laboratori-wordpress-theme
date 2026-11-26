const { test } = require('node:test');
const assert = require('node:assert/strict');
const path = require('node:path');
const fs = require('node:fs');
const os = require('node:os');

const script = import('../../SETUP/npm_scripts/package-theme.mjs');

test('theme package includes runtime dependencies, initial pages, CSV downloads and licences', async () => {
	const { collectRuntimeFiles } = await script;
	const files = new Set(collectRuntimeFiles(path.resolve(__dirname, '../..')));
	for (const file of [
		'functions.php', 'index.php', 'style.css', 'screenshot.png', 'LICENSE', 'CREDITS.txt',
		'inc/vendor/CMB2/init.php', 'inc/vendor/CMB2/includes/CMB2.php',
		'inc/vendor/TGM-Plugin-Activation/class-tgm-plugin-activation.php',
		'assets/html/privacy_it.html', 'assets/html/privacy_en.html',
		'assets/bootstrap-italia/css/bootstrap-italia.min.css',
		'assets/bootstrap-italia/js/bootstrap-italia.bundle.min.js',
		'assets/bootstrap-italia/svg/sprites.svg', 'assets/js/main.js',
		'languages/en_US.mo', 'languages/en_US.l10n.php',
		'SETUP/CSV_Import/people-example.csv', 'SETUP/CSV_Import/pages-example-it.csv',
		'SETUP/CSV_Import/pages-example-en.csv', 'SETUP/CSV_Import/pages-model.csv',
	]) assert.ok(files.has(file), `Missing runtime file: ${file}`);
});

test('theme package excludes development material and backups, including inside runtime directories', async () => {
	const { isRuntimeFile } = await script;
	for (const file of [
		'.env', 'debug.log', 'README.md', 'package.json', 'composer.json',
		'AGENTS/PROJECT.md', 'DEV/private.php', 'tests/unit/test.php',
		'vendor/autoload.php', 'node_modules/library/index.js', 'dist/old.zip',
		'SETUP/Docker/src/uploads.zip', 'SETUP/ACF_Custom_Fields/fields.json',
		'assets/screenshots/homepage.png', 'assets/scss/main.scss',
		'assets/css/compiled/main.css', 'assets/js/main.js.map',
		'inc/vendor/CMB2/example-functions.php',
		'inc/vendor/TGM-Plugin-Activation/plugins/tgm-example-plugin.zip',
		'assets/js/components/fitvids/tests.html', 'inc/vendor/library/tests/test.php',
		'languages/en_US.po', 'languages/en_US.l10n.php.orig',
		'assets/js/.private.js', 'inc/file.php~',
	]) assert.equal(isRuntimeFile(file), false, `Unexpected package file: ${file}`);
	assert.equal(isRuntimeFile('assets/fonts/Roboto/LICENSE.txt'), true);
	assert.equal(isRuntimeFile('inc/vendor/TGM-Plugin-Activation/LICENSE.md'), true);
});

test('an incomplete theme fails before creating an installable package', async () => {
	const { packageTheme } = await script;
	const root = fs.mkdtempSync(path.join(os.tmpdir(), 'dli-package-test-'));
	try {
		fs.writeFileSync(path.join(root, 'style.css'), 'Version: 1.2.3\n');
		assert.throws(() => packageTheme(root), /File necessario mancante: index.php/);
		assert.equal(fs.existsSync(path.join(root, 'dist')), false);
	} finally {
		fs.rmSync(root, { recursive: true, force: true });
	}
});
