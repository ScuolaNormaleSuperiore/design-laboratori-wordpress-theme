

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
			<a class="p-2 text-white" href="<?php echo $facebook; ?>" aria-label="Facebook" target="_blank">
				<svg class="icon icon-sm icon-white align-top">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $youtube = dli_get_option( 'youtube', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $youtube; ?>" aria-label="Youtube" target="_blank">
				<svg class="icon icon-sm icon-white align-top">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-youtube'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $instagram = dli_get_option( 'instagram', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $instagram; ?>" aria-label="Instagram" target="_blank">
				<svg class="icon icon-sm icon-white align-top">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-instagram'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $twitter = dli_get_option( 'twitter', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $twitter; ?>" aria-label="Twitter" target="_blank">
				<svg class="icon icon-sm icon-white align-top">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $linkedin = dli_get_option( 'linkedin', 'socials' ) ) {
		?>
		<li class="list-inline-item">
			<a class="p-2 text-white" href="<?php echo $linkedin; ?>" aria-label="Linkedin" target="_blank">
				<svg class="icon icon-sm icon-white align-top">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'; ?>"></use>
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