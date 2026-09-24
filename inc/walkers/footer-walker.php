<?php
/**
 * Nav Menu API: Header_Walker_Nav_Menu class
 *
 * @package WordPress
 * @subpackage Nav_Menus
 * @since 4.6.0
 */

/**
 * Custom class used to implement an HTML list of nav menu items.
 *
 * @since 3.0.0
 *
 * @see Walker
 */
class Footer_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Renders a single menu item as an <li> for the footer menu.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item, used for padding.
	 * @param array  $args   Additional strings.
	 * @param int    $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$output     .= '<li>';
		$custom_data = '';
		if ( 'privacy-policy' === $item->post_name || stripos( $item->title, 'privacy' ) !== false ) {
			$custom_data = 'data-element="privacy-policy-link"';
		} elseif ( 'dichiarazione-di-accessibilita' === $item->post_name || stripos( $item->title, 'accessibilità' ) !== false ) {
			$custom_data = 'data-element="accessibility-link"';
		}
		if ( $item->url ) {
			$output .= '<a class="text-underline-hover" href="' . esc_url( $item->url ) . '" ' . $custom_data . '>';
		} else {
			$output .= '<a class="text-underline-hover" href="#" ' . $custom_data . '>';
		}

		$output .= esc_html( $item->title );
		$output .= '</a>';
	}
}
