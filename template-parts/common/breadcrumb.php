<?php
	global $post;
	if ( is_home() ) {
		$steps = array();
	} else {
		$steps = DLI_ContentsManager::build_content_path( $post );
	}
	$index = 0;
?>
<section id="breadcrumb">
	<div class="container">
		<div class="row">
			<div class="col-12 ms-4 ">
				<nav class="breadcrumb-container" aria-label="Percorso di navigazione">
				<ol class="breadcrumb pb-0">
					<?php
						foreach( $steps as $step ){
					?>
					<li class="<?php echo esc_attr( $step['class'] ); ?>">
						<a href="<?php echo esc_url( $step['url'] ); ?>"><?php echo esc_attr( $step['label'] ); ?></a>
						<?php
							if ( $index < count( $steps) -1 ) {
						?>
						<span class="separator">&gt;</span>
						<?php
							}
						?>
					</li>
					<?php
						$index++;
						}
					?>
				</ol>
			</nav>
		</div>
		</div>
	</div>
</section>
