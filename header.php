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

dli_check_mandatory_plugins();

require_once get_template_directory() . '/inc/walkers/class-dli-main-menu-walker.php';

$dli_locations    = get_nav_menu_locations();
$dli_current_lang = dli_current_language();
?>
<!doctype html>
<html lang="<?php echo esc_attr( $dli_current_lang ); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
	<!-- ANALYTICS CODE -->
	<?php get_template_part( 'template-parts/header/analytics' ); ?>
	<!-- META TAGS -->
	<?php get_template_part( 'template-parts/header/metatags' ); ?>
	<!-- SEO - OG Internal Management -->
	<?php get_template_part( 'template-parts/header/seo-tags' ); ?>
	<!-- FAVICON -->
	<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.ico' ); ?>" />
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
	get_template_part( 'template-parts/common/svg' );
	get_template_part( 'template-parts/common/sprites' );
	get_template_part( 'template-parts/common/skiplink' );
?>

<!-- HEADER -->
<header class="it-header-wrapper" data-bs-target="#header-nav-wrapper">
	<div class="it-header-slim-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-12">
				<!-- SLIM HEADER -->
				<div class="it-header-slim-wrapper-content">
						<?php
						$dli_parent_name = dli_get_option( 'nome_ente_appartenza' );
						$dli_parent_url  = dli_get_option( 'url_ente_appartenenza' );
						if ( $dli_parent_name && $dli_parent_url ) {
							?>
						<a class="d-none d-lg-block navbar-brand"
							href="<?php echo esc_url( $dli_parent_url ); ?>"
							target="_blank" rel="noopener noreferrer"><?php echo esc_html( $dli_parent_name ); ?></a>
							<?php
						} elseif ( $dli_parent_name ) {
							?>
						<span class="d-none d-lg-block navbar-brand"><?php echo esc_html( $dli_parent_name ); ?></span>
							<?php
						}
						?>
					<div class="nav-mobile">
						<!-- TOP MENU -->
						<?php
								get_template_part(
									'template-parts/header/top-menu',
									null,
									array(
										'locations' => $dli_locations,
									)
								);
								?>
					</div>
					<div class="it-header-slim-right-zone">
						<!-- Language selector -->
						<?php
								$dli_selettore_visibile = dli_get_option( 'selettore_lingua_visible', 'setup' );
								$dli_current_language   = dli_current_language( 'slug' );
						if ( 'true' === $dli_selettore_visibile ) {
							$dli_languages_list = dli_languages_list(
								array(
									'hide_empty' => 0,
									'fields'     => 'slug',
								)
							);
							?>
						<div class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
							<span class="visually-hidden"><?php echo esc_html__( 'Selezione lingua: lingua selezionata', 'design_laboratori_italia' ); ?></span>
								<span><?php echo esc_html( $dli_current_language ); ?></span>
							<svg class="icon d-none d-lg-block" role="img">
								<title><?php echo esc_html__( 'Expand', 'design_laboratori_italia' ); ?></title>
								<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-expand' ); ?>"></use>
							</svg>
							</a>
							<div class="dropdown-menu">
							<div class="row">
								<div class="col-12">
								<div class="link-list-wrapper">
									<ul class="link-list">
							<?php
									$dli_selectors = dli_get_page_selectors();
							foreach ( $dli_selectors as $dli_selector ) {
								?>
											<li>
												<a class="dropdown-item list-item" href="<?php echo esc_url( $dli_selector['url'] ); ?>">
													<span>
										<?php echo esc_html( $dli_selector['slug'] ); ?>
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
								$dli_login_visible = dli_get_option( 'login_button_visible', 'setup' );
						if ( 'true' === $dli_login_visible ) {
							$dli_header_user         = wp_get_current_user();
							$dli_reserved_area_user  = dli_is_reserved_area_enabled() && dli_user_has_reserved_area_role( $dli_header_user );
							$dli_show_access_control = ! is_user_logged_in() || $dli_reserved_area_user;
							if ( $dli_show_access_control ) {
								?>
								<div class="it-access-top-wrapper">
									<?php if ( $dli_reserved_area_user ) : ?>
										<a class="btn btn-primary btn-icon btn-full" href="<?php echo esc_url( wp_logout_url( dli_get_language_home_url( dli_current_language() ) ) ); ?>" aria-label="<?php echo esc_attr__( 'Esci dal sito', 'design_laboratori_italia' ); ?>">
									<?php else : ?>
										<a class="btn btn-primary btn-icon btn-full" href="<?php echo esc_url( admin_url() ); ?>">
									<?php endif; ?>
									<span class="rounded-icon"<?php echo $dli_reserved_area_user ? ' title="' . esc_attr( $dli_header_user->display_name ) . '"' : ''; ?>>
										<svg class="icon icon-primary" aria-hidden="true" focusable="false">
											<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-user' ); ?>"></use>
										</svg>
									</span>
									<span class="d-none d-lg-block"><?php echo $dli_reserved_area_user ? esc_html__( 'Esci', 'design_laboratori_italia' ) : esc_html__( 'Accedi', 'design_laboratori_italia' ); ?></span>
									</a>
								</div>
								<?php
							}
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
						<a href="<?php echo esc_url( dli_homepage_url() ); ?>">
							<!-- header logo -->
							<?php get_template_part( 'template-parts/common/logo' ); ?>
							<div class="it-brand-text ms-4">
							<h1 class="it-brand-title"><?php echo esc_html( dli_get_option_by_lang( 'nome_laboratorio' ) ); ?></h1>
							<div class="it-brand-tagline d-none d-md-block">
								<?php echo esc_html( dli_get_option_by_lang( 'tagline_laboratorio' ) ); ?>
							</div>
							</div>
						</a>
					</div>
					<div class="it-right-zone">
						<div class="it-socials d-none d-md-flex">
							<?php get_template_part( 'template-parts/common/social' ); ?>
						</div>
						<div class="it-search-wrapper">
							<span class="d-none d-md-block"><?php echo esc_html__( 'Cerca', 'design_laboratori_italia' ); ?></span>
							<?php
									$dli_search_link = dli_get_search_link( $dli_current_language );
							?>
								<a class="search-link rounded-icon" aria-label="<?php echo esc_attr__( 'Cerca nel sito', 'design_laboratori_italia' ); ?>" href="<?php echo esc_url( $dli_search_link ); ?>">
								<svg class="icon" role="img" aria-label="<?php echo esc_attr__( 'Cerca nel sito', 'design_laboratori_italia' ); ?>">
									<title><?php echo esc_html__( 'Cerca nel sito', 'design_laboratori_italia' ); ?></title>
									<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-search' ); ?>"></use>
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
		<?php
		/*
		 * Use the same light navigation bar in every site style so that the
		 * menus remain visually separate from the page hero.
		 */
		?>
		<div class="it-header-navbar-wrapper theme-light-desk">
		<div class="container">
			<div class="row">
			<div class="col-12">
				<!--start nav-->
				<nav class="navbar navbar-expand-lg has-megamenu" aria-label="<?php echo esc_attr__( 'Navigazione principale', 'design_laboratori_italia' ); ?>">
				<button class="custom-navbar-toggler" type="button" aria-controls="nav1" aria-expanded="false"
					aria-label="<?php echo esc_attr__( 'Mostra/Nascondi la navigazione', 'design_laboratori_italia' ); ?>" data-bs-toggle="navbarcollapsible" data-bs-target="#nav1">
					<svg class="icon bg-override" role="img">
						<title><?php echo esc_html__( 'Mostra/Nascondi la navigazione', 'design_laboratori_italia' ); ?></title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-burger' ); ?>"></use>
					</svg>
				</button>
				<div class="navbar-collapsable" id="nav1" style="display: none;">
					<div class="overlay" style="display: none;"></div>
					<div class="close-div">
					<button class="btn close-menu" type="button">
						<span class="visually-hidden"><?php echo esc_html__( 'Nascondi la navigazione', 'design_laboratori_italia' ); ?></span>
						<svg class="icon" role="img" aria-label="<?php echo esc_attr__( 'Nascondi la navigazione', 'design_laboratori_italia' ); ?>">
							<title><?php echo esc_html__( 'Nascondi la navigazione', 'design_laboratori_italia' ); ?></title>
							<use href="<?php echo esc_url( get_template_directory_uri() . '/assets/bootstrap-italia/svg/sprites.svg#it-close-big' ); ?>"></use>
						</svg>
					</button>
					</div>
					<div class="menu-wrapper" id="menu-principale">
						<!-- MAIN MENU -->
						<?php
								get_template_part(
									'template-parts/header/main-menu',
									null,
									array(
										'locations' => $dli_locations,
									)
								);
								?>
						<!-- SECONDARY MENU -->
						<?php
								get_template_part(
									'template-parts/header/secondary-menu',
									null,
									array(
										'locations' => $dli_locations,
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
