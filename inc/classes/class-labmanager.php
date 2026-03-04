<?php
/**
 * Definition of the LabManager used to create the custom content types.
 *
 * @package Design_Laboratori_Italia
 */

if ( ! class_exists( 'DLI_AuthorizationManager' ) ) {
	include_once 'class-authmanager.php';
}

if ( ! class_exists( 'PeopleManager' ) ) {
	include_once 'class-peoplemanager.php';
}

if ( ! class_exists( 'PeopleType_Manager' ) ) {
	include_once 'class-peopletypemanager.php';
}

if ( ! class_exists( 'Project_Manager' ) ) {
	include_once 'class-projectmanager.php';
}

if ( ! class_exists( 'Publication_Manager' ) ) {
	include_once 'class-publicationmanager.php';
}

if ( ! class_exists( 'Patent_Manager' ) ) {
	include_once 'class-patentmanager.php';
}

if ( ! class_exists( 'SpinOff_Manager' ) ) {
	include_once 'class-spinoffmanager.php';
}

if ( ! class_exists( 'Sponsor_Manager' ) ) {
	include_once 'class-sponsormanager.php';
}

if ( ! class_exists( 'TechnicalResource_Manager' ) ) {
	include_once 'class-technicalresourcemanager.php';
}

if ( ! class_exists( 'ResearchActivities_Manager' ) ) {
	include_once 'class-researchactivitiesmanager.php';
}

if ( ! class_exists( 'News_Manager' ) ) {
	include_once 'class-newsmanager.php';
}

if ( ! class_exists( 'Event_Manager' ) ) {
	include_once 'class-eventmanager.php';
}

if ( ! class_exists( 'Post_Manager' ) ) {
	include_once 'class-postmanager.php';
}

if ( ! class_exists( 'Page_Manager' ) ) {
	include_once 'class-pagemanager.php';
}

if ( ! class_exists( 'Polylang_Manager' ) ) {
	include_once 'class-polylangmanager.php';
}

if ( ! class_exists( 'Place_Manager' ) ) {
	include_once 'class-placemanager.php';
}

if ( ! class_exists( 'Banner_Manager' ) ) {
	include_once 'class-bannermanager.php';
}

if ( ! class_exists( 'DLI_IndicoImporter' ) ) {
	include_once 'class-indicoimporter.php';
}

if ( ! class_exists( 'DLI_IrisPatentImporter' ) ) {
	include_once 'class-patentimporter.php';
}

/**
 * The manager that builds the tool and configures WordPress.
 */
class DLI_LabManager {

	/**
	 * The static instance of the LabManager.
	 *
	 * @var object
	 */
	protected static $instance = null;


	/**
	 * Create the instance of the manager.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Used to install and configure the plugin.
	 *
	 * @return void
	 */
	public function plugin_setup() {

		// Setup internationalisation.
		add_action( 'after_setup_theme', array( $this, 'configure_languages' ), 0 );

		// Setup image sizes.
		add_action( 'after_setup_theme', array( $this, 'setup_image_sizes' ) );

		// Setup permalink structure only on theme activation/switch.
		add_action( 'after_switch_theme', array( $this, 'configure_permalink' ) );

		// Setup REST API.
		add_filter( 'rest_authentication_errors', array( $this, 'setup_rest_api' ) );

		// Setup upload limits.
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'setup_upload_limits' ) );

		// Mostra avviso sui limiti di upload nel media uploader (modal Backbone.js).
		add_action( 'wp_enqueue_media', array( $this, 'enqueue_upload_limits_script' ) );

		add_filter(
			'upload_size_limit',
			function () {
				return DLI_MAX_FILE_SIZE_MB * 1024 * 1024;
			}
		);

		// Disabilita email notifiche aggiornamento plugin.
		add_filter( 'auto_plugin_update_send_email', '__return_false' );

		// Disabilita anche quelle per i temi.
		add_filter( 'auto_theme_update_send_email', '__return_false' );

		// Imposta configurazioni di sicurezza.
		$this->enable_security_configurations();

		// Setup dei post type personalizzati e delle tassonomie associate.
		// Setup di Polylang.
		$polylang = new Polylang_Manager();
		$polylang->setup();

		// Setup dei reuoli e dei permessi.
		$authm = new DLI_AuthorizationManager();
		$authm->setup();

		// Setup del post type Persona.
		$cpm = new People_Manager();
		$cpm->setup();

		// Setup del post type Tipologia Persona.
		$cptm = new PeopleType_Manager();
		$cptm->setup();

		// Setup del post type Progetto.
		$ctprm = new Project_Manager();
		$ctprm->setup();

		// Setup del post type Pubblicazione.
		$publm = new Publication_Manager();
		$publm->setup();

		// Setup del post type Brevetto.
		$pat = new Patent_Manager();
		$pat->setup();

		// Setup del post type Indirizzo di ricerca.
		$ctram = new ResearchActivities_Manager();
		$ctram->setup();

		// Setup del post type News.
		$ctprog = new News_Manager();
		$ctprog->setup();

		// Setup del post type Eventi.
		$ctprog = new Event_Manager();
		$ctprog->setup();

		// Setup del post base.
		$ctprog = new Post_Manager();
		$ctprog->setup();

		// Setup della pagina base.
		$pgprog = new Page_Manager();
		$pgprog->setup();

		// Setup del post type Luoghi.
		$ctluoghi = new Place_Manager();
		$ctluoghi->setup();

		// Setup the Banner Manager.
		$bm = new Banner_Manager();
		$bm->setup();

		// Setup the Indico Manager.
		$indicoi = new DLI_IndicoImporter();
		$indicoi->setup();

		// Setup the Patent Importer.
		$ipm = new DLI_IrisPatentImporter();
		$ipm->setup();

		// Setup del post type Spin-Off.
		$som = new SpinOff_Manager();
		$som->setup();

		// Setup del post type Sponsor.
		$spm = new Sponsor_Manager();
		$spm->setup();

		// Setup del post type Technical Resource.
		$trm = new TechnicalResource_Manager();
		$trm->setup();
	}

	/**
	 * Imposta la cartella con i file delle traduzioni.
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Design Laboratori Italia, use a find and replace
	 * to change 'design_laboratori_italia' to the name of your theme in all the template files.
	 *
	 * @return void
	 */
	public function configure_languages() {
		load_theme_textdomain( 'design_laboratori_italia', get_template_directory() . '/languages' );
	}

	/**
	 * Setup custom image sizes.
	 *
	 * @return void
	 */
	public function setup_image_sizes() {
		// Image size.
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'item-card-list', 416, 232, false );
			add_image_size( 'item-carousel', 592, 334, false );
		}
	}

	/**
	 * Configure permalink structure if needed.
	 *
	 * @return void
	 */
	public function configure_permalink() {
		$desired_structure = '/%postname%/';
		$current_structure = get_option( 'permalink_structure' );
		if ( $current_structure !== $desired_structure ) {
			update_option( 'permalink_structure', $desired_structure );
			flush_rewrite_rules();
		}
	}

	/**
	 * Disabilita la REST API se necessario.
	 *
	 * @return object.
	 */
	public function setup_rest_api() {
		if ( 'true' !== dli_get_option( 'rest_api_enabled', 'setup' ) ) {
			return new WP_Error(
				'rest_disabled',
				__( 'L\'API REST di WordPress è disabilitata.', 'design_laboratori_italia' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
	}

	/**
	 * Validate upload size limits by file type.
	 *
	 * @param array $file Upload metadata array.
	 * @return array
	 */
	public function setup_upload_limits( $file ) {
		$image_max_size = DLI_MAX_IMG_FILE_SIZE_MB * 1024 * 1024;  // 1MB per immagini.
		$pdf_max_size   = DLI_MAX_PDF_FILE_SIZE_MB * 1024 * 1024;  // 2MB per PDF.
		$type           = $file['type'];
		$size           = $file['size'];
		// Limiti per immagini.
		$image_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
		if ( in_array( $type, $image_types, true ) && $size > $image_max_size ) {
			$file['error'] = __( 'L\'immagine è troppo grande. Massimo consentito: 1MB.', 'design_laboratori_italia' );
		}
		// Limite per PDF.
		if ( 'application/pdf' === $type && $size > $pdf_max_size ) {
			$file['error'] = __( 'Il file PDF è troppo grande. Massimo consentito: 2MB.', 'design_laboratori_italia' );
		}
		return $file;
	}

	/**
	 * Inject an upload size notice in the WordPress Backbone.js media modal.
	 *
	 * @return void
	 */
	public function enqueue_upload_limits_script() {
		$notice = "<b style='color: #00728D;'>" . esc_js( __( 'Dimensione massima consentita sul Sito Federato: PDF 2 MB, Immagini (JPEG, PNG, GIF, WebP) 1 MB.', 'design_laboratori_italia' ) ) . '</b>';
		wp_add_inline_script(
			'media-views',
			'(function(){
				if ( typeof wp === "undefined" || ! wp.media || ! wp.media.view || ! wp.media.view.UploaderInline ) { return; }
				var Parent = wp.media.view.UploaderInline;
				wp.media.view.UploaderInline = Parent.extend({
					ready: function() {
						Parent.prototype.ready.apply( this, arguments );
						if ( ! this.$el.find( ".dli-upload-notice" ).length ) {
							this.$el.append( "<p class=\"description dli-upload-notice\" style=\"text-align:center;margin-top:8px;\">' . $notice . '</p>" );
						}
					}
				});
			})();'
		);
	}

	/**
	 * Enable core security-related filters.
	 *
	 * @return void
	 */
	private function enable_security_configurations() {
		// Hook per nascondere sovrascrivere il messaggio di errore in fase di login.
		add_filter(
			'login_errors',
			function () {
				return __( 'Invalid username or password', 'design_laboratori_italia' );
			}
		);
		// Hook per nascondere la versione del CMS (tag generator).
		add_filter( 'the_generator', '__return_null' );
		// Disable XMLRPC service.
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}
}
