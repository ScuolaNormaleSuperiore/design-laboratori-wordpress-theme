<?php
/**
 * Nav Menu API: DLI_Main_Menu_Walker class
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
class DLI_Main_Menu_Walker extends Walker_Nav_Menu {
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
	 * Only the first level is walked (`depth` 1): each item, with its subpages from the menu tree, is printed by the
	 * partial that the secondary menu uses too (`template-parts/header/menu-nav-item.php`), complete with its closing tag.
	 *
	 * @param string $output Output HTML.
	 * @param object $item   Menu item.
	 * @param int    $depth  Depth level.
	 * @param object $args   Walker arguments.
	 * @param int    $id     Item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( 0 !== $depth || '0' !== (string) $item->menu_item_parent ) {
			return;
		}

		$menu_tree = $this->dli_get_menu_tree( $args );
		$children  = isset( $menu_tree[ $item->ID ]['children'] ) ? $menu_tree[ $item->ID ]['children'] : array();

		ob_start();
		get_template_part(
			'template-parts/header/menu-nav-item',
			null,
			array(
				'item'      => $item,
				'children'  => $children,
				'id_prefix' => 'main-nav',
			)
		);
		$output .= (string) ob_get_clean();
	}

	/**
	 * End the element output: nothing, the partial prints the closing tag of the item itself.
	 *
	 * An item that leads nowhere is not printed at all, so no closing tag may be added here for it.
	 *
	 * @param string $output Output HTML.
	 * @param object $item   Menu item.
	 * @param int    $depth  Depth level.
	 * @param object $args   Walker arguments.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
	}
}
