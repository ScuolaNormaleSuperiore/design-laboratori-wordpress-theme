<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	define( 'EVENTI_PER_ROW', 3 );
?>
<section id="<?php echo esc_attr( 'sezione-' . $section_id ); ?>">
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
					$id             = $item->ID;
					$foto           = get_field( 'foto', $id );
					$link           = get_the_permalink( $id );
					$desc           = get_field( 'descrizione_breve', $id );
					$title          = get_the_title( $id );
					$date           = get_field( 'data_inizio', $id );
					$orario_inizio  = get_field( 'orario_inizio', $id );
					$event_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $date );
					$event_day      = $event_date ? intval( $event_date->format( 'd' ) ) : '';
					$event_month    = $event_date ? dli_get_monthname( $event_date->format( 'm' ) ) : '';
					$event_year     = $event_date ? intval( $event_date->format( 'Y' ) ) : '';
					$image_metadata = dli_get_image_metadata( $item, 'medium', '/assets/img/img-avatar-250x250.png' );

					?>
						<!--begin card eventi-->
						<div class="col-12 col-lg-4">
							<div class="card-wrapper">
								<div class="card card-img no-after card-bg">
									<div class="img-responsive-wrapper">
										<div class="img-responsive img-responsive-panoramic">
											<figure class="img-wrapper">
											<img src="<?php echo esc_url( $image_metadata['image_url'] ); ?>"
												title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
												alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
											</figure>
											<?php if ( $event_date ) { ?>
											<div class="card-calendar d-flex flex-column justify-content-center">
												<span class="card-date">
													<?php echo esc_html( $event_day ); ?>
												</span>
												<span class="card-day">
													<?php echo esc_html( __( $event_month, 'design_laboratori_italia' ) ); ?> <?php echo esc_html( $event_year ); ?>
												</span>
											</div>
											<?php } ?>
										</div>
									</div>
									<div class="card-body p-4">
										<h3 class="card-title cardTitlecustomSpacing h4"><?php echo esc_html( $title ); ?></h3>
										<p class="card-text"><?php echo wp_kses_post( get_field( 'descrizione_breve' ) ); ?></p>
										<?php if ( $orario_inizio ) {
										?>
											<p class="card-text">
												<?php echo esc_html( $orario_inizio ); ?>
											</p>
										<?php
										}
										?>
										<a class="read-more" href="<?php echo esc_url( $link ); ?>">
											<span class="text customSpacing"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
											<span class="visually-hidden"><?php echo wp_kses_post( $desc ); ?></span>
											<svg class="icon" role="img" aria-labelledby="Arrow right">
												<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
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
