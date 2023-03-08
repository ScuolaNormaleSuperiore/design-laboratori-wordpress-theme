<?php
/**
 * The manager that setups People type post types.
 */
class PeopleType_Manager {
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
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		// Register post type Tipologia Persona.

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
			'supports'        => array( 'title' ),
			'public'          => true,
			'show_in_menu'    =>	'edit.php?post_type=persona',
			'show_in_rest'    => true,
			'menu_position'   => 2,
			'menu_icon'       => 'dashicons-nametag',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rewrite'         => array( 'slug' => 'tipologia-persone' ),
		);

		register_post_type( PEOPLE_TYPE_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_fields();
	}
	
	/**
	 * Add the custom fields of the tipologia persona custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {
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
