import childProcess from 'node:child_process';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));

function run(command, argumentsList, options = {}) {
	const result = childProcess.spawnSync(command, argumentsList, { stdio: 'inherit', ...options });
	if (result.error) throw result.error;
	if (result.status !== 0) throw new Error(`${command} terminato con exit ${result.status}`);
}

run(process.execPath, [path.join(scriptDirectory, 'build-site.mjs')]);
run(process.execPath, [path.join(scriptDirectory, 'preview.mjs')], { env: { ...process.env, DLI_DOCS_OUTPUT: 'documentazione' } });
