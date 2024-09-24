<?php
global $post;
$current_url = get_permalink();
?>

<div class="dropdown d-inline">
	<button class="btn btn-dropdown dropdown-toggle" type="button" id="shareActions" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<svg class="icon" aria-hidden="true" focusable="false">
			<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-share' ?>"></use>
		</svg>
		<small>
			<?php echo __( 'Condividi', 'design_laboratori_italia' ); ?>
		</small>
	</button>
	<div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
		<div class="link-list-wrapper">
			<ul class="link-list">
				<li>
					<a class="list-item" href="https://facebook.com/sharer/sharer.php?u=<?php echo esc_url( $current_url ) ; ?>" target="_blank" rel="noopener" aria-label="Share on Facebook">
						<svg class="icon" aria-hidden="true" focusable="false">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'?>"></use>
						</svg>
						<span>Facebook</span>
					</a>
				</li>
				<li>
					<a class="list-item" href="https://twitter.com/share?url=<?php echo esc_url( $current_url ) ; ?>">
						<svg class="icon" aria-hidden="true" focusable="false">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'?>"></use>
						</svg>
						<br/>
						<span>Twitter</span>
					</a>
				</li>
				<li>
					<a class="list-item" href="http://www.linkedin.com/shareArticle?mini=true&amp;url#=">
						<svg class="icon" aria-hidden="true" focusable="false">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-linkedin'?>"></use>
						</svg>
						<span>Linkedin</span>
					</a>
				</li>
				<li>
					<a class="list-item" href="whatsapp://send?text==url" target="_blank" rel="noopener" aria-label="Share on Whatsapp">
						<svg class="icon" aria-hidden="true" focusable="false">
							<use xlink:href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-whatsapp'?>"></use>
						</svg>
						<span>Whatsapp</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
