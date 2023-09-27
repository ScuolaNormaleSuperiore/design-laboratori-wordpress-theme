<?php
	$current_group = $args['current_group'];
	$locations     = $args['locations'];
?>

	<nav aria-label="Principale">
		<?php
		$location = 'menu-lab';
		if ( has_nav_menu( $location ) ) {
			wp_nav_menu(
				array(
					'theme_location'  => $location,
					'depth'           => 1,
					'menu_class'      => 'navbar-nav',
					'items_wrap'      => '<ul class="%2$s" id="%1$s" data-element="main-navigation">%3$s</ul>',
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