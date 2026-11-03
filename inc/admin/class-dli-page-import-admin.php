<?php
/**
 * Strumenti DLI → Importa → Pagine: the form, the request, the report and the downloads of the examples.
 *
 * Like the import of the people, the page handles its own form: the file is sent again at each step and nothing is kept
 * on the server between the simulation and the import. The reading of the file is done by `DLI_CSV_Reader`, the pages
 * by `DLI_Page_File_Importer`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The tool that imports pages from a CSV file.
 */
class DLI_Page_Import_Admin {

	/**
	 * Action of the nonce of the form.
	 */
	const NONCE_ACTION = 'dli_import_pagine';

	/**
	 * Name of the field of the nonce of the form.
	 */
	const NONCE_FIELD = 'dli_import_pagine_nonce';

	/**
	 * Action of the nonce of the downloads.
	 */
	const DOWNLOAD_NONCE = 'dli_download_pagine';

	/**
	 * Name of the field of the file.
	 */
	const FIELD_FILE = 'dli_pages_csv';

	/**
	 * The files that can be downloaded, by the name in the request, in `SETUP/CSV_Import/`.
	 */
	const DOWNLOADS = array(
		'esempio-it' => 'pages-example-it.csv',
		'esempio-en' => 'pages-example-en.csv',
		'modello'    => 'pages-model.csv',
	);

	/**
	 * The path of a file that can be downloaded.
	 *
	 * @param mixed $name Requested name; only the strings `esempio-it`, `esempio-en` or `modello` are accepted.
	 * @return string The path, empty when the name is not one of the three.
	 */
	public static function download_path( $name ) {
		if ( ! is_string( $name ) || ! isset( self::DOWNLOADS[ $name ] ) ) {
			return '';
		}
		return dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/' . self::DOWNLOADS[ $name ];
	}

	/**
	 * Send an example file, if the request is the one of a download. Called before any output.
	 *
	 * @return void
	 */
	public static function handle_download() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		$name   = isset( $_GET['file'] ) ? sanitize_text_field( wp_unslash( $_GET['file'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( 'download-pagine' !== $action ) {
			return;
		}
		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) );
		}
		check_admin_referer( self::DOWNLOAD_NONCE );

		$path = self::download_path( $name );
		if ( '' === $path || ! is_readable( $path ) ) {
			wp_die( esc_html__( 'File non trovato.', 'design_laboratori_italia' ), '', array( 'response' => 404 ) );
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . basename( $path ) . '"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- A file of the theme, chosen from a fixed list.
		exit;
	}

	/**
	 * Process the form: check who sends it, the file and the choices, then simulate or import.
	 *
	 * The report has: `exec` (`dry_run` or `commit`: anything that is not exactly `commit` is a simulation), `language`,
	 * `lorem`, `filename`, `error` (a code for what is wrong with the request: `forbidden`, `nonce`, `no_file`,
	 * `not_uploaded`, `extension`, `too_large`, `mime` or `not_confirmed`, empty when the request is fine), `errors` (the
	 * errors of the file and of the plan, with their lines: they stop everything), `rows` and `counts` (the plan, or what
	 * was done), `applied` (whether the import was run) and `written` (whether any page was created).
	 *
	 * The real import is run only with the confirmation of the publication. The user needs `DLI_EDIT_CONFIG_PERMISSION`
	 * and `publish_pages`.
	 *
	 * @param array<string, mixed> $post             The values of the form, without slashes.
	 * @param array<string, mixed> $files            The uploaded files, as in `$_FILES`.
	 * @param bool                 $require_upload   Whether the file must come from an upload through HTTP; only the tests turn it off.
	 * @return array<string, mixed> The report.
	 */
	public static function process_request( array $post, array $files, $require_upload = true ) {
		$report = array(
			'exec'     => 'commit' === ( $post['dli_pages_exec'] ?? '' ) ? 'commit' : 'dry_run',
			'language' => sanitize_key( (string) ( $post['dli_pages_language'] ?? '' ) ),
			'lorem'    => '1' === (string) ( $post['dli_pages_lorem'] ?? '' ),
			'filename' => '',
			'error'    => '',
			'errors'   => array(),
			'rows'     => array(),
			'counts'   => array(
				'created' => 0,
				'skipped' => 0,
				'errors'  => 0,
			),
			'applied'  => false,
			'written'  => false,
		);

		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) || ! current_user_can( 'publish_pages' ) ) {
			$report['error'] = 'forbidden';
			return $report;
		}
		$nonce = isset( $post[ self::NONCE_FIELD ] ) ? sanitize_text_field( (string) $post[ self::NONCE_FIELD ] ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			$report['error'] = 'nonce';
			return $report;
		}

		$file  = isset( $files[ self::FIELD_FILE ] ) && is_array( $files[ self::FIELD_FILE ] ) ? $files[ self::FIELD_FILE ] : array();
		$check = self::check_upload( $file, $require_upload );
		if ( '' !== $check ) {
			$report['error'] = $check;
			return $report;
		}
		$report['filename'] = sanitize_file_name( (string) $file['name'] );

		$read = DLI_CSV_Reader::read(
			(string) $file['tmp_name'],
			array(
				'required' => array( 'level_1', 'level_2', 'level_3', 'menu_order', 'promote_in_home', 'show_subpages_as_cards' ),
				'optional' => array( 'page_key', 'excerpt', 'slug' ),
				'max_rows' => DLI_Page_File_Importer::MAX_PAGES,
			)
		);
		if ( ! empty( $read['errors'] ) ) {
			$report['errors'] = $read['errors'];
			return $report;
		}
		$parsed = DLI_Page_File_Importer::parse_rows( $read['rows'] );
		if ( ! empty( $parsed['errors'] ) ) {
			$report['errors'] = $parsed['errors'];
			return $report;
		}

		$confirmed = '1' === (string) ( $post['dli_pages_confirm'] ?? '' );
		if ( 'commit' === $report['exec'] && $confirmed ) {
			$result            = DLI_Page_File_Importer::import( $parsed['pages'], $report['language'], $report['lorem'], get_current_user_id() );
			$report['applied'] = empty( $result['errors'] ) || $result['written'];
		} else {
			$result = DLI_Page_File_Importer::plan( $parsed['pages'], $report['language'] );
			if ( 'commit' === $report['exec'] ) {
				$report['error'] = 'not_confirmed';
			}
		}

		$report['errors']  = $result['errors'];
		$report['rows']    = $result['rows'];
		$report['counts']  = $result['counts'];
		$report['written'] = ! empty( $result['written'] );

		return $report;
	}

	/**
	 * Check the file that was sent: sent, from an upload, a CSV, of the size allowed and of a type of text.
	 *
	 * @param array<string, mixed> $file           The entry of `$_FILES`.
	 * @param bool                 $require_upload Whether the file must come from an upload through HTTP.
	 * @return string The code of what is wrong, empty when the file is fine.
	 */
	private static function check_upload( array $file, $require_upload ) {
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
			if ( $mime && ! in_array( $mime, array( 'text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel' ), true ) ) {
				return 'mime';
			}
		}
		return '';
	}

	/**
	 * The message for the operator of an error.
	 *
	 * @param string $code   Code of the error.
	 * @param int    $line   Line of the file, 0 when the error is not about a line.
	 * @param string $detail Detail of the error: the column, the key, the title, the ID of a page.
	 * @return string The message.
	 */
	public static function message( $code, $line = 0, $detail = '' ) {
		switch ( $code ) {
			case 'forbidden':
				return __( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' );
			case 'nonce':
				return __( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' );
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
			case 'not_confirmed':
				return __( 'Per importare serve la conferma della pubblicazione: non è stato scritto nulla.', 'design_laboratori_italia' );
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
			case 'unknown_header':
				/* translators: %s: name of the column */
				return sprintf( __( 'Intestazione non valida: colonna sconosciuta "%s".', 'design_laboratori_italia' ), $detail );
			case 'too_many_rows':
				/* translators: %d: the most pages of a file */
				return sprintf( __( 'Il file ha troppe pagine: il massimo è %d.', 'design_laboratori_italia' ), DLI_Page_File_Importer::MAX_PAGES );
			case 'columns_count':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: il numero di colonne non corrisponde all\'intestazione.', 'design_laboratori_italia' ), $line );
			case 'no_title':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: manca il titolo.', 'design_laboratori_italia' ), $line );
			case 'several_titles':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: un solo titolo per riga, in un solo livello.', 'design_laboratori_italia' ), $line );
			case 'level_jump':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: il livello salta quello superiore (manca la pagina genitore).', 'design_laboratori_italia' ), $line );
			case 'plain_text':
				/* translators: 1: line of the file, 2: name of the field */
				return sprintf( __( 'Riga %1$d: il campo «%2$s» deve essere testo semplice, senza i simboli < e >.', 'design_laboratori_italia' ), $line, $detail );
			case 'invalid_order':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: menu_order deve essere un numero intero da 0 in su.', 'design_laboratori_italia' ), $line );
			case 'invalid_flag':
				/* translators: 1: line of the file, 2: name of the column */
				return sprintf( __( 'Riga %1$d: «%2$s» deve valere 0 oppure 1.', 'design_laboratori_italia' ), $line, $detail );
			case 'invalid_key':
				/* translators: 1: line of the file, 2: the key */
				return sprintf( __( 'Riga %1$d: page_key «%2$s» non valida (minuscole, numeri, trattini e underscore, fino a 64 caratteri).', 'design_laboratori_italia' ), $line, $detail );
			case 'duplicate_key':
				/* translators: 1: line of the file, 2: the key */
				return sprintf( __( 'Riga %1$d: page_key «%2$s» ripetuta.', 'design_laboratori_italia' ), $line, $detail );
			case 'invalid_slug':
				/* translators: 1: line of the file, 2: the slug */
				return sprintf( __( 'Riga %1$d: slug «%2$s» non valido (minuscole, numeri e trattini).', 'design_laboratori_italia' ), $line, $detail );
			case 'duplicate_path':
				/* translators: 1: line of the file, 2: title of the page */
				return sprintf( __( 'Riga %1$d: la pagina «%2$s» è ripetuta sotto lo stesso genitore.', 'design_laboratori_italia' ), $line, $detail );
			case 'plugins':
				return __( 'ACF e Polylang devono essere attivi.', 'design_laboratori_italia' );
			case 'language':
				return __( 'La lingua richiesta non esiste in questo sito.', 'design_laboratori_italia' );
			case 'empty_slug':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: dal titolo non si ricava un indirizzo.', 'design_laboratori_italia' ), $line );
			case 'ambiguous':
				/* translators: %d: line of the file */
				return sprintf( __( 'Riga %d: più pagine hanno la stessa identità di importazione: va risolto a mano.', 'design_laboratori_italia' ), $line );
			case 'trashed':
				/* translators: 1: line of the file, 2: ID of the page */
				return sprintf( __( 'Riga %1$d: la pagina era già stata importata ed è nel cestino (ID %2$s): ripristinala o eliminala.', 'design_laboratori_italia' ), $line, $detail );
			case 'path_in_use':
				/* translators: 1: line of the file, 2: ID of the page */
				return sprintf( __( 'Riga %1$d: l\'indirizzo è già usato dalla pagina con ID %2$s.', 'design_laboratori_italia' ), $line, $detail );
			case 'translation_conflict':
				/* translators: 1: line of the file, 2: ID of the page */
				return sprintf( __( 'Riga %1$d: la pagina con ID %2$s, in un\'altra lingua, ha già una traduzione in questa lingua.', 'design_laboratori_italia' ), $line, $detail );
			case 'insert_failed':
				/* translators: 1: line of the file, 2: message of WordPress */
				return sprintf( __( 'Riga %1$d: creazione non riuscita (%2$s). Le pagine già create restano: correggi e invia di nuovo il file per continuare.', 'design_laboratori_italia' ), $line, $detail );
			case 'slug_changed':
				/* translators: 1: line of the file, 2: the address WordPress gave */
				return sprintf( __( 'Riga %1$d: WordPress ha cambiato l\'indirizzo in «%2$s».', 'design_laboratori_italia' ), $line, $detail );
		}
		return $code;
	}

	/**
	 * Render the page of the tool: the explanation, the form, and the report of the request that was just sent.
	 *
	 * @return void
	 */
	public static function render() {
		$report = null;
		if ( isset( $_POST[ self::NONCE_FIELD ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in process_request().
			$report = self::process_request( wp_unslash( $_POST ), $_FILES, true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Verified and sanitized in process_request().
			if ( in_array( $report['error'], array( 'forbidden', 'nonce' ), true ) ) {
				wp_die( esc_html( self::message( $report['error'] ) ) );
			}
		}

		self::render_intro();
		self::render_form( $report );
		if ( null !== $report ) {
			self::render_report( $report );
			?>
			<script>
			( function () {
				var el = document.getElementById( 'dli-report-section' );
				if ( el ) { el.focus(); el.scrollIntoView( { behavior: 'smooth', block: 'start' } ); }
			} )();
			</script>
			<?php
		}
	}

	/**
	 * The link to download an example.
	 *
	 * @param string $name Name of the file in the request.
	 * @return string The URL, with the nonce.
	 */
	private static function download_url( $name ) {
		return wp_nonce_url(
			admin_url( 'admin.php?page=dli-tools-import&action=download-pagine&file=' . $name ),
			self::DOWNLOAD_NONCE
		);
	}

	/**
	 * The box that explains the file and gives the examples.
	 *
	 * @return void
	 */
	private static function render_intro() {
		?>
		<div class="postbox dli-tool-panel dli-tool-panel-intro">
			<div class="inside">
				<h2><?php esc_html_e( 'Come funziona', 'design_laboratori_italia' ); ?></h2>
				<p class="dli-section-lead"><?php esc_html_e( 'Crea le pagine del sito da un file CSV: una pagina per riga, su tre livelli (pagina principale, sottopagina, sottopagina della sottopagina). Il file va salvato in UTF-8, con separatore punto e virgola (;).', 'design_laboratori_italia' ); ?></p>

				<div class="dli-info-list">
					<div class="dli-info-row">
						<h3><?php esc_html_e( 'Intestazione richiesta', 'design_laboratori_italia' ); ?></h3>
						<code class="dli-csv-header">level_1;level_2;level_3;menu_order;promote_in_home;show_subpages_as_cards</code>
						<p><?php esc_html_e( 'Ogni riga ha un solo titolo, in una delle tre colonne dei livelli. menu_order è l\'ordine tra le pagine dello stesso livello; gli altri due campi valgono 1 (sì) o 0 (no).', 'design_laboratori_italia' ); ?></p>
					</div>
					<div class="dli-info-row">
						<h3><?php esc_html_e( 'Colonne facoltative', 'design_laboratori_italia' ); ?></h3>
						<p>
							<code>page_key</code>: <?php esc_html_e( 'chiave che collega le traduzioni (la stessa in due file di lingue diverse); consigliata nei siti con più lingue.', 'design_laboratori_italia' ); ?>
							<code>excerpt</code>: <?php esc_html_e( 'riassunto.', 'design_laboratori_italia' ); ?>
							<code>slug</code>: <?php esc_html_e( 'indirizzo, se diverso da quello del titolo.', 'design_laboratori_italia' ); ?>
						</p>
					</div>
					<div class="dli-info-row">
						<h3><?php esc_html_e( 'Cosa fa', 'design_laboratori_italia' ); ?></h3>
						<p>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %d: the most pages of a file */
									__( 'Le pagine nuove vengono pubblicate subito, nella lingua scelta. Le pagine già importate vengono saltate: puoi inviare di nuovo lo stesso file senza creare duplicati. Se una pagina del sito ha lo stesso indirizzo, non viene creato nulla. Massimo %d pagine per file. Prima di importare esegui sempre una simulazione.', 'design_laboratori_italia' ),
									DLI_Page_File_Importer::MAX_PAGES
								)
							);
							?>
						</p>
					</div>
				</div>

				<p class="dli-section-actions dli-import-downloads">
					<a class="button" href="<?php echo esc_url( self::download_url( 'esempio-it' ) ); ?>"><?php esc_html_e( 'Scarica l\'esempio in italiano (dimostrativo)', 'design_laboratori_italia' ); ?></a>
					<a class="button" href="<?php echo esc_url( self::download_url( 'esempio-en' ) ); ?>"><?php esc_html_e( 'Scarica l\'esempio in inglese (dimostrativo)', 'design_laboratori_italia' ); ?></a>
					<a class="button" href="<?php echo esc_url( self::download_url( 'modello' ) ); ?>"><?php esc_html_e( 'Scarica il modello vuoto', 'design_laboratori_italia' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * The form, with the choices of the request that was just sent.
	 *
	 * @param array<string, mixed>|null $report The report of the request just sent, or null.
	 * @return void
	 */
	private static function render_form( $report ) {
		$slugs     = array_map( 'strval', (array) dli_languages_list( array( 'fields' => 'slug' ) ) );
		$names     = array_map( 'strval', (array) dli_languages_list( array( 'fields' => 'name' ) ) );
		$languages = array_combine( $slugs, array_slice( array_pad( $names, count( $slugs ), '' ), 0, count( $slugs ) ) );
		$language  = null !== $report && '' !== $report['language'] ? $report['language'] : (string) dli_default_language( 'slug' );
		$exec      = null !== $report ? $report['exec'] : 'dry_run';
		$lorem     = null !== $report && $report['lorem'];
		?>
		<div class="postbox dli-tool-panel">
			<div class="inside">
				<h2><?php esc_html_e( 'Esegui importazione', 'design_laboratori_italia' ); ?></h2>
				<form method="post"
						action="<?php echo esc_url( admin_url( 'admin.php?page=dli-tools-import&type=pagine' ) ); ?>"
						enctype="multipart/form-data"
						class="dli-import-form">
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
					<table class="form-table dli-import-form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row"><label for="dli-pages-csv"><?php esc_html_e( 'File CSV', 'design_laboratori_italia' ); ?></label></th>
								<td>
									<input type="file" id="dli-pages-csv" name="<?php echo esc_attr( self::FIELD_FILE ); ?>" accept=".csv" required aria-describedby="dli-pages-csv-note">
									<p id="dli-pages-csv-note" class="dli-form-note">
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: the most pages of a file */
												__( 'Dimensione massima: 2 MB, al massimo %d pagine. Separatore: punto e virgola (;).', 'design_laboratori_italia' ),
												DLI_Page_File_Importer::MAX_PAGES
											)
										);
										?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="dli-pages-language"><?php esc_html_e( 'Lingua delle pagine', 'design_laboratori_italia' ); ?></label></th>
								<td>
									<select id="dli-pages-language" name="dli_pages_language" aria-describedby="dli-pages-language-note">
										<?php foreach ( $languages as $slug => $name ) : ?>
											<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $language, $slug ); ?>><?php echo esc_html( '' !== $name ? $name : $slug ); ?></option>
										<?php endforeach; ?>
									</select>
									<p id="dli-pages-language-note" class="dli-form-note"><?php esc_html_e( 'Tutte le righe del file sono nella lingua scelta. Per un sito in più lingue invia un file per lingua, con le stesse page_key.', 'design_laboratori_italia' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="dli-pages-exec"><?php esc_html_e( 'Modalità di esecuzione', 'design_laboratori_italia' ); ?></label></th>
								<td>
									<select id="dli-pages-exec" name="dli_pages_exec">
										<option value="dry_run"<?php selected( $exec, 'dry_run' ); ?>><?php esc_html_e( 'Simulazione (non scrive nulla)', 'design_laboratori_italia' ); ?></option>
										<option value="commit"<?php selected( $exec, 'commit' ); ?>><?php esc_html_e( 'Importa (crea le pagine)', 'design_laboratori_italia' ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Testo di riempimento', 'design_laboratori_italia' ); ?></th>
								<td>
									<label for="dli-pages-lorem">
										<input type="checkbox" id="dli-pages-lorem" name="dli_pages_lorem" value="1"<?php checked( $lorem ); ?> aria-describedby="dli-pages-lorem-note">
										<?php esc_html_e( 'Inserisci testo Lorem ipsum nelle nuove pagine', 'design_laboratori_italia' ); ?>
									</label>
									<p id="dli-pages-lorem-note" class="dli-form-note"><?php esc_html_e( 'Solo per le pagine nuove, mai per quelle già presenti. Le pagine sono pubbliche: usalo solo su siti di prova o di dimostrazione.', 'design_laboratori_italia' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Conferma', 'design_laboratori_italia' ); ?></th>
								<td>
									<label for="dli-pages-confirm">
										<input type="checkbox" id="dli-pages-confirm" name="dli_pages_confirm" value="1" aria-describedby="dli-pages-confirm-note">
										<?php esc_html_e( 'Confermo la pubblicazione immediata delle pagine nuove nella lingua scelta', 'design_laboratori_italia' ); ?>
									</label>
									<p id="dli-pages-confirm-note" class="dli-form-note"><?php esc_html_e( 'Serve solo per importare davvero; la simulazione non la richiede.', 'design_laboratori_italia' ); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="dli-form-actions">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Esegui importazione pagine', 'design_laboratori_italia' ); ?></button>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * The report of the request: the errors, the counters and the pages one by one.
	 *
	 * @param array<string, mixed> $report The report.
	 * @return void
	 */
	private static function render_report( array $report ) {
		$is_dry_run = ! $report['applied'];
		$mode_label = $is_dry_run ? __( 'esecuzione simulata', 'design_laboratori_italia' ) : __( 'esecuzione reale', 'design_laboratori_italia' );
		?>
		<div id="dli-report-section" class="postbox dli-tool-panel dli-tool-panel-report" tabindex="-1">
			<div class="inside">
				<div class="dli-report-title">
					<h2><?php esc_html_e( 'Report importazione', 'design_laboratori_italia' ); ?></h2>
					<span class="dli-report-mode <?php echo esc_attr( $is_dry_run ? 'is-dryrun' : 'is-commit' ); ?>"><?php echo esc_html( $mode_label ); ?></span>
				</div>

				<?php if ( '' !== $report['filename'] ) : ?>
					<p class="dli-report-meta">
						<?php
						printf(
							/* translators: 1: language, 2: lorem ipsum yes/no, 3: file name */
							esc_html__( 'Lingua: %1$s | Testo Lorem ipsum: %2$s | File: %3$s', 'design_laboratori_italia' ),
							esc_html( $report['language'] ),
							esc_html( $report['lorem'] ? __( 'sì', 'design_laboratori_italia' ) : __( 'no', 'design_laboratori_italia' ) ),
							esc_html( $report['filename'] )
						);
						?>
					</p>
				<?php endif; ?>

				<?php if ( '' !== $report['error'] ) : ?>
					<div class="notice notice-error inline" role="alert">
						<p><?php echo esc_html( self::message( $report['error'] ) ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $report['errors'] ) ) : ?>
					<div class="notice notice-error inline" role="alert">
						<p><?php esc_html_e( 'Il file non è stato importato: correggi gli errori e invialo di nuovo.', 'design_laboratori_italia' ); ?></p>
						<ul class="dli-import-errors">
							<?php foreach ( $report['errors'] as $error ) : ?>
								<li><?php echo esc_html( self::message( $error['code'], (int) $error['line'], (string) $error['detail'] ) ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $report['rows'] ) ) : ?>
					<table class="widefat striped fixed dli-report-summary">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Pagine', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Create', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Saltate', 'design_laboratori_italia' ); ?></th>
								<th scope="col" class="col-errors"><?php esc_html_e( 'Errori', 'design_laboratori_italia' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo esc_html( (string) count( $report['rows'] ) ); ?></td>
								<td><?php echo esc_html( (string) $report['counts']['created'] ); ?></td>
								<td><?php echo esc_html( (string) $report['counts']['skipped'] ); ?></td>
								<td><?php echo esc_html( (string) $report['counts']['errors'] ); ?></td>
							</tr>
						</tbody>
					</table>

					<div class="dli-log-box">
						<h3><?php esc_html_e( 'Pagine', 'design_laboratori_italia' ); ?></h3>
						<table class="widefat striped fixed dli-log-table">
							<caption class="screen-reader-text"><?php esc_html_e( 'Le pagine del file, con l\'indirizzo e l\'esito', 'design_laboratori_italia' ); ?></caption>
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Riga', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Pagina', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Indirizzo', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Ordine', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'In home', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Card', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Esito', 'design_laboratori_italia' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $report['rows'] as $row ) : ?>
									<tr>
										<td class="dli-log-cell-id"><?php echo esc_html( (string) $row['line'] ); ?></td>
										<td class="dli-import-indent-<?php echo esc_attr( (string) $row['level'] ); ?>">
											<?php echo esc_html( $row['title'] ); ?>
											<span class="screen-reader-text">
												<?php
												/* translators: %d: level of the page, from 1 */
												echo esc_html( sprintf( __( '(livello %d)', 'design_laboratori_italia' ), (int) $row['level'] ) );
												?>
											</span>
										</td>
										<td><?php echo esc_html( (string) wp_parse_url( $row['url'], PHP_URL_PATH ) ); ?></td>
										<td><?php echo esc_html( (string) $row['order'] ); ?></td>
										<td><?php echo esc_html( $row['promote'] ? __( 'sì', 'design_laboratori_italia' ) : __( 'no', 'design_laboratori_italia' ) ); ?></td>
										<td><?php echo esc_html( $row['cards'] ? __( 'sì', 'design_laboratori_italia' ) : __( 'no', 'design_laboratori_italia' ) ); ?></td>
										<td class="<?php echo esc_attr( self::status_class( $row['action'] ) ); ?>"><?php echo esc_html( self::row_outcome( $row, $report['applied'] ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * The class of the outcome of a row.
	 *
	 * @param string $action Action of the row.
	 * @return string The class.
	 */
	private static function status_class( $action ) {
		if ( 'created' === $action ) {
			return 'dli-log-status-ok';
		}
		return 'skipped' === $action ? 'dli-log-status-saltata' : 'dli-log-status-errore';
	}

	/**
	 * The words for the outcome of a row, in the simulation or after the import.
	 *
	 * @param array<string, mixed> $row     Row of the plan.
	 * @param bool                 $applied Whether the import was run.
	 * @return string The outcome.
	 */
	private static function row_outcome( array $row, $applied ) {
		switch ( $row['action'] ) {
			case 'created':
				return $applied ? __( 'Creata', 'design_laboratori_italia' ) : __( 'Verrà creata', 'design_laboratori_italia' );
			case 'skipped':
				return __( 'Saltata: già importata', 'design_laboratori_italia' );
			case 'not_created':
				return __( 'Non creata', 'design_laboratori_italia' );
		}
		return self::message( (string) $row['message'], (int) $row['line'], (string) $row['detail'] );
	}
}
