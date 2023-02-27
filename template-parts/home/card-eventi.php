<?php
$query = new WP_Query(
	array(
		'post_type'      => array( 'evento' ),
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'posts_per_page' => -1,
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
			$last_hero_event = $query->posts[0];
			$event_date      = DateTime::createFromFormat( DLI_ACF_DATE_FORMAT, get_the_date( DLI_ACF_DATE_FORMAT, $last_hero_event ) );
		?>
			<div class="img-responsive-wrapper">
				<div class="img-responsive img-responsive-panoramic">
					<figure class="img-wrapper">
					<img src="<?php echo get_the_post_thumbnail_url( $last_hero_event , 'item-hero-event' ); ?>"
						alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>"
						title="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>" 
						alt="<?php echo esc_attr( get_the_title( $last_hero_event ) ); ?>">
					</figure>
					<div class="card-calendar d-flex flex-column justify-content-center">
						<span class="card-date"><?php echo $event_date->format( 'd' ); ?></span>
						<span class="card-day"><?php echo __( dli_get_monthname( $event_date->format( 'm' ), 'design_laboratori_italia' ) ); ?></span>
					</div>
				</div>
			</div>
			<div class="card-body p-4">
			<h3 class="card-title h4"><?php echo get_the_title( $last_hero_event ); ?></h3>
			<p class="card-text"><?php echo wp_trim_words( get_field( 'descrizione_breve', $last_hero_event ), DLI_ACF_SHORT_DESC_LENGTH ); ?></p>
			<a class="read-more" href="<?php echo get_permalink( $last_hero_event ); ?>">
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
