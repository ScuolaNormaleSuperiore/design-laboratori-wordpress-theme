<?php
/**
 * Definition of the Indico Manager used to import Indico Events.
 *
 * @package Design_Laboratori_Italia
 */


define( 'MSG_MODULE_DISABLED', __( 'Import da Indico disabilitato', 'design_laboratori_italia' ) );
define( 'MSG_MODULE_NOT_CONFIGURED',  __( 'Import da Indico non configurato correttamente', 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_ERROR', __( "Si è verificato un errore durante l'esecuzione dell'import", 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_SUCCESSFUL', __( 'Importazione eseguita correttamente', 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_DRY_RUN', __( 'Dry-run - Importo evento: ', 'design_laboratori_italia' ) );
define( 'MSG_IMPORTED_EVENT', __( 'Importato evento: ', 'design_laboratori_italia' ) );
define( 'MSG_ERROR_IMPORTING_EVENT', __( "Errore importanto l'evento: ", 'design_laboratori_italia' ) );
define( 'INDICO_API_SUFFIX_CATEGORY', '/export/categ' );
class DLI_IndicoManager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	public function setup(){
		// Register the indico import endpoint.
		add_action( 'rest_api_init', array( $this, 'register_indico_import' ) );
		// @TODO: Schedule indico import.
	}

	public function register_indico_import(){
		register_rest_route(
			'custom/v1',
			'/indico-import',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'indico_import' ),
				// 'permission_callback' => function () {
				// 	return is_user_logged_in();
				// },
			)
		);
	}

	public function indico_import() {
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
			$start_date = $this->start_date_from_criteria( $criteria );
			$conf = array(
				'base_url'    => dli_get_option( 'indico_baseurl', 'indico' ),
				'token'       => dli_get_option( 'indico_token_api', 'indico' ),
				'category'    => dli_get_option( 'indico_category', 'indico' ),
				'keywords'    => dli_get_option( 'indico_keywords', 'indico' ),
				'import_type' => dli_get_option( 'indico_import_type', 'indico' ),
				'post_status' => dli_get_option( 'indico_imp_item_status', 'indico' ),
				'criteria'    => dli_get_option( 'indico_import_criteria', 'indico' ),
				'action'      => dli_get_option( 'indico_item_existent_action', 'indico' ),
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

	private function start_date_from_criteria( $criteria ){
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

	private function execute_import( $conf ): array {
		$category    = $conf['category'];
		$keywords    = explode( ',', $conf['keywords'] );
		$base_url    = $conf['base_url'];
		$start_date  = $conf['start_date'];
		$import_type = $conf['import_type'];
		$data       = array();
		// Creazione del client.
		// Invocazione del servizio.
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

		// Loop di importazione.
		foreach ( $resp_data['results'] as $event ) {
			$event_title = $event['title'];
			$msg         = '';
			if ( $import_type === 'dryrun' ){
				// Importazione dry run.
				array_push(
					$data,
					MSG_IMPORT_DRY_RUN . $event_title,
				);
			} else {
				// Importazione effettiva.
				try {
					$event_code = $this->create_event( $event, $conf );
					array_push(
						$data,
						MSG_IMPORTED_EVENT . $event_code . ' - ' . $event_title,
					);
				} catch ( Exception $e ) {
					array_push(
						$data,
						MSG_ERROR_IMPORTING_EVENT . $event_title . ' - ' . $e->getMessage(),
					);
				}
			}
		}
		return $data;
	}

	private function create_event( $event, $conf ): int {
		$post_name    = dli_generate_slug( $event['title'] );
		$post_content = $this->prepare_post_content( $event['description'], $conf['base_url'] );
		$new_page = array(
			'post_type'    => EVENT_POST_TYPE,
			'post_name'    => $post_name,
			'post_title'   => $event['title'],
			'post_content' => $post_content,
			'post_status'  => $conf['post_status'],
			'post_parent'  => 0,
		);
		$update_existent = ( $conf['action'] === 'update' ) ? true : false;
		// Creazione degli eventi su WordPress.
		// Verifico esistenza evento.
		$page_check     = dli_get_content( $post_name, EVENT_POST_TYPE );
		$post_id = $page_check ? $page_check->ID : 0;
		if ( ! $post_id ) {
			$post_id = wp_insert_post( $new_page );
			$this->update_custom_fields( $post_id, $event );

			$this->add_post_featured_image( $post_id, $event['url'], $conf['base_url'] );
			// Scarico e aggiungo l'immagine.


			// @TODO: Gestione multilingua.
			// update_post_meta( $post_id, '_wp_page_template', $new_content_template );
			// Assign the IT language to the page.
			// Create the EN page.
			// Associate it and en translations.
			// $related_posts = array(
			// 	'it' => $post_id,
			// 	'en' => $new_page_en_id,
			// );
			// dli_save_post_translations( $related_posts );

			dli_set_post_language( $post_id, 'it' );
		} else {
			if ( $update_existent ) {
				// Aggiorna i campi del post.
				wp_update_post( $new_page );
				$this->update_custom_fields( $post_id, $event );
			}
		}
		return $post_id;
	}

	private function add_post_featured_image( $post_id, $event_url, $base_url ) {
		if ( $event_url ) {
			$property = 'og:image';
			try {
				$content = $this->get_meta_content( $event_url, $property );
				if ( strpos( $content, 'http' ) !== false ) {
					$img_url = $content;
				} else {
					$img_url = $base_url . $content;
				}
				error_log( $img_url );
				dli_set_post_featured_image_from_url( $post_id,  $img_url );
			} catch ( Exception $e ) {
				error_log( $e->getMessage() );
			}
		}
	}

	private function prepare_post_content( $post_content, $base_url ) {
		$pattern = '/src="\/([^"]*)"/';
		// Sostituisce il pattern con la base URL.
		$replacement = 'src="' .$base_url . '/$1"';
		// Esegui la sostituzione.
		$new_content = preg_replace( $pattern, $replacement, $post_content );
		return $new_content;
	}

	private function update_custom_fields( $post_id, $event ){
			// Assegno valori ai campi dell'evento.
			$plain_text     = strip_tags( $event['description'] );
			$truncated_text = mb_substr( $plain_text, 0, DLI_SHORT_DESCRIPTION_SIZE -3 );
			if  ( strlen( $plain_text ) > DLI_SHORT_DESCRIPTION_SIZE ) {
				$truncated_text .= '...';
			}
			dli_update_field( 'descrizione_breve', $truncated_text, $post_id );

			$start_date = $event['startDate']['date'];
			$start_date = DateTime::createFromFormat('Y-m-d', $start_date  )->format('Y-m-d');
			dli_update_field( 'data_inizio', $start_date, $post_id ); // data_inizio d/m/Y.
			$orario_inizio = $event['startDate']['time'];
			dli_update_field( 'orario_inizio', $orario_inizio, $post_id ); // orario_inizio g:i a.
			$end_date   = $event['endDate']['date'];
			$end_date   = DateTime::createFromFormat('Y-m-d', $end_date  )->format('Y-m-d');
			dli_update_field( 'data_fine', $start_date, $post_id ); // data_inizio d/m/Y.
			if ( $event['url'] ) {
				dli_update_field( 'sitoweb', $event['url'], $post_id );
			}
			$address = array();
			if ( $event['roomFullname'] ) array_push( $address, $event['roomFullname'] );
			if ( $event['location'] ) array_push( $address, $event['location'] );
			if ( $event['address'] ) array_push( $address, $event['address'] );
			$full_location = implode( ' - ', $address );
			dli_update_field( 'luogo', $full_location, $post_id );
	}

	private function send_response( $code, $message, $data ){
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		return new WP_REST_Response( $result, $code );
	}

	private function get_meta_content($url, $property) {
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

}
