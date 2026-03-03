<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_testo_sezione = dli_get_configuration_field_by_lang( 'testo_progetti', 'progetti' );
?>
<section id="banner-progetti" class="bg-banner-progetti" aria-labelledby="dli-hero-progetti-archive-title">
	<div class="section-muted p-3 primary-bg-c1">
		<div class="container">
			<div class="hero-title text-left ms-4 pb-3 pt-3">
				<h2 id="dli-hero-progetti-archive-title" class="p-0  ">
					<?php echo esc_html( get_the_title() ); ?>
				</h2>
				<p class="font-weight-normal"><?php echo esc_html__( 'Elenco dei progetti archiviati', 'design_laboratori_italia' ); ?></p>
			</div>
		</div>
	</div>
</section>
