<?php
/**
 * Definition of the IRIS Manager used to import IRIS patents.
 *
 * @package Design_Laboratori_Italia
 */

require_once 'class-base-importer.php';

define(
	'DLI_LEGAL_STATUS_EN',
	array(
		'Abbandonato' => 'Abandoned',
		'Ceduto'      => 'Transferred',
		'Concesso'    => 'Granted',
		'Licenza'     => 'Licence',
		'Pending'     => 'Pending',
		'Terminato'   => 'Ended',
	)
);

class DLI_IrisPatentImporter extends DLI_BaseImporter {

	public function __construct() {
		$this->importer_name    = 'Iris Patent Importer';
		$this->job_name         = 'dli_iris_patent_import_job';
		$this->endpoint         = '/iris-patent-import';
		$this->post_type        = PATENT_POST_TYPE;
		$this->debug_enabled    = ( dli_get_option( 'iris_debug_enabled', 'iris' ) === 'true' );
		$this->module_enabled   = ( dli_get_option( 'iris_brevetti_enabled', 'iris' ) === 'true' );
		$this->schedule_enabled = ( dli_get_option( 'iris_brevetti_schedule', 'iris' ) === 'true' );
		$this->schedule_type    = dli_get_option( 'iris_brevetti_schedule', 'iris' );
		// Schedule patent import.
		$this->manage_import_job();
	}

	public function import( ) {
		// $request
		$code = 200;
		$data = array();
		// Verifica modulo abilitato.
		if ( ! $this->module_enabled ) {
			// Modulo disabilitato.
			return $this->send_response( 500, MSG_MODULE_DISABLED, $data );
		}
	
		// Recupero parametri di configurazione.
		$conf = array(
			'ws_url'        => dli_get_option( 'iris_brevetti_url', 'iris' ),
			'username'      => dli_get_option( 'iris_brevetti_username', 'iris' ),
			'password'      => dli_get_option( 'iris_brevetti_password', 'iris' ),
			'import_type'   => dli_get_option( 'iris_brevetti_import_type', 'iris' ),
			'import_action' => dli_get_option( 'iris_brevetti_item_existent_action', 'iris' ),
		);

		if ( $conf['ws_url'] && $conf['username'] && $conf['password'] ) {
			try {
				$data = $this->execute_import( $conf );
				// Importazione completata: ritorno i risultati in formato json.
				return $this->send_response( $code, MSG_IMPORT_SUCCESSFUL, $data );
			} catch ( Exception $e ) {
				// Errore nell'importazione.
				return $this->send_response( 500, $e->getMessage(), $data );
			}
		} else {
			// Modulo non configurato correttamente.
			return $this->send_response( 500, MSG_MODULE_NOT_CONFIGURED, $data );
		}
	}

	public function get_data_to_import( $conf ) {
		$ws_url   = $conf['ws_url'];
		$username = $conf['username'];
		$password = $conf['password'];
		$data = array();
		// Recupero JSON dati.
		$auth = base64_encode("$username:$password");
		$args = array(
			'headers' => array(
				'Authorization' => "Basic $auth"
			)
		);
		// Invocazione dell'endpoint.
		$response = wp_remote_get($ws_url, $args);
		// Controllo della risposta
		if ( is_wp_error($response ) ) {
			// Errore invocando il web service.
			throw new Exception( $response->get_error_message());
		}
		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code != 200 ) {
			$error_msg = "Errore: codice di stato $status_code invocando il web service.";
			throw new Exception( $error_msg );
		} else {
			// Recupera dati dalla risposta.
			$body = wp_remote_retrieve_body($response);
			// Se la codifica della risposta non è UTF-8, converti in UTF-8.
			if ( ! mb_check_encoding($body, 'UTF-8' ) ) {
				$body = mb_convert_encoding($body, 'UTF-8', 'auto' );
			}
			if ( $body ){
				$data = json_decode($body);
			}
		}
		return $data;
	}

	private function execute_import( $conf = array() ): array {
		$this->log_string("*** INIZIO importazione da IRIS (brevetti) ***");
		// Invocazione del web service per recuperare i dati da Iris.
		$data    = $this->get_data_to_import( $conf );
		$results = array();

		// Loop di importazione.
		$counter         = 0;
		$errors          = 0;
		$simulated_items = 0;
		$added_items     = 0;
		$updated_items   = 0;
		$ignored_items   = 0;

		foreach ( $data as $item ){
			$counter++;
			$item_pid   = $item->pid;
			$item_title = $item->displayValue;

			if ( $conf['import_type'] === 'dryrun' ){
				// Importazione dry run.
				array_push(
					$results,
					MSG_IMPORT_DRY_RUN . $item_pid . ' - ' . $item_title,
				);
				$simulated_items++;
			} else {
				// Importazione effettiva.
				try{
					$updated    = false;
					$ignored    = false;
					// Creazione del contenuto.
					$item_code = $this->create_wp_content(
						$item,
						$conf,
						$updated,
						$ignored
					);
					// Gestione del risultato.
					if ( $item_code !=0 ){
						$this->_process_result(
							$results,
							$item_pid . ' - ' . $item_code,
							$item->displayValue,
							$updated,
							$ignored,
							$added_items,
							$updated_items,
							$ignored_items,
						);
					}
					// Creazione del contenuto corrispondente in inglese.
					$item_code_en = $this->_translate_content( $item_code, $item, $conf, 'en' );
					// Gestione del risultato.
					if ( $item_code_en !=0 ){
						$this->_process_result(
							$results,
							$item_pid . ' - ' . $item_code_en,
							$item->displayValue_en,
							$updated,
							$ignored,
							$added_items,
							$updated_items,
							$ignored_items,
						);
					}
				} catch ( Exception $e ) {
					array_push(
						$results,
						MSG_ERROR_IMPORTING_ITEM . $item_title . ' - ' . $e->getMessage(),
					);
					$errors++;
				}
			}
		}
		$this->log_string( "*** FINE importazione da IRIS (brevetti) ***");
		return $results;
	}
	private function _process_result( &$results, $itemCode, $itemTitle, $updated, $ignored,  &$added_items, &$updated_items, &$ignored_items ) {
		if ( $updated ) {
			array_push(
				$results,
				MSG_UPDATED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			$updated_items++;
		} else if ( $ignored ) {
			array_push(
						$results,
						MSG_IGNORED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			$ignored_items++;
		} else {
			array_push(
				$results,
				MSG_IMPORTED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			$added_items++;
		}
	}

	private function create_wp_content( $item, $conf, &$updated, &$ignored, $lang='it' ): int {
		$post_name    = dli_generate_slug( $item->displayValue );
		$post_content = $item->abstract ?? '.';

		$new_content  = array(
			'post_type'    => $this->post_type,
			'post_name'    => $post_name,
			'post_title'   => $item->displayValue,
			'post_content' => $post_content,
			'post_status'  => 'draft',
			'post_parent'  => 0,
		);
		$update_content = ( $conf['import_action'] === 'update' ) ? true : false;
		// Verifico l'esistenza dell'oggetto su WordPress.
		$pid  = $this->get_wp_content_id( $item );
		$contents = dli_get_post_translations( $pid );

		if ( ! isset( $contents[$lang] ) ) {
			$post_id = wp_insert_post( $new_content );
			$updated = false;
			// Aggiorna campi personalizzati.
			$this->update_custom_fields( $post_id, $item );
			// La lingua impostata per l'oggetto importato è l'italiano.
			dli_set_post_language( $post_id, $lang );
		} else {
			$post_id = $contents[$lang];
			if ( $update_content ) {
				// Aggiorna i campi del post.
				$pars = array(
					'ID'           => $post_id,
					'post_content' => $new_content['post_content'],
				);
				wp_update_post( $pars, true );
				// Aggiorna titolo.
				$this->update_title( $post_id, $item->displayValue );
				// Aggiorna campi personalizzati.
				$this->update_custom_fields( $post_id, $item );
				$updated = true;
			} else {
				$ignored = true;
			}
		}
		return $post_id;
	}

	private function _translate_content( $post_id, $item, $conf, $lang='en' ): int {
		$traslate_content = $item->displayValue_en ? true : false;
		$new_content_en   = null;
		$post_id_en       = 0;

		// Si crea la versione inglese solo se c'è il titolo in inglese.
		if ( $traslate_content ) {
			$post_name_en    = dli_generate_slug( $item->displayValue_en );
			$post_content_en = $item->abstract_en ?? '.';
			$post_title_en   = $item->displayValue_en;
			$contents        = dli_get_post_translations( $post_id );

			if ( ! isset( $contents[$lang] ) ) {
				// Crea nuova versione in inglese.
				$new_content_en = array(
					'post_type'    => $this->post_type,
					'post_name'    => $post_name_en,
					'post_title'   => $post_title_en,
					'post_content' => $post_content_en,
					'post_status'  => 'draft',
					'post_parent'  => 0,
				);

				// Associa versione italiana e versione inglese;
				$post_id_en = wp_insert_post( $new_content_en );

				// Assign the EN language to the page.
				dli_set_post_language( $post_id_en, $lang );

				// Associate it and en translations.
				$related_posts = array(
				'it' => $post_id,
				'en' => $post_id_en,
				);
				dli_save_post_translations( $related_posts );
				// Aggiorna campi personalizzati.
				$this->update_custom_fields( $post_id_en, $item, 'en' );
			} else {
				// Aggiorna versione esistente.
				$post_id_en     = $contents[$lang];
				$update_content = ( $conf['import_action'] === 'update' ) ? true : false;
				if ( $update_content ) {
					// Aggiorna i campi del post.
					$pars = array(
						'ID'           => $post_id_en,
						'post_content' => $post_content_en,
					);
					wp_update_post( $pars, true );
					// Aggiorna titolo.
					$this->update_title( $post_id_en, $post_title_en );
					// Aggiorna campi personalizzati.
					$this->update_custom_fields( $post_id_en, $item, 'en' );
				}
			}
		}
		return $post_id_en;
	}

	private function update_title( $post_id, $title ){
		// Dati da aggiornare
		$updated_post = array(
			'ID'         => $post_id,
			'post_title' => $title,
		);
		// Aggiorna il post.
		wp_update_post( $updated_post, true );
	}

	private function update_custom_fields( $post_id, $item, $lang= 'it' ) {

		// Aggiornamento campi generici.
		$this->_update_common_fields( $post_id, $item );

		// Aggiornamento campi differenti per lingua.
		if ( $lang === 'it' ) {
			$this->_update_fields_it( $post_id, $item );
		} else {
			$this->_update_fields_en( $post_id, $item );
		}

	}

	private function _update_common_fields( $post_id, $item ){
		// Codice Brevetto (codice_brevetto).
		$item_code = $item->pid;
		dli_update_field( 'codice_brevetto', $item_code, $post_id );

		// Numero Deposito (numero_deposito).
		dli_update_field( 'numero_deposito', $item->applicationNumber, $post_id );
		if ( $item->id_family ) {
			// Id famiglia (id_famiglia).
			dli_update_field( 'id_famiglia', $item->id_family, $post_id );
		}

		// Famiglia.
		if ( $item->family ) {
			try {
				dli_update_field( 'famiglia', json_encode( $item->family ), $post_id );
			} catch ( Exception $e ) {
				$this->log_string( '*** Error: Msg:' . $e->getMessage() . ' - Code: ' . $e->getCode() );
			}
		}

		if ( $item->deposit_date ) {
			// Data deposito (data_deposito).
			$dp_date_str  = $item->deposit_date;
			$deposit_date = dli_format_date_from_format( 'd-m-Y', $dp_date_str, 'Ymd' );
			if ( $deposit_date ) {
				dli_update_field( 'data_deposito', $deposit_date, $post_id );
			}
			// Anno Deposito (anno_deposito).
			$deposit_year = dli_format_date_from_format( 'd-m-Y', $dp_date_str, 'Y' );
			if ( $deposit_year ) {
				dli_update_field( 'anno_deposito', $deposit_year, $post_id );
			}
		}

		// isPriority
		if ( $item->isPriority ) {
			dli_update_field( 'prioritario', true, $post_id );
		} else {
			dli_update_field( 'prioritario', false, $post_id );
		}

		// Inventori referenti e non (inventori_referenti, inventori).
		$inv_ref = array();
		$inv     = array();
		if ( $item->ownerSet && count( $item->ownerSet ) > 0) {
			foreach ( $item->ownerSet as $i ) {
				$inv_name = $i->person->firstName . ' ' . $i->person->lastName;
				if ( $i->role->description === 'Titolare' ) {
					array_push( $inv, trim( $inv_name ) );
				} else {
					array_push( $inv_ref, trim( $inv_name ) );
				}
			}
		}
		$inv_ref_str = implode( ', ', $inv_ref );
		$inv_str     = implode( ', ', $inv );
		dli_update_field( 'inventori', $inv_ref_str, $post_id );
		dli_update_field( 'inventori_referenti', $inv_str, $post_id );

		// Titolari (titolari).
		$tit = array();
		if ( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet && count( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet ) > 0) {
			foreach ( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet as $o ) {
				$tit_name = $o->organizationUnit->description;
				if ( $tit_name ) {
					array_push( $tit, trim( $tit_name ) );
				}
				
			}
		}
		$tit_str = implode( ', ', $tit );
		dli_update_field( 'titolari', $tit_str, $post_id );
	}

	private function _update_fields_it( $post_id, $item ){
		$lang='it';
		// Stato Legale (stato_legale).
		dli_update_field( 'stato_legale', $item->legal_status, $post_id );
		// Aree tematiche (area_tematica).
		$thematic_area_list_str = $item->thematic_area_list;
		if ( $thematic_area_list_str ) {
			$thematic_areas = explode( '###', $thematic_area_list_str );
			foreach ( $thematic_areas as $term ) {
				// Controllo esistenza tassonomia.
				// Creo tassonomia.
				$term_item = term_exists( $term, THEMATIC_AREA_TAXONOMY );
				if ( $term_item ) {
					$term_id = $term_item['term_id'];
				} else {
					$new_term = wp_insert_term( $term, THEMATIC_AREA_TAXONOMY );
					$term_id  = $new_term['term_id'];
				}
				dli_set_term_language( $term_id, $lang );
				// Associo la tassonomia al contenuto.
				wp_set_post_terms( $post_id, array( $term_id ), THEMATIC_AREA_TAXONOMY, false );
			}
		}
	}

	private function _update_fields_en( $post_id, $item ){
		$lang='en';
		// Stato Legale (stato_legale).
		$legal_status_en = isset( DLI_LEGAL_STATUS_EN[$item->legal_status] ) ? DLI_LEGAL_STATUS_EN[$item->legal_status] : '';
		if ( $legal_status_en !== '' ) {
			dli_update_field( 'stato_legale', $legal_status_en, $post_id );
		}

		// Aree tematiche (area_tematica).
		$thematic_area_list_str = $item->thematic_area_list;
		if ( $thematic_area_list_str ) {
			$thematic_areas = explode( '###', $thematic_area_list_str );
			foreach ( $thematic_areas as $term ) {
				// Controllo esistenza tassonomia.
				// Creo tassonomia.
				$term_item  = term_exists( $term, THEMATIC_AREA_TAXONOMY );
				$term_id    = isset( $term_item['term_id'] ) ? $term_item['term_id'] : 0;
				$trans      = $term_id ? dli_get_term_translations( $term_id ) : array();
				$term_id_en = isset( $trans[$lang] ) ? $trans[$lang] : 0;
				if ( $term_id_en ) {
					// Associo la tassonomia al contenuto.
					wp_set_post_terms( $post_id, array( $term_id_en ), THEMATIC_AREA_TAXONOMY, false );
				}
			}
		}
	}

	private function get_wp_content_id( $item ){
		$args = array(
			'post_type' => $this->post_type,
			'meta_query' => array(
				array(
					'key'     => 'codice_brevetto',
					'value'   => $item->pid,
					'compare' => '='
				)
			),
			'posts_per_page' => 1,
			'post_status'    => 'any',
		);
		$query = new WP_Query( $args );
		return $query->found_posts ? $query->posts[0]->ID : 0;
	}



	// *** Funzioni di utilità dedicate *** //

}
