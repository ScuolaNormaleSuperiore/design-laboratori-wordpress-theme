<?php
	// $current_group = $args['current_group'];
	$locations     = $args['locations'];
?>

<nav aria-label="Navigazione accessoria">
	<a class="it-opener d-lg-none" data-bs-toggle="collapse" href="#menu1a" role="button"
	aria-expanded="false" aria-controls="menu4">
	<span><?php echo esc_html__( 'News e contatti', 'design_laboratori_italia' ); ?></span>
	<svg class="icon" aria-hidden="true" role="img" aria-labelledby="Expand" aria-label="Expand">
		<title>Expand</title>
		<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
	</svg>
	</a>
	<div class="link-list-wrapper collapse" id="menu1a">
	<?php
		$menu_name = 'menu-header-right';
		$menu_items = array();
		if ( has_nav_menu( $menu_name ) ) {
			$menu       = wp_get_nav_menu_object( $locations[ $menu_name ] );
			$menuitems  = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
			$menuitems  = $menuitems  ? $menuitems : array();
		?>
		<ul class="link-list">
		<?php
		foreach ( $menuitems as $item ) {
			$active_class = '';
			if ( get_permalink() === $item->url ) {
				$active_class = 'active';
			}
		?>
		<li><a class="list-item <?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $item->url ); ?>" aria-current="page"><?php echo esc_html( $item->title ); ?></a></li>
			<?php
			}
			?>
		</ul>
		<?php
			}
		?>
	</div> <!-- menu1a -->
</nav>
