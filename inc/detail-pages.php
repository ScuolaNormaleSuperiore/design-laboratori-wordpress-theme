<?php
/**
 * Detail pages that the editor has switched off.
 *
 * A person can be set up without a detail page (`disattiva_pagina_dettaglio`), and an event, a news item or a
 * publication can lead somewhere else, to a website or to an attachment (`link_dettaglio` other than `scheda`).
 * The lists and the search already leave the link out; this file makes the page itself answer 404, with the one
 * rule that every other place asks.
 *
 * Provides:
 *  - dli_is_detail_page_disabled()        Whether the detail page of a content has been switched off.
 *  - dli_block_disabled_detail_pages()    Answers 404 on the detail page of a content that has been switched off.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_is_detail_page_disabled' ) ) {
	/**
	 * Whether the editor has switched off the detail page of a content.
	 *
	 * - A person: the field "Disattiva pagina dettaglio". The direct link to the personal site only changes where
	 *   the link leads and does not close the page.
	 * - An event, a news item or a publication: a link mode other than the detail page (`DLI_ITEM_LINK`). An empty or
	 *   unknown mode is the detail page, as in `dli_manage_item_link()`.
	 * - A publication protected by a password always leads to its own page, so its page stays open.
	 * - Any other content type has no such choice: it is never switched off.
	 *
	 * @param WP_Post|int|null $post The content.
	 * @return bool True when the detail page has been switched off.
	 */
	function dli_is_detail_page_disabled( $post ) {
		$post = get_post( $post );
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		switch ( $post->post_type ) {
			case PEOPLE_POST_TYPE:
				return (bool) dli_get_field( 'disattiva_pagina_dettaglio', $post->ID );
			case PUBLICATION_POST_TYPE:
				if ( '' !== $post->post_password ) {
					return false;
				}
				return dli_has_link_mode_without_detail_page( $post );
			case EVENT_POST_TYPE:
			case NEWS_POST_TYPE:
				return dli_has_link_mode_without_detail_page( $post );
			default:
				return false;
		}
	}
}

if ( ! function_exists( 'dli_has_link_mode_without_detail_page' ) ) {
	/**
	 * Whether the field "Link dettaglio" of a content leads to a website or to an attachment.
	 *
	 * @param WP_Post $post The content.
	 * @return bool True for the website and the attachment, false for the detail page, empty or unknown.
	 */
	function dli_has_link_mode_without_detail_page( $post ) {
		$mode = dli_get_field( 'link_dettaglio', $post->ID );
		return in_array( $mode, array( DLI_ITEM_LINK['WEBSITE'], DLI_ITEM_LINK['ATTACHMENT'] ), true );
	}
}

if ( ! function_exists( 'dli_block_disabled_detail_pages' ) ) {
	/**
	 * Answer 404 on the detail page of a content whose detail page has been switched off.
	 *
	 * Whoever can edit the content still sees it, otherwise the editor could not fill the page in. It runs before
	 * the canonical redirect, so a closed page is never redirected to another address.
	 *
	 * @return void
	 */
	function dli_block_disabled_detail_pages() {
		if ( ! is_singular( array( PEOPLE_POST_TYPE, EVENT_POST_TYPE, NEWS_POST_TYPE, PUBLICATION_POST_TYPE ) ) ) {
			return;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post || ! dli_is_detail_page_disabled( $post ) ) {
			return;
		}

		if ( current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'dli_block_disabled_detail_pages', 5 );
