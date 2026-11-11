<?php
/**
 * The ACF exports in `SETUP/ACF_Custom_Fields/` describe the same field groups that the theme registers in PHP.
 *
 * The exports are used to import the fields again into ACF, so a difference from the PHP would create fields that the
 * theme does not use. When a field group changes in PHP, export it again and update the file.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Comparison of every export with the field groups registered by the managers of the theme.
 */
class DLI_Acf_Exports_Integration_Test extends DLI_Integration_Test_Case {

	/**
	 * Properties of an export that do not describe the field.
	 *
	 * @var string[]
	 */
	const IGNORED_PROPERTIES = array( 'ID', 'parent', 'menu_order', 'prefix', 'value', '_name', '_valid', 'id', 'class', 'sub_fields', 'modified', 'local', 'active', '_prepare', '_clone' );

	/**
	 * Properties of a field group that are compared.
	 *
	 * @var string[]
	 */
	const GROUP_PROPERTIES = array( 'title', 'location', 'position', 'style', 'label_placement', 'instruction_placement', 'hide_on_screen' );

	/**
	 * The export files of the theme.
	 *
	 * @return string[] Paths.
	 */
	private function export_files() {
		$files = glob( DLI_TEST_THEME_DIR . '/SETUP/ACF_Custom_Fields/*.json' );
		$this->assertNotEmpty( $files, 'No ACF export found.' );
		return $files;
	}

	/**
	 * The field groups of an export file.
	 *
	 * @param string $file Path of the file.
	 * @return array<int, array<string, mixed>>
	 */
	private function groups_of( $file ) {
		$groups = json_decode( (string) file_get_contents( $file ), true );
		$this->assertIsArray( $groups, basename( $file ) . ' is not valid JSON.' );
		return $groups;
	}

	/**
	 * The fields of a group with their sub fields, by key.
	 *
	 * @param array<int, array<string, mixed>> $fields Fields.
	 * @param array<string, array>             $out    Fields found so far.
	 * @return array<string, array>
	 */
	private function flatten( $fields, $out = array() ) {
		foreach ( $fields as $field ) {
			if ( empty( $field['key'] ) ) {
				continue;
			}
			$out[ $field['key'] ] = $field;
			if ( ! empty( $field['sub_fields'] ) ) {
				$out = $this->flatten( $field['sub_fields'], $out );
			}
		}
		return $out;
	}

	/**
	 * A value in a form that does not tell "0", 0 and false apart, which ACF mixes.
	 *
	 * @param mixed $value Value.
	 * @return mixed
	 */
	private function normalise( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'normalise' ), $value );
		}
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}
		return is_numeric( $value ) ? (string) $value : $value;
	}

	/**
	 * A value as short text, for the message of a difference.
	 *
	 * @param mixed $value Value, or null when the field has no such property.
	 * @return string
	 */
	private function show( $value ) {
		if ( null === $value ) {
			return '(missing)';
		}
		$text = is_scalar( $value ) ? (string) $value : (string) wp_json_encode( $value );
		return mb_substr( $text, 0, 60 );
	}

	/**
	 * The differences between the groups of an export and the groups registered in PHP.
	 *
	 * @param string                           $name   Name of the export, for the messages.
	 * @param array<int, array<string, mixed>> $groups The groups of the export.
	 * @return string[] One message for each difference.
	 */
	private function differences( $name, $groups ) {
		$differences = array();
		foreach ( $groups as $export ) {
			$key = $export['key'];
			$php = acf_get_field_group( $key );
			if ( ! $php ) {
				$differences[] = "$name: the group $key is not registered by the theme.";
				continue;
			}
			foreach ( self::GROUP_PROPERTIES as $property ) {
				if ( isset( $export[ $property ] ) && $this->normalise( $export[ $property ] ) !== $this->normalise( $php[ $property ] ?? null ) ) {
					$differences[] = "$name: group $key, $property differs.";
				}
			}

			$exported   = $this->flatten( $export['fields'] ?? array() );
			$registered = $this->flatten( acf_get_fields( $key ) );
			foreach ( array_diff_key( $registered, $exported ) as $field_key => $field ) {
				$differences[] = "$name: the field {$field['name']} ($field_key) is in PHP and not in the export.";
			}
			foreach ( array_diff_key( $exported, $registered ) as $field_key => $field ) {
				$differences[] = "$name: the field {$field['name']} ($field_key) is in the export and not in PHP.";
			}
			foreach ( array_intersect_key( $exported, $registered ) as $field_key => $field ) {
				foreach ( $field as $property => $value ) {
					if ( in_array( $property, self::IGNORED_PROPERTIES, true ) ) {
						continue;
					}
					$registered_value = $registered[ $field_key ][ $property ] ?? null;
					if ( $this->normalise( $value ) !== $this->normalise( $registered_value ) ) {
						$differences[] = "$name: the field {$field['name']} ($field_key), $property: export " . $this->show( $value ) . ' / PHP ' . $this->show( $registered_value ) . '.';
					}
				}
			}
		}
		return $differences;
	}

	/**
	 * Every export describes the groups that the theme registers, field by field.
	 */
	public function test_every_export_matches_the_registered_groups() {
		foreach ( $this->export_files() as $file ) {
			$this->assertSame(
				array(),
				$this->differences( basename( $file ), $this->groups_of( $file ) ),
				basename( $file ) . ' differs from the PHP: export the group again from ACF, or update the file by hand.'
			);
		}
	}

	/**
	 * Every field group that the theme registers is in an export.
	 */
	public function test_every_registered_group_has_an_export() {
		$exported = array();
		foreach ( $this->export_files() as $file ) {
			$exported = array_merge( $exported, wp_list_pluck( $this->groups_of( $file ), 'key' ) );
		}

		$missing = array();
		foreach ( acf_get_field_groups() as $group ) {
			if ( ! in_array( $group['key'], $exported, true ) ) {
				$missing[] = $group['key'] . ' (' . $group['title'] . ')';
			}
		}

		$this->assertSame( array(), $missing, 'Field groups registered by the theme without an export in SETUP/ACF_Custom_Fields.' );
	}

	/**
	 * Control: the comparison sees a changed property, a missing field and an extra field, and a group that is not in PHP.
	 */
	public function test_the_comparison_detects_the_differences() {
		$groups = $this->groups_of( DLI_TEST_THEME_DIR . '/SETUP/ACF_Custom_Fields/acf-export-banner.json' );
		$this->assertSame( array(), $this->differences( 'banner', $groups ), 'The banner export must match before it is altered.' );

		// A changed property.
		$changed                         = $groups;
		$changed[0]['fields'][0]['type'] = 'textarea';
		$this->assertCount( 1, $this->differences( 'banner', $changed ), 'A changed type is one difference.' );

		// A field that the export does not have.
		$without_field = $groups;
		$removed       = array_shift( $without_field[0]['fields'] );
		$differences   = $this->differences( 'banner', $without_field );
		$this->assertCount( 1, $differences );
		$this->assertStringContainsString( $removed['key'], $differences[0] );
		$this->assertStringContainsString( 'in PHP and not in the export', $differences[0] );

		// A field that PHP does not have.
		$extra                = $groups;
		$extra[0]['fields'][] = array(
			'key'  => 'field_does_not_exist',
			'name' => 'does_not_exist',
			'type' => 'text',
		);
		$differences          = $this->differences( 'banner', $extra );
		$this->assertCount( 1, $differences );
		$this->assertStringContainsString( 'in the export and not in PHP', $differences[0] );

		// A group that PHP does not register.
		$unknown           = $groups;
		$unknown[0]['key'] = 'group_does_not_exist';
		$differences       = $this->differences( 'banner', $unknown );
		$this->assertCount( 1, $differences );
		$this->assertStringContainsString( 'is not registered by the theme', $differences[0] );
	}
}
