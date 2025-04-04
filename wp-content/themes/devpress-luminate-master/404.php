<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Luminate
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div id="error-404" class="module">

					<header class="entry-header">
						<h1 class="entry-title"><?php _e( '404 - Page Not Found', 'luminate' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'The page you are looking for doesn\'t exist. Try a search?', 'luminate' ); ?></p>

						<?php get_search_form(); ?>

					</div><!-- .entry-content -->
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
