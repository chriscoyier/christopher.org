<?php
/**
 * The template for displaying single posts.
 *
 * @package Luminate
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<div class="module">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'partials/content', 'single' ); ?>

				<?php the_post_navigation( array(
					'prev_text' => _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'luminate' ),
					'next_text' => _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'luminate' )
				) ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>
		</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
