<?php
/**
 * Base class of the integration tests.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Adds the Italian language to the test database and a few helpers.
 *
 * Polylang starts with no language in an empty database, so every test that
 * queries content by language needs `it` to exist. Everything runs inside the
 * transaction of the WordPress test framework and is rolled back at the end.
 */
abstract class DLI_Integration_Test_Case extends WP_UnitTestCase {

	/**
	 * Create the Italian language, the default one of the theme.
	 */
	public function set_up() {
		parent::set_up();

		$language = PLL()->model->languages->add(
			array(
				'name'   => 'Italiano',
				'slug'   => 'it',
				'locale' => 'it_IT',
				'rtl'    => false,
				'flag'   => 'it',
			)
		);
		if ( is_wp_error( $language ) ) {
			$this->fail( 'Cannot create the Italian language: ' . $language->get_error_message() );
		}
	}

	/**
	 * Option groups written with set_theme_options() during the test.
	 *
	 * @var string[]
	 */
	private $theme_option_groups = array();

	/**
	 * Empty the theme option groups used by the test and forget the languages
	 * cached in memory: the database is rolled back, the in-memory caches are not.
	 */
	public function tear_down() {
		// Inside the transaction, so that the reset is rolled back too.
		foreach ( $this->theme_option_groups as $group ) {
			cmb2_options( $group )->set( array() );
		}
		parent::tear_down();
		PLL()->model->languages->clean_cache();
	}

	/**
	 * Replace a theme option group (for example `setup`).
	 *
	 * CMB2 keeps the options it has read in memory for the whole process, so
	 * `update_option()` alone is not seen by `dli_get_option()` in later tests.
	 *
	 * @param string $group  Option group key.
	 * @param array  $values Option values.
	 */
	protected function set_theme_options( $group, array $values ) {
		$this->theme_option_groups[] = $group;
		cmb2_options( $group )->set( $values );
	}

	/**
	 * Create a post of any type in the Italian language.
	 *
	 * @param string $post_type Post type.
	 * @param string $title     Title.
	 * @param string $status    Post status.
	 * @return int Post ID.
	 */
	protected function create_post( $post_type, $title, $status = 'publish' ) {
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => $post_type,
				'post_title'  => $title,
				'post_status' => $status,
			)
		);
		dli_set_post_language( $post_id, 'it' );
		return $post_id;
	}
}
