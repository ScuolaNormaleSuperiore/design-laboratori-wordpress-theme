<?php
/**
 * Bootstrap of the unit test suite.
 *
 * The unit tests run without WordPress: only the theme files that do not need a
 * running WordPress are loaded, with a few minimal stubs of core functions.
 * Tests that need WordPress belong to the integration suite.
 *
 * @package Design_Laboratori_Italia
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'DLI_TEST_THEME_DIR', dirname( __DIR__ ) );

require_once DLI_TEST_THEME_DIR . '/vendor/autoload.php';

if ( ! function_exists( 'trailingslashit' ) ) {
	/**
	 * Stub: append a trailing slash.
	 *
	 * @param string $value Path or URL.
	 * @return string
	 */
	function trailingslashit( $value ) {
		return rtrim( $value, '/\\' ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	/**
	 * Stub: directory of a file, with a trailing slash.
	 *
	 * @param string $file File path.
	 * @return string
	 */
	function plugin_dir_path( $file ) {
		return trailingslashit( dirname( $file ) );
	}
}

if ( ! function_exists( 'get_template_directory_uri' ) ) {
	/**
	 * Stub: URL of the theme directory.
	 *
	 * @return string
	 */
	function get_template_directory_uri() {
		return 'https://example.test/wp-content/themes/design-laboratori-wordpress-theme';
	}
}

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Stub: hooks are not run in unit tests, the registration is ignored.
	 *
	 * @param string   $hook     Hook name.
	 * @param callable $callback Callback.
	 * @return true
	 */
	function add_action( $hook, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * Stub: hooks are not run in unit tests, the registration is ignored.
	 *
	 * @param string   $hook     Hook name.
	 * @param callable $callback Callback.
	 * @return true
	 */
	function add_filter( $hook, $callback ) {
		return true;
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Stub: translation, returns the text unchanged.
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( '_x' ) ) {
	/**
	 * Stub: translation with context, returns the text unchanged.
	 *
	 * @param string $text    Text.
	 * @param string $context Context.
	 * @param string $domain  Text domain.
	 * @return string
	 */
	function _x( $text, $context, $domain = 'default' ) {
		return $text;
	}
}

require_once DLI_TEST_THEME_DIR . '/config-lab.php';
require_once DLI_TEST_THEME_DIR . '/inc/utils.php';
require_once DLI_TEST_THEME_DIR . '/inc/captcha.php';
require_once DLI_TEST_THEME_DIR . '/inc/home-sections.php';
