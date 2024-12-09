<?php
$testo_sezione_persone = dli_get_configuration_field_by_lang( 'testo_sezione_persone', 'persone' );

?>
<section id="banner-persone" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-persone">
			<div class="section-muted p-3 primary-bg-c1">
				<div class="container">
					<div class="hero-title text-left ms-4 pb-3 pt-3">
						<h2 class="p-0  "><?php _e( 'Le persone', 'design_laboratori_italia' ); ?></h2>
						<p class="font-weight-normal"><?php echo $testo_sezione_persone; ?></p>
					</div>
				</div>
			</div>
		</section>
