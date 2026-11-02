<?php
/**
 * Helpers of the base pages, shared by the page template and the homepage sections.
 *
 * Provides:
 *  - dli_get_subpage_cards()        The direct subpages of a page that can be listed as navigation cards.
 *  - dli_get_promoted_page_cards()  The pages promoted to the home that "Card di navigazione" lists as cards, by level.
 *  - dli_get_promoted_page_groups() The promoted pages with their subpages, for "Navigazione per pagine".
 *  - dli_get_page_in_current_language() A page, or its translation, in the current language, when it can be shown.
 *  - dli_get_pages_for_select()      The pages of the default language that an editor can choose in an options field.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_page_card' ) ) {
	/**
	 * A page as the data of a navigation card, or null when it must not be listed.
	 *
	 * A password-protected page is not listed: its title and text are what the password protects, and WordPress
	 * would print "There is no excerpt because this is a protected post" as its abstract. The abstract is the
	 * summary written by the editor or, when there is none, the beginning of the text without tags, blocks and
	 * shortcodes, cut at `DLI_PAGE_CARD_EXCERPT_WORDS` words.
	 *
	 * @param WP_Post $page The page.
	 * @return array{id: int, title: string, url: string, abstract: string}|null The card, or null for a protected page.
	 */
	function dli_get_page_card( $page ) {
		if ( post_password_required( $page ) ) {
			return null;
		}

		$abstract = trim( $page->post_excerpt );
		if ( '' === $abstract ) {
			$text     = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $page->post_content ) ) );
			$abstract = wp_trim_words( $text, DLI_PAGE_CARD_EXCERPT_WORDS );
		}

		return array(
			'id'       => $page->ID,
			'title'    => get_the_title( $page ),
			'url'      => (string) get_permalink( $page ),
			'abstract' => $abstract,
		);
	}
}

if ( ! function_exists( 'dli_get_subpage_cards' ) ) {
	/**
	 * The direct subpages of a page, ready to be printed as navigation cards.
	 *
	 * Only published pages are listed, in the order of the menu. A password-protected subpage is left
	 * out: its title and text are what the password protects, and WordPress would print "There is no
	 * excerpt because this is a protected post" as its abstract. The abstract is the summary written
	 * by the editor or, when there is none, the beginning of the text without tags, blocks and
	 * shortcodes, cut at `DLI_PAGE_CARD_EXCERPT_WORDS` words.
	 *
	 * @param WP_Post|int $page The parent page.
	 * @return array<int, array{id: int, title: string, url: string, abstract: string}> The cards, in order.
	 */
	function dli_get_subpage_cards( $page ) {
		$page = get_post( $page );
		if ( ! $page instanceof WP_Post ) {
			return array();
		}

		$children = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'post_parent'    => $page->ID,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'posts_per_page' => -1,
			)
		);

		$cards = array();
		foreach ( $children as $child ) {
			$card = dli_get_page_card( $child );
			if ( null !== $card ) {
				$cards[] = $card;
			}
		}

		return $cards;
	}
}

if ( ! function_exists( 'dli_get_promoted_page_cards' ) ) {
	/**
	 * The pages promoted to the home, as cards, for the section "Card di navigazione".
	 *
	 * They are the published pages of the current language whose field "Promuovi in Home" is on, ordered by the
	 * field "Ordine" of the page (`menu_order`, Attributi pagina) and then by title; the password-protected ones
	 * are left out before counting, so they do not take a place. The query does not touch the global post.
	 *
	 * The level of a page is its depth in the tree: 1 for a page without a parent, 2 for its subpages, and so on.
	 * By default only the pages of the first level are listed, so a promoted subpage is not a card; a wider level
	 * lists the pages up to it, and 0 lists all of them. The limit is applied after the level, so the pages that
	 * are left out never take a place.
	 *
	 * @param int $limit     The most cards to return; by default `DLI_HP_CARD_NAVIGATION_DEFAULT_ITEMS`.
	 * @param int $max_level The deepest level listed: 1, 2, 3, or 0 for all the levels; by default
	 *                       `DLI_HP_CARD_NAVIGATION_DEFAULT_LEVEL`. A value that is not one of them is the default.
	 * @return array<int, array{id: int, title: string, url: string, abstract: string}> The cards, in order.
	 */
	function dli_get_promoted_page_cards( $limit = DLI_HP_CARD_NAVIGATION_DEFAULT_ITEMS, $max_level = DLI_HP_CARD_NAVIGATION_DEFAULT_LEVEL ) {
		$limit     = max( 1, (int) $limit );
		$max_level = dli_normalize_hp_page_level( $max_level );
		$args      = array(
			'post_type'      => WP_DEFAULT_PAGE,
			'post_status'    => 'publish',
			'has_password'   => false,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'posts_per_page' => $limit,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Few pages are promoted.
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		);
		if ( 1 === $max_level ) {
			// The first level is a condition of the query, so that the limit is applied to the right pages.
			$args['post_parent'] = 0;
		} elseif ( 0 !== $max_level ) {
			// A deeper level is counted from the ancestors of each page, so all the promoted pages are read first.
			$args['posts_per_page'] = -1;
		}

		$cards = array();
		foreach ( get_posts( $args ) as $page ) {
			if ( $max_level > 1 && count( get_post_ancestors( $page ) ) >= $max_level ) {
				continue;
			}
			$card = dli_get_page_card( $page );
			if ( null !== $card ) {
				$cards[] = $card;
			}
			if ( count( $cards ) >= $limit ) {
				break;
			}
		}

		return $cards;
	}
}

if ( ! function_exists( 'dli_get_promoted_page_groups' ) ) {
	/**
	 * The promoted pages with their subpages, as groups, for the section "Navigazione per pagine".
	 *
	 * A group is a published page of the current language with the field "Promuovi in Home" on that has at least
	 * one published subpage without a password; the others are left out. Groups and subpages are ordered by the
	 * field "Ordine" of the page (`menu_order`) and then by title, and the limit is applied after that, so the
	 * ones kept are the first by order. Password-protected pages and subpages are never listed and do not take a
	 * place. The menus of the site are not read: the subpages are the ones of `post_parent`.
	 *
	 * @param int $max_groups The most groups to return.
	 * @param int $max_items  The most subpages to return in each group.
	 * @return array<int, array{id: int, title: string, items: array<int, array{id: int, title: string, url: string}>}> The groups, in order.
	 */
	function dli_get_promoted_page_groups( $max_groups, $max_items ) {
		$max_groups = max( 1, (int) $max_groups );
		$max_items  = max( 1, (int) $max_items );

		$parents = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'has_password'   => false,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'posts_per_page' => -1,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Few pages are promoted.
					array(
						'key'     => 'promuovi_in_home',
						'compare' => '=',
						'value'   => 1,
					),
				),
			)
		);

		$groups = array();
		foreach ( $parents as $parent ) {
			$children = get_posts(
				array(
					'post_type'      => WP_DEFAULT_PAGE,
					'post_status'    => 'publish',
					'has_password'   => false,
					'post_parent'    => $parent->ID,
					'orderby'        => array(
						'menu_order' => 'ASC',
						'title'      => 'ASC',
					),
					'posts_per_page' => $max_items,
				)
			);
			if ( empty( $children ) ) {
				continue;
			}

			$items = array();
			foreach ( $children as $child ) {
				$items[] = array(
					'id'    => $child->ID,
					'title' => get_the_title( $child ),
					'url'   => (string) get_permalink( $child ),
				);
			}

			$groups[] = array(
				'id'    => $parent->ID,
				'title' => get_the_title( $parent ),
				'items' => $items,
			);
			if ( count( $groups ) >= $max_groups ) {
				break;
			}
		}

		return $groups;
	}
}

if ( ! function_exists( 'dli_get_page_in_current_language' ) ) {
	/**
	 * A page in the current language, when it can be shown.
	 *
	 * The page is the one that is already in the current language or, for a page in another language, its
	 * translation. A page without a translation in the current language is not shown: Italian pages are never
	 * printed in an English site. Only a published page, of the type page, without a password, is given back.
	 *
	 * @param int $page_id The ID of a page, in any language.
	 * @return WP_Post|null The page to show, or null.
	 */
	function dli_get_page_in_current_language( $page_id ) {
		$page = get_post( (int) $page_id );
		if ( ! $page instanceof WP_Post || WP_DEFAULT_PAGE !== $page->post_type ) {
			return null;
		}

		$translations = dli_get_post_translations( $page->ID );
		$current      = dli_current_language();
		if ( ! empty( $translations ) ) {
			if ( empty( $translations[ $current ] ) ) {
				return null;
			}
			$page = get_post( (int) $translations[ $current ] );
		}

		if ( ! $page instanceof WP_Post || WP_DEFAULT_PAGE !== $page->post_type || 'publish' !== $page->post_status || '' !== $page->post_password ) {
			return null;
		}

		return $page;
	}
}

if ( ! function_exists( 'dli_get_pages_for_select' ) ) {
	/**
	 * The pages that an editor can choose in a field of the options pages, for `options_cb`.
	 *
	 * They are the published pages without a password in the default language, in the order of the tree: each
	 * subpage follows its parent and is indented with dashes; a page whose parent is not listed (a draft, a
	 * protected page) starts a subtree at the first level. The pages of the other languages are not listed:
	 * the choice is made once, and the translation is shown on the site (`dli_get_page_in_current_language()`).
	 *
	 * @return array<int, string> The titles of the pages, by page ID.
	 */
	function dli_get_pages_for_select() {
		$pages = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'has_password'   => false,
				'lang'           => dli_default_language(),
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'posts_per_page' => -1,
			)
		);

		// A page whose parent is not in the list (a draft, a protected page) starts a subtree at the first level.
		$listed   = wp_list_pluck( $pages, 'ID' );
		$children = array();
		foreach ( $pages as $page ) {
			$parent_id                = in_array( (int) $page->post_parent, $listed, true ) ? (int) $page->post_parent : 0;
			$children[ $parent_id ][] = $page;
		}

		$options = array();
		$walk    = static function ( $parent_id, $depth ) use ( &$walk, &$children, &$options ) {
			foreach ( $children[ $parent_id ] ?? array() as $page ) {
				$options[ $page->ID ] = str_repeat( '— ', $depth ) . get_the_title( $page );
				$walk( (int) $page->ID, $depth + 1 );
			}
		};
		$walk( 0, 0 );

		return $options;
	}
}
