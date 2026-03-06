<?php
/* Template Name: Mappa del sito
 *
 * @package Design_Laboratori_Italia
 */
get_header();

// Build the PAGE TREE.

$lng_slug  = dli_current_language( 'slug' );

try {
	$pt = dli_get_site_tree();
} catch ( Exception $e ) {
	$pt  = array();
	$msg = 'Caught exception: ' . $e->getMessage() . '\n';
	error_log( $msg );
}

$homepage_node = array();
if ( isset( $pt[ DLI_HOMEPAGE_SLUG ] ) && is_array( $pt[ DLI_HOMEPAGE_SLUG ] ) ) {
	$homepage_node = $pt[ DLI_HOMEPAGE_SLUG ];
}

if ( ! function_exists( 'dli_render_sitemap_node' ) ) {
	/**
	 * Render sitemap node recursively with escaped output.
	 *
	 * @param array $node Sitemap node.
	 * @return void
	 */
	function dli_render_sitemap_node( $node ) {
		if ( ! is_array( $node ) ) {
			return;
		}

		$name     = isset( $node['name'] ) ? esc_html( (string) $node['name'] ) : '';
		$link     = isset( $node['link'] ) ? esc_url( (string) $node['link'] ) : '';
		$external = ! empty( $node['external'] );

		echo '<li>';
		if ( '' !== $link ) {
			echo '<a class="mappasitolink"';
			if ( $external ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			echo ' href="' . $link . '">' . $name . '</a>';
		} else {
			echo '<span class="mappasitolink">' . $name . '</span>';
		}

		if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
			echo '<ul>';
			foreach ( $node['children'] as $child_node ) {
				dli_render_sitemap_node( $child_node );
			}
			echo '</ul>';
		}

		echo '</li>';
	}
}
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER MAPPA DEL SITO -->
	<?php get_template_part( 'template-parts/hero/mappasito' ); ?>

	<!-- MAPPA DEL SITO -->
	<div class="container my-4">
		<div class="row variable-gutters d-flex justify-content-center">
				<div class="col-lg-8 pt84">
					<ul class="menutree">
						<?php if ( ! empty( $homepage_node ) ) { ?>
						<li><a class="mappasitolink" href="<?php echo esc_url( (string) $homepage_node['link'] ); ?>"><?php echo esc_html( (string) $homepage_node['name'] ); ?></a></li>
						<?php if ( ! empty( $homepage_node['children'] ) && is_array( $homepage_node['children'] ) ) { ?>
							<ul>
								<?php
								foreach ( $homepage_node['children'] as $item ) {
									dli_render_sitemap_node( $item );
								}
								?>
							</ul>
						<?php } ?>
						<?php } ?>
					</ul>

				<div class="box_change_map_lang">
					<?php
						$selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
						if ( 'true' === $selettore_visibile ) {
							if ( 'it' === $lng_slug ) {
								echo '<a href="' . esc_url( trailingslashit( get_site_url() ) . 'en/' . SLUG_MAPPA_SITO_EN ) . '">Site Map in English</a>';
							} else {
								echo '<a href="' . esc_url( trailingslashit( get_site_url() ) . SLUG_MAPPA_SITO_IT ) . '">Mappa del sito in Italiano</a>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>

</main>

<?php
get_footer();
