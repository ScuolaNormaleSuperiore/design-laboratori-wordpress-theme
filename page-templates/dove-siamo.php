<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

?>

<main id="main-container" class="main-container bluelectric">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>


	<section id="banner-dove.siamo" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-contatti">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0  "><?php echo __( 'Dove siamo', 'design_laboratori_italia' ); ?></h2>
					<p class="font-weight-normal">Puoi trovarci qui</p>
				</div>
			</div>
		</div>
	</section>

	<div class="container my-4 pt-4">     
		<!-- DOVE SIAMO-->
		<div class="row">
			<div class="col-12 col-lg-3 border-end pe-0 ps-0">
				<div class="it-list-wrapper pt-4">
					<h3 class="h6 text-uppercase border-bottom ps-3"><?php echo __( 'Indirizzo', 'design_laboratori_italia' ); ?></h3>

				</div>
			</div><!-- /col-lg-3 -->


			<div class="col-lg-9">
				<div class="row ">
					Qui va la mappa
				</div>
			</div>

		</div>
	</div>
	
</main>

<?php
get_footer();
