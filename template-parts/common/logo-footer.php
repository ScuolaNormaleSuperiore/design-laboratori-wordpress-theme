<?php
/**
 * Footer logo partial.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_show_logo_footer = wp_validate_boolean( dli_get_option( 'logo_footer_visible' ) );

if ( true === $dli_show_logo_footer ) {
	$dli_nome_laboratorio = esc_attr( dli_get_option_by_lang( 'nome_laboratorio' ) );
	$dli_logo_header      = esc_url( dli_get_option( 'logo_laboratorio' ) );
	$dli_logo_footer      = esc_url( dli_get_option( 'logo_laboratorio_footer' ) );
	$dli_logo_laboratorio = $dli_logo_footer ? $dli_logo_footer : $dli_logo_header;
	$dli_class_color      = $dli_logo_footer ? '' : 'color-invert';
	$dli_logo_src         = $dli_logo_laboratorio ? $dli_logo_laboratorio : dli_get_default_logo();
	?>
	<!-- Logo -->
	<img height="80" class="<?php echo esc_attr( $dli_class_color ); ?>"
		src="<?php echo esc_url( $dli_logo_src ); ?>"
		alt="<?php echo esc_attr( $dli_nome_laboratorio ); ?>"
		title="<?php echo esc_attr( $dli_nome_laboratorio ); ?>" />
	<?php
}
?>
