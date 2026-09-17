<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_show_logo_header = esc_attr( dli_get_option( 'logo_header_visible' ) );

if ( 'false' !== $dli_show_logo_header ) {
	$dli_file_name = esc_url( dli_get_option( 'logo_laboratorio' ) );

	if ( $dli_file_name ) {
		?>

		<!-- Logo -->
		<img src="<?php echo esc_url( $dli_file_name ); ?>"
			alt=""
			height="80"/>

		<?php
	} else {
		?>

		<!-- Logo di default -->
		<img src="<?php echo esc_url( dli_get_default_logo() ); ?>"
			alt=""
			height="80"/>

		<?php
	}
}
?>
