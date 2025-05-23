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
			'label'         => __( 'Risorsa Tecnica', 'design_laboratori_italia' ),
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
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( array(
		'key' => 'group_682edcc15188d',
		'title' => 'Campi Risorsa Tecnica',
		'fields' => array(
			array(
				'key' => 'field_682ed8f4bbd48',
				'label' => 'Foto',
				'name' => 'foto',
				'aria-label' => '',
				'type' => 'image',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
				'preview_size' => 'medium',
			),
			array(
				'key' => 'field_682de92c811fe',
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
				'key' => 'field_682ecb5410ce5',
				'label' => 'Codice interno',
				'name' => 'codice_interno',
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
				'key' => 'field_682de981811ff',
				'label' => 'Costo',
				'name' => 'costo',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => false,
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
			array(
				'key' => 'field_682de99c81200',
				'label' => 'Posizione',
				'name' => 'posizione',
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
				'key' => 'field_682ecbda4b491',
				'label' => 'Localizzazione',
				'name' => 'localizzazione',
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
					0 => 'luogo',
				),
				'post_status' => '',
				'taxonomy' => '',
				'filters' => array(
					0 => 'search',
				),
				'return_format' => 'object',
				'min' => '',
				'max' => 1,
				'allow_in_bindings' => 0,
				'elements' => '',
				'bidirectional' => 0,
				'bidirectional_target' => array(
				),
			),
			array(
				'key' => 'field_682ecb6e4b48b',
				'label' => 'Marca',
				'name' => 'marca',
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
				'key' => 'field_682ecb784b48c',
				'label' => 'Modello',
				'name' => 'modello',
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
				'key' => 'field_682ecb894b48d',
				'label' => 'Anno acquisizione',
				'name' => 'anno_acquisizione',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
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
				'key' => 'field_682ecbb14b48f',
				'label' => 'Stato',
				'name' => 'stato',
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
				'key' => 'field_682ecb974b48e',
				'label' => 'Dimensioni e peso',
				'name' => 'dimensioni_e_peso',
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
				'key' => 'field_682eccac56fd0',
				'label' => 'Conformità',
				'name' => 'conformita',
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
				'key' => 'field_682ecbc14b490',
				'label' => 'Responsabili',
				'name' => 'responsabile',
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
				'post_status' => '',
				'taxonomy' => '',
				'filters' => array(
					0 => 'search',
				),
				'return_format' => 'object',
				'min' => '',
				'max' => '',
				'allow_in_bindings' => 0,
				'elements' => '',
				'bidirectional' => 0,
				'bidirectional_target' => array(
				),
			),
			array(
				'key' => 'field_682de9f981202',
				'label' => 'Scheda tecnica',
				'name' => 'scheda_tecnica',
				'aria-label' => '',
				'type' => 'file',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
			),
			array(
				'key' => 'field_682ecc5856fcf',
				'label' => 'Manuale uso',
				'name' => 'manuale_uso',
				'aria-label' => '',
				'type' => 'file',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
			),
			array(
				'key' => 'field_682ece796b420',
				'label' => 'Allegato 1',
				'name' => 'allegato_1',
				'aria-label' => '',
				'type' => 'file',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
			),
			array(
				'key' => 'field_682eef148801d',
				'label' => 'Allegato 2',
				'name' => 'allegato_2',
				'aria-label' => '',
				'type' => 'file',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => '',
				'allow_in_bindings' => 0,
			),
			array(
				'key' => 'field_682edc961fb0a',
				'label' => 'Promuovi in home',
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
				'allow_in_bindings' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_682edcd60e3aa',
				'label' => 'Promuovi in carousel',
				'name' => 'promuovi_in_carousel',
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
				'allow_in_bindings' => 0,
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
					'value' => 'risorsa-tecnica',
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
