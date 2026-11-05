import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../..');
const output = path.join(root, 'build', 'documentazione');
const baseurl = (process.env.DOCS_BASEURL || '').replace(/\/+$/u, '');

function withoutBaseurl(pathname) {
	if (!baseurl || !pathname.startsWith('/')) return pathname;
	if (pathname === baseurl) return '/';
	return pathname.startsWith(`${baseurl}/`) ? pathname.slice(baseurl.length) : pathname;
}

function htmlFiles(directory) {
	return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
		const target = path.join(directory, entry.name);
		return entry.isDirectory() ? htmlFiles(target) : entry.name.endsWith('.html') ? [target] : [];
	});
}

function pageFor(target) {
	if (fs.existsSync(target) && fs.statSync(target).isDirectory()) return path.join(target, 'index.html');
	return target;
}

if (!fs.existsSync(path.join(output, 'index.html'))) throw new Error('Output documentazione mancante: eseguire prima npm run docs:build.');
const pages = htmlFiles(output);
const ids = new Map(pages.map((page) => [page, new Set([...fs.readFileSync(page, 'utf8').matchAll(/\sid=["']([^"']+)["']/gu)].map((match) => match[1]))]));
const problems = [];

for (const page of pages) {
	const html = fs.readFileSync(page, 'utf8');
	for (const match of html.matchAll(/\s(?:href|src)=["']([^"']+)["']/gu)) {
		const href = match[1];
		if (/^(?:[a-z][a-z0-9+.-]*:|\/\/)/iu.test(href)) continue;
		const [rawPathname, anchor] = href.split('#', 2);
		const pathname = withoutBaseurl(rawPathname);
		const target = pathname.startsWith('/') ? pageFor(path.join(output, pathname)) : pageFor(path.resolve(path.dirname(page), pathname || '.'));
		if (!fs.existsSync(target)) { problems.push(`${path.relative(output, page)} → ${href}: destinazione mancante`); continue; }
		if (anchor && path.extname(target) === '.html' && !ids.get(target)?.has(anchor)) problems.push(`${path.relative(output, page)} → ${href}: ancora mancante`);
	}
}

if (problems.length) throw new Error(`Controllo documentazione fallito:\n${problems.join('\n')}`);
console.log(`docs:check OK: ${pages.length} pagine, link e ancore interni validi.`);
