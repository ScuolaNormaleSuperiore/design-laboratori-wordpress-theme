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

		// Register the post type.
		add_action( 'init', array( $this, 'add_people_post_type' ) );

		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );
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
				'manage_terms' => 'manage_tipologia_persone',
				'edit_terms'   => 'edit_tipologia_persone',
				'delete_terms' => 'delete_tipologia_persone',
				'assign_terms' => 'assign_tipologia_persone',
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
			// 'hierarchical'    => true,
			'public'          => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-businessperson',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array('slug' => 'persone'),
			// 'map_meta_cap'    => true,
		);

		register_post_type( PEOPLE_POST_TYPE, $args );

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
		if ( PEOPLE_POST_TYPE === $post->post_type ) {
			_e( '<span><i>Inserire nel titolo il nome completo della persona ed eventualmente il nome completo per facilitare la ricerca.</i></span>','design_laboratori_italia' );
			_e( '<h1>Biografia</h1> Biografia della persona', 'design_laboratori_italia' );
		}
	}

	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
		if( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group(array(
				'key' => 'group_63c808f35b51a',
				'title' => 'Campi Persona',
				'fields' => array(
					array(
						'key' => 'field_63c958fb0e14f',
						'label' => 'Titolo',
						'name' => 'titolo',
						'aria-label' => '',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'Dott.' => 'Dott.',
							'Dott.ssa' => 'Dott.ssa',
							'Dr.' => 'Dr.',
							'Prof.' => 'Prof.',
							'Prof.ssa' => 'Prof.ssa',
							'Ing.' => 'Ing.',
							'Avv.' => 'Avv.',
							'Sig.' => 'Sig.',
							'Sig.ra' => 'Sig.ra',
						),
						'default_value' => false,
						'return_format' => 'value',
						'multiple' => 0,
						'allow_null' => 1,
						'ui' => 0,
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_63c958b563559',
						'label' => 'Nome',
						'name' => 'nome',
						'aria-label' => '',
						'type' => 'text',
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
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63c808f420306',
						'label' => 'Cognome',
						'name' => 'cognome',
						'aria-label' => '',
						'type' => 'text',
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
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63c8090f20307',
						'label' => 'Email',
						'name' => 'email',
						'aria-label' => '',
						'type' => 'email',
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
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63c8141a68f06',
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
						'preview_size' => 'medium',
					),
					array(
						'key' => 'field_63c8144668f07',
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
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_63c8146368f08',
						'label' => 'Escludi da elenco',
						'name' => 'escludi_da_elenco',
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
					array(
						'key' => 'field_63c814c668f09',
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
						'placeholder' => 'https://',
					),
					array(
						'key' => 'field_63c8152d68f0b',
						'label' => 'Progetti correlati',
						'name' => 'progetti_correlati',
						'aria-label' => '',
						'type' => 'post_object',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'progetto',
						),
						'taxonomy' => '',
						'return_format' => 'object',
						'multiple' => 0,
						'allow_null' => 0,
						'ui' => 1,
					),
					array(
						'key' => 'field_63c8159068f0c',
						'label' => 'Allegato CV',
						'name' => 'allegato_cv',
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
						'max_size' => 5,
						'mime_types' => 'pdf',
					),
					array(
						'key' => 'field_63c815d868f0d',
						'label' => 'Pubblicazioni',
						'name' => 'pubblicazioni',
						'aria-label' => '',
						'type' => 'post_object',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'scheda_progetto',
						),
						'taxonomy' => '',
						'return_format' => 'object',
						'multiple' => 0,
						'allow_null' => 0,
						'ui' => 1,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'persona',
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
			));
		}

	}

}
