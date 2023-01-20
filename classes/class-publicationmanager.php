<?php
// Post Types.
define( 'PUBLICATION_POST_TYPE', 'pubblicazione' );

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
		// Register the post type.
		add_action( 'init', array( $this, 'add_post_type' ) );
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
			// 'hierarchical'    => true,
			'public'          => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-book',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array(
				'slug' => 'strutture',
			),
			// 'map_meta_cap'    => true,
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
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
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
