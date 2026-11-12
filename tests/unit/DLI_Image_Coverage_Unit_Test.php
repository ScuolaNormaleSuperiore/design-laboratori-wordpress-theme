<?php
/**
 * Unit tests that keep the list of image formats complete: every file of the theme that uploads or shows an image is
 * known to this test and tied to a place of the list in inc/image-guidelines.php (or has a reason to be left out).
 *
 * When this test fails after you add or change a template, a field or an image size:
 * 1. add or check the place in `dli_get_image_guidelines()` (inc/image-guidelines.php);
 * 2. update the guide DOC/HowTo/Gestione-delle-immagini.md (DLI_Image_Guide_Unit_Test checks that its sizes match the list);
 * 3. add the file to KNOWN_FILES below, with the places it shows (or the reason why it is left out).
 *
 * The check is by file: it finds a new file that shows an image, not a new call inside a file that is already known.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Coverage of the list of image formats.
 */
class DLI_Image_Coverage_Unit_Test extends TestCase {

	/**
	 * What shows or uploads an image, as patterns of PHP code.
	 *
	 * The image tags of the templates (`<img`) are not searched: most of them are the decorative placeholders or the
	 * logos, and an uploaded image reaches a template through one of these functions or fields.
	 *
	 * @var array<string, string>
	 */
	const PATTERNS = array(
		'image size'         => '/\badd_image_size\s*\(/',
		'featured image'     => '/\b(?:get_the_post_thumbnail(?:_url)?|the_post_thumbnail)\s*\(/',
		'attachment image'   => '/\bwp_get_attachment_(?:image(?:_src|_url)?|url)\s*\(/',
		'attachment by url'  => '/\battachment_url_to_postid\s*\(/',
		'shared image data'  => '/\bdli_get_image_metadata\s*\(/',
		'ACF image field'    => '/[\'"]type[\'"]\s*=>\s*[\'"]image[\'"]/',
		'upload field (file)' => '/[\'"]type[\'"]\s*=>\s*[\'"]file[\'"]/',
	);

	/**
	 * Folders searched, relative to the theme: the root files are searched without their subfolders.
	 *
	 * @var string[]
	 */
	const FOLDERS = array( 'inc', 'page-templates', 'template-parts' );

	/**
	 * Folders left out: bundled libraries.
	 *
	 * @var string[]
	 */
	const SKIPPED = array( 'inc/vendor/' );

	/**
	 * The files that show or upload an image, with the places of the list that they serve.
	 *
	 * An empty list of places needs a reason: the file has an upload field that is not an image (an attachment).
	 *
	 * @var array<string, array{0: string[], 1: string}>
	 */
	const KNOWN_FILES = array(
		'inc/classes/class-dli-labmanager.php'                    => array( array( 'card_elenco', 'carousel_correlati', 'persona' ), 'Registers the image sizes.' ),
		'inc/utils.php'                                           => array( array( 'card_elenco', 'carousel_correlati', 'persona' ), 'Shared helpers: item data, carousel images, photo of a person.' ),
		'inc/classes/class-dli-contentsmanager.php'               => array( array( 'carousel_correlati' ), 'Image of the contents in the carousel.' ),
		'inc/classes/class-spinoffmanager.php'                    => array( array( 'logo_spinoff' ), 'ACF field Logo.' ),
		'inc/classes/class-technicalresourcemanager.php'          => array( array( 'foto_risorsa_tecnica' ), 'ACF field Foto, and the attachments of the resource.' ),
		'inc/admin/options.php'                                   => array( array( 'hero_home', 'logo_header', 'logo_footer', 'logo_mobile' ), 'CMB2 fields of the configuration: hero and logos.' ),
		'template-parts/home/main-hero.php'                       => array( array( 'hero_home' ), 'Background of the hero of the home.' ),
		'template-parts/home/hp-banners-section.php'              => array( array( 'banner_home' ), 'Banners of the home.' ),
		'template-parts/home/hp-sponsor-section.php'              => array( array( 'sponsor' ), 'Sponsors of the home.' ),
		'template-parts/home/featured-contents/card-fallback.php' => array( array( 'card_elenco' ), 'Card of a featured content of another type.' ),
		'page-templates/archive-progetti.php'                     => array( array( 'card_elenco' ), 'List of the projects.' ),
		'page-templates/brevetti.php'                             => array( array( 'card_elenco' ), 'List of the patents.' ),
		'page-templates/eventi.php'                               => array( array( 'card_elenco' ), 'List of the events.' ),
		'page-templates/progetti.php'                             => array( array( 'card_elenco' ), 'List of the projects.' ),
		'page-templates/ricerca.php'                              => array( array( 'card_elenco' ), 'List of the research addresses.' ),
		'page-templates/spinoff.php'                              => array( array( 'logo_spinoff', 'card_elenco' ), 'List of the spin-offs: the logo, or the featured image.' ),
		'page-templates/pubblicazioni.php'                        => array( array( 'pubblicazione_copertina' ), 'List of the publications: the cover.' ),
		'page-templates/privacy.php'                              => array( array( 'dettaglio' ), 'Page of the privacy policy.' ),
		'page.php'                                                => array( array( 'dettaglio' ), 'Standard page.' ),
		'single-brevetto.php'                                     => array( array( 'dettaglio' ), 'Detail of a patent.' ),
		'single-evento.php'                                       => array( array( 'dettaglio' ), 'Detail of an event.' ),
		'single-indirizzo-di-ricerca.php'                         => array( array( 'dettaglio' ), 'Detail of a research address.' ),
		'single-luogo.php'                                        => array( array( 'dettaglio' ), 'Detail of a place.' ),
		'single-notizia.php'                                      => array( array( 'dettaglio' ), 'Detail of a news item.' ),
		'single-post.php'                                         => array( array( 'dettaglio' ), 'Detail of a post.' ),
		'single-progetto.php'                                     => array( array( 'dettaglio' ), 'Detail of a project.' ),
		'single-risorsa-tecnica.php'                              => array( array( 'foto_risorsa_tecnica', 'dettaglio' ), 'Detail of a technical resource: the photo, or the featured image.' ),
		'single-pubblicazione.php'                                => array( array( 'pubblicazione_copertina' ), 'Detail of a publication: the cover.' ),
		'inc/classes/class-dli-publication-manager.php'           => array( array(), 'Attachment of a publication (a PDF), not an image.' ),
		'inc/classes/class-eventmanager.php'                      => array( array(), 'Attachment of an event, not an image.' ),
		'inc/classes/class-newsmanager.php'                       => array( array(), 'Attachment of a news item, not an image.' ),
		'inc/classes/class-peoplemanager.php'                     => array( array(), 'Attachments of a person (CV and documents), not images.' ),
		'inc/classes/class-projectmanager.php'                    => array( array(), 'Attachments of a project, not images.' ),
	);

	/**
	 * The PHP files of the theme that the check reads, as `path => content`.
	 *
	 * @return array<string, string>
	 */
	private function theme_files() {
		$files = array();
		foreach ( glob( DLI_TEST_THEME_DIR . '/*.php' ) as $path ) {
			$files[ basename( $path ) ] = (string) file_get_contents( $path );
		}
		foreach ( self::FOLDERS as $folder ) {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( DLI_TEST_THEME_DIR . '/' . $folder, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $iterator as $file ) {
				if ( 'php' !== $file->getExtension() ) {
					continue;
				}
				$relative = str_replace( '\\', '/', substr( $file->getPathname(), strlen( DLI_TEST_THEME_DIR ) + 1 ) );
				foreach ( self::SKIPPED as $skipped ) {
					if ( 0 === strpos( $relative, $skipped ) ) {
						continue 2;
					}
				}
				$files[ $relative ] = (string) file_get_contents( $file->getPathname() );
			}
		}
		return $files;
	}

	/**
	 * The files that show or upload an image, with the names of the patterns that matched.
	 *
	 * @param array<string, string> $files Path and content.
	 * @return array<string, string[]>
	 */
	private function files_with_images( array $files ) {
		$found = array();
		foreach ( $files as $path => $content ) {
			foreach ( self::PATTERNS as $name => $pattern ) {
				if ( 1 === preg_match( $pattern, $content ) ) {
					$found[ $path ][] = $name;
				}
			}
		}
		return $found;
	}

	/**
	 * The problems between the files found and the known ones.
	 *
	 * @param array<string, string[]>                       $found The files that show an image.
	 * @param array<string, array{0: string[], 1: string}>  $known The known files.
	 * @return string[] One message for each problem.
	 */
	private function problems( array $found, array $known ) {
		$problems = array();
		foreach ( $found as $path => $names ) {
			if ( ! isset( $known[ $path ] ) ) {
				$problems[] = "$path shows or uploads an image (" . implode( ', ', $names ) . ') and is not in the list of known files: add its place to dli_get_image_guidelines() and to the guide, then add the file to KNOWN_FILES.';
			}
		}
		foreach ( $known as $path => $entry ) {
			if ( ! isset( $found[ $path ] ) ) {
				$problems[] = "$path is in KNOWN_FILES but no longer shows or uploads an image: remove it from the list (and check the places it served).";
			}
			if ( '' === trim( $entry[1] ) ) {
				$problems[] = "$path has no description in KNOWN_FILES.";
			}
			foreach ( $entry[0] as $place ) {
				if ( null === dli_get_image_guideline( $place ) ) {
					$problems[] = "$path names the place $place, which is not in dli_get_image_guidelines().";
				}
			}
		}
		return $problems;
	}

	/**
	 * Every file of the theme that shows or uploads an image is known and tied to a place of the list.
	 */
	public function test_every_file_with_an_image_is_in_the_list() {
		$problems = $this->problems( $this->files_with_images( $this->theme_files() ), self::KNOWN_FILES );

		$this->assertSame( array(), $problems, implode( "\n", $problems ) );
	}

	/**
	 * Every place of the list is served by at least one known file, so that no place stays in the list for nothing.
	 */
	public function test_every_place_of_the_list_has_a_file() {
		$used = array();
		foreach ( self::KNOWN_FILES as $entry ) {
			$used = array_merge( $used, $entry[0] );
		}
		foreach ( array_keys( dli_get_image_guidelines() ) as $place ) {
			if ( in_array( $place, array( 'logo_header', 'logo_footer', 'logo_mobile' ), true ) ) {
				continue;
			}
			$this->assertContains( $place, $used, "$place is in the list of formats but no known file shows it." );
		}
		$this->assertContains( 'logo_header', $used );
		$this->assertContains( 'logo_footer', $used );
		$this->assertContains( 'logo_mobile', $used );
	}

	/**
	 * Control: a template that shows an image and is not known is reported, with what to do.
	 */
	public function test_the_check_reports_a_new_template_with_an_image() {
		$files = array(
			'template-parts/new/hero-new.php' => '<?php echo get_the_post_thumbnail_url( $post, "full" );',
			'page.php'                        => '<?php dli_get_image_metadata( $post );',
			'footer.php'                      => '<?php echo "no image here";',
		);

		$problems = $this->problems( $this->files_with_images( $files ), array( 'page.php' => self::KNOWN_FILES['page.php'] ) );

		$this->assertCount( 1, $problems );
		$this->assertStringContainsString( 'template-parts/new/hero-new.php', $problems[0] );
		$this->assertStringContainsString( 'featured image', $problems[0] );
		$this->assertStringContainsString( 'dli_get_image_guidelines()', $problems[0] );
	}

	/**
	 * Control: a known file that no longer shows an image, and a place that is not in the list, are reported.
	 */
	public function test_the_check_reports_a_stale_file_and_an_unknown_place() {
		$known = array(
			'page.php'     => array( array( 'dettaglio' ), 'Standard page.' ),
			'old-file.php' => array( array( 'dettaglio' ), 'Gone.' ),
			'single.php'   => array( array( 'non-esiste' ), 'Wrong place.' ),
			'empty.php'    => array( array(), '' ),
		);
		$found = array(
			'page.php'   => array( 'featured image' ),
			'single.php' => array( 'featured image' ),
			'empty.php'  => array( 'upload field (file)' ),
		);

		$problems = $this->problems( $found, $known );

		$this->assertCount( 3, $problems );
		$this->assertStringContainsString( 'old-file.php', $problems[0] );
		$this->assertStringContainsString( 'non-esiste', $problems[1] );
		$this->assertStringContainsString( 'empty.php has no description', $problems[2] );
	}

	/**
	 * Control: every pattern recognises the code that it is meant for, and nothing it should not.
	 */
	public function test_the_patterns_recognise_their_code() {
		$samples = array(
			'image size'          => "add_image_size( 'x', 1, 1 );",
			'featured image'      => 'the_post_thumbnail( $id );',
			'attachment image'    => 'wp_get_attachment_image_src( $id, "large" );',
			'attachment by url'   => 'attachment_url_to_postid( $url );',
			'shared image data'   => 'dli_get_image_metadata( $post, "full" );',
			'ACF image field'     => "'type'              => 'image',",
			'upload field (file)' => "'type' => 'file',",
		);
		foreach ( $samples as $name => $code ) {
			$found = $this->files_with_images( array( 'a.php' => '<?php ' . $code ) );
			$this->assertSame( array( $name ), $found['a.php'] ?? array(), "The pattern '$name' does not recognise its sample." );
		}

		// An image tag, a variable called $type and a function name that only contains the words are not images.
		$this->assertSame( array(), $this->files_with_images( array( 'b.php' => '<?php echo "<img src=\"x.png\">"; $type = "image"; my_add_image_size_note();' ) ) );
	}
}
