<?php
/**
 * Strumenti DLI → Importa → Pagine: who can use it, what the request must have, and what it does with the file.
 *
 * @package Design_Laboratori_Italia
 */

require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-reader.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-page-file-importer.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-page-import-admin.php';

/**
 * `DLI_Page_Import_Admin::process_request()`, the download of the examples and the form.
 */
class DLI_Page_Import_Admin_Integration_Test extends DLI_Integration_Test_Case {

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
		$_GET = array();
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * Add the English language: the set-up of the base class creates only the Italian one.
	 */
	private function add_english() {
		$language = PLL()->model->languages->add(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => false,
				'flag'   => 'gb',
			)
		);
		$this->assertNotWPError( $language );
	}

	/**
	 * Log in a user with a role and, if asked, the capability of the tools of the site.
	 *
	 * @param string $role       Role.
	 * @param bool   $with_tools Whether the user has `DLI_EDIT_CONFIG_PERMISSION`.
	 * @return int User ID.
	 */
	private function login( $role, $with_tools = true ) {
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
				'dli_import_pagine_nonce' => wp_create_nonce( 'dli_import_pagine' ),
				'dli_pages_language'      => 'it',
				'dli_pages_exec'          => 'dry_run',
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
	private function upload( $content, $name = 'pagine.csv' ) {
		$path = tempnam( sys_get_temp_dir(), 'dli-pages-' );
		file_put_contents( $path, $content );
		$this->files[] = $path;
		return array(
			'dli_pages_csv' => array(
				'name'     => $name,
				'tmp_name' => $path,
				'error'    => UPLOAD_ERR_OK,
				'size'     => filesize( $path ),
			),
		);
	}

	/**
	 * The file of the form, from one of the example files.
	 *
	 * @param string $name Example file.
	 * @return array<string, array<string, mixed>> The part of `$_FILES`.
	 */
	private function upload_example( $name = 'pages-example-it.csv' ) {
		return $this->upload( (string) file_get_contents( dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/' . $name ), $name );
	}

	/**
	 * Send the request: the file is a temporary file, not an upload through HTTP.
	 *
	 * @param array<string, string>                $post  The POST.
	 * @param array<string, array<string, mixed>> $files The FILES.
	 * @return array<string, mixed> The report.
	 */
	private function send( array $post, array $files ) {
		return DLI_Page_Import_Admin::process_request( $post, $files, false );
	}

	/**
	 * Every page on the site, in every status.
	 *
	 * @return int
	 */
	private function count_pages() {
		return count(
			get_posts(
				array(
					'post_type'        => 'page',
					'post_status'      => array_keys( get_post_stati() ),
					'numberposts'      => -1,
					'suppress_filters' => true,
					'fields'           => 'ids',
				)
			)
		);
	}

	/**
	 * The codes of the errors of a report.
	 *
	 * @param array<string, mixed> $report The report.
	 * @return string[]
	 */
	private function codes( array $report ) {
		return array_column( $report['errors'], 'code' );
	}

	/**
	 * Without the right to the tools of the site and to publish pages, nothing is read and nothing is written.
	 */
	public function test_who_cannot_use_the_tool_is_refused() {
		$before = $this->count_pages();
		$users  = array(
			'nobody'                         => static function () {
				wp_set_current_user( 0 );
			},
			'an editor without the tools'    => function () {
				$this->login( 'editor', false );
			},
			'a subscriber with the tools'    => function () {
				$this->login( 'subscriber', true );
			},
			'an author with the tools'       => function () {
				$this->login( 'author', true );
			},
		);

		foreach ( $users as $who => $login ) {
			$login();
			$report = $this->send( $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) ), $this->upload_example() );

			$this->assertSame( 'forbidden', $report['error'], $who );
			$this->assertSame( $before, $this->count_pages(), "$who wrote nothing." );
		}
	}

	/**
	 * An administrator with the tools can use it.
	 */
	public function test_an_administrator_with_the_tools_can_use_it() {
		$this->login( 'administrator' );

		$report = $this->send( $this->post(), $this->upload_example() );

		$this->assertSame( '', $report['error'] );
		$this->assertSame( array(), $report['errors'] );
	}

	/**
	 * The nonce of the form is needed: missing, wrong or made for another action.
	 */
	public function test_the_nonce_is_needed() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		foreach ( array( '', 'sbagliato', wp_create_nonce( 'dli_import_persone' ) ) as $nonce ) {
			$post   = $this->post( array( 'dli_import_pagine_nonce' => $nonce, 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) );
			$report = $this->send( $post, $this->upload_example() );

			$this->assertSame( 'nonce', $report['error'], json_encode( $nonce ) );
		}
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * The simulation reports what would be done, with the language chosen, and writes nothing.
	 */
	public function test_dry_run_reports_the_plan_and_writes_nothing() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		$report = $this->send( $this->post(), $this->upload_example() );

		$this->assertSame( 'dry_run', $report['exec'] );
		$this->assertSame( 'it', $report['language'] );
		$this->assertSame( 'pages-example-it.csv', $report['filename'] );
		$this->assertSame( array( 'created' => 15, 'skipped' => 0, 'errors' => 0 ), $report['counts'] );
		$this->assertCount( 15, $report['rows'] );
		$this->assertFalse( $report['written'] );
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * Anything that is not "commit" is a simulation, whatever the request says.
	 */
	public function test_an_unknown_mode_is_a_simulation() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		foreach ( array( '', 'import', 'COMMIT ', 'true' ) as $mode ) {
			$report = $this->send( $this->post( array( 'dli_pages_exec' => $mode, 'dli_pages_confirm' => '1' ) ), $this->upload_example() );

			$this->assertSame( 'dry_run', $report['exec'], json_encode( $mode ) );
		}
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * Without the confirmation of the publication, the real import writes nothing: the plan is shown with the reason.
	 */
	public function test_the_import_without_the_confirmation_writes_nothing() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		foreach ( array( array(), array( 'dli_pages_confirm' => '' ), array( 'dli_pages_confirm' => '0' ), array( 'dli_pages_confirm' => 'on' ) ) as $confirmation ) {
			$report = $this->send( $this->post( array_merge( array( 'dli_pages_exec' => 'commit' ), $confirmation ) ), $this->upload_example() );

			$this->assertSame( 'commit', $report['exec'] );
			$this->assertSame( 'not_confirmed', $report['error'], json_encode( $confirmation ) );
			$this->assertFalse( $report['written'] );
			$this->assertCount( 15, $report['rows'], 'The plan is still shown.' );
		}
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * With the confirmation the pages are created, in the language chosen, with the text only if asked.
	 */
	public function test_the_confirmed_import_creates_the_pages() {
		$this->login( 'administrator' );

		$report = $this->send( $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) ), $this->upload_example() );

		$this->assertSame( '', $report['error'] );
		$this->assertTrue( $report['written'] );
		$this->assertSame( array( 'created' => 15, 'skipped' => 0, 'errors' => 0 ), $report['counts'] );
		$root = get_page_by_path( 'dati-della-ricerca' );
		$this->assertSame( 'it', pll_get_post_language( $root->ID ) );
		$this->assertSame( '', $root->post_content, 'Without the lorem the pages have no text.' );
		$this->assertSame( get_current_user_id(), (int) $root->post_author, 'The author is the user who imports.' );
	}

	/**
	 * Lorem is written in the new pages when the box is ticked.
	 */
	public function test_lorem_is_written_when_asked() {
		$this->login( 'administrator' );

		$this->send( $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1', 'dli_pages_lorem' => '1' ) ), $this->upload_example() );

		$this->assertSame( DLI_Page_File_Importer::lorem_html( 'research-data' ), get_page_by_path( 'dati-della-ricerca' )->post_content );
	}

	/**
	 * Sending the same file again, with the confirmation, creates nothing more.
	 */
	public function test_sending_the_file_again_creates_nothing_more() {
		$this->login( 'administrator' );
		$post = $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) );
		$this->send( $post, $this->upload_example() );
		$count = $this->count_pages();

		$report = $this->send( $post, $this->upload_example() );

		$this->assertSame( array( 'created' => 0, 'skipped' => 15, 'errors' => 0 ), $report['counts'] );
		$this->assertSame( $count, $this->count_pages() );
	}

	/**
	 * The language is one of the site: the request cannot make up another.
	 */
	public function test_a_language_that_the_site_does_not_have_is_refused() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		$report = $this->send( $this->post( array( 'dli_pages_language' => 'fr', 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) ), $this->upload_example() );

		$this->assertSame( array( 'language' ), $this->codes( $report ) );
		$this->assertFalse( $report['written'] );
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * The file that is not a usable upload is refused with its reason.
	 */
	public function test_a_file_that_cannot_be_used_is_refused() {
		$this->login( 'administrator' );
		$before = $this->count_pages();

		$this->assertSame( 'no_file', $this->send( $this->post(), array() )['error'] );

		$files                                = $this->upload_example();
		$files['dli_pages_csv']['error']      = UPLOAD_ERR_NO_FILE;
		$this->assertSame( 'no_file', $this->send( $this->post(), $files )['error'] );

		$this->assertSame( 'extension', $this->send( $this->post(), $this->upload( "level_1\n", 'pagine.txt' ) )['error'] );

		$files                           = $this->upload_example();
		$files['dli_pages_csv']['size']  = DLI_CSV_Reader::MAX_BYTES + 1;
		$this->assertSame( 'too_large', $this->send( $this->post(), $files )['error'] );

		$this->assertSame( 'not_uploaded', DLI_Page_Import_Admin::process_request( $this->post(), $this->upload_example(), true )['error'], 'A path that is not an upload of HTTP is refused.' );
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * The errors of the file are all reported with their lines, and nothing is written, not even the pages that are right.
	 */
	public function test_the_errors_of_the_file_are_reported_and_nothing_is_written() {
		$this->login( 'administrator' );
		$before  = $this->count_pages();
		$content = "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\nBuona;;;1;0;0\n;Senza genitore;;x;0;0\n;;;1;0;0\nAltra;;;1;5;0\n";

		$report = $this->send( $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) ), $this->upload( $content ) );

		$this->assertSame( array( 'invalid_order', 'no_title', 'invalid_flag' ), $this->codes( $report ) );
		$this->assertSame( array( 3, 4, 5 ), array_column( $report['errors'], 'line' ) );
		$this->assertFalse( $report['written'] );
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * A wrong header, columns that do not match, a file that is not UTF-8 and a file with too many pages are refused.
	 */
	public function test_the_file_is_refused_for_its_header_its_encoding_and_its_size_in_pages() {
		$this->login( 'administrator' );

		$header = $this->send( $this->post(), $this->upload( "level_1;level_2\nUno;Due\n" ) );
		$this->assertContains( 'missing_header', $this->codes( $header ) );

		$latin = $this->send( $this->post(), $this->upload( "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\nCitt\xE0;;;1;0;0\n" ) );
		$this->assertSame( array( 'invalid_utf8' ), $this->codes( $latin ) );

		$rows = '';
		for ( $number = 1; $number <= DLI_Page_File_Importer::MAX_PAGES + 1; $number++ ) {
			$rows .= "Pagina $number;;;1;0;0\n";
		}
		$many = $this->send( $this->post(), $this->upload( "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\n" . $rows ) );
		$this->assertSame( array( 'too_many_rows' ), $this->codes( $many ) );
	}

	/**
	 * A page that exists with the same address stops the import with a message that says which page.
	 */
	public function test_a_conflict_stops_the_import() {
		$this->login( 'administrator' );
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Open Access',
				'post_name'  => 'open-access',
			)
		);
		dli_set_post_language( $page_id, 'it' );
		$before = $this->count_pages();

		$report = $this->send( $this->post( array( 'dli_pages_exec' => 'commit', 'dli_pages_confirm' => '1' ) ), $this->upload_example() );

		$this->assertSame( array( 'path_in_use' ), $this->codes( $report ) );
		$this->assertSame( (string) $page_id, $report['errors'][0]['detail'] );
		$this->assertSame( $before, $this->count_pages() );
	}

	/**
	 * The example files can be downloaded by name only: no path, nothing else.
	 */
	public function test_the_examples_are_downloaded_by_name_only() {
		$this->assertSame( dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/pages-example-it.csv', DLI_Page_Import_Admin::download_path( 'esempio-it' ) );
		$this->assertSame( dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/pages-example-en.csv', DLI_Page_Import_Admin::download_path( 'esempio-en' ) );
		$this->assertSame( dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/pages-model.csv', DLI_Page_Import_Admin::download_path( 'modello' ) );

		foreach ( array( '', 'altro', '../../wp-config', 'esempio-it/../../wp-config', 'pages-example-it.csv', 'ESEMPIO-IT' ) as $wrong ) {
			$this->assertSame( '', DLI_Page_Import_Admin::download_path( $wrong ), json_encode( $wrong ) );
		}
		foreach ( array( 'esempio-it', 'esempio-en', 'modello' ) as $name ) {
			$this->assertFileExists( DLI_Page_Import_Admin::download_path( $name ) );
		}
	}

	/**
	 * The form: the file, the languages of the site, the simulation as the default, the lorem and the confirmation, and the downloads.
	 */
	public function test_the_form_has_the_fields_and_the_downloads() {
		$this->add_english();
		$this->login( 'administrator' );

		ob_start();
		DLI_Page_Import_Admin::render();
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'name="dli_pages_csv"', $html );
		$this->assertStringContainsString( 'name="dli_import_pagine_nonce"', $html );
		$this->assertSame( 1, preg_match( '#<select[^>]*name="dli_pages_language".*?</select>#s', $html, $select ) );
		$this->assertStringContainsString( 'value="it"', $select[0] );
		$this->assertStringContainsString( 'value="en"', $select[0] );
		$this->assertSame( 1, preg_match( '#<option value="dry_run"[^>]*selected#', $html ), 'The simulation is the default.' );
		$this->assertStringContainsString( 'name="dli_pages_lorem"', $html );
		$this->assertStringContainsString( 'name="dli_pages_confirm"', $html );
		$this->assertStringNotContainsString( 'dli_pages_lorem" value="1" checked', $html, 'The lorem is off by default.' );
		foreach ( array( 'esempio-it', 'esempio-en', 'modello' ) as $name ) {
			$this->assertStringContainsString( 'file=' . $name, $html );
		}
		$this->assertSame( 3, preg_match_all( '/_wpnonce=[a-z0-9]+/', $html ), 'Each download has its nonce.' );
	}

	/**
	 * After a request the form keeps the language and the run mode, but the lorem and the confirmation are always off:
	 * both are asked again at every submit, the file too.
	 */
	public function test_after_a_request_the_lorem_and_the_confirmation_are_asked_again() {
		$this->add_english();
		$this->login( 'administrator' );
		$_POST = $this->post(
			array(
				'dli_pages_language' => 'en',
				'dli_pages_exec'     => 'commit',
				'dli_pages_lorem'    => '1',
				'dli_pages_confirm'  => '1',
			)
		);

		ob_start();
		DLI_Page_Import_Admin::render();
		$html = (string) ob_get_clean();
		$_POST = array();

		$this->assertSame( 1, preg_match( '#<option value="en"[^>]*selected#', $html ), 'The language is kept.' );
		$this->assertSame( 1, preg_match( '#<option value="commit"[^>]*selected#', $html ), 'The run mode is kept.' );
		$this->assertSame( 1, preg_match( '#<input[^>]*id="dli-pages-lorem"[^>]*>#', $html, $lorem ) );
		$this->assertStringNotContainsString( 'checked', $lorem[0], 'The lorem is asked again.' );
		$this->assertSame( 1, preg_match( '#<input[^>]*id="dli-pages-confirm"[^>]*>#', $html, $confirm ) );
		$this->assertStringNotContainsString( 'checked', $confirm[0], 'The confirmation is asked again.' );
	}

	/**
	 * The stylesheet of the tools is loaded on every page of the tools, whatever the language of the menu title: the
	 * suffix of the page is made by WordPress from the title, which is translated.
	 */
	public function test_the_stylesheet_of_the_tools_is_loaded_on_each_of_their_pages() {
		$this->login( 'administrator' );
		// The title as it is written in the code, in Italian: its suffix is `strumenti-dli`, not the slug of the menu.
		$untranslated = static function ( $translation, $text, $domain ) {
			return 'design_laboratori_italia' === $domain ? $text : $translation;
		};
		add_filter( 'gettext', $untranslated, 10, 3 );
		unset( $GLOBALS['admin_page_hooks']['dli-tools'] );
		DLI_Tools_Admin::register_menu();
		remove_filter( 'gettext', $untranslated, 10 );
		$this->assertSame( 'strumenti-dli', $GLOBALS['admin_page_hooks']['dli-tools'], 'The menu has its title in Italian.' );

		foreach ( array( 'dli-tools', 'dli-tools-import', 'dli-tools-utils' ) as $slug ) {
			wp_dequeue_style( 'dli-tools-admin' );
			$hook = 'dli-tools' === $slug ? 'toplevel_page_dli-tools' : get_plugin_page_hookname( $slug, 'dli-tools' );

			DLI_Tools_Admin::enqueue_assets( $hook );

			$this->assertTrue( wp_style_is( 'dli-tools-admin', 'enqueued' ), "The page $slug ($hook) has the stylesheet." );
		}

		wp_dequeue_style( 'dli-tools-admin' );
		DLI_Tools_Admin::enqueue_assets( 'edit.php' );
		$this->assertFalse( wp_style_is( 'dli-tools-admin', 'enqueued' ), 'Another page of the admin does not.' );
	}

	/**
	 * The page of the tool lists the new type, and its address shows the form.
	 */
	public function test_the_import_page_has_the_new_type() {
		$this->login( 'administrator' );
		$_GET = array(
			'page' => 'dli-tools-import',
			'type' => 'pagine',
		);

		ob_start();
		DLI_Tools_Admin::render_importa();
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'type=pagine', $html, 'The sub-navigation links to the new type.' );
		$this->assertStringContainsString( 'name="dli_pages_csv"', $html );
		$this->assertStringNotContainsString( 'name="dli_csv_file"', $html, 'The form of the people is not on this page.' );
	}
}
