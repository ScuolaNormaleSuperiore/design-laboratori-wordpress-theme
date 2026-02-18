<?php
/**
 * Homepage site presentation section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled           = $args['enabled'] ?? false;
$dli_show_title                = $args['show_title'] ?? false;
$dli_testo_sezione_laboratorio = dli_get_configuration_field_by_lang( 'descrizione_laboratorio', 'il_laboratorio' );
$dli_etichetta_laboratorio     = dli_get_configuration_field_by_lang( 'etichetta', 'il_laboratorio' );

if ( $dli_testo_sezione_laboratorio && ( 'true' === $dli_section_enabled ) ) {
	?>
<section id="presentazione" aria-describedby="Presentazione del laboratorio" class="section section-muted">
	<div>
		<div class="container my-12">
			<h2 class="h3 pb-1"><?php echo esc_html( $dli_etichetta_laboratorio ); ?></h2>
			<p>
				<?php echo wp_kses_post( wpautop( $dli_testo_sezione_laboratorio, true ) ); ?>
			</p>
		</div>
	</div>
</section>
	<?php
}
?>
