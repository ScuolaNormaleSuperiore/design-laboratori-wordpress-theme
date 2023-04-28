<?php
/* Template Name: Notizie.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

// Build the PAGE TREE.
$pt = dli_get_site_tree();
?>

<main id="main-container">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER MAPPA DEL SITO -->
	<?php get_template_part( 'template-parts/hero/mappasito' ); ?>

	<!-- MAPPA DEL SITO -->
	<div class="container my-4">
		<ul class="menutree">
			<li><a href="<?php echo $pt[DLI_HOMEPAGE_SLUG]['link']; ?>"><?php echo $pt[DLI_HOMEPAGE_SLUG]['name']; ?></a></li>
			<ul>
				<?php
				// I livello.
				foreach ( $pt[DLI_HOMEPAGE_SLUG]['children'] as $item ) {
					if ( $item['external'] ) {
						echo '<li><a target="_blank" href="' . $item['link'] . '">' . $item['name'] . '</a></li>';
					} else {
						echo '<li><a href="' . $item['link'] . '">' . $item['name'] . '</a></li>';
					}
					// II livello.
					foreach ( $item['children'] as $childitem ) {
						if ( $childitem['external'] ) {
							echo '<li><a target="_blank" href="' . $childitem['link'] . '">' . $childitem['name'] . '</a></li>';
						} else {
							echo '<li><a href="' . $childitem['link'] . '">' . $childitem['name'] . '</a></li>';
						}
						// III livello.
						foreach ( $childitem['children'] as $grandchilditem ) {
							if ( $grandchilditem['external'] ) {
								echo '<li><a target="_blank" href="' . $grandchilditem['link'] . '">' . $grandchilditem['name'] . '</a></li>';
							} else {
								echo '<li><a href="' . $grandchilditem['link'] . '">' . $grandchilditem['name'] . '</a></li>';
							}
						}
					}
				}
				?>
			</ul>
		</ul>
	</div>

</main>

<?php
get_footer();
