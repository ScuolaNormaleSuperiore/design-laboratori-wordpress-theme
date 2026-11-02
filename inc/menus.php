<?php
/**
 * Helpers of the header menus: the main menu (`DLI_Main_Menu_Walker`) and the secondary one (`secondary-menu.php`), which
 * print their items with the same partial, `template-parts/header/menu-nav-item.php`.
 *
 * Provides:
 *  - dli_get_menu_data_element()      The attribute `data-element` of a menu item, from its page or its title.
 *  - dli_get_menu_dropdown_id()       The identifier of the button that opens a dropdown, unique in the page.
 *  - dli_get_menu_item_data_element() The attribute of a nav menu item (WordPress needed).
 *  - dli_menu_item_has_address()      Whether a nav menu item leads somewhere.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_menu_data_element' ) ) {
	/**
	 * The value of `data-element` of a menu item, which the crawlers of Designers Italia read.
	 *
	 * It follows the page the item points to (its slug, in Italian or in English), so that it does not depend on the
	 * words of the title; when the page is not one of the four, the title is read as it always was, so that a site that
	 * built its menu on the titles keeps the attribute. Anything else has none.
	 *
	 * @param string $slug  The slug of the page the item points to, or an empty string.
	 * @param string $title The title of the item.
	 * @return string `people`, `projects`, `research`, `publications` or an empty string.
	 */
	function dli_get_menu_data_element( $slug, $title ) {
		$by_slug  = array(
			SLUG_PERSONE_IT       => 'people',
			SLUG_PERSONE_EN       => 'people',
			SLUG_PROGETTI_IT      => 'projects',
			SLUG_PROGETTI_EN      => 'projects',
			SLUG_RICERCA_IT       => 'research',
			SLUG_RICERCA_EN       => 'research',
			SLUG_PUBBLICAZIONI_IT => 'publications',
			SLUG_PUBBLICAZIONI_EN => 'publications',
		);
		$by_title = array(
			'Persone'             => 'people',
			'Progetti'            => 'projects',
			'Attività di ricerca' => 'research',
			'Pubblicazioni'       => 'publications',
		);

		if ( isset( $by_slug[ $slug ] ) ) {
			return $by_slug[ $slug ];
		}

		return $by_title[ $title ] ?? '';
	}
}

if ( ! function_exists( 'dli_get_menu_dropdown_id' ) ) {
	/**
	 * The identifier of the button that opens the dropdown of a menu item.
	 *
	 * It is made of the menu (`main-nav` or `secondary-nav`) and the ID of the menu item, so that every dropdown has
	 * its own identifier in the page, in the two menus too: the dropdown is named by its button with `aria-labelledby`.
	 *
	 * @param string     $prefix  The menu, for example `main-nav`; made safe to be part of an identifier.
	 * @param int|string $item_id The ID of the menu item.
	 * @return string The identifier.
	 */
	function dli_get_menu_dropdown_id( $prefix, $item_id ) {
		$prefix = trim( (string) preg_replace( '/[^a-z0-9_-]+/i', '-', (string) $prefix ), '-' );

		return $prefix . '-dropdown-' . (int) $item_id;
	}
}

if ( ! function_exists( 'dli_menu_item_has_address' ) ) {
	/**
	 * Whether a nav menu item leads somewhere: it has an address and it is not just `#`.
	 *
	 * @param object $item A nav menu item.
	 * @return bool True when the item has an address.
	 */
	function dli_menu_item_has_address( $item ) {
		$url = isset( $item->url ) ? trim( (string) $item->url ) : '';

		return '' !== $url && '#' !== $url;
	}
}

if ( ! function_exists( 'dli_get_menu_item_data_element' ) ) {
	/**
	 * The value of `data-element` of a nav menu item: from the page it points to, else from its title.
	 *
	 * @param object $item A nav menu item.
	 * @return string The value, or an empty string.
	 */
	function dli_get_menu_item_data_element( $item ) {
		$slug = '';
		if ( isset( $item->object, $item->object_id ) && 'page' === $item->object ) {
			$slug = (string) get_post_field( 'post_name', (int) $item->object_id );
		}

		return dli_get_menu_data_element( $slug, isset( $item->title ) ? (string) $item->title : '' );
	}
}
