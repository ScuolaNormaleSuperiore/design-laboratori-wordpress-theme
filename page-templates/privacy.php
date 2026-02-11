<?php
/**
* Template Name: Privacy
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Design_Laboratori_Italia
 */

global $post;
get_header();
$image_metadata = dli_get_image_metadata( $post, 'full' );
$related_items  = dli_get_field( 'pagine_collegate' );
$current_lang   = dli_current_language();

// Gestione dei cookies: Verifica presenza ed eventuale rimozione.
$cookies          = $_COOKIE;
$cookie_name      = 'cc_cookies';
$cookies_presenti = false;
?>

<main id="main-container" role="main">

<script>
	// Verifica al caricamento della pagina se ci sono cookies accettati.
	function checkLocalStorage() {
		const cookies = document.cookie;
		const keyExists = localStorage.getItem('bs-ck3') !== null;
		const noDenyCookiesMsg = document.getElementById('dli_no_accepted_cookies_msg');
		const denyCookiesButton = document.getElementById('dli_deny_cookies_button');
		if (keyExists) {
				// La chiave esiste: mostra "denyCookiesButton", nascondi "noDenyCookiesMsg".
				noDenyCookiesMsg.style.display = "none";
				denyCookiesButton.style.display = "block";
		} else {
				// La chiave non esiste: mostra "noDenyCookiesMsg", nascondi "denyCookiesButton".
				noDenyCookiesMsg.style.display = "block";
				denyCookiesButton.style.display = "none";
		}
	}
	window.addEventListener('DOMContentLoaded', checkLocalStorage);

	function deleteYouTubeCookies() {
		const domain = 'youtube.com';
		debugger;
		// Cancella tutti i cookies di YouTube
		document.cookie.split(';').forEach(function(cookie) {
			const cookieName = cookie.split('=')[0].trim();
			document.cookie = cookieName + '=; path=/; domain=' + domain + '; expires=Thu, 01 Jan 1970 00:00:00 GMT';
		});
	}

	// Rimuove i cookies accettati.
	function removeThirdPartiesCookies(event){
		const keyExists = localStorage.getItem('bs-ck3') !== null;
		const noDenyCookiesMsg = document.getElementById('dli_no_accepted_cookies_msg');
		const denyCookiesButton = document.getElementById('dli_deny_cookies_button');
		if (keyExists) {
				localStorage.removeItem('bs-ck3');
				noDenyCookiesMsg.style.display = "block";
				denyCookiesButton.style.display = "none";
				deleteYouTubeCookies();
		}
	}
</script>


	<!-- BREADCRUMB -->
	<?php get_template_part( 'template-parts/common/breadcrumb' ); ?>

	<!-- BANNER PAGINA -->
	<section id="banner-paginabase" aria-describedby="Testo introduttivo paginabase" class="bg-banner-paginabase">
		<div class="section-muted p-3 primary-bg-c1">
			<div class="container">
				<div class="hero-title text-left ms-4 pb-3 pt-3">
					<h2 class="p-0"><?php echo get_the_title(); ?></h2>
					<p class="font-weight-normal">
						<?php echo wp_trim_words( dli_get_field( 'descrizione_breve' ), DLI_ACF_SHORT_DESC_LENGTH ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CONTENUTO PAGINA-->
	<section id="paginabase" class="pb-3">
		<div class="container container-border-top pt-5">
			<div class="row">
				<?php
					//get top parent page id
					$top_parent = dli_get_page_anchestor_id( $post );
					$slugs      = array_reverse ( dli_get_page_slug_anchestors( $post ) );
					if (
						count( $slugs ) > 0
						&& isset( $slugs[1] )
						&& ( $slugs[0] === SLUG_LABORATORIO_IT || $slugs[0] === SLUG_LABORATORIO_EN )
					) {
						$parent_page = get_page_by_path( $slugs[0] . '/' . $slugs[1] );
						if ( $parent_page ) {
							$top_parent = $parent_page->ID;
						}
					}
					//Retrieve till second level pages
					$pages = get_pages(
						array(
						'child_of'    => $top_parent,
						'offset'      => 0,
						'parent'      => $top_parent,
						'sort_column' => 'menu_order',
						)
					);
					if($pages) {
						?>

				<!-- MENU LATERALE (INIZIO SIDEBAR) -->
				<div class="sidebar-wrapper border-end col-12 col-lg-3">
					<?php if ( $post->post_parent !== 0 ) {
					?>
					<a href="<?php echo get_permalink( $post->post_parent ); ?>" class="btn btn-primary btn-xs btn-me mb-5" role="button">
						<svg class="icon icon-sm icon-white me-2" role="img" aria-labelledby="<?php echo __( 'Torna indietro', 'design_laboratori_italia' ); ?>">
							<title><?php echo __( 'Torna indietro', 'design_laboratori_italia' ); ?></title>
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-arrow-left';?>"></use>
						</svg>
						<?php echo __( 'Torna indietro', 'design_laboratori_italia' ); ?>
					</a>
					<?php
					}
					?>
					<h3><?php echo __( 'Pagine collegate', 'design_laboratori_italia' ); ?></h3>
					<div class="progress">
						<div class="progress-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div class="sidebar-linklist-wrapper">
						<div class="link-list-wrapper">
							<ul class="link-list">
								<?php
									foreach ( $pages as $pg ) {
										?>
										<li>
											<a class="list-item large medium right-icon <?php if ( $post->ID === $pg->ID || $pg->ID === $post->post_parent ) echo "active"; ?>" href="<?php echo get_permalink( $pg->ID ); ?>">
												<span class="list-item-title-icon-wrapper">
													<span><?php echo esc_attr( get_the_title( $pg ) ); ?></span>
												</span>
											</a>
											<?php
											//Show subpages of current branch page till second level.
											$subspg = get_pages(
												array(
												'child_of'    => $pg->ID,
												'offset'      => 0,
												'parent'      => $pg->ID,
												'sort_column' => 'menu_order',
											));
											if ( $post->post_parent !== 0 && ($post->ID === $pg->ID || in_array( $post, $subspg ) ) ) {
												?>
												<ul class="link-sublist">
													<?php
													foreach( $subspg as $subpg ) {
														?>
														<li>
															<a class="list-item <?php if ( $post->ID === $subpg->ID ) echo "active"; ?>" href="<?php echo get_permalink( $subpg->ID ); ?>">
																<span><?php echo esc_attr( get_the_title( $subpg ) ); ?></span>
															</a>
														</li>
														<?php
													}
													?>
												</ul>
											<?php
										}
									}
								?>
							</ul>
						</div>
					</div>
				</div>
				<!-- FINE SIDEBAR -->
				<?php
				} else {
				?>
				<!-- La pagina non ha genitori e figli -->
				<div class="sidebar-wrapper border-end col-12 col-lg-3"></div>
				<?php
				}
				?>

				<!-- CORPO ARTICOLO-->
				<div class="col-lg-8 pt84">
					<div class="mb-4">
						<?php
						if ( $image_metadata['image_url'] ) {
						?>
							<img src="<?php echo $image_metadata['image_url']; ?>"
								alt="<?php echo esc_attr( $image_metadata['image_alt'] ); ?>" 
								title="<?php echo esc_attr( $image_metadata['image_title'] ); ?>" 
								class="img-fluid">
							<?php
								if( $image_metadata['image_caption'] ) {
							?>
								<figcaption class="figure-caption">
									<?php echo esc_attr( $image_metadata['image_caption'] ); ?>
								</figcaption>
							<?php
								}
							?>
						<?php
						}
						?>
					</div>

					<!-- BODY ARTICLE -->
					<article class="article-wrapper">

						<!-- TESTO PAGINA EDITOR BACK-OFFICE -->
						<?php the_content(); ?>

						<!-- TESTO AUTOMATICO -->
						<div>
							<div class="text-image-cta d-flex mb-0"><div class="content w-100">
								<h5 class="mb-3"><?php echo __( 'Gestione cookies', 'design_laboratori_italia' ); ?></h5></div>
							</div>
							<div class="text-image-cta d-flex mb-5">
								<div class="content w-100">
									<div id="dli_no_accepted_cookies_msg">
										<p><?php echo __( 'Non hai installato cookie di terze parti', 'design_laboratori_italia' ); ?>.</p>
									</div>
									<div id="dli_deny_cookies_button">
										<p>
											<?php echo esc_html( __( 'Hai installato i seguenti cookies di terze parti:', 'design_laboratori_italia' ) ); ?>
										</p>
										<p>
											<strong><?php echo esc_html( __( 'Youtube per la visualizzazione di video', 'design_laboratori_italia' ) ); ?></strong>
											&nbsp;&nbsp;&nbsp;
											<button type="submit" class="btn btn-primary" onclick="removeThirdPartiesCookies();">
												<?php echo esc_html( __( 'Revoca', 'design_laboratori_italia' ) ); ?>
											</button>
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- FINE TESTO AUTOMATICO -->

					</article>

					<!-- NEWS ED EVENTI (related_items) -->
					<?php
						if ( $related_items ){
							get_template_part(
								'template-parts/common/sezione-related-items',
								null,
								array(
									'items' => $related_items,
								)
							);
						}
					?>

				</div><!-- /col-lg-8 -->

			</div><!-- /row -->
		</div><!-- /container -->
	</section>

		</div>   <!-- END container -->

</main>


<?php
get_footer();
