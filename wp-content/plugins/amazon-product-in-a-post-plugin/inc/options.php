<?php
//VARIABLES
	global $fullname_apipp, $shortname_apipp, $options_apipp;
	add_action('init','appip_start_options');
	function appip_start_options(){
		global $wpdb,$fullname_apipp, $shortname_apipp, $options_apipp;
		$appiptable = $wpdb->prefix . 'amazoncache';
		$checkTable = $wpdb->get_var("SHOW TABLES LIKE '{$appiptable}'");
		if($checkTable != $appiptable)
			appip_install();
		$shortname_apipp = 'apipp';
		$current_tab = isset( $_GET['tab'] ) ? esc_attr($_GET['tab']) : 'general';
		$fullname_apipp = __('Amazon Product In a Post Plugin', 'amazon-product-in-a-post-plugin');
		$options_apipp= array (
			array(	"name" => 'General Tab',
					"id" => 'apipp_general_tab',
					"tab" => 'general',
					"current_tab" => $current_tab,
					"type" => "tab-wrapper-start"),
			array(	"name" => __('Amazon API Settings','amazon-product-in-a-post-plugin'),
					"type" => "heading"),
			array(	"name" => __('Your Amazon Locale/Region','amazon-product-in-a-post-plugin'),
					"desc" => __('The Locale to use for Amazon API Calls (ae, ca, cn, com, com.au, com.br, com.mx, co.jp, co.uk, de, fr, es, in, it, nl, sa, se, sg). Default is "com" for US.','amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_amazon_locale",
					"type" => "select",
					"options" => array(
						"0"  => array("value" => "com",    "text" => __('US (default)','amazon-product-in-a-post-plugin')),
						"12" => array("value" => "com.au", "text" => __('Australia','amazon-product-in-a-post-plugin')),
						"1"  => array("value" => "com.br", "text" => __('Brazil','amazon-product-in-a-post-plugin')),
						"2"  => array("value" => "ca",     "text" => __('Canada','amazon-product-in-a-post-plugin')),
						"3"  => array("value" => "cn",     "text" => __('China','amazon-product-in-a-post-plugin')),
						"4"  => array("value" => "fr",     "text" => __('France','amazon-product-in-a-post-plugin')),
						"5"  => array("value" => "de",     "text" => __('Germany','amazon-product-in-a-post-plugin')),
						"6"  => array("value" => "co.jp",  "text" => __('Japan','amazon-product-in-a-post-plugin')),
						"7"  => array("value" => "in",     "text" => __('India','amazon-product-in-a-post-plugin')),
						"8"  => array("value" => "it",     "text" => __('Italy','amazon-product-in-a-post-plugin')),
						"9"  => array("value" => "com.mx", "text" => __('Mexico','amazon-product-in-a-post-plugin')),
						"13" => array("value" => "nl",     "text" => __('Netherlands','amazon-product-in-a-post-plugin')),
						"15" => array("value" => "sa",     "text" => __('Saudi Arabia','amazon-product-in-a-post-plugin')),
						"14" => array("value" => "sg",     "text" => __('Singapore','amazon-product-in-a-post-plugin')),
						"10" => array("value" => "es",     "text" => __('Spain','amazon-product-in-a-post-plugin')),
						"17" => array("value" => "se",     "text" => __('Sweden','amazon-product-in-a-post-plugin')),
						"16" => array("value" => "ae",     "text" => __('United Arab Emirates','amazon-product-in-a-post-plugin')),
						"11" => array("value" => "co.uk",  "text" => __('United Kingdom','amazon-product-in-a-post-plugin')),

					 )),
			array(	"name" => __('Amazon Affiliate ID','amazon-product-in-a-post-plugin'),
					"desc" => __('Your Amazon Affiliate ID','amazon-product-in-a-post-plugin'),'<br /><br />',
					"id" => "apipp_amazon_associateid",
					"type" => "text",
					"width" => '150'),
			array(	"name" => __('Amazon Access Key ID','amazon-product-in-a-post-plugin'),
					"desc" => __('Your Amazon Access Key ID (or Public Key). If you do not have one, you will need to sign up for one (click a link below):', 'amazon-product-in-a-post-plugin')."<br />
					<a target='_blank' href='https://affiliate-program.amazon.com.au/'>" .__('Australia', 'amazon-product-in-a-post-plugin')           ." (com.au)</a>, 
					<a target='_blank' href='https://associados.amazon.com.br/'>"        .__('Brazil', 'amazon-product-in-a-post-plugin')              ." (com.br)</a>, 
					<a target='_blank' href='https://associates.amazon.ca/'>"            .__('Canada', 'amazon-product-in-a-post-plugin')              ." (ca)</a>, 
					<a target='_blank' href='https://associates.amazon.cn/'>"            .__('China', 'amazon-product-in-a-post-plugin')               ." (cn)</a>, 
					<a target='_blank' href='https://partenaires.amazon.fr/'>"           .__('France', 'amazon-product-in-a-post-plugin')              ." (fr)</a>, 
					<a target='_blank' href='https://partnernet.amazon.de/'>"            .__('Germany', 'amazon-product-in-a-post-plugin')             ." (de)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.in/'>"     .__('India', 'amazon-product-in-a-post-plugin')               ." (in)</a>, 
					<a target='_blank' href='https://programma-affiliazione.amazon.it/'>".__('Italy', 'amazon-product-in-a-post-plugin')               ." (it)</a>, 
					<a target='_blank' href='https://affiliate.amazon.co.jp/'>"          .__('Japan', 'amazon-product-in-a-post-plugin')               ." (jp)</a>, 
					<a target='_blank' href='https://afiliados.amazon.com.mx/'>"         .__('Mexico', 'amazon-product-in-a-post-plugin')              ." (com.mx)</a>, 
					<a target='_blank' href='https://partnernet.amazon.nl/'>"            .__('Netherlands', 'amazon-product-in-a-post-plugin')         ." (nl)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.sa/'>"     .__('Saudi Arabia', 'amazon-product-in-a-post-plugin')        ." (sa)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.sg/'>"     .__('Singapore', 'amazon-product-in-a-post-plugin')           ." (sg)</a>, 
					<a target='_blank' href='https://afiliados.amazon.es/'>"             .__('Spain', 'amazon-product-in-a-post-plugin')               ." (es)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.se/'>"     .__('Sweden', 'amazon-product-in-a-post-plugin')              ." (se)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.ae/'>"     .__('United Arab Emirates', 'amazon-product-in-a-post-plugin')." (ae)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.co.uk/'>"  .__('United Kingdom', 'amazon-product-in-a-post-plugin')      ." (co.uk)</a>, 
					<a target='_blank' href='https://affiliate-program.amazon.com/'>"    .__('United States', 'amazon-product-in-a-post-plugin')       ." (com)</a>
					<br>".__('For general information, visit the','amazon-product-in-a-post-plugin')." <a target='_blank' href='https://docs.aws.amazon.com/'>'".__('Locale Reference for the Product Advertising API','amazon-product-in-a-post-plugin')."</a> ".__('page.','amazon-product-in-a-post-plugin'),
					"id" => "apipp_amazon_publickey",
					"type" => "text",
					"autocomplete" => "off"),
			array(	"name" => __('Amazon Secret Access Key','amazon-product-in-a-post-plugin'),
					"desc" => sprintf(__('Your Amazon Secret Access Key (or Private Key). %1$sCheck out this page%2$s for more information on the Access Key IDs and Secret Access Keys.','amazon-product-in-a-post-plugin'),'<a href="admin.php?page=apipp-main-menu">','</a>').'<br/><br/>',
					"id" => "apipp_amazon_secretkey",
					"type" => "password",
					"width" => '400',
					"autocomplete" => "off"),
			array(	"name" => __('Test Settings','amazon-product-in-a-post-plugin'),
					"desc" => __('Test your API settings to make sure everything is setup correctly. Save your settings before testing.','amazon-product-in-a-post-plugin').'<br/><br/>',
					"id" => "apipp_amazon_test_settings",
					"button_label" => __('Test API Settings','amazon-product-in-a-post-plugin'),
					"class" => 'button button-primary tgm-plugin-update-modal',
					"link" => add_query_arg( array( 'action' => 'action_appip_do_test', 'security' => wp_create_nonce( 'appip_ajax_do_settings_test' ),'tab' => 'changelog','width' => 600, 'height' => 500, 'plugin' => 'plugin-name', 'section' => 'changelog', 'TB_iframe' => true ), admin_url( 'admin-ajax.php' ) ),
					"wrapper" => "div",
					"wrapper_class" => "update-modal-holder",
					"type" => "button"),
			array(	"name" => __('Debug Key','amazon-product-in-a-post-plugin'),
					"desc" => sprintf(__('Your Custom Debug Key. This is <strong>unique</strong> to your site.<br/><br/>If you have problems with the plugin not working and you need help from the developers, email %1$s with this key and your website url or use the notes box below and click the "Developers Only" button. They will be able to use this info it to figure out issues and help resolve them for you. Without it, they can do nothing to help. If you received help and the problem is resolved, you can change this debug key to make sure the developers cannot access the debugging features again, should you feel inclined to do so.<br/><br/>If you would like to send debug info via email (to yourself or the developers), enter your email address below and click "Send Debug Info". If you need help and you want a copy to go to the <strong>plugin developers</strong>, click the "Copy Developers" button (if you add an email address and want it to be sent to you as well as the developers) or "Send to Developers Only" to just sned to the Developers without copying anyone else. <strong>If you send a copy to the Developers, be sure to include a note or we can\'t help you.</strong>','amazon-product-in-a-post-plugin'),'<a href="mailto:'.APIAP_HELP_EMAIL.'">'.APIAP_HELP_EMAIL.'</a>'),
					"id" => "apipp_amazon_debugkey",
					"type" => "text"),

			/*
			array(	"name" => __('API get method', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you are seeing BLANK products it may be because your server does not support the php file_get_contents() function. If that is the case, try CURL option to see if it resolves the problem. Default is File Get Contents method.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_API_call_method",
					"type" => "select",
					"options" => array(
						"0" => array("value" => "0","text" => "file_get_contents() (default)"),
						"1" => array("value" => "1","text" => "CURL"),
					 )),
			*/
			array(	"name" => 'General Tab End',
					"id" => 'apipp_general_tab_end',
					"type" => "tab-wrapper-end",
					),
			array(	"name" => 'Advanced Tab',
					"id" => 'apipp_advanced_tab',
					"tab" => 'advanced',
					"current_tab" => $current_tab,
					"type" => "tab-wrapper-start",
					),
			array(	"name" => __('Advanced Settings','amazon-product-in-a-post-plugin'),
					"type" => "heading"),
			array(	"name" => __('Cache Ahead?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Allows plugin to cache products prior to the WordPress loop. If you have products on posts list pages or categories, this may be required to prevent throttling from Amazon due to rapid API requests.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_amazon_cache_ahead",
					"type" => "checkbox"),
			array(	"name" => __('Cache Length','amazon-product-in-a-post-plugin'),
					"desc" => __('The product cache length in seconds (i.e., 60 min. = 3600 seconds). Recommend setting less than 24 hours (86400). Default is 3600.','amazon-product-in-a-post-plugin'),'<br /><br />',
					"id" => "apipp_amazon_cache_sec",
					"type" => "text",
					"width" => '100'),
			array(	"name" => __('Show metabox in edit page when Gutenberg is installed?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Show the Amazon Product Metabox on the edit page if the Gutenburg Editor is installed? Defaults to ture. You can turn this off if you have created products only using the BLOCKS method.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_show_metaboxes",
					"type" => "checkbox"),
			array(	"name" => __('Use Add To Cart URL?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Uses the "Add to Cart" URL instead of product page URL for links. This can help with 90 day Cookie Conversions.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_use_cartURL",
					"type" => "checkbox"),
			array(	"name" => __('Use Amazon Featured Image Functionality?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Adds a meta box section to the Edit Screen to add an Amazon Featured Image URL. Integrates into the WordPress Featured Image functionality.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_product_featured_image",
					"type" => "checkbox"),
			array(	"name" => __('Add Amazon Mobile Popover Functionality?', 'amazon-product-in-a-post-plugin'),
					"desc" => '<em style="color:#444;">'.__('Disabled until future update.','amazon-product-in-a-post-plugin').'</em> '.__('Check if you would also like to use Mobile Popovers. IMPORTANT! Do not use if you have Amazon OneLink on your site already, as it is not compatible with Mobile Popovers.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_product_mobile_popover",
					"type" => "checkbox-disabled"),
			array(	"name" => __('Hook plugin into Content?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you want to have the product displayed when the <code>the_content()</code> function is called, select this box. NOTE: This is the standard call that theme\'s use to display the page/post content. If you disable both Excerpt and Content, the only way you can add products to a page/post is to add the shortcode. You can override this on each individual page/post.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_hook_content",
					"type" => "checkbox"),
			array(	"name" => __('Hook plugin into Excerpt?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you want to have the product displayed when the <code>the_excerpt()</code> function is called, select this box. This is typically used on Post list or archive/category pages. Disable this function if your theme uses short excerpts on pages, such as the home page. You can override this on each individual page/post.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_hook_excerpt",
					"type" => "checkbox"),
			array(	"name" => __('Open Product Link in New Window?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you want to have the product displayed in a new window, check this box. Default is no.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_open_new_window",
					"type" => "checkbox"),
			array(	"name" => __('Show on Single Page Only?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you want to have the product displayed only when the page/post is singular, check this box. Default is no.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_show_single_only",
					"type" => "checkbox"),
					//apipp_amazon_use_lightbox
			array(	"name" => __('Use Internal Lightbox?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Use the the included Lightbox scripts for "larger image" popup. If you have a lightbox script already, you can turn this off so it will not conflict or show two popups.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_amazon_use_lightbox",
					"type" => "checkbox"),
			array(	"name" => __('Hide Binding in Title?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If selected, the binding will be not shown in the Product Title. i.e., With Binding - <code>Product Title (DVD)</code>, Without Binding - <code>Product Title</code>.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_hide_binding",
					"type" => "checkbox"),
			array(	"name" => __('Not Available Error Message', 'amazon-product-in-a-post-plugin'),
					"desc" => __('The message to display if the item is not available for some reason, i.e., your locale or no longer available.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_amazon_notavailable_message",
					"type" => "textlong"),
			array(	"name" => __('Amazon Hidden Price Message', 'amazon-product-in-a-post-plugin'),
					"desc" => __('For Some products, Amazon will hide the List price of a product. When hidden, this plugin cannot show a price for the product. This message will display in the List Price area when that occurs.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_amazon_hiddenprice_message",
					"type" => "textlong"),
			array(	"name" => __('Quick Fix - Hide Warnings?', 'amazon-product-in-a-post-plugin'),
					"desc" => '<span style="color:red;font-weight:bold;">'.__('IMPORTANT:', 'amazon-product-in-a-post-plugin').'</span> '.__('Checking this box will excecute the code, <code>ini_set("display_errors", 0); </code> to force stop NOTICE and WARNING messages. This can be helpful if your server php configuration has error reporting on and you are getting warning messages. This WILL override any setting you have in your php.ini or php config files. It is not recommended you turn this on unless you need it.', 'amazon-product-in-a-post-plugin') ."<br /><br />",
					"id" => "apipp_hide_warnings_quickfix",
					"type" => "checkbox"),
			array(	"name" => __('Clear data on Deactivate?', 'amazon-product-in-a-post-plugin'),
					"desc" => "<span style='color:red;font-weight:bold;'>".__('CAREFUL!!', 'amazon-product-in-a-post-plugin')."</span> ".__('Checking this box will delete ALL <strong>settings</strong> and <strong>database</strong> items when you deativate the plugin', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_uninstall",
					"type" => "checkbox"),
			array(	"name" => __('Remove ALL traces on Uninstall?', 'amazon-product-in-a-post-plugin'),
					"desc" => "<span style='color:red;font-weight:bold;'>".__('CAREFUL!!', 'amazon-product-in-a-post-plugin')."</span> ".__('Checking this box AND the above box will delete <em>ALL</em> Amazon shortcodes from posts and pages, and all meta data associated with this plugin will be cleaned up and cleared out when you deativate this plugin. As a safety precaution, both boxes must be checked or data will not be removed.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_uninstall_all",
					"type" => "checkbox"),
			array(	"name" => 'Advanced Tab End',
					"id" => 'apipp_advanced_tab_end',
					"type" => "tab-wrapper-end"),
			array(	"name" => 'Styles Tab',
					"id" => 'apipp_styles_tab',
					"tab" => 'styles',
					"current_tab" => $current_tab,
					"type" => "tab-wrapper-start"),
			array(	"name" => __('Plugin Styles','amazon-product-in-a-post-plugin'),
					"type" => "heading"),
			array(	"name" => __('Use My Custom Styles?', 'amazon-product-in-a-post-plugin'),
					"desc" => __('If you want to use your own styles, check this box and enter them below. Additionally, if you want to override any plugin styles, you can put your own styles in a CSS file called <code>appip-styles.css</code> located at: ', 'amazon-product-in-a-post-plugin').'<br/><code>'.get_stylesheet_directory_uri().'/appip-styles.css</code><br>or<br/><code>'.get_stylesheet_directory_uri()."/css/appip-styles.css</code><br /><br />",
					"id" => "apipp_product_styles_mine",
					"type" => "checkbox"),
			array(	"name" => __('Product Styles', 'amazon-product-in-a-post-plugin'),
					"desc" => __('Your Custom styles can go here. To reset the styles, remove all styles from textarea and then save the options - the default styles will be loaded.', 'amazon-product-in-a-post-plugin')."<br /><br />",
					"id" => "apipp_product_styles",
					"type" => "textareabig"),
			array(	"name" => 'Styles Tab End',
					"id" => 'apipp_styles_tab_end',
					"type" => "tab-wrapper-end",
					),
		);
		$options_apipp = apply_filters( 'amazon_product_options_additional_options', $options_apipp, $current_tab );
	}
	function apipp_options_add_subpage(){
		global $fullname_apipp, $shortname_apipp, $options_apipp;
		apipp_options_admin_page($fullname_apipp, 'apipp', $options_apipp);
	}

	function apipp_options_add_admin_page($themename,$shortname,$options) {
	$up_opt='';
	    if ( basename(__FILE__) == 'options.php' ) {
	    	if(isset($_REQUEST['action'])){
				$req_action = esc_attr( $_REQUEST['action'] );
			}else{
				$req_action = '';
			}
		    if( isset($_REQUEST[$shortname.'_option']) ){
				$req_option = esc_attr( $_REQUEST[$shortname.'_option'] );
			}else{
				$req_option = '';
			}
	        if ( 'save' == $req_action && $req_option == $shortname ) {
				check_ajax_referer( 'appip_options_nonce_ji9osdjfkjl', 'appip_nonce', true );
	                foreach ($options as $value) {
						if(isset($value['type']) && $value['type'] == 'multicheck'){
							if(isset($value['options']) && !empty($value['options'])){
							foreach($value['options'] as $mc_key => $mc_value){
								$up_opt = $value['id'].'_'.$mc_key;
								if( isset( $_REQUEST[ $up_opt ] ) ) {
									update_option( $up_opt, ($_REQUEST[ $up_opt ])  );
									$update_optionapp = ($_REQUEST[ $up_opt ]);
								} else {
									delete_option( $up_opt );
									$update_optionapp='';
								}
							}
							}
						}elseif(isset($value['type']) && $value['type'] == 'select'){
							foreach( $value['options'] as $mc_key => $mc_value ){
								$up_opt = $value['id'];
								if( isset( $_REQUEST[ $up_opt ] ) && ($_REQUEST[ $up_opt ] == $mc_value['value']) ) {
									update_option( $value['id'], $mc_value['value']);
								}
							}
						}else{
	                    	if( isset($value['id']) && isset( $_REQUEST[ $value['id'] ] ) ) {
								if( $value['id'] == 'apipp_amazon_publickey' || $value['id'] == 'apipp_amazon_secretkey' ){
									update_option( $value['id'], trim(($_REQUEST[ $value['id'] ]))  );
								}else{
									update_option( $value['id'], esc_attr(stripslashes($_REQUEST[ $value['id'] ]))  );
								}
	                    	} else {
								if(isset($value['id'])){
	                    			delete_option( $value['id'] );
								}
	                    	}
						}
					}
					if(
					(isset($_REQUEST['appip_debug_submit']) && $_REQUEST['appip_debug_submit'] != '' )
					||
					(isset($_REQUEST['appip_debug_submit_all']) && $_REQUEST['appip_debug_submit_all'] != '' )
					||
					(isset($_REQUEST['appip_debug_submit_dev']) && $_REQUEST['appip_debug_submit_dev'] != '' )
					){
						$temp = Amazon_Product_Debug_Info::generate_server_data(true);
					}
					amazon_product_delete_all_cache('option-update');
					$curtab = isset($_REQUEST['appip_current_tab']) && $_REQUEST['appip_current_tab'] !== '' ? esc_attr($_REQUEST['appip_current_tab']) : 'general';
	                wp_redirect("admin.php?page=".$shortname."_plugin_admin&saved=true".($curtab != '' ? '&tab='.$curtab : '' ),302);
	                die;

	        } else if( 'reset' == $req_action && $req_option == $shortname ) {
				check_ajax_referer( 'appip_options_nonce_ji9osdjfkjl', 'appip_nonce', true );
	            foreach ($options as $value) {
					if($value['type'] != 'multicheck'){
	                	delete_option( $value['id'] );
					}else{
						foreach($value['options'] as $mc_key => $mc_value){
							$del_opt = $value['id'].'_'.$mc_key;
							delete_option($del_opt);
						}
					}
				}
	            wp_redirect("admin.php?page=".$shortname."_plugin_admin&reset=true",302);
	            die;

	        }
	    }
	}

	function apipp_options_admin_page($themename, $shortname, $options) {
		//if ( get_option('apipp_product_styles') == ''){ update_option('apipp_product_styles',$thedefaultapippstyle); }
		if (isset($_REQUEST['dismissmsg']) && $_REQUEST['dismissmsg'] == '1'){update_option('appip_dismiss_msg',1);echo '<div id="message" class="updated fade"><p><strong>'.$themename.' message dismissed.</strong></p></div>';}
	    if (isset($_REQUEST['saved']) && $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	    if (isset($_REQUEST['reset']) &&  $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
	    if (isset($_REQUEST['debug_sent']) &&  $_REQUEST['debug_sent'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' Debug Information Sent.</strong></p></div>';
	?>
	<div class="wrap">
	<style type="text/css">
        .width150{width:150px;}
        .width200{width:200px;}
        .width250{width:250px;}
        .width300{width:300px;}
        .width350{width:350px;}
        .width400{width:400px;}
        a#show-hide-ask { font-size: .70em; outline: none; padding: 0 5px; text-decoration: none; font-style: italic; }
        a#show-hide-ask:focus { outline: none; }
        .align-top.table-row { max-width: 800px; margin: 1em auto; background-color: #fff; border: 1px solid #dacece; padding: 1em; }
        .option-th { width: 100%; max-width: 100%; padding: 0 0 .5em 0; font-weight: normal; font-size: 1.25rem; border-bottom: 1px solid #dacece; margin-bottom: .5em; }
        .option-val { width: 100%; max-width: 100%; padding: 0; font-size: 1.25rem; display: block; position: relative; box-sizing: border-box; }
        input[type="checkbox"][id^="apipp_"] { position: absolute; left: 0; top: .75em; }
        .table-row * { box-sizing: border-box; }
        .option-val input[type="checkbox"] + .align-top { padding-left: 1.75em; }
        .option-val .align-top {padding-top: .25em; font-size: .70em; line-height: 1.25em; color: #1e8cbe; }
        .amazon-pip-options code { background: rgba(62, 148, 198, 0.2); padding: 0 .3em .2em; margin: 0; white-space: normal; }
        textarea#apipp_product_styles { width: 100%; max-width: 100%; font-family: monospace; line-height: 1.5em; color: #0c05c6; height: 250px; }
        .appip-options-heading h2 { font-weight: normal; font-size: 1.75em; padding: .5em 0; margin: 0; color: #1e8cbe; }
        p.submit { max-width: 800px; margin: 1em auto; background-color: #fff; border: 1px solid #dacece; padding: 1em; }
        .appip-options-heading { max-width: 800px; margin: 1em auto; background-color: #fff; border: 1px solid #dacece; padding: 1em; }
		@media screen and (max-width:600px){
			select,input[type="text"],input[type="textlong"],input[type="password"],textarea{width:100% !important;max-width:100%;}
			.option-val input[type="checkbox"] + .align-top { padding-left: 3em; }
			.width150,.width200,.width250,.width300,.width350,.width400{width:100% !important; max-width:100% !important;}
			.optiontable .option-th{text-align:center;width: 100%;display: block;max-width: 100%;vertical-align: top;}
			.optiontable .option-val{display:block;text-align:left;width: 100%;}
			.optiontable > div{display:block;}
			.optiontable > div.dnone{display:none;}
            .option-val .align-top { padding-top: .25em; font-size: .75em; line-height: 1.5em; color: #1e8cbe; }
		}
    </style>
	<h2><?php echo $themename; ?> Settings/Options</h2>

<?php
	if(isset($_REQUEST['notice_sent']) && isset($_REQUEST['sent'])){
		$sent 	= esc_attr(urldecode($_REQUEST['sent']));
		$notice = (int) $_REQUEST['notice_sent'];
		if($notice == 0 && $sent != ''){
			if($sent == 'noemail'){
				echo '<div class="notice notice-error is-dismissible"><p>'.__('No Email Address Provided.', 'amazon-product-in-a-post-plugin').'</p></div>';
			}else{
				echo '<div class="notice notice-error is-dismissible"><p>'.__('Error: ', 'amazon-product-in-a-post-plugin'). $sent.'</p></div>';
			}
		}elseif(($notice == 1 || $notice == 2 ) && $sent != ''){
			echo '<div class="notice notice-success is-dismissible"><p>'.__('Success! Debug info sent to ').$sent.'</p></div>';
		}
		/*
		<div class="notice notice-success is-dismissible"><p>This is a success message.</p></div>
		<div class="notice notice-info is-dismissible"><p>This is some information.</p></div>
		*/
	}
	$current_tab = isset( $_GET['tab'] ) ? esc_attr($_GET['tab']) : 'general';
	echo '<h2 class="nav-tab-wrapper">';
	echo '<a id="general" class="appiptabs nav-tab ' . ($current_tab == 'general' ? 'nav-tab-active' : '') . '" href="?page=apipp_plugin_admin&tab=general">API Options</a>';
	echo '<a id="advanced" class="appiptabs nav-tab ' . ($current_tab == 'advanced' ? 'nav-tab-active' : '') . '" href="?page=apipp_plugin_admin&tab=advanced">Advanced Options</a>';
	echo '<a id="styles" class="appiptabs nav-tab ' . ($current_tab == 'styles' ? 'nav-tab-active' : '') . '" href="?page=apipp_plugin_admin&tab=styles">Styles</a>';
	$AddlTabs =  apply_filters('amazon_product_options_additional_tabs', array());
	if(is_array($AddlTabs) && !empty($AddlTabs)){
		foreach($AddlTabs as $key => $AddlTab){
			$tab_slug = is_array($AddlTab) && isset($AddlTab['slug']) ? sanitize_title(esc_attr($AddlTab['slug'])) : '';
			$tab_name = is_array($AddlTab) && isset($AddlTab['name']) ? esc_attr($AddlTab['name']) : '{Name}';
			if( $tab_slug !== '' ){
				if( in_array( $tab_slug, array( 'general', 'advanced', 'styles' ) ) ){
					appip_write_log('WARNING: You cannot use \'general\',\'advanced\',\'styles\' as a tab name for the filter \'amazon_product_options_additional_tabs\'');
				}else{
					echo '<a id="' .$tab_slug.'" class="appiptabs nav-tab ' .$tab_slug.'" href="?page=apipp_plugin_admin&tab='.$tab_slug.'">'.$tab_name.'</a>';
				}
			}
		}
	}
	echo '</h2>';
?>
<div class="tab-content">
	<form method="post" action="" autocomplete="off" class="amazon-pip-options">
	<input type="hidden" name="<?php echo $shortname; ?>_option" id="<?php echo $shortname; ?>_option" value="<?php echo $shortname; ?>">
    <input type="hidden" name="appip_current_tab" id="appip_current_tab"  value="<?php echo $current_tab;?>">
    <input type="hidden" name="form-changed-awsplugin" id="form-changed-awsplugin"  value="false">
	<?php wp_nonce_field( 'appip_options_nonce_ji9osdjfkjl', 'appip_nonce' ); ?>
	<div class="optiontable">
	<?php foreach ($options as $key => $value) {
		$autoComplete = isset($value['autocomplete']) && $value['autocomplete'] == "off" ? ' autocomplete="off"' : '';
		$value['std'] = isset($value['std']) ? $value['std'] : '';
		switch ( $value['type'] ) {
			case 'tab-wrapper-start':
				$currTab	= $value['current_tab'];
				$tabID 		= $value['id'];
				$tabName 	= $value['tab'];
				echo '<div id="'.$tabName.'-content" class="nav-tab-content'.($current_tab == $tabName ? ' active' : '').'" style="'.($current_tab == $tabName ? 'display:block;' : 'display:none;').'">';
				break;
			case 'tab-wrapper-end':
				echo '</div>';
				break;
			case 'text':
			apipp_option_wrapper_header($value);
			$width = isset($value['width']) && $value['width']!= '' ? 'width'.$value['width'] : 'width300';
			$placeholder = isset($value['placeholder']) && $value['placeholder']!= '' ? esc_attr($value['placeholder']) : '';
			?>
			        <input class="<?php echo $width;?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo (get_option( $value['id'] )); } else { echo ($value['std']); } ?>"<?php echo $autoComplete; echo $placeholder != '' ? 'placeholder="'.$placeholder.'"' : '';?>>
			<?php
			apipp_option_wrapper_footer($value, $value['id']);
			break;
			case 'password':
			apipp_option_wrapper_header($value);
			$width = isset($value['width']) && $value['width']!= '' ? 'width'.$value['width'] : 'width300';
			$typeis = (get_option( $value['id'] ) == "" && $value['std'] == "" ) ? 'text' : 'password';
			?>
			        <input class="<?php echo $width;?> password-field" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $typeis;?>" value="<?php if ( get_option( $value['id'] ) != "") { echo (get_option( $value['id'] )); } else { echo ($value['std']); } ?>"<?php echo $autoComplete;?>> <a href="#" id="show-hide-ask" data-pwid="<?php echo $value['id']; ?>">show</a>
			<?php
			apipp_option_wrapper_footer($value);
			break;
			case 'textlong':
			apipp_option_wrapper_header($value);
			?>
			        <input style="width:95%;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo ( get_option( $value['id'])); } else { echo ($value['std']); } ?>"<?php echo $autoComplete;?>>
			<?php
			apipp_option_wrapper_footer($value);
			break;

			case 'select':
			apipp_option_wrapper_header($value);
			$selectOption = get_option( $value['id'] , '' );
			$selectDefault = $selectOption === '' && isset($value['std']) && $value['std'] !== '' ? $value['std'] : $selectOption;
			?>
		            <select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"<?php echo $autoComplete;?>>
		                <?php foreach ($value['options'] as $option) { ?>
		                <option<?php if ( $selectDefault === $option["value"]) { echo ' selected="selected"'; }?> value="<?php echo $option["value"]; ?>"><?php echo ($option["text"]); ?></option>
		                <?php } ?>
		            </select>
			<?php
			apipp_option_wrapper_footer($value);
			break;

			case 'cat_select':
				apipp_option_wrapper_header($value);
				$categories = get_categories('hide_empty=0');
				?>
			            <select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
							<?php foreach ($categories as $cat) {
							if ( get_option( $value['id'] ) == $cat->cat_ID) { $selected = ' selected="selected"'; } else { $selected = ''; }
							$opt = '<option value="' . $cat->cat_ID . '"' . $selected . '>' . $cat->cat_name . '</option>';
							echo $opt; } ?>
			            </select>
				<?php
				apipp_option_wrapper_footer($value);
				break;

			case 'textarea':
				//$ta_options = $value['options'];
				apipp_option_wrapper_header($value);
				?>
						<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="style-text-sm"><?php
						if( get_option($value['id']) != "") {
								echo stripslashes(get_option($value['id']));
							}else{
								echo $value['std'];
						}?></textarea>
				<?php
					apipp_option_wrapper_footer($value);
				break;

			case 'textareabig':
				//$ta_options = $value['options'];
				apipp_option_wrapper_header($value);

				?>
				<div class="<?php echo $value['id']; ?>-wrapper">
					<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="style-text"><?php
						if( get_option($value['id']) != "") {
								echo stripslashes(get_option($value['id']));
							}else{
								echo $value['std'];
						}?></textarea>
				</div>
				<?php
				apipp_option_wrapper_footer($value);
				break;

			case "radio":
				apipp_option_wrapper_header($value);
		 		foreach ($value['options'] as $key=>$option) {
						$radio_setting = get_option($value['id']);
						if($radio_setting != ''){
				    		if ($key == get_option($value['id']) ) {
								$checked = "checked=\"checked\"";
								} else {
									$checked = "";
								}
						}else{
							if($key == $value['std']){
								$checked = "checked=\"checked\"";
							}else{
								$checked = "";
							}
						}?>
			            <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?>><?php echo $option; ?><br />
				<?php
				}

				apipp_option_wrapper_footer($value);
				break;
			case "checkbox-disabled":
				apipp_option_wrapper_header($value);
								if( (bool) get_option($value['id'], false) ){
									$checked = "checked=\"checked\"";
								}else{
									$checked = "";
								}
								//echo '+==+'.$value['id'].':'.(bool) get_option($value['id'], false).'+==+';
							?>
				            <input type="checkbox" disabled="disabled" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?>>
				<?php
				apipp_option_wrapper_footer($value);
				break;

			case "checkbox":
				apipp_option_wrapper_header($value);
								if( (bool) get_option($value['id'], false) ){
									$checked = "checked=\"checked\"";
								}else{
									$checked = "";
								}
								//echo '+==+'.$value['id'].':'.(bool) get_option($value['id'], false).'+==+';
							?>
				            <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?>>
				<?php
				apipp_option_wrapper_footer($value);
				break;

			case "multicheck":
				apipp_option_wrapper_header($value);
		 		foreach ($value['options'] as $key=>$option) {
			 			$pn_key = $value['id'] . '_' . $key;
						$checkbox_setting = get_option($pn_key);
						if($checkbox_setting != ''){
				    		if (get_option($pn_key) ) {
								$checked = "checked=\"checked\"";
								} else {
									$checked = "";
								}
						}else{
							if($key == $value['std']){
								$checked = "checked=\"checked\"";
							}else{
								$checked = "";
							}
						}?>
			            <input type="checkbox" name="<?php echo $pn_key; ?>" id="<?php echo $pn_key; ?>" value="true" <?php echo $checked; ?>><label for="<?php echo $pn_key; ?>"><?php echo $option; ?></label><br />
				<?php
				}

				apipp_option_wrapper_footer($value);
				break;

			case "button":
				apipp_option_wrapper_header($value);
				$wrappcl 	= isset($value['wrapper_class']) &&  $value['wrapper_class'] != '' ? 'class="'.$value['wrapper_class'].'"' : '';
				$wrapp 		= isset($value['wrapper']) &&  $value['wrapper'] != '' ? array('start' => '<'.$value['wrapper'].' '.$wrappcl.'>', 'end' => '</'.$value['wrapper'].'>') : array('start' => '', 'end' => '') ;
				echo $wrapp['start'].'<a class="'.$value['class'].'" href="'.$value['link'].'" id="'.$value['id'].'" aria-label="'.$value['button_label'].'" data-title="'.$value['button_label'].'">'.$value['button_label'].'</a>'.$wrapp['end'];
				apipp_option_wrapper_footer($value);
				break;
			case "heading":
				?>
				<div class="appip-options-heading">
				    <div style="text-align: left;"><h2><?php echo $value['name']; ?></h2></div>
				</div>
				<?php
				break;

			default:
				break;
		}
	}
	?>

	</div>
	<p class="submit"><input name="save" type="submit" value="Save changes" class="button-primary" /><input type="hidden" name="action" value="save"></p>
	</form>
</div>
	<?php
	}
	function apipp_option_wrapper_header($values){
		?>
		<div class="align-top table-row">
		    <div scope="row" class="option-th"><?php echo $values['name']; ?></div>
		    <div class="option-val">
		<?php
	}
	function apipp_option_wrapper_footer($values, $id = ''){
		?>
		    	<div class="align-top"><?php echo $values['desc']; ?></div>
                <?php
				if($id == 'apipp_amazon_debugkey' ){
					echo "<br /><br />".Amazon_Product_Debug_Info::get_email_input();
					Amazon_Product_Debug_Info::generate_server_data();
				} ?>
			</div>
		</div>
		<?php
	}