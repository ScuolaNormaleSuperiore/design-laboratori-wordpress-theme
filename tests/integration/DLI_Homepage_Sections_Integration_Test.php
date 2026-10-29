<?php
/**
 * Integration tests of the homepage sections configuration helpers.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The sections saved in the options page are returned all together or only the enabled ones.
 */
class DLI_Homepage_Sections_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Save two sections, one enabled and one disabled, as the options page does.
	 */
	public function set_up() {
		parent::set_up();

		$this->set_theme_options(
			'homepage_sections',
			array(
				'site_sections' => array(
					array(
						'id'         => 'main_hero',
						'enabled'    => 'true',
						'show_title' => 'false',
					),
					array(
						'id'         => 'site_description',
						'enabled'    => 'false',
						'show_title' => 'true',
					),
				),
			)
		);
	}

	/**
	 * Without the flag every configured section is returned, enabled or not.
	 */
	public function test_all_sections_are_returned_by_default() {
		$ids = wp_list_pluck( DLI_ContentsManager::get_hp_section_options(), 'id' );

		$this->assertSame( array( 'main_hero', 'site_description' ), $ids );
	}

	/**
	 * With the flag only the enabled sections are returned, as the home page template asks.
	 */
	public function test_only_enabled_sections_are_returned_when_requested() {
		$ids = wp_list_pluck( DLI_ContentsManager::get_hp_section_options( true ), 'id' );

		$this->assertSame( array( 'main_hero' ), $ids );
	}

	/**
	 * Replace the saved sections, as the options page does.
	 *
	 * @param array<int, mixed> $rows The rows of `site_sections`.
	 */
	private function save_sections( array $rows ) {
		$this->set_theme_options( 'homepage_sections', array( 'site_sections' => $rows ) );
	}

	/**
	 * Rows that are not rows, or without a section, are ignored without warnings.
	 */
	public function test_malformed_rows_are_ignored() {
		$this->save_sections(
			array(
				array(
					'id'         => 'main_hero',
					'enabled'    => 'true',
					'show_title' => 'true',
				),
				'non una riga',
				array( 'enabled' => 'true' ),
				array(
					'id'      => 'news_section',
					'enabled' => 'true',
				),
			)
		);

		$rows = DLI_ContentsManager::get_hp_section_options( true );

		$this->assertSame( array( 'main_hero', 'news_section' ), wp_list_pluck( $rows, 'id' ) );
		$this->assertSame( 'false', $rows[1]['show_title'], 'A row saved without the flag does not show the title.' );
	}

	/**
	 * A row missing `enabled` is not shown, as it was before: nothing is guessed to be on.
	 */
	public function test_a_row_without_the_enabled_flag_is_not_active() {
		$this->save_sections( array( array( 'id' => 'main_hero' ) ) );

		$this->assertSame( array(), DLI_ContentsManager::get_hp_section_options( true ) );
		$this->assertCount( 1, DLI_ContentsManager::get_hp_section_options() );
	}

	/**
	 * Each row has the position it has in the saved list, also when only the active ones are asked for.
	 */
	public function test_instance_is_the_position_in_the_saved_list() {
		$this->save_sections(
			array(
				array(
					'id'      => 'main_hero',
					'enabled' => 'false',
				),
				array(
					'id'      => 'news_section',
					'enabled' => 'true',
				),
				array(
					'id'      => 'events_section',
					'enabled' => 'true',
				),
			)
		);

		$active = DLI_ContentsManager::get_hp_section_options( true );

		$this->assertSame( array( 2, 3 ), wp_list_pluck( $active, 'instance' ) );
	}

	/**
	 * Old rows (without background) keep their data and their order; the background is checked.
	 */
	public function test_old_rows_are_kept_and_background_is_checked() {
		$this->save_sections(
			array(
				array(
					'id'         => 'main_hero',
					'enabled'    => 'true',
					'show_title' => 'false',
				),
				array(
					'id'         => 'news_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'background' => 'muted',
					'page_id'    => '42',
				),
				array(
					'id'         => 'events_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'background' => 'rosso',
				),
			)
		);

		$rows = DLI_ContentsManager::get_hp_section_options();

		$this->assertArrayNotHasKey( 'background', $rows[0] );
		$this->assertSame( 'muted', $rows[1]['background'] );
		$this->assertSame( '42', $rows[1]['page_id'], 'Extra properties reach the template.' );
		$this->assertArrayNotHasKey( 'background', $rows[2], 'A wrong background is not passed on.' );
	}

	/**
	 * Reading the sections never writes them back: a save without changes keeps the old rows as they are.
	 */
	public function test_reading_does_not_rewrite_the_saved_options() {
		$this->save_sections(
			array(
				array( 'id' => 'main_hero' ),
				'non una riga',
			)
		);
		$before = get_option( 'homepage_sections' );

		DLI_ContentsManager::get_hp_section_options();
		DLI_ContentsManager::get_hp_section_options( true );

		$this->assertSame( $before, get_option( 'homepage_sections' ) );
	}

	/**
	 * The home page prints the known sections and ignores the unknown ones, passing every property of the row.
	 */
	public function test_home_prints_known_sections_and_ignores_unknown_ones() {
		$publication = $this->create_post( PUBLICATION_POST_TYPE, 'Pubblicazione della home' );
		$this->set_acf_fields( $publication, array( 'promuovi_in_home' => 1 ) );
		$this->save_sections(
			array(
				array(
					'id'         => 'sezione_che_non_esiste',
					'enabled'    => 'true',
					'show_title' => 'true',
				),
				'non una riga',
				array(
					'id'         => 'publications_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'background' => 'muted',
				),
			)
		);
		$this->go_to( home_url( '/' ) );

		$html = $this->render_theme_template( 'home.php' );

		$this->assertStringContainsString( 'id="blocco-pubblicazioni"', $html );
		$this->assertStringContainsString( 'Pubblicazione della home', $html );
		$this->assertStringNotContainsString( 'sezione_che_non_esiste', $html );
	}

	/**
	 * The sections that let the editor choose the background are sections of the catalog, with a known default.
	 */
	public function test_sections_with_background_are_catalog_sections_with_a_known_default() {
		$with_background = DLI_ContentsManager::get_hp_sections_with_background();

		$this->assertSame( array(), array_diff_key( $with_background, DLI_ContentsManager::get_hp_sections() ), 'Only sections of the catalog.' );
		foreach ( $with_background as $section_id => $default ) {
			$this->assertContains( $default, array( 'white', 'muted' ), "Default background of {$section_id}." );
		}
	}
}
