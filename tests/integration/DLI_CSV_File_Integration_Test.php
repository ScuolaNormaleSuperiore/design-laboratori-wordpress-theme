<?php
/**
 * The check of the uploaded CSV file and the messages shared by the imports from a file.
 *
 * @package Design_Laboratori_Italia
 */

require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-reader.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-file.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-page-file-importer.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-page-import-admin.php';

/**
 * `DLI_CSV_File::check()`, `DLI_CSV_File::message()` and `DLI_CSV_File::ignored_message()`.
 */
class DLI_CSV_File_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Temporary files that the test wrote.
	 *
	 * @var string[]
	 */
	private $files = array();

	/**
	 * Take away the temporary files.
	 */
	public function tear_down() {
		foreach ( $this->files as $path ) {
			if ( file_exists( $path ) ) {
				unlink( $path );
			}
		}
		parent::tear_down();
	}

	/**
	 * An entry of `$_FILES` for a temporary file with a content.
	 *
	 * @param string $content Content of the file.
	 * @param string $name    Name of the file.
	 * @return array<string, mixed> The entry.
	 */
	private function file( $content, $name = 'dati.csv' ) {
		$path = tempnam( sys_get_temp_dir(), 'dli-csv-file-' );
		file_put_contents( $path, $content );
		$this->files[] = $path;
		return array(
			'name'     => $name,
			'tmp_name' => $path,
			'error'    => UPLOAD_ERR_OK,
			'size'     => filesize( $path ),
		);
	}

	/**
	 * A CSV file of the right size and type is accepted; every other file has the code of what is wrong.
	 */
	public function test_the_uploaded_file_is_checked() {
		$this->assertSame( '', DLI_CSV_File::check( $this->file( "a;b\n1;2\n" ), false ) );
		$this->assertSame( '', DLI_CSV_File::check( $this->file( "a;b\n1;2\n", 'DATI.CSV' ), false ), 'The extension in upper case is a CSV.' );

		$this->assertSame( 'no_file', DLI_CSV_File::check( array(), false ) );
		$missing          = $this->file( "a;b\n" );
		$missing['error'] = UPLOAD_ERR_NO_FILE;
		$this->assertSame( 'no_file', DLI_CSV_File::check( $missing, false ) );

		$this->assertSame( 'not_uploaded', DLI_CSV_File::check( $this->file( "a;b\n" ), true ), 'A path that is not an upload of HTTP is refused.' );
		$this->assertSame( 'extension', DLI_CSV_File::check( $this->file( "a;b\n", 'dati.txt' ), false ) );

		$big         = $this->file( "a;b\n" );
		$big['size'] = DLI_CSV_Reader::MAX_BYTES + 1;
		$this->assertSame( 'too_large', DLI_CSV_File::check( $big, false ) );

		if ( function_exists( 'mime_content_type' ) ) {
			$png = $this->file( base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==' ) );
			$this->assertSame( 'mime', DLI_CSV_File::check( $png, false ), 'An image named .csv is refused.' );
		}
	}

	/**
	 * The messages of the upload and of the reader carry their detail; a code of a single import has none here.
	 */
	public function test_the_messages_shared_by_the_imports() {
		foreach ( array( 'no_file', 'not_uploaded', 'extension', 'too_large', 'mime', 'unreadable', 'nul_byte', 'invalid_utf8', 'empty' ) as $code ) {
			$this->assertNotSame( '', DLI_CSV_File::message( $code ), $code );
		}
		$this->assertStringContainsString( '4', DLI_CSV_File::message( 'malformed', 4 ) );
		$this->assertStringContainsString( 'nome', DLI_CSV_File::message( 'missing_header', 1, 'nome' ) );
		$this->assertStringContainsString( 'email', DLI_CSV_File::message( 'duplicate_header', 1, 'email' ) );
		$this->assertStringContainsString( '3', DLI_CSV_File::message( 'empty_header', 1, '3' ) );
		$this->assertStringContainsString( (string) DLI_CSV_Reader::MAX_ROWS, DLI_CSV_File::message( 'too_many_rows' ) );
		$this->assertStringContainsString( '7', DLI_CSV_File::message( 'columns_count', 7 ) );

		$this->assertSame( '', DLI_CSV_File::message( 'no_title', 3 ), 'A code of the pages is not a shared one.' );
		$this->assertStringContainsString( 'Note, Colore', DLI_CSV_File::ignored_message( array( 'Note', 'Colore' ) ) );
	}

	/**
	 * The import of the pages gives the shared messages for the shared codes, and its own for its codes.
	 */
	public function test_the_pages_use_the_shared_messages() {
		foreach ( array( 'no_file', 'invalid_utf8', 'too_many_rows' ) as $code ) {
			$this->assertSame( DLI_CSV_File::message( $code ), DLI_Page_Import_Admin::message( $code ), $code );
		}
		$this->assertSame( DLI_CSV_File::message( 'missing_header', 1, 'level_1' ), DLI_Page_Import_Admin::message( 'missing_header', 1, 'level_1' ) );
		$this->assertStringContainsString( '3', DLI_Page_Import_Admin::message( 'no_title', 3 ) );
		$this->assertSame( 'codice_sconosciuto', DLI_Page_Import_Admin::message( 'codice_sconosciuto' ), 'An unknown code is shown as it is.' );
	}
}
