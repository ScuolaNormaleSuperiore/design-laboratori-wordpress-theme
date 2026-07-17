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

class Header_Menu_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$output        .= '<li>';
		$item_url       = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
			$item_title = isset( $item->title ) ? esc_html( $item->title ) : '';
		$custom_data    = '';

		if ( stripos( $item->title, 'personale scolastico' ) !== false || stripos( $item->title, 'famiglie e studenti' ) !== false ) {
			$custom_data = 'data-element="service-type"';
		}

		if ( $custom_data ) {
			$output .= '<a class="list-item" href="' . $item_url . '" ' . $custom_data . '>';
		} else {
			$output .= '<a class="list-item" href="' . $item_url . '">';
		}

		$output .= $item_title;
		$output .= '</a>';
	}
}
