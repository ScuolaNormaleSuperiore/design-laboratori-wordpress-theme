<?php
/**
 * Unit tests of the lorem ipsum of the new pages of the import: the same key gives the same text.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-page-file-importer.php';

/**
 * `DLI_Page_File_Importer::lorem_paragraphs()` and `::lorem_html()`.
 */
class DLI_Page_Import_Lorem_Unit_Test extends TestCase {

	/**
	 * The pool has six paragraphs of different length.
	 */
	public function test_the_pool_has_paragraphs_of_different_length() {
		$pool = DLI_Page_File_Importer::lorem_pool();

		$this->assertCount( 6, $pool );
		$this->assertGreaterThan( 3, count( array_unique( array_map( 'strlen', $pool ) ) ), 'The paragraphs are not all of the same length.' );
		foreach ( $pool as $paragraph ) {
			$this->assertNotSame( '', trim( $paragraph ) );
		}
	}

	/**
	 * The same key gives always the same paragraphs, so the two languages of a page have the same text.
	 */
	public function test_the_same_key_gives_the_same_text() {
		$this->assertSame( DLI_Page_File_Importer::lorem_paragraphs( 'plan' ), DLI_Page_File_Importer::lorem_paragraphs( 'plan' ) );
		$this->assertSame( DLI_Page_File_Importer::lorem_html( 'plan' ), DLI_Page_File_Importer::lorem_html( 'plan' ) );
	}

	/**
	 * A page has from one to three paragraphs of the pool, without repeating one.
	 */
	public function test_a_page_has_one_to_three_paragraphs_of_the_pool() {
		$pool   = DLI_Page_File_Importer::lorem_pool();
		$counts = array();

		for ( $number = 0; $number < 120; $number++ ) {
			$paragraphs = DLI_Page_File_Importer::lorem_paragraphs( 'chiave-' . $number );

			$this->assertGreaterThanOrEqual( 1, count( $paragraphs ) );
			$this->assertLessThanOrEqual( 3, count( $paragraphs ) );
			$this->assertSame( $paragraphs, array_values( array_unique( $paragraphs ) ), 'No paragraph is repeated.' );
			foreach ( $paragraphs as $paragraph ) {
				$this->assertContains( $paragraph, $pool, 'Every paragraph is one of the pool.' );
			}
			$counts[ count( $paragraphs ) ] = true;
		}

		$this->assertSame( array( 1, 2, 3 ), array_keys( array_replace( array_flip( array( 1, 2, 3 ) ), $counts ) ) );
		$this->assertCount( 3, $counts, 'All the three lengths are used.' );
	}

	/**
	 * The keys do not all give the same text.
	 */
	public function test_different_keys_give_different_texts() {
		$texts = array();
		for ( $number = 0; $number < 40; $number++ ) {
			$texts[ DLI_Page_File_Importer::lorem_html( 'chiave-' . $number ) ] = true;
		}

		$this->assertGreaterThan( 5, count( $texts ) );
	}

	/**
	 * The text is the content of a page: one `<p>` for each paragraph, nothing else.
	 */
	public function test_the_html_is_one_paragraph_tag_for_each_paragraph() {
		$paragraphs = DLI_Page_File_Importer::lorem_paragraphs( 'open-access' );
		$html       = DLI_Page_File_Importer::lorem_html( 'open-access' );

		$this->assertSame( count( $paragraphs ), substr_count( $html, '<p>' ) );
		$this->assertSame( count( $paragraphs ), substr_count( $html, '</p>' ) );
		$this->assertSame( '<p>' . implode( '</p><p>', $paragraphs ) . '</p>', $html );
	}
}
