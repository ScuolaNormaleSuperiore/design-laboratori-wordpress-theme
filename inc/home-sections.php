<?php
/**
 * Helpers of the homepage sections configured in Configurazione → Sezioni HP.
 *
 * Provides, all of them without any dependency on WordPress:
 *  - dli_normalize_hp_background()         A background value, white or muted, or a default.
 *  - dli_get_hp_section_background_class() The CSS class of the background of a section.
 *  - dli_sanitize_hp_background()          Sanitization of the background field of the options page.
 *  - dli_normalize_hp_section_rows()       The saved rows of the sections, safe to use.
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

if ( ! function_exists( 'dli_normalize_hp_section_rows' ) ) {
	/**
	 * The rows of the homepage sections as saved in the options, made safe to use.
	 *
	 * Nothing is rewritten in the database: the saved rows are only read. A row that is not an array, or
	 * without the id of a section, is left out. `enabled` and `show_title` are `true` only when saved
	 * as `true`, otherwise `false`. A background that is not white or muted is dropped, so the template uses
	 * the default of its section. Any other property of the row is kept as it is, in the same order. Every row
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
			$row['instance'] = $position;

			$result[] = $row;
		}

		return $result;
	}
}
