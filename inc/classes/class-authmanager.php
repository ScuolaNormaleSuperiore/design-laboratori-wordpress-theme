<?php
/**
 * Definition of the Authorization Manager used to create and modify permissions and roles.
 *
 * @package Design_Laboratori_Italia
 */


class DLI_AuthorizationManager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	public function setup(){
		// Register the custom roles.
		add_action( 'init', array( $this, 'add_super_editor' ) );
	}

	public function add_super_editor(){
		// Recupera il ruolo Editor.
		$base_role = get_role( 'editor' );
		// Controlla se il ruolo Editor esiste.
		if ( $base_role ) {

			// Aggiungi un nuovo ruolo basato sul ruolo Editor, se non esiste.
			$new_role = get_role( DLI_SUPER_EDITOR_ROLE_SLUG );
			if ( ! $new_role ){
				$new_role = add_role( DLI_SUPER_EDITOR_ROLE_SLUG, DLI_SUPER_EDITOR_ROLE_NAME, $base_role->capabilities );
			}

			// Assegna al nuovo ruolo il permesso di lavorare sul tema e sul menu del sito.
			// Abilita la voce: Aspetto/Appearance del backoffice di WP.
			$new_role->add_cap( 'edit_theme_options' );
			// Assegna il permesso di modificare le configurazioni del tema al nuovo ruolo.
			$new_role->add_cap( DLI_EDIT_CONFIG_PERMISSION );

			// Assegna il permesso di modificare le configurazioni del tema all'amministratore del sito.
			$admin_role = get_role( 'administrator' );
			if ( $admin_role ){
				$admin_role->add_cap( DLI_EDIT_CONFIG_PERMISSION );
			}
		}
	}

}
