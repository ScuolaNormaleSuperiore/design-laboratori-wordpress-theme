<?php
	$current_group = $args['current_group'];
	$locations     = $args['locations'];
?>

<nav aria-label="Secondaria">
	<?php
	$location   = "menu-right";
	$menu       = wp_get_nav_menu_object( $locations[ $location ] );
	$menuitems  = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
	$menuitems  = $menuitems  ? $menuitems : array();

	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location'  => $location,
				'depth'           => 0,
				'menu_class'      => 'navbar-nav navbar-secondary',
				'container'       => '',
				'list_item_class' => 'nav-item',
				'link_class'      => 'nav-link',
				'current_group'   => $current_group,
				'walker'          => new Main_Menu_Walker(),
			)
		);
	}
	?>
</nav>