<?php
/**
 * Configuration of the WordPress test framework for the integration suite.
 *
 * Read by the bootstrap of the WordPress test framework (`$WP_TESTS_DIR/includes/bootstrap.php`), inside the CLI
 * container of the wp-env test environment (`.wp-env.inttest.json`, see tests/integration/README.md). Every value has
 * the default of that environment and can be changed with an environment variable: DLI_TEST_DB_HOST,
 * DLI_TEST_DB_USER, DLI_TEST_DB_PASSWORD (connection; the defaults are the public ones of wp-env, the environment holds
 * no real data) and DLI_TEST_ABSPATH (WordPress folder).
 *
 * SAFETY: the framework drops every table of the database it is given, so the database name is fixed to
 * `dli_wordpress_test` and the suite refuses any other name. Never point it to the database of a site.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * A setting: the environment variable when it is set, otherwise the default.
 *
 * This file can be included from inside a function (PHPUnit loads the bootstrap that way) and on its own by the
 * installer subprocess of the framework: it keeps no global state and depends on nothing else.
 *
 * @param string $env_name Environment variable.
 * @param string $fallback Default value.
 * @return string
 */
function dli_test_setting( $env_name, $fallback ) {
	$value = getenv( $env_name );

	return false !== $value && '' !== $value ? $value : $fallback;
}

define( 'DLI_TEST_DB_NAME', 'dli_wordpress_test' );

// WordPress of the container; the test framework comes with wp-env (WP_TESTS_DIR).
define( 'ABSPATH', rtrim( str_replace( '\\', '/', dli_test_setting( 'DLI_TEST_ABSPATH', '/var/www/html/' ) ), '/' ) . '/' );

define( 'WP_DEFAULT_THEME', 'default' );
define( 'WP_DEBUG', true );

define( 'DB_NAME', DLI_TEST_DB_NAME );
define( 'DB_USER', dli_test_setting( 'DLI_TEST_DB_USER', 'root' ) );
define( 'DB_PASSWORD', dli_test_setting( 'DLI_TEST_DB_PASSWORD', 'password' ) );
define( 'DB_HOST', dli_test_setting( 'DLI_TEST_DB_HOST', 'mysql' ) );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// Test-only keys: the test database holds no real data.
define( 'AUTH_KEY', 'dli-tests' );
define( 'SECURE_AUTH_KEY', 'dli-tests' );
define( 'LOGGED_IN_KEY', 'dli-tests' );
define( 'NONCE_KEY', 'dli-tests' );
define( 'AUTH_SALT', 'dli-tests' );
define( 'SECURE_AUTH_SALT', 'dli-tests' );
define( 'LOGGED_IN_SALT', 'dli-tests' );
define( 'NONCE_SALT', 'dli-tests' );

$table_prefix = 'wptests_'; // Required name of the WordPress test framework.

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
