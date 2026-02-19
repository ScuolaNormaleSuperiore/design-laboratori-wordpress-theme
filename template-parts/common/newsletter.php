<?php
/**
 * Newsletter widget block.
 *
 * @package Design_Laboratori_Italia
 */

$dli_newsletter_enabled = dli_get_option( 'newsletter_enabled', 'setup' );

if ( 'true' === $dli_newsletter_enabled ) {
	$dli_current_language = dli_current_language( 'slug' );
	$dli_page_url         = dli_get_newsletter_link( $dli_current_language );
	?>
	<h4>
		<a href="<?php echo esc_url( $dli_page_url ); ?>" title="<?php echo esc_attr__( 'Vai alla pagina: Newsletter', 'design_laboratori_italia' ); ?>">
			<?php echo esc_html__( 'Newsletter', 'design_laboratori_italia' ); ?>
		</a>
	</h4>

	<div class="pb-2 bg-dark bg-transparent ">
		<p>
			<?php echo esc_html__( 'Ricevi via email la nostra newsletter', 'design_laboratori_italia' ); ?>
		</p>
		<a href="<?php echo esc_url( $dli_page_url ); ?>"
				title="<?php echo esc_html__( 'Vai alla pagina: Newletter', 'design_laboratori_italia' ); ?>"
				class="btn-icon btn btn-outline-primary">
			<span>
				<?php echo esc_html__( 'Iscrivimi...', 'design_laboratori_italia' ); ?>
			</span>
		</a>
	</div>

	<?php
}
?>
