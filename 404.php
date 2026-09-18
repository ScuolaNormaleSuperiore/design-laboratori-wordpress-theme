<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Design_Laboratori_Italia
 */

get_header();
$dli_back_url = wp_get_referer();
$dli_back_url = $dli_back_url ? $dli_back_url : home_url( '/' );
?>

<main id="main-container" class="main-container" role="main">

	<!-- BANNER 404: hero a due colonne con breadcrumb integrato, stesso
	     pattern standard delle pagine "di servizio" senza prototipo dedicato
	     (Contatti/Newsletter). "404" come span informativo sopra il titolo,
	     non come h1 separato: l'unico h1 di pagina resta quello del nome del
	     laboratorio nell'header. -->
	<section class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-404-title">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 col-lg-7">
					<section class="pt-2">
						<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
					</section>
					<div class="it-hero-text-wrapper px-lg-2">
						<span class="it-category"><?php esc_html_e( '404', 'design_laboratori_italia' ); ?></span>
						<h2 id="dli-hero-404-title"><?php esc_html_e( 'Pagina non trovata', 'design_laboratori_italia' ); ?></h2>
					</div>
				</div>
				<div class="col-12 col-lg-5 d-none d-lg-block">
					<?php if ( dli_show_hero_decorative_image() ) : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section bg-white">
		<div class="container">
			<article class="article-wrapper">
				<div class="box_404 text-center clearfix">
					<p class="fs-5">
						<?php
						/* translators: %1$s is the fallback URL for the "go back" link. */
						$dli_404_message = __(
							'Oops! La pagina che cerchi non è stata trovata, <a href="%1$s" title="Torna alla pagina precedente">torna indietro</a> o utilizza il menu per continuare la navigazione.',
							'design_laboratori_italia'
						);
						printf(
							wp_kses_post( $dli_404_message ),
							esc_url( $dli_back_url )
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
