<?php
/**
 * Design Laboratori Italia functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Define the theme parameters and configurations.
 */
require get_template_directory() . '/config-lab.php';


/**
 * Define the static pages of the site.
 */
require get_template_directory() . '/config-pages.php';

/**
 * Define the menu of the site.
 */
require get_template_directory() . '/config-menu.php';


/**
 * Warappers functions fo Polylang.
 */
require get_template_directory() . '/inc/wrappers_polylang.php';

/**
 * Warappers functions for ACF.
 */
require get_template_directory() . '/inc/wrappers_acf.php';


/**
 * Implement Plugin Activations Rules.
 */
require get_template_directory() . '/inc/theme-dependencies.php';

/**
 * Header menu walker.
 */
require get_template_directory() . '/inc/walkers/header-walker.php';

/**
 * Footer menu walker.
 */
require get_template_directory() . '/inc/walkers/footer-walker.php';

/**
 * Implement CMB2 Custom Field Manager: import the library.
 */
require get_template_directory() . '/inc/cmb2.php';

// Import the code to create the admin section: Configurazione.
require get_template_directory() . '/inc/admin/options.php';

// Import the code to create the admin section: Strumenti DLI.
require get_template_directory() . '/inc/admin/class-dli-people-file-importer.php';
require get_template_directory() . '/inc/admin/class-dli-tools-admin.php';

// Admin menu ordering and visibility.
require get_template_directory() . '/inc/menu-order.php';

/**
 * Utils functions.
 */
require get_template_directory() . '/inc/utils.php';

/**
 * Optional CAPTCHA integration.
 */
require get_template_directory() . '/inc/captcha.php';

/**
 * Sitemap — HTML helpers and XML endpoints.
 */
require get_template_directory() . '/inc/sitemap.php';

/**
 * PWA support — web app manifest and related head tags.
 */
require get_template_directory() . '/inc/pwa.php';

/**
 * Site search autocomplete — REST suggestions endpoint and front-end asset.
 */
require get_template_directory() . '/inc/search-autocomplete.php';


/**
 * Activation Hooks.
 */
require get_template_directory() . '/inc/activation.php';

/**
 * Actions & Hooks
 */
require get_template_directory() . '/inc/actions.php';


/**
 * Import the Contents Manager.
 */
if ( ! class_exists( 'DLI_ContentsManager' ) ) {
	require get_template_directory() . '/inc/classes/class-contents-manager.php';
}


// @TODO: Move all functions.php setup into ThemeManager.

// Setup the post types used by the theme.
if ( ! class_exists( 'DLI_LabManager' ) ) {
	include_once 'inc/classes/class-labmanager.php';
	global $dli_lab_manager;
	$dli_lab_manager = new DLI_LabManager();
	$dli_lab_manager->plugin_setup();
}



if ( ! function_exists( 'dli_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function dli_setup() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/**
		 * This theme uses wp_nav_menu().
		 * Definizione delle locations dei menu: wp-admin/nav-menus.php?action=locations.
		 */
		register_nav_menus(
			array(
				'menu-lab'          => esc_html__( 'Menu principale di link a sinistra', 'design_laboratori_italia' ),
				'menu-right'        => esc_html__( 'Menu secondario multilevel a destra', 'design_laboratori_italia' ),
				'menu-header-right' => esc_html__( 'Menu header di link in alto a destra', 'design_laboratori_italia' ),
				'menu-links'        => esc_html__( 'Menu link utili - footer', 'design_laboratori_italia' ),
				'menu-footer'       => esc_html__( 'Menu a piè di pagina di link - footer', 'design_laboratori_italia' ),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'dli_setup' );

/**
 * Enqueue scripts and styles.
 */
function dli_scripts() {
	$theme_version = wp_get_theme()->get( 'Version' );

	/*
	 * Cache-busting per file, non per il tema intero: $theme_version resta
	 * invariato tra un aggiornamento e l'altro di bootstrap-italia-custom.
	 * min.css/custom-colors.css/main.css (dichiarato in style.css, non
	 * legato a queste modifiche), quindi il browser può continuare a
	 * servire dalla cache la versione precedente anche dopo un refresh
	 * normale, con lo stesso "?ver=" nell'URL. Per questi 3 file, versione
	 * dedotta da filemtime() (data di modifica reale), come già fatto per
	 * gli asset v3 sincronizzati.
	 */
	$dli_asset_version = function ( $relative_path ) use ( $theme_version ) {
		$absolute_path = get_template_directory() . $relative_path;
		$mtime         = file_exists( $absolute_path ) ? filemtime( $absolute_path ) : false;
		return $mtime ? (string) $mtime : $theme_version;
	};

	// Load CSS files.
	wp_enqueue_style( 'dli-wp-style', get_stylesheet_uri(), array(), $theme_version ); // Empty style.css file.
	wp_enqueue_style( 'dli-font', get_template_directory_uri() . '/assets/css/fonts.css', array(), $theme_version );

	if ( 'custom' === dli_get_option( 'choose_style', 'setup' ) ) {
		wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/css/bootstrap-italia-custom.min.css', array(), $dli_asset_version( '/assets/css/bootstrap-italia-custom.min.css' ) );
		wp_enqueue_style( 'dli-custom-css', get_template_directory_uri() . '/assets/css/custom-colors.css', array(), $dli_asset_version( '/assets/css/custom-colors.css' ) );
	} else {
		wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/bootstrap-italia/css/bootstrap-italia.min.css', array(), $dli_asset_version( '/assets/bootstrap-italia/css/bootstrap-italia.min.css' ) );
	}
	wp_enqueue_style( 'dli-main', get_template_directory_uri() . '/assets/css/main.css', array(), $dli_asset_version( '/assets/css/main.css' ) );

	// Load JavaScript files.
	wp_enqueue_script( 'dli-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), $theme_version, false );
	wp_enqueue_script( 'dli-modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.js', array(), $theme_version, false );
	wp_enqueue_script( 'dli-boostrap-italia-js', get_template_directory_uri() . '/assets/bootstrap-italia/js/bootstrap-italia.bundle.min.js', array(), $theme_version, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'dli_scripts' );

/**
 * Add CSS classes to menu links when provided by menu args.
 *
 * @param array    $atts Link attributes.
 * @param \WP_Post $item Menu item.
 * @param object   $args Menu arguments.
 * @return array
 */
function dli_add_menu_link_class( $atts, $item, $args ) {
	if ( property_exists( $args, 'link_class' ) ) {
		$atts['class'] = $args->link_class;
	}
	return $atts;
}

add_filter( 'nav_menu_link_attributes', 'dli_add_menu_link_class', 1, 3 );

/**
 * Harden the Polylang language cookie when the installed plugin supports it.
 *
 * Polylang 3.6+ applies this filter before setting its language-selection
 * cookie. Registering it on older versions is harmless because the hook is
 * never fired.
 *
 * @param mixed $cookie_args Cookie arguments supplied by Polylang (an array when the filter is well-formed).
 * @return mixed The hardened arguments, or the unchanged value when it is not an array.
 */
function dli_harden_polylang_cookie( $cookie_args ) {
	if ( ! is_array( $cookie_args ) ) {
		return $cookie_args;
	}

	$cookie_args['httponly'] = true;
	$cookie_args['samesite'] = 'Lax';
	$cookie_args['secure']   = is_ssl();

	return $cookie_args;
}
add_filter( 'pll_cookie_args', 'dli_harden_polylang_cookie' );

/**
 * Prevent unauthenticated REST reads of publications without detail pages.
 *
 * The REST route remains available to users who can edit posts so that the
 * block editor continues to work. The public archive uses its own WP_Query
 * and is not affected by this authorization check.
 *
 * @param mixed          $result  Response to replace, or null to continue.
 * @param WP_REST_Server $server  REST server instance.
 * @param mixed          $request Current REST request (a WP_REST_Request when WordPress calls the filter).
 * @return mixed|WP_Error
 */
function dli_restrict_publication_rest_access( $result, $server, $request ) {
	if ( DLI_PUBLICATION_HAS_DETAIL || ! ( $request instanceof WP_REST_Request ) ) {
		return $result;
	}

	$route      = untrailingslashit( $request->get_route() );
	$route_base = '/wp/v2/' . PUBLICATION_POST_TYPE;
	if ( $route_base !== $route && 0 !== strpos( $route, $route_base . '/' ) ) {
		return $result;
	}

	if ( current_user_can( 'edit_posts' ) ) {
		return $result;
	}

	return new WP_Error(
		'rest_forbidden',
		__( 'Non hai i permessi per accedere a questa risorsa.', 'design_laboratori_italia' ),
		array( 'status' => rest_authorization_required_code() )
	);
}
add_filter( 'rest_pre_dispatch', 'dli_restrict_publication_rest_access', 10, 3 );

/**
 * Allow SVG upload MIME type.
 *
 * @param array $upload_mimes Allowed upload MIME types.
 * @return array
 */
function dli_enable_svg_upload( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'dli_enable_svg_upload', 10, 1 );

/**
 * Print pagination dropdown inline script in footer when needed.
 *
 * @return void
 */
function dli_load_pagination_script() {
	if ( is_page_template() || is_singular() ) {
		?>
		<script>
			if (document.querySelector('.dropdown-menu.dli-pagination-dropdown')) {

				// Disabilita il comportamento di default del click.
				var dropdownLinks = document.querySelectorAll('.dropdown-menu.dli-pagination-dropdown a');
				dropdownLinks.forEach(function(link) {
					link.addEventListener('click', function(event) {
						event.preventDefault();
						// Rimuovi la classe 'active' da tutti i link.
						dropdownLinks.forEach(function(item) {
							item.classList.remove('active');
						});
						// Aggiungi la classe 'active' al link cliccato.
						link.classList.add('active');
					});
				});

				// Ricarica pagina con il valore per_page selezionato.
				var pagerDropDown = document.getElementById('pagerChanger');
				if( pagerDropDown ){
					pagerDropDown.addEventListener('hidden.bs.dropdown', function (event) {
						var selectedItem = document.querySelector('.dropdown-menu.dli-pagination-dropdown .active');
						if (selectedItem) {
							// Recupera il valore dell'attributo 'data-perpage'.
							var perPageValue = selectedItem.getAttribute('data-perpage');
							// Ottiene l'URL corrente e i parametri GET.
							var currentUrl = new URL(window.location.href);
							var params = currentUrl.searchParams;
							const oldPerPage = params.get('per_page');
							// Per evitare incongruenze,
							// quando si cambia numero di elementi per pagina,
							// si riparte dalla pagina numero 1.
							if (perPageValue != oldPerPage){
								params.set('paged', 1);
							}
							// Aggiunge o aggiorna il parametro per_page.
							params.set('per_page', perPageValue);
							// Aggiorna l'URL e ricarica la pagina.
							window.location.href = currentUrl.toString();
						}
					});
				}
			}
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'dli_load_pagination_script' );
