<?php
	$testo_sezione = dli_get_configuration_field_by_lang( 'testo_brevetti', 'brevetti' );
?>

<section id="banner-progetti" aria-describedby="Testo introduttivo sezione brevetti" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  "><?php _e( 'I brevetti', 'design_laboratori_italia' ); ?></h2>
				<p class="font-weight-normal"><?php echo $testo_sezione; ?></p>
			</div>
		</div>
	</div>
</section>