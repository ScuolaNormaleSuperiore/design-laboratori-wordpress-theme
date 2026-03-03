<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_locations = $args['locations'];
?>

	<nav aria-label="Principale" class="p-0">
		<?php
		$dli_location = 'menu-lab';
		if ( has_nav_menu( $dli_location ) ) {
			wp_nav_menu(
				array(
					'theme_location'  => $dli_location,
					'depth'           => 1,
					'menu_class'      => 'navbar-nav',
					'items_wrap'      => '<ul class="%2$s" id="%1$s" data-element="main-navigation">%3$s</ul>',
					'container'       => '',
					'list_item_class' => 'nav-item',
					'link_class'      => 'nav-link',
					'walker'          => new Main_Menu_Walker(),
				)
			);
		}
		?>
</nav>