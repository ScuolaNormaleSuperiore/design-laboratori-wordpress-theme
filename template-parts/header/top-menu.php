<?php
/**
 * Header top menu template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_locations = ( isset( $args['locations'] ) && is_array( $args['locations'] ) ) ? $args['locations'] : array();
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
		$dli_menu_name = 'menu-header-right';
		$dli_menu          = null;
		$dli_menuitems = array();
	if ( has_nav_menu( $dli_menu_name ) && array_key_exists( $dli_menu_name, $dli_locations ) ) {
		$dli_menu = wp_get_nav_menu_object( $dli_locations[ $dli_menu_name ] );
		if ( ! $dli_menu || ! isset( $dli_menu->term_id ) ) {
			$dli_menu = null;
		}
	}

	if ( ! empty( $dli_menu ) ) {
		$dli_menuitems = wp_get_nav_menu_items( $dli_menu->term_id, array( 'order' => 'DESC' ) );
		$dli_menuitems = $dli_menuitems ? $dli_menuitems : array();
		?>
		<ul class="link-list">
		<?php
		foreach ( $dli_menuitems as $dli_item ) {
			$dli_active_class = '';
			if ( get_permalink() === $dli_item->url ) {
				$dli_active_class = 'active';
			}
			?>
		<li><a class="list-item <?php echo esc_attr( $dli_active_class ); ?>" href="<?php echo esc_url( $dli_item->url ); ?>" aria-current="page"><?php echo esc_html( $dli_item->title ); ?></a></li>
			<?php
		}
		?>
		</ul>
		<?php
	}
	?>
	</div> <!-- menu1a -->
</nav>
