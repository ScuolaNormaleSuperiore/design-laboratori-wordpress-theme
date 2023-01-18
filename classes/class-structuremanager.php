<?php
// Post Types.
define( 'STRUCTURE_POST_TYPE', 'struttura' );

// Taxonomies
define( 'STRUCTURE_TYPE_TAXONOMY', 'tipo-struttura' );

/**
 * The manager that setups Course post types.
 */
class Structure_Manager {
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
		add_action( 'init', array( $this, 'add_structure_taxonomies' ) );

		// Register the post type of this plugin.
		add_action( 'init', array( $this, 'add_structure_post_type' ) );
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_structure_taxonomies() {
		$labels = array(
			'name'              => _x( 'Tipologia Struttura', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Tipologia Struttura', 'taxonomy singular name', 'design_laboratori_italia' ),
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
			'rewrite'           => array( 'slug' => 'tipologia-struttura' ),
			'capabilities'      => array(
					'manage_terms'  => 'manage_tipologia_strutture',
					'edit_terms'    => 'edit_tipologia_strutture',
					'delete_terms'  => 'delete_tipologia_strutture',
					'assign_terms'  => 'assign_tipologia_strutture'
			),
		);

		register_taxonomy( STRUCTURE_TYPE_TAXONOMY, array( STRUCTURE_POST_TYPE ), $args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_structure_post_type() {

		$labels = array(
			'name'                  => _x( 'Strutture', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Struttura', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Immagine principale della Struttura', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine' ),
			'remove_featured_image' => __( 'Rimuovi Immagine' . 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine della Struttura' . 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Struttura Organizzativa', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => true,
			'public'          => true,
			// 'menu_position'   => 2,
			'menu_icon'       => 'dashicons-networking',
			'has_archive'     => true,
			'capability_type' => array( 'struttura', 'strutture' ),
			'map_meta_cap'    => true,
		);

		// Needed to refrewsh permalinks
		// Same as: Admin->Settings->Permalinks->Save.
		flush_rewrite_rules();
		register_post_type( STRUCTURE_POST_TYPE, $args );
	}

}
