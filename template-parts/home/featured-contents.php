<?php
	$featuredcontents_enabled = dli_get_option( 'home_featuredcontents_is_visible', 'homepage' );

	if ( 'true' === $featuredcontents_enabled ) {

?>
<!-- BLOCCO CARD - CONTENUTI IN EVIDENZA (Featured contents) -->
<section id="blocco-card" aria-describedby="Blocco news, eventi e pubblicazioni" class="section pt-5" >
	<div class="section-content">
		<div class="container">
		<div class="row">
			<?php
			// Mostra 3 box nella sezione Contenuti in evideza.
			$boxes= array( 1, 2, 3 );
			foreach( $boxes as $index ) {
				// Print BOX i.
				$fc        = dli_get_option( 'featured_contents_' . $index, 'homepage' )[0];
				$label     = $fc[ 'featured_contents_label_box_' . $index ];
				$template  = $fc[ 'featured_contents_template_box_' . $index];
				$pt        = $fc[ 'featured_contents_type_box_' . $index];

				if ( 'card-news' === $template ) {
					// CARD NOTIZIE.
					get_template_part( 'template-parts/home/card-news', null, array( $index, $label, $template, $pt ) );
				} elseif ( 'card-eventi' === $template ) {
					// CARD_EVENTI.
					get_template_part( 'template-parts/home/card-eventi', null, array( $index, $label, $template, $pt ) );
				} else {
					// CARD PUBBLICAZIONI.
					get_template_part( 'template-parts/home/card-pubblicazioni', null, array( $index, $label, $template, $pt ) );
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
