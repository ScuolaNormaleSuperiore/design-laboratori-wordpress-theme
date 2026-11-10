<?php
/**
 * Definition of the Authorization Manager used to create and modify permissions and roles.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that creates and configures the custom roles: Super Editor, the pending user and the reserved area user.
 */
class DLI_AuthorizationManager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Register the custom roles hooks.
	 *
	 * @return void
	 */
	public function setup() {
		// Register the custom roles.
		add_action( 'init', array( $this, 'add_super_editor' ) );
		add_action( 'init', array( $this, 'add_pending_user_role' ) );
		add_action( 'init', array( $this, 'add_reserved_area_user_role' ) );
		add_filter( 'map_meta_cap', array( $this, 'deny_profile_editing_to_pending_user' ), 10, 3 );
	}

	/**
	 * Slugs of the roles of the reserved area: the pending user and the approved user.
	 *
	 * @return string[] Role slugs.
	 */
	public static function get_reserved_area_roles() {
		return array( DLI_PENDING_USER_ROLE_SLUG, DLI_RESERVED_AREA_USER_ROLE_SLUG );
	}

	/**
	 * Refuse editing a user to someone with a role of the reserved area, the own profile included.
	 *
	 * WordPress lets everybody edit the own profile whatever the capabilities are, for example with the REST route
	 * `POST /wp/v2/users/me`; this closes it for the pending user and the reserved area user, so that neither can
	 * change the email, which is the key of the user.
	 *
	 * @param string[] $caps    Primitive capabilities that the check requires.
	 * @param string   $cap     Capability being checked.
	 * @param int      $user_id ID of the user the check is for.
	 * @return string[] The capabilities required, or `do_not_allow`.
	 */
	public function deny_profile_editing_to_pending_user( $caps, $cap, $user_id ) {
		if ( 'edit_user' !== $cap ) {
			return $caps;
		}

		$user = get_userdata( (int) $user_id );
		if ( $user && array_intersect( self::get_reserved_area_roles(), (array) $user->roles ) ) {
			return array( 'do_not_allow' );
		}

		return $caps;
	}

	/**
	 * Create the reserved area user role: an approved user who can do nothing in WordPress.
	 *
	 * The role has only the theme capability `DLI_RESERVED_AREA_ACCESS_CAP`, which WordPress ignores: no dashboard,
	 * no profile. It is created only when it does not exist and is never rewritten, so a capability decided later
	 * is not lost.
	 *
	 * @return void
	 */
	public function add_reserved_area_user_role() {
		if ( ! get_role( DLI_RESERVED_AREA_USER_ROLE_SLUG ) ) {
			add_role( DLI_RESERVED_AREA_USER_ROLE_SLUG, DLI_RESERVED_AREA_USER_ROLE_NAME, array( DLI_RESERVED_AREA_ACCESS_CAP => true ) );
		}
	}

	/**
	 * Create the pending user role: a user who is authenticated and can do nothing.
	 *
	 * The role has no capability at all, not even `read`. It is created only when it does not exist and is never
	 * rewritten, so a capability decided later is not lost. The theme does not assign it to anybody.
	 *
	 * @return void
	 */
	public function add_pending_user_role() {
		if ( ! get_role( DLI_PENDING_USER_ROLE_SLUG ) ) {
			add_role( DLI_PENDING_USER_ROLE_SLUG, DLI_PENDING_USER_ROLE_NAME, array() );
		}
	}

	/**
	 * Create the Super Editor role (based on Editor) and grant it (and the
	 * administrator) permission to manage the theme's appearance and
	 * configuration options.
	 *
	 * @return void
	 */
	public function add_super_editor() {
		// Recupera il ruolo Editor.
		$base_role = get_role( 'editor' );
		// Controlla se il ruolo Editor esiste.
		if ( $base_role ) {

			// Aggiungi un nuovo ruolo basato sul ruolo Editor, se non esiste.
			$new_role = get_role( DLI_SUPER_EDITOR_ROLE_SLUG );
			if ( ! $new_role ) {
				$new_role = add_role( DLI_SUPER_EDITOR_ROLE_SLUG, DLI_SUPER_EDITOR_ROLE_NAME, $base_role->capabilities );
			}

			// Assegna al nuovo ruolo il permesso di lavorare sul tema e sul menu del sito.
			// Abilita la voce: Aspetto/Appearance del backoffice di WP.
			$new_role->add_cap( 'edit_theme_options' );
			// Assegna il permesso di modificare le configurazioni del tema al nuovo ruolo.
			$new_role->add_cap( DLI_EDIT_CONFIG_PERMISSION );

			// Assegna il permesso di modificare le configurazioni del tema all'amministratore del sito.
			$admin_role = get_role( 'administrator' );
			if ( $admin_role ) {
				$admin_role->add_cap( DLI_EDIT_CONFIG_PERMISSION );
			}
		}
	}
}
