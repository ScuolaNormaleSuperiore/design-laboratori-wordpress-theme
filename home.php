<?php
/**
 * The template for displaying home
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

get_header();
?>
<main id="main-container" class="main-container redbrown">

<!-- CAROUSEL -->
<section class="section pt-5 pb-5">
	<?php get_template_part( 'template-parts/home/carousel' ); ?>
</section>

<!-- PRESENTAZIONE -->
<section id="presentazione" aria-describedby="Presentazione del laboratorio" class="section section-muted pt-5">
	<div>
		<div class="container my-12">
			<h2 class="h3 pb-1"><?php echo __( 'Il laboratorio', 'design_laboratori_italia' ); ?></h2>
			<p>
				<?php
				$post = get_page_by_path( 'presentazione' );
				if ( ! $post ) {
					echo 'Qui va il contenuto della pagina con slug: "presentazione". Se non esiste, va creata.';
				} else {
					$content = apply_filters( 'the_content', $post->post_content);
					echo $content;
				}
				?>
			</p>
		</div>
	</div>
</section>

</main>
<?php
get_footer();
