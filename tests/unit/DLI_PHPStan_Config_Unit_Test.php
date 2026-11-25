<?php
/**
 * Contract test: PHPStan analyzes every first-party PHP file at the agreed minimum level.
 *
 * It fails when a PHP file is added without being listed in phpstan.neon (as happened to the
 * root templates, which stayed out of the analysis for months) or when the level is lowered.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Coverage of phpstan.neon.
 */
class DLI_PHPStan_Config_Unit_Test extends TestCase {

	/**
	 * Minimum PHPStan level of the project (AGENTS/CODING_STANDARDS.md).
	 */
	const MINIMUM_LEVEL = 6;

	/**
	 * Content of phpstan.neon.
	 *
	 * @return string
	 */
	private function neon() {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/phpstan.neon' );
		$this->assertNotFalse( $content, 'Cannot read phpstan.neon' );
		return str_replace( "\r\n", "\n", $content );
	}

	/**
	 * Entries of a list of phpstan.neon ("    - value") that follows a key such as "paths:".
	 *
	 * @param string $key Key of the list, with the colon.
	 * @return string[]
	 */
	private function neon_list( $key ) {
		$lines   = explode( "\n", $this->neon() );
		$start   = array_search( '  ' . $key, $lines, true );
		$this->assertNotFalse( $start, "{$key} not found in phpstan.neon" );
		$entries = array();
		for ( $i = $start + 1, $count = count( $lines ); $i < $count; $i++ ) {
			if ( ! preg_match( '/^    - (.+)$/', $lines[ $i ], $matches ) ) {
				break;
			}
			$entries[] = trim( $matches[1] );
		}
		return $entries;
	}

	/**
	 * Whether a theme-relative path is analyzed or excluded by phpstan.neon.
	 *
	 * @param string   $relative Path relative to the theme root, with forward slashes.
	 * @param string[] $paths    Analyzed paths.
	 * @return bool
	 */
	private function is_under( $relative, array $paths ) {
		foreach ( $paths as $path ) {
			$path = rtrim( $path, '*/' );
			if ( $relative === $path || 0 === strpos( $relative, $path . '/' ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * The level is not lower than the agreed minimum.
	 */
	public function test_the_level_is_at_least_the_agreed_minimum() {
		$this->assertSame( 1, preg_match( '/^  level:\s*(\d+)\s*$/m', $this->neon(), $matches ), 'level not found in phpstan.neon' );
		$this->assertGreaterThanOrEqual( self::MINIMUM_LEVEL, (int) $matches[1], 'The PHPStan level was lowered below the project minimum.' );
	}

	/**
	 * The PHPStan badge of the README shows the level of phpstan.neon.
	 */
	public function test_the_readme_badge_shows_the_configured_level() {
		$this->assertSame( 1, preg_match( '/^  level:\s*(\d+)\s*$/m', $this->neon(), $level ), 'level not found in phpstan.neon' );
		$readme = file_get_contents( DLI_TEST_THEME_DIR . '/README.md' );
		$this->assertNotFalse( $readme, 'Cannot read README.md' );
		$this->assertSame( 1, preg_match( '/\[!\[PHPStan level (\d+)\]\(https:\/\/img\.shields\.io\/badge\/PHPStan-level%20(\d+)-/', $readme, $badge ), 'PHPStan level badge not found in README.md' );
		$this->assertSame( $level[1], $badge[1], 'The alt text of the PHPStan badge does not match phpstan.neon.' );
		$this->assertSame( $level[1], $badge[2], 'The PHPStan badge does not match the level of phpstan.neon.' );
	}

	/**
	 * Every PHP file in the root of the theme is analyzed.
	 */
	public function test_every_root_php_file_is_analyzed() {
		$paths   = $this->neon_list( 'paths:' );
		$missing = array();
		foreach ( glob( DLI_TEST_THEME_DIR . '/*.php' ) as $file ) {
			$name = basename( $file );
			if ( ! in_array( $name, $paths, true ) ) {
				$missing[] = $name;
			}
		}
		$this->assertSame( array(), $missing, 'Root PHP files missing from the paths of phpstan.neon: ' . implode( ', ', $missing ) );
	}

	/**
	 * Every top-level directory that holds PHP files is either analyzed or deliberately excluded.
	 */
	public function test_every_directory_with_php_files_is_analyzed_or_excluded() {
		$paths    = $this->neon_list( 'paths:' );
		$excluded = $this->neon_list( 'excludePaths:' );
		$unlisted = array();
		foreach ( glob( DLI_TEST_THEME_DIR . '/*', GLOB_ONLYDIR ) as $dir ) {
			$name = basename( $dir );
			if ( $this->is_under( $name, $paths ) || $this->is_under( $name, $excluded ) ) {
				continue;
			}
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $iterator as $file ) {
				if ( 'php' === strtolower( $file->getExtension() ) ) {
					$unlisted[] = $name;
					break;
				}
			}
		}
		$this->assertSame( array(), $unlisted, 'Directories with PHP files that are neither analyzed nor excluded in phpstan.neon: ' . implode( ', ', $unlisted ) );
	}
}
