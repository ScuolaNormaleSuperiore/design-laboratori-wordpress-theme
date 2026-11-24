<?php
/**
 * The data of the People page (`DLI_ContentsManager::get_people_page_data()`): the filters of the cards view.
 *
 * Structure and tag are filters of the query (a structure includes its substructures); type and text search are
 * applied to the rows (a person with one of its types), with the same rule as the table (`inc/people-filters.php`).
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `DLI_ContentsManager::get_people_page_data()`.
 */
class DLI_People_Page_Data_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * IDs of the types of person, by name.
	 *
	 * @var array<string, int>
	 */
	private $types = array();

	/**
	 * Create the types, the structures (a parent with a child) and the tag of the tests.
	 */
	public function set_up() {
		parent::set_up();
		$this->types = array(
			'professore'  => $this->create_type( 'Professore ordinario', 1 ),
			'ricercatore' => $this->create_type( 'Ricercatore', 2 ),
		);
		$parent = $this->create_term( 'Dipartimento di Scienze', 'scienze', STRUCTURE_TAXONOMY );
		$this->create_term( 'Laboratorio di Fisica', 'fisica', STRUCTURE_TAXONOMY, $parent );
		$this->create_term( 'Centro Dati', 'centro-dati', STRUCTURE_TAXONOMY );
		$this->create_term( 'Senior', 'senior', WP_DEFAULT_TAGS );
	}

	/**
	 * A type of person, with its name and its priority.
	 *
	 * @param string $name     Name.
	 * @param int    $priority Priority (order of the types).
	 * @param string $status   Status of the type.
	 * @return int ID.
	 */
	private function create_type( $name, $priority, $status = 'publish' ) {
		$type_id = $this->create_post( PEOPLE_TYPE_POST_TYPE, $name, $status );
		$this->set_acf_fields(
			$type_id,
			array(
				'nome'     => $name,
				'priorita' => $priority,
			)
		);
		return $type_id;
	}

	/**
	 * A term in Italian.
	 *
	 * @param string $name     Name.
	 * @param string $slug     Slug.
	 * @param string $taxonomy Taxonomy.
	 * @param int    $parent   Parent term, 0 for none.
	 * @return int ID.
	 */
	private function create_term( $name, $slug, $taxonomy, $parent = 0 ) {
		$term = wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug'   => $slug,
				'parent' => $parent,
			)
		);
		$this->assertNotWPError( $term );
		dli_set_term_language( (int) $term['term_id'], 'it' );
		return (int) $term['term_id'];
	}

	/**
	 * A person with name, surname, types, structures and tags.
	 *
	 * @param string               $name       Name.
	 * @param string               $surname    Surname.
	 * @param string[]             $types      Keys of the types.
	 * @param string[]             $structures Slugs of the structures.
	 * @param string[]             $tags       Slugs of the tags.
	 * @param array<string, mixed> $fields     Other ACF fields.
	 * @return int ID.
	 */
	private function create_person( $name, $surname, array $types, array $structures = array(), array $tags = array(), array $fields = array() ) {
		$person_id = $this->create_post( PEOPLE_POST_TYPE, $name . ' ' . $surname );
		$type_ids  = array();
		foreach ( $types as $type ) {
			$type_ids[] = $this->types[ $type ];
		}
		$this->set_acf_fields(
			$person_id,
			array_merge(
				array(
					'nome'                   => $name,
					'cognome'                => $surname,
					'categoria_appartenenza' => $type_ids,
				),
				$fields
			)
		);
		wp_set_object_terms( $person_id, $structures, STRUCTURE_TAXONOMY );
		wp_set_object_terms( $person_id, $tags, WP_DEFAULT_TAGS );
		return $person_id;
	}

	/**
	 * The people of the cards view, by type key, as surnames.
	 *
	 * @param array<string, mixed> $args Arguments of `get_people_page_data()`.
	 * @return array{count: int, by_type: array<string, string[]>} The number of people and their surnames by type.
	 */
	private function cards( array $args ) {
		$data    = DLI_ContentsManager::get_people_page_data( $args );
		$by_type = array();
		foreach ( $this->types as $key => $type_id ) {
			$surnames = array();
			foreach ( $data['people_by_category'][ $type_id ] ?? array() as $person ) {
				$surnames[] = (string) dli_get_field( 'cognome', $person->ID );
			}
			if ( $surnames ) {
				sort( $surnames );
				$by_type[ $key ] = $surnames;
			}
		}
		return array(
			'count'   => (int) $data['result_count'],
			'by_type' => $by_type,
		);
	}

	/**
	 * The text search finds by name, surname, name and surname in both orders, type and structure.
	 */
	public function test_the_search_finds_by_name_surname_type_and_structure() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore' ), array( 'fisica' ) );
		$this->create_person( 'Anna', 'Bianchi', array( 'ricercatore' ), array( 'centro-dati' ) );

		$expected = array(
			'rossi'       => array( 'professore' => array( 'Rossi' ) ),
			'MARIO'       => array( 'professore' => array( 'Rossi' ) ),
			'anna bianchi' => array( 'ricercatore' => array( 'Bianchi' ) ),
			'bianchi anna' => array( 'ricercatore' => array( 'Bianchi' ) ),
			'ordinario'   => array( 'professore' => array( 'Rossi' ) ),
			'centro'      => array( 'ricercatore' => array( 'Bianchi' ) ),
		);
		foreach ( $expected as $search => $by_type ) {
			$cards = $this->cards( array( 'selected_search' => $search ) );
			$this->assertSame( $by_type, $cards['by_type'], $search );
			$this->assertSame( 1, $cards['count'], $search );
		}
	}

	/**
	 * A person of two types searched by one of them is listed only under that type.
	 */
	public function test_a_person_of_two_types_is_found_only_under_the_type_searched() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore', 'ricercatore' ) );

		$this->assertSame( array( 'ricercatore' => array( 'Rossi' ) ), $this->cards( array( 'selected_search' => 'ricercatore' ) )['by_type'] );
		$this->assertSame(
			array(
				'professore'  => array( 'Rossi' ),
				'ricercatore' => array( 'Rossi' ),
			),
			$this->cards( array( 'selected_search' => 'rossi' ) )['by_type'],
			'By surname the person is under both types.'
		);
	}

	/**
	 * The type is applied to the data: only that type, and the number of people is the one of that type.
	 */
	public function test_the_type_is_applied_to_the_data() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore' ) );
		$this->create_person( 'Anna', 'Bianchi', array( 'ricercatore' ) );
		$this->create_person( 'Carla', 'Verdi', array( 'ricercatore' ) );

		$cards = $this->cards( array( 'selected_type' => (string) $this->types['ricercatore'] ) );

		$this->assertSame( array( 'ricercatore' => array( 'Bianchi', 'Verdi' ) ), $cards['by_type'] );
		$this->assertSame( 2, $cards['count'] );
	}

	/**
	 * A structure includes its substructures: a person of the child is found by the parent.
	 */
	public function test_a_structure_includes_its_substructures() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore' ), array( 'fisica' ) );
		$this->create_person( 'Anna', 'Bianchi', array( 'ricercatore' ), array( 'centro-dati' ) );

		$this->assertSame( array( 'professore' => array( 'Rossi' ) ), $this->cards( array( 'selected_structure' => 'scienze' ) )['by_type'] );
		$this->assertSame( array( 'professore' => array( 'Rossi' ) ), $this->cards( array( 'selected_structure' => 'fisica' ) )['by_type'] );
	}

	/**
	 * Structure, tag, type and search together; a combination without people gives nothing.
	 */
	public function test_the_filters_combine() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore', 'ricercatore' ), array( 'fisica' ), array( 'senior' ) );
		$this->create_person( 'Marco', 'Rossini', array( 'ricercatore' ), array( 'fisica' ) );
		$this->create_person( 'Anna', 'Rossetti', array( 'ricercatore' ), array( 'centro-dati' ), array( 'senior' ) );

		$all = array(
			'selected_structure' => 'scienze',
			'selected_level'     => 'senior',
			'selected_type'      => (string) $this->types['ricercatore'],
			'selected_search'    => 'ross',
		);
		$this->assertSame( array( 'ricercatore' => array( 'Rossi' ) ), $this->cards( $all )['by_type'] );

		$none = $this->cards( array_merge( $all, array( 'selected_search' => 'bianchi' ) ) );
		$this->assertSame( array(), $none['by_type'] );
		$this->assertSame( 0, $none['count'] );
	}

	/**
	 * A type that is not published is not listed by the cards view, so its people are neither listed nor counted under
	 * it: a person whose only type is a draft does not count, and the page says that nobody was found.
	 */
	public function test_a_type_that_is_not_published_does_not_count() {
		$this->types['bozza'] = $this->create_type( 'Collaboratore', 3, 'draft' );
		$this->create_person( 'Mario', 'Rossi', array( 'bozza' ) );
		$this->create_person( 'Anna', 'Bianchi', array( 'bozza', 'ricercatore' ) );

		foreach ( array( array(), array( 'selected_search' => 'r' ) ) as $args ) {
			$cards = $this->cards( $args );
			$this->assertSame( array( 'ricercatore' => array( 'Bianchi' ) ), $cards['by_type'], 'Only the published types are listed.' );
			$this->assertSame( 1, $cards['count'], 'A person whose types are not published is not counted: the cards view would show nothing.' );
		}
	}

	/**
	 * Accents do not count, as with the search of the database used before: an old link `?cognome=Nicolo` still finds
	 * «Nicolò».
	 */
	public function test_accents_do_not_count_in_the_search() {
		$this->create_person( 'Anna', 'Nicolò', array( 'ricercatore' ) );

		foreach ( array( 'Nicolo', 'nicolò', 'NICOLO' ) as $search ) {
			$this->assertSame( array( 'ricercatore' => array( 'Nicolò' ) ), $this->cards( array( 'selected_search' => $search ) )['by_type'], $search );
		}
	}

	/**
	 * A person left out of the lists stays out, also when the search would find it.
	 */
	public function test_a_person_left_out_of_the_lists_stays_out() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore' ), array(), array(), array( 'escludi_da_elenco' => 1 ) );

		$this->assertSame( 0, $this->cards( array( 'selected_search' => 'rossi' ) )['count'] );
	}

	/**
	 * The search field of the cards view shows the search applied and reloads with `cerca`, without a help note.
	 */
	public function test_the_search_field_of_the_cards_view() {
		$this->use_italian_texts();
		try {
			ob_start();
			get_template_part(
				'template-parts/persone/filters',
				null,
				array(
					'selected_search' => 'Rossi Mario',
					'result_count'    => 1,
				)
			);
			$html = (string) ob_get_clean();
		} finally {
			$this->restore_theme_translations();
		}

		$this->assertSame( 1, preg_match( '#<label for="searchPeople">([^<]*)</label>#', $html, $label ) );
		$this->assertSame( 'Cerca', trim( $label[1] ), 'Not «Cerca per cognome» any more.' );
		$this->assertSame( 1, preg_match( '#<input[^>]*id="searchPeople"[^>]*>#', $html, $input ) );
		$this->assertStringContainsString( 'value="Rossi Mario"', $input[0] );
		$this->assertStringContainsString( 'data-dli-people-search="cerca"', $input[0], 'The field reloads with cerca.' );
		$this->assertStringContainsString( 'aria-label="Cerca per nome, cognome, tipologia o struttura"', $input[0], 'The name of the field says what it searches, as the field of the table.' );
		$this->assertStringNotContainsString( 'searchPeopleHelp', $html, 'The removed help note leaves no dangling description reference.' );
		$this->assertStringNotContainsString( 'Cerca per nome, cognome, tipologia o struttura.', $html );
		$this->assertStringNotContainsString( 'onsearch', $html, 'onsearch is not standard and Firefox does not have it: the script handles the field.' );
		$this->assertTrue( wp_script_is( 'dli-people-filters', 'enqueued' ), 'The filters load the script that handles the field.' );
	}

	/**
	 * The data say which search and which type were applied; without filters every person is there.
	 */
	public function test_the_data_carry_the_filters_applied() {
		$this->create_person( 'Mario', 'Rossi', array( 'professore' ) );
		$this->create_person( 'Anna', 'Bianchi', array( 'ricercatore' ) );

		$data = DLI_ContentsManager::get_people_page_data(
			array(
				'selected_search' => 'rossi',
				'selected_type'   => (string) $this->types['professore'],
			)
		);
		$this->assertSame( 'rossi', $data['selected_search'] );
		$this->assertSame( (string) $this->types['professore'], $data['selected_type'] );
		$this->assertSame( 2, $this->cards( array() )['count'] );
	}
}
