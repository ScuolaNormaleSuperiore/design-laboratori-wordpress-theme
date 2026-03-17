<?php
/**
 * Template Name: Ricerca
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

define( 'SITESEARCH_CELLS_PER_PAGE', 10 );

// Begin preparing search params.
$dli_all_content_types = DLI_ContentsManager::get_all_contenttypes_with_results();
$dli_num_results       = 0;
$dli_selected_contents = array();
$dli_search_string     = '';
$dli_query             = null;

if ( isset( $_GET['isreset'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only reset flag for the search form.
	$dli_is_reset = sanitize_text_field( wp_unslash( $_GET['isreset'] ) );
} else {
	$dli_is_reset = '';
}

if ( 'yes' !== $dli_is_reset ) {
	if ( isset( $_GET['selected_contents'] ) && is_array( $_GET['selected_contents'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter parameter, actual search is still nonce-gated below.
		$dli_selected_contents = array_values(
			array_filter(
				array_map(
					'sanitize_text_field',
					wp_unslash( $_GET['selected_contents'] )
				)
			)
		);
	}

	if ( isset( $_GET['searchstring'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only form input, actual query is still nonce-gated below.
		$dli_search_string = sanitize_text_field( wp_unslash( $_GET['searchstring'] ) );
	}
}

if ( '' !== $dli_search_string ) {
	// Verify nonce before performing the search query.
	if (
		isset( $_GET['cercasito_nonce_field'] ) &&
		wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_GET['cercasito_nonce_field'] ) ),
			'sf_cercasito_nonce'
		)
	) {
		$dli_query = DLI_ContentsManager::main_search_query(
			$dli_selected_contents,
			$dli_search_string,
			SITESEARCH_CELLS_PER_PAGE
		);

		if ( $dli_query instanceof WP_Query ) {
			$dli_num_results = $dli_query->found_posts;
		}
	}
}
// End preparing search params.
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<!-- SEZIONE BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<form action="." id="ricercasitoform" method="get">
		<?php wp_nonce_field( 'sf_cercasito_nonce', 'cercasito_nonce_field' ); ?>

		<!-- SEZIONE BANNER -->
		<section id="banner-cerca" class="bg-banner-cerca">
			<div class="section-muted p-3 primary-bg-c1">
				<div class="container">
					<div class="hero-title text-left ms-4 pb-3 pt-3">
						<h2 class="pt-0 pb-0"><?php echo esc_html__( 'Cerca nel sito', 'design_laboratori_italia' ); ?></h2>
						<div class="row m-0">
							<div class="form-group col-md-12 mb-4 text-left">
								<label class="active visually-hidden" for="searchstring">
									<?php echo esc_html__( 'Cerca nel sito', 'design_laboratori_italia' ); ?>
								</label>
								<input
									type="text"
									id="searchstring"
									name="searchstring"
									class="form-control"
									value="<?php echo esc_attr( $dli_search_string ); ?>"
									placeholder="<?php echo esc_attr__( 'Inserisci il testo da cercare', 'design_laboratori_italia' ); ?>"
								>
								<input type="hidden" name="isreset" id="isreset" value="">
							</div>
						</div>
						<div class="row">
							<div class="form-group col text-left ps-4 mb-2">
								<button type="reset" value="reset" onclick="resetForm('ricercasitoform', 'isreset');" class="btn btn-outline-primary">
									<?php echo esc_html__( 'Cancella', 'design_laboratori_italia' ); ?>
								</button>
								<button type="submit" value="submit" class="btn btn-primary">
									<?php echo esc_html__( 'Cerca', 'design_laboratori_italia' ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- SEZIONE RICERCA NEL SITO -->
		<section id="risultati" class="p-4">
			<div class="container my-4">
				<div class="row pt-0">

					<!-- COLONNA FILTRI -->
					<div class="col-12 col-lg-3 border-end">
						<div class="row pt-4">
							<?php if ( count( $dli_all_content_types ) > 0 ) : ?>
								<h3 class="h6 text-uppercase border-bottom">
									<?php echo esc_html__( 'Filtra per tipo di contenuto', 'design_laboratori_italia' ); ?>
								</h3>
								<div>
									<?php foreach ( $dli_all_content_types as $dli_content_type ) : ?>
										<div class="form-check">
											<input
												type="checkbox"
												name="selected_contents[]"
												id="<?php echo esc_attr( $dli_content_type ); ?>"
												value="<?php echo esc_attr( $dli_content_type ); ?>"
												<?php checked( in_array( $dli_content_type, $dli_selected_contents, true ) ); ?>
											>
											<label for="<?php echo esc_attr( $dli_content_type ); ?>">
												<?php echo esc_html( ucfirst( str_replace( '-', ' ', $dli_content_type ) ) ); ?>
											</label>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Inizio ELENCO RISULTATI -->
					<div class="col-12 col-lg-8">
						<div class="row ps-4">
							<p>
								<em>
									<span><?php echo esc_html__( 'Risultati', 'design_laboratori_italia' ); ?>:</span>
									<span><?php echo esc_html( strval( $dli_num_results ) ); ?></span>
								</em>
							</p>
						</div>

						<?php
						// The main loop of the page.
						$dli_result_index = 0;
						?>
						<?php if ( ( $dli_num_results > 0 ) && ( '' !== $dli_search_string ) && ( $dli_query instanceof WP_Query ) ) : ?>
							<?php while ( $dli_query->have_posts() ) : ?>
								<?php
								$dli_query->the_post();
								$dli_result = dli_get_post_wrapper( $post, 'medium' );
								?>
								<!-- begin row -->
								<div class="row">
									<!-- start card -->
									<div class="col-12 col-lg-12">
										<div class="card-wrapper">
											<div class="card">
												<div class="card-body mb-0">
													<?php if ( $dli_result['image_url'] ) : ?>
														<img
															src="<?php echo esc_url( $dli_result['image_url'] ); ?>"
															height="100"
															width="100"
															class="img-thumbnail float-sm-start me-2 text-nowrap"
															title="<?php echo esc_attr( $dli_result['image_title'] ); ?>"
															alt="<?php echo esc_attr( $dli_result['image_alt'] ); ?>"
														>
													<?php endif; ?>

													<span class="text" style="text-transform: uppercase;">
														<a class="text-decoration-none" href="<?php echo esc_url( $dli_result['category_link'] ); ?>">
															<?php echo esc_html( $dli_result['type'] ); ?>
														</a>
													</span>
													<span>&nbsp;-&nbsp;</span>
													<a class="text-decoration-none" href="<?php echo esc_url( $dli_result['link'] ); ?>">
														<h3 class="card-title h5"><?php echo esc_html( $dli_result['title'] ); ?></h3>
													</a>
													<p class="card-text">
														<?php echo esc_html( wp_trim_words( $dli_result['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
													</p>
												</div>
											</div>
										</div>
									</div>
									<!-- end card -->
								</div>
								<!-- end row -->
								<?php ++$dli_result_index; ?>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
					<!-- FINE elenco RISULTATI -->

				</div>
			</div>
		</section>
	</form>

	<!-- RESTORE ORIGINAL POST DATA -->
	<?php wp_reset_postdata(); ?>

	<!-- PAGINAZIONE -->
	<?php
	if ( $dli_query instanceof WP_Query ) {
		get_template_part(
			'template-parts/common/paginazione',
			null,
			array(
				'query' => $dli_query,
			)
		);
	}
	?>
</main>

<?php get_footer(); ?>
