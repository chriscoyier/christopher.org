<?php
/*
Plugin Name: Amazon Product In a Post
Plugin URI: https://wordpress.org/plugins/amazon-product-in-a-post-plugin/
Description: Add stylized Amazon Products to your site via the Product Advertising API.
Author: KryptoniteWP
Author URI: https://kryptonitewp.com
Author Email: support@kryptonitewp.com
Text Domain: amazon-product-in-a-post-plugin
Domain Path: /lang/
Version: 5.2.2
    Copyright (C) 2009-2020 KryptoniteWP
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * Define Owner Info, etc.
 */
define( 'APIAP_OWNER_BUSINESS', 'KryptoniteWP' );
define( 'APIAP_DONATION_URL', 'https://donate.flowdee.de/' );
define( 'APIAP_HELP_EMAIL', 'support@kryptonitewp.com' );
define( 'APIAP_OTHER_PLUGINS_URL', 'https://kryptonitewp.com/downloads/category/plugins/' );

/*// debugging only
ini_set( 'memory_limit', '256M' );
ini_set( 'display_errors', 0 );
ini_set( 'log_errors', 1 );
ini_set( 'error_log', dirname( __FILE__ ) . '/error-log.txt' );
error_reporting( E_ALL & ~E_NOTICE & ~E_WARNING );
*/

/**
 * Load textdomain for plugin.
 *
 * @method amazon_product_load_textdomain
 */
function amazon_product_load_textdomain(){
	load_plugin_textdomain( 'amazon-product-in-a-post-plugin', FALSE, basename( dirname( __FILE__ ) ) . '/lang');	//load the plugin text domain
}
add_action('init', 'amazon_product_load_textdomain');

// Variables
/**
 * Plugin version.
 *
 * @var [string]
 */
define( 'APIAP_PLUGIN_VER', '5.2.2' );
/**
 * Plugin Database version.
 *
 * @var [string]
 */
define( 'APIAP_DBASE_VER', 	'3.8.0' );
/**
 * Amazon public key.
 *
 * @var [string]
 */
define( 'APIAP_PUB_KEY', 	get_option( 'apipp_amazon_publickey', '' ) );
/**
 * Amazon secret key.
 *
 * @var [string]
 */
define( 'APIAP_SECRET_KEY', get_option( 'apipp_amazon_secretkey', '' ) );
/**
 * Amazon associate id - also called affiliate id or partner id.
 *
 * @var [string]
 */
define( 'APIAP_ASSOC_ID', 	get_option( 'apipp_amazon_associateid', '' ) );//Amazon Partner ID
/**
 * Amazon locale.
 *
 * @var [string]
 */
define( 'APIAP_LOCALE', 	get_option( 'apipp_amazon_locale', 'com' ) ); // default to Amazon.com

//global $thedefaultapippstyle;
global $amazonhiddenmsg;
global $amazonerrormsg;
global $apipphookexcerpt;
global $apipphookcontent;
global $apippopennewwindow;
global $apippnewwindowhtml;
global $encodemode;
global $awspagequery;
global $debuggingAPPIP;
global $appip_running_excerpt;
global $APIAP_USE_GUTENBERG;
global $amazon_styles_enqueued; //added 5.0.9
register_activation_hook(__FILE__,'appip_install');
register_deactivation_hook(__FILE__,'appip_deinstall');

// MISC Settings, etc.
	$amazon_styles_enqueued = false;
	$APIAP_USE_GUTENBERG = false;
	$appip_running_excerpt = false;
	$debuggingAPPIP		= false;
	$appipitemnumber	= 0;
	$awspagequery		= '';
	$awsPageRequest 	= 1;
	$amazonhiddenmsg 	= get_option( 'apipp_amazon_hiddenprice_message', __( 'Visit Amazon for Price.', 'amazon-product-in-a-post-plugin' )); //Amazon Hidden Price Message
	$amazonerrormsg 	= get_option( 'apipp_amazon_notavailable_message',__( 'Product Unavailable.', 'amazon-product-in-a-post-plugin' ) ); //Amazon Error No Product Message
	$apipphookexcerpt 	= get_option( 'apipp_hook_excerpt' ); //Hook the excerpt?
	$apipphookcontent 	= get_option( 'apipp_hook_content' ); //Hook the content?
	$apippopennewwindow = get_option( 'apipp_open_new_window', true ); //open in new window?
	$apippnewwindowhtml	= (bool) $apippopennewwindow === true ? ' target="amazonwin" ' : '';
	$apip_getmethod 	= get_option( 'apipp_API_call_method' );
	$encodemode 		= get_option( 'appip_encodemode', 'UTF-8' ); //UTF-8 will be default

	//Encode Mode
	if( get_option( 'appip_encodemode', '' ) === '' ){
		update_option( 'appip_encodemode', 'UTF-8' ); //set default to UTF-8
		$encodemode = "UTF-8";
	}

	//backward compat.
	if( !function_exists('mb_convert_encoding') ){
		function mb_convert_encoding( $etext = '', $encodemode = '', $encis = '' ){
			return $etext;
		}
	}
	//backward compat.
	if( !function_exists('mb_detect_encoding') ){
		/**
		 * Detect encoding for returned text from API. Only used for backward compatibility.
		 *
		 * @method mb_detect_encoding
		 * @param  string             $etext      Encoding text.
		 * @param  array              $encodemode Encode Mode.
		 * @param  bool 							$encstrict	Strict Encoding.
		 * @return [string]
		 */
		function mb_detect_encoding( $etext = '', $encodemode = array(), $encstrict = false ){
			return $etext;
		}
	}
	//backward compat.
	if(!function_exists('mb_detect_order')){
		/**
		 * Detect encoding order for returned text from API. Only used for backward compatibility.
		 *
		 * @method mb_detect_order
		 * @return [array]          Order of encoding modes.
		 */
		function mb_detect_order(){
			return array('ASCII','ISO-8859-1','UTF-8');
		}
	}

	// Change encoding if needed via GET - use :
	// http://yoursite.com/?resetenc=[encode mode] i.e., http://yoursite.com/?resetenc=UTF-8 or http://yoursite.com/?resetenc=ISO-8859-1, etc.
	// This will be the mode you want the text OUTPUT as. Note - You MUST be logged in and have 'manage_options' (administrator) capabilities.
	if( isset( $_GET['resetenc'] ) && ( is_user_logged_in() && current_user_can( 'manage_options' ) ) || appip_can_set_debug() ){
		$validEncModes = apply_filters('amazon-product-valid-enc-modes', array('ISO-8859-1','ASCII','ISO-8859-2','ISO-8859-3','ISO-8859-4','ISO-8859-5','ISO-8859-6','ISO-8859-7','ISO-8859-8','ISO-8859-9','ISO-8859-10','ISO-8859-15','ISO-2022-JP','ISO-2022-JP-2','ISO-2022-KR','UTF-8','UTF-16'));
		if( in_array( strtoupper( $_GET[ 'resetenc' ] ), $validEncModes ) ){
			update_option( 'appip_encodemode', strtoupper( esc_attr( $_GET['resetenc'] ) ) );
			$encodemode = strtoupper( esc_attr($_GET['resetenc'] ) );
		}
	}

	//reset to default styles if user deletes styles in admin
	// 4.0.3.3 No longer needed - Load Default styles and then any additional custom styles after - makes it easier.
	//if(trim(get_option("apipp_product_styles",'')) == '')
		//update_option("apipp_product_styles",$thedefaultapippstyle);

	//generate debug key
	if(trim(get_option("apipp_amazon_debugkey",'')) == ''){
		$randomkey = md5( uniqid( get_bloginfo( 'url' ) . time(), true ) );
		update_option( "apipp_amazon_debugkey", $randomkey );
	}

/** Filters & Hooks **/
	//add_action('wp','aws_prodinpost_cartsetup', 1, 2); //Future Item
	if(!is_admin()){
		// only add for front-end calls
		add_filter( 'the_content', 'aws_prodinpost_filter_content_test', 1); //hook content - we will filter the override after
		add_filter( 'the_excerpt', 'aws_prodinpost_filter_excerpt_test', 1); //hook excerpt - we will filter the override after
		add_filter( 'get_the_excerpt', 'aws_prodinpost_filter_get_excerpt', 1);
	}
	//add_action('wp_footer','amazon_add_one_link',100);
	function amazon_add_one_link(){
		//testing only for Amazon onelink
	}
	function apipp_hide_binding_in_title($val, $binding = ''){
		// override this with a later priority than 1 if you want to turn off for certain bindings.
		/* example for turning off for a specific binding:
		if(strtolower($binding) == 'kitchen')
			return true;
		*/
		if( (bool) get_option('apipp_hide_binding', false) === true)
			return true;
	}
	add_filter( 'amazon-hide-binding-in-title', 'apipp_hide_binding_in_title', 1, 2);

	function apipp_plugin_row_meta( $links, $file ){
		if ( $file == plugin_basename(__FILE__) ){
			$links[] = '<a href="admin.php?page=apipp_plugin-shortcode"><span style="font-weight:bold;font-size:20px;line-height: .75;height: 20px;display: inline-block;">[]</span>'.__( "Shortcode/Block Usage", 'amazon-product-in-a-post-plugin') .'</a>';
			$links[] = '<a href="admin.php?page=apipp_plugin-faqs"><span class="dashicons dashicons-editor-help"></span>'.__( "FAQs", 'amazon-product-in-a-post-plugin') .'</a>';
			//$links[] = '<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DL8L8WQU25RZC"><span class="dashicons dashicons-heart" style="color: #e00505;"></span>Donate</a>';
			$links[] = '<a target="_blank" href="'.APIAP_DONATION_URL.'"><span class="dashicons dashicons-heart" style="color: #e00505;"></span>Donate</a>';
		}
		return $links;
	}
	add_filter( 'plugin_row_meta', 'apipp_plugin_row_meta' , 10, 2 );

	function apipp_plugin_action_links($links){
			$new_links = array();
			$new_links[] = '<a href="admin.php?page=apipp-main-menu" class="amazon-plugin-action-icon"><span class="dashicons dashicons-info"></span>'.__( "Getting Started", 'amazon-product-in-a-post-plugin') .'</a>';
			return array_merge($links,$new_links );
	}
	add_action( 'plugin_action_links_' . plugin_basename(__FILE__), 'apipp_plugin_action_links');

	/* Add styles to help with AMP plugins */
	add_action( 'amp_post_template_css', 'amp_appip_ampstyles' , 1000);
	function amp_appip_ampstyles(){
	?>
	.amp-wp-content .amazon-image-wrapper a amp-img img{height:auto;position:relative;}.amp-wp-content .amazon-image-wrapper a amp-img>*{padding:0;}.amp-wp-content .amp-wp-content table{width:100%;background:0 0}.amp-wp-content .amazon-buying{padding:4px}.amp-wp-content .amazon-image-wrapper{margin:0;background:0 0;padding:4px}.amp-wp-content .amazon-buying hr{border-style:solid;border-width:0 0 1px;border-color:#ccc}.amp-wp-content h2.amazon-asin-title{max-width:100%;font-size:1.3em;line-height:1.35;background:0 0}.amp-wp-content .amazon-product-pricing-wrap{max-width:100%}.amp-wp-content .amazon-product-pricing-wrap table tr td{border:0;background:0 0;margin:0;padding:0 2px;display:inline-block;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}.amp-wp-content .amazon-product-pricing-wrap table tr{border:0;background:0 0;margin:0;padding:2px;text-align:left}.amp-wp-content .amazon-image-wrapper amp-img{width:160px;margin:0 auto;max-width:100%}.amp-wp-content .amazon-image-wrapper>a{display:block;text-align:center}.amp-wp-content .amazon-image-wrapper>br{display:none}.amp-wp-content .amazon-product-pricing-wrap table tr td:last-child:first-child,.amp-wp-content .amazon-product-pricing-wrap tbody,.amp-wp-content .amazon-product-pricing-wrap tr{width:100%;display:block}.amp-wp-content .amazon-price-button amp-img{margin:0 auto}.amp-wp-content p.amazon-asin-title{margin-bottom:0;font-size:1.25em;line-height:1.35}.amp-wp-content .amazon-product-pricing-wrap table{background:0 0}.amp-wp-content .amazon-image-wrapper .amazon-additional-images-wrapper amp-img{width:50px;display:inline-block;margin:0 2px}.amp-wp-content span.amazon-additional-images-text{display:block}.amp-wp-content .amazon-additional-images-wrapper{line-height:1.25;text-align:center}.amp-wp-content .amazon-additional-images-wrapper br{display:none}@media screen and (min-width:550px){.amp-wp-content .amazon-image-wrapper{float:left;width:28%}.amp-wp-content .amazon-buying{float:left;width:70%}.amp-wp-content .amazon-price-button amp-img{margin:5px 0 0}.amp-wp-content p.amazon-asin-title{margin-bottom:12px}}
	body[class*=amp-mode] .amazon-image-wrapper a amp-img img{height:auto;position:relative;}body[class*=amp-mode] .amazon-image-wrapper a amp-img>*{padding:0;}body[class*=amp-mode] body[class*=amp-mode] table{width:100%;background:0 0}body[class*=amp-mode] .amazon-buying{padding:4px}body[class*=amp-mode] .amazon-image-wrapper{margin:0;background:0 0;padding:4px}body[class*=amp-mode] .amazon-buying hr{border-style:solid;border-width:0 0 1px;border-color:#ccc}body[class*=amp-mode] h2.amazon-asin-title{max-width:100%;font-size:1.3em;line-height:1.35;background:0 0}body[class*=amp-mode] .amazon-product-pricing-wrap{max-width:100%}body[class*=amp-mode] .amazon-product-pricing-wrap table tr td{border:0;background:0 0;margin:0;padding:0 2px;display:inline-block;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}body[class*=amp-mode] .amazon-product-pricing-wrap table tr{border:0;background:0 0;margin:0;padding:2px;text-align:left}body[class*=amp-mode] .amazon-image-wrapper amp-img{width:160px;margin:0 auto;max-width:100%}body[class*=amp-mode] .amazon-image-wrapper>a{display:block;text-align:center}body[class*=amp-mode] .amazon-image-wrapper>br{display:none}body[class*=amp-mode] .amazon-product-pricing-wrap table tr td:last-child:first-child,body[class*=amp-mode] .amazon-product-pricing-wrap tbody,body[class*=amp-mode] .amazon-product-pricing-wrap tr{width:100%;display:block}body[class*=amp-mode] .amazon-price-button amp-img{margin:0 auto}body[class*=amp-mode] p.amazon-asin-title{margin-bottom:0;font-size:1.25em;line-height:1.35}body[class*=amp-mode] .amazon-product-pricing-wrap table{background:0 0}body[class*=amp-mode] .amazon-image-wrapper .amazon-additional-images-wrapper amp-img{width:50px;display:inline-block;margin:0 2px}body[class*=amp-mode] span.amazon-additional-images-text{display:block}body[class*=amp-mode] .amazon-additional-images-wrapper{line-height:1.25;text-align:center}body[class*=amp-mode] .amazon-additional-images-wrapper br{display:none}@media screen and (min-width:550px){body[class*=amp-mode] .amazon-image-wrapper{float:left;width:28%}body[class*=amp-mode] .amazon-buying{float:left;width:70%}body[class*=amp-mode] .amazon-price-button amp-img{margin:5px 0 0}body[class*=amp-mode] p.amazon-asin-title{margin-bottom:12px}}
	<?php
	}
	add_filter('appip_single_product_filter','amp_appip_remove_bad',100);
	function amp_appip_remove_bad($returnval){
		$returnval = str_replace('<hr noshade="noshade" size="1" />','',$returnval);
		return $returnval;
	}
	/* end AMP */

	// Warnings Quickfix
	if( (bool) get_option( 'apipp_hide_warnings_quickfix', false ) === true ){
		if(!(defined('WP_DEBUG') && WP_DEBUG))//do nothing if WP_DEBUG is on - warnings and notices should show in debug mode no matter what. Fixed 3.7.1
			ini_set("display_errors", 0); //turns off error display
	}

    // Includes
	require_once( plugin_dir_path( __FILE__ )."inc/activation.php");                    //Install and Uninstall hooks
	require_once( plugin_dir_path( __FILE__ )."inc/functions.php");                     //Functions
	require_once( plugin_dir_path( __FILE__ ).'inc/class-debug.php' );                  //debug functionality for plugin
	require_once( plugin_dir_path( __FILE__ ).'inc/class-request-v5.php' );                //New 5.0 Class
	if (version_compare(phpversion(), '5.4.0', '<'))
		require_once( plugin_dir_path( __FILE__ )."inc/class-sha256.php");              //3.8.0+ only require if PHP less than 5.4.0 ('hash' function was introduced then)

	function appip_gutenberg_editor_test() {
		global $APIAP_USE_GUTENBERG;
		$APIAP_USE_GUTENBERG = appip_check_blockEditor_is_active();
	}
	add_action( 'plugins_loaded', 'appip_gutenberg_editor_test' );                          //4.0.4+ Gutenberg Check

	require_once( plugin_dir_path( __FILE__ )."inc/get-product.php");                   //main product function
	require_once( plugin_dir_path( __FILE__ )."inc/aws-signed-request.php");            //major workhorse for plugin
	require_once( plugin_dir_path( __FILE__ )."inc/aws-signed-request-test.php");       //class for testing the request.
	require_once( plugin_dir_path( __FILE__ )."inc/class-shortcode.php");               //shortcode class for plugin
	require_once( plugin_dir_path( __FILE__ )."inc/class-shortcode-elements.php");      //amazon-element shortcodes for plugin
	require_once( plugin_dir_path( __FILE__ )."inc/class-shortcode-products.php");      //amazonproducr shortcodes for plugin
	require_once( plugin_dir_path( __FILE__ )."inc/class-shortcode-search.php");        //search shortcodes for plugin
	//require_once( plugin_dir_path( __FILE__ )."inc/class-cron-jobs.php");                 //cron job functions 5.1.0
	function apipp_plugins_loaded(){
		// we need to make sure this loads late enough so the plugin takes priority (may change later if needed)
		require_once( plugin_dir_path( __FILE__ )."inc/class-shortcode-grid.php");      //grid shortcodes for plugin (add-on plugin overrides this). ADDED 3.7.1
	}
	add_action('plugins_loaded', 'apipp_plugins_loaded', 11);
	require_once( plugin_dir_path( __FILE__ )."inc/class-featured-image.php");          //Featured Image Function for URL based Amazon Featured images.
	if(is_admin()){
		require_once( plugin_dir_path( __FILE__ )."inc/tools.php");                     //edit box for plugin
		require_once( plugin_dir_path( __FILE__ )."inc/options.php");                   //admin options for plugin
		//require_once( plugin_dir_path( __FILE__ ).'inc/class-add-custom-mce.php' );      //editor Button Funcitonality - Temp Removed in 4.0
	}
	if( version_compare(APIAP_PLUGIN_VER, get_option("apipp_product_upgraded_version", '0.0.0'), '>' )){
		// less than 4.0.0, fix lightbox setting.
		if( version_compare('4.0.0', get_option("apipp_product_upgraded_version", '0.0.0'), '<' )){
			if(get_option("apipp_amazon_use_lightbox", '') === '' )
				update_option("apipp_amazon_use_lightbox", 'true');
		}
		if( version_compare('4.0.3.3', get_option("apipp_product_upgraded_version", '0.0.0'), '<' )){
			delete_option("apipp_product_styles_default_version");
			if( get_option("apipp_product_styles_mine",'') === '')
				delete_option("apipp_product_styles");
		}

		// less than 5.0.0, set show metbox with gutengerg to true (can be turned off later if not needed).
		if( version_compare('5.0.0', get_option("apipp_product_upgraded_version", '0.0.0'), '<' )){
			if( get_option("apipp_show_metaboxes",'') === '')
				update_option("apipp_show_metaboxes", 'true');
			// clear up a miss-spelled option and fix it, or delete it if present and correct version is already present
			if( get_option("appip_show_single_only",'none') !== 'none' && get_option("apipp_show_single_only",'none') === 'none'){
				update_option("apipp_show_single_only", get_option("appip_show_single_only"));
				delete_option('appip_show_single_only');
			}elseif(get_option("appip_show_single_only",'none') !== 'none' && get_option("apipp_show_single_only",'none') !== 'none'){
				delete_option('appip_show_single_only');
			}
			// clear old XML cached products - new API uses JSON and this will just make it easier.
			amazon_product_delete_all_cache('option-update');
		}
		if( version_compare('5.1.0', get_option("apipp_product_upgraded_version", '0.0.0'), '<' )){
			//clear cache on update (just to make sure things don't go nuts.)
			amazon_product_delete_all_cache('option-update');
		}
		if( get_option("apipp_amazon_cache_sec",'') === '')
			update_option("apipp_amazon_cache_sec","3600"); //default 3600 - 1 hr

		//update new version in DB
		update_option("apipp_product_upgraded_version", APIAP_PLUGIN_VER);
	}


	function appip_can_set_debug(){
		global $debuggingAPPIP;
		if( $debuggingAPPIP )
			return true;
		return false;
	}

	function appip_admin_scripts($hook) {
		wp_enqueue_style( 'amazon-plugin-admin-styles',plugins_url('/css/amazon-admin.css',__FILE__),null,'2018-06-01');
		$template_array = apply_filters( 'appip-register-templates', array());
		$button_array = amazon_product_get_new_button_array( APIAP_LOCALE );
		wp_localize_script('jquery', 'appipTemplates', array('templates' => $template_array, 'buttons' => $button_array ));
		if ( strpos($hook, 'page_appip-layout-styles') !== false ) {
			//do nothing
		}elseif( strpos( $hook, 'page_apipp-cache-page' ) !== false || $hook === "post.php" || $hook === "post-new.php" || $hook === "edit.php"){
			wp_enqueue_script('amazon-plugin-admin',plugins_url('/js/amazon-admin.js',__FILE__),array('jquery-ui-tooltip'),'2018-06-01');
			wp_localize_script('amazon-plugin-admin','appipData',array( 'ajaxURL' => admin_url('admin-ajax.php'), 'appip_nonce' => wp_create_nonce( 'appip_cache_delete_nonce_ji9osdjfkjl' ), 'confirmDel' => __('Are you sure you want to delete this cache?', 'amazon-product-in-a-post-plugin'),'noCacheMsg' => __('no cached products at this time', 'amazon-product-in-a-post-plugin'), 'deleteMsgErr' => __('there was an error - the cache could not be deleted', 'amazon-product-in-a-post-plugin') ) );
		}elseif( strpos( $hook, '_page_apipp_plugin_admin' ) !== false  || strpos( $hook, 'page_apipp_plugin-shortcode' ) !== false ){
			add_thickbox();
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script('amazon-plugin-admin-options',plugins_url('/js/amazon-admin-options.js',__FILE__),array('jquery'),'2018-06-01');
			wp_enqueue_script('amazon-plugin-admin',plugins_url('/js/amazon-admin.js',__FILE__),array('jquery-ui-tooltip'),'2018-06-01');
		}elseif( strpos($hook, 'page_apipp-add-new') !== false ){
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script('amazon-plugin-admin',plugins_url('/js/amazon-admin.js',__FILE__),array('jquery'),'2018-06-01');
			add_thickbox();
		}
	}
	add_action( 'admin_enqueue_scripts', 'appip_admin_scripts' );

add_action( 'admin_enqueue_scripts', function($hook){
	amazon_product_get_new_button_array( APIAP_LOCALE );
},100 );
add_action( 'wp_enqueue_scripts', function(){
	amazon_product_get_new_button_array( APIAP_LOCALE );
},100 );