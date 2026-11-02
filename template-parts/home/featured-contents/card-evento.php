<?php
/**
 * Item of a box of "Contenuti in evidenza" for an event: title, date, place and category, with no image.
 *
 * Laid out like the card of the playground: the title first, then a body with the date and time of the event
 * (its own, `dli_get_event_datetime_display()`, not the date of publication) and the place, then a footer with
 * the category. Receives the content already read and prints only its markup. The title leads where the editor
 * chose (`dli_get_content_link()`). A line that the event does not have (place, category, date) is not printed.
 * The category leads to the events page filtered by it when that page exists (`archive_url`), otherwise it is text.
 *
 * Arguments (`$args`):
 *  - `post`        (WP_Post) The event.
 *  - `archive_url` (string)  Address of the events page, used by the link of the category.
 *  - `heading`     (string)  Tag of the title, `h4` by default.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args        = isset( $args ) && is_array( $args ) ? $args : array();
$dli_item        = isset( $dli_args['post'] ) && $dli_args['post'] instanceof WP_Post ? $dli_args['post'] : null;
$dli_heading     = isset( $dli_args['heading'] ) && in_array( $dli_args['heading'], array( 'h2', 'h3', 'h4', 'h5' ), true ) ? $dli_args['heading'] : 'h4';
$dli_archive_url = isset( $dli_args['archive_url'] ) ? (string) $dli_args['archive_url'] : '';

if ( null === $dli_item ) {
	return;
}

$dli_link  = dli_get_content_link( $dli_item );
$dli_start = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, (string) dli_get_field( 'data_inizio', $dli_item->ID ) );
// A date over several days is written on one line here: the helper breaks it with a line break for the lists.
$dli_when  = trim( str_replace( array( '<br>', '<br />', '<br/>' ), ' ', dli_get_event_datetime_display( $dli_item->ID ) ) );
$dli_place = trim( (string) dli_get_field( 'luogo', $dli_item->ID ) );
$dli_terms = get_the_terms( $dli_item, WP_DEFAULT_CATEGORY );
$dli_term  = is_array( $dli_terms ) && ! empty( $dli_terms ) ? $dli_terms[0] : null;
?>
<article class="it-card border-bottom">
	<<?php echo esc_attr( $dli_heading ); ?> class="it-card-title h5 text-break" style="overflow-wrap: anywhere;">
		<?php
		get_template_part(
			'template-parts/common/linked-title',
			null,
			array(
				'title'    => get_the_title( $dli_item ),
				'url'      => $dli_link['url'],
				'external' => $dli_link['external'],
			)
		);
		?>
	</<?php echo esc_attr( $dli_heading ); ?>>
	<?php if ( ( $dli_start && '' !== $dli_when ) || '' !== $dli_place ) : ?>
		<div class="it-card-body">
			<?php if ( $dli_start && '' !== $dli_when ) : ?>
				<p class="it-card-subtitle"><span class="visually-hidden"><?php esc_html_e( 'Data evento:', 'design_laboratori_italia' ); ?></span> <time datetime="<?php echo esc_attr( $dli_start->format( 'Y-m-d' ) ); ?>"><?php echo esc_html( $dli_when ); ?></time></p>
			<?php endif; ?>
			<?php if ( '' !== $dli_place ) : ?>
				<p class="it-card-text mb-0" style="overflow-wrap: anywhere;"><span class="visually-hidden"><?php esc_html_e( 'Luogo:', 'design_laboratori_italia' ); ?></span> <?php echo esc_html( $dli_place ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( $dli_term ) : ?>
		<footer class="it-card-footer">
			<div class="it-card-taxonomy">
				<?php if ( '' !== $dli_archive_url ) : ?>
					<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'cat', array( $dli_term->term_id ), $dli_archive_url ) ); ?>">
						<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
						<?php echo esc_html( $dli_term->name ); ?>
					</a>
				<?php else : ?>
					<span class="it-card-category">
						<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
						<?php echo esc_html( $dli_term->name ); ?>
					</span>
				<?php endif; ?>
			</div>
		</footer>
	<?php endif; ?>
</article>
