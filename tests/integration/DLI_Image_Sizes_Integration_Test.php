<?php
/**
 * The image sizes registered by the theme and the photo of a person.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_LabManager::setup_image_sizes()` and `dli_get_persona_avatar()`.
 */
class DLI_Image_Sizes_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * A photo of the test data of WordPress, big enough to have every size of the theme.
	 *
	 * @return string Path of the file.
	 */
	private function sample_photo() {
		$file = DIR_TESTDATA . '/images/canola.jpg';
		$this->assertFileExists( $file, 'The sample photo of the WordPress test data is missing.' );
		return $file;
	}

	/**
	 * Create a person and a user, and give the person a featured image.
	 *
	 * @param bool $with_photo Whether the person gets a featured image.
	 * @return array{0: int, 1: int} The ID of the person and the ID of the user.
	 */
	private function create_person( $with_photo ) {
		$person_id = self::factory()->post->create( array( 'post_type' => PEOPLE_POST_TYPE ) );
		$user_id   = self::factory()->user->create();
		if ( $with_photo ) {
			$attachment_id = self::factory()->attachment->create_upload_object( $this->sample_photo(), $person_id );
			set_post_thumbnail( $person_id, $attachment_id );
		}
		return array( $person_id, $user_id );
	}

	/**
	 * The size of the photo of a person is registered: 250 px square, cropped around the centre.
	 */
	public function test_item_thumb_size_is_registered_square_and_cropped() {
		$sizes = wp_get_additional_image_sizes();

		$this->assertArrayHasKey( 'item-thumb', $sizes );
		$this->assertSame( 250, $sizes['item-thumb']['width'] );
		$this->assertSame( 250, $sizes['item-thumb']['height'] );
		$this->assertTrue( (bool) $sizes['item-thumb']['crop'], 'The photo is cropped to a square.' );
	}

	/**
	 * Control: the two sizes that the theme already had do not change.
	 */
	public function test_existing_sizes_do_not_change() {
		$sizes = wp_get_additional_image_sizes();

		$this->assertSame( 416, $sizes['item-card-list']['width'] );
		$this->assertSame( 232, $sizes['item-card-list']['height'] );
		$this->assertFalse( (bool) $sizes['item-card-list']['crop'] );
		$this->assertSame( 592, $sizes['item-carousel']['width'] );
		$this->assertSame( 334, $sizes['item-carousel']['height'] );
		$this->assertFalse( (bool) $sizes['item-carousel']['crop'] );
	}

	/**
	 * The photo of a person is the small square file and not the original.
	 */
	public function test_person_avatar_is_the_item_thumb_file() {
		list( $person_id, $user_id ) = $this->create_person( true );

		$url = dli_get_persona_avatar( $person_id, $user_id );

		$this->assertStringEndsWith( '-250x250.jpg', $url, 'The avatar must be the 250 × 250 file, not the original.' );
	}

	/**
	 * Control: a person without a photo still gets the avatar of the user.
	 */
	public function test_person_without_a_photo_gets_the_user_avatar() {
		list( $person_id, $user_id ) = $this->create_person( false );

		$this->assertSame( get_avatar_url( $user_id, array( 'size' => 250 ) ), dli_get_persona_avatar( $person_id, $user_id ) );
	}
}
