<?php
if (!defined('WP_UNINSTALL_PLUGIN'))
    die;

$appuninstallshortcodes 	= (bool) get_option( 'apipp_uninstall_all', false ); //Uninstall shortcodes in pages an posts
$appuninstalloptions 		= (bool) get_option( 'apipp_uninstall', false ); //Uninstall database and options

global $wpdb;
if( $appuninstalloptions ){ //options and database
	/* Remove CACHE Table */
	$wpdb->query("DROP TABLE `{$wpdb->prefix}amazoncache`");

	$remove_options = array(
		'amazon_pip_options', // Removed 3.7.0 but still might be present
		'amazon_product_dummy_featured_image_ID', // added 3.7.0
		'amazon-button-image', // added 3.7.0
		'apipp_advanced_tab', // added 4.0 
		'apipp_advanced_tab_end', // added 4.0 
		'apipp_amazon_associate_ad_linkid', // added 4.0 
		'apipp_amazon_associate_ad_region', // added 4.0 
		'apipp_amazon_associateid',
		'apipp_amazon_cache_ahead', // Added 3.6
		'apipp_amazon_cache_sec', // added 3.7.0
		'apipp_amazon_debugkey',
		'apipp_amazon_hiddenprice_message',
		'apipp_amazon_language', // Removed 3.7.0 but still might be present
		'apipp_amazon_locale',
		'apipp_amazon_notavailable_message',
		'apipp_amazon_publickey',
		'apipp_amazon_secretkey',
		'apipp_amazon_test_settings', // added 4.0.3.3
		'apipp_amazon_use_lightbox', // added 3.7.0
		'apipp_API_call_method', // Removed 3.7.0 but still might be present
		'apipp_db_trouble', // added 4.0.3.8
		'apipp_dbversion',
		'apipp_general_tab', // added 4.0
		'apipp_general_tab_end', // added 4.0 
		'apipp_hide_binding', //added 3.7
		'apipp_hide_warnings_quickfix',
		'apipp_hook_content',
		'apipp_hook_excerpt',
		'apipp_open_new_window',
		'apipp_product_featured_image', //added 3.7.0
		'apipp_product_mobile_popover', // added 4.0.3 future feature
		'apipp_product_styles',
		'apipp_product_styles_default', // Removed 4.0.3.2 but still might be present
		'apipp_product_styles_default_version', // Removed 4.0.3.2 but still might be present
		'apipp_product_styles_mine',
		'apipp_product_upgraded_version',
		'apipp_show_metaboxes', // added 4.0.3.8
		'apipp_show_single_only',
		'apipp_ssl_images', // Removed 3.7.0 but still might be present
		'apipp_styles_tab', // added 4.0 
		'apipp_styles_tab_end', // added 4.0 
		'apipp_uninstall',
		'apipp_uninstall_all',
		'apipp_use_cartURL',
		'apipp_version',
		'appip_amazon_usecurl', // Removed 3.7.0 but still might be present
		'appip_amazon_usefilegetcontents', // Removed 3.7.0 but still might be present
		'appip_button_color_bg', //test item may be present in some older versions
		'appip_dismiss_msg',
		'appip_encodemode',
		'appip_show_single_only', // miss-spelled option
		'appip_use_ssl_images', 
	);
	foreach($remove_options as $k => $option_nm){
		delete_option($option_nm);
	}
}

if( $appuninstalloptions && $appuninstallshortcodes ){ //both have to be to to protect user
	/* Still needs some debugging.
	//DELETE ALL POST META FOR ITEMS WITH APIPP USAGE
	$remSQL = "DELETE FROM $wpdb->postmeta WHERE `meta_key` LIKE '%amazon-product%';";
	$cleanit = $wpdb->query($remSQL);
	//Now get data for IDs with content or excerpt containing the shortcodes.
	$thesqla = "SELECT ID, post_content, post_excerpt FROM $wpdb->posts 
	WHERE `post_content` like '%[AMAZONPRODUCT%' 
	OR `post_content` like '%[amazon-element%'
	OR `post_content` like '%[amazon-product-search%'
	OR `post_content` like '%[amazon-grid%'
	OR `post_excerpt` like '%[AMAZONPRODUCT%'
	OR `post_excerpt` like '%[amazon-element%'
	OR `post_excerpt` like '%[amazon-product-search%'
	OR `post_excerpt` like '%[amazon-grid%'
	;";
	$postData = $wpdb->get_results($thesqla);
	if(count($postData)>0){
		foreach ($postData as $pdata){
			$pcontent = $pdata->post_content;
			$pexcerpt = $pdata->post_excerpt;
			$pupdate  = 0;
			$pid 	  = $pdata->ID;
			//$search   = "@(?:<p>)*\s*\[[AMAZONPRODUCT|amazon-element|amazon-grid|amazon-product-search]\s*=\s*(.+|^\+)\]\s*(?:</p>)*@i"; 
			$search = "@\n?(\[AMAZONPRODUCT.*\]|\[amazon\-element.*\]|\[amazon\-grid.*]|\[amazon\-product\-search.*\])@i";
			if(preg_match_all($search, $pcontent, $matches1)) {
				if (is_array($matches1)) {
					foreach ($matches1[1] as $key =>$v0) {
						$search 	= $matches1[0][$key];
						$ASINis		= $matches1[1][$key];
						$pcontent 	= addslashes(str_replace ($search, '', $pcontent));
					}
					$pupdate  = 1;
				}
			}
			if(preg_match_all($search, $pexcerpt, $matches2)) {
				if (is_array($matches2)) {
					foreach ($matches2[1] as $key =>$v0) {
						$search		= $matches2[0][$key];
						$ASINis		= $matches2[1][$key];
						$pexcerpt	= addslashes(str_replace ($search, '', $pexcerpt));
					}
					$pupdate  = 1;
				}
			}
			if($pupdate == 1){
				$wpdb->query("UPDATE $wpdb->posts SET post_excerpt = '{$pexcerpt}', post_content = '{$pcontent}' WHERE ID = '{$pid}';");
			}
		}
	}
	*/
}
