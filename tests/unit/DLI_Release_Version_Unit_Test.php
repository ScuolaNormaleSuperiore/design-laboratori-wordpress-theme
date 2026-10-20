<?php
/**
 * Contract test: the version is the same in every file that declares it.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Release version consistency.
 */
class DLI_Release_Version_Unit_Test extends TestCase {

	/**
	 * Read a theme file.
	 *
	 * @param string $relative_path Path relative to the theme root.
	 * @return string
	 */
	private function read( $relative_path ) {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/' . $relative_path );
		$this->assertNotFalse( $content, "Cannot read {$relative_path}" );
		return $content;
	}

	/**
	 * Extract a value with a regular expression and fail with a clear message.
	 *
	 * @param string $pattern       Regular expression with one capture group.
	 * @param string $content       Text to search.
	 * @param string $relative_path File name, for the failure message.
	 * @return string
	 */
	private function extract( $pattern, $content, $relative_path ) {
		$this->assertSame( 1, preg_match( $pattern, $content, $matches ), "Version not found in {$relative_path}" );
		return trim( $matches[1] );
	}

	/**
	 * The versions found in the release files.
	 *
	 * @return array<string,string> Version by file.
	 */
	private function versions() {
		$package = json_decode( $this->read( 'package.json' ), true );
		return array(
			'VERSION.txt'                 => trim( $this->read( 'VERSION.txt' ) ),
			'style.css'                   => $this->extract( '/^Version:\s*(\S+)/mi', $this->read( 'style.css' ), 'style.css' ),
			'package.json'                => (string) ( $package['version'] ?? '' ),
			'publiccode.yml'              => $this->extract( '/^softwareVersion:\s*[\'"]?([^\'"\s]+)/m', $this->read( 'publiccode.yml' ), 'publiccode.yml' ),
			'CHANGELOG.md (first entry)'  => $this->extract( '/^##\s*\[(\d+\.\d+\.\d+)\]/m', $this->read( 'CHANGELOG.md' ), 'CHANGELOG.md' ),
		);
	}

	/**
	 * Every release file declares the same version.
	 */
	public function test_version_is_the_same_in_every_release_file() {
		$versions = $this->versions();
		$expected = $versions['VERSION.txt'];
		foreach ( $versions as $file => $version ) {
			$this->assertSame( $expected, $version, "Version in {$file} differs from VERSION.txt ({$expected})" );
		}
	}

	/**
	 * The version is a plain MAJOR.MINOR.PATCH number.
	 */
	public function test_version_has_semantic_format() {
		$this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', $this->versions()['VERSION.txt'] );
	}
}
