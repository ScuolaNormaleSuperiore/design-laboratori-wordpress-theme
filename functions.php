<?php
/**
 * Design Laboratori Italia functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Define
 */
require get_template_directory() . '/lab_config.php';

/**
 * Warappers functions fo Polylang.
 */
require get_template_directory() . '/inc/wrappers_polylang.php';

/**
 * Warappers functions for ACF.
 */
require get_template_directory() . '/inc/wrappers_acf.php';

/**
 * Define
 */
require get_template_directory() . '/inc/define.php';

/**
 * Extend User Taxonomy
 */
// require get_template_directory() . '/inc/extend-tax-to-user.php';

/**
 * Implement Plugin Activations Rules
 */
require get_template_directory() . '/inc/theme-dependencies.php';

/**
 * header menu walker
 */
require get_template_directory() . '/walkers/header-walker.php';

/**
 * footer menu walker
 */
require get_template_directory() . '/walkers/footer-walker.php';

/**
 * Implement CMB2 Custom Field Manager
 */
require get_template_directory() . '/inc/cmb2.php';
require get_template_directory() . '/inc/backend-template.php';


/**
 * Utils functions.
 */
require get_template_directory() . '/inc/utils.php';

/**
 * Search Utils functions.
 */
require get_template_directory() . '/inc/search-utils.php';

/**
 * Notifications functions.
 */
require get_template_directory() . '/inc/notification.php';


/**
 * Activation Hooks.
 */
require get_template_directory() . '/inc/activation.php';

/**
 * Actions & Hooks
 */
require get_template_directory() . '/inc/actions.php';

/**
 * Gutenberg editor rules
 */
require get_template_directory() . '/inc/gutenberg.php';

/**
 * Welcome page
 */
//require get_template_directory() . '/inc/welcome.php';

/**
 * Admin menu
 */
// require get_template_directory() . '/inc/menu-order.php';


/**
 * Import
 */
require get_template_directory() . '/inc/import.php';

/**
 * TCPDF
 */
require get_template_directory() . '/inc/dompdf.php';




/**
 * Check plugin dependencies.
 *
 * @return boolean
 */
function check_dependencies() {
	$result = true;
	if ( ! class_exists( 'ACF' ) ) {
		error_log( 'The plugin ACF (advanced-custom-fields) is missing, please install and activate it: https://wordpress.org/plugins/advanced-custom-fields' );
		$result = false;
	}
	if ( ! class_exists( 'Members_Plugin' ) ) {
		error_log( 'The plugin Members is missing, please install and activate it: https://wordpress.org/plugins/members' );
		$result = false;
	}
	return $result;
}

if ( check_dependencies() ) {
	// SETUP THE POST TYPES  USED BY THE THEME.

	if ( ! class_exists( 'LabManager' ) ) {
		include_once 'classes/class-labmanager.php';

		global $lab_manager;
		$lab_manager = new LabManager();
		$lab_manager->plugin_setup();
	}

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
		load_theme_textdomain( 'design_laboratori_italia', get_template_directory() . '/languages' );
		load_theme_textdomain( 'easy-appointments', get_template_directory() . '/languages' );

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
		}

		/**
		 * This theme uses wp_nav_menu().
		 * Definizione delle locations dei menu: wp-admin/nav-menus.php?action=locations.
		 */
		error_log( '@@@ HERE WE REGISTER THE MENU POSITIONS @@@' );
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

	// wp_deregister_script('jquery' );

	// Importazione dei file CSS.
	wp_enqueue_style( 'dli-wp-style', get_stylesheet_uri() ); // File style.css vuoto.
	wp_enqueue_style( 'dli-font', get_template_directory_uri() . '/assets/css/fonts.css' );
	wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/bootstrap-italia/css/bootstrap-italia.min.css' );
	wp_enqueue_style( 'dli-boostrap-italia', get_template_directory_uri() . '/assets/bootstrap-italia/css/bootstrap-italia-comuni.min.css' );
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

function console_log ($output, $msg = "log") {
    echo '<script> console.log("'. $msg .'",'. json_encode($output) .')</script>';
};

/*
 * Set post views count using post meta
 */
function set_views($post_ID) {
	$key = 'views';
	$count = get_post_meta($post_ID, $key, true); //retrieves the count

	if($count == ''){ //check if the post has ever been seen

		//set count to 0
		$count = 0;

		//just in case
		delete_post_meta($post_ID, $key);

		//set number of views to zero
		add_post_meta($post_ID, $key, '0' );

	} else { //increment number of views.
		$count++;
		update_post_meta($post_ID, $key, $count);
	}
}

// Keeps the count accurate by removing prefetching.
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function add_menu_link_class( $atts, $item, $args ) {
	if (property_exists($args, 'link_class')) {
		$atts['class'] = $args->link_class;
	}
	return $atts;
}

add_filter( 'nav_menu_link_attributes', 'add_menu_link_class', 1, 3 );
