<?php
/**
 * Definition of the Sponsor Manager used to setup Sponsor post types.
 *
 * @package Design_Laboratori_Italia
 */




class Sponsor_Manager {
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
			'name'                  => _x( 'Sponsor', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Sponsor', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi uno Sponsor', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi uno Sponsor', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica lo Sponsor', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza lo Sponsor', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( "Logo Identificativo dello Sponsor", 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Sponsor' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Sponsor' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Sponsor' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Sponsor', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => false,
			'public'          => true,
			'show_in_menu'    => true,
			'menu_position'   => 6,
			'menu_icon'       => 'dashicons-admin-tools',
			'has_archive'     => false,
			'show_in_rest'    => true,
			'taxonomies'      => array( WP_DEFAULT_CATEGORY, WP_DEFAULT_TAGS ),
		);

		register_post_type( SPONSOR_POST_TYPE, $args );

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
		if ( SPONSOR_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Descrizione dello Sponsor', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}




	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {


		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
	
		acf_add_local_field_group( array(
		'key' => 'group_67a9c1b364f8f',
		'title' => 'Campi Sponsor',
		'fields' => array(
			array(
				'key' => 'field_67a9c1b6e6ecb',
				'label' => 'Priorità',
				'name' => 'priorita',
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
				'default_value' => 1,
				'min' => '',
				'max' => '',
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_67a9c1dce6ecc',
				'label' => 'Link esterno',
				'name' => 'link_esterno',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'sponsor',
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
		'show_in_rest' => 0,
	) );


	}

}
