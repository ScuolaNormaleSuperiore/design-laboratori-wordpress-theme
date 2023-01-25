<?php
/**
 * Action to add page templates used by theme
 */

add_action( 'after_switch_theme', 'dsi_create_pages_on_theme_activation' );

/**
 * Create default pages on theme activation.
 *
 * @return void
 */
function dsi_create_pages_on_theme_activation() {
	// verifico se è una prima installazione.
	$dsi_has_installed = get_option( 'dsi_has_installed' );

	// template page per Le Persone.
	$new_page_title    = __( 'Persone', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/persone.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'persone',
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// template page per Progetti.
	$new_page_title    = __( 'Progetti', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/progetti.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'progetti',
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// template page per la scuola.
	$new_page_title    = __( 'Attività di ricerca', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/attivita-ricerca.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'attivita-ricerca',
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		$lab_page_id = $new_page_id;
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	} else {
		$lab_page_id = $page_check->ID;
	}

	// template page per Progetti.
	$new_page_title    = __( 'Pubblicazioni', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/pubblicazioni.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'pubblicazioni',
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// template page per La Home di Sezione Notizie.
	$new_page_title    = __( 'News', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/notizie.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'news',
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
			$new_page_id = wp_insert_post( $new_page );
			if ( ! empty( $new_page_template ) ) {
				update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
			}
	}

	// template page per Presentazione.
	$new_page_title    = __( 'Presentazione', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = 'Il nostro laboratorio.'; // Content goes here.
	$new_page_template = 'page-templates/presentazione.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'presentazione',
		// 'post_parent'  => $lab_page_id,
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
		$presentazione_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $presentazione_page_id, '_wp_page_template', $new_page_template );
		}
	} else {
		$presentazione_page_id = $page_check->ID;
		update_post_meta( $presentazione_page_id, '_wp_page_template', $new_page_template );
	}

	/**
	 * Creazione del menu Laboratorio.
	 */
	$name = __( 'La Scuola' , 'design_laboratori_italia' );

	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {

			$menu_laboratorio = $menu_object->term_id;

	} else {

		$menu_id          = wp_create_nav_menu( $name );
		$menu             = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_laboratorio = $menu_id;

		$persone_id = dsi_get_template_page_id( 'page-templates/persone.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Le persone', 'design_laboratori_italia' ),
				'menu-item-object-id' => $persone_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		// $numeri_id = dsi_get_template_page_id("page-templates/numeri.php");
		// wp_update_nav_menu_item($menu->term_id, 0, array(
		// 		'menu-item-title' => __('I numeri della scuola', 'design_laboratori_italia'),
		// 		'menu-item-object-id' => $numeri_id,
		// 		'menu-item-object' => 'page',
		// 		'menu-item-status' => 'publish',
		// 		'menu-item-type' => 'post_type',
		// 		'menu-item-classes' => 'footer-link',
		// ));


		$locations_primary_arr = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-scuola'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

}
/**
 * Update the theme.
 *
 * @return void
 */
function dsi_add_update_theme_page() {
		add_theme_page( 'Ricarica i dati', 'Ricarica i dati', 'edit_theme_options', 'reload-data-theme-options', 'dsi_reload_theme_option_page' );
}
add_action( 'admin_menu', 'dsi_add_update_theme_page' );

/**
 * Reload the theme.
 *
 * @return void
 */
function dsi_reload_theme_option_page() {
		if( isset( $_GET['action'] ) && $_GET['action'] == 'reload' ){
			dsi_create_pages_on_theme_activation();
		}
		echo "<div class='wrap'>";
		echo '<h1>Ricarica i dati di attivazione del tema</h1>';
		echo '<a href="themes.php?page=reload-data-theme-options&action=reload" class="button button-primary">Ricarica i dati di attivazione (menu, tipologie, etc)</a>';
		echo '</div>';
}
