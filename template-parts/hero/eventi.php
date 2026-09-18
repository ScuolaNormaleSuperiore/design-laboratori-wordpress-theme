<?php
/**
 * Template part: hero con breadcrumb integrato per la pagina elenco Eventi.
 *
 * Pattern standard v3 per le pagine di elenco/archivio (hero a due colonne,
 * breadcrumb nella colonna di testo, immagine decorativa nella colonna
 * destra), già validato nel prototipo statico bs-playground
 * (sf-archivio-eventi.html) e in template-parts/hero/brevetti.php.
 *
 * @package Design_Laboratori_Italia
 */

$dli_testo_sezione = dli_get_configuration_field_by_lang( 'testo_eventi', 'eventi' );
?>

<section id="banner-eventi" class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-eventi-title">
	<div class="container">
		<div class="row align-items-stretch">
			<div class="col-12 col-lg-7">
				<section class="pt-2">
					<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
				</section>
				<div class="it-hero-text-wrapper px-lg-2">
					<h2 id="dli-hero-eventi-title"><?php echo esc_html( get_the_title() ); ?></h2>
					<?php if ( $dli_testo_sezione ) : ?>
						<div class="fs-5"><?php echo wp_kses_post( wpautop( $dli_testo_sezione ) ); ?></div>
					<?php endif; ?>
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
