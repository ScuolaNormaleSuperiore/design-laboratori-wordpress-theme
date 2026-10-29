<?php
/**
 * Helpers of the base pages, shared by the page template and the homepage sections.
 *
 * Provides:
 *  - dli_get_subpage_cards()  The direct subpages of a page that can be listed as navigation cards.
 *
 * @package Design_Laboratori_Italia
 */

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
			if ( post_password_required( $child ) ) {
				continue;
			}

			$abstract = trim( $child->post_excerpt );
			if ( '' === $abstract ) {
				$text     = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $child->post_content ) ) );
				$abstract = wp_trim_words( $text, DLI_PAGE_CARD_EXCERPT_WORDS );
			}

			$cards[] = array(
				'id'       => $child->ID,
				'title'    => get_the_title( $child ),
				'url'      => (string) get_permalink( $child ),
				'abstract' => $abstract,
			);
		}

		return $cards;
	}
}
