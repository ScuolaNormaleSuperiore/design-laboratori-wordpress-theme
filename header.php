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

check_mandatory_plugins();

require_once get_template_directory() . '/inc/walkers/main-menu-walker.php';
require_once get_template_directory() . '/inc/walkers/menu-right-walker.php';

$locations    = get_nav_menu_locations();
$current_lang = dli_current_language();
?>
<!doctype html>
<html lang="<?php echo $current_lang; ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
	<!-- ANALYTICS CODE -->
	<?php get_template_part( 'template-parts/header/analytics' ); ?>
	<!-- META TAGS -->
	<?php get_template_part( 'template-parts/header/metatags' ); ?>
	<!-- SEO - OG Internal Management -->
	<?php get_template_part( 'template-parts/header/seo_tags' ); ?>
	<!-- FAVICON -->
	<link rel="icon" href="<?php echo get_template_directory_uri() . '/assets/img/favicon.ico'; ?> " />
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

<!-- HEADER -->
<header class="it-header-wrapper" data-bs-target="#header-nav-wrapper" role="navigation">
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
						<!-- TOP MENU -->
						<?php
							echo get_template_part(
								'template-parts/header/top-menu',
								false,
								array(
									// 'current_group' => $current_group,
									'locations'     => $locations,
								)
							);
						?>
					</div>
					<div class="it-header-slim-right-zone">
						<!-- Language selector -->
						<?php	
							$selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
							$current_language   = dli_current_language( 'slug' );
							if ( 'true' === $selettore_visibile ) {
							$languages_list = dli_languages_list( array( 'hide_empty' => 0, 'fields' => 'slug' ) );
						?>
						<div class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
							<span class="visually-hidden"><?php echo __( 'Selezione lingua: lingua selezionata', 'design_laboratori_italia' );?></span>
							<span><?php echo $current_language; ?></span>
							<svg class="icon d-none d-lg-block" role="img" aria-labelledby="Expand">
								<title>Expand</title>
								<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand'; ?>"></use>
							</svg>
							</a>
							<div class="dropdown-menu">
							<div class="row">
								<div class="col-12">
								<div class="link-list-wrapper">
									<ul class="link-list">
									<?php
										$selectors = dli_get_page_selectors();
										foreach ( $selectors as $selector ) {
									?>
										<li>
											<a class="dropdown-item list-item" href="<?php echo $selector['url'] ?>">
												<span>
													<?php echo $selector['slug'] ?>
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
							<a class="btn btn-primary btn-sm" href="<?php echo get_site_url();?>/admin"><?php echo __( 'Accedi', 'design_laboratori_italia' ); ?></a>
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
						<a href="<?php echo dli_homepage_url(); ?>">
							<!-- header logo -->
							<?php get_template_part( 'template-parts/common/logo' ); ?>
							<div class="it-brand-text ms-4">
							<div class="it-brand-title"><h1 class="h3"><?php echo dli_get_option( 'nome_laboratorio' ); ?></h1></div>
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
								<svg class="icon" role="img" aria-labelledby="Search" aria-label="<?php echo __( 'Cerca nel sito', 'design_laboratori_italia' ); ?>">
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
					<svg class="icon bg-override" role="img" aria-labelledby="Burger">
					<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-burger'; ?>"></use>
					</svg>
				</button>
				<div class="navbar-collapsable" id="nav1" style="display: none;">
					<div class="overlay" style="display: none;"></div>
					<div class="close-div">
					<button class="btn close-menu" type="button">
						<span class="visually-hidden">Nascondi la navigazione</span>
						<svg class="icon" role="img" aria-labelledby="Close big" aria-label="Nascondi la navigazione">
						<use href="<?php echo get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close-big'; ?>"></use>
						</svg>
					</button>
					</div>
					<div class="menu-wrapper" id="menu-principale">
						<!-- MAIN MENU -->
						<?php
							echo get_template_part(
								'template-parts/header/main-menu',
								false,
								array(
									// 'current_group' => $current_group,
									'locations'     => $locations,
								)
							);
						?>
						<!-- SECONDARY MENU -->
						<?php
							echo get_template_part(
								'template-parts/header/secondary-menu',
								false,
								array(
									// 'current_group' => $current_group,
									'locations'     => $locations,
								)
							);
						?>
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
