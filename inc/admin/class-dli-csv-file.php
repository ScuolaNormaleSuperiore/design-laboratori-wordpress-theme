<?php
/**
 * The uploaded CSV file of the import tools: the check of the upload and the messages shared by the imports.
 *
 * The reading of the file is done by `DLI_CSV_Reader`; each import has its own messages for its own codes and asks
 * this class for the others.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Check of the uploaded file and messages of the codes of the upload and of the reader.
 */
class DLI_CSV_File {

	/**
	 * The types of file that a CSV can have.
	 */
	const MIME_TYPES = array( 'text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel' );

	/**
	 * Check the file that was sent: sent, from an upload, a CSV, of the size allowed and of a type of text.
	 *
	 * @param array<string, mixed> $file           The entry of `$_FILES`.
	 * @param bool                 $require_upload Whether the file must come from an upload through HTTP; only the tests turn it off.
	 * @return string The code of what is wrong (`no_file`, `not_uploaded`, `extension`, `too_large`, `mime`), empty when the file is fine.
	 */
	public static function check( array $file, $require_upload = true ) {
		if ( empty( $file['tmp_name'] ) || ! isset( $file['error'], $file['name'], $file['size'] ) || UPLOAD_ERR_OK !== (int) $file['error'] ) {
			return 'no_file';
		}
		if ( $require_upload && ! is_uploaded_file( (string) $file['tmp_name'] ) ) {
			return 'not_uploaded';
		}
		if ( 'csv' !== strtolower( pathinfo( sanitize_file_name( (string) $file['name'] ), PATHINFO_EXTENSION ) ) ) {
			return 'extension';
		}
		if ( (int) $file['size'] > DLI_CSV_Reader::MAX_BYTES ) {
			return 'too_large';
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime = mime_content_type( (string) $file['tmp_name'] );
			if ( $mime && ! in_array( $mime, self::MIME_TYPES, true ) ) {
				return 'mime';
			}
		}
		return '';
	}

	/**
	 * The message for the operator of a code of the upload or of the reader.
	 *
	 * @param string $code   Code of the error.
	 * @param int    $line   Line of the file, 0 when the error is not about a line.
	 * @param string $detail Detail of the error: the name or the position of a column.
	 * @return string The message, empty when the code is not one of the upload or of the reader.
	 */
	public static function message( $code, $line = 0, $detail = '' ) {
		switch ( $code ) {
			case 'no_file':
				return __( 'Nessun file caricato o errore nel caricamento.', 'design_laboratori_italia' );
			case 'not_uploaded':
				return __( 'File non valido.', 'design_laboratori_italia' );
			case 'extension':
				return __( 'Formato non valido: è richiesto un file .csv.', 'design_laboratori_italia' );
			case 'too_large':
				return __( 'File troppo grande. Dimensione massima: 2 MB.', 'design_laboratori_italia' );
			case 'mime':
				return __( 'Tipo di file non valido: è richiesto un file CSV.', 'design_laboratori_italia' );
			case 'unreadable':
				return __( 'Impossibile leggere il file caricato.', 'design_laboratori_italia' );
			case 'nul_byte':
				return __( 'Il file contiene caratteri non validi.', 'design_laboratori_italia' );
			case 'invalid_utf8':
				return __( 'Il file non è in UTF-8: salvalo come «CSV UTF-8».', 'design_laboratori_italia' );
			case 'empty':
				return __( 'File vuoto o intestazione non leggibile.', 'design_laboratori_italia' );
			case 'malformed':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: virgolette aperte e mai chiuse.', 'design_laboratori_italia' ), $line );
			case 'missing_header':
				/* translators: %s: name of the column */
				return sprintf( __( 'Intestazione non valida: colonna obbligatoria "%s" non trovata.', 'design_laboratori_italia' ), $detail );
			case 'duplicate_header':
				/* translators: %s: name of the column */
				return sprintf( __( 'Intestazione non valida: la colonna "%s" è ripetuta.', 'design_laboratori_italia' ), $detail );
			case 'empty_header':
				/* translators: %s: position of the column, from 1 */
				return sprintf( __( 'Intestazione non valida: la colonna %s non ha un nome.', 'design_laboratori_italia' ), $detail );
			case 'too_many_rows':
				/* translators: %d: the most rows of a file */
				return sprintf( __( 'Il file ha troppe righe: il massimo è %d.', 'design_laboratori_italia' ), DLI_CSV_Reader::MAX_ROWS );
			case 'columns_count':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: la riga ha meno colonne di quelle previste dall\'intestazione.', 'design_laboratori_italia' ), $line );
		}
		return '';
	}

	/**
	 * The message that lists the columns of the file that the import ignored.
	 *
	 * @param string[] $names Names of the columns, as written in the file.
	 * @return string The message.
	 */
	public static function ignored_message( array $names ) {
		/* translators: %s: names of the columns, separated by commas */
		return sprintf( __( 'Colonne ignorate: %s.', 'design_laboratori_italia' ), implode( ', ', $names ) );
	}
}
