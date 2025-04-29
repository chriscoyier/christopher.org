<?php
/*
Plugin Name: PrettyLinks
Plugin URI: https://prettylinks.com/pl/plugin-uri
Description: Shrink, track and share any URL using your website and brand!
Version: 3.6.15
Requires PHP: 7.4
Author: Pretty Links
Author URI: http://prettylinks.com
Text Domain: pretty-link
Copyright: 2004-2020, Caseproof, LLC

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

require_once __DIR__ . '/vendor-prefixed/autoload.php';

define('PRLI_PLUGIN_SLUG','pretty-link/pretty-link.php');
define('PRLI_PLUGIN_NAME','pretty-link');
define('PRLI_PATH',WP_PLUGIN_DIR.'/'.PRLI_PLUGIN_NAME);
define('PRLI_CONTROLLERS_PATH',PRLI_PATH.'/app/controllers');
define('PRLI_MODELS_PATH',PRLI_PATH.'/app/models');
define('PRLI_HELPERS_PATH',PRLI_PATH.'/app/helpers');
define('PRLI_VIEWS_PATH',PRLI_PATH.'/app/views');
define('PRLI_LIB_PATH',PRLI_PATH.'/app/lib');
define('PRLI_I18N_PATH',PRLI_PATH.'/i18n');
define('PRLI_CSS_PATH',PRLI_PATH.'/css');
define('PRLI_JS_PATH',PRLI_PATH.'/js');
define('PRLI_IMAGES_PATH',PRLI_PATH.'/images');
define('PRLI_VENDOR_LIB_PATH',PRLI_PATH.'/vendor/lib');

define('PRLI_URL',plugins_url($path = '/'.PRLI_PLUGIN_NAME));
define('PRLI_CONTROLLERS_URL',PRLI_URL.'/app/controllers');
define('PRLI_MODELS_URL',PRLI_URL.'/app/models');
define('PRLI_HELPERS_URL',PRLI_URL.'/app/helpers');
define('PRLI_VIEWS_URL',PRLI_URL.'/app/views');
define('PRLI_LIB_URL',PRLI_URL.'/app/lib');
define('PRLI_I18N_URL',PRLI_URL.'/i18n');
define('PRLI_CSS_URL',PRLI_URL.'/css');
define('PRLI_JS_URL',PRLI_URL.'/js');
define('PRLI_IMAGES_URL',PRLI_URL.'/images');
define('PRLI_VENDOR_LIB_URL',PRLI_URL.'/vendor/lib');
define('PRLI_SCRIPT_URL',site_url('/index.php?plugin=prli'));

define('PRLI_BROWSER_URL','https://d14715w921jdje.cloudfront.net/browser');
define('PRLI_OS_URL','https://d14715w921jdje.cloudfront.net/os');

define('PRLI_EDITION', 'pretty-link-lite');

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function prli_plugin_info($field) {
  static $plugin_folder, $plugin_file;

  if( !isset($plugin_folder) or !isset($plugin_file) ) {
    if( ! function_exists( 'get_plugins' ) ) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }

    $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
    $plugin_file = basename( ( __FILE__ ) );
  }

  if(isset($plugin_folder[$plugin_file][$field])) {
    return $plugin_folder[$plugin_file][$field];
  }

  return '';
}

// Plugin Information from the plugin header declaration
define('PRLI_VERSION', prli_plugin_info('Version'));
define('PRLI_DISPLAY_NAME', prli_plugin_info('Name'));
define('PRLI_AUTHOR', prli_plugin_info('Author'));
define('PRLI_AUTHOR_URI', prli_plugin_info('AuthorURI'));
define('PRLI_DESCRIPTION', prli_plugin_info('Description'));

// Autoload all the requisite classes
function prli_autoloader($class) {
  // Only load Pretty Link classes here
  if(preg_match('/^Prli.+$/', $class)) {
    if(preg_match('/^(PrliBaseController)$/', $class)) {
      $filepath = PRLI_LIB_PATH."/{$class}.php";
    }
    else if(preg_match('/^.+Controller$/', $class)) {
      $filepath = PRLI_CONTROLLERS_PATH."/{$class}.php";
    }
    else if(preg_match('/^.+Helper$/', $class)) {
      $filepath = PRLI_HELPERS_PATH."/{$class}.php";
    }
    else {
      $filepath = PRLI_MODELS_PATH."/{$class}.php";

      // Now let's try the lib dir if its not a model
      if(!file_exists($filepath)) {
        $filepath = PRLI_LIB_PATH."/{$class}.php";
      }
    }

    if(file_exists($filepath)) {
      require_once($filepath);
    }
  }
}

// if __autoload is active, put it on the spl_autoload stack
if(is_array(spl_autoload_functions()) && in_array('__autoload', spl_autoload_functions())) {
  spl_autoload_register('__autoload');
}

// Add the autoloader
spl_autoload_register('prli_autoloader');

// The number of items per page on a table
global $page_size;
$page_size = 10;

global $prli_blogurl, $prli_siteurl, $prli_blogname, $prli_blogdescription;

function prli_get_home_url() {
  $prli_bid = null;

  if(function_exists('is_multisite') && is_multisite() && function_exists('get_current_blog_id')) {
    $prli_bid = get_current_blog_id();
  }

  // Fix WPML adding the language code at the start of the URL
  if(defined('ICL_SITEPRESS_VERSION')) {
    if(empty($prli_bid) || !function_exists('is_multisite') || !is_multisite()) {
      $url = get_option('home');
    }
    else {
      switch_to_blog($prli_bid);
      $url = get_option('home');
      restore_current_blog();
    }

    return $url;
  }

  return get_home_url($prli_bid);
}

$prli_blogurl = prli_get_home_url();
$prli_siteurl = get_option('siteurl');
$prli_blogname = get_option('blogname');
$prli_blogdescription = get_option('blogdescription');

/***** SETUP OPTIONS OBJECT *****/
global $prli_options;
$prli_options = PrliOptions::get_options();

register_activation_hook( __FILE__, 'prli_activation' );
function prli_activation() {
  add_option( 'prli_just_activated', true );
}

add_action( 'plugins_loaded', 'prli_run_activation' );
function prli_run_activation() {
  if ( empty( get_option( 'prli_just_activated' ) ) ) {
    return;
  }
  $pl_options = PrliOptions::get_options();
  $pl_options->activated_timestamp = time();
  $pl_options->store();
  delete_option( 'prli_just_activated' );
}

global $prli_link, $prli_link_meta, $prli_click, $prli_group, $prli_utils, $plp_update;

$prli_link      = new PrliLink();
$prli_link_meta = new PrliLinkMeta();
$prli_click     = new PrliClick();
$prli_group     = new PrliGroup();
$prli_utils     = new PrliUtils();

global $prli_db_version, $plp_db_version;

$prli_db_version = 24; // this is the version of the database we're moving to
$plp_db_version = 11; // this is the version of the database we're moving to

global $prli_app_controller;

// Load our controllers
$controllers = apply_filters( 'prli_controllers', @glob( PRLI_CONTROLLERS_PATH . '/*', GLOB_NOSORT ) );
foreach( $controllers as $controller ) {
  $class = preg_replace( '#\.php#', '', basename($controller) );
  if( preg_match( '#Prli.*Controller#', $class ) ) {
    $obj = new $class;
    $obj->load_hooks();

    if( $class==='PrliAppController' ) {
      $prli_app_controller = $obj;
    }
  }
}

$plp_update = new PrliUpdateController();

// Provide Back End Hooks to the Pro version of Pretty Link
if($plp_update->is_installed()) {
  add_action('after_setup_theme', function () {
    require_once(PRLI_PATH.'/pro/pretty-link-pro.php');
  });
}

require_once(PRLI_PATH.'/app/lib/PrliNotifications.php');
