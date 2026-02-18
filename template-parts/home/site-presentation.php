<!-- PRESENTAZIONE -->
<?php
$section_enabled           = $args['enabled'] ?? false;
$show_title                = $args['show_title'] ?? false;
$testo_sezione_laboratorio = dli_get_configuration_field_by_lang( 'descrizione_laboratorio', 'il_laboratorio' );
$etichetta_laboratorio     = dli_get_configuration_field_by_lang( 'etichetta', 'il_laboratorio' );

if ( $testo_sezione_laboratorio && ( 'true' === $section_enabled)  ) {
	?>
<section id="presentazione" aria-describedby="Presentazione del laboratorio" class="section section-muted">
	<div>
		<div class="container my-12">
			<h2 class="h3 pb-1"><?php echo esc_html( $etichetta_laboratorio ); ?></h2>
			<p>
				<?php echo wp_kses_post( wpautop( $testo_sezione_laboratorio, true ) ); ?>
			</p>
		</div>
	</div>
</section>
<?php
}
?>
