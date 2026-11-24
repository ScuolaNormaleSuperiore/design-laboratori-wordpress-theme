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
 * Reads a CSV text: BOM, UTF-8, the `;` separator, quoting, new lines inside a cell, empty and incomplete rows, and
 * the columns of a schema.
 *
 * @phpstan-type DLI_CSV_Result array{headers: string[], ignored: string[], rows: array<int, array{line: int, cells: array<string, string>}>, errors: array<int, array{code: string, line: int, detail: string}>}
 */
class DLI_CSV_Reader {

	/**
	 * The most bytes of a file.
	 */
	const MAX_BYTES = 2097152;

	/**
	 * The most rows of a file with a value in the columns that are read.
	 */
	const MAX_ROWS = 200;

	/**
	 * The separator of the cells.
	 */
	const DELIMITER = ';';

	/**
	 * Read a file from its path.
	 *
	 * @param string               $path    Path of the file.
	 * @param array<string, mixed> $options See `read_string()`.
	 * @return DLI_CSV_Result The result.
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
	 * - `optional`: names of the columns that the header may have.
	 * - `max_bytes`: the most bytes of the text (default `MAX_BYTES`).
	 *
	 * The names of the columns are compared in lower case and without the spaces around them; the cells of a row are
	 * given back by the name in lower case. `required` and `optional` are the schema: a column that is not in it is
	 * ignored, also with values, and its name, as written, is listed in `ignored`. Without a schema every column is read.
	 *
	 * The cells are trimmed. A row is left out when the columns that are read are empty in it (values in ignored
	 * columns or after the last column of the header do not count); the others count for `MAX_ROWS`. A row that is
	 * too short to reach the last column that is read is an error of its line; the cells after the last column of the
	 * header are left out. Lines are those of the file, also after a cell on more lines.
	 *
	 * The codes of the errors are: `unreadable`, `too_large`, `nul_byte`, `invalid_utf8`, `empty`, `malformed`,
	 * `missing_header` and `duplicate_header` (the name is in `detail`), `empty_header` (only without a schema; the
	 * position of the column is in `detail`), `too_many_rows` and `columns_count` (a row too short, on `line`). All of
	 * them but the last refuse the whole file: no row is given back.
	 *
	 * @param string               $content Text of the file.
	 * @param array<string, mixed> $options Options.
	 * @return DLI_CSV_Result The result.
	 */
	public static function read_string( $content, array $options = array() ) {
		$options = array_merge(
			array(
				'required'  => array(),
				'optional'  => array(),
				'max_bytes' => self::MAX_BYTES,
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

		$parsed = self::parse( $content, self::DELIMITER );
		if ( null !== $parsed['unclosed_quote_line'] ) {
			return self::failure( 'malformed', $parsed['unclosed_quote_line'] );
		}

		$records = $parsed['records'];
		$columns = self::columns( $records[0]['cells'], (array) $options['required'], (array) $options['optional'] );
		$result  = array(
			'headers' => array_values( $columns['names'] ),
			'ignored' => $columns['ignored'],
			'rows'    => array(),
			'errors'  => $columns['errors'],
		);
		if ( ! empty( $result['errors'] ) ) {
			return $result;
		}

		$last    = empty( $columns['names'] ) ? -1 : max( array_keys( $columns['names'] ) );
		$counted = 0;
		foreach ( array_slice( $records, 1 ) as $record ) {
			$cells = array();
			foreach ( $columns['names'] as $position => $name ) {
				$cells[ $name ] = isset( $record['cells'][ $position ] ) ? trim( $record['cells'][ $position ] ) : '';
			}
			if ( '' === implode( '', $cells ) ) {
				continue;
			}
			++$counted;
			if ( count( $record['cells'] ) <= $last ) {
				$result['errors'][] = self::error( 'columns_count', $record['line'] );
				continue;
			}
			$result['rows'][] = array(
				'line'  => $record['line'],
				'cells' => $cells,
			);
		}

		if ( $counted > self::MAX_ROWS ) {
			return self::failure( 'too_many_rows' );
		}

		return $result;
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
	 * The columns that are read, by their position in the header, the columns that are ignored and the errors.
	 *
	 * With a schema (`required` or `optional` not empty) only its columns are read: the others are ignored and listed
	 * once, as written (an empty name is not listed); a column of the schema that is there twice and a required column
	 * that is missing are errors. Without a schema every column is read, and an empty or repeated name is an error.
	 *
	 * @param string[] $header   Cells of the first line of the file.
	 * @param string[] $required Names that must be there.
	 * @param string[] $optional Names that may be there.
	 * @return array{names: array<int, string>, ignored: string[], errors: array<int, array{code: string, line: int, detail: string}>} The columns.
	 */
	private static function columns( array $header, array $required, array $optional ) {
		$schema  = array_map( array( __CLASS__, 'normalize' ), array_merge( $required, $optional ) );
		$columns = array(
			'names'   => array(),
			'ignored' => array(),
			'errors'  => array(),
		);
		$seen    = array();

		foreach ( $header as $position => $written ) {
			$written = trim( $written );
			$name    = self::normalize( $written );

			if ( ! empty( $schema ) && ! in_array( $name, $schema, true ) ) {
				if ( '' !== $written && ! isset( $seen[ $name ] ) ) {
					$columns['ignored'][] = $written;
				}
				$seen[ $name ] = true;
				continue;
			}
			if ( '' === $name ) {
				$columns['errors'][] = self::error( 'empty_header', 1, (string) ( $position + 1 ) );
				continue;
			}
			if ( isset( $seen[ $name ] ) ) {
				$columns['errors'][] = self::error( 'duplicate_header', 1, $name );
				continue;
			}
			$seen[ $name ]                 = true;
			$columns['names'][ $position ] = $name;
		}

		foreach ( array_map( array( __CLASS__, 'normalize' ), $required ) as $name ) {
			if ( ! in_array( $name, $columns['names'], true ) ) {
				$columns['errors'][] = self::error( 'missing_header', 1, $name );
			}
		}

		return $columns;
	}

	/**
	 * The name of a column as it is compared: without the spaces around and in lower case.
	 *
	 * @param string $name Name of the column.
	 * @return string The name.
	 */
	private static function normalize( $name ) {
		return strtolower( trim( $name ) );
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
	 * @return DLI_CSV_Result The result.
	 */
	private static function failure( $code, $line = 0 ) {
		return array(
			'headers' => array(),
			'ignored' => array(),
			'rows'    => array(),
			'errors'  => array( self::error( $code, $line ) ),
		);
	}
}
