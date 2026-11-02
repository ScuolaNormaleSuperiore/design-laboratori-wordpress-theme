<?php
/**
 * Unit tests of the pure helpers that read the page size of the archives, in inc/pagination.php.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * The page size asked for in the address (`per_page`) is never trusted: it is a whole positive number, and either
 * one of the offered values or not more than the largest of them.
 */
class DLI_Pagination_Unit_Test extends TestCase {

	/**
	 * The values that are not a whole positive number are not numbers at all.
	 */
	public function test_only_whole_positive_numbers_are_parsed() {
		$this->assertSame( 12, dli_parse_positive_int( '12' ) );
		$this->assertSame( 12, dli_parse_positive_int( 12 ) );
		$this->assertSame( 12, dli_parse_positive_int( '012' ), 'Leading zeros are the same number.' );
		$this->assertSame( PHP_INT_MAX, dli_parse_positive_int( '99999999999999999999999' ), 'A number too large for an integer is the largest one, not a negative wrap-around.' );

		foreach ( array( null, false, true, '', '0', 0, '-1', -1, '-12', '+5', ' 5', '5 ', '1.5', 1.5, '1e3', '0x1A', 'abc', '12abc', array( '12' ), array(), new stdClass() ) as $value ) {
			$this->assertNull( dli_parse_positive_int( $value ), 'Not a whole positive number: ' . var_export( $value, true ) );
		}
	}

	/**
	 * With a list of choices only the offered values are accepted, anything else is the default.
	 */
	public function test_the_choice_is_one_of_the_offered_values_or_the_default() {
		$values = array( '3', '6', '9', '12', '24', '48', '96' );

		$this->assertSame( 12, dli_pick_per_page( '12', 6, $values ) );
		$this->assertSame( 96, dli_pick_per_page( 96, 6, $values ) );
		$this->assertSame( 6, dli_pick_per_page( null, 6, $values ), 'Absent: the default.' );
		$this->assertSame( 6, dli_pick_per_page( '7', 6, $values ), 'A value that is not offered: the default.' );
		$this->assertSame( 6, dli_pick_per_page( '99999999', 6, $values ), 'A huge value: the default.' );
		$this->assertSame( 6, dli_pick_per_page( '-1', 6, $values ) );
		$this->assertSame( 6, dli_pick_per_page( '0', 6, $values ) );
		$this->assertSame( 6, dli_pick_per_page( 'abc', 6, $values ) );
		$this->assertSame( 6, dli_pick_per_page( array( '12' ), 6, $values ), 'A list is not a size.' );
		$this->assertSame( 6, dli_pick_per_page( false, 6, $values ) );
		$this->assertSame( 12, dli_pick_per_page( '12', 6, array( 3, 6, 12 ) ), 'The offered values can be numbers.' );
		$this->assertSame( 6, dli_pick_per_page( '12', 6, array() ), 'No offered value: the default.' );
	}

	/**
	 * With a limit, any size up to the largest offered value (or the default, if larger) is accepted.
	 */
	public function test_the_size_is_clamped_to_the_largest_allowed_value() {
		$values = array( '3', '6', '9', '12', '24', '48', '96' );

		$this->assertSame( 6, dli_clamp_per_page( null, 6, $values ), 'Absent: the default.' );
		$this->assertSame( 12, dli_clamp_per_page( '12', 6, $values ) );
		$this->assertSame( 7, dli_clamp_per_page( '7', 6, $values ), 'A value in range that is not offered is kept: it may come from an old link.' );
		$this->assertSame( 96, dli_clamp_per_page( '96', 6, $values ) );
		$this->assertSame( 96, dli_clamp_per_page( '97', 6, $values ), 'Over the largest value: the largest value.' );
		$this->assertSame( 96, dli_clamp_per_page( '99999999', 6, $values ) );
		$this->assertSame( 96, dli_clamp_per_page( '99999999999999999999999', 6, $values ), 'Larger than an integer: still the largest value.' );
		$this->assertSame( 6, dli_clamp_per_page( '-1', 6, $values ), 'Negative: the default.' );
		$this->assertSame( 6, dli_clamp_per_page( '0', 6, $values ) );
		$this->assertSame( 6, dli_clamp_per_page( 'abc', 6, $values ) );
		$this->assertSame( 6, dli_clamp_per_page( '12abc', 6, $values ) );
		$this->assertSame( 6, dli_clamp_per_page( '1e3', 6, $values ) );
		$this->assertSame( 6, dli_clamp_per_page( array( '99999' ), 6, $values ), 'A list is not a size.' );
		$this->assertSame( 6, dli_clamp_per_page( false, 6, $values ), 'filter_input() gives false for a list.' );

		// A default larger than every offered value (the "show all" of the projects) raises the limit.
		$this->assertSame( 500, dli_clamp_per_page( '500', 999, $values ) );
		$this->assertSame( 999, dli_clamp_per_page( '5000', 999, $values ) );
	}
}
