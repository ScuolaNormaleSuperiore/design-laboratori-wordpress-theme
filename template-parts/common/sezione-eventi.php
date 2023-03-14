<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	define( 'EVENTI_PER_ROW', 3 );
?>
<section id="<?php echo $section_id; ?>">
	<div class="section-content">
	<?php
		// The mani loop of the page.
		$pindex = 0;
		if ( $num_results ) {
			foreach ( $items as $item ) {
				if ( ( $pindex % EVENTI_PER_ROW ) == 0 ) {
	?>
					<!-- begin row eventi -->
					<div class="row pt-3">
					<?php
					}
					$id         = $item->ID;
					$foto       = get_field( 'foto', $id );
					$link       = get_the_permalink( $id );
					$desc       = get_field( 'descrizione_breve', $id );
					$title      = get_the_title( $id );
					$date       = get_field( 'data_inizio', $id );
					$event_date = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, $date );
					$img_link   = get_the_post_thumbnail_url( $id , 'item-thumb' );
					if ( ! $img_link ) {
						$img_link = get_template_directory_uri() . '/assets/img/img-avatar-250x250.png';
					}
					?>
						<!--begin card eventi-->
						<div class="col-12 col-lg-4">
							<div class="card-wrapper">
								<div class="card card-img no-after card-bg">
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
											<img src="<?php echo $img_link; ?>"
												title="<?php echo esc_attr( $title ); ?>" alt="<?php echo esc_attr( $title ); ?>">
											</figure>
											<div class="card-calendar d-flex flex-column justify-content-center">
												<span class="card-date">
													<?php echo $event_date->format( 'd' ); ?>
												</span>
												<span class="card-day">
													<?php echo __( dli_get_monthname( $event_date->format( 'm' ), 'design_laboratori_italia' ) ); ?>
												</span>
											</div>
										</div>
									</div>
									<div class="card-body p-4">
										<h3 class="card-title h4"><?php echo esc_attr( $title ); ?></h3>
										<p class="card-text"><?php echo esc_attr( get_field( 'descrizione_breve' ) ); ?></p>
										<a class="read-more" href="<?php echo esc_url( $link ); ?>">
											<span class="text"><?php echo __('Leggi di più', 'design_laboratori_italia' ) ?></span>
											<span class="visually-hidden"><?php echo $desc; ?></span>
											<svg class="icon">
												<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
											</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!--end card eventi -->

					<?php
						if ( ( ( $pindex % EVENTI_PER_ROW ) === EVENTI_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
					?>
					</div>
					<!-- end row eventi -->
			<?php
					}
					$pindex++;
				}
			} else {
			echo '<p>-</p>';
			}
			?>
	</div>
</section>
