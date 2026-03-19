<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_cookie_pol_url = dli_get_translated_page_url_by_slug( SLUG_PRIVACY_IT );
$dli_args           = is_array( $args ) ? $args : array();
$dli_video          = $dli_args['video'] ?? '';
$dli_video_text     = $dli_args['video_text'] ?? '';
$dli_video_title    = $dli_args['video_title'] ?? '';
$dli_video_track    = $dli_args['video_track'] ?? '';
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
					<svg class="icon icon-xl"><use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-video' ); ?>"></use></svg>
				</div>
					<p>
					<?php echo esc_html__( 'Accetta i cookie di YouTube per vedere il video. Puoi gestire le preferenze nella ', 'design_laboratori_italia' ); ?>
					<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_cookie_pol_url ); ?>" class="text-white">cookie policy</a>.
					</p>
				<div class="acceptoverlay-buttons bg-dark">
					<button type="button" class="btn btn-primary" data-bs-accept-from="youtube.com"
						onclick="loadYouTubeVideo('<?php echo esc_js( esc_url_raw( $dli_video ) ); ?>')">
						<?php echo esc_html__( 'Accetta', 'design_laboratori_italia' ); ?>
					</button>
					<div class="form-check">
						<input id="chk-remember" type="checkbox" data-bs-accept-remember>
						<label for="chk-remember"><?php echo esc_html__( 'Ricorda per tutti i video', 'design_laboratori_italia' ); ?></label>
					</div>
				</div>
			</div>
		</div>
		<div>
			<video controls data-bs-video id="vid1"
				title="<?php echo esc_attr( $dli_video_title . ' Video' ); ?>"
				aria-label="<?php echo esc_attr( $dli_video_title . ' Video' ); ?>"
				class="video-js"
				width="500"
				height="281">
				<?php if ( $dli_video_track ) : ?>
				<track kind="captions" src="<?php echo esc_url( $dli_video_track ); ?>" srclang="it" label="Italiano" default>
				<?php endif; ?>
			</video>
			<?php
			if ( $dli_video_text ) {
				?>
			<div class="vjs-transcription accordion">
				<div class="accordion-item">
					<h2 class="accordion-header " id="transcription-head9">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transcription9" aria-expanded="true" aria-controls="transcription">
					<?php echo esc_html__( 'Trascrizione', 'design_laboratori_italia' ); ?>
						</button>
					</h2>
					<div id="transcription9" class="accordion-collapse collapse" role="region" aria-labelledby="transcription-head9">
						<div class="accordion-body">
						<?php echo esc_html( $dli_video_text ); ?>
						</div>
					</div>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	</div>
