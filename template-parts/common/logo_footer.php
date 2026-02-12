<?php
$show_logo_footer = esc_attr( text: dli_get_option( 'logo_footer_visible' ) );

if ( $show_logo_footer === 'true' ) {
	$nome_laboratorio = esc_attr( dli_get_option_by_lang( 'nome_laboratorio' ) );
	$logo_header      = esc_url( dli_get_option( "logo_laboratorio" ) );
	$logo_footer      = esc_url( dli_get_option( "logo_laboratorio_footer" ) );
	$logo_laboratorio = esc_url( $logo_footer ? $logo_footer : $logo_header );
	$class_color      = $logo_footer ? '' : 'color-invert';

	if ( $logo_laboratorio ) {
	?>
			<!-- Logo -->
			<img width="82" class="<?php echo esc_attr( $class_color ); ?>" 
				src="<?php echo esc_url( $logo_laboratorio ); ?>" 
				alt="<?php echo esc_attr( $nome_laboratorio ); ?>" 
				title="<?php echo esc_attr( $nome_laboratorio ); ?>" />
	<?php
	}
	else { ?>

		<!-- Logo di default -->
		<img width="82" class="<?php echo esc_attr( $class_color ); ?>" 
		src="<?php echo esc_url( dli_get_default_logo() );?>" 
		alt="<?php echo esc_attr( $nome_laboratorio ); ?>" 
		title="<?php echo esc_attr( $nome_laboratorio ); ?>" />
	<?php
	}
}
?>
