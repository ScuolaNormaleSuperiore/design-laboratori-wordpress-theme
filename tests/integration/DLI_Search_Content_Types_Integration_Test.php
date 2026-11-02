<?php
/**
 * The content types offered by the site search as filters: only the ones that have a match for the search.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_ContentsManager::get_contenttypes_with_search_results()`.
 */
class DLI_Search_Content_Types_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * The arguments of every query made while a call runs.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private $queries = array();

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
	 * Remember the arguments of a query before it runs.
	 *
	 * @param WP_Query $query The query.
	 */
	public function record_query( $query ) {
		$this->queries[] = array(
			'posts_per_page' => $query->get( 'posts_per_page' ),
			'fields'         => $query->get( 'fields' ),
			'no_found_rows'  => $query->get( 'no_found_rows' ),
			'post_type'      => $query->get( 'post_type' ),
		);
	}

	/**
	 * Only the types with a published match are listed, in the order of the search.
	 */
	public function test_only_types_with_a_match_are_listed_in_the_order_of_the_search() {
		$this->create_post( PUBLICATION_POST_TYPE, 'Zebrafish pubblicazione' );
		$this->create_post( NEWS_POST_TYPE, 'Notizia sullo Zebrafish' );
		$this->create_post( PEOPLE_POST_TYPE, 'Zebrafish Rossi' );
		$this->create_post( PROGETTO_POST_TYPE, 'Un altro argomento' );

		$types = DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' );

		$this->assertSame( array( PEOPLE_POST_TYPE, NEWS_POST_TYPE, PUBLICATION_POST_TYPE ), $types, 'The order is the one of the types searched, not of the creation.' );
	}

	/**
	 * A draft is not a match, a search without a match gives nothing.
	 */
	public function test_drafts_and_missing_matches_give_nothing() {
		$this->create_post( EVENT_POST_TYPE, 'Evento Zebrafish in bozza', 'draft' );
		$this->create_post( NEWS_POST_TYPE, 'Notizia pubblicata' );

		$this->assertSame( array(), DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' ) );
		$this->assertSame( array(), DLI_ContentsManager::get_contenttypes_with_search_results( 'parola-inesistente-xyz' ) );
	}

	/**
	 * Without a search string every type with a published content is listed.
	 */
	public function test_an_empty_search_lists_every_type_with_a_published_content() {
		$this->create_post( NEWS_POST_TYPE, 'Notizia' );
		$this->create_post( PATENT_POST_TYPE, 'Brevetto' );
		$this->create_post( EVENT_POST_TYPE, 'Evento in bozza', 'draft' );

		$types = DLI_ContentsManager::get_contenttypes_with_search_results( '' );

		$this->assertContains( NEWS_POST_TYPE, $types );
		$this->assertContains( PATENT_POST_TYPE, $types );
		$this->assertNotContains( EVENT_POST_TYPE, $types, 'A type with only a draft is not listed.' );
		$this->assertSame( $types, DLI_ContentsManager::get_contenttypes_with_search_results( '   ' ), 'Spaces are no search string.' );
	}

	/**
	 * The types of the people (the categories, not the persons) are never a filter.
	 */
	public function test_the_people_types_are_never_listed() {
		$this->create_post( PEOPLE_TYPE_POST_TYPE, 'Tipologia Zebrafish' );

		$this->assertNotContains( PEOPLE_TYPE_POST_TYPE, DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' ) );
		$this->assertNotContains( PEOPLE_TYPE_POST_TYPE, DLI_ContentsManager::get_contenttypes_with_search_results( '' ) );
	}

	/**
	 * The matches are the ones of the current language.
	 */
	public function test_the_matches_are_the_ones_of_the_current_language() {
		$english = $this->create_post( NEWS_POST_TYPE, 'English Zebrafish news' );
		dli_set_post_language( $english, 'en' );

		$this->assertSame( array(), DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' ), 'Italian: the news is in English.' );

		PLL()->curlang = PLL()->model->get_language( 'en' );
		$this->assertSame( array( NEWS_POST_TYPE ), DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' ) );
	}

	/**
	 * The question is whether a type has a match, not which matches there are: no query loads every match.
	 */
	public function test_no_query_loads_all_the_matches() {
		for ( $index = 1; $index <= 5; $index++ ) {
			$this->create_post( NEWS_POST_TYPE, 'Notizia Zebrafish ' . $index );
		}
		add_action( 'pre_get_posts', array( $this, 'record_query' ) );

		try {
			$this->assertSame( array( NEWS_POST_TYPE ), DLI_ContentsManager::get_contenttypes_with_search_results( 'Zebrafish' ) );
		} finally {
			remove_action( 'pre_get_posts', array( $this, 'record_query' ) );
		}

		$this->assertNotEmpty( $this->queries );
		foreach ( $this->queries as $query ) {
			$this->assertSame( 1, (int) $query['posts_per_page'], 'A query asks for one result at most.' );
			$this->assertSame( 'ids', $query['fields'], 'It loads no full post.' );
			$this->assertTrue( (bool) $query['no_found_rows'], 'It does not count the total.' );
		}
	}
}
