<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items           = ( isset( $args['items'] ) && is_array( $args['items'] ) ) ? $args['items'] : array();
	$dli_section_id  = isset( $args['section_id'] ) ? $args['section_id'] : '';
	$dli_num_results = count( $dli_items );
	define( 'ALLEGATI_PER_ROW', 3 );
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
<?php
	// The mani loop of the page.
	$dli_pindex = 0;
if ( $dli_num_results ) {
	foreach ( $dli_items as $dli_item ) {
		if ( 0 === ( $dli_pindex % ALLEGATI_PER_ROW ) ) {
			?>
			<!-- begin row  allegati-->
			<div class="row pb-3">
				<div class="card-wrapper card-teaser-wrapper">
			<?php
		}
						$dli_post_title = $dli_item['title'];
						$dli_post_url   = $dli_item['url'];
		?>

						<!-- begin card allegati -->
						<div class="card card-teaser rounded shadow ">
							<div class="card-body">
								<h3 class="card-title cardTitlecustomSpacing h5 ">
									<svg class="icon" role="img" aria-labelledby="File PDF">
										<title>File PDF</title>
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf' ); ?>"></use>
									</svg>
									<a href="<?php echo esc_url( $dli_post_url ); ?>" target="_blank"><?php echo esc_attr( $dli_post_title ); ?></a>
								</h3>
							</div>
						</div>
						<!-- end card allegati -->

					<?php
					if ( ( ( $dli_pindex % ALLEGATI_PER_ROW ) === ALLEGATI_PER_ROW - 1 ) || ( $dli_pindex + 1 === $dli_num_results ) ) {
						?>
				</div>
			</div>
			<!-- end row allegati -->
						<?php
					}
					++$dli_pindex;
	}
} else {
	echo '<p>-</p>';
}
?>

</section>
