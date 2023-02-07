<?php
	$show_socials = dli_get_option( 'show_socials', 'socials' );
	if( $show_socials === 'true' ) {
?>
	<span><?php echo __("Seguici su", 'design_laboratori_italia'); ?></span>
	<ul>
		<?php
		if ( $facebook = dli_get_option( 'facebook', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo $facebook; ?>" aria-label="Facebook" target="_blank">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $youtube = dli_get_option( 'youtube', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo $youtube; ?>" aria-label="Youtube" target="_blank">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-youtube'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $instagram = dli_get_option( 'instagram', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo $instagram; ?>" aria-label="Instagram" target="_blank">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-instagram'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $twitter = dli_get_option( 'twitter', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo $twitter; ?>" aria-label="Twitter" target="_blank">
				<svg class="icon">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'; ?>"></use>
				</svg>
			</a>
			</li>
		<?php
		}
		if ( $linkedin = dli_get_option( 'linkedin', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo $linkedin; ?>" aria-label="Linkedin" target="_blank">
				<svg class="icon">
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