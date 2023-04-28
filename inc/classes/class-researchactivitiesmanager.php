<?php
/**
 * Creazione del post type Indirizzi di ricerca.
 *
 * @package Design_Laboratori_Italia
 */


/**
	* Il manager che fa il setup del custom type Indirizzo di Ricerca.
	*/
class ResearchActivities_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
	}

	/**
	 * Installa e configura il post type Indirizzo di Ricerca.
	 *
	 * @return void
	 */
	public function setup() {
		// Register the post type.
		add_action( 'init', array( $this, 'dsi_register_indirizzo_di_ricerca_post_type' ) );
		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );
	}

		function dsi_register_indirizzo_di_ricerca_post_type() {
				$labels = array(
						'name'          => _x( 'Indirizzi di ricerca', 'Post Type General Name', 'design_laboratori_italia' ),
						'singular_name' => _x( 'Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new'       => _x( 'Aggiungi un Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new_item'  => _x( 'Aggiungi un nuovo Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'edit_item'     => _x( 'Modifica l\'Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' )
				);

				$args   = array(
						'label'         => __( 'Indirizzo di ricerca', 'design_laboratori_italia' ),
						'labels'        => $labels,
						'supports'      => array( 'title', 'editor', 'thumbnail' ),
						'public'        => true,
						'show_in_rest'  => true,
						'show_in_menu'  => true,
						'menu_position' => 6,
						'menu_icon'     => 'dashicons-book-alt',
						'has_archive'   => true,
						'rewrite'       => array( 'slug' => 'indirizzi-di-ricerca' ),
				);

				register_post_type( RESEARCHACTIVITY_POST_TYPE, $args );

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
		if ( RESEARCHACTIVITY_POST_TYPE === $post->post_type ) {
			_e( '<h1>Descrizione indirizzo di ricerca</h1>', 'design_laboratori_italia' );
		}
	}

		function add_fields() {

			if( function_exists('acf_add_local_field_group') ) {

				acf_add_local_field_group(array(
					'key' => 'group_63ca9591d0d91',
					'title' => 'Campi Indirizzo di ricerca',
					'fields' => array(
						array(
							'key' => 'field_63ca95fba16fc',
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
							'maxlength' => 255,
							'rows' => '',
							'placeholder' => '',
							'new_lines' => '',
						),
						array(
							'key' => 'field_63ca964ca16fe',
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
							'key' => 'field_63e65557d07f6',
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
							'key' => 'field_63e65567d07f7',
							'label' => 'Sito web',
							'name' => 'sitioweb',
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
							'key' => 'field_63ca969ca16ff',
							'label' => 'Responsabile dell’attività di ricerca',
							'name' => 'responsabile_attivita_di_ricerca',
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
							'min' => 0,
							'max' => '',
							'elements' => '',
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'indirizzo-di-ricerca',
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
