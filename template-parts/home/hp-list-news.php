<?php
	$section_enabled = dli_get_option( 'home_news_list_is_visible', 'homepage' );

	if ( 'true' === $section_enabled ) {

?>
<!-- INIZIO ELENCO NOTIZIE HP -->
<section id="blocco-news" class="section pt-3 pb-3" >
	<div class="section-content">
		<div class="container">
			<h2 class="h3 pb-2 ">News</h2>
			<div class="row">
				<div class="col-12 col-lg-4"> 
					<!-- NEWS -->
					<div class="card-wrapper">
						<div class="card card-bg">
								<div class="img-responsive-wrapper">
					<div class="img-responsive">
						<figure class="img-wrapper">
							<img src="https://via.placeholder.com/310x190/0066cc/FFFFFF/?text=IMMAGINE%20DI%20ESEMPIO" title="titolo immagine" alt="descrizione immagine">
						</figure>
					</div>
				</div>
							<div class="card-body">
								<div class="category-top"> <a class="category" href="#">Categoria</a> <span class="data">10/12/2023</span> </div>
								<h3 class="card-title h4">Titolo della news su più righe</h3>
								<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
								<a class="read-more" href="sf-scheda-news.html"> <span class="text">Leggi di più</span> <span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
								<svg class="icon">
									<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
								</svg>
								</a> </div>
						</div>
					</div>
					<!-- FINE NEWS --> 
				</div>
				<div class="col-12 col-lg-4"> 
					<!-- NEWS -->
					<div class="card-wrapper">
						<div class="card card-bg">
								<div class="img-responsive-wrapper">
					<div class="img-responsive">
						<figure class="img-wrapper">
							<img src="https://via.placeholder.com/310x190/0066cc/FFFFFF/?text=IMMAGINE%20DI%20ESEMPIO" title="titolo immagine" alt="descrizione immagine">
						</figure>
					</div>
				</div>
							<div class="card-body">
								<div class="category-top"> <a class="category" href="#">Categoria</a> <span class="data">10/12/2023</span> </div>
								<h3 class="card-title h4">Titolo della news su più righe</h3>
								<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
								<a class="read-more" href="sf-scheda-news.html"> <span class="text">Leggi di più</span> <span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
								<svg class="icon">
									<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
								</svg>
								</a> </div>
						</div>
					</div>
					<!-- FINE NEWS --> 
				</div>
				<div class="col-12 col-lg-4"> 
					<!-- NEWS -->
					<div class="card-wrapper">
						<div class="card card-bg">
								<div class="img-responsive-wrapper">
					<div class="img-responsive">
						<figure class="img-wrapper">
							<img src="https://via.placeholder.com/310x190/0066cc/FFFFFF/?text=IMMAGINE%20DI%20ESEMPIO" title="titolo immagine" alt="descrizione immagine">
						</figure>
					</div>
				</div>
							<div class="card-body">
								<div class="category-top"> <a class="category" href="#">Categoria</a> <span class="data">10/12/2023</span> </div>
								<h3 class="card-title h4">Titolo della news su più righe</h3>
								<p class="card-text">Abstract della news su più righe con riduzione testo o utilizzo campo riassunto standard wordpress&nbsp;</p>
								<a class="read-more" href="sf-scheda-news.html"> <span class="text">Leggi di più</span> <span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
								<svg class="icon">
									<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
								</svg>
								</a>
							</div>
						</div>
					</div>
					<!-- FINE NEWS -->
				</div>
			</div>
				<div class="text-center pt-5">
	<a href="#" class="btn btn-secondary">Tutte le news</a>
</div>
		</div>
	</div>
</section>
<!-- FINE ELENCO NOTIZIE HP -->
<?php
	}
?>
