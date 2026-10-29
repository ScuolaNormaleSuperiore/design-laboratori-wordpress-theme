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

	/**
	 * The columns of the cards of "Card di navigazione" follow how many there are, up to six.
	 */
	public function test_navigation_columns_follow_the_number_of_cards() {
		$this->assertSame( 'col-12 col-lg-6', dli_get_hp_navigation_column_class( 1 ) );
		$this->assertSame( 'col-12 col-md-6', dli_get_hp_navigation_column_class( 2 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-4', dli_get_hp_navigation_column_class( 3 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-3', dli_get_hp_navigation_column_class( 4 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-4', dli_get_hp_navigation_column_class( 5 ), 'Three and two: the last row is centered by the template.' );
		$this->assertSame( 'col-12 col-md-6 col-lg-4', dli_get_hp_navigation_column_class( 6 ) );
	}

	/**
	 * Zero, negative or more than six cards still give a usable class.
	 */
	public function test_navigation_columns_for_out_of_range_numbers() {
		$this->assertSame( 'col-12 col-lg-6', dli_get_hp_navigation_column_class( 0 ) );
		$this->assertSame( 'col-12 col-lg-6', dli_get_hp_navigation_column_class( -3 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-4', dli_get_hp_navigation_column_class( 9 ) );
	}

	/**
	 * The number of items is a whole number between the minimum and the maximum, otherwise the default.
	 */
	public function test_max_items_is_a_whole_number_in_range() {
		$this->assertSame( 4, dli_normalize_hp_max_items( 4, 6 ) );
		$this->assertSame( 4, dli_normalize_hp_max_items( '4', 6 ), 'A number saved as text.' );
		$this->assertSame( 6, dli_normalize_hp_max_items( '', 6 ), 'Empty gives the default.' );
		$this->assertSame( 6, dli_normalize_hp_max_items( null, 6 ) );
		$this->assertSame( 6, dli_normalize_hp_max_items( 'molte', 6 ), 'Not a number gives the default.' );
		$this->assertSame( 6, dli_normalize_hp_max_items( '3.5', 6 ), 'A number with decimals is not a whole number.' );
		$this->assertSame( 6, dli_normalize_hp_max_items( array( 3 ), 6 ) );
		$this->assertSame( DLI_HP_ITEMS_MIN, dli_normalize_hp_max_items( 0, 6 ), 'Below the minimum gives the minimum.' );
		$this->assertSame( DLI_HP_ITEMS_MIN, dli_normalize_hp_max_items( -5, 6 ) );
		$this->assertSame( DLI_HP_ITEMS_MAX, dli_normalize_hp_max_items( 500, 6 ), 'Above the maximum gives the maximum.' );
	}

	/**
	 * The field of the options page is sanitized to a number in range, or to nothing.
	 */
	public function test_sanitization_of_the_max_items_field() {
		$this->assertSame( 3, dli_sanitize_hp_max_items( '3' ) );
		$this->assertSame( DLI_HP_ITEMS_MAX, dli_sanitize_hp_max_items( '999' ) );
		$this->assertSame( DLI_HP_ITEMS_MIN, dli_sanitize_hp_max_items( '0' ) );
		$this->assertSame( '', dli_sanitize_hp_max_items( '' ), 'Empty stays empty: the section uses its default.' );
		$this->assertSame( '', dli_sanitize_hp_max_items( 'abc' ) );
		$this->assertSame( '', dli_sanitize_hp_max_items( null ) );
	}

	/**
	 * A row keeps a valid number of items, in range; a wrong one is dropped so that the default applies.
	 */
	public function test_rows_keep_a_valid_number_of_items() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'        => 'card_navigation_section',
					'max_items' => '4',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_items' => '900',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_items' => 'tanti',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_items' => '',
				),
				array( 'id' => 'card_navigation_section' ),
			)
		);

		$this->assertSame( 4, $rows[0]['max_items'] );
		$this->assertSame( DLI_HP_ITEMS_MAX, $rows[1]['max_items'] );
		$this->assertArrayNotHasKey( 'max_items', $rows[2] );
		$this->assertArrayNotHasKey( 'max_items', $rows[3] );
		$this->assertArrayNotHasKey( 'max_items', $rows[4], 'A row saved before the field existed is not given one.' );
	}

	/**
	 * The columns of the groups of "Navigazione per pagine" follow how many groups there are, up to four per row.
	 */
	public function test_navigation_groups_columns_follow_the_number_of_groups() {
		$this->assertSame( 'col-12 col-md-8 col-lg-6', dli_get_hp_groups_column_class( 1 ) );
		$this->assertSame( 'col-12 col-md-6', dli_get_hp_groups_column_class( 2 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-4', dli_get_hp_groups_column_class( 3 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-3', dli_get_hp_groups_column_class( 4 ) );
		$this->assertSame( 'col-12 col-md-6 col-lg-3', dli_get_hp_groups_column_class( 9 ), 'More than four: four per row, then the next row.' );
		$this->assertSame( 'col-12 col-md-8 col-lg-6', dli_get_hp_groups_column_class( 0 ) );
	}

	/**
	 * A row keeps a valid number of sub-items per group, like it keeps the number of items.
	 */
	public function test_rows_keep_a_valid_number_of_subitems() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'           => 'page_navigation_section',
					'max_subitems' => '3',
				),
				array(
					'id'           => 'page_navigation_section',
					'max_subitems' => '900',
				),
				array(
					'id'           => 'page_navigation_section',
					'max_subitems' => 'tante',
				),
				array(
					'id'           => 'page_navigation_section',
					'max_subitems' => '',
				),
				array( 'id' => 'page_navigation_section' ),
			)
		);

		$this->assertSame( 3, $rows[0]['max_subitems'] );
		$this->assertSame( DLI_HP_ITEMS_MAX, $rows[1]['max_subitems'] );
		$this->assertArrayNotHasKey( 'max_subitems', $rows[2] );
		$this->assertArrayNotHasKey( 'max_subitems', $rows[3] );
		$this->assertArrayNotHasKey( 'max_subitems', $rows[4] );
	}
}
