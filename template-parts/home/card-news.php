<?php
$query = new WP_Query(
	array(
		'post_type'      => array( 'notizia' ),
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'     => 'promuovi_in_hero',
				'compare' => '=',
				'value'   => 1,
			),
		),
	)
);
$num_items = $query->post_count;
?>

<div class="col-12 col-lg-4">
	<h2 class="h3 pb-2"><?php echo __( 'Notizie', 'design_laboratori_italia' ); ?></h2>
	<div class="card-wrapper">
		<div class="card card-bg">
		<?php
		if ( $num_items != 0) {
			$last_hero_news = $query->posts[0];
		?>
			<div class="card-body">
				<div class="category-top">
					<a class="category" href="#">Categoria</a>
					<span class="data"><?php echo get_the_date( 'd/m/Y', $last_hero_news ); ?></span>
				</div>
				<h3 class="card-title h4"><?php echo get_the_title( $last_hero_news ); ?></h3>
				<p class="card-text"><?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_news ), 200 ); ?></p>
				<a class="read-more" href="#">
					<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
					<span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
					<svg class="icon"><use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>"></use></svg>
				</a>
			</div>
		<?php
		}
		?>
		</div>
	</div>
</div>