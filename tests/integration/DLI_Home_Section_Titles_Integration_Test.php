<?php
/**
 * The titles of the homepage sections Sponsor and Carousel follow the choice "Mostra titolo" of their row.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `template-parts/home/hp-sponsor-section.php` and `template-parts/home/carousel.php`.
 */
class DLI_Home_Section_Titles_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Print a section of the home.
	 *
	 * @param string $template   The template of the section, under `template-parts/home/`.
	 * @param string $show_title Value of the choice "Mostra titolo" of the row.
	 * @return string
	 */
	private function render_section( $template, $show_title ) {
		ob_start();
		get_template_part(
			'template-parts/home/' . $template,
			null,
			array(
				'enabled'    => 'true',
				'show_title' => $show_title,
			)
		);
		return (string) ob_get_clean();
	}

	/**
	 * Check that the section is named by its h2, seen with the common style or visually hidden.
	 *
	 * @param string $html       The section.
	 * @param string $title_id   The id of the title.
	 * @param string $show_title Value of the choice "Mostra titolo".
	 */
	private function assert_section_title( $html, $title_id, $show_title ) {
		$this->assertSame( 1, preg_match( '#<h2 id="' . preg_quote( $title_id, '#' ) . '" class="([^"]*)"#', $html, $match ), "The h2 is there (show_title {$show_title})." );
		$this->assertSame( 'true' === $show_title ? 'h3 pb-2' : 'visually-hidden', $match[1] );
		$this->assertStringContainsString( 'aria-labelledby="' . $title_id . '"', $html );
	}

	/**
	 * The sponsor title is seen when the row asks for it; otherwise it is hidden, but the section stays named.
	 */
	public function test_sponsor_title_follows_the_row() {
		$sponsor_id = $this->create_post( SPONSOR_POST_TYPE, 'Uno sponsor' );
		update_post_meta( $sponsor_id, 'priorita', 1 );

		foreach ( array( 'true', 'false' ) as $show_title ) {
			$this->assert_section_title( $this->render_section( 'hp-sponsor-section', $show_title ), 'sponsor-title', $show_title );
		}
	}

	/**
	 * The carousel title is seen when the row asks for it; otherwise it is hidden.
	 */
	public function test_carousel_title_follows_the_row() {
		foreach ( array( 'true', 'false' ) as $show_title ) {
			$this->assert_section_title( $this->render_section( 'carousel', $show_title ), 'carousel-principale-title', $show_title );
		}
	}

	/**
	 * The carousel title names one landmark only: the section and the region inside it must not both point to it,
	 * or screen readers announce two nested regions with the same name.
	 */
	public function test_carousel_title_names_one_landmark() {
		$html = $this->render_section( 'carousel', 'true' );
		$this->assertSame( 1, substr_count( $html, 'aria-labelledby="carousel-principale-title"' ), 'One element only is named by the carousel title.' );
		$this->assertStringContainsString( 'role="region" aria-labelledby="carousel-principale-title"', $html, 'The carousel region keeps its name.' );
	}
}
