<?php
/**
 * The advice on the format of the image written under the ACF image fields of the contents.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The logo of a spin-off and the photo of a technical resource.
 */
class DLI_Image_Field_Instructions_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The texts of the theme are written in Italian, and the test site is in English: run every test with the
	 * Italian texts, so that the checks do not depend on the English catalogue.
	 */
	public function set_up() {
		parent::set_up();
		$this->use_italian_texts();
	}

	/**
	 * Load the translations of the theme again.
	 */
	public function tear_down() {
		$this->restore_theme_translations();
		parent::tear_down();
	}

	/**
	 * Key of the field group of the spin-offs.
	 */
	const SPINOFF_GROUP = 'group_679359beb87e6';

	/**
	 * Key of the field group of the technical resources.
	 */
	const TECHNICAL_RESOURCE_GROUP = 'group_682edcc15188d';

	/**
	 * A field of an ACF field group, by name.
	 *
	 * @param string $group_key The key of the field group.
	 * @param string $name      The name of the field.
	 * @return array The field.
	 */
	private function get_field_by_name( $group_key, $name ) {
		$fields = acf_get_fields( $group_key );
		$this->assertIsArray( $fields, "The field group $group_key has no fields." );
		foreach ( $fields as $field ) {
			if ( $name === $field['name'] ) {
				return $field;
			}
		}
		$this->fail( "The field group $group_key has no field named $name." );
	}

	/**
	 * The logo of a spin-off says that it is seen only in the list, where it is cropped, and where to keep the margin.
	 */
	public function test_spinoff_logo_gives_the_format_of_the_list() {
		$field = $this->get_field_by_name( self::SPINOFF_GROUP, 'logo' );

		$this->assertStringContainsString( '800 × 500', $field['instructions'] );
		$this->assertStringContainsString( 'elenco', $field['instructions'] );
		$this->assertStringContainsString( 'tagliato', $field['instructions'] );
		$this->assertStringContainsString( 'margine', $field['instructions'] );
	}

	/**
	 * The photo of a technical resource says that it is not cropped, and the width that keeps it sharp.
	 */
	public function test_technical_resource_photo_gives_the_format_of_the_page() {
		$field = $this->get_field_by_name( self::TECHNICAL_RESOURCE_GROUP, 'foto' );

		$this->assertStringContainsString( 'non viene tagliata', $field['instructions'] );
		$this->assertStringContainsString( '560 px', $field['instructions'] );
		$this->assertStringContainsString( 'immagine in evidenza', $field['instructions'] );
	}

	/**
	 * The instructions are advice and not a rule: the fields do not refuse a file by size, and they stay image fields.
	 */
	public function test_the_fields_give_advice_and_do_not_refuse_files() {
		foreach ( array(
			array( self::SPINOFF_GROUP, 'logo' ),
			array( self::TECHNICAL_RESOURCE_GROUP, 'foto' ),
		) as $spec ) {
			$field = $this->get_field_by_name( $spec[0], $spec[1] );

			$this->assertSame( 'image', $field['type'], "{$spec[1]}: still an image field." );
			foreach ( array( 'min_width', 'min_height', 'min_size', 'max_width', 'max_height', 'max_size' ) as $limit ) {
				$this->assertEmpty( $field[ $limit ], "{$spec[1]}: $limit must stay empty, the text only advises." );
			}
			$this->assertSame( $field['instructions'], wp_strip_all_tags( $field['instructions'] ), "{$spec[1]}: the instructions have markup." );
		}
	}
}
