<?php
/**
 * Data submitted through the newsletter subscription form.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Value object holding the data submitted through the newsletter subscription form.
 */
class DLI_Newsletter_Data {

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
