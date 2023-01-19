<?php

/**
 * Il manager che fa il setup del custom type Progetto.
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
        add_action( 'init', array($this, 'dsi_register_progetto_post_type'));
		// Register the taxonomies of this plugin.
		// add_action( 'init', array( $this, 'add_emt_taxonomies' ) );

		// Register the post type of this plugin.
		// add_action( 'init', array( $this, 'add_course_post_type' ) );

		// Register the parent file of taxonomies.
		// NOTE: this is needed to fix the highligth of the menu when a plugin taxonomy is selected.
		// add_action( 'parent_file', array( $this, 'emt_parent_file' ) );

		// Register the template of the Course post type.
		// add_filter( 'single_template', array( $this, 'course_single_template' ) );

		// Register the template for the archive of the Course post type.
		// add_filter( 'archive_template', array( $this, 'course_archive_template' ) );
	}

    function dsi_register_progetto_post_type() {
        register_post_type('progetto', array(
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'excerpt'),
            'rewrite' => array('slug' => 'progetti'),
            'public' => true,
            'labels' => array(
                'name' => 'Progetti',
                'add_new_item' => 'Aggiungi un nuovo Progetto',
                'edit_item' => 'Modifica il Progetto',
                'all_items' => 'Tutti i Progetti',
                'singular_name' => 'Progetto'
            ),
            'menu_icon' => 'dashicons-media-document'
        ));
        $this->add_fields();
    }

    function add_fields() {
        if( function_exists('acf_add_local_field_group') ) {

            acf_add_local_field_group(array(
                'key' => 'group_63c6cba778a4e',
                'title' => 'Progetto',
                'fields' => array(
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
                        'key' => 'field_63c6cc2fc647b',
                        'label' => 'Data inizio',
                        'name' => 'data_inizio',
                        'aria-label' => '',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
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
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'display_format' => 'd/m/Y',
                        'return_format' => 'd/m/Y',
                        'first_day' => 1,
                    ),
                    array(
                        'key' => 'field_63c6cc74c647d',
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
                    array(
                        'key' => 'field_63c6ccb8c647e',
                        'label' => 'Logo del progetto',
                        'name' => 'logo_del_progetto',
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
                        'key' => 'field_63c6cce7c647f',
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
                        'key' => 'field_63c6cdf13e66b',
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
                        'key' => 'field_63c6cebf3e66e',
                        'label' => 'Altri allegati',
                        'name' => 'altri_allegati',
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
