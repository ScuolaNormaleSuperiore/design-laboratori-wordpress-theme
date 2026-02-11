<?php
/**
 * Definition of the Newsletter Manager used to manage the external newsletter API.
 *
 * @package Design_Laboratori_Italia
 */

class NewsletterData {

	private string $user_name;
	private string $user_surname;
	private string $user_mail;
	private string $user_phone;

	public function __construct( string $user_name, string $user_surname, string $user_mail, string $user_phone ) {
		$this->user_name    = $user_name;
		$this->user_surname = $user_surname;
		$this->user_mail    = $user_mail;
		$this->user_phone   = $user_phone;
	}

	public function get_user_name() {
		return $this->user_name;
	}
	public function get_user_surname() {
		return $this->user_surname;
	}
	public function get_user_mail() {
		return $this->user_mail;
	}
	public function get_user_phone() {
		return $this->user_phone;
	}

	public function set_name( $user_name ) {
		$this->user_name = $user_name;
	}
	public function set_user_surname( $user_surname ) {
		$this->user_surname = $user_surname;
	}
	public function set_user_mail( $user_mail ) {
		$this->user_mail = $user_mail;
	}
	public function set_user_phone( $user_phone ) {
		$this->user_phone = $user_phone;
	}
}

/**
 * The Newsletter manager.
 */
class Newsletter_Manager {

	private array $errors = array();
	private NewsletterData $data;

	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {

	}

	/**
	 * Imposta i dati della form
	 *
	 * @param NewsletterData $data
	 * @return void
	 */
	public function setup( string $user_name, string $user_surname, string $user_mail, string $user_phone ) {
		$this->data = new NewsletterData( $user_name, $user_surname, $user_mail, $user_phone );
	}

	/**
	 * Valida il form di registrazione alla newsletter.
	 *
	 * @return array
	 */
	public function validate() {
		if ( '' === $this->data->get_user_name() || '' ===  $this->data->get_user_surname() ) {
			array_push( $this->errors, __( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' ) );
		}

		if ( ( ! filter_var( $this->data->get_user_mail(), FILTER_VALIDATE_EMAIL ) ) ) {
			array_push( $this->errors, __( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' ) );
		}
		return count( $this->errors ) === 0 ? false : true;
	}

	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Subscribes a user to the newsletter.
	 *
	 * @return array
	 */
	public function subscribe_user() {
		$result = array(
			'code'    => 0,
			'message' => '',
		);
		try {
			if ( dli_get_option( 'newsletter_enabled', 'setup' ) === 'true' ) {

				$url           = 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation';
				$list_ids      = array();
				$api_token     = dli_get_option( 'newsletter_api_token', 'setup' );
				$newsletter_id = dli_get_option( 'newsletter_list_id', 'setup' );
				$template_id   = dli_get_option( 'newsletter_template_id', 'setup' );
				array_push( $list_ids, $newsletter_id );
				$current_language = dli_current_language( 'slug' );
				$page_url         = dli_get_newsletter_link( $current_language );
				$redirect_url     = $page_url . '?after_confirm=yes';
				$data = array(
					'attributes'  => array(
						'FNAME'     => $this->data->get_user_name(),
						'LNAME'     => $this->data->get_user_surname(),
						'FIRSTNAME' => $this->data->get_user_name(),
						'LASTNAME'  => $this->data->get_user_surname(),
						'NOME'      => $this->data->get_user_name(),
						'COGNOME'   => $this->data->get_user_surname(),
						'NAME'      => $this->data->get_user_name(),
						'SURNAME'   => $this->data->get_user_surname(),
					),
					'email'          => $this->data->get_user_mail(),
					'includeListIds' => $list_ids,
					'templateId'     => $template_id,
					'redirectionUrl' => $redirect_url,
				);
				$json_data = json_encode( $data );
				$header = array(
					'Accept'       => 'application/json',
					'api-key'      => $api_token,
					'Content-Type' => 'application/json',
				);

				// Chiamata al web service.
				$response = wp_remote_post(
					$url,
					array(
						'body' => $json_data,
						'headers' => $header,
					)
				);

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				$result['code']    = wp_remote_retrieve_response_code( $response );
				$result['message'] = wp_remote_retrieve_response_message( $response );
			} else {
				$result['code']    = 400;
				$result['message'] = __( 'Newsletter is disabled.', 'design_laboratori_italia' );
			}

		} catch ( Exception $e ) {

			$result['code']    = '500';
			$result['message'] = $e->getMessage();

		}

		return $result;
	}

}
