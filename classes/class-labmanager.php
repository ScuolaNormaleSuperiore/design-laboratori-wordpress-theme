<?php
/**
 * Definition of the LabManager used to create the custom content types.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'PeopleManager' ) ) {
	include_once 'class-peoplemanager.php';
}

if ( ! class_exists( 'PeopleType_Manager' ) ) {
	include_once 'class-peopletypemanager.php';
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

if ( ! class_exists( 'News_Manager' ) ) {
	include_once 'class-newsmanager.php';
}

if ( ! class_exists( 'Event_Manager' ) ) {
	include_once 'class-eventmanager.php';
}

if ( ! class_exists( 'Post_Manager' ) ) {
	include_once 'class-postmanager.php';
}

if ( ! class_exists( 'Polylang_Manager' ) ) {
	include_once 'class-polylangmanager.php';
}

if ( ! class_exists( 'Place_Manager' ) ) {
	include_once 'class-placemanager.php';
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

		// Setup del post type Persona.
		$cpm = new People_Manager();
		$cpm->setup();

		// Setup del post type Tipologia Persona.
		$cptm = new PeopleType_Manager();
		$cptm->setup();

		// Setup del post type Progetto.
		$ctprm = new Project_Manager();
		$ctprm->setup();

		// Setup del post type Pubblicazione.
		$publm = new Publication_Manager();
		$publm->setup();

		// Setup del post type Indirizzo di ricerca.
		$ctram = new ResearchActivities_Manager();
		$ctram->setup();

		// Setup del post type Progetti.
		$ctprog = new Project_Manager();
		$ctprog->setup();

		// Setup del post type News.
		$ctprog = new News_Manager();
		$ctprog->setup();

		// Setup del post type Eventi.
		$ctprog = new Event_Manager();
		$ctprog->setup();

		// Setup del post.
		$ctprog = new Post_Manager();
		$ctprog->setup();

		// Setup di Polylang.
		$polylang = new Polylang_Manager();
		$polylang->setup();

		// Setup del post type Luoghi.
		$ctluoghi = new Place_Manager();
		$ctluoghi->setup();

	}
}
