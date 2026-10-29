<?php
/**
 * The fields of the base page and the cards of its subpages.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Registration of the page fields, the helper `dli_get_subpage_cards()` and the rendering of `page.php`.
 */
class DLI_Page_Subpage_Cards_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a page in Italian.
	 *
	 * @param string               $title Title.
	 * @param array<string, mixed> $args  Extra arguments of wp_insert_post().
	 * @return int Page ID.
	 */
	private function create_page( $title, array $args = array() ) {
		$page_id = self::factory()->post->create(
			array_merge(
				array(
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_status'  => 'publish',
					'post_excerpt' => '', // The factory invents one otherwise.
				),
				$args
			)
		);
		dli_set_post_language( $page_id, 'it' );
		return $page_id;
	}

	/**
	 * Render page.php as the page that uses it.
	 *
	 * @param int $page_id Page.
	 * @return string Rendered HTML.
	 */
	private function render_page( $page_id ) {
		$this->go_to( get_permalink( $page_id ) );
		$GLOBALS['post'] = get_post( $page_id );
		setup_postdata( $GLOBALS['post'] );
		return $this->render_theme_template( 'page.php' );
	}

	/**
	 * Two flags, off by default, after the related pages, with their own keys.
	 */
	public function test_page_flags_are_registered_with_their_keys() {
		$names = array();
		foreach ( acf_get_fields( 'group_670e6f9be1c67' ) as $field ) {
			$names[ $field['name'] ] = $field;
		}

		$this->assertSame( array( 'pagine_collegate', 'promuovi_in_home', 'mostra_sottopagine_a_card' ), array_keys( $names ) );
		foreach ( array( 'promuovi_in_home' => 'field_670e717b3bedc', 'mostra_sottopagine_a_card' => 'field_670e717b3bedd' ) as $name => $key ) {
			$this->assertSame( $key, $names[ $name ]['key'] );
			$this->assertSame( 'true_false', $names[ $name ]['type'] );
			$this->assertEquals( 0, $names[ $name ]['default_value'], 'The flags are off by default.' );
		}
		$this->assertStringContainsString( 'prima del testo', $names['mostra_sottopagine_a_card']['instructions'], 'The cards come before the text of the page.' );
		$this->assertStringNotContainsString( 'dopo il testo', $names['mostra_sottopagine_a_card']['instructions'] );
	}

	/**
	 * The editor can write a summary for a page.
	 */
	public function test_pages_support_the_excerpt() {
		$this->assertTrue( post_type_supports( 'page', 'excerpt' ) );
	}

	/**
	 * Only the published direct children, in the order of the menu, without the protected ones.
	 */
	public function test_subpage_cards_list_published_direct_children_in_menu_order() {
		$parent = $this->create_page( 'Madre' );
		$second = $this->create_page( 'Figlia seconda', array( 'post_parent' => $parent, 'menu_order' => 2 ) );
		$first  = $this->create_page( 'Figlia prima', array( 'post_parent' => $parent, 'menu_order' => 1 ) );
		$this->create_page( 'Figlia bozza', array( 'post_parent' => $parent, 'post_status' => 'draft' ) );
		$this->create_page( 'Figlia protetta', array( 'post_parent' => $parent, 'post_password' => 'segreto', 'post_excerpt' => 'Riservato.' ) );
		$this->create_page( 'Nipote', array( 'post_parent' => $first ) );

		$cards = dli_get_subpage_cards( $parent );

		$this->assertSame( array( $first, $second ), wp_list_pluck( $cards, 'id' ) );
		$this->assertSame( array( 'Figlia prima', 'Figlia seconda' ), wp_list_pluck( $cards, 'title' ) );
		$this->assertSame( get_permalink( $first ), $cards[0]['url'] );
		$this->assertSame( array(), dli_get_subpage_cards( $second ), 'A page without children has no cards.' );
	}

	/**
	 * The abstract is the summary of the page, or the beginning of its text, cut at the configured words.
	 */
	public function test_abstract_is_the_summary_or_the_cut_text() {
		$parent = $this->create_page( 'Madre estratto' );
		$this->create_page( 'Con riassunto', array( 'post_parent' => $parent, 'menu_order' => 1, 'post_excerpt' => 'Riassunto scritto a mano.', 'post_content' => 'Testo che non si usa.' ) );
		$long = implode( ' ', array_map( static fn( $i ) => 'parola' . $i, range( 1, DLI_PAGE_CARD_EXCERPT_WORDS + 10 ) ) );
		$this->create_page( 'Senza riassunto', array( 'post_parent' => $parent, 'menu_order' => 2, 'post_content' => '<p>' . $long . '</p>[shortcode_finto]' ) );
		$this->create_page( 'Senza nulla', array( 'post_parent' => $parent, 'menu_order' => 3, 'post_content' => '' ) );

		$cards = dli_get_subpage_cards( $parent );

		$this->assertSame( 'Riassunto scritto a mano.', $cards[0]['abstract'] );
		$this->assertStringStartsWith( 'parola1 parola2', $cards[1]['abstract'] );
		$this->assertStringContainsString( 'parola' . DLI_PAGE_CARD_EXCERPT_WORDS, $cards[1]['abstract'] );
		$this->assertStringNotContainsString( 'parola' . ( DLI_PAGE_CARD_EXCERPT_WORDS + 1 ), $cards[1]['abstract'] );
		$this->assertStringNotContainsString( '<p>', $cards[1]['abstract'] );
		$this->assertStringNotContainsString( 'shortcode_finto', $cards[1]['abstract'] );
		$this->assertSame( '', $cards[2]['abstract'] );
	}

	/**
	 * The cards appear before the text, under a visible title, only when the flag is on and there are children.
	 */
	public function test_page_shows_the_cards_before_the_text_only_when_requested() {
		$parent = $this->create_page( 'Pagina con card', array( 'post_content' => 'Testo della pagina madre.' ) );
		$child  = $this->create_page( 'Sottopagina visibile', array( 'post_parent' => $parent, 'post_excerpt' => 'Estratto visibile.' ) );

		$off = $this->render_page( $parent );
		$this->assertStringNotContainsString( 'card-nav', $off, 'Flag off: no cards (the sidebar lists the subpages anyway).' );
		$this->assertStringNotContainsString( 'id="sottosezioni"', $off );

		$this->set_acf_fields( $parent, array( 'mostra_sottopagine_a_card' => 1 ) );
		$on = $this->render_page( $parent );

		$this->assertStringContainsString( 'id="sottosezioni"', $on );
		$section = substr( $on, (int) strpos( $on, 'id="sottosezioni"' ), (int) strpos( $on, 'Testo della pagina madre.' ) - (int) strpos( $on, 'id="sottosezioni"' ) );
		$this->assertStringContainsString( 'Sottopagina visibile', $section );
		$this->assertStringContainsString( 'Estratto visibile.', $section );
		$this->assertStringContainsString( 'href="' . esc_url( get_permalink( $child ) ) . '"', $section );
		$this->assertLessThan( strpos( $on, 'Testo della pagina madre.' ), strpos( $on, 'id="sottosezioni"' ), 'The cards come before the text.' );
		$this->assertMatchesRegularExpression( '#id="sottosezioni"[^>]*aria-labelledby="sottosezioni-title"#', $on, 'The group is named by its visible title.' );
		$this->assertMatchesRegularExpression( '#<h2 id="sottosezioni-title"[^>]*>\s*' . preg_quote( __( 'In questa sezione', 'design_laboratori_italia' ), '#' ) . '\s*</h2>#', $on );
	}

	/**
	 * Flag on without published children: no empty list. A protected child is not shown.
	 */
	public function test_no_empty_list_and_no_protected_child() {
		$parent = $this->create_page( 'Pagina senza figli' );
		$this->set_acf_fields( $parent, array( 'mostra_sottopagine_a_card' => 1 ) );
		$this->assertStringNotContainsString( 'id="sottosezioni"', $this->render_page( $parent ) );

		$this->create_page( 'Figlia riservata', array( 'post_parent' => $parent, 'post_password' => 'segreto' ) );
		$html = $this->render_page( $parent );
		$this->assertStringNotContainsString( 'id="sottosezioni"', $html );
		$this->assertStringNotContainsString( 'card-nav', $html );
		$this->assertStringNotContainsString( 'protected post', $html );
	}

	/**
	 * While the page itself is locked by a password its subpages are not listed.
	 */
	public function test_a_locked_page_does_not_list_its_subpages() {
		$parent = $this->create_page( 'Madre protetta', array( 'post_password' => 'segreto' ) );
		$this->create_page( 'Figlia della protetta', array( 'post_parent' => $parent ) );
		$this->set_acf_fields( $parent, array( 'mostra_sottopagine_a_card' => 1 ) );

		$html = $this->render_page( $parent );

		$this->assertStringNotContainsString( 'card-nav', $html );
		$this->assertStringNotContainsString( 'id="sottosezioni"', $html );
	}
}
