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
 *  - dli_sanitize_hp_page_id()            Sanitization of the field "Pagina": the ID of a page, or nothing.
 *  - dli_sanitize_hp_video_url()          Sanitization of the field "URL del video": a web address, or nothing.
 *  - dli_sanitize_hp_row_title()          Sanitization of the fields "Titolo della sezione": plain text, or nothing.
 *  - dli_get_hp_row_title()               The title of a row in the language of the page, or nothing.
 *  - dli_sanitize_hp_banner_mode()        Sanitization of the field "Modalità" of the section Banner: manual, automatic or nothing.
 *  - dli_sanitize_hp_id_list()            A list of IDs: whole positive numbers, once each, in the order chosen.
 *  - dli_get_hp_navigation_column_class()  The columns of the cards of "Card di navigazione".
 *  - dli_get_hp_groups_column_class()      The columns of the groups of "Navigazione per pagine".
 *  - dli_is_blank_text()                   Whether a text has nothing to show (empty, spaces, tags, non-breaking spaces).
 *  - dli_pick_lang_value()                 The Italian or English value of a pair of fields, English falling back to Italian.
 *  - dli_sanitize_hp_tab_page_ids()        The pages linked to a tab: whole positive numbers, no repeats.
 *  - dli_normalize_hp_tab_rows()           The saved tabs of "Pannello tab", safe to use.
 *  - dli_get_hp_tabs_for_language()        The tabs of "Pannello tab" shown in a language.
 *  - dli_get_featured_renderer()           The partial that prints the items of a box of "Contenuti in evidenza".
 *  - dli_get_featured_column_class()       The columns of the boxes of "Contenuti in evidenza".
 *  - dli_normalize_featured_box()          A saved box of "Contenuti in evidenza": content type and title.
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
	 * the default of its section. A `max_items` or `max_subitems` that is not a whole number is dropped and one out of range is brought to the nearest limit. A `max_level` that is not one of the choices is dropped. A `page_id` that is not the ID of a page (a whole positive number) is dropped and a valid one is kept as an integer (`dli_sanitize_hp_page_id()`). Any other property of the row is kept as it is, in the same order. Every row
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
			if ( array_key_exists( 'page_id', $row ) ) {
				$page_id = dli_sanitize_hp_page_id( $row['page_id'] );
				if ( '' === $page_id ) {
					unset( $row['page_id'] );
				} else {
					$row['page_id'] = $page_id;
				}
			}
			if ( array_key_exists( 'video_url', $row ) ) {
				$video_url = dli_sanitize_hp_video_url( $row['video_url'] );
				if ( '' === $video_url ) {
					unset( $row['video_url'] );
				} else {
					$row['video_url'] = $video_url;
				}
			}
			if ( array_key_exists( 'banner_mode', $row ) ) {
				$banner_mode = dli_sanitize_hp_banner_mode( $row['banner_mode'] );
				if ( '' === $banner_mode ) {
					unset( $row['banner_mode'] );
				} else {
					$row['banner_mode'] = $banner_mode;
				}
			}
			if ( array_key_exists( 'banner_ids', $row ) ) {
				$row['banner_ids'] = dli_sanitize_hp_id_list( $row['banner_ids'] );
			}
			foreach ( array( 'title', 'title_eng' ) as $title_key ) {
				if ( array_key_exists( $title_key, $row ) ) {
					$title = dli_sanitize_hp_row_title( $row[ $title_key ] );
					if ( '' === $title ) {
						unset( $row[ $title_key ] );
					} else {
						$row[ $title_key ] = $title;
					}
				}
			}
			$row['instance'] = $position;

			$result[] = $row;
		}

		return $result;
	}
}

if ( ! function_exists( 'dli_get_hp_row_title' ) ) {
	/**
	 * The title of a row of Sezioni HP (the sections Video and Banner) in the language of the page.
	 *
	 * An Italian page uses `title`, any other page `title_eng`. There is no other: a title missing in the language of
	 * the page is nothing, so the section has no title; it does not take the one of the other language and it has no
	 * name of its own.
	 *
	 * @param array<string, mixed> $row         The row.
	 * @param bool                 $is_italian  True when the page is in Italian, false for any other language.
	 * @return string The title, trimmed, or an empty string.
	 */
	function dli_get_hp_row_title( array $row, $is_italian ) {
		$value = $row[ $is_italian ? 'title' : 'title_eng' ] ?? '';

		return is_string( $value ) ? trim( $value ) : '';
	}
}

if ( ! function_exists( 'dli_sanitize_hp_banner_mode' ) ) {
	/**
	 * Sanitize the mode of the section Banner: `automatic` (the banners by priority) or `manual` (the ones chosen).
	 *
	 * @param mixed $value The submitted value.
	 * @return string `automatic`, `manual`, or an empty string for anything else (the section is then automatic).
	 */
	function dli_sanitize_hp_banner_mode( $value ) {
		$value = is_string( $value ) ? trim( $value ) : '';

		return in_array( $value, array( 'automatic', 'manual' ), true ) ? $value : '';
	}
}

if ( ! function_exists( 'dli_sanitize_hp_row_title' ) ) {
	/**
	 * Sanitize the title of a row of Sezioni HP.
	 *
	 * The title is plain text: tags are taken out, any run of spaces and line breaks becomes one space, the ends are
	 * trimmed and the text is cut at `DLI_HP_ROW_TITLE_MAX` characters. A value that is not text, or empty once cleaned,
	 * gives an empty string (no title: the section keeps its own name).
	 *
	 * @param mixed $value The submitted value.
	 * @return string The title, or an empty string.
	 */
	function dli_sanitize_hp_row_title( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( (string) preg_replace( '/\s+/u', ' ', strip_tags( $value ) ) );

		return mb_substr( $value, 0, DLI_HP_ROW_TITLE_MAX );
	}
}

if ( ! function_exists( 'dli_sanitize_hp_video_url' ) ) {
	/**
	 * Sanitize the address of the video of a row of Sezioni HP.
	 *
	 * Only a web address is an address: text with spaces around it is trimmed; anything that is not a string, an
	 * address with another scheme (`javascript:`, `data:`, `ftp:`) or text that is not an address gives an empty string
	 * (no video).
	 *
	 * @param mixed $value The submitted value.
	 * @return string The address, or an empty string when there is no usable address.
	 */
	function dli_sanitize_hp_video_url( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( $value );
		if ( 1 !== preg_match( '#^https?://[^\s<>"\x27]+$#i', $value ) || false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return '';
		}

		return $value;
	}
}

if ( ! function_exists( 'dli_sanitize_hp_page_id' ) ) {
	/**
	 * Sanitization of the field "Pagina" of a row of Sezioni HP: the ID of a page, or nothing.
	 *
	 * Only a whole positive number is an ID: spaces around it and leading zeros are the same number; a sign, a
	 * decimal point, an exponent, text, a list or a boolean is not, and gives an empty string (no page chosen).
	 *
	 * @param mixed $value The submitted value.
	 * @return int|string The page ID, or an empty string when there is no usable ID.
	 */
	function dli_sanitize_hp_page_id( $value ) {
		$value = is_string( $value ) ? trim( $value ) : $value;

		if ( is_int( $value ) ) {
			$number = $value;
		} elseif ( is_string( $value ) && ctype_digit( $value ) ) {
			$number = (int) $value;
		} else {
			return '';
		}

		return $number > 0 ? $number : '';
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

if ( ! function_exists( 'dli_is_blank_text' ) ) {
	/**
	 * Whether a text has nothing to show: empty, only spaces, or only tags and non-breaking spaces.
	 *
	 * The editor of a description leaves `<p>&nbsp;</p>` behind when its text is deleted. A value that is not text
	 * is blank, and the text "0" is text.
	 *
	 * @param mixed $value The value to check.
	 * @return bool True when there is nothing visible.
	 */
	function dli_is_blank_text( $value ) {
		if ( ! is_string( $value ) ) {
			return true;
		}

		$text = preg_replace( '/<[^>]*>/', '', $value );
		$text = html_entity_decode( (string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$text = preg_replace( '/[\s\x{00A0}]+/u', '', $text );

		return null === $text || '' === $text;
	}
}

if ( ! function_exists( 'dli_pick_lang_value' ) ) {
	/**
	 * The value of a pair of fields, Italian and English, for the language of the page.
	 *
	 * An English page uses the English value; when it is missing or blank, the Italian one. An Italian page always
	 * uses the Italian value: English never takes its place. A value that is not text is empty.
	 *
	 * @param mixed $italian    The Italian value.
	 * @param mixed $english    The English value.
	 * @param bool  $is_italian True when the page is in Italian, false for any other language.
	 * @return string The value to show.
	 */
	function dli_pick_lang_value( $italian, $english, $is_italian ) {
		$italian = is_string( $italian ) ? $italian : '';
		$english = is_string( $english ) ? $english : '';

		if ( $is_italian || dli_is_blank_text( $english ) ) {
			return $italian;
		}
		return $english;
	}
}

if ( ! function_exists( 'dli_sanitize_hp_tab_page_ids' ) ) {
	/**
	 * The pages linked to a tab, as a list of whole positive numbers without repeats, in the saved order.
	 *
	 * Used when the group of the tabs is saved and again when it is read: a value that is not a list, and any item
	 * that is not a whole positive number (text with letters, negative numbers, zero, booleans, lists), are left out;
	 * each item is checked by `dli_sanitize_hp_page_id()`, so a page ID is parsed in one place only.
	 *
	 * @param mixed $value The saved value, normally a list of page IDs written as text.
	 * @return int[] The page IDs.
	 */
	function dli_sanitize_hp_tab_page_ids( $value ) {
		return dli_sanitize_hp_id_list( $value );
	}
}

if ( ! function_exists( 'dli_sanitize_hp_id_list' ) ) {
	/**
	 * A list of IDs of a field that lets the editor choose several items (the pages of a tab, the banners of a row).
	 *
	 * Each item is checked by `dli_sanitize_hp_page_id()`, so an ID is parsed in one place only. An item that is not a
	 * whole positive number is dropped, an ID that is already in the list is kept once, and the order is the saved one.
	 *
	 * @param mixed $value The submitted value.
	 * @return int[] The IDs, in order; an empty list when the value is not a list.
	 */
	function dli_sanitize_hp_id_list( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$ids = array();
		foreach ( $value as $item ) {
			$id = dli_sanitize_hp_page_id( $item );
			if ( '' !== $id && ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}

		return $ids;
	}
}

if ( ! function_exists( 'dli_normalize_hp_tab_rows' ) ) {
	/**
	 * The saved tabs of the section "Pannello tab", safe to use.
	 *
	 * A value that is not a row is left out; in the others every text is a string (empty when it is missing or is
	 * not text) and the linked pages are checked by `dli_sanitize_hp_tab_page_ids()`. The order is the saved one.
	 * Whether a tab is shown is decided later, for the language of the page (`dli_get_hp_tabs_for_language()`).
	 *
	 * @param mixed $rows The saved rows of the group `home_tabs_items`.
	 * @return array<int, array{label: string, label_eng: string, text: string, text_eng: string, page_ids: int[]}> The rows.
	 */
	function dli_normalize_hp_tab_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$result = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$result[] = array(
				'label'     => is_string( $row['etichetta'] ?? null ) ? trim( $row['etichetta'] ) : '',
				'label_eng' => is_string( $row['etichetta_eng'] ?? null ) ? trim( $row['etichetta_eng'] ) : '',
				'text'      => is_string( $row['descrizione'] ?? null ) ? $row['descrizione'] : '',
				'text_eng'  => is_string( $row['descrizione_eng'] ?? null ) ? $row['descrizione_eng'] : '',
				'page_ids'  => dli_sanitize_hp_tab_page_ids( $row['pagine'] ?? null ),
			);
		}

		return $result;
	}
}

if ( ! function_exists( 'dli_get_hp_tabs_for_language' ) ) {
	/**
	 * The tabs of the section "Pannello tab" that are shown in a language.
	 *
	 * The label makes a tab: a row whose label, in the language of the page, is blank is not shown. A tab is kept
	 * while it has a text or at least one linked page; a tab with neither is left out. A missing English label or
	 * text is the Italian one (`dli_pick_lang_value()`). Whether the linked pages can actually be shown (published,
	 * without password, translated) is decided by `dli_get_home_tabs()`, which reads them.
	 *
	 * @param array<int, array{label: string, label_eng: string, text: string, text_eng: string, page_ids: int[]}> $rows The rows from `dli_normalize_hp_tab_rows()`.
	 * @param bool                                                                                                 $is_italian True for an Italian page.
	 * @return array<int, array{label: string, text: string, page_ids: int[]}> The tabs, in order.
	 */
	function dli_get_hp_tabs_for_language( $rows, $is_italian ) {
		$tabs = array();
		foreach ( $rows as $row ) {
			$label = dli_pick_lang_value( $row['label'], $row['label_eng'], $is_italian );
			if ( dli_is_blank_text( $label ) ) {
				continue;
			}

			$text = dli_pick_lang_value( $row['text'], $row['text_eng'], $is_italian );
			if ( dli_is_blank_text( $text ) ) {
				$text = '';
			}
			if ( '' === $text && empty( $row['page_ids'] ) ) {
				continue;
			}

			$tabs[] = array(
				'label'    => $label,
				'text'     => $text,
				'page_ids' => $row['page_ids'],
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'dli_get_featured_renderer' ) ) {
	/**
	 * The partial that prints the items of a box of "Contenuti in evidenza", chosen by its content type.
	 *
	 * News, events and publications have a renderer of their own (`template-parts/home/featured-contents/`); any
	 * other type has the essential fallback, which shows only a title, an image, an abstract and a link. The type
	 * decides the layout: the editor does not choose a template.
	 *
	 * @param mixed $post_type The content type of the box.
	 * @return string The name of the partial, without folder and extension.
	 */
	function dli_get_featured_renderer( $post_type ) {
		$renderers = array(
			NEWS_POST_TYPE        => 'card-notizia',
			EVENT_POST_TYPE       => 'card-evento',
			PUBLICATION_POST_TYPE => 'card-pubblicazione',
		);

		return is_string( $post_type ) && isset( $renderers[ $post_type ] ) ? $renderers[ $post_type ] : 'card-fallback';
	}
}

if ( ! function_exists( 'dli_get_featured_column_class' ) ) {
	/**
	 * The classes of the column of each box of "Contenuti in evidenza", for the number of boxes shown.
	 *
	 * One box takes the whole row, two take half each, three or more are three per row; on a smaller screen each
	 * box takes the whole row. Only the boxes that have something to show are counted, so there is never an empty column.
	 *
	 * @param int $count The number of boxes shown.
	 * @return string The classes of the column.
	 */
	function dli_get_featured_column_class( $count ) {
		$count = (int) $count;

		if ( 2 === $count ) {
			return 'col-12 col-lg-6';
		}
		return $count >= 3 ? 'col-12 col-lg-4' : 'col-12';
	}
}

if ( ! function_exists( 'dli_normalize_featured_box' ) ) {
	/**
	 * A box of "Contenuti in evidenza" as saved in Layout HP: its content type and its title.
	 *
	 * The option of a box (`featured_contents_<n>`) is a group with one row, whose keys end with the number of the
	 * box. The title is the one of the language of the page; a missing English title is the Italian one, and an
	 * empty title stays empty (the section then gives the default of the type). A box without a content type is not
	 * a box. The old key `featured_contents_template_box_<n>` is not read: the layout follows the type.
	 *
	 * @param mixed $option    The saved option of the box.
	 * @param int   $number    The number of the box, from 1.
	 * @param bool  $is_italian True for an Italian page.
	 * @return array{type: string, title: string}|null The box, or null when it has no content type.
	 */
	function dli_normalize_featured_box( $option, $number, $is_italian ) {
		$row = is_array( $option ) && isset( $option[0] ) && is_array( $option[0] ) ? $option[0] : null;
		if ( null === $row ) {
			return null;
		}

		$type_key  = 'featured_contents_type_box_' . (int) $number;
		$label_key = 'featured_contents_label_box_' . (int) $number;
		$type      = is_string( $row[ $type_key ] ?? null ) ? trim( $row[ $type_key ] ) : '';
		if ( '' === $type ) {
			return null;
		}

		return array(
			'type'  => $type,
			'title' => trim( dli_pick_lang_value( $row[ $label_key ] ?? '', $row[ $label_key . DLI_ENG_SUFFIX_LANGUAGE ] ?? '', $is_italian ) ),
		);
	}
}
