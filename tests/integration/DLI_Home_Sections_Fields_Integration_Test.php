<?php
/**
 * The fields of a row of Sezioni HP: what the numbers of a row count.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The descriptions of the fields «Numero massimo di elementi» and «Numero massimo di sottopagine per gruppo».
 *
 * The same two fields serve three sections and a field has one name in CMB2, so the description says what the number
 * counts for each section: cards, groups or columns, and links.
 */
class DLI_Home_Sections_Fields_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The boxes registered by the test, removed afterwards: the registry of CMB2 is shared by all the tests.
	 *
	 * @var array<int, string>
	 */
	private $registered_boxes = array();

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
	 * The description of a field of the rows of Sezioni HP.
	 *
	 * The boxes of the options pages are registered on `cmb2_admin_init`, which does not run outside the admin.
	 *
	 * @param string $field_id Id of the field inside the group `site_sections`.
	 * @return string The description.
	 */
	private function description_of( $field_id ) {
		if ( ! CMB2_Boxes::get( 'dli_home_sections' ) ) {
			dli_register_main_options_metabox();
			$this->registered_boxes[] = 'dli_home_sections';
		}
		$cmb = CMB2_Boxes::get( 'dli_home_sections' );
		$this->assertInstanceOf( CMB2::class, $cmb, 'The box of Sezioni HP is registered.' );

		$group = $cmb->prop( 'fields' )['site_sections'];
		foreach ( $group['fields'] as $field ) {
			if ( $field_id === $field['id'] ) {
				return (string) ( $field['desc'] ?? '' );
			}
		}

		$this->fail( "The field $field_id is not in the rows of Sezioni HP." );
	}

	/**
	 * The number of items is cards for «Card di navigazione», groups for «Navigazione per pagine» and columns for
	 * «Indice di una pagina»: the description names the three sections.
	 */
	public function test_max_items_description_says_what_it_counts_for_each_section() {
		$description = $this->description_of( 'max_items' );

		$this->assertStringContainsString( __( 'Card di navigazione', 'design_laboratori_italia' ), $description );
		$this->assertStringContainsString( __( 'Navigazione per pagine', 'design_laboratori_italia' ), $description );
		$this->assertStringContainsString( __( 'Indice di una pagina', 'design_laboratori_italia' ), $description );
	}

	/**
	 * The number of sub-items is the links of each group or of each column: the description names the two sections
	 * that have them and not the one that has none.
	 */
	public function test_max_subitems_description_says_what_it_counts_for_each_section() {
		$description = $this->description_of( 'max_subitems' );

		$this->assertStringContainsString( __( 'Navigazione per pagine', 'design_laboratori_italia' ), $description );
		$this->assertStringContainsString( __( 'Indice di una pagina', 'design_laboratori_italia' ), $description );
		$this->assertStringNotContainsString( __( 'Card di navigazione', 'design_laboratori_italia' ), $description );
	}

	/**
	 * The limits (from 1 to 12) and the default are still said: the new sentence is added, not put in their place.
	 */
	public function test_descriptions_keep_the_range() {
		foreach ( array( 'max_items', 'max_subitems' ) as $field_id ) {
			$description = $this->description_of( $field_id );

			$this->assertStringContainsString( (string) DLI_HP_ITEMS_MIN, $description, "$field_id says the least." );
			$this->assertStringContainsString( (string) DLI_HP_ITEMS_MAX, $description, "$field_id says the most." );
		}
	}
}
