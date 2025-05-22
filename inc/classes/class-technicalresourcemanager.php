<?php
/**
 * Definition of the Technical Resource Manager.
 *
 * @package Design_Laboratori_Italia
 */




class TechnicalResource_Manager {
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
		// Aggiungo la tassonomia area-tematica.
		$types_labels = array(
			'name'              => _x( 'Tipo risorsa', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Tipo risorsa', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca tipo risorsa', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutti i tipi', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica tipo risorsa', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna tipo risorsa', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi un tipo risorsa', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuovo tipo risorsa', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Tipo risorsa', 'design_laboratori_italia' ),
		);
		$taxonomy_args = array(
			'hierarchical'      => true,
			'labels'            => $types_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipo-risorsa-tecnica' ),
			'show_in_rest'      => true,
		);
		register_taxonomy( RT_TYPE_TAXONOMY, array( TECHNICAL_RESOURCE_POST_TYPE ), $taxonomy_args );
	}


	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'          => _x( 'Risorse Tecniche', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name' => _x( 'Risorsa Tecnica', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'       => _x( 'Aggiungi una risorsa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'  => _x( 'Aggiungi una risorsa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'     => _x( 'Modifica la risorsa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'     => _x( 'Visualizza la risorsa', 'Post Type Singular Name', 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'         => __( 'Risora Tecnica', 'design_laboratori_italia' ),
			'labels'        => $labels,
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'  => false,
			'public'        => true,
			'show_in_menu'  => true,
			'menu_position' => 6,
			'menu_icon'     => 'dashicons-products',
			'has_archive'   => false,
			'show_in_rest'  => true,
			'taxonomies'    => array( WP_DEFAULT_CATEGORY, WP_DEFAULT_TAGS ),
		);

		register_post_type( TECHNICAL_RESOURCE_POST_TYPE, $args );

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
		if ( TECHNICAL_RESOURCE_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Descrizione della Risorsa Tecnica', 'design_laboratori_italia' );
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
