<?php
	$testo_sezione = dli_get_configuration_field_by_lang( 'testo_ricerca', 'ricerca' );

?>
<section id="banner-progetti" aria-describedby="Testo introduttivo sezione progetti" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  "><?php echo __( 'Le attività di ricerca', 'design_laboratori_italia' ); ?></h2>
				<p class="font-weight-normal"><?php echo $testo_sezione; ?></p>
			</div>
		</div>
	</div>
</section>

