<?php
	define( 'DLI_RELATED_ITEMS_NUMBER', 3 );
	$related_items = $args['related_items'];
	$related_items = array_slice( $related_items, 0, 3, true);
?>

<section id="sezione-eventi">
	<div class="section-content">
		<div class="row pt-3">
			<h2 class="it-page-section h4 pb-2"><?php echo __( 'News &amp; Eventi', 'design_laboratori_italia' ); ?></h2>
			
			<?php
			foreach ( $related_items as $item ) {
			?>
				<div class="col-12 col-lg-4">
					<?php
					if ( $item->post_type === 'evento' ) {
						get_template_part(
							'template-parts/common/sezione-box-evento',
							null,
							array(
								'item' => $item,
							)
							);
					} else {
						get_template_part(
							'template-parts/common/sezione-box-notizia',
							null,
							array(
								'item' => $item,
							)
							);
					}
					?>
				</div>
			<?php
			}
			?>

		</div>
	</div>
</section>
