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
 *
 * @return void
 */
function dli_create_pages_on_theme_activation() {
	// Se non è installato Polylang non si può attivare il tema.
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	// Crea le pagine di default se non esistono già.
	dli_create_the_pages();

	// Create the tipologia-persona entities.
	dli_create_the_tipologia_persona();

	// Crea le tassonomie di default.
	dli_create_the_taxonomies();

	// Create all the menus of the site.
	dli_create_the_menus();

	// Create the custom tables.
	dli_create_the_tables();

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
function dli_create_the_tipologia_persona() {
	// Qui crea la tipologia direttore in italiano e in inglese.
	// Ora e un tipo di contenuto PEOPLE_TYPE_POST_TYPE e non più una tassonomia.
}


/**
 * Create the default taxonomies entries.
 *
 * @return void
 */
function dli_create_the_taxonomies() {
	// Valori tassonomia tipologia-luogo.
	$taxonomy = PLACE_TYPE_TAXONOMY;
	$terms    = array(
		array(
			'it' => 'Aula',
			'en' => 'Classroom',
		),
		array(
			'it' => 'Aula studio',
			'en' => 'Study room',
		),
		array(
			'it' => 'Biblioteca',
			'en' => 'Library',
		),
		array(
			'it' => 'Laboratorio',
			'en' => 'Laboratory',
		),
		array(
			'it' => 'Parcheggio',
			'en' => 'Parking',
		),
		array(
			'it' => 'Ufficio',
			'en' => 'Office',
		),
		array(
			'it' => 'Sede',
			'en' => 'Headquarter',
		),
	);
	dli_build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia struttura.
	$taxonomy = STRUCTURE_TAXONOMY;
	$terms    = array(
		array(
			'it' => 'Prima struttura',
			'en' => 'First structure',
		),
	);
	dli_build_taxonomies( $taxonomy, $terms );

	// Valori tassonomia tipo-pubblicazione.
	$taxonomy = PUBLICATION_TYPE_TAXONOMY;
	$terms    = array(
		array(
			'it' => 'Articolo in rivista',
			'en' => 'Article in journal',
		),
		array(
			'it' => 'Monografia',
			'en' => 'Monograph',
		),
	);
	dli_build_taxonomies( $taxonomy, $terms );
}

/**
 * Build the taxonomies.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param array  $terms    List of term label pairs by language.
 *
 * @return void
 */
function dli_build_taxonomies( $taxonomy, $terms ) {

	foreach ( $terms as $term ) {

		// Use a stable slug to avoid missing existing terms.
		$term_slug_it = sanitize_title( $term['it'] );
		$termitem     = get_term_by( 'slug', $term_slug_it, $taxonomy );
		if ( $termitem ) {
			$term_it = $termitem->term_id;
		} else {
			$termobject = wp_insert_term(
				$term['it'],
				$taxonomy,
				array(
					'slug' => $term_slug_it,
				)
			);
			// If the term already exists, wp_insert_term returns WP_Error with term_exists.
			if ( is_wp_error( $termobject ) ) {
				$term_it = isset( $termobject->error_data['term_exists'] ) ? intval( $termobject->error_data['term_exists'] ) : 0;
			} else {
				$term_it = $termobject['term_id'];
			}
		}
		// Set language only when a valid term ID is available.
		if ( $term_it ) {
			dli_set_term_language( $term_it, 'it' );
		}

		// Use a stable slug to avoid missing existing terms.
		$term_slug_en = sanitize_title( $term['en'] );
		$termitem     = get_term_by( 'slug', $term_slug_en, $taxonomy );
		if ( $termitem ) {
			$term_en = $termitem->term_id;
		} else {
			$termobject = wp_insert_term(
				$term['en'],
				$taxonomy,
				array(
					'slug' => $term_slug_en,
				)
			);
			// If the term already exists, wp_insert_term returns WP_Error with term_exists.
			if ( is_wp_error( $termobject ) ) {
				$term_en = isset( $termobject->error_data['term_exists'] ) ? intval( $termobject->error_data['term_exists'] ) : 0;
			} else {
				$term_en = $termobject['term_id'];
			}
		}
		// Set language only when a valid term ID is available.
		if ( $term_en ) {
			dli_set_term_language( $term_en, 'en' );
		}

		// Associate it and en translations.
		if ( $term_it && $term_en ) {
			$related_taxonomies = array(
				'it' => $term_it,
				'en' => $term_en,
			);
			dli_save_term_translations( $related_taxonomies );
		}
	}
}

/**
 * Create the site menus.
 *
 * @return void
 */
function dli_create_the_menus() {
	// Creazione dei menu predefiniti.
	dli_create_the_it_menus();
	dli_create_the_en_menus();
}


/**
 * Create the site menus.
 *
 * @return void
 */
function dli_create_the_it_menus() {

	/**
	 *  1 - Creazione del menu LABORATORIO.
	 */
	$menu = DLI_LAB_MENU_IT;
	dli_build_the_menu( $menu );

	/**
	 * 2 - Creazione del menu PRESENTAZIONE.
	 */
	$menu = DLI_PRESENTATION_MENU_IT;
	dli_build_the_menu( $menu );

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$menu = DLI_NEWS_MENU_IT;
	dli_build_the_menu( $menu );

	/**
	 * 4 - Creazione del menu Footer.
	 */
	$menu = DLI_FOOTER_MENU_IT;
	dli_build_the_menu( $menu );

	/**
	 * 5 - Creazione del menu Link utili.
	 */
	$menu = DLI_USEFUL_LINKS_MENU_IT;
	dli_build_the_menu( $menu );
}

/**
 * Create the site menus.
 *
 * @param array $custom_menu Menu configuration.
 *
 * @return void
 */
function dli_build_the_menu( $custom_menu ) {
	$menu_name     = $custom_menu['name'];
	$menu_items    = $custom_menu['items'];
	$menu_location = $custom_menu['location'];
	$menu_lang     = $custom_menu['lang'];
	if ( 'it' !== $menu_lang ) {
		$menu_location = $menu_location . '___' . $menu_lang;
	}

	$menu_object = wp_get_nav_menu_object( $menu_name );
	if ( $menu_object ) {
		$menu_id = $menu_object->term_id;
		$menu    = get_term_by( 'id', $menu_id, 'nav_menu' );
	} else {
		$menu_id = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) ) {
			return;
		}
		$menu = get_term_by( 'id', $menu_id, 'nav_menu' );

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
						)
					);
				} else {
					// Link esterni.
					wp_update_nav_menu_item(
						$menu->term_id,
						0,
						array(
							'menu-item-title'  => $menu_item['title'],
							'menu-item-status' => $menu_item['status'],
							'menu-item-url'    => $menu_item['link'],
						)
					);
				}
			}
		}

		$locations_primary_arr = get_theme_mod( 'nav_menu_locations' );
		if ( ! is_array( $locations_primary_arr ) ) {
			$locations_primary_arr = array();
		}
		$locations_primary_arr[ $menu_location ] = $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations_primary_arr );
		update_option( 'menu_check', true );
	}

	if ( ! $menu || ! isset( $menu->term_id ) ) {
		return;
	}
}

/**
 * Create the site menus.
 *
 * @return void
 */
function dli_create_the_en_menus() {
	/**
	 *  1 - Creazione del menu LABORATORIO-en.
	 */
	$menu = DLI_LAB_MENU_EN;
	dli_build_the_menu( $menu );

	/**
	 * 2 - Creazione del menu PRESENTAZIONE-en.
	 */
	$menu = DLI_PRESENTATION_MENU_EN;
	dli_build_the_menu( $menu );

	/**
	 * 3 - Creazione del menu NOTIZIE.
	 */
	$menu = DLI_NEWS_MENU_EN;
	dli_build_the_menu( $menu );

	/**
	 * 4 - Creazione del menu Footer.
	 */
	$menu = DLI_FOOTER_MENU_EN;
	dli_build_the_menu( $menu );

	/**
	 * 5 - Creazione del menu Link utili-en.
	 */
	$menu = DLI_USEFUL_LINKS_MENU_EN;
	dli_build_the_menu( $menu );
}


/**
 * Create the default pages in italian and english.
 *
 * @return void
 */
function dli_create_the_pages() {
	// Creazione delle pagine statiche.
	foreach ( DLI_STATIC_PAGE_CATS as $page ) {
		$new_content_template = $page['content_template'];

		// Create the IT page.
		// Store the above data in an array.
		$content_it = ( '' !== $page['content_file_it'] ) ? file_get_contents( DLI_THEMA_PATH . $page['content_file_it'] ) : $page['content_it'];
		if ( false === $content_it ) {
			$content_it = '';
		}
		$new_page       = array(
			'post_type'    => $page['content_type'],
			'post_name'    => $page['content_slug_it'],
			'post_title'   => $page['content_title_it'],
			'post_content' => $content_it,
			'post_status'  => $page['content_status'],
			'post_author'  => intval( $page['content_author'] ),
			'post_parent'  => 0,
		);
		$page_check     = dli_get_content( $page['content_slug_it'], $page['content_type'] );
		$new_page_it_id = $page_check ? $page_check->ID : 0;
		// If the IT page doesn't already exist, create it.
		if ( ! $new_page_it_id ) {
			if ( isset( $page['content_parent'] ) ) {
				$parent_page = get_page_by_path( $page['content_parent'][0] );
				if ( $parent_page ) {
					$post_parent_id          = intval( $parent_page->ID );
					$new_page['post_parent'] = $post_parent_id;
				}
			}
			$new_page_it_id = wp_insert_post( $new_page, true );
			if ( ! is_wp_error( $new_page_it_id ) && $new_page_it_id > 0 ) {
				update_post_meta( $new_page_it_id, '_wp_page_template', $new_content_template );
			} else {
				$new_page_it_id = 0;
			}
		}
		// Assign the IT language to the page.
		if ( $new_page_it_id > 0 ) {
			dli_set_post_language( $new_page_it_id, 'it' );
		}

		// Create the EN page.
		// Store the above data in an array.
		$content_en = ( '' !== $page['content_file_en'] ) ? file_get_contents( DLI_THEMA_PATH . $page['content_file_en'] ) : $page['content_en'];
		if ( false === $content_en ) {
			$content_en = '';
		}
		$new_page       = array(
			'post_type'    => $page['content_type'],
			'post_name'    => $page['content_slug_en'],
			'post_title'   => $page['content_title_en'],
			'post_content' => $content_en,
			'post_status'  => $page['content_status'],
			'post_author'  => intval( $page['content_author'] ),
			'post_parent'  => 0,
		);
		$page_check     = dli_get_content( $page['content_slug_en'], $page['content_type'] );
		$new_page_en_id = $page_check ? $page_check->ID : 0;
		// If the IT page doesn't already exist, create it.
		if ( ! $new_page_en_id ) {
			if ( isset( $page['content_parent'] ) ) {
				$parent_page = get_page_by_path( $page['content_parent'][1] );
				if ( $parent_page ) {
					$post_parent_id          = intval( $parent_page->ID );
					$new_page['post_parent'] = $post_parent_id;
				}
			}
			$new_page_en_id = wp_insert_post( $new_page, true );
			if ( ! is_wp_error( $new_page_en_id ) && $new_page_en_id > 0 ) {
				update_post_meta( $new_page_en_id, '_wp_page_template', $new_content_template );
			} else {
				$new_page_en_id = 0;
			}
		}
		// Assign the EN language to the page.
		if ( $new_page_en_id > 0 ) {
			dli_set_post_language( $new_page_en_id, 'en' );
		}

		// Associate it and en translations.
		if ( $new_page_it_id > 0 && $new_page_en_id > 0 ) {
			$related_posts = array(
				'it' => $new_page_it_id,
				'en' => $new_page_en_id,
			);
			dli_save_post_translations( $related_posts );
		}
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
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Non hai i permessi per accedere a questa pagina.', 'design_laboratori_italia' ) );
	}

	$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
	$nonce  = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

	if ( 'reload' === $action ) {
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'dli_reload_theme_data' ) ) {
			wp_die( esc_html__( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' ) );
		}
		dli_create_pages_on_theme_activation();
	}

	$reload_url = wp_nonce_url(
		admin_url( 'themes.php?page=reload-data-theme-options&action=reload' ),
		'dli_reload_theme_data'
	);

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Ricarica i dati di attivazione del tema', 'design_laboratori_italia' ) . '</h1>';

	echo '<a href="' . esc_url( $reload_url ) . '" class="button button-primary">' . esc_html__( 'Ricarica i dati di attivazione (menu, pagine, tassonomie, etc)', 'design_laboratori_italia' ) . '</a>';
	echo '</div>';
}

/**
 * Create custom translation table if missing.
 *
 * @return void
 */
function dli_create_the_tables() {
	global $wpdb;
	$table_name     = $wpdb->prefix . 'dli_custom_translations';
	$existing_table = $wpdb->get_var(
		$wpdb->prepare(
			'SHOW TABLES LIKE %s',
			$table_name
		)
	);
	if ( $existing_table !== $table_name ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				label TEXT NOT NULL,
				domain VARCHAR(100) NOT NULL,
				lang VARCHAR(2) NOT NULL,
				translation TEXT NOT NULL,
				PRIMARY KEY (id),
				KEY idx_text_domain (domain)
		) ENGINE=InnoDB $charset_collate;";
		dbDelta( $sql );
	}
}
