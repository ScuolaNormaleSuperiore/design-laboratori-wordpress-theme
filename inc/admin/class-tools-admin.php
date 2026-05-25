<?php
/**
 * Strumenti DLI — admin area registration and page rendering.
 *
 * Registers the top-level menu and its initial subpages (Panoramica, Importa, Utilità).
 * Provides a shared shell with horizontal tab navigation for all subpages.
 * Deliberately avoids CMB2: pages here are task-oriented, not persistent-settings forms.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Admin area for DLI operational tools (imports, utilities, maintenance).
 */
class DLI_Tools_Admin {

	const MENU_SLUG     = 'dli-tools';
	const IMPORT_SLUG   = 'dli-tools-import';
	const UTILS_SLUG    = 'dli-tools-utils';
	const MENU_POSITION = 3;

	/**
	 * Wire hooks.
	 *
	 * @return void
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_early_actions' ) );
	}

	/**
	 * Register the top-level menu and its subpages.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_menu_page(
			esc_html__( 'Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Strumenti DLI', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::MENU_SLUG,
			array( __CLASS__, 'render_panoramica' ),
			'dashicons-hammer',
			self::MENU_POSITION
		);

		// Rename the auto-created first submenu item to "Panoramica".
		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Panoramica — Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Panoramica', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::MENU_SLUG,
			array( __CLASS__, 'render_panoramica' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Importa — Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Importa', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::IMPORT_SLUG,
			array( __CLASS__, 'render_importa' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Utilità — Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Utilità', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::UTILS_SLUG,
			array( __CLASS__, 'render_utils' )
		);
	}

	/**
	 * Enqueue admin CSS only on Strumenti DLI pages.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public static function enqueue_assets( $hook_suffix ) {
		$tools_hooks = array(
			'toplevel_page_' . self::MENU_SLUG,
			self::MENU_SLUG . '_page_' . self::IMPORT_SLUG,
			self::MENU_SLUG . '_page_' . self::UTILS_SLUG,
		);

		if ( ! in_array( $hook_suffix, $tools_hooks, true ) ) {
			return;
		}

		wp_enqueue_style(
			'dli-tools-admin',
			get_template_directory_uri() . '/assets/css/tools-admin.css',
			array(),
			(string) filemtime( get_template_directory() . '/assets/css/tools-admin.css' )
		);
	}

	/**
	 * Handle early GET actions that must fire before any output is sent.
	 *
	 * Currently handles: CSV sample file download for the Persone import.
	 *
	 * @return void
	 */
	public static function handle_early_actions() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$page   = isset( $_GET['page'] )   ? sanitize_key( wp_unslash( $_GET['page'] ) )   : '';
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( self::IMPORT_SLUG !== $page || 'download-sample-persone' !== $action ) {
			return;
		}

		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) );
		}

		check_admin_referer( 'dli_download_sample_persone' );

		$headers = array( 'nome', 'cognome', 'email', 'titolo', 'telefono', 'tipologia_persona', 'struttura', 'stato', 'sito_web', 'body', 'escludi_da_elenco', 'disattiva_pagina_dettaglio' );
		$rows    = array(
			array( 'Mario', 'Rossi', 'mario.rossi@example.com', 'Prof.', '050-123456', 'professore-ordinario', 'laboratorio-abc', 'publish', 'https://example.com', 'Breve biografia di Mario Rossi.', '0', '0' ),
			array( 'Anna', 'Bianchi', 'anna.bianchi@example.com', 'Dott.ssa', '', 'ricercatrice', 'laboratorio-abc|laboratorio-xyz', 'draft', '', '', '1', '0' ),
		);

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="persone-esempio.csv"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );

		$output = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $output, "\xEF\xBB\xBF" ); // UTF-8 BOM for correct Excel rendering.
		fputcsv( $output, $headers, ';' );
		foreach ( $rows as $row ) {
			fputcsv( $output, $row, ';' );
		}
		fclose( $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}

	/**
	 * Halt execution if the current user lacks the required capability.
	 *
	 * @return void
	 */
	private static function require_capability() {
		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per accedere a questa pagina.', 'design_laboratori_italia' ) );
		}
	}

	/**
	 * Render the shared page shell: wrap, h1, horizontal tab nav, content area.
	 *
	 * @param callable $content_callback Renders the page-specific body inside the content area.
	 * @return void
	 */
	private static function render_shell( callable $content_callback ) {
		$current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$tabs = array(
			self::MENU_SLUG   => __( 'Panoramica', 'design_laboratori_italia' ),
			self::IMPORT_SLUG => __( 'Importa', 'design_laboratori_italia' ),
			'_export'         => __( 'Esporta', 'design_laboratori_italia' ),
			self::UTILS_SLUG  => __( 'Utilità', 'design_laboratori_italia' ),
		);

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Strumenti DLI', 'design_laboratori_italia' ); ?></h1>

			<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Sezioni Strumenti DLI', 'design_laboratori_italia' ); ?>">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<?php if ( '_export' === $slug ) : ?>
						<span class="nav-tab dli-tab-disabled" aria-disabled="true">
							<?php echo esc_html( $label ); ?>
						</span>
					<?php else : ?>
						<a class="nav-tab<?php echo ( $slug === $current_page ) ? ' nav-tab-active' : ''; ?>"
						   href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>

			<div class="dli-tools-tab-content">
				<?php call_user_func( $content_callback ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Panoramica page.
	 *
	 * @return void
	 */
	public static function render_panoramica() {
		self::require_capability();
		self::render_shell(
			function() {
				?>
				<p><?php esc_html_e( 'Questa sezione contiene gli strumenti di gestione dei contenuti del sito.', 'design_laboratori_italia' ); ?></p>
				<p><?php esc_html_e( "Usa i tab per navigare tra le sezioni operative dell'area.", 'design_laboratori_italia' ); ?></p>
				<?php
			}
		);
	}

	/**
	 * Render the Importa page.
	 *
	 * @return void
	 */
	public static function render_importa() {
		self::require_capability();
		self::render_shell(
			function() {
				$import_type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'persone'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				echo '<h2 class="dli-import-section-title">' . esc_html__( 'Strumenti di importazione', 'design_laboratori_italia' ) . '</h2>';

				self::render_import_subnav( $import_type );

				if ( 'persone' === $import_type ) {
					self::render_import_persone();
				}
			}
		);
	}

	/**
	 * Render the sub-navigation for available import types.
	 *
	 * @param string $current_type Active import type slug.
	 * @return void
	 */
	private static function render_import_subnav( $current_type ) {
		$types = array(
			'persone' => __( 'Persone', 'design_laboratori_italia' ),
		);

		echo '<div class="dli-import-subnav">';
		foreach ( $types as $slug => $label ) {
			$url   = esc_url( admin_url( 'admin.php?page=' . self::IMPORT_SLUG . '&type=' . $slug ) );
			$class = ( $slug === $current_type ) ? ' class="current"' : '';
			echo '<a' . $class . ' href="' . $url . '">' . esc_html( $label ) . '</a> <span class="sep">|</span> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	/**
	 * Render the full Importa → Persone UI (Come funziona + form + report).
	 *
	 * @return void
	 */
	private static function render_import_persone() {
		$report = null;

		if ( isset( $_POST['dli_import_persone_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$report = self::handle_import_persone_post();
		}

		self::render_come_funziona_persone();
		self::render_form_import_persone();

		if ( null !== $report ) {
			self::render_report_persone( $report );
		}
	}

	/**
	 * Process the Importa Persone form POST and return a report array.
	 *
	 * @return array
	 */
	private static function handle_import_persone_post() {
		if ( ! current_user_can( DLI_EDIT_CONFIG_PERMISSION ) ) {
			wp_die( esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) );
		}

		if ( ! isset( $_POST['dli_import_persone_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dli_import_persone_nonce'] ) ), 'dli_import_persone' ) ) {
			wp_die( esc_html__( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' ) );
		}

		$allowed_import_modes = array( 'upsert', 'solo_nuovi', 'aggiorna_esistenti', 'ignora_esistenti' );
		$allowed_exec_modes   = array( 'dry_run', 'commit' );

		$import_mode = isset( $_POST['dli_import_mode'] ) ? sanitize_key( wp_unslash( $_POST['dli_import_mode'] ) ) : 'upsert';
		$exec_mode   = isset( $_POST['dli_exec_mode'] )   ? sanitize_key( wp_unslash( $_POST['dli_exec_mode'] ) )   : 'dry_run';

		if ( ! in_array( $import_mode, $allowed_import_modes, true ) ) {
			$import_mode = 'upsert';
		}
		if ( ! in_array( $exec_mode, $allowed_exec_modes, true ) ) {
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
			'notice'       => '',
		);

		if ( empty( $_FILES['dli_csv_file']['tmp_name'] ) || UPLOAD_ERR_OK !== (int) $_FILES['dli_csv_file']['error'] ) {
			$report['notice'] = __( 'Nessun file caricato o errore nel caricamento.', 'design_laboratori_italia' );
			return $report;
		}

		$filename  = sanitize_file_name( $_FILES['dli_csv_file']['name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$tmp_path  = $_FILES['dli_csv_file']['tmp_name'];                   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$file_size = (int) $_FILES['dli_csv_file']['size'];

		// Ensure the file was actually uploaded via HTTP POST, preventing arbitrary file reads.
		if ( ! is_uploaded_file( $tmp_path ) ) {
			$report['notice'] = __( 'File non valido.', 'design_laboratori_italia' );
			return $report;
		}

		$report['filename'] = $filename;

		if ( 'csv' !== strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			$report['notice'] = __( 'Formato non valido: è richiesto un file .csv.', 'design_laboratori_italia' );
			return $report;
		}

		if ( $file_size > 2 * 1024 * 1024 ) {
			$report['notice'] = __( 'File troppo grande. Dimensione massima: 2 MB.', 'design_laboratori_italia' );
			return $report;
		}

		if ( function_exists( 'mime_content_type' ) ) {
			$mime          = mime_content_type( $tmp_path );
			$allowed_mimes = array( 'text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel' );
			if ( $mime && ! in_array( $mime, $allowed_mimes, true ) ) {
				$report['notice'] = sprintf(
					/* translators: %s: detected MIME type */
					__( 'Tipo file non valido (%s). È richiesto un file CSV.', 'design_laboratori_italia' ),
					$mime
				);
				return $report;
			}
		}

		$handle = fopen( $tmp_path, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( ! $handle ) {
			$report['notice'] = __( 'Impossibile leggere il file caricato.', 'design_laboratori_italia' );
			return $report;
		}

		// Skip UTF-8 BOM if present.
		$bom = fread( $handle, 3 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		if ( "\xEF\xBB\xBF" !== $bom ) {
			rewind( $handle );
		}

		$header_row = fgetcsv( $handle, 0, ';' );
		if ( ! $header_row ) {
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			$report['notice'] = __( 'File vuoto o intestazione non leggibile.', 'design_laboratori_italia' );
			return $report;
		}

		$header_row       = array_map( 'trim', $header_row );
		$required_headers = array( 'nome', 'cognome', 'email' );

		foreach ( $required_headers as $required ) {
			if ( ! in_array( $required, $header_row, true ) ) {
				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				$report['notice'] = sprintf(
					/* translators: %s: missing column name */
					__( 'Intestazione non valida: colonna obbligatoria "%s" non trovata.', 'design_laboratori_italia' ),
					$required
				);
				return $report;
			}
		}

		// Map column names to their index for easy lookup.
		$col     = array_flip( $header_row );
		$row_num = 1;

		while ( ( $row = fgetcsv( $handle, 0, ';' ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
			++$row_num;
			++$report['elaborati'];

			$data = array(
				'nome'                       => trim( isset( $col['nome'] ) ? $row[ $col['nome'] ] : '' ),
				'cognome'                    => trim( isset( $col['cognome'] ) ? $row[ $col['cognome'] ] : '' ),
				'email'                      => trim( isset( $col['email'] ) ? $row[ $col['email'] ] : '' ),
				'titolo'                     => trim( isset( $col['titolo'] ) ? $row[ $col['titolo'] ] : '' ),
				'telefono'                   => trim( isset( $col['telefono'] ) ? $row[ $col['telefono'] ] : '' ),
				'tipologia_persona'          => trim( isset( $col['tipologia_persona'] ) ? $row[ $col['tipologia_persona'] ] : '' ),
				'struttura'                  => trim( isset( $col['struttura'] ) ? $row[ $col['struttura'] ] : '' ),
				'stato'                      => trim( isset( $col['stato'] ) ? $row[ $col['stato'] ] : 'publish' ),
				'sito_web'                   => trim( isset( $col['sito_web'] ) ? $row[ $col['sito_web'] ] : '' ),
				'body'                       => trim( isset( $col['body'] ) ? $row[ $col['body'] ] : '' ),
				'escludi_da_elenco'          => trim( isset( $col['escludi_da_elenco'] ) ? $row[ $col['escludi_da_elenco'] ] : '0' ),
				'disattiva_pagina_dettaglio' => trim( isset( $col['disattiva_pagina_dettaglio'] ) ? $row[ $col['disattiva_pagina_dettaglio'] ] : '0' ),
			);

			$result          = DLI_People_File_Importer::process_row( $data, $import_mode, $exec_mode, $row_num );
			$report['log'][] = $result['log_entry'];

			switch ( $result['action'] ) {
				case 'created':
					++$report['creati'];
					break;
				case 'updated':
					++$report['aggiornati'];
					break;
				case 'skipped':
					++$report['saltati'];
					break;
				case 'error':
					++$report['errori_count'];
					break;
			}
		}

		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		return $report;
	}

	/**
	 * Render the "Come funziona" information box for the Persone import.
	 *
	 * @return void
	 */
	private static function render_come_funziona_persone() {
		$download_url  = wp_nonce_url(
			admin_url( 'admin.php?page=' . self::IMPORT_SLUG . '&action=download-sample-persone' ),
			'dli_download_sample_persone'
		);
		$titolo_values = array( 'Cav.', 'Comm.', 'Dott.', 'Dott.ssa', 'Dr.', 'Prof.', 'Prof.ssa', 'Ing.', 'Avv.', 'Rag.', 'Sig.', 'Sig.ra' );
		?>
		<div class="dli-tool-box">
			<h2><?php esc_html_e( 'Come funziona', 'design_laboratori_italia' ); ?></h2>
			<p><?php esc_html_e( 'Importa persone da un file CSV con separatore punto e virgola (;). L\'intestazione deve corrispondere esattamente a quella richiesta.', 'design_laboratori_italia' ); ?></p>

			<p>
				<strong><?php esc_html_e( 'Intestazione CSV richiesta:', 'design_laboratori_italia' ); ?></strong>
				<code class="dli-csv-header">nome;cognome;email;titolo;telefono;tipologia_persona;struttura;stato;sito_web;body;escludi_da_elenco;disattiva_pagina_dettaglio</code>
			</p>

			<p>
				<strong><?php esc_html_e( 'Colonne obbligatorie:', 'design_laboratori_italia' ); ?></strong>
				<code>nome</code>, <code>cognome</code>, <code>email</code>.
				<?php esc_html_e( 'Tutte le altre colonne sono facoltative.', 'design_laboratori_italia' ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Valori consentiti per', 'design_laboratori_italia' ); ?> <code>titolo</code>:</strong>
				<?php foreach ( $titolo_values as $v ) : ?>
					<span class="dli-value-badge"><?php echo esc_html( $v ); ?></span>
				<?php endforeach; ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Valori consentiti per', 'design_laboratori_italia' ); ?> <code>stato</code>:</strong>
				<span class="dli-value-badge">publish</span>
				<span class="dli-value-badge">draft</span>
				&mdash; <?php esc_html_e( 'predefinito:', 'design_laboratori_italia' ); ?> <code>publish</code>.
			</p>

			<p>
				<strong><?php esc_html_e( 'Campi booleani', 'design_laboratori_italia' ); ?> (<code>escludi_da_elenco</code>, <code>disattiva_pagina_dettaglio</code>):</strong>
				<span class="dli-value-badge">1</span> = <?php esc_html_e( 'attivo', 'design_laboratori_italia' ); ?>,
				<span class="dli-value-badge">0</span> <?php esc_html_e( 'o vuoto = disattivo (predefinito).', 'design_laboratori_italia' ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Valori multipli', 'design_laboratori_italia' ); ?> (<code>struttura</code>):</strong>
				<?php esc_html_e( 'Separare con', 'design_laboratori_italia' ); ?> <code>|</code>
				&mdash; <?php esc_html_e( 'es.:', 'design_laboratori_italia' ); ?> <code>laboratorio-abc|laboratorio-xyz</code>.
			</p>

			<p><?php esc_html_e( 'Per tipologia_persona e struttura: inserire lo slug della voce già esistente. Le voci non trovate vengono ignorate.', 'design_laboratori_italia' ); ?></p>
			<p><?php esc_html_e( 'Il campo body accetta testo semplice o HTML base (corsivo, grassetto, paragrafi).', 'design_laboratori_italia' ); ?></p>

			<a href="<?php echo esc_url( $download_url ); ?>" class="button">
				<?php esc_html_e( 'Scarica CSV di esempio', 'design_laboratori_italia' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render the "Esegui importazione" form for the Persone import.
	 *
	 * @return void
	 */
	private static function render_form_import_persone() {
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
		$exec_disabled = array();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$sel_import = isset( $_POST['dli_import_mode'] ) ? sanitize_key( wp_unslash( $_POST['dli_import_mode'] ) ) : 'upsert';
		$sel_exec   = isset( $_POST['dli_exec_mode'] )   ? sanitize_key( wp_unslash( $_POST['dli_exec_mode'] ) )   : 'dry_run';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		?>
		<div class="dli-tool-box">
			<h2><?php esc_html_e( 'Esegui importazione', 'design_laboratori_italia' ); ?></h2>
			<form method="POST"
				  action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::IMPORT_SLUG ) ); ?>"
				  enctype="multipart/form-data">
				<?php wp_nonce_field( 'dli_import_persone', 'dli_import_persone_nonce' ); ?>
				<table class="dli-form-table">
					<tr>
						<th scope="row">
							<label for="dli-csv-file"><?php esc_html_e( 'File CSV', 'design_laboratori_italia' ); ?></label>
						</th>
						<td>
							<input type="file" id="dli-csv-file" name="dli_csv_file" accept=".csv">
							<p class="dli-form-note"><?php esc_html_e( 'Dimensione massima: 2 MB. Separatore: punto e virgola (;).', 'design_laboratori_italia' ); ?></p>
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
										<?php selected( $sel_exec, $value ); ?>
										<?php disabled( in_array( $value, $exec_disabled, true ), true ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<p>
					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Esegui import persone', 'design_laboratori_italia' ); ?>
					</button>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the import report box.
	 *
	 * @param array $report Report data from handle_import_persone_post().
	 * @return void
	 */
	private static function render_report_persone( array $report ) {
		$is_dryrun  = ( 'dry_run' === $report['exec_mode'] );
		$mode_label = $is_dryrun
			? __( 'esecuzione simulata', 'design_laboratori_italia' )
			: __( 'esecuzione reale', 'design_laboratori_italia' );
		$mode_class = $is_dryrun ? 'is-dryrun' : 'is-commit';
		?>
		<div class="dli-report-box">
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
				<div class="notice notice-info inline"><p><?php echo esc_html( $report['notice'] ); ?></p></div>
			<?php endif; ?>

			<?php if ( $report['filename'] ) : ?>
				<table class="dli-report-summary">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Elaborati', 'design_laboratori_italia' ); ?></th>
							<th><?php esc_html_e( 'Creato', 'design_laboratori_italia' ); ?></th>
							<th><?php esc_html_e( 'Aggiornato', 'design_laboratori_italia' ); ?></th>
							<th><?php esc_html_e( 'Saltati', 'design_laboratori_italia' ); ?></th>
							<th class="col-errors"><?php esc_html_e( 'Errori', 'design_laboratori_italia' ); ?></th>
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
				<div class="dli-tool-box dli-log-box">
					<h3><?php esc_html_e( 'Log importazione', 'design_laboratori_italia' ); ?></h3>
					<table class="dli-log-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Riga', 'design_laboratori_italia' ); ?></th>
								<th><?php esc_html_e( 'Nome e cognome', 'design_laboratori_italia' ); ?></th>
								<th><?php esc_html_e( 'Email', 'design_laboratori_italia' ); ?></th>
								<th><?php esc_html_e( 'Stato', 'design_laboratori_italia' ); ?></th>
								<th><?php esc_html_e( 'Messaggio', 'design_laboratori_italia' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $report['log'] as $entry ) : ?>
								<tr>
									<td class="dli-log-cell-id"><?php echo esc_html( (string) $entry['row'] ); ?></td>
									<td class="dli-log-cell-name"><?php echo esc_html( $entry['name'] ?? '' ); ?></td>
									<td class="dli-log-cell-email"><?php echo esc_html( $entry['email'] ?? '' ); ?></td>
									<td class="dli-log-status-<?php echo esc_attr( strtolower( $entry['status'] ) ) ; ?>">
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
		<?php
	}

	/**
	 * Render the Utilità page.
	 *
	 * @return void
	 */
	public static function render_utils() {
		self::require_capability();
		self::render_shell(
			function() {
				self::render_reload_tool();
			}
		);
	}

	/**
	 * Render and handle the "Ricarica i dati di attivazione" tool.
	 *
	 * Capability: edit_theme_options (kept from the original implementation in activation.php).
	 * Nonce: dli_reload_theme_data (unchanged).
	 *
	 * @return void
	 */
	private static function render_reload_tool() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			echo '<p>' . esc_html__( 'Non hai i permessi per eseguire questa operazione.', 'design_laboratori_italia' ) . '</p>';
			return;
		}

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$nonce  = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'reload' === $action ) {
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'dli_reload_theme_data' ) ) {
				wp_die( esc_html__( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' ) );
			}
			dli_create_pages_on_theme_activation();
			echo '<div class="notice notice-success inline"><p>' . esc_html__( 'Dati di attivazione ricaricati con successo.', 'design_laboratori_italia' ) . '</p></div>';
		}

		$reload_url = wp_nonce_url(
			admin_url( 'admin.php?page=' . self::UTILS_SLUG . '&action=reload' ),
			'dli_reload_theme_data'
		);

		?>
		<h2><?php esc_html_e( 'Ricarica i dati di attivazione del tema', 'design_laboratori_italia' ); ?></h2>
		<p><?php esc_html_e( 'Ricrea menu, pagine e tassonomie predefiniti del tema. Usa questa funzione se la struttura del sito risulta incompleta dopo l\'installazione o un aggiornamento.', 'design_laboratori_italia' ); ?></p>
		<a href="<?php echo esc_url( $reload_url ); ?>" class="button button-primary">
			<?php esc_html_e( 'Ricarica i dati di attivazione (menu, pagine, tassonomie, etc)', 'design_laboratori_italia' ); ?>
		</a>
		<?php
	}
}

DLI_Tools_Admin::register();
