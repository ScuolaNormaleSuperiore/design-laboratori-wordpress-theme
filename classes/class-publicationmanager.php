<?php
/**
 * Creazione del post type Pubblicazioni.
 *
 * @package Design_Laboratori_Italia
 */

// Post Types.
define( 'PUBLICATION_POST_TYPE', 'pubblicazione' );

// Taxonomies.
define( 'CATEGORY_TAXONOMY', 'categoria' );

/**
 * The manager that setups Course post types.
 */
class Publication_Manager {
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
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_taxonomies() {
		// aggiungo la tassonomia categoria

		$category_labels = array(
			'name'              => _x( 'Categoria', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Categoria', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca Categoria', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutte le categorie', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica la Categoria', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna la Categoria', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi una Categoria', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova Categoria', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Categoria', 'design_laboratori_italia' ),
		);

		$category_args = array(
			'hierarchical'      => true,
			'labels'            => $category_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'categoria' ),
			'capabilities'      => array(
				'manage_terms' => 'manage_categorie',
				'edit_terms'   => 'edit_categorie',
				'delete_terms' => 'delete_categorie',
				'assign_terms' => 'assign_categorie',
			),
		);

		register_taxonomy( CATEGORY_TAXONOMY, array( PUBLICATION_POST_TYPE ), $category_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Pubblicazioni', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Pubblicazione', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', ),
			'hierarchical'    => true,
			'public'          => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-book',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array( 'slug' => 'strutture', ),
		);

		register_post_type( PUBLICATION_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_fields();
	}


	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
		if( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group(array(
				'key' => 'group_63ca5c47b914d',
				'title' => 'Campi Pubblicazione',
				'fields' => array(
					array(
						'key' => 'field_63ca5c496dfcd',
						'label' => 'Anno',
						'name' => 'anno',
						'aria-label' => '',
						'type' => 'number',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'min' => '',
						'max' => '',
						'placeholder' => '',
						'step' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63ca5c776dfce',
						'label' => 'Autori',
						'name' => 'autori',
						'aria-label' => '',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'persona',
						),
						'taxonomy' => '',
						'filters' => array(
							0 => 'search',
						),
						'return_format' => 'object',
						'min' => '',
						'max' => '',
						'elements' => '',
					),
					array(
						'key' => 'field_63e652af2c892',
						'label' => 'Abstract',
						'name' => 'abstract',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'maxlength' => 255,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63e652c02c893',
						'label' => 'Url',
						'name' => 'url',
						'aria-label' => '',
						'type' => 'url',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
					),
					array(
						'key' => 'field_63e6d1ff4bf3c',
						'label' => 'Promuovi in hero',
						'name' => 'promuovi_in_hero',
						'aria-label' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 1,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_63e652d32c894',
						'label' => 'Promuovi in Home',
						'name' => 'promuovi_in_home',
						'aria-label' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'pubblicazione',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 1,
			));
		}
	}

}
