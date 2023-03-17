<?php
$messages = dli_get_option( 'messages', 'home_messages' );
if ( $messages && ! empty($messages) ) {
	foreach ( $messages as $msg ) {
	if ( isset( $msg['testo_message'] ) ) {
?>
	<div class="container my-12 p-2">
		<div class="alert alert-<?php echo $msg['colore_message']; ?> alert-dismissible fade show mb-0" role="alert">

			<?php
			if ( $msg['link_message'] !== '' ) {
			?>
			<a target="_blank" href="<?php echo $msg['link_message']; ?>">
			<?php
			}
			?>
				<?php echo $msg['testo_message']; ?>
			<?php
			if ( $msg['link_message'] !== '' ) {
			?>
			</a>
			<?php
			}
			?>

			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __( 'Chiudi avviso', 'design_laboratori_italia' ); ?>">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close'; ?>"></use>
				</svg>
			</button>
		</div>
	</div>
<?php
	}
	}
}
?>