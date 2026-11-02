<?php
/**
 * Homepage section "Pannello tab": a set of tabs, each with a text and, optionally, a list of pages.
 *
 * The tabs are one global set written in Configurazione → Layout HP (group `home_tabs_items`, read by
 * `dli_get_home_tabs()`): every copy of the section in Sezioni HP shows the same set, with identifiers of its
 * own (`instance`, the position of the row). It is the Tabs component of Bootstrap Italia, with no script of
 * the theme: keyboard, focus and `aria-selected` are handled by the library. The first tab is the open one.
 * Without a tab to show nothing is printed. Title and subtitle come from Layout HP, in the language of the page:
 * the title is printed only when the row asks for it and the title is not empty; the subtitle, when it is not
 * empty. Without a visible title the section has an accessible name.
 *
 * @package Design_Laboratori_Italia
 */

$dli_args            = isset( $args ) && is_array( $args ) ? $args : array();
$dli_section_enabled = $dli_args['enabled'] ?? false;
$dli_show_title      = $dli_args['show_title'] ?? false;
$dli_instance        = max( 1, (int) ( $dli_args['instance'] ?? 1 ) );

if ( 'true' !== $dli_section_enabled ) {
	return;
}

$dli_tabs = dli_get_home_tabs();
if ( empty( $dli_tabs ) ) {
	return;
}

$dli_title       = (string) dli_get_configuration_field_by_lang( 'home_tabs_title', 'homepage' );
$dli_text        = (string) dli_get_configuration_field_by_lang( 'home_tabs_text', 'homepage' );
$dli_bg_class    = dli_get_hp_section_background_class( $dli_args, 'white' );
$dli_section_id  = 'blocco-tabs-' . $dli_instance;
$dli_title_id    = $dli_section_id . '-title';
$dli_title_shown = ( 'true' === $dli_show_title && '' !== $dli_title );
$dli_tabs_id     = 'tabs-' . $dli_instance;
$dli_icons       = get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg';
?>
<!-- INIZIO SEZIONE "PANNELLO TAB" -->
<section id="<?php echo esc_attr( $dli_section_id ); ?>" class="section tabs-section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-5" <?php echo $dli_title_shown ? 'aria-labelledby="' . esc_attr( $dli_title_id ) . '"' : 'aria-label="' . esc_attr__( 'Pannello tab', 'design_laboratori_italia' ) . '"'; ?>>
	<div class="section-content">
		<div class="container">
			<?php if ( $dli_title_shown ) : ?>
				<h2 id="<?php echo esc_attr( $dli_title_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $dli_text ) : ?>
				<p class="pb-3"><?php echo esc_html( $dli_text ); ?></p>
			<?php endif; ?>
			<ul class="nav nav-tabs" id="<?php echo esc_attr( $dli_tabs_id ); ?>" role="tablist">
				<?php foreach ( $dli_tabs as $dli_index => $dli_tab ) : ?>
					<?php
					$dli_is_first = ( 0 === $dli_index );
					$dli_number   = $dli_index + 1;
					$dli_tab_id   = $dli_tabs_id . '-tab-' . $dli_number;
					$dli_panel_id = $dli_tabs_id . '-panel-' . $dli_number;
					?>
					<li class="nav-item" role="presentation">
						<a
							class="nav-link<?php echo $dli_is_first ? ' active' : ''; ?>"
							id="<?php echo esc_attr( $dli_tab_id ); ?>"
							data-bs-toggle="tab"
							href="<?php echo esc_attr( '#' . $dli_panel_id ); ?>"
							role="tab"
							aria-controls="<?php echo esc_attr( $dli_panel_id ); ?>"
							aria-selected="<?php echo $dli_is_first ? 'true' : 'false'; ?>"
						><?php echo esc_html( $dli_tab['label'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content" id="<?php echo esc_attr( $dli_tabs_id . '-content' ); ?>">
				<?php foreach ( $dli_tabs as $dli_index => $dli_tab ) : ?>
					<?php
					$dli_is_first = ( 0 === $dli_index );
					$dli_number   = $dli_index + 1;
					?>
					<div class="tab-pane p-4 fade<?php echo $dli_is_first ? ' show active' : ''; ?>" id="<?php echo esc_attr( $dli_tabs_id . '-panel-' . $dli_number ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( $dli_tabs_id . '-tab-' . $dli_number ); ?>">
						<?php if ( '' !== $dli_tab['text'] ) : ?>
							<?php echo wp_kses_post( wpautop( $dli_tab['text'] ) ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $dli_tab['pages'] ) ) : ?>
							<div class="it-list-wrapper">
								<ul class="it-list">
									<?php foreach ( $dli_tab['pages'] as $dli_page ) : ?>
										<li>
											<a class="list-item" href="<?php echo esc_url( $dli_page['url'] ); ?>">
												<div class="it-right-zone">
													<span class="text fw-semibold"><?php echo esc_html( $dli_page['title'] ); ?></span>
													<svg class="icon icon-primary icon-sm" aria-hidden="true" focusable="false">
														<use href="<?php echo esc_url( $dli_icons . '#it-chevron-right' ); ?>"></use>
													</svg>
												</div>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
<!-- FINE SEZIONE "PANNELLO TAB" -->
