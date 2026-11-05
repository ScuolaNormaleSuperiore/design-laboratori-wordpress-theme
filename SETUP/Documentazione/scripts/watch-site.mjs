import childProcess from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';
import { createPreviewServer } from './preview.mjs';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(scriptDirectory, '../../..');
const output = path.join(root, 'build', 'documentazione');
const port = Number(process.env.DLI_DOCS_PORT || 8085);
const basePath = process.env.DLI_DOCS_BASE_PATH || '/';
const watchedRoots = [path.join(root, 'DOC'), path.resolve(scriptDirectory, '..')];
let rebuildTimer;
let rebuilding = false;
let rebuildPending = false;

function build() {
	const result = childProcess.spawnSync(process.execPath, [path.join(scriptDirectory, 'build-site.mjs')], { stdio: 'inherit' });
	if (result.error) throw result.error;
	if (result.status !== 0) throw new Error(`Build documentazione terminata con exit ${result.status}`);
}

function rebuild() {
	if (rebuilding) {
		rebuildPending = true;
		return;
	}
	rebuilding = true;
	try {
		console.log('\nModifica rilevata: rigenerazione documentazione…');
		build();
		console.log('Documentazione aggiornata. Ricarica la pagina nel browser.');
	} catch (error) {
		console.error(`Build non riuscita: ${error.message}`);
	} finally {
		rebuilding = false;
		if (rebuildPending) {
			rebuildPending = false;
			rebuild();
		}
	}
}

function scheduleRebuild() {
	clearTimeout(rebuildTimer);
	rebuildTimer = setTimeout(rebuild, 200);
}

function watchDirectory(directory) {
	fs.watch(directory, (event, filename) => {
		if (filename && (filename.includes('.jekyll-cache') || filename.includes('vendor'))) return;
		scheduleRebuild();
	});
}

function watch(rootDirectory) {
	try {
		fs.watch(rootDirectory, { recursive: true }, scheduleRebuild);
	} catch (error) {
		if (error.code !== 'ERR_FEATURE_UNAVAILABLE_ON_PLATFORM') throw error;
		const directories = [rootDirectory];
		for (let index = 0; index < directories.length; index += 1) {
			for (const entry of fs.readdirSync(directories[index], { withFileTypes: true })) {
				if (entry.isDirectory() && !['.bundle', '.jekyll-cache', 'vendor'].includes(entry.name)) directories.push(path.join(directories[index], entry.name));
			}
		}
		directories.forEach(watchDirectory);
	}
}

build();
if (!Number.isInteger(port) || port < 1024 || port > 65535) throw new Error('DLI_DOCS_PORT deve essere una porta fra 1024 e 65535');
const server = createPreviewServer(output, basePath);
server.listen(port, '127.0.0.1', () => console.log(`Anteprima con watch: http://127.0.0.1:${port}${basePath} — Ctrl+C per arrestare.`));
watchedRoots.forEach(watch);

process.on('SIGINT', () => {
	clearTimeout(rebuildTimer);
	server.close(() => process.exit(0));
});
