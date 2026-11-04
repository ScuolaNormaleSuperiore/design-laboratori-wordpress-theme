import childProcess from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { validateManifest } from './manifest.mjs';
import { prepareDocumentation } from './prepare.mjs';

const scriptDirectory = path.dirname( fileURLToPath( import.meta.url ) );
const toolingDirectory = path.resolve( scriptDirectory, '..' );
const root = path.resolve( toolingDirectory, '../..' );
const sourceDirectory = path.join( root, 'build', 'documentazione-source' );
const bootstrapDirectory = path.join( root, 'node_modules', 'bootstrap-italia', 'dist' );

function copy( from, to ) {
	fs.mkdirSync( path.dirname( to ), { recursive: true } );
	fs.cpSync( from, to, { recursive: true } );
}

function trackedDocuments() {
	const result = childProcess.spawnSync( 'git', [ '-C', root, 'ls-files', '-z', '--', 'DOC' ], { encoding: 'utf8' } );
	if ( result.status !== 0 || typeof result.stdout !== 'string' ) {
		throw new Error( 'Impossibile leggere le guide versionate.' );
	}
	return new Set( result.stdout.split( '\0' ).filter( Boolean ) );
}

function navigation( pages ) {
	const groups = new Map();
	for ( const page of pages ) {
		if ( ! groups.has( page.group ) ) groups.set( page.group, [] );
		groups.get( page.group ).push( { title: page.title, target: page.target, url: page.url, order: page.order } );
	}
	return Array.from( groups, ( [ title, pagesInGroup ] ) => ( {
		title,
		pages: pagesInGroup.sort( ( first, second ) => first.order - second.order ),
	} ) );
}

function searchText( markdown ) {
	return markdown
		.replace( /^---[\s\S]*?---\s*/u, '' )
		.replace( /```[\s\S]*?```/gu, ' ' )
		.replace( /!?(\[[^\]]*\])\([^)]*\)/gu, '$1' )
		.replace( /[`*_#>|]/gu, ' ' )
		.replace( /\s+/gu, ' ' )
		.trim();
}

function buildSource() {
	if ( ! fs.existsSync( bootstrapDirectory ) ) {
		throw new Error( 'Bootstrap Italia non e\' disponibile in node_modules. Eseguire npm ci dalla radice del tema.' );
	}
	const manifest = JSON.parse( fs.readFileSync( path.join( toolingDirectory, 'site-manifest.json' ), 'utf8' ) );
	const tracked = trackedDocuments();
	const selection = validateManifest( manifest, root, tracked );
	prepareDocumentation( { root, manifest, tracked, destination: sourceDirectory } );

	fs.mkdirSync( path.join( sourceDirectory, '_data' ), { recursive: true } );
	fs.writeFileSync( path.join( sourceDirectory, '_data', 'navigation.json' ), JSON.stringify( navigation( selection.pages ), null, 2 ) + '\n' );
	copy( path.join( toolingDirectory, '_layouts' ), path.join( sourceDirectory, '_layouts' ) );
	copy( path.join( toolingDirectory, '_includes' ), path.join( sourceDirectory, '_includes' ) );
	copy( path.join( toolingDirectory, 'assets' ), path.join( sourceDirectory, 'assets' ) );
	fs.writeFileSync(
		path.join( sourceDirectory, 'assets', 'search-index.json' ),
		JSON.stringify( selection.pages.map( ( page ) => ( {
			title: page.title,
			description: page.description,
			url: page.url === '/' ? '/' : `${page.url}/`,
			text: searchText( fs.readFileSync( page.absolute, 'utf8' ) ),
		} ) ), null, 2 ) + '\n'
	);
	for ( const directory of [ 'css', 'js', 'fonts', 'svg' ] ) {
		copy( path.join( bootstrapDirectory, directory ), path.join( sourceDirectory, 'assets', 'vendor', 'bootstrap-italia', directory ) );
	}
	console.log( `Input documentazione pronto: ${sourceDirectory}` );
}

buildSource();
