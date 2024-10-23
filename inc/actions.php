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


/**
 * customize excerpt.
 * @param $length
 *
 * @return int
 */
function dli_excerpt_length( $length ) {
		return 36;
}
add_filter( 'excerpt_length', 'dli_excerpt_length', 999 );

// rimuovo customizer.
add_action(
	'admin_menu',
	function () {
		global $submenu;
		if ( isset( $submenu[ 'themes.php' ] ) ) {
			foreach ( $submenu[ 'themes.php' ] as $index => $menu_item ) {
				foreach ( $menu_item as $value ) {
					if ( strpos( $value,'customize' ) !== false) {
							unset( $submenu['themes.php'][ $index ] );
					}
				}
			}
		}
	}
);

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
	if( ! is_super_admin( $current_user->ID ) && in_array( $screen->base, array( 'user-edit', 'user-edit-network' ) ) ) {
		// editing a user profile
		if ( is_super_admin( $profileuser->ID ) ) {
			// trying to edit a superadmin while less than a superadmin
			wp_die( __( 'You do not have permission to edit this user.' ) );
		} elseif ( ! ( is_user_member_of_blog( $profileuser->ID, get_current_blog_id() ) && is_user_member_of_blog( $current_user->ID, get_current_blog_id() ) )) {
			// editing user and edited user aren't members of the same blog.
			wp_die( __( 'You do not have permission to edit this user.' ) );
		}
	}
}
add_filter( 'admin_head', 'dli_edit_permission_check', 1, 4 );
