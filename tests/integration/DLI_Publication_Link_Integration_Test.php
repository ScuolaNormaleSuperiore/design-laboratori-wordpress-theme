<?php
/**
 * Where the title of a publication leads: the detail page, the external site or the attached file.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Truth table of `dli_get_publication_link()` and of the publication wrapper that the search, the
 * suggestions and the carousel use, plus the events, which share `dli_manage_item_link()`.
 */
class DLI_Publication_Link_Integration_Test extends DLI_Integration_Test_Case {

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
	 * Create a PDF in the media library, without a real file.
	 *
	 * @param string $name File name.
	 * @return int Attachment ID.
	 */
	private function create_pdf( $name ) {
		$attachment_id = self::factory()->attachment->create( array( 'post_mime_type' => 'application/pdf' ) );
		update_post_meta( $attachment_id, '_wp_attached_file', '2026/10/' . $name );
		return $attachment_id;
	}

	/**
	 * Without a choice the title leads to the detail page, as for every publication saved before the field existed.
	 */
	public function test_default_leads_to_the_detail_page() {
		$publ_id = $this->create_publication( array( 'post_title' => 'Senza scelta' ), array( 'url' => 'https://example.org/sito' ) );

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( get_permalink( $publ_id ), $link['url'] );
		$this->assertFalse( $link['external'] );
	}

	/**
	 * The detail page is also chosen explicitly, whatever the other fields hold.
	 */
	public function test_detail_page_choice_ignores_the_external_link() {
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Scheda esplicita' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['DETAIL_PAGE'],
				'url'            => 'https://example.org/sito',
			)
		);

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( get_permalink( $publ_id ), $link['url'] );
		$this->assertFalse( $link['external'] );
	}

	/**
	 * The website choice leads to the Url field, which is an external link.
	 */
	public function test_website_choice_leads_to_the_url_field() {
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Sito web' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/sito',
			)
		);

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( 'https://example.org/sito', $link['url'] );
		$this->assertTrue( $link['external'] );
	}

	/**
	 * The website choice without an Url gives no link at all, never an empty one that points to the same page.
	 */
	public function test_website_choice_without_url_has_no_link() {
		$publ_id = $this->create_publication( array( 'post_title' => 'Sito web vuoto' ), array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( '', $link['url'] );
		$this->assertFalse( $link['external'] );
	}

	/**
	 * The attachment choice leads to the PDF.
	 */
	public function test_attachment_choice_leads_to_the_file() {
		$pdf     = $this->create_pdf( 'articolo.pdf' );
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Con allegato' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['ATTACHMENT'],
				'allegato'       => $pdf,
			)
		);

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( wp_get_attachment_url( $pdf ), $link['url'] );
		$this->assertStringEndsWith( '/2026/10/articolo.pdf', $link['url'] );
		$this->assertTrue( $link['external'] );
	}

	/**
	 * The attachment choice without a file gives no link.
	 */
	public function test_attachment_choice_without_file_has_no_link() {
		$publ_id = $this->create_publication( array( 'post_title' => 'Allegato vuoto' ), array( 'link_dettaglio' => DLI_ITEM_LINK['ATTACHMENT'] ) );

		$this->assertSame( '', dli_get_publication_link( $publ_id )['url'] );
	}

	/**
	 * A password-protected publication always leads to its detail page: the external link and the file are what the password protects.
	 */
	public function test_protected_publication_always_leads_to_the_detail_page() {
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Protetta', 'post_password' => 'segreto' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/riservato',
			)
		);

		$link = dli_get_publication_link( $publ_id );

		$this->assertSame( get_permalink( $publ_id ), $link['url'] );
		$this->assertFalse( $link['external'] );
	}

	/**
	 * The wrapper used by the search, the suggestions and the carousel carries the same link.
	 */
	public function test_wrapper_carries_the_link_and_the_description() {
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Nel wrapper', 'post_content' => 'Descrizione breve.' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/sito',
			)
		);

		$wrapper = dli_get_post_wrapper( get_post( $publ_id ) );

		$this->assertSame( 'https://example.org/sito', $wrapper['link'] );
		$this->assertTrue( $wrapper['link_external'] );
		$this->assertSame( 'Descrizione breve.', $wrapper['description'] );
	}

	/**
	 * The wrapper of a protected publication has neither the description nor the content, and leads to the detail page.
	 */
	public function test_wrapper_of_a_protected_publication_hides_what_the_password_protects() {
		$publ_id = $this->create_publication(
			array( 'post_title' => 'Wrapper protetto', 'post_content' => 'Testo riservato.', 'post_password' => 'segreto' ),
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'url'            => 'https://example.org/riservato',
			)
		);

		$wrapper = dli_get_post_wrapper( get_post( $publ_id ) );

		$this->assertSame( get_permalink( $publ_id ), $wrapper['link'] );
		$this->assertSame( '', $wrapper['description'] );
		$this->assertSame( '', $wrapper['full_content'] );
	}

	/**
	 * Events keep their own website field: the shared function must not read the publication field for them.
	 */
	public function test_events_still_use_their_own_website_field() {
		$event_id = $this->create_post( EVENT_POST_TYPE, 'Evento con sito' );
		$this->set_acf_fields(
			$event_id,
			array(
				'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'],
				'sitoweb'        => 'https://example.org/evento',
			)
		);

		$this->assertSame( 'https://example.org/evento', dli_manage_item_link( get_post( $event_id ) ) );
	}
}
