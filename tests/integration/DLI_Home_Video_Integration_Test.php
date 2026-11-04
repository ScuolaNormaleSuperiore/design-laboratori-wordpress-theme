<?php
/**
 * The homepage section "Video": each row of Sezioni HP has its own URL, and the player is the same shared component
 * that the detail pages of events, patents and spin-offs use.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `template-parts/home/hp-video-section.php` and `template-parts/common/sezione-video.php`.
 */
class DLI_Home_Video_Integration_Test extends DLI_Integration_Test_Case {

	const URL_A = 'https://www.youtube.com/watch?v=aaaaaaaaaaa';
	const URL_B = 'https://www.youtube.com/watch?v=bbbbbbbbbbb';

	/**
	 * The current language that Polylang had before the test, put back afterwards: it is a property of the Polylang
	 * object, shared by all the tests, and is not rolled back with the database.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Remember the current language of Polylang.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
	}

	/**
	 * Put back the current language of Polylang.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Add the English language to Polylang and make Italian the current one.
	 */
	private function add_english() {
		$english = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		$this->assertNotWPError( $english );
		PLL()->curlang = PLL()->model->get_language( 'it' );
	}

	/**
	 * Print the section of a row.
	 *
	 * @param array<string, mixed> $row Values of the row, over the defaults.
	 * @return string The HTML.
	 */
	private function render_section( array $row = array() ) {
		ob_start();
		get_template_part(
			'template-parts/home/hp-video-section',
			null,
			array_merge(
				array(
					'id'         => 'video_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'instance'   => 1,
					'video_url'  => self::URL_A,
				),
				$row
			)
		);
		return (string) ob_get_clean();
	}

	/**
	 * Print the shared component, as a detail page does.
	 *
	 * @param array<string, mixed> $args Arguments of the component.
	 * @return string The HTML.
	 */
	private function render_component( array $args ) {
		ob_start();
		get_template_part( 'template-parts/common/sezione-video', null, $args );
		return (string) ob_get_clean();
	}

	/**
	 * Every id of a page.
	 *
	 * @param string $html The HTML.
	 * @return string[]
	 */
	private function ids_of( $html ) {
		preg_match_all( '#\sid="([^"]+)"#', $html, $found );
		return $found[1];
	}

	/**
	 * Without an instance the component prints what the detail pages have always had: the same identifiers.
	 */
	public function test_the_component_without_an_instance_keeps_the_identifiers_of_the_detail_pages() {
		$html = $this->render_component(
			array(
				'video'       => self::URL_A,
				'video_text'  => 'Testo del video',
				'video_title' => 'Un evento',
			)
		);

		$this->assertContains( 'vid1', $this->ids_of( $html ) );
		$this->assertContains( 'chk-remember', $this->ids_of( $html ) );
		$this->assertStringContainsString( 'for="chk-remember"', $html );
		$this->assertContains( 'transcription9', $this->ids_of( $html ) );
		$this->assertStringContainsString( self::URL_A, $html );
	}

	/**
	 * Two rows give two players: each has its URL and no identifier is repeated in the page.
	 */
	public function test_two_rows_have_each_their_url_and_no_repeated_identifier() {
		$first  = $this->render_section(
			array(
				'instance'  => 1,
				'video_url' => self::URL_A,
			)
		);
		$second = $this->render_section(
			array(
				'instance'  => 2,
				'video_url' => self::URL_B,
			)
		);
		$page   = $first . $second;

		$this->assertSame( array_unique( $this->ids_of( $page ) ), $this->ids_of( $page ), 'No identifier is repeated.' );
		$this->assertStringContainsString( 'id="blocco-video-1"', $first );
		$this->assertStringContainsString( 'id="blocco-video-2"', $second );
		$this->assertStringContainsString( self::URL_A, $first );
		$this->assertStringNotContainsString( self::URL_B, $first );
		$this->assertStringContainsString( self::URL_B, $second );
		$this->assertStringNotContainsString( self::URL_A, $second );

		// The label belongs to the checkbox of its own player.
		foreach ( array( $first, $second ) as $html ) {
			$this->assertSame( 1, preg_match( '#<label for="([^"]+)"#', $html, $label ) );
			$this->assertContains( $label[1], $this->ids_of( $html ), 'The label points to a checkbox of the same section.' );
		}
	}

	/**
	 * The button loads the video in the player of its own row, with one shared function that no script declares twice.
	 */
	public function test_each_button_loads_the_video_in_its_own_player() {
		$page = $this->render_section( array( 'instance' => 1, 'video_url' => self::URL_A ) )
			. $this->render_section( array( 'instance' => 2, 'video_url' => self::URL_B ) );

		$this->assertStringNotContainsString( 'const loadYouTubeVideo', $page, 'A second declaration of a const stops the script.' );
		foreach ( array( 1 => self::URL_A, 2 => self::URL_B ) as $instance => $url ) {
			$this->assertSame( 1, preg_match( '#<video[^>]*id="([^"]+)"#', substr( $page, strpos( $page, 'id="blocco-video-' . $instance . '"' ) ), $player ) );
			$this->assertStringContainsString( "dliLoadYouTubeVideo('" . $player[1] . "', '" . $url . "')", $page, "The button of the row $instance loads its own player." );
		}
	}

	/**
	 * A row without its own URL prints nothing: the old global value is not read any more.
	 */
	public function test_a_row_without_a_url_prints_nothing_and_ignores_the_old_global_value() {
		$this->set_theme_options( 'homepage', array( 'home_page_video_url' => self::URL_B ) );

		$this->assertSame( '', trim( $this->render_section( array( 'video_url' => '' ) ) ) );
		$this->assertSame( '', trim( $this->render_section( array( 'video_url' => null ) ) ) );
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertStringContainsString( self::URL_A, $this->render_section(), 'The row has its own value.' );
	}

	/**
	 * An address that is not http or https is never printed, whatever the saved value.
	 */
	public function test_an_address_that_is_not_a_web_address_is_not_printed() {
		$this->assertSame( '', trim( $this->render_section( array( 'video_url' => 'javascript:alert(1)' ) ) ) );
	}

	/**
	 * The field of the row keeps a web address and drops anything else.
	 */
	public function test_the_url_of_the_row_is_checked_on_the_server() {
		$this->assertSame( self::URL_A, dli_sanitize_hp_video_url( '  ' . self::URL_A . ' ' ) );
		$this->assertSame( 'http://example.org/v.mp4', dli_sanitize_hp_video_url( 'http://example.org/v.mp4' ) );
		foreach ( array( '', '   ', 'non è un indirizzo', 'javascript:alert(1)', 'ftp://example.org/v', 'data:text/html,x', array( self::URL_A ), null, 12 ) as $value ) {
			$this->assertSame( '', dli_sanitize_hp_video_url( $value ), 'Not a web address: ' . wp_json_encode( $value ) );
		}
	}

	/**
	 * The background follows the row and is the grey of today when the row does not say.
	 */
	public function test_the_background_follows_the_row_and_defaults_to_grey() {
		$this->assertMatchesRegularExpression( '#<section [^>]*class="section section-muted#', $this->render_section() );
		$this->assertDoesNotMatchRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'background' => 'white' ) ) );
		$this->assertMatchesRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'background' => 'muted' ) ) );
		$this->assertMatchesRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'background' => 'rosso' ) ), 'A background that is not known is the default.' );
	}

	/**
	 * The section Video is one of those that can choose the background.
	 */
	public function test_the_video_section_can_choose_its_background() {
		$this->assertArrayHasKey( 'video_section', DLI_ContentsManager::get_hp_sections_with_background() );
	}

	/**
	 * The title is shown only when the row has it in the language of the page and asks to show it: no other language
	 * takes its place and the section has no title of its own.
	 */
	public function test_the_title_is_shown_only_in_the_language_it_is_written_in() {
		$this->add_english();
		$row = array(
			'title'     => 'Il nostro laboratorio',
			'title_eng' => 'Our laboratory',
		);

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-video-1-title"[^>]*>\s*Il nostro laboratorio\s*</h2>#', $this->render_section( $row ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-video-1-title"[^>]*>\s*Our laboratory\s*</h2>#', $this->render_section( $row ) );
		$this->assertStringNotContainsString( '<h2', $this->render_section( array_merge( $row, array( 'title_eng' => '  ' ) ) ), 'No English title, no title: the Italian one does not take its place.' );

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertStringNotContainsString( '<h2', $this->render_section(), 'Without a title there is none, not even the word Video.' );
		$this->assertStringNotContainsString( '<h2', $this->render_section( array( 'title' => '', 'title_eng' => 'Only English' ) ), 'An Italian page never takes the English title.' );
	}

	/**
	 * With the title not to be shown nothing of it is printed: no heading, hidden or not, and no name for the section.
	 */
	public function test_a_title_that_is_not_to_be_shown_is_not_printed_at_all() {
		$html = $this->render_section(
			array(
				'show_title' => 'false',
				'title'      => 'Il nostro laboratorio',
			)
		);

		$this->assertStringNotContainsString( '<h2', $html );
		$this->assertStringNotContainsString( 'Il nostro laboratorio', $html );
		$this->assertStringNotContainsString( 'aria-labelledby="blocco-video-1-title"', $html );
		$this->assertStringNotContainsString( 'aria-label="', substr( $html, strpos( $html, '<section' ), 200 ), 'The section is not named either.' );
		$this->assertStringContainsString( 'id="blocco-video-1"', $html, 'The section itself is there.' );
	}

	/**
	 * The title is text: markup in it is shown as text (the save takes it out).
	 */
	public function test_the_title_is_escaped() {
		$html = $this->render_section( array( 'title' => 'A <b>bold</b> & title' ) );

		$this->assertStringNotContainsString( '<b>', $html, 'Markup that reaches the template is shown as text, never run.' );
		$this->assertStringContainsString( 'A &lt;b&gt;bold&lt;/b&gt; &amp; title', $html );
		$this->assertStringContainsString( 'aria-labelledby="blocco-video-1-title"', $html );
	}

	/**
	 * The title of a row is plain text of a reasonable length.
	 */
	public function test_the_title_of_a_row_is_checked_on_the_server() {
		$this->assertSame( 'Il nostro laboratorio', dli_sanitize_hp_row_title( "  Il   nostro
 laboratorio  " ) );
		$this->assertSame( 'Bold', dli_sanitize_hp_row_title( '<b>Bold</b>' ) );
		$this->assertSame( DLI_HP_ROW_TITLE_MAX, strlen( dli_sanitize_hp_row_title( str_repeat( 'a', DLI_HP_ROW_TITLE_MAX + 50 ) ) ) );
		foreach ( array( '', '   ', array( 'x' ), null, 12 ) as $value ) {
			$this->assertSame( '', dli_sanitize_hp_row_title( $value ), 'Not a title: ' . wp_json_encode( $value ) );
		}
	}

	/**
	 * The fields of the title are in the rows of the sections Video and Banner.
	 */
	public function test_the_video_and_banner_sections_have_the_title_fields() {
		$sections = array_keys( DLI_ContentsManager::get_hp_sections_with_title() );
		sort( $sections );
		$this->assertSame( array( 'banners_section', 'video_section' ), $sections );
	}

	/**
	 * Only the section Video has the field of the URL.
	 */
	public function test_only_the_video_section_has_the_url_field() {
		$this->assertSame( array( 'video_section' => true ), DLI_ContentsManager::get_hp_sections_with_video_url() );
	}
}
