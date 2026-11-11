<?php
/**
 * The advice on the format of the image written under the upload fields of the configuration.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Hero of the home and logos in `inc/admin/options.php`.
 */
class DLI_Image_Field_Descriptions_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The boxes registered by the test, removed afterwards: the registry of CMB2 is shared by all the tests.
	 *
	 * @var array<int, string>
	 */
	private $registered_boxes = array();

	/**
	 * Register the boxes of the configuration and remember the ones that are new.
	 *
	 * The boxes of the options pages are registered on `cmb2_admin_init`, which does not run outside the admin.
	 */
	public function set_up() {
		parent::set_up();
		$before = array_keys( CMB2_Boxes::get_all() );
		dli_register_main_options_metabox();
		$this->registered_boxes = array_values( array_diff( array_keys( CMB2_Boxes::get_all() ), $before ) );
	}

	/**
	 * Remove the boxes that the test registered.
	 */
	public function tear_down() {
		foreach ( $this->registered_boxes as $box_id ) {
			CMB2_Boxes::remove( $box_id );
		}
		parent::tear_down();
	}

	/**
	 * The description under a field of the configuration.
	 *
	 * @param string $box_id   Id of the box.
	 * @param string $field_id Id of the field.
	 * @return string The description.
	 */
	private function description_of( $box_id, $field_id ) {
		$cmb = CMB2_Boxes::get( $box_id );
		$this->assertInstanceOf( CMB2::class, $cmb, "The box $box_id is registered." );
		$field = $cmb->get_field( $field_id );
		$this->assertNotFalse( $field, "The field $field_id is in the box $box_id." );
		return (string) $field->args( 'desc' );
	}

	/**
	 * The hero of the home gives the recommended size, the safe zone and the warning, in place of «png o jpg».
	 */
	public function test_hero_field_gives_the_recommended_format() {
		$description = $this->description_of( 'dli_options_home', 'home_main_hero_image' );

		$this->assertStringContainsString( '1920 × 600', $description );
		$this->assertStringContainsString( 'Zona sicura', $description );
		$this->assertStringContainsString( 'indicative', $description );
		$this->assertStringNotContainsString( 'png o jpg', $description );
	}

	/**
	 * Each logo keeps its own sentence on what the logo is for and gives the format of the logos.
	 *
	 * @dataProvider logo_fields
	 *
	 * @param string $field_id Id of the field.
	 * @param string $purpose  Part of the sentence that says what the logo is for.
	 */
	public function test_logo_fields_keep_their_purpose_and_give_the_format( $field_id, $purpose ) {
		$description = $this->description_of( 'dli_options_header', $field_id );

		$this->assertStringContainsString( $purpose, $description );
		$this->assertStringContainsString( 'Formato consigliato: SVG', $description );
		$this->assertStringContainsString( 'alto almeno 160 px', $description );
	}

	/**
	 * The three logo fields and what each logo is for.
	 *
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function logo_fields() {
		return array(
			'header' => array( 'logo_laboratorio', 'Il logo del laboratorio.' ),
			'footer' => array( 'logo_laboratorio_footer', 'Il logo mostrato nel footer.' ),
			'mobile' => array( 'logo_laboratorio_mobile', 'menu hamburger (mobile)' ),
		);
	}

	/**
	 * The text added to a description is plain text: no markup comes from the list of formats.
	 */
	public function test_the_added_text_has_no_markup() {
		foreach ( array(
			array( 'dli_options_home', 'home_main_hero_image' ),
			array( 'dli_options_header', 'logo_laboratorio' ),
			array( 'dli_options_header', 'logo_laboratorio_footer' ),
			array( 'dli_options_header', 'logo_laboratorio_mobile' ),
		) as $field ) {
			$description = $this->description_of( $field[0], $field[1] );
			$this->assertSame( $description, wp_strip_all_tags( $description ), "{$field[1]}: the description has markup." );
		}
	}
}
