import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import test from 'node:test';
import { prepareDocumentation } from '../../SETUP/Documentazione/scripts/prepare.mjs';

function fixture(t) {
	const root = fs.mkdtempSync(path.join(os.tmpdir(), 'dli-documentation-prepare-'));
	const destination = path.join(root, 'prepared');
	fs.mkdirSync(path.join(root, 'DOC', 'Guide'), { recursive: true });
	fs.mkdirSync(path.join(root, 'DOC', 'Immagini'), { recursive: true });
	fs.writeFileSync(path.join(root, 'DOC', 'Guide', 'indice.md'), '# Indice\n[Guida](guida.md#capitolo)\n');
	fs.writeFileSync(path.join(root, 'DOC', 'Guide', 'guida.md'), '# Guida\n[Codice](../../assets/file.css)\n![Immagine](../Immagini/schema.svg)\n');
	fs.writeFileSync(path.join(root, 'DOC', 'Immagini', 'schema.svg'), '<svg/>\n');
	const manifest = {
		version: 2,
		repository: { name: 'owner/repository', ref: 'dev' },
		pages: [
			{ source: 'DOC/Guide/indice.md', target: 'index.md', title: 'Indice', description: 'Pagina iniziale.', group: 'Guide', order: 10 },
			{ source: 'DOC/Guide/guida.md', target: 'guide/guida.md', title: 'Guida', description: 'Guida di prova.', group: 'Guide', order: 20 },
		],
		assets: [{ source: 'DOC/Immagini/schema.svg', target: 'media/schema.svg' }],
		github_documents: [],
	};
	const tracked = new Set(['DOC/Guide/indice.md', 'DOC/Guide/guida.md', 'DOC/Immagini/schema.svg']);
	t.after(() => fs.rmSync(root, { recursive: true, force: true }));
	return { root, destination, manifest, tracked };
}

test('prepares only manifest-selected files and rewrites selected and GitHub links', (t) => {
	const { root, destination, manifest, tracked } = fixture(t);
	prepareDocumentation({ root, destination, manifest, tracked });
	assert.match(fs.readFileSync(path.join(destination, 'index.md'), 'utf8'), /^---\nlayout: docs\n[\s\S]*permalink: \/\n[\s\S]*\[Guida\]\(guide\/guida\.md#capitolo\)/);
	const guide = fs.readFileSync(path.join(destination, 'guide', 'guida.md'), 'utf8');
	assert.match(guide, /permalink: \/guide\/guida\//);
	assert.doesNotMatch(guide, /^# Guida/m);
	assert.match(guide, /https:\/\/github\.com\/owner\/repository\/blob\/dev\/assets\/file\.css/);
	assert.match(guide, /!\[Immagine\]\(\.\.\/media\/schema\.svg\)/);
	assert.equal(fs.readFileSync(path.join(destination, 'media', 'schema.svg'), 'utf8'), '<svg/>\n');
});

test('requires repository metadata and never writes outside the requested output', (t) => {
	const { root, destination, manifest, tracked } = fixture(t);
	delete manifest.repository;
	assert.throws(() => prepareDocumentation({ root, destination, manifest, tracked }), /Repository/i);
});
