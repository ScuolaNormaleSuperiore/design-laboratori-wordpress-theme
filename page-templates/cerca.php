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
$dli_all_content_types = array();
$dli_num_results       = 0;
$dli_selected_contents = array();
$dli_search_string     = '';
$dli_query             = null;
$dli_per_page          = SITESEARCH_CELLS_PER_PAGE;
$dli_per_page_values   = DLI_PER_PAGE_VALUES;

$dli_per_page_input = filter_input( INPUT_GET, 'per_page', FILTER_VALIDATE_INT );
if ( false !== $dli_per_page_input && null !== $dli_per_page_input && $dli_per_page_input > 0 ) {
	$dli_per_page = absint( $dli_per_page_input );
}

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
		$dli_per_page
	);

	if ( $dli_query instanceof WP_Query ) {
		$dli_num_results       = $dli_query->found_posts;
		$dli_all_content_types = DLI_ContentsManager::get_contenttypes_with_search_results( $dli_search_string );
	}
}
$dli_has_searched = ( $dli_query instanceof WP_Query );
// End preparing search params.
?>

<main id="main-container" class="main-container bluelectric" role="main">

	<form id="ricercasitoform" action="<?php echo esc_url( get_permalink() ); ?>" method="get">
		<?php wp_nonce_field( 'sf_cercasito_nonce', 'cercasito_nonce_field' ); ?>

		<!-- BANNER RICERCA: hero con breadcrumb integrato e campo di ricerca, come nel
		     prototipo (sf-site-search.html). -->
		<section id="banner-cerca" class="it-hero-wrapper it-hero-small-size" aria-labelledby="dli-hero-cerca-title">
			<div class="container">
				<div class="row align-items-stretch">
					<div class="col-12 col-lg-7">
						<section class="pt-2">
							<?php get_template_part( 'template-parts/common/breadcrumb-hero' ); ?>
						</section>
						<div class="it-hero-text-wrapper px-lg-2">
							<h2 id="dli-hero-cerca-title"><?php esc_html_e( 'Cerca', 'design_laboratori_italia' ); ?></h2>
							<p class="fs-5"><?php esc_html_e( 'Trova persone, progetti, notizie e altri contenuti del sito.', 'design_laboratori_italia' ); ?></p>
							<div class="row m-0">
								<div class="form-group col-md-8 col-lg-9 mb-2 text-start">
									<label for="searchstring" class="text-white"><?php esc_html_e( 'Scrivi almeno 3 caratteri per cercare', 'design_laboratori_italia' ); ?></label>
									<div class="input-group">
										<span class="input-group-text">
											<svg class="icon icon-sm" aria-hidden="true" focusable="false">
												<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search' ); ?>"></use>
											</svg>
										</span>
										<input
											type="search"
											class="form-control"
											id="searchstring"
											name="searchstring"
											minlength="3"
											placeholder="<?php echo esc_attr__( 'Cosa stai cercando?', 'design_laboratori_italia' ); ?>"
											value="<?php echo esc_attr( $dli_search_string ); ?>"
										>
									</div>
								</div>
							</div>
							<div class="row m-0">
								<div class="form-group col text-start d-flex flex-wrap gap-2">
									<button type="submit" class="btn btn-secondary"><?php esc_html_e( 'Cerca', 'design_laboratori_italia' ); ?></button>
									<?php if ( $dli_has_searched ) : ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-secondary">
											<span><?php esc_html_e( 'Annulla ricerca', 'design_laboratori_italia' ); ?></span>
										</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-12 col-lg-5 d-none d-lg-block">
						<?php if ( dli_show_hero_decorative_image() ) : ?>
						<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-sns.png' ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover" />
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>

		<!-- RISULTATI DI RICERCA: sezione mostrata solo dopo una ricerca effettiva
		     (mai al primo accesso alla pagina), come nel prototipo — che invece la
		     mostra sempre con un esempio statico, essendo una pagina statica senza
		     un vero motore di ricerca dietro. I filtri "Filtra per" mostrano solo le
		     tipologie di contenuto che hanno davvero almeno un risultato per la
		     ricerca corrente (non l'elenco fisso di tutte le tipologie), come nel
		     prototipo. -->
		<?php if ( $dli_has_searched ) : ?>
			<section id="risultati" class="p-4">
				<div class="container my-4">
					<div class="row">
						<div class="col-12">

							<?php if ( count( $dli_all_content_types ) > 0 ) : ?>
								<fieldset class="mb-4">
									<legend class="h6 text-uppercase mb-2"><?php esc_html_e( 'Filtra per', 'design_laboratori_italia' ); ?></legend>
									<?php foreach ( $dli_all_content_types as $dli_content_type ) : ?>
										<div class="form-check form-check-inline">
											<input
												type="checkbox"
												class="form-check-input"
												name="selected_contents[]"
												id="<?php echo esc_attr( $dli_content_type ); ?>"
												value="<?php echo esc_attr( $dli_content_type ); ?>"
												onchange="this.form.submit()"
												<?php checked( in_array( $dli_content_type, $dli_selected_contents, true ) ); ?>
											>
											<label class="form-check-label" for="<?php echo esc_attr( $dli_content_type ); ?>">
												<?php echo esc_html( ucfirst( str_replace( '-', ' ', $dli_content_type ) ) ); ?>
											</label>
										</div>
									<?php endforeach; ?>
								</fieldset>
							<?php endif; ?>

							<p class="fw-bold mb-3" role="status" aria-live="polite">
								<?php
								echo esc_html(
									sprintf(
										/* translators: 1: number of results, 2: search query */
										_n( '%1$d risultato per «%2$s»', '%1$d risultati per «%2$s»', $dli_num_results, 'design_laboratori_italia' ),
										$dli_num_results,
										$dli_search_string
									)
								);
								?>
							</p>

							<?php if ( $dli_num_results > 0 ) : ?>
								<div role="list" aria-label="<?php echo esc_attr__( 'Risultati di ricerca', 'design_laboratori_italia' ); ?>">
									<?php
									while ( $dli_query->have_posts() ) :
										$dli_query->the_post();
										$dli_result     = dli_get_post_wrapper( $post, 'medium' );
										$dli_type_label = ucfirst( str_replace( '-', ' ', $dli_result['type'] ) );
										?>
										<div role="listitem" class="border-bottom py-3">
											<a class="d-flex justify-content-between align-items-center text-decoration-none" href="<?php echo esc_url( $dli_result['link'] ); ?>">
												<span>
													<span class="d-block h6 mb-1"><?php echo esc_html( $dli_result['title'] ); ?></span>
													<span class="d-block text-secondary mb-1"><?php echo esc_html( wp_trim_words( $dli_result['description'], DLI_ACF_SHORT_DESC_LENGTH ) ); ?></span>
													<span class="d-block text-uppercase fw-semibold small">
														<span class="visually-hidden"><?php esc_html_e( 'Tipo di contenuto:', 'design_laboratori_italia' ); ?></span>
														<?php echo esc_html( $dli_type_label ); ?>
													</span>
												</span>
												<svg class="icon icon-primary flex-shrink-0 ms-3" aria-hidden="true" focusable="false">
													<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
												</svg>
											</a>
										</div>
									<?php endwhile; ?>
								</div>
							<?php else : ?>
								<p class="text-center text-secondary py-4" aria-live="polite">
									<?php esc_html_e( 'Nessun risultato per i filtri selezionati.', 'design_laboratori_italia' ); ?>
								</p>
							<?php endif; ?>

						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>
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
				'query'           => $dli_query,
				'per_page'        => $dli_per_page,
				'per_page_values' => $dli_per_page_values,
			)
		);
	}
	?>
</main>

<?php get_footer(); ?>
