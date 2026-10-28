<?php
/**
 * The detail page of a publication: registration, address, REST access and rendered content.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Detail page (`single-pubblicazione.php`) and the settings that expose it.
 */
class DLI_Publication_Detail_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a person with its first name, last name and optional flags.
	 *
	 * @param string               $title  Post title.
	 * @param string               $status Post status.
	 * @param array<string, mixed> $fields ACF fields, by name.
	 * @return int Person ID.
	 */
	private function create_person( $title, $status = 'publish', array $fields = array() ) {
		$person_id = $this->create_post( PEOPLE_POST_TYPE, $title, $status );
		$this->set_acf_fields( $person_id, $fields );
		return $person_id;
	}

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
		$this->set_acf_fields( $publication_id, $fields );
		return $publication_id;
	}

	/**
	 * Switch the site to readable addresses, as a real site already is when WordPress registers the content types.
	 *
	 * In the test process the content types are registered at start-up, when the permalinks are
	 * still empty, and WordPress adds the rewrite rules of a content type only if the permalinks are
	 * on at registration time: the publication post type is registered again, like the core tests do.
	 */
	private function use_readable_addresses() {
		$this->set_permalink_structure( '/%postname%/' );
		( new DLI_Publication_Manager() )->add_post_type();
		flush_rewrite_rules();
	}

	/**
	 * Render the detail template for a publication, as WordPress does for a single request.
	 *
	 * @param int $publication_id Publication ID.
	 * @return string Rendered HTML.
	 */
	private function render_single( $publication_id ) {
		$this->go_to( '/?post_type=' . PUBLICATION_POST_TYPE . '&p=' . $publication_id );
		$GLOBALS['post'] = get_post( $publication_id );
		setup_postdata( $GLOBALS['post'] );

		return $this->render_theme_template( 'single-pubblicazione.php' );
	}

	/**
	 * The detail page is switched on by the configuration.
	 */
	public function test_detail_is_enabled() {
		$this->assertTrue( DLI_PUBLICATION_HAS_DETAIL );
	}

	/**
	 * With the detail active the content type is public, searchable and available in menus.
	 */
	public function test_post_type_is_public_when_the_detail_is_active() {
		$post_type = get_post_type_object( PUBLICATION_POST_TYPE );

		$this->assertTrue( $post_type->publicly_queryable );
		$this->assertFalse( $post_type->exclude_from_search );
		$this->assertTrue( $post_type->show_in_nav_menus );
	}

	/**
	 * A publication has a readable address, like events and news, not `?p=ID`.
	 */
	public function test_publication_has_a_readable_address() {
		$this->use_readable_addresses();
		$publication_id = $this->create_publication( array( 'post_title' => 'Titolo per indirizzo', 'post_name' => 'titolo-per-indirizzo' ) );

		$this->assertSame( home_url( '/pubblicazione/titolo-per-indirizzo/' ), get_permalink( $publication_id ) );
	}

	/**
	 * The rewrite rules of the new address are written once per rules version, not at every request.
	 */
	public function test_rewrite_rules_are_flushed_once_per_version() {
		$this->use_readable_addresses();
		delete_option( 'dli_publication_rules_version' );
		update_option( 'rewrite_rules', 'sentinel' );

		( new DLI_Publication_Manager() )->maybe_flush_rewrite_rules();

		$rules = get_option( 'rewrite_rules' );
		$this->assertIsArray( $rules );
		$this->assertArrayHasKey( 'pubblicazione/([^/]+)(?:/([0-9]+))?/?$', $rules );
		$this->assertNotEmpty( get_option( 'dli_publication_rules_version' ) );

		update_option( 'rewrite_rules', 'sentinel' );
		( new DLI_Publication_Manager() )->maybe_flush_rewrite_rules();
		$this->assertSame( 'sentinel', get_option( 'rewrite_rules' ), 'The rules must not be rewritten when the version is already stored.' );
	}

	/**
	 * With the detail active, anonymous visitors are not blocked on the publications REST route.
	 *
	 * The REST API can still be disabled as a whole by the theme setting: this only checks that the
	 * publication-specific restriction is off, and that the route exists.
	 */
	public function test_publications_rest_route_is_not_restricted_for_anonymous_visitors() {
		wp_set_current_user( 0 );

		$this->assertArrayHasKey( '/wp/v2/' . PUBLICATION_POST_TYPE, rest_get_server()->get_routes() );
		$request = new WP_REST_Request( 'GET', '/wp/v2/' . PUBLICATION_POST_TYPE );
		$this->assertNull( dli_restrict_publication_rest_access( null, rest_get_server(), $request ) );
	}

	/**
	 * The archive page of the publications is listed among the archive pages of the site.
	 */
	public function test_publications_archive_is_listed_when_the_detail_is_active() {
		$this->assertArrayHasKey( SLUG_PUBBLICAZIONI_IT, dli_get_pt_archive_slugs() );
		$this->assertSame( PUBLICATION_POST_TYPE, dli_get_pt_archive_slugs()[ SLUG_PUBBLICAZIONI_IT ] );
	}

	/**
	 * A complete publication shows type, title, year, authors, description and the external link.
	 */
	public function test_complete_publication_shows_all_its_blocks() {
		$type = wp_insert_term( 'Articolo di prova', PUBLICATION_TYPE_TAXONOMY );
		dli_set_term_language( $type['term_id'], 'it' );
		$linked   = $this->create_person( 'Autore Linkato', 'publish', array( 'nome' => 'Ada', 'cognome' => 'Lovelace' ) );
		$no_page  = $this->create_person( 'Autore Senza Scheda', 'publish', array( 'nome' => 'Alan', 'cognome' => 'Turing', 'disattiva_pagina_dettaglio' => 1 ) );
		$publ_id  = $this->create_publication(
			array( 'post_title' => 'Pubblicazione completa', 'post_content' => 'Testo descrittivo della pubblicazione.' ),
			array(
				'anno'   => 2024,
				'autori' => array( $linked, $no_page ),
				'url'    => 'https://example.org/pubblicazione',
			)
		);
		wp_set_object_terms( $publ_id, array( (int) $type['term_id'] ), PUBLICATION_TYPE_TAXONOMY );

		$html = $this->render_single( $publ_id );

		$this->assertMatchesRegularExpression( '#<h2 id="dli-hero-pubblicazione-title">Pubblicazione completa</h2>#', $html );
		$this->assertStringContainsString( 'Articolo di prova', $html );
		$this->assertMatchesRegularExpression( '#<time datetime="2024">2024</time>#', $html );
		$this->assertStringContainsString( 'Testo descrittivo della pubblicazione.', $html );
		$this->assertMatchesRegularExpression( '#<a href="' . preg_quote( get_permalink( $linked ), '#' ) . '">Ada Lovelace</a>#', $html );
		$this->assertStringContainsString( 'Alan Turing', $html );
		$this->assertStringNotContainsString( get_permalink( $no_page ), $html, 'An author with the detail page disabled has no link.' );
		$this->assertMatchesRegularExpression( '#<a class="btn btn-primary btn-icon" href="https://example.org/pubblicazione" target="_blank" rel="noopener noreferrer">#', $html );
		$this->assertStringContainsString( 'pubblicazione-copertina-placeholder', $html, 'Without a cover the placeholder is shown.' );
	}

	/**
	 * A publication with only title and year has no empty blocks.
	 */
	public function test_minimal_publication_has_no_empty_blocks() {
		// The post factory fills the content with a sample text: a minimal publication has none.
		$publ_id = $this->create_publication( array( 'post_title' => 'Pubblicazione minima', 'post_content' => '' ), array( 'anno' => 2023 ) );

		$html = $this->render_single( $publ_id );

		$this->assertStringContainsString( 'Pubblicazione minima', $html );
		$this->assertStringNotContainsString( 'id="autori"', $html );
		$this->assertStringNotContainsString( 'id="descrizione"', $html );
		$this->assertStringNotContainsString( 'id="link"', $html );
		$this->assertStringContainsString( 'pubblicazione-copertina-placeholder', $html );
	}

	/**
	 * The description `.`, written by the importers as a placeholder, is not shown.
	 */
	public function test_placeholder_description_is_not_shown() {
		$publ_id = $this->create_publication( array( 'post_title' => 'Con segnaposto', 'post_content' => '.' ), array( 'anno' => 2021 ) );

		$this->assertStringNotContainsString( 'id="descrizione"', $this->render_single( $publ_id ) );
	}

	/**
	 * A password-protected publication shows the password form, and nothing that the password protects.
	 */
	public function test_protected_publication_shows_only_the_password_form() {
		$author  = $this->create_person( 'Autore Riservato', 'publish', array( 'nome' => 'Grace', 'cognome' => 'Hopper' ) );
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Pubblicazione protetta', 'post_content' => 'Testo riservato.', 'post_password' => 'segreto' ),
			array(
				'anno'   => 2022,
				'autori' => array( $author ),
				'url'    => 'https://example.org/riservato',
			)
		);

		$html = $this->render_single( $publ_id );

		$this->assertStringContainsString( 'name="post_password"', $html );
		$this->assertStringNotContainsString( 'Testo riservato.', $html );
		$this->assertStringNotContainsString( 'Grace Hopper', $html );
		$this->assertStringNotContainsString( 'https://example.org/riservato', $html );
		$this->assertStringNotContainsString( 'id="autori"', $html );
	}

	/**
	 * Authors that are not published or have no name are not listed.
	 */
	public function test_unpublished_and_nameless_authors_are_not_listed() {
		$draft    = $this->create_person( 'Autore In Bozza', 'draft', array( 'nome' => 'Ada', 'cognome' => 'Bozza' ) );
		$nameless = $this->create_person( 'Nome Dal Titolo', 'publish', array( 'nome' => '', 'cognome' => '' ) );
		$publ_id  = $this->create_publication(
			array( 'post_title' => 'Autori particolari' ),
			array(
				'anno'   => 2020,
				'autori' => array( $draft, $nameless ),
			)
		);

		$html = $this->render_single( $publ_id );

		$this->assertStringNotContainsString( 'Ada Bozza', $html );
		$this->assertStringNotContainsString( 'Autore In Bozza', $html );
		$this->assertStringNotContainsString( get_permalink( $draft ), $html );
		$this->assertMatchesRegularExpression( '#<a href="' . preg_quote( get_permalink( $nameless ), '#' ) . '">Nome Dal Titolo</a>#', $html, 'A person without first and last name is shown with the title.' );
	}

	/**
	 * The cover uses the alternative text written in the media library, or a neutral text without it.
	 */
	public function test_cover_uses_the_alternative_text_of_the_attachment() {
		$with_alt = $this->create_publication( array( 'post_title' => 'Con copertina e testo alternativo' ), array( 'anno' => 2019 ) );
		$no_alt   = $this->create_publication( array( 'post_title' => 'Con copertina senza testo' ), array( 'anno' => 2018 ) );

		foreach ( array( $with_alt => 'Copertina rossa con titolo', $no_alt => '' ) as $publ_id => $alt ) {
			$attachment_id = self::factory()->attachment->create(
				array(
					'post_mime_type' => 'image/png',
					'post_parent'    => $publ_id,
				)
			);
			update_post_meta( $attachment_id, '_wp_attached_file', '2026/10/copertina-' . $publ_id . '.png' );
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
			set_post_thumbnail( $publ_id, $attachment_id );
		}

		$html_alt = $this->render_single( $with_alt );
		$this->assertMatchesRegularExpression( '#class="figure-img img-fluid rounded shadow-sm mb-0" src="[^"]+copertina-' . $with_alt . '\.png" alt="Copertina rossa con titolo"#', $html_alt );
		$this->assertStringNotContainsString( 'pubblicazione-copertina-placeholder', $html_alt );

		$html_no_alt = $this->render_single( $no_alt );
		$this->assertMatchesRegularExpression( '#src="[^"]+copertina-' . $no_alt . '\.png" alt="Copertina della pubblicazione"#', $html_no_alt );
	}
}
