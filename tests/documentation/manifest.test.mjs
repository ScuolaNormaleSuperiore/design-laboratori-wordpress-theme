import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import test from 'node:test';
import { validateManifest } from '../../SETUP/Documentazione/scripts/manifest.mjs';

function fixture(t) {
	const root = fs.mkdtempSync(path.join(os.tmpdir(), 'dli-documentation-'));
	fs.mkdirSync(path.join(root, 'DOC', 'Guide'), { recursive: true });
	fs.writeFileSync(path.join(root, 'DOC', 'Guide', 'Guida con accènti.md'), '# Titolo\n');
	fs.writeFileSync(path.join(root, 'DOC', 'Guide', 'indice.md'), '# Indice\n');
	fs.writeFileSync(path.join(root, 'DOC', 'Guide', 'schema.pdf'), '%PDF-1.7\n');
	const tracked = new Set(['DOC/Guide/Guida con accènti.md', 'DOC/Guide/indice.md', 'DOC/Guide/schema.pdf']);
	const manifest = {
		version: 2,
		pages: [{ source: 'DOC/Guide/Guida con accènti.md', target: 'index.md', title: 'Guida', description: 'Descrizione della guida.', group: 'Guide', order: 10 }],
		assets: [{ source: 'DOC/Guide/schema.pdf', target: 'media/schema.pdf' }],
		github_documents: [],
	};
	t.after(() => fs.rmSync(root, { recursive: true, force: true }));
	return { root, tracked, manifest };
}

test('accepts tracked names with spaces/accents and an explicitly selected PDF', (t) => {
	const { root, tracked, manifest } = fixture(t);
	const result = validateManifest(manifest, root, tracked);
	assert.equal(result.pages[0].source, 'DOC/Guide/Guida con accènti.md');
	assert.equal(result.assets.length, 1);
});

for (const source of ['../secret.md', '/etc/passwd', 'DEV/plan.md', '.codex/secret.md', 'DOC/../README.md', 'DOC\\secret.md', 'DOC/%2e%2e/secret.md']) {
	test(`rejects forbidden source ${source}`, (t) => {
		const { root, tracked, manifest } = fixture(t);
		manifest.pages[0].source = source;
		assert.throws(() => validateManifest(manifest, root, tracked), /sorgente/i);
	});
}

test('rejects untracked local documentation', (t) => {
	const { root, manifest } = fixture(t);
	assert.throws(() => validateManifest(manifest, root, new Set()), /versionat/i);
});

test('rejects missing sources even if listed as tracked', (t) => {
	const { root, tracked, manifest } = fixture(t);
	fs.unlinkSync(path.join(root, manifest.pages[0].source));
	assert.throws(() => validateManifest(manifest, root, tracked), /esist/i);
});

test('rejects duplicate output URLs, also with different case', (t) => {
	const { root, tracked, manifest } = fixture(t);
	manifest.pages.push({ ...manifest.pages[0] });
	assert.throws(() => validateManifest(manifest, root, tracked), /duplicat/i);
});

for (const target of ['../escape.md', '/index.md', 'directory/config.json', 'Bad name.md', 'media/file.php']) {
	test(`rejects unsafe output ${target}`, (t) => {
		const { root, tracked, manifest } = fixture(t);
		manifest.pages[0].target = target;
		assert.throws(() => validateManifest(manifest, root, tracked), /destinazione/i);
	});
}

test('rejects symlinked source directories (junction on Windows)', (t) => {
	const { root, tracked, manifest } = fixture(t);
	fs.symlinkSync(path.join(root, 'DOC', 'Guide'), path.join(root, 'DOC', 'Linked'), 'junction');
	manifest.pages[0].source = 'DOC/Linked/Guida con accènti.md';
	tracked.add(manifest.pages[0].source);
	assert.throws(() => validateManifest(manifest, root, tracked), /symlink/i);
});

test('requires an explicit home page and a supported manifest version', (t) => {
	const { root, tracked, manifest } = fixture(t);
	manifest.pages[0].target = 'guida.md';
	assert.throws(() => validateManifest(manifest, root, tracked), /index/i);
	manifest.version = 1;
	assert.throws(() => validateManifest(manifest, root, tracked), /version/i);
});

test('permits only tracked Markdown DOC files as GitHub-only documents', (t) => {
	const { root, tracked, manifest } = fixture(t);
	manifest.github_documents = ['DEV/plan.md'];
	assert.throws(() => validateManifest(manifest, root, tracked), /sorgente/i);
});

test('requires navigation metadata for every published page', (t) => {
	const { root, tracked, manifest } = fixture(t);
	delete manifest.pages[0].group;
	assert.throws(() => validateManifest(manifest, root, tracked), /metadati/i);
});

test('derives a clean Jekyll URL from the declared target', (t) => {
	const { root, tracked, manifest } = fixture(t);
	manifest.pages[0].target = 'guide/guida.md';
	manifest.pages[0].order = 20;
	manifest.pages.push({ source: 'DOC/Guide/indice.md', target: 'index.md', title: 'Indice', description: 'Pagina iniziale.', group: 'Guide', order: 10 });
	assert.equal(validateManifest(manifest, root, tracked).pages[0].url, '/guide/guida');
});
