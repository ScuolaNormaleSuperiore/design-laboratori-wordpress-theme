<?php
/**
 * Helpers that read the page size of the archives, without any dependency on WordPress.
 *
 * The page size is asked for in the address (`per_page`), so it is input from the visitor: it must be a whole
 * positive number, and either one of the values the page offers or not more than the largest of them. A value that
 * is absent, negative, not a number, a list or too large for an integer never reaches the query as it is.
 *
 * Provides:
 *  - dli_parse_positive_int() A whole positive number from a request value, or null.
 *  - dli_pick_per_page()      The page size when it is one of the offered values, otherwise the default.
 *  - dli_clamp_per_page()     The page size, at most the largest allowed value, otherwise the default.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_parse_positive_int' ) ) {
	/**
	 * A whole positive number from a value of the request, or null when it is not one.
	 *
	 * Only digits are accepted (leading zeros are the same number): a sign, a space, a decimal point, an exponent, a
	 * hexadecimal prefix, a boolean, a list or an object are not numbers. A number too large for an integer is the
	 * largest integer, never a negative wrap-around.
	 *
	 * @param mixed $value The value, normally the string given by `filter_input()` (`false` or null when it is a list or is absent).
	 * @return int|null The number, or null.
	 */
	function dli_parse_positive_int( $value ) {
		if ( is_int( $value ) ) {
			return $value > 0 ? $value : null;
		}
		if ( ! is_string( $value ) || ! ctype_digit( $value ) ) {
			return null;
		}

		$number = (int) $value;

		return $number > 0 ? $number : null;
	}
}

if ( ! function_exists( 'dli_pick_per_page' ) ) {
	/**
	 * The page size asked for, when it is one of the values the page offers; otherwise the default.
	 *
	 * @param mixed                  $requested The value of the request.
	 * @param int|string             $default   The page size when nothing valid is asked for.
	 * @param array<int, int|string> $allowed   The offered values.
	 * @return int The page size.
	 */
	function dli_pick_per_page( $requested, $default, $allowed ) {
		$size   = dli_parse_positive_int( $requested );
		$values = array_filter( array_map( 'dli_parse_positive_int', (array) $allowed ) );

		return ( null !== $size && in_array( $size, $values, true ) ) ? $size : (int) $default;
	}
}

if ( ! function_exists( 'dli_clamp_per_page' ) ) {
	/**
	 * The page size asked for, at most the largest allowed value; otherwise the default.
	 *
	 * A size that is not offered but is in range is kept, so an old link keeps working; a larger one is the largest
	 * value. The limit is the largest offered value or the default, whichever is larger: a page whose default is
	 * "show all" can still be asked for any size up to it.
	 *
	 * @param mixed                  $requested The value of the request.
	 * @param int|string             $default   The page size when nothing valid is asked for.
	 * @param array<int, int|string> $allowed   The offered values.
	 * @return int The page size.
	 */
	function dli_clamp_per_page( $requested, $default, $allowed ) {
		$default = (int) $default;
		$values  = array_filter( array_map( 'dli_parse_positive_int', (array) $allowed ) );
		$limit   = max( array_merge( array_values( $values ), array( $default ) ) );
		$size    = dli_parse_positive_int( $requested );

		return null === $size ? $default : min( $size, $limit );
	}
}
