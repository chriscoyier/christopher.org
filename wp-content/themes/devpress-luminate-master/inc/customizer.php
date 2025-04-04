<?php
/**
 * Luminate Theme Customizer
 *
 * @package Luminate
 */

/**
 * Adds controls to the customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function luminate_customize_controls( $wp_customize ) {

	// Top Navigation Settings
	$wp_customize->add_section( 'top-navigation' , array(
		'title'      => __( 'Top Navigation', 'luminate' ),
		'priority'   => 20,
	) );

	$wp_customize->add_setting( 'top-navigation-text', array(
		'default'           => '',
		'sanitize_callback' => 'luminate_sanitize_textarea'
	) );

	$wp_customize->add_control( 'top-navigation-text', array(
		'label'    => __( 'Top Navigation Text', 'luminate' ),
		'description'    => __( 'Text will display in top navigation bar.', 'luminate' ),
		'section'  => 'top-navigation',
		'type'     => 'textarea'
	) );

	// Header Settings
	$wp_customize->add_setting( 'display-site-title', array(
		'default'    =>  1,
		'transport'  =>  'refresh'
	) );

	$wp_customize->add_control( 'display-site-title', array(
		'label'     => __( 'Display Site Title', 'luminate' ),
		'section'   => 'title_tagline',
		'type'      => 'checkbox'
	) );

	$wp_customize->add_setting( 'display-site-description', array(
		'default'    =>  0,
		'transport'  =>  'refresh'
	) );

	$wp_customize->add_control( 'display-site-description', array(
		'label'     => __( 'Display Site Description', 'luminate' ),
		'section'   => 'title_tagline',
		'type'      => 'checkbox'
	) );

	$wp_customize->add_setting( 'logo', array(
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo',
		array(
			'label'    => __( 'Logo', 'luminate' ),
			'section'  => 'title_tagline',
			'settings' => 'logo'
		)
	) );


	// Template Settings
	$wp_customize->add_section( 'page-showcase' , array(
		'title'      => __( 'Page Showcase', 'luminate' ),
		'priority'   => 30,
	) );

	for ( $count = 1; $count <= 7; $count++ ) :

		// Add color scheme setting and control
		$wp_customize->add_setting( 'page-showcase-' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		) );

		$wp_customize->add_control( 'page-showcase-' . $count, array(
			'label'    => __( 'Select Page', 'luminate' ),
			'section'  => 'page-showcase',
			'type'     => 'dropdown-pages'
		) );

		// Read More Text
		$wp_customize->add_setting( 'page-showcase-more-text-' . $count, array(
			'default'           => __( 'Read More', 'luminate' ),
			'sanitize_callback' => 'luminate_sanitize_textarea'
		) );

		$wp_customize->add_control( 'page-showcase-more-text-' . $count, array(
			'description'    => __( 'Link text. Set blank to hide.', 'luminate' ),
			'section'  => 'page-showcase',
			'type'     => 'text',
			'input_attrs' => array(
		        'placeholder'    => __( 'Read More', 'luminate' ),
		    )
		) );

	endfor;

	// Highlight Color Settings
	$wp_customize->add_setting( 'highlight-color', array(
		'default' => '#67acc0',
		'sanitize_callback' => 'sanitize_hex_color'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'highlight-color', array(
		'label' => __( 'Highlight Color', 'luminate' ),
		'section' => 'colors',
		'settings' => 'highlight-color'
	) ) );

	// Highlight Hover Settings
	$wp_customize->add_setting( 'highlight-hover', array(
		'default' => '#4796AD',
		'sanitize_callback' => 'sanitize_hex_color'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'highlight-hover', array(
		'label' => __( 'Highlight Hover', 'luminate' ),
		'section' => 'colors',
		'settings' => 'highlight-hover'
	) ) );

	// Footer
	$wp_customize->add_section( 'footer' , array(
		'title'      => __( 'Footer', 'luminate' ),
		'priority'   => 100,
	) );

	$wp_customize->add_setting( 'footer-text', array(
		'default'           => '',
		'sanitize_callback' => 'luminate_sanitize_textarea'
	) );

	$wp_customize->add_control( 'footer-text', array(
		'label'    => __( 'Footer Text', 'luminate' ),
		'section'  => 'footer',
		'type'     => 'textarea'
	) );

}
add_action( 'customize_register', 'luminate_customize_controls' );

/**
 * Changes header text of the "Site Title" section
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function luminate_customize_headers( $wp_customize ) {

	$wp_customize->get_section( 'title_tagline' )->title = __( 'Site Title, Tagline and Logo', 'luminate' );

}
add_action( 'customize_register', 'luminate_customize_headers' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function luminate_customize_transports( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'highlight-color' )->transport = 'postMessage';
	$wp_customize->get_setting( 'highlight-hover' )->transport = 'postMessage';

}
add_action( 'customize_register', 'luminate_customize_transports' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function luminate_customize_preview_js() {
	wp_enqueue_script(
		'luminate_customizer',
		get_template_directory_uri() . '/js/customizer.js',
		array( 'customize-preview' ),
		'1.0.0',
		true
	);
}
add_action( 'customize_preview_init', 'luminate_customize_preview_js' );

if ( ! function_exists( 'luminate_sanitize_textarea' ) ) :
/**
 * Sanitize textarea.
 *
 * @param string $content
 * @return string
 */
function luminate_sanitize_textarea( $content ) {

	if ( '' === $content ) {
		return '';
	}
	return wp_kses( $content, wp_kses_allowed_html( 'post' ) );

}
endif;

if ( ! function_exists( 'sanitize_hex_color' ) ) :
/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or null.
 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
 *
 * @param string $color
 * @return string|null
 */
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return null;
}
endif;