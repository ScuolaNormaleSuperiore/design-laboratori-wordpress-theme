<?php
/**
 * Homepage indirizzi section.
 *
 * @package Design_Laboratori_Italia
 */

global $post, $tipologia_servizio, $ct;

$dli_container_class = 'bg-white';
if ( $ct % 2 ) {
	$dli_container_class = 'bg-gray-light';
}

?>

<section class="section <?php echo esc_attr( $dli_container_class ); ?> py-5">
	<div class="container">
		<div class="title-section mb-5">
			<h2 class="h4"><?php esc_html_e( 'Indirizzi di studio', 'design_laboratori_italia' ); ?></h2>
		</div><!-- /title-large -->
		<div class="row variable-gutters">
			<?php
			$dli_args      = array(
				'post_type'      => 'indirizzo',
				'posts_per_page' => 9,
			);
			$dli_indirizzi = get_posts( $dli_args );
			foreach ( $dli_indirizzi as $dli_servizio ) {
				$GLOBALS['servizio'] = $dli_servizio;
				?>
			<div class="col-lg-4 mb-4">
				<?php get_template_part( 'template-parts/servizio/card' ); ?>
			</div>
			<?php } ?>

		</div><!-- /row -->
		<div class="pt-3 text-center">
			<a class="text-underline" href="<?php echo esc_url( get_post_type_archive_link( 'indirizzo' ) ); ?>"><strong><?php esc_html_e( 'Vedi tutti', 'design_laboratori_italia' ); ?></strong></a>
		</div>
	</div><!-- /container -->
</section><!-- /section -->
