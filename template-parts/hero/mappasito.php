<?php
/**
 * Template part: hero con breadcrumb integrato per la pagina Mappa del sito.
 *
 * Pattern standard v3 per le pagine di elenco/archivio (hero a due colonne,
 * breadcrumb nella colonna di testo, immagine decorativa nella colonna
 * destra), già validato nel prototipo statico bs-playground
 * (sf-mappa-del-sito.html) e in template-parts/hero/brevetti.php.
 *
 * @package Design_Laboratori_Italia
 */
?>

<section id="banner-mappasito" class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-mappasito-title">
	<div class="container">
		<div class="row align-items-stretch">
			<div class="col-12 col-lg-7">
				<section class="pt-2">
					<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
				</section>
				<div class="it-hero-text-wrapper px-lg-2">
					<h2 id="dli-hero-mappasito-title"><?php esc_html_e( 'Mappa del sito', 'design_laboratori_italia' ); ?></h2>
					<p class="fs-5"><?php esc_html_e( 'Elenco delle pagine del sito', 'design_laboratori_italia' ); ?></p>
				</div>
			</div>
			<div class="col-12 col-lg-5 d-none d-lg-block">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
			</div>
		</div>
	</div>
</section>
