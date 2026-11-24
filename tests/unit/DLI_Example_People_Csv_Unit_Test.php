<?php
/**
 * Contract test: the example file of the people has the shape the import expects.
 *
 * The file in `SETUP/CSV_Import/` is the one downloaded from Importa → Persone.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

require_once DLI_TEST_THEME_DIR . '/inc/admin/class-dli-csv-reader.php';

/**
 * `SETUP/CSV_Import/people-example.csv`.
 */
class DLI_Example_People_Csv_Unit_Test extends TestCase {

	const REQUIRED = array( 'nome', 'cognome', 'email' );
	const OPTIONAL = array( 'titolo', 'telefono', 'tipologia_persona', 'struttura', 'stato', 'sito_web', 'body', 'escludi_da_elenco', 'disattiva_pagina_dettaglio' );

	/**
	 * The example is read by the reader of the import with every column of the people, and no column is ignored.
	 */
	public function test_the_example_has_every_column_and_two_people() {
		$result = DLI_CSV_Reader::read(
			DLI_TEST_THEME_DIR . '/SETUP/CSV_Import/people-example.csv',
			array(
				'required' => self::REQUIRED,
				'optional' => self::OPTIONAL,
			)
		);

		$this->assertSame( array(), $result['errors'] );
		$this->assertSame( array(), $result['ignored'] );
		$this->assertSame( array_merge( self::REQUIRED, self::OPTIONAL ), $result['headers'] );
		$this->assertSame( array( 'mario.rossi@example.com', 'anna.bianchi@example.com' ), array_column( array_column( $result['rows'], 'cells' ), 'email' ) );
		$this->assertSame( 'Breve biografia di Mario Rossi; professore di fisica.', $result['rows'][0]['cells']['body'], 'A cell with a semicolon is quoted.' );
	}
}
