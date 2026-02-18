<?php
/**
 * Homepage featured contents section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_featuredcontents_enabled = dli_get_option( 'home_featuredcontents_is_visible', 'homepage' );

if ( 'true' === $dli_featuredcontents_enabled ) {

	?>
<!-- BLOCCO CARD - CONTENUTI IN EVIDENZA (Featured contents) -->
<section id="blocco-card" aria-describedby="Blocco news, eventi e pubblicazioni" class="section pt-5" >
	<div class="section-content">
		<div class="container">
		<div class="row">
		<?php
		// Mostra 3 box nella sezione Contenuti in evideza.
		$dli_boxes = array( 1, 2, 3 );
		foreach ( $dli_boxes as $dli_index ) {
			// Print BOX i.
			$dli_fc       = dli_get_option( 'featured_contents_' . $dli_index, 'homepage' )[0];
			$dli_label    = $dli_fc[ 'featured_contents_label_box_' . $dli_index ];
			$dli_template = $dli_fc[ 'featured_contents_template_box_' . $dli_index ];
			$dli_pt       = $dli_fc[ 'featured_contents_type_box_' . $dli_index ];

			if ( 'card-news' === $dli_template ) {
				// CARD NOTIZIE.
				get_template_part( 'template-parts/home/card-news', null, array( $dli_index, $dli_label, $dli_template, $dli_pt ) );
			} elseif ( 'card-eventi' === $dli_template ) {
				// CARD_EVENTI.
				get_template_part( 'template-parts/home/card-eventi', null, array( $dli_index, $dli_label, $dli_template, $dli_pt ) );
			} else {
				// CARD PUBBLICAZIONI.
				get_template_part( 'template-parts/home/card-pubblicazioni', null, array( $dli_index, $dli_label, $dli_template, $dli_pt ) );
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
