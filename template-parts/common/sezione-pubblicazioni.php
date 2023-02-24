<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	define( 'PUBBLICAZIONI_PER_ROW', 2 );
?>

<section id="<?php echo $section_id; ?>">
<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % PUBBLICAZIONI_PER_ROW ) == 0 ) {
		?>
				<!-- begin row pubblicazioni -->
				<div class="row pb-3 col-sm-12">
					<div class="card-wrapper card-teaser-wrapper">
						<?php
							}
							$id          = $item->ID;
							$src_icon    = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note';
							$nome        = get_the_title( $id);
							$abstract    = get_field( 'abstract', $id );
							$link        = get_permalink( $id );
						?>
								<!--begin card pubblicazioni -->
								<div class="card card-teaser rounded shadow ">
									<div class="card-body">
										<h3 class="card-title h5 ">
											<svg class="icon">
												<use href="<?php echo $src_icon; ?>"></use>
											</svg>
											<a href="<?php echo $link; ?>"><?php echo $nome; ?></a>
										</h3>
										<div class="card-text">
											<p><?php echo $abstract ?></p>
										</div>
									</div>
								</div>
								<!--end card pubblicazioni-->
							<?php
								if ( ( ( $pindex % PUBBLICAZIONI_PER_ROW ) === PUBBLICAZIONI_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
							?>
					</div>
				</div>
				<!-- end row pubblicazioni -->
		<?php
				}
				$pindex++;
			}
		} else {
		echo '<p>-</p>';
		}
		?>
</section>
