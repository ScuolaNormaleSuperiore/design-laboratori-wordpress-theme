import childProcess from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const scripts = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(scripts, '../../..');
const source = path.join(root, 'build', 'documentazione');
const packageDirectory = path.join(root, 'build', 'documentazione-package');
const archive = path.join(packageDirectory, 'design-laboratori-documentazione.zip');

function run(command, argumentsList, options = {}) {
	const result = childProcess.spawnSync(command, argumentsList, { stdio: 'inherit', ...options });
	if (result.error) throw result.error;
	if (result.status !== 0) throw new Error(`${command} terminato con exit ${result.status}`);
}

run(process.execPath, [path.join(scripts, 'build-site.mjs')]);
fs.rmSync(packageDirectory, { recursive: true, force: true });
fs.mkdirSync(packageDirectory, { recursive: true });

if (process.platform === 'win32') {
	const quote = (value) => `'${value.replace(/'/g, "''")}'`;
	run('powershell.exe', ['-NoProfile', '-NonInteractive', '-Command', `Compress-Archive -Path ${(quote(path.join(source, '*')))} -DestinationPath ${quote(archive)} -Force`]);
} else {
	run('zip', ['-r', archive, '.'], { cwd: source });
}

console.log(`Pacchetto documentazione pronto: ${archive}`);
