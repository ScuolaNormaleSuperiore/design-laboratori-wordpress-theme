<?php
/**
 * Il manager che fa il setup del custom type Contatto.
 *
 * @package Design_Laboratori_Italia
 */

class Contact_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Installa e configura il post type Contatto.
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
			'name'                  => _x( 'Contatti', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Contatto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi Contatto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi un Contatto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica il Contatto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza il Contatto', 'Post Type Singular Name', 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Contatto', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', ),
			'public'          => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-groups',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite' => array('slug' => 'contatti')
		);

		register_post_type( 'contatto', $args );

		// Add the custom fields.
		$this->add_fields();
	}


	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
		if( function_exists('acf_add_local_field_group') ){

			acf_add_local_field_group(array(
				'key' => 'group_63ce5d9c8654b',
				'title' => 'Campi Contatto',
				'fields' => array(
					array(
						'key' => 'field_63ce5d9d8d851',
						'label' => 'Label',
						'name' => 'label',
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
						'key' => 'field_63ce5db38d852',
						'label' => 'Mail',
						'name' => 'mail',
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
						'key' => 'field_63ce5dbe8d853',
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
						'key' => 'field_63ce5dcb8d854',
						'label' => 'URL',
						'name' => 'url',
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
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'contatto',
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
