<?php
$item = $args['item'];
if ( $item ) {
	$id             = $item->ID;
	$foto           = dli_get_field( 'foto', $id );
	$link           = get_the_permalink( $id );
	$desc           = dli_get_field( 'descrizione_breve', $id );
	$title          = get_the_title( $id );
	$date           = dli_get_field( 'data_inizio', $id );
	$orario_inizio  = dli_get_field( 'orario_inizio', $id );
	$event_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $date );
	$event_day      = $event_date ? intval( $event_date->format( 'd' ) ) : '';
	$event_month    = $event_date ? dli_get_monthname( $event_date->format( 'm' ) ) : '';
	$event_year     = $event_date ? intval( $event_date->format( 'Y' ) ) : '';
	$image_metadata = dli_get_image_metadata( $item, 'medium', '/assets/img/img-avatar-250x250.png' );
?>

	<!--start card-->
	<div class="card card-img no-after card-bg">
		<div class="img-responsive-wrapper">
			<div class="img-responsive img-responsive-panoramic">
				<figure class="img-wrapper">
					<img src="<?php echo $image_metadata['image_url']; ?>"
						title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>"
						alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
				</figure>
				<?php if ( $event_date ) { ?>
				<div class="card-calendar d-flex flex-column justify-content-center">
					<span class="card-date">
						<?php echo $event_day; ?>
					</span>
					<span class="card-day">
						<?php echo __( $event_month, 'design_laboratori_italia' ); ?> <?php echo $event_year; ?>
					</span>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="card-body p-4">
			<h3 class="card-title h4"><?php echo esc_attr( $title ); ?></h3>
			<p class="card-text"><?php echo esc_attr( $desc ); ?></p>
			<?php if ( $orario_inizio && false ) {
			?>
				<p class="card-text">
					<?php echo $orario_inizio; ?>
				</p>
			<?php
			}
			?>
			<p class="pt-1">
				<a class="read-more" href="<?php echo esc_url( $link ); ?>">
					<span class="text customSpacing"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ) ?></span>
					<span class="visually-hidden"><?php echo $desc; ?></span>
					<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?>">
						<title><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right'; ?>"></use>
					</svg>
				</a>
			</p>
		</div>
	</div>
	<!--end card-->

<?php
}
?>
