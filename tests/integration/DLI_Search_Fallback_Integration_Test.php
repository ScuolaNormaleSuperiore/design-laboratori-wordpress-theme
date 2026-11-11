<?php
/**
 * The native search of WordPress (`/?s=term`) goes to the search page of the theme, and `index.php` is a real fallback.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `search.php` and `index.php`.
 */
class DLI_Search_Fallback_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * HTTP status codes that WordPress was asked to send while the test ran.
	 *
	 * @var int[]
	 */
	private $status_codes = array();

	/**
	 * Addresses that wp_redirect() was asked to go to while the test ran.
	 *
	 * @var string[]
	 */
	private $redirects = array();

	/**
	 * Remember the status codes and the redirects, and do not send the redirects.
	 */
	public function set_up() {
		parent::set_up();
		$this->status_codes = array();
		$this->redirects    = array();
		add_filter( 'status_header', array( $this, 'record_status' ), 10, 2 );
		add_filter( 'wp_redirect', array( $this, 'record_redirect' ), 10, 1 );
	}

	/**
	 * Record a status code and leave the header as it is.
	 *
	 * @param string $header      Header text.
	 * @param int    $status_code Status code.
	 * @return string The header.
	 */
	public function record_status( $header, $status_code ) {
		$this->status_codes[] = (int) $status_code;
		return $header;
	}

	/**
	 * Record a redirect and cancel it: the test must go on, and `exit` is reached only when WordPress sends it.
	 *
	 * @param string $location Address of the redirect.
	 * @return false
	 */
	public function record_redirect( $location ) {
		$this->redirects[] = $location;
		return false;
	}

	/**
	 * Request the native search for a term and run `search.php`, as WordPress does for that request.
	 *
	 * @param string $term Term, already encoded for the address.
	 * @return string Address of the redirect that the template asked for, or an empty string.
	 */
	private function run_search_template( $term ) {
		$this->go_to( '/?s=' . $term );
		$this->assertTrue( is_search(), 'The request must be a search.' );

		ob_start();
		include get_template_directory() . '/search.php';
		ob_end_clean();

		return empty( $this->redirects ) ? '' : $this->redirects[0];
	}

	/**
	 * A search goes to the search page of the theme, with the term in `searchstring`.
	 */
	public function test_native_search_goes_to_the_search_page_of_the_theme() {
		$location = $this->run_search_template( 'laboratorio' );

		$this->assertNotSame( '', $location, 'The search must redirect.' );
		$this->assertStringContainsString( '/' . SLUG_RICERCA_SITO_IT . '/', $location );
		$this->assertStringContainsString( 'searchstring=laboratorio', $location );
	}

	/**
	 * The term is encoded, so that no character of it can change the address.
	 */
	public function test_the_term_is_encoded_in_the_address() {
		$location = $this->run_search_template( rawurlencode( 'a b&c=d#' ) );

		$this->assertStringContainsString( 'searchstring=a%20b%26c%3Dd%23', $location );
		$this->assertSame( 1, substr_count( $location, '?' ), 'Only one query string.' );
	}

	/**
	 * Markup in the term is removed before the term goes in the address.
	 */
	public function test_markup_in_the_term_is_removed() {
		$location = $this->run_search_template( rawurlencode( '<b>laboratorio</b><script>alert(1)</script>' ) );

		$this->assertStringContainsString( 'searchstring=laboratorio', $location );
		$this->assertStringNotContainsString( '%3C', $location );
		$this->assertStringNotContainsString( 'script', $location );
	}

	/**
	 * Backslashes of the term are kept: WordPress has already removed the escaping of the request.
	 *
	 * In production `get_query_var( 's' )` holds the term as the visitor wrote it. The test framework does not
	 * escape the request, so the query variable is set to that final value after the request.
	 */
	public function test_backslashes_in_the_term_are_kept() {
		$this->go_to( '/?s=prova' );
		set_query_var( 's', 'C:\\documenti' );

		ob_start();
		include get_template_directory() . '/search.php';
		ob_end_clean();

		$this->assertNotEmpty( $this->redirects );
		$this->assertStringContainsString( 'searchstring=C%3A%5Cdocumenti', $this->redirects[0] );
	}

	/**
	 * A search without a term opens the search page with no term.
	 */
	public function test_a_search_without_a_term_opens_the_empty_search_page() {
		$location = $this->run_search_template( '' );

		$this->assertStringContainsString( '/' . SLUG_RICERCA_SITO_IT . '/', $location );
		$this->assertStringNotContainsString( 'searchstring', $location );
	}

	/**
	 * A very long term is cut: the address stays within a sensible size.
	 */
	public function test_a_very_long_term_is_cut() {
		$location = $this->run_search_template( str_repeat( 'a', 500 ) );

		$this->assertStringContainsString( 'searchstring=' . str_repeat( 'a', 100 ), $location );
		$this->assertStringNotContainsString( str_repeat( 'a', 101 ), $location );
	}

	/**
	 * The redirect never leaves the site, whatever the term is.
	 */
	public function test_the_redirect_stays_on_the_site() {
		$location = $this->run_search_template( rawurlencode( 'https://example.org/' ) );

		$this->assertSame( wp_parse_url( home_url() )['host'], wp_parse_url( $location )['host'] );
	}

	/**
	 * A page of an attachment has no template of its own in the theme: it reaches `index.php`.
	 *
	 * @return string Rendered HTML.
	 */
	private function render_the_fallback_template() {
		$attachment_id = self::factory()->attachment->create_object(
			array(
				'file'           => 'prova.jpg',
				'post_mime_type' => 'image/jpeg',
				'post_title'     => 'Immagine di prova',
			)
		);
		$this->go_to( get_attachment_link( $attachment_id ) );
		$this->assertTrue( is_attachment(), 'The request must be the page of an attachment.' );

		// WordPress has defined this constant before it loads any template: define it as it does.
		if ( ! defined( 'WP_USE_THEMES' ) ) {
			define( 'WP_USE_THEMES', true );
		}

		return $this->render_theme_template( 'index.php' );
	}

	/**
	 * The fallback template is a 404, not an empty page with status 200.
	 */
	public function test_the_fallback_template_is_not_found() {
		$html = $this->render_the_fallback_template();

		$this->assertTrue( is_404(), 'The main query must be a 404.' );
		$this->assertContains( 404, $this->status_codes, 'WordPress must be asked to send the status 404.' );
		$this->assertNotSame( '', trim( $html ), 'The page must not be empty.' );
	}

	/**
	 * The 404 page of the theme is the one that is shown.
	 */
	public function test_the_fallback_template_shows_the_404_page_of_the_theme() {
		$this->assertStringContainsString( 'dli-hero-404-title', $this->render_the_fallback_template() );
	}

	/**
	 * Control: a page is still a page, not a 404 and not a search: it never reaches `index.php` nor `search.php`.
	 */
	public function test_a_page_is_still_a_page() {
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Pagina di prova',
			)
		);
		dli_set_post_language( $page_id, 'it' );

		$this->go_to( get_permalink( $page_id ) );

		$this->assertTrue( is_page() );
		$this->assertFalse( is_404() );
		$this->assertFalse( is_search() );
		$this->assertStringEndsWith( '/page.php', get_page_template() );
	}
}
