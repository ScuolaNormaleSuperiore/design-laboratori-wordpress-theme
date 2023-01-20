<?php
// Post Types.
define( 'STRUCTURE_POST_TYPE', 'struttura' );

// Taxonomies
define( 'STRUCTURE_TYPE_TAXONOMY', 'tipo-struttura' );

/**
 * The manager that setups Course post types.
 */
class Structure_Manager {
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
		$labels = array(
			'name'              => _x( 'Tipologia Struttura', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Tipologia Struttura', 'taxonomy singular name', 'design_laboratori_italia' ),
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
			'rewrite'           => array( 'slug' => 'tipologia-struttura' ),
			'capabilities'      => array(
					'manage_terms'  => 'manage_tipologia_strutture',
					'edit_terms'    => 'edit_tipologia_strutture',
					'delete_terms'  => 'delete_tipologia_strutture',
					'assign_terms'  => 'assign_tipologia_strutture'
			),
		);

		register_taxonomy( STRUCTURE_TYPE_TAXONOMY, array( STRUCTURE_POST_TYPE ), $args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Strutture', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Struttura', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Struttura Organizzativa', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Immagine principale della Struttura', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine' ),
			'remove_featured_image' => __( 'Rimuovi Immagine' . 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine della Struttura' . 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Struttura Organizzativa', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			// 'hierarchical'    => true,
			'public'          => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-networking',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array('slug' => 'strutture'),
			// 'map_meta_cap'    => true,
		);

		register_post_type( STRUCTURE_POST_TYPE, $args );

		// Add the custom fields.
		// $this->add_fields();
	}

	/**
	 * Customize the layout of the admin interface.
	 *
	 * @param Object $post - The custom post.
	 * @return string
	 */
	public function custom_layout( $post ) {
		if ( STRUCTURE_POST_TYPE === $post->post_type ) {
			_e( '<h1>Cosa fa </h1> Elenco/descrizione dei compiti assegnati alla struttura', 'design_laboratori_italia' );
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
				'key' => 'group_63c7d0d3cca8a',
				'title' => 'Campi Struttura',
				'fields' => array(
					array(
						'key' => 'field_63c7dcd4ac6bf',
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
						'rows' => '',
						'placeholder' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_63c7dd01ac6c0',
						'label' => 'La struttura dipende da un\'altra struttura.',
						'name' => 'dipendenza',
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
							0 => 'struttura',
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
						'key' => 'field_63c7ed1a64563',
						'label' => 'Progetti',
						'name' => 'progetti',
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
						'key' => 'field_63c7ed4d64564',
						'label' => 'Persone responsabili',
						'name' => 'persone_responsabili',
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
						'key' => 'field_63c7ed8264565',
						'label' => 'Persone',
						'name' => 'persone',
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
						'key' => 'field_63c7edb564566',
						'label' => 'Altri componenti',
						'name' => 'altri_componenti',
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
						'key' => 'field_63c7edc564567',
						'label' => 'Sede',
						'name' => 'sede',
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
						'key' => 'field_63c7ede464568',
						'label' => 'Recapito telefonico struttura',
						'name' => 'recapito_telefonico_struttura',
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
						'key' => 'field_63c7edff64569',
						'label' => 'Email della struttura',
						'name' => 'email_della_struttura',
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
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'struttura',
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
