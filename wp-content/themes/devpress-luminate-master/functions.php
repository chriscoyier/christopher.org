<?php
/**
 * Luminate functions and definitions
 *
 * @package Luminate
 */

/**
 * The current version of the theme.
 */
define( 'LUMINATE_VERSION', '1.1.0' );

if ( ! function_exists( 'luminate_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function luminate_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Luminate, use a find and replace
	 * to change 'luminate' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'luminate', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// Registers navigation menus
	register_nav_menus( array(
		'top'		=> __( 'Top Menu', 'luminate' ),
		'primary'	=> __( 'Primary Menu', 'luminate' ),
		'footer'	=> __( 'Footer Menu', 'luminate' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	// Post editor styles
	add_editor_style( 'editor-style.css' );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'image', 'gallery', 'video', 'quote', 'link'
	) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'luminate_custom_background_args', array(
		'default-color' => 'f2f2f2',
		'default-image' => '',
	) ) );

	// Theme layouts
	add_theme_support(
		'theme-layouts',
		array(
			'single-column' => __( 'Single Column', 'luminate' ),
			'narrow-column' => __( 'Narrow Column', 'luminate' ),
			'sidebar-right' => __( 'Sidebar Right', 'luminate' ),
			'sidebar-left' => __( 'Sidebar Left', 'luminate' )
		),
		array( 'default' => 'sidebar-right' )
	);

	// Excerpt support needed for page showcase template
	add_post_type_support( 'page', 'excerpt' );

}
add_action( 'after_setup_theme', 'luminate_setup' );
endif; // luminate_setup

if ( ! function_exists( 'luminate_content_width' ) ) :
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function luminate_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'luminate_content_width', 720 );
}
add_action( 'after_setup_theme', 'luminate_content_width', 0 );
endif;

if ( ! function_exists( 'luminate_template_content_width' ) ) :
/**
 * Adjust content_width value for single column layouts
 *
 * @since Luminate 1.0.0
 */
function luminate_template_content_width() {

	$layout = get_theme_mod( 'theme_layout', 'sidebar-right' );

	if ( 'single-column' ==  $layout ) {
		$GLOBALS['content_width'] = apply_filters( 'luminate_content_width_single_column', 950 );
	}

	if ( 'narrow-column' == $layout ) {
		$GLOBALS['content_width'] = apply_filters( 'luminate_content_width_narrow_column', 695 );
	}

}
add_action( 'template_redirect', 'luminate_template_content_width' );
endif;

if ( ! function_exists( 'luminate_register_image_sizes' ) ) :
/*
 * Enables support for Post Thumbnails on posts and pages.
 *
 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
 */
function luminate_register_image_sizes() {

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 720, 1200 );
	add_image_size( 'luminate-showcase', 840, 560, true );
	add_image_size( 'luminate-single-column', 950, 9999 );

}
add_action( 'after_setup_theme', 'luminate_register_image_sizes' );
endif;

if ( ! function_exists( 'luminate_widgets_init' ) ) :
/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function luminate_widgets_init() {

	register_sidebar( array(
		'name'          => __( 'Sidebar', 'luminate' ),
		'id'            => 'primary',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget module %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer', 'luminate' ),
		'id'            => 'footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );


}
endif;
add_action( 'widgets_init', 'luminate_widgets_init' );

if ( ! function_exists( 'luminate_body_fonts' ) ) :
/**
 * Enqueue web fonts.
 */
function luminate_body_fonts() {

	// Google font URL to load
	$font_url = '';

    /* Translators: If there are characters in your language that are not
    * supported by Lora, translate this to 'off'. Do not translate
    * into your own language.
    */
    $primary = _x( 'active', 'Roboto font: active or inactive', 'luminate' );

    if ( 'inactive' !== $primary || 'inactive' !== $secondary ) :

        $font_families = array();

        if ( 'inactive' !== $primary ) {
            $font_families[] = 'Roboto:400italic,700italic,700,400';
        }

        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );

        $font_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

    endif;

	// Load Google Fonts
	wp_enqueue_style( 'luminate-body-fonts', $font_url, array(), null, 'screen' );

}
add_action( 'wp_enqueue_scripts', 'luminate_body_fonts' );
endif;

if ( ! function_exists( 'luminate_icon_fonts' ) ) :
/**
 * Enqueue icon fonts.
 */
function luminate_icon_fonts() {

	// Icon Font
	wp_enqueue_style( 'luminate-icons', get_template_directory_uri() . '/fonts/luminate-icons.css', array(), '1.0.0' );

}
add_action( 'wp_enqueue_scripts', 'luminate_icon_fonts' );
endif;

if ( ! function_exists( 'luminate_styles' ) ) :
/**
 * Enqueue theme styles
 */
function luminate_styles() {

	if ( SCRIPT_DEBUG || WP_DEBUG ) :

	 	wp_enqueue_style(
			'luminate-style',
			get_template_directory_uri() . '/css/style.css',
			array(),
			"2"
		);

		if ( is_page_template( 'templates/page-showcase.php' ) ) :
			wp_enqueue_style(
				'luminate-page-showcase',
				get_template_directory_uri() . '/css/page-showcase.css',
				array(),
				LUMINATE_VERSION
			);
		endif;

	else :

		wp_enqueue_style(
			'luminate-style',
			get_template_directory_uri() . '/css/style.min.css',
			array(),
			"2"
		);

		if ( is_page_template( 'templates/page-showcase.php' ) ) :
			wp_enqueue_style(
				'luminate-page-showcase',
				get_template_directory_uri() . '/css/page-showcase.min.css',
				array(),
				LUMINATE_VERSION
			);
		endif;

	endif;

}
endif;
add_action( 'wp_enqueue_scripts', 'luminate_styles' );

if ( ! function_exists( 'luminate_scripts' ) ) :
/**
 * Enqueue theme scripts
 */
function luminate_scripts() {

	if ( SCRIPT_DEBUG || WP_DEBUG ) :

		// FitVids Script conditionally enqueued from inc/extras.php
		wp_register_script(
			'luminate-fitvids',
			get_template_directory_uri() . '/js/jquery.fitvids.js',
			array( 'jquery' ),
			LUMINATE_VERSION,
			true
		);

		wp_enqueue_script(
			'luminate-skip-link-focus-fix',
			get_template_directory_uri() . '/js/skip-link-focus-fix.js',
			array(),
			LUMINATE_VERSION,
			true
		);

		wp_enqueue_script(
			'luminate-fast-click',
			get_template_directory_uri() . '/js/jquery.fastclick.js',
			array(),
			LUMINATE_VERSION,
			true
		);

		wp_enqueue_script(
			'luminate-sidr',
			get_template_directory_uri() . '/js/sidr.js',
			array(),
			LUMINATE_VERSION,
			true
		);

		wp_enqueue_script(
			'luminate-global',
			get_template_directory_uri() . '/js/theme.js',
			array( 'jquery' ),
			LUMINATE_VERSION,
			true
		);

	else :

		// FitVids Script conditionally enqueued from inc/extras.php
		wp_register_script(
			'luminate-fitvids',
			get_template_directory_uri() . '/js/jquery.fitvids.min.js',
			array( 'jquery' ),
			LUMINATE_VERSION,
			true
		);

		wp_enqueue_script(
			'luminate-scripts',
			get_template_directory_uri() . '/js/luminate.min.js',
			array( 'jquery' ),
			LUMINATE_VERSION,
			true
		);

	endif;

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
endif;
add_action( 'wp_enqueue_scripts', 'luminate_scripts' );

// Custom template tags for this theme.
require get_template_directory() . '/inc/template-tags.php';

// Custom functions that act independently of the theme templates.
require get_template_directory() . '/inc/extras.php';

// Add customizer options.
require get_template_directory() . '/inc/customizer.php';

// Additional filters and actions based on theme customizer selections.
require get_template_directory() . '/inc/mods.php';

// Theme Layouts
require get_template_directory() . '/inc/theme-layouts.php';

// JetPack
require get_template_directory() . '/inc/jetpack.php';

// Theme Updater
function luminate_theme_updater() {
	require( get_template_directory() . '/inc/updater/theme-updater.php' );
}
add_action( 'after_setup_theme', 'luminate_theme_updater' );