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
if ( ! class_exists( 'Project_Manager' ) ) {
	include_once 'class-projectmanager.php';
}
if ( ! class_exists( 'Publication_Manager' ) ) {
	include_once 'class-publicationmanager.php';
}
if ( ! class_exists( 'ResearchActivities_Manager' ) ) {
	include_once 'class-researchactivitiesmanager.php';
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

		// Setup dei post type personalizzati e delle tassonomie associate.

		// Setup del post type Struttura.
		$ctm = new Structure_Manager();
		$ctm->setup();

		// Setup del post type Persona.
		$cpm = new People_Manager();
		$cpm->setup();

		// Setup del post type Progetto.
		$ctprm = new Project_Manager();
		$ctprm->setup();

		// Setup del post type Pubblicazione.
		$publm = new Publication_Manager();
		$publm->setup();

		// Setup del post type Indirizzo di ricerca.
		$ctram = new ResearchActivities_Manager();
		$ctram->setup();
	}

}
