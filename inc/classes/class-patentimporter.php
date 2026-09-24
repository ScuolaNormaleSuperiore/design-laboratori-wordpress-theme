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

/**
 * Imports patents from the IRIS institutional repository web service.
 */
class DLI_IrisPatentImporter extends DLI_BaseImporter {

	/**
	 * Configure the importer settings and schedule the import job.
	 */
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

	/**
	 * Run the IRIS patents import (real or dry-run, per configuration).
	 *
	 * @return WP_REST_Response
	 */
	public function import() {
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

	/**
	 * Fetch the patents feed from the IRIS web service (Basic Auth over HTTPS).
	 *
	 * @param array $conf Import configuration (ws_url, username, password, ...).
	 * @return array Decoded JSON payload (array of patent objects).
	 * @throws Exception When the endpoint is not HTTPS, or the request fails/returns an invalid payload.
	 */
	public function get_data_to_import( $conf ) {
		$ws_url   = esc_url_raw( (string) $conf['ws_url'] );
		$username = $conf['username'];
		$password = $conf['password'];

		$ws_url_parts = wp_parse_url( $ws_url );
		if (
			empty( $ws_url_parts['scheme'] ) ||
			'https' !== strtolower( $ws_url_parts['scheme'] )
		) {
			throw new Exception( 'Invalid endpoint: HTTPS is required for patent import.' );
		}

		// Recupero JSON dati.
		$auth = base64_encode( "$username:$password" );
		$args = array(
			'headers'     => array(
				'Authorization' => "Basic $auth",
			),
			'timeout'     => 20,
			'redirection' => 0,
		);
		// Invocazione dell'endpoint.
		$response = wp_safe_remote_get( $ws_url, $args );
		// Controllo della risposta.
		if ( is_wp_error( $response ) ) {
			// Errore invocando il web service.
			throw new Exception( $response->get_error_message() );
		}
		$status_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status_code ) {
			$error_msg = "Errore: codice di stato $status_code invocando il web service.";
			throw new Exception( $error_msg );
		}
		$body = wp_remote_retrieve_body( $response );
		$data = $this->decode_external_json_payload( (string) $body, false, 'IRIS' );

		if ( ! is_array( $data ) ) {
			throw new Exception( 'Payload IRIS non valido: atteso array di brevetti.' );
		}

		return $data;
	}

	/**
	 * Fetch the IRIS patents feed and import each entry (Italian + English content).
	 *
	 * @param array $conf Import configuration (import_type, import_action, ...).
	 * @return array Human-readable log lines, one per processed item plus a summary footer.
	 * @throws Exception When the feed payload is not iterable.
	 */
	private function execute_import( $conf = array() ): array {
		$this->log_string( '*** INIZIO importazione da IRIS (brevetti) ***' );
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

		if ( ! is_iterable( $data ) ) {
			throw new Exception( 'Payload IRIS non iterabile: impossibile avviare il loop di importazione.' );
		}

		foreach ( $data as $item ) {
			if ( ! is_object( $item ) || ! isset( $item->pid ) || ! isset( $item->displayValue ) ) {
				array_push(
					$results,
					MSG_ERROR_IMPORTING_ITEM . 'Elemento IRIS non valido: campi minimi mancanti (pid/displayValue).'
				);
				++$errors;
				continue;
			}

			++$counter;
			$item_pid   = $item->pid;
			$item_title = $this->sanitize_import_title( (string) $item->displayValue );

			if ( 'dryrun' === $conf['import_type'] ) {
				// Importazione dry run.
				array_push(
					$results,
					MSG_IMPORT_DRY_RUN . $item_pid . ' - ' . $item_title,
				);
				++$simulated_items;
			} else {
				// Importazione effettiva.
				try {
					$updated = false;
					$ignored = false;
					// Creazione del contenuto.
					$item_code = $this->create_wp_content(
						$item,
						$conf,
						$updated,
						$ignored
					);
					// Gestione del risultato.
					if ( 0 !== $item_code ) {
						$this->_process_result(
							$results,
							$item_pid . ' - ' . $item_code,
							$this->sanitize_import_title( (string) $item->displayValue ),
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
					if ( 0 !== $item_code_en ) {
						$this->_process_result(
							$results,
							$item_pid . ' - ' . $item_code_en,
							$this->sanitize_import_title( (string) $item->displayValue_en ),
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
					++$errors;
				}
			}
		}
		$msg = sprintf(
			__( '*** Totali: %1$d - Simulati: %2$d - Aggiunti: %3$d - Aggiornati: %4$d - Ignorati: %5$d - Errori: %6$d ***' ),
			$counter,
			$simulated_items,
			$added_items,
			$updated_items,
			$ignored_items,
			$errors
		);
		array_push( $results, $msg );
		$this->log_string( '*** FINE importazione da IRIS (brevetti) ***' );
		return $results;
	}
	/**
	 * Append a log line for one imported item and bump the matching counter.
	 *
	 * @param array  $results       Passed by reference: log lines accumulator.
	 * @param string $itemCode      Post ID (and PID) of the imported item, for the log line.
	 * @param string $itemTitle     Sanitized item title, for the log line.
	 * @param bool   $updated       Whether an existing post was updated.
	 * @param bool   $ignored       Whether an existing post was left untouched.
	 * @param int    $added_items   Passed by reference: bumped when a new post was created.
	 * @param int    $updated_items Passed by reference: bumped when an existing post was updated.
	 * @param int    $ignored_items Passed by reference: bumped when an existing post was ignored.
	 * @return void
	 */
	private function _process_result( &$results, $itemCode, $itemTitle, $updated, $ignored, &$added_items, &$updated_items, &$ignored_items ) {
		if ( $updated ) {
			array_push(
				$results,
				MSG_UPDATED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			++$updated_items;
		} elseif ( $ignored ) {
			array_push(
				$results,
				MSG_IGNORED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			++$ignored_items;
		} else {
			array_push(
				$results,
				MSG_IMPORTED_ITEM . $itemCode . ' - ' . $itemTitle
			);
			++$added_items;
		}
	}

	/**
	 * Create or update the Italian WordPress patent post for a single IRIS feed item.
	 *
	 * @param object $item    Raw feed item (displayValue, abstract, ...).
	 * @param array  $conf    Import configuration (import_action, ...).
	 * @param bool   $updated Passed by reference: set to true when an existing post was updated.
	 * @param bool   $ignored Passed by reference: set to true when an existing post was left untouched.
	 * @param string $lang    Language slug to assign to the created post.
	 * @return int Post ID of the created or matched patent.
	 * @throws Exception When wp_insert_post() fails.
	 */
	private function create_wp_content( $item, $conf, &$updated, &$ignored, $lang = 'it' ): int {
		$post_title       = $this->sanitize_import_title( (string) $item->displayValue );
		$post_name        = dli_generate_slug( $post_title );
			$post_content = wp_kses_post( $item->abstract ?? '.' );

		$new_content    = array(
			'post_type'    => $this->post_type,
			'post_name'    => $post_name,
			'post_title'   => $post_title,
			'post_content' => $post_content,
			'post_status'  => 'draft',
			'post_parent'  => 0,
		);
		$update_content = ( 'update' === $conf['import_action'] ) ? true : false;
		// Verifico l'esistenza dell'oggetto su WordPress.
		$pid      = $this->get_wp_content_id( $item );
		$contents = dli_get_post_translations( $pid );

		if ( ! isset( $contents[ $lang ] ) ) {
			$post_id = wp_insert_post( $new_content, true );
			if ( is_wp_error( $post_id ) || ! $post_id ) {
				$error_message = is_wp_error( $post_id ) ? $post_id->get_error_message() : 'ID = 0';
				throw new Exception( 'wp_insert_post fallito: ' . $error_message );
			}
			$updated = false;
			// Aggiorna campi personalizzati.
			$this->update_custom_fields( $post_id, $item );
			// La lingua impostata per l'oggetto importato è l'italiano.
			dli_set_post_language( $post_id, $lang );
		} else {
			$post_id = $contents[ $lang ];
			if ( $update_content ) {
				// Aggiorna i campi del post.
				$pars = array(
					'ID'           => $post_id,
					'post_content' => $new_content['post_content'],
				);
					wp_update_post( $pars, true );
					// Aggiorna titolo.
					$this->update_title( $post_id, $post_title );
					// Aggiorna campi personalizzati.
					$this->update_custom_fields( $post_id, $item );
				$updated = true;
			} else {
				$ignored = true;
			}
		}
		return $post_id;
	}

	/**
	 * Create or update the English translation of an imported patent post, when available.
	 *
	 * @param int    $post_id Post ID of the Italian version.
	 * @param object $item    Raw feed item (displayValue_en, abstract_en, ...).
	 * @param array  $conf    Import configuration (import_action, ...).
	 * @param string $lang    Language slug to assign to the created translation.
	 * @return int Post ID of the created/matched English translation, or 0 when no English title exists.
	 * @throws Exception When wp_insert_post() fails.
	 */
	private function _translate_content( $post_id, $item, $conf, $lang = 'en' ): int {
		$display_value_en  = isset( $item->displayValue_en ) ? trim( (string) $item->displayValue_en ) : '';
		$translate_content = ( '' !== $display_value_en );
		$new_content_en    = null;
		$post_id_en        = 0;

		// Si crea la versione inglese solo se c'è il titolo in inglese.
		if ( $translate_content ) {
			$post_title_en       = $this->sanitize_import_title( $display_value_en );
			$post_name_en        = dli_generate_slug( $post_title_en );
				$post_content_en = wp_kses_post( $item->abstract_en ?? '.' );
			$contents            = dli_get_post_translations( $post_id );

			if ( ! isset( $contents[ $lang ] ) ) {
				// Crea nuova versione in inglese.
				$new_content_en = array(
					'post_type'    => $this->post_type,
					'post_name'    => $post_name_en,
					'post_title'   => $post_title_en,
					'post_content' => $post_content_en,
					'post_status'  => 'draft',
					'post_parent'  => 0,
				);

					// Associa versione italiana e versione inglese.
					$post_id_en = wp_insert_post( $new_content_en, true );
				if ( is_wp_error( $post_id_en ) || ! $post_id_en ) {
					$error_message = is_wp_error( $post_id_en ) ? $post_id_en->get_error_message() : 'ID = 0';
					throw new Exception( 'wp_insert_post (EN) fallito: ' . $error_message );
				}

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
				$post_id_en     = $contents[ $lang ];
				$update_content = ( 'update' === $conf['import_action'] ) ? true : false;
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

	/**
	 * Update a patent post's title.
	 *
	 * @param int    $post_id Post ID to update.
	 * @param string $title   New post title.
	 * @return void
	 */
	private function update_title( $post_id, $title ) {
		// Dati da aggiornare.
		$updated_post = array(
			'ID'         => $post_id,
			'post_title' => $title,
		);
		// Aggiorna il post.
		wp_update_post( $updated_post, true );
	}

	/**
	 * Populate the ACF fields of a patent post (common fields plus per-language ones).
	 *
	 * @param int    $post_id Post ID to update.
	 * @param object $item    Raw feed item.
	 * @param string $lang    Language of the post being updated ('it' or any other value for English).
	 * @return void
	 */
	private function update_custom_fields( $post_id, $item, $lang = 'it' ) {

		// Aggiornamento campi generici.
		$this->_update_common_fields( $post_id, $item );

		// Aggiornamento campi differenti per lingua.
		if ( 'it' === $lang ) {
			$this->_update_fields_it( $post_id, $item );
		} else {
			$this->_update_fields_en( $post_id, $item );
		}
	}

	/**
	 * Populate the ACF fields shared by both language versions of a patent
	 * (code, application number, family, deposit date, priority flag, inventors, owners).
	 *
	 * @param int    $post_id Post ID to update.
	 * @param object $item    Raw feed item.
	 * @return void
	 */
	private function _update_common_fields( $post_id, $item ) {
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
			$deposit_date = $this->format_import_date( (string) $dp_date_str, 'd-m-Y', 'Ymd' );
			if ( $deposit_date ) {
				dli_update_field( 'data_deposito', $deposit_date, $post_id );
			}
			// Anno Deposito (anno_deposito).
			$deposit_year = $this->format_import_date( (string) $dp_date_str, 'd-m-Y', 'Y' );
			if ( $deposit_year ) {
				dli_update_field( 'anno_deposito', $deposit_year, $post_id );
			}
		}

		// isPriority.
		if ( $item->isPriority ) {
			dli_update_field( 'prioritario', true, $post_id );
		} else {
			dli_update_field( 'prioritario', false, $post_id );
		}

		// Inventori referenti e non (inventori_referenti, inventori).
		$inv_ref = array();
		$inv     = array();
		if ( $item->ownerSet && count( $item->ownerSet ) > 0 ) {
			foreach ( $item->ownerSet as $i ) {
				$inv_name = $i->person->firstName . ' ' . $i->person->lastName;
				if ( 'Titolare' === $i->role->description ) {
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
		if ( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet && count( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet ) > 0 ) {
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

	/**
	 * Populate the Italian-only ACF fields of a patent post (legal status, thematic areas).
	 *
	 * @param int    $post_id Post ID to update.
	 * @param object $item    Raw feed item.
	 * @return void
	 */
	private function _update_fields_it( $post_id, $item ) {
		$lang = 'it';
		// Stato Legale (stato_legale).
		dli_update_field( 'stato_legale', $item->legal_status, $post_id );
		// Aree tematiche (area_tematica).
		$thematic_area_list_str = $item->thematic_area_list;
		if ( $thematic_area_list_str ) {
			$thematic_areas = explode( '###', $thematic_area_list_str );
			$term_ids       = array();
			foreach ( $thematic_areas as $term ) {
				$term_id = $this->get_or_create_term_id( (string) $term, THEMATIC_AREA_TAXONOMY );
				dli_set_term_language( $term_id, $lang );
				$term_ids[] = (int) $term_id;
			}
			$term_ids = array_values( array_unique( array_filter( $term_ids ) ) );
			if ( ! empty( $term_ids ) ) {
				// Associo tutte le tassonomie al contenuto in una sola chiamata.
				wp_set_post_terms( $post_id, $term_ids, THEMATIC_AREA_TAXONOMY, false );
			}
		}
	}

	/**
	 * Populate the English-only ACF fields of a patent post (translated legal status,
	 * thematic areas translated to their English term).
	 *
	 * @param int    $post_id Post ID to update.
	 * @param object $item    Raw feed item.
	 * @return void
	 */
	private function _update_fields_en( $post_id, $item ) {
		$lang = 'en';
		// Stato Legale (stato_legale).
		$legal_status_en = isset( DLI_LEGAL_STATUS_EN[ $item->legal_status ] ) ? DLI_LEGAL_STATUS_EN[ $item->legal_status ] : '';
		if ( '' !== $legal_status_en ) {
			dli_update_field( 'stato_legale', $legal_status_en, $post_id );
		}

		// Aree tematiche (area_tematica).
		$thematic_area_list_str = $item->thematic_area_list;
		if ( $thematic_area_list_str ) {
			$thematic_areas = explode( '###', $thematic_area_list_str );
			$term_ids_en    = array();
			foreach ( $thematic_areas as $term ) {
				$term_id    = $this->get_or_create_term_id( (string) $term, THEMATIC_AREA_TAXONOMY );
				$trans      = $term_id ? dli_get_term_translations( $term_id ) : array();
				$term_id_en = isset( $trans[ $lang ] ) ? $trans[ $lang ] : 0;
				if ( $term_id_en ) {
					$term_ids_en[] = (int) $term_id_en;
				}
			}
			$term_ids_en = array_values( array_unique( array_filter( $term_ids_en ) ) );
			if ( ! empty( $term_ids_en ) ) {
				// Associo tutte le tassonomie tradotte al contenuto in una sola chiamata.
				wp_set_post_terms( $post_id, $term_ids_en, THEMATIC_AREA_TAXONOMY, false );
			}
		}
	}

	/**
	 * Look up the WordPress post already imported for a given IRIS patent code.
	 *
	 * @param object $item Raw feed item.
	 * @return int Post ID, or 0 if not found.
	 */
	private function get_wp_content_id( $item ) {
		$args  = array(
			'post_type'      => $this->post_type,
			'meta_query'     => array(
				array(
					'key'     => 'codice_brevetto',
					'value'   => $item->pid,
					'compare' => '=',
				),
			),
			'posts_per_page' => 1,
			'post_status'    => 'any',
		);
		$query = new WP_Query( $args );
		return $query->found_posts ? $query->posts[0]->ID : 0;
	}



	// *** Funzioni di utilità dedicate *** //
}
