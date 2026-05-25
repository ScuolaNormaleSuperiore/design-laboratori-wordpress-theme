<?php
/**
 * Admin menu ordering and visibility.
 *
 * Hides the Comments menu item and defines a custom order
 * for the WordPress admin sidebar.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Hide Comments from the admin menu.
 */
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

/**
 * Enable custom admin menu order.
 */
add_filter( 'custom_menu_order', '__return_true' );

/**
 * Define custom admin menu order.
 *
 * Items not listed are appended after in their default order.
 *
 * @param array $menu_order Current menu order.
 * @return array Reordered menu.
 */
function dli_custom_menu_order( $menu_order ) {
	$custom_order = array(
		'index.php',                               // Dashboard
		'separator1',                              // ---
		'dli_options',                             // Configurazione
		'dli-tools',                               // Strumenti DLI
		'edit.php',                                // Articoli
		'edit.php?post_type=banner',               // Banner
		'edit.php?post_type=brevetto',             // Brevetti
		'edit.php?post_type=evento',               // Eventi
		'edit.php?post_type=indirizzo-di-ricerca', // Indirizzi di ricerca
		'edit.php?post_type=luogo',                // Luoghi
		'edit.php?post_type=notizia',              // Notizie
		'edit.php?post_type=page',                 // Pagine
		'edit.php?post_type=persona',              // Persone
		'edit.php?post_type=progetto',             // Progetti
		'edit.php?post_type=pubblicazione',        // Pubblicazioni
		'edit.php?post_type=risorsa-tecnica',      // Risorse Tecniche
		'edit.php?post_type=spinoff',              // Spin-off
		'edit.php?post_type=sponsor',              // Sponsor
		'separator2',                              // ---
		'upload.php',                              // Media
		'themes.php',                              // Aspetto
		'users.php',                               // Utenti
	);

	foreach ( $menu_order as $item ) {
		if ( ! in_array( $item, $custom_order, true ) ) {
			$custom_order[] = $item;
		}
	}

	return $custom_order;
}
add_filter( 'menu_order', 'dli_custom_menu_order' );
