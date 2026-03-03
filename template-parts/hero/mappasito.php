<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_testo_sezione_persone = dli_get_configuration_field_by_lang( 'testo_sezione_persone', 'persone' );

?>
<section id="banner-persone" aria-describedby="Testo introduttivo sezione persone" class="bg-banner-persone">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 class="p-0  "><?php echo esc_html__( 'Mappa del sito', 'design_laboratori_italia' ); ?></h2>
				<p class="font-weight-normal"><?php echo esc_html__( 'Elenco delle pagine del sito', 'design_laboratori_italia' ); ?></p>
			</div>
		</div>
	</div>
</section>
