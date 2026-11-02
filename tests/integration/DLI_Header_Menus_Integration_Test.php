<?php
/**
 * The two header menus, the main one and the secondary one: identifiers of the dropdowns, `data-element` and the
 * items that have no address.
 *
 * @package Design_Laboratori_Italia
 */

require_once get_template_directory() . '/inc/walkers/class-dli-main-menu-walker.php';

/**
 * `template-parts/header/main-menu.php` (with `DLI_Main_Menu_Walker`) and `template-parts/header/secondary-menu.php`.
 */
class DLI_Header_Menus_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Forget the menus assigned to the locations, whatever the test did.
	 */
	public function tear_down() {
		remove_theme_mod( 'nav_menu_locations' );
		parent::tear_down();
	}

	/**
	 * Create a menu and give it to a location of the theme.
	 *
	 * @param string $location `menu-lab` (main menu) or `menu-right` (secondary menu).
	 * @return int The menu ID.
	 */
	private function create_menu( $location ) {
		$menu_id = wp_create_nav_menu( 'Menu di prova ' . $location );
		$this->assertIsInt( $menu_id );
		$locations              = get_theme_mod( 'nav_menu_locations', array() );
		$locations              = is_array( $locations ) ? $locations : array();
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return $menu_id;
	}

	/**
	 * Add an item to a menu.
	 *
	 * @param int    $menu_id Menu.
	 * @param string $title   Title.
	 * @param string $url     Address of a custom link; empty to point to a page.
	 * @param int    $parent  Parent item, 0 for the first level.
	 * @param int    $page_id The page the item points to, when it is not a custom link.
	 * @return int The item ID.
	 */
	private function add_item( $menu_id, $title, $url = '', $parent = 0, $page_id = 0 ) {
		$data = array(
			'menu-item-title'     => $title,
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		);
		if ( $page_id ) {
			$data['menu-item-type']      = 'post_type';
			$data['menu-item-object']    = 'page';
			$data['menu-item-object-id'] = $page_id;
		} else {
			$data['menu-item-type'] = 'custom';
			$data['menu-item-url']  = $url;
		}

		$item_id = wp_update_nav_menu_item( $menu_id, 0, $data );
		$this->assertIsInt( $item_id );
		return $item_id;
	}

	/**
	 * Create a page with a slug.
	 *
	 * @param string $slug Slug.
	 * @return int Page ID.
	 */
	private function create_page( $slug ) {
		return self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => ucfirst( $slug ),
				'post_name'   => $slug,
				'post_status' => 'publish',
			)
		);
	}

	/**
	 * Print a template part and return its HTML.
	 *
	 * @param string $slug Template part.
	 * @return string
	 */
	private function render( $slug ) {
		ob_start();
		get_template_part( $slug, null, array( 'locations' => get_nav_menu_locations() ) );
		return (string) ob_get_clean();
	}

	/**
	 * A main menu with a dropdown opened by a custom link, a dropdown opened by a page and a plain item.
	 *
	 * @return array<string, int> The IDs of the items, by name.
	 */
	private function create_main_menu_with_two_dropdowns() {
		$menu    = $this->create_menu( 'menu-lab' );
		$parent1 = $this->add_item( $menu, 'Il laboratorio', '#' );
		$parent2 = $this->add_item( $menu, 'Ricerca', 'https://example.org/ricerca' );
		$ids     = array(
			'parent1' => $parent1,
			'parent2' => $parent2,
			'child1'  => $this->add_item( $menu, 'Chi siamo', 'https://example.org/chi-siamo', $parent1 ),
			'child2'  => $this->add_item( $menu, 'Dove siamo', 'https://example.org/dove-siamo', $parent1 ),
			'child3'  => $this->add_item( $menu, 'Aree', 'https://example.org/aree', $parent2 ),
			'leaf'    => $this->add_item( $menu, 'Contatti', 'https://example.org/contatti' ),
		);
		return $ids;
	}

	/**
	 * Every dropdown has an identifier of its own, built on its menu item, and its menu is named by its own button.
	 */
	public function test_each_dropdown_has_its_own_identifier_and_is_named_by_its_own_button() {
		$ids = $this->create_main_menu_with_two_dropdowns();

		$html = $this->render( 'template-parts/header/main-menu' );

		foreach ( array( 'parent1', 'parent2' ) as $name ) {
			$id = 'main-nav-dropdown-' . $ids[ $name ];
			$this->assertSame( 1, substr_count( $html, 'id="' . $id . '"' ), "The toggle of $name has its identifier." );
			$this->assertSame( 1, substr_count( $html, 'aria-labelledby="' . $id . '"' ), "The dropdown of $name is named by its toggle." );
		}
		$this->assertStringNotContainsString( 'mainNavDropdown1', $html, 'No identifier shared by every dropdown.' );

		preg_match_all( '#\sid="([^"]+)"#', $html, $found );
		$this->assertSame( array_unique( $found[1] ), $found[1], 'No identifier is repeated.' );
	}

	/**
	 * The secondary menu has its own identifiers, so that no identifier is shared with the main menu either.
	 */
	public function test_the_secondary_menu_has_its_own_identifiers() {
		$main_ids  = $this->create_main_menu_with_two_dropdowns();
		$menu      = $this->create_menu( 'menu-right' );
		$parent    = $this->add_item( $menu, 'Area riservata', '#' );
		$secondary = array(
			'parent' => $parent,
			'child'  => $this->add_item( $menu, 'Accedi', 'https://example.org/accedi', $parent ),
		);

		$main      = $this->render( 'template-parts/header/main-menu' );
		$right     = $this->render( 'template-parts/header/secondary-menu' );
		$secondary_id = 'secondary-nav-dropdown-' . $secondary['parent'];

		$this->assertSame( 1, substr_count( $right, 'id="' . $secondary_id . '"' ) );
		$this->assertSame( 1, substr_count( $right, 'aria-labelledby="' . $secondary_id . '"' ) );
		$this->assertStringNotContainsString( 'mainNavDropdown1', $right );

		preg_match_all( '#\sid="([^"]+)"#', $main . $right, $found );
		$this->assertSame( array_unique( $found[1] ), $found[1], 'No identifier is shared by the two menus.' );
		$this->assertStringContainsString( 'main-nav-dropdown-' . $main_ids['parent1'], $main );
	}

	/**
	 * The attribute follows the page of the item, in a first level item and in a dropdown, in Italian and in English.
	 */
	public function test_data_element_is_set_from_the_page_of_the_item() {
		$menu = $this->create_menu( 'menu-lab' );
		$this->add_item( $menu, 'People', '', 0, $this->create_page( 'people' ) );
		$parent = $this->add_item( $menu, 'Il laboratorio', '#' );
		$this->add_item( $menu, 'Tutti i progetti', '', $parent, $this->create_page( 'progetti' ) );
		$this->add_item( $menu, 'Contatti', '', $parent, $this->create_page( 'contatti' ) );

		$html = $this->render( 'template-parts/header/main-menu' );

		$this->assertMatchesRegularExpression( '#<a [^>]*data-element="people"[^>]*><span>People</span>#', $html, 'An item that points to the English people page.' );
		$this->assertMatchesRegularExpression( '#<a [^>]*data-element="projects"[^>]*>\s*<span>Tutti i progetti</span>#', $html, 'The items of a dropdown have it too.' );
		$this->assertSame( 1, preg_match_all( '#data-element="projects"#', $html ) );
		$this->assertDoesNotMatchRegularExpression( '#data-element="[^"]*"[^>]*>\s*<span>Contatti</span>#', $html, 'A page that is not one of them has none.' );
	}

	/**
	 * A parent without an address is a button that opens the dropdown: the dropdown has no link to itself and no divider.
	 */
	public function test_a_parent_without_an_address_has_no_link_to_itself_in_its_dropdown() {
		$ids = $this->create_main_menu_with_two_dropdowns();

		$html = $this->render( 'template-parts/header/main-menu' );

		// Il laboratorio ('#'): only its two children. Ricerca (real address): its own link and a divider first.
		$this->assertDoesNotMatchRegularExpression( '#<li><a class="dropdown-item list-item" href="\#?"><span>Il laboratorio</span>#', $html );
		$this->assertStringContainsString( 'Chi siamo', $html );
		$this->assertStringContainsString( 'Dove siamo', $html );
		$this->assertSame( 1, substr_count( $html, 'class="divider"' ), 'The divider is only in the dropdown that has a link to itself.' );
		$this->assertMatchesRegularExpression( '#<li><a class="dropdown-item list-item" href="https://example.org/ricerca"><span>Ricerca</span></a></li>\s*<li><span class="divider"></span></li>#', $html );
		$this->assertSame( 2, substr_count( $html, 'dropdown-toggle' ) );
		$this->assertGreaterThan( 0, $ids['parent1'] );
	}

	/**
	 * Items that have no address and nothing under them are not printed, and leave no stray tag behind: the list is well formed.
	 */
	public function test_items_without_an_address_are_left_out_and_the_markup_is_balanced() {
		$menu    = $this->create_menu( 'menu-lab' );
		$parent  = $this->add_item( $menu, 'Genitore', '#' );
		$this->add_item( $menu, 'Figlio senza indirizzo', '#', $parent );
		$this->add_item( $menu, 'Figlio valido', 'https://example.org/figlio', $parent );
		$this->add_item( $menu, 'Voce vuota', '#' );
		$this->add_item( $menu, 'Voce normale', 'https://example.org/normale' );

		$html = $this->render( 'template-parts/header/main-menu' );

		$this->assertStringNotContainsString( 'Voce vuota', $html, 'A first level item without address and children is not printed.' );
		$this->assertStringNotContainsString( 'Figlio senza indirizzo', $html, 'A child without address is not listed.' );
		$this->assertStringContainsString( 'Figlio valido', $html );
		$this->assertStringContainsString( 'Voce normale', $html );
		$this->assertSame( substr_count( $html, '<li' ), substr_count( $html, '</li>' ), 'Every item that is opened is closed, and no more.' );
		$this->assertSame( substr_count( $html, '<ul' ), substr_count( $html, '</ul>' ) );
		$this->assertSame( substr_count( $html, '<a ' ), substr_count( $html, '</a>' ) );
	}

	/**
	 * The secondary menu follows the same rules and is balanced too.
	 */
	public function test_the_secondary_menu_is_balanced_and_leaves_out_what_has_no_address() {
		$menu   = $this->create_menu( 'menu-right' );
		$parent = $this->add_item( $menu, 'Genitore', '#' );
		$this->add_item( $menu, 'Figlio valido', 'https://example.org/figlio', $parent );
		$this->add_item( $menu, 'Voce vuota', '#' );
		$this->add_item( $menu, 'Voce normale', 'https://example.org/normale' );

		$html = $this->render( 'template-parts/header/secondary-menu' );

		$this->assertStringNotContainsString( 'Voce vuota', $html );
		$this->assertStringContainsString( 'Figlio valido', $html );
		$this->assertStringContainsString( 'Voce normale', $html );
		$this->assertSame( substr_count( $html, '<li' ), substr_count( $html, '</li>' ) );
		$this->assertSame( substr_count( $html, '<a ' ), substr_count( $html, '</a>' ) );
		$this->assertSame( 1, substr_count( $html, 'dropdown-toggle' ) );
	}

	/**
	 * The dropdown of a menu item is the same markup in the two menus: the main menu does not copy it from the other.
	 */
	public function test_the_two_menus_print_a_dropdown_in_the_same_way() {
		$main_menu = $this->create_menu( 'menu-lab' );
		$main      = $this->add_item( $main_menu, 'Genitore', '#' );
		$this->add_item( $main_menu, 'Figlio', 'https://example.org/figlio', $main );
		$right_menu = $this->create_menu( 'menu-right' );
		$right      = $this->add_item( $right_menu, 'Genitore', '#' );
		$this->add_item( $right_menu, 'Figlio', 'https://example.org/figlio', $right );

		$normalize = static function ( $html ) {
			return preg_replace( '#(main-nav|secondary-nav)-dropdown-\d+#', 'ID', preg_replace( '#\s+#', ' ', $html ) );
		};
		preg_match( '#<li class="nav-item dropdown">.*?</li>\s*(?=</ul>)#s', $this->render( 'template-parts/header/main-menu' ), $first );
		preg_match( '#<li class="nav-item dropdown">.*?</li>\s*(?=</ul>)#s', $this->render( 'template-parts/header/secondary-menu' ), $second );

		$this->assertNotEmpty( $first );
		$this->assertSame( $normalize( $first[0] ), $normalize( $second[0] ) );
	}
}
