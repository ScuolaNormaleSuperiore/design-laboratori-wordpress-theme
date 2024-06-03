<?php
/**
 * Creazione del post type Banner.
 *
 * @package Design_Laboratori_Italia
 */


/**
 * The manager that setups Banner post types.
 */
class Banner_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Install and configure the Banner post type.
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
			'name'                  => _x( 'Banner', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Banner', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi un Banner', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi un Banner', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica il Banner', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza il Banner', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( 'Immagine del Banner', 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Banner' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Banner' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Banner' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Banner', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => false,
			'public'          => true,
			'show_in_menu'    => true,
			'menu_position'   => 3,
			'menu_icon'       => 'dashicons-excerpt-view',
			'has_archive'     => false,
			'show_in_rest'    => false,
		);

		register_post_type( BANNER_POST_TYPE, $args );

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
			echo '<h1>';
			_e( 'Descrizione banner', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}

	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
		if( function_exists('acf_add_local_field_group') ) {
			acf_add_local_field_group( array(
				'key' => 'group_665db72f0e01c',
				'title' => 'Campi Banner',
				'fields' => array(
					array(
						'key' => 'field_665db7b8a6767',
						'label' => 'Sezione',
						'name' => 'sezione',
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
						'key' => 'field_665db79da906f',
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
						'default_value' => 5,
						'min' => '',
						'max' => '',
						'placeholder' => '',
						'step' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_665db731d1d8c',
						'label' => 'Label pulsante',
						'name' => 'label_pulsante',
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
						'key' => 'field_665db781162de',
						'label' => 'Link pulsante',
						'name' => 'link_pulsante',
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
						'key' => 'field_665dbaeac83e7',
						'label' => 'Apri in nuova finestra',
						'name' => 'apri_in_nuova_finestra',
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
						'message' => 'Apri in nuova finestra',
						'default_value' => 1,
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
							'value' => 'banner',
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

}
