<?php

/**
 * The manager that setups Course post types.
 */
class Structure_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {
		$var3 = 3;
	}

	/**
	 * Install and configure the Course post type.
	 *
	 * @return void
	 */
	public function setup() {
		$var4 = 3;
		// Register the taxonomies of this plugin.
		// add_action( 'init', array( $this, 'add_emt_taxonomies' ) );

		// Register the post type of this plugin.
		// add_action( 'init', array( $this, 'add_course_post_type' ) );

		// Register the parent file of taxonomies.
		// NOTE: this is needed to fix the highligth of the menu when a plugin taxonomy is selected.
		// add_action( 'parent_file', array( $this, 'emt_parent_file' ) );

		// Register the template of the Course post type.
		// add_filter( 'single_template', array( $this, 'course_single_template' ) );

		// Register the template for the archive of the Course post type.
		// add_filter( 'archive_template', array( $this, 'course_archive_template' ) );
	}

}
