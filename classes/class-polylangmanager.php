<?php
/**
 * Creazione dei post types Persona e Struttura.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that setups People post types.
 */
class Polylang_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Install and configure the Course post type.
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'pll_get_post_types', array( $this, 'add_cpt_to_pll' ), 10, 2 );

		add_filter( 'pll_get_taxonomies', array( $this, 'add_tax_to_pll' ), 10, 2 );
	}

	public function add_cpt_to_pll() {
		// if ( $is_settings ) {
		// 	// hides 'my_cpt' from the list of custom post types in Polylang settings
		// 	unset( $post_types['my_cpt'] );
		// } else {
		// 	// enables language and translation management for 'my_cpt'
		// 	$post_types['my_cpt'] = 'my_cpt';
		// }
		return DLI_POST_TYPES_TO_TRANSLATE;
	}

	public function add_tax_to_pll() {
		// if ( $is_settings ) {
		// 	unset( $taxonomies['my_tax'] );
		// } else {
		// 	$taxonomies['my_tax'] = 'my_tax';
		// }
		return DLI_TAXONOMIES_TO_TRANSLATE;
	}

}
