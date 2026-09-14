<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_publications = $args['items'];
$dli_section_id   = $args['section_id'];
$dli_num_results  = is_array( $dli_publications ) ? count( $dli_publications ) : 0;
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php if ( $dli_num_results ) : ?>
		<ul class="list-unstyled mb-0">
			<?php
			foreach ( $dli_publications as $dli_publ ) :
				$dli_id      = $dli_publ->ID;
				$dli_nome    = get_the_title( $dli_id );
				$dli_link    = get_permalink( $dli_id );
				$dli_content = $dli_publ->post_content;
				?>
				<li class="py-3 border-bottom">
					<h4 class="h6 mb-1">
						<svg class="icon icon-sm icon-primary align-top" aria-hidden="true" focusable="false">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note' ); ?>"></use>
						</svg>
						<a href="<?php echo esc_url( $dli_link ); ?>"><?php echo esc_html( $dli_nome ); ?></a>
					</h4>
					<div class="mb-0 text-secondary"><?php echo wp_kses_post( $dli_content ); ?></div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p>-</p>
	<?php endif; ?>
</section>
