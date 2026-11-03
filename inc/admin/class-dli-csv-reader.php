<?php
/**
 * Reader of the CSV files of the import tools.
 *
 * Reads the text of a file and gives back the header and the rows, with the line of the file where each row
 * starts and the errors of the file. It does not write anything and does not depend on WordPress, so that it can be
 * tested alone. The upload (origin of the file, extension, size on the disk, MIME) is checked by the tool that
 * receives it; the messages for the operator are written by the tool, from the codes of the errors.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Reads a CSV text: BOM, UTF-8, separator (`;` or `,`), quoting, new lines inside a cell, empty and incomplete rows.
 */
class DLI_CSV_Reader {

	/**
	 * The most bytes of a file.
	 */
	const MAX_BYTES = 2097152;

	/**
	 * Read a file from its path.
	 *
	 * @param string               $path    Path of the file.
	 * @param array<string, mixed> $options See `read_string()`.
	 * @return array{delimiter: string, headers: string[], rows: array<int, array{line: int, cells: array<string, string>}>, errors: array<int, array{code: string, line: int, detail: string}>} The result.
	 */
	public static function read( $path, array $options = array() ) {
		$max_bytes = isset( $options['max_bytes'] ) ? (int) $options['max_bytes'] : self::MAX_BYTES;
		if ( ! is_readable( $path ) || ! is_file( $path ) ) {
			return self::failure( 'unreadable' );
		}
		if ( filesize( $path ) > $max_bytes ) {
			return self::failure( 'too_large' );
		}

		$content = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- A local file chosen by the operator, not a URL.
		if ( false === $content ) {
			return self::failure( 'unreadable' );
		}

		return self::read_string( $content, $options );
	}

	/**
	 * Read a CSV text.
	 *
	 * Options:
	 * - `required`: names of the columns that the header must have.
	 * - `optional`: names of the columns that the header may have. When `required` or `optional` are given, any
	 *   other column is an error; the names are case sensitive.
	 * - `max_bytes`: the most bytes of the text (default `MAX_BYTES`).
	 * - `max_rows`: the most rows with at least a value; 0 for no limit.
	 *
	 * The codes of the errors are: `unreadable`, `too_large`, `nul_byte`, `invalid_utf8`, `empty`, `malformed`,
	 * `missing_header`, `duplicate_header`, `unknown_header` (the name is in `detail`), `too_many_rows` and
	 * `columns_count` (a row with more or fewer cells than the header, on `line`). All of them but the last refuse the
	 * whole file: no row is given back. The cells are trimmed and the rows without any value are left out.
	 *
	 * @param string               $content Text of the file.
	 * @param array<string, mixed> $options Options.
	 * @return array{delimiter: string, headers: string[], rows: array<int, array{line: int, cells: array<string, string>}>, errors: array<int, array{code: string, line: int, detail: string}>} The result.
	 */
	public static function read_string( $content, array $options = array() ) {
		$options = array_merge(
			array(
				'required'  => array(),
				'optional'  => array(),
				'max_bytes' => self::MAX_BYTES,
				'max_rows'  => 0,
			),
			$options
		);

		if ( strlen( $content ) > (int) $options['max_bytes'] ) {
			return self::failure( 'too_large' );
		}
		if ( 0 === strpos( $content, "\xEF\xBB\xBF" ) ) {
			$content = substr( $content, 3 );
		}
		if ( false !== strpos( $content, "\0" ) ) {
			return self::failure( 'nul_byte' );
		}
		if ( 1 !== preg_match( '//u', $content ) ) {
			return self::failure( 'invalid_utf8' );
		}
		if ( '' === trim( $content ) ) {
			return self::failure( 'empty' );
		}

		$delimiter = self::delimiter_of( $content );
		$parsed    = self::parse( $content, $delimiter );
		if ( null !== $parsed['unclosed_quote_line'] ) {
			return self::failure( 'malformed', $parsed['unclosed_quote_line'] );
		}

		$records = $parsed['records'];
		$headers = array_map( 'trim', $records[0]['cells'] );
		$result  = array(
			'delimiter' => $delimiter,
			'headers'   => $headers,
			'rows'      => array(),
			'errors'    => self::header_errors( $headers, (array) $options['required'], (array) $options['optional'] ),
		);
		if ( ! empty( $result['errors'] ) ) {
			return $result;
		}

		$header_count = count( $headers );
		$counted      = 0;
		foreach ( array_slice( $records, 1 ) as $record ) {
			$cells = array_map( 'trim', $record['cells'] );
			if ( '' === implode( '', $cells ) ) {
				continue;
			}
			++$counted;
			if ( count( $cells ) !== $header_count ) {
				$result['errors'][] = self::error( 'columns_count', $record['line'] );
				continue;
			}
			$result['rows'][] = array(
				'line'  => $record['line'],
				'cells' => array_combine( $headers, $cells ),
			);
		}

		if ( $options['max_rows'] > 0 && $counted > (int) $options['max_rows'] ) {
			return self::failure( 'too_many_rows' );
		}

		return $result;
	}

	/**
	 * The separator: the comma when the first line has more of them than semicolons (outside quotes), else the semicolon.
	 *
	 * @param string $content Text.
	 * @return string `;` or `,`.
	 */
	private static function delimiter_of( $content ) {
		$semicolons = 0;
		$commas     = 0;
		$in_quotes  = false;
		$length     = strlen( $content );

		for ( $i = 0; $i < $length; $i++ ) {
			$char = $content[ $i ];
			if ( '"' === $char ) {
				$in_quotes = ! $in_quotes;
			} elseif ( ! $in_quotes && ( "\n" === $char || "\r" === $char ) ) {
				break;
			} elseif ( ! $in_quotes && ';' === $char ) {
				++$semicolons;
			} elseif ( ! $in_quotes && ',' === $char ) {
				++$commas;
			}
		}

		return $commas > $semicolons ? ',' : ';';
	}

	/**
	 * Split the text in records and cells, keeping the line of the file where each record starts.
	 *
	 * A quote that opens a cell starts a quoted cell, where the separator and the new lines are text and a doubled quote
	 * is a quote; a quote in the middle of a cell is a character like the others.
	 *
	 * @param string $content   Text, without BOM.
	 * @param string $delimiter Separator.
	 * @return array{records: array<int, array{line: int, cells: string[]}>, unclosed_quote_line: int|null} The records, and the line where a quote was opened and never closed.
	 */
	private static function parse( $content, $delimiter ) {
		$records    = array();
		$cells      = array();
		$cell       = '';
		$in_quotes  = false;
		$quote_line = 0;
		$line       = 1;
		$start      = 1;
		$length     = strlen( $content );

		for ( $i = 0; $i < $length; $i++ ) {
			$char = $content[ $i ];

			if ( $in_quotes ) {
				if ( '"' === $char ) {
					if ( $i + 1 < $length && '"' === $content[ $i + 1 ] ) {
						$cell .= '"';
						++$i;
					} else {
						$in_quotes = false;
					}
					continue;
				}
				if ( "\r" === $char && $i + 1 < $length && "\n" === $content[ $i + 1 ] ) {
					$cell .= "\r\n";
					++$i;
					++$line;
					continue;
				}
				if ( "\n" === $char || "\r" === $char ) {
					++$line;
				}
				$cell .= $char;
				continue;
			}

			if ( '"' === $char && '' === $cell ) {
				$in_quotes  = true;
				$quote_line = $line;
			} elseif ( $delimiter === $char ) {
				$cells[] = $cell;
				$cell    = '';
			} elseif ( "\n" === $char || "\r" === $char ) {
				if ( "\r" === $char && $i + 1 < $length && "\n" === $content[ $i + 1 ] ) {
					++$i;
				}
				$cells[]   = $cell;
				$records[] = array(
					'line'  => $start,
					'cells' => $cells,
				);
				$cells     = array();
				$cell      = '';
				++$line;
				$start = $line;
			} else {
				$cell .= $char;
			}
		}

		if ( $in_quotes ) {
			return array(
				'records'             => array(),
				'unclosed_quote_line' => $quote_line,
			);
		}
		if ( '' !== $cell || ! empty( $cells ) ) {
			$cells[]   = $cell;
			$records[] = array(
				'line'  => $start,
				'cells' => $cells,
			);
		}

		return array(
			'records'             => $records,
			'unclosed_quote_line' => null,
		);
	}

	/**
	 * Errors of the header: repeated, missing and unknown columns.
	 *
	 * @param string[] $headers  Names in the file.
	 * @param string[] $required Names that must be there.
	 * @param string[] $optional Names that may be there.
	 * @return array<int, array{code: string, line: int, detail: string}> The errors.
	 */
	private static function header_errors( array $headers, array $required, array $optional ) {
		$errors = array();

		foreach ( array_count_values( $headers ) as $name => $count ) {
			if ( $count > 1 ) {
				$errors[] = self::error( 'duplicate_header', 1, (string) $name );
			}
		}
		foreach ( $required as $name ) {
			if ( ! in_array( $name, $headers, true ) ) {
				$errors[] = self::error( 'missing_header', 1, $name );
			}
		}
		if ( ! empty( $required ) || ! empty( $optional ) ) {
			foreach ( array_unique( $headers ) as $name ) {
				if ( ! in_array( $name, $required, true ) && ! in_array( $name, $optional, true ) ) {
					$errors[] = self::error( 'unknown_header', 1, $name );
				}
			}
		}

		return $errors;
	}

	/**
	 * An error.
	 *
	 * @param string $code   Code.
	 * @param int    $line   Line of the file.
	 * @param string $detail Name of the column, when the error is about one.
	 * @return array{code: string, line: int, detail: string} The error.
	 */
	private static function error( $code, $line = 0, $detail = '' ) {
		return array(
			'code'   => $code,
			'line'   => $line,
			'detail' => $detail,
		);
	}

	/**
	 * The result of a file that is refused whole.
	 *
	 * @param string $code Code of the error.
	 * @param int    $line Line of the file.
	 * @return array{delimiter: string, headers: string[], rows: array<int, array{line: int, cells: array<string, string>}>, errors: array<int, array{code: string, line: int, detail: string}>} The result.
	 */
	private static function failure( $code, $line = 0 ) {
		return array(
			'delimiter' => ';',
			'headers'   => array(),
			'rows'      => array(),
			'errors'    => array( self::error( $code, $line ) ),
		);
	}
}
