<?php
/**
 * The publications of the homepage: the list section and the "featured contents" box.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Rendering of `template-parts/home/hp-list-publication.php` and `template-parts/home/card-pubblicazioni.php`.
 */
class DLI_Publication_Home_Cards_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a publication promoted to the homepage.
	 *
	 * @param string               $title  Title.
	 * @param array<string, mixed> $fields ACF fields, by name.
	 * @param array<string, mixed> $post   Extra arguments of wp_update_post().
	 * @return int Publication ID.
	 */
	private function create_promoted_publication( $title, array $fields = array(), array $post = array() ) {
		$publication_id = $this->create_post( PUBLICATION_POST_TYPE, $title );
		if ( ! empty( $post ) ) {
			wp_update_post( array_merge( array( 'ID' => $publication_id ), $post ) );
		}
		$this->set_acf_fields( $publication_id, array_merge( array( 'promuovi_in_home' => 1 ), $fields ) );
		return $publication_id;
	}

	/**
	 * Create the publications page, in Italian, the page the buttons and the type links lead to.
	 *
	 * @return string Its address.
	 */
	private function create_archive_page() {
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Pubblicazioni',
				'post_name'  => SLUG_PUBBLICAZIONI_IT,
			)
		);
		dli_set_post_language( $page_id, 'it' );
		return get_permalink( $page_id );
	}

	/**
	 * Print a template part and return its HTML.
	 *
	 * @param string               $slug Template part, without extension.
	 * @param array<string, mixed> $args Arguments of the part.
	 * @return string
	 */
	private function render_part( $slug, array $args ) {
		ob_start();
		get_template_part( $slug, null, $args );
		return (string) ob_get_clean();
	}

	/**
	 * The list section, enabled, with the title.
	 *
	 * @return string
	 */
	private function render_list() {
		return $this->render_part(
			'template-parts/home/hp-list-publication',
			array(
				'enabled'    => 'true',
				'show_title' => 'true',
			)
		);
	}

	/**
	 * The box of the featured contents.
	 *
	 * @return string
	 */
	private function render_box() {
		return $this->render_part(
			'template-parts/home/card-pubblicazioni',
			array(
				'box_number'    => 1,
				'box_label'     => 'Box pubblicazioni',
				'box_template'  => 'card-pubblicazioni',
				'box_post_type' => PUBLICATION_POST_TYPE,
			)
		);
	}

	/**
	 * Both places show at most three publications, the newest first.
	 */
	public function test_three_publications_at_most_in_both_places() {
		foreach ( array( 'Uno', 'Due', 'Tre', 'Quattro' ) as $index => $title ) {
			$this->create_promoted_publication( 'Home ' . $title, array(), array( 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( 4 - $index ) . ' days' ) ) ) );
		}

		foreach ( array( $this->render_list(), $this->render_box() ) as $html ) {
			$this->assertSame( 3, substr_count( $html, '<article' ) );
			$this->assertStringContainsString( 'Home Quattro', $html );
			$this->assertStringNotContainsString( 'Home Uno', $html, 'The oldest one is left out.' );
		}
	}

	/**
	 * The title follows the choice of the publication; the type leads to the filtered archive.
	 */
	public function test_titles_follow_the_link_and_the_type_filters_the_archive() {
		$detail   = $this->create_promoted_publication( 'Home scheda' );
		$external = $this->create_promoted_publication(
			'Home esterna',
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/home-esterna',
			)
		);
		$this->create_promoted_publication( 'Home senza link', array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );
		$archive = $this->create_archive_page();
		$term = wp_insert_term( 'Articolo', PUBLICATION_TYPE_TAXONOMY, array( 'slug' => 'articolo-home' ) );
		wp_set_object_terms( $detail, array( (int) $term['term_id'] ), PUBLICATION_TYPE_TAXONOMY );

		foreach ( array( $this->render_list(), $this->render_box() ) as $html ) {
			$this->assertMatchesRegularExpression( '#<a href="' . preg_quote( esc_url( get_permalink( $detail ) ), '#' ) . '">\s*Home scheda\s*</a>#', $html );
			$this->assertMatchesRegularExpression( '#<a href="https://example.org/home-esterna" target="_blank" rel="noopener noreferrer">\s*Home esterna#', $html );
			$this->assertStringNotContainsString( esc_url( get_permalink( $external ) ), $html );
			$this->assertDoesNotMatchRegularExpression( '#<a [^>]*>\s*Home senza link#', $html );
			$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
			$this->assertMatchesRegularExpression( '#<a class="it-card-category it-card-link" href="' . preg_quote( esc_url( $archive ), '#' ) . '[^"]*tipologia[^"]*articolo-home#', $html );
			$this->assertStringContainsString( 'Articolo', $html );
		}
	}

	/**
	 * Authors (by display name) and year are shown; the description is not.
	 */
	public function test_authors_and_year_are_shown_instead_of_the_description() {
		$person = $this->create_post( PEOPLE_POST_TYPE, 'Persona home' );
		$this->set_acf_fields( $person, array( 'nome' => 'Grace', 'cognome' => 'Hopper' ) );
		$this->create_promoted_publication(
			'Home autori',
			array(
				'autori' => array( $person ),
				'anno'   => 2023,
			),
			array( 'post_content' => 'Descrizione da non mostrare.' )
		);

		foreach ( array( $this->render_list(), $this->render_box() ) as $html ) {
			$this->assertStringContainsString( 'Grace Hopper', $html );
			$this->assertStringContainsString( '2023', $html );
			$this->assertStringNotContainsString( 'Descrizione da non mostrare.', $html );
		}
	}

	/**
	 * A protected publication shows only its title and type, and leads to its page.
	 */
	public function test_a_protected_publication_shows_only_title_and_type() {
		$person = $this->create_post( PEOPLE_POST_TYPE, 'Persona riservata' );
		$this->set_acf_fields( $person, array( 'nome' => 'Alan', 'cognome' => 'Turing' ) );
		$protected = $this->create_promoted_publication(
			'Home protetta',
			array(
				'autori'         => array( $person ),
				'anno'           => 1950,
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/riservato',
			),
			array(
				'post_password' => 'segreto',
				'post_content'  => 'Testo riservato.',
			)
		);
		$term = wp_insert_term( 'Rapporto', PUBLICATION_TYPE_TAXONOMY, array( 'slug' => 'rapporto-home' ) );
		wp_set_object_terms( $protected, array( (int) $term['term_id'] ), PUBLICATION_TYPE_TAXONOMY );

		foreach ( array( $this->render_list(), $this->render_box() ) as $html ) {
			$this->assertStringContainsString( 'Home protetta', $html );
			$this->assertStringContainsString( 'Rapporto', $html );
			$this->assertStringContainsString( esc_url( get_permalink( $protected ) ), $html );
			$this->assertStringNotContainsString( 'Alan Turing', $html );
			$this->assertStringNotContainsString( '1950', $html );
			$this->assertStringNotContainsString( 'Testo riservato.', $html );
			$this->assertStringNotContainsString( 'example.org/riservato', $html );
		}
	}

	/**
	 * The box keeps its contract: it stays a column of the featured contents with its heading.
	 */
	public function test_box_keeps_its_column_and_heading() {
		$archive = $this->create_archive_page();
		$this->create_promoted_publication( 'Home colonna' );

		$html = $this->render_box();

		$this->assertStringContainsString( 'col-lg-4', $html );
		$this->assertMatchesRegularExpression( '#<h2 id="evidenza-pubblicazioni-title-1" class="h3 pb-2">Box pubblicazioni</h2>#', $html );
		$this->assertStringContainsString( '<a href="' . esc_url( $archive ) . '" class="btn btn-secondary">', $html, 'The button leads to the publications page.' );
	}

	/**
	 * Without a publications page there is no button and no link of the types: never an empty link.
	 */
	public function test_without_the_publications_page_there_are_no_empty_links() {
		$publication = $this->create_promoted_publication( 'Home senza pagina' );
		$term        = wp_insert_term( 'Tesi', PUBLICATION_TYPE_TAXONOMY, array( 'slug' => 'tesi-home' ) );
		wp_set_object_terms( $publication, array( (int) $term['term_id'] ), PUBLICATION_TYPE_TAXONOMY );

		foreach ( array( $this->render_list(), $this->render_box() ) as $html ) {
			$this->assertStringContainsString( 'Tesi', $html );
			$this->assertStringNotContainsString( 'btn btn-secondary', $html );
			$this->assertStringNotContainsString( 'href="?tipologia', $html, 'A relative link would filter the homepage itself.' );
			$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
		}
	}
}
