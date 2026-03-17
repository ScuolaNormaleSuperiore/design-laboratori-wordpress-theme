<?php
/**
 * Template Name: Mappa del sito
 *
 * @package Design_Laboratori_Italia
 */

get_header();

// Build the page tree.
$dli_language_slug = dli_current_language( 'slug' );

try {
	$dli_page_tree = dli_get_site_tree();
} catch ( Exception $dli_exception ) {
	$dli_page_tree = array();
}

$dli_homepage_node = array();

if ( isset( $dli_page_tree[ DLI_HOMEPAGE_SLUG ] ) && is_array( $dli_page_tree[ DLI_HOMEPAGE_SLUG ] ) ) {
	$dli_homepage_node = $dli_page_tree[ DLI_HOMEPAGE_SLUG ];
}

if ( ! function_exists( 'dli_render_sitemap_node' ) ) {
	/**
	 * Render sitemap nodes recursively.
	 *
	 * @param array $dli_node Sitemap node.
	 * @return void
	 */
	function dli_render_sitemap_node( $dli_node ) {
		if ( ! is_array( $dli_node ) ) {
			return;
		}

		$dli_name     = isset( $dli_node['name'] ) ? esc_html( (string) $dli_node['name'] ) : '';
		$dli_link     = isset( $dli_node['link'] ) ? esc_url( (string) $dli_node['link'] ) : '';
		$dli_external = ! empty( $dli_node['external'] );

		echo '<li>';

		if ( '' !== $dli_link ) {
			echo '<a class="mappasitolink"';
			if ( $dli_external ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			echo ' href="' . esc_url( $dli_link ) . '">' . esc_html( $dli_name ) . '</a>';
		} else {
			echo '<span class="mappasitolink">' . esc_html( $dli_name ) . '</span>';
		}

		if ( ! empty( $dli_node['children'] ) && is_array( $dli_node['children'] ) ) {
			echo '<ul>';
			foreach ( $dli_node['children'] as $dli_child_node ) {
				dli_render_sitemap_node( $dli_child_node );
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
					<?php if ( ! empty( $dli_homepage_node ) ) : ?>
						<li>
							<a class="mappasitolink" href="<?php echo esc_url( (string) $dli_homepage_node['link'] ); ?>">
								<?php echo esc_html( (string) $dli_homepage_node['name'] ); ?>
							</a>
						</li>
						<?php if ( ! empty( $dli_homepage_node['children'] ) && is_array( $dli_homepage_node['children'] ) ) : ?>
							<ul>
								<?php foreach ( $dli_homepage_node['children'] as $dli_item ) : ?>
									<?php dli_render_sitemap_node( $dli_item ); ?>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					<?php endif; ?>
				</ul>

				<div class="box_change_map_lang">
					<?php
					$dli_selector_visible = dli_get_option( 'selettore_lingua_visible', 'setup' );
					if ( 'true' === $dli_selector_visible ) :
						?>
						<?php if ( 'it' === $dli_language_slug ) : ?>
							<a href="<?php echo esc_url( trailingslashit( get_site_url() ) . 'en/' . SLUG_MAPPA_SITO_EN ); ?>">
								<?php echo esc_html__( 'Site Map in English', 'design_laboratori_italia' ); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( trailingslashit( get_site_url() ) . SLUG_MAPPA_SITO_IT ); ?>">
								<?php echo esc_html__( 'Mappa del sito in Italiano', 'design_laboratori_italia' ); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

</main>

<?php get_footer(); ?>
