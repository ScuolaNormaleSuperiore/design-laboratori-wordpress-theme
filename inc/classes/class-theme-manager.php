<?php
/**
 * Gestione e configurazione del tema.
 *
 * @package Design_Laboratori_Italia
 */

 /**
 * The Theme manager.
 */
class DLI_ThemeManager
{


	/**
	 * Installa e configura il tema.
	 *
	 * @return void
	 */
	public function theme_setup() {
	
		// Security configurations.
		$this->enable_security_configurations();

	}

	private function enable_security_configurations() {

		// Hook per nascondere sovrascrivere il messaggio di errore in fase di login.
		add_filter( 'login_errors', function(){
				return __( 'Invalid username or password', 'kk_writer_theme' );
			}
		);

		// Hook per nascondere la versione del CMS (tag generator).
		add_filter( 'the_generator', '__return_null' );

		// Disable XMLRPC service.
		add_filter('xmlrpc_enabled', '__return_false');
	}

}
