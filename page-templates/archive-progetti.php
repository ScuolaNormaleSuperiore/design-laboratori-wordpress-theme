<?php
/**
 * Template Name: Archivio progetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

define( 'PROG_CELLS_PER_ROW', 3 );

$dli_per_page        = DLI_POSTS_PER_PAGE;
$dli_per_page_values = DLI_POST_PER_PAGE_VALUES;
$dli_today           = gmdate( 'Ymd' );

if ( isset( $_GET['per_page'] ) && is_numeric( $_GET['per_page'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only pagination/filter parameter.
	$dli_per_page = sanitize_text_field( wp_unslash( $_GET['per_page'] ) );
}

$dli_paged = absint( get_query_var( 'paged' ) );

if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}

if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'today'    => $dli_today,
	'per_page' => $dli_per_page,
	'paged'    => $dli_paged,
);

$dli_query       = DLI_ContentsManager::get_archived_projects_data_query( $dli_params );
$dli_num_results = $dli_query->found_posts;
?>

<main id="main-container" role="main">

	<!-- BANNER PROGETTI -->
	<?php get_template_part( 'template-parts/hero/progetti-archive' ); ?>

	<?php
	// The main loop of the page.
	$dli_project_index = 0;
	?>

	<!-- ELENCO PROGETTI -->
	<section id="progetti">
		<div class="container p-5">
			<?php if ( $dli_num_results ) : ?>
				<?php while ( $dli_query->have_posts() ) : ?>
					<?php
					$dli_query->the_post();

					if ( 0 === ( $dli_project_index % PROG_CELLS_PER_ROW ) ) :
						?>
						<!-- begin row -->
						<div class="row">
					<?php endif; ?>

					<?php $dli_image_metadata = dli_get_image_metadata( $post, 'item-card-list' ); ?>

					<!-- start card -->
					<div class="col-12 col-lg-4 mb-3 mb-md-4">
						<article class="it-card<?php echo ! empty( $dli_image_metadata['image_url'] ) ? ' it-card-image' : ''; ?> it-card-height-full rounded shadow-sm border">
							<h3 class="it-card-title h5">
								<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
							</h3>
							<?php if ( $dli_image_metadata['image_url'] ) : ?>
								<div class="it-card-image-wrapper">
									<div class="ratio ratio-16x9">
										<figure class="figure img-full">
											<img
												src="<?php echo esc_url( $dli_image_metadata['image_url'] ); ?>"
												title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
												alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>"
											>
										</figure>
									</div>
								</div>
							<?php endif; ?>
							<div class="it-card-body">
								<p class="it-card-text">
									<?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
								</p>
							</div>
						</article>
					</div>
					<!-- end card -->

					<?php
					if (
						( PROG_CELLS_PER_ROW - 1 ) === ( $dli_project_index % PROG_CELLS_PER_ROW ) ||
						( $dli_query->current_post + 1 ) === $dli_query->post_count
					) :
						?>
						</div>
						<!-- end row -->
					<?php endif; ?>

					<?php ++$dli_project_index; ?>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="row pt-2">
					<?php echo esc_html__( 'Non è stato trovato nessun progetto', 'design_laboratori_italia' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- RESTORE ORIGINAL POST DATA -->
	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'query'           => $dli_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_values,
		)
	);
	?>
</main>

<?php get_footer(); ?>
