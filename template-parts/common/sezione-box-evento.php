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
	?>
	<article class="it-card rounded border shadow-sm h-100">
		<h3 class="it-card-title h5">
			<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_title ); ?></a>
		</h3>
		<div class="it-card-body">
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
