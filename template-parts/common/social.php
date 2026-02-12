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
			<a href="<?php echo esc_url( $facebook ); ?>" aria-label="Facebook" target="_blank">
				<svg class="icon" role="img" aria-labelledby="Facebook" aria-label="Facebook">
					<title>Facebook</title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'; ?>"></use>
				</svg>
			</a>
		</li>
		<?php
		}
		if ( $youtube = dli_get_option( 'youtube', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo esc_url( $youtube ); ?>" aria-label="Youtube" target="_blank">
				<svg class="icon" role="img" aria-labelledby="Youtube" aria-label="Youtube">
					<title>Youtube</title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-youtube'; ?>"></use>
				</svg>
			</a>
		</li>
		<?php
		}
		if ( $instagram = dli_get_option( 'instagram', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo esc_url( $instagram ); ?>" aria-label="Instagram" target="_blank">
				<svg class="icon" role="img" aria-labelledby="Instagram" aria-label="Instagram">
					<title>Instagram</title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-instagram'; ?>"></use>
				</svg>
			</a>
		</li>
		<?php
		}
		if ( $twitter = dli_get_option( 'twitter', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo esc_url( $twitter ); ?>" aria-label="Twitter" target="_blank">
				<svg class="icon" role="img" aria-labelledby="Twitter" aria-label="Twitter">
					<title>Twitter</title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'; ?>"></use>
				</svg>
			</a>
		</li>
		<?php
		}
		if ( $linkedin = dli_get_option( 'linkedin', 'socials' ) ) {
		?>
		<li>
			<a href="<?php echo esc_url( $linkedin ); ?>" aria-label="Linkedin" target="_blank">
				<svg class="icon" role="img" aria-labelledby="Linkedin" aria-label="Linkedin">
					<title>Linkedin</title>
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'; ?>"></use>
				</svg>
			</a>
		</li>
		<?php
		}
		if ( $github = dli_get_option( 'github', 'socials' ) ) {
		?>
			<li>
				<a href="<?php echo esc_url( $github ); ?>" aria-label="GitHub" target="_blank">
					<svg class="icon" role="img" aria-labelledby="GitHub" aria-label="GitHub">
						<title>GitHub</title>
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-github'; ?>"></use>
					</svg>
				</a>
			</li>
		<?php
		}
		if ( $pinterest = dli_get_option( 'pinterest', 'socials' ) ) {
		?>
			<li>
				<a href="<?php echo esc_url( $pinterest ); ?>" aria-label="Pinterest" target="_blank">
					<svg class="icon" role="img" aria-labelledby="Pinterest" aria-label="Pinterest">
						<title>Pinterest</title>
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-pinterest'; ?>"></use>
					</svg>
				</a>
			</li>
		<?php
		}
		if ( $mastodon = dli_get_option( 'mastodon', 'socials' ) ) {
		?>
			<li>
				<a href="<?php echo esc_url( $mastodon ); ?>" aria-label="Mastodon" target="_blank">
					<svg class="icon" role="img" aria-labelledby="Mastodon" aria-label="Mastodon">
						<title>Mastodon</title>
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mastodon'; ?>"></use>
					</svg>
				</a>
			</li>
		<?php
		}
		if ( $iris = dli_get_option( 'iris', 'socials' ) ) {
		?>
			<li>
				<a href="<?php echo esc_url( $iris ); ?>" aria-label="Iris" target="_blank">
					<svg class="icon" role="img" aria-labelledby="Iris" aria-label="Iris">
						<title>Iris</title>
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-pa'; ?>"></use>
					</svg>
				</a>
			</li>
		<?php
		}
		if ( $alumni = dli_get_option( 'alumni', 'socials' ) ) {
		?>
			<li>
				<a href="<?php echo esc_url( $alumni ); ?>" aria-label="Alumni" target="_blank">
					<svg class="icon" role="img" aria-labelledby="Alumni" aria-label="Alumni">
						<title>Alumni</title>
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
