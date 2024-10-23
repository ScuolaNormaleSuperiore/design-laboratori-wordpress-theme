<?php
/**
 * Definition of the Base importer to import objects into WordPress.
 *
 * @package Design_Laboratori_Italia
 */

define( 'MSG_MODULE_DISABLED', __( 'Import disabilitato', 'design_laboratori_italia' ) );
define( 'MSG_MODULE_NOT_CONFIGURED',  __( 'Import non configurato correttamente', 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_ERROR', __( "Si è verificato un errore durante l'esecuzione dell'import", 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_SUCCESSFUL', __( 'Importazione eseguita correttamente', 'design_laboratori_italia' ) );
define( 'MSG_IMPORT_DRY_RUN', __( 'Dry-run - Importo oggetto: ', 'design_laboratori_italia' ) );
define( 'MSG_IMPORTED_EVENT', __( 'Importato oggetto: ', 'design_laboratori_italia' ) );
define( 'MSG_UPDATED_EVENT', __( 'Aggiornato oggetto: ', 'design_laboratori_italia' ) );
define( 'MSG_IGNORED_EVENT', __( 'Ignorato oggetto: ', 'design_laboratori_italia' ) );
define( 'MSG_ERROR_IMPORTING_EVENT', __( "Errore importando l'oggetto: ", 'design_laboratori_italia' ) );
define( 'MSG_HEADER_DRY_RUN', __( '*** Importazione in modalità DRY-RUN (nessun oggetto creato realmente) ***', 'design_laboratori_italia' ) );
define( 'MSG_HEADER_REAL_IMPORT', __( '*** Importazione effettiva, oggetti creati realmente ***', 'design_laboratori_italia' ) );


class DLI_BaseImporter {
	protected string $job_name;
	protected string $endpoint;
	protected bool $debug_enabled;
	protected bool $module_enabled;
	protected string $post_type;

	public function __construct() {}

	public function setup(){
		// Register the import endpoint.
		add_action( 'rest_api_init', array( $this, 'register_import_endpoint' ) );
		// add_action( $this->job_name, array( $this, 'execute_job' ) );
		// add_action('delete_theme', array( $this, 'remove_all_import_jobs' ) );
		// add_action('switch_theme', array( $this, 'remove_all_import_jobs' ) );
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

	public function get_rest_data( $ws_url, $username, $password ) {
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
			if ( $body ){
				$data = json_decode($body);
			}
		}
		return $data;
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

	public function send_response( $code, $message, $data ) {
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		$this->log_string( json_encode( $result ) );
		return new WP_REST_Response( $result, $code );
	}

	public function log_string( $text ){
		if ( $this->debug_enabled ) {
			error_log( $text );
		}
	}

}
