<?php
/**
 * Reserved area: the tab "Area riservata" of the configuration, its settings and the behaviour of the two roles of
 * the reserved area (pending user and reserved area user).
 *
 * Who sees a reserved content is decided by a restriction plugin (Groups); the theme only manages the roles, the
 * access through single sign-on and the page from which people log in.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * Register the tab "Area riservata" of the configuration, for the administrator only.
 *
 * The tab is registered after the other ones, so it is the last of the group. The two settings that depend on the
 * main switch carry the class `dli-reserved-area-dependent`, which the script of the page disables while the main
 * switch is off; the server ignores them in that case anyway.
 *
 * @return void
 */
function dli_register_reserved_area_options() {
	if ( ! current_user_can( DLI_ADMIN_EDIT_CONFIG_PERMISSION ) ) {
		return;
	}

	$args = array(
		'id'           => 'dli_options_area_riservata',
		'title'        => esc_html__( 'Area riservata', 'design_laboratori_italia' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => DLI_RESERVED_AREA_OPTION_KEY,
		'capability'   => DLI_ADMIN_EDIT_CONFIG_PERMISSION,
		'parent_slug'  => 'dli_options',
		'tab_group'    => 'dli_options',
		'tab_title'    => __( 'Area riservata', 'design_laboratori_italia' ),
		'display_cb'   => 'dli_options_display_with_tabs',
	);

	$box = new_cmb2_box( $args );

	$box->add_field(
		array(
			'id'   => 'reserved_area_title',
			'name' => __( 'Area riservata', 'design_laboratori_italia' ),
			'desc' => __( 'Accesso ai contenuti riservati con il single sign-on (WP SAML Auth) e i gruppi (Groups). Chi vede un contenuto lo decide il plugin Groups.', 'design_laboratori_italia' ),
			'type' => 'title',
		)
	);

	$box->add_field(
		array(
			'id'      => 'reserved_area_enabled',
			'name'    => __( 'Gestione area riservata', 'design_laboratori_italia' ),
			'desc'    => __( 'Attiva le funzioni del tema per gli utenti dell\'area riservata (ruoli «Utente in attesa» e «Utente area riservata»).', 'design_laboratori_italia' ),
			'type'    => 'radio_inline',
			'default' => 'false',
			'options' => array(
				'true'  => __( 'Sì', 'design_laboratori_italia' ),
				'false' => __( 'No', 'design_laboratori_italia' ),
			),
		)
	);

	$box->add_field(
		array(
			'id'               => 'reserved_area_page_id',
			'name'             => __( 'Pagina Area riservata', 'design_laboratori_italia' ),
			'desc'             => __( 'Pagina pubblica da cui si accede all\'area riservata: riceve il pulsante di accesso ed è la pagina in cui arrivano gli utenti dell\'area riservata dopo il login.', 'design_laboratori_italia' ),
			'type'             => 'select',
			'show_option_none' => __( 'Nessuna (home del sito)', 'design_laboratori_italia' ),
			'options_cb'       => 'dli_get_pages_for_select',
			'sanitization_cb'  => 'absint',
			'classes'          => 'dli-reserved-area-dependent',
		)
	);

	$box->add_field(
		array(
			'id'      => 'reserved_area_saml_only',
			'name'    => __( 'Accesso solo tramite SAML', 'design_laboratori_italia' ),
			'desc'    => __( 'Gli utenti con i ruoli dell\'area riservata possono entrare solo con il single sign-on: il login con password e il recupero della password sono disattivati. Gli altri utenti non sono interessati.', 'design_laboratori_italia' ),
			'type'    => 'radio_inline',
			'default' => 'false',
			'options' => array(
				'true'  => __( 'Sì', 'design_laboratori_italia' ),
				'false' => __( 'No', 'design_laboratori_italia' ),
			),
			'classes' => 'dli-reserved-area-dependent',
		)
	);
}
add_action( 'cmb2_admin_init', 'dli_register_reserved_area_options', 20 );

/**
 * Whether the reserved area is switched on in the configuration.
 *
 * @return bool
 */
function dli_is_reserved_area_enabled() {
	return 'true' === dli_get_option( 'reserved_area_enabled', DLI_RESERVED_AREA_OPTION_KEY );
}

/**
 * Whether the users of the reserved area can log in only through single sign-on.
 *
 * @return bool False when the reserved area is switched off.
 */
function dli_is_reserved_area_saml_only() {
	return dli_is_reserved_area_enabled()
		&& 'true' === dli_get_option( 'reserved_area_saml_only', DLI_RESERVED_AREA_OPTION_KEY );
}

/**
 * The page "Area riservata" chosen in the configuration.
 *
 * @return int Page ID, in the default language; 0 when none is chosen or the reserved area is switched off.
 */
function dli_get_reserved_area_page_id() {
	if ( ! dli_is_reserved_area_enabled() ) {
		return 0;
	}
	return absint( dli_get_option( 'reserved_area_page_id', DLI_RESERVED_AREA_OPTION_KEY ) );
}

/**
 * Choose a page as the page "Area riservata" of the configuration, when no page is chosen yet.
 *
 * Called by the reload of the activation data only when it has just made the page "Area riservata", also with the
 * reserved area switched off, so that the page is ready when an administrator switches it on. A page already chosen
 * is never replaced, and a later reload does not touch the field, so "Nessuna" chosen on purpose is kept. A page that
 * is not published is never chosen.
 *
 * @param int $page_id Page ID.
 * @return void
 */
function dli_select_reserved_area_page( $page_id ) {
	if ( absint( dli_get_option( 'reserved_area_page_id', DLI_RESERVED_AREA_OPTION_KEY ) ) > 0 ) {
		return;
	}
	if ( 'publish' !== get_post_status( $page_id ) ) {
		return;
	}
	cmb2_update_option( DLI_RESERVED_AREA_OPTION_KEY, 'reserved_area_page_id', (string) absint( $page_id ) );
}

/**
 * Promote a pending user who enters a group to reserved area user.
 *
 * Runs on the action `groups_created_user_group` of the Groups plugin, fired after a new membership is saved. The
 * group "Registered", which Groups gives to every new user, does not promote: otherwise every pending user would
 * be approved as soon as created. Only the pending role is replaced; any other role of the user is kept.
 *
 * @param int $user_id  User ID.
 * @param int $group_id Group ID.
 * @return void
 */
function dli_promote_pending_user_on_group_join( $user_id, $group_id ) {
	if ( ! dli_is_reserved_area_enabled() || ! class_exists( 'Groups_Group' ) ) {
		return;
	}

	$group = Groups_Group::read( (int) $group_id );
	if ( ! $group || ! isset( $group->name ) || 'Registered' === $group->name ) {
		return;
	}

	$user = get_userdata( (int) $user_id );
	if ( ! $user || ! in_array( DLI_PENDING_USER_ROLE_SLUG, (array) $user->roles, true ) ) {
		return;
	}

	$user->remove_role( DLI_PENDING_USER_ROLE_SLUG );
	$user->add_role( DLI_RESERVED_AREA_USER_ROLE_SLUG );
}
add_action( 'groups_created_user_group', 'dli_promote_pending_user_on_group_join', 10, 2 );

/**
 * Give a neutral slug (`utente-<ID>`) to a user created by WP SAML Auth.
 *
 * The username of these users is their email, and WordPress builds the slug (`user_nicename`) from it: the slug is
 * public, in the address of the author and in the REST API. Runs on the action
 * `wp_saml_auth_new_user_authenticated`, fired once after the user is created; it does not depend on the switch of
 * the reserved area. Username and email do not change.
 *
 * @param WP_User|mixed $user The new user.
 * @return void
 */
function dli_neutral_slug_for_saml_user( $user ) {
	if ( ! $user instanceof WP_User ) {
		return;
	}
	wp_update_user(
		array(
			'ID'            => $user->ID,
			'user_nicename' => 'utente-' . $user->ID,
		)
	);
}
add_action( 'wp_saml_auth_new_user_authenticated', 'dli_neutral_slug_for_saml_user' );

/**
 * Whether a user has one of the roles of the reserved area (pending user or reserved area user).
 *
 * @param WP_User|mixed $user The user.
 * @return bool
 */
function dli_user_has_reserved_area_role( $user ) {
	if ( ! $user instanceof WP_User ) {
		return false;
	}
	return (bool) array_intersect( DLI_AuthorizationManager::get_reserved_area_roles(), (array) $user->roles );
}

/**
 * The address where the users of the reserved area land: the page "Area riservata" in the current language, or the
 * home of the current language when no page is chosen or the page cannot be shown.
 *
 * @return string URL.
 */
function dli_get_reserved_area_url() {
	$page = dli_get_page_in_current_language( dli_get_reserved_area_page_id() );
	if ( $page instanceof WP_Post ) {
		return (string) get_permalink( $page );
	}
	return dli_get_language_home_url( dli_current_language() );
}

/**
 * After the login, send a user of the reserved area who would go to the dashboard to the page "Area riservata".
 *
 * A user who logs in from a page of the site (for example a page of a group, whose address WP SAML Auth carries
 * back as RelayState) goes back to that page.
 *
 * @param string           $redirect_to The address WordPress would go to.
 * @param string           $requested   The address that was asked for.
 * @param WP_User|WP_Error $user        The user, or the error of a failed login.
 * @return string
 */
function dli_reserved_area_login_redirect( $redirect_to, $requested, $user ) {
	if ( ! dli_is_reserved_area_enabled() || ! dli_user_has_reserved_area_role( $user ) ) {
		return $redirect_to;
	}
	if ( '' === (string) $redirect_to || 0 === strpos( (string) $redirect_to, admin_url() ) ) {
		return dli_get_reserved_area_url();
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'dli_reserved_area_login_redirect', 20, 3 );

/**
 * The address where to send the current user away from the admin, or an empty string.
 *
 * A user of the reserved area has no capability, so WordPress would answer every screen of the admin with an
 * error: the user goes to the page "Area riservata" instead. The AJAX requests and `admin-post.php`, which serve
 * the front end, are left alone.
 *
 * @return string URL, or '' when the request stays where it is.
 */
function dli_get_reserved_area_admin_redirect() {
	if ( ! is_admin() || wp_doing_ajax() || ! dli_is_reserved_area_enabled() ) {
		return '';
	}
	if ( isset( $GLOBALS['pagenow'] ) && 'admin-post.php' === $GLOBALS['pagenow'] ) {
		return '';
	}
	if ( ! dli_user_has_reserved_area_role( wp_get_current_user() ) ) {
		return '';
	}
	return dli_get_reserved_area_url();
}

/**
 * Send a user of the reserved area away from the admin.
 *
 * Hooked on `init`: WordPress refuses the screens of the admin to a user without capabilities in
 * `wp-admin/menu.php`, before `admin_init` runs.
 *
 * @return void
 */
function dli_redirect_reserved_area_users_from_admin() {
	$url = dli_get_reserved_area_admin_redirect();
	if ( '' !== $url ) {
		wp_safe_redirect( $url );
		exit;
	}
}
add_action( 'init', 'dli_redirect_reserved_area_users_from_admin', 20 );

/**
 * Hide the admin bar from the users of the reserved area: it would only lead to screens they cannot open.
 *
 * @param bool $show Whether WordPress shows the bar.
 * @return bool
 */
function dli_hide_admin_bar_for_reserved_area_users( $show ) {
	if ( dli_is_reserved_area_enabled() && dli_user_has_reserved_area_role( wp_get_current_user() ) ) {
		return false;
	}
	return $show;
}
add_filter( 'show_admin_bar', 'dli_hide_admin_bar_for_reserved_area_users' );

/**
 * Whether the access only through single sign-on applies to a user.
 *
 * @param WP_User|int|mixed $user The user or its ID.
 * @return bool
 */
function dli_reserved_area_saml_only_applies_to( $user ) {
	if ( ! dli_is_reserved_area_saml_only() ) {
		return false;
	}
	if ( is_numeric( $user ) ) {
		$user = get_userdata( (int) $user );
	}
	return dli_user_has_reserved_area_role( $user );
}

/**
 * The error given to a user of the reserved area who tries to use a password.
 *
 * @return WP_Error
 */
function dli_reserved_area_saml_only_error() {
	return new WP_Error(
		'dli_reserved_area_saml_only',
		__( 'Per questo account l\'accesso è possibile solo con il single sign-on dell\'istituto.', 'design_laboratori_italia' ),
		array( 'status' => 403 )
	);
}

/**
 * No password fields in the profile of a user of the reserved area, with the access only through SAML.
 *
 * @param bool         $show        Whether WordPress shows the fields.
 * @param WP_User|null $profileuser The user of the profile.
 * @return bool
 */
function dli_reserved_area_hide_password_fields( $show, $profileuser = null ) {
	return dli_reserved_area_saml_only_applies_to( $profileuser ) ? false : $show;
}
add_filter( 'show_password_fields', 'dli_reserved_area_hide_password_fields', 10, 2 );

/**
 * No password reset for a user of the reserved area, with the access only through SAML.
 *
 * @param bool $allow   Whether WordPress allows the reset.
 * @param int  $user_id User ID.
 * @return bool
 */
function dli_reserved_area_deny_password_reset( $allow, $user_id ) {
	return dli_reserved_area_saml_only_applies_to( $user_id ) ? false : $allow;
}
add_filter( 'allow_password_reset', 'dli_reserved_area_deny_password_reset', 10, 2 );

/**
 * No login with a password for a user of the reserved area, with the access only through SAML.
 *
 * The filter runs only in the login with name (or email) and password of WordPress; WP SAML Auth does not use it,
 * so the single sign-on is not touched.
 *
 * @param WP_User|WP_Error $user The user found by the login, or an error.
 * @return WP_User|WP_Error
 */
function dli_reserved_area_refuse_password_login( $user ) {
	return dli_reserved_area_saml_only_applies_to( $user ) ? dli_reserved_area_saml_only_error() : $user;
}
add_filter( 'wp_authenticate_user', 'dli_reserved_area_refuse_password_login' );

/**
 * No password given to a user of the reserved area through the REST API, with the access only through SAML.
 *
 * The request is stopped before its callback runs: the filter `rest_pre_insert_user` cannot refuse it, since the
 * users controller of WordPress does not check its result for an error.
 *
 * @param WP_REST_Response|WP_HTTP_Response|WP_Error|mixed $response The response so far.
 * @param array<string, mixed>                             $handler  The route handler.
 * @param WP_REST_Request                                  $request  The request.
 * @return WP_REST_Response|WP_HTTP_Response|WP_Error|mixed
 */
function dli_reserved_area_refuse_rest_password( $response, $handler, $request ) {
	if ( is_wp_error( $response ) || ! dli_is_reserved_area_saml_only() ) {
		return $response;
	}
	if ( 'GET' === $request->get_method() || null === $request->get_param( 'password' ) ) {
		return $response;
	}
	if ( ! preg_match( '#^/wp/v2/users(?:/(\d+|me))?/?$#', $request->get_route(), $matches ) ) {
		return $response;
	}

	if ( ! isset( $matches[1] ) ) {
		// A new user: refused when it is given a role of the reserved area.
		$roles   = (array) $request->get_param( 'roles' );
		$applies = (bool) array_intersect( DLI_AuthorizationManager::get_reserved_area_roles(), $roles );
	} else {
		$user_id = 'me' === $matches[1] ? get_current_user_id() : (int) $matches[1];
		$applies = dli_reserved_area_saml_only_applies_to( $user_id );
	}

	return $applies ? dli_reserved_area_saml_only_error() : $response;
}
add_filter( 'rest_request_before_callbacks', 'dli_reserved_area_refuse_rest_password', 10, 3 );

/**
 * Whether a page is the page "Area riservata" chosen in the configuration, or its translation.
 *
 * @param WP_Post|mixed $post The page.
 * @return bool
 */
function dli_is_reserved_area_page( $post ) {
	$page_id = dli_get_reserved_area_page_id();
	if ( ! $post instanceof WP_Post || 0 === $page_id ) {
		return false;
	}
	if ( (int) $post->ID === $page_id ) {
		return true;
	}
	return in_array( (int) $post->ID, array_map( 'intval', dli_get_post_translations( $page_id ) ), true );
}

/**
 * The login address of the reserved area, which comes back to an address after the login.
 *
 * With WP SAML Auth active (filter `dli_reserved_area_saml_available`) the address starts the single sign-on
 * directly (`action=wp-saml-auth`), without the form of WordPress.
 *
 * @param string $redirect The address to come back to.
 * @return string URL.
 */
function dli_get_reserved_area_login_url( $redirect ) {
	$url = wp_login_url( $redirect );
	if ( apply_filters( 'dli_reserved_area_saml_available', class_exists( 'WP_SAML_Auth' ) ) ) {
		$url = add_query_arg( 'action', 'wp-saml-auth', $url );
	}
	return $url;
}

/**
 * Print the access box of the page "Area riservata": the login button for a visitor, the state and the logout for
 * a logged-in user. Nothing is printed on any other page.
 *
 * @param WP_Post|mixed $post The page being shown.
 * @return void
 */
function dli_render_reserved_area_access( $post ) {
	if ( ! dli_is_reserved_area_page( $post ) ) {
		return;
	}

	$page_url  = (string) get_permalink( $post );
	$user      = wp_get_current_user();
	$logged_in = is_user_logged_in();

	get_template_part(
		'template-parts/common/reserved-area-access',
		null,
		array(
			'logged_in'  => $logged_in,
			'pending'    => $logged_in && in_array( DLI_PENDING_USER_ROLE_SLUG, (array) $user->roles, true ),
			'name'       => $user->display_name,
			'login_url'  => dli_get_reserved_area_login_url( $page_url ),
			'logout_url' => wp_logout_url( $page_url ),
		)
	);
}
