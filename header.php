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
// require_once get_template_directory() . '/walkers/mobile-header-walker.php';
require_once get_template_directory() . '/walkers/main-menu-walker.php';
require_once get_template_directory() . '/walkers/menu-right-walker.php';

$theme_locations = get_nav_menu_locations();
$current_group   = dli_get_current_group();
?>
<!doctype html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
	<?php get_template_part( 'template-parts/header/analytics' ); ?>
</head>
<body <?php body_class(); ?>>

<?php 
	get_template_part( 'template-parts/common/svg' );
	get_template_part( 'template-parts/common/sprites' );
	get_template_part( 'template-parts/common/skiplink' );
?>

<!-- Right menu element-->
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right perfect-scrollbar">
	<div class="menu-user-mobile menu-user-blue">
	</div>
</nav>
<!-- End Right menu element-->




<?php $active_page = dli_get_current_group(); ?>

<!-- HEADER -->
<header class="it-header-wrapper" data-bs-target="#header-nav-wrapper">
	<div class="it-header-slim-wrapper">
		<div class="container">
		<div class="row">
			<div class="col-12">
			<!-- SLIM HEADER -->
			<div class="it-header-slim-wrapper-content">
				<a class="d-none d-lg-block navbar-brand" 
					href="<?php echo dli_get_option( 'url_ente_appartenenza' ); ?>" 
					target="_blank"><?php echo dli_get_option( 'nome_ente_appartenza' ); ?></a>
				<div class="nav-mobile">
				<nav aria-label="Navigazione accessoria">
					<a class="it-opener d-lg-none" data-bs-toggle="collapse" href="#menu1a" role="button"
					aria-expanded="false" aria-controls="menu4">
					<span><?php echo __( 'News e contatti', 'design_laboratori_italia');?></span>
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
							$menuitems  = $menuitems  ? $menuitems : array();
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
					<!-- Language selector -->
					<?php	
						$selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
						$current_language   = dli_current_language( 'slug' );
						if ( 'true' === $selettore_visibile ) {
						$languages_list = dli_languages_list( array( 'hide_empty'=>0, 'fields'=>'slug' ) );
					?>
					<div class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
						<span class="visually-hidden"><?php echo __( 'Selezione lingua: lingua selezionata', 'design_laboratori_italia');?></span>
						<span><?php echo $current_language; ?></span>
						<svg class="icon d-none d-lg-block">
							<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
						</svg>
						</a>
						<div class="dropdown-menu">
						<div class="row">
							<div class="col-12">
							<div class="link-list-wrapper">
								<ul class="link-list">
								<?php
									foreach ( $languages_list as $lang_slug ) {
										$site_url = ('it' === $lang_slug) ? get_site_url() : get_site_url() . '/' . $lang_slug;
								?>
									<li>
										<a class="dropdown-item list-item" href="<?php echo $site_url; ?>">
											<span>
												<?php echo $lang_slug; ?>
											</span>
										</a>
									</li>
								<?php
									}
								?>
								</ul>
							</div>
							</div>
						</div>
						</div>
					</div>
					<?php
						}
						$login_visible = dli_get_option( 'login_button_visible', 'setup' );
						if ( $login_visible === 'true' ) {
					?>
					<div class="it-access-top-wrapper">
						&nbsp;&nbsp;
						<a class="btn btn-primary btn-sm" href="#">Accedi</a>
					</div>
					<?php
						}
					?>
				</div>
			</div>
			<!-- End SLIM HEADER -->
			</div>
		</div>
		</div>
	</div>
	<div class="it-nav-wrapper">
		<div class="it-header-center-wrapper theme-light">
		<div class="container">
			<div class="row">
			<div class="col-12">
				<!-- TITLE ROW -->
				<div class="it-header-center-content-wrapper">
					<div class="it-brand-wrapper">
						<a href="<?php echo get_site_url(); ?>">
							<!-- header logo -->
							<?php get_template_part( 'template-parts/common/logo' ); ?>
							<div class="it-brand-text">
							<div class="it-brand-title"><strong><?php echo dli_get_option( 'nome_laboratorio' ); ?></strong></div>
							<div class="it-brand-tagline d-none d-md-block"><?php echo dli_get_option( 'tagline_laboratorio' ); ?></div>
							</div>
						</a>
					</div>
					<div class="it-right-zone">
						<div class="it-socials d-none d-md-flex">
							<?php get_template_part( 'template-parts/common/social' ); ?>
						</div>
						<div class="it-search-wrapper">
							<span class="d-none d-md-block"><?php echo __( 'Cerca', 'design_laboratori_italia' ); ?></span>
							<?php
								$search_link = dli_get_search_link( $current_language );
							?>
							<a class="search-link rounded-icon" aria-label="<?php echo __( 'Cerca nel sito', 'design_laboratori_italia' ); ?>" href="<?php echo esc_url( $search_link ); ?>">
								<svg class="icon">
									<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search'; ?>"></use>
								</svg>
							</a>
						</div>
					</div> <!-- End it-right-zone -->
				</div>
			</div>
			<!-- End TITLE ROW -->
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
					<div class="menu-wrapper" id="menu-principale">
						<!-- MAIN MENU -->
						<nav aria-label="Principale">
							<?php
							$location = 'menu-lab';
							if ( has_nav_menu( $location ) ) {
								wp_nav_menu(
									array(
										'theme_location'  => $location,
										'depth'           => 0,
										'menu_class'      => 'navbar-nav',
										'items_wrap'      => '<ul class="%2$s" id="%1$s" data-element="main-navigation">%3$s</ul>',
										'container'       => '',
										'list_item_class' => 'nav-item',
										'link_class'      => 'nav-link',
										'current_group'   => $current_group,
										'walker'          => new Main_Menu_Walker(),
									)
								);
								}
								?>
					</nav>

					<!-- SECONDARY MENU -->
					<nav aria-label="Secondaria">
						<?php
						$location = "menu-right";
						if ( has_nav_menu( $location ) ) {
							wp_nav_menu(
								array(
									'theme_location'  => $location,
									'depth'           => 0,
									'menu_class'      => 'navbar-nav navbar-secondary',
									'container'       => '',
									'list_item_class' => 'nav-item',
									'link_class'      => 'nav-link',
									'current_group'   => $current_group,
									'walker'          => new Main_Menu_Walker(),
								)
							);
						}
						?>
					</nav>

					</div> <!-- menu-wrapper -->
				</div>
				</nav>
			</div>
			</div>
		</div>
		</div>
	</div>
</header>
<!-- END HEADER -->


<!-- ALERT SECTION - AVVISI -->
<?php
	get_template_part( 'template-parts/header/alert' );
?>
