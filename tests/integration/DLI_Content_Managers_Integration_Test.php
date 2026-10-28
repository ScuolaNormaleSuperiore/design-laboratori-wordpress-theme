<?php
/**
 * The page and publication managers register their content types and fields.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Registration contract of DLI_Page_Manager and DLI_Publication_Manager.
 */
class DLI_Content_Managers_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Names of the fields of an ACF field group, in their order.
	 *
	 * @param string $group_key The key of the field group.
	 * @return array<int, string>
	 */
	private function get_field_names( $group_key ) {
		$fields = acf_get_fields( $group_key );
		$this->assertIsArray( $fields, "The field group $group_key has no fields." );

		return array_values( wp_list_pluck( $fields, 'name' ) );
	}

	/**
	 * A field of an ACF field group, by name.
	 *
	 * @param string $group_key The key of the field group.
	 * @param string $name      The name of the field.
	 * @return array The field.
	 */
	private function get_field_by_name( $group_key, $name ) {
		foreach ( acf_get_fields( $group_key ) as $field ) {
			if ( $name === $field['name'] ) {
				return $field;
			}
		}
		$this->fail( "The field group $group_key has no field named $name." );
	}

	/**
	 * The managers are loaded with the theme prefix.
	 */
	public function test_manager_classes_have_the_theme_prefix() {
		$this->assertTrue( class_exists( 'DLI_Page_Manager' ) );
		$this->assertTrue( class_exists( 'DLI_Publication_Manager' ) );
	}

	/**
	 * The Open Graph value object is loaded and returned by the contents manager.
	 */
	public function test_og_wrapper_is_loaded_with_the_contents_manager() {
		$this->assertTrue( class_exists( 'DLI_ContentsManager' ) );
		$this->assertTrue( class_exists( 'DLI_OG_Wrapper' ) );
		$this->assertInstanceOf( 'DLI_OG_Wrapper', DLI_ContentsManager::get_og_data() );
	}

	/**
	 * The contents manager and the Open Graph value object live in files named after their class.
	 */
	public function test_contents_manager_classes_live_in_files_named_after_them() {
		$this->assertSame( 'class-dli-contentsmanager.php', basename( ( new ReflectionClass( 'DLI_ContentsManager' ) )->getFileName() ) );
		$this->assertSame( 'class-dli-og-wrapper.php', basename( ( new ReflectionClass( 'DLI_OG_Wrapper' ) )->getFileName() ) );
	}

	/**
	 * Only the post types with a post in the requested status are returned, in the order given.
	 */
	public function test_post_types_with_results_keeps_only_types_with_posts_in_the_status() {
		$this->create_post( NEWS_POST_TYPE, 'Notizia pubblicata' );
		$this->create_post( EVENT_POST_TYPE, 'Evento in bozza', 'draft' );

		$types = array( EVENT_POST_TYPE, NEWS_POST_TYPE, PUBLICATION_POST_TYPE, NEWS_POST_TYPE );

		$this->assertSame( array( NEWS_POST_TYPE ), DLI_ContentsManager::get_post_types_with_results( $types ) );
		$this->assertSame( array( EVENT_POST_TYPE ), DLI_ContentsManager::get_post_types_with_results( $types, 'draft' ) );
	}

	/**
	 * Anything but a non-empty array of post types gives an empty result.
	 */
	public function test_post_types_with_results_rejects_invalid_input() {
		$this->create_post( NEWS_POST_TYPE, 'Notizia pubblicata' );

		$this->assertSame( array(), DLI_ContentsManager::get_post_types_with_results( array() ) );
		$this->assertSame( array(), DLI_ContentsManager::get_post_types_with_results( NEWS_POST_TYPE ) );
		$this->assertSame( array(), DLI_ContentsManager::get_post_types_with_results( null ) );
	}

	/**
	 * The page field group is registered for the page post type.
	 */
	public function test_page_field_group_is_registered() {
		$group = acf_get_field_group( 'group_670e6f9be1c67' );

		$this->assertIsArray( $group );
		$this->assertSame( 'page', $group['location'][0][0]['value'] );
		$this->assertSame( array( 'pagine_collegate' ), $this->get_field_names( 'group_670e6f9be1c67' ) );
	}

	/**
	 * The publication post type and its type taxonomy are registered.
	 */
	public function test_publication_post_type_and_taxonomy_are_registered() {
		$this->assertTrue( post_type_exists( PUBLICATION_POST_TYPE ) );
		$this->assertTrue( taxonomy_exists( PUBLICATION_TYPE_TAXONOMY ) );
		$this->assertContains( PUBLICATION_TYPE_TAXONOMY, get_object_taxonomies( PUBLICATION_POST_TYPE ) );
	}

	/**
	 * The publication field group keeps its keys and fields.
	 */
	public function test_publication_field_group_is_registered() {
		$group = acf_get_field_group( 'group_63ca5c47b914d' );

		$this->assertIsArray( $group );
		$this->assertSame( PUBLICATION_POST_TYPE, $group['location'][0][0]['value'] );
		$this->assertSame(
			array( 'anno', 'autori', 'url', 'allegato', 'link_dettaglio', 'promuovi_in_carousel', 'promuovi_in_home' ),
			$this->get_field_names( 'group_63ca5c47b914d' )
		);
	}

	/**
	 * A publication chooses where its title leads in the lists, like events and news.
	 */
	public function test_publication_link_choice_field() {
		$field = $this->get_field_by_name( 'group_63ca5c47b914d', 'link_dettaglio' );

		$this->assertSame( 'select', $field['type'] );
		$this->assertSame( DLI_ITEM_LINK['DETAIL_PAGE'], $field['default_value'] );
		$this->assertSame(
			array( DLI_ITEM_LINK['DETAIL_PAGE'], DLI_ITEM_LINK['WEBSITE'], DLI_ITEM_LINK['ATTACHMENT'] ),
			array_keys( $field['choices'] )
		);
	}

	/**
	 * The attachment of a publication is a PDF file, returned as an array.
	 */
	public function test_publication_attachment_field_accepts_only_pdf() {
		$field = $this->get_field_by_name( 'group_63ca5c47b914d', 'allegato' );

		$this->assertSame( 'file', $field['type'] );
		$this->assertSame( 'array', $field['return_format'] );
		$this->assertSame( 'pdf', $field['mime_types'] );
	}
}
