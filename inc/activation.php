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
	$new_page_template = '';
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


	// ************ CREAZIONE VOCI DI DEFAULT DELLE TASSONOMIE ************
	wp_insert_term( 'Aula', 'tipologia-luogo' );
	wp_insert_term( 'Aula studio', 'tipologia-luogo' );
	wp_insert_term( 'Biblioteca', 'tipologia-luogo' );
	wp_insert_term( 'Laboratorio', 'tipologia-luogo' );
	wp_insert_term( 'Parcheggio', 'tipologia-luogo' );
	wp_insert_term( "Spazio all'aperto", 'tipologia-luogo' );
	wp_insert_term( 'Ufficio', 'tipologia-luogo' );

	wp_insert_term( 'Dipendenti', 'tipologia-servizio' );
	wp_insert_term( 'Professori e ricercatori', 'tipologia-servizio' );
	wp_insert_term( 'Studenti', 'tipologia-servizio' );
	wp_insert_term( 'Esterni', 'tipologia-servizio' );

	wp_insert_term( 'Notizie', 'tipologia-articolo' );
	wp_insert_term( 'Articoli', 'tipologia-articolo' );
	wp_insert_term( 'Rassegna Stampa', 'tipologia-articolo' );

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

	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {
		$menu_presentazione = $menu_object->term_id;
	} else {

		$menu_id            = wp_create_nav_menu( $name );
		$menu               = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_presentazione = $menu_id;

		$page             = get_page_by_path( 'presentazione' );
		$presentazione_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => 'Presentazione',
				'menu-item-object-id' => $presentazione_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$servizi_id = dsi_get_template_page_id( 'page-templates/servizi.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Servizi', 'design_laboratori_italia' ),
				'menu-item-object-id' => $servizi_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'   => __( 'Eventi', 'design_laboratori_italia' ),
				'menu-item-status'  => 'publish',
				'menu-item-object'  => 'evento',
				'menu-item-type'    => 'post_type_archive',
				'menu-item-classes' => 'footer-link',
			)
		);

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'   => __( 'I luoghi', 'design_laboratori_italia' ),
				'menu-item-status'  => 'publish',
				'menu-item-object'  => 'luogo',
				'menu-item-type'    => 'post_type_archive',
				'menu-item-classes' => 'footer-link',
			)
		);

		$term = get_term_by( 'name', 'Notizie', 'tipologia-articolo' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __('Le notizie', 'design_laboratori_italia' ),
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'taxonomy',
				'menu-item-object'    => 'tipologia-articolo',
				'menu-item-object-id' => $term->term_id,
				'menu-item-classes'    => 'footer-link',
			)
		);

		$locations_primary_arr               = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-right'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$name = __( 'Novità', 'design_laboratori_italia' );
	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {
		$menu_novita = $menu_object->term_id;
	} else {

		$menu_id     = wp_create_nav_menu( $name );
		$menu        = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_novita = $menu_id;

		$term = get_term_by( 'name', 'Notizie', 'tipologia-articolo' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __('Le notizie', 'design_laboratori_italia' ),
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'taxonomy',
				'menu-item-object'    => 'tipologia-articolo',
				'menu-item-object-id' => $term->term_id,
				'menu-item-classes'    => 'footer-link',
			)
		);

		$locations_primary_arr                      = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-header-right'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	/**
	 * 4 - Creazione del menu Footer.
	 */
	$name = __( 'Footer', 'design_laboratori_italia' );
	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {
		$menu_footer = $menu_object->term_id;
	} else {

		$menu_id     = wp_create_nav_menu( $name );
		$menu        = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_footer = $menu_id;

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'   => __( 'Privacy Policy', 'design_scuole_italia' ),
				'menu-item-url'     => get_privacy_policy_url(),
				'menu-item-status'  => 'publish',
				'menu-item-type'    => 'custom',
				'menu-item-classes' => 'footer-link',
			)
		);

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'   => __( 'Dichiarazione di accessibilità', 'design_laboratori_italia' ),
				'menu-item-url'     => "#",
				'menu-item-status'  => 'publish',
				'menu-item-type'    => 'custom',
				'menu-item-classes' => 'footer-link',
			)
		);

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'   => __( 'Contatti', 'design_laboratori_italia' ),
				'menu-item-url'     => "#",
				'menu-item-status'  => 'publish',
				'menu-item-type'    => 'custom',
				'menu-item-classes' => 'footer-link',
			)
		);

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title' => __( 'Dove siamo', 'design_laboratori_italia' ),
				'menu-item-url' => "#",
				'menu-item-status' => 'publish',
				'menu-item-type' => 'custom',
				'menu-item-classes' => 'footer-link',
			)
		);

		$locations_primary_arr                = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-footer'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	// Associazione dei menu ai widget del footer e ai menù di navigazione.

	/**
	* MENU DI NAVIGAZIONE
	*/
	// $nav_menu[0] = array(
	// 	'title'             => 'Il Laboratorio',
	// 	'nav_menu'          => $menu_lab,
	// 	'menu-item-classes' => 'footer-link',
	// );
	// $nav_menu[1] = array(
	// 	'title'             => 'Presentazione',
	// 	'nav_menu'          => $menu_presentazione,
	// 	'menu-item-classes' => 'footer-link',
	// );
	// $nav_menu[2] = array(
	// 	'title'             => 'Novità',
	// 	'nav_menu'          => $menu_novita
	// 	'menu-item-classes' => 'footer-link',
	// );
	// update_option( 'widget_nav_menu', $nav_menu );

	/**
		* Aggiungo i menu come widget: menu del footer (4 colonne) da rivedere??
		*/
	// $active_widgets                = get_option( 'sidebars_widgets' );
	// $active_widgets['footer-1'][0] = 'nav_menu-0';

	// unset( $active_widgets['footer-1'][1] );
	// unset( $active_widgets['footer-1'][2] );

	// $active_widgets['footer-2'][0] = 'nav_menu-1';


	// $active_widgets['footer-3'][0] = 'nav_menu-2';
	// $active_widgets['footer-3'][1] = 'nav_menu-5';


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
