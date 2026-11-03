<?php
/**
 * Contract test: the CSV files of the example pages say what the tree of the old example tool says.
 *
 * The files in `SETUP/CSV_Import/` replace the tree written in PHP; until the old tool is removed this test compares
 * the two, page by page. It goes away with the old tool, and the files stay.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';
require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-page-file-importer.php';
require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-example-content.php';

/**
 * `SETUP/CSV_Import/pages-example-it.csv`, `pages-example-en.csv` and `pages-model.csv`.
 */
class DLI_Example_Pages_Csv_Unit_Test extends TestCase {

	const REQUIRED = array( 'level_1', 'level_2', 'level_3', 'menu_order', 'promote_in_home', 'show_subpages_as_cards' );
	const OPTIONAL = array( 'page_key', 'excerpt', 'slug' );

	/**
	 * Read one of the files with the reader of the import, as the import will.
	 *
	 * @param string $name File name in `SETUP/CSV_Import/`.
	 * @return array<string, mixed> The result of the reader.
	 */
	private function read( $name ) {
		return DLI_CSV_Reader::read(
			DLI_TEST_THEME_DIR . '/SETUP/CSV_Import/' . $name,
			array(
				'required' => self::REQUIRED,
				'optional' => self::OPTIONAL,
			)
		);
	}

	/**
	 * A private static method of the old tool.
	 *
	 * @param string $method Method name.
	 * @return mixed What it gives back.
	 */
	private function example_content( $method ) {
		$reflection = new ReflectionMethod( 'DLI_Example_Content', $method );
		$reflection->setAccessible( true );
		return $reflection->invoke( null );
	}

	/**
	 * The pages that the tree has, one by one, as the file has to say them.
	 *
	 * @param array<string, mixed> $node     Node of the tree.
	 * @param int                  $level    Level, from 1.
	 * @param int                  $position Position among the siblings, from 1.
	 * @param string               $language Language of the file.
	 * @return array<int, array<string, string>> The pages, parents before children.
	 */
	private function expected( array $node, $level, $position, $language ) {
		$titles = array(
			'level_1' => '',
			'level_2' => '',
			'level_3' => '',
		);
		$titles[ 'level_' . $level ] = $node[ $language ][0];

		$pages = array(
			array_merge(
				$titles,
				array(
					'menu_order'             => (string) $position,
					'promote_in_home'        => empty( $node['promote'] ) ? '0' : '1',
					'show_subpages_as_cards' => empty( $node['cards'] ) ? '0' : '1',
					'excerpt'                => $node[ $language ][1],
					'slug'                   => $node['slug'][ $language ] ?? '',
				)
			),
		);
		foreach ( $node['children'] ?? array() as $index => $child ) {
			$pages = array_merge( $pages, $this->expected( $child, $level + 1, $index + 1, $language ) );
		}

		return $pages;
	}

	/**
	 * Each file has the same pages as the tree: title, level, order, flags, summary and address.
	 */
	public function test_the_files_say_the_same_as_the_tree() {
		$tree = $this->example_content( 'tree' );

		foreach ( array( 'it', 'en' ) as $language ) {
			$expected = array();
			foreach ( $tree as $position => $node ) {
				$expected = array_merge( $expected, $this->expected( $node, 1, $position + 1, $language ) );
			}

			$result = $this->read( "pages-example-$language.csv" );
			$this->assertSame( array(), $result['errors'], "The file $language is read without errors." );
			$this->assertSame( array_merge( self::REQUIRED, self::OPTIONAL ), $result['headers'] );

			$found = array();
			foreach ( $result['rows'] as $row ) {
				$found[] = array_diff_key( $row['cells'], array( 'page_key' => true ) );
			}
			$this->assertSame( $expected, $found, "The file $language has the pages of the tree." );
		}
	}

	/**
	 * The size of the example: 15 pages for each language, 4 main pages, 7 promoted to the home, one with the cards.
	 */
	public function test_the_files_have_the_size_of_the_example() {
		foreach ( array( 'it', 'en' ) as $language ) {
			$rows = $this->read( "pages-example-$language.csv" )['rows'];

			$this->assertCount( 15, $rows, $language );
			$this->assertSame( 4, count( array_filter( array_column( array_column( $rows, 'cells' ), 'level_1' ) ) ), "$language: four main pages." );
			$this->assertSame( 7, count( array_filter( array_column( array_column( $rows, 'cells' ), 'promote_in_home' ) ) ), "$language: seven pages promoted to the home." );
			$this->assertSame( 1, count( array_filter( array_column( array_column( $rows, 'cells' ), 'show_subpages_as_cards' ) ) ), "$language: one page with the cards." );
		}
	}

	/**
	 * The keys tie the two languages: the same key in the same position, valid and not repeated.
	 */
	public function test_the_keys_tie_the_two_files() {
		$italian = array_column( array_column( $this->read( 'pages-example-it.csv' )['rows'], 'cells' ), 'page_key' );
		$english = array_column( array_column( $this->read( 'pages-example-en.csv' )['rows'], 'cells' ), 'page_key' );

		$this->assertSame( $italian, $english, 'Each page has the same key in the two files.' );
		$this->assertSame( $english, array_values( array_unique( $english ) ), 'No key is repeated.' );
		foreach ( $english as $key ) {
			$this->assertSame( 1, preg_match( '/^[a-z0-9][a-z0-9_-]{0,63}$/', $key ), "The key $key has the format of a key." );
		}
	}

	/**
	 * The model is the header with the six required columns and no page.
	 */
	public function test_the_model_is_the_required_header_alone() {
		$result = $this->read( 'pages-model.csv' );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( self::REQUIRED, $result['headers'] );
		$this->assertSame( array(), $result['rows'] );
	}

	/**
	 * The lorem ipsum of the new import is the one of the old example tool, paragraph by paragraph.
	 */
	public function test_the_lorem_pool_is_the_one_of_the_old_tool() {
		$this->assertSame( $this->example_content( 'lorem' ), DLI_Page_File_Importer::lorem_pool() );
	}
}
