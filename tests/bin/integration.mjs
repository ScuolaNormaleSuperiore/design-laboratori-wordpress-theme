/**
 * Runs the integration suite in the wp-env test environment (`.wp-env.inttest.json`, port 8893).
 *
 * Steps: check that Docker answers, start the environment (nothing happens when it is already running), create the
 * dedicated database `dli_wordpress_test` in its database server, then run PHPUnit inside the CLI container, from the
 * folder of the theme, with the configuration of tests/integration/wp-tests-config.php. Extra arguments go to PHPUnit,
 * for example `composer test:integration -- --filter DLI_Environment_Integration_Test` (a filter that matches nothing
 * runs no test and still exits with 0: look at the number of tests).
 *
 * The connection values and the WordPress folder are the defaults of tests/integration/wp-tests-config.php (the
 * public defaults of wp-env: the environment holds no real data). The guard of tests/bootstrap-integration.php refuses
 * any database other than `dli_wordpress_test`.
 *
 * Arguments are passed as arrays, never through a shell, so the script behaves the same on Windows.
 *
 * @package Design_Laboratori_Italia
 */

import { spawnSync } from 'node:child_process';
import { createRequire } from 'node:module';
import path from 'node:path';
import { requireDocker } from './docker.mjs';

const require = createRequire( import.meta.url );
const root = path.resolve( import.meta.dirname, '..', '..' );
// The executable is declared in the `bin` field of the package, which does not export it as a module.
const wpEnvPackage = require.resolve( '@wordpress/env/package.json' );
const wpEnv = path.join( path.dirname( wpEnvPackage ), require( wpEnvPackage ).bin[ 'wp-env' ] );
const config = '--config=.wp-env.inttest.json';
const themeDir = 'wp-content/themes/' + path.basename( root );

/**
 * Runs wp-env with the given arguments and stops the script when it fails.
 *
 * @param {string[]} args    Arguments of wp-env.
 * @param {string}   message Message shown when the command fails.
 */
function wpEnvRun( args, message ) {
	const result = spawnSync( process.execPath, [ wpEnv, ...args ], { cwd: root, stdio: 'inherit' } );
	if ( 0 !== result.status ) {
		console.error( '\n' + message );
		process.exit( result.status || 1 );
	}
}

requireDocker();

wpEnvRun(
	[ 'start', config ],
	'The wp-env test environment could not start (is port 8893 free?). See tests/integration/README.md.'
);

wpEnvRun(
	[ 'run', config, 'cli', 'wp', 'db', 'query', 'CREATE DATABASE IF NOT EXISTS dli_wordpress_test' ],
	'The database dli_wordpress_test could not be created in the wp-env test environment.'
);

const result = spawnSync(
	process.execPath,
	[
		wpEnv,
		'run',
		config,
		'cli',
		'--env-cwd=' + themeDir,
		'--',
		'env',
		// wp-env defines its own test configuration: the one of the theme is used instead.
		'WP_TESTS_CONFIG_FILE_PATH=/var/www/html/' + themeDir + '/tests/integration/wp-tests-config.php',
		'vendor/bin/phpunit',
		'-c',
		'phpunit-integration.xml',
		...process.argv.slice( 2 ),
	],
	{ cwd: root, stdio: 'inherit' }
);

process.exit( null === result.status ? 1 : result.status );
