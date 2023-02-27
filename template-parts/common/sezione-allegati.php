<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = count( $items );
	define( 'ALLEGATI_PER_ROW', 3 );
?>

<section id="<?php echo $section_id; ?>">
<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % ALLEGATI_PER_ROW ) == 0 ) {
		?>
			<!-- begin row  allegati-->
			<div class="row pb-3">
				<div class="card-wrapper card-teaser-wrapper">
					<?php
						}
						$id         = $item[ 'ID' ];
						$post_title = $item[ 'title' ];
						$post_url   = $item[ 'url' ];
					?>

						<!-- begin card allegati -->
						<div class="card card-teaser rounded shadow ">
							<div class="card-body">
								<h3 class="card-title h5 ">
									<svg class="icon">
										<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-file-pdf'; ?>"></use>
									</svg>
									<a href="<?php echo esc_url( $post_url ) ; ?>" target="_blank"><?php echo esc_attr( $post_title ) ; ?></a>
								</h3>
							</div>
						</div>
						<!-- end card allegati -->

					<?php
						if ( ( ( $pindex % ALLEGATI_PER_ROW ) === ALLEGATI_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
			?>
				</div>
			</div>
			<!-- end row allegati -->
		<?php
				}
				$pindex++;
			}
		} else {
			echo '<p>-</p>';
		}
		?>

</section>
