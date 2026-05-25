<?php
/**
 * People file importer — processes a single CSV row for dry-run and commit.
 *
 * Handles existence lookup, action decision, post creation/update,
 * ACF field writing, taxonomy assignment, and Polylang language setting.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Processes individual CSV rows for the Importa → Persone tool.
 *
 * Deliberately separate from DLI_BaseImporter (which targets scheduled
 * API/job imports). This class handles one-shot manual file imports
 * executed from the admin UI.
 */
class DLI_People_File_Importer {

	/**
	 * Allowed values for the `stato` column.
	 *
	 * @var string[]
	 */
	private static $allowed_stati = array( 'publish', 'draft' );

	/**
	 * Process one parsed CSV data row.
	 *
	 * Returns an array with:
	 *   - 'action'    string  created|updated|skipped|error
	 *   - 'log_entry' array   row data for the UI report
	 *
	 * @param array  $data        Associative field data for the row.
	 * @param string $import_mode upsert|solo_nuovi|aggiorna_esistenti|ignora_esistenti
	 * @param string $exec_mode   dry_run|commit
	 * @param int    $row_num     1-indexed CSV row number (including header).
	 * @return array
	 */
	public static function process_row( array $data, $import_mode, $exec_mode, $row_num ) {
		$display_name = trim( $data['nome'] . ' ' . $data['cognome'] );
		$email        = $data['email'];

		// Validate required fields.
		if ( empty( $data['nome'] ) || empty( $data['cognome'] ) || empty( $email ) || ! is_email( $email ) ) {
			return self::make_result(
				'error',
				$row_num,
				$display_name,
				$email,
				'ERRORE',
				__( 'Campi obbligatori mancanti o email non valida.', 'design_laboratori_italia' )
			);
		}

		// Look up existing persona by email.
		$existing_id = self::find_by_email( $email );
		$exists      = null !== $existing_id;

		// Determine action from import_mode + existence.
		$should_create = false;
		$should_update = false;
		$skip_reason   = '';

		switch ( $import_mode ) {
			case 'upsert':
				$exists ? $should_update = true : $should_create = true;
				break;
			case 'solo_nuovi':
				$exists ? $skip_reason = 'existing' : $should_create = true;
				break;
			case 'aggiorna_esistenti':
				$exists ? $should_update = true : $skip_reason = 'not_found';
				break;
			case 'ignora_esistenti':
				$exists ? $skip_reason = 'existing' : $should_create = true;
				break;
		}

		if ( $skip_reason ) {
			$message = ( 'existing' === $skip_reason )
				? __( 'Saltata: persona già esistente.', 'design_laboratori_italia' )
				: __( 'Saltata: persona non trovata.', 'design_laboratori_italia' );
			return self::make_result( 'skipped', $row_num, $display_name, $email, 'SALTATA', $message );
		}

		// Dry-run: report the intended action without writing.
		if ( 'dry_run' === $exec_mode ) {
			if ( $should_create ) {
				return self::make_result( 'created', $row_num, $display_name, $email, 'OK', __( 'Creerebbe la persona (dry-run).', 'design_laboratori_italia' ) );
			}
			return self::make_result( 'updated', $row_num, $display_name, $email, 'OK', __( 'Aggiornerebbe la persona (dry-run).', 'design_laboratori_italia' ) );
		}

		// Commit: write to the database.
		if ( $should_create ) {
			return self::do_create( $data, $row_num, $display_name, $email );
		}
		return self::do_update( $existing_id, $data, $row_num, $display_name, $email );
	}

	// ── Private helpers ────────────────────────────────────────────────────

	/**
	 * Find an existing persona post by ACF email field.
	 *
	 * @param string $email Email address to search for.
	 * @return int|null Post ID or null if not found.
	 */
	private static function find_by_email( $email ) {
		$posts = get_posts(
			array(
				'post_type'     => PEOPLE_POST_TYPE,
				'post_status'   => 'any',
				'meta_key'      => 'email',
				'meta_value'    => $email,
				'fields'        => 'ids',
				'numberposts'   => 1,
				'no_found_rows' => true,
			)
		);
		return ! empty( $posts ) ? (int) $posts[0] : null;
	}

	/**
	 * Create a new persona post and populate all fields.
	 *
	 * @param array  $data        CSV row data.
	 * @param int    $row_num     Row number for the log entry.
	 * @param string $display_name Nome Cognome string.
	 * @param string $email       Email address.
	 * @return array Result array.
	 */
	private static function do_create( array $data, $row_num, $display_name, $email ) {
		$status  = in_array( $data['stato'], self::$allowed_stati, true ) ? $data['stato'] : 'publish';
		$post_id = wp_insert_post(
			array(
				'post_type'    => PEOPLE_POST_TYPE,
				'post_title'   => sanitize_text_field( $display_name ),
				'post_content' => wp_kses_post( $data['body'] ),
				'post_status'  => $status,
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$error_msg = is_wp_error( $post_id ) ? $post_id->get_error_message() : __( 'Errore sconosciuto.', 'design_laboratori_italia' );
			return self::make_result( 'error', $row_num, $display_name, $email, 'ERRORE', $error_msg );
		}

		self::set_acf_fields( $post_id, $data );
		self::set_struttura( $post_id, $data['struttura'] );
		self::set_language( $post_id );

		return self::make_result( 'created', $row_num, $display_name, $email, 'OK', __( 'Persona creata.', 'design_laboratori_italia' ) );
	}

	/**
	 * Update an existing persona post and its fields.
	 *
	 * @param int    $post_id     Existing post ID.
	 * @param array  $data        CSV row data.
	 * @param int    $row_num     Row number for the log entry.
	 * @param string $display_name Nome Cognome string.
	 * @param string $email       Email address.
	 * @return array Result array.
	 */
	private static function do_update( $post_id, array $data, $row_num, $display_name, $email ) {
		$update_args = array( 'ID' => $post_id );

		if ( '' !== $data['body'] ) {
			$update_args['post_content'] = wp_kses_post( $data['body'] );
		}
		if ( in_array( $data['stato'], self::$allowed_stati, true ) ) {
			$update_args['post_status'] = $data['stato'];
		}

		$result = wp_update_post( $update_args, true );
		if ( is_wp_error( $result ) ) {
			return self::make_result( 'error', $row_num, $display_name, $email, 'ERRORE', $result->get_error_message() );
		}

		self::set_acf_fields( $post_id, $data );
		self::set_struttura( $post_id, $data['struttura'] );

		return self::make_result( 'updated', $row_num, $display_name, $email, 'OK', __( 'Persona aggiornata.', 'design_laboratori_italia' ) );
	}

	/**
	 * Write all ACF scalar, boolean, and relationship fields.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $data    CSV row data.
	 * @return void
	 */
	private static function set_acf_fields( $post_id, array $data ) {
		if ( ! function_exists( 'update_field' ) ) {
			return;
		}

		// Scalar text fields.
		$scalar_fields = array( 'nome', 'cognome', 'email', 'titolo', 'telefono' );
		foreach ( $scalar_fields as $field ) {
			if ( '' !== $data[ $field ] ) {
				update_field( $field, sanitize_text_field( $data[ $field ] ), $post_id );
			}
		}

		// URL field.
		if ( '' !== $data['sito_web'] ) {
			update_field( 'sito_web', esc_url_raw( $data['sito_web'] ), $post_id );
		}

		// Boolean fields: '1' → 1, anything else (including empty) → 0.
		$bool_fields = array( 'escludi_da_elenco', 'disattiva_pagina_dettaglio' );
		foreach ( $bool_fields as $field ) {
			update_field( $field, ( '1' === $data[ $field ] ) ? 1 : 0, $post_id );
		}

		// Relationship: tipologia_persona → ACF field categoria_appartenenza.
		if ( '' !== $data['tipologia_persona'] ) {
			$tipologia = get_posts(
				array(
					'post_type'     => PEOPLE_TYPE_POST_TYPE,
					'name'          => sanitize_title( $data['tipologia_persona'] ),
					'numberposts'   => 1,
					'fields'        => 'ids',
					'no_found_rows' => true,
				)
			);
			if ( ! empty( $tipologia ) ) {
				update_field( 'categoria_appartenenza', array( (int) $tipologia[0] ), $post_id );
			}
		}
	}

	/**
	 * Assign struttura taxonomy terms (multiple values separated by |).
	 *
	 * @param int    $post_id       Post ID.
	 * @param string $struttura_raw Raw string from CSV (e.g. "lab-abc|lab-xyz").
	 * @return void
	 */
	private static function set_struttura( $post_id, $struttura_raw ) {
		if ( '' === $struttura_raw ) {
			return;
		}

		$slugs    = array_filter( array_map( 'trim', explode( '|', $struttura_raw ) ) );
		$term_ids = array();

		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', sanitize_title( $slug ), STRUCTURE_TAXONOMY );
			if ( $term && ! is_wp_error( $term ) ) {
				$term_ids[] = $term->term_id;
			}
		}

		if ( ! empty( $term_ids ) ) {
			wp_set_post_terms( $post_id, $term_ids, STRUCTURE_TAXONOMY );
		}
	}

	/**
	 * Set the Polylang language to the site default (if Polylang is active).
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	private static function set_language( $post_id ) {
		if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) ) {
			pll_set_post_language( $post_id, pll_default_language() );
		}
	}

	/**
	 * Build a standardised result array.
	 *
	 * @param string $action       created|updated|skipped|error
	 * @param int    $row_num      CSV row number.
	 * @param string $display_name Nome Cognome.
	 * @param string $email        Email address.
	 * @param string $status       OK|ERRORE|SALTATA
	 * @param string $message      Human-readable log message.
	 * @return array
	 */
	private static function make_result( $action, $row_num, $display_name, $email, $status, $message ) {
		return array(
			'action'    => $action,
			'log_entry' => array(
				'row'     => $row_num,
				'name'    => $display_name,
				'email'   => $email,
				'status'  => $status,
				'message' => $message,
			),
		);
	}
}
