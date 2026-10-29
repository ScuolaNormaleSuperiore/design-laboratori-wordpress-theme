<?php
/**
 * Helpers of the homepage sections configured in Configurazione → Sezioni HP.
 *
 * Provides, all of them without any dependency on WordPress:
 *  - dli_normalize_hp_background()         A background value, white or muted, or a default.
 *  - dli_get_hp_section_background_class() The CSS class of the background of a section.
 *  - dli_sanitize_hp_background()          Sanitization of the background field of the options page.
 *  - dli_normalize_hp_section_rows()       The saved rows of the sections, safe to use.
 *  - dli_normalize_hp_max_items()          The number of items a section shows, a whole number in range.
 *  - dli_sanitize_hp_max_items()           Sanitization of the field "Numero massimo di elementi".
 *  - dli_get_hp_navigation_column_class()  The columns of the cards of "Card di navigazione".
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_normalize_hp_background' ) ) {
	/**
	 * A background of a section: `white` or `muted`, otherwise the default.
	 *
	 * @param mixed  $value   The value to check, for example the one saved in a row.
	 * @param string $default The value to give back when `$value` is not a known background: `white`,
	 *                        `muted`, or an empty string for "none" (the default).
	 * @return string `white`, `muted`, or the default when it is a known background, otherwise an empty string.
	 */
	function dli_normalize_hp_background( $value, $default = '' ) {
		$known = array( 'white', 'muted' );

		if ( is_string( $value ) && in_array( $value, $known, true ) ) {
			return $value;
		}
		return in_array( $default, $known, true ) ? $default : '';
	}
}

if ( ! function_exists( 'dli_get_hp_section_background_class' ) ) {
	/**
	 * The class of the background of a homepage section, to append to its `section` class.
	 *
	 * The choice made in the row ("Sfondo sezione") wins; a row saved before the field existed, or with a
	 * value that is not a known background, uses the default of the section.
	 *
	 * @param mixed  $args       The arguments of the template part, that is the row of the section.
	 * @param string $default_bg `white` or `muted`: the background of the section when the row says nothing.
	 * @return string ` section-muted` (with its leading space) or an empty string.
	 */
	function dli_get_hp_section_background_class( $args, $default_bg = 'white' ) {
		$default    = dli_normalize_hp_background( $default_bg, 'white' );
		$background = is_array( $args ) ? dli_normalize_hp_background( $args['background'] ?? '', $default ) : $default;

		return ( 'muted' === $background && is_array( $args ) ) ? ' section-muted' : '';
	}
}

if ( ! function_exists( 'dli_sanitize_hp_background' ) ) {
	/**
	 * Sanitization of the field "Sfondo sezione": a known background or nothing.
	 *
	 * @param mixed $value The submitted value.
	 * @return string `white`, `muted` or an empty string.
	 */
	function dli_sanitize_hp_background( $value ) {
		return dli_normalize_hp_background( $value );
	}
}

if ( ! function_exists( 'dli_normalize_hp_max_items' ) ) {
	/**
	 * The number of items that a homepage section shows: a whole number between the minimum and the maximum.
	 *
	 * @param mixed $value   The value to check, for example the one saved in a row (a number, or a number as text).
	 * @param int   $default The number to give back when `$value` is empty or is not a whole number.
	 * @return int A number between `DLI_HP_ITEMS_MIN` and `DLI_HP_ITEMS_MAX`; a number out of range is brought to the nearest limit.
	 */
	function dli_normalize_hp_max_items( $value, $default ) {
		if ( is_string( $value ) ) {
			$value = trim( $value );
		}
		if ( is_int( $value ) || ( is_string( $value ) && 1 === preg_match( '/^-?\d+$/', $value ) ) ) {
			return max( DLI_HP_ITEMS_MIN, min( DLI_HP_ITEMS_MAX, (int) $value ) );
		}
		return max( DLI_HP_ITEMS_MIN, min( DLI_HP_ITEMS_MAX, (int) $default ) );
	}
}

if ( ! function_exists( 'dli_sanitize_hp_max_items' ) ) {
	/**
	 * Sanitization of the field "Numero massimo di elementi": a number in range, or nothing.
	 *
	 * An empty value stays empty: the section then uses its default number.
	 *
	 * @param mixed $value The submitted value.
	 * @return int|string The number, or an empty string when there is no usable number.
	 */
	function dli_sanitize_hp_max_items( $value ) {
		$value = is_string( $value ) ? trim( $value ) : $value;
		if ( ! is_int( $value ) && ! ( is_string( $value ) && 1 === preg_match( '/^-?\d+$/', $value ) ) ) {
			return '';
		}
		return dli_normalize_hp_max_items( $value, DLI_HP_ITEMS_MIN );
	}
}

if ( ! function_exists( 'dli_normalize_hp_section_rows' ) ) {
	/**
	 * The rows of the homepage sections as saved in the options, made safe to use.
	 *
	 * Nothing is rewritten in the database: the saved rows are only read. A row that is not an array, or
	 * without the id of a section, is left out. `enabled` and `show_title` are `true` only when saved
	 * as `true`, otherwise `false`. A background that is not white or muted is dropped, so the template uses
	 * the default of its section. A `max_items` that is not a whole number is dropped and one out of range is brought to the nearest limit. Any other property of the row is kept as it is, in the same order. Every row
	 * carries `instance`, the position (starting from 1) it has in the saved list, that stays the same when
	 * other rows are turned off: a section that can be repeated uses it to have its own identifiers.
	 *
	 * @param mixed $rows The saved rows, normally an array of arrays.
	 * @return array<int, array<string, mixed>> The usable rows, in the saved order.
	 */
	function dli_normalize_hp_section_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$result   = array();
		$position = 0;
		foreach ( $rows as $row ) {
			++$position;
			if ( ! is_array( $row ) || ! isset( $row['id'] ) || ! is_string( $row['id'] ) || '' === trim( $row['id'] ) ) {
				continue;
			}

			$row['enabled']    = ( 'true' === ( $row['enabled'] ?? '' ) ) ? 'true' : 'false';
			$row['show_title'] = ( 'true' === ( $row['show_title'] ?? '' ) ) ? 'true' : 'false';
			if ( array_key_exists( 'background', $row ) ) {
				$background = dli_normalize_hp_background( $row['background'] );
				if ( '' === $background ) {
					unset( $row['background'] );
				} else {
					$row['background'] = $background;
				}
			}
			if ( array_key_exists( 'max_items', $row ) ) {
				$max_items = dli_sanitize_hp_max_items( $row['max_items'] );
				if ( '' === $max_items ) {
					unset( $row['max_items'] );
				} else {
					$row['max_items'] = $max_items;
				}
			}
			$row['instance'] = $position;

			$result[] = $row;
		}

		return $result;
	}
}

if ( ! function_exists( 'dli_get_hp_navigation_column_class' ) ) {
	/**
	 * The classes of the column of each card of "Card di navigazione", for the number of cards shown.
	 *
	 * With four cards there is one row of four on a large screen; with up to three they are fewer and wider;
	 * with five or six they are three per row, and the template centers the last row when it is not full.
	 * On a tablet the cards are two per row, on a phone one.
	 *
	 * @param int $count The number of cards (the section shows six at most).
	 * @return string The classes of the column.
	 */
	function dli_get_hp_navigation_column_class( $count ) {
		$count = (int) $count;

		if ( $count <= 1 ) {
			return 'col-12 col-lg-6';
		}
		if ( 2 === $count ) {
			return 'col-12 col-md-6';
		}
		if ( 4 === $count ) {
			return 'col-12 col-md-6 col-lg-3';
		}
		return 'col-12 col-md-6 col-lg-4';
	}
}
