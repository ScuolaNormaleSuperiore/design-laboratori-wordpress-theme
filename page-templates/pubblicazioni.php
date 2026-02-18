<?php
/**
 * Template Name: Le pubblicazioni
 *
 * @package Design_Laboratori_Italia
 */

global $post, $wpdb;
get_header();

$per_page        = (string) DLI_PER_PAGE;
$per_page_values = (array) DLI_PER_PAGE_VALUES;
$allowed_pages   = array_map( 'strval', $per_page_values );
$anno_select     = '';

if ( isset( $_GET['per_page'] ) ) {
	$raw_per_page = sanitize_text_field( wp_unslash( $_GET['per_page'] ) );
	if ( in_array( $raw_per_page, $allowed_pages, true ) ) {
		$per_page = $raw_per_page;
	}
}

if ( isset( $_GET['annoSelect'] ) ) {
	$anno_select = (string) absint( wp_unslash( $_GET['annoSelect'] ) );
}

$tipi_pubblicazione = get_terms(
	array(
		'taxonomy'   => PUBLICATION_TYPE_TAXONOMY,
		'hide_empty' => false,
	)
);

$tipi_pubblicazione_params = array();
if ( isset( $_GET['tipologia'] ) ) {
	$tipologia_raw = wp_unslash( $_GET['tipologia'] );
	if ( is_array( $tipologia_raw ) ) {
		$tipi_pubblicazione_params = array_map( 'sanitize_key', $tipologia_raw );
	}
}

if ( is_array( $tipi_pubblicazione ) && ! is_wp_error( $tipi_pubblicazione ) ) {
	$available_type_slugs      = wp_list_pluck( $tipi_pubblicazione, 'slug' );
	$tipi_pubblicazione_params = array_values(
		array_intersect( $tipi_pubblicazione_params, $available_type_slugs )
	);
}

$anni_pubblicazioni = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT DISTINCT pm.meta_value
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
		WHERE pm.meta_key = %s
		AND p.post_type = %s
		AND p.post_status = %s
		ORDER BY pm.meta_value+0 DESC",
		'anno',
		PUBLICATION_POST_TYPE,
		'publish'
	)
);

$paged = absint( get_query_var( 'paged' ) );
if ( 0 === $paged ) {
	$paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $paged ) {
	$paged = 1;
}

$query_args = array(
	'posts_per_page' => (int) $per_page,
	'paged'          => $paged,
	'post_type'      => PUBLICATION_POST_TYPE,
	'meta_key'       => 'anno',
	'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
);

if ( '' !== $anno_select ) {
	$query_args['meta_query'] = array(
		array(
			'key'     => 'anno',
			'compare' => '=',
			'value'   => $anno_select,
			'type'    => 'NUMERIC',
		),
	);
}

if ( ! empty( $tipi_pubblicazione_params ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => PUBLICATION_TYPE_TAXONOMY,
			'field'    => 'slug',
			'operator' => 'IN',
			'terms'    => $tipi_pubblicazione_params,
		),
	);
}

$pubblicazioni = new WP_Query( $query_args );
$num_results   = $pubblicazioni->found_posts;

?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PUBBLICAZIONI -->
	<?php get_template_part( 'template-parts/hero/pubblicazioni' ); ?>

	<!-- ELENCO PUBBLICAZIONI -->
	<section id="pubblicazioni">
		<div class="container p-5">
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<form action="<?php echo esc_url( get_permalink() ); ?>" id="pubblicazioniform" method="GET">
						<!--COLONNA FILTRI -->
						<!-- FILTRO PER ANNO -->
						<div class="row pt-3">
							<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Anno', 'design_laboratori_italia' ); ?></h3>
							<div class="select-wrapper">
								<label for="annoSelect" class="visually-hidden"><?php esc_html_e( 'Anno', 'design_laboratori_italia' ); ?></label>
								<select id="annoSelect" name="annoSelect" onChange="this.form.submit()">
									<option value="" <?php selected( '', $anno_select ); ?>><?php esc_html_e( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php foreach ( $anni_pubblicazioni as $anno ) { ?>
										<option value="<?php echo esc_attr( $anno ); ?>" <?php selected( $anno_select, (string) $anno ); ?>><?php echo esc_html( $anno ); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php
						if ( is_array( $tipi_pubblicazione ) && ! is_wp_error( $tipi_pubblicazione ) && count( $tipi_pubblicazione ) >= 1 ) {
							?>
							<!-- FILTRO PER CATEGORIA -->
							<div class="row pt-5">
								<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Tipologia', 'design_laboratori_italia' ); ?></h3>
								<div>
									<?php foreach ( $tipi_pubblicazione as $tipo_pubblicazione ) { ?>
										<div class="form-check">
											<?php
											$checked      = in_array( $tipo_pubblicazione->slug, $tipi_pubblicazione_params, true );
											$filter_input = 'tipologia-' . sanitize_html_class( $tipo_pubblicazione->slug );
											?>
											<input id="<?php echo esc_attr( $filter_input ); ?>" name="tipologia[]" value="<?php echo esc_attr( $tipo_pubblicazione->slug ); ?>" type="checkbox" <?php checked( true, $checked ); ?> onChange="this.form.submit()">
											<label for="<?php echo esc_attr( $filter_input ); ?>"><?php echo esc_html( $tipo_pubblicazione->name ); ?></label>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php
						}
						?>
						<!--fine filtri -->
					</form>
				</div>
				<!-- PUBBLICAZIONI -->
				<?php
				if ( $num_results ) {
					?>
					<div class="col-12 col-lg-8 pt-3">
						<div class="row">
							<?php
							while ( $pubblicazioni->have_posts() ) {
								$pubblicazioni->the_post();
								$item_id        = get_the_ID();
								$item_title     = get_the_title( $item_id );
								$item_url       = dli_get_field( 'url' );
								$image_metadata = dli_get_image_metadata( get_post( $item_id ) );
								$img_url        = ( isset( $image_metadata['image_url'] ) && $image_metadata['image_url'] ) ? $image_metadata['image_url'] : null;
								$item_terms     = get_the_terms( $item_id, PUBLICATION_TYPE_TAXONOMY );
								?>
								<!--start card-->
								<div class="card-wrapper mb-4">
									<div class="card card-teaser rounded shadow">
										<div class="card-body d-flex gap-3 align-items-start">
											<?php if ( $img_url ) { ?>
												<img src="<?php echo esc_url( $img_url ); ?>" width="150" height="150"
													class="img-fluid flex-shrink-0"
													style="max-width:150px; height:auto;"
													title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>"
													alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>">
											<?php } ?>
											<div class="flex-grow-1">
												<!-- Item title -->
												<h3 class="card-title cardTitlecustomSpacing h5 mb-2">
													<?php if ( ! empty( $item_url ) ) { ?>
														<a href="<?php echo esc_url( $item_url ); ?>">
															<?php echo esc_html( $item_title ); ?>
														</a>
													<?php } else { ?>
														<?php echo esc_html( $item_title ); ?>
													<?php } ?>
												</h3>
												<!-- Item body -->
												<p class="card-text mb-0">
													<?php echo wp_kses_post( apply_filters( 'the_content', get_the_content() ) ); ?>
												</p>
												<!-- Item category -->
												<?php if ( is_array( $item_terms ) && ! is_wp_error( $item_terms ) ) { ?>
													<div class="it-card-taxonomy">
														<?php foreach ( $item_terms as $term ) { ?>
															<span class="visually-hidden">
																<?php esc_html_e( 'Related category:', 'design_laboratori_italia' ); ?>
															</span><?php echo esc_html( $term->name ); ?>
															&nbsp;
														<?php } ?>
													</div>
												<?php } ?>
											</div>

										</div>
									</div>
								</div>
								<!--end card-->
								<?php
							}
							?>
						</div>
					</div>
					<?php
					wp_reset_postdata();
				} else {
					?>
					<div class="col-12 col-lg-8">
						<div class="row pt-2">
							<?php esc_html_e( 'Non e stata trovata nessuna pubblicazione', 'design_laboratori_italia' ); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</section>


	<!-- PAGINAZIONE -->
	<?php
	get_template_part(
		'template-parts/common/paginazione',
		null,
		array(
			'query'           => $pubblicazioni,
			'per_page'        => $per_page,
			'per_page_values' => $per_page_values,
		)
	);
	?>

</main>
<!-- END CONTENT -->

<?php
get_footer();
