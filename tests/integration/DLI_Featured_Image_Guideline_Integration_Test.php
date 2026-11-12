<?php
/**
 * The advice on the format of the featured image, written under the box "Immagine in evidenza" of the banners and
 * of the sponsors, the two content types that use the classic editor.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `dli_add_featured_image_guideline()`.
 */
class DLI_Featured_Image_Guideline_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The texts of the theme are written in Italian, and the test site is in English: run every test with the
	 * Italian texts, so that the checks do not depend on the English catalogue.
	 */
	public function set_up() {
		parent::set_up();
		$this->use_italian_texts();
	}

	/**
	 * Load the translations of the theme again.
	 */
	public function tear_down() {
		$this->restore_theme_translations();
		parent::tear_down();
	}

	/**
	 * Markup that stands for the box that WordPress prints, to see that it stays.
	 */
	const ORIGINAL_BOX = '<p class="original-box">Imposta l\'immagine in evidenza</p>';

	/**
	 * The box of the featured image as the classic editor prints it, with its filters.
	 *
	 * @param int $post_id      The post.
	 * @param int $thumbnail_id The featured image, or 0.
	 * @return string
	 */
	private function box( $post_id, $thumbnail_id = 0 ) {
		return _wp_post_thumbnail_html( $thumbnail_id, $post_id );
	}

	/**
	 * The banner box says that the picture is a wide strip cropped around the centre, and keeps WordPress's own box.
	 */
	public function test_banner_box_gives_the_format_of_the_banner() {
		$banner_id = self::factory()->post->create( array( 'post_type' => BANNER_POST_TYPE ) );

		$html = $this->box( $banner_id );

		$this->assertStringContainsString( '1920 × 480', $html );
		$this->assertStringContainsString( 'Zona sicura', $html );
		$this->assertStringContainsString( 'dli-image-guideline', $html );
		$this->assertStringContainsString( 'set-post-thumbnail', $html, 'The link of WordPress that sets the image stays.' );
	}

	/**
	 * With an image already set, the box keeps its preview and the remove link, and gives the advice.
	 */
	public function test_banner_box_with_an_image_keeps_the_box_and_gives_the_format() {
		$banner_id     = self::factory()->post->create( array( 'post_type' => BANNER_POST_TYPE ) );
		$attachment_id = self::factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/canola.jpg', $banner_id );
		set_post_thumbnail( $banner_id, $attachment_id );

		$html = $this->box( $banner_id, $attachment_id );

		$this->assertStringContainsString( 'remove-post-thumbnail', $html, 'The link of WordPress that removes the image stays.' );
		$this->assertStringContainsString( '1920 × 480', $html );
	}

	/**
	 * The sponsor box says that the logo is shown 64 px high and asks for twice that.
	 */
	public function test_sponsor_box_gives_the_format_of_the_logo() {
		$sponsor_id = self::factory()->post->create( array( 'post_type' => SPONSOR_POST_TYPE ) );

		$html = $this->box( $sponsor_id );

		$this->assertStringContainsString( '64 px', $html );
		$this->assertStringContainsString( '128 px', $html );
		$this->assertStringContainsString( 'dli-image-guideline', $html );
		$this->assertStringContainsString( 'set-post-thumbnail', $html );
	}

	/**
	 * Every other content type, the pages and the posts keep their box as it is.
	 *
	 * @dataProvider other_post_types
	 *
	 * @param string $post_type Post type.
	 */
	public function test_other_post_types_are_not_changed( $post_type ) {
		$post_id = self::factory()->post->create( array( 'post_type' => $post_type ) );

		$this->assertSame( self::ORIGINAL_BOX, apply_filters( 'admin_post_thumbnail_html', self::ORIGINAL_BOX, $post_id, 0 ) );
		$this->assertStringNotContainsString( 'dli-image-guideline', $this->box( $post_id ) );
	}

	/**
	 * Post types that must not get the advice of the banner or of the sponsor.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function other_post_types() {
		return array(
			'news'        => array( NEWS_POST_TYPE ),
			'event'       => array( EVENT_POST_TYPE ),
			'person'      => array( PEOPLE_POST_TYPE ),
			'publication' => array( PUBLICATION_POST_TYPE ),
			'page'        => array( 'page' ),
			'post'        => array( 'post' ),
		);
	}

	/**
	 * With no post, or one that does not exist, the box is returned untouched.
	 */
	public function test_without_a_known_post_the_box_is_untouched() {
		$this->assertSame( self::ORIGINAL_BOX, apply_filters( 'admin_post_thumbnail_html', self::ORIGINAL_BOX, 0, 0 ) );
		$this->assertSame( self::ORIGINAL_BOX, apply_filters( 'admin_post_thumbnail_html', self::ORIGINAL_BOX, 999999, 0 ) );
		$this->assertSame( self::ORIGINAL_BOX, apply_filters( 'admin_post_thumbnail_html', self::ORIGINAL_BOX, null, 0 ) );
	}

	/**
	 * The advice is plain text inside one paragraph: nothing else is added to the box.
	 */
	public function test_the_added_text_is_one_plain_paragraph() {
		$banner_id = self::factory()->post->create( array( 'post_type' => BANNER_POST_TYPE ) );

		$html  = apply_filters( 'admin_post_thumbnail_html', self::ORIGINAL_BOX, $banner_id, 0 );
		$added = substr( $html, strlen( self::ORIGINAL_BOX ) );

		$this->assertStringStartsWith( '<p class="description dli-image-guideline">', $added );
		$this->assertStringEndsWith( '</p>', $added );
		$this->assertSame( $added, '<p class="description dli-image-guideline">' . esc_html( wp_strip_all_tags( dli_get_image_guideline_text( 'banner_home' ) ) ) . '</p>' );
	}
}
