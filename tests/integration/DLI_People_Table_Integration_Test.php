<?php
/**
 * The people table: what it puts in the page, in the HTML and in the data it hands to the script.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * `template-parts/persone/view-tabella.php`.
 */
class DLI_People_Table_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Create a person with its fields.
	 *
	 * @param string               $surname Surname.
	 * @param array<string, mixed> $fields  ACF fields, by name, on top of the name.
	 * @return int Person ID.
	 */
	private function create_person( $surname, array $fields = array() ) {
		$person_id = $this->create_post( PEOPLE_POST_TYPE, 'Persona ' . $surname );
		$this->set_acf_fields( $person_id, array_merge( array( 'nome' => 'Ada', 'cognome' => $surname ), $fields ) );
		return $person_id;
	}

	/**
	 * Print the table for some people and give back the HTML and the data of the script.
	 *
	 * @param int[]                $person_ids People to list.
	 * @param array<string, mixed> $options    Options of the people page (`persone`), such as the hidden columns.
	 * @param array<string, mixed> $page_data  Additional data for the table.
	 * @return array{html: string, data: array<string, mixed>} The page and the decoded data.
	 */
	private function render_table( array $person_ids, array $options = array(), array $page_data = array() ) {
		$this->set_theme_options(
			'persone',
			array_merge(
				array(
					'hide_people_table_email' => 'false',
					'hide_people_table_phone' => 'false',
				),
				$options
			)
		);

		$rows = array();
		foreach ( $person_ids as $person_id ) {
			$rows[] = array(
				'person'      => get_post( $person_id ),
				'category_id' => 0,
			);
		}

		// The script keeps its data from one test to the next: each table starts from a script without data.
		wp_deregister_script( 'dli-people-filters' );

		ob_start();
		get_template_part(
			'template-parts/persone/view-tabella',
			null,
			array(
				'page_data' => array_merge(
					array(
						'people_rows' => $rows,
						'categories'  => array(),
						'structures'  => array(),
						'tags'        => array(),
					),
					$page_data
				),
			)
		);
		$html = (string) ob_get_clean();

		$this->assertTrue( wp_script_is( 'dli-people-filters', 'enqueued' ), 'The table loads the script of the filters.' );
		$this->assertStringNotContainsString( '<script', $html, 'The template prints no script of its own.' );
		// WordPress adds a `//# sourceURL=` line after the data, for the tools of the browser.
		$inline = (string) wp_scripts()->get_inline_script_data( 'dli-people-filters', 'before' );
		$this->assertSame( 1, preg_match( '#^window\.DLI_PEOPLE_TABLE = (\{.*\});$#m', $inline, $found ), 'The script receives the data of the table.' );
		$data = json_decode( $found[1], true );
		$this->assertIsArray( $data );
		$html .= "\n" . $inline;

		return array(
			'html' => $html,
			'data' => $data,
		);
	}

	/**
	 * Each structure of the filter comes with its substructures, so that the table finds the people of a substructure
	 * by its parent as the cards view does; each row carries the names of its structures for the text search.
	 */
	public function test_the_structures_come_with_their_substructures_and_names() {
		$parent = wp_insert_term( 'Dipartimento di Scienze', STRUCTURE_TAXONOMY, array( 'slug' => 'scienze' ) );
		$child  = wp_insert_term( 'Laboratorio di Fisica', STRUCTURE_TAXONOMY, array( 'slug' => 'fisica', 'parent' => $parent['term_id'] ) );
		$other  = wp_insert_term( 'Centro Dati', STRUCTURE_TAXONOMY, array( 'slug' => 'centro-dati' ) );
		$person = $this->create_person( 'Rossi' );
		wp_set_object_terms( $person, array( 'fisica', 'centro-dati' ), STRUCTURE_TAXONOMY );

		$page = $this->render_table(
			array( $person ),
			array(),
			array( 'structures' => array( get_term( $parent['term_id'] ), get_term( $child['term_id'] ), get_term( $other['term_id'] ) ) )
		);

		$this->assertSame(
			array(
				'scienze'     => array( 'scienze', 'fisica' ),
				'fisica'      => array( 'fisica' ),
				'centro-dati' => array( 'centro-dati' ),
			),
			$page['data']['structureDescendants']
		);
		$names = $page['data']['rows'][0]['structureNames'];
		sort( $names );
		$this->assertSame( array( 'Centro Dati', 'Laboratorio di Fisica' ), $names );
	}

	/** The filters asked for in the address are selected in the controls of the table. */
	public function test_the_table_starts_from_the_filters_of_the_address() {
		$structure = wp_insert_term( 'Laboratorio di Fisica', STRUCTURE_TAXONOMY, array( 'slug' => 'fisica' ) );
		$tag       = wp_insert_term( 'Senior', WP_DEFAULT_TAGS, array( 'slug' => 'senior' ) );

		$page = $this->render_table(
			array(),
			array( 'hide_filter_tag' => 'false' ),
			array(
				'structures'         => array( get_term( $structure['term_id'] ) ),
				'tags'               => array( get_term( $tag['term_id'] ) ),
				'selected_structure' => 'fisica',
				'selected_level'     => 'senior',
				'selected_search'    => 'Rossi Mario',
			)
		);

		$this->assertMatchesRegularExpression( '#<option value="fisica"\s+selected=[\'"]selected[\'"]#', $page['html'] );
		$this->assertMatchesRegularExpression( '#<option value="senior"\s+selected=[\'"]selected[\'"]#', $page['html'] );
		$this->assertSame( 1, preg_match( '#<input[^>]*id="dliPeopleSearch"[^>]*>#', $page['html'], $input ) );
		$this->assertStringContainsString( 'value="Rossi Mario"', $input[0] );
	}

	/** The requested type is selected in the table filter. */
	public function test_table_selects_the_requested_people_type() {
		$category = $this->create_post( PEOPLE_TYPE_POST_TYPE, 'Ricercatore' );
		$page = $this->render_table(
			array(),
			array(),
			array(
				'categories'    => array( get_post( $category ) ),
				'selected_type' => (string) $category,
			)
		);
		$this->assertMatchesRegularExpression( '#<option value="' . $category . '"\s+selected=[\'"]selected[\'"]#', $page['html'] );
	}

	/**
	 * With the column hidden the telephone number is neither read nor sent to the page: not in the HTML and not in the
	 * data of the script, where it would be there for anyone who reads the source.
	 */
	public function test_a_hidden_phone_is_not_in_the_page_at_all() {
		$person = $this->create_person( 'Telefono', array( 'telefono' => '+39 050 8888 123' ) );

		$page = $this->render_table( array( $person ), array( 'hide_people_table_phone' => 'true' ) );

		$this->assertStringNotContainsString( '8888', $page['html'], 'The number is nowhere in the page.' );
		$this->assertDoesNotMatchRegularExpression( '#tel:[+\d]#', $page['html'], 'No link to call a number is in the page: the script only knows how to build one.' );
		$this->assertFalse( $page['data']['cols']['phone'] );
		$this->assertSame( '', $page['data']['rows'][0]['phone'] );
		$this->assertSame( '', $page['data']['rows'][0]['phoneHref'] );
	}

	/**
	 * With the column shown the number is there, and the link to call it has only the digits and the plus sign.
	 */
	public function test_a_visible_phone_is_in_the_data_and_its_link_is_safe() {
		$person = $this->create_person( 'Telefono', array( 'telefono' => '+39 (050) 8888-123 <b>' ) );

		$page = $this->render_table( array( $person ) );

		$this->assertTrue( $page['data']['cols']['phone'] );
		$this->assertStringContainsString( '8888', $page['html'] );
		$this->assertSame( '+390508888123', $page['data']['rows'][0]['phoneHref'] );
	}

	/**
	 * The e-mail is not in the page when its column is hidden, and is obfuscated when it is shown.
	 */
	public function test_a_hidden_email_is_not_in_the_page() {
		$person = $this->create_person( 'Posta', array( 'email' => 'ada.posta@example.test' ) );

		$hidden = $this->render_table( array( $person ), array( 'hide_people_table_email' => 'true' ) );
		$this->assertStringNotContainsString( 'example.test', $hidden['html'] );
		$this->assertSame( '', $hidden['data']['rows'][0]['email'] );

		$shown = $this->render_table( array( $person ) );
		$this->assertStringNotContainsString( 'ada.posta@example.test', $shown['html'], 'The address is obfuscated, not written as it is.' );
		$this->assertNotSame( '', $shown['data']['rows'][0]['email'] );
	}

	/**
	 * The address of a person that leads to a site is checked: a legitimate one is kept and opens in a new window,
	 * one with a scheme that runs code is never a link.
	 */
	public function test_the_site_of_a_person_is_a_link_only_with_an_allowed_scheme() {
		$legit = $this->create_person(
			'Legittimo',
			array(
				'abilita_link_diretto_pagina_persona' => 1,
				'sito_web'                            => 'https://example.org/ada',
			)
		);
		$data  = $this->render_table( array( $legit ) )['data']['rows'][0];
		$this->assertSame( 'https://example.org/ada', $data['link'] );
		$this->assertSame( '_blank', $data['linkTarget'] );

		$bare = $this->create_person(
			'Senza schema',
			array(
				'abilita_link_diretto_pagina_persona' => 1,
				'sito_web'                            => 'www.example.org/ada',
			)
		);
		$row  = $this->render_table( array( $bare ) )['data']['rows'][0];
		$this->assertSame( 'http://www.example.org/ada', $row['link'], 'An address written without a scheme is a link, as in the cards view.' );
		$this->assertSame( '_blank', $row['linkTarget'] );

		foreach ( array( 'javascript:alert(1)', 'JaVaScRiPt:alert(1)', ' javascript:alert(1)', "java\tscript:alert(1)", 'data:text/html,<script>alert(1)</script>', 'vbscript:msgbox(1)' ) as $unsafe ) {
			$person = $this->create_person(
				'Pericoloso',
				array(
					'abilita_link_diretto_pagina_persona' => 1,
					'sito_web'                            => $unsafe,
				)
			);
			$row    = $this->render_table( array( $person ) )['data']['rows'][0];

			$this->assertDoesNotMatchRegularExpression( '#^\s*(javascript|data|vbscript):#i', $row['link'], 'Not a link: ' . $unsafe );
			$this->assertSame( (string) get_permalink( $person ), $row['link'], 'The link falls back to the page of the person: ' . $unsafe );
			$this->assertSame( '', $row['linkTarget'] );
		}
	}

	/**
	 * A person with the page disabled has no link, whatever the site says.
	 */
	public function test_a_person_with_the_page_disabled_has_no_link() {
		$person = $this->create_person(
			'Senza scheda',
			array(
				'disattiva_pagina_dettaglio'          => 1,
				'abilita_link_diretto_pagina_persona' => 1,
				'sito_web'                            => 'https://example.org/ada',
			)
		);

		$this->assertSame( '', $this->render_table( array( $person ) )['data']['rows'][0]['link'] );
	}
}
