<?php
/**
 * Template part: bottoni di cambio visualizzazione (Schede / Tabella) per la
 * pagina Persone. Condiviso da template-parts/persone/filters.php (vista
 * Schede) e template-parts/persone/view-tabella.php (vista Tabella), così i
 * due bottoni restano identici indipendentemente da quale vista è attiva.
 *
 * @package Design_Laboratori_Italia
 *
 * @var array $args {
 *     @type string $active      Vista attiva: 'chip'|'tabella'.
 *     @type string $chip_url    URL per passare alla vista Schede, con gli altri filtri già preservati.
 *     @type string $tabella_url URL per passare alla vista Tabella, con gli altri filtri già preservati.
 * }
 */

$dli_vt_active      = isset( $args['active'] ) ? $args['active'] : 'chip';
$dli_vt_chip_url    = isset( $args['chip_url'] ) ? $args['chip_url'] : '';
$dli_vt_tabella_url = isset( $args['tabella_url'] ) ? $args['tabella_url'] : '';

if ( ! $dli_vt_chip_url || ! $dli_vt_tabella_url ) {
	return;
}
?>
<div class="btn-group" role="group" aria-label="<?php echo esc_attr__( 'Modalità di visualizzazione dell\'elenco', 'design_laboratori_italia' ); ?>">
	<a
		href="<?php echo esc_url( $dli_vt_chip_url ); ?>"
		class="btn btn-<?php echo ( 'chip' === $dli_vt_active ) ? 'primary' : 'outline-primary'; ?> btn-sm"
		<?php echo ( 'chip' === $dli_vt_active ) ? ' aria-current="page"' : ''; ?>
	>
		<svg class="icon icon-sm" aria-hidden="true" focusable="false">
			<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-card' ); ?>"></use>
		</svg>
		<span><?php echo esc_html__( 'Schede', 'design_laboratori_italia' ); ?></span>
	</a>
	<a
		href="<?php echo esc_url( $dli_vt_tabella_url ); ?>"
		class="btn btn-<?php echo ( 'tabella' === $dli_vt_active ) ? 'primary' : 'outline-primary'; ?> btn-sm"
		<?php echo ( 'tabella' === $dli_vt_active ) ? ' aria-current="page"' : ''; ?>
	>
		<svg class="icon icon-sm" aria-hidden="true" focusable="false">
			<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-list' ); ?>"></use>
		</svg>
		<span><?php echo esc_html__( 'Tabella', 'design_laboratori_italia' ); ?></span>
	</a>
</div>
