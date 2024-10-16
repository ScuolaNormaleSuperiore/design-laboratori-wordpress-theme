<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	define( 'INDIRIZZI_PER_ROW', 2 );
?>

<section id="<?php echo 'sezione-' . $section_id; ?>">
<?php
	// The mani loop of the page.
	$pindex = 0;
	if ( $num_results ) {
		foreach ( $items as $item ) {
			if ( ( $pindex % INDIRIZZI_PER_ROW ) == 0 ) {
		?>
				<!-- begin row indirizzodiricerca -->
				<div class="row pb-3 col-sm-12">
					<div class="card-wrapper card-teaser-wrapper">
						<?php
							}
							$id          = $item->ID;
							$src_icon    = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder';
							$nome        = get_the_title( $id );
							$descrizione = dli_get_field( 'descrizione_breve', $id );
							$link        = get_permalink( $id );
						?>
								<!--begin card indirizzodiricerca -->
								<div class="card card-teaser rounded shadow">
									<div class="card-body">
										<h3 class="card-title h5 ">
											<svg class="icon">
												<use href="<?php echo $src_icon; ?>"></use>
											</svg>
											<a href="<?php echo $link; ?>"><?php echo $nome; ?></a>
										</h3>
										<div class="card-text">
											<p><?php echo wp_trim_words( $descrizione, DLI_ACF_SHORT_DESC_LENGTH ); ?>
										<?php echo $pindex . ' - ' . INDIRIZZI_PER_ROW . ' - ' . $num_results?>
										</p>
										</div>
									</div>
								</div>
								<!--end card indirizzodiricerca-->
							<?php
								if ( ( ( $pindex % INDIRIZZI_PER_ROW ) === INDIRIZZI_PER_ROW - 1 ) || ( $pindex + 1 === $num_results ) ) {
							?>
					</div>
				</div>
				<!-- end row indirizzodiricerca -->
		<?php
				}
				$pindex++;
			}
		} else {
		echo '<p>-</p>';
		}
		?>
</section>
