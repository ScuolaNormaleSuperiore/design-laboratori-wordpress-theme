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
		$post_name = dli_generate_slug( $event['title'] );
		$new_page = array(
			'post_type'    => EVENT_POST_TYPE,
			'post_name'    => $post_name,
			'post_title'   => $event['title'],
			'post_content' => $event['description'],
			'post_status'  => $conf['post_status'],
			'post_parent'  => 0,
		);
		$update_existent = $conf['action'] === 'update';
		// Creazione degli eventi su WordPress.
		// Verifico esistenza evento.
		$page_check     = dli_get_content( $post_name, EVENT_POST_TYPE );
		$new_post_it_id = $page_check ? $page_check->ID : 0;
		if ( ! $new_post_it_id ) {
			$new_post_it_id = wp_insert_post( $new_page );
			$this->update_custom_fields( $new_post_it_id, $event );

			// @TODO: Gestione multilingua.
			// update_post_meta( $new_post_it_id, '_wp_page_template', $new_content_template );
			// Assign the IT language to the page.
			// Create the EN page.
			// Associate it and en translations.
			// $related_posts = array(
			// 	'it' => $new_post_it_id,
			// 	'en' => $new_page_en_id,
			// );
			// dli_save_post_translations( $related_posts );

			dli_set_post_language( $new_post_it_id, 'it' );
		} else {
			if ( $update_existent ) {
				// Aggiorna i campi del post.
				wp_update_post( $new_page );
				$this->update_custom_fields( $new_post_it_id, $event );
			}
		}
		return $new_post_it_id;
	}

	private function update_custom_fields( $post_id, $event ){
			// Assegno valori ai campi dell'evento.
			$plain_text     = strip_tags( $event['description'] );
			$truncated_text = mb_substr( $plain_text, 0, DLI_SHORT_DESCRIPTION_SIZE -3 );
			if  ( strlen( $plain_text ) > DLI_SHORT_DESCRIPTION_SIZE ) {
				$truncated_text .= '...';
			}
			dli_update_field( 'descrizione_breve', $truncated_text, $post_id );
			// dli_update_field( 'data_inizio', $truncated_text, $new_post_it_id ); // data_inizio d/m/Y
			// dli_update_field( 'data_fine', $truncated_text, $new_post_it_id ); // data_fine  d/m/Y
			// dli_update_field( 'orario_inizio', $truncated_text, $new_post_it_id ); // orario_inizio g:i a
			// dli_update_field( 'label_contatti', $truncated_text, $new_post_it_id );
			// dli_update_field( 'luogo', $truncated_text, $new_post_it_id );
			// dli_update_field( 'telefono', $truncated_text, $new_post_it_id );
			// dli_update_field( 'email', $truncated_text, $new_post_it_id );
			// dli_update_field( 'sitoweb', $truncated_text, $new_post_it_id );
	}

	private function send_response( $code, $message, $data ){
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		return new WP_REST_Response( $result, $code );
	}

}
