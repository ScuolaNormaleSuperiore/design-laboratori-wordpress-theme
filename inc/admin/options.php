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
			'desc'       => __( 'Il Nome della Laboratorio' , 'design_laboratori_italia' ),
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
			'desc'       => __( 'La città dove risiede la Laboratorio' , 'design_laboratori_italia' ),
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
			'name'         => __('Logo per mobile', 'design_laboratori_italia' ),
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
			'name'        => __( 'Avvisi di allerta in home page', 'design_laboratori_italia' ),
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
					'red'   => __( '<span class="radio-color red"></span>Rosso', 'design_laboratori_italia' ),
					'yellow' => __( '<span class="radio-color yellow"></span>Giallo', 'design_laboratori_italia' ),
					'green'     => __( '<span class="radio-color green"></span>Verde', 'design_laboratori_italia' ),
					'blue'     => __( '<span class="radio-color blue"></span>Blu', 'design_laboratori_italia' ),
					'purple'     => __( '<span class="radio-color purple"></span>Viola', 'design_laboratori_italia' ),
			),
			'default' => 'yellow',
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'name' => 'Visualizza icona',
			'id'   => 'icona_message',
			'type' => 'checkbox',
	) );

	$alerts_options->add_group_field( $alerts_group_id, array(
			'id' => $prefix . 'data_message',
			'name'        => __( 'Data fine', 'design_laboratori_italia' ),
			'type' => 'text_date',
			'date_format' => 'd-m-Y',
			'data-datepicker' => json_encode( array(
					'yearRange' => '-100:+0',
			) ),
	) );

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
				'capability'    => 'manage_options',
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'tab_title'    => __('Home', 'design_laboratori_italia'),	);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$home_options = new_cmb2_box( $args );

		/**
		* 3 - Registers options page "Home".
		*/
		$home_options->add_field( array(
				'id' => $prefix . 'home_istruzioni_1',
				'name'        => __( 'Sezione Notizie', 'design_laboratori_italia' ),
				'desc' => __( 'Gestione Notizie / Articoli / Eventi mostrati in home page' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$home_options->add_field(array(
				'id' => $prefix . 'home_is_selezione_automatica',
				'name' => __('Selezione Automatica', 'design_laboratori_italia'),
				'desc' => __('Seleziona <b>Si</b> per mostrare automaticamente gli articoli in home page. Le colonne mostreranno l\'ultimo articolo delle tipologie selezionate nella <a href="admin.php?page=notizie">configurazione della Pagina "Novità"</a>,', 'design_laboratori_italia'),
				'type' => 'radio_inline',
				'default' => 'true',
				'options' => array(
						'true' => __('Si', 'design_laboratori_italia'),
						'false' => __('No', 'design_laboratori_italia'),
				),
		));

		$home_options->add_field(array(
				'id' => $prefix . 'home_show_events',
				'name' => __('Mostra gli eventi in Home', 'design_laboratori_italia'),
				'desc' => __('Abilita gli eventi in Home e decidi come mostrarli', 'design_laboratori_italia'),
				'type' => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'false' => __('No', 'design_laboratori_italia'),
						'true_event' => __('Si, mostra il prossimo evento', 'design_laboratori_italia'),
						// 'true_calendar' => __('Si, mostra il calendario', 'design_laboratori_italia'),
				),
				'attributes' => array(
						'data-conditional-id' => $prefix . 'home_is_selezione_automatica',
						'data-conditional-value' => "true",
				),
		));

		$home_options->add_field( array(
				'id' => $prefix . 'home_istruzioni_banner',
				'name'        => __( 'Sezione Banner', 'design_laboratori_italia' ),
				'desc' => __( 'Gestione sezione Banner (opzionale) mostrata in home page' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$home_options->add_field(  array(
				'id' => $prefix.'visualizza_banner',
				'name'    => __( 'Visualizza la fascia banner', 'design_laboratori_italia' ),
				'type'    => 'radio_inline',
				'options' => array(
						'si' => __( 'Si', 'design_laboratori_italia' ),
						'no'   => __( 'No', 'design_laboratori_italia' ),
				),
				'default' => "no"
		) );


		$bsnner_group_id = $home_options->add_field( array(
				'id'          =>  $prefix . 'banner_group',
				'type'        => 'group',
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => 'Banner {#}',
						'add_button'    => 'Aggiungi un nuovo banner',
						'remove_button' => 'Rimuovi Banner',
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$home_options->add_group_field( $bsnner_group_id, array(
				'name' => 'Banner',
				'id'   => 'banner',
				'type'    => 'file',
				'options' => array(
						'url' => false, // Hide the text input for the url
				),
				'text'    => array(
						'add_upload_file_text' => 'Aggiungi file' // Change upload button text. Default: "Add or Upload File"
				),
				'query_args' => array(
						'type' => array(
							'image/gif',
							'image/jpeg',
							'image/png',
						),
				),
				'preview_size' => 'banner',
		) );

		$home_options->add_group_field( $bsnner_group_id, array(
				'name' => 'Url di destinazione',
				'desc' => 'Url di destinazione (lasciare vuoto se non necessario)',
				'id'   => 'url',
				'type' => 'text_url',
		) );

		$home_options->add_field( array(
				'id' => $prefix . 'home_istruzioni_2',
				'name'        => __( 'Sezione Servizi', 'design_laboratori_italia' ),
				'desc' => __( 'Gestione sezione Servizi mostrati in home page' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$home_options->add_field(array(
				'id' => $prefix . 'home_is_selezione_automatica_servizi',
				'name' => __('Selezione Automatica', 'design_laboratori_italia'),
				'desc' => __('Seleziona per mostrare automaticamente i servizi (mostra gli ultimi)', 'design_laboratori_italia'),
				'type' => 'radio_inline',
				'default' => 'true',
				'options' => array(
						'true' => __('Si', 'design_laboratori_italia'),
						'false' => __('No', 'design_laboratori_italia'),
				),
		));

		$home_options->add_field(array(
						'name' => __('Selezione articoli ', 'design_laboratori_italia'),
						'desc' => __('Seleziona gli articoli da mostrare in Home Page. NB: Selezionane 3 o multipli di 3 per evitare buchi nell\'impaginazione.  ', 'design_laboratori_italia'),
						'id' => $prefix . 'home_servizi_manuali',
						'type'    => 'custom_attached_posts',
						'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
						'options' => array(
								'show_thumbnails' => false, // Show thumbnails on the left
								'filter_boxes'    => true, // Show a text box for filtering the results
								'query_args'      => array(
										'posts_per_page' => -1,
										'post_type'      => 'servizio',
								), // override the get_posts args
						),
						'attributes' => array(
								'data-conditional-id' => $prefix . 'home_is_selezione_automatica_servizi',
								'data-conditional-value' => "false",
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
		'tab_title'    => __('Laboratorio', 'design_laboratori_italia'),
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',

	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$main_options = new_cmb2_box( $args );

	$scuola_landing_url = dli_get_template_page_url("page-templates/il-laboratorio.php");

	$main_options->add_field( array(
			'id' => $prefix . 'scuola_istruzioni',
			'name'        => __( 'Sezione Il Laboratorio', 'design_laboratori_italia' ),
			'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$scuola_landing_url.'">la pagina di panoramica della Laboratorio</a>.' , 'design_laboratori_italia' ),
			'type' => 'title',
			) );


		$main_options->add_field( array(
		'id' => $prefix . 'immagine',
		'name'        => __( 'Immagine', 'design_laboratori_italia' ),
		'desc' => __( 'Immagine di presentazione della scuola' , 'design_laboratori_italia' ),
		'type'    => 'file',
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
		'text'    => array(
			'add_upload_file_text' => 'Aggiungi Immagine' // Change upload button text. Default: "Add or Upload File"
		),
		// query_args are passed to wp.media's library query.
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'preview_size' => 'medium', // Image size to use when previewing in the admin.
		'attributes'    => array(
			'required'    => 'required'
		),
	) );

	$main_options->add_field( array(
		'id' => $prefix . 'citazione',
			'name'        => __( 'Citazione', 'design_laboratori_italia' ),
		'desc' => __( 'Breve (compresa tra 70 e 140 caratteri spazi inclusi) frase identificativa della missione o della identità dell\'istituto . Es. "Da sempre un punto di riferimento per la formazione degli studenti a Roma" Es. "La scuola è una comunità: costruiamo insieme il futuro". Link alla pagina di presentazione della missione della scuola' , 'design_laboratori_italia' ),
		'type' => 'textarea',
		'attributes'    => array(
						'maxlength'  => '140',
			'minlength'  => '70'
		),
	) );

	$main_options->add_field( array(
		'name'        => __( 'La Storia', 'design_laboratori_italia' ),
		'desc' => __('Timeline della Laboratorio', 'design_laboratori_italia' ),
		'type' => 'title',
		'id' => $prefix . 'prefisso_storia',
	) );

	$main_options->add_field( array(
		'id' => $prefix . 'descrizione_scuola',
		'title'        => __( 'La Storia', 'design_laboratori_italia' ),
		'name'        => __( 'Descrizione', 'design_laboratori_italia' ),
		'desc' => __( 'Descrizione introduttiva della timeline' , 'design_laboratori_italia' ),
		'type' => 'textarea_small',
	) );

	$timeline_group_id = $main_options->add_field( array(
		'id'           => $prefix . 'timeline',
		'type'        => 'group',
		'name'        => 'Timeline',
		'desc' => __( 'Ogni fase è costruita attraverso data, titolo (max 60 caratteri) e descrizione breve (max 140 caratteri). NB: Se è un istituto comprende le tappe dei laboratori che ne fanno parte' , 'design_laboratori_italia' ),
		'repeatable'  => true,
		'options'     => array(
			'group_title'   => __( 'Fase {#}', 'design_laboratori_italia' ),
			'add_button'    => __( 'Aggiungi un elemento', 'design_laboratori_italia' ),
			'remove_button' => __( 'Rimuovi l\'elemento ', 'design_laboratori_italia' ),
			'sortable'      => true,  // Allow changing the order of repeated groups.
		),
	) );

	$main_options->add_group_field( $timeline_group_id, array(
		'id' => $prefix . 'data_timeline',
		'name'        => __( 'Data', 'design_laboratori_italia' ),
		'type' => 'text_date_timestamp',
		'date_format' => 'd-m-Y',
		'data-datepicker' => json_encode( array(
			'yearRange' => '-100:+0',
		) ),
	) );

	$main_options->add_group_field( $timeline_group_id, array(
		'id' => $prefix . 'titolo_timeline',
		'name'        => __( 'Titolo', 'design_laboratori_italia' ),
		'type' => 'text',
	) );

	$main_options->add_group_field( $timeline_group_id, array(
		'id' => $prefix . 'descrizione_timeline',
		'name'        => __( 'Descrizione', 'design_laboratori_italia' ),
		'type' => 'textarea_small',
	) );

	$main_options->add_field( array(
		'name'        => __( 'Le Strutture', 'design_laboratori_italia' ),
		'desc' => __('Organizzazione scolastica', 'design_laboratori_italia' ),
		'type' => 'title',
		'id' => $prefix . 'prefisso_strutture_scuola',
	) );

	$main_options->add_field( array(
		'id' => $prefix . 'descrizione_strutture',
		'title'        => __( 'Le Strutture', 'design_laboratori_italia' ),
		'name'        => __( 'Descrizione', 'design_laboratori_italia' ),
		'desc' => __( 'Es: Una scuola è fatta di persone. Ecco come siamo organizzati e come possiamo entrare in contatto' , 'design_laboratori_italia' ),
		'type' => 'textarea_small',
	) );

	$main_options->add_field( array(
		'id' => $prefix . 'link_strutture_evidenza',
		'name'    => __( 'Le Strutture', 'design_laboratori_italia' ),
		'desc' => __( 'Seleziona qui le principali strutture organizzative (es: La Dirigenza, La segreteria, etc)  <a href="post-new.php?post_type=struttura">Qui puoi creare una struttura.</a> ' , 'design_laboratori_italia' ),
		'type'    => 'custom_attached_posts',
		'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
		'options' => array(
			'show_thumbnails' => false, // Show thumbnails on the left
			'filter_boxes'    => true, // Show a text box for filtering the results
			'query_args'      => array(
				'posts_per_page' => -1,
				'post_type'      => 'struttura',
			), // override the get_posts args
		),
	) );

	$main_options->add_field( array(
		'name'        => __( 'I Luoghi', 'design_laboratori_italia' ),
		'desc' => __('Immagini dei luoghi della Laboratorio', 'design_laboratori_italia' ),
		'type' => 'title',
		'id' => $prefix . 'prefisso_luoghi_storia',
	) );

	$main_options->add_field( array(
		'id' => $prefix . 'descrizione_gallery_luoghi',
		'title'        => __( 'I Luoghi', 'design_laboratori_italia' ),
		'name'        => __( 'Descrizione', 'design_laboratori_italia' ),
		'desc' => __( 'Es: Testo descrittivo dei luoghi della scuola' , 'design_laboratori_italia' ),
		'type' => 'textarea_small',
	) );

	$main_options->add_field( array(
		'desc' => 'Una selezione di circa 5 immagini significative della scuola/istituto',
		'id'           => $prefix . 'immagini_luoghi',
		'name'        => __( 'Gallery', 'design_laboratori_italia' ),
		'type' => 'file_list',
		// 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		'query_args' => array( 'type' => 'image' ), // Only images attachment
		// Optional, override default text strings
		'text' => array(
			'add_upload_files_text' => 'Aggiungi', // default: "Add or Upload Files"
			'remove_image_text' => 'Rimuovi', // default: "Remove Image"
			'file_text' => 'File:', // default: "File:"
			'file_download_text' => 'Download', // default: "Download"
			'remove_text' => 'Elimina', // default: "Remove"
		),
	) );

	$main_options->add_field(
		array(
			'name'        => __( 'I numeri del Laboratorio', 'design_laboratori_italia' ),
			'desc' => __('Inserisci il numero di studenti e classi della Laboratorio', 'design_laboratori_italia' ),
			'type' => 'title',
			'id' => $prefix . 'prefisso_numeri',
		)
	);

	$main_options->add_field( array(
		'id' => $prefix . 'numeri_descrizione',
		'name'        => __( 'Descrizione', 'design_laboratori_italia' ),
		'desc' => __( 'Breve descrizione (140 caratteri) *' , 'design_laboratori_italia' ),
		'type' => 'textarea_small',
		'attributes'    => array(
			'required'    => 'required',
						'maxlength' =>  140
		),
	) );

	$main_options->add_field(
		array(
			'id' => $prefix . 'allievi',
			'name'        => __( 'Allievi *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di allievi del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	$main_options->add_field(
		array(
			'id' => $prefix . 'phd',
			'name'        => __( 'Phd *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di phd del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	$main_options->add_field(
		array(
			'id' => $prefix . 'professori',
			'name'        => __( 'Professori *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di professori del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	$main_options->add_field(
		array(
			'id' => $prefix . 'ricercatori',
			'name'        => __( 'Ricercatori *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di ricercatori del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	$main_options->add_field(
		array(
			'id' => $prefix . 'progetti',
			'name'        => __( 'Progetti *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di progetti del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	$main_options->add_field(
		array(
			'id' => $prefix . 'pubblicazioni',
			'name'        => __( 'Pubblicazioni *', 'design_laboratori_italia' ),
			'desc' => __( 'Numero di pubblicazioni del laboratorio' , 'design_laboratori_italia' ),
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
				'required'    => 'required'
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		)
	);

	/**
	* 5 - Registers options page "Servizi".
	*/
	// Intestazione della sezione.
	$args = array(
		'id'           => 'dli_options_servizi',
		'title'        => esc_html__( 'I Servizi', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'servizi',
		'parent_slug'  => 'dli_options',
		'capability'   => 'manage_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __('Servizi', 'design_laboratori_italia'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}
	$servizi_options = new_cmb2_box( $args );
	$servizi_landing_url = dli_get_template_page_url( 'page-templates/servizi.php' );
	$servizi_options->add_field(
		array(
		'id'   => $prefix . 'servizi_istruzioni',
		'name' => __( 'Sezione I Servizi', 'design_laboratori_italia' ),
		'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$servizi_landing_url.'">la pagina di panoramica dei Servizi</a>.' , 'design_laboratori_italia' ),
		'type' => 'title',
		)
	);
	// Campo descrizione della sezione.
	$servizi_options->add_field(
		array(
			'id'         => $prefix . 'testo_servizi',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "I servizi offerti dal liceo scientifico Enriques dedicati a tutti i genitori, studenti, personale ATA e docenti"' , 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140'
			),
		)
	);
	// Campo filtro contenuto mostrato per tipologia.
	$servizi_options->add_field(
		array(
			'name'       => __('Tipologie Servizi', 'design_laboratori_italia' ),
			'desc'       => __( 'Servizi aggregati per tipologie. Seleziona le tipologie da mostrare. ', 'design_laboratori_italia' ),
			'id'         => $prefix . 'tipologie_servizi',
			'type'       => 'pw_multiselect',
			'options'    => dli_get_tipologia_servizi_options(),
			'attributes' => array(
				'placeholder' =>  __( 'Seleziona e ordina le tipologie di servizi da mostrare nella HomePage di sezione', 'design_laboratori_italia' ),
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
				'capability'    => 'manage_options',
				'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __('Novità', 'design_laboratori_italia'),	);

	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'dli_options_display_with_tabs';
	}

	$notizie_options = new_cmb2_box( $args );

		$notizie_landing_url = dli_get_template_page_url("page-templates/notizie.php");
		$notizie_options->add_field( array(
				'id' => $prefix . 'notizie_istruzioni',
				'name'        => __( 'Sezione Le Novità', 'design_laboratori_italia' ),
				'desc' => __( 'Inserisci qui le informazioni utili a popolare <a href="'.$notizie_landing_url.'">la pagina di panoramica delle Novità</a>.' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$notizie_options->add_field( array(
		'id' => $prefix . 'testo_notizie',
		'name'        => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
		'desc' => __( 'es: "Le notizie del liceo scientifico Enriques dedicate a tutti i genitori, studenti, personale ATA e docenti"' , 'design_laboratori_italia' ),
		'type' => 'textarea',
		'attributes'    => array(
			'maxlength'  => '140'
		),
	) );

	$notizie_options->add_field( array(
			'name'       => __('Tipologie Articoli', 'design_laboratori_italia' ),
			'desc' => __( 'Articoli aggregati per tipologie (es: articoli, circolari, notizie), . Seleziona le tipologie da mostrare. ', 'design_laboratori_italia' ),
			'id' => $prefix . 'tipologie_notizie',
			'type'    => 'pw_multiselect',
			'options' => dli_get_tipologia_articoli_options(),
			'attributes' => array(
				'placeholder' =>  __( 'Seleziona e ordina le tipologie di articoli da mostrare nella HomePage di sezione', 'design_laboratori_italia' ),
			),
		)
	);

	/**
	* 7 - Registers options page "Persone".
	*/
	$args = array(
		'id'           => 'dli_options_persone',
		'title'        => esc_html__( 'Persone', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'persone',
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'capability'    => 'manage_options',
		'tab_title'    => __('Persone', 'design_laboratori_italia'),	);
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


	$persone_options->add_field( array(
			'id' => $prefix . 'testo_sezione_persone',
			'name'        => __( 'Descrizione Sezione Persone', 'design_laboratori_italia' ),
			'desc' => __( 'es: "Le persone del liceo scientifico xxx, insegnanti, personale ATA e docenti' , 'design_laboratori_italia' ),
			'type' => 'textarea',
			'attributes'    => array(
					'maxlength'  => '140'
			),
	) );

	$persone_options->add_field( array(
			'id' => $prefix . 'strutture_persone',
			'name'        => __( 'Seleziona e ordina le strutture organizzative a cui fanno capo le persone', 'design_laboratori_italia' ),
			'desc' => __( 'Seleziona le strutture organizzative di cui vuoi mostrare le persone. <a href="'.$persone_landing_url.'">La pagina con la lista delle persone sarà popolata automaticamente</a>. ' , 'design_laboratori_italia' ),
			'type'    => 'pw_multiselect',
			'options' => dli_get_strutture_options(),
			'attributes' => array(
					'placeholder' =>  __( 'Seleziona e ordina le strutture di cui mostrare le persone', 'design_laboratori_italia' ),
			),
	) );

	/**
	* 8 - Registers options page "Pubblicazioni".
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
		'tab_title'    => __('Pubblicazioni', 'design_laboratori_italia'),
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
	// Campo descrizione della sezione.
	$pubblicazioni_options->add_field(
		array(
			'id'         => $prefix . 'testo_pubblicazioni',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Pubblicazioni dei membri del Laboratorio.' , 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140'
			),
		)
	);

	/**
	* 9 - Registers options page "Progetti".
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
		'tab_title'    => __('Progetti', 'design_laboratori_italia'),
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
	// Campo descrizione della sezione.
	$progetti_options->add_field(
		array(
			'id'         => $prefix . 'testo_progetti',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "I progetti del Laboratorio"' , 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140'
			),
		)
	);

	/**
	* 10 - Registers options page "Attività di ricerca".
	*/
	$args = array(
		'id'           => 'dli_options_ricerca',
		'title'        => esc_html__( 'Indirizzi di ricerca', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'ricerca',
		'parent_slug'  => 'dli_options',
		'capability'   => 'manage_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __('Indirizzi di ricerca', 'design_laboratori_italia'),
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
	// Campo descrizione della sezione.
	$ricerca_options->add_field(
		array(
			'id'         => $prefix . 'testo_ricerca',
			'name'       => __( 'Descrizione Sezione', 'design_laboratori_italia' ),
			'desc'       => __( 'es: "Gli indirizzi di ricerca del Laboratorio"' , 'design_laboratori_italia' ),
			'type'       => 'textarea',
			'attributes' => array(
				'maxlength' => '140'
			),
		)
	);




	/**
	* 11 - Registers options page "Attività di ricerca".
	*/
		$args = array(
				'id'           => 'dli_options_luoghi',
				'title'        => esc_html__( 'Luoghi', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'luoghi',
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'capability'    => 'manage_options',
				'tab_title'    => __('Luoghi', 'design_laboratori_italia'),	);

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
				'desc' => __( 'es: "Questi i luoghi della scuola"' , 'design_laboratori_italia' ),
				'type' => 'textarea',
				'attributes'    => array(
						'maxlength'  => '140'
				),
		) );

		$luoghi_options->add_field( array(
				'id' => $prefix . 'strutture_luoghi',
				'name'        => __( 'Seleziona e ordina le tipologie di luoghi  da mostrare', 'design_laboratori_italia' ),
				'desc' => __( 'Seleziona le tipologie di luoghi che vuoi mostrare. ' , 'design_laboratori_italia' ),
				'type'    => 'pw_multiselect',
				'options' => dli_get_tipologie_luoghi_options(),
				'attributes' => array(
						'placeholder' =>  __( ' Seleziona e ordina le tipologie di luoghi da mostrare nella pagina Luoghi', 'design_laboratori_italia' ),
				),
		) );

		$luoghi_options->add_field(array(
				'id' => $prefix . 'posizione_mappa',
				'name' => __('Visualizza mappa', 'design_laboratori_italia'),
				'desc' => __('Seleziona <b>No</b> per visualizzare la mappa in fondo alla pagina dopo l\'elenco delle strutture.', 'design_laboratori_italia'),
				'type' => 'radio_inline',
				'default' => 'true',
				'options' => array(
						'true' => __('Si', 'design_laboratori_italia'),
						'false' => __('No', 'design_laboratori_italia'),
				),
		));

		$luoghi_options->add_field( array(
				'id' => $prefix . 'luogho_istruzioni',
				'name'        => __( 'Dettaglio Luogo', 'design_laboratori_italia' ),
				'desc' => __( 'Specifica le opzioni di visualizzazione per il dettaglio del singolo luogo.' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$luoghi_options->add_field( array(
				'id' => $prefix . 'excerpt_length',
				'name'        => __( 'Testo elementi di interesse *', 'design_laboratori_italia' ),
				'desc' => __('Specificare la lunghezzadi default, in caratteri, per il testo descrittivo degli elmenti di interesse oltre la quale il testo verrà nascosto', 'design_laboratori_italia' ),
				'type' => 'text_small',
				'attributes' => array(
						'type' => 'number',
						'pattern' => '\d*',
						'required'    => 'required',
						'min' => 60
				),
				'sanitization_cb' => 'absint',
				'escape_cb'       => 'absint',
		) );

	/**
	* 12 - Registers options page "Servizi esterni".
	*/
		$args = array(
				'id'           => 'dli_login_menu',
				'title'        => esc_html__( 'Login', 'design_laboratori_italia' ),
				'object_types' => array( 'options-page' ),
				'option_key'   => 'login',
				'tab_title'    => __('Servizi esterni', 'design_laboratori_italia'),
				'parent_slug'  => 'dli_options',
				'tab_group'    => 'dli_options',
				'capability'    => 'manage_options',
		);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
				$args['display_cb'] = 'dli_options_display_with_tabs';
		}

		$login_options = new_cmb2_box( $args );

		$login_options->add_field( array(
				'id' => $prefix . 'login_istruzioni',
				'name'        => __( 'Servizi esterni: informazioni di login', 'design_laboratori_italia' ),
				'desc' => __( 'Area di configurazione dei link di login ai servizi esterni, da mostrare nella maschera di login .' , 'design_laboratori_italia' ),
				'type' => 'title',
		) );

		$login_options->add_field( array(
				'id' => $prefix . 'login_messaggio',
				'name' => 'Testo da mostrare nell\'area di login',
				'type' => 'textarea',
				'default' => 'Da qui puoi accedere ai diversi servizi della scuola che richiedono una autenticazione personale.',
		) );


		$timeline_group_id = $login_options->add_field( array(
				'id'           => $prefix . 'link_esterni',
				'type'        => 'group',
				'name'        => 'Link servizi esterni',
				'desc' => __( 'Definisci tutti i servizi esterni che vuoi mostrare agli utenti in fase di login.' , 'design_laboratori_italia' ),
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => __( 'Link {#}', 'design_laboratori_italia' ),
						'add_button'    => __( 'Aggiungi un elemento', 'design_laboratori_italia' ),
						'remove_button' => __( 'Rimuovi l\'elemento ', 'design_laboratori_italia' ),
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$login_options->add_group_field( $timeline_group_id, array(
				'id' => $prefix . 'nome_link',
				'name'        => __( 'Nome Servizio', 'design_laboratori_italia' ),
				'type' => 'text',
		) );

		$login_options->add_group_field( $timeline_group_id, array(
				'id' => $prefix . 'url_link',
				'name'        => __( 'Link Servizio', 'design_laboratori_italia' ),
				'type' => 'text_url',
		) );


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
				'tab_title'    => __('Socialmedia', 'design_laboratori_italia'),	);

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
				'name' => __('Mostra le icone social', 'design_laboratori_italia'),
				'desc' => __('Abilita la visualizzazione dei socialmedia nell\'header e nel footer della pagina.', 'design_laboratori_italia'),
				'type' => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'true' => __('Si', 'design_laboratori_italia'),
						'false' => __('No', 'design_laboratori_italia'),
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


	/**
	* 14 - Registers options page "Altro".
	*/

	$args = array(
		'id'           => 'dli_setup_menu',
		'title'        => esc_html__( 'Altro', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'setup',
		'tab_title'    => __('Altro', 'design_laboratori_italia'),
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
				'name'    => __('Attiva la newsletter', 'design_laboratori_italia'),
				'type'    => 'radio_inline',
				'default' => 'false',
				'options' => array(
						'true'  => __('Si', 'design_laboratori_italia'),
						'false' => __('No', 'design_laboratori_italia'),
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
				wp_enqueue_script( 'dli_options_dialog', get_stylesheet_directory_uri() . '/inc/admin-js/options.js', array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'), '1.0', true );
		}
}
add_action( 'admin_enqueue_scripts', 'dli_options_assets' );
