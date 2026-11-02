<?php
/**
 * The homepage section "Il laboratorio": its spacing and when it is printed.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `template-parts/home/site-presentation.php`.
 */
class DLI_Home_Presentation_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * The current language that Polylang had before the test, put back afterwards: it is a property of the
	 * Polylang object, which is shared by all the tests, and is not rolled back with the database.
	 *
	 * @var object|false|null
	 */
	private $previous_language = false;

	/**
	 * Remember the current language of Polylang and write the text of the section.
	 */
	public function set_up() {
		parent::set_up();
		$this->previous_language = PLL()->curlang;
		PLL()->curlang           = PLL()->model->get_language( 'it' );
		update_option(
			'il_laboratorio',
			array(
				'etichetta'               => 'Titolo della presentazione',
				'descrizione_laboratorio' => 'Testo della presentazione.',
			)
		);
	}

	/**
	 * Put back the current language of Polylang.
	 */
	public function tear_down() {
		PLL()->curlang = $this->previous_language;
		parent::tear_down();
	}

	/**
	 * Print the section.
	 *
	 * @param string $enabled Value of the flag of the row of Sezioni HP.
	 * @return string
	 */
	private function render_section( $enabled = 'true' ) {
		ob_start();
		get_template_part( 'template-parts/home/site-presentation', null, array( 'enabled' => $enabled ) );
		return (string) ob_get_clean();
	}

	/**
	 * The vertical space is explicit (the padding of the other sections of the home), not the class `my-12`, which
	 * does not exist in Bootstrap Italia or in the theme.
	 */
	public function test_spacing_is_an_explicit_padding_and_not_an_undefined_class() {
		$html = $this->render_section();

		$this->assertMatchesRegularExpression( '/<section id="presentazione" class="[^"]*\bpt-5\b[^"]*\bpb-5\b[^"]*"/', $html );
		$this->assertStringNotContainsString( 'my-12', $html );
	}

	/**
	 * The title, the text and the name of the section are the ones of the settings, as before.
	 */
	public function test_prints_title_and_text() {
		$html = $this->render_section();

		$this->assertStringContainsString( 'aria-labelledby="dli-presentazione-title"', $html );
		$this->assertStringContainsString( '>Titolo della presentazione</h2>', $html );
		$this->assertStringContainsString( 'Testo della presentazione.', $html );
	}

	/**
	 * Nothing is printed when the section is off.
	 */
	public function test_prints_nothing_when_off() {
		$this->assertSame( '', trim( $this->render_section( 'false' ) ) );
	}
}
