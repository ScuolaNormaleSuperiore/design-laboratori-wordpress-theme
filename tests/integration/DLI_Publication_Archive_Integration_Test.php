<?php
/**
 * The list of publications and the site search: where each title leads and what the card shows.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Rendering of `page-templates/pubblicazioni.php` and `page-templates/cerca.php` for publications.
 */
class DLI_Publication_Archive_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a publication in Italian.
	 *
	 * @param array<string, mixed> $post_args Arguments of wp_insert_post().
	 * @param array<string, mixed> $fields    ACF fields, by name.
	 * @return int Publication ID.
	 */
	private function create_publication( array $post_args, array $fields = array() ) {
		$publication_id = $this->create_post( PUBLICATION_POST_TYPE, $post_args['post_title'] );
		wp_update_post( array_merge( array( 'ID' => $publication_id ), $post_args ) );
		$this->set_acf_fields( $publication_id, array_merge( array( 'anno' => 2024 ), $fields ) );
		return $publication_id;
	}

	/**
	 * Render a page template as the page that uses it.
	 *
	 * @param string               $template Path of the template in the theme.
	 * @param array<string, mixed> $query    Values of the query string.
	 * @return string Rendered HTML.
	 */
	private function render_page( $template, array $query = array() ) {
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Pagina di prova',
			)
		);
		dli_set_post_language( $page_id, 'it' );
		$original_get = $_GET;
		$this->go_to( get_permalink( $page_id ) );
		// go_to() rebuilds the query string from the address: set the values afterwards.
		$_GET            = $query;
		$GLOBALS['post'] = get_post( $page_id );
		setup_postdata( $GLOBALS['post'] );

		try {
			return $this->render_theme_template( $template );
		} finally {
			$_GET = $original_get;
		}
	}

	/**
	 * The neutral text that says a link opens in a new window.
	 *
	 * @return string
	 */
	private function new_window_notice() {
		return esc_html__( 'si apre in una nuova finestra', 'design_laboratori_italia' );
	}

	/**
	 * Each title leads where the publication says: the detail page, the external site, or nowhere.
	 */
	public function test_archive_titles_follow_the_choice_of_each_publication() {
		$detail   = $this->create_publication( array( 'post_title' => 'Archivio scheda' ) );
		$external = $this->create_publication(
			array( 'post_title' => 'Archivio sito esterno' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/sito-esterno',
			)
		);
		$this->create_publication( array( 'post_title' => 'Archivio senza link' ), array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );

		$html = $this->render_page( 'page-templates/pubblicazioni.php' );

		$this->assertMatchesRegularExpression( '#<a href="' . preg_quote( esc_url( get_permalink( $detail ) ), '#' ) . '">\s*Archivio scheda\s*</a>#', $html, 'The detail page is the default and opens in the same window.' );
		$this->assertMatchesRegularExpression( '#<a href="https://example.org/sito-esterno" target="_blank" rel="noopener noreferrer">\s*Archivio sito esterno\s*<span class="visually-hidden">\(' . preg_quote( $this->new_window_notice(), '#' ) . '\)</span>\s*</a>#', $html );
		$this->assertStringContainsString( 'Archivio senza link', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*>\s*Archivio senza link#', $html, 'Without a link the title is plain text, not an empty link.' );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html );
		$this->assertStringNotContainsString( esc_url( get_permalink( $external ) ), $html, 'A publication that leads to its site does not link to its own page.' );
	}

	/**
	 * The card shows the authors, not the description, and nothing of a protected publication.
	 */
	public function test_archive_cards_show_authors_instead_of_the_description() {
		$person = $this->create_post( PEOPLE_POST_TYPE, 'Persona autore' );
		$this->set_acf_fields( $person, array( 'nome' => 'Ada', 'cognome' => 'Lovelace' ) );
		$this->create_publication(
			array( 'post_title' => 'Con autori', 'post_content' => 'Testo lungo della descrizione.' ),
			array( 'autori' => array( $person ) )
		);
		$this->create_publication(
			array( 'post_title' => 'Protetta in elenco', 'post_content' => 'Testo riservato.', 'post_password' => 'segreto' ),
			array( 'autori' => array( $person ) )
		);

		$html = $this->render_page( 'page-templates/pubblicazioni.php' );

		$this->assertStringContainsString( 'Ada Lovelace', $html );
		$this->assertStringNotContainsString( 'Testo lungo della descrizione.', $html );
		$this->assertStringNotContainsString( 'Testo riservato.', $html );
		$this->assertSame( 1, substr_count( $html, 'Ada Lovelace' ), 'The authors of a protected publication are not listed.' );
	}

	/**
	 * The site search lists a publication without link as plain text and marks external links.
	 */
	public function test_search_results_without_link_have_no_anchor_and_external_ones_are_marked() {
		$this->create_publication(
			array( 'post_title' => 'Ricerca esterna zebra' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/ricerca',
			)
		);
		$this->create_publication( array( 'post_title' => 'Ricerca senza link zebra' ), array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );

		$html = $this->render_page(
			'page-templates/cerca.php',
			array(
				'searchstring'          => 'zebra',
				'cercasito_nonce_field' => 'x',
			)
		);

		$this->assertMatchesRegularExpression( '#<a class="d-flex[^>]*href="https://example.org/ricerca" target="_blank" rel="noopener noreferrer">#', $html );
		$this->assertStringContainsString( $this->new_window_notice(), $html );
		$this->assertStringContainsString( 'Ricerca senza link zebra', $html );
		$this->assertDoesNotMatchRegularExpression( '#<a [^>]*href=""#', $html, 'A result without a link must not be an empty link.' );
	}
}
