<?php
/**
 * Unit tests of the pure helper functions in inc/utils.php.
 *
 * Only functions that depend on nothing but their arguments are tested here.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Pure helper functions of the theme.
 */
class DLI_Utils_Unit_Test extends TestCase {

	/**
	 * First and last name are joined when both are present.
	 */
	public function test_persona_display_name_joins_first_and_last_name() {
		$this->assertSame( 'Mario Rossi', dli_get_persona_display_name( 'Mario', 'Rossi', 'Titolo' ) );
	}

	/**
	 * The title is the fallback when a name is missing.
	 */
	public function test_persona_display_name_falls_back_to_title() {
		$this->assertSame( 'Titolo', dli_get_persona_display_name( '', 'Rossi', 'Titolo' ) );
		$this->assertSame( 'Titolo', dli_get_persona_display_name( 'Mario', '', 'Titolo' ) );
		$this->assertSame( 'Titolo', dli_get_persona_display_name( '', '', 'Titolo' ) );
	}

	/**
	 * A nested value is found and the key of the top-level element is returned.
	 */
	public function test_multi_array_search_returns_top_level_key() {
		$haystack = array(
			'a' => array( 'x', 'y' ),
			'b' => array( 'k' => array( 'z' ) ),
			'c' => 'w',
		);
		$this->assertSame( 'a', dli_multi_array_search( 'y', $haystack ) );
		$this->assertSame( 'b', dli_multi_array_search( 'z', $haystack ) );
		$this->assertSame( 'c', dli_multi_array_search( 'w', $haystack ) );
	}

	/**
	 * A missing value gives false.
	 */
	public function test_multi_array_search_returns_false_when_missing() {
		$this->assertFalse( dli_multi_array_search( 'nope', array( 'a' => array( 'x' ) ) ) );
		$this->assertFalse( dli_multi_array_search( 'nope', array() ) );
	}

	/**
	 * Month numbers from 1 to 12 give the Italian month name.
	 */
	public function test_monthname_for_valid_months() {
		$this->assertSame( 'gennaio', dli_get_monthname( 1 ) );
		$this->assertSame( 'ottobre', dli_get_monthname( '10' ) );
		$this->assertSame( 'dicembre', dli_get_monthname( 12 ) );
	}

	/**
	 * Months out of range give an empty string.
	 */
	public function test_monthname_for_invalid_months() {
		$this->assertSame( '', dli_get_monthname( 0 ) );
		$this->assertSame( '', dli_get_monthname( 13 ) );
		$this->assertSame( '', dli_get_monthname( 'x' ) );
	}

	/**
	 * A valid date string gives a DateTime.
	 */
	public function test_datetime_from_format_with_valid_date() {
		$date = dli_get_datetime_from_format( 'Ymd', '20261020' );
		$this->assertInstanceOf( DateTime::class, $date );
		$this->assertSame( '2026-10-20', $date->format( 'Y-m-d' ) );
	}

	/**
	 * Empty or invalid input gives false.
	 */
	public function test_datetime_from_format_with_invalid_input() {
		$this->assertFalse( dli_get_datetime_from_format( '', '20261020' ) );
		$this->assertFalse( dli_get_datetime_from_format( 'Ymd', '' ) );
		$this->assertFalse( dli_get_datetime_from_format( 'Ymd', 'non-una-data' ) );
		$this->assertFalse( dli_get_datetime_from_format( 'Ymd', '20261340' ) );
	}

	/**
	 * A date is reformatted with the output format, or an empty string on failure.
	 */
	public function test_format_date_from_format() {
		$this->assertSame( '20/10/2026', dli_format_date_from_format( 'Ymd', '20261020', 'd/m/Y' ) );
		$this->assertSame( '', dli_format_date_from_format( 'Ymd', 'non-una-data', 'd/m/Y' ) );
	}
}
