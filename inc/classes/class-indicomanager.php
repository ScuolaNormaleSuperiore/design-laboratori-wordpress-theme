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
		$code   = 200;
		$result = '';
		$msg    = __( 'Dati importati con successo', 'design_laboratori_italia' );

		// Verifica modulo abilitato.

		// Recupero parametri di configurazione.

		// Creazione del client.

		// Invocazione del servizio.

		// Recupero immagini.

		$response_data = array(
				'code'    => $code,
				'message' => $msg,
				'result'  => $result,
		);
		return new WP_REST_Response( $response_data, $code );
	}

}
