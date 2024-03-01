<?php


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

	public function subscribe_user() {
		$result = array();
		try {
			if ( dli_get_option( 'newsletter_enabled', 'setup' ) === 'true' ) {

				$url           = 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation';
				$list_ids      = array();
				$api_token     = dli_get_option( 'newsletter_api_token', 'setup' );
				$newsletter_id = dli_get_option( 'newsletter_list_id', 'setup' );
				$template_id   = dli_get_option( 'newsletter_template_id', 'setup' );
				array_push( $list_ids, $newsletter_id );
				$page_url      = get_permalink( get_page_by_path( 'newsletter' ) );
				$redirect_url = $page_url . '?after_confirm=yes';
				$data = array(
					'attributes' => array(
						'FNAME'    => $this->data->get_user_name(),
						'LNAME'    => $this->data->get_user_surname(),
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
			}

			$result['code']    = $response['response']['code'];
			$result['message'] = $response['response']['message'];

		} catch ( Exception $e ) {

			$result['code']    = '500';
			$result['message'] = $e->getMessage();

		}

		return $result;
	}

}
