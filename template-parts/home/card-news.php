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
			$termitem       = dli_get_main_taxonomy_termitem( $last_hero_news, 'category' );
		?>
			<div class="card-body">
				<div class="category-top">
					<a class="category" href="<?php echo esc_url( $termitem['url'] ); ?>"><?php echo esc_attr( $termitem['title'] ); ?></a>
					<span class="data"><?php echo get_the_date( DLI_ACF_DATE_FORMAT, $last_hero_news ); ?></span>
				</div>
				<h3 class="card-title h4"><?php echo get_the_title( $last_hero_news ); ?></h3>
				<p class="card-text"><?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_news ), 50 ); ?></p>
				<a class="read-more" href="<?php echo get_permalink( $last_hero_news ); ?>">
					<span class="text"><?php echo __( 'Leggi di più', 'design_laboratori_italia' ); ?></span>
					<svg class="icon"><use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-right' ?>"></use></svg>
				</a>
			</div>
		<?php
		}
		?>
		</div>
	</div>
</div>