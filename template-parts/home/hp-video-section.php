<?php
/**
 * The HP Banners section.
 *
 * @package Design_Laboratori_Italia
 */

$enabled_par     = $args['enabled'] ?? '';
$id_par          = $args['id'] ?? '';
$show_title_par  = $args['show_title'] ?? '';
$show_title      = ( $show_title_par === 'true' ) ? true : false;
$section_enabled = ( $enabled_par === 'true' ) ? true : false;
// Get the video URL.
$video = dli_get_option( 'home_page_video_url', 'homepage' ) ?? null;

if ( $section_enabled && $video ) {

$page_label       = __( 'Privacy Policy', 'design_laboratori_italia' );
$current_language = dli_current_language( 'slug' );
$page_link        = dli_get_privacy_link( $current_language );
$msg_text         = sprintf( __( 'Accetta i cookie di Youtube per guardare il video. Puoi gestire le preferenze in <a target="_blank" href="%s" class="text-white">%s</a>.', 'design_laboratori_italia' ),
	$page_link, 
	$page_label
);

?>

	<div class="container-banner-home">

		<!-- Title -->
		<?php
			if ( $show_title ) {
		?>
			<h2 class="pb-4">
				<?php echo __( 'Video', 'design_laboratori_italia' );?>
			</h2>
		<?php
			}
		?>

		<!-- Body -->
		<section class="section section-muted p-5">
			<div class="row">
				<div class="mx-auto col-12 col-sm-10 col-md-8 col-lg-6">
					<script>
						const loadYouTubeVideo = function (videoUrl) {
							const videoEl = document.getElementById("vid1");
							const video = bootstrap.VideoPlayer.getOrCreateInstance(videoEl);
							video.setYouTubeVideo(videoUrl);
						}
					</script>
					<div class="acceptoverlayable">
						<div class="acceptoverlay acceptoverlay-primary fade show">
							<div class="acceptoverlay-inner">
								<div class="acceptoverlay-icon">
									<svg class="icon icon-xl">
										<use href="/bootstrap-italia/svg/sprites.svg#it-video"></use>
									</svg>
								</div>
								<p>
									<?php echo $msg_text; ?>
								</p>
								<div class="acceptoverlay-buttons bg-dark">
									<button type="button" class="btn btn-primary" data-bs-accept-from="youtube.com"
										onclick="loadYouTubeVideo('<?php echo esc_url( $video ); ?>')">
										<?php echo __( 'Accept', 'design_laboratori_italia' ); ?>
									</button>
									<div class="form-check">
										<input id="chk-remember" type="checkbox" data-bs-accept-remember>
										<label for="chk-remember">
											<?php echo __( 'Ricorda per tutti i video', 'design_laboratori_italia' ); ?>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div>
							<video controls data-bs-video id="vid1" class="video-js" width="640" height="264">
							</video>
						</div>
					</div>
				</div>
			</div>
		</section>

	</div>

<?php
}
?>
