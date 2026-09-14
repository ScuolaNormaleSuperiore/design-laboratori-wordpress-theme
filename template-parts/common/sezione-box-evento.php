<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_item = $args['item'];
if ( $dli_item ) {
	$dli_id             = $dli_item->ID;
	$dli_link           = get_the_permalink( $dli_id );
	$dli_desc           = dli_get_field( 'descrizione_breve', $dli_id );
	$dli_title          = get_the_title( $dli_id );
	$dli_date           = dli_get_field( 'data_inizio', $dli_id );
	$dli_event_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
	$dli_event_day      = $dli_event_date ? intval( $dli_event_date->format( 'd' ) ) : '';
	$dli_event_month    = $dli_event_date ? dli_get_monthname( $dli_event_date->format( 'm' ) ) : '';
	$dli_event_year     = $dli_event_date ? intval( $dli_event_date->format( 'Y' ) ) : '';
	$dli_event_subtitle = $dli_event_date ? trim( $dli_event_day . ' ' . $dli_event_month . ' ' . $dli_event_year ) : '';
	$dli_image_metadata = dli_get_image_metadata( $dli_item, 'medium', '/assets/img/img-avatar-250x250.png' );
	?>
	<article class="it-card it-card-image it-card-height-full rounded shadow-sm border">
		<h3 class="it-card-title h4">
			<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_title ); ?></a>
		</h3>
		<div class="it-card-image-wrapper">
			<div class="ratio ratio-16x9">
				<figure class="figure img-full">
					<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
						title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
						alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
				</figure>
			</div>
		</div>
		<div class="it-card-body p-4">
			<?php if ( $dli_event_subtitle ) : ?>
				<p class="it-card-subtitle"><?php echo esc_html( $dli_event_subtitle ); ?></p>
			<?php endif; ?>
			<p class="it-card-text"><?php echo wp_kses_post( $dli_desc ); ?></p>
		</div>
		<?php if ( $dli_event_date ) : ?>
			<footer class="it-card-footer">
				<time class="it-card-date" datetime="<?php echo esc_attr( $dli_event_date->format( 'Y-m-d' ) ); ?>">
					<?php echo esc_html( $dli_event_subtitle ); ?>
				</time>
			</footer>
		<?php endif; ?>
	</article>
	<?php
}
?>
