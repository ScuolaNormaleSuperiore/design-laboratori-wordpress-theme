<?php
/* Template Name: Le pubblicazioni.
 *
 * @package Design_Laboratori_Italia
 */
get_header();

$anni_pubblicazioni = $wpdb->get_results( "SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key  = 'anno' and pm.post_id=p.ID  and p.post_type='pubblicazione' ",ARRAY_A );
print_r($anni_pubblicazioni);

?>


<main id="main-container">
	<!-- BREADCRUMB -->
	<section id ="breadcrumb">
		<div class="container">
			<div class="row">
				<div class="col-12 ms-4 ">
					<nav class="breadcrumb-container" aria-label="Percorso di navigazione">
						<ol class="breadcrumb pb-0">
							<li class="breadcrumb-item"><a href="<?php echo esc_url(get_site_url()); ?>">Home</a><span class="separator">&gt;</span></li>
							<li class="breadcrumb-item active" aria-current="<?php _e( 'Elenco pubblicazioni', 'design_laboratori_italia' ); ?>"><?php _e( 'Pubblicazioni', 'design_laboratori_italia' ); ?></li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</section>
	<!-- BANNER PUBBLICAZIONI -->
	<?php get_template_part( 'template-parts/hero/pubblicazioni' ); ?>

	<!-- ELENCO PUBBLICAZIONI -->
	<section id="pubblicazioni" class="p-4">   
		<div class="container my-4"> 
			<div class="row pt-0"> <!-- SPAZIATURA ridotta in alto solo sulla prima riga riga pt-0 le card NON uniformate in altezza -->
				<div class="col-12 col-lg-3 border-end">
					<!--COLONNA FILTRI -->
					<!-- FILTRO PER ANNO -->
					<div class="row pt-3">
						<h3 class="h6 text-uppercase border-bottom"><?php _e( 'Anno', 'design_laboratori_italia' ); ?></h3>
						<div class="select-wrapper">
							<label for="defaultSelect" class="visually-hidden"><?php _e( 'Anno', 'design_laboratori_italia' ); ?></label>
							<select id="defaultSelect">
								<option selected="" value=""><?php _e( 'Scegli un\'opzione', 'design_laboratori_italia' ); ?></option>
								<?php
								foreach ( $anni_pubblicazioni as $anno ) { ?>
									<option value=""><?php echo $anno->meta_value; ?></option>
									<option value="Value 1">2023</option>
									<?php
								}
								?>
							</select>
						</div>
					</div>
                             <!-- FILTRO PER CATEGORIA - Se esiste -->
                              <div class="row pt-5">
                                <h3 class="h6 text-uppercase border-bottom">Tipologia</h3>
                                  <div>
                                  <div class="form-check">
                                    <input id="checkbox4" type="checkbox" checked>
                                    <label for="checkbox4">Monografie e articoli</label>
                                  </div>
                                  <div class="form-check">
                                    <input id="checkbox5" type="checkbox">
                                    <label for="checkbox5" class="disabled">Altra categoria</label>
                                  </div>
                                <div class="form-check">
                                    <input id="checkbox5" type="checkbox">
                                    <label for="checkbox5" class="disabled">Altra categoria 2</label>
                                  </div>
                                </div>
                              </div>
                            
                            <!--fine filtri -->
                    </div>
                    <!-- PUBBLICAZIONI -->
                    <div class="col-12 col-lg-8">
                        <div class="row">      
                        
                           <!--start card-->
                            <div class="card-wrapper">
                             <div class="card card-teaser rounded shadow">
						<div class="card-body">
							<h3 class="card-title h5"><a href="https://ricerca.sns.it/handle/11384/75152?mode=complete">Sperimentazione di Sistemi Informativi Geografici applicati alle aree archeologiche</a></h3>
							<p class="card-text">Monografie e Articoli, Pietro Carmelo Manti,  in «Elymos. 1, Quaderni del Parco Archeologico di Segesta», pp. 29-38 2022</p>
						</div>
					</div>
                            </div>
                            <!--end card-->
                            
                            <!--start card-->
                            <div class="card-wrapper pt-3">
                             <div class="card card-teaser ">
						<div class="card-body">
							<h3 class="card-title h5">Sperimentazione di Sistemi Informativi Geografici applicati alle aree archeologiche </h3>
							<p class="card-text">Monografie e Articoli, Pietro Carmelo Manti,  in «Elymos. 1, Quaderni del Parco Archeologico di Segesta», pp. 29-38 2022</p>
						</div>
					</div>
                            </div>
                            <!--end card-->
                            
                            <!--start card-->
                            <div class="card-wrapper pt-3">
                             <div class="card card-teaser ">
						<div class="card-body">
							<h3 class="card-title h5">Sperimentazione di Sistemi Informativi Geografici applicati alle aree archeologiche </h3>
							<p class="card-text">Monografie e Articoli, Pietro Carmelo Manti,  in «Elymos. 1, Quaderni del Parco Archeologico di Segesta», pp. 29-38 2022</p>
						</div>
					</div>
                            </div>
                            <!--end card-->
                            
                            <!--start card-->
                            <div class="card-wrapper pt-3">
                             <div class="card card-teaser ">
						<div class="card-body">
							<h3 class="card-title h5">Sperimentazione di Sistemi Informativi Geografici applicati alle aree archeologiche </h3>
							<p class="card-text">Monografie e Articoli, Pietro Carmelo Manti,  in «Elymos. 1, Quaderni del Parco Archeologico di Segesta», pp. 29-38 2022</p>
						</div>
					</div>
                            </div>
                            <!--end card-->
                        </div>		  
                    </div>
                     
				</div>
		    </div>            
        </section>
<!-- PAGINAZIONE -->
        
<nav class="pagination-wrapper justify-content-center" aria-label="Navigazione centrata">
              <ul class="pagination">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tabindex="-1" aria-hidden="true">
                    <svg class="icon icon-primary"><use href="bootstrap-italia/svg/sprites.svg#it-chevron-left"></use></svg>
                    <span class="visually-hidden">Pagina precedente</span>
                  </a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#" aria-current="page">
                    <span class="d-inline-block d-sm-none">Pagina </span>1
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#">
                    <span class="visually-hidden">Pagina successiva</span>
                    <svg class="icon icon-primary"><use href="bootstrap-italia/svg/sprites.svg#it-chevron-right"></use></svg>
                  </a>
                </li>
              </ul>
            </nav>
    
    </main>
		   <!-- END CONTENT -->

<?php
get_footer();



