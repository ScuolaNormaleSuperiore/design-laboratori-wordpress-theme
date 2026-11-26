<?php
/**
 * Contract test: the minimum versions of PHP and WordPress are the same wherever they are declared, the CI
 * tests the minimum PHP, and the documents that do not need the numbers do not repeat them.
 *
 * WordPress enforces the minimums only through style.css; the other files that declare them (Composer's
 * platform, publiccode.yml, PHPCS, the README) drift silently when a minimum is raised.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Minimum versions of PHP and WordPress.
 */
class DLI_Requirements_Unit_Test extends TestCase {

	/**
	 * Minimum PHP version (major.minor).
	 */
	const PHP = '8.3';

	/**
	 * Minimum WordPress version.
	 */
	const WORDPRESS = '7.1.4';

	/**
	 * Text of a file of the theme, with Unix line endings.
	 *
	 * @param string $relative Path relative to the theme root.
	 * @return string
	 */
	private function read( $relative ) {
		$this->assertFileExists( DLI_TEST_THEME_DIR . '/' . $relative );
		return str_replace( "\r\n", "\n", (string) file_get_contents( DLI_TEST_THEME_DIR . '/' . $relative ) );
	}

	/**
	 * The header of style.css, the one WordPress enforces, declares both minimums.
	 */
	public function test_style_css_declares_the_minimums() {
		$style = $this->read( 'style.css' );

		$this->assertMatchesRegularExpression( '/^Requires PHP: ' . preg_quote( self::PHP, '/' ) . '$/m', $style );
		$this->assertMatchesRegularExpression( '/^Requires at least: ' . preg_quote( self::WORDPRESS, '/' ) . '$/m', $style );
	}

	/**
	 * Composer resolves the development dependencies for the minimum PHP.
	 */
	public function test_composer_resolves_for_the_minimum_php() {
		$composer = json_decode( $this->read( 'composer.json' ), true );

		$this->assertStringStartsWith( self::PHP . '.', (string) ( $composer['config']['platform']['php'] ?? '' ) );
	}

	/**
	 * The catalogue of Developers Italia and PHPCS have the same minimums.
	 */
	public function test_publiccode_and_phpcs_declare_the_minimums() {
		$publiccode = $this->read( 'publiccode.yml' );

		$this->assertMatchesRegularExpression( "/- name: WordPress\n\s+optional: false\n\s+versionMin: '" . preg_quote( self::WORDPRESS, '/' ) . "'/", $publiccode );
		$this->assertMatchesRegularExpression( "/- name: PHP\n\s+optional: false\n\s+versionMin: '" . preg_quote( self::PHP, '/' ) . "'/", $publiccode );
		$this->assertStringContainsString( '<config name="minimum_supported_wp_version" value="' . self::WORDPRESS . '"/>', $this->read( 'phpcs.xml.dist' ) );
	}

	/**
	 * The CI checks the minimum PHP and the version of the test environments.
	 */
	public function test_the_ci_tests_the_minimum_php() {
		$this->assertStringContainsString( "php: ['" . self::PHP . "', '8.4']", $this->read( '.github/workflows/ci.yml' ) );
	}

	/**
	 * The requirements of the README are the only human-readable place with the numbers.
	 */
	public function test_the_readme_lists_the_minimums() {
		$readme = $this->read( 'README.md' );

		$this->assertStringContainsString( '1. Il CMS WordPress (versione >= ' . self::WORDPRESS . ').', $readme );
		$this->assertStringContainsString( '2. PHP >= ' . self::PHP . '.', $readme );
	}

	/**
	 * The other documents refer to the declared minimums instead of repeating the numbers.
	 */
	public function test_the_other_documents_do_not_repeat_the_minimums() {
		$files = array(
			'.github/workflows/ci.yml',
			'AGENTS/ARCHITECTURE.md',
			'AGENTS/CODING_STANDARDS.md',
			'AGENTS/GIT_WORKFLOW.md',
			'DOC/Avvio/Primo-avvio.md',
			'tests/README.md',
			'tests/unit/README.md',
		);
		foreach ( $files as $file ) {
			$content = $this->read( $file );
			$this->assertDoesNotMatchRegularExpression( '/PHP:? (>= ?)?8\.[0-3]\b|minimum 8\.[0-3]\b|8\.[0-3] e 8\.4/', $content, $file );
			$this->assertStringNotContainsString( 'minimum ' . self::WORDPRESS, $content, $file );
		}
		$this->assertStringNotContainsString( self::WORDPRESS, $this->read( 'DOC/Avvio/Primo-avvio.md' ) );
		$this->assertStringNotContainsString( 'PHP 8.0', $this->read( 'README.md' ) );
	}
}
