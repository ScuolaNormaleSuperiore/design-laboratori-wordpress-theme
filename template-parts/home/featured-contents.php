<?php
	$featuredcontents_enabled = dli_get_option( 'home_featuredcontents_is_visible', 'homepage' );

	if ( 'true' === $featuredcontents_enabled ) {
?>
<!-- BLOCCO CARD - CONTENUTI IN EVIDENZA (Featured contents) -->
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
<?php
	}
?>
