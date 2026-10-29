<?php
/**
 * The homepage section "Navigazione per pagine": the groups (promoted pages with subpages) and how they are printed.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_get_promoted_page_groups()` and `template-parts/home/hp-page-navigation-section.php`.
 */
class DLI_Home_Navigation_Groups_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards.
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
	 * @param string               $title    Title.
	 * @param array<string, mixed> $args     Extra arguments of wp_insert_post().
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
	 * A promoted page with some subpages, by order.
	 *
	 * @param string   $title  Title of the page.
	 * @param string[] $items  Titles of the subpages, in order.
	 * @param int      $order  The field "Ordine" of the page.
	 * @return int Page ID.
	 */
	private function create_group( $title, array $items, $order = 0 ) {
		$parent = $this->create_page( $title, array( 'menu_order' => $order ) );
		foreach ( $items as $position => $item ) {
			$this->create_page( $item, array( 'post_parent' => $parent, 'menu_order' => $position ), false );
		}
		return $parent;
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
			'template-parts/home/hp-page-navigation-section',
			null,
			array_merge(
				array(
					'id'         => 'page_navigation_section',
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
	 * A group is a promoted page with published subpages; the others are left out. Order: field Ordine, then title.
	 */
	public function test_groups_are_promoted_pages_with_published_subpages_in_order() {
		$second = $this->create_group( 'Gestire', array( 'Accesso', 'Strutture' ), 2 );
		$first  = $this->create_group( 'Pianificare', array( 'Cosa sono', 'Piano' ), 1 );
		$this->create_page( 'Senza figli', array( 'menu_order' => 3 ) );
		$only_draft = $this->create_page( 'Solo bozze', array( 'menu_order' => 4 ) );
		$this->create_page( 'Bozza figlia', array( 'post_parent' => $only_draft, 'post_status' => 'draft' ), false );
		$not_promoted = $this->create_page( 'Non promossa', array( 'menu_order' => 5 ), false );
		$this->create_page( 'Figlia di una non promossa', array( 'post_parent' => $not_promoted ), false );
		$this->create_page( 'Protetta', array( 'menu_order' => 6, 'post_password' => 'segreto' ) );

		$groups = dli_get_promoted_page_groups( 10, 10 );

		$this->assertSame( array( $first, $second ), wp_list_pluck( $groups, 'id' ), 'Only the promoted pages with subpages, by order.' );
		$this->assertSame( array( 'Pianificare', 'Gestire' ), wp_list_pluck( $groups, 'title' ) );
		$this->assertSame( array( 'Cosa sono', 'Piano' ), wp_list_pluck( $groups[0]['items'], 'title' ) );
		$this->assertSame( get_permalink( $groups[0]['items'][0]['id'] ), $groups[0]['items'][0]['url'] );
	}

	/**
	 * Protected and draft subpages are not listed, and do not take a place among the subpages shown.
	 */
	public function test_protected_and_draft_subpages_are_left_out_before_the_limit() {
		$parent = $this->create_page( 'Gruppo', array( 'menu_order' => 1 ) );
		$this->create_page( 'Figlia protetta', array( 'post_parent' => $parent, 'menu_order' => 0, 'post_password' => 'segreto' ), false );
		$this->create_page( 'Figlia bozza', array( 'post_parent' => $parent, 'menu_order' => 1, 'post_status' => 'draft' ), false );
		foreach ( array( 'Uno', 'Due', 'Tre' ) as $position => $title ) {
			$this->create_page( $title, array( 'post_parent' => $parent, 'menu_order' => 2 + $position ), false );
		}

		$groups = dli_get_promoted_page_groups( 4, 2 );

		$this->assertSame( array( 'Uno', 'Due' ), wp_list_pluck( $groups[0]['items'], 'title' ), 'The first two that can be shown.' );
	}

	/**
	 * The limit of the subpages is applied after the order: the first ones by order, not by chance.
	 */
	public function test_subpages_are_ordered_before_they_are_limited() {
		$this->create_group( 'Gruppo', array( 'Zulu', 'Alfa', 'Bravo', 'Charlie', 'Delta' ) );
		// All with the same field Ordine would be by title: here the order is the position, Zulu first.

		$groups = dli_get_promoted_page_groups( 4, 3 );

		$this->assertSame( array( 'Zulu', 'Alfa', 'Bravo' ), wp_list_pluck( $groups[0]['items'], 'title' ) );
	}

	/**
	 * Never more groups than the limit, the first ones by order.
	 */
	public function test_groups_stop_at_the_limit() {
		for ( $index = 1; $index <= 6; $index++ ) {
			$this->create_group( 'Gruppo ' . $index, array( 'Voce' ), $index );
		}

		$groups = dli_get_promoted_page_groups( 3, 4 );

		$this->assertSame( array( 'Gruppo 1', 'Gruppo 2', 'Gruppo 3' ), wp_list_pluck( $groups, 'title' ) );
	}

	/**
	 * The pages of the current language only.
	 */
	public function test_groups_are_in_the_current_language() {
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
		$italian_group = $this->create_group( 'Gruppo italiano', array( 'Voce italiana' ) );
		$english_group = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'English group',
			)
		);
		dli_set_post_language( $english_group, 'en' );
		$this->set_acf_fields( $english_group, array( 'promuovi_in_home' => 1 ) );
		$english_child = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'English item',
				'post_parent' => $english_group,
			)
		);
		dli_set_post_language( $english_child, 'en' );

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertSame( array( $italian_group ), wp_list_pluck( dli_get_promoted_page_groups( 4, 4 ), 'id' ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( array( $english_group ), wp_list_pluck( dli_get_promoted_page_groups( 4, 4 ), 'id' ) );
	}

	/**
	 * Nothing is printed without groups, or when the row is off.
	 */
	public function test_nothing_is_printed_without_groups_or_when_disabled() {
		$this->create_page( 'Promossa senza figli' );
		$this->assertSame( '', trim( $this->render_section() ), 'No group, no empty section.' );

		$this->create_group( 'Gruppo', array( 'Voce' ) );
		$this->assertSame( '', trim( $this->render_section( array( 'enabled' => 'false' ) ) ) );
		$this->assertStringContainsString( 'Voce', $this->render_section() );
	}

	/**
	 * The title of a group is plain text, not a link; the subpages are links.
	 */
	public function test_group_titles_are_text_and_subpages_are_links() {
		$parent = $this->create_group( 'Pianificare', array( 'Cosa sono i dati' ) );

		$html = $this->render_section();

		$this->assertMatchesRegularExpression( '#<h3 [^>]*>Pianificare</h3>#', $html, 'The title of the group is text.' );
		$this->assertStringNotContainsString( 'href="' . esc_url( get_permalink( $parent ) ) . '"', $html, 'No link to the page of the group.' );
		$this->assertMatchesRegularExpression( '#<a class="list-item" href="[^"]+">\s*<div class="it-right-zone">\s*<span class="text fw-semibold">Cosa sono i dati</span>#', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
	}

	/**
	 * Title and subtitle come from Layout HP; the title can be hidden; an empty one is not printed.
	 */
	public function test_title_and_text_come_from_the_configuration() {
		$this->create_group( 'Pianificare', array( 'Voce' ) );
		$this->set_theme_options(
			'homepage',
			array(
				'home_page_navigation_title' => 'Le sezioni del sito',
				'home_page_navigation_text'  => 'Trova la pagina partendo da ciò che devi fare.',
			)
		);

		$html = $this->render_section();
		$this->assertMatchesRegularExpression( '#<h2 id="blocco-page-navigation-1-title"[^>]*>Le sezioni del sito</h2>#', $html );
		$this->assertStringContainsString( 'aria-labelledby="blocco-page-navigation-1-title"', $html );
		$this->assertStringContainsString( 'Trova la pagina partendo da ciò che devi fare.', $html );

		$hidden = $this->render_section( array( 'show_title' => 'false' ) );
		$this->assertStringNotContainsString( '<h2', $hidden );
		$this->assertMatchesRegularExpression( '#<section [^>]*aria-label="#', $hidden, 'Without a visible title the section keeps an accessible name.' );

		$this->set_theme_options( 'homepage', array( 'home_page_navigation_title' => '' ) );
		$this->assertStringNotContainsString( '<h2', $this->render_section(), 'An empty title is not printed.' );
	}

	/**
	 * Every identifier follows the position of the row and the page of the group: nothing is fixed, so the section can be repeated.
	 */
	public function test_identifiers_follow_the_instance_and_the_group() {
		$parent = $this->create_group( 'Pianificare', array( 'Voce' ) );

		$first  = $this->render_section( array( 'instance' => 1 ) );
		$second = $this->render_section( array( 'instance' => 5 ) );

		foreach ( array(
			'id="blocco-page-navigation-1"',
			'id="page-navigation-1-accordion"',
			'id="page-navigation-1-head-' . $parent . '"',
			'id="page-navigation-1-body-' . $parent . '"',
			'id="page-navigation-1-title-' . $parent . '"',
		) as $needle ) {
			$this->assertStringContainsString( $needle, $first );
		}
		$this->assertStringContainsString( 'id="page-navigation-5-accordion"', $second );
		$this->assertStringNotContainsString( 'id="page-navigation-1-accordion"', $second );
		$this->assertStringNotContainsString( 'id="page-navigation-accordion"', $first, 'No fixed identifier.' );
		$this->assertStringContainsString( 'data-bs-target="#page-navigation-1-body-' . $parent . '"', $first );
		$this->assertStringContainsString( 'aria-controls="page-navigation-1-body-' . $parent . '"', $first );
		$this->assertStringContainsString( 'aria-labelledby="page-navigation-1-title-' . $parent . '"', $first, 'The list is named by the title of its group.' );
	}

	/**
	 * The first panel of the accordion is open, the others closed; the closed ones say so.
	 */
	public function test_first_accordion_panel_is_open() {
		$this->create_group( 'Primo', array( 'Voce uno' ), 1 );
		$this->create_group( 'Secondo', array( 'Voce due' ), 2 );

		$html = $this->render_section();

		$this->assertSame( 1, substr_count( $html, 'aria-expanded="true"' ) );
		$this->assertSame( 1, substr_count( $html, 'aria-expanded="false"' ) );
		$this->assertSame( 1, substr_count( $html, 'accordion-collapse collapse show' ) );
	}

	/**
	 * The numbers chosen in the row are the limits; wrong ones fall back to the defaults.
	 */
	public function test_limits_follow_the_row() {
		for ( $index = 1; $index <= 6; $index++ ) {
			$this->create_group( 'Gruppo ' . $index, array( 'A' . $index, 'B' . $index, 'C' . $index, 'D' . $index, 'E' . $index ), $index );
		}

		$default = $this->render_section();
		$custom  = $this->render_section( array( 'max_items' => 2, 'max_subitems' => 3 ) );
		$wrong   = $this->render_section( array( 'max_items' => 'tanti', 'max_subitems' => '' ) );

		$this->assertSame( DLI_HP_PAGE_NAVIGATION_DEFAULT_ITEMS, substr_count( $default, '<h3 id="page-navigation-1-title-' ), 'Default number of groups.' );
		$this->assertSame( DLI_HP_PAGE_NAVIGATION_DEFAULT_ITEMS * DLI_HP_PAGE_NAVIGATION_DEFAULT_SUBITEMS, substr_count( $default, 'class="list-item"' ) / 2, 'Default number of subpages per group (the markup is printed twice).' );
		$this->assertSame( 2, substr_count( $custom, '<h3 id="page-navigation-1-title-' ) );
		$this->assertSame( 2 * 3, substr_count( $custom, 'class="list-item"' ) / 2 );
		$this->assertSame( DLI_HP_PAGE_NAVIGATION_DEFAULT_ITEMS, substr_count( $wrong, '<h3 id="page-navigation-1-title-' ) );
	}

	/**
	 * The section is in the catalog with its default background and the two numbers.
	 */
	public function test_the_section_is_in_the_catalog() {
		$this->assertArrayHasKey( 'page_navigation_section', DLI_ContentsManager::get_hp_sections() );
		$this->assertSame( 'Navigazione per pagine', DLI_ContentsManager::get_hp_sections()['page_navigation_section']['name'] );
		$this->assertSame( 'muted', DLI_ContentsManager::get_hp_sections_with_background()['page_navigation_section'] );
		$this->assertArrayHasKey( 'page_navigation_section', DLI_ContentsManager::get_hp_sections_with_max_items() );
		$this->assertArrayHasKey( 'page_navigation_section', DLI_ContentsManager::get_hp_sections_with_max_subitems() );
	}
}
