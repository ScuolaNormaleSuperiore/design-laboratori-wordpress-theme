<?php
/**
 * Template part: hero con breadcrumb integrato per la pagina Archivio progetti.
 *
 * Stesso pattern di template-parts/hero/progetti.php (elenco): qui non
 * esiste una pagina "archivio" equivalente nel prototipo statico
 * (bs-playground porta solo l'archivio di news/eventi), quindi si riusa lo
 * stesso schema hero+breadcrumb validato per le pagine di elenco/archivio.
 *
 * @package Design_Laboratori_Italia
 */

?>

<section id="banner-progetti" class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-progetti-archive-title">
	<div class="container">
		<div class="row align-items-stretch">
			<div class="col-12 col-lg-7">
				<section class="pt-2">
					<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
				</section>
				<div class="it-hero-text-wrapper px-lg-2">
					<h2 id="dli-hero-progetti-archive-title"><?php echo esc_html( get_the_title() ); ?></h2>
					<p class="fs-5"><?php echo esc_html__( 'Elenco dei progetti archiviati', 'design_laboratori_italia' ); ?></p>
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
