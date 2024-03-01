<!-- blocco newsletter -->
<?php
$newsletter_enabled = dli_get_option( 'newsletter_enabled', 'setup' );
if ( $newsletter_enabled === 'true' ) {
	$page_url = get_permalink( get_page_by_path( 'newsletter' ) );
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
					<input type="text" class="form-control bg-transparent text-white border-white"
						id="user_mail" name="user_mail"
						placeholder="<?php echo __( 'Indirizzo e-mail', 'design_laboratori_italia' ); ?>">
					<?php wp_nonce_field( 'sf_newsletter_nonce', 'newsletter_nonce_field' ); ?>
					<input type="hidden" name="redirection" id="redirection" value="yes" />
					<div class="input-group-append">
						<button class="btn btn-primary bg-transparent text-white text-light border-white border" type="submit" id="button-3">
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
