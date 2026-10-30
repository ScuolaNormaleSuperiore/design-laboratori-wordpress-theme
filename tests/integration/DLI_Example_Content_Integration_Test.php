<?php
/**
 * The example pages of Strumenti DLI → Utilità: creation, languages, removal, permissions and form.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_Example_Content` and its tool in `DLI_Tools_Admin`.
 */
class DLI_Example_Content_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Add the English language: the set-up of the base class creates only the Italian one.
	 */
	private function add_english() {
		$language = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		$this->assertNotWPError( $language );
	}

	/**
	 * Every page created by the tool, in every language.
	 *
	 * @return int[]
	 */
	private function example_pages() {
		return get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_key'       => DLI_Example_Content::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Test.
				'lang'           => '',
				'fields'         => 'ids',
			)
		);
	}

	/**
	 * Log in an administrator, who can also edit the site configuration.
	 *
	 * @return int User ID.
	 */
	private function login_administrator() {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		wp_get_current_user()->add_cap( DLI_EDIT_CONFIG_PERMISSION );
		return $user_id;
	}

	/**
	 * Only the languages of the site can be asked for.
	 */
	public function test_available_languages_are_the_ones_of_the_site() {
		$this->assertSame( array( 'it' ), DLI_Example_Content::languages() );

		$this->add_english();

		$this->assertSame( array( 'it', 'en' ), DLI_Example_Content::languages() );
	}

	/**
	 * Both languages: 15 pages each, translations of each other, only the page with subpages shows them as cards.
	 */
	public function test_create_both_languages_with_linked_translations() {
		$this->add_english();
		$author = $this->login_administrator();

		$report = DLI_Example_Content::create( array( 'it', 'en' ), $author );

		$this->assertNotWPError( $report );
		$this->assertSame( 30, $report['created'] );
		$this->assertSame( 0, $report['skipped'] );
		$this->assertCount( 30, $this->example_pages() );
		$this->assertSame( array( 'it', 'en' ), array_keys( $report['top'] ), 'The report leads to the main pages of each language.' );
		$this->assertCount( 4, $report['top']['it'], 'Four main pages in Italian.' );
		$this->assertCount( 4, $report['top']['en'] );
		$this->assertSame(
			array( 'Open Access', 'Dati della ricerca', 'Valutazione della ricerca', 'Monitoraggio' ),
			wp_list_pluck( $report['top']['it'], 'title' ),
			'In the order of the menu.'
		);
		$this->assertSame(
			array( 'Open Access', 'Research Data', 'Research Assessment', 'Monitoring' ),
			wp_list_pluck( $report['top']['en'], 'title' )
		);

		$rdm_it = (int) url_to_postid( $report['top']['it'][1]['url'] );
		$rdm_en = (int) url_to_postid( $report['top']['en'][1]['url'] );
		$this->assertSame( 'Dati della ricerca', get_the_title( $rdm_it ) );
		$this->assertSame( 'Research Data', get_the_title( $rdm_en ) );
		$this->assertSame( array( 'it' => $rdm_it, 'en' => $rdm_en ), pll_get_post_translations( $rdm_it ) );
		$this->assertSame( '1', (string) get_post_meta( $rdm_it, 'mostra_sottopagine_a_card', true ) );

		foreach ( $this->example_pages() as $page_id ) {
			$this->assertCount( 2, pll_get_post_translations( $page_id ), 'Every page has its translation.' );
			$this->assertSame( 'publish', get_post_status( $page_id ) );
			if ( $page_id !== $rdm_it && $page_id !== $rdm_en ) {
				$this->assertSame( '', (string) get_post_meta( $page_id, 'mostra_sottopagine_a_card', true ), 'Only the page with subpages shows them as cards.' );
			}
		}

		$this->assertCount( 4, dli_get_subpage_cards( $rdm_it ), 'The page with subpages has the four cards.' );
		$this->assertCount( 4, dli_get_subpage_cards( $rdm_en ) );
		foreach ( array( 0, 2, 3 ) as $position ) {
			$this->assertSame( array(), dli_get_subpage_cards( (int) url_to_postid( $report['top']['it'][ $position ]['url'] ) ), 'The other main pages have no subpages.' );
		}
	}

	/**
	 * Texts of lorem ipsum of different length, the same in the two versions of a page.
	 */
	public function test_pages_have_lorem_ipsum_of_different_length() {
		$this->add_english();

		DLI_Example_Content::create( array( 'it', 'en' ), $this->login_administrator() );

		$lengths = array();
		foreach ( $this->example_pages() as $page_id ) {
			$text = wp_strip_all_tags( get_post( $page_id )->post_content );
			$lengths[ $page_id ] = str_word_count( $text );
		}
		$this->assertGreaterThan( 0, min( $lengths ), 'No page without text.' );
		$this->assertGreaterThan( 5, count( array_unique( $lengths ) ), 'The texts are not all of the same length.' );
		$this->assertGreaterThan( 250, max( $lengths ), 'The longest text has the two paragraphs of the example.' );

		$top = pll_get_post_translations( get_page_by_path( 'dati-della-ricerca' )->ID );
		$this->assertSame( get_post( $top['it'] )->post_content, get_post( $top['en'] )->post_content, 'The two versions have the same text.' );
	}

	/**
	 * A second run changes nothing.
	 */
	public function test_running_again_creates_nothing() {
		$this->add_english();
		$author = $this->login_administrator();
		DLI_Example_Content::create( array( 'it', 'en' ), $author );

		$report = DLI_Example_Content::create( array( 'it', 'en' ), $author );

		$this->assertSame( 0, $report['created'] );
		$this->assertSame( 30, $report['skipped'] );
		$this->assertCount( 30, $this->example_pages() );
	}

	/**
	 * Italian first and English later: the English pages are linked to the Italian ones.
	 */
	public function test_languages_created_apart_are_linked() {
		$this->add_english();
		$author = $this->login_administrator();

		$italian = DLI_Example_Content::create( array( 'it' ), $author );
		$this->assertSame( 15, $italian['created'] );
		foreach ( $this->example_pages() as $page_id ) {
			$this->assertCount( 1, pll_get_post_translations( $page_id ), 'Alone, no translation yet.' );
		}

		$english = DLI_Example_Content::create( array( 'en' ), $author );

		$this->assertSame( 15, $english['created'] );
		foreach ( $this->example_pages() as $page_id ) {
			$this->assertCount( 2, pll_get_post_translations( $page_id ) );
		}
	}

	/**
	 * Removing deletes only the pages the tool created.
	 */
	public function test_remove_deletes_only_the_example_pages() {
		$this->add_english();
		DLI_Example_Content::create( array( 'it', 'en' ), $this->login_administrator() );
		$normal = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Pagina di un redattore',
			)
		);
		dli_set_post_language( $normal, 'it' );

		$removed = DLI_Example_Content::remove();

		$this->assertSame( 30, $removed );
		$this->assertSame( array(), $this->example_pages() );
		$this->assertSame( 'publish', get_post_status( $normal ), 'A page written by an editor is not touched.' );
		$this->assertSame( 0, DLI_Example_Content::remove(), 'Nothing left to remove.' );
	}

	/**
	 * An existing page with the same address in another language stops the creation before it writes anything.
	 */
	public function test_an_address_used_by_another_language_refuses_everything() {
		$this->add_english();
		$clash = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Research Data',
			)
		);
		dli_set_post_language( $clash, 'it' );

		$report = DLI_Example_Content::create( array( 'it', 'en' ), $this->login_administrator() );

		$this->assertWPError( $report );
		$this->assertSame( array(), $this->example_pages(), 'Nothing was created.' );
	}

	/**
	 * A language the site does not have is refused.
	 */
	public function test_missing_language_is_refused() {
		$report = DLI_Example_Content::create( array( 'it', 'en' ), $this->login_administrator() );

		$this->assertWPError( $report );
		$this->assertSame( array(), $this->example_pages() );
	}

	/**
	 * The request of the form needs the capability and the nonce.
	 */
	public function test_request_requires_capability_and_nonce() {
		$this->add_english();
		$subscriber = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber );
		$post = array(
			'dli_example_action' => 'create',
			'dli_example_lang'   => 'iten',
			'_wpnonce'           => wp_create_nonce( 'dli_example_content' ),
		);

		$this->assertWPError( DLI_Tools_Admin::process_example_content_request( $post ), 'A user who cannot edit the theme is refused.' );
		$this->assertSame( array(), $this->example_pages() );

		$this->login_administrator();
		$this->assertWPError( DLI_Tools_Admin::process_example_content_request( array_merge( $post, array( '_wpnonce' => 'sbagliato' ) ) ), 'Without a valid nonce nothing happens.' );
		$this->assertSame( array(), $this->example_pages() );

		$post['_wpnonce'] = wp_create_nonce( 'dli_example_content' );
		$report           = DLI_Tools_Admin::process_example_content_request( $post );
		$this->assertNotWPError( $report );
		$this->assertSame( 30, $report['created'] );

		$remove = DLI_Tools_Admin::process_example_content_request( array_merge( $post, array( 'dli_example_action' => 'remove' ) ) );
		$this->assertSame( 30, $remove['removed'] );
		$this->assertWPError( DLI_Tools_Admin::process_example_content_request( array_merge( $post, array( 'dli_example_action' => 'altro' ) ) ), 'Only create and remove are known actions.' );
	}

	/**
	 * The Utilità page has two separate boxes: the existing reload tool and the example pages.
	 */
	public function test_utilities_page_has_two_boxes_one_under_the_other() {
		$this->add_english();
		$this->login_administrator();
		$_GET['page'] = DLI_Tools_Admin::UTILS_SLUG;

		ob_start();
		DLI_Tools_Admin::render_utils();
		$html = (string) ob_get_clean();
		unset( $_GET['page'] );

		$this->assertSame( 2, substr_count( $html, 'class="postbox dli-tool-panel"' ), 'Two separate boxes.' );
		$reload = strpos( $html, 'action=reload' );
		$form   = strpos( $html, 'name="dli_example_action"' );
		$this->assertNotFalse( $reload );
		$this->assertNotFalse( $form );
		$this->assertLessThan( $form, $reload, 'The example pages come under the reload tool.' );
		$this->assertMatchesRegularExpression( '#<form[^>]*method="post"[^>]*action="[^"]*admin-post\.php"#', $html );
		$this->assertStringContainsString( 'name="action" value="dli_example_content"', $html );
		$this->assertStringContainsString( 'name="_wpnonce"', $html );
		$this->assertMatchesRegularExpression( '#<select[^>]*name="dli_example_lang"#', $html );
		$this->assertMatchesRegularExpression( '#<option value="iten"[^>]*selected#', $html, 'Both languages by default.' );
		$this->assertStringContainsString( 'value="create"', $html );
		$this->assertStringContainsString( 'value="remove"', $html );
	}

	/**
	 * With one language only, there is no choice of "both".
	 */
	public function test_language_choices_follow_the_site_languages() {
		$this->login_administrator();
		$_GET['page'] = DLI_Tools_Admin::UTILS_SLUG;

		ob_start();
		DLI_Tools_Admin::render_utils();
		$html = (string) ob_get_clean();
		unset( $_GET['page'] );

		$this->assertStringContainsString( 'value="it"', $html );
		$this->assertStringNotContainsString( 'value="iten"', $html );
		$this->assertStringNotContainsString( 'value="en"', $html );
	}

	/**
	 * Seven pages per language are promoted: the four main pages and the three subpages that have subpages.
	 *
	 * The cards are the four main pages; the groups are the four pages that have subpages.
	 */
	public function test_main_pages_and_subpages_with_children_are_promoted_to_the_home() {
		$this->add_english();
		DLI_Example_Content::create( array( 'it', 'en' ), $this->login_administrator() );

		$promoted = array();
		foreach ( $this->example_pages() as $page_id ) {
			if ( '1' === (string) get_post_meta( $page_id, 'promuovi_in_home', true ) ) {
				$promoted[] = $page_id;
			}
		}
		$this->assertCount( 14, $promoted, 'The four main pages and the three subpages with subpages, in two languages.' );

		PLL()->curlang = PLL()->model->get_language( 'it' );
		$this->assertSame(
			array( 'Open Access', 'Dati della ricerca', 'Valutazione della ricerca', 'Monitoraggio' ),
			wp_list_pluck( dli_get_promoted_page_cards(), 'title' ),
			'The section "Card di navigazione" lists the main pages only, in the order of the menu.'
		);
		$groups = dli_get_promoted_page_groups( 12, 12 );
		$this->assertSame(
			array( 'Dati della ricerca', 'Pianificare', 'Gestire', 'Condividere' ),
			wp_list_pluck( $groups, 'title' ),
			'The section "Navigazione per pagine" has the pages that have subpages: the three new main pages have none.'
		);
		$this->assertSame( array( 4, 2, 3, 2 ), array_map( 'count', wp_list_pluck( $groups, 'items' ) ) );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertCount( 4, dli_get_promoted_page_cards() );
		$this->assertCount( 4, dli_get_promoted_page_groups( 12, 12 ) );
		PLL()->curlang = null;
	}

	/**
	 * Running the tool again turns the promotion on for the example pages created before it did, and touches nothing else.
	 */
	public function test_running_again_promotes_the_pages_created_before() {
		$this->add_english();
		$author = $this->login_administrator();
		DLI_Example_Content::create( array( 'it' ), $author );
		$top = get_page_by_path( 'dati-della-ricerca' );
		delete_post_meta( $top->ID, 'promuovi_in_home' );
		wp_update_post(
			array(
				'ID'         => $top->ID,
				'post_title' => 'Titolo cambiato da un redattore',
			)
		);

		$report = DLI_Example_Content::create( array( 'it' ), $author );

		$this->assertSame( 0, $report['created'] );
		$this->assertSame( 15, $report['skipped'] );
		$this->assertSame( '1', (string) get_post_meta( $top->ID, 'promuovi_in_home', true ), 'The flag is on again.' );
		$this->assertSame( 'Titolo cambiato da un redattore', get_the_title( $top->ID ), 'The rest of the page is left as it is.' );
	}

	/**
	 * A page that an editor wrote is never promoted by the tool, even with the address of an example page.
	 */
	public function test_pages_not_created_by_the_tool_are_not_promoted() {
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Dati della ricerca',
				'post_name'  => 'dati-della-ricerca',
			)
		);
		dli_set_post_language( $page_id, 'it' );

		DLI_Example_Content::create( array( 'it' ), $this->login_administrator() );

		$this->assertSame( '', (string) get_post_meta( $page_id, 'promuovi_in_home', true ) );
	}
}
