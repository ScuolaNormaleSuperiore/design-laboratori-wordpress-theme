<?php
/**
 * The import of pages from a file: the plan (what the import would do) and the import, on real WordPress with ACF and Polylang.
 *
 * @package Design_Laboratori_Italia
 */

require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-csv-reader.php';
require_once dirname( __DIR__, 2 ) . '/inc/admin/class-dli-page-file-importer.php';

/**
 * `DLI_Page_File_Importer::plan()` and `::import()`, with the example files of `SETUP/CSV_Import/`.
 */
class DLI_Page_File_Importer_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The columns of the files.
	 */
	const REQUIRED = array( 'level_1', 'level_2', 'level_3', 'menu_order', 'promote_in_home', 'show_subpages_as_cards' );
	const OPTIONAL = array( 'page_key', 'excerpt', 'slug' );

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
	 * The pages of an example file, parsed.
	 *
	 * @param string $name File in `SETUP/CSV_Import/`.
	 * @return array<int, array<string, mixed>> The pages.
	 */
	private function pages_of( $name ) {
		$result = DLI_CSV_Reader::read(
			dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/' . $name,
			array(
				'required' => self::REQUIRED,
				'optional' => self::OPTIONAL,
			)
		);
		$this->assertSame( array(), $result['errors'] );
		$parsed = DLI_Page_File_Importer::parse_rows( $result['rows'] );
		$this->assertSame( array(), $parsed['errors'] );
		return $parsed['pages'];
	}

	/**
	 * The pages of a CSV text, parsed.
	 *
	 * @param string $content Text of the file.
	 * @return array<int, array<string, mixed>> The pages.
	 */
	private function pages_from( $content ) {
		$result = DLI_CSV_Reader::read_string(
			$content,
			array(
				'required' => self::REQUIRED,
				'optional' => self::OPTIONAL,
			)
		);
		$this->assertSame( array(), $result['errors'] );
		$parsed = DLI_Page_File_Importer::parse_rows( $result['rows'] );
		$this->assertSame( array(), $parsed['errors'] );
		return $parsed['pages'];
	}

	/**
	 * Log in an administrator.
	 *
	 * @return int User ID.
	 */
	private function login_administrator() {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		return $user_id;
	}

	/**
	 * Every page written by the import, in every language and status.
	 *
	 * @return int[]
	 */
	private function imported_pages() {
		return get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => array_keys( get_post_stati() ),
				'numberposts'      => -1,
				'meta_key'         => DLI_Page_File_Importer::META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Test.
				'suppress_filters' => true,
				'fields'           => 'ids',
			)
		);
	}

	/**
	 * Every page on the site, in every language and status.
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
	 * A page by its title and language.
	 *
	 * @param string $title    Title.
	 * @param string $language Language slug.
	 * @return WP_Post|null
	 */
	private function page( $title, $language ) {
		foreach ( $this->imported_pages() as $page_id ) {
			if ( get_the_title( $page_id ) === $title && pll_get_post_language( $page_id ) === $language ) {
				return get_post( $page_id );
			}
		}
		return null;
	}

	/**
	 * Titles of the subpages of a page, in the order of the menu.
	 *
	 * @param int $parent_id The page.
	 * @return string[]
	 */
	private function children_of( $parent_id ) {
		$children = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'post_parent'      => $parent_id,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'numberposts'      => -1,
				'suppress_filters' => true,
			)
		);
		return wp_list_pluck( $children, 'post_title' );
	}

	/**
	 * The actions of a plan, in the order of the pages.
	 *
	 * @param array<string, mixed> $plan A plan or the result of an import.
	 * @return string[]
	 */
	private function actions( array $plan ) {
		return array_column( $plan['rows'], 'action' );
	}

	/**
	 * The codes of the errors of a plan.
	 *
	 * @param array<string, mixed> $plan A plan or the result of an import.
	 * @return string[]
	 */
	private function error_codes( array $plan ) {
		return array_column( $plan['errors'], 'code' );
	}

	/**
	 * A language that the site does not have is refused before anything else.
	 */
	public function test_a_language_that_the_site_does_not_have_is_refused() {
		$plan = DLI_Page_File_Importer::plan( $this->pages_of( 'pages-example-it.csv' ), 'fr' );

		$this->assertSame( array( 'language' ), $this->error_codes( $plan ) );
		$this->assertSame( array(), $this->imported_pages() );
	}

	/**
	 * The plan says what each page would do, with its address, and writes nothing.
	 */
	public function test_the_plan_lists_the_pages_with_their_addresses_and_writes_nothing() {
		$before = $this->count_pages();

		$plan = DLI_Page_File_Importer::plan( $this->pages_of( 'pages-example-it.csv' ), 'it' );

		$this->assertSame( array(), $plan['errors'] );
		$this->assertCount( 15, $plan['rows'] );
		$this->assertSame( array_fill( 0, 15, 'created' ), $this->actions( $plan ) );
		$this->assertSame( array( 'created' => 15, 'skipped' => 0, 'errors' => 0 ), $plan['counts'] );
		$this->assertStringEndsWith( '/open-access/', $plan['rows'][0]['url'] );
		$this->assertStringEndsWith( '/dati-della-ricerca/pianificare/cosa-sono-i-dati-della-ricerca/', $plan['rows'][4]['url'] );
		$this->assertSame( $before, $this->count_pages(), 'The plan wrote nothing.' );
	}

	/**
	 * The import creates the pages of the file: parents, order, summaries, flags, language, author, status and the mark of the import.
	 */
	public function test_import_creates_the_tree_of_the_file() {
		$author = $this->login_administrator();

		$result = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array( 'created' => 15, 'skipped' => 0, 'errors' => 0 ), $result['counts'] );
		$this->assertCount( 15, $this->imported_pages() );

		$root = $this->page( 'Dati della ricerca', 'it' );
		$this->assertSame( 0, $root->post_parent );
		$this->assertSame(
			array( 'Perché la gestione dei dati della ricerca è importante', 'Pianificare', 'Gestire', 'Condividere' ),
			$this->children_of( $root->ID )
		);
		$this->assertSame( array( 'Cosa sono i dati della ricerca?', 'Piano di gestione dei dati' ), $this->children_of( $this->page( 'Pianificare', 'it' )->ID ) );
		$this->assertSame( 'Contenuto di esempio (albero di navigazione a più livelli)', $root->post_excerpt );
		$this->assertSame( '', $this->page( 'Pubblicare i dati della ricerca', 'it' )->post_excerpt );
		$this->assertSame( 'dati-della-ricerca', $root->post_name );
		$this->assertSame( '1', (string) get_post_meta( $root->ID, 'mostra_sottopagine_a_card', true ) );

		$promoted = 0;
		foreach ( $this->imported_pages() as $page_id ) {
			$page = get_post( $page_id );
			$this->assertSame( 'publish', $page->post_status );
			$this->assertSame( $author, (int) $page->post_author );
			$this->assertSame( 'it', pll_get_post_language( $page_id ) );
			$this->assertSame( '', $page->post_content, 'Without lorem the new pages have no text.' );
			$this->assertNotSame( '', (string) get_post_meta( $page_id, DLI_Page_File_Importer::META_KEY, true ), 'Each page carries its identity.' );
			$promoted += '1' === (string) get_post_meta( $page_id, 'promuovi_in_home', true ) ? 1 : 0;
		}
		$this->assertSame( 7, $promoted );
		$this->assertSame( 'research-data', get_post_meta( $root->ID, DLI_Page_File_Importer::META_KEY, true ), 'The identity is the key of the file.' );
		$this->assertCount( 4, dli_get_subpage_cards( $root->ID ) );
	}

	/**
	 * With lorem the new pages have a text, always the same for the same key; without it they have none.
	 */
	public function test_lorem_is_written_in_the_new_pages_only() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );

		DLI_Page_File_Importer::import( array_slice( $pages, 0, 2 ), 'it', true, $author );

		$open_access = $this->page( 'Open Access', 'it' );
		$this->assertSame( DLI_Page_File_Importer::lorem_html( 'open-access' ), $open_access->post_content );
		$this->assertNotSame( '', $this->page( 'Dati della ricerca', 'it' )->post_content );

		// The same file again with lorem on, after the text of a page was emptied: the existing page is never written.
		wp_update_post(
			array(
				'ID'           => $open_access->ID,
				'post_content' => '',
			)
		);
		$again = DLI_Page_File_Importer::import( array_slice( $pages, 0, 3 ), 'it', true, $author );

		$this->assertSame( 2, $again['counts']['skipped'] );
		$this->assertSame( 1, $again['counts']['created'] );
		$this->assertSame( '', get_post( $open_access->ID )->post_content, 'An existing page is left as it is, empty too.' );
	}

	/**
	 * Running the import again creates nothing and leaves the pages as they are.
	 */
	public function test_running_again_skips_the_pages_already_imported() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		DLI_Page_File_Importer::import( $pages, 'it', false, $author );
		$count = $this->count_pages();

		$plan = DLI_Page_File_Importer::plan( $pages, 'it' );
		$this->assertSame( array_fill( 0, 15, 'skipped' ), $this->actions( $plan ) );

		$again = DLI_Page_File_Importer::import( $pages, 'it', false, $author );
		$this->assertSame( array( 'created' => 0, 'skipped' => 15, 'errors' => 0 ), $again['counts'] );
		$this->assertSame( $count, $this->count_pages() );
	}

	/**
	 * A file that adds pages to a tree already imported puts them under the pages that exist.
	 */
	public function test_pages_added_later_go_under_the_pages_already_imported() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		DLI_Page_File_Importer::import( array_slice( $pages, 0, 4 ), 'it', false, $author );

		$result = DLI_Page_File_Importer::import( $pages, 'it', false, $author );

		$this->assertSame( array( 'created' => 11, 'skipped' => 4, 'errors' => 0 ), $result['counts'] );
		$this->assertSame( array( 'Cosa sono i dati della ricerca?', 'Piano di gestione dei dati' ), $this->children_of( $this->page( 'Pianificare', 'it' )->ID ) );
		$this->assertCount( 15, $this->imported_pages() );
	}

	/**
	 * Without keys the identity is the path of the titles: the file is still imported once.
	 */
	public function test_pages_without_a_key_are_recognised_by_their_path() {
		$author  = $this->login_administrator();
		$content = "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\nUno;;;1;0;0\n;Due;;1;0;0\n";
		$pages   = $this->pages_from( $content );

		DLI_Page_File_Importer::import( $pages, 'it', false, $author );
		$again = DLI_Page_File_Importer::import( $pages, 'it', false, $author );

		$this->assertSame( array( 'created' => 0, 'skipped' => 2, 'errors' => 0 ), $again['counts'] );
		$this->assertSame( 'uno / due', get_post_meta( $this->page( 'Due', 'it' )->ID, DLI_Page_File_Importer::META_KEY, true ) );
	}

	/**
	 * The two example files make the example site (four main pages, seven promoted, one with the cards, per language), and the two languages imported one after the other are linked as translations.
	 */
	public function test_languages_imported_apart_are_linked_by_key() {
		$this->add_english();
		$author = $this->login_administrator();

		DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );
		$english = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-en.csv' ), 'en', false, $author );

		$this->assertSame( array( 'created' => 15, 'skipped' => 0, 'errors' => 0 ), $english['counts'] );
		$this->assertCount( 30, $this->imported_pages() );

		// The example site: four main pages, seven promoted to the home and one with the cards, in each language.
		$roots    = 0;
		$promoted = 0;
		$cards    = 0;
		foreach ( $this->imported_pages() as $page_id ) {
			$roots    += get_post( $page_id )->post_parent ? 0 : 1;
			$promoted += '1' === (string) get_post_meta( $page_id, 'promuovi_in_home', true ) ? 1 : 0;
			$cards    += '1' === (string) get_post_meta( $page_id, 'mostra_sottopagine_a_card', true ) ? 1 : 0;
		}
		$this->assertSame( 8, $roots, 'Four main pages per language.' );
		$this->assertSame( 14, $promoted, 'Seven pages per language are promoted to the home.' );
		$this->assertSame( 2, $cards, 'One page per language shows its subpages as cards.' );

		$italian_root = $this->page( 'Dati della ricerca', 'it' );
		$english_root = $this->page( 'Research Data', 'en' );
		$this->assertSame(
			array(
				'it' => $italian_root->ID,
				'en' => $english_root->ID,
			),
			pll_get_post_translations( $italian_root->ID )
		);
		foreach ( $this->imported_pages() as $page_id ) {
			$this->assertCount( 2, pll_get_post_translations( $page_id ), 'Every page has its translation.' );
		}
		$this->assertSame( 'open-access-en', $this->page( 'Open Access', 'en' )->post_name, 'The address that the file names for the English page.' );
		$this->assertSame( 'open-access', $this->page( 'Open Access', 'it' )->post_name );
	}

	/**
	 * The order of the two imports does not matter: English first, then Italian, they are linked the same.
	 */
	public function test_languages_imported_in_the_other_order_are_linked_too() {
		$this->add_english();
		$author = $this->login_administrator();

		DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-en.csv' ), 'en', false, $author );
		DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );

		foreach ( $this->imported_pages() as $page_id ) {
			$this->assertCount( 2, pll_get_post_translations( $page_id ) );
		}
	}

	/**
	 * Pages without a key are never linked: the key is the only thing that ties two files.
	 */
	public function test_pages_without_a_key_are_not_linked() {
		$this->add_english();
		$author = $this->login_administrator();
		$header = "level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards\n";

		DLI_Page_File_Importer::import( $this->pages_from( $header . "Uno;;;1;0;0\n" ), 'it', false, $author );
		DLI_Page_File_Importer::import( $this->pages_from( $header . "One;;;1;0;0\n" ), 'en', false, $author );

		$this->assertCount( 1, pll_get_post_translations( $this->page( 'Uno', 'it' )->ID ) );
		$this->assertCount( 1, pll_get_post_translations( $this->page( 'One', 'en' )->ID ) );
	}

	/**
	 * A page of the site with the same address blocks everything: whatever its status, and without being adopted.
	 */
	public function test_an_address_already_used_blocks_the_import() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );

		foreach ( array( 'publish', 'draft', 'private', 'pending' ) as $status ) {
			$page_id = self::factory()->post->create(
				array(
					'post_type'   => 'page',
					'post_status' => $status,
					'post_title'  => 'Open Access',
					'post_name'   => 'open-access',
				)
			);
			dli_set_post_language( $page_id, 'it' );
			$before = $this->count_pages();

			$plan = DLI_Page_File_Importer::plan( $pages, 'it' );
			$this->assertSame( array( 'path_in_use' ), $this->error_codes( $plan ), "A $status page." );
			$this->assertSame( 2, $plan['errors'][0]['line'] );
			$this->assertSame( (string) $page_id, $plan['errors'][0]['detail'], 'The error says which page.' );

			$result = DLI_Page_File_Importer::import( $pages, 'it', false, $author );
			$this->assertSame( array( 'path_in_use' ), $this->error_codes( $result ) );
			$this->assertSame( $before, $this->count_pages(), 'Nothing was written, not even the pages without a conflict.' );
			$this->assertSame( array(), $this->imported_pages() );

			wp_delete_post( $page_id, true );
		}
	}

	/**
	 * A page that was moved to the bin has already given its address back (WordPress renames it): it does not block.
	 */
	public function test_a_page_moved_to_the_bin_does_not_block_its_address() {
		$author  = $this->login_administrator();
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Open Access',
				'post_name'  => 'open-access',
			)
		);
		dli_set_post_language( $page_id, 'it' );
		wp_trash_post( $page_id );

		$result = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( 15, $result['counts']['created'] );
		$this->assertSame( 'open-access', $this->page( 'Open Access', 'it' )->post_name );
	}

	/**
	 * A page of the old example tool is not adopted either: it is a page with the same address.
	 */
	public function test_a_page_of_the_old_example_tool_is_a_conflict() {
		$author  = $this->login_administrator();
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Open Access',
				'post_name'  => 'open-access',
			)
		);
		dli_set_post_language( $page_id, 'it' );
		update_post_meta( $page_id, '_dli_example_content', '1' );

		$result = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );

		$this->assertSame( array( 'path_in_use' ), $this->error_codes( $result ) );
		$this->assertSame( array(), $this->imported_pages() );
		$this->assertSame( '', (string) get_post_meta( $page_id, DLI_Page_File_Importer::META_KEY, true ), 'The page was not marked as imported.' );
	}

	/**
	 * The address used by a page of another language is a conflict too: two pages cannot have the same address.
	 */
	public function test_an_address_used_by_another_language_blocks_the_import() {
		$this->add_english();
		$author  = $this->login_administrator();
		$page_id = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Open Access',
				'post_name'  => 'open-access',
			)
		);
		dli_set_post_language( $page_id, 'en' );

		$result = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );

		$this->assertSame( array( 'path_in_use' ), $this->error_codes( $result ) );
		$this->assertSame( array(), $this->imported_pages() );
	}

	/**
	 * Under a page that exists, a subpage with the address of another one is a conflict.
	 */
	public function test_a_conflict_under_a_page_that_exists_is_found() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		DLI_Page_File_Importer::import( array_slice( $pages, 0, 2 ), 'it', false, $author );
		$root_id = $this->page( 'Dati della ricerca', 'it' )->ID;
		$manual  = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'Pianificare',
				'post_name'   => 'pianificare',
				'post_parent' => $root_id,
			)
		);
		dli_set_post_language( $manual, 'it' );

		$plan = DLI_Page_File_Importer::plan( $pages, 'it' );

		$this->assertSame( array( 'path_in_use' ), $this->error_codes( $plan ) );
		$this->assertSame( 5, $plan['errors'][0]['line'] );
	}

	/**
	 * A page that was imported and then put in the bin is not created again: it is an error to be solved by hand.
	 */
	public function test_a_page_imported_and_then_trashed_is_not_created_again() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		DLI_Page_File_Importer::import( array_slice( $pages, 0, 1 ), 'it', false, $author );
		wp_trash_post( $this->page( 'Open Access', 'it' )->ID );
		$count = $this->count_pages();

		$result = DLI_Page_File_Importer::import( $pages, 'it', false, $author );

		$this->assertSame( array( 'trashed' ), $this->error_codes( $result ) );
		$this->assertSame( $count, $this->count_pages(), 'Nothing was created.' );
	}

	/**
	 * Two pages of the same language with the same identity are not guessed between.
	 */
	public function test_an_identity_that_two_pages_have_is_ambiguous() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		DLI_Page_File_Importer::import( array_slice( $pages, 0, 1 ), 'it', false, $author );
		$copy = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Altra copia',
			)
		);
		dli_set_post_language( $copy, 'it' );
		update_post_meta( $copy, DLI_Page_File_Importer::META_KEY, 'open-access' );

		$plan = DLI_Page_File_Importer::plan( array_slice( $pages, 0, 1 ), 'it' );

		$this->assertSame( array( 'ambiguous' ), $this->error_codes( $plan ) );
	}

	/**
	 * The translation that the file would make cannot go into a group that has already a page in that language.
	 */
	public function test_a_translation_group_that_has_already_the_language_blocks_the_import() {
		$this->add_english();
		$author = $this->login_administrator();
		DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-it.csv' ), 'it', false, $author );
		$italian = $this->page( 'Open Access', 'it' );
		$manual  = self::factory()->post->create(
			array(
				'post_type'  => 'page',
				'post_title' => 'Una pagina inglese a mano',
				'post_name'  => 'una-pagina-inglese',
			)
		);
		dli_set_post_language( $manual, 'en' );
		dli_save_post_translations(
			array(
				'it' => $italian->ID,
				'en' => $manual,
			)
		);
		$count = $this->count_pages();

		$result = DLI_Page_File_Importer::import( $this->pages_of( 'pages-example-en.csv' ), 'en', false, $author );

		$this->assertSame( array( 'translation_conflict' ), $this->error_codes( $result ) );
		$this->assertSame( $count, $this->count_pages(), 'Nothing was created.' );
	}

	/**
	 * A failure while writing stops the import: what was created stays and is reported, the rest is not created, and
	 * running the import again goes on from there.
	 */
	public function test_a_failure_while_writing_stops_and_the_next_run_goes_on() {
		$author = $this->login_administrator();
		$pages  = $this->pages_of( 'pages-example-it.csv' );
		$refuse = static function ( $empty, $postarr ) {
			return 'Pianificare' === ( $postarr['post_title'] ?? '' ) ? true : $empty;
		};
		add_filter( 'wp_insert_post_empty_content', $refuse, 10, 2 );

		$result = DLI_Page_File_Importer::import( $pages, 'it', false, $author );

		remove_filter( 'wp_insert_post_empty_content', $refuse, 10 );
		$this->assertSame( array( 'insert_failed' ), $this->error_codes( $result ) );
		$this->assertSame( 5, $result['errors'][0]['line'], 'The error is on the line of the page that failed.' );
		$this->assertTrue( $result['written'] );
		$this->assertSame( array( 'created' => 3, 'skipped' => 0, 'errors' => 1 ), $result['counts'] );
		$this->assertSame( array_merge( array( 'created', 'created', 'created', 'error' ), array_fill( 0, 11, 'not_created' ) ), $this->actions( $result ) );
		$this->assertCount( 3, $this->imported_pages(), 'The pages created before the failure stay.' );

		$again = DLI_Page_File_Importer::import( $pages, 'it', false, $author );

		$this->assertSame( array( 'created' => 12, 'skipped' => 3, 'errors' => 0 ), $again['counts'] );
		$this->assertCount( 15, $this->imported_pages() );
	}

	/**
	 * Without ACF and Polylang the import is refused before anything is written.
	 */
	public function test_the_plan_checks_the_prerequisites_first() {
		$this->assertSame( '', DLI_Page_File_Importer::prerequisites_error( 'it' ) );
		$this->assertSame( 'language', DLI_Page_File_Importer::prerequisites_error( 'xx' ) );
	}
}
