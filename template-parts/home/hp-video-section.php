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
// Each row of Sezioni HP has its own address; an empty or not web address means no section. The old global option
// `home_page_video_url` is not read any more.
$dli_video_url = dli_sanitize_hp_video_url( $args['video_url'] ?? '' );
$dli_instance  = isset( $args['instance'] ) ? absint( $args['instance'] ) : 1;
$dli_block_id  = 'blocco-video-' . $dli_instance;
$dli_args      = isset( $args ) && is_array( $args ) ? $args : array();
$dli_bg_class  = dli_get_hp_section_background_class( $dli_args, 'muted' );
// The title is the one of the row in the language of the page, and is printed only when the row asks to show it: a
// title missing in this language, or not to be shown, leaves the section without a title and without a name.
$dli_title      = dli_get_hp_row_title( $dli_args, dli_current_language() === DLI_IT_SLUG );
$dli_show_title = $dli_show_title && '' !== $dli_title;

if ( $dli_section_enabled && $dli_video_url ) {
	?>
	<!-- BLOCCO VIDEO: stesso pattern standard delle altre sezioni elenco della
		home (section+id+aria-labelledby > section-content > container > h2),
		già validato nel prototipo statico (sf-index.html, #blocco-video).
		Il markup del player (script, accept-overlay cookie YouTube, <video>,
		accordion di trascrizione se presente) è quello condiviso di
		template-parts/common/sezione-video.php, già usato dalle schede
		Brevetto/Spin-off/Evento — nessuna ragione per duplicarlo qui. -->
	<section id="<?php echo esc_attr( $dli_block_id ); ?>" class="section<?php echo esc_attr( $dli_bg_class ); ?> pt-3 pb-5"<?php echo ( $dli_show_title ) ? ' aria-labelledby="' . esc_attr( $dli_block_id ) . '-title"' : ''; ?>>
		<div class="section-content">
			<div class="container">
				<?php if ( $dli_show_title ) : ?>
					<h2 id="<?php echo esc_attr( $dli_block_id ); ?>-title" class="h3 pb-2"><?php echo esc_html( $dli_title ); ?></h2>
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
								'instance'    => $dli_instance,
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
