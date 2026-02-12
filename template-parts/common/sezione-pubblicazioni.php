<?php
	$publications = $args['items'];
	$section_id   = $args['section_id'];
	$num_results  = is_array( $publications ) ? count( $publications ) : 0;
	define( 'PUBBLICAZIONI_PER_ROW', 2 );
?>

<section id="<?php echo esc_attr( 'sezione-' . $section_id ); ?>">
<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $publications as $publ ) {
			if ( ( $pindex % PUBBLICAZIONI_PER_ROW ) == 0 ) {
		?>
				<!-- begin row pubblicazioni -->
				<div class="row pb-3 col-sm-12">
					<div class="card-wrapper card-teaser-wrapper">
						<?php
							}
							$id       = $publ->ID;
							$src_icon = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note';
							$nome     = get_the_title( $id );
							$link     = get_permalink( $id );
							$content  = $publ->post_content;
						?>
								<!--begin card pubblicazioni -->
								<div class="card card-teaser rounded shadow ">
									<div class="card-body">
										<h3 class="card-title cardTitlecustomSpacing h5 ">
											<svg class="icon" aria-label="Note">
												<title>Note</title>
												<use href="<?php echo esc_url( $src_icon ); ?>"></use>
											</svg>
											<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $nome ); ?></a>
										</h3>
										<div class="card-text">
											<p><?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?></p>
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
