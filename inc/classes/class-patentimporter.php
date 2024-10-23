<?php
/**
 * Definition of the IRIS Manager used to import IRIS patents.
 *
 * @package Design_Laboratori_Italia
 */

require_once 'class-base-importer.php';

// @TODO: Rifattorizzare class-irismanager e class-patentimporter.
// .
// Definizione delle costanti in class.irismanager.php.

class DLI_IrisPatentImporter extends DLI_BaseImporter {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
		$this->job_name       = 'dli_iris_patent_import_job';
		$this->endpoint       = '/iris-patent-import';
		$this->debug_enabled  = false;
		$this->module_enabled = false;
		$this->post_type      = PATENT_POST_TYPE;
		// Schedule import.
		// @TODO: $this->manage_import_job();
		$this->debug_enabled  = ( dli_get_option( 'iris_debug_enabled', 'iris' ) === 'true' );
		$this->module_enabled = ( dli_get_option( 'iris_brevetti_enabled', 'iris' ) === 'true' );
	}

	public function import( $request ) {
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
	 *  Creazione/Modifica dell'post su WordPress
	 * 
	 * @param mixed $item
	 * @param mixed $conf
	 * @param mixed $updated
	 * @param mixed $ignored
	 * @return int
	 */
	private function create_wp_content( $item, $conf, &$updated, &$ignored ): int {
		$post_name    = dli_generate_slug( $item->displayValue );
		$post_content = $item->abstract_en ?? $item->abstract ?? '.';
		$new_content  = array(
			'post_type'    => $this->post_type,
			'post_name'    => $post_name,
			'post_title'   => $item->displayValue,
			'post_content' => $post_content,
			'post_status'  => 'draft',
			'post_parent'  => 0,
		);
		$update_content = ( $conf['import_action'] === 'update' ) ? true : false;

		// Verifico l'esistenza dell'oggetto su Worpress.
		$post_id = $this->get_wp_content_id( $item );
		if ( ! $post_id ) {
			$post_id = wp_insert_post( $new_content );
			$updated = false;
			$this->update_custom_fields( $post_id, $item );
			// La lingua impostata per l'oggetto importato è quella settata come lingua di default.
			$lang = dli_current_language();
			dli_set_post_language( $post_id, $lang );
		} else {
			if ( $update_content ) {
				// Aggiorna i campi del post.
				$pars = array(
					'ID'           => $post_id,
					'post_content' => $new_content['post_content'],
				);
				wp_update_post( $pars );
				$this->update_custom_fields( $post_id, $item );
				$updated = true;
			} else {
				$ignored = true;
			}
		}
		return $post_id;
	}

	private function update_custom_fields( $post_id, $item ){
		// Codice Brevetto (codice_brevetto).
		$item_code = $item->pid;
		dli_update_field( 'codice_brevetto', $item_code, $post_id );
		// Stato Legale (stato_legale).
		dli_update_field( 'stato_legale', $item->legal_status, $post_id );
		// Numero Deposito (numero_deposito).
		dli_update_field( 'numero_deposito', $item->applicationNumber, $post_id );
		if ( $item->id_family ) {
			// Id famiglia (id_famiglia).
			dli_update_field( 'id_famiglia', $item->id_family, $post_id );
		}
		if ( $item->deposit_date ){
			// Data deposito (data_deposito).
			$dp_date_str  = $item->deposit_date;
			$deposit_date = DateTime::createFromFormat('d-m-Y', $dp_date_str )->format('d/m/Y');
			dli_update_field( 'data_deposito', $deposit_date, $post_id );
			// Anno Deposito (anno_deposito).
			$deposit_year = DateTime::createFromFormat('d-m-Y', $dp_date_str )->format('Y');
			dli_update_field( 'anno_deposito', $deposit_year, $post_id );
		}
		// isPriority
		if ( $item->isPriority ) {
			dli_update_field( 'prioritario', true, $post_id );
		} else {
			dli_update_field( 'prioritario', false, $post_id );
		}

		// Inventori referenti e non (inventori_referenti, inventori).
		$inv_ref = array();
		$inv     = array();
		if ( $item->ownerSet && count( $item->ownerSet ) > 0) {
			foreach ( $item->ownerSet as $i ) {
				$inv_name = $i->person->firstName . ' ' . $i->person->lastName;
				if ( $i->role->description === 'Titolare' ) {
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
		if ( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet && count( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet ) > 0) {
			foreach ( $item->ownerPersonCurrentOrLastInternalOrganizationUnitSet as $o ) {
				$tit_name = $o->organizationUnit->description;
				if ( $tit_name ) {
					array_push( $tit, trim( $tit_name ) );
				}
				
			}
		}
		$tit_str = implode( ', ', $tit );
		dli_update_field( 'titolari', $tit_str, $post_id );

		// Aree tematiche (area_tematica)
		$thematic_area_list_str = $item->thematic_area_list;
		if ( $thematic_area_list_str ) {
			$thematic_areas = explode( '###', $thematic_area_list_str );
			foreach ( $thematic_areas as $term ) {
				// $area_slug = dli_generate_slug( trim( $t ) );
				// Controllo esistenza tassonomia
				// Creo tassonomia
				$lang     = dli_current_language();
				$termitem = term_exists( $term, THEMATIC_AREA_TAXONOMY );
				if ( $termitem ) {
					$term_id = $termitem['term_id'];
				} else {
					$new_term = wp_insert_term( $term, THEMATIC_AREA_TAXONOMY );
					$term_id  = $new_term['term_id'];
				}
				dli_set_term_language( $term_id, $lang );
				// Associo la tassonomia al contenuto.
				wp_set_post_terms( $post_id, array( $term_id ), THEMATIC_AREA_TAXONOMY, true );
			}
		}
	}

	private function get_wp_content_id( $item ){
		$args = array(
			'post_type' => $this->post_type,
			'meta_query' => array(
				array(
					'key'     => 'codice_brevetto',
					'value'   => $item->pid,
					'compare' => '='
				)
			),
			'posts_per_page' => 1,
			'post_status'    => 'any',
		);
		$query = new WP_Query($args);
		return $query->found_posts ? $query->posts[0]->ID : 0;
	}

	// *** FUNZIONI DI UTILITA' *** //

	private function execute_import( $conf ): array {
		$this->log_string("*** INIZIO importazione da IRIS (brevetti) ***");
		// Invocazione del web service per recuperare i dati da Iris.
		$data = $this->get_rest_data( $conf['ws_url'], $conf['username'], $conf['password'] );
		$results = array();

		// Loop di importazione.
		$counter   = 0;
		$errors    = 0;
		$processed = 0;
		$updated   = 0;
		$ignored   = 0;

		// @TODO: Remove the following line:
		// $data = array_slice($data, 0, 10);
		foreach ( $data as $item ){
			$counter++;
			$item_code  = $item->pid;
			$item_title = $item->displayValue;

			if ( $conf['import_type'] === 'dryrun' ){
				// Importazione dry run.
				array_push(
					$results,
					MSG_IMPORT_DRY_RUN . $item_code . ' - ' . $item_title,
				);
				$processed++;
			} else {
				// Importazione effettiva.
				try{
					$updated    = false;
					$ignored    = false;
					$item_code = $this->create_wp_content( $item, $conf, $updated, $ignored );
					if ( $updated ) {
						array_push(
							$results,
							MSG_UPDATED_EVENT . $item_code . ' - ' . $item_title,
						);
						$updated++;
					} else if ( $ignored ){
						array_push(
							$results,
							MSG_IGNORED_EVENT . $item_code . ' - ' . $item_title,
						);
						$ignored++;
					} else {
						array_push(
							$results,
							MSG_IMPORTED_EVENT . $item_code . ' - ' . $item_title,
						);
						$processed++;
					}
				} catch ( Exception $e ) {
					array_push(
						$results,
						MSG_ERROR_IMPORTING_EVENT . $item_title . ' - ' . $e->getMessage(),
					);
					$errors++;
				}
			}
		}
		$this->log_string(text: "*** FINE importazione da IRIS (brevetti) ***");
		return $results;
	}

}
