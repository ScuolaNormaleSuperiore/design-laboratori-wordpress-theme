import fs from 'node:fs';
import http from 'node:http';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const mime = {
	'.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8',
	'.js': 'text/javascript; charset=utf-8', '.mjs': 'text/javascript; charset=utf-8',
	'.json': 'application/json', '.svg': 'image/svg+xml', '.png': 'image/png',
	'.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.woff': 'font/woff',
	'.woff2': 'font/woff2', '.pdf': 'application/pdf', '.ico': 'image/x-icon',
};

/** Serve a generated static directory on localhost, never the repository. */
export function createPreviewServer(directory, basePath = '/') {
	const root = fs.realpathSync(directory);
	if (!/^\/(?:[a-zA-Z0-9_-]+\/)*$/.test(basePath)) {
		throw new Error('Il percorso base deve iniziare e terminare con /');
	}
	return http.createServer((request, response) => {
		try {
			if (!['GET', 'HEAD'].includes(request.method)) {
				response.writeHead(405).end();
				return;
			}
			const pathname = decodeURIComponent(new URL(request.url, 'http://localhost').pathname);
			if (!pathname.startsWith(basePath)) {
				response.writeHead(404).end();
				return;
			}
			const relative = pathname.slice(basePath.length);
			if (relative.split('/').some((part) => part.startsWith('.') || part.includes('\\'))) {
				response.writeHead(403).end();
				return;
			}
			const target = path.resolve(root, relative || 'index.html');
			const actual = fs.realpathSync(target);
			if (!actual.startsWith(root + path.sep) || !fs.statSync(actual).isFile()) {
				response.writeHead(403).end();
				return;
			}
			response.writeHead(200, { 'Content-Type': mime[path.extname(actual)] || 'application/octet-stream', 'Cache-Control': 'no-store' });
			response.end(request.method === 'HEAD' ? undefined : fs.readFileSync(actual));
		} catch {
			response.writeHead(404).end();
		}
	});
}

if (process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
	const outputName = process.env.DLI_DOCS_OUTPUT;
	if (!outputName || !/^[a-zA-Z0-9_-]+$/.test(outputName)) {
		console.error('Impostare DLI_DOCS_OUTPUT con il nome semplice di una directory HTML sotto SETUP/Documentazione/.build. Il generatore Jekyll non è ancora configurato.');
		process.exit(1);
	}
	const output = fileURLToPath(new URL(`../.build/${outputName}/`, import.meta.url));
	if (!fs.existsSync(path.join(output, 'index.html'))) {
		console.error(`Anteprima non disponibile: manca index.html in ${output}. Generare prima il sito statico; il generatore Jekyll non è ancora configurato.`);
		process.exit(1);
	}
	const port = Number(process.env.DLI_DOCS_PORT || 8085);
	if (!Number.isInteger(port) || port < 1024 || port > 65535) {
		throw new Error('DLI_DOCS_PORT deve essere una porta fra 1024 e 65535');
	}
	const basePath = process.env.DLI_DOCS_BASE_PATH || '/';
	const server = createPreviewServer(output, basePath);
	server.on('error', (error) => {
		console.error(`Anteprima non avviabile sulla porta ${port}: ${error.message}`);
		process.exitCode = 1;
	});
	server.listen(port, '127.0.0.1', () => console.log(`Anteprima ${outputName}: http://127.0.0.1:${port}${basePath} — Ctrl+C per arrestare; rigenerare dopo le modifiche.`));
}
