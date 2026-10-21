<?php
/**
 * Bootstrap of the integration test suite.
 *
 * Loads the WordPress test framework (wordpress-develop), then ACF and Polylang
 * from the plugins cache and the theme, before WordPress finishes booting. Run
 * `php tests/utils/fetch-test-plugins.php` first (the Composer script does it).
 *
 * Environment variables:
 * - WP_TESTS_DIR: `tests/phpunit` folder of wordpress-develop
 *   (default `C:/WordpressDEV/wordpress-develop/tests/phpunit`).
 * - WP_TESTS_CONFIG_FILE_PATH: alternative `wp-tests-config.php`.
 * - DLI_TEST_PLUGINS_DIR: folder with the plugins to load
 *   (default `tests/integration/.cache/plugins`).
 *
 * @package Design_Laboratori_Italia
 */

define( 'DLI_TEST_THEME_DIR', str_replace( '\\', '/', dirname( __DIR__ ) ) );

$dli_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( false === $dli_tests_dir || '' === $dli_tests_dir ) {
	$dli_tests_dir = 'C:/WordpressDEV/wordpress-develop/tests/phpunit';
}
$dli_tests_dir = rtrim( str_replace( '\\', '/', $dli_tests_dir ), '/' );

$dli_plugins_dir = getenv( 'DLI_TEST_PLUGINS_DIR' );
if ( false === $dli_plugins_dir || '' === $dli_plugins_dir ) {
	$dli_plugins_dir = DLI_TEST_THEME_DIR . '/tests/integration/.cache/plugins';
}
define( 'DLI_TEST_PLUGINS_DIR', rtrim( str_replace( '\\', '/', $dli_plugins_dir ), '/' ) );

if ( ! file_exists( $dli_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "WordPress test framework not found in $dli_tests_dir (set WP_TESTS_DIR).\n" );
	exit( 1 );
}

$dli_config = getenv( 'WP_TESTS_CONFIG_FILE_PATH' );
if ( false !== $dli_config && '' !== $dli_config ) {
	define( 'WP_TESTS_CONFIG_FILE_PATH', $dli_config );
}

define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', DLI_TEST_THEME_DIR . '/vendor/yoast/phpunit-polyfills' );

require_once $dli_tests_dir . '/includes/functions.php';

// Plugins that the theme needs to activate (MANDATORY_PLUGINS in config-lab.php).
define(
	'DLI_TEST_PLUGIN_FILES',
	array(
		'advanced-custom-fields/acf.php',
		'polylang/polylang.php',
	)
);

foreach ( DLI_TEST_PLUGIN_FILES as $dli_plugin_file ) {
	if ( ! file_exists( DLI_TEST_PLUGINS_DIR . '/' . $dli_plugin_file ) ) {
		fwrite( STDERR, "Plugin $dli_plugin_file not found in " . DLI_TEST_PLUGINS_DIR . ". Run: php tests/utils/fetch-test-plugins.php\n" );
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
