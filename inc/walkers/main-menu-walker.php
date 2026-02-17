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
	/**
	 * In-request cache for menu trees, keyed by menu term ID.
	 *
	 * @var array<int, array>
	 */
	protected $dli_menu_tree_cache = array();

	/**
	 * Get the menu tree for the current walker args, cached per request.
	 *
	 * @param object $args Walker arguments.
	 * @return array
	 */
	protected function dli_get_menu_tree( $args ) {
		if ( ! isset( $args->menu ) || ! isset( $args->menu->term_id ) ) {
			return array();
		}

		$menu_id = intval( $args->menu->term_id );
		if ( isset( $this->dli_menu_tree_cache[ $menu_id ] ) ) {
			return $this->dli_menu_tree_cache[ $menu_id ];
		}

		$menu_items = wp_get_nav_menu_items( $menu_id, array( 'order' => 'DESC' ) );
		$menu_tree  = $menu_items ? dli_menu_tree_by_items( $menu_items ) : array();

		$this->dli_menu_tree_cache[ $menu_id ] = $menu_tree;
		return $menu_tree;
	}

	/**
	 * Start the element output.
	 *
	 * @param string $output Output HTML.
	 * @param object $item   Menu item.
	 * @param int    $depth  Depth level.
	 * @param array  $args   Walker args.
	 * @param int    $id     Item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		// Set active tab.
		$active_class = '';
		if ( get_permalink() === $item->url ) {
			$active_class = 'active';
		}

		// Set data-element for crawler.
		$data_element = '';
		if ( 'Persone' === $item->title ) {
			$data_element .= 'people';
		}
		if ( 'Progetti' === $item->title ) {
			$data_element .= 'projects';
		}
		if ( 'Attività di ricerca' === $item->title ) {
			$data_element .= 'research';
		}
		if ( 'Pubblicazioni' === $item->title ) {
			$data_element .= 'publications';
		}

		if ( $item->url && '#' !== $item->url ) {
			if ( true === $args->walker->has_children && 0 === $depth && '0' === $item->menu_item_parent ) {
				$output   .= '<li class="nav-item dropdown">';
				$output   .= '<a class="nav-link ' . $active_class . ' dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdown1">
				<span>';
				$output   .= esc_attr( $item->title );
				$output   .= '</span><svg class="icon icon-xs" role="img" aria-labelledby="Expand">
					<title>Expand</title>
					<use href="';
				$output   .= get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand';
				$output   .= '"></use></svg></a>';
				$output   .= '<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown1">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<li><a class="dropdown-item list-item" href="';
				$output   .= esc_attr( $item->url );
				$output   .= '"><span>';
				$output   .= esc_attr( $item->title );
				$output   .= '</span></a></li>
								<li><span class="divider"></span></li>';
				$menu_tree = $this->dli_get_menu_tree( $args );
				$children  = isset( $menu_tree[ $item->ID ]['children'] ) ? $menu_tree[ $item->ID ]['children'] : array();
				foreach ( $children as $subitem ) {
					$output .= '<li><a class="dropdown-item list-item" href="';
					$output .= esc_attr( $subitem->url );
					$output .= '"><span>';
					$output .= esc_attr( $subitem->title );
					$output .= '</span></a></li>';
				} // End foreach.
				$output .= '</ul></div></div>';
			} elseif ( false === $args->walker->has_children && '0' === $item->menu_item_parent ) {
				$output .= "<li class='nav-item'>";
				$output .= '<a class="nav-link ' . $active_class . '" href="' . $item->url . '" data-element="' . $data_element . '">';
			}
		}

		if ( false === $args->walker->has_children && '0' === $item->menu_item_parent ) {
			$output .= '<span>' . $item->title . '</span>';
		}

		if ( $item->url && '#' !== $item->url ) {
			if ( false === $args->walker->has_children && '0' === $item->menu_item_parent ) {
				$output .= '</a>';
			}
		}
	}
}
