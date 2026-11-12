<?php
/**
 * Unit tests of the guide to the images, DOC/HowTo/Gestione-delle-immagini.md: it exists, it is in the documentation manifest and it
 * gives the same sizes as the list of formats in inc/image-guidelines.php.
 *
 * A failing test means the list and the guide were not updated together: fix the guide, or the list.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Guide to the images and its link with the list of formats.
 */
class DLI_Image_Guide_Unit_Test extends TestCase {

	/**
	 * Path of the guide, relative to the theme.
	 */
	const GUIDE = 'DOC/HowTo/Gestione-delle-immagini.md';

	/**
	 * The text of the guide.
	 *
	 * @return string
	 */
	private function guide() {
		$path = DLI_TEST_THEME_DIR . '/' . self::GUIDE;
		$this->assertFileExists( $path, 'The guide to the images does not exist.' );
		return (string) file_get_contents( $path );
	}

	/**
	 * The guide starts with its title and says what it is for.
	 */
	public function test_the_guide_has_a_title() {
		$this->assertStringStartsWith( '# Gestione delle immagini', $this->guide() );
	}

	/**
	 * Every size of the list is written in the guide, in the same form as the texts of the fields.
	 */
	public function test_the_guide_gives_every_size_of_the_list() {
		$guide = $this->guide();
		foreach ( dli_get_image_guidelines() as $id => $guideline ) {
			if ( null !== $guideline['recommended'] ) {
				$size = $guideline['recommended'][0] . ' × ' . $guideline['recommended'][1];
				$this->assertStringContainsString( $size, $guide, "$id: the recommended size $size is not in the guide." );
			}
			if ( null !== $guideline['minimum'] ) {
				$minimum = null === $guideline['minimum'][1]
					? $guideline['minimum'][0] . ' px di larghezza'
					: $guideline['minimum'][0] . ' × ' . $guideline['minimum'][1];
				$this->assertStringContainsString( $minimum, $guide, "$id: the minimum $minimum is not in the guide." );
			}
		}
	}

	/**
	 * Every place of the list has its name in the guide, so that nobody looks for a place that is not described.
	 */
	public function test_the_guide_names_every_place_of_the_list() {
		$guide = $this->guide();
		foreach ( dli_get_image_guidelines() as $id => $guideline ) {
			$this->assertStringContainsString( $guideline['label'], $guide, "$id: its name is not in the guide." );
		}
	}

	/**
	 * The maximum weight is the one of the upload limit, and the guide says that the sizes are indicative.
	 */
	public function test_the_guide_gives_the_weight_and_the_warning() {
		$guide = $this->guide();

		$this->assertStringContainsString( DLI_MAX_IMG_FILE_SIZE_MB . ' MB', $guide );
		$this->assertStringContainsString( '## Perché le dimensioni sono indicative', $guide );
	}

	/**
	 * The guide is in the documentation manifest, in the group of the administrators.
	 */
	public function test_the_guide_is_in_the_documentation_manifest() {
		$manifest = json_decode( (string) file_get_contents( DLI_TEST_THEME_DIR . '/SETUP/Documentazione/site-manifest.json' ), true );
		$this->assertIsArray( $manifest );

		$entries = array_values(
			array_filter(
				$manifest['pages'] ?? array(),
				static function ( $page ) {
					return self::GUIDE === ( $page['source'] ?? '' );
				}
			)
		);
		$this->assertCount( 1, $entries, 'The guide must be in the manifest exactly once.' );
		$this->assertSame( 'Amministra', $entries[0]['group'] );
		$this->assertNotSame( '', $entries[0]['title'] );
		$this->assertNotSame( '', $entries[0]['description'] );
	}

	/**
	 * The address that the media window links is the page of the guide in the documentation site: the address of the
	 * site in the README plus the target of the guide in the manifest, as the generator makes it (pretty addresses).
	 * It is deduced, so the page may not exist yet: if the published address differs, correct `DLI_IMAGE_GUIDE_URL`.
	 */
	public function test_the_address_of_the_guide_follows_the_manifest_and_the_documentation_site() {
		$readme = (string) file_get_contents( DLI_TEST_THEME_DIR . '/README.md' );
		$this->assertSame( 1, preg_match( '#\[documentazione di Design Laboratori Italia\]\((https://[a-z0-9.-]+\.github\.io/[a-z0-9-]+/)\)#', $readme, $site ), 'The README gives no address of the documentation site.' );

		$manifest = json_decode( (string) file_get_contents( DLI_TEST_THEME_DIR . '/SETUP/Documentazione/site-manifest.json' ), true );
		$targets  = array();
		foreach ( $manifest['pages'] ?? array() as $page ) {
			if ( self::GUIDE === ( $page['source'] ?? '' ) ) {
				$targets[] = $page['target'];
			}
		}
		$this->assertCount( 1, $targets, 'The guide must be in the manifest exactly once.' );

		$this->assertSame( $site[1] . preg_replace( '/\.md$/', '/', $targets[0] ), DLI_IMAGE_GUIDE_URL );
		$this->assertStringStartsWith( 'https://', DLI_IMAGE_GUIDE_URL );
	}

	/**
	 * The guide is public: no reference to the private working folder or to the private backlog.
	 */
	public function test_the_guide_has_no_private_reference() {
		$guide = $this->guide();

		foreach ( array( 'DEV/', 'ISSUES_TODO', 'ISSUES_RESOLVED', 'C:\\', '/Users/', 'Local Sites' ) as $private ) {
			$this->assertStringNotContainsString( $private, $guide, "The guide mentions $private." );
		}
	}
}
