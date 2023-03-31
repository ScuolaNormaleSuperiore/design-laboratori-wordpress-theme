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
		'capability'   => 'manage_options',
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
			'name'       => __( 'Tipologia *', 'design_laboratori_italia' ),
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
			'name'       => __( 'Nome Laboratorio *', 'design_laboratori_italia' ),
			'desc'       => __( 'Il Nome del Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required'   => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'tagline_laboratorio',
			'name'       => __( 'Tagline *', 'design_laboratori_italia' ),
			'desc'       => __( 'La tagline del Laboratorio' , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'luogo_laboratorio',
			'name'       => __( 'Città *', 'design_laboratori_italia' ),
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
			'name'       => __( 'Indirizzo *', 'design_laboratori_italia' ),
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
			'name'       => __( 'Email *', 'design_laboratori_italia' ),
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
			'attributes' => array(
				// 'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'telefono_laboratorio',
			'name'       => __( 'Telefono *', 'design_laboratori_italia' ),
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
			'name'       => __( 'Ente padre *', 'design_laboratori_italia' ),
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
			'name'       => __( 'Url ente padre *', 'design_laboratori_italia' ),
			'desc'       => __( "L'url dell'ente padre" , 'design_laboratori_italia' ),
			'type'       => 'text',
			'attributes' => array(
				'required' => 'required',
			),
		)
	);

	$header_options->add_field(
		array(
			'id'         => $prefix . 'logo_laboratorio',
			'name'       => __( 'Logo', 'design_laboratori_italia' ),
			'desc'       => __( 'Il logo del laboratorio. Si raccomanda di caricare un\'immagine in formato svg' , 'design_laboratori_italia' ),
			'type'       => 'file',
			'query_args' => array(
				'type' => array(
					'image/svg',
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
					'image/svg',
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
		'capability'   => 'manage_options',
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
			'id' => $prefix . 'messages_istruzioni',
			'name'        => __( 'Avvisi di allerta in Home Page', 'design_laboratori_italia' ),
			'desc' => __( 'Inserisci messaggi che saranno visualizzati nella homepage.' , 'design_laboratori_italia' ),
			'type' => 'title',
	) );

	$alerts_group_id = $alerts_options->add_field( array(
			'id'           => $prefix . 'messages',
			'type'        => 'group',
			'desc' => __( 'Ogni messaggio è costruito attraverso descrizione breve (max 140 caratteri) e data di scadenza (opzionale).' , 'design_laboratori_italia' ),
			'repeatable'  => true,
			'options'     => array(
					'group_title'   => __( 'Messaggio {#}', 'design_laboratori_italia' ),
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
					'danger'  => __( '<span class="radio-color red"></span>Danger', 'design_laboratori_italia' ),
					'success' => __( '<span class="radio-color green"></span>Success', 'design_laboratori_italia' ),
					'warning' => __( '<span class="radio-color brown"></span>Warning', 'design_laboratori_italia' ),
					'info'    => __( '<span class="radio-color gray"></span>Info', 'design_laboratori_italia' ),
			),
			'default' => 'info',
	) );

	// $alerts_options->add_group_field( $alerts_group_id, array(
	// 		'name' => 'Visualizza icona',
	// 		'id'   => 'icona_message',
	// 		'type' => 'checkbox',
	// ) );

	// $alerts_options->add_group_field( $alerts_group_id, array(
	// 		'id' => $prefix . 'data_message',
	// 		'name'        => __( 'Data fine', 'design_laboratori_italia' ),
	// 		'type' => 'text_date',
	// 		'date_format' => 'd-m-Y',
	// 		'data-datepicker' => json_encode( array(
	// 				'yearRange' => '-100:+0',
	// 		) ),
	// ) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'id' => $prefix . 'testo_message',
			'name'        => __( 'Testo', 'design_laboratori_italia' ),
			'desc' => __( 'Massimo 140 caratteri' , 'design_laboratori_italia' ),
			'type' => 'textarea_small',
			'attributes'    => array(
					'rows'  => 3,
					'maxlength'  => '140',
			),
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'id' => $prefix . 'link_message',
			'name'        => __( 'Collegamento', 'design_laboratori_italia' ),
			'desc' => __( 'Link al una pagina di approfondimento anche esterna al sito' , 'design_laboratori_italia' ),
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
				'capability'   => 'manage_options',
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
		$home_options->add_field(
			array(
				'id' => $prefix . 'home_istruzioni_1',
				'name'        => __( 'Sezione Carousel', 'design_laboratori_italia' ),
				'desc' => __( 'Gestione del carousel in Home Page' , 'design_laboratori_italia' ),
				'type' => 'title',
			)
		);

		$home_options->add_field(
			array(
				'id' => $prefix . 'home_carousel_is_selezione_automatica',
				'name' => __( 'Selezione Automatica', 'design_laboratori_italia' ),
				'desc' => __( 'Seleziona <b>Si</b> per mostrare automaticamente gli articoli per i quali è stato settato il flag "Promuovi in carousel". <b>No</b> per sceglierli manualmente nella sezione seguente.', 'design_laboratori_italia' ),
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
				'name'    => __( 'Selezione articoli ', 'design_laboratori_italia' ),
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

	// *** SEZIONE MAIN HERO (HOMEPAGE) ***
	$home_options->add_field(
		array(
			'id'   => $prefix . 'home_main_hero',
			'name' => __( 'Sezione hero principale', 'design_laboratori_italia' ),
			'desc' => __( 'Gestione sezione Hero principale (opzionale) mostrato in Home Page' , 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$home_options->add_field(
		array(
			'id' => $prefix . 'home_main_hero_enabled',
			'name' => __( 'Hero principale attivo', 'design_laboratori_italia' ),
			'desc' => __( 'Attiva l\'hero principale in Home Page', 'design_laboratori_italia' ),
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
			'id'         => $prefix . 'home_main_hero_title',
			'name'       => __( 'Titolo hero', 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_title' . DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Titolo hero ENG', 'design_laboratori_italia' ),
			'type'       => 'text',
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
			'id'         => $prefix . 'home_main_hero_button_label'. DLI_ENG_SUFFIX_LANGUAGE,
			'name'       => __( 'Label bottone ENG', 'design_laboratori_italia' ),
			'type'       => 'text',
		)
	);

	$home_options->add_field(
		array(
			'id'         => $prefix . 'home_main_hero_url',
			'name'       => __( 'Url', 'design_laboratori_italia' ),
			'type'       => 'text',
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
					'image/svg',
				),
			),
		)
	);

	/**
	* 4 - Registers options page "Laboratorio".
	*/
	$args = array(
		'id'           => 'dli_options_la_scuola',
		'title'        => esc_html__( 'Il Laboratorio', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
				'capability'    => 'manage_options',
				'option_key'   => 'la_scuola',
		'tab_title'    => __( 'Laboratorio', 'design_laboratori_italia' ),
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',

	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$main_options = new_cmb2_box( $args );

	$scuola_landing_url = dli_get_template_page_url("page-templates/il-laboratorio.php");

	$main_options->add_field(
		array(
			'id'   => $prefix . 'laboratorio_istruzioni',
			'name' => __( 'Sezione Il Laboratorio', 'design_laboratori_italia' ),
			'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$scuola_landing_url.'">la pagina di panoramica del Laboratorio</a>.' , 'design_laboratori_italia' ),
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
			'desc'       => __( 'Titolo della sezione in Inglese' , 'design_laboratori_italia' ),
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
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em',
				),
			),
		)
	);

	$main_options->add_field(
		array(
			'id'    => $prefix . 'descrizione_laboratorio'. DLI_ENG_SUFFIX_LANGUAGE,
			'title' => __( 'Descrizione ENG', 'design_laboratori_italia' ),
			'name'  => __( 'Descrizione ENG', 'design_laboratori_italia' ),
			'desc'  => __( 'Descrizione del laboratorio in Inglese' , 'design_laboratori_italia' ),
			'type' => 'wysiwyg',
			'options' => array(
				'textarea_rows' => 1,
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => false,
				'tinymce'       => array(
					'toolbar1'       => 'bold,italic,link,unlink,undo,redo',
					'valid_elements' => 'a[href],strong,em',
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
		'capability'   => 'manage_options',
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
		$blog_options->add_field(
			array(
				'id'   => $prefix . 'blog_istruzioni',
				'name' => __( 'Sezione Il Blog', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$blog_landing_url.'"> il blog</a>.' , 'design_laboratori_italia' ),
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
						'valid_elements' => 'a[href],strong,em', 
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
						'valid_elements' => 'a[href],strong,em', 
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
		'capability'   => 'manage_options',
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
		$notizie_options->add_field(
			array(
				'id'   => $prefix . 'notizie_istruzioni',
				'name' => __( 'Sezione Le Novità', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$notizie_landing_url.'">la pagina di panoramica delle Novità</a>.' , 'design_laboratori_italia' ),
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
						'valid_elements' => 'a[href],strong,em',
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
						'valid_elements' => 'a[href],strong,em',
					),
				),
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
		'capability'   => 'manage_options',
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
		$eventi_options->add_field(
			array(
				'id'   => $prefix . 'eventi_istruzioni',
				'name' => __( 'Sezione Eventi', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$eventi_landing_url.'">la pagina di panoramica degli Eventi</a>.' , 'design_laboratori_italia' ),
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
						'valid_elements' => 'a[href],strong,em',
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
						'valid_elements' => 'a[href],strong,em',
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
		'capability'    => 'manage_options',
		'tab_title'    => __( 'Persone', 'design_laboratori_italia' ),	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
			$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$persone_options = new_cmb2_box( $args );

	$persone_landing_url = dli_get_template_page_url("page-templates/persone.php");
	$persone_options->add_field( array(
			'id' => $prefix . 'persone_istruzioni',
			'name'        => __( 'Sezione Persone', 'design_laboratori_italia' ),
			'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$persone_landing_url.'">la pagina delle Persone</a>.' , 'design_laboratori_italia' ),
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
					'valid_elements' => 'a[href],strong,em', 
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
					'valid_elements' => 'a[href],strong,em', 
				),
			),
		),
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
		'capability'   => 'manage_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Pubblicazioni', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$pubblicazioni_options = new_cmb2_box( $args );
	$pubblicazioni_landing_url = dli_get_template_page_url( 'page-templates/pubblicazioni.php' );
	$pubblicazioni_options->add_field(
		array(
		'id'   => $prefix . 'pubblicazioni_istruzioni',
		'name' => __( 'Sezione I Pubblicazioni', 'design_laboratori_italia' ),
		'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$pubblicazioni_landing_url.'">la pagina di panoramica delle Pubblicazioni</a>.' , 'design_laboratori_italia' ),
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
					'valid_elements' => 'a[href],strong,em', 
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
					'valid_elements' => 'a[href],strong,em', 
				),
			),
		)
	);

	/**
	* 10 - Registers options page "Progetti".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_progetti',
		'title'        => esc_html__( 'Le progetti', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'progetti',
		'parent_slug'  => 'dli_options',
		'capability'   => 'manage_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Progetti', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$progetti_options = new_cmb2_box( $args );
	$progetti_landing_url = dli_get_template_page_url( 'page-templates/progetti.php' );
	$progetti_options->add_field(
		array(
		'id'   => $prefix . 'progetti_istruzioni',
		'name' => __( 'Sezione I Progetti', 'design_laboratori_italia' ),
		'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$progetti_landing_url.'">la pagina di panoramica dei Progetti</a>.' , 'design_laboratori_italia' ),
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
					'valid_elements' => 'a[href],strong,em', 
				),
			),
		)
	);

	// Campi descrizione della sezione.
	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_progetti'. DLI_ENG_SUFFIX_LANGUAGE,
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
					'valid_elements' => 'a[href],strong,em', 
				),
			),
		)
	);

	/**
	* 11 - Registers options page "Attività di ricerca".
	*/
	$args = array(
		'id'           => 'dli_options_ricerca',
		'title'        => esc_html__( 'Indirizzi di ricerca', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'ricerca',
		'parent_slug'  => 'dli_options',
		'capability'   => 'manage_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Indirizzi di ricerca', 'design_laboratori_italia' ),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$ricerca_options = new_cmb2_box( $args );
	$ricerca_landing_url = dli_get_template_page_url( 'page-templates/ricerca.php' );
	$ricerca_options->add_field(
		array(
		'id'   => $prefix . 'ricerca_istruzioni',
		'name' => __( 'Sezione Indirizzi Ricerca', 'design_laboratori_italia' ),
		'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$ricerca_landing_url.'">la pagina di panoramica degli Indirizzi di ricerca</a>.' , 'design_laboratori_italia' ),
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
					'valid_elements' => 'a[href],strong,em',
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
					'valid_elements' => 'a[href],strong,em',
				),
			),
		)
	);




	/**
	* 12 - Registers options page "Attività di ricerca".
	*/
		$args = array(
				'id'           => 'dli_options_luoghi',
				'title'        => esc_html__( 'Luoghi', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'luoghi',
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'capability'    => 'manage_options',
				'tab_title'    => __( 'Luoghi', 'design_laboratori_italia' ),	);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$luoghi_options = new_cmb2_box( $args );

		$luoghi_options->add_field( array(
				'id' => $prefix . 'luoghi_istruzioni',
				'name'        => __( 'Sezione Luoghi', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.get_post_type_archive_link("luogo").'">la pagina dei luoghi scolastici</a>.' , 'design_laboratori_italia' ),
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
						'valid_elements' => 'a[href],strong,em', 
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
					'valid_elements' => 'a[href],strong,em',
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
	* 13 - Registers options page "Social media".
	*/
		$args = array(
				'id'           => 'dli_options_socials',
				'title'        => esc_html__( 'Socialmedia', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'socials',
				'capability'    => 'manage_options',
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
				'desc' => __( 'Inserisci qui i link ai tuoi socialmedia.' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$social_options->add_field(array(
				'id' => $prefix . 'show_socials',
				'name' => __( 'Mostra le icone social', 'design_laboratori_italia' ),
				'desc' => __( 'Abilita la visualizzazione dei socialmedia nell\'header e nel footer della pagina.', 'design_laboratori_italia' ),
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

	/**
	* 14 - Registers options page "Altro".
	*/

	$args = array(
		'id'           => 'dli_setup_menu',
		'title'        => esc_html__( 'Altro', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'setup',
		'tab_title'    => __( 'Altro', 'design_laboratori_italia' ),
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
				'capability'    => 'manage_options',
		);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$setup_options = new_cmb2_box( $args );

	$setup_options->add_field( array(
				'id' => $prefix . 'altro_istruzioni',
				'name'        => __( 'Altre Informazioni', 'design_laboratori_italia' ),
				'desc' => __( 'Area di configurazione delle opzioni generali del tema.' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

	$setup_options->add_field(
		array(
			'id'   => $prefix . 'mapbox_key',
			'name' => 'Access Token MapBox',
			'desc' => __( 'Inserisci l\'access token mapbox per l\'erogazione delle mappe. Puoi crearlo <a target="_blank" href="https://www.mapbox.com/studio/account/tokens/">da qui</a>', 'design_laboratori_italia' ),
			'type' => 'text',
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
