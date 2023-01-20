<?php

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
	}

		function dsi_register_indirizzo_di_ricerca_post_type() {
				$labels = array(
						'name'          => _x( 'Indirizzi di ricerca', 'Post Type General Name', 'design_laboratori_italia' ),
						'singular_name' => _x( 'Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new'       => _x( 'Aggiungi un Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new_item'  => _x( 'Aggiungi un nuovo Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'edit_item'       => _x( 'Modifica l\'Indirizzo di ricerca', 'Post Type Singular Name', 'design_laboratori_italia' )
				);

				$args   = array(
						'label'         => __( 'Indirizzo di ricerca', 'design_laboratori_italia' ),
						'labels'        => $labels,
						'supports'      => array( 'title'),
						'public'        => true,
						'show_in_rest' => true,
						'menu_position' => 2,
						'menu_icon'     => 'dashicons-book-alt',
						'has_archive'   => true,
						'rewrite' => array('slug' => 'indirizzi-di-ricerca'),
				);

				register_post_type( 'indirizzo-di-ricerca', $args );

				// Add the custom fields.
				$this->add_fields();
		}

		function add_fields() {
			if( function_exists('acf_add_local_field_group') ) {

				acf_add_local_field_group(array(
					'key' => 'group_63ca9591d0d91',
					'title' => 'Campi Indirizzo di ricerca',
					'fields' => array(
						array(
							'key' => 'field_63ca9592c7532',
							'label' => 'Logo indirizzo di ricerca',
							'name' => 'logo_indirizzo_di_ricerca',
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
							'library' => 'uploadedTo',
							'min_width' => '',
							'min_height' => '',
							'min_size' => '',
							'max_width' => '',
							'max_height' => '',
							'max_size' => '',
							'mime_types' => 'png, jpg, jpeg',
							'preview_size' => 'medium',
						),
						array(
							'key' => 'field_63ca95fba16fc',
							'label' => 'Descrizione breve',
							'name' => 'descrizione_breve',
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
							'maxlength' => 255,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						),
						array(
							'key' => 'field_63ca9616a16fd',
							'label' => 'Testo descrittivo',
							'name' => 'testo_descrittivo',
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
							'key' => 'field_63ca964ca16fe',
							'label' => 'Contatti di riferimento',
							'name' => 'contatti_di_riferimento',
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
							'key' => 'field_63ca969ca16ff',
							'label' => 'Responsabile dell’attività di ricerca',
							'name' => 'responsabile_attivita_di_ricerca',
							'aria-label' => '',
							'type' => 'relationship',
							'instructions' => '',
							'required' => 1,
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
							'min' => 1,
							'max' => '',
							'elements' => '',
						),
						array(
							'key' => 'field_63ca96e9a1700',
							'label' => 'Elenco progetti collegati',
							'name' => 'elenco_progetti_collegati',
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
							'key' => 'field_63ca9713a1701',
							'label' => 'Eventi collegati',
							'name' => 'eventi_collegati',
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
