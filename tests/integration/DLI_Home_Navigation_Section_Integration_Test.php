<?php
/**
 * The homepage section "Card di navigazione": the pages it lists and how it is printed.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_get_promoted_page_cards()` and `template-parts/home/hp-navigation-section.php`.
 */
class DLI_Home_Navigation_Section_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards: it is a property of the
	 * Polylang object, which is shared by all the tests, and is not rolled back with the database.
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
	 * Create a page in Italian, promoted to the home unless said otherwise.
	 *
	 * @param string               $title Title.
	 * @param array<string, mixed> $args  Extra arguments of wp_insert_post().
	 * @param bool                 $promoted Whether the field "Promuovi in Home" is on.
	 * @return int Page ID.
	 */
	private function create_page( $title, array $args = array(), $promoted = true ) {
		$page_id = self::factory()->post->create(
			array_merge(
				array(
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_status'  => 'publish',
					'post_excerpt' => '',
					'post_content' => '',
				),
				$args
			)
		);
		dli_set_post_language( $page_id, 'it' );
		if ( $promoted ) {
			$this->set_acf_fields( $page_id, array( 'promuovi_in_home' => 1 ) );
		}
		return $page_id;
	}

	/**
	 * Print the section for a row of Sezioni HP.
	 *
	 * @param array<string, mixed> $row Properties of the row, on top of the defaults.
	 * @return string
	 */
	private function render_section( array $row = array() ) {
		ob_start();
		get_template_part(
			'template-parts/home/hp-navigation-section',
			null,
			array_merge(
				array(
					'id'         => 'navigation_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'instance'   => 1,
				),
				$row
			)
		);
		return (string) ob_get_clean();
	}

	/**
	 * Only promoted, published, unprotected pages, by the order of the page and then by title.
	 */
	public function test_cards_are_the_promoted_published_pages_in_order() {
		$later = $this->create_page( 'Zeta', array( 'menu_order' => 2 ) );
		$first = $this->create_page( 'Beta', array( 'menu_order' => 1 ) );
		$tie_a = $this->create_page( 'Alfa pari', array( 'menu_order' => 3 ) );
		$tie_b = $this->create_page( 'Beta pari', array( 'menu_order' => 3 ) );
		$this->create_page( 'Non promossa', array(), false );
		$this->create_page( 'Bozza', array( 'post_status' => 'draft' ) );
		$this->create_page( 'Protetta', array( 'post_password' => 'segreto' ) );

		$cards = dli_get_promoted_page_cards();

		$this->assertSame( array( $first, $later, $tie_a, $tie_b ), wp_list_pluck( $cards, 'id' ), 'By the field Ordine, then by title.' );
		$this->assertSame( array( 'Beta', 'Zeta', 'Alfa pari', 'Beta pari' ), wp_list_pluck( $cards, 'title' ) );
		$this->assertSame( get_permalink( $first ), $cards[0]['url'] );
	}

	/**
	 * Never more than the configured number, the first ones by order.
	 */
	public function test_cards_stop_at_the_configured_maximum() {
		$ids = array();
		for ( $index = 1; $index <= DLI_HP_NAVIGATION_DEFAULT_ITEMS + 2; $index++ ) {
			$ids[] = $this->create_page( 'Pagina ' . $index, array( 'menu_order' => $index ) );
		}

		$cards = dli_get_promoted_page_cards();

		$this->assertCount( DLI_HP_NAVIGATION_DEFAULT_ITEMS, $cards );
		$this->assertSame( array_slice( $ids, 0, DLI_HP_NAVIGATION_DEFAULT_ITEMS ), wp_list_pluck( $cards, 'id' ) );
		$this->assertCount( 2, dli_get_promoted_page_cards( 2 ), 'The limit can be asked for.' );
	}

	/**
	 * A protected page does not take a place: the six are the six that can be shown.
	 */
	public function test_protected_pages_do_not_take_a_place() {
		for ( $index = 1; $index <= 3; $index++ ) {
			$this->create_page( 'Protetta ' . $index, array( 'menu_order' => $index, 'post_password' => 'segreto' ) );
		}
		for ( $index = 4; $index <= 9; $index++ ) {
			$this->create_page( 'Visibile ' . $index, array( 'menu_order' => $index ) );
		}

		$this->assertCount( DLI_HP_NAVIGATION_DEFAULT_ITEMS, dli_get_promoted_page_cards() );
	}

	/**
	 * The pages of the current language only.
	 */
	public function test_cards_are_in_the_current_language() {
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
		$italian_page = $this->create_page( 'Pagina italiana' );
		$english_page = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'English page',
			)
		);
		dli_set_post_language( $english_page, 'en' );
		$this->set_acf_fields( $english_page, array( 'promuovi_in_home' => 1 ) );

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertSame( array( $italian_page ), wp_list_pluck( dli_get_promoted_page_cards(), 'id' ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( array( $english_page ), wp_list_pluck( dli_get_promoted_page_cards(), 'id' ) );
	}

	/**
	 * The abstract is the summary of the page, or the beginning of its text.
	 */
	public function test_cards_have_the_summary_or_the_cut_text() {
		$this->create_page( 'Con riassunto', array( 'menu_order' => 1, 'post_excerpt' => 'Riassunto scritto.' ) );
		$this->create_page( 'Con testo', array( 'menu_order' => 2, 'post_content' => '<p>Testo della pagina.</p>' ) );

		$cards = dli_get_promoted_page_cards();

		$this->assertSame( 'Riassunto scritto.', $cards[0]['abstract'] );
		$this->assertSame( 'Testo della pagina.', $cards[1]['abstract'] );
	}

	/**
	 * Without promoted pages, or when the row is off, nothing is printed.
	 */
	public function test_nothing_is_printed_without_pages_or_when_disabled() {
		$this->assertSame( '', trim( $this->render_section() ), 'No promoted page, no empty section.' );

		$this->create_page( 'Visibile' );
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertStringContainsString( 'Visibile', $this->render_section() );
	}

	/**
	 * Title and text come from Layout HP in the language of the page; the title can be hidden.
	 */
	public function test_title_and_text_come_from_the_configuration() {
		$this->create_page( 'Visibile' );
		$this->set_theme_options(
			'homepage',
			array(
				'home_navigazione_title' => 'Cosa puoi fare',
				'home_navigazione_text'  => 'I servizi più richiesti.',
			)
		);

		$html = $this->render_section();

		$this->assertMatchesRegularExpression( '#<h2 id="blocco-navigazione-1-title"[^>]*>Cosa puoi fare</h2>#', $html );
		$this->assertStringContainsString( 'aria-labelledby="blocco-navigazione-1-title"', $html );
		$this->assertStringContainsString( 'I servizi più richiesti.', $html );

		$hidden = $this->render_section( array( 'show_title' => 'false' ) );
		$this->assertStringNotContainsString( '<h2', $hidden );
		$this->assertStringNotContainsString( 'aria-labelledby', $hidden );
		$this->assertMatchesRegularExpression( '#<section [^>]*aria-label="#', $hidden, 'Without a visible title the section keeps an accessible name.' );

		// An empty array would leave the options as they are in memory: the title is emptied explicitly.
		$this->set_theme_options( 'homepage', array( 'home_navigazione_title' => '' ) );
		$empty = $this->render_section();
		$this->assertStringNotContainsString( '<h2', $empty, 'An empty title is not printed, even if it should be shown.' );
		$this->assertStringContainsString( 'Visibile', $empty );
	}

	/**
	 * Each copy of the section has its own identifiers.
	 */
	public function test_identifiers_follow_the_instance() {
		$this->create_page( 'Visibile' );
		$this->set_theme_options( 'homepage', array( 'home_navigazione_title' => 'Titolo' ) );

		$first  = $this->render_section( array( 'instance' => 1 ) );
		$second = $this->render_section( array( 'instance' => 4 ) );

		$this->assertStringContainsString( 'id="blocco-navigazione-1"', $first );
		$this->assertStringContainsString( 'id="blocco-navigazione-4"', $second );
		$this->assertStringNotContainsString( 'id="blocco-navigazione-1"', $second );
	}

	/**
	 * The background is grey by default, white or grey when the row says so.
	 */
	public function test_background_follows_the_row_and_defaults_to_muted() {
		$this->create_page( 'Visibile' );

		$this->assertMatchesRegularExpression( '#<section [^>]*class="section section-muted#', $this->render_section() );
		$this->assertDoesNotMatchRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'background' => 'white' ) ) );
		$this->assertMatchesRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'background' => 'muted' ) ) );
	}

	/**
	 * Each page is a card with a meaningful link; the columns follow the number of cards.
	 */
	public function test_cards_have_links_and_balanced_columns() {
		$pages = array();
		foreach ( array( 'Uno', 'Due', 'Tre', 'Quattro', 'Cinque' ) as $index => $title ) {
			$pages[] = $this->create_page( $title, array( 'menu_order' => $index, 'post_excerpt' => 'Riassunto di ' . $title ) );
		}

		$html = $this->render_section();

		$this->assertSame( 5, substr_count( $html, 'class="it-card ' ) );
		$this->assertStringContainsString( 'href="' . esc_url( get_permalink( $pages[0] ) ) . '"', $html );
		$this->assertStringContainsString( 'Riassunto di Cinque', $html );
		$this->assertSame( 5, substr_count( $html, 'col-12 col-md-6 col-lg-4' ), 'Five cards: three per row.' );
		$this->assertStringContainsString( 'justify-content-center', $html, 'The last row, not full, is centered.' );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
	}

	/**
	 * Promoting is a field of the page, with the same key the home sections read.
	 */
	public function test_the_section_is_in_the_catalog_with_a_default_background() {
		$this->assertArrayHasKey( 'navigation_section', DLI_ContentsManager::get_hp_sections() );
		$this->assertSame( 'muted', DLI_ContentsManager::get_hp_sections_with_background()['navigation_section'] );
	}

	/**
	 * The number of cards is the one chosen in the row; without it, the default.
	 */
	public function test_the_number_of_cards_follows_the_row() {
		for ( $index = 1; $index <= 9; $index++ ) {
			$this->create_page( 'Pagina ' . $index, array( 'menu_order' => $index ) );
		}

		$default = $this->render_section();
		$two     = $this->render_section( array( 'max_items' => 2 ) );
		$nine    = $this->render_section( array( 'max_items' => 9 ) );

		$this->assertSame( DLI_HP_NAVIGATION_DEFAULT_ITEMS, substr_count( $default, 'class="it-card ' ), 'No choice: the default number.' );
		$this->assertSame( 2, substr_count( $two, 'class="it-card ' ) );
		$this->assertStringContainsString( 'Pagina 2', $two );
		$this->assertStringNotContainsString( 'Pagina 3', $two, 'The first ones by order.' );
		$this->assertSame( 9, substr_count( $nine, 'class="it-card ' ) );
		$this->assertSame( 2, substr_count( $two, 'col-md-6' ), 'The columns follow the number shown: two cards, two columns.' );
	}

	/**
	 * A number that is not usable is ignored: the default applies and nothing breaks.
	 */
	public function test_a_wrong_number_of_cards_uses_the_default() {
		for ( $index = 1; $index <= 8; $index++ ) {
			$this->create_page( 'Pagina ' . $index, array( 'menu_order' => $index ) );
		}

		$text  = $this->render_section( array( 'max_items' => 'tante' ) );
		$empty = $this->render_section( array( 'max_items' => '' ) );
		$huge  = $this->render_section( array( 'max_items' => 500 ) );

		$this->assertSame( DLI_HP_NAVIGATION_DEFAULT_ITEMS, substr_count( $text, 'class="it-card ' ) );
		$this->assertSame( DLI_HP_NAVIGATION_DEFAULT_ITEMS, substr_count( $empty, 'class="it-card ' ) );
		$this->assertSame( 8, substr_count( $huge, 'class="it-card ' ), 'Brought to the maximum, which here is more than the pages there are.' );
	}

	/**
	 * Only the sections that declare it let the editor choose the number, and the default is in range.
	 */
	public function test_sections_with_a_number_of_items_are_catalog_sections() {
		$with_items = DLI_ContentsManager::get_hp_sections_with_max_items();

		$this->assertArrayHasKey( 'navigation_section', $with_items );
		$this->assertSame( array(), array_diff_key( $with_items, DLI_ContentsManager::get_hp_sections() ) );
		foreach ( $with_items as $section_id => $default ) {
			$this->assertGreaterThanOrEqual( DLI_HP_ITEMS_MIN, $default, "Default of {$section_id}." );
			$this->assertLessThanOrEqual( DLI_HP_ITEMS_MAX, $default, "Default of {$section_id}." );
		}
	}
}

