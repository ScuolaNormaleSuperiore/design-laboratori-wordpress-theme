<?php
/**
 * Il manager che fa il setup del custom type Progetto.
 *
 * @package Design_Laboratori_Italia
 */


class Project_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
	}

	/**
	 * Installa e configura il post type Progetto.
	 *
	 * @return void
	 */
	public function setup() {
		// Register the post type.
		add_action( 'init', array( $this, 'add_post_type' ) );
		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );
	}

		function add_post_type() {
			$labels = array(
						'name'          => _x( 'Progetti', 'Post Type General Name', 'design_laboratori_italia' ),
						'singular_name' => _x( 'Progetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new'       => _x( 'Aggiungi un Progetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'add_new_item'  => _x( 'Aggiungi un nuovo Progetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
						'edit_item'     => _x( 'Modifica il Progetto', 'Post Type Singular Name', 'design_laboratori_italia' )
					);

					$args   = array(
						'label'           => __( 'Progetto', 'design_laboratori_italia' ),
						'labels'          => $labels,
						'supports'        => array( 'title', 'editor', 'thumbnail' ),
						'hierarchical'    => true,
						'public'          => true,
						'show_in_menu'    => true,
						'menu_position'   => 6,
						'menu_icon'       => 'dashicons-media-document',
						'has_archive'     => true,
						'show_in_rest'    => true,
						// 'capability_type' => array( 'notizia', 'notizie' ),
					);

					register_post_type( PROGETTO_POST_TYPE, $args );

					// Add the custom fields
					$this->add_fields();
		}

		/**
	 * Customize the layout of the admin interface.
	 *
	 * @param Object $post - The custom post.
	 * @return string
	 */
	public function custom_layout( $post ) {
		if ( PROGETTO_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Descrizione del progetto', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}

		function add_fields() {
			if( function_exists('acf_add_local_field_group') ){

				acf_add_local_field_group(array(
					'key' => 'group_63c6cba778a4e',
					'title' => 'Campi Progetto',
					'fields' => array(
						array(
							'key' => 'field_63c6cce7c647f',
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
							'key' => 'field_63c6cc2fc647b',
							'label' => 'Data inizio',
							'name' => 'data_inizio',
							'aria-label' => '',
							'type' => 'date_picker',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'display_format' => 'd/m/Y',
							'return_format' => 'd/m/Y',
							'first_day' => 1,
						),
						array(
							'key' => 'field_63c6cc62c647c',
							'label' => 'Data fine',
							'name' => 'data_fine',
							'aria-label' => '',
							'type' => 'date_picker',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'display_format' => 'd/m/Y',
							'return_format' => 'd/m/Y',
							'first_day' => 1,
						),
						array(
							'key' => 'field_63c6cba8c647a',
							'label' => 'Archiviato',
							'name' => 'archiviato',
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
							'message' => 'Non visualizzare nella lista dei progetti',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
						),
						array(
							'key' => 'field_6448ebe0d41ce',
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
							'key' => 'field_63c6cc74c647d',
							'label' => 'Promuovi in carousel',
							'name' => 'promuovi_in_carousel',
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
							'key' => 'field_63c6ce6f3e66c',
							'label' => 'Url',
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
							'placeholder' => 'Link al sito web del progetto',
						),
						array(
							'key' => 'field_63c6ceae3e66d',
							'label' => 'Responsabile del progetto',
							'name' => 'responsabile_del_progetto',
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
							'key' => 'field_63ca605161313',
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
							'key' => 'field_63ca9a376bf2e',
							'label' => 'Elenco indirizzi di ricerca correlati',
							'name' => 'elenco_indirizzi_di_ricerca_correlati',
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
								0 => 'indirizzo-di-ricerca',
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
							'key' => 'field_63c6cebf3e66e',
							'label' => 'Allegato 1',
							'name' => 'allegato1',
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
							'key' => 'field_63e650bfa74ba',
							'label' => 'Allegato 2',
							'name' => 'allegato2',
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
							'key' => 'field_63e650cba74bb',
							'label' => 'Allegato 3',
							'name' => 'allegato3',
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
							'key' => 'field_63e650d5a74bc',
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
								'value' => 'progetto',
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
