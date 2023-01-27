<?php
/**
 * Creazione del post type News.
 *
 * @package Design_Laboratori_Italia
 */

// Post Types.
define( 'NEWS_POST_TYPE', 'notizia' );

/**
 * The manager that setups Course post types.
 */
class News_Manager {
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
			'name'                  => _x( 'Notizie', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Notizia', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi una Notizia', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi una Notizia', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica la Notizia', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza la Notizia', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Logo Identificativo della Notizia', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Notizia' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Notizia' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Notizia' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Notizia', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			// 'hierarchical'    => true,
			'public'          => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-welcome-widgets-menus',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array(
				'slug' => 'notizie',
			),
			// 'map_meta_cap'    => true,
		);

		register_post_type( NEWS_POST_TYPE, $args );

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
		if ( NEWS_POST_TYPE === $post->post_type ) {
			_e( '<h1>Descrizione notizia</h1>', 'design_laboratori_italia' );
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
				'key' => 'group_63cab53e5eb0e',
				'title' => 'Campi Notizia',
				'fields' => array(
					array(
						'key' => 'field_63cab53e63c56',
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
						'key' => 'field_63cab53e76998',
						'label' => 'Promuovi in hero',
						'name' => 'promuovi_in_hero',
						'aria-label' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 1,
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
						'key' => 'field_63cab53e7a489',
						'label' => 'Progetto',
						'name' => 'progetto',
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
						'key' => 'field_63cab53e7de3e',
						'label' => 'Indirizzo di ricerca',
						'name' => 'indirizzo_di_ricerca',
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
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'notizia',
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
