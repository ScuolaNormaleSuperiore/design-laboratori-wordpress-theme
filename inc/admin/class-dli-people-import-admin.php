<?php
/**
 * Strumenti DLI → Importa → Persone: the form, the request, the report and the download of the example.
 *
 * Like the import of the pages, the page handles its own form: the file is sent at each step and nothing is kept on
 * the server. The upload is checked by `DLI_CSV_File`; each row is imported by `DLI_People_File_Importer`.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The tool that imports people from a CSV file.
 */
class DLI_People_Import_Admin {

	/**
	 * Action of the nonce of the form.
	 */
	const NONCE_ACTION = 'dli_import_persone';

	/**
	 * Field of the nonce of the form.
	 */
	const NONCE_FIELD = 'dli_import_persone_nonce';

	/**
	 * Field of the file in the form.
	 */
	const FIELD_FILE = 'dli_csv_file';

	/**
	 * Action of the address that downloads the example.
	 */
	const DOWNLOAD_ACTION = 'download-sample-persone';

	/**
	 * Action of the nonce of the download of the example.
	 */
	const DOWNLOAD_NONCE = 'dli_download_sample_persone';

	/**
	 * The modes of the import.
	 */
	const IMPORT_MODES = array( 'upsert', 'solo_nuovi', 'aggiorna_esistenti', 'ignora_esistenti' );

	/**
	 * The columns that the header must have.
	 */
	const REQUIRED_COLUMNS = array( 'nome', 'cognome', 'email' );

	/**
	 * The columns that the header may have.
	 */
	const OPTIONAL_COLUMNS = array( 'titolo', 'telefono', 'tipologia_persona', 'struttura', 'stato', 'sito_web', 'body', 'escludi_da_elenco', 'disattiva_pagina_dettaglio' );

	/**
	 * The value of a column that is not in the file.
	 */
	const DEFAULTS = array(
		'stato'                      => 'publish',
		'escludi_da_elenco'          => '0',
		'disattiva_pagina_dettaglio' => '0',
	);

	/**
	 * The path of the example file, in `SETUP/CSV_Import/`.
	 *
	 * @return string The path.
	 */
	public static function example_path() {
		return dirname( __DIR__, 2 ) . '/SETUP/CSV_Import/people-example.csv';
	}

	/**
	 * Send the example file and stop: only for who can use the tools, with the nonce of the link.
	 *
	 * @return void
	 */
	public static function handle_download() {
		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) );
		}
		check_admin_referer( self::DOWNLOAD_NONCE );

		$path = self::example_path();
		if ( ! is_readable( $path ) ) {
			wp_die( esc_html__( 'File non trovato.', 'design_laboratori_italia' ), '', array( 'response' => 404 ) );
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . basename( $path ) . '"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- A file of the theme, with a fixed path.
		exit;
	}

	/**
	 * Check the request and import the file, or simulate the import.
	 *
	 * Without `DLI_EDIT_CONFIG_PERMISSION` or with a wrong nonce the request stops with `wp_die()`. A mode that is not
	 * known is `upsert`, a run mode that is not known is `dry_run`. The file is read by `DLI_CSV_Reader` with the
	 * columns of the people: a file that cannot be used or read, or whose header misses a required column, gives a
	 * notice and nothing is processed. Otherwise each row is processed on its own, in the order of the file, and an
	 * error (also a row too short) stops only its row; the columns that are not of the format are listed in `ignored`.
	 *
	 * @param array<string, mixed> $post           The values of the form, without slashes.
	 * @param array<string, mixed> $files          The uploaded files, as in `$_FILES`.
	 * @param bool                 $require_upload Whether the file must come from an upload through HTTP; only the tests turn it off.
	 * @return array<string, mixed> The report.
	 */
	public static function process_request( array $post, array $files, $require_upload = true ) {
		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) );
		}

		$nonce = isset( $post[ self::NONCE_FIELD ] ) ? sanitize_text_field( (string) $post[ self::NONCE_FIELD ] ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' ) );
		}

		$import_mode = isset( $post['dli_import_mode'] ) ? sanitize_key( (string) $post['dli_import_mode'] ) : 'upsert';
		$exec_mode   = isset( $post['dli_exec_mode'] ) ? sanitize_key( (string) $post['dli_exec_mode'] ) : 'dry_run';
		if ( ! in_array( $import_mode, self::IMPORT_MODES, true ) ) {
			$import_mode = 'upsert';
		}
		if ( ! in_array( $exec_mode, array( 'dry_run', 'commit' ), true ) ) {
			$exec_mode = 'dry_run';
		}

		$report = array(
			'exec_mode'    => $exec_mode,
			'import_mode'  => $import_mode,
			'filename'     => '',
			'elaborati'    => 0,
			'creati'       => 0,
			'aggiornati'   => 0,
			'saltati'      => 0,
			'errori_count' => 0,
			'log'          => array(),
			'ignored'      => array(),
			'notice'       => '',
			'notice_type'  => 'info',
		);

		$file  = isset( $files[ self::FIELD_FILE ] ) && is_array( $files[ self::FIELD_FILE ] ) ? $files[ self::FIELD_FILE ] : array();
		$check = DLI_CSV_File::check( $file, $require_upload );
		if ( ! in_array( $check, array( 'no_file', 'not_uploaded' ), true ) ) {
			$report['filename'] = sanitize_file_name( (string) $file['name'] );
		}
		if ( '' !== $check ) {
			$report['notice']      = DLI_CSV_File::message( $check );
			$report['notice_type'] = 'no_file' === $check ? 'warning' : 'error';
			return $report;
		}

		$read = DLI_CSV_Reader::read(
			(string) $file['tmp_name'],
			array(
				'required' => self::REQUIRED_COLUMNS,
				'optional' => self::OPTIONAL_COLUMNS,
			)
		);

		$file_errors = array();
		$short_rows  = array();
		foreach ( $read['errors'] as $error ) {
			if ( 'columns_count' === $error['code'] ) {
				$short_rows[] = $error['line'];
			} else {
				$file_errors[] = DLI_CSV_File::message( $error['code'], $error['line'], $error['detail'] );
			}
		}
		if ( ! empty( $file_errors ) ) {
			$report['notice']      = implode( ' ', $file_errors );
			$report['notice_type'] = 'error';
			return $report;
		}
		$report['ignored'] = $read['ignored'];

		foreach ( self::rows_by_line( $read['rows'], $short_rows ) as $line => $cells ) {
			++$report['elaborati'];
			if ( null === $cells ) {
				$report['log'][] = self::short_row_entry( $line );
				$report          = self::count( $report, 'error' );
				continue;
			}

			$data            = array_merge( array_fill_keys( self::OPTIONAL_COLUMNS, '' ), self::DEFAULTS, $cells );
			$result          = DLI_People_File_Importer::process_row( $data, $import_mode, $exec_mode, $line );
			$report['log'][] = $result['log_entry'];
			$report          = self::count( $report, $result['action'] );
		}

		return $report;
	}

	/**
	 * The rows that were read and the rows too short, in the order of the file.
	 *
	 * @param array<int, array{line: int, cells: array<string, string>}> $rows       The rows of the reader.
	 * @param int[]                                                      $short_rows The lines of the rows too short.
	 * @return array<int, array<string, string>|null> The cells by line; null for a row too short.
	 */
	private static function rows_by_line( array $rows, array $short_rows ) {
		$by_line = array_fill_keys( $short_rows, null );
		foreach ( $rows as $row ) {
			$by_line[ $row['line'] ] = $row['cells'];
		}
		ksort( $by_line );
		return $by_line;
	}

	/**
	 * The line of the log of a row too short to be read.
	 *
	 * @param int $line Line of the file.
	 * @return array<string, mixed> The line of the log.
	 */
	private static function short_row_entry( $line ) {
		return array(
			'row'     => $line,
			'name'    => '',
			'email'   => '',
			'status'  => 'ERRORE',
			'message' => DLI_CSV_File::message( 'columns_count', $line ),
		);
	}

	/**
	 * Add the outcome of a row to the counters of the report.
	 *
	 * @param array<string, mixed> $report The report.
	 * @param string               $action `created`, `updated`, `skipped` or `error`.
	 * @return array<string, mixed> The report.
	 */
	private static function count( array $report, $action ) {
		$counters = array(
			'created' => 'creati',
			'updated' => 'aggiornati',
			'skipped' => 'saltati',
			'error'   => 'errori_count',
		);
		if ( isset( $counters[ $action ] ) ) {
			++$report[ $counters[ $action ] ];
		}
		return $report;
	}

	/**
	 * The whole tool: the explanation, the form and, after a request, its report.
	 *
	 * @return void
	 */
	public static function render() {
		$report = null;

		if ( isset( $_POST[ self::NONCE_FIELD ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in process_request().
			$report = self::process_request( wp_unslash( $_POST ), $_FILES, true ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Verified and sanitized in process_request().
		}

		self::render_intro();
		self::render_form();

		if ( null !== $report ) {
			self::render_report( $report );
			// Scroll to report after form submission so the result is immediately visible.
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
	 * Render the "Come funziona" information box for the Persone import.
	 *
	 * @return void
	 */
	private static function render_intro() {
		$download_url  = wp_nonce_url(
			admin_url( 'admin.php?page=' . DLI_Tools_Admin::IMPORT_SLUG . '&action=' . self::DOWNLOAD_ACTION ),
			self::DOWNLOAD_NONCE
		);
		$titolo_values = array( 'Cav.', 'Comm.', 'Dott.', 'Dott.ssa', 'Dr.', 'Prof.', 'Prof.ssa', 'Ing.', 'Avv.', 'Rag.', 'Sig.', 'Sig.ra' );
		?>
		<div class="postbox dli-tool-panel dli-tool-panel-intro">
			<div class="inside">
				<h2><?php esc_html_e( 'Come funziona', 'design_laboratori_italia' ); ?></h2>
				<p class="dli-section-lead"><?php esc_html_e( 'Importa persone da un file CSV in UTF-8, con separatore punto e virgola (;). I nomi delle colonne si possono scrivere anche in maiuscolo; le altre colonne e le righe vuote vengono ignorate, e le colonne ignorate sono elencate nel report.', 'design_laboratori_italia' ); ?></p>

				<div class="dli-info-list">
					<div class="dli-info-row">
						<h3><?php esc_html_e( 'Intestazione CSV richiesta', 'design_laboratori_italia' ); ?></h3>
						<code class="dli-csv-header">nome;cognome;email;titolo;telefono;tipologia_persona;struttura;stato;sito_web;body;escludi_da_elenco;disattiva_pagina_dettaglio</code>
					</div>

					<div class="dli-info-row">
						<details class="dli-advanced-details">
							<summary><?php esc_html_e( 'Dettagli campi e valori supportati', 'design_laboratori_italia' ); ?></summary>
							<div class="dli-advanced-details-body">
								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Colonne obbligatorie', 'design_laboratori_italia' ); ?></h3>
									<p><code>nome</code>, <code>cognome</code>, <code>email</code>. <?php esc_html_e( 'Tutte le altre colonne sono facoltative.', 'design_laboratori_italia' ); ?></p>
								</div>

								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Valori consentiti per', 'design_laboratori_italia' ); ?> <code>titolo</code></h3>
									<p>
										<?php foreach ( $titolo_values as $v ) : ?>
											<span class="dli-value-badge"><?php echo esc_html( $v ); ?></span>
										<?php endforeach; ?>
									</p>
								</div>

								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Valori consentiti per', 'design_laboratori_italia' ); ?> <code>stato</code></h3>
									<p>
										<span class="dli-value-badge">publish</span>
										<span class="dli-value-badge">draft</span>
										<span class="dli-inline-note"><?php esc_html_e( 'Predefinito: publish.', 'design_laboratori_italia' ); ?></span>
									</p>
								</div>

								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Campi booleani', 'design_laboratori_italia' ); ?></h3>
									<p><code>escludi_da_elenco</code>, <code>disattiva_pagina_dettaglio</code>: <span class="dli-value-badge">1</span> = <?php esc_html_e( 'attivo', 'design_laboratori_italia' ); ?>, <span class="dli-value-badge">0</span> <?php esc_html_e( 'o vuoto = disattivo (predefinito).', 'design_laboratori_italia' ); ?></p>
								</div>

								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Valori multipli', 'design_laboratori_italia' ); ?></h3>
									<p><code>struttura</code>: <?php esc_html_e( 'separare con', 'design_laboratori_italia' ); ?> <code>|</code>. <span class="dli-inline-note"><code>prima-struttura|seconda-struttura</code></span></p>
								</div>

								<div class="dli-info-row">
									<h3><?php esc_html_e( 'Note operative', 'design_laboratori_italia' ); ?></h3>
									<p><?php esc_html_e( 'Per tipologia_persona e struttura: inserire lo slug della voce già esistente. Le voci non trovate vengono ignorate.', 'design_laboratori_italia' ); ?></p>
									<p><?php esc_html_e( 'Il campo body accetta testo semplice o HTML base (corsivo, grassetto, paragrafi).', 'design_laboratori_italia' ); ?></p>
								</div>
							</div>
						</details>
					</div>
				</div>

				<p class="dli-section-actions">
					<a href="<?php echo esc_url( $download_url ); ?>" class="button">
						<?php esc_html_e( 'Scarica CSV di esempio', 'design_laboratori_italia' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the "Esegui importazione" form for the Persone import.
	 *
	 * @return void
	 */
	private static function render_form() {
		$import_modes = array(
			'upsert'             => __( 'upsert (crea o aggiorna)', 'design_laboratori_italia' ),
			'solo_nuovi'         => __( 'solo nuovi (salta se esiste)', 'design_laboratori_italia' ),
			'aggiorna_esistenti' => __( 'aggiorna esistenti (salta se non esiste)', 'design_laboratori_italia' ),
			'ignora_esistenti'   => __( 'ignora esistenti (non aggiorna)', 'design_laboratori_italia' ),
		);

		$exec_modes = array(
			'dry_run' => __( 'dry_run (simulazione)', 'design_laboratori_italia' ),
			'commit'  => __( 'commit (esecuzione reale)', 'design_laboratori_italia' ),
		);

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$sel_import = isset( $_POST['dli_import_mode'] ) ? sanitize_key( wp_unslash( $_POST['dli_import_mode'] ) ) : 'upsert';
		$sel_exec   = isset( $_POST['dli_exec_mode'] ) ? sanitize_key( wp_unslash( $_POST['dli_exec_mode'] ) ) : 'dry_run';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		?>
		<div class="postbox dli-tool-panel dli-tool-panel-form">
			<div class="inside">
				<h2><?php esc_html_e( 'Esegui importazione', 'design_laboratori_italia' ); ?></h2>
				<form method="POST"
						action="<?php echo esc_url( admin_url( 'admin.php?page=' . DLI_Tools_Admin::IMPORT_SLUG ) ); ?>"
						enctype="multipart/form-data"
						class="dli-import-form">
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
					<table class="form-table dli-import-form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row">
									<label for="dli-csv-file"><?php esc_html_e( 'File CSV', 'design_laboratori_italia' ); ?></label>
								</th>
								<td>
									<input type="file" id="dli-csv-file" name="<?php echo esc_attr( self::FIELD_FILE ); ?>" accept=".csv"
											required aria-describedby="dli-csv-file-note">
									<p id="dli-csv-file-note" class="dli-form-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %d: the most rows of a file */
											__( 'Dimensione massima: 2 MB, al massimo %d righe. Separatore: punto e virgola (;).', 'design_laboratori_italia' ),
											DLI_CSV_Reader::MAX_ROWS
										)
									);
									?>
								</p>
								</td>
							</tr>

							<tr>
								<th scope="row">
									<label for="dli-import-mode"><?php esc_html_e( 'Modalità importazione', 'design_laboratori_italia' ); ?></label>
								</th>
								<td>
									<select id="dli-import-mode" name="dli_import_mode">
										<?php foreach ( $import_modes as $value => $label ) : ?>
											<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $sel_import, $value ); ?>>
												<?php echo esc_html( $label ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>

							<tr>
								<th scope="row">
									<label for="dli-exec-mode"><?php esc_html_e( 'Modalità di esecuzione', 'design_laboratori_italia' ); ?></label>
								</th>
								<td>
									<select id="dli-exec-mode" name="dli_exec_mode">
										<?php foreach ( $exec_modes as $value => $label ) : ?>
											<option value="<?php echo esc_attr( $value ); ?>"
												<?php selected( $sel_exec, $value ); ?>>
												<?php echo esc_html( $label ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</tbody>
					</table>

					<p class="dli-form-actions">
						<button type="submit" class="button button-primary">
							<?php esc_html_e( 'Esegui import persone', 'design_laboratori_italia' ); ?>
						</button>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the import report box.
	 *
	 * @param array<string, mixed> $report Report data from process_request().
	 * @return void
	 */
	public static function render_report( array $report ) {
		$is_dryrun    = ( 'dry_run' === $report['exec_mode'] );
		$mode_label   = $is_dryrun
			? __( 'esecuzione simulata', 'design_laboratori_italia' )
			: __( 'esecuzione reale', 'design_laboratori_italia' );
		$mode_class   = $is_dryrun ? 'is-dryrun' : 'is-commit';
		$notice_class = 'notice-' . ( $report['notice_type'] ?? 'info' );
		?>
		<div id="dli-report-section" class="postbox dli-tool-panel dli-tool-panel-report" tabindex="-1">
			<div class="inside">
				<div class="dli-report-title">
					<h2><?php esc_html_e( 'Report importazione', 'design_laboratori_italia' ); ?></h2>
					<span class="dli-report-mode <?php echo esc_attr( $mode_class ); ?>">
						<?php echo esc_html( $mode_label ); ?>
					</span>
				</div>

				<?php if ( $report['filename'] ) : ?>
					<p class="dli-report-meta">
						<?php
						printf(
							/* translators: 1: exec mode 2: import mode 3: filename */
							esc_html__( 'Modalità esecuzione: %1$s | Modalità importazione: %2$s | File: %3$s', 'design_laboratori_italia' ),
							esc_html( $report['exec_mode'] ),
							esc_html( $report['import_mode'] ),
							esc_html( $report['filename'] )
						);
						?>
					</p>
				<?php endif; ?>

				<?php if ( $report['notice'] ) : ?>
					<div class="notice <?php echo esc_attr( $notice_class ); ?> inline" role="alert">
						<p><?php echo esc_html( $report['notice'] ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $report['ignored'] ) ) : ?>
					<div class="notice notice-warning inline">
						<p><?php echo esc_html( DLI_CSV_File::ignored_message( $report['ignored'] ) ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $report['filename'] ) : ?>
				<table class="widefat striped fixed dli-report-summary">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Elaborati', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Creati', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Aggiornati', 'design_laboratori_italia' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Saltati', 'design_laboratori_italia' ); ?></th>
								<th scope="col" class="col-errors"><?php esc_html_e( 'Errori', 'design_laboratori_italia' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo esc_html( (string) $report['elaborati'] ); ?></td>
								<td><?php echo esc_html( (string) $report['creati'] ); ?></td>
								<td><?php echo esc_html( (string) $report['aggiornati'] ); ?></td>
								<td><?php echo esc_html( (string) $report['saltati'] ); ?></td>
								<td><?php echo esc_html( (string) $report['errori_count'] ); ?></td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>

				<?php if ( ! empty( $report['log'] ) ) : ?>
					<div class="dli-log-box">
						<h3><?php esc_html_e( 'Log importazione', 'design_laboratori_italia' ); ?></h3>
					<table class="widefat striped fixed dli-log-table">
							<caption class="screen-reader-text"><?php esc_html_e( 'Dettaglio righe elaborate', 'design_laboratori_italia' ); ?></caption>
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Riga', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Nome e cognome', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Email', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Stato', 'design_laboratori_italia' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Messaggio', 'design_laboratori_italia' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $report['log'] as $entry ) : ?>
									<tr>
										<td class="dli-log-cell-id"><?php echo esc_html( (string) $entry['row'] ); ?></td>
										<td class="dli-log-cell-name"><?php echo esc_html( $entry['name'] ?? '' ); ?></td>
										<td class="dli-log-cell-email"><?php echo esc_html( $entry['email'] ?? '' ); ?></td>
										<td class="dli-log-status-<?php echo esc_attr( strtolower( $entry['status'] ) ); ?>">
											<?php echo esc_html( $entry['status'] ); ?>
										</td>
										<td><?php echo esc_html( $entry['message'] ); ?></td>
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
}
