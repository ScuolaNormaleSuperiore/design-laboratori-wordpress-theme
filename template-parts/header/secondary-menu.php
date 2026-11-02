<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_locations = ( isset( $args['locations'] ) && is_array( $args['locations'] ) ) ? $args['locations'] : array();
?>

<nav aria-label="<?php echo esc_attr__( 'Secondaria', 'design_laboratori_italia' ); ?>">
	<?php
	$dli_location = 'menu-right';

	$dli_menu = array_key_exists( $dli_location, $dli_locations ) ? wp_get_nav_menu_object( $dli_locations[ $dli_location ] ) : array();
	if ( $dli_menu ) {
		$dli_menuitems = wp_get_nav_menu_items( $dli_menu->term_id, array( 'order' => 'DESC' ) );
		$dli_menuitems = $dli_menuitems ? dli_menu_tree_by_items( $dli_menuitems ) : array();

		if ( has_nav_menu( $dli_location ) && count( $dli_menuitems ) ) {
			?>
		<ul id="secondary-menu" class="navbar-nav">
			<?php
			foreach ( $dli_menuitems as $dli_item ) {
				get_template_part(
					'template-parts/header/menu-nav-item',
					null,
					array(
						'item'      => $dli_item['element'],
						'children'  => $dli_item['children'],
						'id_prefix' => 'secondary-nav',
					)
				);
			} // foreach
			?>
		</ul> <!-- secondary-menu -->
			<?php
		} // has_nav_menu
	}
	?>
</nav>
