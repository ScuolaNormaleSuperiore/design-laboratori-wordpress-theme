<?php
/**
 * Helpers of the base pages, shared by the page template and the homepage sections.
 *
 * Provides:
 *  - dli_get_subpage_cards()        The direct subpages of a page that can be listed as navigation cards.
 *  - dli_get_promoted_page_cards()  The pages promoted to the home that "Card di navigazione" lists as cards.
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
	 * @param int $limit The most cards to return; by default `DLI_HP_NAVIGATION_DEFAULT_ITEMS`.
	 * @return array<int, array{id: int, title: string, url: string, abstract: string}> The cards, in order.
	 */
	function dli_get_promoted_page_cards( $limit = DLI_HP_NAVIGATION_DEFAULT_ITEMS ) {
		$pages = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'has_password'   => false,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'posts_per_page' => max( 1, (int) $limit ),
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Few pages are promoted.
					array(
						'key'     => 'promuovi_in_home',
						'compare' => '=',
						'value'   => 1,
					),
				),
			)
		);

		$cards = array();
		foreach ( $pages as $page ) {
			$card = dli_get_page_card( $page );
			if ( null !== $card ) {
				$cards[] = $card;
			}
		}

		return $cards;
	}
}
