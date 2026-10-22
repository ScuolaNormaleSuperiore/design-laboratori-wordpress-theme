<?php
/**
 * Homepage "Navigazione per pagine" section.
 *
 * Per ogni pagina con il flag "Promuovi in home" (lo stesso usato dalla
 * sezione "Pagine in evidenza", per ora) mostra il titolo come intestazione di
 * colonna e le sue sottopagine come lista di link ("Azioni, con freccia").
 * Doppio markup: colonne su tablet/desktop, accordion su mobile (primo
 * pannello aperto) — stesso pattern del prototipo statico bs-playground
 * (sf-index.html, #blocco-fasi).
 *
 * @package Design_Laboratori_Italia
 */

$dli_section_enabled = $args['enabled'] ?? false;
$dli_show_title       = $args['show_title'] ?? false;
$dli_title            = dli_get_configuration_field_by_lang( 'home_fasi_title', 'homepage' );
$dli_text             = dli_get_configuration_field_by_lang( 'home_fasi_text', 'homepage' );
$dli_bg_class         = dli_get_hp_section_background_class( $args, 'muted' );

if ( 'true' === $dli_section_enabled ) {
	$dli_fasi_query = new WP_Query(
		array(
			'post_type'      => WP_DEFAULT_PAGE,
			'post_status'    => 'publish',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'posts_per_page' => 6,
			'meta_query'     => array(
				array(
					'key'     => 'promuovi_in_home',
					'compare' => '=',
					'value'   => 1,
				),
			),
		)
	);

	// Alberi dei menu di navigazione principali (stesso meccanismo di
	// Main_Menu_Walker::dli_get_menu_tree(), inc/walkers/main-menu-walker.php):
	// usati solo per RIORDINARE le sottopagine come nel dropdown del menu, non
	// come fonte delle voci (che restano le sottopagine WordPress). Le pagine
	// di primo livello con un proprio dropdown (come "Pagina base") stanno
	// nel menu "menu-right" ("Menu secondario multilevel a destra"); "menu-lab"
	// è incluso per coprire anche eventuali voci di primo livello lì presenti.
	$dli_menu_locations    = get_nav_menu_locations();
	$dli_menu_trees        = array();
	foreach ( array( 'menu-lab', 'menu-right' ) as $dli_menu_location ) {
		$dli_menu_id = isset( $dli_menu_locations[ $dli_menu_location ] ) ? $dli_menu_locations[ $dli_menu_location ] : 0;
		if ( ! $dli_menu_id ) {
			continue;
		}
		$dli_menu_items = wp_get_nav_menu_items( $dli_menu_id, array( 'order' => 'DESC' ) );
		if ( $dli_menu_items ) {
			$dli_menu_trees[] = dli_menu_tree_by_items( $dli_menu_items );
		}
	}
	$dli_menu_item_by_page = array();
	foreach ( $dli_menu_trees as $dli_tree_index => $dli_menu_tree ) {
		foreach ( $dli_menu_tree as $dli_menu_item_id => $dli_menu_node ) {
			if ( isset( $dli_menu_node['element']->object_id ) ) {
				$dli_menu_item_by_page[ (int) $dli_menu_node['element']->object_id ] = array( $dli_tree_index, $dli_menu_item_id );
			}
		}
	}

	// Per ogni fase, recupera le sottopagine: le fasi senza sottopagine non vengono mostrate.
	$dli_fasi = array();
	foreach ( $dli_fasi_query->posts as $dli_fase ) {
		$dli_azioni = get_posts(
			array(
				'post_type'      => WP_DEFAULT_PAGE,
				'post_status'    => 'publish',
				'post_parent'    => $dli_fase->ID,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => 4,
			)
		);

		// Riordina le sottopagine come nel dropdown del menu, quando la fase
		// e' presente in uno dei menu con sottovoci: le sottopagine non
		// presenti nel menu restano in fondo, nell'ordine gia' calcolato sopra.
		$dli_fase_menu_ref     = $dli_menu_item_by_page[ $dli_fase->ID ] ?? null;
		$dli_fase_menu_tree    = null !== $dli_fase_menu_ref ? $dli_menu_trees[ $dli_fase_menu_ref[0] ] : null;
		$dli_fase_menu_item_id = null !== $dli_fase_menu_ref ? $dli_fase_menu_ref[1] : null;
		if ( null !== $dli_fase_menu_item_id && ! empty( $dli_fase_menu_tree[ $dli_fase_menu_item_id ]['children'] ) ) {
			$dli_menu_order = array();
			foreach ( $dli_fase_menu_tree[ $dli_fase_menu_item_id ]['children'] as $dli_position => $dli_menu_child ) {
				if ( isset( $dli_menu_child->object_id ) ) {
					$dli_menu_order[ (int) $dli_menu_child->object_id ] = $dli_position;
				}
			}
			usort(
				$dli_azioni,
				function ( $dli_a, $dli_b ) use ( $dli_menu_order ) {
					$dli_pos_a = $dli_menu_order[ $dli_a->ID ] ?? PHP_INT_MAX;
					$dli_pos_b = $dli_menu_order[ $dli_b->ID ] ?? PHP_INT_MAX;
					return $dli_pos_a <=> $dli_pos_b;
				}
			);
		}

		if ( ! empty( $dli_azioni ) ) {
			$dli_fasi[] = array(
				'id'      => $dli_fase->ID,
				'title'   => get_the_title( $dli_fase->ID ),
				'azioni'  => $dli_azioni,
			);
		}
	}

	if ( ! empty( $dli_fasi ) ) {
		// Larghezza delle colonne da lg in su: si adatta al numero di fasi
		// effettive (mezza con 2, un terzo con 3, un quarto da 4 in su),
		// invece di restare fissa a un terzo indipendentemente da quante sono.
		$dli_col_lg_class = 'col-lg-' . intdiv( 12, min( count( $dli_fasi ), 4 ) );
		?>
	<!-- INIZIO SEZIONE "LE SEZIONI DEL SITO" -->
	<section id="blocco-fasi" class="section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" <?php echo ( 'true' === $dli_show_title && $dli_title ) ? 'aria-labelledby="blocco-fasi-title"' : 'aria-label="' . esc_attr__( 'Navigazione per pagine', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( 'true' === $dli_show_title && $dli_title ) : ?>
					<h2 id="blocco-fasi-title" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $dli_text ) : ?>
					<p class="pb-3"><?php echo esc_html( $dli_text ); ?></p>
				<?php endif; ?>

				<!-- Tablet e desktop: colonne -->
				<div class="row d-none d-md-flex">
					<?php foreach ( $dli_fasi as $dli_fase ) : ?>
						<div class="col-12 col-md-6 <?php echo esc_attr( $dli_col_lg_class ); ?> mb-4">
							<h3 id="<?php echo esc_attr( 'fase-' . $dli_fase['id'] . '-title' ); ?>" class="h5 fw-bold"><?php echo esc_html( $dli_fase['title'] ); ?></h3>
							<div class="it-list-wrapper">
								<ul class="it-list">
									<?php foreach ( $dli_fase['azioni'] as $dli_azione ) : ?>
										<li>
											<a class="list-item" href="<?php echo esc_url( get_permalink( $dli_azione->ID ) ); ?>">
												<div class="it-right-zone">
													<span class="text fw-semibold"><?php echo esc_html( get_the_title( $dli_azione->ID ) ); ?></span>
													<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
														<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
													</svg>
												</div>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Mobile: accordion -->
				<div class="accordion d-md-none" id="fasi-accordion">
					<?php foreach ( $dli_fasi as $dli_index => $dli_fase ) : ?>
						<?php
						$dli_is_first  = ( 0 === $dli_index );
						$dli_head_id   = 'fasi-acc-head-' . $dli_fase['id'];
						$dli_body_id   = 'fasi-acc-' . $dli_fase['id'];
						?>
						<div class="accordion-item">
							<h3 class="accordion-header" id="<?php echo esc_attr( $dli_head_id ); ?>">
								<button
									class="accordion-button<?php echo $dli_is_first ? '' : ' collapsed'; ?>"
									type="button"
									data-bs-toggle="collapse"
									data-bs-target="#<?php echo esc_attr( $dli_body_id ); ?>"
									aria-expanded="<?php echo $dli_is_first ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $dli_body_id ); ?>"
								>
									<?php echo esc_html( $dli_fase['title'] ); ?>
								</button>
							</h3>
							<div id="<?php echo esc_attr( $dli_body_id ); ?>" class="accordion-collapse collapse<?php echo $dli_is_first ? ' show' : ''; ?>" role="region" aria-labelledby="<?php echo esc_attr( $dli_head_id ); ?>">
								<div class="accordion-body">
									<div class="it-list-wrapper">
										<ul class="it-list">
											<?php foreach ( $dli_fase['azioni'] as $dli_azione ) : ?>
												<li>
													<a class="list-item" href="<?php echo esc_url( get_permalink( $dli_azione->ID ) ); ?>">
														<div class="it-right-zone">
															<span class="text fw-semibold"><?php echo esc_html( get_the_title( $dli_azione->ID ) ); ?></span>
															<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
																<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-chevron-right' ); ?>"></use>
															</svg>
														</div>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<!-- FINE SEZIONE "LE SEZIONI DEL SITO" -->
		<?php
	}
}
?>
