<?php
/**
 * @package Luminate
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'module' ); ?>>

	<?php if ( has_post_thumbnail() ) { ?>
	<figure class="testimonial-avatar">
		<?php the_post_thumbnail( 'luminate-testimonial' ); ?>
	</figure>
	<?php } ?>

	<div class="testimonial-text">
		<div class="testimonial-content">
			<?php the_content(); ?>
		</div>
		<div class="testimonial-cite">
			<?php the_title(); ?>
		</div>
	</div>

</article><!-- #post-## -->
