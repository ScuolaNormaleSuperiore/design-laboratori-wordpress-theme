<?php
/**
 * Definition of the IRIS Manager used to import IRIS patents.
 *
 * @package Design_Laboratori_Italia
 */

require_once 'class-base-importer.php';

// @TODO: Rifattorizzare class-irismanager e class-irispatentimporter.
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
		$post_name = dli_generate_slug( $item->title );
		$new_content = array(
			'post_type'    => $this->post_type,
			'post_name'    => $post_name,
			'post_title'   => $item->displayValue,
			'post_content' => $item->abstract,
			'post_status'  => 'draft',
			'post_parent'  => 0,
		);
		$update_content = ( $conf['action'] === 'update' ) ? true : false;

		// Verifico l'esistenza dell'oggetto su Worpress.
		$post_id = $this->get_wp_content_id( $item );
		if ( ! $post_id ) {
			$post_id = wp_insert_post( $new_content );
			$updated = false;
			// $this->update_custom_fields( $post_id, $item );
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
				// $this->update_custom_fields( $post_id, $item );
				$updated = true;
			} else {
				$ignored = true;
			}
		}
		return $post_id;
	}

	private function get_wp_content_id( $item ){
		$item_code  = 'PAT-' . $item->id; //@TODO: Remove this (use pid).
		$args = array(
			'post_type' => $this->post_type,
			'meta_query' => array(
				array(
					'key'   => 'codice_brevetto',
					'value' => $item_code,
					'compare' => '='
				)
			),
			'posts_per_page' => 1 // Limita a un solo risultato
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

		$data = array_slice($data, 0, 2);
		foreach ( $data as $item ){
			$counter++;
			// @TODO Aggiungere PID al json.
			$item_code  = 'PAT-' . $item->id;
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
