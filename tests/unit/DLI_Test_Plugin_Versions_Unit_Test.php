<?php
/**
 * Unit tests of the helpers that choose which version of a test plugin the integration suite downloads
 * (tests/integration/support/plugin-versions.php).
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/tests/integration/support/plugin-versions.php';

/**
 * Without a pinned version the latest stable release is downloaded, as it always was; with one, exactly that
 * release is. The pinned value comes from the environment, so it is checked before it reaches an address, and a
 * plugin already in the cache is reused only when it is the pinned version.
 */
class DLI_Test_Plugin_Versions_Unit_Test extends TestCase {

	/**
	 * With no version the address is the latest stable release.
	 */
	public function test_without_a_version_the_address_is_the_latest_stable_release() {
		$this->assertSame(
			'https://downloads.wordpress.org/plugin/polylang.latest-stable.zip',
			dli_test_plugin_zip_url( 'polylang', '' )
		);
	}

	/**
	 * With a pinned version the address names that release.
	 */
	public function test_a_pinned_version_gives_the_address_of_that_release() {
		$this->assertSame(
			'https://downloads.wordpress.org/plugin/advanced-custom-fields.6.8.10.zip',
			dli_test_plugin_zip_url( 'advanced-custom-fields', '6.8.10' )
		);
		$this->assertSame(
			'https://downloads.wordpress.org/plugin/polylang.3.9.zip',
			dli_test_plugin_zip_url( 'polylang', '3.9' )
		);
	}

	/**
	 * The version is read from the environment: anything that is not a dotted number never reaches the address.
	 */
	public function test_a_version_that_is_not_a_dotted_number_is_refused() {
		foreach ( array( 'latest', '6', '6.8.', '.6.8', '6..8', '6.8.10/../x', '6.8.10.zip', '6.8.10 ', ' 6.8.10', "6.8\n", '6.8.10?x=1', '6.8.10-beta', '1.2.3.4.5', '../6.8', 'v6.8.10' ) as $version ) {
			$this->assertFalse( dli_test_plugin_is_valid_version( $version ), 'Not a version: ' . var_export( $version, true ) );

			$refused = false;
			try {
				dli_test_plugin_zip_url( 'polylang', $version );
			} catch ( InvalidArgumentException $e ) {
				$refused = true;
			}
			$this->assertTrue( $refused, 'The address is not built for: ' . var_export( $version, true ) );
		}
	}

	/**
	 * A dotted number of two to four parts is a version.
	 */
	public function test_a_dotted_number_is_a_valid_version() {
		foreach ( array( '3.9', '6.8.10', '1.2.3.4' ) as $version ) {
			$this->assertTrue( dli_test_plugin_is_valid_version( $version ), 'A version: ' . $version );
		}
	}

	/**
	 * The slug is part of the address too: only the plugin names the suite uses are accepted.
	 */
	public function test_a_slug_that_is_not_a_plugin_name_is_refused() {
		$this->expectException( InvalidArgumentException::class );
		dli_test_plugin_zip_url( '../polylang', '3.9.1' );
	}

	/**
	 * The version of an installed plugin is the `Version:` line of its header.
	 */
	public function test_the_installed_version_is_read_from_the_plugin_header() {
		$header = "<?php\n/**\n * Plugin Name:       Advanced Custom Fields\n * Version:           6.8.10\n * Requires PHP:      7.4\n */\n";
		$this->assertSame( '6.8.10', dli_test_plugin_header_version( $header ) );
		$this->assertSame( '3.9.1', dli_test_plugin_header_version( "<?php\n/*\nPlugin Name: Polylang\nVersion: 3.9.1\n*/" ) );
		$this->assertSame( '', dli_test_plugin_header_version( "<?php\n// no header\n" ) );
	}

	/**
	 * A cached plugin is reused when nothing is pinned, or when it is the pinned version; otherwise it is downloaded again.
	 */
	public function test_a_cached_plugin_is_reused_only_when_it_is_the_pinned_version() {
		$this->assertTrue( dli_test_plugin_cache_is_usable( '', '6.8.10' ), 'Nothing pinned: any cached version is fine.' );
		$this->assertTrue( dli_test_plugin_cache_is_usable( '', '' ), 'Nothing pinned: a cache of unknown version is fine.' );
		$this->assertTrue( dli_test_plugin_cache_is_usable( '6.8.10', '6.8.10' ) );
		$this->assertFalse( dli_test_plugin_cache_is_usable( '6.8.10', '6.8.9' ) );
		$this->assertFalse( dli_test_plugin_cache_is_usable( '6.8.10', '6.8.10.1' ), 'A longer version is another version.' );
		$this->assertFalse( dli_test_plugin_cache_is_usable( '6.8.10', '' ), 'A pinned version cannot be checked against a cache of unknown version.' );
	}

	/**
	 * Every plugin the suite downloads has one environment variable that pins it.
	 */
	public function test_each_plugin_has_its_own_environment_variable() {
		$this->assertSame( 'DLI_ACF_VERSION', dli_test_plugin_version_variable( 'advanced-custom-fields' ) );
		$this->assertSame( 'DLI_POLYLANG_VERSION', dli_test_plugin_version_variable( 'polylang' ) );
		$this->assertSame( '', dli_test_plugin_version_variable( 'hello-dolly' ) );
	}
}
