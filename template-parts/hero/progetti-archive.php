<?php
	$testo_sezione = dli_get_configuration_field_by_lang( 'testo_progetti', 'progetti' );
?>
<section id="banner-progetti" aria-describedby="Testo introduttivo sezione progetti" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  ">
					<?php echo esc_html( get_the_title() ); ?>
				</h2>
				<p class="font-weight-normal"><?php echo __( 'Elenco dei progetti archiviati', 'design_laboratori_italia' ); ?></p>
			</div>
		</div>
	</div>
</section>
