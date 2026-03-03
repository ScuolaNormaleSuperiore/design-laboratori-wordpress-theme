<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_locations = $args['locations'];
?>

<nav aria-label="Secondaria">
	<?php
	$dli_location = 'menu-right';

	$dli_menu = array_key_exists( $dli_location, $dli_locations ) ? wp_get_nav_menu_object( $dli_locations[ $dli_location ] ) : array();
	if ( $dli_menu ) {
		$dli_menuitems = $dli_menu ? wp_get_nav_menu_items( $dli_menu->term_id, array( 'order' => 'DESC' ) ) : array();
		$dli_menuitems = $dli_menuitems ? dli_menu_tree_by_items( $dli_menuitems ) : array();

		if ( has_nav_menu( $dli_location ) && count( $dli_menuitems ) ) {
			?>
		<ul id="secondary-menu" class="navbar-nav">
			<?php
			foreach ( $dli_menuitems as $dli_item ) {
				$dli_active_class = '';
				if ( get_permalink() === $dli_item['element']->url ) {
					$dli_active_class = 'active';
				}
				if ( count( $dli_item['children'] ) > 0 ) {
					?>
				<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle <?php echo esc_attr( $dli_active_class ); ?>" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdown1">
						<span><?php echo esc_attr( $dli_item['element']->title ); ?></span>
						<svg class="icon icon-xs" role="img" aria-labelledby="Expand">
							<title>Expand</title>
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
						</svg>
					</a>
					<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown1">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<li><a class="dropdown-item list-item" href="<?php echo esc_attr( $dli_item['element']->url ); ?>"><span><?php echo esc_attr( $dli_item['element']->title ); ?></span></a></li>
								<li><span class="divider"></span></li>
							<?php
							foreach ( $dli_item['children'] as $dli_subitem ) {
								?>
								<li><a class="dropdown-item list-item" href="<?php echo esc_attr( $dli_subitem->url ); ?>"><span><?php echo esc_attr( $dli_subitem->title ); ?></span></a></li>
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
						<a class="nav-link <?php echo esc_attr( $dli_active_class ); ?>" href="<?php echo esc_url( $dli_item['element']->url ); ?>"><span><?php echo esc_attr( $dli_item['element']->title ); ?></span></a>
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
