<?php
	$testo_sezione = dli_get_configuration_field_by_lang( 'testo_spinoff', 'spinoff' );
?>

<section id="banner-spinoff" aria-describedby="Testo introduttivo sezione spinoff" class="bg-banner-progetti">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  ">
					<?php echo esc_html( get_the_title() ); ?>
				</h2>
				<p class="font-weight-normal">
					<?php echo wp_kses_post( wpautop( $testo_sezione ) ); ?>
				</p>
			</div>
		</div>
	</div>
</section>
