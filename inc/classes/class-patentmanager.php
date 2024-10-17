<?php
/**
 * Definition of the Patent Manager used to setup Patent post types.
 *
 * @package Design_Laboratori_Italia
 */


define( 'PATENT_SORTABLE_FIELD', 'codice_brevetto' );

class Patent_Manager {
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
		// Register the taxonomies used by this post type.
		add_action( 'init', array( $this, 'add_taxonomies' ) );
		// Register the post type.
		add_action( 'init', array( $this, 'add_post_type' ) );
		// Customize the post type layout of the admin interface.
		add_action( 'edit_form_after_title', array( $this, 'custom_layout' ) );

		// Add column to the list of items in the back-office.
		add_action( 'manage_edit-brevetto_columns', array( $this, 'add_custom_admin_columns' ) );
		add_action( 'manage_brevetto_posts_custom_column', array( $this, 'populate_custom_admin_columns' ), 10, 2 );
		add_filter( 'manage_edit-brevetto_sortable_columns', array( $this, 'sort_custom_admin_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'order_custom_admin_columns' ) );
	}


	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_taxonomies() {
		// Aggiungo la tassonomia tipo-pubblicazione.
		$publ_types_labels = array(
			'name'              => _x( 'Area tematica', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name'     => _x( 'Area tematica', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'      => __( 'Cerca area tematica', 'design_laboratori_italia' ),
			'all_items'         => __( 'Tutti i tipi', 'design_laboratori_italia' ),
			'edit_item'         => __( 'Modifica area tematica', 'design_laboratori_italia' ),
			'update_item'       => __( 'Aggiorna area tematica', 'design_laboratori_italia' ),
			'add_new_item'      => __( 'Aggiungi un\'area tematica', 'design_laboratori_italia' ),
			'new_item_name'     => __( 'Nuova area tematica', 'design_laboratori_italia' ),
			'menu_name'         => __( 'Area Tematica', 'design_laboratori_italia' ),
		);
		$taxnomy_args = array(
			'hierarchical'      => true,
			'labels'            => $publ_types_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipo-pubblicazione' ),
			'show_in_rest'      => true,
		);
		register_taxonomy( THEMATIC_AREA_TAXONOMY, array( PATENT_POST_TYPE ), $taxnomy_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'                  => _x( 'Brevetti', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name'         => _x( 'Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'               => _x( 'Aggiungi un Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'          => _x( 'Aggiungi un Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'             => _x( 'Modifica il Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'             => _x( 'Visualizza il Brevetto', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'featured_image'        => __( "Logo Identificativo dell'Brevetto", 'design_laboratori_italia' ),
			'set_featured_image'    => __( 'Seleziona Immagine Brevetto' ),
			'remove_featured_image' => __( 'Rimuovi Immagine Brevetto' , 'design_laboratori_italia' ),
			'use_featured_image'    => __( 'Usa come Immagine Brevetto' , 'design_laboratori_italia' ),
		);

		$args   = array(
			'label'           => __( 'Brevetto', 'design_laboratori_italia' ),
			'labels'          => $labels,
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'    => false,
			'public'          => true,
			'show_in_menu'    => true,
			'menu_position'   => 6,
			'menu_icon'       => 'dashicons-lightbulb',
			'has_archive'     => true,
			'show_in_rest'    => true,
			'taxonomies'      => array( WP_DEFAULT_CATEGORY, WP_DEFAULT_TAGS ),
		);

		register_post_type( PATENT_POST_TYPE, $args );

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
		if ( PATENT_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			_e( 'Abstract del brevetto', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}


	function add_custom_admin_columns( $columns ) {
		// Inserisci una nuova colonna per il meta field PATENT_SORTABLE_FIELD.
		$columns[PATENT_SORTABLE_FIELD] = __( 'Codice', 'design_laboratori_italia' );
		return $columns;
	}

	function populate_custom_admin_columns( $column, $post_id ) {
		// Popolare la colonna personalizzata con i dati del meta field.
		if ( $column == PATENT_SORTABLE_FIELD ) {
				// Ottieni il valore del meta field PATENT_SORTABLE_FIELD
				$value = get_post_meta( $post_id, PATENT_SORTABLE_FIELD, true );
				echo $value ? esc_html( $value ) : __( 'N/D', 'design_laboratori_italia' );
		}
	}


	function sort_custom_admin_columns( $columns ) {
		// Rendere la colonna PATENT_SORTABLE_FIELD ordinabile.
		$columns[PATENT_SORTABLE_FIELD] = PATENT_SORTABLE_FIELD;
		return $columns;
	}

	function order_custom_admin_columns( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->get('orderby') == PATENT_SORTABLE_FIELD ) {
			// Ordina per meta field PATENT_SORTABLE_FIELD.
			$query->set('meta_key', PATENT_SORTABLE_FIELD);
			$query->set('orderby', 'meta_value');
		}
	}


	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	function add_fields() {

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
	
		acf_add_local_field_group( array(
		'key' => 'group_66fbbba06e4fb',
		'title' => 'Campi Brevetto',
		'fields' => array(
			array(
				'key' => 'field_6707df1a481eb',
				'label' => 'Sommario elenco',
				'name' => 'sommario_elenco',
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
				'allow_in_bindings' => 1,
				'rows' => '',
				'placeholder' => '',
				'new_lines' => '',
			),
			array(
				'key' => 'field_670502099b7a3',
				'label' => 'Note',
				'name' => 'note',
				'aria-label' => '',
				'type' => 'wysiwyg',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'allow_in_bindings' => 1,
				'tabs' => 'all',
				'toolbar' => 'full',
				'media_upload' => 1,
				'delay' => 0,
			),
			array(
				'key' => 'field_66fbbf1fa1cfa',
				'label' => 'Stato legale',
				'name' => 'stato_legale',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'Abbandonato' => 'Abbandonato',
					'Ceduto' => 'Ceduto',
					'Concesso' => 'Concesso',
					'Licenza' => 'Licenza',
					'Pending' => 'Pending',
					'Terminato' => 'Terminato',
				),
				'default_value' => 'Pending',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'allow_in_bindings' => 1,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_66fbbef9af705',
				'label' => 'Codice brevetto',
				'name' => 'codice_brevetto',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_66fbbba091e1a',
				'label' => 'Data deposito',
				'name' => 'data_deposito',
				'aria-label' => '',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'd/m/Y',
				'return_format' => 'd/m/Y',
				'first_day' => 1,
				'allow_in_bindings' => 0,
			),
			array(
				'key' => 'field_66fbbf55d8169',
				'label' => 'Anno deposito',
				'name' => 'anno_deposito',
				'aria-label' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'min' => '',
				'max' => '',
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'step' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_66fbbc1beb100',
				'label' => 'Numero deposito',
				'name' => 'numero_deposito',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_6704ef0dffeca',
				'label' => 'Inventori referenti',
				'name' => 'inventori_referenti',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_6704eefcffec9',
				'label' => 'Inventori',
				'name' => 'inventori',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_6704eeeaffec8',
				'label' => 'Titolari',
				'name' => 'titolari',
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
				'allow_in_bindings' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_6707e403dbf67',
				'label' => 'Prioritario',
				'name' => 'prioritario',
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
				'default_value' => 1,
				'allow_in_bindings' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_6710d8a365f4a',
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
				'allow_in_bindings' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_6710d8e165f4b',
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
				'allow_in_bindings' => 0,
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
					'value' => 'brevetto',
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
