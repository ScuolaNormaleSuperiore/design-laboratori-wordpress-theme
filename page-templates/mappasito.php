<?php
/* Template Name: Mappa del sito
 *
 * @package Design_Laboratori_Italia
 */
get_header();

// Build the PAGE TREE.

$lng_slug  = dli_current_language( 'slug' );

try {
	$pt = dli_get_site_tree();
} catch ( Exception $e ) {
	$pt  = array();
	$msg = 'Caught exception: ' . $e->getMessage() . '\n';
	error_log( $msg );
}
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER MAPPA DEL SITO -->
	<?php get_template_part( 'template-parts/hero/mappasito' ); ?>

	<!-- MAPPA DEL SITO -->
	<div class="container my-4">
		<div class="row variable-gutters d-flex justify-content-center">
			<div class="col-lg-8 pt84">
				<ul class="menutree">
					<li><a class="mappasitolink" href="<?php echo $pt[DLI_HOMEPAGE_SLUG]['link']; ?>"><?php echo $pt[DLI_HOMEPAGE_SLUG]['name']; ?></a></li>
                    <?php if(!empty($pt[DLI_HOMEPAGE_SLUG]['children'])) { ?>
                        <ul>
                            <?php
                            // I livello.
                            foreach ( $pt[DLI_HOMEPAGE_SLUG]['children'] as $item ) {
                                if ( $item['external'] ) {
                                    echo '<li><a class="mappasitolink" target="_blank" href="' . $item['link'] . '">' . $item['name'] . '</a></li>';
                                } else {
                                    echo '<li><a class="mappasitolink" href="' . $item['link'] . '">' . $item['name'] . '</a></li>';
                                }
                                if (!empty($item['children'])) {
                                    // II livello.
                                    echo '<ul>';
                                    foreach ( $item['children'] as $childitem ) {
                                        if ( $childitem['external'] ) {
                                            echo '<li><a class="mappasitolink" target="_blank" href="' . $childitem['link'] . '">' . $childitem['name'] . '</a></li>';
                                        } else {
                                            echo '<li><a class="mappasitolink" href="' . $childitem['link'] . '">' . $childitem['name'] . '</a></li>';
                                        }
                                        if (!empty($childitem['children'])) {
                                            // III livello.
                                            echo '<ul>';
                                            foreach ($childitem['children'] as $grandchilditem) {
                                                if ($grandchilditem['external']) {
                                                    echo '<li><a class="mappasitolink" target="_blank" href="' . $grandchilditem['link'] . '">' . $grandchilditem['name'] . '</a></li>';
                                                } else {
                                                    echo '<li><a class="mappasitolink" href="' . $grandchilditem['link'] . '">' . $grandchilditem['name'] . '</a></li>';
                                                }
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    echo '</ul>';
                                }
                            }
                            ?>
                        </ul>
                    <?php } ?>
				</ul>

				<div class="box_change_map_lang">
					<?php
					$selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
					if ( 'true' === $selettore_visibile ) {
						if ( 'it' === $lng_slug ) {
							echo '<a href="' . get_site_url() . '/en/' . SLUG_MAPPA_SITO_EN . '">Site Map in English</a>';
						} else {
							echo '<a href="' . get_site_url() . '/' . SLUG_MAPPA_SITO_IT . '">Mappa del sito in Italiano</a>';
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

</main>

<?php
get_footer();
