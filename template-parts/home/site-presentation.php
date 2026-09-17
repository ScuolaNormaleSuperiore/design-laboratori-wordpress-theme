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
	<section id="presentazione" class="section section-muted" aria-labelledby="dli-presentazione-title">
		<div class="container my-12">
			<h2 id="dli-presentazione-title" class="h3 pb-1"><?php echo esc_html( $dli_etichetta_laboratorio ); ?></h2>
			<?php echo wp_kses_post( wpautop( $dli_testo_sezione_laboratorio, true ) ); ?>
		</div>
	</section>
	<?php
}
?>
