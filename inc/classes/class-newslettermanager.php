<?php
/**
 * Definition of the Newsletter Manager used to manage the external newsletter API.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Value object holding the data submitted through the newsletter subscription form.
 */
class NewsletterData {

	/**
	 * First name.
	 *
	 * @var string
	 */
	private string $user_name;

	/**
	 * Last name.
	 *
	 * @var string
	 */
	private string $user_surname;

	/**
	 * Email address.
	 *
	 * @var string
	 */
	private string $user_mail;

	/**
	 * Phone number.
	 *
	 * @var string
	 */
	private string $user_phone;

	/**
	 * Store the newsletter subscription form data.
	 *
	 * @param string $user_name    First name.
	 * @param string $user_surname Last name.
	 * @param string $user_mail    Email address.
	 * @param string $user_phone   Phone number.
	 */
	public function __construct( string $user_name, string $user_surname, string $user_mail, string $user_phone ) {
		$this->user_name    = $user_name;
		$this->user_surname = $user_surname;
		$this->user_mail    = $user_mail;
		$this->user_phone   = $user_phone;
	}

	/**
	 * Get the first name.
	 *
	 * @return string
	 */
	public function get_user_name() {
		return $this->user_name;
	}
	/**
	 * Get the last name.
	 *
	 * @return string
	 */
	public function get_user_surname() {
		return $this->user_surname;
	}
	/**
	 * Get the email address.
	 *
	 * @return string
	 */
	public function get_user_mail() {
		return $this->user_mail;
	}
	/**
	 * Get the phone number.
	 *
	 * @return string
	 */
	public function get_user_phone() {
		return $this->user_phone;
	}

	/**
	 * Set the first name.
	 *
	 * @param string $user_name First name.
	 * @return void
	 */
	public function set_name( $user_name ) {
		$this->user_name = $user_name;
	}
	/**
	 * Set the last name.
	 *
	 * @param string $user_surname Last name.
	 * @return void
	 */
	public function set_user_surname( $user_surname ) {
		$this->user_surname = $user_surname;
	}
	/**
	 * Set the email address.
	 *
	 * @param string $user_mail Email address.
	 * @return void
	 */
	public function set_user_mail( $user_mail ) {
		$this->user_mail = $user_mail;
	}
	/**
	 * Set the phone number.
	 *
	 * @param string $user_phone Phone number.
	 * @return void
	 */
	public function set_user_phone( $user_phone ) {
		$this->user_phone = $user_phone;
	}
}

/**
 * The Newsletter manager.
 */
class Newsletter_Manager {

	/**
	 * Validation error messages.
	 *
	 * @var array
	 */
	private array $errors = array();

	/**
	 * Submitted form data.
	 *
	 * @var NewsletterData
	 */
	private NewsletterData $data;

	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
	}

	/**
	 * Imposta i dati della form.
	 *
	 * @param string $user_name    First name.
	 * @param string $user_surname Last name.
	 * @param string $user_mail    Email address.
	 * @param string $user_phone   Phone number.
	 * @return void
	 */
	public function setup( string $user_name, string $user_surname, string $user_mail, string $user_phone ) {
		$this->data = new NewsletterData( $user_name, $user_surname, $user_mail, $user_phone );
	}

	/**
	 * Valida il form di registrazione alla newsletter.
	 *
	 * @return bool True if there are validation errors, false otherwise.
	 */
	public function validate() {
		if ( '' === $this->data->get_user_name() || '' === $this->data->get_user_surname() ) {
			array_push( $this->errors, __( 'Compilare tutti i campi obbligatori.', 'design_laboratori_italia' ) );
		}

		if ( ( ! filter_var( $this->data->get_user_mail(), FILTER_VALIDATE_EMAIL ) ) ) {
			array_push( $this->errors, __( 'Indicare un indirizzo email valido.', 'design_laboratori_italia' ) );
		}
		return count( $this->errors ) === 0 ? false : true;
	}

	/**
	 * Get the validation error messages.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Subscribes a user to the newsletter.
	 *
	 * @return array Response with 'code', 'message' and 'body' keys.
	 * @throws Exception When the Brevo API request fails (caught internally; never propagates).
	 */
	public function subscribe_user() {
		$result = array(
			'code'    => 0,
			'message' => '',
			'body'    => '',
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
				$data             = array(
					'attributes'     => array(
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
				$json_data        = wp_json_encode( $data );
				$header           = array(
					'Accept'       => 'application/json',
					'api-key'      => $api_token,
					'Content-Type' => 'application/json',
				);

				// Chiamata al web service.
				$response = wp_remote_post(
					$url,
					array(
						'body'        => $json_data,
						'headers'     => $header,
						'timeout'     => 10,
						'redirection' => 2,
					)
				);

				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				$result['code']    = wp_remote_retrieve_response_code( $response );
				$result['message'] = wp_remote_retrieve_response_message( $response );
				$result['body']    = wp_remote_retrieve_body( $response );
			} else {
				$result['code']    = 400;
				$result['message'] = __( 'La Newslettere è disabilitata', 'design_laboratori_italia' );
			}
		} catch ( Exception $e ) {

			$result['code']    = 500;
			$result['message'] = $e->getMessage();

		}

		return $result;
	}
}
