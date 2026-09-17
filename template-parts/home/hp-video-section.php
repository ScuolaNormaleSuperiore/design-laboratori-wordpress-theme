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
	?>
	<!-- BLOCCO VIDEO: stesso pattern standard delle altre sezioni elenco della
	     home (section+id+aria-labelledby > section-content > container > h2),
	     già validato nel prototipo statico (sf-index.html, #blocco-video).
	     Il markup del player (script, accept-overlay cookie YouTube, <video>,
	     accordion di trascrizione se presente) è quello condiviso di
	     template-parts/common/sezione-video.php, già usato dalle schede
	     Brevetto/Spin-off/Evento — nessuna ragione per duplicarlo qui. -->
	<section id="blocco-video" class="section section-muted pt-3 pb-5" <?php echo ( $dli_show_title ) ? 'aria-labelledby="blocco-video-title"' : 'aria-label="' . esc_attr__( 'Video', 'design_laboratori_italia' ) . '"'; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( $dli_show_title ) : ?>
					<h2 id="blocco-video-title" class="h3 pb-2"><?php esc_html_e( 'Video', 'design_laboratori_italia' ); ?></h2>
				<?php endif; ?>
				<div class="row justify-content-center">
					<div class="col-12 col-lg-8">
						<?php
						get_template_part(
							'template-parts/common/sezione-video',
							null,
							array(
								'video'       => $dli_video_url,
								'video_text'  => null,
								'video_title' => get_bloginfo( 'name' ),
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}
