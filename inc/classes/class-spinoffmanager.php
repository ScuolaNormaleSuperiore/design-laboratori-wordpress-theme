<?php
/**
 * Definition of the Spin-off Manager used to setup Spinoff post types.
 *
 * @package Design_Laboratori_Italia
 */


 define(
	'DLI_SPINOFF_STATUS',
	array(
		'In attività' => __( 'Cerca area tematica', 'design_laboratori_italia' ),
		'Cessata'     => __( 'Cessata', 'design_laboratori_italia' ),
	)
);


class SpinOff_Manager {
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
		// Aggiungo la tassonomia settore-attività.
		$types_labels = array(
			'name'              => _x( 'Settore attività', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Settore attività', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca settore attività', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutti i settori', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica settore attività', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna settore attività', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi un settore attività', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuovo area settore attività', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Settore attività', 'design_laboratori_italia' ),
		);
		$taxonomy_args = array(
			'hierarchical'      => true,
			'labels'            => $types_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'settore-attivita' ),
			'show_in_rest'      => true,
		);
		register_taxonomy( BUSINESS_SECTOR_TAXONOMY, array( SPINOFF_POST_TYPE ), $taxonomy_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Spin-off', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Spin-off', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi una Spin-off', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi una Spin-off', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Spin-off', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Spin-off', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( "Logo Identificativo della Spin-off", 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Spin-off' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Spin-off' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Spin-off' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Spin-off', 'design_laboratori_italia' ),
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

		register_post_type( SPINOFF_POST_TYPE, $args );

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
		if ( SPINOFF_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Descrizione della Spin-off', 'design_laboratori_italia' );
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
		'key' => 'group_679359beb87e6',
		'title' => 'Campi Spin-off',
		'fields' => array(
			array(
				'key' => 'field_67936f245aeb3',
				'label' => 'Descrizione breve',
				'name' => 'descrizione_breve',
				'aria-label' => '',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'maxlength' => '',
				'allow_in_bindings' => 0,
				'rows' => '',
				'placeholder' => '',
				'new_lines' => '',
			),
			array(
				'key' => 'field_67935a0de738c',
				'label' => 'Anno di costituzione',
				'name' => 'anno_costituzione',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_67935a3ce738d',
				'label' => 'Stato',
				'name' => 'stato',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'In attività' => 'In attività',
					'Cessata' => 'Cessata',
				),
				'default_value' => 'In attività',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'allow_in_bindings' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_679cd99d9a82a',
				'label' => 'Telefono',
				'name' => 'telefono',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_67935aaf5b6a5',
				'label' => 'Email',
				'name' => 'email',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_67935a9c5b6a4',
				'label' => 'Sito web',
				'name' => 'sito_web',
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
			array(
				'key' => 'field_67935ab55b6a6',
				'label' => 'Note',
				'name' => 'note',
				'aria-label' => '',
				'type' => 'wysiwyg',
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
				'tabs' => 'all',
				'toolbar' => 'full',
				'media_upload' => 1,
				'delay' => 0,
			),
			array(
				'key' => 'field_67935ae63ddd6',
				'label' => 'Video',
				'name' => 'video',
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
					'value' => 'spinoff',
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
