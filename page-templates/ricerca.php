<?php
/**
 * Template Name: Attività di ricerca
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

define( 'RIC_CELLS_PER_ROW', 3 );

$dli_per_page        = DLI_POSTS_PER_PAGE;
$dli_per_page_values = DLI_POST_PER_PAGE_VALUES;
$dli_allowed_pages   = array_map( 'strval', (array) $dli_per_page_values );

$dli_raw_per_page = filter_input( INPUT_GET, 'per_page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( is_string( $dli_raw_per_page ) ) {
	$dli_raw_per_page = sanitize_text_field( wp_unslash( $dli_raw_per_page ) );
	if ( in_array( $dli_raw_per_page, $dli_allowed_pages, true ) ) {
		$dli_per_page = $dli_raw_per_page;
	}
}

$dli_paged = absint( get_query_var( 'paged' ) );

if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}

if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_params = array(
	'per_page' => $dli_per_page,
	'paged'    => $dli_paged,
);

$dli_query       = DLI_ContentsManager::get_research_area_data_query( $dli_params );
$dli_num_results = $dli_query->found_posts;
?>

<main id="main-container" role="main">

	<!-- BANNER INDIRIZZI DI RICERCA -->
	<?php get_template_part( 'template-parts/hero/ricerca' ); ?>

	<?php
	// The main loop of the page.
	$dli_research_index = 0;
	?>

	<!-- ELENCO INDIRIZZI DI RICERCA -->
	<section id="indirizziricerca">
		<div class="container p-5">
			<?php if ( $dli_num_results ) : ?>
				<?php while ( $dli_query->have_posts() ) : ?>
					<?php
					$dli_query->the_post();

					if ( 0 === ( $dli_research_index % RIC_CELLS_PER_ROW ) ) :
						?>
						<div class="row">
					<?php endif; ?>

					<?php
					$dli_research_id    = get_the_ID();
					$dli_research_post  = get_post( $dli_research_id );
					$dli_image_metadata = dli_get_image_metadata( $dli_research_post, 'item-card-list' );
					$dli_responsabili   = dli_get_field( 'responsabile_attivita_di_ricerca', $dli_research_id );
					$dli_levels         = wp_get_post_terms( $dli_research_id, 'post_tag' );
					$dli_levels         = ( is_wp_error( $dli_levels ) || ! is_array( $dli_levels ) ) ? array() : $dli_levels;

					$dli_responsabili_names = array();
					if ( is_array( $dli_responsabili ) ) {
						foreach ( $dli_responsabili as $dli_persona ) {
							if ( isset( $dli_persona->post_title ) ) {
								$dli_responsabili_names[] = $dli_persona->post_title;
							}
						}
					}
					$dli_nomi_resp = implode( ', ', $dli_responsabili_names );
					?>

					<div class="col-12 col-lg-4 mb-3 mb-md-4">
						<article class="it-card<?php echo ! empty( $dli_image_metadata['image_url'] ) ? ' it-card-image' : ''; ?> it-card-height-full rounded shadow-sm border">
							<h3 class="it-card-title h5">
								<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
							</h3>
							<?php if ( ! empty( $dli_image_metadata['image_url'] ) ) : ?>
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
							<footer class="it-card-footer flex-column align-items-start">
								<?php if ( $dli_nomi_resp ) : ?>
									<span class="it-card-signature"><?php echo esc_html( $dli_nomi_resp ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $dli_levels ) ) : ?>
									<div class="it-card-taxonomy">
										<ul class="it-card-chips" aria-label="<?php echo esc_attr__( 'Argomenti correlati:', 'design_laboratori_italia' ); ?>">
											<?php foreach ( $dli_levels as $dli_level ) : ?>
												<li class="list-item">
													<span class="chip chip-secondary">
														<span class="chip-label">
															<span class="visually-hidden"><?php echo esc_html__( 'Argomento:', 'design_laboratori_italia' ); ?></span>
															<?php echo esc_html( $dli_level->name ); ?>
														</span>
													</span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</footer>
						</article>
					</div>

					<?php
					if (
						( RIC_CELLS_PER_ROW - 1 ) === ( $dli_research_index % RIC_CELLS_PER_ROW ) ||
						( $dli_query->current_post + 1 ) === $dli_query->post_count
					) :
						?>
						</div>
					<?php endif; ?>

					<?php ++$dli_research_index; ?>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="row pt-2">
					<?php echo esc_html__( 'Non è stata trovata nessuna attività di ricerca', 'design_laboratori_italia' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php wp_reset_postdata(); ?>

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
