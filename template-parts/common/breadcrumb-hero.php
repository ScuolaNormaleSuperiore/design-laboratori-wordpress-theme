<?php
/**
 * Template part: breadcrumb integrato nell'hero a due colonne (v3).
 *
 * Variante di common/breadcrumb.php pensata per stare DENTRO al contenitore
 * hero (colonna sinistra, sopra al titolo), non come sezione a sé stante
 * sopra l'hero. Stessa fonte dati (DLI_ContentsManager::build_content_path),
 * markup adattato al pattern hero+breadcrumb standard del porting Bootstrap
 * Italia 3 (testo bianco, nessun wrapper <section>/.container proprio: il
 * chiamante fornisce già la colonna).
 *
 * @package Design_Laboratori_Italia
 */

global $post;
if ( is_home() ) {
	$dli_steps = array();
} else {
	$dli_steps = DLI_ContentsManager::build_content_path( $post );
}
$dli_index = 0;
?>
<nav class="breadcrumb-container" aria-label="<?php echo esc_attr__( 'Percorso di navigazione', 'design_laboratori_italia' ); ?>">
	<ol class="breadcrumb mb-0">
		<?php
		foreach ( $dli_steps as $dli_step ) :
			$dli_is_active = false !== strpos( (string) $dli_step['class'], 'active' );
			?>
			<li class="<?php echo esc_attr( $dli_step['class'] ); ?>"<?php echo $dli_is_active ? ' aria-current="page"' : ''; ?>>
				<a class="text-white" href="<?php echo esc_url( $dli_step['url'] ); ?>"><?php echo esc_html( $dli_step['label'] ); ?></a>
				<?php if ( $dli_index < count( $dli_steps ) - 1 ) : ?>
					<span class="separator text-white" aria-hidden="true">&gt;</span>
				<?php endif; ?>
			</li>
			<?php ++$dli_index; ?>
		<?php endforeach; ?>
	</ol>
</nav>
