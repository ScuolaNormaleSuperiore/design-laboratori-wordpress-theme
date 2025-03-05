<?php
/**
 * Definition of the Indico Manager used to import Indico Events.
 *
 * @package Design_Laboratori_Italia
 */

require_once 'class-base-importer.php';

define( 'INDICO_API_SUFFIX_CATEGORY', '/export/categ' );

class DLI_IndicoImporter extends DLI_BaseImporter {

	public function __construct() {
		$this->importer_name    = 'Indico Event Importer';
		$this->job_name         = 'dli_indico_import_job';
		$this->endpoint         = '/indico-import';
		$this->post_type        = EVENT_POST_TYPE;
		$this->debug_enabled    = ( dli_get_option( 'indico_debug_enabled', 'indico' ) === 'true' );
		$this->module_enabled   = ( dli_get_option( 'indico_enabled', 'indico' ) === 'true' );
		$this->schedule_enabled = ( dli_get_option( 'indico_schedule', 'indico' ) === 'true' );
		$this->schedule_type    = dli_get_option( 'indico_schedule', 'indico' );
		// Schedule indico events import.
		$this->manage_import_job();
	}

	public function import() {
		// Gestione parametri.
		// WP_REST_Request $request.
		// $param1 = $request->get_param('param1') .
		$code = 200;
		$data = array();
		// Verifica modulo abilitato.
		$module_enabled = dli_get_option( 'indico_enabled', 'indico' );
		if ( $module_enabled && $module_enabled === 'true' ) {
			// Recupero parametri di configurazione.
			$criteria  = dli_get_option( 'indico_import_criteria', 'indico' );
			$start_date = $this->_start_date_from_criteria( $criteria );
			$conf = array(
				'base_url'    => dli_get_option( 'indico_baseurl', 'indico' ),
				'token'       => dli_get_option( 'indico_token_api', 'indico' ),
				'category'    => dli_get_option( 'indico_category', 'indico' ),
				'keywords'    => dli_get_option( 'indico_keywords', 'indico' ),
				'import_type' => dli_get_option( 'indico_import_type', 'indico' ),
				'post_status' => dli_get_option( 'indico_imp_item_status', 'indico' ),
				'criteria'    => dli_get_option( 'indico_import_criteria', 'indico' ),
				'action'      => dli_get_option( 'indico_item_existent_action', 'indico' ),
				'schedule'    => dli_get_option( 'indico_schedule', 'indico' ),
				'debug'       => dli_get_option( 'indico_debug_enabled', 'indico' ),
				'lang'        => dli_get_option( 'indico_default_lang', 'indico' ),
				'start_date'  => $start_date,
			);

			if ( $conf['base_url'] && $conf['category'] && $conf['keywords'] && $conf['import_type'] ) {
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
		} else {
			// Modulo disabilitato.
			return $this->send_response( 500, MSG_MODULE_DISABLED, $data );
		}
	}

	private function get_data_to_import( $conf ) {
		$category    = $conf['category'];
		$base_url    = $conf['base_url'];
		$start_date  = $conf['start_date'];
		// Creazione del client e invocazione del servizio.
		$api_url = $base_url . INDICO_API_SUFFIX_CATEGORY . '/'. $category . '.json?from=' . $start_date . '&pretty=yes';
		$response = wp_remote_get( $api_url );
		if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
			$msg = 'Errore invocando la Indico REST API: ' . $response->get_error_message();
			throw new Exception( $msg );
		}
		$resp_body = wp_remote_retrieve_body( $response );
		$resp_data = json_decode( $resp_body, true );
		// Controlla se la decodifica è riuscita
		if ( json_last_error() !== JSON_ERROR_NONE) {
			$msg = 'Errore nella decodifica JSON: ' . json_last_error_msg();
			throw new Exception( $msg );
		}
		return $resp_data;
	}

	private function execute_import( $conf = array() ): array {
		$import_type = $conf['import_type'];
		$data        = array();

		// Retrieve data to import.
		$resp_data = $this->get_data_to_import( $conf );

		// Import type header.
		array_push( $data, ( $import_type === 'dryrun' ) ? MSG_HEADER_DRY_RUN : MSG_HEADER_REAL_IMPORT );

		// Loop di importazione.
		$total     = count( $resp_data['results'] );
		$counter   = 0;
		$discarded = 0;
		$errors    = 0;
		$processed = 0;
		$modified  = 0;
		$ignored   = 0;
		foreach ( $resp_data['results'] as $item ) {
			$counter++;
			$item_title = $item['title'];
			$msg         = '';
			$source_array = $this->trim_array( $conf['keywords'] ? explode(',', $conf['keywords']) : array() );
			$dest_array   = $this->trim_array( $item['keywords'] ? $item['keywords'] : array() );
			if ( ( ! $source_array ) || ( ! $dest_array ) || ( count( array_intersect( $source_array, $dest_array ) ) === 0 ) ) {
				$discarded++;
				continue;
			}

			if ( $import_type === 'dryrun' ){
				// Importazione dry run.
				array_push(
					$data,
					MSG_IMPORT_DRY_RUN . $item_title,
				);
				$processed++;
			} else {
				// Importazione effettiva.
				try {
					$updated    = false;
					$ignored    = false;
					$item_code = $this->create_wp_content( $item, $conf, $updated, $ignored, $conf['lang'] );
					if ( $updated ) {
						array_push(
							$data,
							MSG_UPDATED_ITEM . $item_code . ' - ' . $item_title,
						);
						$modified++;
					} else if ( $ignored ){
						array_push(
							$data,
							MSG_IGNORED_ITEM . $item_code . ' - ' . $item_title,
						);
						$ignored++;
					} else {
						array_push(
							$data,
							MSG_IMPORTED_ITEM . $item_code . ' - ' . $item_title,
						);
						$processed++;
					}
				} catch ( Exception $e ) {
					array_push(
						$data,
						MSG_ERROR_IMPORTING_ITEM . $item_title . ' - ' . $e->getMessage(),
					);
					$errors++;
				}
			}
		}
		// Import footer.
		$msg = sprintf(
			__( "*** Totali: %d - Scartati: %d - Processati: %d - Aggiornati: %d - Ignorati: %d - Errori: %d ***" ), 
			$total, $discarded, $processed, $modified, $ignored, $errors
		);
		array_push( $data, $msg );
		return $data;
	}

	private function create_wp_content( $item, $conf, &$updated, &$ignored, $lang='it' ): int {
		$post_name    = dli_generate_slug( $item['title'] );
		$post_content = $this->_prepare_post_content( $item['description'], $conf['base_url'] );
		$new_page = array(
			'post_type'    => EVENT_POST_TYPE,
			'post_name'    => $post_name,
			'post_title'   => $item['title'],
			'post_content' => $post_content,
			'post_status'  => $conf['post_status'],
			'post_parent'  => 0,
		);
		$update_existent = ( $conf['action'] === 'update' ) ? true : false;
		// Creazione degli eventi su WordPress.
		// Verifico esistenza evento.
		$page_check = dli_get_content( $post_name, EVENT_POST_TYPE );
		$post_id    = $page_check ? $page_check->ID : 0;
		if ( ! $post_id ) {
			$post_id = wp_insert_post( $new_page );
			$updated = false;
			$this->update_custom_fields( $post_id, $item );
			// Scarico e aggiungo l'immagine.
			$this->_add_post_featured_image( $post_id, $item['url'], $conf['base_url'] );
			// La lingua del contenuto è modificabile nelle configurazioni dell'import.
			dli_set_post_language( $post_id, $lang );
		} else {
			if ( $update_existent ) {
				// Aggiorna i campi del post.
				$pars = array(
					'ID'           => $post_id,
					'post_content' => $new_page['post_content'],
				);
				wp_update_post( $pars );
				$this->update_custom_fields( $post_id, $item, $conf['lang'] );
				// Scarico e aggiungo l'immagine.
				$this->_add_post_featured_image( $post_id, $item['url'], $conf['base_url'] );
				$updated = true;
			} else {
				$ignored = true;
			}
		}
		return $post_id;
	}

	private function update_custom_fields( $post_id, $item, $lang='it' ){
		// Assegno valori ai campi dell'evento.
		dli_update_field( 'link_dettaglio', DLI_ITEM_LINK['DETAIL_PAGE'], $post_id );
		$plain_text     = strip_tags( $item['description'] );
		$truncated_text = mb_substr( $plain_text, 0, DLI_SHORT_DESCRIPTION_SIZE -3 );
		if  ( strlen( $plain_text ) > DLI_SHORT_DESCRIPTION_SIZE ) {
			$truncated_text .= '...';
		}
		dli_update_field( 'descrizione_breve', $truncated_text, $post_id );
		// Aggiorna data inizio e fine dell'evento.
		$start_date = $item['startDate']['date'];
		$start_date = DateTime::createFromFormat('Y-m-d', $start_date  )->format('Ymd');
		dli_update_field( 'data_inizio', $start_date, $post_id ); // data_inizio d/m/Y.
		$orario_inizio = $item['startDate']['time'];
		dli_update_field( 'orario_inizio', $orario_inizio, $post_id ); // orario inizio.
		$end_date   = $item['endDate']['date'];
		$end_date   = DateTime::createFromFormat('Y-m-d', $end_date  )->format('Ymd');
		dli_update_field( 'data_fine', $end_date, $post_id ); // data_fine d/m/Y.
		$orario_fine = $item['endDate']['time'];
		dli_update_field( 'orario_fine', $orario_fine, $post_id ); // orario fine.
		// Aggiorna url dell'evento.
		if ( $item['url'] ) {
			dli_update_field( 'sitoweb', $item['url'], $post_id );
		}
		// Aggiorna indirizzo dell'evento.
		$address = array();
		if ( $item['roomFullname'] ) array_push( $address, $item['roomFullname'] );
		if ( $item['location'] ) array_push( $address, $item['location'] );
		if ( $item['address'] ) array_push( $address, $item['address'] );
		$full_location = implode( ' - ', $address );
		dli_update_field( 'luogo', $full_location, $post_id );

		// Aggiorna categoria dell'evento (sottosezione dell'evento su Indico).
		if ( isset( $item['category'] ) ) {
			$category = $item['category'];
			// Controllo esistenza tassonomia.
			// Creo tassonomia.
			$term_item = term_exists( $category, WP_DEFAULT_CATEGORY );
			if ( $term_item ) {
				$term_id = $term_item['term_id'];
			} else {
				$new_term = wp_insert_term( $category, WP_DEFAULT_CATEGORY );
				$term_id  = $new_term['term_id'];
			}
			dli_set_term_language( $term_id, $lang );
			// Associo la tassonomia al contenuto.
			wp_set_post_terms( $post_id, array( $term_id ), WP_DEFAULT_CATEGORY, false );
		}

	}



	// *** Funzioni di utilità dedicate *** //
	private function _start_date_from_criteria( $criteria ){
		$date_string = date( 'Y-m-d') ;
		switch ( $criteria ) {
			case 'all':
					$date_string = DateTime::createFromFormat('d-m-Y', '01-01-1970')->format('Y-m-d');
					break;
			case 'this-year':
					$this_year    = date( 'Y' );
					$date_string = DateTime::createFromFormat('d-m-Y', '01-01-'. $this_year )->format('Y-m-d');
					break;
			case 'future':
				$date_string = date( 'Y-m-d' );
			}
		return $date_string;
	}

	private function _add_post_featured_image( $post_id, $item_url, $base_url ) {
		if ( $item_url ) {
			$property = 'og:image';
			try {
				$content = $this->_get_meta_content( $item_url, $property );
				if ( strpos( $content, 'http' ) !== false ) {
					$img_url = $content;
				} else {
					$img_url = $base_url . $content;
				}
				$this->log_string( $img_url );
				dli_set_post_featured_image_from_url( $post_id,  $img_url );
			} catch ( Exception $e ) {
				error_log( $e->getMessage() );
			}
		}
	}

	private function _get_meta_content($url, $property) {
		$html = file_get_contents($url);
		if ($html === FALSE) {
				die("Errore nel caricare la pagina");
		}
		// Crea un nuovo DOMDocument.
		$dom = new DOMDocument();
		// Sopprimi gli errori dovuti a HTML non valido.
		libxml_use_internal_errors(true);
		// Carica l'HTML
		$dom->loadHTML($html);
		// Ripristina la gestione degli errori.
		libxml_clear_errors();
		// Crea un nuovo DOMXPath.
		$xpath = new DOMXPath($dom);
		// Cerca i tag <meta> con l'attributo property specificato.
		$meta_tags = $xpath->query("//meta[@property='$property']");
		// Verifica se sono stati trovati tag <meta>.
		if ($meta_tags->length > 0) {
				// Ottieni il valore dell'attributo content.
				$content = $meta_tags->item(0)->getAttribute('content');
				return $content;
		}
		// Restituisce null se il tag <meta> non è trovato.
		return null;
	}

	private function _prepare_post_content( $post_content, $base_url ) {
		$pattern = '/src="\/([^"]*)"/';
		// Sostituisce il pattern con la base URL.
		$replacement = 'src="' .$base_url . '/$1"';
		// Esegui la sostituzione.
		$new_content = preg_replace( $pattern, $replacement, $post_content );
		return $new_content;
	}

}
