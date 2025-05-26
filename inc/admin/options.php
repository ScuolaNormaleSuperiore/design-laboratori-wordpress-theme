<?php
/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function dli_register_main_options_metabox() {
	$prefix = '';

	$args = array(
		'id'           => 'dli_options_header',
		'title'        => esc_html__( 'Configurazione', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Opzioni di base', 'design_laboratori_italia' ),
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'position'     => 2, // Menu position. Only applicable if 'parent_slug' is left empty.
		'icon_url'     => 'dashicons-admin-tools', // Menu icon. Only applicable if 'parent_slug' is left empty.
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$header_options = new_cmb2_box( $args );

	/**
	 * Registers options page "Opzioni di base".
	 */
	$header_options->add_field(
		array(
			'id'   => $prefix . 'home_istruzioni',
			'name' => __( 'Configurazione Laboratorio', 'design_laboratori_italia' ),
			'desc' => __( 'Area di configurazione delle informazioni di base' , 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'tipologia_laboratorio',
			'name'       => __( 'Tipologia', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( 'La Tipologia del Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'nome_laboratorio',
			'name'       => __( 'Nome Laboratorio', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( 'Il Nome del Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'nome_laboratorio' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Nome Laboratorio EN', 'design_laboratori_italia' ),
			'desc'       => __( 'Il Nome del Laboratorio in inglese, se diverso da quello italiano' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'tagline_laboratorio',
			'name'       => __( 'Tagline', 'design_laboratori_italia' ) . '&nbsp;',
			'desc'       => __( 'La tagline del Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'tagline_laboratorio'  . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Tagline EN', 'design_laboratori_italia' ),
			'desc'       => __( 'La tagline del Laboratorio in inglese, se diversa da quella italiana' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'label_contact_footer',
			'name'       => __( 'Etichetta contatto', 'design_laboratori_italia' ),
			'desc'       => __( 'Etichetta da mostrare nei contatti del footer, se non specificato viene usato il contenuto del campo Nome laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'luogo_laboratorio',
			'name'       => __( 'Città', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( 'La città dove risiede il Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'indirizzo_laboratorio',
			'name'       => __( 'Indirizzo', 'design_laboratori_italia' )  . '&nbsp;*',
			'desc'       => __( "L'indirizzo del Laboratorio" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'email_laboratorio',
			'name'       => __( 'Email', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( "L'email del Laboratorio" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'pec_laboratorio',
			'name'       => __( 'PEC', 'design_laboratori_italia' ),
			'desc'       => __( "La PEC del Laboratorio" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'telefono_laboratorio',
			'name'       => __( 'Telefono', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( "Il numero di telefono del Laboratorio" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'nome_ente_appartenza',
			'name'       => __( 'Ente padre', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( "Il nome dell'ente padre" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'url_ente_appartenenza',
			'name'       => __( 'Url ente padre', 'design_laboratori_italia' ) . '&nbsp;*',
			'desc'       => __( "L'url dell'ente padre" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'      => $prefix . 'logo_header_visible',
			'name'    => __( 'Visualizza il logo nello header', 'design_laboratori_italia' ),
			'desc'    => __( 'Indicare se il logo nello header deve essere visualizzato', 'design_laboratori_italia' ) . '.',
			'type'    => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'logo_laboratorio',
			'name'       => __( 'Logo header', 'design_laboratori_italia' ),
			'desc'       => __( 'Il logo del laboratorio. Si raccomanda di caricare un\'immagine in formato svg' , 'design_laboratori_italia' ),
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image',
				),
			),
		)
	);

	$header_options->add_field(
		array(
			'id'      => $prefix . 'logo_footer_visible',
			'name'    => __( 'Visualizza il logo nel footer', 'design_laboratori_italia' ),
			'desc'    => __( 'Indicare se il logo nel footer deve essere visualizzato', 'design_laboratori_italia' ) . '.',
			'type'    => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'logo_laboratorio_footer',
			'name'       => __( 'Logo footer', 'design_laboratori_italia' ),
			'desc'       => __( 'Il logo mostrato nel footer. Se non è presente, ma è abilitata la visualizzazione del logo nel footer, viene mostrato quello dello header con colori invertiti. Si raccomanda di caricare un\'immagine in formato svg' , 'design_laboratori_italia' ),
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image',
				),
			),
		)
	);

	$header_options->add_field(
		array(
			'id'           => $prefix . 'logo_laboratorio_mobile',
			'name'         => __( 'Logo per mobile', 'design_laboratori_italia' ),
			'desc'         => __( 'Utilizzare questo campo per caricare un\'immagine alternativa del logo del laboratorio visibile dal menu hamburger (mobile). Si raccomanda di caricare un\'immagine in formato svg' , 'design_laboratori_italia' ),
			'type'         => 'file',
			'query_args'   => array(
				'type' => array(
					'image',
				),
			),
		)
	);

	/**
	 * 1 - Registers options page "Avvisi in Home".
	 */

	$args = array(
		'id'           => 'dli_options_messages',
		'title'        => esc_html__( 'Messaggi', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'home_messages',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Avvisi in Home', 'design_laboratori_italia' ),
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
			$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$alerts_options = new_cmb2_box( $args );

	$alerts_options->add_field( array(
			'id'   => $prefix . 'messages_istruzioni',
			'name' => __( 'Avvisi di allerta in Home Page', 'design_laboratori_italia' ),
			'desc' => __( 'Inserisci messaggi che saranno visualizzati nella homepage' , 'design_laboratori_italia' )  . '.',
			'type' => 'title',
	) );

	$alerts_group_id = $alerts_options->add_field( array(
			'id'          => $prefix . 'messages',
			'type'        => 'group',
			'desc'        => __( 'Ogni messaggio è costruito attraverso descrizione breve (max 140 caratteri) e data di scadenza (opzionale)' , 'design_laboratori_italia' )   . '.',
			'repeatable'  => true,
			'options'     => array(
					'group_title'   => __( 'Messaggio', 'design_laboratori_italia' ) . '&nbsp{#}',
					'add_button'    => __( 'Aggiungi un messaggio', 'design_laboratori_italia' ),
					'remove_button' => __( 'Rimuovi il messaggio', 'design_laboratori_italia' ),
					'sortable'      => true,  // Allow changing the order of repeated groups.
			),
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'name'    => 'Selezione colore del messaggio',
			'id'      => 'colore_message',
			'type'    => 'radio_inline',
			'options' => array(
					'danger'  => '<span class="radio-color red"></span>' . __( 'Danger', 'design_laboratori_italia' ),
					'success' => '<span class="radio-color green"></span>' . __( 'Success', 'design_laboratori_italia' ),
					'warning' => '<span class="radio-color brown"></span>' . __( 'Warning', 'design_laboratori_italia' ),
					'info'    => '<span class="radio-color gray"></span>' . __( 'Info', 'design_laboratori_italia' ),
			),
			'default' => 'info',
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'id'         => $prefix . 'testo_message',
			'name'       => __( 'Testo', 'design_laboratori_italia' ),
			'desc'       => __( 'Massimo 140 caratteri' , 'design_laboratori_italia' ),
			'type'       => 'textarea_small',
			'attributes' => array(
					'rows'      => 3,
					'maxlength' => '140',
			),
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'id'   => $prefix . 'link_message',
			'name' => __( 'Collegamento', 'design_laboratori_italia' ),
			'desc' => __( 'Link a una pagina di approfondimento anche esterna al sito' , 'design_laboratori_italia' ),
			'type' => 'text_url',
	) );


		/**
		 * 2 - Registers options page "Home".
		 */

		$args = array(
				'id'           => 'dli_options_home',
				'title'        => esc_html__( 'Home Page', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'homepage',
				'capability'   => DLI_EDIT_CONFIG_PERMISSION,
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'tab_title'    => __( 'Home', 'design_laboratori_italia' ),	);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$home_options = new_cmb2_box( $args );

		/**
		* 3 - Registers options page "Home".
		*/
	// *** SEZIONE MAIN HERO (HOMEPAGE) ***
	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero',
			'name' => __( 'Sezione hero principale', 'design_laboratori_italia' ),
			'desc' => __( 'Gestione sezione Hero principale (opzionale) mostrato in Home Page. Per visualizzare un contenuto in questa sezione spuntare il campo "Promuovi in home"' , 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$home_options->add_field(
		array(
			'id'      => $prefix . 'home_main_hero_enabled',
			'name'    => __( 'Hero principale attivo', 'design_laboratori_italia' ),
			'desc'    => __( 'Attiva l\'hero principale in Home Page', 'design_laboratori_italia' ),
			'type'    => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id'               => $prefix . 'home_main_hero_size',
			'name'             => __( 'Dimensione hero', 'design_laboratori_italia' ),
			'desc'             => __( 'Scegli il formato dello header in home page' , 'design_laboratori_italia' ),
			'type'             => 'select',
			'default'          => 'Grande',
			'show_option_none' => false,
			'options'          => array(
				'big'   => __( 'Grande', 'design_laboratori_italia' ),
				'small' => __( 'Piccolo', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero_title',
			'name' => __( 'Titolo hero', 'design_laboratori_italia' ),
			'type' => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero_title' . DLI_ENG_SUFFIX_LANGUAGE,
			'name' => __( 'Titolo hero ENG', 'design_laboratori_italia' ),
			'type' => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_text',
			'name'       => __( 'Testo hero', 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140',
			),
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_text'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Testo hero ENG', 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140',
			),
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_button_label',
			'name'       => __( 'Label bottone', 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero_button_label'. DLI_ENG_SUFFIX_LANGUAGE,
			'name' => __( 'Label bottone ENG', 'design_laboratori_italia' ),
			'type' => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero_url',
			'name' => __( 'Url', 'design_laboratori_italia' ),
			'type' => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_image',
			'name'       => __( 'Immagine di sfondo', 'design_laboratori_italia' ),
			'desc'       => __( 'Immagine in formato png o jpg' , 'design_laboratori_italia' ),
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image/png',
					'image/jpg',
					'image/jpeg',
				),
			),
		)
	);

	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_istruzioni_x',
			'name' => __( 'Sezione Descrizione del sito', 'design_laboratori_italia' ),
			'desc' => __( 'Gestione della descrizione del sito in Home Page' , 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$home_options->add_field(
		array(
			'id'      => $prefix . 'site_description_is_visible',
			'name'    => __( 'Visualizza la descrizione del sito', 'design_laboratori_italia' ),
			'desc'    => __( 'Indicare se la descrizione del sito in Home Page deve essere visualizzata', 'design_laboratori_italia' ) . '.',
			'type'    => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);


	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_istruzioni_1',
			'name' => __( 'Sezione Carousel', 'design_laboratori_italia' ),
			'desc' => __( 'Gestione del carousel in Home Page' , 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$home_options->add_field(
		array(
			'id'      => $prefix . 'home_carousel_is_visible',
			'name'    => __( 'Visualizza carousel', 'design_laboratori_italia' ),
			'desc'    => __( 'Indicare se il carousel in Home Page deve essere visualizzato', 'design_laboratori_italia' ) . '.',
			'type'    => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_carousel_after_presentation_enabled',
			'name' => __( 'Mostra carousel dopo presentazione', 'design_laboratori_italia' ) . '.',
			'desc' => __( 'Il carousel dopo la presentazione', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_carousel_is_selezione_automatica',
			'name' => __( 'Selezione Automatica', 'design_laboratori_italia' ),
			'desc' => __( 'Seleziona <b>Si</b> per mostrare automaticamente gli articoli per i quali è stato settato il flag "Promuovi in carousel". <b>No</b> per sceglierli manualmente nella sezione seguente', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'name'    => __( 'Selezione articoli', 'design_laboratori_italia' ),
			'desc'    => __( 'Seleziona gli articoli da mostrare nel carousel della Home Page, se disabilitata la selezione automatica', 'design_laboratori_italia' ),
			'id'      => $prefix . 'articoli_presentazione',
			'type'    => 'custom_attached_posts',
			'column'  => true,
			'options' => array(
					'show_thumbnails' => false, // Show thumbnails on the left.
					'filter_boxes'    => true, // Show a text box for filtering the results.
					'query_args'      => array(
							'posts_per_page' => -1,
							'post_type'      => array('post', 'notizia', 'evento', 'pubblicazione' ),
					), // override the get_posts args.
				)
			)
	);

	// *** SEZIONE CONTENUTI IN EVIDENZA (HOMEPAGE) ***
		$home_options->add_field(
			array(
				'id' => $prefix . 'home_featured_contents',
				'name'        => __( 'Sezione contenuti in evidenza', 'design_laboratori_italia' ),
				'desc' => __( 'Gestione della sezione dei contenuti in evidenza in Home Page' , 'design_laboratori_italia' ) . '.',
				'type' => 'title',
			)
		);

		$home_options->add_field(
			array(
				'id' => $prefix . 'home_featuredcontents_is_visible',
				'name' => __( 'Visualizza contenuti in evidenza', 'design_laboratori_italia' ),
				'desc' => __( 'Indicare se la sezione dei contenuti in evidenza deve essere visualizzata', 'design_laboratori_italia' ) . '.',
				'type' => 'radio_inline',
				'default' => 'true',
				'options' => array(
						'true' => __( 'Si', 'design_laboratori_italia' ),
						'false' => __( 'No', 'design_laboratori_italia' ),
				),
			)
		);

		// Definizione del BOX 1.
		$featured_contents_group_id = $home_options->add_field(
			array(
				'id'          => $prefix . 'featured_contents_1',
				'type'       => 'group',
				'repeatable' => false,
				'options'    => array(
					'group_title' => __( 'Box 1 dei contenuti in evidenza', 'design_laboratori_italia' ),
					'closed'      => true
					,
				)
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'   => $prefix . 'featured_contents_label_box_1',
				'name' => __( 'Label bottone', 'design_laboratori_italia' ),
				'type' => 'text',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_type_box_1',
				'name'             => __( 'Contenuto del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona un tipo di contenuto' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => NEWS_POST_TYPE,
				'options_cb'       => 'dli_get_boxes_post_types',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_template_box_1',
				'name'             => __( 'Template del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona uno di questi template' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'card-news',
				'show_option_none' => false,
				'options'          => array(
					'card-news'             => __( 'News singola', 'design_laboratori_italia' ),
					'card-pubblicazioni'    => __( '2 Pubblicazioni', 'design_laboratori_italia' ),
					'card-eventi'           => __( 'Evento singolo', 'design_laboratori_italia' ),
				),
			)
		);


		// Definizione del BOX 2.
		$featured_contents_group_id = $home_options->add_field(
			array(
				'id'         => $prefix . 'featured_contents_2',
				'type'       => 'group',
				'repeatable' => false,
				'options'    => array(
					'group_title' => __( 'Box 2 dei contenuti in evidenza', 'design_laboratori_italia' ),
					'closed'      => true
					,
				)
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'         => $prefix . 'featured_contents_label_box_2',
				'name'       => __( 'Label bottone', 'design_laboratori_italia' ),
				'type'       => 'text',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_type_box_2',
				'name'             => __( 'Contenuto del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona un tipo di contenuto' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => EVENT_POST_TYPE,
				'options_cb'       => 'dli_get_boxes_post_types',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_template_box_2',
				'name'             => __( 'Template del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona uno di questi template' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'card-eventi',
				'show_option_none' => false,
				'options'          => array(
					'card-news'             => __( 'News singola', 'design_laboratori_italia' ),
					'card-pubblicazioni'    => __( '2 Pubblicazioni', 'design_laboratori_italia' ),
					'card-eventi'           => __( 'Evento singolo', 'design_laboratori_italia' ),
				),
			)
		);


		// Definizione del BOX 3.
		$featured_contents_group_id = $home_options->add_field(
			array(
				'id'         => $prefix . 'featured_contents_3',
				'type'       => 'group',
				'repeatable' => false,
				'options'    => array(
					'group_title' => __( 'Box 3 dei contenuti in evidenza', 'design_laboratori_italia' ),
					'closed'      => true
					,
				)
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'   => $prefix . 'featured_contents_label_box_3',
				'name' => __( 'Label bottone', 'design_laboratori_italia' ),
				'type' => 'text',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_type_box_3',
				'name'             => __( 'Contenuto del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona un tipo di contenuto' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => PUBLICATION_POST_TYPE,
				'options_cb'       => 'dli_get_boxes_post_types',
			)
		);

		$home_options->add_group_field(
			$featured_contents_group_id,
			array(
				'id'               => $prefix . 'featured_contents_template_box_3',
				'name'             => __( 'Template del box', 'design_laboratori_italia' ),
				'desc'             => __( 'Seleziona uno di questi template' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'card-pubblicazioni',
				'show_option_none' => false,
				'options'          => array(
					'card-news'             => __( 'News singola', 'design_laboratori_italia' ),
					'card-pubblicazioni'    => __( '2 Pubblicazioni', 'design_laboratori_italia' ),
					'card-eventi'           => __( 'Evento singolo', 'design_laboratori_italia' ),
				),
			)
		);

	// *** SEZIONE ELENCO CONTENUTI (HOMEPAGE) ***
	$home_options->add_field(
		array(
			'id' => $prefix . 'home_content_list',
			'name'        => __( 'Sezione elenco contenuti', 'design_laboratori_italia' ),
			'desc' => __( 'Gestione della sezione delle righe di contenuti in Home Page. Per visualizzare un contenuto in questa sezione spuntare il campo "Promuovi in home"' , 'design_laboratori_italia' ) . '.',
			'type' => 'title',
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_event_list_is_visible',
			'name' => __( 'Visualizza l\'elenco degli eventi', 'design_laboratori_italia' ),
			'desc' => __( 'Indicare se l\'elenco degli eventi deve essere visualizzato in  HP', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_news_list_is_visible',
			'name' => __( 'Visualizza l\'elenco delle notizie', 'design_laboratori_italia' ),
			'desc' => __( 'Indicare se l\'elenco delle notizie deve essere visualizzato in  HP', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_publication_list_is_visible',
			'name' => __( 'Visualizza l\'elenco delle pubblicazioni', 'design_laboratori_italia' ),
			'desc' => __( 'Indicare se l\'elenco delle pubblicazioni deve essere visualizzato in  HP', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_article_list_is_visible',
			'name' => __( 'Visualizza l\'elenco degli articoli', 'design_laboratori_italia' ),
			'desc' => __( 'Indicare se l\'elenco degli articoli deve essere visualizzato in  HP', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_banner_section_is_visible',
			'name' => __( 'Visualizza la sezione dei banner', 'design_laboratori_italia' ),
			'desc' => __( 'Indicare se la sezione dei banner deve essere visualizzata in  HP', 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_sponsor_list_is_visible',
			'name' => __( "Visualizza l'elenco degli sponsor", 'design_laboratori_italia' ),
			'desc' => __( "Indicare se l'elenco degli sponsor deve essere visualizzato in  HP", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);


	/**
	* 4 - Registers options page "Laboratorio".
	*/
	$args = array(
		'id'           => 'dli_options_il_laboratorio',
		'title'        => esc_html__( 'Il Laboratorio', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'option_key'   => 'il_laboratorio',
		'tab_title'    => __( 'Laboratorio', 'design_laboratori_italia' ),
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$main_options = new_cmb2_box( $args );

	$lab_landing_url = dli_get_template_page_url("page-templates/il-laboratorio.php");
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica del Laboratorio</a>', 'design_laboratori_italia' ), $lab_landing_url );

	$main_options->add_field(
		array(
			'id'   => $prefix . 'laboratorio_istruzioni',
			'name' => __( 'Sezione Il Laboratorio', 'design_laboratori_italia' ),
			'desc' => $descr,
			'type' => 'title',
			'type' => 'title',
		)
	);

	$main_options->add_field(
		array(
			'id'         => $prefix . 'etichetta',
			'name'       => __( 'Etichetta', 'design_laboratori_italia' ),
			'desc'       => __( 'Titolo della sezione' , 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$main_options->add_field(
		array(
			'id'         => $prefix . 'etichetta'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Etichetta ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'Titolo della sezione in inglese' , 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$main_options->add_field(
		array(
			'id'    => $prefix . 'descrizione_laboratorio',
			'title' => __( 'Descrizione', 'design_laboratori_italia' ),
			'name'  => __( 'Descrizione', 'design_laboratori_italia' ),
			'desc'  => __( 'Descrizione del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => true,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br,img[src|alt|class|width|height]',
				),
			),
		)
	);

	$main_options->add_field(
		array(
			'id'    => $prefix . 'descrizione_laboratorio'. DLI_ENG_SUFFIX_LANGUAGE,
			'title' => __( 'Descrizione ENG', 'design_laboratori_italia' ),
			'name'  => __( 'Descrizione ENG', 'design_laboratori_italia' ),
			'desc'  => __( 'Descrizione del laboratorio in inglese' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => true,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br,img[src|alt|class|width|height]',
				),
			),
		)
	);

	/**
	* 5 - Registers options page "Blog".
	*/
	$args = array(
		'id'           => 'dli_options_blog',
		'title'        => esc_html__( 'Il blog', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'blog',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Blog', 'design_laboratori_italia' ),
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$blog_options = new_cmb2_box( $args );

		$blog_landing_url = dli_get_template_page_url( 'page-templates/blog.php' );
		$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> il blog</a>', 'design_laboratori_italia' ), $blog_landing_url );
		$blog_options->add_field(
			array(
				'id'   => $prefix . 'blog_istruzioni',
				'name' => __( 'Sezione Il Blog', 'design_laboratori_italia' ),
				'desc' => $descr,
				'type' => 'title',
			)
		);

		$blog_options->add_field(
			array(
				'id'         => $prefix . 'testo_blog',
				'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "Gli articoli del laboratorio"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br', 
					),
				),
			)
		);

		$blog_options->add_field(
			array(
				'id'         => $prefix . 'testo_blog'. DLI_ENG_SUFFIX_LANGUAGE,
				'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "The papers from the Lab"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br', 
					),
				),
			)
		);

	/**
	* 6 - Registers options page "Novità".
	*/
	$args = array(
		'id'           => 'dli_options_notizie',
		'title'        => esc_html__( 'Le Novità', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'notizie',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Novità', 'design_laboratori_italia' ),
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$notizie_options = new_cmb2_box( $args );

		$notizie_landing_url = dli_get_template_page_url( 'page-templates/notizie.php' );
		$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica delle Novità</a>', 'design_laboratori_italia' ), $notizie_landing_url );

		$notizie_options->add_field(
			array(
				'id'   => $prefix . 'notizie_istruzioni',
				'name' => __( 'Sezione Le Novità', 'design_laboratori_italia' ),
				'desc' => $descr,
				'type' => 'title',
			)
		);

		$notizie_options->add_field(
			array(
				'id'         => $prefix . 'testo_notizie',
				'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "Le notizie del laboratorio"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br',
					),
				),
			)
		);

		$notizie_options->add_field(
			array(
				'id'         => $prefix . 'testo_notizie'. DLI_ENG_SUFFIX_LANGUAGE,
				'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "News from the Lab"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br',
					),
				),
			)
		);

		$notizie_options->add_field(
			array(
				'id'              => $prefix . 'numero_pagine_collegate',
				'name'            => __( 'Numero pagine collegate', 'design_laboratori_italia' ),
				'desc'            => __( 'Numero massimo di pagine (notizie ed eventi) associabili a una pagina' , 'design_laboratori_italia' ),
				'type'            => 'text_small',
				'default'         => 5,
				'attributes'      => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
				'sanitization_cb' => 'absint',
				// 'escape_cb'       => 'absint', // Commentato per far funzionare default. Da togliere?
			)
		);


	/**
	* 7- Registers options page "Eventi".
	*/
	$args = array(
		'id'           => 'dli_options_eventi',
		'title'        => esc_html__( 'Gli eventi', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'eventi',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Eventi', 'design_laboratori_italia' ),
	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$eventi_options = new_cmb2_box( $args );

		$eventi_landing_url = dli_get_template_page_url( 'page-templates/eventi.php' );
		$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica degli Eventi</a>', 'design_laboratori_italia' ), $eventi_landing_url );
		$eventi_options->add_field(
			array(
				'id'   => $prefix . 'eventi_istruzioni',
				'name' => __( 'Sezione Eventi', 'design_laboratori_italia' ),
				'desc' => $descr,
				'type' => 'title',
			)
		);

		$eventi_options->add_field(
			array(
				'id'         => $prefix . 'testo_eventi',
				'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "Gli eventi del laboratorio"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br',
					),
				),
			)
		);

		$eventi_options->add_field(
			array(
				'id'         => $prefix . 'testo_eventi'. DLI_ENG_SUFFIX_LANGUAGE,
				'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
				'desc'       => __( 'es: "Events from the Lab"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br',
					),
				),
			)
		);
	
	/**
	* 8 - Registers options page "Persone".
	*/
	$args = array(
		'id'           => 'dli_options_persone',
		'title'        => esc_html__( 'Persone', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'persone',
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'capability'    => DLI_EDIT_CONFIG_PERMISSION,
		'tab_title'    => __( 'Persone', 'design_laboratori_italia' ),	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
			$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$persone_options = new_cmb2_box( $args );

	$persone_landing_url = dli_get_template_page_url("page-templates/persone.php");
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica delle Persone</a>', 'design_laboratori_italia' ), $persone_landing_url );
	$persone_options->add_field( array(
			'id' => $prefix . 'persone_istruzioni',
			'name'        => __( 'Sezione Persone', 'design_laboratori_italia' ),
			'desc' => $descr,
			'type' => 'title',
	) );


	$persone_options->add_field(
		array(
			'id' => $prefix . 'testo_sezione_persone',
			'name'        => __( 'Descrizione Sezione Persone', 'design_laboratori_italia' ),
			'desc' => __( 'es: "Le persone del laboratorio"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	$persone_options->add_field(
		array(
			'id' => $prefix . 'testo_sezione_persone'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'        => __( 'Descrizione Sezione Persone ENG', 'design_laboratori_italia' ),
			'desc' => __( 'es: "People from the Lab"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		),
	);

	$persone_options->add_field(
		array(
			'id' => $prefix . 'hide_person_icon',
			'name' => __( "Nascondi icona", 'design_laboratori_italia' ),
			'desc' => __( "Nascondi l'icona della persona nell'elenco delle persone", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);


	$persone_options->add_field(
		array(
			'id'               => $prefix . 'pagination_mode',
			'name'             => __( 'Modalità scelta struttura', 'design_laboratori_italia' ),
			'desc'             => __( 'Scegli se filtrare le strutture con dei chip o una select oppure non mostrare alcun filtro' , 'design_laboratori_italia' ),
			'type'             => 'select',
			'default'          => 'chip',
			'show_option_none' => false,
			'options'          => array(
				'chip'          => __( 'Mostra chip', 'design_laboratori_italia' ),
				'combobox'      => __( 'Mostra select', 'design_laboratori_italia' ),
				'disabled'      => __( 'Non mostrare filtro', 'design_laboratori_italia' ),
			),
		)
	);

	$persone_options->add_field(
		array(
			'id'      => $prefix . 'level_filter_enabled',
			'name'    => __( 'Filtra per TAG', 'design_laboratori_italia' ),
			'desc'    => __( 'Attiva il filtro per TAG', 'design_laboratori_italia' ),
			'type'    => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$persone_options->add_field(
		array(
			'id'         => $prefix . 'seleziona_livello_persone',
			'name'       => __( "Etichetta 'Seleziona TAG' ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Seleziona TAG'. E' usata per filtrare i contenuti per tag." , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Seleziona TAG',
			'attributes' => array(),
		)
	);

	$persone_options->add_field(
		array(
			'id'         => $prefix . 'seleziona_livello_persone' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( "Etichetta 'Seleziona TAG' ENG ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Seleziona TAG' in inglese.  E' usata per filtrare i contenuti per tag" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Select TAG',
			'attributes' => array(),
		)
	);

	$persone_options->add_field(
		array(
			'id'         => $prefix . 'tutti_i_livelli_persone',
			'name'       => __( "Etichetta 'Tutti i TAG' ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Tutti i TAG'. E' usata per filtrare i contenuti per tag." , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Tutti i TAG',
			'attributes' => array(),
		)
	);

	$persone_options->add_field(
		array(
			'id'         => $prefix . 'tutti_i_livelli_persone' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( "Etichetta 'Tutti i TAG' ENG ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Tutti i TAG' in inglese.  E' usata per filtrare i contenuti per tag" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'All TAGs',
			'attributes' => array(),
		)
	);

	$persone_options->add_field(
		array(
			'id' => $prefix . 'label_person_details_is_visible',
			'name' => __( "Visualizza etchetta Dettagli", 'design_laboratori_italia' ),
			'desc' => __( "Indicare se nella pagina di dettaglio deve comparire l'etichetta 'Dettagli'", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	/**
	* 9 - Registers options page "Pubblicazioni".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_pubblicazioni',
		'title'        => esc_html__( 'Le pubblicazioni', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'pubblicazioni',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Pubblicazioni', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$pubblicazioni_options = new_cmb2_box( $args );
	$pubblicazioni_landing_url = dli_get_template_page_url( 'page-templates/pubblicazioni.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica delle Pubblicazioni</a>', 'design_laboratori_italia' ), $pubblicazioni_landing_url	);
	$pubblicazioni_options->add_field(
		array(
		'id'   => $prefix . 'pubblicazioni_istruzioni',
		'name' => __( 'Sezione Le Pubblicazioni', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	
	// Campi descrizione della sezione.
	$pubblicazioni_options->add_field(
		array(
			'id'         => $prefix . 'testo_pubblicazioni',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Pubblicazioni dei membri del Laboratorio."' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	$pubblicazioni_options->add_field(
		array(
			'id'         => $prefix . 'testo_pubblicazioni'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Publications by Lab members."' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	/**
	* 10 - Registers options page "Brevetti".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_brevetti',
		'title'        => esc_html__( 'I brevetti', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'brevetti',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Brevetti', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$brevetti_options = new_cmb2_box( $args );
	$brevetti_landing_url = dli_get_template_page_url( 'page-templates/brevetti.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica dei Brevetti</a>', 'design_laboratori_italia' ), $brevetti_landing_url	);
	$brevetti_options->add_field(
		array(
		'id'   => $prefix . 'brevetti_istruzioni',
		'name' => __( 'Sezione I Brevetti', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	
	// Campi descrizione della sezione.
	$brevetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_brevetti',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Brevetti dei membri del Laboratorio."' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	$brevetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_brevetti'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Publications by Lab members."' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	/**
	* 11 - Registers options page "Spin-off".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_spinoff',
		'title'        => esc_html__( 'Le Spin-off', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'spinoff',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Spin-off', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$spinoff_options = new_cmb2_box( $args );
	$spinoff_landing_url = dli_get_template_page_url( 'page-templates/spinoff.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica delle Spin-off</a>', 'design_laboratori_italia' ), $spinoff_landing_url	);
	$spinoff_options->add_field(
		array(
		'id'   => $prefix . 'spinoff_istruzioni',
		'name' => __( 'Sezione Le Spin-off', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	
	// Campi descrizione della sezione.
	$spinoff_options->add_field(
		array(
			'id'         => $prefix . 'testo_spinoff',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Spin-off del Laboratorio."' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	$spinoff_options->add_field(
		array(
			'id'         => $prefix . 'testo_spinoff'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Spin-off of the Lab"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);


	/**
	* 12 - Registers options page "Progetti".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_progetti',
		'title'        => esc_html__( 'I progetti', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'progetti',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Progetti', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$progetti_options = new_cmb2_box( $args );
	$progetti_landing_url = dli_get_template_page_url( 'page-templates/progetti.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica dei Progetti</a>', 'design_laboratori_italia' ), $progetti_landing_url );

	$progetti_options->add_field(
		array(
		'id'   => $prefix . 'progetti_istruzioni',
		'name' => __( 'Sezione I Progetti', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	// Campi descrizione della sezione.
	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_progetti',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "I progetti del Laboratorio"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br',
				),
			),
		)
	);

	// Campi descrizione della sezione.
	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_progetti' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Lab projects"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br',
				),
			),
		)
	);

	$progetti_options->add_field(
		array(
			'id'               => $prefix . 'pagination_mode',
			'name'             => __( 'Modalità di paginazione', 'design_laboratori_italia' ),
			'desc'             => __( 'Mostra tutti i risultati o attiva la paginazione' , 'design_laboratori_italia' ),
			'type'             => 'select',
			'default'          => 'Grande',
			'show_option_none' => false,
			'options'          => array(
				'show_all'      => __( 'Mostra tutti risultati', 'design_laboratori_italia' ),
				'show_paged'    => __( 'Attiva la paginazione', 'design_laboratori_italia' ),
			),
		)
	);

	$progetti_options->add_field(
		array(
			'id'               => $prefix . 'pagination_number',
			'name'             => __( 'Numero di elementi mostrati', 'design_laboratori_italia' ),
			'desc'             => __( 'Numero di elementi mostrati di default, se è attivata la paginazione' , 'design_laboratori_italia' ),
			'type'             => 'select',
			'default'          => DLI_POSTS_PER_PAGE.'',
			'show_option_none' => false,
			'options'          => DLI_POST_PER_PAGE_VALUES_COMBINED,
		)
	);

	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'seleziona_livello_progetti',
			'name'       => __( "Etichetta 'Seleziona TAG' ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Seleziona TAG'. E' usata per filtrare i contenuti per tag." , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Seleziona TAG',
			'attributes' => array(),
		)
	);

	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'seleziona_livello_progetti' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( "Etichetta 'Seleziona TAG' ENG ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Seleziona TAG' in inglese.  E' usata per filtrare i contenuti per tag" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Select TAG',
			'attributes' => array(),
		)
	);

	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'tutti_i_livelli_progetti',
			'name'       => __( "Etichetta 'Tutti i TAG' ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Tutti i TAG'. E' usata per filtrare i contenuti per tag." , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'Tutti i TAG',
			'attributes' => array(),
		)
	);

	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'tutti_i_livelli_progetti' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( "Etichetta 'Tutti i TAG' ENG ", 'design_laboratori_italia' ),
			'desc'       => __( "Indicare la personalizzazione, se necessaria, dell'etichetta 'Tutti i TAG' in inglese.  E' usata per filtrare i contenuti per tag" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'default'    => 'All TAGs',
			'attributes' => array(),
		)
	);

	$progetti_options->add_field(
		array(
			'id' => $prefix . 'label_project_details_is_visible',
			'name' => __( "Visualizza etchetta Dettagli", 'design_laboratori_italia' ),
			'desc' => __( "Indicare se nella pagina di dettaglio deve comparire l'etichetta 'Dettagli'", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'true',
			'options' => array(
					'true' => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	/**
	* 13 - Registers options page "Attività di ricerca".
	*/
	$args = array(
		'id'           => 'dli_options_ricerca',
		'title'        => esc_html__( 'Indirizzi di ricerca', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'ricerca',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Indirizzi di ricerca', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$ricerca_options = new_cmb2_box( $args );
	$ricerca_landing_url = dli_get_template_page_url( 'page-templates/ricerca.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica degli Indirizzi di ricerca</a>', 'design_laboratori_italia' ), $ricerca_landing_url );

	$ricerca_options->add_field(
		array(
		'id'   => $prefix . 'ricerca_istruzioni',
		'name' => __( 'Sezione Indirizzi Ricerca', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	// Campi descrizione della sezione.
	$ricerca_options->add_field(
		array(
			'id'         => $prefix . 'testo_ricerca',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Gli indirizzi di ricerca del Laboratorio"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br',
				),
			),
		)
	);

	// Campi descrizione della sezione.
	$ricerca_options->add_field(
		array(
			'id'         => $prefix . 'testo_ricerca'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Lab research activities"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br',
				),
			),
		)
	);




	/**
	* 14 - Registers options page "Attività di ricerca".
	*/
		$args = array(
				'id'           => 'dli_options_luoghi',
				'title'        => esc_html__( 'Luoghi', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'luoghi',
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'capability'    => DLI_EDIT_CONFIG_PERMISSION,
				'tab_title'    => __( 'Luoghi', 'design_laboratori_italia' ),	);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$luoghi_options = new_cmb2_box( $args );
		$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina dei luoghi</a>', 'design_laboratori_italia' ), get_post_type_archive_link("luogo") );

		$luoghi_options->add_field( array(
				'id'   => $prefix . 'luoghi_istruzioni',
				'name' => __( 'Sezione Luoghi', 'design_laboratori_italia' ),
				'desc' => $descr,
				'type' => 'title',
		) );

		$luoghi_options->add_field( array(
				'id' => $prefix . 'testo_sezione_luoghi',
				'name'        => __( 'Descrizione Sezione Luoghi', 'design_laboratori_italia' ),
				'desc' => __( 'es: "Questi i luoghi del laboratorio"' , 'design_laboratori_italia' ),
				'type' => 'wysiwyg',
				'options' => array(
					'textarea_rows' => 1,
					'media_buttons' => false,
					'teeny'         => true,
					'quicktags'     => false,
					'tinymce'       => array(
						'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
						'valid_elements' => 'a[href],strong,em,p,br', 
					),
				),
		) );

		$luoghi_options->add_field( array(
			'id' => $prefix . 'testo_sezione_luoghi'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'        => __( 'Descrizione Sezione Luoghi ENG', 'design_laboratori_italia' ),
			'desc' => __( 'es: "Lab places"' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br',
				),
			),
	) );

		$luoghi_options->add_field(array(
				'id' => $prefix . 'posizione_mappa',
				'name' => __( 'Visualizza mappa', 'design_laboratori_italia' ),
				'desc' => __( 'Seleziona <b>No</b> per visualizzare la mappa in fondo alla pagina dopo l\'elenco delle strutture.', 'design_laboratori_italia' ),
				'type' => 'radio_inline',
				'default' => 'true',
				'options' => array(
						'true' => __( 'Si', 'design_laboratori_italia' ),
						'false' => __( 'No', 'design_laboratori_italia' ),
				),
		));

	/**
	* 15 - Registers options page "Technical-Resources".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_technical_resources',
		'title'        => esc_html__( 'Risorse Tecniche', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'risorse-tecniche',
		'parent_slug'  => 'dli_options',
		'capability'   => DLI_EDIT_CONFIG_PERMISSION,
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Risorse tecniche', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$spinoff_options = new_cmb2_box( $args );
	$spinoff_landing_url = dli_get_template_page_url( 'page-templates/risorse-tecniche.php' );
	$descr = sprintf( __( 'Inserisci qui le informazioni utili a popolare <a href="%s"> la pagina di panoramica delle Risorse Tecniche</a>', 'design_laboratori_italia' ), $spinoff_landing_url	);
	$spinoff_options->add_field(
		array(
		'id'   => $prefix . 'risorse_tecniche_istruzioni',
		'name' => __( 'Sezione Risorse Tecniche', 'design_laboratori_italia' ),
		'desc' => $descr,
		'type' => 'title',
		)
	);
	
	// Campi descrizione della sezione.
	$spinoff_options->add_field(
		array(
			'id'      => $prefix . 'testo_risorse_tecniche',
			'name'    => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'    => __( 'es: "Risorsa Tecnica del Laboratorio."' , 'design_laboratori_italia' ),
			'type'    => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);

	$spinoff_options->add_field(
		array(
			'id'         => $prefix . 'testo_risorse_tecniche'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Descrizione Sezione ENG', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Technical Resource of the Lab"' , 'design_laboratori_italia' ),
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em,p,br', 
				),
			),
		)
	);


	/**
	* 16 - Registers options page "Social media".
	*/
		$args = array(
				'id'           => 'dli_options_socials',
				'title'        => esc_html__( 'Socialmedia', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'socials',
				'capability'    => DLI_EDIT_CONFIG_PERMISSION,
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'tab_title'    => __( 'Socialmedia', 'design_laboratori_italia' ),	);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$social_options = new_cmb2_box( $args );

		$social_options->add_field( array(
				'id' => $prefix . 'socials_istruzioni',
				'name'        => __( 'Sezione socialmedia', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui i link ai tuoi socialmedia' , 'design_laboratori_italia' ) . '.',
				'type' => 'title',
		) );

		$social_options->add_field(array(
				'id' => $prefix . 'show_socials',
				'name' => __( 'Mostra le icone social', 'design_laboratori_italia' ),
				'desc' => __( "Abilita la visualizzazione dei socialmedia nell'header e nel footer della pagina", 'design_laboratori_italia' ) . '.',
				'type' => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'true' => __( 'Si', 'design_laboratori_italia' ),
						'false' => __( 'No', 'design_laboratori_italia' ),
				),
				'attributes' => array(
						'data-conditional-value' => "false",
				),
		));

		$social_options->add_field( array(
				'id' => $prefix . 'facebook',
				'name' => 'Facebook',
				'type' => 'text_url',
		) );

		$social_options->add_field( array(
				'id' => $prefix . 'youtube',
				'name' => 'Youtube',
				'type' => 'text_url',
		) );
		
		$social_options->add_field( array(
				'id' => $prefix . 'instagram',
				'name' => 'Instagram',
				'type' => 'text_url',
		) );

		$social_options->add_field( array(
				'id' => $prefix . 'twitter',
				'name' => 'Twitter',
				'type' => 'text_url',
		) );

		$social_options->add_field( array(
				'id' => $prefix . 'linkedin',
				'name' => 'Linkedin',
				'type' => 'text_url',
		) );

		$social_options->add_field( array(
			'id' => $prefix . 'github',
			'name' => 'GitHub',
			'type' => 'text_url',
	) );

	$social_options->add_field( array(
		'id' => $prefix . 'pinterest',
		'name' => 'Pinterest',
		'type' => 'text_url',
	) );

	$social_options->add_field( array(
		'id' => $prefix . 'iris',
		'name' => 'Iris',
		'type' => 'text_url',
	) );

	$social_options->add_field( array(
		'id' => $prefix . 'alumni',
		'name' => 'Alumni',
		'type' => 'text_url',
	) );

	// BEGIN SECTION FOR ADMINISTRATORS
	if ( current_user_can( DLI_ADMIN_EDIT_CONFIG_PERMISSION ) ) {
		/**
		* 17 - Registers options page "Integrazione con Indico".
		*/
		$args = array(
			'id'           => 'dli_options_indico',
			'title'        => esc_html__( 'Indico', 'design_laboratori_italia' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'indico',
			'capability'    => DLI_ADMIN_EDIT_CONFIG_PERMISSION,
			'parent_slug'  => 'dli_options',
			'tab_group'    => 'dli_options',
			'tab_title'    => __( 'Indico', 'design_laboratori_italia' ),	);


		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$indico_options = new_cmb2_box( $args );

		$indico_options->add_field( array(
				'id' => $prefix . 'indico_istruzioni',
				'name'        => __( 'Sezione integrazione con Indico', 'design_laboratori_italia' ),
				'desc' => __( 'Impostazioni per configurare l\'integrazione con Indico' , 'design_laboratori_italia' ) . '.',
				'type' => 'title',
		) );

		$indico_options->add_field(array(
				'id' => $prefix . 'indico_enabled',
				'name' => __( "Attiva l'integrazione con Indico", 'design_laboratori_italia' ),
				'desc' => __( "Abilita l'integrazione con Indico", 'design_laboratori_italia' ) . '.',
				'type' => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'true'  => __( 'Si', 'design_laboratori_italia' ),
						'false' => __( 'No', 'design_laboratori_italia' ),
				),
				'attributes' => array(
						'data-conditional-value' => "false",
				),
		));

		$indico_options->add_field(array(
			'id' => $prefix . 'indico_debug_enabled',
			'name' => __( "Abilita messaggi debug", 'design_laboratori_italia' ),
			'desc' => __( "Abilita messaggi debug nel file error.log", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
			'attributes' => array(
					'data-conditional-value' => "false",
			),
		));

		$indico_options->add_field(
			array(
				'id'         => $prefix . 'indico_baseurl',
				'name'       => __( 'Url Indico', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( "L'url del sito Indico da cui importare i dati" , 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'         => $prefix . 'indico_token_api',
				'name'       => __( 'Token API', 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'type'     => 'password',
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'         => $prefix . 'indico_category',
				'name'       => __( 'Categoria', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( 'ID della categoria degli eventi da importare' , 'design_laboratori_italia' ),
				'type'       => 'text_small',
				'default'    => 1,
				'attributes' => array(
					'required' => 'required',
					'type'    => 'number',
					'pattern' => '\d*',
				),
				'sanitization_cb' => 'absint',
				'escape_cb'       => 'absint',
			)
		);

		$indico_options->add_field(
			array(
				'id'         => $prefix . 'indico_keywords',
				'name'       => __( 'Keywords', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( 'Le parole chiave degli eventi da importare, separate da virgola (operatore usato per la selezione: OR)' , 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_default_lang',
				'name'             => __( "Lingua default", 'design_laboratori_italia' ),
				'desc'             => __( "Lingua in cui devono essere importati gli eventi." , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'Italiano',
				'show_option_none' => false,
				'options'          => array(
					'it' => __( 'Italiano', 'design_laboratori_italia' ),
					'en' => __( 'Inglese', 'design_laboratori_italia' ),
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_schedule',
				'name'             => __( "Schedulazione", 'design_laboratori_italia' ),
				'desc'             => __( "Indica se l'import deve essere schedulato. L'import, in alternativa, può essere eseguito 'manualmente' invocando da browser l'endpoint protetto da autenticazione http://miosito/wp-json/custom/v1/indico-import." , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'never',
				'show_option_none' => false,
				'options'          => array(
					'never'      => __( 'Schedulazione disabilitata', 'design_laboratori_italia' ),
					'hourly'     => __( 'Ogni ora', 'design_laboratori_italia' ),
					'daily'      => __( 'Una volta al giorno', 'design_laboratori_italia' ),
					'twicedaily' => __( 'Due volte al giorno', 'design_laboratori_italia' ),
					'weekly'     => __( 'Una volta alla settimana', 'design_laboratori_italia' ),
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_import_type',
				'name'             => __( "Tipo import", 'design_laboratori_italia' ),
				'desc'             => __( "Indica se l'import deve essere finalizzato o si deve eseguire solo una prova (dry-run)" , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'dryrun',
				'show_option_none' => false,
				'options'          => array(
					'commit'   => __( 'Finalizza import', 'design_laboratori_italia' ),
					'dryrun'   => __( 'Dry Run (test import)', 'design_laboratori_italia' ),
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_import_criteria',
				'name'             => __( 'Scelta eventi', 'design_laboratori_italia' ),
				'desc'             => __( 'Criterio di scelta degli eventi da importare' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'this-year',
				'show_option_none' => false,
				'options'          => array(
					'all'      => __( 'Tutti gli eventi', 'design_laboratori_italia' ),
					'future'   => __( 'Solo eventi futuri', 'design_laboratori_italia' ),
					'this-year' => __( "Solo eventi di quest'anno", 'design_laboratori_italia' ),
				),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_imp_item_status',
				'name'             => __( "Stato dell'oggetto importato", 'design_laboratori_italia' ),
				'desc'             => __( "Stato di pubblicazione in cui un oggetto importato viene salvato" , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'draft',
				'show_option_none' => false,
				'options'          => array(
					'publish' => __( 'Pubblicato', 'design_laboratori_italia' ),
					'draft'   => __( 'Bozza', 'design_laboratori_italia' ),
			),
			)
		);

		$indico_options->add_field(
			array(
				'id'               => $prefix . 'indico_item_existent_action',
				'name'             => __( 'Evento esistente', 'design_laboratori_italia' ),
				'desc'             => __( "Azione da intraprendere se l'evento esiste già" , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'ignore',
				'show_option_none' => false,
				'options'          => array(
					'update' => __( 'Aggiorna', 'design_laboratori_italia' ),
					'ignore' => __( 'Ignora', 'design_laboratori_italia' ),
				),
			)
		);

		/**
		* 18 - Registers options page "Integrazione con IRIS".
		*/
		$args = array(
			'id'           => 'dli_options_iris',
			'title'        => esc_html__( 'Iris', 'design_laboratori_italia' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'iris',
			'capability'    => DLI_ADMIN_EDIT_CONFIG_PERMISSION,
			'parent_slug'  => 'dli_options',
			'tab_group'    => 'dli_options',
			'tab_title'    => __( 'Iris', 'design_laboratori_italia' ),	);


		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$iris_options = new_cmb2_box( $args );

		$iris_options->add_field( array(
				'id' => $prefix . 'iris_brevetti_istruzioni',
				'name'        => __( 'Sezione integrazione con IRIS AP Brevetti', 'design_laboratori_italia' ),
				'desc' => __( 'Impostazioni per configurare l\'integrazione con IRIS Cineca (AP Brevetti)' , 'design_laboratori_italia' ) . '.',
				'type' => 'title',
		) );

		$iris_options->add_field(array(
				'id' => $prefix . 'iris_brevetti_enabled',
				'name' => __( "Attiva l'importazione dei brevetti", 'design_laboratori_italia' ),
				'desc' => __( "Abilita l'integrazione con IRIS AP Brevetti", 'design_laboratori_italia' ) . '.',
				'type' => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'true'  => __( 'Si', 'design_laboratori_italia' ),
						'false' => __( 'No', 'design_laboratori_italia' ),
				),
				'attributes' => array(
						'data-conditional-value' => "false",
				),
		));

		$iris_options->add_field(array(
			'id' => $prefix . 'iris_debug_enabled',
			'name' => __( "Abilita messaggi debug", 'design_laboratori_italia' ),
			'desc' => __( "Abilita messaggi debug nel file error.log", 'design_laboratori_italia' ) . '.',
			'type' => 'radio_inline',
			'default' => 'false',
			'options' => array(
					'true'  => __( 'Si', 'design_laboratori_italia' ),
					'false' => __( 'No', 'design_laboratori_italia' ),
			),
			'attributes' => array(
					'data-conditional-value' => "false",
			),
		));

		$iris_options->add_field(
			array(
				'id'         => $prefix . 'iris_brevetti_url',
				'name'       => __( 'Url endpoint brevetti', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( "L'url dell'endpoint da invocare per scaricare i brevetti." , 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		$iris_options->add_field(
			array(
				'id'         => $prefix . 'iris_brevetti_username',
				'name'       => __( 'Username', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( 'Lo username per autenticarsi sul web-service.' , 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		$iris_options->add_field(
			array(
				'id'         => $prefix . 'iris_brevetti_password',
				'name'       => __( 'Password', 'design_laboratori_italia' ) . '&nbsp;*',
				'desc'       => __( 'La password per autenticarsi sul web-service.' , 'design_laboratori_italia' ),
				'type'       => 'text',
				'default'    => 'xxx',
				'attributes' => array(
					'type'     => 'password',
				),
			)
		);

		$iris_options->add_field(
			array(
				'id'               => $prefix . 'iris_brevetti_schedule',
				'name'             => __( "Schedulazione", 'design_laboratori_italia' ),
				'desc'             => __( "Indica se l'import deve essere schedulato. L'import, in alternativa, può essere eseguito 'manualmente' invocando da browser l'endpoint protetto da autenticazione http://miosito/wp-json/custom/v1/iris-ap-brevetti-import." , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'never',
				'show_option_none' => false,
				'options'          => array(
					'never'      => __( 'Schedulazione disabilitata', 'design_laboratori_italia' ),
					'hourly'     => __( 'Ogni ora', 'design_laboratori_italia' ),
					'daily'      => __( 'Una volta al giorno', 'design_laboratori_italia' ),
					'twicedaily' => __( 'Due volte al giorno', 'design_laboratori_italia' ),
					'weekly'     => __( 'Una volta alla settimana', 'design_laboratori_italia' ),
				),
			)
		);

		$iris_options->add_field(
			array(
				'id'               => $prefix . 'iris_brevetti_import_type',
				'name'             => __( "Tipo import", 'design_laboratori_italia' ),
				'desc'             => __( "Indica se l'import deve essere finalizzato o si deve eseguire solo una prova (dry-run)" , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'dryrun',
				'show_option_none' => false,
				'options'          => array(
					'commit'   => __( 'Finalizza import', 'design_laboratori_italia' ),
					'dryrun'   => __( 'Dry Run (test import)', 'design_laboratori_italia' ),
				),
			)
		);

		$iris_options->add_field(
			array(
				'id'               => $prefix . 'iris_brevetti_item_existent_action',
				'name'             => __( 'Brevetto esistente', 'design_laboratori_italia' ),
				'desc'             => __( "Azione da intraprendere se il brevetto esiste già" , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'ignore',
				'show_option_none' => false,
				'options'          => array(
					'update' => __( 'Aggiorna', 'design_laboratori_italia' ),
					'ignore' => __( 'Ignora', 'design_laboratori_italia' ),
				),
			)
		);

		$iris_options->add_field( array(
			'id' => $prefix . 'iris_pubblicazioni_istruzioni',
			'name'        => __( 'Sezione integrazione con IRIS Pubblicazioni', 'design_laboratori_italia' ),
			'desc' => __( 'Impostazioni per configurare l\'integrazione con IRIS Cineca (Pubblicazioni)' , 'design_laboratori_italia' ) . '.',
			'type' => 'title',
		) );


		/**
		* 19 - Registers options page "Altro".
		*/

		$args = array(
			'id'           => 'dli_setup_menu',
			'title'        => esc_html__( 'Altro', 'design_laboratori_italia' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'setup',
			'tab_title'    => __( 'Altro', 'design_laboratori_italia' ),
			'parent_slug'  => 'dli_options',
			'tab_group'    => 'dli_options',
			'capability'   => DLI_ADMIN_EDIT_CONFIG_PERMISSION,
			);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
			$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$setup_options = new_cmb2_box( $args );

		$setup_options->add_field( array(
					'id' => $prefix . 'altro_istruzioni',
					'name'        => __( 'Altre Informazioni', 'design_laboratori_italia' ),
					'desc' => __( 'Area di configurazione delle opzioni generali del tema' , 'design_laboratori_italia' ) . '.',
					'type' => 'title',
			) );

		$setup_options->add_field(
			array(
				'id'   => $prefix . 'style_manager',
				'name' => __( 'Gestione dello stile del sito', 'design_laboratori_italia' ),
				'type' => 'title',
			)
		);

		$setup_options->add_field(
			array(
				'id'               => $prefix . 'choose_style',
				'name'             => __( 'Stile del sito', 'design_laboratori_italia' ),
				'desc'             => __( 'Selezione lo stile del sito (scegliere default per usare quello standard di Designers Italia)' , 'design_laboratori_italia' ),
				'type'             => 'select',
				'default'          => 'default',
				'show_option_none' => false,
				'options'          => array(
					'standard' => __( 'Stile Bootstrap Italia standard', 'design_laboratori_italia' ),
					'custom'   => __( 'Stile personalizzato', 'design_laboratori_italia' ),
			),
			)
		);
		
			$setup_options->add_field(
				array(
					'id'   => $prefix . 'newsletter',
					'name' => __( 'Newsletter', 'design_laboratori_italia' ),
					'type' => 'title',
				)
			);
		
			$setup_options->add_field(
				array(
					'id'      => $prefix . 'newsletter_enabled',
					'name'    => __( 'Attiva la newsletter', 'design_laboratori_italia' ),
					'type'    => 'radio_inline',
					'default' => 'false',
					'options' => array(
							'true'  => __( 'Si', 'design_laboratori_italia' ),
							'false' => __( 'No', 'design_laboratori_italia' ),
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'               => $prefix . 'newsletter_manager',
					'name'             => __( 'Gestore delle newsletter', 'design_laboratori_italia' ),
					'desc'             => __( 'Selezione del programma usato per gestire la newsletter del sito' , 'design_laboratori_italia' ),
					'type'             => 'select',
					'default'          => 'default',
					'show_option_none' => false,
					'options'          => array(
						'brevo' => __( 'Brevo', 'design_laboratori_italia' ),
				),
				)
			);

			$setup_options->add_field(
				array(
					'id'         => $prefix . 'newsletter_api_token',
					'name'       => __( 'Token API', 'design_laboratori_italia' ),
					'type'       => 'text',
					'attributes' => array(
						'type' => 'password',
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'              => $prefix . 'newsletter_list_id',
					'name'            => __( 'ID della lista', 'design_laboratori_italia' ),
					'desc'            => __( 'ID della lista associata al sito' , 'design_laboratori_italia' ),
					'type'            => 'text_small',
					'attributes'      => array(
						'type'    => 'number',
						'pattern' => '\d*',
					),
					'sanitization_cb' => 'absint',
					'escape_cb'       => 'absint',
				)
			);

			$setup_options->add_field(
				array(
					'id'              => $prefix . 'newsletter_template_id',
					'name'            => __( 'ID del template', 'design_laboratori_italia' ),
					'desc'            => __( 'ID del template della pagina che gestisce la double OptIn' , 'design_laboratori_italia' ),
					'type'            => 'text_small',
					'attributes'      => array(
						'type'    => 'number',
						'pattern' => '\d*',
					),
					'sanitization_cb' => 'absint',
					'escape_cb'       => 'absint',
				)
			);

			$setup_options->add_field(
				array(
					'id'   => $prefix . 'login',
					'name' => __( 'Login', 'design_laboratori_italia' ),
					'type' => 'title',
				)
			);
		
			$setup_options->add_field(
				array(
					'id'      => $prefix . 'login_button_visible',
					'name'    => __( 'Pulsante per il login visibile', 'design_laboratori_italia' ),
					'type'    => 'radio_inline',
					'default' => 'true',
					'options' => array(
							'true'  => __( 'Si', 'design_laboratori_italia' ),
							'false' => __( 'No', 'design_laboratori_italia' ),
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'   => $prefix . 'multilingua',
					'name' => __( 'Multilingua', 'design_laboratori_italia' ),
					'type' => 'title',
				)
			);
		
			$setup_options->add_field(
				array(
					'id'      => $prefix . 'selettore_lingua_visible',
					'name'    => __( 'Selettore lingua visibile', 'design_laboratori_italia' ),
					'type'    => 'radio_inline',
					'default' => 'true',
					'options' => array(
							'true'  => __( 'Si', 'design_laboratori_italia' ),
							'false' => __( 'No', 'design_laboratori_italia' ),
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'   => $prefix . 'analytics',
					'name' => __( 'Web Analytics Italia', 'design_laboratori_italia' ),
					'type' => 'title',
				)
			);
		
			$setup_options->add_field(
				array(
					'id'   => $prefix . 'analytics_code',
					'name' => 'Codice analytics',
					'desc' => __( 'Inserisci il codice Analytics. Puoi crearlo <a target="_blank" href="https://webanalytics.italia.it/">da qui</a>', 'design_laboratori_italia' ),
					'type' => 'textarea_code',
					'attributes'    => array(
							'rows'  => 10,
							'maxlength'  => '1000',
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'   => $prefix . 'restapi',
					'name' => __( 'REST API', 'design_laboratori_italia' ),
					'type' => 'title',
				)
			);

			$setup_options->add_field(
				array(
					'id'      => $prefix . 'rest_api_enabled',
					'name'    => __( 'Abilita REST API', 'design_laboratori_italia' ),
					'type'    => 'radio_inline',
					'default' => 'false',
					'options' => array(
							'true'  => __( 'Si', 'design_laboratori_italia' ),
							'false' => __( 'No', 'design_laboratori_italia' ),
					),
				)
			);

			$setup_options->add_field(
				array(
					'id'   => 'seo_section',
					'name' => __( 'SEO', 'kk_writer_theme' ),
					'type' => 'title',
				)
			);
			$setup_options->add_field(
				array(
					'id'      => 'seo_internal_management_enabled',
					'name'    => __( 'Enable internal SEO management', 'kk_writer_theme' ),
					'desc'    => __( 'Enable the internal management of SEO and OG tags or disable it to delegate this job to an external plugin.', 'kk_writer_theme' ),
					'type'    => 'radio_inline',
					'default' => 'true',
					'options' => array(
							'true'  => __( 'Yes', 'kk_writer_theme' ),
							'false' => __( 'No', 'kk_writer_theme' ),
					),
				)
			);

	} // END SECTION FOR ADMINISTRATORS

}
add_action( 'cmb2_admin_init', 'dli_register_main_options_metabox' );

/**
	* A CMB2 options-page display callback override which adds tab navigation among
	* CMB2 options pages which share this same display callback.
	*
	* @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	*/
function dli_options_display_with_tabs( $cmb_options ) {
	$tabs = dli_options_page_tabs( $cmb_options );
	?>
	<div class="wrap cmb2-options-page option-<?php echo $cmb_options->option_key; ?>">
		<?php if ( get_admin_page_title() ) : ?>
			<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
		<?php endif; ?>

				<div class="cmb2-options-box">
						<div class="nav-tab-wrapper">
								<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
										<a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
								<?php endforeach; ?>
						</div>

						<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo $cmb_options->cmb->cmb_id; ?>" enctype="multipart/form-data" encoding="multipart/form-data">
								<fieldset class="form-content">
										<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
										<?php $cmb_options->options_page_metabox(); ?>
								</fieldset>

								<fieldset class="form-footer">
										<div class="submit-box"><?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb', false ); ?></div>
								</fieldset>
						</form>

						<div class="clear-form"></div>
				</div>
	</div>
	<?php
}

/**
	* Gets navigation tabs array for CMB2 options pages which share the given
	* display_cb param.
	*
	* @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	*
	* @return array Array of tab information.
	*/
function dli_options_page_tabs( $cmb_options ) {
	$tab_group = $cmb_options->cmb->prop( 'tab_group' );
	$tabs      = array();

	foreach ( CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
		if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
			$tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
				? $cmb->prop( 'tab_title' )
				: $cmb->prop( 'title' );
		}
	}

	return $tabs;
}


function dli_options_assets() {
		$current_screen = get_current_screen();

		if(strpos($current_screen->id, 'configurazione_page_') !== false || $current_screen->id === 'toplevel_page_dli_options') {
				wp_enqueue_style( 'dli_options_dialog', get_stylesheet_directory_uri() . '/inc/admin-css/jquery-ui.css' );
				wp_enqueue_script( 'dli_options_dialog', get_stylesheet_directory_uri() . '/inc/admin-js/options.js', array('jquery', 'jquery-ui-core', 'jquery-ui-dialog' ), '1.0', true );
		}
}
add_action( 'admin_enqueue_scripts', 'dli_options_assets' );
