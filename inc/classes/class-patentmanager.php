<?php
/**
 * Definition of the Patent Manager used to setup Patent post types.
 *
 * @package Design_Laboratori_Italia
 */


class Patent_Manager {
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
		add_action( 'init', array( $this, 'add_taxonomies' ) );
		// Register the post type.
		add_action( 'init', array( $this, 'add_post_type' ) );
		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_taxonomies() {
		// Aggiungo la tassonomia tipo-pubblicazione.
		$publ_types_labels = array(
			'name'              => _x( 'Area tematica', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Area tematica', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca area tematica', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutti i tipi', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica area tematica', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna area tematica', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi un\'area tematica', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova area tematica', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Area Tematica', 'design_laboratori_italia' ),
		);
		$taxnomy_args = array(
			'hierarchical'      => true,
			'labels'            => $publ_types_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipo-pubblicazione' ),
			'show_in_rest'      => true,
		);
		register_taxonomy( THEMATIC_AREA_TAXONOMY, array( PATENT_POST_TYPE ), $taxnomy_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Brevetti', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi un Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi un Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica il Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza il Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( "Logo Identificativo dell'Brevetto", 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Brevetto' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Brevetto' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Brevetto' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Brevetto', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => false,
			'public'          => true,
			'show_in_menu'    => true,
			'menu_position'   => 6,
			'menu_icon'       => 'dashicons-lightbulb',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'taxonomies'      => array( WP_DEFAULT_CATEGORY, WP_DEFAULT_TAGS ),
		);

		register_post_type( PATENT_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_fields();
	}

	/**
	 * Customize the layout of the admin interface.
	 *
	 * @param Object $post - The custom post.
	 * @return string
	 */
	public function custom_layout( $post ) {
		if ( PATENT_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Descrizione brevetto', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}

	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {

	}

}
