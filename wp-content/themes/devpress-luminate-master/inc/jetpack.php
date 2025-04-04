<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Luminate
 */

/**
 * Add support for Jetpack features.
 */
function luminate_jetpack_setup() {

	/**
	 * Add theme support for Jetpack Testimonials
	 */
	add_theme_support( 'jetpack-testimonial' );

	/**
	 * Add a new image size for Site Logo
	 */
	add_image_size( 'luminate-testimonial', 90, 90, true );

	/**
	 * Add theme support for Infinite Scroll.
	 * See: http://jetpack.me/support/infinite-scroll/
	 */
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'luminate_infinite_scroll_render',
		'footer'    => 'colophon'
	) );

}
add_action( 'after_setup_theme', 'luminate_jetpack_setup' );

/**
 * Flush the Rewrite Rules for the testimonials CPT after the user has activated the theme.
 */
function luminate_flush_rewrite_rules() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'luminate_flush_rewrite_rules' );

/**
 * Define the code that is used to render the posts added by Infinite Scroll.
 *
 * Includes the whole loop. Used to include the correct template part for the Testimonials CPT.
 */
function luminate_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();

		if ( is_post_type_archive( 'jetpack-testimonial' ) ) {
			get_template_part( 'partials/content', 'testimonial' );
		} else {
			get_template_part( 'partials/content', get_post_format() );
		}
	}
}

/**
 * Filter the 'infinite_scroll_has_footer_widgets' value.
 * If the navigation menu in the footer is used or if the footer sidebar contains any widgets, the scroll will be changed to 'click' from 'scroll'.
 */
function luminate_infinite_scroll_has_footer_widgets( $has_widgets ) {
	if ( is_active_sidebar( 'footer' ) ) {
		$has_widgets = true;
	}

	return $has_widgets;
}
add_filter( 'infinite_scroll_has_footer_widgets', 'luminate_infinite_scroll_has_footer_widgets' );