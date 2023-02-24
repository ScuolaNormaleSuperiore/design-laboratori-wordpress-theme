<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	define( 'PROGETTI_PER_ROW', 3 );
?>
<section id="<?php echo 'sezione' . $section_id; ?>">

<div class="row pb-3">
	<div class="card-wrapper card-teaser-wrapper">
	<?php
		// The mani loop of the page.
		$pindex = 0;
		if ( $num_results ) {
			foreach ( $items as $item ) {
				$id    = $item->ID;
				$link  = get_the_permalink( $id );
				$title = get_the_title( $id );
				$desc  = get_field( 'descrizione_breve', $id );
	?>

		<!--begin card progetti-->
		<div class="card card-teaser rounded shadow">
			<div class="card-body">
				<h3 class="card-title h5 ">
					<svg class="icon">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder'; ?>"></use>
					</svg>
					<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_attr( $title ); ?></a>
				</h3>
				<div class="card-text">
					<p><?php echo wp_trim_words( get_field( 'descrizione_breve', $id ), DLI_ACF_SHORT_DESC_LENGTH ); ?></p>
				</div>
			</div>
		</div>
		<!--end card progetti-->

			<?php
			$pindex++;
				}
			} else {
			echo '<p>-</p>';
			}
			?>
	</div>
</div>

</section>
