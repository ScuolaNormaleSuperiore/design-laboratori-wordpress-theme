<?php
/**
 * Template part.
 *
 * @package Design_Laboratori_WordPress_Theme
 */

$dli_cookie_pol_url = dli_get_translated_page_url_by_slug( SLUG_PRIVACY_IT );
$dli_args           = isset( $args ) && is_array( $args ) ? $args : array();
$dli_video          = $dli_args['video'] ?? '';
$dli_video_text     = $dli_args['video_text'] ?? '';
$dli_video_title    = $dli_args['video_title'] ?? '';
$dli_video_track    = $dli_args['video_track'] ?? '';
// Without an instance (the detail pages of events, patents and spin-offs, one video each) the identifiers are the
// ones this component has always had. With one (a row of Sezioni HP, the same video section can be repeated) every
// identifier carries it, so that two players in a page share none.
$dli_instance   = isset( $dli_args['instance'] ) && '' !== (string) $dli_args['instance'] ? absint( $dli_args['instance'] ) : 0;
$dli_player_id  = $dli_instance ? 'vid-' . $dli_instance : 'vid1';
$dli_remember   = $dli_instance ? 'chk-remember-' . $dli_instance : 'chk-remember';
$dli_trans_head = $dli_instance ? 'transcription-head-' . $dli_instance : 'transcription-head9';
$dli_trans_body = $dli_instance ? 'transcription-' . $dli_instance : 'transcription9';
?>

	<script>
		// One function for every player of the page: a second copy of this script leaves it as it is.
		window.dliLoadYouTubeVideo = window.dliLoadYouTubeVideo || function(playerId, videoUrl) {
			const videoEl = document.getElementById(playerId);
			const video = bootstrap.VideoPlayer.getOrCreateInstance(videoEl);
			video.setYouTubeVideo(videoUrl);
		};
	</script>
	<div class="acceptoverlayable">
		<div class="acceptoverlay acceptoverlay-primary fade show">
			<div class="acceptoverlay-inner">
				<div class="acceptoverlay-icon">
					<svg class="icon icon-xl"><use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-video' ); ?>"></use></svg>
				</div>
					<p>
					<?php echo esc_html__( 'Accetta i cookie di YouTube per vedere il video. Puoi gestire le preferenze nella ', 'design_laboratori_italia' ); ?>
					<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dli_cookie_pol_url ); ?>" class="text-white"><?php echo esc_html__( 'cookie policy', 'design_laboratori_italia' ); ?></a>.
					</p>
				<div class="acceptoverlay-buttons bg-dark">
					<button type="button" class="btn btn-primary" data-bs-accept-from="youtube.com"
						onclick="dliLoadYouTubeVideo('<?php echo esc_js( $dli_player_id ); ?>', '<?php echo esc_js( esc_url_raw( $dli_video ) ); ?>')">
						<?php echo esc_html__( 'Accetta', 'design_laboratori_italia' ); ?>
					</button>
					<div class="form-check">
						<input id="<?php echo esc_attr( $dli_remember ); ?>" type="checkbox" data-bs-accept-remember>
						<label for="<?php echo esc_attr( $dli_remember ); ?>"><?php echo esc_html__( 'Ricorda per tutti i video', 'design_laboratori_italia' ); ?></label>
					</div>
				</div>
			</div>
		</div>
		<div>
			<video controls data-bs-video id="<?php echo esc_attr( $dli_player_id ); ?>"
				title="
				<?php
					/* translators: %s: video title. */
					echo esc_attr( sprintf( __( '%s Video', 'design_laboratori_italia' ), $dli_video_title ) );
				?>
				"
				aria-label="
				<?php
					/* translators: %s: video title. */
					echo esc_attr( sprintf( __( '%s Video', 'design_laboratori_italia' ), $dli_video_title ) );
				?>
				"
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
					<h2 class="accordion-header " id="<?php echo esc_attr( $dli_trans_head ); ?>">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $dli_trans_body ); ?>" aria-expanded="true" aria-controls="<?php echo esc_attr( $dli_trans_body ); ?>">
					<?php echo esc_html__( 'Trascrizione', 'design_laboratori_italia' ); ?>
						</button>
					</h2>
					<div id="<?php echo esc_attr( $dli_trans_body ); ?>" class="accordion-collapse collapse" role="region" aria-labelledby="<?php echo esc_attr( $dli_trans_head ); ?>">
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
