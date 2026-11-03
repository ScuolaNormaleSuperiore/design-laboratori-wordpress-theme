<?php
/**
 * Unit tests of the reader of CSV files of the import tools: the text of the file is read, the file is not touched.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';

/**
 * `DLI_CSV_Reader`: BOM, UTF-8, separator, quoting, lines, empty and incomplete rows, headers and limits.
 */
class DLI_Csv_Reader_Unit_Test extends TestCase {

	/**
	 * The codes of the errors of a result.
	 *
	 * @param array<string, mixed> $result Result of the reader.
	 * @return string[] Codes, in order.
	 */
	private function codes( array $result ) {
		return array_column( $result['errors'], 'code' );
	}

	/**
	 * Header and rows are read as cells by the name of the column, with the line of the file of each row.
	 */
	public function test_rows_are_read_by_column_name_with_their_line() {
		$result = DLI_CSV_Reader::read_string( "level_1;level_2\nUno;Due\nTre;Quattro\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( ';', $result['delimiter'] );
		$this->assertSame( array( 'level_1', 'level_2' ), $result['headers'] );
		$this->assertSame(
			array(
				array( 'line' => 2, 'cells' => array( 'level_1' => 'Uno', 'level_2' => 'Due' ) ),
				array( 'line' => 3, 'cells' => array( 'level_1' => 'Tre', 'level_2' => 'Quattro' ) ),
			),
			$result['rows']
		);
	}

	/**
	 * A BOM at the start is not part of the first column name.
	 */
	public function test_the_bom_is_dropped() {
		$result = DLI_CSV_Reader::read_string( "\xEF\xBB\xBFlevel_1;level_2\nUno;Due\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'level_1', 'level_2' ), $result['headers'] );
	}

	/**
	 * The separator is the semicolon, or the comma when the header has more commas than semicolons.
	 */
	public function test_separator_is_read_from_the_header_line() {
		$comma = DLI_CSV_Reader::read_string( "a,b,c\n1,2,3\n" );
		$this->assertSame( ',', $comma['delimiter'] );
		$this->assertSame( array( 'a' => '1', 'b' => '2', 'c' => '3' ), $comma['rows'][0]['cells'] );

		$one_column = DLI_CSV_Reader::read_string( "titolo\nUno\n" );
		$this->assertSame( ';', $one_column['delimiter'], 'No separator in the header: the semicolon.' );

		$commas_in_data = DLI_CSV_Reader::read_string( "a;b\nUno, due;Tre\n" );
		$this->assertSame( ';', $commas_in_data['delimiter'], 'Commas in the data do not change the separator.' );
		$this->assertSame( 'Uno, due', $commas_in_data['rows'][0]['cells']['a'] );
	}

	/**
	 * Quoted cells keep the separator, the doubled quotes and the new lines, and the next row has the right line.
	 */
	public function test_quoted_cells_keep_separator_quotes_and_new_lines() {
		$content = "titolo;nota\n\"Dati; ricerca\";\"Dice \"\"ciao\"\"\"\n\"Due\nrighe\";x\nUltima;y\n";
		$result  = DLI_CSV_Reader::read_string( $content );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 'Dati; ricerca', $result['rows'][0]['cells']['titolo'] );
		$this->assertSame( 'Dice "ciao"', $result['rows'][0]['cells']['nota'] );
		$this->assertSame( "Due\nrighe", $result['rows'][1]['cells']['titolo'] );
		$this->assertSame( array( 2, 3, 5 ), array_column( $result['rows'], 'line' ), 'The row after a cell on two lines is on line 5.' );
	}

	/**
	 * Windows and old Mac line endings are read as the others.
	 */
	public function test_every_kind_of_line_ending_is_read() {
		foreach ( array( "\r\n", "\r", "\n" ) as $ending ) {
			$result = DLI_CSV_Reader::read_string( 'a;b' . $ending . '1;2' . $ending . '3;4' . $ending );

			$this->assertSame( array(), $result['errors'] );
			$this->assertCount( 2, $result['rows'], json_encode( $ending ) );
			$this->assertSame( array( 2, 3 ), array_column( $result['rows'], 'line' ) );
		}
	}

	/**
	 * The spaces around a cell are not part of it.
	 */
	public function test_cells_are_trimmed() {
		$result = DLI_CSV_Reader::read_string( "a;b\n  Uno  ;\tDue \n" );

		$this->assertSame( array( 'a' => 'Uno', 'b' => 'Due' ), $result['rows'][0]['cells'] );
	}

	/**
	 * A row with no value at all is left out, but the lines of the others stay those of the file.
	 */
	public function test_empty_rows_are_ignored_and_lines_are_kept() {
		$result = DLI_CSV_Reader::read_string( "a;b\n1;2\n\n;\n  ; \n3;4\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 2, 6 ), array_column( $result['rows'], 'line' ) );
	}

	/**
	 * A row with more or fewer cells than the header is an error of that row, and the others are read.
	 */
	public function test_a_row_with_a_wrong_number_of_cells_is_an_error_of_its_line() {
		$result = DLI_CSV_Reader::read_string( "a;b;c\n1;2;3\n4;5\n6;7;8;9\n10;11;12\n" );

		$this->assertSame( array( 'columns_count', 'columns_count' ), $this->codes( $result ) );
		$this->assertSame( array( 3, 4 ), array_column( $result['errors'], 'line' ) );
		$this->assertSame( array( 2, 5 ), array_column( $result['rows'], 'line' ), 'The good rows are still read.' );
	}

	/**
	 * Missing, repeated and unknown columns are errors of the header; the names are case sensitive.
	 */
	public function test_header_errors() {
		$options = array(
			'required' => array( 'level_1', 'level_2' ),
			'optional' => array( 'page_key' ),
		);

		$missing = DLI_CSV_Reader::read_string( "level_1\nUno\n", $options );
		$this->assertSame( array( 'missing_header' ), $this->codes( $missing ) );
		$this->assertSame( 'level_2', $missing['errors'][0]['detail'] );
		$this->assertSame( array(), $missing['rows'], 'A file with a wrong header gives no rows.' );

		$twice = DLI_CSV_Reader::read_string( "level_1;level_2;level_1\n1;2;3\n", $options );
		$this->assertContains( 'duplicate_header', $this->codes( $twice ) );

		$unknown = DLI_CSV_Reader::read_string( "level_1;level_2;colore\n1;2;3\n", $options );
		$this->assertSame( array( 'unknown_header' ), $this->codes( $unknown ) );
		$this->assertSame( 'colore', $unknown['errors'][0]['detail'] );

		$case = DLI_CSV_Reader::read_string( "Level_1;level_2\n1;2\n", $options );
		$this->assertEqualsCanonicalizing( array( 'missing_header', 'unknown_header' ), $this->codes( $case ), 'Level_1 is not level_1.' );

		$optional = DLI_CSV_Reader::read_string( "level_1;level_2;page_key\n1;2;k\n", $options );
		$this->assertSame( array(), $optional['errors'], 'An optional column is accepted.' );

		$free = DLI_CSV_Reader::read_string( "x;y\n1;2\n" );
		$this->assertSame( array(), $free['errors'], 'Without a list of columns any header is accepted.' );
	}

	/**
	 * A text that is not UTF-8, with a NUL byte, with a quote that is never closed, or without content is refused whole.
	 */
	public function test_a_file_that_cannot_be_read_is_refused_whole() {
		$this->assertSame( array( 'invalid_utf8' ), $this->codes( DLI_CSV_Reader::read_string( "a;b\n\xE8;2\n" ) ) );
		$this->assertSame( array( 'nul_byte' ), $this->codes( DLI_CSV_Reader::read_string( "a;b\n1;2\0\n" ) ) );
		$this->assertSame( array( 'empty' ), $this->codes( DLI_CSV_Reader::read_string( '' ) ) );
		$this->assertSame( array( 'empty' ), $this->codes( DLI_CSV_Reader::read_string( "\xEF\xBB\xBF\n\n" ) ) );

		$open = DLI_CSV_Reader::read_string( "a;b\n1;\"due\n3;4\n" );
		$this->assertSame( array( 'malformed' ), $this->codes( $open ) );
		$this->assertSame( 2, $open['errors'][0]['line'], 'The error is on the line where the quote was opened.' );
		$this->assertSame( array(), $open['rows'] );
	}

	/**
	 * A file with a header and no rows is valid and has no rows.
	 */
	public function test_a_header_alone_is_a_file_without_rows() {
		$result = DLI_CSV_Reader::read_string( "a;b\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'a', 'b' ), $result['headers'] );
		$this->assertSame( array(), $result['rows'] );
	}

	/**
	 * The size of the file and the number of the rows have a limit.
	 */
	public function test_limits_of_size_and_rows() {
		$big = DLI_CSV_Reader::read_string( "a;b\n" . str_repeat( 'x', 100 ) . ";1\n", array( 'max_bytes' => 50 ) );
		$this->assertSame( array( 'too_large' ), $this->codes( $big ) );
		$this->assertSame( array(), $big['rows'] );

		$many = DLI_CSV_Reader::read_string( "a;b\n1;1\n2;2\n3;3\n", array( 'max_rows' => 2 ) );
		$this->assertSame( array( 'too_many_rows' ), $this->codes( $many ) );
		$this->assertSame( array(), $many['rows'] );

		$enough = DLI_CSV_Reader::read_string( "a;b\n1;1\n\n2;2\n", array( 'max_rows' => 2 ) );
		$this->assertSame( array(), $enough['errors'], 'Empty rows are not counted.' );
		$this->assertCount( 2, $enough['rows'] );
	}

	/**
	 * A file on the disk is read as its text; a file that is not there is an error.
	 */
	public function test_a_file_is_read_from_its_path() {
		$path = tempnam( sys_get_temp_dir(), 'dli-csv-' );
		file_put_contents( $path, "a;b\n1;2\n" );

		try {
			$result = DLI_CSV_Reader::read( $path );
			$this->assertSame( array(), $result['errors'] );
			$this->assertSame( array( 'a' => '1', 'b' => '2' ), $result['rows'][0]['cells'] );
		} finally {
			unlink( $path );
		}

		$this->assertSame( array( 'unreadable' ), $this->codes( DLI_CSV_Reader::read( $path ) ), 'The file is gone.' );
	}
}
