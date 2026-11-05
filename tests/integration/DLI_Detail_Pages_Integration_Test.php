<?php
/**
 * Detail pages that the editor has switched off: they answer 404 and are not offered by the site search.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * People (`disattiva_pagina_dettaglio`) and events, news and publications (`link_dettaglio` other than
 * `scheda`) can be set up "without a detail page". The setting must close the page itself, not only the
 * links that lead to it.
 */
class DLI_Detail_Pages_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Make Italian the current language and keep the canonical redirect out of the way.
	 *
	 * `redirect_canonical()` and the redirect of the old slugs may answer a request with a redirect and
	 * stop the process: the tests only look at what the theme decides on the request.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
		PLL()->curlang           = PLL()->model->get_language( 'it' );
		remove_action( 'template_redirect', 'redirect_canonical' );
		remove_action( 'template_redirect', 'wp_old_slug_redirect' );
	}

	/**
	 * Put back the current language.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Create a post with its ACF fields.
	 *
	 * @param string               $post_type Post type.
	 * @param string               $title     Title.
	 * @param array<string, mixed> $fields    ACF fields, by name.
	 * @param string               $status    Post status.
	 * @return int Post ID.
	 */
	private function create_content( $post_type, $title, array $fields = array(), $status = 'publish' ) {
		$post_id = $this->create_post( $post_type, $title, $status );
		$this->set_acf_fields( $post_id, $fields );
		return $post_id;
	}

	/**
	 * Request the detail page of a post and let the theme decide, as WordPress does before the template.
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Query The main query after `template_redirect`.
	 */
	private function request_detail( $post_id ) {
		$this->go_to( '/?post_type=' . get_post_type( $post_id ) . '&p=' . $post_id );
		$this->assertTrue( is_singular(), 'The request must be a single post.' );
		do_action( 'template_redirect' );
		return $GLOBALS['wp_query'];
	}

	/**
	 * Create the page of the people list, which the search results need to build a link.
	 */
	private function create_people_page() {
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Persone',
				'post_name'  => SLUG_PERSONE_IT,
			)
		);
		dli_set_post_language( $page_id, 'it' );
	}

	/**
	 * A person with the detail page switched off answers 404.
	 */
	public function test_person_with_disabled_detail_page_is_not_found() {
		$person_id = $this->create_content( PEOPLE_POST_TYPE, 'Persona senza scheda', array( 'disattiva_pagina_dettaglio' => 1 ) );

		$this->assertTrue( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * A person that did not switch it off keeps the page.
	 */
	public function test_person_with_detail_page_is_found() {
		$person_id = $this->create_content( PEOPLE_POST_TYPE, 'Persona con scheda' );

		$this->assertFalse( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * The direct link to the personal site changes where the link leads, it does not close the page.
	 */
	public function test_person_with_direct_site_link_keeps_its_page() {
		$person_id = $this->create_content( PEOPLE_POST_TYPE, 'Persona con sito', array( 'abilita_link_diretto_pagina_persona' => 1 ) );

		$this->assertFalse( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * Only the switch of the detail page closes it, also when the direct link is on.
	 */
	public function test_person_with_direct_site_link_and_disabled_page_is_not_found() {
		$person_id = $this->create_content(
			PEOPLE_POST_TYPE,
			'Persona con sito senza scheda',
			array(
				'abilita_link_diretto_pagina_persona' => 1,
				'disattiva_pagina_dettaglio'          => 1,
			)
		);

		$this->assertTrue( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * The ways of linking an event, a news item or a publication that leave the detail page out.
	 *
	 * @return array<string, array{0: string, 1: string}> Post type and link mode.
	 */
	public function link_modes_without_detail_page() {
		return array(
			'event, website'          => array( EVENT_POST_TYPE, DLI_ITEM_LINK['WEBSITE'] ),
			'event, attachment'       => array( EVENT_POST_TYPE, DLI_ITEM_LINK['ATTACHMENT'] ),
			'news, website'           => array( NEWS_POST_TYPE, DLI_ITEM_LINK['WEBSITE'] ),
			'news, attachment'        => array( NEWS_POST_TYPE, DLI_ITEM_LINK['ATTACHMENT'] ),
			'publication, website'    => array( PUBLICATION_POST_TYPE, DLI_ITEM_LINK['WEBSITE'] ),
			'publication, attachment' => array( PUBLICATION_POST_TYPE, DLI_ITEM_LINK['ATTACHMENT'] ),
		);
	}

	/**
	 * An event, a news item or a publication that leads somewhere else answers 404 on its own page.
	 *
	 * @dataProvider link_modes_without_detail_page
	 *
	 * @param string $post_type Post type.
	 * @param string $mode      Value of `link_dettaglio`.
	 */
	public function test_content_that_links_elsewhere_is_not_found( $post_type, $mode ) {
		$post_id = $this->create_content( $post_type, 'Contenuto senza scheda', array( 'link_dettaglio' => $mode ) );

		$this->assertTrue( $this->request_detail( $post_id )->is_404() );
	}

	/**
	 * The types that have a detail page choice.
	 *
	 * @return array<string, array{0: string}> Post type.
	 */
	public function types_with_link_mode() {
		return array(
			'event'       => array( EVENT_POST_TYPE ),
			'news'        => array( NEWS_POST_TYPE ),
			'publication' => array( PUBLICATION_POST_TYPE ),
		);
	}

	/**
	 * The detail page is open when it is the chosen destination, and when nothing was chosen.
	 *
	 * @dataProvider types_with_link_mode
	 *
	 * @param string $post_type Post type.
	 */
	public function test_content_with_detail_page_is_found( $post_type ) {
		$chosen    = $this->create_content( $post_type, 'Con scheda scelta', array( 'link_dettaglio' => DLI_ITEM_LINK['DETAIL_PAGE'] ) );
		$not_asked = $this->create_content( $post_type, 'Con scheda di default' );

		$this->assertFalse( $this->request_detail( $chosen )->is_404() );
		$this->assertFalse( $this->request_detail( $not_asked )->is_404() );
	}

	/**
	 * A publication protected by a password always leads to its own page, so the page stays open.
	 */
	public function test_protected_publication_keeps_its_page() {
		$publication_id = $this->create_content( PUBLICATION_POST_TYPE, 'Pubblicazione protetta', array( 'link_dettaglio' => DLI_ITEM_LINK['WEBSITE'] ) );
		wp_update_post(
			array(
				'ID'            => $publication_id,
				'post_password' => 'segreta',
			)
		);

		$this->assertFalse( $this->request_detail( $publication_id )->is_404() );
	}

	/**
	 * Who can edit the content still sees the page, otherwise the editor could not fill it in.
	 */
	public function test_editor_who_can_edit_the_content_still_sees_the_page() {
		$person_id = $this->create_content( PEOPLE_POST_TYPE, 'Scheda per chi modifica', array( 'disattiva_pagina_dettaglio' => 1 ) );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$this->assertFalse( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * A logged-in user who cannot edit the content gets the 404 like any visitor.
	 */
	public function test_user_who_cannot_edit_the_content_gets_404() {
		$person_id = $this->create_content( PEOPLE_POST_TYPE, 'Scheda per un lettore', array( 'disattiva_pagina_dettaglio' => 1 ) );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'subscriber' ) ) );

		$this->assertTrue( $this->request_detail( $person_id )->is_404() );
	}

	/**
	 * Types that cannot be set up without a detail page are not touched.
	 */
	public function test_other_content_types_are_not_affected() {
		$project_id = $this->create_content( PROGETTO_POST_TYPE, 'Progetto con scheda' );

		$this->assertFalse( $this->request_detail( $project_id )->is_404() );
	}

	/**
	 * The site search does not list the people that the editor left out of the lists.
	 */
	public function test_search_does_not_list_people_left_out_of_the_lists() {
		$this->create_content( PEOPLE_POST_TYPE, 'Zebrafish Escluso', array( 'escludi_da_elenco' => 1 ) );
		$this->create_content( PEOPLE_POST_TYPE, 'Zebrafish Visibile' );

		$query  = DLI_ContentsManager::main_search_query( array( PEOPLE_POST_TYPE ), 'Zebrafish', 10, 1 );
		$titles = wp_list_pluck( $query->posts, 'post_title' );

		$this->assertSame( array( 'Zebrafish Visibile' ), $titles );
	}

	/**
	 * A search result for a person without detail page is shown without a link, as in the lists.
	 */
	public function test_search_result_of_a_person_without_detail_page_has_no_link() {
		$this->create_people_page();
		$closed = $this->create_content( PEOPLE_POST_TYPE, 'Persona chiusa', array( 'disattiva_pagina_dettaglio' => 1 ) );
		$open   = $this->create_content( PEOPLE_POST_TYPE, 'Persona aperta' );

		$this->assertSame( '', dli_get_post_wrapper( get_post( $closed ), 'medium' )['link'] );
		$this->assertSame( get_permalink( $open ), dli_get_post_wrapper( get_post( $open ), 'medium' )['link'] );
	}
}
