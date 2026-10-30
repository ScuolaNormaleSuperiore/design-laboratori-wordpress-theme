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
 *  - dli_parse_hp_page_level()             The level of the pages if it is one of the choices, otherwise null.
 *  - dli_normalize_hp_page_level()         The level of the pages a section lists, one of the choices or a default.
 *  - dli_sanitize_hp_page_level()          Sanitization of the field "Livello delle pagine".
 *  - dli_get_hp_navigation_column_class()  The columns of the cards of "Card di navigazione".
 *  - dli_get_hp_groups_column_class()      The columns of the groups of "Navigazione per pagine".
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

if ( ! function_exists( 'dli_parse_hp_page_level' ) ) {
	/**
	 * The level of the pages, if the value is one of the choices of `DLI_HP_PAGE_LEVELS`.
	 *
	 * Only the exact values are accepted: a number or the text of a number, without spaces or decimals.
	 *
	 * @param mixed $value The value to check.
	 * @return int|null 1, 2, 3 or 0 (all the levels), or null when the value is not one of them.
	 */
	function dli_parse_hp_page_level( $value ) {
		if ( is_string( $value ) && 1 === preg_match( '/^\d$/', $value ) ) {
			$value = (int) $value;
		}
		return ( is_int( $value ) && in_array( $value, DLI_HP_PAGE_LEVELS, true ) ) ? $value : null;
	}
}

if ( ! function_exists( 'dli_normalize_hp_page_level' ) ) {
	/**
	 * The level of the pages that a homepage section lists: 1, 2, 3, or 0 for all the levels.
	 *
	 * @param mixed $value   The value to check, for example the one saved in a row (a number, or a number as text).
	 * @param int   $default_level The level to give back when `$value` is not one of the choices; when it is not
	 *                             one either, the first level.
	 * @return int One of the choices of `DLI_HP_PAGE_LEVELS`.
	 */
	function dli_normalize_hp_page_level( $value, $default_level = DLI_HP_CARD_NAVIGATION_DEFAULT_LEVEL ) {
		$level = dli_parse_hp_page_level( $value );
		if ( null !== $level ) {
			return $level;
		}
		$fallback = dli_parse_hp_page_level( $default_level );
		return null !== $fallback ? $fallback : 1;
	}
}

if ( ! function_exists( 'dli_sanitize_hp_page_level' ) ) {
	/**
	 * Sanitization of the field "Livello delle pagine": one of the choices, or nothing.
	 *
	 * An empty value stays empty: the section then uses its default level. "All the levels" is the number 0, not
	 * an empty value.
	 *
	 * @param mixed $value The submitted value.
	 * @return int|string The level, or an empty string when the value is not one of the choices.
	 */
	function dli_sanitize_hp_page_level( $value ) {
		$level = dli_parse_hp_page_level( $value );
		return null !== $level ? $level : '';
	}
}

if ( ! function_exists( 'dli_normalize_hp_section_rows' ) ) {
	/**
	 * The rows of the homepage sections as saved in the options, made safe to use.
	 *
	 * Nothing is rewritten in the database: the saved rows are only read. A row that is not an array, or
	 * without the id of a section, is left out. `enabled` and `show_title` are `true` only when saved
	 * as `true`, otherwise `false`. A background that is not white or muted is dropped, so the template uses
	 * the default of its section. A `max_items` or `max_subitems` that is not a whole number is dropped and one out of range is brought to the nearest limit. A `max_level` that is not one of the choices is dropped. Any other property of the row is kept as it is, in the same order. Every row
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
			foreach ( array( 'max_items', 'max_subitems' ) as $number_key ) {
				if ( array_key_exists( $number_key, $row ) ) {
					$number = dli_sanitize_hp_max_items( $row[ $number_key ] );
					if ( '' === $number ) {
						unset( $row[ $number_key ] );
					} else {
						$row[ $number_key ] = $number;
					}
				}
			}
			if ( array_key_exists( 'max_level', $row ) ) {
				$level = dli_sanitize_hp_page_level( $row['max_level'] );
				if ( '' === $level ) {
					unset( $row['max_level'] );
				} else {
					$row['max_level'] = $level;
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

if ( ! function_exists( 'dli_get_hp_groups_column_class' ) ) {
	/**
	 * The classes of the column of each group of "Navigazione per pagine", for the number of groups shown.
	 *
	 * One group is narrower than the page, two take half each, three a third, four or more a quarter and then
	 * the next row. On a tablet the groups are two per row; on a phone the template prints an accordion instead.
	 *
	 * @param int $count The number of groups.
	 * @return string The classes of the column.
	 */
	function dli_get_hp_groups_column_class( $count ) {
		$count = (int) $count;

		if ( $count <= 1 ) {
			return 'col-12 col-md-8 col-lg-6';
		}
		if ( 2 === $count ) {
			return 'col-12 col-md-6';
		}
		if ( 3 === $count ) {
			return 'col-12 col-md-6 col-lg-4';
		}
		return 'col-12 col-md-6 col-lg-3';
	}
}
