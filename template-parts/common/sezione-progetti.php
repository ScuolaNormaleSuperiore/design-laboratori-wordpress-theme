<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items           = $args['items'];
	$dli_section_id  = $args['section_id'];
	$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
	define( 'PROGETTI_PER_ROW', 3 );
?>
<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
<div class="row pb-3">
	<div class="card-wrapper card-teaser-wrapper">
	<?php
		// The mani loop of the page.
		$dli_pindex = 0;
	if ( $dli_num_results ) {
		foreach ( $dli_items as $dli_item ) {
			$dli_id       = $dli_item->ID;
			$dli_link     = get_the_permalink( $dli_id );
			$dli_title    = get_the_title( $dli_id );
			$dli_desc = dli_get_field( 'descrizione_breve', $dli_id );
			?>

		<!--begin card progetti-->
		<div class="card card-teaser rounded shadow">
			<div class="card-body">
				<h3 class="card-title cardTitlecustomSpacing h5 ">
					<svg class="icon" role="img" aria-labelledby="Folder">
						<title>Folder</title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
					</svg>
					<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_title ); ?></a>
				</h3>
				<div class="card-text">
					<p><?php echo wp_kses_post( wp_trim_words( dli_get_field( 'descrizione_breve', $dli_id ), DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
				</div>
			</div>
		</div>
		<!--end card progetti-->

			<?php
				++$dli_pindex;
		}
	} else {
		echo '<p>-</p>';
	}
	?>
	</div>
</div>

</section>
