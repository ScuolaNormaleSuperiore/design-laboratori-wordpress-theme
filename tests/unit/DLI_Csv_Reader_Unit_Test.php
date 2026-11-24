<?php
/**
 * Unit tests of the reader of CSV files of the import tools: the text of the file is read, the file is not touched.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';

/**
 * `DLI_CSV_Reader`: BOM, UTF-8, separator, quoting, lines, empty and incomplete rows, columns and limits.
 */
class DLI_Csv_Reader_Unit_Test extends TestCase {

	/**
	 * A schema of columns, as the import of the pages passes it.
	 */
	const SCHEMA = array(
		'required' => array( 'level_1', 'level_2' ),
		'optional' => array( 'page_key' ),
	);

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
	 * A CSV text with a header and as many rows as asked, each with a value in every column.
	 *
	 * @param int $count Number of the rows.
	 * @return string The text.
	 */
	private function rows_of( $count ) {
		$content = "level_1;level_2\n";
		for ( $number = 1; $number <= $count; $number++ ) {
			$content .= "Uno $number;Due\n";
		}
		return $content;
	}

	/**
	 * Header and rows are read as cells by the name of the column, with the line of the file of each row.
	 */
	public function test_rows_are_read_by_column_name_with_their_line() {
		$result = DLI_CSV_Reader::read_string( "level_1;level_2\nUno;Due\nTre;Quattro\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'level_1', 'level_2' ), $result['headers'] );
		$this->assertSame( array(), $result['ignored'] );
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
	 * The separator is always the semicolon: a comma is a character of the cell, also in the header.
	 */
	public function test_the_separator_is_always_the_semicolon() {
		$commas = DLI_CSV_Reader::read_string( "a,b,c\n1,2,3\n" );
		$this->assertSame( array(), $commas['errors'] );
		$this->assertSame( array( 'a,b,c' ), $commas['headers'], 'A header with commas is one column.' );
		$this->assertSame( array( 'a,b,c' => '1,2,3' ), $commas['rows'][0]['cells'] );

		$commas_in_data = DLI_CSV_Reader::read_string( "a;b\nUno, due;Tre\n" );
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
		$result = DLI_CSV_Reader::read_string( "a;b\n1;2\n\n;\n  ; \n;;;;\n3;4\n" );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 2, 7 ), array_column( $result['rows'], 'line' ) );
	}

	/**
	 * A row too short to reach the last column is an error of its line, also after a cell on two lines; the cells
	 * after the last column of the header are left out, also when they have a value.
	 */
	public function test_short_rows_are_errors_and_cells_after_the_header_are_left_out() {
		$result = DLI_CSV_Reader::read_string( "a;b;c\n\"Uno\ndue\";2;3\n4;5\n6;7;8;9;10\n11;12;13\n" );

		$this->assertSame( array( 'columns_count' ), $this->codes( $result ) );
		$this->assertSame( 4, $result['errors'][0]['line'], 'The short row is on line 4, after the cell on two lines.' );
		$this->assertSame( array( 2, 5, 6 ), array_column( $result['rows'], 'line' ), 'The other rows are read.' );
		$this->assertSame( array( 'a' => '6', 'b' => '7', 'c' => '8' ), $result['rows'][1]['cells'], 'The cells 9 and 10 are left out.' );
	}

	/**
	 * The names of the columns are compared without case and spaces around; the cells have the names of the schema.
	 */
	public function test_the_names_of_the_columns_do_not_depend_on_the_case() {
		$result = DLI_CSV_Reader::read_string( " Level_1 ;LEVEL_2;Page_Key\nUno;Due;Ricerca\n", self::SCHEMA );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'level_1', 'level_2', 'page_key' ), $result['headers'] );
		$this->assertSame( array( 'level_1' => 'Uno', 'level_2' => 'Due', 'page_key' => 'Ricerca' ), $result['rows'][0]['cells'], 'The values keep their case.' );
	}

	/**
	 * With a schema, the columns that are not in it are left out, also with values, and are listed once, as written.
	 */
	public function test_columns_not_in_the_schema_are_ignored_and_listed() {
		$result = DLI_CSV_Reader::read_string( "level_1;Colore;level_2;;colore;Nota\nUno;rosso;Due;x;blu;y\n", self::SCHEMA );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'level_1', 'level_2' ), $result['headers'] );
		$this->assertSame( array( 'Colore', 'Nota' ), $result['ignored'], 'Each ignored column once, as written; an empty name is not listed.' );
		$this->assertSame( array( 'level_1' => 'Uno', 'level_2' => 'Due' ), $result['rows'][0]['cells'] );
	}

	/**
	 * A column of the schema that is there twice, also with a different case, refuses the file; a missing required
	 * column too, with its name; an optional column may be missing.
	 */
	public function test_errors_of_the_columns_of_the_schema() {
		$missing = DLI_CSV_Reader::read_string( "level_1;page_key\nUno;k\n", self::SCHEMA );
		$this->assertSame( array( 'missing_header' ), $this->codes( $missing ) );
		$this->assertSame( 'level_2', $missing['errors'][0]['detail'] );
		$this->assertSame( array(), $missing['rows'], 'A file with a wrong header gives no rows.' );

		$twice = DLI_CSV_Reader::read_string( "level_1;level_2;Level_1\n1;2;3\n", self::SCHEMA );
		$this->assertSame( array( 'duplicate_header' ), $this->codes( $twice ) );
		$this->assertSame( 'level_1', $twice['errors'][0]['detail'] );
		$this->assertSame( array(), $twice['rows'] );

		$without_optional = DLI_CSV_Reader::read_string( "level_1;level_2\n1;2\n", self::SCHEMA );
		$this->assertSame( array(), $without_optional['errors'] );
	}

	/**
	 * A row that misses only the cells of ignored columns is read; one that misses a column of the schema is an error.
	 */
	public function test_a_short_row_is_an_error_only_when_it_misses_a_column_of_the_schema() {
		$result = DLI_CSV_Reader::read_string( "level_1;level_2;nota;colore\nUno;Due\nTre\n", self::SCHEMA );

		$this->assertSame( array( 'columns_count' ), $this->codes( $result ) );
		$this->assertSame( 3, $result['errors'][0]['line'] );
		$this->assertSame( array( array( 'line' => 2, 'cells' => array( 'level_1' => 'Uno', 'level_2' => 'Due' ) ) ), $result['rows'] );
	}

	/**
	 * A row with values only in ignored columns, or after the header, is an empty row: left out and not counted.
	 */
	public function test_a_row_with_values_only_outside_the_schema_is_empty() {
		$result = DLI_CSV_Reader::read_string( "level_1;level_2;nota\nUno;Due;x\n;;solo nota\n;;;oltre\nTre;Quattro;\n", self::SCHEMA );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 2, 5 ), array_column( $result['rows'], 'line' ) );
	}

	/**
	 * A file has at most `MAX_ROWS` (200) rows with a value in the schema; empty rows do not count, short rows do.
	 */
	public function test_a_file_has_at_most_two_hundred_rows() {
		$this->assertSame( 200, DLI_CSV_Reader::MAX_ROWS );

		$enough = DLI_CSV_Reader::read_string( $this->rows_of( 200 ) . "\n;\n;;solo nota\n", self::SCHEMA );
		$this->assertSame( array(), $enough['errors'], 'Empty rows and rows with values only in ignored columns do not count.' );
		$this->assertCount( 200, $enough['rows'] );

		$too_many = DLI_CSV_Reader::read_string( $this->rows_of( 201 ), self::SCHEMA );
		$this->assertSame( array( 'too_many_rows' ), $this->codes( $too_many ) );
		$this->assertSame( array(), $too_many['rows'] );

		$short = DLI_CSV_Reader::read_string( $this->rows_of( 200 ) . "Corta\n", self::SCHEMA );
		$this->assertSame( array( 'too_many_rows' ), $this->codes( $short ), 'A short row with a value counts.' );
	}

	/**
	 * Without a schema every column counts: the names are normalized, and an empty or repeated name refuses the file.
	 */
	public function test_without_a_schema_every_column_counts() {
		$free = DLI_CSV_Reader::read_string( "X; Y \n1;2\n" );
		$this->assertSame( array(), $free['errors'] );
		$this->assertSame( array( 'x', 'y' ), $free['headers'] );
		$this->assertSame( array(), $free['ignored'] );
		$this->assertSame( array( 'x' => '1', 'y' => '2' ), $free['rows'][0]['cells'] );

		$empty_name = DLI_CSV_Reader::read_string( "x;;y\n1;2;3\n" );
		$this->assertSame( array( 'empty_header' ), $this->codes( $empty_name ) );
		$this->assertSame( '2', $empty_name['errors'][0]['detail'], 'The detail is the position of the column.' );

		$twice = DLI_CSV_Reader::read_string( "x;X\n1;2\n" );
		$this->assertSame( array( 'duplicate_header' ), $this->codes( $twice ) );
		$this->assertSame( 'x', $twice['errors'][0]['detail'] );
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
	 * The size of the file has a limit.
	 */
	public function test_the_size_has_a_limit() {
		$big = DLI_CSV_Reader::read_string( "a;b\n" . str_repeat( 'x', 100 ) . ";1\n", array( 'max_bytes' => 50 ) );
		$this->assertSame( array( 'too_large' ), $this->codes( $big ) );
		$this->assertSame( array(), $big['rows'] );
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
