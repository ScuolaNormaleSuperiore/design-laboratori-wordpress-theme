<!-- blocco newsletter -->
<?php
$newsletter_enabled = dli_get_option( 'newsletter_enabled', 'setup' );
if ( $newsletter_enabled === 'true' ) {
?>
	<h4>
		<a href="#" title="<?php echo __( 'Vai alla pagina: Newsletter', 'design_laboratori_italia' ); ?>">
			<?php echo __( 'Newsletter', 'design_laboratori_italia' ); ?>
		</a>
	</h4>
	<div class="form-group">
		<div class="input-group border">
			<div class="input-group-prepend">
					<div class="input-group-text bg-transparent border-white">
						<svg class="icon icon-sm icon-white">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ?>">
							</use>
						</svg>
					</div>
			</div>
			<label for="input-group-3" class="text-white text-light"><?php echo __( 'Indirizzo e-mail', 'design_laboratori_italia' ); ?></label>
			<input type="text" class="form-control bg-transparent text-white border-white" id="input-group-3" name="input-group-3">
			<div class="input-group-append">
				<button class="btn btn-primary bg-transparent text-white text-light border-white border" type="button" id="button-3">
				<?php echo __( 'Invio', 'design_laboratori_italia' ); ?>
				</button>
			</div>
		</div>
	</div>
<?php
}
?>
<!-- fine blocco newsletter -->
