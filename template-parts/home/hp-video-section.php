<?php
/**
 * Homepage video section.
 *
 * @package Design_Laboratori_Italia
 */

$dli_enabled_par     = $args['enabled'] ?? '';
$dli_show_title_par  = $args['show_title'] ?? '';
$dli_show_title      = ( 'true' === $dli_show_title_par );
$dli_section_enabled = ( 'true' === $dli_enabled_par );
$dli_video_url       = dli_get_option( 'home_page_video_url', 'homepage' );

if ( $dli_section_enabled && $dli_video_url ) {
	$dli_page_label       = __( 'Privacy Policy', 'design_laboratori_italia' );
	$dli_current_language = dli_current_language( 'slug' );
	$dli_page_link        = dli_get_privacy_link( $dli_current_language );

	$dli_message_text = sprintf(
		/* translators: 1: Privacy page URL, 2: Privacy page label. */
		__(
			'Accetta i cookie di Youtube per guardare il video. Puoi gestire le preferenze in <a target="_blank" href="%1$s" class="text-white">%2$s</a>.',
			'design_laboratori_italia'
		),
		esc_url( $dli_page_link ),
		esc_html( $dli_page_label )
	);
	$dli_allowed_html = array(
		'a' => array(
			'target' => true,
			'href'   => true,
			'class'  => true,
		),
	);
	?>

	<div class="container-banner-home">
		<?php if ( $dli_show_title ) : ?>
			<h2 class="pb-4">
				<?php esc_html_e( 'Video', 'design_laboratori_italia' ); ?>
			</h2>
		<?php endif; ?>

		<section class="section section-muted p-5">
			<div class="row">
				<div class="mx-auto col-12 col-sm-10 col-md-8 col-lg-6">
					<script>
						const loadYouTubeVideo = function (videoUrl) {
							const videoEl = document.getElementById('vid1');
							const video = bootstrap.VideoPlayer.getOrCreateInstance(videoEl);
							video.setYouTubeVideo(videoUrl);
						};
					</script>
					<div class="acceptoverlayable">
						<div class="acceptoverlay acceptoverlay-primary fade show">
							<div class="acceptoverlay-inner">
								<div class="acceptoverlay-icon">
									<svg class="icon icon-xl">
										<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-video' ); ?>"></use>
									</svg>
								</div>
								<p>
									<?php echo wp_kses( $dli_message_text, $dli_allowed_html ); ?>
								</p>
								<div class="acceptoverlay-buttons bg-dark">
									<button type="button" class="btn btn-primary" data-bs-accept-from="youtube.com"
										onclick="loadYouTubeVideo('<?php echo esc_url( $dli_video_url ); ?>')">
										<?php esc_html_e( 'Accept', 'design_laboratori_italia' ); ?>
									</button>
									<div class="form-check">
										<input id="chk-remember" type="checkbox" data-bs-accept-remember>
										<label for="chk-remember">
											<?php esc_html_e( 'Ricorda per tutti i video', 'design_laboratori_italia' ); ?>
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
