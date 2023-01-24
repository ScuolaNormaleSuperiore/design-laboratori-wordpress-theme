<?php
// Post Types.
define( 'PEOPLE_POST_TYPE', 'persona' );

define( 'PEOPLE_TYPE_POST_TYPE', 'tipologia-persona' );

define ('STRUCTURE_TAXONOMY', 'struttura');

/**
 * The manager that setups People post types.
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
		//aggiungo la tassonomia struttura

		$structure_labels = array(
			'name'              => _x( 'Struttura', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Struttura', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca Struttura', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutte le strutture', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica la Struttura', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna la Struttura', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi una Struttura', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova Struttura', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Struttura', 'design_laboratori_italia' ),
		);

		$structure_args = array(
			'hierarchical'      => true,
			'labels'            => $structure_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'struttura' ),
			'capabilities'      => array(
				'manage_terms' => 'manage_strutture',
				'edit_terms'   => 'edit_strutture',
				'delete_terms' => 'delete_strutture',
				'assign_terms' => 'assign_strutture'
			),
		);

		register_taxonomy( STRUCTURE_TAXONOMY, array( PEOPLE_POST_TYPE ), $structure_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

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
			'public'          => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-businessperson',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array('slug' => 'persone'),
		);

		register_post_type( PEOPLE_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_fields();

		//register post type Tipologia Persona

		$labels = array(
			'name'                  => _x( 'Tipologia Persone', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Tipologia Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Tipologia  Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi la Tipologia Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Tipologia Persona', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Tipologia  Persona', 'Post Type Singular Name', 'design_laboratori_italia' )
		);

		$args   = array(
			'label'           => __( 'Tipologia Persona', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title'),
			'public'          => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-nametag',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array('slug' => 'tipologia-persone'),
		);

		register_post_type( PEOPLE_TYPE_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_tipologia_fields();
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
		if( function_exists('acf_add_local_field_group') ) {

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
						'key' => 'field_63ceb5a83d83e',
						'label' => 'Disattiva pagina dettaglio',
						'name' => 'disattiva_pagina_dettaglio',
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
						'message' => 'Disattiva il link alla pagina di dettaglio della persona',
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
						'key' => 'field_63cfb60b96994',
						'label' => 'Categoria di appartenenza',
						'name' => 'categoria_appartenenza',
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
							0 => 'tipologia-persona',
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
						'key' => 'field_63c8152d68f0b',
						'label' => 'Progetti correlati',
						'name' => 'progetti_correlati',
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
							0 => 'progetto',
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
						'key' => 'field_63ceb661fa3b1',
						'label' => 'Altri allegati',
						'name' => 'altri_allegati',
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
						'library' => 'uploadedTo',
						'min_size' => '',
						'max_size' => '',
						'mime_types' => '',
					),
					array(
						'key' => 'field_63c815d868f0d',
						'label' => 'Pubblicazioni',
						'name' => 'pubblicazioni',
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
							0 => 'pubblicazione',
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
				'show_in_rest' => 1,
			));
			
			
		}
	}

	/**
	 * Add the custom fields of the tipologia persona custom post-type.
	 *
	 * @return void
	 */
	function add_tipologia_fields() {
		if( function_exists('acf_add_local_field_group') ) {

			acf_add_local_field_group(array(
				'key' => 'group_63cfb5b25e045',
				'title' => 'Campi Tipologia Persona',
				'fields' => array(
					array(
						'key' => 'field_63cfb5b3bd2e0',
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
						'key' => 'field_63cfb5c6bd2e1',
						'label' => 'Descrizione',
						'name' => 'descrizione',
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
						'key' => 'field_63cfb5cdbd2e2',
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
						'default_value' => '',
						'min' => 1,
						'max' => '',
						'placeholder' => '',
						'step' => '',
						'prepend' => '',
						'append' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'tipologia-persona',
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
