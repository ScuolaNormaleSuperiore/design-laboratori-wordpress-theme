<?php
/**
 * Bootstrap of the integration test suite.
 *
 * Runs inside the CLI container of the wp-env test environment (`composer test:integration`, see
 * tests/integration/README.md). Loads the WordPress test framework that wp-env provides (`WP_TESTS_DIR`), with the
 * configuration in tests/integration/wp-tests-config.php (dedicated database `dli_wordpress_test`), then ACF and
 * Polylang from the plugins folder of the container (installed by `.wp-env.inttest.json`) and the theme, before
 * WordPress finishes booting.
 *
 * Environment variables:
 * - WP_TESTS_DIR: folder of the WordPress test framework (set by wp-env).
 * - WP_TESTS_CONFIG_FILE_PATH: alternative `wp-tests-config.php` (tests/bin/integration.mjs passes the one of the theme).
 * - DLI_TEST_DB_HOST, DLI_TEST_DB_USER, DLI_TEST_DB_PASSWORD, DLI_TEST_ABSPATH: see tests/integration/wp-tests-config.php.
 *
 * @package Design_Laboratori_Italia
 */

define( 'DLI_TEST_THEME_DIR', str_replace( '\\', '/', dirname( __DIR__ ) ) );

$dli_tests_dir = rtrim( str_replace( '\\', '/', (string) getenv( 'WP_TESTS_DIR' ) ), '/' );

if ( '' === $dli_tests_dir || ! file_exists( $dli_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "WordPress test framework not found: run the suite with `composer test:integration` (it runs in the wp-env test environment, see tests/integration/README.md).\n" );
	exit( 1 );
}

$dli_config = getenv( 'WP_TESTS_CONFIG_FILE_PATH' );
if ( false === $dli_config || '' === $dli_config ) {
	$dli_config = __DIR__ . '/integration/wp-tests-config.php';
}
define( 'WP_TESTS_CONFIG_FILE_PATH', $dli_config );

// Safety net: the framework drops every table of the configured database. Load the configuration first (the
// framework then skips it, it uses require_once) and refuse any database but the dedicated one.
if ( ! is_readable( WP_TESTS_CONFIG_FILE_PATH ) ) {
	fwrite( STDERR, 'Test configuration not found: ' . WP_TESTS_CONFIG_FILE_PATH . "\n" );
	exit( 1 );
}
require_once WP_TESTS_CONFIG_FILE_PATH;
if ( ! defined( 'DB_NAME' ) || 'dli_wordpress_test' !== DB_NAME ) {
	fwrite( STDERR, "Refusing to run: DB_NAME must be 'dli_wordpress_test' (the framework empties the database).\n" );
	exit( 1 );
}

define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', DLI_TEST_THEME_DIR . '/vendor/yoast/phpunit-polyfills' );

require_once $dli_tests_dir . '/includes/functions.php';

// Plugins that the theme needs to activate (MANDATORY_PLUGINS in config-lab.php), installed by .wp-env.inttest.json.
define( 'DLI_TEST_PLUGINS_DIR', rtrim( ABSPATH, '/' ) . '/wp-content/plugins' );
define(
	'DLI_TEST_PLUGIN_FILES',
	array(
		'advanced-custom-fields/acf.php',
		'polylang/polylang.php',
	)
);

foreach ( DLI_TEST_PLUGIN_FILES as $dli_plugin_file ) {
	if ( ! file_exists( DLI_TEST_PLUGINS_DIR . '/' . $dli_plugin_file ) ) {
		fwrite( STDERR, "Plugin $dli_plugin_file not found in " . DLI_TEST_PLUGINS_DIR . ": start the environment again with `composer test:integration` (the plugins are installed by .wp-env.inttest.json).\n" );
		exit( 1 );
	}
}

/**
 * Loads the plugins and the theme before WordPress boots.
 *
 * @return void
 */
function dli_tests_load_plugins_and_theme() {
	foreach ( DLI_TEST_PLUGIN_FILES as $plugin_file ) {
		require_once DLI_TEST_PLUGINS_DIR . '/' . $plugin_file;
	}

	// Polylang loads its API only when a context class is chosen, and with no
	// language defined (an empty test database) it chooses none: force the front end.
	add_filter(
		'pll_context',
		static function ( $context ) {
			return '' === $context ? 'PLL_Frontend' : $context;
		}
	);

	// The theme checks the option, so the plugins must look active.
	add_filter(
		'pre_option_active_plugins',
		static function () {
			return DLI_TEST_PLUGIN_FILES;
		}
	);

	// Make the working copy of the theme the active theme.
	register_theme_directory( dirname( DLI_TEST_THEME_DIR ) );
	$stylesheet = basename( DLI_TEST_THEME_DIR );
	add_filter(
		'pre_option_template',
		static function () use ( $stylesheet ) {
			return $stylesheet;
		}
	);
	add_filter(
		'pre_option_stylesheet',
		static function () use ( $stylesheet ) {
			return $stylesheet;
		}
	);
}
tests_add_filter( 'muplugins_loaded', 'dli_tests_load_plugins_and_theme' );

require $dli_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/integration/support/class-dli-integration-test-case.php';
require_once __DIR__ . '/integration/support/class-really-simple-captcha-double.php';
