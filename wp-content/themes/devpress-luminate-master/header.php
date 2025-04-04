<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Luminate
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'luminate' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<?php if ( has_nav_menu( 'top' ) || '' != get_theme_mod( 'top-navigation-text', '' ) ) : ?>
		<nav id="top-navigation" class="navigation-menu" role="navigation">
			<div class="col-width clearfix">
				<?php if ( '' != get_theme_mod( 'top-navigation-text', '' ) ) : ?>
				<div class="top-navigation-text">
					<?php echo luminate_sanitize_textarea( get_theme_mod( 'top-navigation-text', '' ) ); ?>
				</div>
				<?php endif; ?>
				<?php if ( has_nav_menu( 'top' ) ) :
					wp_nav_menu( array(
						'theme_location' => 'top',
						'link_before' => '<span>',
						'link_after' => '</span>',
						'depth' => '1',
					) );
				endif; ?>
			</div>
		</nav>
		<?php endif; ?>

		<div class="site-branding">

			<div class="col-width clearfix">
				<div class="brand">

					<?php if ( function_exists( 'jetpack_the_site_logo' ) ) : ?>
						<?php jetpack_the_site_logo(); ?>
					<?php else : ?>
						<?php if ( get_theme_mod( 'logo', 0 ) ) : ?>
						<div class="site-logo">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<img src="<?php echo esc_url( get_theme_mod( 'logo' ) ); ?>" alt="<?php esc_attr( get_bloginfo( 'name' ) ); ?>">
							</a>
						</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( luminate_brand_text() ) : ?>
					<div class="brand-text">

						<?php if ( get_theme_mod( 'display-site-title', 1 ) ) : ?>
							<div class="site-title">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
									<?php echo get_bloginfo( 'name' ); ?>
								</a>
							</div>
						<?php endif; ?>

						<?php if ( get_theme_mod( 'display-site-description', 0 ) && get_bloginfo( 'description' ) != '' ) : ?>
							<div class="site-description"><?php bloginfo( 'description' ); ?></div>
						<?php endif; ?>

					</div>
					<?php endif; ?>

				</div>

				<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<div id="primary-navigation-section">
					<nav id="primary-navigation" class="navigation-menu clearfix" role="navigation">
						<div class="menu-toggle">
							<a href="#offcanvas" class="navigation-button">
								<span class="screen-reader-text"><?php _e( 'Menu', 'luminate' ); ?></span>
								<span class="toggle"></span>
							</a>
						</div>
						<?php wp_nav_menu( array(
							'theme_location' => 'primary',
							'link_before' => '<span>',
							'link_after' => '</span>',
							'depth' => '2'
						) ); ?>
					</nav>
				</div>
				<?php endif; ?>
			</div>
		</div>

	</header><!-- #masthead -->

	<div id="content" class="site-content clearfix">
		<div class="col-width clearfix">