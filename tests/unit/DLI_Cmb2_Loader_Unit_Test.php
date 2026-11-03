<?php
/**
 * Contract test: how the theme loads CMB2 and the version it declares for it.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * `inc/cmb2.php`, `inc/vendor/CMB2/init.php` and the version placeholder of PHPStan.
 */
class DLI_Cmb2_Loader_Unit_Test extends TestCase {

	/**
	 * Read a file of the theme.
	 *
	 * @param string $relative_path Path inside the theme.
	 * @return string The content.
	 */
	private function read( $relative_path ) {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/' . $relative_path );
		$this->assertNotFalse( $content, "Cannot read {$relative_path}" );
		return $content;
	}

	/**
	 * The libraries are loaded from a path anchored on the file, not from a path that depends on the directory the
	 * request started from.
	 */
	public function test_libraries_are_loaded_with_paths_anchored_on_the_file() {
		$content = $this->read( 'inc/cmb2.php' );

		$this->assertSame( 0, preg_match( '/^\s*(?:require|include)(?:_once)?\s*\(?\s*[\'"]/m', $content ), 'A require of inc/cmb2.php uses a relative path.' );
		$this->assertGreaterThan( 0, preg_match_all( '/^\s*require(?:_once)?\s+__DIR__\s*\.\s*\'\/vendor\/CMB2\/init\.php\'/m', $content ), 'CMB2 is loaded from __DIR__.' );
	}

	/**
	 * The version that PHPStan is told about is the version of the library that is shipped.
	 */
	public function test_stub_version_is_the_version_of_the_shipped_library() {
		$this->assertSame( 1, preg_match( "/const VERSION = '([^']+)'/", $this->read( 'inc/vendor/CMB2/init.php' ), $library ), 'Version not found in CMB2 init.php' );
		$this->assertSame( 1, preg_match( "/define\( 'CMB2_VERSION', '([^']+)' \)/", $this->read( 'tests/phpstan/wordpress-lite-stubs.php' ), $stub ), 'CMB2_VERSION not found in the PHPStan stub' );

		$this->assertSame( $library[1], $stub[1], 'tests/phpstan/wordpress-lite-stubs.php must declare the version of inc/vendor/CMB2.' );
	}

	/**
	 * The library has no file of development tools, which would bring their vulnerabilities back in the dependency scan.
	 */
	public function test_library_has_no_package_or_lock_file() {
		$found = array();
		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( DLI_TEST_THEME_DIR . '/inc/vendor/CMB2', FilesystemIterator::SKIP_DOTS ) );
		foreach ( $files as $file ) {
			if ( preg_match( '/^(package(-lock)?\.json|composer\.(json|lock)|yarn\.lock)$/', $file->getFilename() ) ) {
				$found[] = $file->getPathname();
			}
		}

		$this->assertSame( array(), $found, 'Files of development tools must not be copied with the library.' );
	}
}
