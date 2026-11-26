// Runs WP-CLI in the CLI container of the wp-env E2E environment (.wp-env.e2etest.json). Arguments are passed as
// arrays to wp-env, never through a shell, so they behave the same on Windows. See tests/e2e/README.md.
const { spawnSync } = require( 'node:child_process' );
const path = require( 'node:path' );

const ROOT = path.resolve( __dirname, '..', '..', '..' );
const WP_ENV_PACKAGE = require.resolve( '@wordpress/env/package.json' );
const WP_ENV = path.join( path.dirname( WP_ENV_PACKAGE ), require( WP_ENV_PACKAGE ).bin[ 'wp-env' ] );

// Folder of the theme inside the containers.
const THEME_DIR = 'wp-content/themes/' + path.basename( ROOT );

/**
 * Run `wp` in the CLI container of the E2E environment.
 *
 * @param {string[]} args Arguments of `wp`.
 * @return {string} Standard output, trimmed.
 */
function wp( args ) {
	const result = run( args );
	if ( 0 !== result.status ) {
		throw new Error( 'WP-CLI failed: wp ' + args.join( ' ' ) + '\n' + ( result.stderr || result.error || '' ) );
	}
	return lastLine( result.stdout );
}

/**
 * Whether `wp` exits with 0 in the CLI container of the E2E environment, for the
 * checks that answer with the exit code (`wp language core is-installed it_IT`).
 *
 * @param {string[]} args Arguments of `wp`.
 * @return {boolean} True on exit code 0.
 */
function wpSucceeds( args ) {
	return 0 === run( args ).status;
}

/**
 * Run `wp` through wp-env.
 *
 * @param {string[]} args Arguments of `wp`.
 * @return {import('node:child_process').SpawnSyncReturns<string>} Result.
 */
function run( args ) {
	return spawnSync(
		process.execPath,
		[ WP_ENV, 'run', '--config=.wp-env.e2etest.json', 'cli', '--', 'wp', ...args ],
		{ cwd: ROOT, encoding: 'utf8' }
	);
}

/**
 * The last line of an output that is not a status line of wp-env (`ℹ Starting…`, `✔ Ran…`).
 *
 * @param {string} output Output of a command.
 * @return {string} Line, trimmed; empty when there is none.
 */
function lastLine( output ) {
	const lines = String( output )
		.split( /\r?\n/ )
		.map( ( line ) => line.trim() )
		.filter( ( line ) => '' !== line && ! /^[ℹ✔✖√×]/.test( line ) );
	return lines.length ? lines[ lines.length - 1 ] : '';
}

module.exports = { wp, wpSucceeds, THEME_DIR };
