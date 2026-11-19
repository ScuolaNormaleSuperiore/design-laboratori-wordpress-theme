<?php
/**
 * Strumenti DLI → Importa → Persone: who can use it, what the request must have, and what it does with the file.
 *
 * These tests describe the behaviour of the tool as it was before it moved into its own class, so that the move, and
 * then the change to the common reader, can be checked against them.
 *
 * @package Design_Laboratori_Italia
 */

require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-reader.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-file.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-people-file-importer.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-people-import-admin.php';

/**
 * `DLI_People_Import_Admin::process_request()` and the download of the example.
 */
class DLI_People_Import_Admin_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The header of the files of the tests.
	 */
	const HEADER = "nome;cognome;email;stato\n";

	/**
	 * Temporary files that the test wrote.
	 *
	 * @var string[]
	 */
	private $files = array();

	/**
	 * Take away the temporary files and what the test put in the request.
	 */
	public function tear_down() {
		foreach ( $this->files as $path ) {
			if ( file_exists( $path ) ) {
				unlink( $path );
			}
		}
		$_GET     = array();
		$_REQUEST = array();
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Log in a user with a role and, if asked, the capability of the tools of the site.
	 *
	 * @param string $role       Role.
	 * @param bool   $with_tools Whether the user has `DLI_EDIT_CONFIG_PERMISSION`.
	 * @return int User ID.
	 */
	private function login( $role = 'administrator', $with_tools = true ) {
		$user_id = self::factory()->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		if ( $with_tools ) {
			wp_get_current_user()->add_cap( DLI_EDIT_CONFIG_PERMISSION );
		}
		return $user_id;
	}

	/**
	 * What the form sends, with the nonce.
	 *
	 * @param array<string, string> $values Values that replace the default ones.
	 * @return array<string, string> The POST.
	 */
	private function post( array $values = array() ) {
		return array_merge(
			array(
				'dli_import_persone_nonce' => wp_create_nonce( 'dli_import_persone' ),
				'dli_import_mode'          => 'upsert',
				'dli_exec_mode'            => 'dry_run',
			),
			$values
		);
	}

	/**
	 * The file of the form, from a text.
	 *
	 * @param string $content Content of the file.
	 * @param string $name    Name of the file.
	 * @return array<string, array<string, mixed>> The part of `$_FILES`.
	 */
	private function upload( $content, $name = 'persone.csv' ) {
		$path = tempnam( sys_get_temp_dir(), 'dli-people-' );
		file_put_contents( $path, $content );
		$this->files[] = $path;
		return array(
			'dli_csv_file' => array(
				'name'     => $name,
				'tmp_name' => $path,
				'error'    => UPLOAD_ERR_OK,
				'size'     => filesize( $path ),
			),
		);
	}

	/**
	 * Send the request: the file is a temporary file, not an upload through HTTP.
	 *
	 * @param array<string, string>               $post  The POST.
	 * @param array<string, array<string, mixed>> $files The FILES.
	 * @return array<string, mixed> The report.
	 */
	private function send( array $post, array $files ) {
		return DLI_People_Import_Admin::process_request( $post, $files, false );
	}

	/**
	 * The counters of a report: processed, created, updated, skipped, errors.
	 *
	 * @param array<string, mixed> $report The report.
	 * @return int[]
	 */
	private function counters( array $report ) {
		return array( $report['elaborati'], $report['creati'], $report['aggiornati'], $report['saltati'], $report['errori_count'] );
	}

	/**
	 * Every person on the site, in any status.
	 *
	 * @return int[]
	 */
	private function people() {
		return get_posts(
			array(
				'post_type'   => PEOPLE_POST_TYPE,
				'post_status' => 'any',
				'numberposts' => -1,
				'lang'        => '',
				'fields'      => 'ids',
			)
		);
	}

	/**
	 * The person with an email, or null.
	 *
	 * @param string $email Email.
	 * @return WP_Post|null
	 */
	private function person( $email ) {
		$ids = get_posts(
			array(
				'post_type'   => PEOPLE_POST_TYPE,
				'post_status' => 'any',
				'numberposts' => 1,
				'lang'        => '',
				'meta_key'    => 'email',
				'meta_value'  => $email,
			)
		);
		return $ids ? $ids[0] : null;
	}

	/**
	 * Without the right to the tools of the site the request stops and nothing is written.
	 */
	public function test_who_cannot_use_the_tool_is_stopped() {
		$this->login( 'editor', false );
		$post  = $this->post( array( 'dli_exec_mode' => 'commit' ) );
		$files = $this->upload( self::HEADER . "Ada;Lovelace;ada@example.org;publish\n" );

		$this->expectException( WPDieException::class );
		try {
			$this->send( $post, $files );
		} finally {
			$this->assertSame( array(), $this->people() );
		}
	}

	/**
	 * Without a valid nonce the request stops.
	 */
	public function test_the_nonce_is_needed() {
		$this->login();

		foreach ( array( '', 'sbagliato' ) as $nonce ) {
			try {
				$this->send( $this->post( array( 'dli_import_persone_nonce' => $nonce ) ), $this->upload( self::HEADER ) );
				$this->fail( 'A request without a valid nonce must stop: ' . json_encode( $nonce ) );
			} catch ( WPDieException $stop ) {
				$this->assertStringContainsString( 'nonce', $stop->getMessage() );
			}
		}
	}

	/**
	 * A mode that is not known is `upsert`, a run mode that is not known is the simulation (the values are read with
	 * `sanitize_key()`, so `COMMIT` would be `commit`).
	 */
	public function test_unknown_modes_are_upsert_and_simulation() {
		$this->login();

		$report = $this->send( $this->post( array( 'dli_import_mode' => 'tutto', 'dli_exec_mode' => 'esegui' ) ), $this->upload( self::HEADER . "Ada;Lovelace;ada@example.org;publish\n" ) );

		$this->assertSame( 'upsert', $report['import_mode'] );
		$this->assertSame( 'dry_run', $report['exec_mode'] );
		$this->assertSame( array(), $this->people() );
	}

	/**
	 * A file that is not a usable upload, or whose header misses a required column, gives a notice and nothing is
	 * processed; the name of the file is shown from the check of the extension on.
	 */
	public function test_a_file_that_cannot_be_used_is_refused() {
		$this->login();

		$none = $this->send( $this->post(), array() );
		$this->assertSame( array( 'warning', '' ), array( $none['notice_type'], $none['filename'] ) );
		$this->assertSame( DLI_CSV_File::message( 'no_file' ), $none['notice'] );

		$http = DLI_People_Import_Admin::process_request( $this->post(), $this->upload( self::HEADER ), true );
		$this->assertSame( array( 'error', '' ), array( $http['notice_type'], $http['filename'] ), 'A path that is not an upload of HTTP is refused.' );

		$text = $this->send( $this->post(), $this->upload( self::HEADER, 'persone.txt' ) );
		$this->assertSame( array( 'error', 'persone.txt' ), array( $text['notice_type'], $text['filename'] ) );
		$this->assertSame( DLI_CSV_File::message( 'extension' ), $text['notice'] );

		$files                         = $this->upload( self::HEADER );
		$files['dli_csv_file']['size'] = DLI_CSV_Reader::MAX_BYTES + 1;
		$this->assertSame( DLI_CSV_File::message( 'too_large' ), $this->send( $this->post(), $files )['notice'] );

		$header = $this->send( $this->post(), $this->upload( "nome;cognome\nAda;Lovelace\n" ) );
		$this->assertSame( 'error', $header['notice_type'] );
		$this->assertStringContainsString( '"email"', $header['notice'] );
		$this->assertSame( array( 0, 0, 0, 0, 0 ), $this->counters( $header ) );
	}

	/**
	 * The simulation reports what each row would do and writes nothing.
	 */
	public function test_the_simulation_writes_nothing() {
		$this->login();

		$report = $this->send( $this->post(), $this->upload( self::HEADER . "Ada;Lovelace;ada@example.org;publish\nAlan;Turing;alan@example.org;draft\n" ) );

		$this->assertSame( '', $report['notice'] );
		$this->assertSame( 'persone.csv', $report['filename'] );
		$this->assertSame( array( 2, 2, 0, 0, 0 ), $this->counters( $report ) );
		$this->assertSame( array( 2, 3 ), array_column( $report['log'], 'row' ) );
		$this->assertSame( array(), $this->people() );
	}

	/**
	 * The import creates the people; a file with only the required columns gets the default values, and the BOM of
	 * Excel is not part of the first column.
	 */
	public function test_the_import_creates_the_people_with_the_default_values() {
		$this->login();

		$report = $this->send( $this->post( array( 'dli_exec_mode' => 'commit' ) ), $this->upload( "\xEF\xBB\xBFnome;cognome;email\nAda;Lovelace;ada@example.org\n" ) );

		$this->assertSame( array( 1, 1, 0, 0, 0 ), $this->counters( $report ) );
		$ada = $this->person( 'ada@example.org' );
		$this->assertNotNull( $ada );
		$this->assertSame( 'Ada Lovelace', $ada->post_title );
		$this->assertSame( 'publish', $ada->post_status, 'Without the column stato the person is published.' );
		$this->assertFalse( (bool) dli_get_field( 'escludi_da_elenco', $ada->ID ) );
		$this->assertFalse( (bool) dli_get_field( 'disattiva_pagina_dettaglio', $ada->ID ) );
	}

	/**
	 * A row with an error is reported with its line, and the other rows are processed.
	 */
	public function test_a_row_with_an_error_does_not_stop_the_others() {
		$this->login();

		$report = $this->send( $this->post( array( 'dli_exec_mode' => 'commit' ) ), $this->upload( self::HEADER . "Ada;Lovelace;;publish\nAlan;Turing;alan@example.org;publish\n" ) );

		$this->assertSame( array( 2, 1, 0, 0, 1 ), $this->counters( $report ) );
		$this->assertSame( array( 'ERRORE', 'OK' ), array_column( $report['log'], 'status' ) );
		$this->assertSame( 2, $report['log'][0]['row'] );
		$this->assertNotNull( $this->person( 'alan@example.org' ) );
	}

	/**
	 * Each of the four modes decides by the existence of the person: one that exists and one that does not.
	 */
	public function test_each_mode_through_the_request() {
		$this->login();
		$this->send( $this->post( array( 'dli_exec_mode' => 'commit' ) ), $this->upload( self::HEADER . "Ada;Lovelace;ada@example.org;publish\n" ) );
		$file = self::HEADER . "Ada;Byron;ada@example.org;publish\nAlan;Turing;alan@example.org;publish\n";

		$expected = array(
			'upsert'             => array( 2, 1, 1, 0, 0 ),
			'solo_nuovi'         => array( 2, 1, 0, 1, 0 ),
			'aggiorna_esistenti' => array( 2, 0, 1, 1, 0 ),
			'ignora_esistenti'   => array( 2, 1, 0, 1, 0 ),
		);
		foreach ( $expected as $mode => $counters ) {
			$report = $this->send( $this->post( array( 'dli_import_mode' => $mode ) ), $this->upload( $file ) );
			$this->assertSame( $counters, $this->counters( $report ), $mode );
		}
		$this->assertCount( 1, $this->people(), 'The simulations wrote nothing.' );
	}

	/**
	 * The example is downloaded only by who can use the tools.
	 */
	public function test_the_example_needs_the_right_to_the_tools() {
		$this->login( 'editor', false );
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'dli_download_sample_persone' );

		$this->expectException( WPDieException::class );
		DLI_People_Import_Admin::handle_download();
	}

	/**
	 * The example is downloaded only with the nonce of the link.
	 */
	public function test_the_example_needs_the_nonce() {
		$this->login();
		$_REQUEST['_wpnonce'] = 'sbagliato';
		$this->expectException( WPDieException::class );
		DLI_People_Import_Admin::handle_download();
	}
}
