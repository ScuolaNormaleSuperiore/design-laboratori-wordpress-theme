<?php
/**
 * Definition of the Indico Manager used to import Indico Events.
 *
 * @package Design_Laboratori_Italia
 */


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



	public function indico_import( WP_REST_Request $request){
		// $param1 = $request->get_param('param1');
		// ...
		$code         = 200;
		$data         = array();
		
		
		$msg_disabled = __( 'Modulo disabilitato', 'design_laboratori_italia' );
		$msg_not_conf = __( 'Modulo non configurato correttamente', 'design_laboratori_italia' );
		$msg_error    = __( "Si è verificato un errore durante l'esecuzione dell'import", 'design_laboratori_italia' );
		$msg_success  = __( 'Importazione eseguita correttamente', 'design_laboratori_italia' );
		$event_api_suffix    = '/export/event';
		$category_api_suffix = '/export/categ';
		$items_to_import     = array();

		// Verifica modulo abilitato.
		$module_enabled = dli_get_option( 'indico_enabled', 'indico' );
		if ( $module_enabled && $module_enabled = 'true' ) {
			// Recupero parametri di configurazione.
			$base_url    = dli_get_option( 'indico_baseurl', 'indico' );
			$token       = dli_get_option( 'indico_token_api', 'indico' );
			$cat_string  = dli_get_option( 'indico_categories', 'indico' );
			$kw_string   = dli_get_option( 'indico_keywords', 'indico' );
			$post_status = dli_get_option( 'indico_imp_item_status', 'indico' );
			$import_type = dli_get_option( 'indico_import_type', 'indico' );
			$categories  = explode( ',', $cat_string );
			$keywords    = explode( ',', $kw_string );
			$items_to_import = array();

			if ( $base_url && $token && $cat_string && $kw_string && $post_status && $import_type ) {

				// Creazione del client.
				// Invocazione del servizio.
				$cat     = $categories[0];
				$api_url = $base_url . $category_api_suffix . '/'. $cat . '.json?pretty=yes';
				$response = wp_remote_get( $api_url );
				if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
					$msg = 'Errore invocando la Indico REST API: ' . $response->get_error_message();
					return $this->send_response( 500, $msg, $data );
				}
				$resp_body = wp_remote_retrieve_body($response);
				$resp_data = json_decode($resp_body, true);
				// Controlla se la decodifica è riuscita
				if ( json_last_error() !== JSON_ERROR_NONE)  {
					$msg = 'Errore nella decodifica JSON: ' . json_last_error_msg();
					return $this->send_response( 500, $msg, $data );
				}

				// Preparazione della lista di eventi da importare.
				foreach ( $resp_data['results'] as $event ) {
					$item = array(
						'post_type'    => EVENT_POST_TYPE,
						'post_name'    => dli_generate_slug( $event['title'] ),
						'post_title'   => $event['title'],
						'post_content' => $event['description'],
						'post_status'  => $post_status,
						// 'post_author'  => intval( $page['content_author'] ),
						'post_parent'  => 0,
					);
					array_push( $items_to_import, $item );
					array_push(
						$data,
						'Importo evento: ' . $event['title'],
					);
				}

// descrizione_breve
// data_inizio d/m/Y
// data_fine  d/m/Y
// orario_inizio g:i a
// luogo
// label_contatti
// telefono
// email
// sitoweb

			} else {
				// Modulo non configurato correttamente.
				return $this->send_response( 500, $msg_not_conf, $data );
			}
		} else {
			// Modulo disabilitato.
			return $this->send_response( 500, $msg_disabled, $data );
		}

		// @TODO: Recupero immagini.
		$msg = $msg_success;
		return $this->send_response( $code, $msg, $data );
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
