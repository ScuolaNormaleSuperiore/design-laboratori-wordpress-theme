<?php
/**
 * Unit tests of the parser of the pages import: the rows of the file become a tree of pages, or the list of what is wrong.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';
require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-page-file-importer.php';

/**
 * `DLI_Page_File_Importer::parse_rows()`: levels and parents, titles, order, flags, keys, slugs and repeated paths.
 */
class DLI_Page_Import_Parser_Unit_Test extends TestCase {

	/**
	 * Rows as the reader of the file gives them: each with the line of the file.
	 *
	 * @param array<int, array<int, string>|array<string, string>> $lines Each row is `array( level_1, level_2, level_3 )` plus optional values by name.
	 * @return array<int, array{line: int, cells: array<string, string>}> The rows.
	 */
	private function rows( array $lines ) {
		$rows = array();
		foreach ( $lines as $index => $line ) {
			$titles  = array_slice( $line, 0, 3 );
			$options = array_diff_key( $line, array( 0 => 1, 1 => 1, 2 => 1 ) );
			$rows[]  = array(
				'line'  => $index + 2,
				'cells' => array_merge(
					array(
						'level_1'                => (string) ( $titles[0] ?? '' ),
						'level_2'                => (string) ( $titles[1] ?? '' ),
						'level_3'                => (string) ( $titles[2] ?? '' ),
						'menu_order'             => '1',
						'promote_in_home'        => '0',
						'show_subpages_as_cards' => '0',
					),
					$options
				),
			);
		}
		return $rows;
	}

	/**
	 * The codes of the errors.
	 *
	 * @param array<string, mixed> $result Result of the parser.
	 * @return string[] Codes, in order.
	 */
	private function codes( array $result ) {
		return array_column( $result['errors'], 'code' );
	}

	/**
	 * A file has at most 200 pages: an import of more takes too long for a request, and the reader refuses the 201st.
	 */
	public function test_a_file_has_at_most_two_hundred_pages() {
		$this->assertSame( 200, DLI_Page_File_Importer::MAX_PAGES );

		$header = "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\n";
		$page   = static function ( $number ) {
			return 'Pagina ' . $number . ";;;1;0;0\n";
		};
		$rows   = '';
		for ( $number = 1; $number <= 200; $number++ ) {
			$rows .= $page( $number );
		}
		$options = array( 'max_rows' => DLI_Page_File_Importer::MAX_PAGES );

		$enough = DLI_CSV_Reader::read_string( $header . $rows, $options );
		$this->assertSame( array(), $enough['errors'] );
		$this->assertCount( 200, $enough['rows'] );

		$too_many = DLI_CSV_Reader::read_string( $header . $rows . $page( 201 ), $options );
		$this->assertSame( array( 'too_many_rows' ), $this->codes( $too_many ) );
	}

	/**
	 * The example of the file format: one main page with two groups, the first with two pages.
	 */
	public function test_the_example_of_the_format_is_a_tree() {
		$result = DLI_Page_File_Importer::parse_rows(
			$this->rows(
				array(
					array( 'Research Data Management', '', '', 'menu_order' => '1', 'promote_in_home' => '1', 'show_subpages_as_cards' => '1' ),
					array( '', 'Plan', '', 'menu_order' => '1', 'promote_in_home' => '1' ),
					array( '', '', 'What are research data?', 'menu_order' => '1' ),
					array( '', '', 'Data Management Plan', 'menu_order' => '2' ),
					array( '', 'Manage', '', 'menu_order' => '2', 'promote_in_home' => '1' ),
				)
			)
		);

		$this->assertSame( array(), $result['errors'] );
		$pages = $result['pages'];
		$this->assertSame( array( 1, 2, 3, 3, 2 ), array_column( $pages, 'level' ) );
		$this->assertSame( array( null, 0, 1, 1, 0 ), array_column( $pages, 'parent' ) );
		$this->assertSame( array( 'Research Data Management', 'Plan', 'What are research data?', 'Data Management Plan', 'Manage' ), array_column( $pages, 'title' ) );
		$this->assertSame( array( 1, 1, 1, 2, 2 ), array_column( $pages, 'order' ) );
		$this->assertSame( array( true, true, false, false, true ), array_column( $pages, 'promote' ) );
		$this->assertSame( array( true, false, false, false, false ), array_column( $pages, 'cards' ) );
		$this->assertSame( array( 2, 3, 4, 5, 6 ), array_column( $pages, 'line' ) );
	}

	/**
	 * A new main page closes the branch: the subpages that follow belong to it, and a third level needs its own second.
	 */
	public function test_a_new_branch_resets_the_levels_below() {
		$result = DLI_Page_File_Importer::parse_rows(
			$this->rows(
				array(
					array( 'Uno' ),
					array( '', 'Uno A' ),
					array( '', '', 'Uno A 1' ),
					array( 'Due' ),
					array( '', 'Due A' ),
					array( '', 'Due B' ),
					array( '', '', 'Due B 1' ),
				)
			)
		);

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( null, 0, 1, null, 3, 3, 5 ), array_column( $result['pages'], 'parent' ) );
	}

	/**
	 * A level that skips the one above is an error: a second level without a main page, a third without a second, also
	 * after a new branch has begun.
	 */
	public function test_a_jump_of_level_is_an_error() {
		$first = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( '', 'Senza radice' ) ) ) );
		$this->assertSame( array( 'level_jump' ), $this->codes( $first ) );
		$this->assertSame( 2, $first['errors'][0]['line'] );

		$second = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno' ), array( '', '', 'Salta il secondo' ) ) ) );
		$this->assertSame( array( 'level_jump' ), $this->codes( $second ) );

		$third = DLI_Page_File_Importer::parse_rows(
			$this->rows(
				array(
					array( 'Uno' ),
					array( '', 'Uno A' ),
					array( 'Due' ),
					array( '', '', 'Il secondo di Uno non vale per Due' ),
				)
			)
		);
		$this->assertSame( array( 'level_jump' ), $this->codes( $third ) );
		$this->assertSame( 5, $third['errors'][0]['line'] );
	}

	/**
	 * A row has one title, in one of the three columns.
	 */
	public function test_a_row_has_exactly_one_title() {
		$none = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( '', '', '', 'menu_order' => '3' ) ) ) );
		$this->assertSame( array( 'no_title' ), $this->codes( $none ) );

		$two = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'Due' ) ) ) );
		$this->assertSame( array( 'several_titles' ), $this->codes( $two ) );
		$this->assertSame( 2, $two['errors'][0]['line'] );
	}

	/**
	 * Titles and summaries are plain text.
	 */
	public function test_titles_and_summaries_are_plain_text() {
		$title = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno <b>grassetto</b>' ) ) ) );
		$this->assertSame( array( 'plain_text' ), $this->codes( $title ) );
		$this->assertSame( 'title', $title['errors'][0]['detail'] );

		$excerpt = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'excerpt' => 'Un <script>' ) ) ) );
		$this->assertSame( array( 'plain_text' ), $this->codes( $excerpt ) );
		$this->assertSame( 'excerpt', $excerpt['errors'][0]['detail'] );
	}

	/**
	 * The order is a whole number that is not negative.
	 */
	public function test_the_order_is_a_whole_number_not_negative() {
		foreach ( array( '', '-1', 'a', '1.5', ' ' ) as $wrong ) {
			$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'menu_order' => $wrong ) ) ) );
			$this->assertSame( array( 'invalid_order' ), $this->codes( $result ), json_encode( $wrong ) );
		}
		foreach ( array( '0', '7', '100' ) as $good ) {
			$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'menu_order' => $good ) ) ) );
			$this->assertSame( array(), $result['errors'], $good );
			$this->assertSame( (int) $good, $result['pages'][0]['order'] );
		}
	}

	/**
	 * The two flags are 0 or 1 and nothing else, and the error says which flag.
	 */
	public function test_the_flags_are_zero_or_one() {
		foreach ( array( '2', '', 'si', 'true' ) as $wrong ) {
			$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'promote_in_home' => $wrong ) ) ) );
			$this->assertSame( array( 'invalid_flag' ), $this->codes( $result ), json_encode( $wrong ) );
			$this->assertSame( 'promote_in_home', $result['errors'][0]['detail'] );
		}

		$cards = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'show_subpages_as_cards' => 'x' ) ) ) );
		$this->assertSame( 'show_subpages_as_cards', $cards['errors'][0]['detail'] );
	}

	/**
	 * The key has a format and is not repeated; it is optional.
	 */
	public function test_keys_have_a_format_and_are_not_repeated() {
		foreach ( array( 'Bad Key', '-inizia-col-trattino', 'è', str_repeat( 'a', 65 ), 'MAIUSCOLA' ) as $wrong ) {
			$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'page_key' => $wrong ) ) ) );
			$this->assertSame( array( 'invalid_key' ), $this->codes( $result ), $wrong );
		}

		$good = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'page_key' => 'a1_b-2' ), array( 'Due', 'page_key' => str_repeat( 'a', 64 ) ), array( 'Tre' ) ) ) );
		$this->assertSame( array(), $good['errors'] );
		$this->assertSame( array( 'a1_b-2', str_repeat( 'a', 64 ), '' ), array_column( $good['pages'], 'key' ) );

		$twice = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'page_key' => 'k' ), array( 'Due', 'page_key' => 'k' ) ) ) );
		$this->assertSame( array( 'duplicate_key' ), $this->codes( $twice ) );
		$this->assertSame( 3, $twice['errors'][0]['line'] );
		$this->assertSame( 'k', $twice['errors'][0]['detail'] );
	}

	/**
	 * The slug that a row asks for is a slug already: lower case, digits and hyphens.
	 */
	public function test_a_slug_is_already_a_slug() {
		foreach ( array( 'Open Access', 'open_access', 'open--access', '-open', 'perché' ) as $wrong ) {
			$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'slug' => $wrong ) ) ) );
			$this->assertSame( array( 'invalid_slug' ), $this->codes( $result ), $wrong );
		}

		$good = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno', 'slug' => 'open-access-en' ), array( 'Due' ) ) ) );
		$this->assertSame( array(), $good['errors'] );
		$this->assertSame( array( 'open-access-en', '' ), array_column( $good['pages'], 'slug' ) );
	}

	/**
	 * Two pages with the same title under the same parent are the same path; under different parents they are not.
	 */
	public function test_the_same_title_under_the_same_parent_is_a_repeated_path() {
		$same = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno' ), array( '', 'Figlia' ), array( '', 'figlia ' ) ) ) );
		$this->assertSame( array( 'duplicate_path' ), $this->codes( $same ), 'The case and the spaces around do not make a difference.' );
		$this->assertSame( 4, $same['errors'][0]['line'] );

		$roots = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno' ), array( 'Uno' ) ) ) );
		$this->assertSame( array( 'duplicate_path' ), $this->codes( $roots ) );

		$different = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno' ), array( '', 'Figlia' ), array( 'Due' ), array( '', 'Figlia' ) ) ) );
		$this->assertSame( array(), $different['errors'], 'The same title under two parents is allowed.' );
	}

	/**
	 * The identity of a page is its key, or the path of its titles when it has no key.
	 */
	public function test_the_identity_is_the_key_or_the_path_of_the_titles() {
		$result = DLI_Page_File_Importer::parse_rows(
			$this->rows(
				array(
					array( 'Research Data', 'page_key' => 'rd' ),
					array( '', 'Plan' ),
					array( '', '', 'What are  Research Data?' ),
				)
			)
		);

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 'rd', $result['pages'][0]['identity'] );
		$this->assertSame( 'research data / plan', $result['pages'][1]['identity'], 'Without a key, the path of the titles, in lower case.' );
		$this->assertSame( 'research data / plan / what are research data?', $result['pages'][2]['identity'], 'The spaces inside a title are one.' );
	}

	/**
	 * Every error is reported with its line, not just the first: the operator corrects the file once.
	 */
	public function test_all_the_errors_are_reported_with_their_lines() {
		$result = DLI_Page_File_Importer::parse_rows(
			$this->rows(
				array(
					array( 'Uno', 'menu_order' => 'x' ),
					array( '', 'Due', '', 'promote_in_home' => '5' ),
					array( '', '', '', 'menu_order' => '1' ),
				)
			)
		);

		$this->assertSame( array( 'invalid_order', 'invalid_flag', 'no_title' ), $this->codes( $result ) );
		$this->assertSame( array( 2, 3, 4 ), array_column( $result['errors'], 'line' ) );
	}

	/**
	 * Optional columns that the file does not have are the same as empty cells.
	 */
	public function test_missing_optional_columns_are_empty() {
		$result = DLI_Page_File_Importer::parse_rows( $this->rows( array( array( 'Uno' ) ) ) );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( '', $result['pages'][0]['key'] );
		$this->assertSame( '', $result['pages'][0]['excerpt'] );
		$this->assertSame( '', $result['pages'][0]['slug'] );
	}
}
