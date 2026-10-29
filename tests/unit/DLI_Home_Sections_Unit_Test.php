<?php
/**
 * Unit tests of the pure helpers of the homepage sections, in inc/home-sections.php.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Background of a section and normalization of the saved rows.
 */
class DLI_Home_Sections_Unit_Test extends TestCase {

	/**
	 * Only white and muted are backgrounds; anything else falls back to the default.
	 */
	public function test_background_accepts_only_the_two_known_values() {
		$this->assertSame( 'white', dli_normalize_hp_background( 'white', 'muted' ) );
		$this->assertSame( 'muted', dli_normalize_hp_background( 'muted', 'white' ) );
		$this->assertSame( 'muted', dli_normalize_hp_background( 'rosso', 'muted' ), 'An unknown value gives the default.' );
		$this->assertSame( 'white', dli_normalize_hp_background( '', 'white' ) );
		$this->assertSame( 'white', dli_normalize_hp_background( null, 'white' ) );
		$this->assertSame( 'white', dli_normalize_hp_background( array( 'muted' ), 'white' ), 'A non-string value gives the default.' );
		$this->assertSame( '', dli_normalize_hp_background( 'rosso' ), 'Without default, an unknown value is empty.' );
	}

	/**
	 * The class is the one of the muted background, or nothing.
	 */
	public function test_background_class_follows_the_row_or_the_default_of_the_section() {
		$this->assertSame( ' section-muted', dli_get_hp_section_background_class( array( 'background' => 'muted' ), 'white' ) );
		$this->assertSame( '', dli_get_hp_section_background_class( array( 'background' => 'white' ), 'muted' ), 'The choice of the row wins over the default.' );
		$this->assertSame( ' section-muted', dli_get_hp_section_background_class( array(), 'muted' ), 'A row saved before the field existed uses the default of the section.' );
		$this->assertSame( '', dli_get_hp_section_background_class( array(), 'white' ) );
		$this->assertSame( '', dli_get_hp_section_background_class( array(), 'rosso' ), 'A wrong default is white.' );
		$this->assertSame( ' section-muted', dli_get_hp_section_background_class( array( 'background' => 'rosso' ), 'muted' ), 'A wrong value of the row falls back to the default.' );
		$this->assertSame( '', dli_get_hp_section_background_class( 'non un array' ) );
	}

	/**
	 * A value saved in the options page is sanitized to a known background or to nothing.
	 */
	public function test_sanitization_of_the_background_field() {
		$this->assertSame( 'muted', dli_sanitize_hp_background( 'muted' ) );
		$this->assertSame( 'white', dli_sanitize_hp_background( 'white' ) );
		$this->assertSame( '', dli_sanitize_hp_background( '<script>' ) );
		$this->assertSame( '', dli_sanitize_hp_background( '' ) );
		$this->assertSame( '', dli_sanitize_hp_background( null ) );
	}

	/**
	 * Rows that are not rows, or have no section, are left out; the others keep order and data.
	 */
	public function test_rows_without_a_section_are_left_out() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'         => 'main_hero',
					'enabled'    => 'true',
					'show_title' => 'true',
				),
				'non una riga',
				array(
					'enabled' => 'true',
				),
				array(
					'id'      => '',
					'enabled' => 'true',
				),
				array(
					'id'      => 'news_section',
					'enabled' => 'false',
				),
			)
		);

		$this->assertSame( array( 'main_hero', 'news_section' ), array_column( $rows, 'id' ) );
	}

	/**
	 * A missing or unknown flag is "false", like before; nothing is guessed to be on.
	 */
	public function test_missing_flags_are_false() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array( 'id' => 'main_hero' ),
				array(
					'id'         => 'news_section',
					'enabled'    => 'forse',
					'show_title' => 1,
				),
			)
		);

		$this->assertSame( 'false', $rows[0]['enabled'] );
		$this->assertSame( 'false', $rows[0]['show_title'] );
		$this->assertSame( 'false', $rows[1]['enabled'] );
		$this->assertSame( 'false', $rows[1]['show_title'] );
	}

	/**
	 * Every row carries the position it has in the saved list, also when rows are left out around it.
	 */
	public function test_instance_is_the_saved_position() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array( 'id' => 'main_hero' ),
				'non una riga',
				array( 'id' => 'news_section' ),
			)
		);

		$this->assertSame( 1, $rows[0]['instance'] );
		$this->assertSame( 3, $rows[1]['instance'], 'The position counts the row that was left out, so it does not move when a row is fixed.' );
	}

	/**
	 * Extra properties are kept; a wrong background is dropped, a right one is kept.
	 */
	public function test_extra_properties_are_kept_and_background_is_checked() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'         => 'main_hero',
					'enabled'    => 'true',
					'show_title' => 'true',
					'background' => 'muted',
					'page_id'    => '42',
				),
				array(
					'id'         => 'news_section',
					'enabled'    => 'true',
					'show_title' => 'true',
					'background' => 'rosso',
				),
			)
		);

		$this->assertSame( 'muted', $rows[0]['background'] );
		$this->assertSame( '42', $rows[0]['page_id'] );
		$this->assertArrayNotHasKey( 'background', $rows[1], 'A wrong background is not passed to the template.' );
	}

	/**
	 * Anything that is not a list of rows gives an empty list.
	 */
	public function test_not_a_list_gives_an_empty_list() {
		$this->assertSame( array(), dli_normalize_hp_section_rows( '' ) );
		$this->assertSame( array(), dli_normalize_hp_section_rows( false ) );
		$this->assertSame( array(), dli_normalize_hp_section_rows( null ) );
		$this->assertSame( array(), dli_normalize_hp_section_rows( array() ) );
	}
}
