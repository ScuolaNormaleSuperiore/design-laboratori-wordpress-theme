<?php
/**
 * Contract test: the CSV files of the example pages and the model have the shape the import expects.
 *
 * The files in `SETUP/CSV_Import/` are the only source of the example site (downloadable from Importa → Pagine).
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';
require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-page-file-importer.php';

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
}
