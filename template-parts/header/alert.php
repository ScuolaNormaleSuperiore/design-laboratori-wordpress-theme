<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_messages = dli_get_option( 'messages', 'home_messages' );
if ( is_array( $dli_messages ) && ! empty( $dli_messages ) ) {
	$dli_allowed_alert_colors = array( 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark' );

	foreach ( $dli_messages as $dli_msg ) {
		if ( ! isset( $dli_msg['testo_message'] ) ) {
			continue;
		}

		$dli_message_text = esc_html( (string) $dli_msg['testo_message'] );
		if ( '' === $dli_message_text ) {
			continue;
		}

		$dli_raw_color     = isset( $dli_msg['colore_message'] ) ? sanitize_key( $dli_msg['colore_message'] ) : '';
		$dli_message_color = in_array( $dli_raw_color, $dli_allowed_alert_colors, true ) ? $dli_raw_color : 'info';
		$dli_message_link  = isset( $dli_msg['link_message'] ) ? esc_url( $dli_msg['link_message'] ) : '';
		?>
	<div class="container my-12 p-2">
		<div class="alert alert-<?php echo esc_attr( $dli_message_color ); ?> alert-dismissible fade show mb-0" role="alert">

			<?php
			if ( ! empty( $dli_message_link ) ) {
				?>
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_message_link ); ?>">
				<?php
			}
			?>
					<?php echo esc_html( $dli_message_text ); ?>
			<?php
			if ( ! empty( $dli_message_link ) ) {
				?>
			</a>
				<?php
			}
			?>

			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
				<svg class="icon" role="img" aria-labelledby="Close" aria-label="<?php echo esc_attr__( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
					<title><?php echo esc_html__( 'Chiudi avviso', 'design_laboratori_italia' ); ?></title>
					<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close' ); ?>"></use>
				</svg>
			</button>
		</div>
	</div>
		<?php
	}
}
?>
