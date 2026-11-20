<?php
/**
 * Unit tests of the text search of the People page, in inc/people-filters.php.
 *
 * The cases are in `tests/unit/fixtures/people-search-cases.json`, which `people-search.test.js` reads too: the PHP
 * rule of the cards view and the JavaScript rule of the table give the same answer to every case.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/people-filters.php';

/**
 * `dli_normalize_people_search()`, `dli_people_row_matches_search()`, `dli_people_search_from_request()` and
 * `dli_people_filter_query_args()`.
 */
class DLI_People_Search_Unit_Test extends TestCase {

	/**
	 * The cases of the shared file, by their description.
	 *
	 * @return array<string, array{0: string, 1: array<string, mixed>, 2: bool}>
	 */
	public static function shared_cases() {
		$data  = json_decode( (string) file_get_contents( __DIR__ . '/fixtures/people-search-cases.json' ), true );
		$cases = array();
		foreach ( $data['cases'] as $case ) {
			$cases[ $case['description'] ] = array( $case['search'], $data['rows'][ $case['row'] ], $case['matches'] );
		}
		return $cases;
	}

	/**
	 * Each case of the shared file has the expected answer.
	 *
	 * @dataProvider shared_cases
	 *
	 * @param string               $search  The text of the search.
	 * @param array<string, mixed> $row     The row (name, surname, type, structures).
	 * @param bool                 $matches Whether the row matches.
	 */
	public function test_each_shared_case( $search, array $row, $matches ) {
		$this->assertSame( $matches, dli_people_row_matches_search( $search, $row ) );
	}

	/**
	 * The text is normalized: spaces around removed, runs of spaces (also no-break) made one, lower case, no accents.
	 */
	public function test_the_text_is_normalized() {
		$this->assertSame( '', dli_normalize_people_search( "  \t " ) );
		$this->assertSame( 'mario rossi', dli_normalize_people_search( "  Mario \xC2\xA0  ROSSI " ) );
		$this->assertSame( 'niccolo', dli_normalize_people_search( 'NICCOLÒ' ), 'Accents do not count.' );
	}

	/**
	 * The search of the address is `cerca`; an old link with `cognome` still works when `cerca` is not there.
	 */
	public function test_the_search_of_the_address_is_cerca_or_the_old_cognome() {
		$this->assertSame( 'rossi', dli_people_search_from_request( 'rossi', '' ) );
		$this->assertSame( 'rossi', dli_people_search_from_request( '', 'rossi' ), 'An old link with cognome.' );
		$this->assertSame( 'mario', dli_people_search_from_request( 'mario', 'rossi' ), 'cerca wins over cognome.' );
		$this->assertSame( '', dli_people_search_from_request( '', '' ) );
		$this->assertSame( 'rossi', dli_people_search_from_request( '  rossi ', '' ), 'Spaces around are removed.' );
		$this->assertSame( 'rossi', dli_people_search_from_request( '   ', 'rossi' ), 'A cerca of spaces only is not there.' );
	}

	/**
	 * The arguments of the links between the views keep the active filters, with the search as `cerca`.
	 */
	public function test_the_links_between_the_views_keep_the_active_filters() {
		$this->assertSame(
			array(
				'struttura' => 'fisica',
				'level'     => 'senior',
				'tipologia' => '12',
				'cerca'     => 'rossi',
			),
			dli_people_filter_query_args( 'fisica', 'senior', '12', 'rossi' )
		);
		$this->assertSame( array( 'cerca' => 'rossi' ), dli_people_filter_query_args( '', '', '', 'rossi' ), 'Only the active filters.' );
		$this->assertSame( array(), dli_people_filter_query_args( '', '', '', '' ) );
	}

	/**
	 * A row without some of its fields is read as if they were empty.
	 */
	public function test_a_row_with_missing_fields_is_read_as_empty() {
		$this->assertTrue( dli_people_row_matches_search( 'rossi', array( 'surname' => 'Rossi' ) ) );
		$this->assertFalse( dli_people_row_matches_search( 'rossi', array() ) );
		$this->assertTrue( dli_people_row_matches_search( '', array() ) );
	}
}
