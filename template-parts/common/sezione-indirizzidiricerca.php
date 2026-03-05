<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items           = $args['items'];
	$dli_section_id  = $args['section_id'];
	$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
	define( 'INDIRIZZI_PER_ROW', 2 );
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
<?php
	// The mani loop of the page.
	$dli_pindex = 0;
if ( $dli_num_results ) {
	foreach ( $dli_items as $dli_item ) {
		if ( 0 === ( $dli_pindex % INDIRIZZI_PER_ROW ) ) {
			?>
				<!-- begin row indirizzodiricerca -->
				<div class="row pb-3 col-sm-12">
					<div class="card-wrapper card-teaser-wrapper">
				<?php
		}
						$dli_id              = $dli_item->ID;
						$dli_src_icon        = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder';
							$dli_nome        = get_the_title( $dli_id );
							$dli_descrizione = dli_get_field( 'descrizione_breve', $dli_id );
							$dli_link        = get_permalink( $dli_id );
		?>
								<!--begin card indirizzodiricerca -->
								<div class="card card-teaser rounded shadow">
									<div class="card-body">
										<h3 class="card-title cardTitlecustomSpacing h5 ">
											<svg class="icon" aria-label="Folder">
												<title>Folder</title>
												<use href="<?php echo esc_url( $dli_src_icon ); ?>"></use>
											</svg>
											<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_nome ); ?></a>
										</h3>
											<div class="card-text">
												<p><?php echo wp_kses_post( wp_trim_words( $dli_descrizione, DLI_ACF_SHORT_DESC_LENGTH ) ); ?>
											</p>
											</div>
									</div>
								</div>
								<!--end card indirizzodiricerca-->
							<?php
							if ( ( ( $dli_pindex % INDIRIZZI_PER_ROW ) === INDIRIZZI_PER_ROW - 1 ) || ( $dli_pindex + 1 === $dli_num_results ) ) {
								?>
					</div>
				</div>
				<!-- end row indirizzodiricerca -->
								<?php
							}
							++$dli_pindex;
	}
} else {
	echo '<p>-</p>';
}
?>
</section>
