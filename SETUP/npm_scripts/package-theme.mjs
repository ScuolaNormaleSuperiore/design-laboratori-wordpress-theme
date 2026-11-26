import { spawnSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { tmpdir } from 'node:os';
import { fileURLToPath } from 'node:url';

export const themeSlug = 'design-laboratori-wordpress-theme';
const themeRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const developmentDirectories = new Set([
	'node_modules', 'tests', 'test', 'examples', 'demo', 'docs', 'src', 'types', 'compiled',
]);
const runtimeDirectories = new Set([
	'inc', 'page-templates', 'template-parts',
	'assets/bootstrap-italia', 'assets/css', 'assets/fonts', 'assets/html',
	'assets/icons', 'assets/img', 'assets/js', 'assets/placeholders', 'assets/svg',
]);
const runtimeExtensions = new Set([
	'.php', '.css', '.js', '.html', '.svg', '.png', '.jpg', '.jpeg', '.gif',
	'.webp', '.avif', '.ico', '.woff', '.woff2', '.ttf', '.eot', '.otf', '.mo',
]);
const csvFiles = new Set([
	'SETUP/CSV_Import/people-example.csv',
	'SETUP/CSV_Import/pages-example-it.csv',
	'SETUP/CSV_Import/pages-example-en.csv',
	'SETUP/CSV_Import/pages-model.csv',
]);

/** Select runtime files only; paths use forward slashes on every platform. */
export function isRuntimeFile(relativePath) {
	const parts = relativePath.split('/');
	const name = parts.at(-1);
	if (parts.some((part) => part.startsWith('.') || developmentDirectories.has(part))) {
		return false;
	}
	if (parts.length === 1) {
		return name.endsWith('.php') || ['style.css', 'screenshot.png', 'LICENSE', 'CREDITS.txt'].includes(name);
	}
	if (csvFiles.has(relativePath)) return true;
	if (parts[0] === 'languages') return name.endsWith('.mo') || name.endsWith('.l10n.php');
	if (![...runtimeDirectories].some((directory) => relativePath.startsWith(`${directory}/`))) return false;
	// Keep notices and font licences alongside bundled third-party assets.
	if (/^(licen[sc]e|copying|notice|ofl)(\.|$)/i.test(name)) return true;
	if (/^(example|tests?)([.-]|$)/i.test(name)) return false;
	return runtimeExtensions.has(path.posix.extname(name));
}

export function collectRuntimeFiles(root) {
	const files = [];
	function visit(directory = '') {
		for (const entry of fs.readdirSync(path.join(root, directory), { withFileTypes: true })) {
			const relativePath = directory ? `${directory}/${entry.name}` : entry.name;
			// Never follow links into private or external directories.
			if (entry.isSymbolicLink()) continue;
			if (entry.isDirectory()) {
				if ([...runtimeDirectories, 'languages', 'SETUP/CSV_Import'].some((allowed) => allowed === relativePath || allowed.startsWith(`${relativePath}/`) || relativePath.startsWith(`${allowed}/`))) {
					if (!entry.name.startsWith('.') && !developmentDirectories.has(entry.name)) visit(relativePath);
				}
			} else if (entry.isFile() && isRuntimeFile(relativePath)) {
				files.push(relativePath);
			}
		}
	}
	visit();
	return files.sort();
}

export function packageTheme(root = themeRoot) {
	const style = fs.readFileSync(path.join(root, 'style.css'), 'utf8');
	const version = style.match(/^Version:\s*([0-9]+\.[0-9]+\.[0-9]+(?:-[\w.-]+)?)\s*$/m)?.[1];
	if (!version) throw new Error('Versione non valida in style.css.');
	const files = collectRuntimeFiles(root);
	for (const required of ['style.css', 'index.php', 'functions.php', 'LICENSE', ...csvFiles,
		'assets/bootstrap-italia/css/bootstrap-italia.min.css',
		'assets/bootstrap-italia/js/bootstrap-italia.bundle.min.js',
		'assets/css/bootstrap-italia-custom.min.css',
		'inc/vendor/CMB2/init.php']) {
		if (!files.includes(required)) throw new Error(`File necessario mancante: ${required}`);
	}
	const dist = path.join(root, 'dist');
	fs.mkdirSync(dist, { recursive: true });
	// A short temporary path also supports Windows PowerShell's legacy path limit.
	const staging = fs.mkdtempSync(path.join(tmpdir(), 'dli-'));
	const archiveName = `${themeSlug}-${version}.zip`;
	const archive = path.join(dist, archiveName);
	try {
		for (const file of files) {
			const target = path.join(staging, themeSlug, file);
			fs.mkdirSync(path.dirname(target), { recursive: true });
			fs.copyFileSync(path.join(root, file), target);
		}
		// Build first, then replace the previous package only after success.
		const temporaryArchive = path.join(staging, archiveName);
		let result;
		if (process.platform === 'win32') {
			const quote = (value) => `'${value.replace(/'/g, "''")}'`;
			// Explicit '/' entry names keep ZIPs portable with Windows PowerShell 5 too.
			const command = `Add-Type -AssemblyName System.IO.Compression.FileSystem;
				$source = ${quote(path.join(staging, themeSlug))};
				$zip = [System.IO.Compression.ZipFile]::Open(${quote(temporaryArchive)}, 'Create');
				try {
					Get-ChildItem -LiteralPath $source -File -Recurse | ForEach-Object {
						$entryName = '${themeSlug}/' + $_.FullName.Substring($source.Length + 1).Replace([char]92, [char]47);
						[System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $entryName, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null;
					}
				} finally { $zip.Dispose() }`;
			result = spawnSync('powershell.exe', ['-NoProfile', '-NonInteractive', '-Command', `$ErrorActionPreference = 'Stop'; ${command}`], { stdio: 'inherit' });
		} else {
			result = spawnSync('zip', ['-q', '-r', temporaryArchive, themeSlug], { cwd: staging, stdio: 'inherit' });
		}
		if (result.error) throw result.error;
		if (result.status !== 0) throw new Error(`Creazione ZIP fallita (exit ${result.status}).`);
		fs.copyFileSync(temporaryArchive, `${archive}.tmp`);
		fs.renameSync(`${archive}.tmp`, archive);
		console.log(`Tema pronto: ${archive} (${files.length} file)`);
		return archive;
	} finally {
		// Remove only the unique temporary directory created by this invocation.
		fs.rmSync(staging, { recursive: true, force: true });
	}
}

if (process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
	try {
		packageTheme();
	} catch (error) {
		console.error(error.message);
		process.exitCode = 1;
	}
}
