<div id="pre-header" class="bg-petrol it-header-slim-wrapper">
	<div class="container">
		<div class="row variable-gutters">
			<div class="col-12">
			<div class="it-header-slim-wrapper-content">
				<a href="https://www.sns.it/" target="_blank" aria-label="Scuola Normale Superiore - Collegamento esterno - Apre su nuova scheda">
					<strong>Scuola Normale Superiore</strong>
				</a>
				<div class="nav-mobile">
				<nav aria-label="Navigazione accessoria">
					<a class="it-opener d-lg-none" data-bs-toggle="collapse" href="#menu1a" role="button" aria-expanded="false" aria-controls="menu4">
					<span>Ente appartenenza</span>
					<svg class="icon" aria-hidden="true">
						<use href="bootstrap-italia/svg/sprites.svg#it-expand"></use>
					</svg>
					</a>
					<div class="link-list-wrapper collapse" id="menu1a">
					<?php
						$locations = get_nav_menu_locations();
						$menu_name = 'menu-header-right';
						$menu_items = array();
						if ( has_nav_menu( $menu_name ) ) {
							$menu       = wp_get_nav_menu_object( $locations[ $menu_name ] );
							$menuitems  = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
					?>
						<ul class="link-list">
							<?php
							foreach ( $menuitems as $item ) {
							?>
							<li><a class="list-item" href="<?php echo $item->url; ?>" data-focus-mouse="false"><?php echo $item->post_title; ?></a></li>
							<?php
							}
						?>
						</ul>
					<?php
						}
					?>
					</div>
				</nav>
				</div>
				<div class="it-header-slim-right-zone">
				<div class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
					<span class="visually-hidden">Selezione lingua: lingua selezionata</span>
					<span>ITA</span>
					<svg class="icon d-none d-lg-block">
						<use href="bootstrap-italia/svg/sprites.svg#it-expand"></use>
					</svg>
					</a>
					<div class="dropdown-menu">
					<div class="row">
						<div class="col-12">
						<div class="link-list-wrapper">
							<ul class="link-list">
							<li><a class="dropdown-item list-item" href="#"><span>ITA <span class="visually-hidden">selezionata</span></span></a></li>
							<li><a class="dropdown-item list-item" href="#"><span>ENG</span></a></li>
							</ul>
						</div>
						</div>
					</div>
					</div>
				</div>
				<div class="it-access-top-wrapper">
				<div class="header-utils">
					<?php
					if( ! is_user_logged_in() ) {
						get_template_part( 'template-parts/header/header-anon' );
					}else{
						get_template_part( 'template-parts/header/header-logged' );
					}
				?>
				</div><!-- /header-utils -->
				</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<!-- OLD VERSION -->

<div id="pre-header" class="bg-petrol">
	<div class="container">
		<div class="row variable-gutters">
			<div class="col-6">
				<a href="https://www.sns.it/" target="_blank" aria-label="Scuola Normale Superiore - Collegamento esterno - Apre su nuova scheda">
					<strong>Scuola Normale Superiore</strong>
				</a>
			</div><!-- /col-6 -->
			<div class="col-6 header-utils-wrapper">
				<div class="header-utils">
					<?php
					if( ! is_user_logged_in() ) {
						get_template_part( 'template-parts/header/header-anon' );
					}else{
						get_template_part( 'template-parts/header/header-logged' );
					}
					?>
				</div><!-- /header-utils -->
			</div><!-- /col-6 -->
		</div><!-- /row -->
	</div><!-- /container -->
</div>
<?php
