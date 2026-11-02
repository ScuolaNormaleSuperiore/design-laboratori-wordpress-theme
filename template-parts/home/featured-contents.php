<?php
/**
 * Homepage section "Contenuti in evidenza": up to three boxes, each with the promoted contents of a type.
 *
 * The boxes are configured in Configurazione → Layout HP (a content type and a title in Italian and English) and
 * read by `dli_get_featured_boxes()`: a box with nothing to show is left out and the others share the row
 * (`dli_get_featured_column_class()`). The layout of the items is decided by the type: each item is printed by
 * the partial of `template-parts/home/featured-contents/` that `dli_get_featured_renderer()` gives, which receives
 * the content already read and prints only its markup. The section has a visually hidden `h2`, each box an `h3`
 * and each item an `h4`. A section that is off prints nothing and reads nothing.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args     = isset( $args ) && is_array( $args ) ? $args : array();
$dli_instance = max( 1, (int) ( $dli_args['instance'] ?? 1 ) );

if ( 'true' !== ( $dli_args['enabled'] ?? false ) ) {
	return;
}

$dli_boxes = dli_get_featured_boxes();
if ( empty( $dli_boxes ) ) {
	return;
}

$dli_column_class = dli_get_featured_column_class( count( $dli_boxes ) );
?>
<!-- BLOCCO CARD - CONTENUTI IN EVIDENZA (Featured contents) -->
<section id="blocco-card" class="section pt-5" aria-labelledby="blocco-card-title">
	<div class="section-content">
		<div class="container">
			<h2 id="blocco-card-title" class="visually-hidden"><?php esc_html_e( 'Contenuti in evidenza', 'design_laboratori_italia' ); ?></h2>
			<div class="row">
				<?php foreach ( $dli_boxes as $dli_box ) : ?>
					<?php $dli_heading_id = 'evidenza-' . $dli_instance . '-box-' . $dli_box['number'] . '-title'; ?>
					<div class="<?php echo esc_attr( $dli_column_class ); ?> pb-5">
						<h3 id="<?php echo esc_attr( $dli_heading_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_box['title'] ); ?></h3>
						<ul class="it-card-list row" aria-labelledby="<?php echo esc_attr( $dli_heading_id ); ?>">
							<?php foreach ( $dli_box['posts'] as $dli_post ) : ?>
								<li class="col-12 mb-3">
									<?php
									get_template_part(
										'template-parts/home/featured-contents/' . $dli_box['renderer'],
										null,
										array(
											'post'        => $dli_post,
											'archive_url' => $dli_box['archive_url'],
											'heading'     => 'h4',
										)
									);
									?>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php if ( '' !== $dli_box['archive_url'] ) : ?>
							<div class="pt-2 text-center">
								<a href="<?php echo esc_url( $dli_box['archive_url'] ); ?>" class="btn btn-secondary">
									<?php echo esc_html( $dli_box['button_label'] ); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
<!-- FINE BLOCCO CARD -->
