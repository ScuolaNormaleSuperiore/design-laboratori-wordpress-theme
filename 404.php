<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Design_Laboratori_Italia
 */


get_header();
$back_url = wp_get_referer();
$back_url = $back_url ? $back_url : home_url( '/' );
?>

    <main id="main-container" class="main-container" role="main">

		<?php get_template_part("template-parts/common/breadcrumb"); ?>
        <section class="section bg-white">
            <div class="container ">
                <article class="article-wrapper">
                    <div class="box_404 text-center clearfix">
                        <h1 class="xl"><?php esc_html_e( '404', 'design_laboratori_italia' ); ?></h1>
                        <h2><?php esc_html_e( 'Pagina non trovata', 'design_laboratori_italia' ); ?></h2>
                        <p>
							<?php
							printf(
								wp_kses_post( __( 'Oops! La pagina che cerchi non è stata trovata, <a href="%1$s" title="Torna alla pagina precedente" onclick="history.back(); return false;">torna indietro</a> o utilizza il menu per continuare la navigazione.', 'design_laboratori_italia' ) ),
								esc_url( $back_url )
							);
							?>
						</p>

                    </div>
                </article>
            </div>
        </section>
    </main><!-- #main -->


<?php
get_footer();
