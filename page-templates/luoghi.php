<?php
/**
 * Template Name: I Luoghi
 *
 * @package Design_Laboratori_Italia
 */

global $post;

get_header();

// Extract selected place-type filters from valid taxonomy slugs only.
$dli_place_type_params   = array();
$dli_allowed_place_types = get_terms(
	array(
		'taxonomy'   => PLACE_TYPE_TAXONOMY,
		'hide_empty' => false,
		'fields'     => 'slugs',
	)
);

$dli_allowed_place_types = ( is_wp_error( $dli_allowed_place_types ) || ! is_array( $dli_allowed_place_types ) )
	? array()
	: $dli_allowed_place_types;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only archive filters.
foreach ( $_GET as $dli_parameter_key => $dli_parameter_value ) {
	if ( ! is_string( $dli_parameter_value ) ) {
		continue;
	}

	$dli_sanitized_key   = sanitize_key( wp_unslash( $dli_parameter_key ) );
	$dli_sanitized_value = sanitize_key( wp_unslash( $dli_parameter_value ) );

	if ( $dli_sanitized_key !== $dli_sanitized_value ) {
		continue;
	}

	if ( ! in_array( $dli_sanitized_key, $dli_allowed_place_types, true ) ) {
		continue;
	}

	$dli_place_type_params[] = $dli_sanitized_key;
}

$dli_place_type_params = array_values( array_unique( $dli_place_type_params ) );
$dli_paged             = absint( get_query_var( 'paged' ) );

if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}

if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_query_args = array(
	'posts_per_page' => DLI_POSTS_PER_PAGE,
	'paged'          => $dli_paged,
	'post_type'      => PLACE_POST_TYPE,
	'orderby'        => 'title',
);

if ( count( $dli_place_type_params ) > 0 ) {
	$dli_query_args['tax_query'] = array(
		array(
			'taxonomy' => PLACE_TYPE_TAXONOMY,
			'field'    => 'slug',
			'operator' => 'IN',
			'terms'    => $dli_place_type_params,
		),
	);
}

$dli_places_query = new WP_Query( $dli_query_args );
$dli_num_results  = $dli_places_query->found_posts;
$dli_place_types  = dli_get_all_place_types_with_results();
?>

<form action="<?php echo esc_url( get_permalink() ); ?>" id="luoghiform" method="get">
	<main id="main-container" role="main">
		<!-- BREADCRUMB -->
		<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

		<!-- BANNER LUOGHI -->
		<?php get_template_part( 'template-parts/hero/luoghi' ); ?>

		<!-- ELENCO LUOGHI -->
		<section id="luoghi" class="p-4">
			<div class="container my-4">
				<div class="row pt-0">
					<div class="col-12 col-lg-3 border-end">
						<!-- COLONNA FILTRI -->
						<?php if ( count( $dli_place_types ) >= 1 ) : ?>
							<div class="row pt-4">
								<h3 class="h6 text-uppercase border-bottom"><?php echo esc_html__( 'Tipologia', 'design_laboratori_italia' ); ?></h3>
								<div>
									<?php foreach ( $dli_place_types as $dli_place_type ) : ?>
										<div class="form-check">
											<input
												id="<?php echo esc_attr( $dli_place_type->slug ); ?>"
												name="<?php echo esc_attr( $dli_place_type->slug ); ?>"
												value="<?php echo esc_attr( $dli_place_type->slug ); ?>"
												type="checkbox"
												<?php checked( in_array( $dli_place_type->slug, $dli_place_type_params, true ) ); ?>
												onchange="this.form.submit()"
											>
											<label for="<?php echo esc_attr( $dli_place_type->slug ); ?>"><?php echo esc_html( $dli_place_type->name ); ?></label>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
						<!-- fine filtri -->
					</div>

					<!-- LUOGHI -->
					<?php if ( $dli_num_results ) : ?>
						<div class="col-12 col-lg-8">
							<div class="row">
								<?php while ( $dli_places_query->have_posts() ) : ?>
									<?php
									$dli_places_query->the_post();
									$dli_place_id    = get_the_ID();
									$dli_place_title = get_the_title( $dli_place_id );
									$dli_desc        = dli_get_field( 'descrizione_breve' );
									$dli_address     = dli_get_field( 'indirizzo' );
									$dli_terms       = get_the_terms( $dli_place_id, PLACE_TYPE_TAXONOMY );
									$dli_place_type  = $dli_terms ? $dli_terms[0]->name : '';
									?>
									<div class="card-wrapper">
										<div class="card card-teaser rounded shadow">
											<div class="card-body">
												<h3 class="card-title cardTitlecustomSpacing h5">
													<svg class="icon" role="img" aria-labelledby="map-marker-title">
														<title id="map-marker-title">Map Marker</title>
														<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-map-marker' ); ?>"></use>
													</svg>
													<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( $dli_place_title ); ?></a>
												</h3>
												<div class="card-text">
													<p>
														<?php echo wp_kses_post( $dli_desc ); ?><br>
														<?php echo esc_html( $dli_address ); ?><br>
														<?php echo esc_html( $dli_place_type ); ?><br>
													</p>
												</div>
											</div>
										</div>
									</div>
								<?php endwhile; ?>
							</div>
						</div>
					<?php else : ?>
						<div class="col-12 col-lg-8">
							<div class="row pt-2">
								<?php echo esc_html__( 'Non è stata trovato nessun luogo', 'design_laboratori_italia' ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
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
				'query' => $dli_places_query,
			)
		);
		?>
	</main>
</form>

<?php get_footer(); ?>
