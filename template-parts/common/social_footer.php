

<?php
	$show_socials = dli_get_option( 'show_socials', 'socials' );
	if( $show_socials === 'true' ) {
?>
	<h4><a href="#" title="<?php echo __("Seguici su", 'design_laboratori_italia'); ?>"><?php echo __("Seguici su", 'design_laboratori_italia'); ?></a></h4>
	<ul class="list-inline text-left social">
		<?php
		if ( $facebook = dli_get_option( 'facebook', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $facebook; ?>" title="Facebook" aria-label="Facebook" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Facebook">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $youtube = dli_get_option( 'youtube', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $youtube; ?>" title="Youtube" aria-label="Youtube" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Youtube">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-youtube'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $instagram = dli_get_option( 'instagram', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $instagram; ?>" title="Instagram" aria-label="Instagram" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Instagram">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-instagram'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $twitter = dli_get_option( 'twitter', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $twitter; ?>" title="Twitter" aria-label="Twitter" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Twitter">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $linkedin = dli_get_option( 'linkedin', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $linkedin; ?>" title="Linkedin" aria-label="Linkedin" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Linkedin">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $github = dli_get_option( 'github', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $github; ?>" title="GitHub" aria-label="GitHub" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="GitHub">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-github'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $pinterest = dli_get_option( 'pinterest', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $pinterest; ?>" title="Pinterest" aria-label="Pinterest" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Pinterest">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-pinterest'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $iris = dli_get_option( 'iris', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $iris; ?>" title="Iris" aria-label="Iris" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Iris">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-pa'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $alumni = dli_get_option( 'alumni', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $alumni; ?>" title="Alumni" aria-label="Alumni" target="_blank">
				<svg class="icon icon-sm icon-white align-top" role="img" aria-labelledby="Alumni">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-open-source'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		?>
	</ul>
	<?php
	}
	?>