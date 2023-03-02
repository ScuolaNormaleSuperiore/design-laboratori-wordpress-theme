<?php
/**
 * Creazione dei contenuti di default.
 *
 * @package Design_Laboratori_Italia
 */

// The slug is the name of the post, that is the name that appears in the url.
define( 'SLUG_SERVIZI_IT', 'servizi' );
define( 'SLUG_SERVIZI_EN', 'services' );
define( 'SLUG_LUOGHI_IT', 'luoghi' );
define( 'SLUG_LUOGHI_EN', 'places' );
define( 'SLUG_EVENTI_IT', 'eventi' );
define( 'SLUG_EVENTI_EN', 'events' );
define( 'SLUG_NOTIZIE_IT', 'notizie' );
define( 'SLUG_NOTIZIE_EN', 'news' );
define( 'SLUG_PUBBLICAZIONI_IT', 'pubblicazioni' );
define( 'SLUG_PUBBLICAZIONI_EN', 'publications' );
define( 'SLUG_RICERCA_IT', 'ricerca' );
define( 'SLUG_RICERCA_EN', 'research' );
define( 'SLUG_PROGETTI_IT', 'progetti' );
define( 'SLUG_PROGETTI_EN', 'projects' );
define( 'SLUG_PERSONE_IT', 'persone' );
define( 'SLUG_PERSONE_EN', 'people' );
define( 'SLUG_LABORATORIO_IT', 'il-laboratorio' );
define( 'SLUG_LABORATORIO_EN', 'the-lab' );
define( 'SLUG_ACCESSIBILITA_IT', 'accessibilita' );
define( 'SLUG_ACCESSIBILITA_EN', 'accessibility' );
define( 'SLUG_DOVESIAMO_IT', 'dove-siamo' );
define( 'SLUG_DOVESIAMO_EN', 'where-we-are' );
define( 'SLUG_PRESENTAZIONE_IT', 'presentazione' );
define( 'SLUG_PRESENTAZIONE_EN', 'presentation' );
define( 'SLUG_CONTATTI_IT', 'contatti' );
define( 'SLUG_CONTATTI_EN', 'contacts' );
define( 'SLUG_PRIVACY_IT', 'privacy-policy' );
define( 'SLUG_PRIVACY_EN', 'privacy-policy-en' );

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

	// Create the default pages.
	create_the_pages();

	// Add some term to taxonomies.
	create_the_taxonomies();

	// Create all the menus of the site.
	//create_the_menus();

	global $wp_rewrite;
	$wp_rewrite->init(); // important...
	$wp_rewrite->set_tag_base( 'argomento' );
	$wp_rewrite->flush_rules();

	update_option( 'dli_has_installed', true );
}

/**
 * Create the default taxonomies entries.
 *
 * @return void
 */
function create_the_taxonomies() {
	// // Valori tassonomia tipologia-luogo.
	// wp_insert_term( 'Aula', 'tipologia-luogo' );
	// wp_insert_term( 'Aula studio', 'tipologia-luogo' );
	// wp_insert_term( 'Biblioteca', 'tipologia-luogo' );
	// wp_insert_term( 'Laboratorio', 'tipologia-luogo' );
	// wp_insert_term( 'Parcheggio', 'tipologia-luogo' );
	// wp_insert_term( "Spazio all'aperto", 'tipologia-luogo' );
	// wp_insert_term( 'Ufficio', 'tipologia-luogo' );

	// // Valori tassonomia tipologia-servizio.
	// wp_insert_term( 'Dipendenti', 'tipologia-servizio' );
	// wp_insert_term( 'Professori e ricercatori', 'tipologia-servizio' );
	// wp_insert_term( 'Studenti', 'tipologia-servizio' );
	// wp_insert_term( 'Esterni', 'tipologia-servizio' );

	// // Valori tassonomia struttura.
	// wp_insert_term( 'Prima struttura', 'struttura' );
}

/**
 * Create the site menus.
 *
 * @return void
 */
function create_the_menus() {
	/**
	 *  1 - Creazione del menu LABORATORIO.
	 */
	$name = __( 'Il Laboratorio', 'design_laboratori_italia' );

	wp_delete_nav_menu( $name );

	$menu_object = wp_get_nav_menu_object( $name );
	if ( $menu_object ) {
			$menu_id = $menu_object->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
		$menu    = get_term_by( 'id', $menu_id, 'nav_menu' );

		$page    = get_page_by_path( SLUG_PERSONE_IT );
		$page_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => 'Persone',
				'menu-item-object-id' => $page_id,
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
}


/**
 * Create the default pages in italian and english.
 *
 * @return void
 */
function create_the_pages() {
		// Creazione delle pagine statiche.
		$static_pages = array(
			array(
				'page_slug_it'    => SLUG_PRESENTAZIONE_IT,
				'page_slug_en'    => SLUG_PRESENTAZIONE_EN,
				'page_title_it'   => 'Presentazione',
				'page_title_en'   => 'Presentation',
				'page_content_it' => 'La nostra storia...',
				'page_content_en' => 'Our history...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => '',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_PRIVACY_IT,
				'page_slug_en'    => SLUG_PRIVACY_EN,
				'page_title_it'   => 'Privacy Policy',
				'page_title_en'   => 'Privacy Policy',
				'page_content_it' => 'La nostra Privacy Policy...',
				'page_content_en' => 'Our Privacy Policy...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => '',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_CONTATTI_IT,
				'page_slug_en'    => SLUG_CONTATTI_EN,
				'page_title_it'   => 'Contatti',
				'page_title_en'   => 'Contacts',
				'page_content_it' => 'I nostri contatti...',
				'page_content_en' => 'Our contacts...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => '',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_DOVESIAMO_IT,
				'page_slug_en'    => SLUG_DOVESIAMO_EN,
				'page_title_it'   => 'Dove siamo',
				'page_title_en'   => 'Where we are',
				'page_content_it' => 'Noi siamo qui...',
				'page_content_en' => 'We are here...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => '',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_ACCESSIBILITA_IT,
				'page_slug_en'    => SLUG_ACCESSIBILITA_EN,
				'page_title_it'   => 'Accessibilità',
				'page_title_en'   => 'Accessibility',
				'page_content_it' => 'La di chiarazione di accessibilità...',
				'page_content_en' => 'The accessibility declaration...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => '',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_LABORATORIO_IT,
				'page_slug_en'    => SLUG_LABORATORIO_EN,
				'page_title_it'   => 'Il Laboratorio',
				'page_title_en'   => 'The Lab',
				'page_content_it' => 'Descrizione del laboratorio...',
				'page_content_en' => 'Lab description...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/il-laboratorio.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_PERSONE_IT,
				'page_slug_en'    => SLUG_PERSONE_EN,
				'page_title_it'   => 'Le Persone',
				'page_title_en'   => 'People',
				'page_content_it' => 'Descrizione dello staff del laboratorio...',
				'page_content_en' => 'Description of the Lab staff...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/persone.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_PROGETTI_IT,
				'page_slug_en'    => SLUG_PROGETTI_EN,
				'page_title_it'   => 'I progetti',
				'page_title_en'   => 'The projects',
				'page_content_it' => 'Descrizione dei progetti del laboratorio...',
				'page_content_en' => 'Description of the Lab projects...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/progetti.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_RICERCA_IT,
				'page_slug_en'    => SLUG_RICERCA_EN,
				'page_title_it'   => 'Attività di ricerca',
				'page_title_en'   => 'Research activities',
				'page_content_it' => 'Descrizione delle attività di ricerca...',
				'page_content_en' => 'Description of the research activities...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/progetti.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_PUBBLICAZIONI_IT,
				'page_slug_en'    => SLUG_PUBBLICAZIONI_EN,
				'page_title_it'   => 'Le pubblicazioni',
				'page_title_en'   => 'Publications',
				'page_content_it' => 'Le nostre pubblicazioni ...',
				'page_content_en' => 'Our publications...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/pubblicazioni.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_NOTIZIE_IT,
				'page_slug_en'    => SLUG_NOTIZIE_IT,
				'page_title_it'   => 'Le notizie',
				'page_title_en'   => 'News',
				'page_content_it' => 'Le notizie del laboratorio ...',
				'page_content_en' => 'Lab publications...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/notizie.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_EVENTI_IT,
				'page_slug_en'    => SLUG_EVENTI_EN,
				'page_title_it'   => 'Gli eventi',
				'page_title_en'   => 'Events',
				'page_content_it' => 'Gli eventi del laboratorio ...',
				'page_content_en' => 'The events of the lab...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/eventi.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),
			array(
				'page_slug_it'    => SLUG_SERVIZI_IT,
				'page_slug_en'    => SLUG_SERVIZI_EN,
				'page_title_it'   => 'I servizi',
				'page_title_en'   => 'Services',
				'page_content_it' => 'I servizi del laboratorio ...',
				'page_content_en' => 'The services of the lab...',
				'page_status'     => 'publish',
				'page_author'     => 1,
				'page_template'   => 'page-templates/servizi.php',
				'page_type'       => 'page',
				'page_parent'     => null,
			),

		);

		foreach ( $static_pages as $page ) {
			$new_page_template   = $page['page_template'];

			// Create the IT page.
			// Store the above data in an array.
			$new_page = array(
				'post_type'    => $page['page_type'],
				'post_name'    => $page['page_slug_it'],
				'post_title'   => $page['page_title_it'],
				'post_content' => $page['page_content_it'],
				'post_status'  => $page['page_status'],
				'post_author'  => intval( $page['page_author'] ),
			);
			$page_check     = get_page_by_path( $page['page_slug_it'] );
			$new_page_it_id = $page_check ? $page_check->ID : 0;
			// If the IT page doesn't already exist, create it.
			if ( ! $new_page_it_id ) {
				$new_page_it_id = wp_insert_post( $new_page );
				update_post_meta( $new_page_it_id, '_wp_page_template', $new_page_template );
			}
			// Assign the IT language to the page.
			pll_set_post_language( $new_page_it_id, 'it' );

			// Create the EN page.
			// Store the above data in an array.
			$new_page = array(
				'post_type'    => $page['page_type'],
				'post_slug'    => $page['page_slug_en'],
				'post_title'   => $page['page_title_en'],
				'post_content' => $page['page_content_en'],
				'post_status'  => $page['page_status'],
				'post_author'  => intval( $page['page_author'] ),
			);
			$page_check     = get_page_by_path( $page['page_slug_en'] );
			$new_page_en_id = $page_check ? $page_check->ID : 0;
			// If the IT page doesn't already exist, create it.
			if ( ! $new_page_en_id ) {
				$new_page_en_id = wp_insert_post( $new_page );
				update_post_meta( $new_page_en_id, '_wp_page_template', $new_page_template );
			}
			// Assign the EN language to the page.
			pll_set_post_language( $new_page_en_id, 'en' );

			// Associate it and en translations.
			$related_posts = array(
				'it' => $new_page_it_id,
				'en' => $new_page_en_id,
			);
			pll_save_post_translations( $related_posts );

		}
}

/**
 * Funzione per ricaricare i dati di default: pagine, post types, tassonomie, ecc.
 * WP->Aspetto->Ricarica dati.
 *
 * @return void
 */
function dsi_add_update_theme_page() {
		add_theme_page( 'Ricarica i dati', 'Ricarica i dati', 'edit_theme_options', 'reload-data-theme-options', 'dsi_reload_theme_option_page' );
}
add_action( 'admin_menu', 'dsi_add_update_theme_page' );

/**
 * Pagina contenente il pulsante per ricaricare i dati.
 * WP->Aspetto->Ricarica dati.
 *
 * @return void
 */
function dsi_reload_theme_option_page() {
	if( isset( $_GET["action"] ) && $_GET["action"] == "reload" ){
			dli_create_pages_on_theme_activation();
	}

	echo "<div class='wrap'>";
	echo '<h1>Ricarica i dati di attivazione del tema</h1>';

	echo '<a href="themes.php?page=reload-data-theme-options&action=reload" class="button button-primary">Ricarica i dati di attivazione (menu, tipologie, etc)</a>';
	echo '</div>';
}
