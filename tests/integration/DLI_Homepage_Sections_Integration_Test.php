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
	 * Only the rows that are on are active: a row without the flag is not, and each row keeps the position it has in the
	 * saved list, also when only the active ones are asked for. The normalization of the rows is in
	 * DLI_Home_Sections_Unit_Test.
	 */
	public function test_only_the_rows_that_are_on_are_active_and_keep_their_saved_position() {
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
				array( 'id' => 'site_description' ),
				array(
					'id'      => 'events_section',
					'enabled' => 'true',
				),
			)
		);

		$active = DLI_ContentsManager::get_hp_section_options( true );

		$this->assertSame( array( 'news_section', 'events_section' ), wp_list_pluck( $active, 'id' ), 'A row without the flag is not active.' );
		$this->assertSame( array( 2, 4 ), wp_list_pluck( $active, 'instance' ) );
		$this->assertCount( 4, DLI_ContentsManager::get_hp_section_options() );
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

	/**
	 * Only the sections whose copies have their own data and identifiers can be in the home more than once.
	 */
	public function test_three_sections_can_be_repeated() {
		$repeatable = array_keys( DLI_ContentsManager::get_hp_sections_repeatable() );
		sort( $repeatable );

		$this->assertSame( array( 'banners_section', 'page_index_section', 'video_section' ), $repeatable );
	}

	/**
	 * Every other section is single, and a section nobody declared as repeatable is single too.
	 */
	public function test_the_other_sections_are_single() {
		$all        = array_keys( DLI_HP_SECTIONS );
		$repeatable = array_keys( DLI_ContentsManager::get_hp_sections_repeatable() );
		$single     = DLI_ContentsManager::get_hp_sections_single();

		$this->assertSame( array(), array_values( array_intersect( $single, $repeatable ) ), 'No section is both.' );
		$this->assertEqualsCanonicalizing( $all, array_merge( $single, $repeatable ), 'Every section is one or the other.' );
		foreach ( array( 'main_hero', 'site_description', 'main_carousel', 'featured_contents', 'card_navigation_section', 'page_navigation_section', 'tabs_section', 'events_section', 'news_section', 'publications_section', 'articles_section', 'sponsors_section' ) as $id ) {
			$this->assertContains( $id, $single, "$id is single." );
		}
	}
}
