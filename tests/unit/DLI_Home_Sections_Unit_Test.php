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
					'extra'      => '42',
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
		$this->assertSame( '42', $rows[0]['extra'], 'A property that is not a known field is passed as it is.' );
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

	/**
	 * The level of the pages is one of the four known choices: 1, 2, 3, or 0 for all the levels.
	 */
	public function test_page_level_is_one_of_the_known_choices() {
		$this->assertSame( 1, dli_normalize_hp_page_level( 1 ) );
		$this->assertSame( 2, dli_normalize_hp_page_level( 2 ) );
		$this->assertSame( 3, dli_normalize_hp_page_level( 3 ) );
		$this->assertSame( 0, dli_normalize_hp_page_level( 0 ), 'Zero means all the levels.' );
		$this->assertSame( 2, dli_normalize_hp_page_level( '2' ), 'A choice saved as text.' );
		$this->assertSame( 0, dli_normalize_hp_page_level( '0' ), 'All the levels, saved as text, is not an empty value.' );
		$this->assertSame( 1, dli_normalize_hp_page_level( '' ), 'Empty gives the default.' );
		$this->assertSame( 1, dli_normalize_hp_page_level( null ) );
		$this->assertSame( 1, dli_normalize_hp_page_level( 4 ), 'A level that is not offered gives the default.' );
		$this->assertSame( 1, dli_normalize_hp_page_level( -1 ) );
		$this->assertSame( 1, dli_normalize_hp_page_level( '2.5' ) );
		$this->assertSame( 1, dli_normalize_hp_page_level( '02' ), 'Only the exact values are accepted.' );
		$this->assertSame( 1, dli_normalize_hp_page_level( 'tutti' ) );
		$this->assertSame( 1, dli_normalize_hp_page_level( array( 2 ) ) );
		$this->assertSame( 3, dli_normalize_hp_page_level( 'x', 3 ), 'The default can be asked for.' );
		$this->assertSame( 1, dli_normalize_hp_page_level( 'x', 7 ), 'A default that is not a choice is the first level.' );
	}

	/**
	 * The field of the options page keeps a known choice, or nothing.
	 */
	public function test_sanitization_of_the_page_level_field() {
		$this->assertSame( 2, dli_sanitize_hp_page_level( '2' ) );
		$this->assertSame( 0, dli_sanitize_hp_page_level( '0' ), 'All the levels is a value, not an empty one.' );
		$this->assertSame( 1, dli_sanitize_hp_page_level( 1 ) );
		$this->assertSame( '', dli_sanitize_hp_page_level( '' ), 'Empty stays empty: the section uses its default.' );
		$this->assertSame( '', dli_sanitize_hp_page_level( '9' ) );
		$this->assertSame( '', dli_sanitize_hp_page_level( '<script>' ) );
		$this->assertSame( '', dli_sanitize_hp_page_level( null ) );
	}

	/**
	 * A row keeps a valid level of the pages (also "all the levels") and drops any other value.
	 */
	public function test_rows_keep_a_valid_page_level() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'        => 'card_navigation_section',
					'max_level' => '2',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_level' => '0',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_level' => '7',
				),
				array(
					'id'        => 'card_navigation_section',
					'max_level' => '',
				),
				array( 'id' => 'card_navigation_section' ),
			)
		);

		$this->assertSame( 2, $rows[0]['max_level'] );
		$this->assertSame( 0, $rows[1]['max_level'], 'All the levels is kept.' );
		$this->assertArrayNotHasKey( 'max_level', $rows[2] );
		$this->assertArrayNotHasKey( 'max_level', $rows[3] );
		$this->assertArrayNotHasKey( 'max_level', $rows[4], 'A row saved before the field existed is not given one.' );
	}

	/**
	 * The value of the language of the page, English falling back to Italian only when it is blank.
	 */
	public function test_pick_lang_value_falls_back_from_english_to_italian() {
		$this->assertSame( 'Ciao', dli_pick_lang_value( 'Ciao', 'Hello', true ), 'An Italian page uses the Italian value.' );
		$this->assertSame( 'Hello', dli_pick_lang_value( 'Ciao', 'Hello', false ), 'An English page uses the English value.' );
		$this->assertSame( 'Ciao', dli_pick_lang_value( 'Ciao', '', false ), 'A missing English value gives the Italian one.' );
		$this->assertSame( 'Ciao', dli_pick_lang_value( 'Ciao', "  \n", false ), 'A blank English value gives the Italian one.' );
		$this->assertSame( 'Ciao', dli_pick_lang_value( 'Ciao', '<p>&nbsp;</p>', false ), 'An empty paragraph is a missing value.' );
		$this->assertSame( '', dli_pick_lang_value( '', 'Hello', true ), 'English never replaces a missing Italian value.' );
		$this->assertSame( '', dli_pick_lang_value( null, null, false ) );
		$this->assertSame( '', dli_pick_lang_value( array( 'x' ), 'Hello', true ), 'A value that is not text is empty.' );
	}

	/**
	 * Text without visible content, however it is written.
	 */
	public function test_blank_text_has_no_visible_content() {
		$this->assertTrue( dli_is_blank_text( '' ) );
		$this->assertTrue( dli_is_blank_text( null ) );
		$this->assertTrue( dli_is_blank_text( "  \n\t" ) );
		$this->assertTrue( dli_is_blank_text( '<p></p>' ) );
		$this->assertTrue( dli_is_blank_text( '<p>&nbsp;</p><br />' ) );
		$this->assertTrue( dli_is_blank_text( array( 'x' ) ), 'A value that is not text is blank.' );
		$this->assertFalse( dli_is_blank_text( 'Testo' ) );
		$this->assertFalse( dli_is_blank_text( '<p>Testo</p>' ) );
		$this->assertFalse( dli_is_blank_text( '0' ), 'The text "0" is text.' );
	}

	/**
	 * The pages linked to a tab: whole positive numbers, no repeats, the saved order.
	 */
	public function test_tab_page_ids_are_positive_unique_numbers() {
		$this->assertSame( array( 12, 7, 30 ), dli_sanitize_hp_tab_page_ids( array( '12', 7, '12', 30 ) ) );
		$this->assertSame( array( 5 ), dli_sanitize_hp_tab_page_ids( array( 0, '-3', 'abc', 5, 'x7', 5.0 ) ), 'Zero, negative and non numeric values are dropped.' );
		$this->assertSame( array(), dli_sanitize_hp_tab_page_ids( array() ) );
		$this->assertSame( array(), dli_sanitize_hp_tab_page_ids( '12' ), 'A single value that is not a list is no page.' );
		$this->assertSame( array(), dli_sanitize_hp_tab_page_ids( null ) );
		$this->assertSame( array(), dli_sanitize_hp_tab_page_ids( array( array( 3 ), true ) ), 'Nested and boolean values are dropped.' );
	}

	/**
	 * The saved tabs, safe to use: rows that are not rows are left out, the others keep their order.
	 */
	public function test_tab_rows_are_safe_and_keep_their_order() {
		$rows = dli_normalize_hp_tab_rows(
			array(
				'not a row',
				array(
					'etichetta'       => 'Aree',
					'etichetta_eng'   => 'Areas',
					'descrizione'     => '<p>Testo</p>',
					'descrizione_eng' => '<p>Text</p>',
					'pagine'          => array( '4', 4, '9' ),
				),
				array( 'etichetta' => 'Solo etichetta' ),
				array(
					'etichetta'   => array( 'x' ),
					'descrizione' => 12,
					'pagine'      => 'non una lista',
				),
			)
		);

		$this->assertCount( 3, $rows, 'The value that is not a row is left out.' );
		$this->assertSame(
			array(
				'label'       => 'Aree',
				'label_eng'   => 'Areas',
				'text'        => '<p>Testo</p>',
				'text_eng'    => '<p>Text</p>',
				'page_ids'    => array( 4, 9 ),
			),
			$rows[0]
		);
		$this->assertSame( 'Solo etichetta', $rows[1]['label'] );
		$this->assertSame( '', $rows[1]['label_eng'], 'A missing value is an empty string.' );
		$this->assertSame( array(), $rows[1]['page_ids'] );
		$this->assertSame( '', $rows[2]['label'], 'A value that is not text is empty.' );
		$this->assertSame( '', $rows[2]['text'] );
		$this->assertSame( array(), $rows[2]['page_ids'] );
		$this->assertSame( array(), dli_normalize_hp_tab_rows( 'non una lista' ) );
		$this->assertSame( array(), dli_normalize_hp_tab_rows( false ), 'An option never saved gives no tab.' );
	}

	/**
	 * Which tabs are shown in a language: the label is what makes a tab, the text or the pages are what keep it.
	 */
	public function test_tabs_for_a_language_follow_the_rules_of_the_section() {
		$rows = dli_normalize_hp_tab_rows(
			array(
				array(
					'etichetta'       => 'Uno',
					'etichetta_eng'   => 'One',
					'descrizione'     => '<p>Testo uno</p>',
					'descrizione_eng' => '<p>Text one</p>',
				),
				array(
					'etichetta'   => 'Due',
					'descrizione' => '<p>Testo due</p>',
				),
				array(
					'etichetta'   => '',
					'descrizione' => '<p>Senza etichetta</p>',
				),
				array(
					'etichetta'   => 'Vuoto',
					'descrizione' => '<p>&nbsp;</p>',
				),
				array(
					'etichetta' => 'Con pagine',
					'pagine'    => array( 8 ),
				),
			)
		);

		$italian = dli_get_hp_tabs_for_language( $rows, true );
		$this->assertSame( array( 'Uno', 'Due', 'Con pagine' ), array_column( $italian, 'label' ), 'A row without a label is not a tab, and a tab without text and without pages is left out.' );
		$this->assertSame( '<p>Testo uno</p>', $italian[0]['text'] );

		$english = dli_get_hp_tabs_for_language( $rows, false );
		$this->assertSame( array( 'One', 'Due', 'Con pagine' ), array_column( $english, 'label' ), 'A missing English label is the Italian one.' );
		$this->assertSame( '<p>Text one</p>', $english[0]['text'] );
		$this->assertSame( '<p>Testo due</p>', $english[1]['text'], 'A missing English text is the Italian one.' );
		$this->assertSame( array( 8 ), $english[2]['page_ids'] );
	}

	/**
	 * The renderer of a box follows its content type; a type without a dedicated one has the essential fallback.
	 */
	public function test_featured_renderer_follows_the_content_type() {
		$this->assertSame( 'card-notizia', dli_get_featured_renderer( 'notizia' ) );
		$this->assertSame( 'card-evento', dli_get_featured_renderer( 'evento' ) );
		$this->assertSame( 'card-pubblicazione', dli_get_featured_renderer( 'pubblicazione' ) );
		$this->assertSame( 'card-fallback', dli_get_featured_renderer( 'progetto' ), 'A type without a renderer has the fallback.' );
		$this->assertSame( 'card-fallback', dli_get_featured_renderer( '' ) );
		$this->assertSame( 'card-fallback', dli_get_featured_renderer( null ) );
		$this->assertSame( 'card-fallback', dli_get_featured_renderer( array( 'notizia' ) ), 'A value that is not text has the fallback.' );
	}

	/**
	 * The boxes share the row: one is full width, two take half each, three a third; never an empty column.
	 */
	public function test_featured_columns_follow_the_number_of_boxes() {
		$this->assertSame( 'col-12', dli_get_featured_column_class( 1 ) );
		$this->assertSame( 'col-12 col-lg-6', dli_get_featured_column_class( 2 ) );
		$this->assertSame( 'col-12 col-lg-4', dli_get_featured_column_class( 3 ) );
		$this->assertSame( 'col-12', dli_get_featured_column_class( 0 ), 'No box is the full width, for safety.' );
		$this->assertSame( 'col-12 col-lg-4', dli_get_featured_column_class( 7 ), 'More than three boxes are three per row.' );
	}

	/**
	 * A saved box: the content type and the title in the language of the page, English falling back to Italian.
	 */
	public function test_featured_box_is_read_from_the_saved_group() {
		$option = array(
			0 => array(
				'featured_contents_label_box_2'                              => 'Eventi',
				'featured_contents_label_box_2' . DLI_ENG_SUFFIX_LANGUAGE    => 'Events',
				'featured_contents_type_box_2'                               => 'evento',
				'featured_contents_template_box_2'                           => 'card-notizia',
			),
		);

		$this->assertSame(
			array(
				'type'  => 'evento',
				'title' => 'Eventi',
			),
			dli_normalize_featured_box( $option, 2, true )
		);
		$this->assertSame( 'Events', dli_normalize_featured_box( $option, 2, false )['title'] );

		unset( $option[0][ 'featured_contents_label_box_2' . DLI_ENG_SUFFIX_LANGUAGE ] );
		$this->assertSame( 'Eventi', dli_normalize_featured_box( $option, 2, false )['title'], 'A missing English title is the Italian one.' );

		$option[0]['featured_contents_label_box_2'] = '';
		$this->assertSame( '', dli_normalize_featured_box( $option, 2, true )['title'], 'An empty title stays empty: the section gives the default.' );
	}

	/**
	 * A box without a usable content type is not a box.
	 */
	public function test_featured_box_needs_a_content_type() {
		$this->assertNull( dli_normalize_featured_box( false, 1, true ), 'An option never saved.' );
		$this->assertNull( dli_normalize_featured_box( array(), 1, true ) );
		$this->assertNull( dli_normalize_featured_box( array( 'not a row' ), 1, true ) );
		$this->assertNull( dli_normalize_featured_box( array( array( 'featured_contents_label_box_1' => 'Solo titolo' ) ), 1, true ), 'No type.' );
		$this->assertNull( dli_normalize_featured_box( array( array( 'featured_contents_type_box_1' => '' ) ), 1, true ), 'An empty type.' );
		$this->assertNull( dli_normalize_featured_box( array( array( 'featured_contents_type_box_1' => array( 'notizia' ) ) ), 1, true ), 'A type that is not text.' );
		$this->assertNull( dli_normalize_featured_box( array( array( 'featured_contents_type_box_2' => 'notizia' ) ), 1, true ), 'The keys of another box are not read.' );
	}

	/**
	 * The page chosen in a row of Sezioni HP is a whole positive number, or nothing.
	 */
	public function test_page_id_is_a_positive_whole_number_or_nothing() {
		$this->assertSame( 42, dli_sanitize_hp_page_id( '42' ) );
		$this->assertSame( 42, dli_sanitize_hp_page_id( 42 ) );
		$this->assertSame( 42, dli_sanitize_hp_page_id( ' 042 ' ), 'Spaces and leading zeros are the same number.' );

		foreach ( array( '', '0', 0, '-3', -3, '4.5', 4.5, 'abc', '12abc', '1e3', array( 42 ), null, false, true ) as $value ) {
			$this->assertSame( '', dli_sanitize_hp_page_id( $value ), 'No page: ' . var_export( $value, true ) );
		}
	}

	/**
	 * A row keeps a valid page, drops a wrong one, and a row saved before the field existed is not given one.
	 */
	public function test_rows_keep_a_valid_page_id_and_drop_the_others() {
		$rows = dli_normalize_hp_section_rows(
			array(
				array(
					'id'      => 'page_index_section',
					'page_id' => '17',
				),
				array(
					'id'      => 'page_index_section',
					'page_id' => 'abc',
				),
				array(
					'id'      => 'page_index_section',
					'page_id' => '',
				),
				array( 'id' => 'page_index_section' ),
			)
		);

		$this->assertSame( 17, $rows[0]['page_id'], 'A valid page is kept, as a number.' );
		$this->assertArrayNotHasKey( 'page_id', $rows[1] );
		$this->assertArrayNotHasKey( 'page_id', $rows[2] );
		$this->assertArrayNotHasKey( 'page_id', $rows[3], 'A row saved before the field existed is not given one.' );
	}
}
