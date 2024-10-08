<?php
/**
 * Definition of the IRIS Manager used to import IRIS patents.
 *
 * @package Design_Laboratori_Italia
 */

// @TODO: Rifattorizzare class-indicomanager e class-irispatentimporter.
// .
// Definizione delle costanti in class.indicomanager.php.

class DLI_IrisPatentImporter {
	private string $job_name    = 'dli_iris_patent_import_job';
	private string $endpoint    = '/iris-patent-import';
	private bool $debug_enabled = false;

	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
		// Schedule import.
		// @TODO: $this->manage_import_job();
		$this->debug_enabled = ( dli_get_option( 'indico_debug_enabled', 'indico' ) === 'true' );
	}

	public function import() {
		// Gestione parametri.
		// WP_REST_Request $request.
		// $param1 = $request->get_param('param1') .
		$code = 200;
		$data = array();
		return 'ciao';
		// Verifica modulo abilitato.
		// $module_enabled = dli_get_option( 'indico_enabled', 'indico' );
		// if ( $module_enabled && $module_enabled === 'true' ) {
		// 	// Recupero parametri di configurazione.
		// 	$conf = array(
		// 		'base_url'    => dli_get_option( 'indico_baseurl', 'indico' ),
		// 	);

		// 	if ( $conf['base_url'] && $conf['category'] && $conf['keywords'] && $conf['import_type'] ) {
		// 		try {
		// 			// $data = $this->execute_import( $conf );
		// 			// Importazione completata: ritorno i risultati in formato json.
		// 			return $this->send_response( $code, MSG_IMPORT_SUCCESSFUL, $data );
		// 		} catch ( Exception $e ) {
		// 			// Errore nell'importazione.
		// 			return $this->send_response( 500, $e->getMessage(), $data );
		// 		}
		// 	} else {
		// 		// Modulo non configurato correttamente.
		// 		return $this->send_response( 500, MSG_MODULE_NOT_CONFIGURED, $data );
		// 	}
		// } else {
		// 	// Modulo disabilitato.
		// 	return $this->send_response( 500, MSG_MODULE_DISABLED, $data );
		// }
	}



	// *** FUNZIONI DI UTILITA' *** //

	public function setup(){
		// Register the import endpoint.
		add_action( 'rest_api_init', array( $this, 'register_import_endpoint' ) );
		// add_action( $this->job_name, array( $this, 'execute_job' ) );
		// add_action('delete_theme', array( $this, 'remove_all_import_jobs' ) );
		// add_action('switch_theme', array( $this, 'remove_all_import_jobs' ) );
	}

	/**
	 * Verifica la Basic Authentication.
	 *
	 * @param WP_REST_Request $request
	 * @return bool | WP_Error
	 */
	public function dli_permission_callback( WP_REST_Request $request ) {
		$auth_header = $request->get_header( 'Authorization' );
		if ( ! $auth_header ) {
			return new WP_Error(
				'rest_not_logged_in',
				__( 'Non sei autenticato.', 'design_laboratori_italia' ),
				array( 'status' => 401 ),
			);
		}
		list( $username, $password ) = explode(':', base64_decode( substr( $auth_header, 6 ) ) );
		$user = wp_authenticate( $username, $password );
		if ( is_wp_error( $user ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Credenziali non valide.', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	public function register_import_endpoint() {
		register_rest_route(
			'custom/v1',
			$this->endpoint,
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'import' ),
				'permission_callback' => array( $this, 'dli_permission_callback' ),
			)
		);
	}

	private function send_response( $code, $message, $data ) {
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		$this->logString( json_encode( $result ) );
		return new WP_REST_Response( $result, $code );
	}

	private function logString( $text ){
		if ( $this->debug_enabled ) {
			error_log( $text );
		}
	}
}
