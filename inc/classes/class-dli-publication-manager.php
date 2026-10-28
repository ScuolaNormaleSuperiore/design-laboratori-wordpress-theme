<?php
/**
 * Creazione del post type Pubblicazioni.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that setups Course post types.
 */
class DLI_Publication_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Install and configure the Publication post type.
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
		// Write the rewrite rules of the detail address once (after the post type is registered).
		add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 20 );
	}

	/**
	 * Version of the rewrite rules of the publications.
	 *
	 * Raise it when the address of a publication changes: the rules are written again once, at the
	 * next request, on every site that updates the theme (no manual permalink save is needed).
	 */
	const REWRITE_RULES_VERSION = '1';

	/**
	 * Write the rewrite rules again, once per version of the rules.
	 *
	 * WordPress keeps the rules in the database and rewrites them only when asked: without this, a
	 * site that updates the theme answers 404 on the address of every publication until someone
	 * saves the permalinks. Same pattern as `dli_pwa_maybe_flush_rewrite_rules()`.
	 *
	 * @return void
	 */
	public function maybe_flush_rewrite_rules() {
		if ( get_option( 'dli_publication_rules_version' ) === self::REWRITE_RULES_VERSION ) {
			return;
		}
		flush_rewrite_rules();
		update_option( 'dli_publication_rules_version', self::REWRITE_RULES_VERSION );
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function add_taxonomies() {
		// Aggiungo la tassonomia tipo-pubblicazione.

		$types_labels = array(
			'name'          => _x( 'Tipo pubblicazione', 'taxonomy general name', 'design_laboratori_italia' ),
			'singular_name' => _x( 'Tipo pubblicazione', 'taxonomy singular name', 'design_laboratori_italia' ),
			'search_items'  => __( 'Cerca tipo pubblicazione', 'design_laboratori_italia' ),
			'all_items'     => __( 'Tutti i tipi', 'design_laboratori_italia' ),
			'edit_item'     => __( 'Modifica il tipo pubblicazione', 'design_laboratori_italia' ),
			'update_item'   => __( 'Aggiorna il tipo pubblicazione', 'design_laboratori_italia' ),
			'add_new_item'  => __( 'Aggiungi un tipo pubblicazione', 'design_laboratori_italia' ),
			'new_item_name' => __( 'Nuovo tipo pubblicazione', 'design_laboratori_italia' ),
			'menu_name'     => __( 'Tipo Pubblicazione', 'design_laboratori_italia' ),
		);

		$publ_types_args = array(
			'hierarchical'      => true,
			'labels'            => $types_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'tipo-pubblicazione' ),
			'show_in_rest'      => true,
		);

		register_taxonomy( PUBLICATION_TYPE_TAXONOMY, array( PUBLICATION_POST_TYPE ), $publ_types_args );
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function add_post_type() {

		$labels = array(
			'name'          => _x( 'Pubblicazioni', 'Post Type General Name', 'design_laboratori_italia' ),
			'singular_name' => _x( 'Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new'       => _x( 'Aggiungi Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'add_new_item'  => _x( 'Aggiungi la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'edit_item'     => _x( 'Modifica la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
			'view_item'     => _x( 'Visualizza la Pubblicazione', 'Post Type Singular Name', 'design_laboratori_italia' ),
		);

		$args = array(
			'label'               => __( 'Pubblicazione', 'design_laboratori_italia' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'        => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'rewrite'             => array( 'slug' => PUBLICATION_POST_TYPE ),
			'query_var'           => false,
			'show_ui'             => true,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'menu_position'       => 6,
			'menu_icon'           => 'dashicons-book',
			'has_archive'         => false,
			'show_in_rest'        => true,
			'taxonomies'          => array( WP_DEFAULT_TAGS ),
		);

		register_post_type( PUBLICATION_POST_TYPE, $args );

		// Add the custom fields.
		$this->add_fields();
	}

	/**
	 * Customize the layout of the admin interface.
	 *
	 * @param Object $post - The custom post.
	 * @return void
	 */
	public function custom_layout( $post ) {
		if ( PUBLICATION_POST_TYPE === $post->post_type ) {
			echo '<h1>';
			esc_html_e( 'Descrizione della pubblicazione', 'design_laboratori_italia' );
			echo '</h1>';
		}
	}


	/**
	 * Add the custom fields of the custom post-type.
	 *
	 * @return void
	 */
	public function add_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group(
				array(
					'key'                   => 'group_63ca5c47b914d',
					'title'                 => 'Campi Pubblicazione',
					'fields'                => array(
						array(
							'key'               => 'field_63ca5c496dfcd',
							'label'             => 'Anno',
							'name'              => 'anno',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => '',
						),
						array(
							'key'               => 'field_63ca5c776dfce',
							'label'             => 'Autori',
							'name'              => 'autori',
							'aria-label'        => '',
							'type'              => 'relationship',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type'         => array(
								0 => 'persona',
							),
							'taxonomy'          => '',
							'filters'           => array(
								0 => 'search',
							),
							'return_format'     => 'object',
							'min'               => '',
							'max'               => '',
							'elements'          => '',
						),
						array(
							'key'               => 'field_63e652c02c893',
							'label'             => 'Url',
							'name'              => 'url',
							'aria-label'        => '',
							'type'              => 'url',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
						),
						array(
							'key'               => 'field_6a3c9e0b1d4f3',
							'label'             => 'Allegato',
							'name'              => 'allegato',
							'aria-label'        => '',
							'type'              => 'file',
							'instructions'      => 'File PDF della pubblicazione. Il pulsante della scheda lo offre in download; se la scelta qui sotto è «Link ad allegato» il titolo nelle liste porta al file.',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'return_format'     => 'array',
							'library'           => 'all',
							'min_size'          => '',
							'max_size'          => '',
							'mime_types'        => 'pdf',
						),
						array(
							'key'               => 'field_6a3c9e0b1d4f2',
							'label'             => 'Link dettaglio',
							'name'              => 'link_dettaglio',
							'aria-label'        => '',
							'type'              => 'select',
							'instructions'      => 'Dove porta il titolo della pubblicazione negli elenchi, nella ricerca e nel carousel. «Sito web» usa il campo Url; «Link ad allegato» usa il campo Allegato. La scheda resta comunque raggiungibile dal suo indirizzo.',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'choices'           => array(
								DLI_ITEM_LINK['DETAIL_PAGE'] => 'Scheda dettaglio',
								DLI_ITEM_LINK['WEBSITE'] => 'Sito web',
								DLI_ITEM_LINK['ATTACHMENT'] => 'Link ad allegato',
							),
							'default_value'     => DLI_ITEM_LINK['DETAIL_PAGE'],
							'return_format'     => 'value',
							'multiple'          => 0,
							'allow_null'        => 0,
							'allow_in_bindings' => 0,
							'ui'                => 0,
							'ajax'              => 0,
							'placeholder'       => '',
							'create_options'    => 0,
							'save_options'      => 0,
						),
						array(
							'key'               => 'field_63e6d1ff4bf3c',
							'label'             => 'Promuovi in carousel',
							'name'              => 'promuovi_in_carousel',
							'aria-label'        => '',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_63e652d32c894',
							'label'             => 'Promuovi in Home',
							'name'              => 'promuovi_in_home',
							'aria-label'        => '',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'pubblicazione',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => true,
					'description'           => '',
					'show_in_rest'          => 1,
				)
			);
		}
	}
}
