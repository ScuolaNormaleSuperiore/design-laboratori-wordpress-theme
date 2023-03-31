<?php
$testo_sezione_luoghi = dli_get_configuration_field_by_lang( 'testo_sezione_luoghi', 'luoghi' );
?>
<section id="banner-luoghi" aria-describedby="Testo introduttivo sezione progetti" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  "><?php _e( 'I Luoghi', 'design_laboratori_italia' ); ?></h2>
				<p class="font-weight-normal"><?php echo $testo_sezione_luoghi; ?></p>
			</div>
		</div>
	</div>
</section> 