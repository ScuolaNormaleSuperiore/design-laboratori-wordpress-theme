<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
?>
<section id="<?php echo 'sezione-' . $section_id; ?>">
<?php
	// The main loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % TECHNICAL_RESOURCES_PER_ROW ) == 0 ) {
		?>
			<!-- begin row  person-->
			<div class="row pb-3 pt-3">
			<?php
				}
				$risorsa      = dli_get_post_wrapper( $item, 'medium' );
				$tipo_risorsa = dli_get_post_main_category( $item, RT_TYPE_TAXONOMY );
			?>

				<!-- begin card person -->
				<div class="col-lg-4">
					<div class="avatar-wrapper avatar-extra-text">

						<div class="avatar size-xl">
							<img
								src="<?php echo esc_url( $risorsa['image_url'] ); ?>"
								alt="<?php echo esc_attr( $risorsa['image_alt'] ); ?>"
								title="<?php echo esc_attr( $risorsa['image_title'] ); ?>"
								aria-hidden="true">
						</div>

						<div class="extra-text">
							<a href="<?php echo esc_attr( $risorsa['link'] ); ?>" >
								<?php echo esc_attr( $risorsa['title'] ); ?>
							</a>
							<span>
								<?php echo esc_attr( $tipo_risorsa['title'] ); ?>
							</span>
						</div>

					</div>
				</div>
				<!-- end card person -->

			<?php
				if ( ( ( $pindex % TECHNICAL_RESOURCES_PER_ROW ) === TECHNICAL_RESOURCES_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
			?>
			</div>
			<!-- end row person -->
		<?php
				}
				$pindex++;
			}
		} else {
			echo '<p>-</p>';
		}
		?>

</section>
