<?php
/**
 * Integration tests of the term helper shared by the importers (DLI_BaseImporter::get_or_create_term_id()).
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The helper returns the ID of an existing term, creates a missing one and refuses what it cannot create.
 */
class DLI_Base_Importer_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Importer that exposes the protected helper under test.
	 *
	 * @return DLI_BaseImporter
	 */
	private function importer() {
		return new class() extends DLI_BaseImporter {
			/**
			 * Public constructor: the base one is protected.
			 */
			public function __construct() {}

			/**
			 * Expose the protected helper.
			 *
			 * @param string $term_name Term label.
			 * @param string $taxonomy  Taxonomy name.
			 * @return int
			 */
			public function term_id( string $term_name, string $taxonomy ): int {
				return $this->get_or_create_term_id( $term_name, $taxonomy );
			}

			/**
			 * Expose the protected JSON decoder.
			 *
			 * @param string $body   Raw response body.
			 * @param string $source Source name used in the messages.
			 * @return mixed
			 */
			public function decode( string $body, string $source ) {
				return $this->decode_external_json_payload( $body, true, $source );
			}
		};
	}

	/**
	 * The message of the exception raised by a callback.
	 *
	 * @param callable $callback Code expected to throw.
	 * @return string
	 */
	private function exception_message( callable $callback ) {
		try {
			$callback();
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
		$this->fail( 'An exception was expected.' );
	}

	/**
	 * A missing term is created and its ID returned.
	 */
	public function test_a_missing_term_is_created() {
		$term_id = $this->importer()->term_id( 'Nuovo tag', 'post_tag' );

		$this->assertIsInt( $term_id );
		$this->assertGreaterThan( 0, $term_id );
		$term = get_term( $term_id, 'post_tag' );
		$this->assertInstanceOf( WP_Term::class, $term );
		$this->assertSame( 'Nuovo tag', $term->name );
	}

	/**
	 * An existing term is reused: the same ID comes back and no duplicate is created.
	 */
	public function test_an_existing_term_is_reused_without_a_duplicate() {
		$existing = self::factory()->tag->create( array( 'name' => 'Esistente' ) );

		$first  = $this->importer()->term_id( 'Esistente', 'post_tag' );
		$second = $this->importer()->term_id( 'Esistente', 'post_tag' );

		$this->assertSame( $existing, $first );
		$this->assertSame( $existing, $second );
		$this->assertCount( 1, get_terms( array( 'taxonomy' => 'post_tag', 'name' => 'Esistente', 'hide_empty' => false ) ) );
	}

	/**
	 * Surrounding spaces do not matter and an empty name is refused.
	 */
	public function test_the_name_is_trimmed_and_an_empty_name_is_refused() {
		$existing = self::factory()->tag->create( array( 'name' => 'Con spazi' ) );

		$this->assertSame( $existing, $this->importer()->term_id( '  Con spazi  ', 'post_tag' ) );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Nome termine tassonomia non valido.' );
		$this->importer()->term_id( '   ', 'post_tag' );
	}

	/**
	 * A term that cannot be created (here: unknown taxonomy) raises an exception that carries the WordPress error.
	 */
	public function test_a_term_that_cannot_be_created_raises_an_exception() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Errore creazione termine tassonomia' );
		$this->importer()->term_id( 'Qualcosa', 'tassonomia_inesistente' );
	}

	/**
	 * The WordPress error carried by the message of a term that cannot be created is in the message as it is (the
	 * message reaches only the JSON of the REST response and the error log).
	 */
	public function test_the_term_error_in_the_message_is_kept_as_it_is() {
		add_filter(
			'pre_insert_term',
			static function () {
				return new WP_Error( 'dli_test', "L'elemento \"x\" esiste già" );
			}
		);

		$message = $this->exception_message(
			function () {
				$this->importer()->term_id( 'Qualcosa', 'post_tag' );
			}
		);

		$this->assertSame( "Errore creazione termine tassonomia: L'elemento \"x\" esiste già", $message );
	}

	/**
	 * The source named in the message of an empty payload is in the message as it is.
	 */
	public function test_the_source_of_an_empty_payload_is_kept_as_it_is() {
		$message = $this->exception_message(
			function () {
				$this->importer()->decode( '   ', "Indico dell'ente" );
			}
		);

		$this->assertSame( "Risposta Indico dell'ente vuota.", $message );
	}

	/**
	 * The source and the decoder error in the message of an invalid payload are in the message as they are.
	 */
	public function test_the_message_of_an_invalid_payload_is_kept_as_it_is() {
		$message = $this->exception_message(
			function () {
				$this->importer()->decode( '{non valido', "Indico dell'ente" );
			}
		);

		$this->assertStringStartsWith( "Errore decodifica JSON Indico dell'ente: ", $message );
		$this->assertStringNotContainsString( '&#039;', $message );
	}
}
