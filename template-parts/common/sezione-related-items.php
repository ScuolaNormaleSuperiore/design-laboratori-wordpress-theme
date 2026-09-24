<?php
/**
 * Template part.
 *
 * Card semplice senza immagine, su griglia (non un carousel): stesso pattern
 * della sezione "Eventi" nella scheda di dettaglio del prototipo statico
 * (sf-scheda-progetto.html), non quello del carousel "Eventi" della home
 * (sf-index.html, con immagine) usato inizialmente per errore.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_related_items = $args['items'];
$dli_num_items     = dli_get_option( 'numero_pagine_collegate', 'notizie' );
$dli_num_items     = $dli_num_items ? $dli_num_items : 5;
$dli_related_items = array_slice( $dli_related_items, 0, $dli_num_items, true );
?>

<section id="sezione-eventi">
	<h3 class="it-page-section h4 pt-3"><?php echo esc_html__( 'Eventi e notizie', 'design_laboratori_italia' ); ?></h3>
	<div class="row g-3">
		<?php
		foreach ( $dli_related_items as $dli_item ) {
			?>
			<div class="col-12 col-md-6">
				<?php
				if ( 'evento' === $dli_item->post_type ) {
					get_template_part(
						'template-parts/common/sezione-box-evento',
						null,
						array(
							'item' => $dli_item,
						)
					);
				} else {
					get_template_part(
						'template-parts/common/sezione-box-notizia',
						null,
						array(
							'item' => $dli_item,
						)
					);
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</section>
