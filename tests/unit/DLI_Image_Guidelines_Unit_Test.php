<?php
/**
 * Unit tests of the list of recommended image formats in inc/image-guidelines.php.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Recommended image formats of the places where an image is uploaded.
 */
class DLI_Image_Guidelines_Unit_Test extends TestCase {

	/**
	 * Identifiers of the places of the list, as agreed in the plan.
	 *
	 * @var string[]
	 */
	const EXPECTED_IDS = array(
		'hero_home',
		'banner_home',
		'card_elenco',
		'carousel_correlati',
		'dettaglio',
		'sponsor',
		'persona',
		'pubblicazione_copertina',
		'logo_header',
		'logo_footer',
		'logo_mobile',
		'logo_spinoff',
		'foto_risorsa_tecnica',
	);

	/**
	 * Every agreed place is in the list, and nothing else.
	 */
	public function test_the_list_has_the_agreed_places() {
		$this->assertSame( self::EXPECTED_IDS, array_keys( dli_get_image_guidelines() ) );
	}

	/**
	 * Every place has a label, a ratio, a note on the crop and the formats.
	 */
	public function test_every_place_has_the_required_fields() {
		foreach ( dli_get_image_guidelines() as $id => $guideline ) {
			foreach ( array( 'label', 'ratio', 'formats', 'note' ) as $field ) {
				$this->assertArrayHasKey( $field, $guideline, "$id: missing $field." );
				$this->assertIsString( $guideline[ $field ], "$id: $field is not text." );
				$this->assertNotSame( '', $guideline[ $field ], "$id: $field is empty." );
			}
			foreach ( array( 'recommended', 'minimum', 'aspect' ) as $field ) {
				$this->assertArrayHasKey( $field, $guideline, "$id: missing $field." );
			}
			$this->assertArrayHasKey( 'safe_zone', $guideline, "$id: missing safe_zone." );
			$this->assertIsString( $guideline['safe_zone'], "$id: safe_zone is not text." );
		}
	}

	/**
	 * The minimum never exceeds the recommended size.
	 */
	public function test_the_minimum_does_not_exceed_the_recommended_size() {
		foreach ( dli_get_image_guidelines() as $id => $guideline ) {
			if ( null === $guideline['recommended'] || null === $guideline['minimum'] ) {
				continue;
			}
			list( $recommended_width, $recommended_height ) = $guideline['recommended'];
			list( $minimum_width, $minimum_height )         = $guideline['minimum'];
			$this->assertLessThanOrEqual( $recommended_width, $minimum_width, "$id: the minimum width exceeds the recommended one." );
			if ( null !== $minimum_height ) {
				$this->assertLessThanOrEqual( $recommended_height, $minimum_height, "$id: the minimum height exceeds the recommended one." );
			}
		}
	}

	/**
	 * When a place has a fixed aspect, the sizes of the list follow it.
	 */
	public function test_the_sizes_follow_the_aspect_of_the_place() {
		$with_aspect = 0;
		foreach ( dli_get_image_guidelines() as $id => $guideline ) {
			if ( null === $guideline['aspect'] ) {
				continue;
			}
			++$with_aspect;
			$expected = $guideline['aspect'][0] / $guideline['aspect'][1];
			foreach ( array( 'recommended', 'minimum' ) as $field ) {
				if ( null === $guideline[ $field ] || null === $guideline[ $field ][1] ) {
					continue;
				}
				$actual = $guideline[ $field ][0] / $guideline[ $field ][1];
				$this->assertEqualsWithDelta( $expected, $actual, $expected * 0.01, "$id: the $field size does not follow the aspect." );
			}
		}
		$this->assertGreaterThan( 0, $with_aspect, 'No place has a fixed aspect: the check would test nothing.' );
	}

	/**
	 * The short text names the recommended size and says that it is a recommendation, never an exact size.
	 */
	public function test_the_text_gives_the_recommended_size_and_the_warning() {
		$text = dli_get_image_guideline_text( 'card_elenco' );
		$this->assertStringContainsString( 'consigliato', $text );
		$this->assertStringContainsString( '1200 × 675', $text );
		$this->assertStringContainsString( 'minimo 600 × 338', $text );
		$this->assertStringContainsString( 'indicative', $text );
		$this->assertStringNotContainsString( 'esatta', $text );
	}

	/**
	 * A minimum without a height is written as a width.
	 */
	public function test_a_minimum_without_a_height_is_written_as_a_width() {
		$text = dli_get_image_guideline_text( 'hero_home' );
		$this->assertStringContainsString( '1920 × 600', $text );
		$this->assertStringContainsString( 'minimo 1600 px di larghezza', $text );
	}

	/**
	 * The maximum weight comes from the constant that the upload limit already uses.
	 */
	public function test_the_maximum_weight_comes_from_the_configuration() {
		foreach ( array_keys( dli_get_image_guidelines() ) as $id ) {
			$this->assertStringContainsString(
				DLI_MAX_IMG_FILE_SIZE_MB . ' MB',
				dli_get_image_guideline_text( $id ),
				"$id: the text does not give the maximum weight."
			);
		}
	}

	/**
	 * Hero and banner, whose crop changes the most, say where to keep the subject.
	 */
	public function test_hero_and_banner_give_the_safe_zone() {
		foreach ( array( 'hero_home', 'banner_home' ) as $id ) {
			$guideline = dli_get_image_guideline( $id );
			$this->assertNotSame( '', $guideline['safe_zone'], "$id: no safe zone." );
			$this->assertStringContainsString( 'Zona sicura', dli_get_image_guideline_text( $id ), "$id: the text has no safe zone." );
		}
	}

	/**
	 * A place without a safe zone does not print the label of one.
	 */
	public function test_a_place_without_a_safe_zone_does_not_print_one() {
		$this->assertSame( '', dli_get_image_guideline( 'logo_header' )['safe_zone'] );
		$this->assertStringNotContainsString( 'Zona sicura', dli_get_image_guideline_text( 'logo_header' ) );
	}

	/**
	 * The image of a detail page is shown in its own proportions (measured: 526 × 351 from a 600 × 400 file), so the
	 * list does not ask for a ratio and says that the image is not cropped.
	 */
	public function test_the_detail_image_is_not_cropped() {
		$guideline = dli_get_image_guideline( 'dettaglio' );
		$this->assertNull( $guideline['aspect'] );
		$this->assertStringContainsString( 'non viene tagliata', dli_get_image_guideline_text( 'dettaglio' ) );
		$this->assertStringNotContainsString( '16:9', dli_get_image_guideline_text( 'dettaglio' ) );
	}

	/**
	 * The logos are shown 80 px high (measured: 131 × 80), so a raster logo needs 160 px for the dense screens.
	 */
	public function test_a_raster_logo_is_asked_at_twice_the_height_it_is_shown() {
		foreach ( array( 'logo_header', 'logo_footer', 'logo_mobile' ) as $id ) {
			$text = dli_get_image_guideline_text( $id );
			$this->assertStringContainsString( 'alto almeno 160 px', $text, "$id: wrong logo height." );
			$this->assertStringStartsWith( 'Formato consigliato: SVG', $text, "$id: the logo asks for an SVG first." );
		}
	}

	/**
	 * The photo of a person is shown in a circle at most 112 px wide (measured), so it is asked square, with the face
	 * at the centre, and at least twice the size it is shown for the dense screens.
	 */
	public function test_the_person_photo_is_asked_square_for_a_circle() {
		$guideline = dli_get_image_guideline( 'persona' );
		$this->assertSame( array( 1, 1 ), $guideline['aspect'] );
		$this->assertSame( array( 224, 224 ), $guideline['minimum'] );
		$this->assertStringContainsString( 'cerchio', dli_get_image_guideline_text( 'persona' ) );
	}

	/**
	 * The cover of a publication is shown in its own proportions (measured: 305 × 404 from a 390 × 517 file) in a column
	 * of about 305 px, so no ratio is asked and the minimum is twice the column for the dense screens.
	 */
	public function test_the_publication_cover_is_not_cropped() {
		$guideline = dli_get_image_guideline( 'pubblicazione_copertina' );
		$this->assertNull( $guideline['aspect'] );
		$this->assertNull( $guideline['recommended'] );
		$this->assertSame( array( 610, null ), $guideline['minimum'] );
		$text = dli_get_image_guideline_text( 'pubblicazione_copertina' );
		$this->assertStringContainsString( 'verticale', $text );
		$this->assertStringContainsString( 'non viene tagliata', $text );
	}

	/**
	 * The logo of a spin-off fills a box of the list (measured: 403 wide, 238 to 277 high, `object-fit: cover`) and is
	 * cropped at the edges: it is asked in a ratio close to the box, with a margin around the logo.
	 */
	public function test_the_spinoff_logo_is_asked_with_a_margin_because_the_list_crops_it() {
		$guideline = dli_get_image_guideline( 'logo_spinoff' );
		$this->assertNull( $guideline['aspect'] );
		$this->assertEqualsWithDelta( 1.6, $guideline['recommended'][0] / $guideline['recommended'][1], 0.05 );
		$this->assertNotSame( '', $guideline['safe_zone'] );
		$text = dli_get_image_guideline_text( 'logo_spinoff' );
		$this->assertStringContainsString( 'elenco', $text );
		$this->assertStringContainsString( 'tagliato', $text );
		$this->assertStringContainsString( 'margine', $text );
	}

	/**
	 * The photo of a technical resource is shown in its own proportions in a figure at most 280 px wide (measured: 280 × 280
	 * from a 512 × 512 file), so no ratio is asked, the minimum is twice the figure, and the fallback is told.
	 */
	public function test_the_technical_resource_photo_is_not_cropped() {
		$guideline = dli_get_image_guideline( 'foto_risorsa_tecnica' );
		$this->assertNull( $guideline['aspect'] );
		$this->assertNull( $guideline['recommended'] );
		$this->assertSame( array( 560, null ), $guideline['minimum'] );
		$text = dli_get_image_guideline_text( 'foto_risorsa_tecnica' );
		$this->assertStringContainsString( 'non viene tagliata', $text );
		$this->assertStringContainsString( 'immagine in evidenza', $text );
	}

	/**
	 * An unknown place gives nothing.
	 */
	public function test_an_unknown_place_gives_nothing() {
		$this->assertNull( dli_get_image_guideline( 'non-esiste' ) );
		$this->assertSame( '', dli_get_image_guideline_text( 'non-esiste' ) );
	}

	/**
	 * Every place gives a text.
	 */
	public function test_every_place_gives_a_text() {
		foreach ( array_keys( dli_get_image_guidelines() ) as $id ) {
			$this->assertNotSame( '', dli_get_image_guideline_text( $id ), "$id: empty text." );
		}
	}
}
