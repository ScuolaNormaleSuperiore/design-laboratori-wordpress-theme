<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_item = $args['item'];
if ( $dli_item ) {
	$dli_id             = $dli_item->ID;
	$dli_foto           = dli_get_field( 'foto', $dli_id );
	$dli_link           = get_the_permalink( $dli_id );
	$dli_desc           = dli_get_field( 'descrizione_breve', $dli_id );
	$dli_title          = get_the_title( $dli_id );
	$dli_date           = dli_get_field( 'data_inizio', $dli_id );
	$dli_orario_inizio  = dli_get_field( 'orario_inizio', $dli_id );
	$dli_event_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
	$dli_event_day      = $dli_event_date ? intval( $dli_event_date->format( 'd' ) ) : '';
	$dli_event_month    = $dli_event_date ? dli_get_monthname( $dli_event_date->format( 'm' ) ) : '';
	$dli_event_year     = $dli_event_date ? intval( $dli_event_date->format( 'Y' ) ) : '';
	$dli_image_metadata = dli_get_image_metadata( $dli_item, 'medium', '/assets/img/img-avatar-250x250.png' );
	?>

	<!--start card-->
	<div class="card card-img no-after card-bg">
		<div class="img-responsive-wrapper">
			<div class="img-responsive img-responsive-panoramic">
				<figure class="img-wrapper">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
				</figure>
				<?php if ( $dli_event_date ) { ?>
				<div class="card-calendar d-flex flex-column justify-content-center">
					<span class="card-date">
						<?php echo esc_html( $dli_event_day ); ?>
					</span>
					<span class="card-day">
							<?php echo esc_html( $dli_event_month ); ?> <?php echo esc_html( $dli_event_year ); ?>
					</span>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="card-body p-4">
			<h3 class="card-title h4"><?php echo esc_html( $dli_title ); ?></h3>
			<p class="card-text"><?php echo wp_kses_post( $dli_desc ); ?></p>
			<?php
			if ( $dli_orario_inizio && false ) {
				?>
				<p class="card-text">
					<?php echo esc_html( $dli_orario_inizio ); ?>
				</p>
				<?php
			}
			?>
			<p class="pt-1">
				<a class="read-more" href="<?php echo esc_url( $dli_link ); ?>">
					<span class="text customSpacing"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
					<span class="visually-hidden"><?php echo wp_kses_post( $dli_desc ); ?></span>
					<svg class="icon" role="img" aria-labelledby="Arrow right" aria-label="<?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
						<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
					</svg>
				</a>
			</p>
		</div>
	</div>
	<!--end card-->

	<?php
}
?>
