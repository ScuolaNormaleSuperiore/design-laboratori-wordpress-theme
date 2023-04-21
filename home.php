<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
$carousel_after_pres = dli_get_option( 'home_carousel_after_presentation_enabled', 'homepage' );
?>
<main id="main-container" class="main-container redbrown">

<!-- Section MAIN HERO  -->
<?php get_template_part( 'template-parts/home/main-hero' ); ?>

<!-- Section CAROUSEL -->
<?php
 if ( 'false' === $carousel_after_pres ){
?>
<?php get_template_part( 'template-parts/home/carousel' ); ?>
<?php
	}
?>

<!-- PRESENTAZIONE -->
<?php
$testo_sezione_laboratorio = dli_get_configuration_field_by_lang( 'descrizione_laboratorio', 'la_scuola' );
$etichetta_laboratorio     = dli_get_configuration_field_by_lang( 'etichetta', 'la_scuola' );

if ( $testo_sezione_laboratorio ) {
	?>
<section id="presentazione" aria-describedby="Presentazione del laboratorio" class="section section-muted pt-5">
	<div>
		<div class="container my-12">
			<h2 class="h3 pb-1"><?php echo $etichetta_laboratorio; ?></h2>
			<p>
				<?php
					echo $testo_sezione_laboratorio;
				?>
			</p>
		</div>
	</div>
</section>
<?php
}
?>

<!-- Section CAROUSEL -->
<?php
 if ( 'false' !== $carousel_after_pres ){
?>
<?php get_template_part( 'template-parts/home/carousel' ); ?>
<?php
	}
?>

<!-- Section Featured Contents (Contenuti in evidenza) -->
<?php get_template_part( 'template-parts/home/featured-contents' ); ?>

</main>
<?php
get_footer();
