<?php
/**
 * The homepage section "Indice di una pagina": the page chosen in a row, its subpages as columns and their subpages as links.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_get_page_link_group()`, `dli_get_page_index()` and `template-parts/home/hp-page-index-section.php`.
 */
class DLI_Home_Page_Index_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Make Italian the current language and add English.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
		if ( ! PLL()->model->get_language( 'en' ) ) {
			PLL()->model->languages->add(
				array(
					'name'   => 'English',
					'slug'   => 'en',
					'locale' => 'en_US',
					'rtl'    => false,
					'flag'   => 'gb',
				)
			);
		}
		PLL()->curlang = PLL()->model->get_language( 'it' );
	}

	/**
	 * Put back the current language.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Create a page in a language.
	 *
	 * @param string               $title Title.
	 * @param int                  $parent Parent page, 0 for the first level.
	 * @param array<string, mixed> $args  Extra arguments of wp_insert_post().
	 * @param string               $lang  Language slug.
	 * @return int Page ID.
	 */
	private function create_page( $title, $parent = 0, array $args = array(), $lang = 'it' ) {
		$page_id = self::factory()->post->create(
			array_merge(
				array(
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_status'  => 'publish',
					'post_parent'  => $parent,
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
	 * Create a tree: a root with a column that has two links, a column with one link, a subpage without children and
	 * a subpage whose only child is a draft.
	 *
	 * @return array<string, int> The IDs, by name.
	 */
	private function create_tree() {
		$root   = $this->create_page( 'Radice' );
		$plan   = $this->create_page( 'Pianificare', $root, array( 'menu_order' => 1 ) );
		$manage = $this->create_page( 'Gestire', $root, array( 'menu_order' => 2 ) );

		return array(
			'root'     => $root,
			'plan'     => $plan,
			'manage'   => $manage,
			'alone'    => $this->create_page( 'Senza figlie', $root, array( 'menu_order' => 3 ) ),
			'drafty'   => $this->create_page( 'Solo bozze', $root, array( 'menu_order' => 4 ) ),
			'plan_a'   => $this->create_page( 'Cosa sono i dati', $plan, array( 'menu_order' => 1 ) ),
			'plan_b'   => $this->create_page( 'Il piano di gestione', $plan, array( 'menu_order' => 2 ) ),
			'manage_a' => $this->create_page( 'Archiviare', $manage ),
			'draft'    => $this->create_page( 'Figlia in bozza', 0, array( 'post_status' => 'draft' ) ),
		);
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
			'template-parts/home/hp-page-index-section',
			null,
			array_merge(
				array(
					'id'         => 'page_index_section',
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
	 * A group is a page with its direct subpages that can be shown, in order; a page without any is no group.
	 */
	public function test_a_link_group_lists_the_subpages_that_can_be_shown() {
		$tree = $this->create_tree();
		$this->create_page( 'Protetta', $tree['plan'], array( 'post_password' => 'segreto', 'menu_order' => 3 ) );
		$this->create_page( 'Bozza', $tree['plan'], array( 'post_status' => 'draft', 'menu_order' => 4 ) );

		$group = dli_get_page_link_group( get_post( $tree['plan'] ), 6 );

		$this->assertSame( $tree['plan'], $group['id'] );
		$this->assertSame( 'Pianificare', $group['title'] );
		$this->assertSame( array( 'Cosa sono i dati', 'Il piano di gestione' ), wp_list_pluck( $group['items'], 'title' ), 'Draft and protected subpages are left out.' );
		$this->assertSame( get_permalink( $tree['plan_a'] ), $group['items'][0]['url'] );
		$this->assertCount( 1, dli_get_page_link_group( get_post( $tree['plan'] ), 1 )['items'], 'The limit applies to the subpages.' );
		$this->assertNull( dli_get_page_link_group( get_post( $tree['alone'] ), 6 ), 'No subpages, no group.' );
		$this->assertNull( dli_get_page_link_group( get_post( $tree['drafty'] ), 6 ), 'A page whose only subpage is a draft is no group.' );
	}

	/**
	 * The index is the title of the page and one column for each subpage that has subpages of its own.
	 */
	public function test_the_index_is_one_column_for_each_subpage_with_children() {
		$tree = $this->create_tree();

		$index = dli_get_page_index( $tree['root'], 4, 6 );

		$this->assertSame( 'Radice', $index['title'] );
		$this->assertSame( array( 'Pianificare', 'Gestire' ), wp_list_pluck( $index['groups'], 'title' ), 'A subpage without children, or with drafts only, is not a column.' );
		$this->assertSame( array( 'Cosa sono i dati', 'Il piano di gestione' ), wp_list_pluck( $index['groups'][0]['items'], 'title' ) );
		$this->assertCount( 1, dli_get_page_index( $tree['root'], 1, 6 )['groups'], 'The number of columns is limited.' );
		$this->assertCount( 1, dli_get_page_index( $tree['root'], 4, 1 )['groups'][0]['items'], 'The number of links of a column is limited.' );
	}

	/**
	 * A page that does not exist, cannot be shown or has no column is no index.
	 */
	public function test_a_page_that_cannot_give_an_index_gives_nothing() {
		$tree      = $this->create_tree();
		$protected = $this->create_page( 'Pagina protetta', 0, array( 'post_password' => 'segreto' ) );
		$this->create_page( 'Figlia', $protected );
		$post      = $this->create_page( 'Un articolo', 0, array( 'post_type' => 'post' ) );

		$this->assertNull( dli_get_page_index( 999999, 4, 6 ), 'A page that does not exist.' );
		$this->assertNull( dli_get_page_index( $tree['draft'], 4, 6 ), 'A draft.' );
		$this->assertNull( dli_get_page_index( $protected, 4, 6 ), 'A protected page.' );
		$this->assertNull( dli_get_page_index( $post, 4, 6 ), 'Not a page.' );
		$this->assertNull( dli_get_page_index( $tree['alone'], 4, 6 ), 'A page without subpages.' );
		$this->assertNull( dli_get_page_index( 0, 4, 6 ) );
	}

	/**
	 * In English the translation of the chosen page is shown, with its own subpages; without a translation nothing.
	 */
	public function test_the_index_follows_the_translation_of_the_chosen_page() {
		$tree          = $this->create_tree();
		$english_root  = $this->create_page( 'Root', 0, array(), 'en' );
		$english_group = $this->create_page( 'Plan', $english_root, array(), 'en' );
		$this->create_page( 'What are data', $english_group, array(), 'en' );
		dli_save_post_translations(
			array(
				'it' => $tree['root'],
				'en' => $english_root,
			)
		);
		$lonely = $this->create_page( 'Pagina senza traduzione' );
		$this->create_page( 'Figlia', $this->create_page( 'Colonna', $lonely ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$index         = dli_get_page_index( $tree['root'], 4, 6 );

		$this->assertSame( 'Root', $index['title'] );
		$this->assertSame( array( 'Plan' ), wp_list_pluck( $index['groups'], 'title' ), 'The columns are the ones of the English page.' );
		$this->assertNull( dli_get_page_index( $lonely, 4, 6 ), 'No translation, no index: the Italian page is never shown in English.' );
	}

	/**
	 * The section prints the title, the columns and the accordion of the telephone, with the links.
	 */
	public function test_the_section_prints_the_title_the_columns_and_the_accordion() {
		$tree = $this->create_tree();

		$html = $this->render_section( array( 'page_id' => $tree['root'] ) );

		$this->assertMatchesRegularExpression( '#<h2 id="blocco-page-index-1-title"[^>]*>Radice</h2>#', $html );
		$this->assertStringContainsString( 'id="blocco-page-index-1"', $html );
		$this->assertStringContainsString( 'aria-labelledby="blocco-page-index-1-title"', $html );
		$this->assertSame( 2, preg_match_all( '#<h3 id="page-index-1-title-\d+" class="h5 fw-bold">#', $html ), 'A column for each group.' );
		$this->assertSame( 2, preg_match_all( '#<h3 class="accordion-header"#', $html ), 'An accordion panel for each group.' );
		$this->assertStringContainsString( 'id="page-index-1-accordion"', $html );
		$this->assertStringContainsString( 'Cosa sono i dati', $html );
		$this->assertStringContainsString( 'href="' . esc_url( get_permalink( $tree['manage_a'] ) ) . '"', $html );
		$this->assertStringNotContainsString( 'Senza figlie', $html );
		$this->assertStringNotContainsString( 'Solo bozze', $html );
		$this->assertSame( 1, substr_count( $html, 'aria-expanded="true"' ), 'Only the first panel is open.' );
	}

	/**
	 * The title is seen only when the row asks for it, but it is always there: the columns are `h3` and need their `h2`.
	 */
	public function test_the_title_follows_show_title_and_is_always_the_heading_of_the_columns() {
		$tree = $this->create_tree();

		$hidden = $this->render_section(
			array(
				'page_id'    => $tree['root'],
				'show_title' => 'false',
			)
		);

		$this->assertMatchesRegularExpression( '#<h2 id="blocco-page-index-1-title" class="visually-hidden">Radice</h2>#', $hidden, 'The title is there for the assistive technologies, not seen.' );
		$this->assertStringContainsString( 'aria-labelledby="blocco-page-index-1-title"', $hidden, 'The section is named by it.' );
		$this->assertDoesNotMatchRegularExpression( '#<section [^>]*aria-label=#', $hidden, 'No second name for the section.' );
		$this->assertLessThan( strpos( $hidden, '<h3' ), strpos( $hidden, '<h2' ), 'The h2 comes before the h3 of the columns.' );
	}

	/**
	 * Nothing is printed when the row is off, has no page, or the page gives no index.
	 */
	public function test_nothing_is_printed_without_a_page_or_when_disabled() {
		$tree = $this->create_tree();

		$this->assertSame( '', trim( $this->render_section( array( 'page_id' => $tree['root'], 'enabled' => 'false' ) ) ) );
		$this->assertSame( '', trim( $this->render_section() ), 'A row with no page.' );
		$this->assertSame( '', trim( $this->render_section( array( 'page_id' => 999999 ) ) ), 'A page that does not exist.' );
		$this->assertSame( '', trim( $this->render_section( array( 'page_id' => $tree['alone'] ) ) ), 'A page without columns.' );
	}

	/**
	 * Two copies of the section, also on the same page, have no identifier in common.
	 */
	public function test_two_copies_of_the_section_have_unique_identifiers() {
		$tree = $this->create_tree();

		$page = $this->render_section( array( 'page_id' => $tree['root'], 'instance' => 1 ) )
			. $this->render_section( array( 'page_id' => $tree['root'], 'instance' => 3 ) )
			. $this->render_section( array( 'page_id' => $tree['root'], 'instance' => 5 ) );

		preg_match_all( '#\sid="([^"]+)"#', $page, $matches );
		$this->assertSame( array_unique( $matches[1] ), $matches[1], 'No identifier is repeated, not even for the same page.' );
		$this->assertStringContainsString( 'id="blocco-page-index-3"', $page );
		$this->assertStringContainsString( 'data-bs-target="#page-index-3-body-' . $tree['plan'] . '"', $page );
	}

	/**
	 * The numbers of the row limit the columns and the links; without them the defaults of the section apply.
	 */
	public function test_the_row_limits_the_columns_and_the_links() {
		$tree = $this->create_tree();

		$limited = $this->render_section(
			array(
				'page_id'      => $tree['root'],
				'max_items'    => 1,
				'max_subitems' => 1,
			)
		);

		$this->assertSame( 1, preg_match_all( '#<h3 id="page-index-1-title-\d+"#', $limited ), 'One column.' );
		$this->assertStringContainsString( 'Cosa sono i dati', $limited );
		$this->assertStringNotContainsString( 'Il piano di gestione', $limited, 'One link in the column.' );

		$default = $this->render_section( array( 'page_id' => $tree['root'] ) );
		$this->assertSame( 2, preg_match_all( '#<h3 id="page-index-1-title-\d+"#', $default ) );
		$this->assertSame( 4, DLI_HP_PAGE_INDEX_DEFAULT_ITEMS );
		$this->assertSame( 6, DLI_HP_PAGE_INDEX_DEFAULT_SUBITEMS );
	}

	/**
	 * The section carries the class that its list of links is styled by (no underline at rest, see assets/css/main.css).
	 */
	public function test_the_section_carries_the_class_of_the_list_style() {
		$tree = $this->create_tree();

		$this->assertMatchesRegularExpression( '#<section [^>]*class="section page-index-section[ "]#', $this->render_section( array( 'page_id' => $tree['root'] ) ) );
	}

	/**
	 * The background is white by default, grey when the row says so.
	 */
	public function test_the_background_is_white_by_default() {
		$tree = $this->create_tree();

		$this->assertDoesNotMatchRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'page_id' => $tree['root'] ) ) );
		$this->assertMatchesRegularExpression( '#<section [^>]*section-muted#', $this->render_section( array( 'page_id' => $tree['root'], 'background' => 'muted' ) ) );
	}

	/**
	 * A page already chosen is found whatever the language of the screen: the filter of Polylang by language must not
	 * hide an Italian draft from an editor who works in English, or saving would drop it.
	 */
	public function test_a_chosen_page_is_found_in_any_language() {
		$draft = $this->create_page( 'Era pubblicata', 0, array( 'post_status' => 'draft' ) );
		$this->set_theme_options(
			'homepage_sections',
			array(
				'site_sections' => array(
					array(
						'id'      => 'page_index_section',
						'enabled' => 'true',
						'page_id' => (string) $draft,
					),
				),
			)
		);
		$this->set_theme_options( 'homepage', array( 'home_tabs_items' => array( array( 'etichetta' => 'Uno', 'pagine' => array( $draft ) ) ) ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );

		$this->assertArrayHasKey( $draft, dli_get_index_pages_for_select(), 'The index choices keep it.' );
		$this->assertArrayHasKey( $draft, dli_get_tab_pages_for_select(), 'The tab choices keep it.' );
	}

	/**
	 * A page already chosen in a row stays among the choices when it can no longer be shown: saving Sezioni HP must
	 * not drop it.
	 */
	public function test_the_choices_keep_the_page_already_chosen() {
		$visible = $this->create_page( 'Visibile' );
		$draft   = $this->create_page( 'Era pubblicata', 0, array( 'post_status' => 'draft' ) );
		$this->set_theme_options(
			'homepage_sections',
			array(
				'site_sections' => array(
					array(
						'id'      => 'page_index_section',
						'enabled' => 'true',
						'page_id' => (string) $draft,
					),
					array(
						'id'      => 'page_index_section',
						'enabled' => 'true',
						'page_id' => '999999',
					),
				),
			)
		);

		$options = dli_get_index_pages_for_select();

		$this->assertArrayHasKey( $visible, $options );
		$this->assertArrayHasKey( $draft, $options, 'A draft that was chosen is still a choice.' );
		$this->assertStringStartsWith( 'Era pubblicata', $options[ $draft ] );
		$this->assertNotSame( 'Era pubblicata', $options[ $draft ], 'A page that cannot be shown says so.' );
		$this->assertArrayNotHasKey( 999999, $options, 'A page that no longer exists is not a choice.' );
		$this->assertArrayNotHasKey( $draft, dli_get_pages_for_select(), 'The plain list is not changed by what was chosen.' );
	}
}
