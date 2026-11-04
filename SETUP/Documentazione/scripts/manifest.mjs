import fs from 'node:fs';
import path from 'node:path';

/** Validate a repository-relative, explicitly selected documentation source. */
export function sourceFile(source, root, tracked) {
	if (typeof source !== 'string' || !source.startsWith('DOC/') || /[\\:%\x00-\x1f]/.test(source) || source.split('/').some((part) => !part || part === '.' || part === '..')) {
		throw new Error(`Sorgente non ammessa: ${source}`);
	}
	let current = root;
	for (const part of source.split('/')) {
		current = path.join(current, part);
		if (!fs.existsSync(current)) {
			throw new Error(`Sorgente non esistente: ${source}`);
		}
		if (fs.lstatSync(current).isSymbolicLink()) {
			throw new Error(`Sorgente symlink non ammessa: ${source}`);
		}
	}
	if (!fs.statSync(current).isFile()) {
		throw new Error(`La sorgente non è un file: ${source}`);
	}
	if (!tracked.has(source)) {
		throw new Error(`Sorgente non versionata: ${source}`);
	}
	return current;
}

/** Produce a validated selection without scanning or copying unlisted files. */
export function validateManifest(manifest, root, tracked) {
	if (!manifest || manifest.version !== 1) {
		throw new Error('Versione del manifest non supportata');
	}
	for (const key of ['pages', 'assets', 'github_documents']) {
		if (!Array.isArray(manifest[key])) {
			throw new Error(`Elenco manifest mancante: ${key}`);
		}
	}
	const outputs = new Set();
	const sources = new Set();
	const selection = { pages: [], assets: [], github_documents: [] };
	for (const kind of ['pages', 'assets']) {
		for (const entry of manifest[kind]) {
			if (!entry || typeof entry !== 'object') {
				throw new Error('Voce manifest non valida');
			}
			const absolute = sourceFile(entry.source, root, tracked);
			const pattern = kind === 'pages'
				? /^(?:[a-z0-9_-]+\/)*[a-z0-9_-]+\.md$/
				: /^media\/(?:[a-z0-9_-]+\/)*[a-z0-9_-]+\.(?:png|jpg|jpeg|gif|webp|svg|pdf)$/;
			if (typeof entry.target !== 'string' || !pattern.test(entry.target)) {
				throw new Error(`Destinazione non ammessa: ${entry.target}`);
			}
			if (kind === 'pages' && (!entry.source.endsWith('.md') || typeof entry.title !== 'string' || !entry.title.trim())) {
				throw new Error(`Pagina senza titolo o sorgente Markdown: ${entry.source}`);
			}
			if (kind === 'assets' && path.posix.extname(entry.source).toLowerCase() !== path.posix.extname(entry.target)) {
				throw new Error(`Estensione asset diversa nella destinazione: ${entry.target}`);
			}
			const url = entry.target.replace(/\.md$/, '.html').toLowerCase();
			if (outputs.has(url) || sources.has(entry.source)) {
				throw new Error(`Sorgente o URL duplicato: ${entry.target}`);
			}
			outputs.add(url);
			sources.add(entry.source);
			selection[kind].push({ ...entry, absolute });
		}
	}
	if (!selection.pages.some((entry) => entry.target === 'index.md')) {
		throw new Error('Il manifest deve selezionare una pagina index.md');
	}
	for (const source of manifest.github_documents) {
		sourceFile(source, root, tracked);
		if (!source.endsWith('.md') || sources.has(source)) {
			throw new Error(`Sorgente GitHub duplicata o non Markdown: ${source}`);
		}
		sources.add(source);
		selection.github_documents.push(source);
	}
	return selection;
}
