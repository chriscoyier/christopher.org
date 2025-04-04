<?php
/**
 * Template Name: Page Showcase
 *
 * Displays a showcase layout of pages.
 *
 * @package Luminate
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php

		// Get pages set in the customizer (if any)
		$pages = array();
		for ( $count = 1; $count <= 7; $count++ ) {
			$mod = get_theme_mod( 'page-showcase-' . $count );
			if ( 'luminate-none-selected' != $mod ) {
				$pages[] = $mod;
			}
		}

		$args = array(
			'posts_per_page' => 7,
			'post_type' => 'page',
			'post__in' => $pages,
			'orderby' => 'post__in'
		);

		$query = new WP_Query( apply_filters( 'luminate_showcase_args', $args ) );

		if ( $query->have_posts() ) :
			$count = 1;
			while ( $query->have_posts() ) : $query->the_post();

				// Set default image sizes to use
				$thumbnail = 'luminate-showcase';
				$width = 840;
				$height = 560;

				// If no image is set, we'll use a fallback image
				if ( has_post_thumbnail() ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumbnail, true );
					// Additional error checking
					if ( is_array( $image ) ) {
						$image = $image[0];
						$class = "image-thumbnail";
					} else {
						$image = get_template_directory_uri() . '/images/post.svg';
						$class = 'fallback-thumbnail';
					}
				} else {
					$image = get_template_directory_uri() . '/images/post.svg';
					$class = 'fallback-thumbnail';
				}

				if ( post_password_required() ) {
					$image = get_template_directory_uri() . '/images/lock.svg';
					$class = 'fallback-thumbnail';
				}
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'module featured-' . $count ); ?>>

					<a href="<?php the_permalink(); ?>" class="entry-image-link">
						<figure class="entry-image <?php echo $class; ?>">
							<img src="<?php echo esc_url( $image ); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
						</figure>
					</a>

					<header class="entry-header">
						<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
						<?php if ( 'post' == get_post_type() ) : ?>
							<div class="entry-meta entry-header-meta">
								<?php luminate_posted_on(); ?>
							</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->

					<div class="entry-content clearfix">
						<?php the_excerpt(); ?>
					</div><!-- .entry-content -->

					<?php $moretext = luminate_sanitize_textarea( get_theme_mod( 'page-showcase-more-text-' . $count, __( 'Read More', 'luminate' ) ) ); ?>

					<?php if ( '' != $moretext ) : ?>
					<p class="read-more">
						<a href="<?php the_permalink(); ?>">
							<?php echo $moretext; ?>
						</a>
					</p>
					<?php endif; ?>

				</article><!-- #post-## -->

			<?php
			$count++;
			endwhile;
		else :
			if ( current_user_can( 'customize' ) ) { ?>
				<div class="admin-msg">
					<p><?php _e( 'There are no pages available to display.', 'luminate' ); ?></p>
					<p><?php printf(
						__( 'Please check your settings in the <a href="%s">customizer</a>.', 'luminate' ),
						admin_url( 'customize.php?autofocus[control]=page-showcase-1' )
					); ?></p>
				</div>
			<?php }
		endif;
		?>

		<?php wp_reset_query(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
