<?php
/**
 * Data of the homepage section "Pannello tab".
 *
 * The tabs are one global set, written in Configurazione → Layout HP (group `home_tabs_items`); every copy of the
 * section in Sezioni HP shows the same set. The pure rules (which row is a tab, English falling back to Italian)
 * are in `inc/home-sections.php`; here the pages linked to a tab are resolved with WordPress and Polylang.
 *
 * Provides:
 *  - dli_get_tab_pages() The pages linked to a tab that can be shown in the current language.
 *  - dli_get_home_tabs() The tabs of the section, ready to print.
 *  - dli_get_tab_pages_for_select() The pages to choose for a tab, with the ones already chosen.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! function_exists( 'dli_get_tab_pages' ) ) {
	/**
	 * The pages linked to a tab, as links, in the current language.
	 *
	 * The IDs are the ones chosen by the editor, from the pages of the default language; each one is shown as its
	 * translation (`dli_get_page_in_current_language()`). A page that does not exist, is a draft or protected, has
	 * no translation, or is not a page, is left out. The order of the choice is kept.
	 *
	 * @param int[] $page_ids The chosen page IDs.
	 * @return array<int, array{id: int, title: string, url: string}> The links.
	 */
	function dli_get_tab_pages( $page_ids ) {
		$pages = array();
		foreach ( dli_sanitize_hp_tab_page_ids( $page_ids ) as $page_id ) {
			$page = dli_get_page_in_current_language( $page_id );
			if ( null === $page || isset( $pages[ $page->ID ] ) ) {
				continue;
			}

			$pages[ $page->ID ] = array(
				'id'    => $page->ID,
				'title' => get_the_title( $page ),
				'url'   => (string) get_permalink( $page ),
			);
		}

		return array_values( $pages );
	}
}

if ( ! function_exists( 'dli_get_home_tabs' ) ) {
	/**
	 * The tabs of the section "Pannello tab" in the current language, ready to print.
	 *
	 * A tab with no text and no page that can be shown is left out, so that no panel is empty
	 * (`dli_get_hp_tabs_for_language()` decides on the label and the text, this function on the pages).
	 *
	 * @return array<int, array{label: string, text: string, pages: array<int, array{id: int, title: string, url: string}>}> The tabs, in order.
	 */
	function dli_get_home_tabs() {
		$rows = dli_normalize_hp_tab_rows( dli_get_option( 'home_tabs_items', 'homepage' ) );

		$is_italian = ( dli_current_language() === DLI_IT_SLUG );

		$tabs = array();
		foreach ( dli_get_hp_tabs_for_language( $rows, $is_italian ) as $tab ) {
			$pages = dli_get_tab_pages( $tab['page_ids'] );
			if ( '' === $tab['text'] && empty( $pages ) ) {
				continue;
			}

			$tabs[] = array(
				'label' => $tab['label'],
				'text'  => $tab['text'],
				'pages' => $pages,
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'dli_get_tab_pages_for_select' ) ) {
	/**
	 * The pages an editor can choose for a tab, for the `options_cb` of the field "Pagine collegate".
	 *
	 * They are the pages of `dli_get_pages_for_select()` plus the ones already chosen by any tab that can no longer
	 * be shown (a draft, a protected page, a page of another language): the multiple choice drops from the saved
	 * value what it does not list, so without them saving Layout HP for any reason would lose the link for good.
	 * A page that cannot be shown says so in its label. A page that no longer exists is not listed.
	 *
	 * @return array<int, string> The titles of the pages, by page ID.
	 */
	function dli_get_tab_pages_for_select() {
		$options = dli_get_pages_for_select();

		$chosen = array();
		foreach ( dli_normalize_hp_tab_rows( dli_get_option( 'home_tabs_items', 'homepage' ) ) as $row ) {
			$chosen = array_merge( $chosen, $row['page_ids'] );
		}
		return dli_add_chosen_pages_to_select( $options, $chosen );
	}
}
