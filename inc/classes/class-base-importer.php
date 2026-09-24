<?php
/**
 * Definition of the Base importer to import objects into WordPress.
 *
 * @package Design_Laboratori_Italia
 */

define( 'MSG_MODULE_DISABLED', 'Import disabilitato' );
define( 'MSG_MODULE_NOT_CONFIGURED', 'Import non configurato correttamente' );
define( 'MSG_IMPORT_ERROR', "Si è verificato un errore durante l'esecuzione dell'import" );
define( 'MSG_IMPORT_SUCCESSFUL', 'Importazione eseguita correttamente' );
define( 'MSG_IMPORT_DRY_RUN', 'Dry-run - Importo oggetto: ' );
define( 'MSG_IMPORTED_ITEM', 'Importato oggetto: ' );
define( 'MSG_UPDATED_ITEM', 'Aggiornato oggetto:' );
define( 'MSG_IGNORED_ITEM', 'Ignorato oggetto:' );
define( 'MSG_ERROR_IMPORTING_ITEM', "Errore importando l'oggetto:" );
define( 'MSG_HEADER_DRY_RUN', '*** Importazione in modalità DRY-RUN (nessun oggetto creato realmente) ***' );
define( 'MSG_HEADER_REAL_IMPORT', '*** Importazione effettiva, oggetti creati realmente ***' );


/**
 * Base class for scheduled/REST-triggered import jobs (Indico events, IRIS patents, ...).
 */
class DLI_BaseImporter {

	/**
	 * Human-readable name, used in log lines.
	 *
	 * @var string
	 */
	protected string $importer_name;

	/**
	 * Name of the scheduled WP-Cron job.
	 *
	 * @var string
	 */
	protected string $job_name;

	/**
	 * REST route suffix the importer registers, when enabled.
	 *
	 * @var string
	 */
	protected string $endpoint;

	/**
	 * Configured schedule recurrence (e.g. 'daily'), from the module's options.
	 *
	 * @var string
	 */
	protected string $schedule_type;

	/**
	 * Post type the importer creates/updates.
	 *
	 * @var string
	 */
	protected string $post_type;

	/**
	 * Whether verbose logging is enabled, from the module's options.
	 *
	 * @var bool
	 */
	protected bool $debug_enabled;

	/**
	 * Whether the scheduled job is enabled, from the module's options.
	 *
	 * @var bool
	 */
	protected bool $schedule_enabled;

	/**
	 * Whether the whole import module is enabled, from the module's options.
	 *
	 * @var bool
	 */
	protected bool $module_enabled;

	/**
	 * Base constructor; subclasses configure the properties above.
	 */
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
	 * @param array $conf Import configuration.
	 * @return void
	 */
	private function execute_import( $conf ) {}

	/**
	 * Recupera dalla sorgente i dati da importare.
	 *
	 * @param array $conf Import configuration.
	 * @return void
	 */
	private function get_data_to_import( $conf ) {}

	/**
	 *  Creazione/Modifica dell'post su WordPress.
	 *
	 * @param mixed  $item    Raw source item to import.
	 * @param mixed  $conf    Import configuration.
	 * @param mixed  $updated Passed by reference: set when an existing post was updated.
	 * @param mixed  $ignored Passed by reference: set when an existing post was left untouched.
	 * @param string $lang    Language slug to assign to the created/updated post.
	 * @return int
	 */
	private function create_wp_content( $item, $conf, &$updated, &$ignored, $lang = 'it' ): int {}

	/**
	 * Modifica dei post su WordPress.
	 *
	 * @param int    $post_id Post ID to update.
	 * @param mixed  $item    Raw source item to import.
	 * @param string $lang    Language slug of the post being updated.
	 * @return void
	 */
	private function update_custom_fields( $post_id, $item, $lang = 'it' ) { }

	/**
	 * Modifica del titolo del post.
	 *
	 * @param int    $post_id Post ID to update.
	 * @param string $title   New post title.
	 * @return void
	 */
	private function update_title( $post_id, $title ) { }

	/**
	 * Register hooks for jobs and REST endpoints.
	 *
	 * @return void
	 */
	public function setup() {
		// Register the import endpoint.
		add_action( 'rest_api_init', array( $this, 'register_import_endpoint' ) );
		add_action( $this->job_name, array( $this, 'execute_job' ) );
		add_action( 'delete_theme', array( $this, 'remove_all_import_jobs' ) );
		add_action( 'switch_theme', array( $this, 'remove_all_import_jobs' ) );
	}

	/**
	 * Add o modify a cron job.
	 *
	 * @return void
	 */
	public function manage_import_job() {
		// SELECT * FROM wp_options WHERE option_name = 'cron'.
		$this->log_string( '*** manage_import_job: ' . $this->importer_name . '  ***' );
		$schedule       = (string) $this->schedule_type;
		$module_enabled = (bool) $this->module_enabled;
		$schedules      = wp_get_schedules();
		$valid_schedule = isset( $schedules[ $schedule ] );
		if ( ! $module_enabled || ( 'never' === $schedule ) || ! $valid_schedule ) {
			$this->remove_all_import_jobs();
			return;
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

	/**
	 * Clear the importer's scheduled WP-Cron job.
	 *
	 * @return void
	 */
	public function remove_all_import_jobs() {
		$this->log_string( '*** CANCELLO schedulazione job: ' . $this->job_name . ' ***' );
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
		// Respect global REST API toggle from setup options.
		if ( 'true' !== dli_get_option( 'rest_api_enabled', 'setup' ) ) {
			return;
		}
		// Endpoint used by the import procedure.
		register_rest_route(
			'custom/v1',
			$this->endpoint,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'import' ),
				'permission_callback' => array( $this, 'dli_permission_callback' ),
			)
		);
	}



	// *** Funzioni di utilità generali *** //

	/**
	 * Verifica la Basic Authentication e che l'utente sia Amministratore.
	 *
	 * @param WP_REST_Request $request Incoming REST request.
	 * @return bool | WP_Error
	 */
	public function dli_permission_callback( WP_REST_Request $request ) {
		if ( 'POST' !== $request->get_method() ) {
			return new WP_Error(
				'rest_method_not_allowed',
				__( 'Metodo non consentito. Usare POST.', 'design_laboratori_italia' ),
				array( 'status' => 405 )
			);
		}

		$auth_header = $request->get_header( 'Authorization' );
		if ( ! $auth_header ) {
			return new WP_Error(
				'rest_not_logged_in',
				__( 'Non sei autenticato.', 'design_laboratori_italia' ),
				array( 'status' => 401 ),
			);
		}
		if ( 0 !== stripos( $auth_header, 'Basic ' ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Credenziali non valide.', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}

		$decoded_credentials = base64_decode( substr( $auth_header, 6 ), true );
		if ( false === $decoded_credentials ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Credenziali non valide.', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}

		$credentials = explode( ':', $decoded_credentials, 2 );
		if ( 2 !== count( $credentials ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Credenziali non valide.', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}

			list( $username, $password ) = $credentials;
			$user                        = wp_authenticate( $username, $password );
		if ( is_wp_error( $user ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Credenziali non valide.', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		} elseif ( ! user_can( $user, 'manage_options' ) ) {
			return new WP_Error(
				'rest_authentication_failed',
				__( 'Utente non autorizzato', 'design_laboratori_italia' ),
				array( 'status' => 401 )
			);
		}
		return true;
	}

	/**
	 * Decode external JSON payload with UTF-8 normalization and strict error handling.
	 *
	 * @param string $body Raw response body.
	 * @param bool   $assoc Whether to return associative arrays.
	 * @param string $source Source name used in exception messages.
	 * @return mixed
	 * @throws Exception When payload is empty or invalid.
	 */
	protected function decode_external_json_payload( string $body, bool $assoc = false, string $source = 'external' ) {
		if ( '' === trim( $body ) ) {
			throw new Exception( 'Risposta ' . $source . ' vuota.' );
		}

		$normalized_body = $this->normalize_json_body( $body );
		try {
			return json_decode( $normalized_body, $assoc, 512, JSON_THROW_ON_ERROR );
		} catch ( JsonException $e ) {
			throw new Exception( 'Errore decodifica JSON ' . $source . ': ' . $e->getMessage() );
		}
	}

	/**
	 * Normalize JSON body before decoding.
	 *
	 * @param string $body Raw response body.
	 * @return string
	 */
	protected function normalize_json_body( string $body ): string {
		// Strip UTF-8 BOM if present.
		$body = preg_replace( '/^\xEF\xBB\xBF/', '', $body );

		// Decode escaped UTF-16 surrogate pairs to real UTF-8 codepoints.
		$normalized_body = preg_replace_callback(
			'/\\\\u(d[89ab][0-9a-f]{2})\\\\u(d[cdef][0-9a-f]{2})/i',
			array( $this, 'decode_surrogate_pair_callback' ),
			$body
		);

		return is_string( $normalized_body ) ? $normalized_body : $body;
	}

	/**
	 * Convert JSON UTF-16 surrogate pair into UTF-8.
	 *
	 * @param array $matches Regex matches.
	 * @return string
	 */
	private function decode_surrogate_pair_callback( array $matches ): string {
		$high      = hexdec( $matches[1] );
		$low       = hexdec( $matches[2] );
		$codepoint = ( ( $high - 0xD800 ) << 10 ) + ( $low - 0xDC00 ) + 0x10000;

		return $this->utf8_from_codepoint( $codepoint );
	}

	/**
	 * Encode a Unicode codepoint as UTF-8.
	 *
	 * @param int $codepoint Unicode codepoint.
	 * @return string
	 */
	private function utf8_from_codepoint( int $codepoint ): string {
		if ( function_exists( 'mb_chr' ) ) {
			return (string) mb_chr( $codepoint, 'UTF-8' );
		}

		if ( $codepoint <= 0x7F ) {
			return chr( $codepoint );
		}
		if ( $codepoint <= 0x7FF ) {
			return chr( 0xC0 | ( $codepoint >> 6 ) ) .
				chr( 0x80 | ( $codepoint & 0x3F ) );
		}
		if ( $codepoint <= 0xFFFF ) {
			return chr( 0xE0 | ( $codepoint >> 12 ) ) .
				chr( 0x80 | ( ( $codepoint >> 6 ) & 0x3F ) ) .
				chr( 0x80 | ( $codepoint & 0x3F ) );
		}

		return chr( 0xF0 | ( $codepoint >> 18 ) ) .
			chr( 0x80 | ( ( $codepoint >> 12 ) & 0x3F ) ) .
			chr( 0x80 | ( ( $codepoint >> 6 ) & 0x3F ) ) .
			chr( 0x80 | ( $codepoint & 0x3F ) );
	}

	/**
	 * Stampa il report dell'importazione.
	 *
	 * @param int    $code    HTTP-like status code to report.
	 * @param string $message Human-readable result message.
	 * @param array  $data    Log lines / import result payload.
	 * @return WP_REST_Response
	 */
	public function send_response( $code, $message, $data ) {
		$result = array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		);
		$this->log_string( wp_json_encode( $result ) );
		return new WP_REST_Response( $result, $code );
	}

	/**
	 * Stampa messaggi di log, se abilitati da Configurazione.
	 *
	 * @param string $text Message to log.
	 * @return void
	 */
	public function log_string( $text ) {
		if ( $this->debug_enabled ) {
			error_log( $text );
		}
	}

	/**
	 * Trim delle stringhe di un array.
	 *
	 * @param array $strings Array of strings to trim.
	 * @return array
	 */
	public function trim_array( $strings ): array {
		return array_map( 'trim', $strings );
	}

	/**
	 * Sanitize imported titles removing invisible formatting chars and control chars.
	 *
	 * @param string $title Raw title.
	 * @return string
	 */
	protected function sanitize_import_title( string $title ): string {
		$title = wp_strip_all_tags( $title );
		$title = preg_replace( '/\p{Cf}+/u', '', $title );
		$title = preg_replace( '/[\x00-\x1F\x7F]+/u', '', $title );
		$title = preg_replace( '/\s+/u', ' ', $title );
		return trim( (string) $title );
	}

	/**
	 * Resolve an existing term ID or create a new term safely.
	 *
	 * @param string $term_name Term label.
	 * @param string $taxonomy  Taxonomy name.
	 * @return int
	 * @throws Exception If term cannot be resolved/created.
	 */
	protected function get_or_create_term_id( string $term_name, string $taxonomy ): int {
		$term_name = trim( $term_name );
		if ( '' === $term_name ) {
			throw new Exception( 'Nome termine tassonomia non valido.' );
		}

		$term_item = term_exists( $term_name, $taxonomy );
		if ( is_array( $term_item ) && isset( $term_item['term_id'] ) ) {
			return (int) $term_item['term_id'];
		}
		if ( is_int( $term_item ) ) {
			return $term_item;
		}

		$new_term = wp_insert_term( $term_name, $taxonomy );
		if ( is_wp_error( $new_term ) ) {
			throw new Exception( 'Errore creazione termine tassonomia: ' . $new_term->get_error_message() );
		}
		if ( ! is_array( $new_term ) || ! isset( $new_term['term_id'] ) ) {
			throw new Exception( 'Creazione termine tassonomia fallita: term_id mancante.' );
		}

		return (int) $new_term['term_id'];
	}

	/**
	 * Convert an external date string between formats with defensive fallback.
	 *
	 * @param string $date_string   Raw date string.
	 * @param string $input_format  Input format.
	 * @param string $output_format Output format.
	 * @return string
	 */
	protected function format_import_date( string $date_string, string $input_format, string $output_format ): string {
		$date_string = trim( $date_string );
		if ( '' === $date_string ) {
			return '';
		}

		$formatted = dli_format_date_from_format( $input_format, $date_string, $output_format );
		return $formatted ? (string) $formatted : '';
	}
}
