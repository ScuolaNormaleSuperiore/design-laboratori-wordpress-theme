<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
?>
<main id="main-container" class="main-container redbrown">

<!-- Section MAIN HERO  -->
<?php get_template_part( 'template-parts/home/main-hero' ); ?>

<!-- Section CAROUSEL -->
<?php get_template_part( 'template-parts/home/carousel' ); ?>


<!-- PRESENTAZIONE -->
<?php
$testo_sezione_laboratorio = dli_get_configuration_field_by_lang( 'descrizione_laboratorio', 'la_scuola' );
$etichetta_laboratorio = dli_get_configuration_field_by_lang( 'etichetta', 'la_scuola' );

if($testo_sezione_laboratorio) {
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

<!-- BLOCCO CARD -->
<section id="blocco-card" aria-describedby="Blocco news, eventi e pubblicazioni" class="section pt-5" >
	<div class="section-content">
		<div class="container">
		<div class="row">
			<!-- CARD NOTIZIE -->
			<?php get_template_part( 'template-parts/home/card-news' ); ?>
			<!-- CARD EVENTI -->
			<?php get_template_part( 'template-parts/home/card-eventi' ); ?>
			<!-- CARD PUBBLICAZIONI -->
			<?php get_template_part( 'template-parts/home/card-pubblicazioni' ); ?>
			</div>
		</div>
	</div>
</section>
<!-- FINE BLOCCO CARD -->

</main>
<?php
get_footer();
