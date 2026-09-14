<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_items       = $args['items'];
$dli_section_id  = $args['section_id'];
$dli_num_results = is_array( $dli_items ) ? count( $dli_items ) : 0;
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php if ( $dli_num_results ) : ?>
		<div class="row g-3 pb-3">
			<?php
			foreach ( $dli_items as $dli_item ) :
				$dli_id          = $dli_item->ID;
				$dli_nome        = get_the_title( $dli_id );
				$dli_descrizione = dli_get_field( 'descrizione_breve', $dli_id );
				$dli_link        = get_permalink( $dli_id );
				?>
				<div class="col-md-6">
					<article class="it-card rounded shadow h-100">
						<h3 class="it-card-title cardTitlecustomSpacing h5">
							<svg class="icon" aria-hidden="true" focusable="false">
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-folder' ); ?>"></use>
							</svg>
							<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_nome ); ?></a>
						</h3>
						<div class="it-card-body">
							<p class="it-card-text"><?php echo wp_kses_post( wp_trim_words( $dli_descrizione, DLI_ACF_SHORT_DESC_LENGTH ) ); ?></p>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p>-</p>
	<?php endif; ?>
</section>
