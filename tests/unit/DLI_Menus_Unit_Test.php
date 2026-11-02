<?php
/**
 * Unit tests of the pure helpers of the header menus, in inc/menus.php.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * The attribute `data-element` that the crawlers of Designers Italia read on the main menu, and the identifiers of
 * the dropdowns.
 */
class DLI_Menus_Unit_Test extends TestCase {

	/**
	 * The attribute follows the page the item points to, in Italian and in English, not the words of the title.
	 */
	public function test_data_element_follows_the_slug_of_the_page_in_both_languages() {
		$this->assertSame( 'people', dli_get_menu_data_element( 'persone', 'Qualunque titolo' ) );
		$this->assertSame( 'people', dli_get_menu_data_element( 'people', 'Any title' ) );
		$this->assertSame( 'projects', dli_get_menu_data_element( 'progetti', '' ) );
		$this->assertSame( 'projects', dli_get_menu_data_element( 'projects', '' ) );
		$this->assertSame( 'research', dli_get_menu_data_element( 'ricerca', '' ) );
		$this->assertSame( 'research', dli_get_menu_data_element( 'research', '' ) );
		$this->assertSame( 'publications', dli_get_menu_data_element( 'pubblicazioni', '' ) );
		$this->assertSame( 'publications', dli_get_menu_data_element( 'publications', '' ) );
	}

	/**
	 * Without a known page the title is still read, as it always was, so that a site that built its menu on the titles
	 * keeps the attribute; anything else has none.
	 */
	public function test_data_element_falls_back_to_the_title_and_is_empty_for_the_rest() {
		$this->assertSame( 'people', dli_get_menu_data_element( '', 'Persone' ) );
		$this->assertSame( 'projects', dli_get_menu_data_element( 'una-pagina', 'Progetti' ) );
		$this->assertSame( 'research', dli_get_menu_data_element( '', 'Attività di ricerca' ) );
		$this->assertSame( 'publications', dli_get_menu_data_element( '', 'Pubblicazioni' ) );
		$this->assertSame( '', dli_get_menu_data_element( 'contatti', 'Contatti' ) );
		$this->assertSame( '', dli_get_menu_data_element( '', '' ) );
		$this->assertSame( 'people', dli_get_menu_data_element( 'people', 'Contatti' ), 'The page wins over the title.' );
	}

	/**
	 * The identifier of a dropdown is made of the menu and the menu item, so that it is unique in the page.
	 */
	public function test_dropdown_id_is_made_of_the_menu_and_the_item() {
		$this->assertSame( 'main-nav-dropdown-12', dli_get_menu_dropdown_id( 'main-nav', 12 ) );
		$this->assertSame( 'secondary-nav-dropdown-12', dli_get_menu_dropdown_id( 'secondary-nav', 12 ) );
		$this->assertNotSame( dli_get_menu_dropdown_id( 'main-nav', 1 ), dli_get_menu_dropdown_id( 'main-nav', 2 ) );
		$this->assertSame( 'main-nav-dropdown-7', dli_get_menu_dropdown_id( 'main-nav', '7' ), 'The item may come as text.' );
		$this->assertSame( 'a-b-dropdown-3', dli_get_menu_dropdown_id( 'a b<>', 3 ), 'The prefix is made safe to be part of an identifier.' );
	}
}
