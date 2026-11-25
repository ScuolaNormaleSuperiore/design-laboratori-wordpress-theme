<?php
/**
 * Definition of the Polylang Manager: which post types and taxonomies Polylang translates.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that tells Polylang which post types and taxonomies it translates.
 */
class DLI_Polylang_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Register the Polylang filters.
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'pll_get_post_types', array( $this, 'add_cpt_to_pll' ), 10, 2 );

		add_filter( 'pll_get_taxonomies', array( $this, 'add_tax_to_pll' ), 10, 2 );
	}

	/**
	 * All the post types that must be managed by Polylang.
	 *
	 * @param string[] $post_types  Post types proposed by Polylang (ignored: the theme list replaces them).
	 * @param bool     $is_settings True when the list is shown in the settings of Polylang.
	 * @return string[]
	 */
	public function add_cpt_to_pll( $post_types, $is_settings ) {
		return DLI_POST_TYPES_TO_TRANSLATE;
	}

	/**
	 * All the taxonomies that must be managed by Polylang.
	 *
	 * @param string[] $taxonomies  Taxonomies proposed by Polylang (ignored: the theme list replaces them).
	 * @param bool     $is_settings True when the list is shown in the settings of Polylang.
	 * @return string[]
	 */
	public function add_tax_to_pll( $taxonomies, $is_settings ) {
		return DLI_TAXONOMIES_TO_TRANSLATE;
	}
}
