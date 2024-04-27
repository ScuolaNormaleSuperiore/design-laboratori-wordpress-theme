<?php
	// $current_group = $args['current_group'];
	$locations     = $args['locations'];
?>

<nav aria-label="Secondaria">
	<?php
	$location   = "menu-right";

	$menu       = array_key_exists( $location, $locations) ? wp_get_nav_menu_object( $locations[ $location ] ) : array();
	if ( $menu ) {
		$menuitems  = $menu ? wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) ) : array();
		$menuitems  = $menuitems  ? dli_menu_tree_by_items( $menuitems ) : array();

		if ( has_nav_menu( $location ) && count( $menuitems  ) ) {
		?>
		<ul id="secondary-menu" class="navbar-nav navbar-secondary">
			<?php
				foreach ( $menuitems as $item ) {
					$active_class = '';
					if ( get_permalink( ) == $item['element']->url ) {
						$active_class = 'active';
					}
					if ( count( $item['children'] ) > 0 ) {
			?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle <?php echo $active_class;?>" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdown1">
						<span><?php echo esc_attr( $item['element']->title); ?></span>
						<svg class="icon icon-xs" role="img" aria-labelledby="Expand">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
						</svg>
					</a>
					<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown1">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<li><a class="dropdown-item list-item" href="<?php echo esc_attr( $item['element']->url ); ?>"><span><?php echo esc_attr( $item['element']->title ); ?></span></a></li>
								<li><span class="divider"></span></li>
								<?php
									foreach ( $item['children'] as $subitem ) {
								?>
								<li><a class="dropdown-item list-item" href="<?php echo esc_attr( $subitem->url); ?>"><span><?php echo esc_attr( $subitem->title ); ?></span></a></li>
								<?php
									} // foreach
								?>
							</ul>
						</div>
					</div>
				</li>
			<?php
					} else {
			?>
				<li class="nav-item">
					<a class="nav-link <?php echo $active_class;?>" href="<?php echo esc_url( $item['element']->url ); ?>"><span><?php echo esc_attr( $item['element']->title ); ?></span></a>
				</li>
			<?php
					} // else
				} // foreach
			?>
		</ul> <!-- secondary-menu -->
		<?php
		} // has_nav_menu
	}
	?>
</nav>
