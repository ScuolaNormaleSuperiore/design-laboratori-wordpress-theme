<?php
/**
 * An item of a header menu, as a first level item of the navigation bar.
 *
 * Shared by the main menu (`DLI_Main_Menu_Walker`) and the secondary menu (`secondary-menu.php`), so that they print the
 * same markup. An item with subpages that lead somewhere is a dropdown: a button (`dropdown-toggle`) and a menu that
 * the button names (`aria-labelledby`), with an identifier built on the menu item (`dli_get_menu_dropdown_id()`), so
 * that there is one for each dropdown and none is shared by the two menus. The menu starts with a link to the item
 * itself and a divider only when the item has an address of its own; a parent that is just `#` is only the button. An
 * item without an address and without subpages that lead somewhere is not printed, and so is a subpage without an
 * address: nothing is printed that leads nowhere, and no tag is left open or closed. The attribute `data-element`
 * (`dli_get_menu_item_data_element()`) is printed on the links that have one.
 *
 * Arguments (`$args`):
 *  - `item`      (object) The nav menu item.
 *  - `children`  (array)  Its subpages, nav menu items.
 *  - `id_prefix` (string) The menu: `main-nav` or `secondary-nav`.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args      = isset( $args ) && is_array( $args ) ? $args : array();
$dli_item      = isset( $dli_args['item'] ) && is_object( $dli_args['item'] ) ? $dli_args['item'] : null;
$dli_children  = isset( $dli_args['children'] ) && is_array( $dli_args['children'] ) ? $dli_args['children'] : array();
$dli_id_prefix = isset( $dli_args['id_prefix'] ) ? (string) $dli_args['id_prefix'] : 'main-nav';

if ( null === $dli_item ) {
	return;
}

$dli_children    = array_values( array_filter( $dli_children, 'dli_menu_item_has_address' ) );
$dli_has_address = dli_menu_item_has_address( $dli_item );
$dli_active      = get_permalink() === $dli_item->url ? ' active' : '';

if ( empty( $dli_children ) && ! $dli_has_address ) {
	return;
}

$dli_item_element = dli_get_menu_item_data_element( $dli_item );

if ( empty( $dli_children ) ) :
	?>
<li class="nav-item"><a class="nav-link<?php echo esc_attr( $dli_active ); ?>" href="<?php echo esc_url( $dli_item->url ); ?>"<?php echo '' !== $dli_item_element ? ' data-element="' . esc_attr( $dli_item_element ) . '"' : ''; ?>><span><?php echo esc_html( $dli_item->title ); ?></span></a></li>
	<?php
	return;
endif;

$dli_dropdown_id = dli_get_menu_dropdown_id( $dli_id_prefix, $dli_item->ID );
?>
<li class="nav-item dropdown">
	<a class="nav-link<?php echo esc_attr( $dli_active ); ?> dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="<?php echo esc_attr( $dli_dropdown_id ); ?>">
		<span><?php echo esc_html( $dli_item->title ); ?></span>
		<svg class="icon icon-xs" role="img">
			<title><?php echo esc_html__( 'Expand', 'design_laboratori_italia' ); ?></title>
			<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
		</svg>
	</a>
	<div class="dropdown-menu" role="region" aria-labelledby="<?php echo esc_attr( $dli_dropdown_id ); ?>">
		<div class="link-list-wrapper">
			<ul class="link-list">
				<?php if ( $dli_has_address ) : ?>
					<li><a class="dropdown-item list-item" href="<?php echo esc_url( $dli_item->url ); ?>"<?php echo '' !== $dli_item_element ? ' data-element="' . esc_attr( $dli_item_element ) . '"' : ''; ?>><span><?php echo esc_html( $dli_item->title ); ?></span></a></li>
					<li><span class="divider"></span></li>
				<?php endif; ?>
				<?php foreach ( $dli_children as $dli_child ) : ?>
					<li><a class="dropdown-item list-item" href="<?php echo esc_url( $dli_child->url ); ?>"<?php echo '' !== dli_get_menu_item_data_element( $dli_child ) ? ' data-element="' . esc_attr( dli_get_menu_item_data_element( $dli_child ) ) . '"' : ''; ?>><span><?php echo esc_html( $dli_child->title ); ?></span></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</li>
