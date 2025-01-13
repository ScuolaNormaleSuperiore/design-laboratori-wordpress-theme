<?php
/**
* Template Name: Cookies
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
$form_inviato     = sanitize_text_field( isset( $postdata['form_inviato'] ) ? $postdata['form_inviato'] : 'no' );
if ( ( 'yes' === $form_inviato ) && ( $cookies_presenti  === true ) ){
	if ( isset( $postdata['contatti_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( $postdata['contatti_nonce_field'] ), 'sf_contatti_nonce' ) ) {
		// Rimuovi cookie.
	}
}

?>

<main id="main-container" role="main">

<script>
	function checkLocalStorage() {
		const keyExists = localStorage.getItem('bs-ck3') !== null;
		const nodenyCookiesButton = document.getElementById('dli_no_accepted_cookies_msg');
		const denyCookiesButton = document.getElementById('dli_deny_cookies_button');
		if (keyExists) {
				// La chiave esiste: mostra "accepted", nascondi "no accepted"
				nodenyCookiesButton.style.display = "none";
				denyCookiesButton.style.display = "block";
		} else {
				// La chiave non esiste: mostra "no accepted", nascondi "accepted"
				nodenyCookiesButton.style.display = "block";
				denyCookiesButton.style.display = "none";
		}
	}

	// Verifica al caricamento della pagina
	window.addEventListener('DOMContentLoaded', checkLocalStorage);

	function removeThirdPartiesCookies(event){
		console.log("Eccomi in removeThirdPartiesCookies");
		if (localStorage.getItem('bs-ck3')) {
				localStorage.removeItem('bs-ck3');
				console.log("Chiave 'bs-ck3' rimossa.");
				nodenyCookiesButton.style.display = "none";
				denyCookiesButton.style.display = "block";
		} else {
				console.log("Chiave 'bs-ck3' non trovata.");
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
					if ( count ( $slugs ) > 0 && ( $slugs[0] === SLUG_LABORATORIO_IT || $slugs[0] === SLUG_LABORATORIO_EN ) ) {
						$top_parent = get_page_by_path( $slugs[0] . '/' . $slugs[1] )->ID;
					}
					//Retrieve till second level pages
					$pages = get_pages(
						array(
						'child_of'     => $top_parent,
						'offset'       => 0,
						'parent'       => $top_parent,
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
											$subspg = get_pages( array(
												'child_of' => $pg->ID,
												'offset'   => 0,
												'parent'   => $pg->ID,
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
					<div>
						<h2 class="mb-0" id="gestione-cookie">Cookies</h2>
						<div class="text-image-cta d-flex mb-5">
							<div class="content w-100">
								<?php 
								if ( $current_lang === 'it' ) {
								?>
								<div>
									<p>Questa sezione fornisce informazioni dettagliate sull'uso dei cookie, su come sono utilizzati dal sito e su come gestirli, in attuazione dell'art. 122 del decreto legislativo 30 giugno 2003, n. 196, nonché nel rispetto delle "<a href="https://www.garanteprivacy.it/web/guest/home/docweb/-/docweb-display/docweb/9677876">Linee guida cookie e altri strumenti di tracciamento</a>" emanate dal Garante per la protezione dei dati personali con provvedimento del 10 giugno 2021.</p>
									<p>Questo sito utilizza cookie tecnici necessari per il suo funzionamento e per migliorare l'esperienza d'uso dei propri visitatori, nonché cookie analitici che raccolgono, in forma anonima (attraverso il mascheramento di porzioni dell'indirizzo IP dell'utente), informazioni statistiche e aggregate sulle modalità di navigazione degli utenti (ad es., numero di pagine visitate e accessi, tempo di permanenza nel sito). Tali cookie sono assimilati ai cookie tecnici e non necessitano del tuo consenso. Inoltre, i predetti cookie analitici, una volta memorizzati sul tuo browser, possono permanere fino a un massimo di 13 mesi, ferma la possibilità di decidere di disabilitarne l'utilizzo sul proprio browser, in qualsiasi momento, nelle modalità indicate nel paragrafo che segue.</p>
									<p>Per accedere ai contenuti video ospitati direttamente all'interno del sito, prima della loro visualizzazione ti sarà richiesto di approvare i cookie di profilazione di terza parte di YouTube (<a href="https://policies.google.com/privacy?hl=it">https://policies.google.com/privacy?hl=it</a>) che attivano anche gli altri identificatori tecnici utilizzati da YouTube per consentirti lo streaming video dal sito. Tali cookie saranno memorizzati sul browser solo dopo aver acquisito il tuo consenso che potrà essere limitato ad un solo video oppure esteso a tutti i video del sito, inserendo la spunta alla voce "<strong>Ricorda per tutti i video</strong>". Ti ricordiamo che potrai sempre modificare la tua scelta tramite il pulsante "<strong>Revoca</strong>" presente nel paragrafo "<strong>Gestione cookie</strong>" in fondo a questa informativa. La funzionalità di gestione dei cookie è raggiungibile anche dal piede del sito.</p>
									<h3>Come disabilitare i cookie (opt-out) sul proprio dispositivo</h3>
									<p>La maggior parte dei browser accetta i cookie automaticamente, ma è possibile rifiutarli. Se non si desidera ricevere o memorizzare i cookie, si possono modificare le impostazioni di sicurezza del browser utilizzato, secondo le istruzioni rese disponibili dai relativi fornitori ai link di seguito indicati. Nel caso in cui si disabilitino tutti i cookie, il sito potrebbe non funzionare correttamente.</p>
								</div>
								<?php
								} else {
								?>
								<div>
									<p>This section provides detailed information on the use of cookies, how they are used by the site and how to manage them, in compliance with art. 122 of Legislative Decree 30 June 2003, n. 196, as well as in compliance with the "<a href="https://www.garanteprivacy.it/web/guest/home/docweb/-/docweb-display/docweb/9677876">Guidelines for cookies and other tracking tools</a>" issued by the Guarantor for the protection of personal data with provision of 10 June 2021.</p>
									<p>This site uses technical cookies necessary for its operation and to improve the user experience of its visitors, as well as analytical cookies that collect, anonymously (by masking portions of the user's IP address), statistical and aggregate information on the users' browsing methods (e.g., number of pages visited and accesses, time spent on the site). These cookies are assimilated to technical cookies and do not require your consent. Furthermore, the aforementioned analytical cookies, once stored on your browser, can remain for up to a maximum of 13 months, without prejudice to the possibility of deciding to disable their use on your browser, at any time, in the ways indicated in the following paragraph.</p>
									<p>To access video content hosted directly within the site, before viewing it you will be asked to approve YouTube's third-party profiling cookies (<a href="https://policies.google.com/privacy?hl=it">https://policies.google.com/privacy?hl=it</a>) which also activate the other technical identifiers used by YouTube to allow you to stream videos from the site. These cookies will be stored on the browser only after having acquired your consent, which may be limited to a single video or extended to all videos on the site, by checking the box "<strong>Remember for all videos</strong>". We remind you that you can always change your choice using the "<strong>Revoke</strong>" button in the "<strong>Cookie management</strong>" paragraph at the bottom of this policy. The cookie management functionality can also be accessed from the footer of the site.</p>
									<h3>How to disable cookies (opt-out) on your device</h3>
									<p>Most browsers automatically accept cookies, but you can refuse them. If you do not wish to receive or store cookies, you can change the security settings of your browser, following the instructions provided by the relevant providers at the links below. If you disable all cookies, the site may not function properly.</p>
								</div>
								<?php
								}
								?>
								<ul>
									<li><a href="https://support.google.com/chrome/answer/95647">Chrome</a></li>
									<li><a href="https://support.mozilla.org/it/kb/protezione-antitracciamento-avanzata-firefox-desktop">Firefox</a></li>
									<li><a href="https://support.apple.com/it-it/guide/safari/sfri11471/mac">Safari</a></li>
									<li><a href="https://support.microsoft.com/it-it/microsoft-edge/eliminare-i-cookie-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09">Edge</a></li>
									<li><a href="https://help.opera.com/en/latest/web-preferences/#cookies">Opera</a></li>
								</ul>
							</div>
						</div>
						<div class="text-image-cta d-flex mb-0"><div class="content w-100">
							<h3 class="mb-3"><?php echo __( 'Gestione cookies', 'design_laboratori_italia' ); ?></h3></div>
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
					</article>

					<!-- NEWS ED EVENTI (related_items) -->
					<?php
						if ( $related_items ){
							get_template_part(
								'template-parts/common/sezione-related-items',
								null,
								array(
									'related_items' => $related_items,
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
