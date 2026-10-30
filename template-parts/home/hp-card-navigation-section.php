<?php
/**
 * Homepage section "Card di navigazione": the pages promoted to the home, as navigation cards.
 *
 * Lists up to `max_items` (the number chosen in the row of Sezioni HP, `DLI_HP_CARD_NAVIGATION_DEFAULT_ITEMS` when not
 * chosen) published pages of the current language with the field "Promuovi in Home" on, in the order of their
 * field "Ordine" and then by title (`dli_get_promoted_page_cards()`), each one as the card of `template-parts/common/card-nav.php`.
 * Only the pages of the first level are listed, unless the row chooses `max_level` (2, 3, or 0 for all the levels).
 * Nothing is printed when no page is promoted.
 * Title and subtitle come from Configurazione → Layout HP, in the language of the page: the title is printed
 * only when the row asks for it and the title is not empty; the subtitle, when it is not empty. Without a
 * visible title the section has an accessible name. The identifiers use the position of the row (`instance`),
 * so the section can be repeated.
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

$dli_cards = dli_get_promoted_page_cards(
	dli_normalize_hp_max_items( $dli_args['max_items'] ?? null, DLI_HP_CARD_NAVIGATION_DEFAULT_ITEMS ),
	dli_normalize_hp_page_level( $dli_args['max_level'] ?? null )
);
if ( empty( $dli_cards ) ) {
	return;
}

$dli_title        = (string) dli_get_configuration_field_by_lang( 'home_card_navigation_title', 'homepage' );
$dli_text         = (string) dli_get_configuration_field_by_lang( 'home_card_navigation_text', 'homepage' );
$dli_bg_class     = dli_get_hp_section_background_class( $dli_args, 'muted' );
$dli_section_id   = 'blocco-card-navigation-' . $dli_instance;
$dli_title_id     = $dli_section_id . '-title';
$dli_title_shown  = ( 'true' === $dli_show_title && '' !== $dli_title );
$dli_column_class = dli_get_hp_navigation_column_class( count( $dli_cards ) );
?>
<!-- INIZIO SEZIONE "CARD DI NAVIGAZIONE" -->
<section id="<?php echo esc_attr( $dli_section_id ); ?>" class="section<?php echo esc_attr( $dli_bg_class ); ?> pt-5 pb-4" <?php echo $dli_title_shown ? 'aria-labelledby="' . esc_attr( $dli_title_id ) . '"' : 'aria-label="' . esc_attr__( 'Card di navigazione', 'design_laboratori_italia' ) . '"'; ?>>
	<div class="section-content">
		<div class="container">
			<?php if ( $dli_title_shown ) : ?>
				<h2 id="<?php echo esc_attr( $dli_title_id ); ?>" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $dli_text ) : ?>
				<p class="pb-3"><?php echo esc_html( $dli_text ); ?></p>
			<?php endif; ?>
			<ul class="row list-unstyled mb-0 justify-content-center">
				<?php foreach ( $dli_cards as $dli_card ) : ?>
					<li class="<?php echo esc_attr( $dli_column_class ); ?> mb-4">
						<?php get_template_part( 'template-parts/common/card-nav', null, array_merge( $dli_card, array( 'heading' => 'h3' ) ) ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
<!-- FINE SEZIONE "CARD DI NAVIGAZIONE" -->
