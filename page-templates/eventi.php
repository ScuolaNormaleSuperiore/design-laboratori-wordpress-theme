<?php
/**
 * Template Name: Eventi
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_events_cells_per_row = 3;
$dli_per_page             = DLI_POSTS_PER_PAGE;
$dli_per_page_values      = DLI_POST_PER_PAGE_VALUES;

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

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER EVENTI -->
	<?php get_template_part( 'template-parts/hero/eventi' ); ?>

	<!-- SEZIONE EVENTI -->
	<section id="events" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
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

				<?php
				// The main loop of the page.
				$dli_pindex = 0;
				if ( $dli_num_results ) :
					?>
					<!-- Inizio ELENCO EVENTI -->
					<div class="col-12 col-lg-8">
						<?php
						while ( $dli_the_query->have_posts() ) :
							$dli_the_query->the_post();
							if ( 0 === ( $dli_pindex % $dli_events_cells_per_row ) ) :
								?>
								<!-- begin row -->
								<div class="row pt-5">
							<?php endif; ?>

							<?php
							$dli_post_id        = get_the_ID();
							$dli_date           = dli_get_field( 'data_inizio', $dli_post_id );
							$dli_event_date     = dli_get_datetime_from_format( DLI_ACF_DATE_FORMAT, $dli_date );
							$dli_event_day      = $dli_event_date ? intval( $dli_event_date->format( 'd' ) ) : '';
							$dli_event_month    = $dli_event_date ? dli_get_monthname( $dli_event_date->format( 'm' ) ) : '';
							$dli_event_year     = $dli_event_date ? intval( $dli_event_date->format( 'Y' ) ) : '';
							$dli_orario_inizio  = dli_get_field( 'orario_inizio', $dli_post_id );
							$dli_evento         = get_post( $dli_post_id );
							$dli_image_metadata = dli_get_image_metadata( $dli_evento, 'item-card-list' );
							$dli_item_link      = dli_manage_item_link( $post );
							?>

							<!-- start card-->
							<div class="col-12 col-lg-4">
								<div class="card-wrapper">
									<div class="card card-img no-after card-bg">
										<div class="img-responsive-wrapper">
											<div class="img-responsive img-responsive-panoramic">
												<figure class="img-wrapper">
													<img src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
														alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ?? '' ); ?>"
														title="<?php echo esc_attr( $dli_image_metadata['image_title'] ?? '' ); ?>">
												</figure>
												<?php if ( $dli_event_date ) : ?>
													<div class="card-calendar d-flex flex-column justify-content-center">
														<span class="card-date"><?php echo esc_html( $dli_event_day ); ?></span>
														<span class="card-day">
															<?php echo esc_html( $dli_event_month ); ?> <?php echo esc_html( $dli_event_year ); ?>
														</span>
													</div>
												<?php endif; ?>
											</div>
										</div>
										<div class="card-body p-4">
											<h3 class="card-title h4"><?php echo esc_html( get_the_title() ); ?></h3>
											<p class="card-text">
												<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
											</p>
											<?php if ( $dli_orario_inizio ) : ?>
												<p class="card-text">
													<?php echo esc_html( $dli_orario_inizio ); ?>
												</p>
											<?php endif; ?>
											<a class="read-more" href="<?php echo esc_url( $dli_item_link ); ?>">
												<span class="text"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
												<svg class="icon" role="img" aria-label="<?php echo esc_attr__( 'Leggi di più', 'design_laboratori_italia' ); ?>">
													<title><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></title>
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
												</svg>
											</a>
										</div>
									</div>
								</div>
							</div>
							<!--end card-->

							<?php
							if ( ( ( $dli_pindex % $dli_events_cells_per_row ) === $dli_events_cells_per_row - 1 ) || ( $dli_the_query->current_post + 1 === $dli_the_query->post_count ) ) :
								?>
								</div>
								<!-- end row -->
							<?php endif; ?>
							<?php ++$dli_pindex; ?>
						<?php endwhile; ?>
					</div>
					<!-- Fine elenco eventi-->
				<?php else : ?>
					<div class="col-12 col-lg-8">
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
