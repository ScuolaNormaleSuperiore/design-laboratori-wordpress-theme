<?php
if ( dli_get_option( "logo_laboratorio" ) ) {
	$file_name = dli_get_option( "logo_laboratorio" );
	if( pathinfo($file_name, PATHINFO_EXTENSION) !== 'svg') {
?>
		<img width="82"  class="color-invert" src="<?php echo dli_get_option( "logo_laboratorio" );?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />
	<?php 
	} else {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="82" height="82" class="color-invert" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>">
		<?php
			echo file_get_contents( $file_name );
		?>
		</svg>
		<?php
	}
}
else { ?>

	<img width="82" class="color-invert" src="<?php echo dli_get_default_logo();?>" alt="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" title="<?php echo dli_get_option( 'nome_laboratorio' ); ?>" />

<?php } ?>