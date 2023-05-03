<?php

/**
 * disable all comments
 */
function dli_disable_all_comments() {
	// Turn off comments
	if( '' != get_option( 'default_comment_status' ) ) {
		update_option( 'default_comment_status', '' );
	}
}
add_action( 'after_setup_theme', 'dli_disable_all_comments' );

/**
 * Add css admin style: TAB di Configurazione laterale.
 */

function dli_admin_css_load() {
		wp_enqueue_style( 'style-admin-css', get_stylesheet_directory_uri() . '/inc/admin-css/style-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'dli_admin_css_load' );


// /**
//  * filter for search
//  */
// function dsi_search_filters( $query ) {
// 		if ( ! is_admin() && $query->is_main_query() && $query->is_search ) {
// 				$allowed_types = array( "any", "laboratory", "news", "education", "service" );
// 				if ( isset( $_GET["type"] ) && in_array( $_GET["type"], $allowed_types ) ) {
// 						$type = $_GET["type"];
// 						$post_types = dli_get_post_types_grouped( $type );
// 						$query->set( 'post_type', $post_types );
// 				}
// 				if ( isset( $_GET["post_types"] ) ) {
// 						$query->set( 'post_type', $_GET["post_types"] );
// 				}
// 				if ( isset( $_GET["post_terms"] ) ) {
// 						$query->set( 'tag__in', $_GET["post_terms"]);
// 				}
// 				// associazione tra types e post_type
// 		}
// }
// add_action( 'pre_get_posts', 'dsi_search_filters' );

/**
 * customize excerpt
 * @param $length
 *
 * @return int
 */
function dli_excerpt_length( $length ) {
		return 36;
}
add_filter( 'excerpt_length', 'dli_excerpt_length', 999 );


// /**
//  * filter for events
//  *  controllo le query sugli eventi e le modifico per estrarre gli eventi futuri
//  */
// function dli_eventi_filters( $query ) {
// 		if ( ! is_admin() && $query->is_main_query() && is_post_type_archive("evento") ) {
// 				if(isset($_GET["date"]) && ($_GET["date"] != "")){
// 						$arrdate = explode("-", $_GET["date"]);
// 						if(is_array($arrdate) && count($arrdate) != 3) return;
// 						$newdate = $arrdate[1]."/".$arrdate[0]."/".$arrdate[2];
// 						$date = strtotime($newdate);
// 						$date_begin = strtotime($newdate ." 00:00:00" );
// 						$date_end = strtotime($newdate ." 23:59:59");
// 						$query->set( 'meta_query', array(
// 								array(
// 										'key' => '_dsi_evento_timestamp_inizio',
// 										'value' => $date_end,
// 										'compare' => '<=',
// 										'type' => 'numeric'
// 								),
// 								array(
// 										'key' => '_dsi_evento_timestamp_fine',
// 										'value' => $date_begin,
// 										'compare' => '>=',
// 										'type' => 'numeric'
// 								)
// 						));
// 				}else if(isset($_GET["archive"]) && ($_GET["archive"] == "true")){
// 						$query->set('meta_key', '_dsi_evento_timestamp_inizio' );
// 						$query->set('orderby', array('meta_value' => 'DESC', 'date' => 'DESC'));
// 						$query->set( 'meta_query', array(
// 								array(
// 										'key' => '_dsi_evento_timestamp_inizio'
// 								),
// 								array(
// 										'key' => '_dsi_evento_timestamp_fine',
// 										'value' => time(),
// 										'compare' => '<=',
// 										'type' => 'numeric'
// 								)
// 						));
// 				}else{
// 						$query->set('meta_key', '_dsi_evento_timestamp_inizio' );
// 						$query->set('orderby', array('meta_value' => 'ASC', 'date' => 'ASC'));
// 						$query->set( 'meta_query', array(
// 								array(
// 										'key' => '_dsi_evento_timestamp_inizio'
// 								),
// 								array(
// 										'key' => '_dsi_evento_timestamp_fine',
// 										'value' => time(),
// 										'compare' => '>=',
// 										'type' => 'numeric'
// 								)
// 						));

// 				}
// 		}
// }
// add_action( 'pre_get_posts', 'dli_eventi_filters' );





// /**
//  * Personalizzo archive title
//  */
// add_filter( 'get_the_archive_title', function ($title) {
// global $wp_query;
// 		if ( is_tag() ) {
// 				$title = __("Argomento", 'design_laboratori_italia').": ".single_cat_title( '', false );
// 		} elseif ( is_tag() ) {
// 				$title = single_tag_title( '', false );
// 		} elseif ( is_tax("tipologia-articolo") ) {

// 				$title = single_term_title('', false);
// 				/*    if($title == "Articoli"){
// 								$title = "Presentazione";
// 						}*/
// 		} elseif ( is_tax("tipologia-documento") ) {
// 				$title = single_term_title('', false);
// 		} elseif ( is_tax("percorsi-di-studio") ) {
// 				//  $title = post_type_archive_title('', false)." ";
// 				//$title .= single_term_title('', false);
// 				$title = single_term_title('', false);
// 		} elseif ( is_post_type_archive("evento") ) {
// 				$title = __("Calendario", 'design_laboratori_italia');
// 		}elseif ( is_tax("tipologia-circolare") ) {
// 				$title = single_term_title('', false);
// 		} elseif ( is_tax("tipologia-progetto") ) {
// 				$title = single_term_title('', false);
// 		}elseif ( is_post_type_archive("luogo") ) {
// 				$title = __("I luoghi della scuola", 'design_laboratori_italia');
// 		} elseif ( is_post_type_archive("struttura") ) {
// 				$title = __("Organizzazione", 'design_laboratori_italia');
// 		} elseif ( is_post_type_archive("evento") ) {
// 				$title = __("Eventi", 'design_laboratori_italia');
// 				if(isset($_GET["date"]) && $_GET["date"] != ""){
// 						$title .= " del ".$_GET["date"];
// 				}
// 				if(isset($_GET["archive"]) && $_GET["archive"] == "true"){
// 						$title .= " archiviati  ";
// 				}
// 		} elseif ( is_post_type_archive() ) {
// 				$title = post_type_archive_title('', false);
// 		}

// 		$title = dsi_pluralize_string($title);
// 		return $title;

// });


/**
 * fix plugin amministrazione aperta
 */
// function dsi_ammap_getJs(){
// 		wp_deregister_script('ammap_functions');
// 		wp_dequeue_script('ammap_functions');
// 		// wp_register_script( 'ammap_functions', plugins_url('italia-amministrazione-aperta/js/ammap.js'));
// 		wp_register_script( 'ammap_functions', get_template_directory_uri() . '/assets/js/main.js' );
// 		wp_enqueue_script( 'ammap_functions');
// }
// add_filter('admin_footer', 'dsi_ammap_getJs', 100);


// /**
//  * Fix plugin bandi
//  */

// add_action( 'after_setup_theme', 'dsi_replace_bandi_shortcode' );

// function dsi_replace_bandi_shortcode() {
// 		remove_shortcode( 'avcp' );
// 		remove_shortcode( 'anac' );
// 		remove_shortcode( 'gare' );
// 		add_shortcode( 'avcp', 'dsi_bandi_shortcode' );
// 		add_shortcode( 'anac', 'dsi_bandi_shortcode' );
// 		add_shortcode( 'gare', 'dsi_bandi_shortcode' );

// }


// /** add responsive class to table **/

// function dsi_bootstrap_responsive_table( $content ) {
// 		$content = str_replace( ['<table', '</table>'], ['<div class="table-responsive"><table class="table  table-striped table-bordered table-hover" ', '</table></div>'], $content );

// 		return $content;
// }
// add_filter( 'the_content', 'dsi_bootstrap_responsive_table' );




// /**
//  * Admin header customization
//  *
//  */
// function dli_admin_bar_customize_header() {
// 		global $wp_admin_bar;

// 		if ( current_user_can( 'read' ) ) {
// 				$about_url = self_admin_url( 'about.php' );
// 		} elseif ( is_multisite() ) {
// 				$about_url = get_dashboard_url( get_current_user_id(), 'about.php' );
// 		} else {
// 				$about_url = false;
// 		}
// 		$wp_admin_bar->add_menu(
// 				array(
// 						'id'     => 'design-scuole',
// 						'title' => '<span class="dsi-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 92 74"><g fill="#06C"><path d="M31.799 71.9V15.7h15.1V72h-15.1zM91.099 28.5h-13.8v23.1c0 2.3.1 3.8.2 4.8.1.9.5 1.7 1.2 2.4s1.8 1 3.3 1l8.6-.2.7 12c-5 1.1-8.9 1.7-11.5 1.7-6.8 0-11.4-1.5-13.8-4.6-2.5-3-3.7-8.6-3.7-16.8V0h15.1v15.6h13.8v12.9zM9.099 32.8c-2.6 0-4.8-.9-6.5-2.7s-2.6-4-2.6-6.6.9-4.8 2.5-6.6c1.7-1.8 3.9-2.6 6.5-2.6s4.8.9 6.5 2.7 2.5 4 2.5 6.7-.8 4.8-2.5 6.6c-1.6 1.6-3.7 2.5-6.4 2.5z"></path></g></svg></span><span class="screen-reader-text">' . __( 'About Design Laboratori' ) . '</span>',
// 						'href'   => '#'
// 				)
// 		);
// 		$wp_admin_bar->add_group(
// 				array(
// 						'parent' => 'design-scuole',
// 						'id'     => 'design-scuole-external',
// 						'meta'   => array(
// 								'class' => 'ab-sub-secondary',
// 						),
// 				)
// 		);
// 		$wp_admin_bar->add_menu(
// 				array(
// 						'parent' => 'design-scuole-external',
// 						'id'     => 'dsi-about-design',
// 						'title'  => __( 'About Design Laboratori' ),
// 						'href'   => 'https://designers.italia.it/progetti/siti-web-scuole/',
// 						'meta'  => array( 'target' => '_blank')
// 				)
// 		);
// 		$wp_admin_bar->add_menu(
// 				array(
// 						'parent' => 'design-scuole',
// 						'id'     => 'dsi-about-wp',
// 						'title'  => __( 'About WordPress' ),
// 						'href'   => $about_url,
// 				)
// 		);
// 		$wp_admin_bar->add_menu(
// 				array(
// 						'parent' => 'design-scuole',
// 						'id'     => 'dsi-github',
// 						'title'  => __( 'Design su GitHub' ),
// 						'href'   => "https://github.com/italia/design-scuole-wordpress-theme",
// 						'meta'  => array( 'target' => '_blank')
// 				)
// 		);
// 		if(current_user_can("manage_options")){
// 				$wp_admin_bar->add_menu(
// 						array(
// 								'id'     => 'design-scuole-conf',
// 								'title' => __( '<div class="dashicons-before dashicons-admin-tools" style="float:left; padding-top: 6px; padding-right:4px;"> </div>Configurazione', 'design_laboratori_italia' ),
// 								'href'   => admin_url("admin.php?page=homepage")
// 						)
// 				);
// 		}
// }
// add_action( 'admin_bar_menu', 'dli_admin_bar_customize_header', -10 );

// add_action( 'wp_before_admin_bar_render', 'dsi_admin_bar_before_customize_header', -10 );
// function dsi_admin_bar_before_customize_header(){
// 		global $wp_admin_bar;
// 		$wp_admin_bar->remove_menu("wp-logo");
// }

// rimuovo customizer
add_action( 'admin_menu', function () {
	global $submenu;
	if ( isset( $submenu[ 'themes.php' ] ) ) {
			foreach ( $submenu[ 'themes.php' ] as $index => $menu_item ) {
				foreach ($menu_item as $value) {
					if (strpos($value,'customize') !== false) {
							unset( $submenu[ 'themes.php' ][ $index ] );
					}
				}
			}
	}
});

add_action( 'wp_before_admin_bar_render', 'dli_before_admin_bar_render' );
function dli_before_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('customize');
}

remove_all_filters( 'enable_edit_any_user_configuration' );
add_filter( 'enable_edit_any_user_configuration', '__return_true');

/**
 * Checks that both the editing user and the user being edited are
 * members of the blog and prevents the super admin being edited.
 */
function dli_edit_permission_check() {
	global $current_user, $profileuser;
	$screen = get_current_screen();
	$current_user = wp_get_current_user();
	if( ! is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) { // editing a user profile
		if ( is_super_admin( $profileuser->ID ) ) { // trying to edit a superadmin while less than a superadmin
				wp_die( __( 'You do not have permission to edit this user.' ) );
		} elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) { // editing user and edited user aren't members of the same blog
				wp_die( __( 'You do not have permission to edit this user.' ) );
		}
	}
}
add_filter( 'admin_head', 'dli_edit_permission_check', 1, 4 );



// /**
//  * filtri avcp per i path di salvataggio dei file, per fixare problema multisite
//  */

// add_filter( 'anac_filter_basexmlpath', function( $string ) { // Base PATH
// 		if(is_multisite()){
// 				$upload_dir = wp_get_upload_dir();
// 	return $upload_dir["basedir"] . '/avcp/';
// 		}else{
// 	return $string;
// 		}

// }, 10, 3 );

// add_filter( 'anac_filter_basexmlurl', function( $string ) { // Base URL

// 		if(is_multisite()){
// 				$upload_dir = wp_get_upload_dir();
// 	return $upload_dir["baseurl"] . '/avcp/';
// 		}else{
// 	return $string;
// 		}

// }, 10, 3 );

