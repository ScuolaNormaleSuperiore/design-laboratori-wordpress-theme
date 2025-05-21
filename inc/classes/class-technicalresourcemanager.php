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
		// Register the post type.
		add_action( 'init', array( $this, 'add_post_type' ) );
		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );
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
