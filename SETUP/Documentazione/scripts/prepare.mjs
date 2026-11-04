import childProcess from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';
import { validateManifest } from './manifest.mjs';

const MARKDOWN_LINK = /(!?\[[^\]]*\])\(([^\s)]+)(\s+['"][^'"]*['"])?\)/g;

function githubUrl(repository, ref, source, suffix = '') {
	const encoded_path = source.split('/').map(encodeURIComponent).join('/');
	return `https://github.com/${repository}/blob/${encodeURIComponent(ref)}/${encoded_path}${suffix}`;
}

function splitLinkTarget(target) {
	const match = target.match(/^([^?#]*)([?#].*)?$/);
	return { pathname: match[1], suffix: match[2] || '' };
}

function sourceFromLink(source, link_path) {
	let decoded_path;
	try {
		decoded_path = decodeURIComponent(link_path);
	} catch {
		return null;
	}
	if (!decoded_path || decoded_path.startsWith('/') || decoded_path.includes('\\')) {
		return null;
	}
	const resolved = path.posix.normalize(path.posix.join(path.posix.dirname(source), decoded_path));
	return resolved.startsWith('../') || resolved === '..' ? null : resolved;
}

function relativeTarget(from_target, to_target) {
	const relative = path.posix.relative(path.posix.dirname(from_target), to_target);
	return relative || path.posix.basename(to_target);
}

/** Rewrite selected document links and safely point all other local files to GitHub. */
export function rewriteLinks(markdown, page, selection, repository, ref) {
	const selected = new Map();
	for (const entry of [...selection.pages, ...selection.assets]) {
		selected.set(entry.source, entry.target);
	}
	return markdown.replace(MARKDOWN_LINK, (match, label, target, title = '') => {
		if (/^(?:[a-z][a-z0-9+.-]*:|\/\/|#)/i.test(target)) {
			return match;
		}
		const { pathname, suffix } = splitLinkTarget(target);
		const source = sourceFromLink(page.source, pathname);
		if (!source) {
			return match;
		}
		const selected_target = selected.get(source);
		if (selected_target) {
			return `${label}(${relativeTarget(page.target, selected_target)}${suffix}${title})`;
		}
		return `${label}(${githubUrl(repository, ref, source, suffix)}${title})`;
	});
}

function safeDestination(destination, target) {
	const absolute = path.resolve(destination, ...target.split('/'));
	const relative = path.relative(destination, absolute);
	if (relative.startsWith('..') || path.isAbsolute(relative)) {
		throw new Error(`Destinazione fuori dall'output: ${target}`);
	}
	return absolute;
}

/** Copy the selected documentation into a temporary, generator-independent input. */
export function prepareDocumentation({ root, manifest, tracked, destination }) {
	const selection = validateManifest(manifest, root, tracked);
	if (!manifest.repository || typeof manifest.repository.name !== 'string' || typeof manifest.repository.ref !== 'string') {
		throw new Error('Repository o ref del manifest mancanti');
	}
	const output = path.resolve(destination);
	fs.rmSync(output, { recursive: true, force: true });
	fs.mkdirSync(output, { recursive: true });
	for (const page of selection.pages) {
		const content = fs.readFileSync(page.absolute, 'utf8');
		const target = safeDestination(output, page.target);
		fs.mkdirSync(path.dirname(target), { recursive: true });
		fs.writeFileSync(target, rewriteLinks(content, page, selection, manifest.repository.name, manifest.repository.ref));
	}
	for (const asset of selection.assets) {
		const target = safeDestination(output, asset.target);
		fs.mkdirSync(path.dirname(target), { recursive: true });
		fs.copyFileSync(asset.absolute, target);
	}
	return selection;
}

function trackedFiles(root) {
	const result = childProcess.spawnSync('git', ['-C', root, 'ls-files', '-z', '--', 'DOC'], { encoding: 'utf8' });
	if (result.status !== 0 || typeof result.stdout !== 'string') {
		throw result.error || new Error(`Impossibile leggere i file versionati di DOC (exit ${result.status})`);
	}
	return new Set(result.stdout.split('\0').filter(Boolean));
}

function parseArguments(arguments_list) {
	const result = {};
	for (const argument of arguments_list) {
		const match = argument.match(/^--(manifest|destination)=(.+)$/);
		if (match) {
			result[match[1]] = match[2];
		}
	}
	if (!result.manifest || !result.destination) {
		throw new Error('Uso: node prepare.mjs --manifest=<file> --destination=<directory>');
	}
	return result;
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
	const options = parseArguments(process.argv.slice(2));
	const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../..');
	const manifest = JSON.parse(fs.readFileSync(path.resolve(root, options.manifest), 'utf8'));
	prepareDocumentation({
		root,
		manifest,
		tracked: trackedFiles(root),
		destination: path.resolve(root, options.destination),
	});
}
