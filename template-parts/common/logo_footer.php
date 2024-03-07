<?php
if ( dli_get_option( 'logo_footer_visible' ) === 'true' ) {
	$logo_header      = esc_url( dli_get_option( "logo_laboratorio" ) );
	$logo_footer      = esc_url( dli_get_option( "logo_laboratorio_footer" ) );
	$logo_laboratorio = esc_url( $logo_footer ? $logo_footer : $logo_header );
	$class_color      = $logo_footer ? '' : 'color-invert';

	if ( $logo_laboratorio ) {
		$file_name = $logo_laboratorio;
		if( pathinfo($file_name, PATHINFO_EXTENSION) !== 'svg') {
	?>
			<img width="82" class="<?php echo $class_color; ?>" src="<?php echo $logo_laboratorio;?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />
		<?php 
		} else {
			?>
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="82" height="82" class="<?php echo $class_color; ?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>">
			<?php
				echo file_get_contents( $file_name );
			?>
			</svg>
			<?php
		}
	}
	else { ?>
		<img width="82" class="<?php echo $class_color; ?>" src="<?php echo dli_get_default_logo();?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />
	<?php
	}
}
?>