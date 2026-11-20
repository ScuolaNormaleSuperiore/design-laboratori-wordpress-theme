<?php
/**
 * Helpers of the filters of the People page, without any dependency on WordPress.
 *
 * The text search is the same in the two views: the cards view applies it here, on the server, and the table applies
 * the same rule in `assets/js/people-filters.js`. The cases of `tests/unit/fixtures/people-search-cases.json` are
 * checked against both, so that the two do not drift apart.
 *
 * Provides:
 *  - dli_people_search_letters()      The accented letters and the plain letter each one is compared as.
 *  - dli_normalize_people_search()    The text of a search as it is compared.
 *  - dli_people_row_matches_search()  Whether a row (a person with one of its types) matches a search.
 *  - dli_people_search_from_request() The search asked for in the address: `cerca`, or the old `cognome`.
 *  - dli_people_filter_query_args()   The arguments of the address that keep the active filters.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_people_search_letters' ) ) {
	/**
	 * The accented letters (in lower case) and the letter each one is compared as. The same table is in
	 * `assets/js/people-filters.js`, so the two rules stay the same without depending on the `intl` extension.
	 *
	 * @return array<string, string> The letters, accented → plain.
	 */
	function dli_people_search_letters() {
		static $letters = null;
		if ( null === $letters ) {
			$letters = array();
			$groups  = array(
				'a' => 'àáâãäåāăą',
				'c' => 'çćč',
				'd' => 'ďđ',
				'e' => 'èéêëēėęě',
				'i' => 'ìíîïīį',
				'l' => 'ľĺł',
				'n' => 'ñńň',
				'o' => 'òóôõöøōő',
				'r' => 'ŕř',
				's' => 'śšş',
				't' => 'ťţ',
				'u' => 'ùúûüūůűų',
				'y' => 'ýÿ',
				'z' => 'źżž',
			);
			foreach ( $groups as $plain => $accented ) {
				foreach ( mb_str_split( $accented, 1, 'UTF-8' ) as $letter ) {
					$letters[ $letter ] = $plain;
				}
			}
		}
		return $letters;
	}
}

if ( ! function_exists( 'dli_normalize_people_search' ) ) {
	/**
	 * The text of a search as it is compared: every run of spaces (also no-break spaces) is one space, the spaces
	 * around are removed, the letters are in lower case and without accents (as the database compared the surname
	 * before: «Nicolo» finds «Nicolò»).
	 *
	 * @param string $text The text.
	 * @return string The normalized text.
	 */
	function dli_normalize_people_search( $text ) {
		$text = (string) preg_replace( '/[\s\x{00A0}]+/u', ' ', (string) $text );
		return strtr( mb_strtolower( trim( $text ), 'UTF-8' ), dli_people_search_letters() );
	}
}

if ( ! function_exists( 'dli_people_row_matches_search' ) ) {
	/**
	 * Whether a row of the People page matches a search.
	 *
	 * A row is a person with one of its types. It matches when the normalized text is part of «name surname», of
	 * «surname name», of the name of the type of the row or of the name of one of the structures of the person. An
	 * empty text matches every row. Words of two different fields do not match together.
	 *
	 * @param string               $search The text of the search.
	 * @param array<string, mixed> $row    The row: `name`, `surname`, `type` (strings) and `structures` (names).
	 * @return bool Whether the row matches.
	 */
	function dli_people_row_matches_search( $search, array $row ) {
		$search = dli_normalize_people_search( $search );
		if ( '' === $search ) {
			return true;
		}

		$name    = isset( $row['name'] ) ? (string) $row['name'] : '';
		$surname = isset( $row['surname'] ) ? (string) $row['surname'] : '';
		$fields  = array(
			$name . ' ' . $surname,
			$surname . ' ' . $name,
			isset( $row['type'] ) ? (string) $row['type'] : '',
		);
		if ( isset( $row['structures'] ) && is_array( $row['structures'] ) ) {
			foreach ( $row['structures'] as $structure ) {
				$fields[] = (string) $structure;
			}
		}

		foreach ( $fields as $field ) {
			if ( false !== strpos( dli_normalize_people_search( $field ), $search ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'dli_people_search_from_request' ) ) {
	/**
	 * The search asked for in the address of the People page.
	 *
	 * The parameter is `cerca`. Before it, the search was on the surname only and its parameter was `cognome`: an old
	 * link with `cognome` still works, and is read only when `cerca` is not there (empty or made of spaces only).
	 *
	 * @param string $cerca   The value of `cerca`, already sanitized.
	 * @param string $cognome The value of `cognome`, already sanitized.
	 * @return string The search, without spaces around.
	 */
	function dli_people_search_from_request( $cerca, $cognome ) {
		$cerca = trim( (string) $cerca );
		return '' !== $cerca ? $cerca : trim( (string) $cognome );
	}
}

if ( ! function_exists( 'dli_people_filter_query_args' ) ) {
	/**
	 * The arguments of the address that keep the active filters of the People page, for the links between the views.
	 *
	 * Only the active filters are kept, and the search is always `cerca` (never the old `cognome`).
	 *
	 * @param string $structure Slug of the structure.
	 * @param string $level     Slug of the tag.
	 * @param string $type      ID of the type of person.
	 * @param string $search    Text of the search.
	 * @return array<string, string> The arguments, by name.
	 */
	function dli_people_filter_query_args( $structure, $level, $type, $search ) {
		return array_filter(
			array(
				'struttura' => (string) $structure,
				'level'     => (string) $level,
				'tipologia' => (string) $type,
				'cerca'     => (string) $search,
			),
			static function ( $value ) {
				return '' !== $value;
			}
		);
	}
}
