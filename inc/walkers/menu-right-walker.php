<?php
/**
 * Nav Menu API: Menu_Header_Right_Walker class
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
class Menu_Right_Walker extends Walker_Nav_Menu {

	/**
	 * Renders a single menu item as an <li> for the header right-side menu.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item, used for padding.
	 * @param array  $args   Additional strings.
	 * @param int    $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$output .= "<li class='nav-item'>";

		$data_element = '';

		if ( ! $item->url ) {
			$item->url = '#';
		} elseif ( false !== strpos( $item->url, '/argomenti' ) ) {
			$data_element = "data-element='all-topics'";
		}

		$output .= '<a class="nav-link" href="' . esc_url( $item->url ) . '" ' . $data_element . '>';
		if ( $item->menu_order == $args->menu->count ) {
			$output .= '<span class="fw-bold">' . esc_html( $item->title ) . '</span>';
		} else {
			$output .= esc_html( $item->title );
		}

		$output .= '</a>';
	}
}
