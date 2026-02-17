<?php
/**
 * Template Name: Archive.
 *
 * @package Design_Laboratori_Italia
 */

get_header();
define( 'DLI_ARCHIVE_CELLS_PER_ROW', 3 );

$dli_selected_categories = array();
// Filter by category IDs from query string (?cat[]=1&cat[]=2).
$dli_raw_categories = filter_input( INPUT_GET, 'cat', FILTER_DEFAULT, FILTER_FORCE_ARRAY );
if ( is_array( $dli_raw_categories ) ) {
	$dli_selected_categories = array_map( 'absint', wp_unslash( $dli_raw_categories ) );
}

$dli_the_query = new WP_Query(
	array(
		'paged'          => get_query_var( 'paged', 1 ),
		'post_type'      => WP_DEFAULT_POST,
		'posts_per_page' => DLI_POSTS_PER_PAGE,
		'category__in'   => $dli_selected_categories,
	)
);

$dli_num_results = $dli_the_query->found_posts;
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER ARTICOLI -->
	<?php get_template_part( 'template-parts/hero/archive' ); ?>

	<!-- SEZIONE ARTICOLI -->
	<section id="news" class="p-4">
		<div class="container my-4">
			<div class="row pt-0">

				<?php
				// Main loop of the page.
				$dli_pindex = 0;
				if ( $dli_num_results ) {
					?>
				<!-- Inizio ELENCO NEWS -->
				<div class="col-12 col-lg-12">
					<?php
					while ( $dli_the_query->have_posts() ) {
						$dli_the_query->the_post();
						if ( 0 === ( $dli_pindex % DLI_ARCHIVE_CELLS_PER_ROW ) ) {
							?>
					<!-- begin row -->
					<div class="row pt-5">
							<?php
						}

						$dli_termitem = dli_get_post_main_category( get_post(), 'category' );
						?>
						<!-- start card -->
						<div class="col-12 col-lg-4">
							<div class="card-wrapper">
								<div class="card card-bg">
									<div class="card-body">
										<div class="category-top">
											<?php
											if ( ! empty( $dli_termitem['title'] ) ) {
												?>
											<a class="category" href="#"><?php echo esc_html( $dli_termitem['title'] ); ?></a>
												<?php
											}
											?>
											<span class="data"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
										</div>
										<h3 class="card-title h4"><?php echo esc_html( get_the_title() ); ?></h3>
										<p class="card-text">
											<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
										</p>
										<a class="read-more" href="<?php echo esc_url( get_permalink() ); ?>">
											<span class="text"><?php echo esc_html__( 'Leggi di piu', 'design_laboratori_italia' ); ?></span>
											<svg class="icon" role="img" aria-labelledby="Arrow right">
												<title><?php echo esc_html__( 'Leggi di piu', 'design_laboratori_italia' ); ?></title>
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ); ?>"></use>
											</svg>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- end card -->
						<?php
						if ( ( ( $dli_pindex % DLI_ARCHIVE_CELLS_PER_ROW ) === ( DLI_ARCHIVE_CELLS_PER_ROW - 1 ) ) || ( $dli_the_query->current_post + 1 === $dli_the_query->post_count ) ) {
							?>
					</div>
					<!-- end row -->
							<?php
						}

						++$dli_pindex;
					}
					?>
				</div>
				<!-- Fine elenco news -->
					<?php
				} else {
					?>
				<div class="row pt-2">
					<?php echo esc_html__( 'Non è stato trovato nessun articolo', 'design_laboratori_italia' ); ?>
				</div>
					<?php
				}
				?>

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
			'query' => $dli_the_query,
		)
	);
	?>

</main>

<?php get_footer(); ?>
