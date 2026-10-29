<?php
/**
 * Content of the card of a publication: title, authors, type and year.
 *
 * Shared by the archive and the homepage, so that they show the same thing. The title leads where the
 * publication says (see `dli_get_publication_link()`); the type leads to the archive filtered by that
 * type. A password-protected publication shows only its title and type: authors and year are part of
 * what the password protects.
 *
 * Arguments (`$args`):
 *  - `post_id`     (int)    The publication.
 *  - `archive_url` (string) Address of the publications page, used by the links of the types.
 *  - `heading`     (string) Tag of the title, `h3` by default.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args        = isset( $args ) && is_array( $args ) ? $args : array();
$dli_item_id     = isset( $dli_args['post_id'] ) ? (int) $dli_args['post_id'] : 0;
$dli_archive_url = isset( $dli_args['archive_url'] ) ? (string) $dli_args['archive_url'] : '';
$dli_heading     = isset( $dli_args['heading'] ) && in_array( $dli_args['heading'], array( 'h2', 'h3', 'h4', 'h5' ), true ) ? $dli_args['heading'] : 'h3';

if ( ! $dli_item_id ) {
	return;
}

$dli_item_link      = dli_get_publication_link( $dli_item_id );
$dli_item_protected = post_password_required( $dli_item_id );
$dli_item_autori    = $dli_item_protected ? array() : wp_list_pluck( dli_get_publication_authors( $dli_item_id ), 'name' );
$dli_item_anno      = $dli_item_protected ? '' : (string) dli_get_field( 'anno', $dli_item_id );
$dli_item_terms     = get_the_terms( $dli_item_id, PUBLICATION_TYPE_TAXONOMY );
$dli_item_terms     = is_array( $dli_item_terms ) ? $dli_item_terms : array();
?>
<<?php echo esc_attr( $dli_heading ); ?> class="it-card-title h5 text-break" style="overflow-wrap: anywhere;">
	<?php
	get_template_part(
		'template-parts/common/linked-title',
		null,
		array(
			'title'    => get_the_title( $dli_item_id ),
			'url'      => $dli_item_link['url'],
			'external' => $dli_item_link['external'],
		)
	);
	?>
</<?php echo esc_attr( $dli_heading ); ?>>
<?php if ( ! empty( $dli_item_autori ) ) : ?>
	<div class="it-card-body">
		<p class="it-card-text text-break" style="overflow-wrap: anywhere;"><?php echo esc_html( implode( ', ', $dli_item_autori ) ); ?></p>
	</div>
<?php endif; ?>
<?php if ( ! empty( $dli_item_terms ) || '' !== $dli_item_anno ) : ?>
	<footer class="it-card-footer">
		<?php if ( ! empty( $dli_item_terms ) ) : ?>
			<div class="it-card-taxonomy">
				<?php foreach ( $dli_item_terms as $dli_item_term ) : ?>
					<?php if ( '' !== $dli_archive_url ) : ?>
						<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'tipologia', array( $dli_item_term->slug ), $dli_archive_url ) ); ?>">
							<span class="visually-hidden"><?php esc_html_e( 'Tipologia:', 'design_laboratori_italia' ); ?></span>
							<?php echo esc_html( $dli_item_term->name ); ?>
						</a>
					<?php else : ?>
						<span class="it-card-category">
							<span class="visually-hidden"><?php esc_html_e( 'Tipologia:', 'design_laboratori_italia' ); ?></span>
							<?php echo esc_html( $dli_item_term->name ); ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( '' !== $dli_item_anno ) : ?>
			<time class="it-card-date" datetime="<?php echo esc_attr( $dli_item_anno ); ?>"><?php echo esc_html( $dli_item_anno ); ?></time>
		<?php endif; ?>
	</footer>
<?php endif; ?>
