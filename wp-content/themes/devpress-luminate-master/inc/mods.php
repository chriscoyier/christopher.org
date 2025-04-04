<?php
/**
 * Functions used to implement options
 *
 * @package Luminate
 */

if ( ! function_exists( 'luminate_inline_styles' ) ) :
/**
 * Output inline styles in the header if options are set
 */
function luminate_inline_styles() {

	// Variable for inline styles
	$output = '';

	// Highlight Color
	$default = '#67acc0';
	$mod = get_theme_mod( 'highlight-color',  $default );

	if ( $default != $mod ) {
		$output .= "a { color:" . sanitize_hex_color( $mod ) . " }\n";
		$output .= ".page-header, #content blockquote { border-left-color:" . sanitize_hex_color( $mod ) . " }\n";
		$output .= "button, .button, input[type='button'], input[type='reset'], input[type='submit'] { background:" . sanitize_hex_color( $mod ) . " }\n";
	}

	// Highlight Hover
	$default = '#4796AD';
	$mod = get_theme_mod( 'highlight-hover',  $default );

	if ( $default != $mod) {
		$output .= "a:hover { color:" . sanitize_hex_color( $mod ) . " }\n";
		$output .= "button:hover, .button:hover, input[type='button']:hover, input[type='reset']:hover, input[type='submit']:hover { background:" . sanitize_hex_color( $mod ) . " }\n";
	}

	// Output styles
	if ( '' != $output ) {
		$output = "<!-- Luminate Styles -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
		echo $output;
	}
}
add_action( 'wp_head', 'luminate_inline_styles', 100 );
endif;

if ( ! function_exists( 'luminate_footer_text' ) ) :
/**
 * Get default footer text
 *
 * @return string $text
 */
function luminate_footer_text() {
	$text = sprintf(
		__( 'Powered by %s', 'luminate' ),
		'<a href="' . esc_url( __( 'http://wordpress.org/', 'luminate' ) ) . '">WordPress</a>'
	);
	$text .= '<span class="sep"> | </span>';
	$text .= sprintf(
		__( '%1$s by %2$s.', 'luminate' ),
			'Luminate Theme',
			'<a href="http://devpress.com/" rel="designer">DevPress</a>'
	);
	return $text;
}
endif;

/**
 * Append class "social" to specific off-site links
 *
 * @since Luminate 1.0.0
 */
function luminate_social_nav_class( $classes, $item ) {

    if ( 0 == $item->parent && 'custom' == $item->type) {

    	$url = parse_url( $item->url );

    	if ( !isset( $url['host'] ) ) {
	    	return $classes;
    	}

    	$base = str_replace( "www.", "", $url['host'] );

    	$social = array(
    		'behance.com',
    		'dribbble.com',
    		'facebook.com',
    		'flickr.com',
    		'github.com',
    		'linkedin.com',
    		'pinterest.com',
    		'plus.google.com',
    		'instagr.am',
    		'instagram.com',
    		'skype.com',
    		'spotify.com',
    		'twitter.com',
    		'vimeo.com'
    	);
    	$social = apply_filters( 'luminate_social_links', $social );

    	// Tumblr needs special attention
    	if ( strpos( $base, 'tumblr' ) ) {
			$classes[] = 'social';
		}

    	if ( in_array( $base, $social ) ) {
	    	$classes[] = 'social';
    	}

    }

    return $classes;

}
add_filter( 'nav_menu_css_class', 'luminate_social_nav_class', 10, 2 );

/**
 * Returns true if site title or tagline is visible
 *
 * @since Luminate 1.0.0
 */
function luminate_brand_text() {

	if ( get_theme_mod( 'display_site_title', 1 ) ) {
		return true;
	}

	if ( get_theme_mod( 'display_site_description', 0 ) && get_bloginfo( 'description' ) != '' ) {
		return true;
	}

	return false;
}