<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items       = $args['items'];
$dli_section_id  = $args['section_id'];
$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
?>
<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
<?php
	// The main loop of the page.
	$dli_pindex = 0;
if ( $dli_num_results ) {
	foreach ( $dli_items as $dli_item ) {
		if ( ( $dli_pindex % TECHNICAL_RESOURCES_PER_ROW ) === 0 ) {
			?>
			<!-- begin row  person-->
			<div class="row pb-3 pt-3">
			<?php
		}
			$dli_risorsa      = dli_get_post_wrapper( $dli_item, 'medium' );
			$dli_tipo_risorsa = dli_get_post_main_category( $dli_item, RT_TYPE_TAXONOMY );
		?>

				<!-- begin card person -->
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">

						<div class="avatar size-xl">
							<img
								src="<?php echo esc_url( $dli_risorsa['image_url'] ); ?>"
								alt="<?php echo esc_attr( $dli_risorsa['image_alt'] ); ?>"
								title="<?php echo esc_attr( $dli_risorsa['image_title'] ); ?>"
								aria-hidden="true">
						</div>

						<div class="extra-text">
							<a href="<?php echo esc_url( $dli_risorsa['link'] ); ?>" >
							<?php echo esc_html( $dli_risorsa['title'] ); ?>
							</a>
							<span>
							<?php echo esc_html( $dli_tipo_risorsa['title'] ); ?>
							</span>
						</div>

					</div>
				</div>
				<!-- end card person -->

			<?php
			if ( ( ( $dli_pindex % TECHNICAL_RESOURCES_PER_ROW ) === TECHNICAL_RESOURCES_PER_ROW - 1 ) || ( $dli_pindex + 1 === $dli_num_results ) ) {
				?>
			</div>
			<!-- end row person -->
				<?php
			}
			++$dli_pindex;
	}
} else {
	echo '<p>-</p>';
}
?>

</section>
