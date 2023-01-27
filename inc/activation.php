<?php
/**
 * Action to add page templates used by theme
 */

add_action( 'after_switch_theme', 'dli_create_pages_on_theme_activation' );

/**
 * Attivazione del tema: creazione di contenuti, pagine e tassonomie.
 * @return void
 */
function dli_create_pages_on_theme_activation() {
	// Verifico se è una prima installazione.
	$dli_has_installed = get_option( 'dli_has_installed' );

	// Template page per Il laboratorio.
	$new_page_title    = __( 'Il Laboratorio', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/il-laboratorio.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'il-laboratorio',
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

	// Template page per Le Persone.
	$new_page_title    = __( 'Persone', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/persone.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'     => 'page',
		'post_title'    => $new_page_title,
		'post_content'  => $new_page_content,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_slug'     => 'persone',
		'post_parent'   => $lab_page_id,
		// 'page_template' => $new_page_template,
	);
	// If the page doesn't already exist, create it
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// template page per I Progetti.
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
		'post_parent'   => $lab_page_id,
		// 'page_template' => $new_page_template,
	);
	// If the page doesn't already exist, create it
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// template page per Attività di ricerca.
	$new_page_title    = __( 'Attività di ricerca', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = ''; // Content goes here.
	$new_page_template = 'page-templates/ricerca.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'ricerca',
		'post_parent'   => $lab_page_id,
		// 'page_template' => $new_page_template,
	);
	// If the page doesn't already exist, create it
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// Template page per le Pubblicazioni.
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
		'post_parent'   => $lab_page_id,
		// 'page_template' => $new_page_template,
	);
	// If the page doesn't already exist, create it
	if ( ! isset( $page_check->ID ) ) {
		$new_page_id = wp_insert_post( $new_page );
		if ( ! empty( $new_page_template ) ) {
			update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
		}
	}

	// Creazione della pagina Presentazione (Pagina statica).
	$new_page_title    = __( 'Presentazione', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = 'La nostra storia'; // Content goes here.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists
	// Store the above data in an array.
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'presentazione',
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

	// Template page per La Home di Sezione Notizie.
	$new_page_title    = __( 'Notizie', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = 'Qui ci sono le notizie'; // Content goes here.
	$new_page_template = 'page-templates/notizie.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'notizie'
	);
	// If the page doesn't already exist, create it.
	if ( ! isset( $page_check->ID ) ) {
			$new_page_id = wp_insert_post( $new_page );
			if ( ! empty( $new_page_template ) ) {
				update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
			}
	}

	// Template page per La Home di Sezione Servizi.
	$new_page_title    = __( 'Servizi', 'design_laboratori_italia' ); // Page's title.
	$new_page_content  = 'Qui ci sono i servizi'; // Content goes here.
	$new_page_template = 'page-templates/servizi.php'; // The template to use for the page.
	$page_check        = get_page_by_title( $new_page_title ); // Check if the page already exists.
	// Store the above data in an array
	$new_page = array(
		'post_type'    => 'page',
		'post_title'   => $new_page_title,
		'post_content' => $new_page_content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_slug'    => 'servizi'
	);
	// If the page doesn't already exist, create it
	if ( ! isset( $page_check->ID ) ) {
			$new_page_id = wp_insert_post( $new_page );
			if ( ! empty( $new_page_template ) ) {
				update_post_meta( $new_page_id, '_wp_page_template', $new_page_template );
			}
	}


	// ************ CREAZIONE DEI MENU ************

	/**
	 *  1 - Creazione del menu LABORATORIO.
	 */
	$name = __( 'Il Laboratorio', 'design_laboratori_italia' );

	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {
			$menu_lab = $menu_object->term_id;
	} else {
		$menu_id  = wp_create_nav_menu( $name );
		$menu     = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_lab = $menu_id;

		$persone_id = dsi_get_template_page_id( 'page-templates/persone.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Persone', 'design_laboratori_italia' ),
				'menu-item-object-id' => $persone_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$progetti_id = dsi_get_template_page_id( 'page-templates/progetti.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Progetti', 'design_laboratori_italia' ),
				'menu-item-object-id' => $progetti_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$ricerca_id = dsi_get_template_page_id( 'page-templates/ricerca.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Attività di ricerca', 'design_laboratori_italia' ),
				'menu-item-object-id' => $ricerca_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$pubblicazioni_id = dsi_get_template_page_id( 'page-templates/pubblicazioni.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Pubblicazioni', 'design_laboratori_italia' ),
				'menu-item-object-id' => $pubblicazioni_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$locations_primary_arr             = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-lab'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	/**
	 * 2 - Creazione del menu PRESENTAZIONE.
	 */
	$name = __( 'Presentazione', 'design_laboratori_italia' );

	// wp_delete_nav_menu( $name );
	// $menu_object = wp_get_nav_menu_object( $name );
	// if( $menu_object ) {
	// 	$menu_presentazione = $menu_object->term_id;
	// } else {

	// 	$menu_id            = wp_create_nav_menu( $name );
	// 	$menu               = get_term_by( 'id', $menu_id, 'nav_menu' );
	// 	$menu_presentazione = $menu_id;

	// 	$cicli_id = dsi_get_template_page_id( 'page-templates/cicli-scolastici.php' );
	// 	// wp_update_nav_menu_item(
	// 	// 	$menu->term_id,
	// 	// 	0,
	// 	// 	array(
	// 	// 		'menu-item-title'     => __( 'Offerta formativa', 'design_laboratori_italia' ),
	// 	// 		'menu-item-object-id' => $cicli_id,
	// 	// 		'menu-item-object'    => 'page',
	// 	// 		'menu-item-status'    => 'publish',
	// 	// 		'menu-item-type'      => 'post_type',
	// 	// 		'menu-item-classes'   => 'footer-link',
	// 	// 	)
	// 	// );

	// 	$locations_primary_arr                   = get_theme_mod( 'nav_menu_locations' );
	// 	$locations_primary_arr['menu-didattica'] = $menu->term_id;
	// 	set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
	// 	update_option( 'menu_check', true );
	// }

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$name = __( 'Notizie', 'design_laboratori_italia' );


	/**
		* Aggiungo i menu come widget: menu del footer (4 colonne) da rivedere??
		*/
	// $active_widgets                = get_option( 'sidebars_widgets' );
	// $active_widgets['footer-1'][0] = 'nav_menu-0';
	$nav_menu[0] = array(
		'title'             => 'Il Laboratorio',
		'nav_menu'          => $menu_link_esterno,
		'menu-item-classes' => 'footer-link',
	);
	// unset( $active_widgets['footer-1'][1] );
	// unset( $active_widgets['footer-1'][2] );

	// $active_widgets['footer-2'][0] = 'nav_menu-1';
	$nav_menu[1] = array(
		'title'             => 'Presentazione',
		'nav_menu'          => $menu_lab,
		'menu-item-classes' => 'footer-link',
	);

	// $active_widgets['footer-3'][0] = 'nav_menu-2';
	// $active_widgets['footer-3'][1] = 'nav_menu-5';

	$nav_menu[ 2 ] = array (
			'title'        => 'I Servizi',
			'nav_menu'     => $menu_servizi,
			'menu-item-classes' => 'footer-link',
	);
	// $nav_menu[ 5 ] = array (
	// 		'title'        => 'Didattica',
	// 		'nav_menu'     => $menu_presentazione,
	// 		'menu-item-classes' => 'footer-link',
	// );

	// $active_widgets["footer-4"][0] = 'nav_menu-4' ;
	// $active_widgets["footer-4"][1] = 'nav_menu-6' ;
	// $nav_menu[ 4 ] = array (
	// 		'title'        => 'Novità',
	// 		'nav_menu'     => $menu_notizie,
	// 		'menu-item-classes' => 'footer-link',
	// );
	// $nav_menu[ 6 ] = array (
	// 		'title'        => '',
	// 		'nav_menu'     => $menu_top,
	// );

	update_option( 'widget_nav_menu', $nav_menu );
	// update_option( 'sidebars_widgets', $active_widgets );

	global $wp_rewrite;
	$wp_rewrite->init(); //important...
	$wp_rewrite->set_tag_base( 'argomento' );
	$wp_rewrite->flush_rules();

	update_option( 'dli_has_installed', true );
}


function dsi_add_update_theme_page() {
		add_theme_page( 'Ricarica i dati', 'Ricarica i dati', 'edit_theme_options', 'reload-data-theme-options', 'dsi_reload_theme_option_page' );
}
add_action( 'admin_menu', 'dsi_add_update_theme_page' );

function dsi_reload_theme_option_page() {
	if(isset($_GET["action"]) && $_GET["action"] == "reload"){
			dli_create_pages_on_theme_activation();
	}

	echo "<div class='wrap'>";
	echo '<h1>Ricarica i dati di attivazione del tema</h1>';

	echo '<a href="themes.php?page=reload-data-theme-options&action=reload" class="button button-primary">Ricarica i dati di attivazione (menu, tipologie, etc)</a>';
	echo "</div>";
}
