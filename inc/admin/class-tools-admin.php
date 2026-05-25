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
			wp_get_theme()->get( 'Version' )
		);
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
				?>
				<p><?php esc_html_e( 'Strumenti di importazione dati da file.', 'design_laboratori_italia' ); ?></p>
				<?php
			}
		);
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
