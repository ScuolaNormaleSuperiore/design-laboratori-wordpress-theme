<?php
	$cookie_pol_url = dli_get_translated_page_url_by_slug( SLUG_COOKIES_POLICY_IT);
	$video          = $args['video'];
	$video_text     = $args['video_text'];
	$video_title    = $args['video_title'];
?>

	<script>
		const loadYouTubeVideo = function(videoUrl) {
			const videoEl = document.getElementById("vid1");
			const video = bootstrap.VideoPlayer.getOrCreateInstance(videoEl);
			video.setYouTubeVideo(videoUrl);
		}
	</script>
	<div class="acceptoverlayable">
		<div class="acceptoverlay acceptoverlay-primary fade show">
			<div class="acceptoverlay-inner">
				<div class="acceptoverlay-icon">
					<svg class="icon icon-xl"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-video"></use></svg>
				</div>
					<p>
					<?php echo __( 'Accetta i cookie di YouTube per vedere il video. Puoi gestire le preferenze nella ', 'design_laboratori_italia' ); ?>
					<a target="_blank" href="<?php echo esc_url( $cookie_pol_url ); ?>" class="text-white">cookie policy</a>.
					</p>
				<div class="acceptoverlay-buttons bg-dark">
					<button type="button" class="btn btn-primary" data-bs-accept-from="youtube.com"
						onclick="loadYouTubeVideo('<?php echo esc_url( $video ); ?>')">
						<?php echo __( 'Accept', 'design_laboratori_italia' ); ?>
					</button>
					<div class="form-check">
						<input id="chk-remember" type="checkbox" data-bs-accept-remember>
						<label for="chk-remember"><?php echo __( 'Ricorda per tutti i video', 'design_laboratori_italia' ); ?></label>
					</div>
				</div>
			</div>
		</div>
		<div>
			<video controls data-bs-video id="vid1"
				title="<?php echo $video_title; ?> Video'"
				aria-label="<?php echo $video_title; ?> Video'"
				class="video-js"
				width="500"
				height="281">
			</video>
			<?php
				if ( $video_text ) {
			?>
			<div class="vjs-transcription accordion">
				<div class="accordion-item">
					<h2 class="accordion-header " id="transcription-head9">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transcription9" aria-expanded="true" aria-controls="transcription">
						<?php echo __( 'Trascrizione', 'design_laboratori_italia' ); ?>
						</button>
					</h2>
					<div id="transcription9" class="accordion-collapse collapse" role="region" aria-labelledby="transcription-head9">
						<div class="accordion-body">
							<?php echo esc_html( $video_text ); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
				}
			?>
		</div>
	</div>
