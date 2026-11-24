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
	 * Hook suffixes that WordPress gives to the pages of the menu.
	 *
	 * The suffix of a submenu page is made from the title of the menu, which is translated, so it cannot be built from the slug.
	 *
	 * @var string[]
	 */
	private static $page_hooks = array();

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
		self::$page_hooks[] = add_menu_page(
			esc_html__( 'Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Strumenti DLI', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::MENU_SLUG,
			array( __CLASS__, 'render_panoramica' ),
			'dashicons-hammer',
			self::MENU_POSITION
		);

		// Rename the auto-created first submenu item to "Panoramica".
		self::$page_hooks[] = add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Panoramica — Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Panoramica', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::MENU_SLUG,
			array( __CLASS__, 'render_panoramica' )
		);

		self::$page_hooks[] = add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Importa — Strumenti DLI', 'design_laboratori_italia' ),
			esc_html__( 'Importa', 'design_laboratori_italia' ),
			DLI_EDIT_CONFIG_PERMISSION,
			self::IMPORT_SLUG,
			array( __CLASS__, 'render_importa' )
		);

		self::$page_hooks[] = add_submenu_page(
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
		if ( ! in_array( $hook_suffix, self::$page_hooks, true ) ) {
			return;
		}

		$dli_tools_css_path = get_template_directory() . '/assets/css/tools-admin.css';
		$dli_tools_css_ver  = file_exists( $dli_tools_css_path ) ? (string) filemtime( $dli_tools_css_path ) : wp_get_theme()->get( 'Version' );
		wp_enqueue_style(
			'dli-tools-admin',
			get_template_directory_uri() . '/assets/css/tools-admin.css',
			array(),
			$dli_tools_css_ver
		);
	}

	/**
	 * Handle early GET actions that must fire before any output is sent.
	 *
	 * Currently handles: the downloads of the example files of the imports of the pages and of the people.
	 *
	 * @return void
	 */
	public static function handle_early_actions() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$page   = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( self::IMPORT_SLUG !== $page ) {
			return;
		}
		if ( 'download-pagine' === $action ) {
			DLI_Page_Import_Admin::handle_download();
		} elseif ( DLI_People_Import_Admin::DOWNLOAD_ACTION === $action ) {
			DLI_People_Import_Admin::handle_download();
		}
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
			function () {
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
			function () {
				$import_type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'persone'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				echo '<h2 class="dli-import-section-title">' . esc_html__( 'Strumenti di importazione', 'design_laboratori_italia' ) . '</h2>';

				self::render_import_subnav( $import_type );

				if ( 'persone' === $import_type ) {
					DLI_People_Import_Admin::render();
				} elseif ( 'pagine' === $import_type ) {
					DLI_Page_Import_Admin::render();
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
			'pagine'  => __( 'Pagine', 'design_laboratori_italia' ),
		);

		echo '<nav class="dli-import-subnav" aria-label="' . esc_attr__( 'Tipi di importazione', 'design_laboratori_italia' ) . '">';
		foreach ( $types as $slug => $label ) {
			$url   = esc_url( admin_url( 'admin.php?page=' . self::IMPORT_SLUG . '&type=' . $slug ) );
			$class = ( $slug === $current_type ) ? ' class="current"' : '';
			echo '<a' . $class . ' href="' . $url . '">' . esc_html( $label ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</nav>';
	}

	/**
	 * Render the Utilità page.
	 *
	 * @return void
	 */
	public static function render_utils() {
		self::require_capability();
		self::render_shell(
			function () {
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

		$reload_url = wp_nonce_url(
			admin_url( 'admin.php?page=' . self::UTILS_SLUG . '&action=reload' ),
			'dli_reload_theme_data'
		);
		?>
		<div class="postbox dli-tool-panel">
			<div class="inside">
				<h2><?php esc_html_e( 'Ricarica i dati di attivazione del tema', 'design_laboratori_italia' ); ?></h2>
				<?php if ( 'reload' === $action ) : ?>
					<?php
					if ( ! $nonce || ! wp_verify_nonce( $nonce, 'dli_reload_theme_data' ) ) {
						wp_die( esc_html__( 'Richiesta non valida: nonce mancante o non valido.', 'design_laboratori_italia' ) );
					}
					dli_create_pages_on_theme_activation();
					?>
					<div class="notice notice-success inline">
						<p><?php esc_html_e( 'Dati di attivazione ricaricati con successo.', 'design_laboratori_italia' ); ?></p>
					</div>
				<?php endif; ?>
				<p class="dli-section-lead"><?php esc_html_e( 'Ricrea menu, pagine e tassonomie predefiniti del tema. Usa questa funzione se la struttura del sito risulta incompleta dopo l\'installazione o un aggiornamento.', 'design_laboratori_italia' ); ?></p>
				<p class="dli-section-actions">
					<a href="<?php echo esc_url( $reload_url ); ?>" class="button button-primary">
						<?php esc_html_e( 'Ricarica i dati di attivazione (menu, pagine, tassonomie, etc)', 'design_laboratori_italia' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}
}

DLI_Tools_Admin::register();
