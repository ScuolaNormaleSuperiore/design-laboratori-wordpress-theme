<?php
/**
 * Definition of the Base importer to import objects into WordPress.
 *
 * @package Design_Laboratori_Italia
 */

if ( function_exists( 'dli_define_sponsor_constants' ) ) {
	function dli_define_sponsor_constants() {
		define( 'MSG_MODULE_DISABLED', __( 'Import disabilitato', 'design_laboratori_italia' ) );
		define( 'MSG_MODULE_NOT_CONFIGURED',  __( 'Import non configurato correttamente', 'design_laboratori_italia' ) );
		define( 'MSG_IMPORT_ERROR', __( "Si è verificato un errore durante l'esecuzione dell'import", 'design_laboratori_italia' ) );
		define( 'MSG_IMPORT_SUCCESSFUL', __( 'Importazione eseguita correttamente', 'design_laboratori_italia' ) );
		define( 'MSG_IMPORT_DRY_RUN', __( 'Dry-run - Importo oggetto: ', 'design_laboratori_italia' ) );
		define( 'MSG_IMPORTED_ITEM', __( 'Importato oggetto: ', 'design_laboratori_italia' ) );
		define( 'MSG_UPDATED_ITEM', __( 'Aggiornato oggetto: ', 'design_laboratori_italia' ) );
		define( 'MSG_IGNORED_ITEM', __( 'Ignorato oggetto: ', 'design_laboratori_italia' ) );
		define( 'MSG_ERROR_IMPORTING_ITEM', __( "Errore importando l'oggetto: ", 'design_laboratori_italia' ) );
		define( 'MSG_HEADER_DRY_RUN', __( '*** Importazione in modalità DRY-RUN (nessun oggetto creato realmente) ***', 'design_laboratori_italia' ) );
		define( 'MSG_HEADER_REAL_IMPORT', __( '*** Importazione effettiva, oggetti creati realmente ***', 'design_laboratori_italia' ) );
	}
	add_action( 'after_setup_theme', 'dli_define_sponsor_constants' );
}
class DLI_BaseImporter {

	protected string $importer_name;
	protected string $job_name;
	protected string $endpoint;
	protected string $schedule_type;
	protected string $post_type;
	protected bool $debug_enabled;
	protected bool $schedule_enabled;
	protected bool $module_enabled;

	protected function __construct() {}

	/**
	 * Gestione dell'import.
	 *
	 * @return void
	 */
	protected function import() {}

	/**
	 * Esecuzione dell'import.
	 *
	 * @param [type] $conf
	 * @return void
	 */
	private function execute_import( $conf ) {}

	/**
	 * Recupera dalla sorgente i dati da importare.
	 *
	 * @param [type] $conf
	 * @return void
	 */
	private function get_data_to_import( $conf ) {}

	/**
	 *  Creazione/Modifica dell'post su WordPress.
	 * 
	 * @param mixed $item
	 * @param mixed $conf
	 * @param mixed $updated
	 * @param mixed $ignored
	 * @return int
	 */
	private function create_wp_content( $item, $conf, &$updated, &$ignored, $lang='it' ): int {}

	/**
	 * Modifica dei post su WordPress.
	 *
	 * @param [type] $post_id
	 * @param [type] $item
	 * @return void
	 */
	private function update_custom_fields( $post_id, $item, $lang='it' ){ }

	/**
	 * Modifica del titolo del post.
	 *
	 * @param [type] $post_id
	 * @param [type] $title
	 * @return void
	 */
	private function update_title( $post_id, $title ){ }

	/**
	 * Register hooks for jobs and REST endpoints.
	 *
	 * @return void
	 */
	public function setup(){
		// Register the import endpoint.
		add_action( 'rest_api_init', array( $this, 'register_import_endpoint' ) );
		add_action( $this->job_name, array( $this, 'execute_job' ) );
		add_action('delete_theme', array( $this, 'remove_all_import_jobs' ) );
		add_action('switch_theme', array( $this, 'remove_all_import_jobs' ) );
	}

	/**
	 * Add o modify a cron job.
	 *
	 * @return void
	 */
	public function manage_import_job() {
		// SELECT * FROM wp_options WHERE option_name = 'cron'.
		$this->log_string('*** manage_import_job: ' . $this->importer_name . '  ***');
		$schedule       = $this->schedule_type;
		$module_enabled = $this->module_enabled;
		if ( ( $module_enabled ==='false' ) || ( $schedule === 'never' ) ){
			$this->remove_all_import_jobs();
		} else {
			$next_scheduled = wp_get_scheduled_event( $this->job_name );
			if ( ! $next_scheduled ) {
				$this->log_string( '*** CREO schedulazione per job: ' . $this->job_name . ' ***' );
				wp_schedule_event( current_time( 'timestamp' ), $schedule, $this->job_name );
			} elseif ( $next_scheduled->schedule !== $schedule ) {
				$this->log_string( '*** CAMBIO schedulazione per job: ' . $this->job_name . ' ***' );
				wp_clear_scheduled_hook( $this->job_name );
				wp_schedule_event( current_time( 'timestamp' ), $schedule, $this->job_name );
			}
		}
	}

	public function remove_all_import_jobs() {
		$this->log_string('*** CANCELLO schedulazione job: ' . $this->job_name . ' ***');
		wp_clear_scheduled_hook( $this->job_name );
	}

	/**
	 * Execute a scheduled job.
	 *
	 * @return void
	 */
	public function execute_job() {
		$this->log_string( '*** ESEGUO il job: ' . $this->job_name . ' ***' );
		$this->import();
	}

	/**
	 * Register the REST API endpoint to execute the script.
	 * Basi authentication of administrator user is required.
	 *
	 * @return void
	 */
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



	// *** Funzioni di utilità generali *** //

	/**
	 * Verifica la Basic Authentication e che l'utente sia Amministratore.
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
		} elseif ( ! user_can( $user, 'administrator' ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Utente non autorizzato', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Stampa il report dell'importazione.
	 *
	 * @param [type] $code
	 * @param [type] $message
	 * @param [type] $data
	 * @return void
	 */
	public function send_response( $code, $message, $data ) {
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		$this->log_string( json_encode( $result ) );
		return new WP_REST_Response( $result, $code );
	}

	/**
	 * Stampa messaggi di log, se abilitati da Configurazione.
	 *
	 * @param [type] $text
	 * @return void
	 */
	public function log_string( $text ){
		if ( $this->debug_enabled ) {
			error_log( $text );
		}
	}

	/**
	 * Trim delle stringhe di un array.
	 *
	 * @param [type] $array
	 * @return array
	 */
	public function trim_array( $array ): array {
		return array_map( 'trim', $array );
	}

}
