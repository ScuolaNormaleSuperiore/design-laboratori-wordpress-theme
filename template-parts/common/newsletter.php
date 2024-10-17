<!-- blocco newsletter -->
<?php
$newsletter_enabled = dli_get_option( 'newsletter_enabled', 'setup' );
if ( $newsletter_enabled === 'true' ) {
	$current_language = dli_current_language( 'slug' );
	$page_url         = dli_get_newsletter_link($current_language);
?>
	<h4>
		<a href="#" title="<?php echo __( 'Vai alla pagina: Newsletter', 'design_laboratori_italia' ); ?>">
			<?php echo __( 'Newsletter', 'design_laboratori_italia' ); ?>
		</a>
	</h4>
	<FORM action="<?php echo esc_url( $page_url ); ?>" id="boxnewsletter" name="boxnewsletter" method="POST">
		<div class="form-group">
			<div class="input-group border">
				<div class="input-group-prepend">
						<div class="input-group-text bg-transparent border-white">
							<svg class="icon icon-sm icon-white" role="img" aria-labelledby="Mail">
								<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ?>">
								</use>
							</svg>
						</div>
				</div>
					<input type="text" title="<?php echo __( 'Inserisci il tuo indirizzo email per ricevere aggiornamenti', 'design_laboratori_italia' ); ?>" class="form-control bg-transparent text-white border-white"
						id="user_mail" name="user_mail"
						placeholder="<?php echo __( 'Indirizzo e-mail', 'design_laboratori_italia' ); ?>">
					<?php wp_nonce_field( 'sf_newsletter_nonce', 'newsletter_nonce_field' ); ?>
					<input type="hidden" name="redirection" id="redirection" value="yes" />
					<div class="input-group-append">
						<button class="btn btn-primary bg-transparent text-white text-light border-white border" type="submit" id="button-newsletter-iscriviti">
						<?php echo __( 'Iscrivimi...', 'design_laboratori_italia' ); ?>
						</button>
					</div>
			</div>
		</div>
</FORM>
<?php
}
?>
<!-- fine blocco newsletter -->
