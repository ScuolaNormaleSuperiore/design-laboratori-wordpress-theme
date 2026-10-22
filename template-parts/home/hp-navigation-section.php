<?php
/**
 * Homepage navigation cards section ("Pagine in evidenza").
 *
 * Mostra fino a 6 pagine per cui è stato spuntato il flag "Promuovi in home"
 * (campo ACF della scheda Pagina), ordinate come nel menu. Pattern "Card
 * servizi" di Bootstrap Italia 3 (it-card, bg-white, link "Vai al servizio"
 * in fondo, non l'intera card), validato nel prototipo statico bs-playground
 * (sf-index.html, #blocco-navigazione).
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title       = $args['show_title'] ?? false;
$dli_title            = dli_get_configuration_field_by_lang( 'home_navigazione_title', 'homepage' );
$dli_text             = dli_get_configuration_field_by_lang( 'home_navigazione_text', 'homepage' );
$dli_bg_class         = dli_get_hp_section_background_class( $args, 'muted' );

if ( 'true' === $dli_section_enabled ) {
	$dli_query     = new WP_Query(
		array(
			'post_type'      => WP_DEFAULT_PAGE,
			'post_status'    => 'publish',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'posts_per_page' => 6,
			'meta_query'     => array(
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		)
	);
	$dli_num_items = $dli_query->post_count;
	if ( $dli_num_items > 0 ) {
		?>
	<!-- INIZIO SEZIONE "COSA PUOI FARE" -->
	<section id="blocco-navigazione" class="section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-4" <?php echo ( 'true' === $dli_show_title && $dli_title ) ? 'aria-labelledby="blocco-navigazione-title"' : 'aria-label="' . esc_attr__( 'Pagine in evidenza', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title && $dli_title ) : ?>
					<h2 id="blocco-navigazione-title" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $dli_text ) : ?>
					<p class="pb-3"><?php echo esc_html( $dli_text ); ?></p>
				<?php endif; ?>
				<ul class="row list-unstyled mb-0">
					<?php
					foreach ( $dli_query->posts as $dli_page ) {
						$dli_page_id    = $dli_page->ID;
						$dli_page_title = get_the_title( $dli_page_id );
						$dli_page_link  = get_permalink( $dli_page_id );
						$dli_abstract   = get_the_excerpt( $dli_page_id );
						?>
						<li class="col-12 col-md-6 col-lg-3 mb-4">
							<article class="it-card it-card-height-full bg-white rounded shadow-sm border card-nav">
								<h3 class="it-card-title h4 fw-bold"><?php echo esc_html( $dli_page_title ); ?></h3>
								<?php if ( $dli_abstract ) : ?>
									<div class="it-card-body">
										<p class="it-card-text text-muted"><?php echo esc_html( $dli_abstract ); ?></p>
									</div>
								<?php endif; ?>
								<footer class="it-card-footer justify-content-start">
									<a class="fw-semibold" href="<?php echo esc_url( $dli_page_link ); ?>">
										<?php echo esc_html__( 'Vai al servizio', 'design_laboratori_italia' ); ?>
										<span class="visually-hidden">: <?php echo esc_html( $dli_page_title ); ?></span>
										<svg class="icon icon-sm icon-primary ms-2" aria-hidden="true" focusable="false">
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
										</svg>
									</a>
								</footer>
							</article>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</section>
	<!-- FINE SEZIONE "COSA PUOI FARE" -->
		<?php
	}
}
?>
