<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = is_array( $items ) ? count( $items ) : 0;
	$phone       = $items[ 'phone' ];
	$email       = $items[ 'email' ];
	$website     = $items[ 'website' ];
?>
<section id="<?php echo 'sezione-' . $section_id; ?>">
	<div class="it-list-wrapper">
		<ul class="it-list">
		<?php
			if ( $phone ) {
		?>
		<li>
			<div class="list-item">
				<div class="it-rounded-icon">
					<svg class="icon">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-telephone'; ?>"></use>
					</svg>
				</div>
				<div class="it-right-zone">
					<span class="text">050 509662</span>
				</div>
			</div>
		</li>
		<?php
			}
			if ( $email ) {
		?>
		<li>
			<a href="#" class="list-item">
			<div class="it-rounded-icon">
			<svg class="icon">
				<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-mail'; ?>"></use>
			</svg>
			</div>
			<div class="it-right-zone">
				<span class="text">mail@sns.it</span>
			</div>
			</a>
		</li>
		<?php
			}
			if ( $website ) {
		?>
		<li>
			<a class="list-item" href="#">
			<div class="it-rounded-icon">
			<svg class="icon">
				<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-link'; ?>"></use>
			</svg>
			</div>
			<div class="it-right-zone">
				<span class="text">www.sns.it</span>
			</div>
			</a>
		</li>
		<?php
			}
		?>
		</ul>
	</div>
</section>
