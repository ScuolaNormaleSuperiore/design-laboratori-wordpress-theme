<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Design_Laboratori_Italia
 */

/** Header_Mobile_Menu class */
require_once get_template_directory() . '/walkers/mobile-header-walker.php';

$theme_locations = get_nav_menu_locations();
?>
<!doctype html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php 
	$active_page = dsi_get_current_group();
	get_template_part("template-parts/common/svg"); 
?>

<!-- Right menu element-->
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right perfect-scrollbar">
	<div class="menu-user-mobile menu-user-blue">
	</div>
</nav>
<!-- End Right menu element-->

<?php

if(is_search() || is_archive())
	get_template_part("template-parts/header/search-filters");
?>


<?php $active_page = dsi_get_current_group(); ?>

<!-- HEADER -->
<div class="it-header-wrapper">
	<div class="it-header-slim-wrapper">
		<div class="container">
		<div class="row">
			<div class="col-12">
			<div class="it-header-slim-wrapper-content">
				<a class="d-none d-lg-block navbar-brand" href="https://www.sns.it/" target="_blank">Scuola Normale Superiore</a>
				<div class="nav-mobile">
				<nav aria-label="Navigazione accessoria">
					<a class="it-opener d-lg-none" data-bs-toggle="collapse" href="#menu1a" role="button"
					aria-expanded="false" aria-controls="menu4">
					<span>Ente appartenenza</span>
					<svg class="icon" aria-hidden="true">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
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
							<li><a class="list-item" href="<?php echo $item->url ?>" aria-current="page"><?php echo $item->title;?></a></li>
							<?php
							}
							?>
						</ul>
						<?php
							}
						?>
					</div> <!-- menu1a -->
				</nav>
				</div>
				<div class="it-header-slim-right-zone">
				<div class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
					<span class="visually-hidden">Selezione lingua: lingua selezionata</span>
					<span>ITA</span>
					<svg class="icon d-none d-lg-block">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
					</svg>
					</a>
					<div class="dropdown-menu">
					<div class="row">
						<div class="col-12">
						<div class="link-list-wrapper">
							<ul class="link-list">
							<li><a class="dropdown-item list-item" href="#"><span>ITA <span
									class="visually-hidden">selezionata</span></span></a></li>
							<li><a class="dropdown-item list-item" href="#"><span>ENG</span></a></li>
							</ul>
						</div>
						</div>
					</div>
					</div>
				</div>
				<div class="it-access-top-wrapper">
					<a class="btn btn-primary btn-sm" href="#">Accedi</a>
				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
	<div class="it-nav-wrapper">
		<div class="it-header-center-wrapper theme-light">
		<div class="container">
			<div class="row">
			<div class="col-12">
				<div class="it-header-center-content-wrapper">
				<div class="it-brand-wrapper">
					<a href="sf-index.html">
					<?php get_template_part( 'template-parts/common/logo' ); ?>
					<div class="it-brand-text">
						<div class="it-brand-title">DocStAr</div>
						<div class="it-brand-tagline d-none d-md-block">Laboratorio di documentazione storico artistica</div>
					</div>
					</a>
				</div>
				<div class="it-right-zone">
					<div class="it-socials d-none d-md-flex">
					<span>Seguici su</span>
					<ul>
						<li>
						<a href="#" aria-label="Facebook" target="_blank">
							<svg class="icon">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-facebook'; ?>"></use>
							</svg>
						</a>
						</li>
						<li>
						<a href="#" aria-label="Github" target="_blank">
							<svg class="icon">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-github'; ?>"></use>
							</svg>
						</a>
						</li>
						<li>
						<a href="#" aria-label="Twitter" target="_blank">
							<svg class="icon">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-twitter'; ?>"></use>
							</svg>
						</a>
						</li>
					</ul>
					</div>
					<div class="it-search-wrapper">
					<span class="d-none d-md-block">Cerca</span>
					<a class="search-link rounded-icon" aria-label="Cerca nel sito" href="#">
						<svg class="icon">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search'; ?>"></use>
						</svg>
					</a>
					</div>
				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
		<div class="it-header-navbar-wrapper theme-light-desk">
		<div class="container">
			<div class="row">
			<div class="col-12">
				<!--start nav-->
				<nav class="navbar navbar-expand-lg has-megamenu" aria-label="Navigazione principale">
				<button class="custom-navbar-toggler" type="button" aria-controls="nav1" aria-expanded="false"
					aria-label="Mostra/Nascondi la navigazione" data-bs-toggle="navbarcollapsible" data-bs-target="#nav1">
					<svg class="icon bg-override">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-burger'; ?>"></use>
					</svg>
				</button>
				<div class="navbar-collapsable" id="nav1" style="display: none;">
					<div class="overlay" style="display: none;"></div>
					<div class="close-div">
					<button class="btn close-menu" type="button">
						<span class="visually-hidden">Nascondi la navigazione</span>
						<svg class="icon">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close-big'; ?>"></use>
						</svg>
					</button>
					</div>
					<div class="menu-wrapper">
					<ul class="navbar-nav">
						
						
						<li class="nav-item active"><a class="nav-link" href="persone.html" aria-current="Persone"><span>Persone</span></a></li>
						<li class="nav-item"><a class="nav-link " href="#"><span>Progetti</span></a></li>
						<li class="nav-item"><a class="nav-link" href="#"><span>Attività di ricerca</span></a></li>
						<li class="nav-item"><a class="nav-link" href="#"><span>Pubblicazioni</span></a></li>
					
						</ul>
					<ul class="navbar-nav navbar-secondary">
						<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"
							id="mainNavDropdown1">
							<span>Laboratorio Drop</span>
							<svg class="icon icon-xs">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
							</svg>
						</a>
						<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown1">
							<div class="link-list-wrapper">
							<div class="link-list-heading">Sezione</div>
							<ul class="link-list">
								<li><a class="dropdown-item list-item" href="#"><span>Link lista 1</span></a></li>
								<li><a class="dropdown-item list-item" href="#"><span>Link lista 2</span></a></li>
								<li><a class="dropdown-item list-item" href="#"><span>Link lista 3</span></a></li>
								<li><span class="divider"></span></li>
								<li><a class="dropdown-item list-item" href="#"><span>Link lista 4</span></a></li>
							</ul>
							</div>
						</div>
						</li>
					
		
						<li class="nav-item dropdown megamenu">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false"
							id="mainNavMegamenu1">
							<span>Laboratorio MM</span>
							<svg class="icon icon-xs">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
							</svg>
						</a>
						<div class="dropdown-menu" role="region" aria-labelledby="mainNavMegamenu1">
							<div class="row">
							<div class="col-12 col-lg-4">
								<div class="link-list-wrapper">
								<div class="link-list-heading">Sezione 1</div>
								<ul class="link-list">
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 1</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 2</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 3</span></a></li>
								</ul>
								</div>
							</div>
							<div class="col-12 col-lg-4">
								<div class="link-list-wrapper">
								<ul class="link-list">
									<li>
									<div class="link-list-heading">Sezione 2</div>
									</li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 4</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 5</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 6</span></a></li>
								</ul>
								</div>
							</div>
							<div class="col-12 col-lg-4">
								<div class="link-list-wrapper">
								<ul class="link-list">
									<li>
									<div class="link-list-heading">Sezione 3</div>
									</li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 7</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 8</span></a></li>
									<li><a class="dropdown-item list-item" href="#"><span>Link lista 9</span></a></li>
								</ul>
								</div>
							</div>
							</div>
						</div>
						</li>

					</ul>
					</div>
				</div>
				</nav>
				
			</div>
			</div>
		</div>
		</div>
	</div>
	</div>
<!-- END HEADER -->
<DIV style="width: 100%; height: 100px; border: 1px solid red;">&nbsp;</DIV>




<div id="main-wrapper" class="push_container" id="page_top">
	<?php get_template_part("template-parts/common/skiplink"); ?>
	<header id="main-header" class="bg-white">
		<?php get_template_part("template-parts/header/slimheader"); ?>
		<div class="container header-top">
			<div class="row variable-gutters">
				<div class="col-8 d-flex align-items-center">
					<button class="hamburger hamburger--spin-r toggle-menu menu-left push-body d-xl-none" type="button" aria-label="apri chiudi navigazione">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
					<!-- Left menu element-->
					<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left perfect-scrollbar">
						<div class="logo-header">
							<?php get_template_part("template-parts/common/logo"); ?>
							<div class="h1">
								<a href="<?php echo home_url(); ?>">
									<span><?php echo dli_get_option("tipologia_scuola"); ?></span>
									<span><strong><?php echo dli_get_option("nome_laboratorio"); ?></strong></span>
									<span class="d-none d-lg-block"><?php echo dli_get_option("luogo_laboratorio"); ?></span>
								</a>
							</div>
						</div><!-- /logo-header -->
						<div class="nav-list-mobile dl-menuwrapper">
							<nav aria-label="Principale">
								<ul class="dl-menu nav-list nav-list-primary" data-element="menu">
									<?php
									// check if scuola has menu.
									$theme_location = "menu-lab";
									$option_location = "item_menu_lab";
									unset($menu_obj);
									if(isset($theme_locations[$theme_location]))
										$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
									if(isset($menu_obj) && !is_wp_error($menu_obj)) {
										$landing_url = dli_get_template_page_url("page-templates/il-laboratorio.php");
										if($landing_url)
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown1" data-element="school-submenu" class="%2$s"><li class="menu-title" ><div class="h3"><a href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
										else
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown1" data-element="school-submenu" class="%2$s">%3$s</ul>';

										?>
										<li class="text-redbrown menu-dropdown-simple-wrapper">
											<a class="toggle-dropdown toggle-dropdown-simple <?php echo $active_page == 'school' ? 'active' : ''?>" role="button" href="#" aria-expanded="false" id="mainNavDropdown1" title="Vai alla pagina: <?php _e("Laboratorio",'design_laboratori_italia'); ?>"><?php _e("Laboratorio",'design_laboratori_italia'); ?></a>
											<?php wp_nav_menu(array(
												"menu" => $menu_obj, 
												"items_wrap" => $items_wrap,
												"depth" => 1, 
												"menu_class" => "menu-dropdown dl-submenu menu-dropdown-simple",
												"container" => "", 
												"walker" => new Mobile_Header_Menu_Walker()
												)) ?>
										</li>
										<?php
									}
									?>
									<?php
									// check if servizi has menu
									$theme_location = "menu-servizi";
									$option_location = "item_menu_servizi";
									unset($menu_obj);
									if(isset($theme_locations[$theme_location]))
										$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
									if(isset($menu_obj) && !is_wp_error($menu_obj)) {
										$landing_url = dli_get_template_page_url("page-templates/servizi.php");
										if($landing_url)
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown2" id="%1$s" class="%2$s"><li class="menu-title" ><div class="h3"><a href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
										else
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown2" id="%1$s" class="%2$s">%3$s</ul>';

										?>
										<li class="text-purplelight menu-dropdown-simple-wrapper">
											<a class="toggle-dropdown toggle-dropdown-simple <?php echo $active_page == 'service' ? 'active' : ''?>" role="button" href="#" aria-expanded="false" id="mainNavDropdown2" title="Vai alla pagina: <?php _e("Servizi",'design_laboratori_italia'); ?>"><?php _e("Servizi",'design_laboratori_italia'); ?></a>
											<?php wp_nav_menu(array(
												"menu" => $menu_obj, 
												"items_wrap" => $items_wrap,
												"depth" => 1, 
												"menu_class" => "menu-dropdown dl-submenu menu-dropdown-simple",
												"container" => "", 
												"walker" => new Mobile_Header_Menu_Walker()
											)) ?>
										</li>
										<?php

									}
									?>
									<?php
									// check if notizie has menu
									$theme_location = "menu-notizie";
									$option_location = "item_menu_notizie";
									unset($menu_obj);
									if(isset($theme_locations[$theme_location]))
										$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
									if(isset($menu_obj) && !is_wp_error($menu_obj)) {
										$landing_url = dli_get_template_page_url("page-templates/notizie.php");
										if($landing_url)
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown3" id="%1$s" class="%2$s"><li class="menu-title" ><div class="h3"><a href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
										else
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown3" id="%1$s" class="%2$s">%3$s</ul>';

										?>
										<li class="text-greendark menu-dropdown-simple-wrapper">
											<a class="toggle-dropdown toggle-dropdown-simple <?php echo $active_page == 'news' ? 'active' : ''?>" role="button" href="#" aria-expanded="false" id="mainNavDropdown3" title="Vai alla pagina: <?php _e("Novità",'design_laboratori_italia'); ?>"><?php _e("Novità",'design_laboratori_italia'); ?></a>
											<?php wp_nav_menu(array(
												"menu" => $menu_obj, 
												"items_wrap" => $items_wrap,
												"depth" => 1, 
												"menu_class" => "menu-dropdown dl-submenu menu-dropdown-simple",
												"container" => "", 
												"walker" => new Mobile_Header_Menu_Walker()
											)) ?>
										</li>
										<?php
									}
									?>
									<?php
									// check if didattica has menu
									$theme_location = "menu-didattica";
									$option_location = "item_menu_didattica";
									unset($menu_obj);
									if(isset($theme_locations[$theme_location]))
										$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
									if(isset($menu_obj) && !is_wp_error($menu_obj)) {
										$landing_url = dli_get_template_page_url("page-templates/didattica.php");
										if($landing_url)
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown4" id="%1$s" class="%2$s"><li class="menu-title" ><div class="h3"><a href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
										else
											$items_wrap = '<ul  aria-labelledby="mainNavDropdown4" id="%1$s" class="%2$s">%3$s</ul>';

										?>
										<li class="text-bluelectric menu-dropdown-simple-wrapper">
											<a class="toggle-dropdown toggle-dropdown-simple <?php echo $active_page == 'education' ? 'active' : ''?>" role="button" href="#" aria-expanded="false" id="mainNavDropdown4" title="Vai alla pagina: <?php _e("Didattica",'design_laboratori_italia'); ?>"><?php _e("Didattica",'design_laboratori_italia'); ?></a>
											<?php wp_nav_menu(array(
												"menu" => $menu_obj, 
												"items_wrap" => $items_wrap,
												"depth" => 1, 
												"menu_class" => "menu-dropdown dl-submenu menu-dropdown-simple",
												"container" => "", 
												"walker" => new Mobile_Header_Menu_Walker()
											)) ?>
										</li>
										<?php
									}
									?>
								</ul><!-- /nav-list -->
							</nav>
							<?php
							$location = "menu-right";
							if ( has_nav_menu( $location ) ) {
								echo '<nav aria-label="Argomenti">';
								wp_nav_menu(array("theme_location" => $location, "depth" => 1,  "menu_class" => "nav-list nav-list-secondary", "container" => ""));
								echo '</nav>';
							}
							?>
						</div>
					</div>
					<!-- End Left menu element-->
					<div class="logo-header">
			<?php get_template_part("template-parts/common/logo"); ?>
						<div class="h1">
							<a href="<?php echo home_url(); ?>" aria-label="Vai alla homepage" title="vai alla homepage" >
								<span><?php echo dli_get_option("tipologia_scuola"); ?></span>
								<span><strong><?php echo dli_get_option("nome_laboratorio"); ?></strong></span>
								<span class="d-none d-lg-block"><?php echo dli_get_option("luogo_laboratorio"); ?></span>
							</a>
						</div>
					</div><!-- /logo-header -->
					<div class="sticky-main-nav">

					</div><!-- /sticky-main-nav -->

				</div><!-- /col -->
				<div class="col-4 d-flex align-items-center justify-content-end">
					<div class="header-search d-flex align-items-center">
						<button type="button" class="d-flex align-items-center search-btn" data-toggle="modal" data-target="#search-modal" aria-label="Cerca nel sito" data-element="search-modal-button">
							<span class="d-none d-lg-block mr-2"><strong>Cerca</strong></span>
							<svg class="svg-search">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-search"></use>
							</svg>
						</button>
					</div><!-- /header-search -->
					<div class="header-utils-sticky">
					</div>
					<?php
					$show_socials = dli_get_option( "show_socials", "socials" );
					if($show_socials == "true") : ?>
					<div class="header-social">
						<?php
						if (1==0) {
						?>
						<span>Seguici su:</span>
						<div class="header-social-wrapper">
						<?php if($facebook = dli_get_option( "facebook", "socials" )) :?><a href="<?php echo $facebook; ?>" aria-label="facebook" title="vai alla pagina facebook"><svg class="icon it-social-facebook"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-social-facebook"></use></svg></a><?php endif; ?>
							<?php if($youtube = dli_get_option( "youtube", "socials" )) :?><a href="<?php echo $youtube; ?>" aria-label="youtube" title="vai alla pagina youtube"><svg class="icon it-social-youtube"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-social-youtube"></use></svg></a><?php endif; ?>
							<?php if($instagram = dli_get_option( "instagram", "socials" )) :?><a href="<?php echo $instagram; ?>" aria-label="instagram" title="vai alla pagina instagram"><svg class="icon it-social-instagram"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-social-instagram"></use></svg></a><?php endif; ?>
							<?php if($twitter = dli_get_option( "twitter", "socials" )) :?><a href="<?php echo $twitter; ?>" aria-label="twitter" title="vai alla pagina twitter"><svg class="icon it-social-twitter"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-social-twitter"></use></svg></a><?php endif; ?>
							<?php if($linkedin = dli_get_option( "linkedin", "socials" )) :?><a href="<?php echo $linkedin; ?>" aria-label="linkedin" title="vai alla pagina linkedin"><svg class="icon it-social-linkedin"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#it-social-linkedin"></use></svg></a><?php endif; ?>
						</div><!-- /header-social-wrapper -->
						<?php
						}
						?>
					</div><!-- /header-social -->
					<?php endif ?>
				</div><!-- /col -->
			</div><!-- /row -->
		</div><!-- /container -->

		<div class="bg-white d-none d-xl-block header-bottom" id="sub-nav">
			<div class="container">
				<div class="row variable-gutters">
					<div class="col nav-container">
						<nav aria-label="Principale" class="main-nav" id="menu-principale">
							<ul class="dl-menu nav-list nav-list-primary" data-element="menu">
								<?php
								// check if scuola has menu
								$theme_location = "menu-lab";
								$option_location = "item_menu_lab";
								unset($menu_obj);
								if(isset($theme_locations[$theme_location]))
									$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
								if(isset($menu_obj) && !is_wp_error($menu_obj)) {
									$landing_url = dli_get_template_page_url("page-templates/il-laboratorio.php");
									if($landing_url)
										$items_wrap = '<ul class="%2$s" data-element="school-submenu"><li class="menu-title" ><div class="h3"><a class="list-item" href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
									else
										$items_wrap = '<ul class="%2$s" data-element="school-submenu">%3$s</ul>';

									?>
									<li class="text-redbrown menu-dropdown-simple-wrapper">
										<a class="nav-link dropdown-toggle <?php echo $active_page == 'school' ? 'active' : ''?>" data-toggle="dropdown"  role="button" href="#" aria-expanded="false" id="mainNavDropdown1"><?php _e("Laboratorio",'design_laboratori_italia'); ?></a>
										<div class="dropdown-menu menu-dropdown dl-submenu menu-dropdown-simple"  aria-labelledby="mainNavDropdown1">
											<div class="link-list-wrapper">
												<?php wp_nav_menu(array(
													"menu" => $menu_obj, 
													"items_wrap" => $items_wrap,
													"depth" => 1, 
													"menu_class" => "link-list", 
													"container" => "", 
													"walker" => new Header_Menu_Walker()
												)) ?>
											</div>
										</div>
									</li>
									<?php
								}
								?>
								<?php
								// check if servizi has menu
								$theme_location = "menu-servizi";
								$option_location = "item_menu_servizi";
								unset($menu_obj);
								if(isset($theme_locations[$theme_location]))
									$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
								if(isset($menu_obj) && !is_wp_error($menu_obj)) {
									$landing_url = dli_get_template_page_url("page-templates/servizi.php");
									if($landing_url)
										$items_wrap = '<ul class="%2$s"><li class="menu-title" ><div class="h3"><a class="list-item" href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
									else
										$items_wrap = '<ul class="%2$s">%3$s</ul>';

									?>
									<li class="text-purplelight menu-dropdown-simple-wrapper">
										<a class="nav-link dropdown-toggle <?php echo $active_page == 'service' ? 'active' : ''?>" data-toggle="dropdown"  role="button" href="#" aria-expanded="false" id="mainNavDropdown2"><?php _e("Servizi",'design_laboratori_italia'); ?></a>
										<div class="dropdown-menu menu-dropdown dl-submenu menu-dropdown-simple"  aria-labelledby="mainNavDropdown2">
											<div class="link-list-wrapper">
												<?php wp_nav_menu(array(
													"menu" => $menu_obj, 
													"items_wrap" => $items_wrap,
													"depth" => 1, 
													"menu_class" => "link-list", 
													"container" => "", 
													"walker" => new Header_Menu_Walker()
												)) ?>
											</div>
										</div>
									</li>
									<?php

								}
								?>
								<?php
								// check if notizie has menu
								$theme_location = "menu-notizie";
								$option_location = "item_menu_notizie";
								unset($menu_obj);
								if(isset($theme_locations[$theme_location]))
									$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
								if(isset($menu_obj) && !is_wp_error($menu_obj)) {
									$landing_url = dli_get_template_page_url("page-templates/notizie.php");
									if($landing_url)
										$items_wrap = '<ul class="%2$s"><li class="menu-title" ><div class="h3"><a class="list-item" href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
									else
										$items_wrap = '<ul class="%2$s">%3$s</ul>';

									?>
									<li class="text-greendark menu-dropdown-simple-wrapper">
										<a class="nav-link dropdown-toggle <?php echo $active_page == 'news' ? 'active' : ''?>" data-toggle="dropdown"  role="button" href="#" aria-expanded="false" id="mainNavDropdown3"><?php _e("Novità",'design_laboratori_italia'); ?></a>
										<div class="dropdown-menu menu-dropdown dl-submenu menu-dropdown-simple"  aria-labelledby="mainNavDropdown3">
											<div class="link-list-wrapper">
												<?php wp_nav_menu(array(
													"menu" => $menu_obj, 
													"items_wrap" => $items_wrap,
													"depth" => 1, 
													"menu_class" => "link-list", 
													"container" => "", 
													"walker" => new Header_Menu_Walker()
												)) ?>
											</div>
										</div>
									</li>
									<?php
								}
								?>
								<?php
								// check if didattica has menu
								$theme_location = "menu-didattica";
								$option_location = "item_menu_didattica";
								unset($menu_obj);
								if(isset($theme_locations[$theme_location]))
									$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
								if(isset($menu_obj) && !is_wp_error($menu_obj)) {
									$landing_url = dli_get_template_page_url("page-templates/didattica.php");
									if($landing_url)
										$items_wrap = '<ul class="%2$s"><li class="menu-title" ><div class="h3"><a class="list-item" href="'.$landing_url.'">'.__("Panoramica", 'design_laboratori_italia').'</a></div></li>%3$s</ul>';
									else
										$items_wrap = '<ul class="%2$s">%3$s</ul>';

									?>
									<li class="text-bluelectric menu-dropdown-simple-wrapper">
										<a class="nav-link dropdown-toggle <?php echo $active_page == 'education' ? 'active' : ''?>" data-toggle="dropdown"  role="button" href="#" aria-expanded="false" id="mainNavDropdown4"><?php _e("Didattica",'design_laboratori_italia'); ?></a>
										<div class="dropdown-menu menu-dropdown dl-submenu menu-dropdown-simple"  aria-labelledby="mainNavDropdown4">
											<div class="link-list-wrapper">
												<?php wp_nav_menu(array(
													"menu" => $menu_obj, 
													"items_wrap" => $items_wrap,
													"depth" => 1, 
													"menu_class" => "link-list", 
													"container" => "", 
													"walker" => new Header_Menu_Walker()
												)) ?>
											</div>
										</div>
									</li>
									<?php
								}
								?>
							</ul><!-- /nav-list -->
						</nav>
						<?php
						$location = "menu-right";
						if ( has_nav_menu( $location ) ) {
							echo '<nav aria-label="Argomenti">';
							wp_nav_menu(array("theme_location" => $location, "depth" => 1,  "menu_class" => "nav-list nav-list-secondary", "container" => ""));
							echo '</nav>';
						}
						?>

					</div><!-- /col nav-container -->
				</div><!-- /row -->
			</div><!-- /container -->
		</div><!-- /sub-nav -->


	</header><!-- /header -->

	<?php get_template_part("template-parts/common/search-modal"); ?>
	<?php
	if(!is_user_logged_in())
		get_template_part("template-parts/common/access-modal");
	?>

