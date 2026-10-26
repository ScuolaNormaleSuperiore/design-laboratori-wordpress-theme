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
}
