<?php
/**
 * Unit tests for the CAPTCHA helper.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * CAPTCHA helper behavior without the optional plugin.
 */
class DLI_Captcha_Unit_Test extends TestCase {

	/**
	 * The helper returns safe defaults when Really Simple CAPTCHA is unavailable.
	 */
	public function test_captcha_data_is_disabled_without_the_optional_plugin() {
		$dli_captcha = dli_get_captcha_data();

		$this->assertFalse( $dli_captcha['enabled'] );
		$this->assertNull( $dli_captcha['instance'] );
		$this->assertSame( '', $dli_captcha['image_src'] );
		$this->assertSame( 0, $dli_captcha['image_width'] );
		$this->assertSame( 0, $dli_captcha['image_height'] );
		$this->assertSame( '', $dli_captcha['prefix'] );
	}

	/**
	 * Validation fails safely when the CAPTCHA service is unavailable.
	 */
	public function test_captcha_validation_fails_safely_without_the_optional_plugin() {
		$this->assertFalse( dli_validate_captcha( dli_get_captcha_data(), 'prefix', 'response' ) );
	}
}
