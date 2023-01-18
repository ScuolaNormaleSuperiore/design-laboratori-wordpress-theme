<?php
// Post Types.
define( 'PEOPLE_POST_TYPE', 'persona' );

// Taxonomies
define( 'PEOPLE_TYPE_TAXONOMY', 'tipo-persona' );

/**
 * The manager that setups Course post types.
 */
class People_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Install and configure the Course post type.
	 *
	 * @return void
	 */
	public function setup() {
		// Register the taxonomies used by this post type.
		add_action( 'init', array( $this, 'add_people_taxonomies' ) );

		// Register the post type of this plugin.
		add_action( 'init', array( $this, 'add_people_post_type' ) );

		// Set default permissions for this content-type.
		add_action( 'init', array( $this, 'add_people_permissions' ) );
	}
	
	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_people_taxonomies() {
		$labels = array(
			'name'              => _x( 'Tipologia Persona', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Tipologia Persona', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca Tipologia', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutte le tipologie', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica la Tipologia', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna la Tipologia', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi una Tipologia', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova Tipologia', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Tipologia', 'design_laboratori_italia' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipologia-persona' ),
			'capabilities'      => array(
					'manage_terms'  => 'manage_tipologia_persone',
					'edit_terms'    => 'edit_tipologia_persone',
					'delete_terms'  => 'delete_tipologia_persone',
					'assign_terms'  => 'assign_tipologia_persone'
			),
		);

		register_taxonomy( PEOPLE_TYPE_TAXONOMY, array( PEOPLE_POST_TYPE ), $args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_people_post_type() {

		$labels = array(
			'name'                  => _x( 'Persone', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi la Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Immagine principale della Persona', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine' ),
			'remove_featured_image' => __( 'Rimuovi Immagine' . 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine della Persona' . 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Persona', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => true,
			'public'          => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			// 'menu_position'   => 2,
			'menu_icon'       => 'dashicons-dashboard',
			'has_archive'     => true,
			'capability_type' => array( 'persona', 'persone' ),
			'map_meta_cap'    => true,
		);

		register_post_type( PEOPLE_POST_TYPE, $args );

		// Needed to refrewsh permalinks
		// Same as: Admin->Settings->Permalinks->Save.
		flush_rewrite_rules();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function add_people_permissions() {

	}

}
