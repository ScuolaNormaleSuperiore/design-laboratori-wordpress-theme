<?php
	$items       = $args['items'];
	$section_id  = $args['section_id'];
	$num_results = count( $items );
	define( 'ITEMS_PER_ROW', 3 );
?>

<section id="indirizzi-ricerca">
	<div class="row pb-3 col-sm-12">
		<div class="card-wrapper card-teaser-wrapper">
			<!--start card-->
			<div class="card card-teaser rounded shadow">
				<div class="card-body">
					<h3 class="card-title h5 ">
						<svg class="icon">
							<use href="bootstrap-italia/svg/sprites.svg#it-folder"></use>
						</svg>
						<a href="sf-scheda-attivita.html">Nome indirizzo</a>
					</h3>
					<div class="card-text">
						<p>Descrizione breve</p>
					</div>
				</div>
			</div>
			<!--end card-->
			<!--start card-->
			<div class="card card-teaser rounded shadow">
				<div class="card-body">
					<h3 class="card-title h5 ">
						<svg class="icon">
							<use href="bootstrap-italia/svg/sprites.svg#it-folder"></use>
						</svg>
						<a href="sf-scheda-attivita.html">Nome indirizzo</a>
					</h3>
					<div class="card-text">
					<p>Descrizione breve</p>
					</div>
				</div>
			</div>
			<!--end card-->
		</div>
	</div>
</section>
