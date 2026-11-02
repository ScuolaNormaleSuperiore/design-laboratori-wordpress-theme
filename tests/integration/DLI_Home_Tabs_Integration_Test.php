<?php
/**
 * The homepage section "Pannello tab": the tabs it reads, the pages it links and how it is printed.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_get_tab_pages()`, `dli_get_home_tabs()` and `template-parts/home/hp-tabs-section.php`.
 */
class DLI_Home_Tabs_Integration_Test extends DLI_Integration_Test_Case {

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
	 * Switch the current language of Polylang.
	 *
	 * @param string $slug `it` or `en`.
	 */
	private function use_language( $slug ) {
		PLL()->curlang = PLL()->model->get_language( $slug );
	}

	/**
	 * Create a page in a language.
	 *
	 * @param string               $title Title.
	 * @param string               $lang  Language slug.
	 * @param array<string, mixed> $args  Extra arguments of wp_insert_post().
	 * @return int Page ID.
	 */
	private function create_page( $title, $lang = 'it', array $args = array() ) {
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
		dli_set_post_language( $page_id, $lang );
		return $page_id;
	}

	/**
	 * Save the tabs in Layout HP, as the group `home_tabs_items` does.
	 *
	 * @param array<int, array<string, mixed>> $tabs   The rows of the group.
	 * @param array<string, mixed>             $extra  Other options of Layout HP, for example the title.
	 */
	private function set_tabs( array $tabs, array $extra = array() ) {
		$this->set_theme_options( 'homepage', array_merge( array( 'home_tabs_items' => $tabs ), $extra ) );
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
			'template-parts/home/hp-tabs-section',
			null,
			array_merge(
				array(
					'id'         => 'tabs_section',
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
	 * Only published pages without password, of the type page, are linked.
	 */
	public function test_linked_pages_are_published_unprotected_pages() {
		$this->add_english();
		$published = $this->create_page( 'Pubblicata' );
		$draft     = $this->create_page( 'Bozza', 'it', array( 'post_status' => 'draft' ) );
		$protected = $this->create_page( 'Protetta', 'it', array( 'post_password' => 'segreto' ) );
		$article   = $this->create_page( 'Articolo', 'it', array( 'post_type' => 'post' ) );

		$pages = dli_get_tab_pages( array( $published, $draft, $protected, $article, 999999 ) );

		$this->assertSame( array( $published ), wp_list_pluck( $pages, 'id' ), 'Draft, protected, other types and missing pages are left out.' );
		$this->assertSame( 'Pubblicata', $pages[0]['title'] );
		$this->assertSame( get_permalink( $published ), $pages[0]['url'] );
		$this->assertSame( array(), dli_get_tab_pages( array() ) );
	}

	/**
	 * The selected pages are the Italian ones: in English the translation is shown, never the Italian page.
	 */
	public function test_linked_pages_are_translated_in_the_current_language() {
		$this->add_english();
		$italian_a = $this->create_page( 'Pagina A' );
		$english_a = $this->create_page( 'Page A', 'en' );
		$italian_b = $this->create_page( 'Pagina B' );
		dli_save_post_translations(
			array(
				'it' => $italian_a,
				'en' => $english_a,
			)
		);

		$this->use_language( 'it' );
		$this->assertSame( array( $italian_a, $italian_b ), wp_list_pluck( dli_get_tab_pages( array( $italian_a, $italian_b ) ), 'id' ) );

		$this->use_language( 'en' );
		$pages = dli_get_tab_pages( array( $italian_a, $italian_b ) );
		$this->assertSame( array( $english_a ), wp_list_pluck( $pages, 'id' ), 'A page without a translation is not shown.' );
		$this->assertSame( 'Page A', $pages[0]['title'] );
	}

	/**
	 * A translation that is a draft or is protected is not linked either.
	 */
	public function test_a_translation_that_cannot_be_shown_is_left_out() {
		$this->add_english();
		$italian_draft = $this->create_page( 'Bozza' );
		$english_draft = $this->create_page( 'Draft', 'en', array( 'post_status' => 'draft' ) );
		$italian_lock  = $this->create_page( 'Chiusa' );
		$english_lock  = $this->create_page( 'Locked', 'en', array( 'post_password' => 'secret' ) );
		dli_save_post_translations(
			array(
				'it' => $italian_draft,
				'en' => $english_draft,
			)
		);
		dli_save_post_translations(
			array(
				'it' => $italian_lock,
				'en' => $english_lock,
			)
		);

		$this->use_language( 'en' );

		$this->assertSame( array(), dli_get_tab_pages( array( $italian_draft, $italian_lock ) ) );
	}

	/**
	 * The saved group is read in the language of the page, with the pages resolved.
	 */
	public function test_home_tabs_follow_the_language_and_resolve_the_pages() {
		$this->add_english();
		$italian_page = $this->create_page( 'Pagina' );
		$english_page = $this->create_page( 'Page', 'en' );
		dli_save_post_translations(
			array(
				'it' => $italian_page,
				'en' => $english_page,
			)
		);
		$this->set_tabs(
			array(
				array(
					'etichetta'       => 'Aree',
					'etichetta_eng'   => 'Areas',
					'descrizione'     => '<p>Testo</p>',
					'descrizione_eng' => '<p>Text</p>',
					'pagine'          => array( (string) $italian_page ),
				),
				array(
					'etichetta'   => 'Solo italiano',
					'descrizione' => '<p>Tutto in italiano</p>',
				),
			)
		);

		$this->use_language( 'it' );
		$tabs = dli_get_home_tabs();
		$this->assertSame( array( 'Aree', 'Solo italiano' ), wp_list_pluck( $tabs, 'label' ) );
		$this->assertSame( array( $italian_page ), wp_list_pluck( $tabs[0]['pages'], 'id' ) );

		$this->use_language( 'en' );
		$tabs = dli_get_home_tabs();
		$this->assertSame( array( 'Areas', 'Solo italiano' ), wp_list_pluck( $tabs, 'label' ), 'A missing English label is the Italian one.' );
		$this->assertSame( '<p>Text</p>', $tabs[0]['text'] );
		$this->assertSame( '<p>Tutto in italiano</p>', $tabs[1]['text'] );
		$this->assertSame( array( $english_page ), wp_list_pluck( $tabs[0]['pages'], 'id' ) );
		$this->assertSame( array(), $tabs[1]['pages'] );
	}

	/**
	 * A tab that has no text and whose pages cannot be shown is left out: no empty panel.
	 */
	public function test_a_tab_without_anything_to_show_is_left_out() {
		$this->add_english();
		$draft = $this->create_page( 'Bozza', 'it', array( 'post_status' => 'draft' ) );
		$this->set_tabs(
			array(
				array(
					'etichetta' => 'Solo bozza',
					'pagine'    => array( $draft ),
				),
				array(
					'etichetta'   => 'Con testo',
					'descrizione' => '<p>Testo</p>',
				),
				array(
					'etichetta'   => '',
					'descrizione' => '<p>Senza etichetta</p>',
				),
			)
		);

		$this->assertSame( array( 'Con testo' ), wp_list_pluck( dli_get_home_tabs(), 'label' ) );
	}

	/**
	 * Nothing is printed when the row is off, or when there is no tab to show.
	 */
	public function test_nothing_is_printed_when_disabled_or_without_tabs() {
		$this->add_english();
		$this->set_tabs( array() );
		$this->assertSame( '', trim( $this->render_section() ), 'No tab, no empty section.' );

		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo</p>',
				),
			)
		);
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertStringContainsString( 'Uno', $this->render_section() );
	}

	/**
	 * Tabs and panels are bound by their identifiers, the first one is open, and one tab is a valid panel.
	 */
	public function test_tabs_and_panels_are_bound_and_the_first_is_open() {
		$this->add_english();
		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo uno</p>',
				),
				array(
					'etichetta'   => 'Due',
					'descrizione' => '<p>Testo due</p>',
				),
			)
		);

		$html = $this->render_section();

		$this->assertSame( 2, substr_count( $html, 'role="tab"' ) );
		$this->assertSame( 2, substr_count( $html, 'role="tabpanel"' ) );
		$this->assertSame( 1, substr_count( $html, 'aria-selected="true"' ), 'Only the first tab is selected.' );
		$this->assertMatchesRegularExpression( '#id="tabs-1-tab-1"[^>]*aria-selected="true"|aria-selected="true"[^>]*id="tabs-1-tab-1"#', $html );
		preg_match_all( '#aria-controls="([^"]+)"#', $html, $controls );
		preg_match_all( '#role="tabpanel"[^>]*#', $html, $panels );
		foreach ( $controls[1] as $panel_id ) {
			$this->assertStringContainsString( 'id="' . $panel_id . '"', $html, 'Every tab points to a panel that exists.' );
		}
		foreach ( array( 'tabs-1-tab-1', 'tabs-1-tab-2' ) as $tab_id ) {
			$this->assertStringContainsString( 'aria-labelledby="' . $tab_id . '"', $html, 'Every panel is named by its tab.' );
		}
		$this->assertStringContainsString( 'Testo uno', $html );

		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo uno</p>',
				),
			)
		);
		$single = $this->render_section();
		$this->assertSame( 1, substr_count( $single, 'role="tab"' ), 'A single tab is a valid panel.' );
	}

	/**
	 * Every copy of the section has its own identifiers, also with the same set of tabs.
	 */
	public function test_identifiers_are_unique_for_each_instance() {
		$this->add_english();
		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo uno</p>',
				),
				array(
					'etichetta'   => 'Due',
					'descrizione' => '<p>Testo due</p>',
				),
			)
		);

		$page = $this->render_section( array( 'instance' => 1 ) ) . $this->render_section( array( 'instance' => 3 ) );

		preg_match_all( '#\sid="([^"]+)"#', $page, $matches );
		$this->assertSame( array_unique( $matches[1] ), $matches[1], 'No identifier is repeated in the page.' );
		$this->assertStringContainsString( 'id="blocco-tabs-1"', $page );
		$this->assertStringContainsString( 'id="blocco-tabs-3"', $page );
	}

	/**
	 * The description is cleaned when it is printed, whatever is in the database.
	 */
	public function test_the_description_is_sanitized_when_printed() {
		$this->add_english();
		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<script>alert(1)</script><p onclick="x()">Testo <a href="https://example.org/">link</a></p>',
				),
			)
		);

		$html = $this->render_section();

		$this->assertStringNotContainsString( '<script', $html );
		$this->assertStringNotContainsString( 'onclick', $html );
		$this->assertStringContainsString( '<a href="https://example.org/">link</a>', $html );
	}

	/**
	 * The title is printed only when the row asks for it; the section always has an accessible name.
	 */
	public function test_title_and_text_come_from_the_configuration() {
		$this->add_english();
		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo</p>',
				),
			),
			array(
				'home_tabs_title' => 'Le nostre aree',
				'home_tabs_text'  => 'Una scelta.',
			)
		);

		$html = $this->render_section();
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-tabs-1-title"[^>]*>Le nostre aree</h2>#', $html );
		$this->assertStringContainsString( 'aria-labelledby="blocco-tabs-1-title"', $html );
		$this->assertStringContainsString( 'Una scelta.', $html );

		$hidden = $this->render_section( array( 'show_title' => 'false' ) );
		$this->assertStringNotContainsString( '<h2', $hidden );
		$this->assertMatchesRegularExpression( '#<section [^>]*aria-label="#', $hidden );
	}

	/**
	 * The section carries the class that its list of links is styled by (no underline at rest, see assets/css/main.css).
	 */
	public function test_the_section_carries_the_class_of_the_list_style() {
		$this->add_english();
		$this->set_tabs(
			array(
				array(
					'etichetta'   => 'Uno',
					'descrizione' => '<p>Testo</p>',
				),
			)
		);

		$this->assertMatchesRegularExpression( '#<section [^>]*class="section tabs-section[ "]#', $this->render_section() );
	}

	/**
	 * The linked pages are printed as a list of links in the language of the page.
	 */
	public function test_linked_pages_are_printed_as_links() {
		$this->add_english();
		$page = $this->create_page( 'Pagina collegata' );
		$this->set_tabs(
			array(
				array(
					'etichetta' => 'Con pagine',
					'pagine'    => array( $page ),
				),
			)
		);

		$html = $this->render_section();

		$this->assertStringContainsString( 'href="' . esc_url( get_permalink( $page ) ) . '"', $html );
		$this->assertStringContainsString( 'Pagina collegata', $html );
	}

	/**
	 * The choice of the pages: the published pages of the default language, in tree order, subpages indented.
	 */
	public function test_select_lists_the_pages_as_a_tree() {
		$this->add_english();
		$root       = $this->create_page( 'Radice', 'it', array( 'menu_order' => 1 ) );
		$child      = $this->create_page( 'Figlia', 'it', array( 'post_parent' => $root ) );
		$grandchild = $this->create_page( 'Nipote', 'it', array( 'post_parent' => $child ) );
		$other      = $this->create_page( 'Altra', 'it', array( 'menu_order' => 2 ) );
		$english    = $this->create_page( 'English page', 'en' );
		$this->create_page( 'Bozza', 'it', array( 'post_status' => 'draft' ) );
		$this->create_page( 'Protetta', 'it', array( 'post_password' => 'segreto' ) );

		$options = dli_get_pages_for_select();

		$this->assertSame( array( $root, $child, $grandchild, $other ), array_keys( $options ), 'Tree order; no draft, no protected page, no page of another language.' );
		$this->assertSame( array( 'Radice', '— Figlia', '— — Nipote', 'Altra' ), array_values( $options ) );
		$this->assertArrayNotHasKey( $english, $options );
	}

	/**
	 * A page whose parent is not in the list keeps its own subtree, starting from the first level.
	 */
	public function test_select_keeps_the_subtree_of_a_page_whose_parent_is_not_listed() {
		$this->add_english();
		$draft      = $this->create_page( 'Genitore in bozza', 'it', array( 'post_status' => 'draft' ) );
		$child      = $this->create_page( 'Figlia', 'it', array( 'post_parent' => $draft, 'menu_order' => 5 ) );
		$grandchild = $this->create_page( 'Nipote', 'it', array( 'post_parent' => $child ) );
		$root       = $this->create_page( 'Radice', 'it', array( 'menu_order' => 1 ) );

		$options = dli_get_pages_for_select();

		$this->assertSame( array( $root, $child, $grandchild ), array_keys( $options ) );
		$this->assertSame( array( 'Radice', 'Figlia', '— Nipote' ), array_values( $options ), 'The child of an unlisted parent is a first level choice, with its subpage indented.' );
	}

	/**
	 * A page already chosen by a tab stays among the choices when it can no longer be shown: saving the form
	 * must not drop it.
	 */
	public function test_tab_choices_keep_the_pages_already_chosen() {
		$this->add_english();
		$visible = $this->create_page( 'Visibile' );
		$draft   = $this->create_page( 'Era pubblicata', 'it', array( 'post_status' => 'draft' ) );
		$locked  = $this->create_page( 'Protetta', 'it', array( 'post_password' => 'segreto' ) );
		$this->set_tabs(
			array(
				array(
					'etichetta' => 'Uno',
					'pagine'    => array( $visible, $draft ),
				),
				array(
					'etichetta' => 'Due',
					'pagine'    => array( $locked, 999999 ),
				),
			)
		);

		$options = dli_get_tab_pages_for_select();

		$this->assertArrayHasKey( $visible, $options );
		$this->assertArrayHasKey( $draft, $options, 'A draft that was chosen is still a choice.' );
		$this->assertArrayHasKey( $locked, $options, 'A protected page that was chosen is still a choice.' );
		$this->assertArrayNotHasKey( 999999, $options, 'A page that no longer exists is not a choice.' );
		$this->assertSame( 'Visibile', $options[ $visible ], 'A page that can be shown has its title only.' );
		$this->assertStringStartsWith( 'Era pubblicata', $options[ $draft ] );
		$this->assertNotSame( 'Era pubblicata', $options[ $draft ], 'A page that cannot be shown says so.' );

		// The plain list is not changed by what was chosen.
		$this->assertArrayNotHasKey( $draft, dli_get_pages_for_select() );
	}
}
