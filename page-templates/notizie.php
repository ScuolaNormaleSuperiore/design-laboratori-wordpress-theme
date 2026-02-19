<?php
/**
 * Template Name: Notizie
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_news_cells_per_row = 3;
$dli_per_page           = DLI_POSTS_PER_PAGE;
$dli_per_page_values    = DLI_POST_PER_PAGE_VALUES;

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
	'per_page'            => $dli_per_page,
	'selected_categories' => $dli_selected_categories,
	'paged'               => $dli_paged,
);

$dli_the_query      = DLI_ContentsManager::get_news_data_query( $dli_params );
$dli_num_results    = $dli_the_query->found_posts;
$dli_all_categories = dli_get_all_categories_by_ct( 'category', NEWS_POST_TYPE );
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER NOTIZIE -->
	<?php get_template_part( 'template-parts/hero/notizie' ); ?>

	<!-- SEZIONE NOTIZIE -->
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<!--COLONNA FILTRI -->
				<div class="col-12 col-lg-3 border-end">
					<div class="row pt-4">
						<?php if ( ! empty( $dli_all_categories ) && is_array( $dli_all_categories ) ) : ?>
							<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Categoria', 'design_laboratori_italia' ); ?></h3>
							<div>
								<form action="<?php echo esc_url( get_permalink() ); ?>" id="notizieform" method="GET">
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
					<!-- Inizio ELENCO NEWS -->
					<div class="col-12 col-lg-8">
						<?php
						while ( $dli_the_query->have_posts() ) :
							$dli_the_query->the_post();
							if ( 0 === ( $dli_pindex % $dli_news_cells_per_row ) ) :
								?>
								<!-- begin row -->
								<div class="row pt-5">
							<?php endif; ?>

							<?php
							$dli_termitem  = dli_get_post_main_category( $post, 'category' );
							$dli_item_link = dli_manage_item_link( $post );
							?>

							<!-- start card-->
							<div class="col-12 col-lg-4">
								<div class="card-wrapper">
									<div class="card card-bg">
										<div class="card-body">
											<div class="category-top">
												<?php if ( ! empty( $dli_termitem['title'] ) ) : ?>
													<span class="category"><?php echo esc_html( $dli_termitem['title'] ); ?></span>
												<?php endif; ?>
												<span class="data"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
											</div>
											<h3 class="card-title cardTitlecustomSpacing h4"><?php echo esc_html( get_the_title() ); ?></h3>
											<p class="card-text">
												<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
											</p>
											<a class="read-more" href="<?php echo esc_url( $dli_item_link ); ?>">
												<span class="text customSpacing"><?php echo esc_html__( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
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
							if ( ( ( $dli_pindex % $dli_news_cells_per_row ) === $dli_news_cells_per_row - 1 ) || ( $dli_the_query->current_post + 1 === $dli_the_query->post_count ) ) :
								?>
								</div>
								<!-- end row -->
							<?php endif; ?>
							<?php ++$dli_pindex; ?>
						<?php endwhile; ?>
					</div>
					<!-- Fine elenco news-->
				<?php else : ?>
					<div class="col-12 col-lg-8">
						<div class="row pt-2">
							<?php echo esc_html__( 'Non è stata trovata nessuna notizia', 'design_laboratori_italia' ); ?>
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
