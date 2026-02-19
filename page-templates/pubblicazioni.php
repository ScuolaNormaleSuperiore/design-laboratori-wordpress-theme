<?php
/**
 * Template Name: Le pubblicazioni
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();

$dli_per_page        = (string) DLI_PER_PAGE;
$dli_per_page_values = (array) DLI_PER_PAGE_VALUES;
$dli_allowed_pages   = array_map( 'strval', $dli_per_page_values );
$dli_anno_select     = '';
$dli_testo_sezione   = dli_get_configuration_field_by_lang( 'ordine_pubblicazioni', 'pubblicazioni' );

$dli_raw_per_page = filter_input( INPUT_GET, 'per_page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( is_string( $dli_raw_per_page ) ) {
	$dli_raw_per_page = sanitize_text_field( wp_unslash( $dli_raw_per_page ) );
	if ( in_array( $dli_raw_per_page, $dli_allowed_pages, true ) ) {
		$dli_per_page = $dli_raw_per_page;
	}
}

$dli_raw_anno_select = filter_input( INPUT_GET, 'annoSelect', FILTER_SANITIZE_NUMBER_INT );
if ( is_scalar( $dli_raw_anno_select ) ) {
	$dli_anno_value = absint( $dli_raw_anno_select );
	if ( $dli_anno_value > 0 ) {
		$dli_anno_select = (string) $dli_anno_value;
	}
}

$dli_tipi_pubblicazione = get_terms(
	array(
		'taxonomy'   => PUBLICATION_TYPE_TAXONOMY,
		'hide_empty' => false,
	)
);

$dli_tipi_pubblicazione_params = array();
$dli_tipologia_raw             = filter_input( INPUT_GET, 'tipologia', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
if ( is_array( $dli_tipologia_raw ) ) {
	$dli_tipi_pubblicazione_params = array_map( 'sanitize_key', $dli_tipologia_raw );
}

if ( is_array( $dli_tipi_pubblicazione ) && ! is_wp_error( $dli_tipi_pubblicazione ) ) {
	$dli_available_type_slugs      = wp_list_pluck( $dli_tipi_pubblicazione, 'slug' );
	$dli_tipi_pubblicazione_params = array_values(
		array_intersect( $dli_tipi_pubblicazione_params, $dli_available_type_slugs )
	);
}

$dli_anni_pubblicazioni = array();
$dli_publication_ids    = get_posts(
	array(
		'post_type'      => PUBLICATION_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_key'       => 'anno',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	)
);

if ( is_array( $dli_publication_ids ) ) {
	foreach ( $dli_publication_ids as $dli_publication_id ) {
		$dli_anno = absint( get_post_meta( $dli_publication_id, 'anno', true ) );
		if ( $dli_anno > 0 ) {
			$dli_anni_pubblicazioni[ $dli_anno ] = (string) $dli_anno;
		}
	}
}

$dli_paged = absint( get_query_var( 'paged' ) );
if ( 0 === $dli_paged ) {
	$dli_paged = absint( get_query_var( 'page' ) );
}
if ( 0 === $dli_paged ) {
	$dli_paged = 1;
}

$dli_query_args = array(
	'posts_per_page' => (int) $dli_per_page,
	'paged'          => $dli_paged,
	'post_type'      => PUBLICATION_POST_TYPE,
);

$dli_sort_mode = is_string( $dli_testo_sezione ) ? sanitize_key( $dli_testo_sezione ) : 'post_date';
if ( ! in_array( $dli_sort_mode, array( 'post_date', 'post_modified', 'event_date', 'publish_year' ), true ) ) {
	$dli_sort_mode = 'post_date';
}

switch ( $dli_sort_mode ) {
	case 'post_modified':
		$dli_query_args['orderby'] = 'modified';
		$dli_query_args['order']   = 'DESC';
		break;

	case 'event_date':
		$dli_query_args['orderby'] = 'title';
		$dli_query_args['order']   = 'ASC';
		break;

	case 'publish_year':
		$dli_query_args['meta_key'] = 'anno';
		$dli_query_args['orderby']  = 'meta_value_num';
		$dli_query_args['order']    = 'DESC';
		break;

	case 'post_date':
	default:
		$dli_query_args['orderby'] = 'date';
		$dli_query_args['order']   = 'DESC';
		break;
}

if ( '' !== $dli_anno_select ) {
	$dli_query_args['meta_query'] = array(
		array(
			'key'     => 'anno',
			'compare' => '=',
			'value'   => $dli_anno_select,
			'type'    => 'NUMERIC',
		),
	);
}

if ( ! empty( $dli_tipi_pubblicazione_params ) ) {
	$dli_query_args['tax_query'] = array(
		array(
			'taxonomy' => PUBLICATION_TYPE_TAXONOMY,
			'field'    => 'slug',
			'operator' => 'IN',
			'terms'    => $dli_tipi_pubblicazione_params,
		),
	);
}

$dli_pubblicazioni_query = new WP_Query( $dli_query_args );
$dli_num_results         = $dli_pubblicazioni_query->found_posts;

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
									<option value="" <?php selected( '', $dli_anno_select ); ?>><?php esc_html_e( "Scegli un'opzione", 'design_laboratori_italia' ); ?></option>
									<?php foreach ( $dli_anni_pubblicazioni as $dli_anno_option ) { ?>
										<option value="<?php echo esc_attr( $dli_anno_option ); ?>" <?php selected( $dli_anno_select, (string) $dli_anno_option ); ?>><?php echo esc_html( $dli_anno_option ); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php
						if ( is_array( $dli_tipi_pubblicazione ) && ! is_wp_error( $dli_tipi_pubblicazione ) && count( $dli_tipi_pubblicazione ) >= 1 ) {
							?>
							<!-- FILTRO PER CATEGORIA -->
							<div class="row pt-5">
								<h3 class="h6 text-uppercase border-bottom"><?php esc_html_e( 'Tipologia', 'design_laboratori_italia' ); ?></h3>
								<div>
									<?php foreach ( $dli_tipi_pubblicazione as $dli_tipo_pubblicazione ) { ?>
										<div class="form-check">
											<?php
											$dli_checked      = in_array( $dli_tipo_pubblicazione->slug, $dli_tipi_pubblicazione_params, true );
											$dli_filter_input = 'tipologia-' . sanitize_html_class( $dli_tipo_pubblicazione->slug );
											?>
											<input id="<?php echo esc_attr( $dli_filter_input ); ?>" name="tipologia[]" value="<?php echo esc_attr( $dli_tipo_pubblicazione->slug ); ?>" type="checkbox" <?php checked( true, $dli_checked ); ?> onChange="this.form.submit()">
											<label for="<?php echo esc_attr( $dli_filter_input ); ?>"><?php echo esc_html( $dli_tipo_pubblicazione->name ); ?></label>
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
				if ( $dli_num_results ) {
					?>
					<div class="col-12 col-lg-8 pt-3">
						<div class="row">
							<?php
							while ( $dli_pubblicazioni_query->have_posts() ) {
								$dli_pubblicazioni_query->the_post();
								$dli_item_id        = get_the_ID();
								$dli_item_title     = get_the_title( $dli_item_id );
								$dli_item_url       = dli_get_field( 'url' );
								$dli_image_metadata = dli_get_image_metadata( get_post( $dli_item_id ) );
								$dli_img_url        = ( isset( $dli_image_metadata['image_url'] ) && $dli_image_metadata['image_url'] ) ? $dli_image_metadata['image_url'] : null;
								$dli_item_terms     = get_the_terms( $dli_item_id, PUBLICATION_TYPE_TAXONOMY );
								?>
								<!--start card-->
								<div class="card-wrapper mb-4">
									<div class="card card-teaser rounded shadow">
										<div class="card-body d-flex gap-3 align-items-start">
											<?php if ( $dli_img_url ) { ?>
												<img src="<?php echo esc_url( $dli_img_url ); ?>" width="150" height="150"
													class="img-fluid flex-shrink-0"
													style="max-width:150px; height:auto;"
													title="<?php echo esc_attr( $dli_image_metadata['image_title'] ); ?>"
													alt="<?php echo esc_attr( $dli_image_metadata['image_alt'] ); ?>">
											<?php } ?>
											<div class="flex-grow-1">
												<!-- Item title -->
												<h3 class="card-title cardTitlecustomSpacing h5 mb-2">
													<?php if ( ! empty( $dli_item_url ) ) { ?>
														<a href="<?php echo esc_url( $dli_item_url ); ?>">
															<?php echo esc_html( $dli_item_title ); ?>
														</a>
													<?php } else { ?>
														<?php echo esc_html( $dli_item_title ); ?>
													<?php } ?>
												</h3>
												<!-- Item body -->
												<p class="card-text mb-0">
													<?php echo wp_kses_post( wpautop( get_the_content() ) ); ?>
												</p>
												<!-- Item category -->
												<?php if ( is_array( $dli_item_terms ) && ! is_wp_error( $dli_item_terms ) ) { ?>
													<div class="it-card-taxonomy">
														<?php foreach ( $dli_item_terms as $dli_item_term ) { ?>
															<span class="visually-hidden">
																<?php esc_html_e( 'Categorie collegate:', 'design_laboratori_italia' ); ?>
															</span><?php echo esc_html( $dli_item_term->name ); ?>
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
						<div class="row p-2">
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
			'query'           => $dli_pubblicazioni_query,
			'per_page'        => $dli_per_page,
			'per_page_values' => $dli_per_page_values,
		)
	);
	?>

</main>
<!-- END CONTENT -->

<?php
get_footer();
