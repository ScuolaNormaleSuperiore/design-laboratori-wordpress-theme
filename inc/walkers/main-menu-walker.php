<?php
/**
 * Nav Menu API: Main_Menu_Walker class
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


class Main_Menu_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth=0, $args=[], $id=0 ) {
		// set active tab
		// $group = $args->current_group;
		$active_class = '';
		if ( $item->url == get_permalink( ) ) {
			$active_class = 'active';
		}

		// set data-element for crawler
		$data_element = '';
		if ( $item->title == 'Persone' ) $data_element .= 'people'; 
		if ( $item->title == 'Progetti' ) $data_element .= 'projects'; 
		if ( $item->title == 'Attività di ricerca' ) $data_element .= 'research'; 
		if ( $item->title == 'Pubblicazioni' ) $data_element .= 'publications'; 
 
		if ( $item->url && $item->url != '#' ) {
			if ( $args->walker->has_children && $depth === 0 && $item->menu_item_parent === '0' ) {
				$output .= '<li class="nav-item dropdown">';
				$output .= '<a class="nav-link ' .$active_class. ' dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdown1">
				<span>';
				$output .= esc_attr( $item->title );
				$output .= '</span><svg class="icon icon-xs" role="img" aria-labelledby="Expand">
					<title>Expand</title>
					<use href="';
				$output .= get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand';
				$output .= '"></use></svg></a>';
				$output .= '<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown1">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<li><a class="dropdown-item list-item" href="';
				$output .= esc_attr( $item->url );
				$output .= '"><span>';
				$output .= esc_attr( $item->title );
				$output .= '</span></a></li>
								<li><span class="divider"></span></li>';
				//show sub pages
				if ( $args->menu ) {
					$menuitems  = $args->menu ? wp_get_nav_menu_items( $args->menu->term_id, array( 'order' => 'DESC' ) ) : array();
					$menuitems  = $menuitems  ? dli_menu_tree_by_items( $menuitems ) : array();
				}
				foreach ( $menuitems[$item->ID]['children'] as $subitem ) {
					$output .= '<li><a class="dropdown-item list-item" href="';
					$output .= esc_attr( $subitem->url );
					$output .= '"><span>';
					$output .= esc_attr( $subitem->title );
					$output .= '</span></a></li>';
				} // foreach
				$output .= '</ul></div></div>';
			}
			else if ( !$args->walker->has_children && $item->menu_item_parent === '0' ) {
				$output .= "<li class='nav-item'>";
				$output .= '<a class="nav-link '.$active_class.'" href="' . $item->url . '" data-element="'.$data_element.'">';
			}
		}
 
		if ( !$args->walker->has_children && $item->menu_item_parent === '0' ) {
			$output .= '<span>' . $item->title . '</span>';
		}
 
		if ( $item->url && $item->url != '#' ) {
			if ( !$args->walker->has_children && $item->menu_item_parent === '0' ) {
				$output .= "</a>";
			}
		}
	}
}