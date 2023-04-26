<?php
/**
 * Creazione del post types Luogo.
 *
 * @package Design_Laboratori_Italia
 */



/**
 * The manager that setups People post types.
 */
class Place_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Install and configure the Place post type.
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
		// aggiungo la tassonomia tipologia luogo.

		$place_type_labels = array(
			'name'              => _x( 'Tipologia luogo', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Tipologia luogo', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca Tipologia luogo', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutte le tipologie luogo', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica la Tipologia luogo', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna la Tipologia luogo', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi una Tipologia luogo', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova Tipologia luogo', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Tipologia luogo', 'design_laboratori_italia' ),
		);

		$place_type_args = array(
			'hierarchical'      => true,
			'labels'            => $place_type_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipologia-luogo' ),
			// 'capabilities'      => array(
			// 	'manage_terms' => 'manage_tipologie_luogo',
			// 	'edit_terms'   => 'edit_tipologie_luogo',
			// 	'delete_terms' => 'delete_tipologie_luogo',
			// 	'assign_terms' => 'assign_tipologie_luogo',
			// ),
		);

		register_taxonomy( PLACE_TYPE_TAXONOMY, array( PLACE_POST_TYPE ), $place_type_args );
	}


	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Luoghi', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Luogo', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Luogo', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi il Luogo', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica il Luogo', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza il Luogo', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Immagine principale del Luogo', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine' ),
			'remove_featured_image' => __( 'Rimuovi Immagine' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine del Luogo' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Luogo', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'public'          => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 6,
			'menu_icon'       => 'dashicons-pressthis',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'hierarchical'    => true,
			'rewrite'         => array('slug' => 'luoghi'),
		);

		register_post_type( PLACE_POST_TYPE, $args );

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
		if ( PLACE_POST_TYPE === $post->post_type ) {
			_e( '<span><i>il <b>Titolo</b> è il <b>Nome del Luogo</b>.</i></span> in cui si svolge l\'attività del laboratorio. I luoghi possono essere sede di strutture e canali fisici di erogazione di un servizio<br><br>','design_laboratori_italia' );
			_e( '<h1>Descrizione del luogo</h1>', 'design_laboratori_italia' );
		}
	}

	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {

			acf_add_local_field_group(array(
				'key' => 'group_640091684b5ab',
				'title' => 'Campi Luogo',
				'fields' => array(
					array(
						'key' => 'field_640b0dc4ea339',
						'label' => 'Descrizione breve',
						'name' => 'descrizione_breve',
						'aria-label' => '',
						'type' => 'textarea',
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
						'rows' => '',
						'placeholder' => '',
						'new_lines' => '',
					),
					array(
						'center_lat' => 43.716667,
						'center_lng' => 10.4,
						'zoom' => 12,
						'height' => 400,
						'return_format' => 'leaflet',
						'allow_map_layers' => 1,
						'max_markers' => '',
						'layers' => array(
							0 => 'OpenStreetMap.Mapnik',
						),
						'key' => 'field_64009299f3a34',
						'label' => 'Posizione GPS',
						'name' => 'posizione_gps',
						'aria-label' => '',
						'type' => 'open_street_map',
						'instructions' => 'NB: Inserisci e cerca l\'indirizzo, anche se lo hai già inserito nel campo precedente. Questo permetterà una corretta georeferenziazione del luogo',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
					),
					array(
						'key' => 'field_64009a913a198',
						'label' => 'Riferimento mail',
						'name' => 'riferimento_mail',
						'aria-label' => '',
						'type' => 'email',
						'instructions' => 'Indirizzo di posta elettronica del luogo.',
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
						'key' => 'field_64009abf3a199',
						'label' => 'PEC',
						'name' => 'pec',
						'aria-label' => '',
						'type' => 'email',
						'instructions' => 'Indirizzo di posta elettronica certificata (PEC).',
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
						'key' => 'field_64009ad53a19a',
						'label' => 'Riferimento telefonico',
						'name' => 'riferimento_telefonico',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => 'Telefono del luogo.',
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
						'key' => 'field_6400916967a3b',
						'label' => 'Indirizzo',
						'name' => 'indirizzo',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => 'Indirizzo del luogo.',
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
						'key' => 'field_64009a193a197',
						'label' => 'CAP',
						'name' => 'cap',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => 'Codice di avviamento postale del luogo',
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
						'key' => 'field_64009af93a19b',
						'label' => 'Orario per il pubblico',
						'name' => 'orario_per_il_pubblico',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => 'Orario di apertura al pubblico del luogo.',
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
						'key' => 'field_64009b8ebdef8',
						'label' => 'Come raggiungerci',
						'name' => 'come_raggiungerci',
						'aria-label' => '',
						'type' => 'textarea',
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
						'rows' => '',
						'placeholder' => '',
						'new_lines' => '',
					),
					array(
						'key' => 'field_6448ecc3db200',
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
							'value' => 'luogo',
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
