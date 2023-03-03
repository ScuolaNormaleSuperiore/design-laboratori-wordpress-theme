<?php
/**
 * Creazione dei contenuti di default.
 *
 * @package Design_Laboratori_Italia
 */

// The slug is the name of the post, that is the name that appears in the url.
// Default pages of the lab.
define( 'SLUG_LABORATORIO_IT', 'il-laboratorio' );
define( 'SLUG_LABORATORIO_EN', 'the-lab' );
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
// Default pages of the site.
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

	// Create the tipologia-persona entities.
	create_the_tipologia_persona();

	// Add some term to taxonomies.
	create_the_taxonomies();

	// Create all the menus of the site.
	create_the_menus();

	global $wp_rewrite;
	$wp_rewrite->init(); // important...
	$wp_rewrite->set_tag_base( 'argomento' );
	$wp_rewrite->flush_rules();

	update_option( 'dli_has_installed', true );
}


/**
 * Create the tipologia persona.
 *
 * @return void
 */
function create_the_tipologia_persona() {
	// Qui crea la tipologia direttore in italiano e in inglese.
	// 'tipologia-persona'
}


/**
 * Create the default taxonomies entries.
 *
 * @return void
 */
function create_the_taxonomies() {
	// Valori tassonomia tipologia-luogo.
	$taxonomy = 'tipologia-luogo';
	$terms = array(
		array( 'it' => 'Aula', 'en' => 'Classroom' ),
		array( 'it' => 'Aula studio', 'en' => 'Study room' ),
		array( 'it' => 'Biblioteca', 'en' => 'Library' ),
		array( 'it' => 'Laboratorio', 'en' => 'Laboratory' ),
		array( 'it' => 'Parcheggio', 'en' => 'Parking' ),
		array( 'it' => 'Ufficio', 'en' => 'Office' ),
	);
	build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia tipologia-servizio.
	$taxonomy = 'tipologia-servizio';
	$terms = array(
		array( 'it' => 'Dipendenti', 'en' => 'Employees' ),
		array( 'it' => 'Professori', 'en' => 'Professors' ),
		array( 'it' => 'Studenti', 'en' => 'Students' ),
		array( 'it' => 'Esterni', 'en' => 'External' ),
		array( 'it' => 'Ricercatori', 'en' => 'Researchers' ),
	);
	build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia struttura.
	$taxonomy = 'struttura';
	$terms = array(
		array( 'it' => 'Prima struttura', 'en' => 'First structure' ),
	);
	build_taxonomies( $taxonomy, $terms );

}

/**
 * Build the taxonomies.
 *
 * @return void
 */
function build_taxonomies( $taxonomy, $terms ) {

	foreach ( $terms as $term ) {

		$termitem = get_term_by( 'slug', $term['it'], $taxonomy );
		if ( $termitem ) {
			$term_it = $termitem->term_id;
		} else {
			$termobject = wp_insert_term( $term['it'], $taxonomy );
			$term_it    = $termobject['term_id'];
		}
		pll_set_term_language( $term_it, 'it' );

		$termitem = get_term_by( 'slug', $term['en'], $taxonomy );
		if ( $termitem ) {
			$term_en = $termitem->term_id;
		} else {
			$termobject = wp_insert_term( $term['en'], $taxonomy );
			$term_en    = $termobject['term_id'];
		}
		pll_set_term_language( $term_en, 'en' );

		// Associate it and en translations.
		$related_taxonomies = array(
			'it' => $term_it,
			'en' => $term_en,
		);
		pll_save_term_translations( $related_taxonomies );
	}

}

/**
 * Create the site menus.
 *
 * @return void
 */
function create_the_menus() {
	error_log( '@@@ HERE WE CREATE THE MENUS @@@' );
	create_the_it_menus();
	create_the_en_menus();
}


/**
 * Create the site menus.
 *
 * @return void
 */
function create_the_it_menus() {
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
				'menu-item-title'   => __( 'I luoghi', 'design_laboratori_italia' ),
				'menu-item-status'  => 'publish',
				'menu-item-object'  => 'luogo',
				'menu-item-type'    => 'post_type_archive',
				'menu-item-classes' => 'footer-link',
			)
		);

		$notizie_id = dsi_get_template_page_id( 'page-templates/notizie.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Notizie', 'design_laboratori_italia' ),
				'menu-item-object-id' => $notizie_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
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

		$notizie_id = dsi_get_template_page_id( 'page-templates/notizie.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Notizie', 'design_laboratori_italia' ),
				'menu-item-object-id' => $notizie_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$eventi_id = dsi_get_template_page_id( 'page-templates/eventi.php' );
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Eventi', 'design_laboratori_italia' ),
				'menu-item-object-id' => $eventi_id,
				'menu-item-object'    => 'page',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$page        = get_page_by_path( SLUG_CONTATTI_IT );
		$contatti_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Contatti', 'design_laboratori_italia' ),
				'menu-item-object-id' => $contatti_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$page        = get_page_by_path( 'dove-siamo' );
		$dove_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Dove siamo', 'design_laboratori_italia' ),
				'menu-item-object-id' => $dove_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
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

		// Di solito la Pagina Policy è già presente in una installazione di WP (con id=3).
		$policy_id = get_option( 'wp_page_for_privacy_policy' );
		if ( ( ! $policy_id ) || ( get_post( (int) $policy_id ) == null ) ) {
			$page             = get_page_by_path( SLUG_PRIVACY_IT );
			$policy_id = $page->ID;
		} else {
			$policy_id = (int) $policy_id;
		}
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Privacy Policy', 'design_laboratori_italia' ),
				'menu-item-object-id' => $policy_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$page             = get_page_by_path( 'accessibilita' );
		$accessibilita_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Dichiarazione di accessibilità', 'design_laboratori_italia' ),
				'menu-item-object-id' => $accessibilita_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$page        = get_page_by_path( SLUG_CONTATTI_IT );
		$contatti_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Contatti', 'design_laboratori_italia' ),
				'menu-item-object-id' => $contatti_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$page        = get_page_by_path( 'dove-siamo' );
		$dove_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Dove siamo', 'design_laboratori_italia' ),
				'menu-item-object-id' => $dove_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$locations_primary_arr                = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-footer'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	/**
	 * 5 - Creazione del menu Link utili.
	 */
	$name = __( 'Link utili', 'design_laboratori_italia' );
	wp_delete_nav_menu( $name );
	$menu_object = wp_get_nav_menu_object( $name );
	if( $menu_object ) {
		$menu_link = $menu_object->term_id;
	} else {

		$menu_id     = wp_create_nav_menu( $name );
		$menu        = get_term_by( 'id', $menu_id, 'nav_menu' );
		$menu_links  = $menu_id;

		$page             = get_page_by_path( 'accessibilita' );
		$accessibilita_id = $page->ID;
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => __( 'Dichiarazione di accessibilità', 'design_laboratori_italia' ),
				'menu-item-object-id' => $accessibilita_id,
				'menu-item-object'    => 'post',
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-classes'   => 'footer-link',
			)
		);

		$locations_primary_arr                = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr['menu-links'] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

}


/**
 * Create the site menus.
 *
 * @return void
 */
function create_the_en_menus() {

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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_template'   => 'page-templates/ricerca.php',
				'page_type'       => 'page',
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'page_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
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
				'post_parent'  => 0,
			);
			$page_check     = get_page_by_path( $page['page_slug_it'] );
			$new_page_it_id = $page_check ? $page_check->ID : 0;
			// If the IT page doesn't already exist, create it.
			if ( ! $new_page_it_id ) {
				if ( isset( $page['page_parent'] ) ) {
					$post_parent_id          = intval( get_page_by_path( $page['page_parent'][0] )->ID );
					$new_page['post_parent'] = $post_parent_id;
				}
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
				'post_parent'  => 0,
			);
			$page_check     = get_page_by_path( $page['page_slug_en'] );
			$new_page_en_id = $page_check ? $page_check->ID : 0;
			// If the IT page doesn't already exist, create it.
			if ( ! $new_page_en_id ) {
				if ( isset( $page['page_parent'] ) ) {
					$post_parent_id          = intval( get_page_by_path( $page['page_parent'][1] )->ID );
					$new_page['post_parent'] = $post_parent_id;
				}
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
