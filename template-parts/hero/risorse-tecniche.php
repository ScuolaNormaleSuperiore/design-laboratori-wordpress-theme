<?php
	$testo_sezione = dli_get_configuration_field_by_lang( 'testo_risorse_tecniche', 'risorse-tecniche' );
?>

<section id="banner-risorse-tecniche" aria-describedby="Testo introduttivo sezione risorse tecniche" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  ">
					<?php echo get_the_title(); ?>
				</h2>
				<p class="font-weight-normal">
					<?php echo $testo_sezione; ?>
				</p>
			</div>
		</div>
	</div>
</section>
