<?php
/**
 * Creazione dei contenuti di default.
 *
 * @package Design_Laboratori_Italia
 */

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

	// Se non è installato Polylang non si può attivare il tema.
	if ( ! function_exists( 'pll_the_languages' ) ) {
		$msg = 'The plugin Polylang  is missing, please install and activate it: https://wordpress.org/plugins/polylang/';
		return false;
	}

	// Create permissions and roles.

	$auth_manager = new DLI_AuthorizationManager();
	$auth_manager->create_permissions_and_roles();

	// Crea le pagine di default se non esistono già.
	create_the_pages();

	// Create the tipologia-persona entities.
	create_the_tipologia_persona();

	// Crea le tassonomie di default.
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
	// Ora e un tipo di contenuto PEOPLE_TYPE_POST_TYPE e non più una tassonomia.
}


/**
 * Create the default taxonomies entries.
 *
 * @return void
 */
function create_the_taxonomies() {
	// Valori tassonomia tipologia-luogo.
	$taxonomy = PLACE_TYPE_TAXONOMY;
	$terms = array(
		array( 'it' => 'Aula', 'en' => 'Classroom' ),
		array( 'it' => 'Aula studio', 'en' => 'Study room' ),
		array( 'it' => 'Biblioteca', 'en' => 'Library' ),
		array( 'it' => 'Laboratorio', 'en' => 'Laboratory' ),
		array( 'it' => 'Parcheggio', 'en' => 'Parking' ),
		array( 'it' => 'Ufficio', 'en' => 'Office' ),
		array( 'it' => 'Sede', 'en' => 'Headquarter' ),
	);
	build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia struttura.
	$taxonomy = STRUCTURE_TAXONOMY;
	$terms = array(
		array( 'it' => 'Prima struttura', 'en' => 'First structure' ),
	);
	build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia tipo-pubblicazione.
	$taxonomy = PUBLICATION_TYPE_TAXONOMY;
	$terms = array(
		array( 'it' => 'Articolo in rivista', 'en' => 'Article in journal' ),
		array( 'it' => 'Monografia', 'en' => 'Monograph' ),
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
		dli_set_term_language( $term_it, 'it' );

		$termitem = get_term_by( 'slug', $term['en'], $taxonomy );
		if ( $termitem ) {
			$term_en = $termitem->term_id;
		} else {
			$termobject = wp_insert_term( $term['en'], $taxonomy );
			$term_en    = $termobject['term_id'];
		}
		dli_set_term_language( $term_en, 'en' );

		// Associate it and en translations.
		$related_taxonomies = array(
			'it' => $term_it,
			'en' => $term_en,
		);
		dli_save_term_translations( $related_taxonomies );
	}

}

/**
 * Create the site menus.
 *
 * @return void
 */
function create_the_menus() {
	// Creazione dei menu predefiniti.
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
	$menu = array(
		'name'     => 'Il laboratorio',
		'lang'     => 'it',
		'location' => 'menu-lab',
		'items' => array(
			array(
				'slug'         => SLUG_PERSONE_IT,
				'title'        => 'Persone',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PROGETTI_IT,
				'title'        => 'Progetti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_IT,
				'title'        => 'Attività di ricerca',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PUBBLICAZIONI_IT,
				'title'        => 'Pubblicazioni',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);

	build_the_menu( $menu );

	/**
	 * 2 - Creazione del menu PRESENTAZIONE.
	 */
	$menu = array(
		'name'     => 'Presentazione',
		'lang'     => 'it',
		'location' => 'menu-right',
		'items' => array(
			array(
				'slug'         => SLUG_PRESENTAZIONE_IT,
				'title'        => 'Presentazione',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$menu = array(
		'name'     => 'Novità',
		'lang'     => 'it',
		'location' => 'menu-header-right',
		'items' => array(
			array(
				'slug'         => SLUG_NOTIZIE_IT,
				'title'        => 'Notizie',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_EVENTI_IT,
				'title'        => 'Eventi',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_CONTATTI_IT,
				'title'        => 'Contatti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_LUOGHI_IT,
				'title'        => 'Dove siamo',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 4 - Creazione del menu Footer.
	 */
	$menu = array(
		'name'     => 'Footer it',
		'lang'     => 'it',
		'location' => 'menu-footer',
		'items' => array(
			array(
				'slug'         => SLUG_MEDIA_POLICY_IT,
				'title'        => 'Media policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PRIVACY_IT,
				'title'        => 'Privacy policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_NOTE_LEGALI_IT,
				'title'        => 'Note legali',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_MAPPA_SITO_IT,
				'title'        => 'Mappa del sito',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_SITO_IT,
				'title'        => 'Cerca',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BLOG_IT,
				'title'        => 'Blog',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Riuso',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => 'https://developers.italia.it/it/software/sns_pi-scuolanormalesuperiore-design-laboratori-wordpress-theme',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 5 - Creazione del menu Link utili.
	 */
	$menu = array(
		'name'     => 'Link utili',
		'lang'     => 'it',
		'location' => 'menu-links',
		'items' => array(
			array(
				'slug'         => SLUG_ACCESSIBILITA_IT,
				'title'        => 'Dichiarazione di accessibilità',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_ARCHIVIO_PROGETTI_IT,
				'title'        => 'Archivio progetti',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

}

/**
 * Create the site menus.
 *
 * @return void
 */
function build_the_menu( $custom_menu ) {
	$menu_name     = $custom_menu['name'];
	$menu_items    = $custom_menu['items'];
	$menu_location = $custom_menu['location'];
	$menu_lang     = $custom_menu['lang'];
	if ( 'it' !== $menu_lang ) {
		$menu_location = $menu_location . '___' . $menu_lang;
	}

	// wp_delete_nav_menu( $menu_name );

	$menu_object = wp_get_nav_menu_object( $menu_name );
	if ( $menu_object ) {
		$menu_id = $menu_object->term_id;
		$menu    = get_term_by( 'id', $menu_id, 'nav_menu' );
	} else {

		$menu_id  = wp_create_nav_menu( $menu_name );
		$menu     = get_term_by( 'id', $menu_id, 'nav_menu' );

		foreach ( $menu_items as $menu_item ) {
			$result = dli_get_content( $menu_item['slug'], $menu_item['content_type'] );
			if ( $result ) {
				$menu_item_id = $result->ID;
				if ( ( ! isset( $menu_item['link'] ) ) || ( '' === $menu_item['link'] ) ) {
					// Link a pagine o post.
					wp_update_nav_menu_item(
						$menu->term_id,
						0,
						array(
							'menu-item-title'     => $menu_item['title'],
							'menu-item-object-id' => $menu_item_id,
							'menu-item-object'    => $menu_item['content_type'],
							'menu-item-status'    => $menu_item['status'],
							'menu-item-type'      => $menu_item['post_type'],
							'menu-item-url'       => $menu_item['link'],
							// 'menu-item-classes'   => $menu_item['footer-link'],
						)
					);
				} else {
					// Link esterni.
					wp_update_nav_menu_item(
						$menu->term_id,
						0,
						array(
							'menu-item-title'     => $menu_item['title'],
							'menu-item-status'    => $menu_item['status'],
							'menu-item-url'       => $menu_item['link'],
							// 'menu-item-classes'   => $menu_item['footer-link'],
						)
					);
				}
			}
		}

		$locations_primary_arr                   = get_theme_mod( 'nav_menu_locations' );
		$locations_primary_arr[ $menu_location ] = $menu->term_id;
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
	/**
	 *  1 - Creazione del menu LABORATORIO-en.
	 */
	$menu = array(
		'name'     => 'The Lab',
		'lang'     => 'en',
		'location' => 'menu-lab',
		'items' => array(
			array(
				'slug'         => SLUG_PERSONE_EN,
				'title'        => 'People',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PROGETTI_EN,
				'title'        => 'Projects',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_EN,
				'title'        => 'Research activities',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PUBBLICAZIONI_EN,
				'title'        => 'Publications',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 2 - Creazione del menu PRESENTAZIONE-en.
	 */
	$menu = array(
		'name'     => 'Presentation',
		'lang'     => 'en',
		'location' => 'menu-right',
		'items' => array(
			array(
				'slug'         => SLUG_PRESENTAZIONE_EN,
				'title'        => 'Presentation',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$menu = array(
		'name'     => 'News',
		'lang'     => 'en',
		'location' => 'menu-header-right',
		'items' => array(
			array(
				'slug'         => SLUG_NOTIZIE_EN,
				'title'        => 'News',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_EVENTI_EN,
				'title'        => 'Events',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_CONTATTI_EN,
				'title'        => 'Contacts',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_LUOGHI_EN,
				'title'        => 'Where we are',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 4 - Creazione del menu Footer.
	 */
	$menu = array(
		'name'     => 'Footer en',
		'lang'     => 'en',
		'location' => 'menu-footer',
		'items' => array(
			array(
				'slug'         => SLUG_MEDIA_POLICY_EN,
				'title'        => 'Media policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_PRIVACY_EN,
				'title'        => 'Privacy policy',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_NOTE_LEGALI_EN,
				'title'        => 'Legal notes',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_MAPPA_SITO_EN,
				'title'        => 'Site map',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_RICERCA_SITO_EN,
				'title'        => 'Search',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_BLOG_EN,
				'title'        => 'Blog',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => '',
				'title'        => 'Reuse',
				'content_type' => '',
				'post_type'    => '',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => 'https://developers.italia.it/it/software/sns_pi-scuolanormalesuperiore-design-laboratori-wordpress-theme',
			),
		),
	);
	build_the_menu( $menu );

	/**
	 * 5 - Creazione del menu Link utili-en.
	 */
	$menu = array(
		'name'     => 'Useful link',
		'lang'     => 'en',
		'location' => 'menu-links',
		'items' => array(
			array(
				'slug'         => SLUG_ACCESSIBILITA_EN,
				'title'        => 'Accessibility declaration',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
			array(
				'slug'         => SLUG_ARCHIVIO_PROGETTI_EN,
				'title'        => 'Projects archive',
				'content_type' => 'page',
				'post_type'    => 'post_type',
				'status'       => 'publish',
				'classes'      => 'footer-link',
				'link'         => '',
			),
		),
	);
	build_the_menu( $menu );
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
			'content_slug_it'    => SLUG_PRESENTAZIONE_IT,
			'content_slug_en'    => SLUG_PRESENTAZIONE_EN,
			'content_title_it'   => 'Presentazione',
			'content_title_en'   => 'Presentation',
			'content_it'         => 'La nostra storia...',
			'content_en'         => 'Our history...',
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => '',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_PRIVACY_IT,
			'content_slug_en'    => SLUG_PRIVACY_EN,
			'content_title_it'   => 'Privacy policy',
			'content_title_en'   => 'Privacy policy',
			'content_it'         => 'La nostra Privacy Policy...',
			'content_en'         => 'Our Privacy Policy...',
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => '',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_CONTATTI_IT,
			'content_slug_en'    => SLUG_CONTATTI_EN,
			'content_title_it'   => 'Contatti',
			'content_title_en'   => 'Contacts',
			'content_it'         => 'I nostri contatti...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Our contacts...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/contatti.php',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_NEWSLETTER_IT,
			'content_slug_en'    => SLUG_NEWSLETTER_EN,
			'content_title_it'   => 'Newsletter',
			'content_title_en'   => 'Newsletter',
			'content_it'         => 'Registrati alla newsletter...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Register to the newsletter...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/newsletter.php',
			'content_type'       => 'page',
			'content_parent'     => null,
			'content_category'   => DLI_CUSTOM_PAGE_CAT,
		),
		array(
			'content_slug_it'    => SLUG_ACCESSIBILITA_IT,
			'content_slug_en'    => SLUG_ACCESSIBILITA_EN,
			'content_title_it'   => 'Dichiarazione',
			'content_title_en'   => 'Accessibility',
			'content_it'         => 'La di chiarazione di accessibilità...',
			'content_en'         => 'The accessibility declaration...',
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => '',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_MEDIA_POLICY_IT,
			'content_slug_en'    => SLUG_MEDIA_POLICY_EN,
			'content_title_it'   => 'Media policy',
			'content_title_en'   => 'Media policy',
			'content_it'         => 'La Media policy ( italiano )...',
			'content_en'         => 'The Media policy ( english )...',
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => '',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_NOTE_LEGALI_IT,
			'content_slug_en'    => SLUG_NOTE_LEGALI_EN,
			'content_title_it'   => 'Note legali',
			'content_title_en'   => 'Legal notes',
			'content_it'         => 'Le note legali...',
			'content_en'         => 'The legal notes...',
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => '',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_MAPPA_SITO_IT,
			'content_slug_en'    => SLUG_MAPPA_SITO_EN,
			'content_title_it'   => 'Mappa del sito',
			'content_title_en'   => 'Site map',
			'content_it'         => 'La mappa del sito...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'The map of the site...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/mappasito.php',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_LABORATORIO_IT,
			'content_slug_en'    => SLUG_LABORATORIO_EN,
			'content_title_it'   => 'Il Laboratorio',
			'content_title_en'   => 'The Lab',
			'content_it'         => 'Descrizione del laboratorio...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Lab description...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/il-laboratorio.php',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_PERSONE_IT,
			'content_slug_en'    => SLUG_PERSONE_EN,
			'content_title_it'   => 'Persone',
			'content_title_en'   => 'People',
			'content_it'         => 'Descrizione dello staff del laboratorio...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Description of the Lab staff...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/persone.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_PROGETTI_IT,
			'content_slug_en'    => SLUG_PROGETTI_EN,
			'content_title_it'   => 'Progetti',
			'content_title_en'   => 'Projects',
			'content_it'         => 'Descrizione dei progetti del laboratorio...',
			'content_en'         => 'Description of the Lab projects...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/progetti.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_ARCHIVIO_PROGETTI_IT,
			'content_slug_en'    => SLUG_ARCHIVIO_PROGETTI_EN,
			'content_title_it'   => 'Archivio progetti',
			'content_title_en'   => 'Projects archive',
			'content_it'         => 'Archivio dei progetti del laboratorio...',
			'content_en'         => 'Projects archive of the Lab projects...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/archive-progetti.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_RICERCA_IT,
			'content_slug_en'    => SLUG_RICERCA_EN,
			'content_title_it'   => 'Attività di ricerca',
			'content_title_en'   => 'Research activities',
			'content_it'         => 'Descrizione delle attività di ricerca...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Description of the research activities...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/ricerca.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_PUBBLICAZIONI_IT,
			'content_slug_en'    => SLUG_PUBBLICAZIONI_EN,
			'content_title_it'   => 'Pubblicazioni',
			'content_title_en'   => 'Publications',
			'content_it'         => 'Le nostre pubblicazioni ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Our publications...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/pubblicazioni.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_NOTIZIE_IT,
			'content_slug_en'    => SLUG_NOTIZIE_EN,
			'content_title_it'   => 'Le notizie',
			'content_title_en'   => 'News',
			'content_it'         => 'Le notizie del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Lab publications...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/notizie.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_EVENTI_IT,
			'content_slug_en'    => SLUG_EVENTI_EN,
			'content_title_it'   => 'Gli eventi',
			'content_title_en'   => 'Events',
			'content_it'         => 'Gli eventi del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'The events of the lab...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/eventi.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_LUOGHI_IT,
			'content_slug_en'    => SLUG_LUOGHI_EN,
			'content_title_it'   => 'I luoghi',
			'content_title_en'   => 'Places',
			'content_it'         => 'I luoghi del laboratorio ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'The places of the lab...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/luoghi.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
		array(
			'content_slug_it'    => SLUG_RICERCA_SITO_IT,
			'content_slug_en'    => SLUG_RICERCA_SITO_EN,
			'content_title_it'   => 'Ricerca',
			'content_title_en'   => 'Search',
			'content_it'         => 'Ricerca cose nel sito ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'Search things in the site...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/cerca.php',
			'content_type'       => 'page',
			'content_parent'     => null,
		),
		array(
			'content_slug_it'    => SLUG_BLOG_IT,
			'content_slug_en'    => SLUG_BLOG_EN,
			'content_title_it'   => 'Blog',
			'content_title_en'   => 'Blog',
			'content_it'         => 'Il blog ...' . DLI_TEXT_TEMPLATE_ACTIVE_IT,
			'content_en'         => 'The blog...' . DLI_TEXT_TEMPLATE_ACTIVE_EN,
			'content_status'     => 'publish',
			'content_author'     => 1,
			'content_template'   => 'page-templates/blog.php',
			'content_type'       => 'page',
			'content_parent'     => array( SLUG_LABORATORIO_IT, SLUG_LABORATORIO_EN ),
		),
	);

	foreach ( $static_pages as $page ) {
		$new_content_template   = $page['content_template'];

		// Create the IT page.
		// Store the above data in an array.
		$new_page = array(
			'post_type'    => $page['content_type'],
			'post_name'    => $page['content_slug_it'],
			'post_title'   => $page['content_title_it'],
			'post_content' => $page['content_it'],
			'post_status'  => $page['content_status'],
			'post_author'  => intval( $page['content_author'] ),
			'post_parent'  => 0,
		);
		$page_check     = dli_get_content( $page['content_slug_it'], $page['content_type'] );
		$new_page_it_id = $page_check ? $page_check->ID : 0;
		// If the IT page doesn't already exist, create it.
		if ( ! $new_page_it_id ) {
			if ( isset( $page['content_parent'] ) ) {
				$post_parent_id          = intval( get_page_by_path( $page['content_parent'][0] )->ID );
				$new_page['post_parent'] = $post_parent_id;
			}
			$new_page_it_id = wp_insert_post( $new_page );
			update_post_meta( $new_page_it_id, '_wp_page_template', $new_content_template );
		}
		// Assign the IT language to the page.
		dli_set_post_language( $new_page_it_id, 'it' );

		// Create the EN page.
		// Store the above data in an array.
		$new_page = array(
			'post_type'    => $page['content_type'],
			'post_name'    => $page['content_slug_en'],
			'post_title'   => $page['content_title_en'],
			'post_content' => $page['content_en'],
			'post_status'  => $page['content_status'],
			'post_author'  => intval( $page['content_author'] ),
			'post_parent'  => 0,
		);
		$page_check     = dli_get_content( $page['content_slug_en'], $page['content_type'] );
		$new_page_en_id = $page_check ? $page_check->ID : 0;
		// If the IT page doesn't already exist, create it.
		if ( ! $new_page_en_id ) {
			if ( isset( $page['content_parent'] ) ) {
				$post_parent_id          = intval( get_page_by_path( $page['content_parent'][1] )->ID );
				$new_page['post_parent'] = $post_parent_id;
			}
			$new_page_en_id = wp_insert_post( $new_page );
			update_post_meta( $new_page_en_id, '_wp_page_template', $new_content_template );
		}
		// Assign the EN language to the page.
		dli_set_post_language( $new_page_en_id, 'en' );

		// Associate it and en translations.
		$related_posts = array(
			'it' => $new_page_it_id,
			'en' => $new_page_en_id,
		);
		dli_save_post_translations( $related_posts );

	}
}

/**
 * Funzione per ricaricare i dati di default: pagine, post types, tassonomie, ecc.
 * WP->Aspetto->Ricarica dati.
 *
 * @return void
 */
function dli_add_update_theme_page() {
		add_theme_page( 'Ricarica i dati', 'Ricarica i dati', 'edit_theme_options', 'reload-data-theme-options', 'dli_reload_theme_option_page' );
}
add_action( 'admin_menu', 'dli_add_update_theme_page' );

/**
 * Pagina contenente il pulsante per ricaricare i dati.
 * WP->Aspetto->Ricarica dati.
 *
 * @return void
 */
function dli_reload_theme_option_page() {
	if( isset( $_GET["action"] ) && $_GET["action"] == "reload" ){
			dli_create_pages_on_theme_activation();
	}

	echo "<div class='wrap'>";
	echo '<h1>Ricarica i dati di attivazione del tema</h1>';

	echo '<a href="themes.php?page=reload-data-theme-options&action=reload" class="button button-primary">Ricarica i dati di attivazione (menu, pagine, tassonomie, etc)</a>';
	echo '</div>';
}
