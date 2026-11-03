<?php
/**
 * The headings of the footer: their levels and their links.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `footer.php`, `template-parts/common/social-footer.php` and `template-parts/common/newsletter.php`.
 */
class DLI_Footer_Headings_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Fill the footer: name, tagline, a menu of useful links, the social icons and the newsletter.
	 */
	public function set_up() {
		parent::set_up();
		$this->set_theme_options(
			'dli_options',
			array(
				'nome_laboratorio'    => 'Laboratorio di prova',
				'tagline_laboratorio' => 'Tagline di prova',
			)
		);
		$this->set_theme_options(
			'socials',
			array(
				'show_socials' => 'true',
				'facebook'     => 'https://example.org/facebook',
			)
		);
		$this->set_theme_options( 'setup', array( 'newsletter_enabled' => 'true' ) );

		$menu_id = wp_create_nav_menu( 'Link utili di prova' );
		$this->assertIsInt( $menu_id );
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Un link utile',
				'menu-item-type'   => 'custom',
				'menu-item-url'    => 'https://example.org/utile',
				'menu-item-status' => 'publish',
			)
		);
		set_theme_mod( 'nav_menu_locations', array( 'menu-links' => $menu_id ) );
	}

	/**
	 * Remove the menu locations that the test assigned.
	 */
	public function tear_down() {
		remove_theme_mod( 'nav_menu_locations' );
		parent::tear_down();
	}

	/**
	 * Render the footer and read it as a document.
	 *
	 * @return DOMXPath
	 */
	private function footer() {
		// Printing the footer alone, without the header that unhooks it, would call a function that WordPress deprecated.
		remove_action( 'wp_footer', 'the_block_template_skip_link' );

		$html     = $this->render_theme_template( 'footer.php' );
		$previous = libxml_use_internal_errors( true );
		$document = new DOMDocument();
		$document->loadHTML( '<?xml encoding="utf-8"?>' . $html );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous );
		return new DOMXPath( $document );
	}

	/**
	 * The headings of the footer, in the order of the page: the level and the text.
	 *
	 * @param DOMXPath $xpath The footer.
	 * @return array<int, array{0: int, 1: string}>
	 */
	private function headings( DOMXPath $xpath ) {
		$found = array();
		foreach ( $xpath->query( '//footer//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6]' ) as $node ) {
			$found[] = array( (int) substr( $node->nodeName, 1 ), trim( preg_replace( '/\s+/', ' ', $node->textContent ) ) );
		}
		return $found;
	}

	/**
	 * The levels follow the same order as the branch: the name of the laboratory is an h3, the titles of
	 * the columns are h4 and Newsletter is an h3, so that no heading skips a level.
	 */
	public function test_headings_do_not_skip_a_level() {
		$headings = $this->headings( $this->footer() );
		$levels   = array_column( $headings, 0 );

		$this->assertNotEmpty( $levels );
		foreach ( $levels as $position => $level ) {
			if ( 0 === $position ) {
				continue;
			}
			$this->assertLessThanOrEqual( $levels[ $position - 1 ] + 1, $level, 'The heading "' . $headings[ $position ][1] . '" skips a level.' );
		}
		$this->assertSame( array( 3, 4, 4, 4, 3, 3 ), $levels, 'Name h3, Contatti h4, Link utili h4, Seguici su h4, Newsletter h3, hidden title h3. Found: ' . wp_json_encode( $headings ) );
	}

	/**
	 * The tagline is a sentence, not a heading.
	 */
	public function test_tagline_is_a_paragraph() {
		$xpath = $this->footer();

		$this->assertSame( 1, $xpath->query( '//footer//*[contains(@class,"it-brand-text")]//p[contains(.,"Tagline di prova")]' )->length );
		foreach ( $this->headings( $xpath ) as $heading ) {
			$this->assertStringNotContainsString( 'Tagline di prova', $heading[1] );
		}
	}

	/**
	 * Contatti, Link utili and Seguici su are plain text: a link that leads nowhere (`#`) is not a link.
	 */
	public function test_titles_of_the_columns_have_no_empty_link() {
		$xpath = $this->footer();

		$titles = array(
			'Contatti'   => esc_html__( 'Contatti', 'design_laboratori_italia' ),
			'Link utili' => esc_html__( 'Link utili', 'design_laboratori_italia' ),
			'Seguici su' => esc_html__( 'Seguici su', 'design_laboratori_italia' ),
		);
		foreach ( $titles as $name => $label ) {
			$nodes = $xpath->query( '//footer//h4[normalize-space(.)="' . $label . '"]' );
			$this->assertSame( 1, $nodes->length, "$name is an h4." );
			$this->assertSame( 0, $xpath->query( './/a', $nodes->item( 0 ) )->length, "$name has no link." );
		}

		$this->assertSame( 0, $xpath->query( '//footer//a[@href="#"]' )->length, 'No link of the footer leads to #.' );
	}

	/**
	 * Newsletter keeps its link: it leads to a real page.
	 */
	public function test_newsletter_title_keeps_its_link() {
		$xpath = $this->footer();
		$label = esc_html__( 'Newsletter', 'design_laboratori_italia' );
		$nodes = $xpath->query( '//footer//h3[normalize-space(.)="' . $label . '"]//a[@href]' );

		$this->assertSame( 1, $nodes->length );
	}
}
