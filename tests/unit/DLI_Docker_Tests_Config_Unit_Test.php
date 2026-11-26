<?php
/**
 * Contract test: the integration suite runs in the wp-env test environment (Docker), with pinned versions and the
 * dedicated database, and no local path is left.
 *
 * Before 2026-11-26 the suite ran on a local wordpress-develop checkout (`C:/WordpressDEV`) with a database of Local
 * and with plugins downloaded by a script of the theme; a value of that setup left in the files would make the suite
 * depend again on one machine.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Files of the integration environment.
 */
class DLI_Docker_Tests_Config_Unit_Test extends TestCase {

	/**
	 * Version of WordPress of the environments: the minimum declared by the theme.
	 */
	const WORDPRESS = '7.1.4';

	/**
	 * Version of PHP of the environments (the one of the CI).
	 */
	const PHP = '8.4';

	/**
	 * Plugins of the integration environment and their pinned versions.
	 */
	const INTEGRATION_PLUGINS = array(
		'advanced-custom-fields' => '6.8.12',
		'polylang'               => '3.9.2',
	);

	/**
	 * Text of a file of the theme.
	 *
	 * @param string $relative Path relative to the theme root.
	 * @return string
	 */
	private function read( $relative ) {
		$this->assertFileExists( DLI_TEST_THEME_DIR . '/' . $relative );
		return str_replace( "\r\n", "\n", (string) file_get_contents( DLI_TEST_THEME_DIR . '/' . $relative ) );
	}

	/**
	 * Decoded content of a wp-env configuration.
	 *
	 * @param string $relative Path relative to the theme root.
	 * @return array<string, mixed>
	 */
	private function wp_env( $relative ) {
		$config = json_decode( $this->read( $relative ), true );
		$this->assertIsArray( $config, "$relative is not valid JSON." );
		return $config;
	}

	/**
	 * The integration environment pins WordPress, PHP, port, theme and plugins.
	 */
	public function test_the_integration_environment_is_pinned() {
		$config = $this->wp_env( '.wp-env.inttest.json' );

		$this->assertSame( 'https://wordpress.org/wordpress-' . self::WORDPRESS . '.zip', $config['core'] ?? null );
		$this->assertSame( self::PHP, $config['phpVersion'] ?? null );
		$this->assertSame( 8893, $config['port'] ?? null, 'Port 8893: 8890 and 8891 belong to kk-content-exporter.' );
		$this->assertSame( array( '.' ), $config['themes'] ?? null );

		$expected = array();
		foreach ( self::INTEGRATION_PLUGINS as $slug => $version ) {
			$expected[] = "https://downloads.wordpress.org/plugin/$slug.$version.zip";
		}
		$this->assertSame( $expected, $config['plugins'] ?? null );
	}

	/**
	 * The test configuration keeps the dedicated database and reads its connection from the environment, with the
	 * defaults of the wp-env test environment.
	 */
	public function test_the_test_configuration_uses_the_wp_env_defaults() {
		$content = $this->read( 'tests/integration/wp-tests-config.php' );

		$this->assertStringContainsString( "define( 'DLI_TEST_DB_NAME', 'dli_wordpress_test' );", $content );
		foreach ( array( "'DLI_TEST_DB_HOST', 'mysql'", "'DLI_TEST_DB_USER', 'root'", "'DLI_TEST_DB_PASSWORD', 'password'", "'DLI_TEST_ABSPATH', '/var/www/html/'" ) as $default ) {
			$this->assertStringContainsString( $default, $content );
		}
	}

	/**
	 * The Composer and npm scripts run the suite in Docker.
	 */
	public function test_the_scripts_run_the_suite_in_docker() {
		$composer = json_decode( $this->read( 'composer.json' ), true );
		$npm      = json_decode( $this->read( 'package.json' ), true );

		$this->assertContains( 'npm run test:integration --', (array) $composer['scripts']['test:integration'] );
		$this->assertSame( 'node tests/bin/integration.mjs', $npm['scripts']['test:integration'] ?? null );
		$this->assertSame( '12.1.0', $npm['devDependencies']['@wordpress/env'] ?? null, 'wp-env is pinned to an exact version.' );
	}

	/**
	 * The E2E environment has the same WordPress, PHP and plugins as the integration one, plus the map field that
	 * the baseline has active, on its own port.
	 */
	public function test_the_e2e_environment_is_pinned() {
		$integration = $this->wp_env( '.wp-env.inttest.json' );
		$e2e         = $this->wp_env( '.wp-env.e2etest.json' );

		$this->assertSame( $integration['core'], $e2e['core'] ?? null );
		$this->assertSame( self::PHP, $e2e['phpVersion'] ?? null );
		$this->assertSame( 8892, $e2e['port'] ?? null, 'Port 8892: 8890 and 8891 belong to kk-content-exporter, 8893 to the integration suite.' );
		$this->assertSame( array( '.' ), $e2e['themes'] ?? null );
		$this->assertSame(
			array_merge( $integration['plugins'], array( 'https://downloads.wordpress.org/plugin/acf-openstreetmap-field.1.6.1.zip' ) ),
			$e2e['plugins'] ?? null
		);
		$this->assertTrue( $e2e['config']['WP_DEBUG_LOG'] ?? false, 'PHP errors of the E2E site are written to the log.' );
	}

	/**
	 * Playwright, the scripts and the baseline point to the E2E environment, and nothing points to the Local site.
	 */
	public function test_the_e2e_suite_runs_in_docker() {
		$npm      = json_decode( $this->read( 'package.json' ), true );
		$composer = json_decode( $this->read( 'composer.json' ), true );

		$this->assertSame( 'node tests/bin/e2e.mjs', $npm['scripts']['test:e2e'] ?? null );
		$this->assertContains( 'npm run test:e2e --', (array) $composer['scripts']['test:e2e'] );
		$this->assertStringContainsString( "'http://localhost:8892'", $this->read( 'playwright.config.js' ) );
		$this->assertStringContainsString( "'siteurl','http://localhost:8892'", $this->read( 'tests/e2e/fixtures/baseline.sql' ) );

		foreach ( array( 'playwright.config.js', 'tests/e2e/global-setup.js', 'tests/e2e/support/reset.js', 'tests/bin/e2e.mjs' ) as $file ) {
			$content = $this->read( $file );
			$this->assertStringNotContainsString( 'DLI_E2E_SITE_PATH', $content, $file );
			$this->assertStringNotContainsString( 'dli-wp-e2e.local', $content, $file );
		}
		$this->assertFileDoesNotExist( DLI_TEST_THEME_DIR . '/tests/e2e/support/reset-baseline.php' );
	}

	/**
	 * No local path or plugin cache of the old setup is left, and its download script is gone.
	 */
	public function test_no_local_path_is_left() {
		foreach ( array( 'tests/bootstrap-integration.php', 'tests/integration/wp-tests-config.php', 'tests/bin/integration.mjs' ) as $file ) {
			$content = $this->read( $file );
			$this->assertStringNotContainsString( 'WordpressDEV', $content, $file );
			$this->assertStringNotContainsString( '.cache/plugins', $content, $file );
		}
		$this->assertFileDoesNotExist( DLI_TEST_THEME_DIR . '/tests/integration/support/fetch-test-plugins.php' );
		$this->assertFileDoesNotExist( DLI_TEST_THEME_DIR . '/tests/integration/support/plugin-versions.php' );
	}
}
