<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = count( $items );
	define( 'EVENTI_PER_ROW', 3 );
?>

<section id="eventi" >
	<div class="section-content">
		<!-- begin row eventi -->
		<div class="row pt-3">
			<div class="col-12 col-lg-4">
			<!--begin card eventi-->
			<div class="card-wrapper">
			<div class="card card-img no-after card-bg">
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
			<span class="text">Leggi di più</span>
			<span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
			<svg class="icon">
			<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
			</svg></a>
			</div>
			</div>
			</div>
			<!--end card eventi -->
			</div>
			<div class="col-12 col-lg-4">
			<!--start card-->
			<div class="card-wrapper">
			<div class="card card-img no-after card-bg">
			<div class="img-responsive-wrapper">
			<div class="img-responsive img-responsive-panoramic">
			<figure class="img-wrapper">
			<img src="img/img-avatar-250x250.png" title="titolo immagine" alt="descrizione immagine">
			</figure>
			<div class="card-calendar d-flex flex-column justify-content-center">
			<span class="card-date">23</span>
			<span class="card-day">Dicembre</span>
			</div>
			</div>
			</div>
			<div class="card-body p-4">
			<h3 class="card-title h4">Titolo evento 2</h3>
			<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
			<a class="read-more" href="#">
			<span class="text">Leggi di più</span>
			<span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
			<svg class="icon">
			<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
			</svg></a>
			</div>
			</div>
			</div>
			<!--end card eventi-->
			</div>
		</div>
		<!-- end row eventi -->
	</div>
</section>
