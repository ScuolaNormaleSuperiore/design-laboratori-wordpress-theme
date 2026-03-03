<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

global $post;
if ( is_home() ) {
	$dli_steps = array();
} else {
	$dli_steps = DLI_ContentsManager::build_content_path( $post );
}
	$dli_index = 0;
?>
<section id="breadcrumb">
	<div class="container">
		<div class="row">
			<div class="col-12 ms-4 ">
				<nav class="breadcrumb-container" aria-label="Percorso di navigazione">
				<ol class="breadcrumb pb-0">
					<?php
					foreach ( $dli_steps as $dli_step ) {
						?>
					<li class="<?php echo esc_attr( $dli_step['class'] ); ?>">
						<a href="<?php echo esc_url( $dli_step['url'] ); ?>"><?php echo esc_attr( $dli_step['label'] ); ?></a>
						<?php
						if ( $dli_index < count( $dli_steps ) - 1 ) {
							?>
						<span class="separator">&gt;</span>
							<?php
						}
						?>
					</li>
						<?php
						++$dli_index;
					}
					?>
				</ol>
			</nav>
		</div>
		</div>
	</div>
</section>
