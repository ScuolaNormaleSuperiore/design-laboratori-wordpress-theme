<?php
/**
 * The notice of the media window: the limits of the upload and, under them, the general advice on the format of the
 * images, with the link to the guide when its address is known.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_LabManager::enqueue_upload_limits_script()` and `dli_get_media_window_guideline_html()`.
 */
class DLI_Media_Window_Guideline_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The texts of the theme are written in Italian, and the test site is in English: run every test with the
	 * Italian texts, so that the checks do not depend on the English catalogue.
	 */
	public function set_up() {
		parent::set_up();
		$this->use_italian_texts();
	}

	/**
	 * Load the translations of the theme again.
	 */
	public function tear_down() {
		$this->restore_theme_translations();
		parent::tear_down();
	}

	/**
	 * The inline script that the theme adds to `media-views`, and nothing else.
	 *
	 * @return string
	 */
	private function inline_script() {
		wp_scripts()->add_data( 'media-views', 'after', array() );
		do_action( 'wp_enqueue_media' );
		return implode( "\n", (array) wp_scripts()->get_data( 'media-views', 'after' ) );
	}

	/**
	 * The notice that the script writes in the window, read back from the JSON that the theme embeds.
	 *
	 * @return string The HTML of the notice.
	 */
	private function notice_html() {
		$script = $this->inline_script();
		$this->assertSame( 1, preg_match( '/var noticeHtml = (".*?");\s*wp\.media\.view\.UploaderInline/s', $script, $matches ), 'The notice is not in the script.' );
		$notice = json_decode( $matches[1], true );
		$this->assertIsString( $notice, 'The notice is not valid JSON, so the script would break.' );
		return $notice;
	}

	/**
	 * The limits of the upload are still told, as before.
	 */
	public function test_the_limits_are_still_in_the_notice() {
		$notice = $this->notice_html();

		$this->assertStringContainsString( 'Dimensione massima consentita sul Sito Federato', $notice );
		$this->assertStringContainsString( 'PDF 2 MB', $notice );
	}

	/**
	 * Under the limits the notice gives the general advice: 16:9, at least 1200 px, subject at the centre.
	 */
	public function test_the_notice_gives_the_general_advice_on_the_format() {
		$notice = $this->notice_html();

		$this->assertStringContainsString( '16:9', $notice );
		$this->assertStringContainsString( '1200 px', $notice );
		$this->assertStringContainsString( 'soggetto al centro', $notice );
		$this->assertStringContainsString( 'indicative', $notice );
	}

	/**
	 * The notice links the guide, with the address of the configuration, in a new window.
	 */
	public function test_the_notice_links_the_guide() {
		$notice = $this->notice_html();

		$this->assertStringContainsString( '<a href="' . esc_url( DLI_IMAGE_GUIDE_URL ) . '"', $notice );
		$this->assertStringContainsString( 'Guida alle immagini', $notice );
		$this->assertStringContainsString( 'target="_blank"', $notice );
	}

	/**
	 * Without an address (an empty configuration) the notice has no link.
	 */
	public function test_there_is_no_link_without_an_address() {
		$this->assertStringNotContainsString( '<a ', dli_get_media_window_guideline_html( '' ) );
	}

	/**
	 * With an address, the guide is linked safely, in a new window.
	 */
	public function test_the_guide_is_linked_when_its_address_is_known() {
		$html = dli_get_media_window_guideline_html( 'https://example.org/guida/immagini/' );

		$this->assertStringContainsString( '<a href="https://example.org/guida/immagini/"', $html );
		$this->assertStringContainsString( 'rel="noopener noreferrer"', $html );
		$this->assertStringContainsString( 'Guida alle immagini', $html );
	}

	/**
	 * The address is escaped: a hostile one cannot close the attribute or add markup.
	 */
	public function test_the_address_of_the_guide_is_escaped() {
		$html = dli_get_media_window_guideline_html( 'https://example.org/"><script>alert(1)</script>' );

		$this->assertStringNotContainsString( '<script>', $html );
		$this->assertStringNotContainsString( '"><', $html );
	}

	/**
	 * An address that is not http or https is not linked.
	 */
	public function test_an_address_with_another_scheme_is_not_linked() {
		$this->assertStringNotContainsString( '<a ', dli_get_media_window_guideline_html( 'javascript:alert(1)' ) );
	}
}
