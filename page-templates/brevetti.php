<?php
/* Template Name: Brevetti
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
define( 'PATENT_CELLS_PER_ROW', 2 );

if ( isset( $_GET['area'] ) ){
	$selected_areas = $_GET['area'];
} else {
	$selected_areas = array();
}

$post_per_page = 10;
$params        = array(
	'search_string' => '',
	'areas'         => array(),
	'deposit_year'  => '',
	'post_per_page' => $post_per_page,
);

$the_query     = DLI_ContentsManager::get_patent_data_query( $params );
$num_results   = $the_query->found_posts;

// $all_areas = dli_get_all_categories_by_ct( 'category', NEWS_POST_TYPE );
?>

<main id="main-container" role="main">

	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER BREVETTI -->
	<?php get_template_part( 'template-parts/hero/brevetti' ); ?>

	<!-- ELENCO BREVETTI -->
	<section id="brevetti">
		<div class="container p-5">
			<!-- inizio row principale -->
			<div class="row">
				<div class="col-12 col-lg-3 border-end pb-3">
					<!--COLONNA FILTRI -->
					
					<!-- FILTRO PER ANNO -->
					<div class="row pt-3">
						<h3 class="h6 text-uppercase border-bottom">Anno</h3>
						<div class="select-wrapper">
							<label for="defaultSelect" class="visually-hidden">Anno</label>
							<select id="defaultSelect">
								<option selected="" value="">Scegli un'opzione</option>
								<option value="Value 1">2023</option>
								<option value="Value 2">2022</option>
								<option value="Value 3">2021</option>
								<option value="Value 4">2020</option>
								<option value="Value 5">2019</option>
							</select>
						</div>
					</div>
					<!-- FILTRO PER AREA TEMATICA - Se esiste -->
					<div class="row pt-5">
						<h3 class="h6 text-uppercase border-bottom">AREA TEMATICA</h3>
						<div>
							<div class="form-check">
								<input id="checkbox4" type="checkbox" checked>
								<label for="checkbox4">Voce 1</label>
							</div>
							<div class="form-check">
								<input id="checkbox5" type="checkbox">
								<label for="checkbox5" class="disabled">Voce 2</label>
							</div>
							<div class="form-check">
								<input id="checkbox5" type="checkbox">
								<label for="checkbox5" class="disabled">Voce 3</label>
							</div>
						</div>
					</div>
					<!-- FILTRO PER RICERCA LIBERA -->
					<!--fine filtri -->
					<div class="row pt-4">
						<h3 class="h6 text-uppercase border-bottom vjs-hidden">Ricerca libera</h3>
						<div>
							<div class="form-group">
								<div class="input-group"> <span class="input-group-text">
									<svg class="icon icon-sm">
										<use href="/bootstrap-italia/dist/svg/sprites.svg#it-search"></use>
									</svg>
									</span>
									<label for="input-group-1">Cerca contenuto</label>
									<input type="text" class="form-control" id="input-group-1" name="input-group-1">
								</div>
							</div>
						</div>
					</div>
				</div>


				<!-- BREVETTI 6 per pagina -->
				<?php
				// The mani loop of the page.
				$pindex = 0;
				if ( $num_results > 0 ) {
				?>
					<!-- inizio contenitore brevetti -->
					<div class="cKl-12 col-lg-8 pt-3">
						<?php
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							if ( ( $pindex % PATENT_CELLS_PER_ROW ) == 0 ) {
						?>
						<div class="row pt-5"> <!-- row -->
							<?php
								}
								$post_id       = get_the_ID();
								$summary       = dli_get_field( 'sommario_elenco' );
								$stato         = dli_get_field( 'stato_legale' );
								$titolari      = dli_get_field( 'titolari' );
								$area_tematica = dli_get_post_main_category( $post, THEMATIC_AREA_TAXONOMY );
							?>
							<div class="col-12 col-lg-6"> 
								<!--start card-->
								<div class="card-wrapper shadow">
									<div class="card card-img no-after">
										<div class="img-responsive-wrapper">
											<div class="img-responsive ">
												<figure class="img-wrapper">
													<img
														src="img/esempiobrevetti1.png?text=IMMAGINE DI ESEMPIO"
														title="titolo immagine"
														alt="descrizione immagine">
												</figure>
											</div>
										</div>
										<div class="card-body">
											<h3 class="h5"><?php the_title(); ?></h3>
											<p class="card-text font-serif"><?php echo esc_html( $summary ); ?></p>
											<p class="card-text font-serif titolari">
												<em><?php echo esc_attr( $titolari ); ?></em>
											</p>
											<p class="card-text font-serif area">
												<?php
												if ($area_tematica && array_key_exists('title', $area_tematica ) ) {
												?>
												<strong><?php echo esc_attr( $area_tematica['title'] ); ?></strong> - 
												<?php
												}
												?>
												<?php echo esc_attr( $stato ); ?>
											</p>
											<div class="pt-5">
												<a class="read-more" href="sf-scheda-brevetto.html"> <span class="text">Leggi di più</span> <span class="visually-hidden">su Lorem ipsum dolor sit amet, consectetur adipiscing elit…</span>
													<svg class="icon">
														<use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-right"></use>
													</svg>
												</a>
											</div>
										</div>
									</div>
								</div>
								<!--end card--> 
							</div>
						<?php
						if ( ( ( $pindex % PATENT_CELLS_PER_ROW ) === PATENT_CELLS_PER_ROW - 1 ) || ( $the_query->current_post + 1 === $the_query->post_count ) ) {
						?>
						</div>
						<!-- end row -->
						<?php
							}
							$pindex++;
						}
						?>
					</div>
					<!-- end contenitore brevetti -->
				<?php
				} else {
				?>
				<div class="col-12 col-lg-8">
					<div clas="row pt-2">
						<?php echo __( 'Non è stato trovato alcun brevetto', 'design_laboratori_italia' ); ?>
					</div>
				</div>
				<?php
				}
				?>
			</div>
			<!-- end row principale -->
		</div> <!-- end container -->
	</section>

	<!-- RESTORE ORIGINAL Post Data -->
	<?php
	wp_reset_postdata();
	?>
	
	<!-- PAGINAZIONE con selettore  -->
	<nav class="pagination-wrapper justify-content-center" aria-label="Esempio di navigazione con page changer">
		<ul class="pagination">
			<li class="page-item">
				<a class="page-link" href="#">
				<svg class="icon icon-primary"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-chevron-left"></use></svg>
				<span class="visually-hidden">Pagina precedente</span>
				</a>
			</li>
			<li class="page-item"><a class="page-link" href="#">1</a></li>
			<li class="page-item"><span class="page-link">...</span></li>
			<li class="page-item"><a class="page-link" href="#">24</a></li>
			<li class="page-item"><a class="page-link" href="#">25</a></li>
			<li class="page-item active">
				<a class="page-link" href="#" aria-current="page">
				<span class="d-inline-block d-sm-none">Pagina </span>26
				</a>
			</li>
			<li class="page-item"><a class="page-link" href="#">27</a></li>
			<li class="page-item"><a class="page-link" href="#">28</a></li>
			<li class="page-item"><span class="page-link">...</span></li>
			<li class="page-item"><a class="page-link" href="#">50</a></li>
			<li class="page-item">
				<a class="page-link" href="#">
				<span class="visually-hidden">Pagina successiva</span>
				<svg class="icon icon-primary"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-chevron-right"></use></svg>
				</a>
			</li>
		</ul>
		<div class="dropdown">
			<button class="btn btn-dropdown dropdown-toggle" type="button" id="pagerChanger" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Salta alla pagina">
			10/pagina
			<svg class="icon icon-primary icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-expand"></use></svg>
			</button>
			<div class="dropdown-menu" aria-labelledby="pagerChanger">
			<div class="link-list-wrapper">
			<ul class="link-list">
				<li><a class="list-item active" href="#" aria-current="page"><span>10/pagina</span></a></li>
				<li><a class="dropdown-item list-item" href="#"><span>20/pagina</span></a></li>
				<li><a class="dropdown-item list-item" href="#"><span>30/pagina</span></a></li>
				<li><a class="dropdown-item list-item" href="#"><span>40/pagina</span></a></li>
				<li><a class="dropdown-item list-item" href="#"><span>50/pagina</span></a></li>
			</ul>
			</div>
			</div>
		</div>
	</nav>

</main>

<?php
get_footer();
