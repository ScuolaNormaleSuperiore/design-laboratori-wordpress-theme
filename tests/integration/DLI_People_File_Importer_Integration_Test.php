<?php
/**
 * The import of the people from a file (Strumenti DLI → Importa → Persone): what one row of the file does.
 *
 * These tests describe the behaviour as it is, so that the import can be shared with other tools without changing it.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_People_File_Importer::process_row()` on the real people post type, with ACF and Polylang.
 */
class DLI_People_File_Importer_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * A row of the file, as the reader of the tool hands it to the importer.
	 *
	 * @param array<string, string> $values Values that replace the default ones.
	 * @return array<string, string> The row.
	 */
	private function row( array $values = array() ) {
		return array_merge(
			array(
				'nome'                       => 'Ada',
				'cognome'                    => 'Lovelace',
				'email'                      => 'ada.lovelace@example.org',
				'titolo'                     => 'Dott.ssa',
				'telefono'                   => '050 000000',
				'tipologia_persona'          => '',
				'struttura'                  => '',
				'stato'                      => 'publish',
				'sito_web'                   => 'https://example.org/ada',
				'body'                       => '<p>Matematica</p>',
				'escludi_da_elenco'          => '0',
				'disattiva_pagina_dettaglio' => '0',
			),
			$values
		);
	}

	/**
	 * Every person on the site, in any status.
	 *
	 * @return int[]
	 */
	private function people() {
		return get_posts(
			array(
				'post_type'   => PEOPLE_POST_TYPE,
				'post_status' => 'any',
				'numberposts' => -1,
				'lang'        => '',
				'fields'      => 'ids',
			)
		);
	}

	/**
	 * Run a row and give back the action.
	 *
	 * @param array<string, string> $data        The row.
	 * @param string                $import_mode Mode of the import.
	 * @param string                $exec_mode   `dry_run` or `commit`.
	 * @return array{action: string, log_entry: array<string, mixed>} The result.
	 */
	private function import_row( array $data, $import_mode, $exec_mode ) {
		return DLI_People_File_Importer::process_row( $data, $import_mode, $exec_mode, 2 );
	}

	/**
	 * A row without a name, a surname or a valid email is an error, in every mode, and writes nothing.
	 */
	public function test_a_row_without_the_required_fields_is_an_error() {
		foreach ( array( 'nome', 'cognome', 'email' ) as $field ) {
			$result = $this->import_row( $this->row( array( $field => '' ) ), 'upsert', 'commit' );
			$this->assertSame( 'error', $result['action'], "Without $field." );
			$this->assertSame( 'ERRORE', $result['log_entry']['status'] );
		}
		$this->assertSame( 'error', $this->import_row( $this->row( array( 'email' => 'non-una-email' ) ), 'solo_nuovi', 'commit' )['action'], 'An email that is not valid.' );
		$this->assertSame( array(), $this->people(), 'Nothing was written.' );
	}

	/**
	 * The simulation says what would happen and writes nothing, whether the person exists or not.
	 */
	public function test_dry_run_reports_the_action_and_writes_nothing() {
		$new = $this->import_row( $this->row(), 'upsert', 'dry_run' );
		$this->assertSame( 'created', $new['action'] );
		$this->assertSame( 'OK', $new['log_entry']['status'] );
		$this->assertSame( array(), $this->people(), 'The simulation did not create the person.' );

		$this->import_row( $this->row(), 'upsert', 'commit' );
		$before = $this->people();
		$this->assertCount( 1, $before );

		$existing = $this->import_row( $this->row( array( 'cognome' => 'Byron' ) ), 'upsert', 'dry_run' );
		$this->assertSame( 'updated', $existing['action'] );
		$this->assertSame( 'Ada Lovelace', get_the_title( $before[0] ), 'The simulation did not change the person.' );
		$this->assertSame( $before, $this->people() );
	}

	/**
	 * A new person gets its title, its fields, its status and the default language.
	 */
	public function test_commit_creates_the_person_with_title_fields_status_and_language() {
		$result = $this->import_row( $this->row(), 'solo_nuovi', 'commit' );

		$this->assertSame( 'created', $result['action'] );
		$this->assertSame( 2, $result['log_entry']['row'] );
		$this->assertSame( 'ada.lovelace@example.org', $result['log_entry']['email'] );

		$people = $this->people();
		$this->assertCount( 1, $people );
		$person = get_post( $people[0] );
		$this->assertSame( 'Ada Lovelace', $person->post_title );
		$this->assertSame( 'publish', $person->post_status );
		$this->assertSame( '<p>Matematica</p>', $person->post_content );
		$this->assertSame( 'ada.lovelace@example.org', get_post_meta( $person->ID, 'email', true ) );
		$this->assertSame( 'Dott.ssa', get_post_meta( $person->ID, 'titolo', true ) );
		$this->assertSame( 'https://example.org/ada', get_post_meta( $person->ID, 'sito_web', true ) );
		$this->assertSame( pll_default_language(), pll_get_post_language( $person->ID ) );
	}

	/**
	 * The status is the one of the file when it is allowed (publish or draft) and publish otherwise.
	 */
	public function test_status_is_publish_or_draft_and_publish_for_any_other_value() {
		$this->import_row( $this->row( array( 'stato' => 'draft' ) ), 'solo_nuovi', 'commit' );
		$this->import_row( $this->row( array( 'email' => 'b@example.org', 'cognome' => 'B', 'stato' => 'private' ) ), 'solo_nuovi', 'commit' );

		$statuses = array();
		foreach ( $this->people() as $person_id ) {
			$statuses[ get_post_meta( $person_id, 'email', true ) ] = get_post_status( $person_id );
		}
		$this->assertSame(
			array(
				'ada.lovelace@example.org' => 'draft',
				'b@example.org'            => 'publish',
			),
			$statuses
		);
	}

	/**
	 * The two flags are 1 only when the cell says `1`; the structures and the type are found by their slug.
	 */
	public function test_flags_structures_and_type_are_written() {
		wp_insert_term( 'Laboratorio ABC', STRUCTURE_TAXONOMY, array( 'slug' => 'lab-abc' ) );
		wp_insert_term( 'Laboratorio XYZ', STRUCTURE_TAXONOMY, array( 'slug' => 'lab-xyz' ) );
		$type_id = self::factory()->post->create(
			array(
				'post_type'  => PEOPLE_TYPE_POST_TYPE,
				'post_title' => 'Docenti',
				'post_name'  => 'docenti',
			)
		);

		$this->import_row(
			$this->row(
				array(
					'escludi_da_elenco'          => '1',
					'disattiva_pagina_dettaglio' => 'si',
					'struttura'                  => 'lab-abc|lab-xyz|non-esiste',
					'tipologia_persona'          => 'docenti|non-esiste',
				)
			),
			'solo_nuovi',
			'commit'
		);

		$person_id = $this->people()[0];
		$this->assertSame( '1', (string) get_post_meta( $person_id, 'escludi_da_elenco', true ) );
		$this->assertSame( '0', (string) get_post_meta( $person_id, 'disattiva_pagina_dettaglio', true ), 'Anything but 1 is 0.' );
		$terms = wp_get_post_terms( $person_id, STRUCTURE_TAXONOMY, array( 'fields' => 'slugs' ) );
		sort( $terms );
		$this->assertSame( array( 'lab-abc', 'lab-xyz' ), $terms, 'The slug that does not exist is ignored.' );
		$this->assertSame( array( $type_id ), array_map( 'intval', (array) get_field( 'categoria_appartenenza', $person_id, false ) ) );
	}

	/**
	 * Each mode decides what to do with a person who exists (found by email) and with one who does not.
	 */
	public function test_each_mode_decides_by_the_existence_of_the_person() {
		$this->import_row( $this->row(), 'solo_nuovi', 'commit' );
		$person_id = $this->people()[0];
		$changed   = $this->row( array( 'cognome' => 'King', 'body' => '<p>Nuovo testo</p>' ) );
		$another   = $this->row( array( 'email' => 'new@example.org', 'nome' => 'Anna', 'cognome' => 'Nuova' ) );

		// The person who exists.
		$this->assertSame( 'skipped', $this->import_row( $changed, 'solo_nuovi', 'commit' )['action'] );
		$this->assertSame( 'skipped', $this->import_row( $changed, 'ignora_esistenti', 'commit' )['action'] );
		$this->assertSame( 'Ada Lovelace', get_the_title( $person_id ), 'The modes that skip did not change the person.' );

		$this->assertSame( 'updated', $this->import_row( $changed, 'aggiorna_esistenti', 'commit' )['action'] );
		$this->assertSame( 'Ada King', get_the_title( $person_id ) );
		$this->assertSame( '<p>Nuovo testo</p>', get_post( $person_id )->post_content );

		$this->assertSame( 'updated', $this->import_row( $this->row( array( 'cognome' => 'Byron' ) ), 'upsert', 'commit' )['action'] );
		$this->assertSame( 'Ada Byron', get_the_title( $person_id ) );

		// The person who does not exist.
		$this->assertSame( 'skipped', $this->import_row( $another, 'aggiorna_esistenti', 'commit' )['action'], 'Only updating: a new person is skipped.' );
		$this->assertCount( 1, $this->people() );
		$this->assertSame( 'created', $this->import_row( $another, 'upsert', 'commit' )['action'] );
		$this->assertCount( 2, $this->people() );
		$this->assertSame( 'created', $this->import_row( $this->row( array( 'email' => 'x@example.org', 'cognome' => 'X' ) ), 'ignora_esistenti', 'commit' )['action'] );
		$this->assertCount( 3, $this->people() );
	}

	/**
	 * An update keeps the status when the file has no allowed one, and the language it had.
	 */
	public function test_update_keeps_the_status_when_the_file_has_no_allowed_status() {
		$this->import_row( $this->row( array( 'stato' => 'draft' ) ), 'solo_nuovi', 'commit' );
		$person_id = $this->people()[0];

		$this->import_row( $this->row( array( 'stato' => 'boh', 'cognome' => 'Byron' ) ), 'aggiorna_esistenti', 'commit' );
		$this->assertSame( 'draft', get_post_status( $person_id ), 'A status that is not allowed leaves the one the person has.' );

		$this->import_row( $this->row( array( 'stato' => 'publish' ) ), 'aggiorna_esistenti', 'commit' );
		$this->assertSame( 'publish', get_post_status( $person_id ) );
	}

	/**
	 * An email that two people have is not guessed: the row is an error and nothing changes.
	 */
	public function test_an_email_of_two_people_is_an_error_without_changes() {
		$first  = $this->create_post( PEOPLE_POST_TYPE, 'Persona uno' );
		$second = $this->create_post( PEOPLE_POST_TYPE, 'Persona due' );
		$this->set_acf_fields( $first, array( 'email' => 'doppia@example.org' ) );
		$this->set_acf_fields( $second, array( 'email' => 'doppia@example.org' ) );

		$result = $this->import_row( $this->row( array( 'email' => 'doppia@example.org' ) ), 'upsert', 'commit' );

		$this->assertSame( 'error', $result['action'] );
		$this->assertSame( 'Persona uno', get_the_title( $first ) );
		$this->assertSame( 'Persona due', get_the_title( $second ) );
		$this->assertCount( 2, $this->people() );
	}
}
