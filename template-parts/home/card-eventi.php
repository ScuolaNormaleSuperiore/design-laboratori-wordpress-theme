<?php
$query = new WP_Query(
	array(
		'post_type'      => array( 'evento' ),
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
	<h2 class="h3 pb-2"><?php echo __( 'Eventi', 'design_laboratori_italia' ); ?></h2>
	<div class="card-wrapper">
		<div class="card card-img no-after card-bg">
		<?php
		if ( $num_items != 0) {
			$last_hero_news = $query->posts[0];
		?>
			<div class="img-responsive-wrapper">
				<div class="img-responsive img-responsive-panoramic">
					<figure class="img-wrapper">
					<img src="img/img-avatar-250x250.png" title="titolo immagine" alt="descrizione immagine">
					</figure>
					<div class="card-calendar d-flex flex-column justify-content-center">
						<span class="card-date">30</span>
						<span class="card-day">novembre</span>
					</div>
				</div>
			</div>
			<div class="card-body p-4">
			<h3 class="card-title h4">Titolo evento</h3>
			<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
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
