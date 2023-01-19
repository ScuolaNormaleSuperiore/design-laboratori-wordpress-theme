<?php
/**
 * Definition of the LabManager used to create the custom content types.
 *
 * @package design-laboratori-wordpress-theme
 */

if ( ! class_exists( 'StructureManager' ) ) {
	include_once 'class-structuremanager.php';
}
if ( ! class_exists( 'PeopleManager' ) ) {
	include_once 'class-peoplemanager.php';
}

if(! class_exists('Project_Manager')) {
    include_once 'class-projectmanager.php';
}

/**
 * The manager that builds the tool and configures Wordpress.
 */
class LabManager {

	/**
	 * The static instance of the LabManager.
	 *
	 * @var object
	 */
	protected static $instance = null;


	/**
	 * Create the instance of the manager.
	 *
	 * @return void
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Used to install and configure the plugin.
	 *
	 * @return void
	 */
	public function plugin_setup() {

		// Setup the custom post types and their taxonomies.

		// Setup of the Structure post type.
		$ctm = new Structure_Manager();
		$ctm->setup();

		// Setup of the People post type.
		$ctm = new People_Manager();
		$ctm->setup();
        //Setup del post type Progetti
        $ctProgetti = new Project_Manager();
        $ctProgetti->setup();
	}

}
