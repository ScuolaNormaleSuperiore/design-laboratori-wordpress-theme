<?php
/**
 * The language that the head of a page declares.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `header.php` and `template-parts/header/metatags.php`: the language of the page is the `lang` attribute of the
 * `html` element, in the language of the page, and nothing else. The `http-equiv="content-language"` meta is
 * obsolete in HTML and carried a locale (`it_IT`) that is not a language tag.
 */
class DLI_Header_Language_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The language that was current before the test.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Create English next to Italian, and keep the language to put back.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;

		$language = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		if ( is_wp_error( $language ) ) {
			$this->fail( 'Cannot create the English language: ' . $language->get_error_message() );
		}
	}

	/**
	 * Put back the current language.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Render the header in a language and read it as a document.
	 *
	 * @param string $slug Language slug.
	 * @return DOMDocument
	 */
	private function head( $slug ) {
		PLL()->curlang = PLL()->model->get_language( $slug );

		$html     = $this->render_theme_template( 'header.php' );
		$previous = libxml_use_internal_errors( true );
		$document = new DOMDocument();
		$document->loadHTML( '<?xml encoding="utf-8"?>' . $html );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous );
		return $document;
	}

	/**
	 * The language of the page is the `lang` attribute of the `html` element.
	 */
	public function test_html_element_declares_the_language_of_the_page() {
		foreach ( array( 'it', 'en' ) as $slug ) {
			$root = $this->head( $slug )->documentElement;
			$this->assertSame( 'html', $root->nodeName );
			$this->assertSame( $slug, $root->getAttribute( 'lang' ), "The lang of the page in '$slug'." );
		}
	}

	/**
	 * The obsolete content-language meta is not printed.
	 */
	public function test_the_obsolete_content_language_meta_is_not_printed() {
		foreach ( array( 'it', 'en' ) as $slug ) {
			$xpath = new DOMXPath( $this->head( $slug ) );
			$found = $xpath->query( '//meta[translate(@http-equiv, "CONTENT-LANGUAGE", "content-language")="content-language"]' );
			$this->assertSame( 0, $found->length, "No content-language meta in '$slug'." );
		}
	}

	/**
	 * The other meta tags of the head are still there.
	 */
	public function test_the_other_meta_tags_are_still_printed() {
		$xpath = new DOMXPath( $this->head( 'it' ) );
		foreach ( array( 'resource-type', 'description', 'copyright', 'keywords' ) as $name ) {
			$this->assertSame( 1, $xpath->query( '//meta[@name="' . $name . '"]' )->length, "The meta '$name'." );
		}
	}
}
