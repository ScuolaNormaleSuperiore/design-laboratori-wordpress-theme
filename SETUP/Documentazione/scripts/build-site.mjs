import childProcess from 'node:child_process';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));
const toolingDirectory = path.resolve(scriptDirectory, '..');
const root = path.resolve(toolingDirectory, '../..');
const source = path.join(root, 'build', 'documentazione-source');
const destination = path.join(root, 'build', 'documentazione');

function run(command, argumentsList, options = {}) {
	const result = childProcess.spawnSync(command, argumentsList, { stdio: 'inherit', ...options });
	if (result.error) throw result.error;
	if (result.status !== 0) throw new Error(`${command} terminato con exit ${result.status}`);
}

run(process.execPath, [path.join(scriptDirectory, 'build.mjs')]);
const bundlerArguments = ['exec', 'jekyll', 'build', '--config', path.join(toolingDirectory, '_config.yml'), '--source', source, '--destination', destination];
if (process.platform === 'win32') {
	const quote = (argument) => `'${argument.replace(/'/g, "''")}'`;
	const command = ['& bundle', ...bundlerArguments.map(quote)].join(' ');
	console.log(`Jekyll Windows: ${command}`);
	run('powershell.exe', ['-NoProfile', '-NonInteractive', '-Command', command], { cwd: toolingDirectory });
} else {
	run('bundle', bundlerArguments, { cwd: toolingDirectory });
}
console.log(`Documentazione HTML pronta: ${destination}`);
