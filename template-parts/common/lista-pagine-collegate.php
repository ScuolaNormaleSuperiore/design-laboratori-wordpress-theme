<?php
/**
 * Template part: un livello dell'albero "Pagine collegate" (sidebar delle
 * pagine base con figli), richiamato ricorsivamente per ogni livello di
 * profondità reale (nessun limite fisso a 2 livelli).
 *
 * Ogni voce con figli ha un link di navigazione reale e un bottone di
 * espansione separato: in Bootstrap `data-bs-toggle="collapse"` su un `<a>`
 * con `href` reale blocca sempre la navigazione, quindi non può essere
 * insieme link e trigger dell'accordion sullo stesso elemento (vedi
 * sf-pagina-base-voce1.html). Tutti i rami restano aperti di default; la
 * classe "active" è applicata solo alla voce esattamente corrente, mai agli
 * antenati del ramo.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_pages           = ( isset( $args['pages'] ) && is_array( $args['pages'] ) ) ? $args['pages'] : array();
$dli_current_post_id = isset( $args['current_post_id'] ) ? absint( $args['current_post_id'] ) : 0;
$dli_top_level       = ! empty( $args['top_level'] );
$dli_list_id         = isset( $args['list_id'] ) ? $args['list_id'] : '';

if ( empty( $dli_pages ) ) {
	return;
}
?>

<ul class="<?php echo esc_attr( $dli_top_level ? 'link-list' : 'link-sublist collapse show' ); ?>"<?php echo ( ! $dli_top_level && $dli_list_id ) ? ' id="' . esc_attr( $dli_list_id ) . '"' : ''; ?>>
	<?php foreach ( $dli_pages as $dli_page ) : ?>
		<?php
		$dli_children      = get_pages(
			array(
				'child_of'    => $dli_page->ID,
				'parent'      => $dli_page->ID,
				'sort_column' => 'menu_order',
				'sort_order'  => 'ASC',
			)
		);
		$dli_has_children  = ! empty( $dli_children );
		$dli_is_active     = ( $dli_current_post_id === $dli_page->ID );
		$dli_child_list_id = 'collapse-' . $dli_page->ID;
		$dli_link_class    = 'list-item' . ( $dli_top_level ? ' large medium' : '' ) . ( $dli_has_children ? ' flex-grow-1' : '' ) . ( $dli_is_active ? ' active' : '' );
		?>
		<li>
			<?php if ( $dli_has_children ) : ?>
				<div class="d-flex align-items-center">
					<a class="<?php echo esc_attr( $dli_link_class ); ?>" href="<?php echo esc_url( get_permalink( $dli_page->ID ) ); ?>"<?php echo $dli_is_active ? ' aria-current="page"' : ''; ?>>
						<span><?php echo esc_html( get_the_title( $dli_page ) ); ?></span>
					</a>
					<button
						type="button"
						class="btn-toggle-sublist"
						data-bs-toggle="collapse"
						data-bs-target="#<?php echo esc_attr( $dli_child_list_id ); ?>"
						aria-expanded="true"
						aria-controls="<?php echo esc_attr( $dli_child_list_id ); ?>"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %s: page title */ __( 'Mostra/Nascondi le sottovoci di %s', 'design_laboratori_italia' ), get_the_title( $dli_page ) ) ); ?>"
					>
						<svg class="icon icon-sm icon-primary" aria-hidden="true" focusable="false">
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
						</svg>
					</button>
				</div>
				<?php
				get_template_part(
					'template-parts/common/lista-pagine-collegate',
					null,
					array(
						'pages'           => $dli_children,
						'current_post_id' => $dli_current_post_id,
						'top_level'       => false,
						'list_id'         => $dli_child_list_id,
					)
				);
				?>
			<?php else : ?>
				<a class="<?php echo esc_attr( $dli_link_class ); ?>" href="<?php echo esc_url( get_permalink( $dli_page->ID ) ); ?>"<?php echo $dli_is_active ? ' aria-current="page"' : ''; ?>>
					<span><?php echo esc_html( get_the_title( $dli_page ) ); ?></span>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
