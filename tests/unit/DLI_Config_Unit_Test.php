<?php
/**
 * Unit tests of the configuration constants in config-lab.php.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Configuration tables the theme relies on.
 */
class DLI_Config_Unit_Test extends TestCase {

	/**
	 * Italian is the default language and English the other one.
	 */
	public function test_languages() {
		$this->assertSame( 'it', DLI_DEFAULT_LANGUAGE );
		$this->assertSame( 'it', DLI_IT_SLUG );
		$this->assertSame( 'en', DLI_EN_SLUG );
	}

	/**
	 * ACF and Polylang are required: the theme does not work without them.
	 */
	public function test_suggested_plugins_include_required_plugins() {
		$slugs = array_map(
			static function ( $plugin ) {
				return $plugin['slug'] ?? '';
			},
			SUGGESTED_PLUGINS
		);
		$this->assertContains( 'advanced-custom-fields', $slugs );
		$this->assertContains( 'polylang', $slugs );
	}

	/**
	 * The home page sections are the expected ones, each one with a configuration.
	 */
	public function test_home_page_sections() {
		$expected = array(
			'main_hero',
			'site_description',
			'main_carousel',
			'featured_contents',
			'card_navigation_section',
			'page_navigation_section',
			'events_section',
			'news_section',
			'publications_section',
			'articles_section',
			'banners_section',
			'sponsors_section',
			'video_section',
		);
		$this->assertSame( $expected, array_keys( DLI_HP_SECTIONS ) );
		$this->assertSame( 'Card di navigazione', DLI_HP_SECTIONS['card_navigation_section']['name'] );
	}

	/**
	 * Every content type that has a listing page is mapped in DLI_PAGE_PER_CT.
	 */
	public function test_page_per_content_type_has_the_main_content_types() {
		foreach ( array( 'persona', 'progetto', 'evento', 'notizia', 'pubblicazione', 'luogo', 'brevetto', 'spinoff' ) as $content_type ) {
			$this->assertArrayHasKey( $content_type, DLI_PAGE_PER_CT, "Missing content type {$content_type}" );
		}
	}
}
