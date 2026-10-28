<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_args         = isset( $args ) && is_array( $args ) ? $args : array();
$dli_publications = isset( $dli_args['items'] ) && is_array( $dli_args['items'] ) ? $dli_args['items'] : array();
$dli_section_id   = $dli_args['section_id'] ?? '';
$dli_num_results  = count( $dli_publications );
?>

<section id="<?php echo esc_attr( 'sezione-' . $dli_section_id ); ?>">
	<?php if ( $dli_num_results ) : ?>
		<ul class="list-unstyled mb-0">
			<?php
			foreach ( $dli_publications as $dli_publ ) :
				$dli_id      = $dli_publ->ID;
				$dli_nome    = get_the_title( $dli_id );
				$dli_link    = dli_get_publication_link( $dli_id );
				$dli_content = $dli_publ->post_content;
				?>
				<li class="py-3 border-bottom">
					<h4 class="h6 mb-1">
						<svg class="icon icon-sm icon-primary align-top" aria-hidden="true" focusable="false">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-note' ); ?>"></use>
						</svg>
						<?php
						get_template_part(
							'template-parts/common/linked-title',
							null,
							array(
								'title'    => $dli_nome,
								'url'      => $dli_link['url'],
								'external' => $dli_link['external'],
							)
						);
						?>
					</h4>
					<div class="mb-0 text-secondary"><?php echo wp_kses_post( $dli_content ); ?></div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p>-</p>
	<?php endif; ?>
</section>
