<?php
/**
 * Homepage featured contents section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;

// Raccoglie prima i box effettivamente configurati E con almeno un contenuto
// da mostrare (post del tipo scelto con "Promuovi in home" attivo): la
// larghezza delle colonne deve adattarsi al numero di colonne davvero piene
// (tutta la larghezza con 1, metà con 2, un terzo con 3) — una colonna
// configurata ma senza contenuti per quella tipologia non va contata, e non
// va neanche mostrata vuota.
$dli_active_boxes = array();
foreach ( array( 1, 2, 3 ) as $dli_index ) {
	$dli_option = dli_get_option( 'featured_contents_' . $dli_index, 'homepage' );
	if ( ! is_array( $dli_option ) || empty( $dli_option[0] ) ) {
		continue;
	}
	$dli_fc = $dli_option[0];
	$dli_pt = $dli_fc[ 'featured_contents_type_box_' . $dli_index ];

	$dli_has_items = new WP_Query(
		array(
			'post_type'      => array( $dli_pt ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		)
	);

	if ( $dli_has_items->have_posts() ) {
		$dli_active_boxes[ $dli_index ] = $dli_fc;
	}
}

if ( 'true' === $dli_section_enabled && ! empty( $dli_active_boxes ) ) {
	$dli_col_lg_class = 'col-lg-' . intdiv( 12, count( $dli_active_boxes ) );
	?>
<!-- BLOCCO CARD - CONTENUTI IN EVIDENZA (Featured contents) -->
<section id="blocco-card" class="section pt-5" aria-labelledby="blocco-card-title">
	<div class="section-content">
		<div class="container">
			<h2 id="blocco-card-title" class="visually-hidden"><?php esc_html_e( 'Contenuti in evidenza', 'design_laboratori_italia' ); ?></h2>
		<div class="row">
		<?php
		foreach ( $dli_active_boxes as $dli_index => $dli_fc ) {
			$dli_label    = $dli_fc[ 'featured_contents_label_box_' . $dli_index ];
			$dli_template = $dli_fc[ 'featured_contents_template_box_' . $dli_index ];
			$dli_pt       = $dli_fc[ 'featured_contents_type_box_' . $dli_index ];

			if ( 'card-news' === $dli_template ) {
				// CARD NOTIZIE.
				get_template_part( 'template-parts/home/card-news', null, array( $dli_index, $dli_label, $dli_template, $dli_pt, $dli_col_lg_class ) );
			} elseif ( 'card-eventi' === $dli_template ) {
				// CARD_EVENTI.
				get_template_part( 'template-parts/home/card-eventi', null, array( $dli_index, $dli_label, $dli_template, $dli_pt, $dli_col_lg_class ) );
			} else {
				// CARD PUBBLICAZIONI.
				get_template_part( 'template-parts/home/card-pubblicazioni', null, array( $dli_index, $dli_label, $dli_template, $dli_pt, $dli_col_lg_class ) );
			}
		}
		?>
			</div>
		</div>
	</div>
</section>
<!-- FINE BLOCCO CARD -->
	<?php
}
?>
