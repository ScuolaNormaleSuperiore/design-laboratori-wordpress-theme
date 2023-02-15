<?php
/**
 * Creazione dei post.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that setups People post types.
 */
class Post_Manager {
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
		add_action( 'init', array( $this, 'add_post_fields' ) );
	}

	/**
	 * Add fields to the post.
	 *
	 * @return void
	 */
	public function add_post_fields() {
		if( function_exists( 'acf_add_local_field_group' ) ){

			acf_add_local_field_group(array(
				'key' => 'group_63ecbcdb295e6',
				'title' => 'Campi Articolo',
				'fields' => array(
					array(
						'key' => 'field_63ecbcdc7e02b',
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
					array(
						'key' => 'field_63ecbd31e4611',
						'label' => 'Promuovi in hero',
						'name' => 'promuovi_in_hero',
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
							'value' => 'post',
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
