<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items           = $args['items'];
	$dli_section_id  = $args['section_id'];
	$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
	$dli_phone       = $dli_items['phone'];
	$dli_email       = $dli_items['email'];
	$dli_website     = $dli_items['website'];
?>
<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php
	if ( $dli_phone || $dli_email || $dli_website ) {
		?>
		<div class="it-list-wrapper">
			<ul class="it-list">
			<?php
			if ( $dli_phone ) {
				?>
				<li>
					<a href="<?php echo esc_url( 'tel:' . $dli_phone ); ?>" class="list-item" target="_blank" rel="noopener noreferrer">
					<div class="list-item">
						<div class="it-rounded-icon">
							<svg class="icon" role="img" aria-labelledby="Telephone">
								<title>Telephone</title>
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone' ); ?>"></use>
							</svg>
						</div>
						<div class="it-right-zone">
								<span class="text"><?php echo esc_html( $dli_phone ); ?></span>
						</div>
					</div>
				</a>
			</li>
				<?php
			}
			if ( $dli_email ) {
				?>
				<li>
					<a href="<?php echo esc_url( 'mailto:' . $dli_email ); ?>" class="list-item" target="_blank" rel="noopener noreferrer">
					<div class="it-rounded-icon">
					<svg class="icon" role="img" aria-labelledby="Mail">
						<title>Mail</title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail' ); ?>"></use>
					</svg>
					</div>
					<div class="it-right-zone">
							<span class="text"><?php echo esc_html( $dli_email ); ?></span>
					</div>
				</a>
			</li>
				<?php
			}
			if ( $dli_website ) {
				?>
				<li>
					<a class="list-item" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_website ); ?>">
					<div class="it-rounded-icon">
					<svg class="icon" role="img" aria-labelledby="Link">
						<title>Link</title>
						<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link' ); ?>"></use>
					</svg>
					</div>
					<div class="it-right-zone">
						<span class="text"><?php echo esc_html__( 'Sito web', 'design_laboratori_italia' ); ?></span>
					</div>
				</a>
			</li>
				<?php
			}
			?>
			</ul>
		</div>
		<?php
	} else {
		echo '<p>-</p>';
	}
	?>
</section>
