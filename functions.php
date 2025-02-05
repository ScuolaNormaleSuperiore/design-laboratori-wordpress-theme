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
require get_template_directory() . '/config_lab.php';


/**
 * Define the static pages of the site.
 */
require get_template_directory() . '/config_pages.php';

/**
 * Define the menu of the site.
 */
require get_template_directory() . '/config_menu.php';


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

/**
 * Utils functions.
 */
require get_template_directory() . '/inc/utils.php';

/**
 * Search Utils functions.
 */
require get_template_directory() . '/inc/search-utils.php';


/**
 * Activation Hooks.
 */
require get_template_directory() . '/inc/activation.php';

/**
 * Actions & Hooks
 */
require get_template_directory() . '/inc/actions.php';


// /**
//  * TCPDF
//  */
// require get_template_directory() . '/inc/dompdf.php';


/**
 * Import the Contents Manager.
 */
if ( ! class_exists( 'DLI_ContentsManager' ) ) {
	require get_template_directory() . '/inc/classes/class-contents-manager.php';
}


// @TODO: Spostare nel ThemeManager tutte le configurazioni fatte nel file functions.php.

////// SETUP THE POST TYPES  USED BY THE THEME. //////
if ( ! class_exists( 'DLI_LabManager' ) ) {
	include_once 'inc/classes/class-labmanager.php';
	global $lab_manager;
	$lab_manager = new DLI_LabManager();
	$lab_manager->plugin_setup();
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
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Design Laboratori Italia, use a find and replace
		 * to change 'design_laboratori_italia' to the name of your theme in all the template files.
		 */
		// load_theme_textdomain( 'design_laboratori_italia', get_template_directory() . '/languages' );
		// load_theme_textdomain( 'easy-appointments', get_template_directory() . '/languages' );

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

		// Image size.
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'item-thumb', 280, 280 , true );
			add_image_size( 'item-gallery', 730, 485 , true );
			add_image_size( 'item-hero-event', 418, 130 , true );
			add_image_size( 'item-card-list', 416, 232 , true );
			add_image_size( 'item-carousel', 592, 334 , true );
			add_image_size( 'banner', 600, 250 , false );
			add_image_size( 'page-body', 860, 238 , true );
		}

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

	// Importazione dei file CSS.
	wp_enqueue_style( 'dli-wp-style', get_stylesheet_uri() ); // File style.css vuoto.
	wp_enqueue_style( 'dli-font', get_template_directory_uri() . '/assets/css/fonts.css' );

	if ( 'custom' === dli_get_option( 'choose_style', 'setup' ) ) {
		wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/css/bootstrap-italia-custom.min.css' );
		wp_enqueue_style( 'dli-custom-css', get_template_directory_uri() . '/assets/css/custom-colors.css' );
	} else {
		wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/bootstrap-italia/css/bootstrap-italia.min.css' );
	}
	wp_enqueue_style( 'dli-main', get_template_directory_uri() . '/assets/css/main.css' );

	// Importazione dei file JAVASCRIPT.
	wp_enqueue_script( 'dli-main-js', get_template_directory_uri() . '/assets/js/main.js' );
	wp_enqueue_script( 'dli-modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.js' );
	wp_enqueue_script( 'dli-boostrap-italia-js', get_template_directory_uri() . '/assets/bootstrap-italia/js/bootstrap-italia.bundle.min.js', array(), false, true);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}

add_action( 'wp_enqueue_scripts', 'dli_scripts' );

function add_menu_link_class( $atts, $item, $args ) {
	if ( property_exists( $args, 'link_class' ) ) {
		$atts['class'] = $args->link_class;
	}
	return $atts;
}

add_filter( 'nav_menu_link_attributes', 'add_menu_link_class', 1, 3 );

function enable_svg_upload( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'enable_svg_upload', 10, 1 );

function load_pagination_script(){
	if (is_page_template() || is_singular()) {
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
add_action('wp_footer', 'load_pagination_script');
