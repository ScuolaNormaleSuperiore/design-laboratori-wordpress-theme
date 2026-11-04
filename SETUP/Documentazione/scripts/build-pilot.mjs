import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const scriptDirectory = path.dirname( fileURLToPath( import.meta.url ) );
const toolingDirectory = path.resolve( scriptDirectory, '..' );
const root = path.resolve( toolingDirectory, '../..' );
const sourceDirectory = path.join( toolingDirectory, '.build', 'pilot-source' );
const bootstrapDirectory = path.join( root, 'node_modules', 'bootstrap-italia', 'dist' );

function copy( from, to ) {
	fs.mkdirSync( path.dirname( to ), { recursive: true } );
	fs.cpSync( from, to, { recursive: true } );
}

function pageBody( source, title, target ) {
	const markdown = fs.readFileSync( source, 'utf8' );
	const heading = /^# [^\r\n]+\r?\n+/;
	if ( ! heading.test( markdown ) ) {
		throw new Error( `Il pilota richiede un H1 iniziale: ${ source }` );
	}

	return [
		'---',
		'layout: docs',
		`title: ${ JSON.stringify( title ) }`,
		`pilot_target: ${ JSON.stringify( target ) }`,
		'toc: true',
		'---',
		'',
		markdown.replace( heading, '' ),
	].join( '\n' );
}

function buildPilot() {
	const manifest = JSON.parse( fs.readFileSync( path.join( toolingDirectory, 'site-manifest.json' ), 'utf8' ) );
	if ( ! Array.isArray( manifest.pages ) || manifest.pages.length < 2 ) {
		throw new Error( 'Il pilota richiede almeno due pagine nel manifest.' );
	}
	if ( ! fs.existsSync( bootstrapDirectory ) ) {
		throw new Error( 'Bootstrap Italia non e\' disponibile in node_modules. Eseguire npm ci dalla radice del tema.' );
	}

	fs.rmSync( sourceDirectory, { recursive: true, force: true } );
	fs.mkdirSync( sourceDirectory, { recursive: true } );

	for ( const page of manifest.pages ) {
		if ( ! page.source.startsWith( 'DOC/' ) || ! /^[a-z0-9-]+\.md$/i.test( page.target ) && page.target !== 'index.md' ) {
			throw new Error( `Pagina del manifest non ammessa nel pilota: ${ page.source }` );
		}
		const source = path.resolve( root, page.source );
		const destination = path.join( sourceDirectory, page.target );
		fs.mkdirSync( path.dirname( destination ), { recursive: true } );
		fs.writeFileSync( destination, pageBody( source, page.title, page.target ) );
	}

	fs.mkdirSync( path.join( sourceDirectory, '_data' ), { recursive: true } );
	fs.writeFileSync(
		path.join( sourceDirectory, '_data', 'pilot-nav.json' ),
		JSON.stringify( manifest.pages.map( ( page ) => ( { title: page.title, target: page.target } ) ), null, 2 ) + '\n'
	);

	copy( path.join( toolingDirectory, '_layouts' ), path.join( sourceDirectory, '_layouts' ) );
	copy( path.join( toolingDirectory, '_includes' ), path.join( sourceDirectory, '_includes' ) );
	copy( path.join( toolingDirectory, 'assets' ), path.join( sourceDirectory, 'assets' ) );
	copy( path.join( bootstrapDirectory, 'css' ), path.join( sourceDirectory, 'assets', 'vendor', 'bootstrap-italia', 'css' ) );
	copy( path.join( bootstrapDirectory, 'js' ), path.join( sourceDirectory, 'assets', 'vendor', 'bootstrap-italia', 'js' ) );
	copy( path.join( bootstrapDirectory, 'fonts' ), path.join( sourceDirectory, 'assets', 'vendor', 'bootstrap-italia', 'fonts' ) );
	copy( path.join( bootstrapDirectory, 'svg' ), path.join( sourceDirectory, 'assets', 'vendor', 'bootstrap-italia', 'svg' ) );

	console.log( `Input del pilota pronto: ${ sourceDirectory }` );
}

buildPilot();
