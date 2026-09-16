<?php
/**
 * Template Name: Eventi
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

// Permalink della pagina stessa, catturato prima che il loop sui risultati sotto sposti $post: usato per il link di filtro sulla categoria della card.
$dli_page_permalink = get_permalink();

$dli_per_page        = DLI_POSTS_PER_PAGE;
$dli_per_page_values = DLI_POST_PER_PAGE_VALUES;

$dli_per_page_input = filter_input( INPUT_GET, 'per_page', FILTER_VALIDATE_INT );
if ( false !== $dli_per_page_input && null !== $dli_per_page_input && $dli_per_page_input > 0 ) {
	$dli_per_page = absint( $dli_per_page_input );
}

$dli_selected_categories     = array();
$dli_raw_selected_categories = filter_input( INPUT_GET, 'cat', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

if ( ! is_array( $dli_raw_selected_categories ) ) {
	$dli_single_selected_category = filter_input( INPUT_GET, 'cat', FILTER_DEFAULT );
	if ( null !== $dli_single_selected_category && false !== $dli_single_selected_category ) {
		$dli_raw_selected_categories = array( $dli_single_selected_category );
	} else {
		$dli_raw_selected_categories = array();
	}
}

if ( ! empty( $dli_raw_selected_categories ) ) {
	$dli_selected_categories = array_values(
		array_unique(
			array_filter(
				array_map( 'absint', array_map( 'wp_unslash', $dli_raw_selected_categories ) )
			)
		)
	);
}

$dli_paged = absint( get_query_var( 'paged' ) );
if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'selected_categories' => $dli_selected_categories,
	'per_page'            => $dli_per_page,
	'paged'               => $dli_paged,
);

$dli_the_query      = DLI_ContentsManager::get_event_data_query( $dli_params );
$dli_num_results    = $dli_the_query->found_posts;
$dli_all_categories = dli_get_all_categories_by_ct( 'category', EVENT_POST_TYPE );
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BANNER EVENTI -->
	<?php get_template_part( 'template-parts/hero/eventi' ); ?>

	<!-- SEZIONE EVENTI -->
	<section id="events" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-bottom pb-3 mb-4 mb-lg-0">
					<div class="sticky-top pt-4" style="top: 1rem;">
						<?php if ( ! empty( $dli_all_categories ) && is_array( $dli_all_categories ) ) : ?>
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Categoria', 'design_laboratori_italia' ); ?></h3>
							<div>
								<form action="<?php echo esc_url( get_permalink() ); ?>" id="eventiform" method="GET">
									<?php foreach ( $dli_all_categories as $dli_category ) : ?>
										<div class="form-check">
											<input type="checkbox" name="cat[]" id="<?php echo esc_attr( $dli_category['slug'] ); ?>"
												value="<?php echo esc_attr( $dli_category['id'] ); ?>" onChange="this.form.submit()"
												<?php checked( in_array( absint( $dli_category['id'] ), $dli_selected_categories, true ) ); ?>>
											<label for="<?php echo esc_attr( $dli_category['slug'] ); ?>"><?php echo esc_html( $dli_category['name'] ); ?></label>
										</div>
									<?php endforeach; ?>
								</form>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<!--COLONNA FILTRI -->

				<?php if ( $dli_num_results ) : ?>
					<!-- Inizio ELENCO EVENTI -->
					<div class="col-12 col-lg-8 offset-lg-1 pt-3">
						<div class="row">
							<?php
							while ( $dli_the_query->have_posts() ) :
								$dli_the_query->the_post();
								$dli_post_id          = get_the_ID();
								$dli_datetime_display = dli_get_event_datetime_display( $dli_post_id );
								$dli_evento           = get_post( $dli_post_id );
								$dli_image_metadata   = dli_get_image_metadata( $dli_evento, 'item-card-list' );
								$dli_has_image        = ! empty( $dli_image_metadata['image_url'] );
								$dli_item_link        = dli_manage_item_link( $post );
								$dli_termitem         = dli_get_post_main_category( $post, 'category' );
								?>
								<div class="col-12 col-lg-6 mb-4">
									<div class="card-wrapper h-100 pb-0">
										<article class="it-card<?php echo $dli_has_image ? ' it-card-image' : ''; ?> it-card-height-full rounded shadow-sm border">
											<h3 class="it-card-title h5">
												<a href="<?php echo esc_url( $dli_item_link ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
											</h3>
											<?php if ( $dli_has_image ) : ?>
												<div class="it-card-image-wrapper">
													<div class="ratio ratio-16x9">
														<figure class="figure img-full">
															<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
																alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
																title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>">
														</figure>
													</div>
												</div>
											<?php endif; ?>
											<div class="it-card-body">
												<?php if ( $dli_datetime_display ) : ?>
													<p class="it-card-subtitle"><?php echo esc_html( $dli_datetime_display ); ?></p>
												<?php endif; ?>
												<p class="it-card-text"><?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve', $dli_post_id ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
											</div>
											<?php if ( $dli_termitem && ! empty( $dli_termitem['title'] ) ) : ?>
												<footer class="it-card-footer">
													<div class="it-card-taxonomy">
														<?php if ( $dli_termitem['id'] ) : ?>
															<a class="it-card-category it-card-link" href="<?php echo esc_url( add_query_arg( 'cat', array( $dli_termitem['id'] ), $dli_page_permalink ) ); ?>">
																<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
																<?php echo esc_html( $dli_termitem['title'] ); ?>
															</a>
														<?php else : ?>
															<span class="it-card-category">
																<span class="visually-hidden"><?php esc_html_e( 'Categoria correlata:', 'design_laboratori_italia' ); ?></span>
																<?php echo esc_html( $dli_termitem['title'] ); ?>
															</span>
														<?php endif; ?>
													</div>
												</footer>
											<?php endif; ?>
										</article>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
					<!-- Fine elenco eventi-->
				<?php else : ?>
					<div class="col-12 col-lg-8 offset-lg-1">
						<div class="row pt-2">
							<?php echo esc_html__( 'Non è stato trovato alcun evento', 'design_laboratori_italia' ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- RESTORE ORIGINAL Post Data -->
	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'query'           => $dli_the_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_values,
		)
	);
	?>
</main>

<?php
get_footer();
