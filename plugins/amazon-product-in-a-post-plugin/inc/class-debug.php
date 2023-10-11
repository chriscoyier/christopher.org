<?php
class Amazon_Product_Debug_Info {

	public static function generate_server_data($send = false, $main_debug = false) {
		global $wpdb;
		global $wp_version;
		$sqlversion 		= $wpdb->get_var( "SELECT VERSION() AS version" );
		$mysqlinfo  		= $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		$sql_mode 			= is_array( $mysqlinfo )  && !empty( $sql_mode ) ? $mysqlinfo[0]->Value : __( 'Not set', 'amazon-product-in-a-post-plugin' );
		$allow_url_fopen 	= ini_get( 'allow_url_fopen' ) ? __( 'On', 'amazon-product-in-a-post-plugin' ) : __( 'Off', 'amazon-product-in-a-post-plugin' );
		$max_execute 		= ini_get( 'max_execution_time' ) ? ini_get( 'max_execution_time' ) : __( 'N/A', 'amazon-product-in-a-post-plugin' );
		$memory_limit 		= ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : __( 'N/A', 'amazon-product-in-a-post-plugin' );
		$memory_usage 		= function_exists( 'memory_get_usage' ) ? round( memory_get_usage() / 1024 / 1024, 2 ) . __( ' MByte', 'amazon-product-in-a-post-plugin' ) : __( 'N/A', 'amazon-product-in-a-post-plugin' );
		//$xml 				= is_callable( 'xml_parser_create' ) ? __( 'Yes', 'amazon-product-in-a-post-plugin' ): __( 'No', 'amazon-product-in-a-post-plugin' );
		//$xml2 			= extension_loaded( 'SimpleXML' ) ? __( 'Yes', 'amazon-product-in-a-post-plugin' ): __( 'No', 'amazon-product-in-a-post-plugin' );
		$current_user 		= wp_get_current_user();
    	$currentuserEmail 	= $current_user->user_email ;
		$currentuserName 	= isset($current_user->display_name) ? $current_user->display_name : 'WordPress User';
		$ms 				= function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'amazon-product-in-a-post-plugin' ) : __( 'No', 'amazon-product-in-a-post-plugin' );
		$site_title     	= get_bloginfo( 'name' );
		$language       	= get_bloginfo( 'language' );
		$theme 				= wp_get_theme();
		$siteurl        	= get_option( 'siteurl' );
		$homeurl        	= get_option( 'home' );
		$db_version     	= get_option( 'db_version' );
		$front_displays 	= get_option( 'show_on_front' );
		$page_on_front  	= get_option( 'page_on_front' );
		//$blog_public    	= get_option( 'blog_public' );
		$perm_struct    	= get_option( 'permalink_structure' );
		$appip_publickey 	= get_option('apipp_amazon_publickey', 'Not Set');
		$appip_privatekey 	= base64_encode(get_option('apipp_amazon_secretkey', 'Not Set'));
		$appip_partner_id 	= get_option('apipp_amazon_associateid', 'Not Set');
		$appip_dismiss 		= (bool)get_option('appip_dismiss_msg');
		$apipphookexcerpt 	= (bool)get_option('apipp_hook_excerpt'); 
		$apipphookcontent 	= (bool)get_option('apipp_hook_content'); 
		$apippopennewwindow	= (bool)get_option('apipp_open_new_window'); 
		$apip_getmethod 	= get_option('apipp_API_call_method', 'wp_remote_request');
		$encodemode 		= get_option('appip_encodemode', 'Not Set'); 
		$cacheSec 			= (int) apply_filters( 'amazon_product_post_cache',  get_option( 'apipp_amazon_cache_sec', 3600 ) );
		$debug_key			= get_option('apipp_amazon_debugkey');
		$region 			= get_option('apipp_amazon_locale','.com (default)'); 
		$single_only 		= (bool)get_option('apipp_show_single_only');
		$cache_ahead 		= (bool)get_option('apipp_amazon_cache_ahead');
		$use_SSL_img 		= (bool)get_option('apipp_ssl_images');
		$use_own_styles		= (bool)get_option("apipp_product_styles_mine", false);	
		//$styles_ver			= get_option("apipp_product_styles_default_version","2.1");
		$apipp_use_cartURL  = (bool)get_option("apipp_use_cartURL");
		$uselightbox 		= (bool)get_option('apipp_amazon_use_lightbox', true);
		$hasCurl 			= function_exists('curl_version') ? "Yes" : "No";
		$appip_version		= APIAP_PLUGIN_VER;
		$appip_dbversion	= APIAP_DBASE_VER;
		$wpDebug			= defined('WP_DEBUG') && (bool) WP_DEBUG == true ? 'On' : 'Off';
		$hide_binding		= (bool)get_option('apipp_hide_binding');
		$hide_warnings 		= (bool)get_option('apipp_hide_warnings_quickfix');
		$uninstall 			= (bool)get_option('apipp_uninstall');
		$uninstall_all 		= (bool)get_option('apipp_uninstall_all');	
		$db_trouble 		= (bool)get_option('apipp_db_trouble', false); // added 4.0.3.8
		$show_metaboxes		= (bool)get_option('apipp_show_metaboxes', true); // added 4.0.3.8
		$appiptable			= $wpdb->prefix . 'amazoncache';
		$check_table 		= $wpdb->get_var("SHOW TABLES LIKE '{$appiptable}'");
		
		$debug_info         = array(
			'Operating System'            => PHP_OS,
			'Server'                      => $_SERVER['SERVER_SOFTWARE'],
			'Memory usage'                => $memory_usage,
			'MYSQL Version'               => $sqlversion,
			'SQL Mode'                    => $sql_mode,
			'PHP Version'                 => PHP_VERSION,
			'PHP Allow URL fopen'         => $allow_url_fopen,
			'PHP Curl Enabled'			  => $hasCurl,
			'PHP Memory Limit'            => $memory_limit,
			'PHP Max Script Execute Time' => $max_execute,
			//'PHP XML support'             => $xml,
			//'PHP SimpleXMLElement'        => $xml2,
			'WordPress SETTINGS'		  => 'divider',
			'Admin Email Address'		  => get_bloginfo('admin_email'),
			'User Email Address'		  => $currentuserEmail,
			'WP_DEBUG'					  => $wpDebug,
			'Site URL'                    => $siteurl.'/',
			'Home URL'                    => $homeurl.'/',
			'WordPress Version'           => $wp_version,
			'WordPress DB Version'        => $db_version,
			'Multisite'                   => $ms,
			'Active Theme'                => $theme['Name'] . ' ' . $theme['Version'],
			'Site Title'                  => $site_title,
			'Site Language'               => $language,
			'Front Page Displays'         => $front_displays === 'page' ? $front_displays . ' [ID = ' . $page_on_front . ']' : $front_displays,
			//'Search Engine Visibility'    => $blog_public,
			'Permalink Setting'           => $perm_struct,
			'Gutenberg Active'			  => (appip_check_blockEditor_is_active() ? 'Yes' : 'No'),
			'PLUGIN SETTINGS'		  	  => 'divider',
			'APIAP Version' 			  => $appip_version,
			'APIAP DB Version' 			  => $appip_dbversion,
			'Access Key ID' 			  => $appip_publickey,
			'Secret Key' 				  => $appip_privatekey,
			'Affiliate ID' 				  => $appip_partner_id,
			'Debug Key'					  => $debug_key,
			'Database Table Name'		  => ($check_table !== '' ? $check_table : 'Not present!'), // new in 4.0.3.8
			'Database Trouble'		  	  => ($db_trouble ? 'Yes' : 'No'), // new in 4.0.3.8
			'Show Metaboxes'		  	  => ($show_metaboxes ? 'Yes' : 'No'), // new in 4.0.3.8
			'Dismiss Key Notice'		  => ($appip_dismiss ? 'Yes' : 'No'),
			'Hook Excerpt' 			 	  => ($apipphookexcerpt ? 'Yes' : 'No'),
			'Hook Content' 				  => ($apipphookcontent ? 'Yes' : 'No'),
			'Links Open New Window'		  => ($apippopennewwindow ? 'Yes' : 'No'), 
			'Use cart URL?'				  => ($apipp_use_cartURL ? 'Yes' : 'No'),
			'API Request Method' 		  => 'wp_remote_request',//((int)$apip_getmethod == 1 ? 'CURL' :'fopen' ). ' (`wp_remote_request` v3.7.1+)',
			'Encode Mode' 				  => $encodemode,
			'Cache Secs' 				  => $cacheSec,
			'Region/Locate'				  => $region,
			'Show on Single Only'		  => ($single_only ? 'Yes' : 'No'),
			'Enable Cache Ahead' 		  => ($cache_ahead ? 'Yes' : 'No'),
			//'Use SSL imgs' 				  => ($use_SSL_img ? 'Yes' : 'No') . ' (N/A v3.7+)',
			'Use Custom CSS'			  => ($use_own_styles ? 'Yes' : 'No'),
			'Use Internal lightbox' 	  => ($uselightbox ? 'Yes' : 'No'),
			//'Styles Version'			  => $styles_ver,
			'Hide Binding in Title'		  => ($hide_binding ? 'Yes' : 'No'),
			'Quick Fix' 				  => ($hide_warnings ? 'Yes' : 'No'),
			'Clear data' 				  => ($uninstall ? 'Yes' : 'No'),
			'Remove ALL traces' 		  => ($uninstall_all ? 'Yes' : 'No'),		
		);
		$debug_info['Active Plugins'] =  'divider';
		if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
    	}
		$active_plugins               = array();
		$active_dropins               = array();
		$inactive_plugins             = array('Inactive Plugins'=>'divider');
		$mu_plugins             	  = array();
		$plugins                      = get_plugins();
		$muplugins                    = get_mu_plugins();
		$dropins                      = get_dropins();
		foreach ( $plugins as $path => $plugin ) {
			if ( is_plugin_active( $path ) ) {
				$active_plugins ['active: '.$plugin['Name'] ] = $plugin['Version'];
			} else {
				$inactive_plugins ['inactive: '.$plugin['Name']] = $plugin['Version'];
			}
		}
		foreach ( $muplugins as $path => $plugin ) {
			$mu_plugins ['must-use: '.$plugin['Name'] ] = ($plugin['Version'] != '' ? $plugin['Version'] : 'N/A');
		}
		foreach ( $dropins as $path => $plugin ) {
			$active_dropins ['drop-in: '.$plugin['Name'] ] = ($plugin['Version'] != '' ? $plugin['Version'] : 'N/A');
		}
		$debug_info = array_merge($debug_info, $mu_plugins );
		$debug_info = array_merge($debug_info, $active_dropins );
		$debug_info = array_merge($debug_info, $active_plugins );
		$debug_info = array_merge($debug_info, $inactive_plugins );

		$mail_text = 'Amazon Product In a Post Debug Info' . "\r\n----------------------\r\n\r\n";
		$page_text = '<style>ul.debug_settings {font-size: 13px;font-weight: normal;font-family: monospace;padding-left: 20px;}ul.debug_settings li {margin: 0;}h2.debug_title {font-size: 18px;font-family: monospace; margin: 15px 0 5px 0;}</style>';
		$page_text .= '<h2 class="debug_title">SERVER SETTINGS</h2><ul class="debug_settings">';
		if ( ! empty( $debug_info ) ) {
			foreach ( $debug_info as $name => $value ) {
				if ( $value === 'divider') {
					$page_text .= "</ul><h2 class=\"debug_title\">$name</h2><ul class='debug_settings'>";
					$mail_text .= "\r\n$name\r\n-----------------\r\n";
				} else {
					if($name == 'Secret Key'){
						if($main_debug)
							$value1 = $value;
						else
							$value1 = "Not Shown";
					}else{
						$value1 = $value;
					}
					$page_text .= "<li><strong>$name:</strong> $value1</li>";
					$mail_text .= "$name: $value\r\n";
				}
			}
		}

		do {
			
			if ( isset($_REQUEST['appip_debug_submit']) || isset($_REQUEST['appip_debug_submit_all']) || isset($_REQUEST['appip_debug_submit_dev']) ) {
				$nonce = $_REQUEST['appip_debug_nonce'];
				if ( ! wp_verify_nonce( $nonce, 'appip-debug-nonce' ) ) {
					echo "<div class='appip_debug_error'>" . __( 'Form submission error: verification check failed.', 'amazon-product-in-a-post-plugin' ) . '</div>';
					break;
				}
				$email 			= '';
				$allemail 		= false;
				$devemailonly	= false;
				$notes			= isset($_REQUEST['appip_debug_notes']) ? wp_strip_all_tags($_REQUEST['appip_debug_notes']) : '';
				$headers 		= array();
				$headers[] 		= 'Reply-To: '.$currentuserName.' <'.$currentuserEmail.'>';
				$debugURL 		= 'Debug URL:'."\r\n".$siteurl.'/?appip_debug='.$debug_key.'&keycheck='.(md5(get_bloginfo( 'url' ) . '/?appip_debug=' . $debug_key));
				if($notes != ''){
					$mail_text = $notes ."\r\n\r\n".$debugURL ."\r\n\r\n===================\r\n".$mail_text;
				}else{
					$mail_text = $debugURL ."\r\n\r\n===================\r\n".$mail_text;
				}
				
				if ( isset( $_REQUEST['appip_debug_send_email'] ) )
					$email 		= sanitize_email( $_REQUEST['appip_debug_send_email'] );
				if ( isset( $_REQUEST['appip_debug_submit_all'] ) )
					$allemail 	= true;
				if ( isset( $_REQUEST['appip_debug_submit_dev'] ) )
					$devemailonly 	= true;
				if ( $email ) {
					$send = true;
					if($allemail){
						$to = APIAP_HELP_EMAIL.';'.$email;
						$totxt = APIAP_HELP_EMAIL.' and '.$email;
					}else{
						$to = $email;
						$totxt = $email;
					}
					if ( wp_mail( $to , sprintf( __( 'APPIP Debug Mail From Site %s.', 'amazon-product-in-a-post-plugin' ), $siteurl ), $mail_text, $headers, array() ) ) {
						wp_redirect( admin_url('admin.php?page=apipp_plugin_admin&notice_sent=1&sent='.urlencode($totxt)));
						exit;
					} else {
						wp_redirect( admin_url('admin.php?page=apipp_plugin_admin&notice_sent=0&sent=Could+not+send.'));
						exit;
					}
				}elseif( $devemailonly ){
					$send = true;
					if ( wp_mail( APIAP_HELP_EMAIL, sprintf( __( 'APPIP Debug Mail From Site %s.', 'amazon-product-in-a-post-plugin' ), $siteurl ), $mail_text, $headers, array() ) ) {
						wp_redirect( admin_url('admin.php?page=apipp_plugin_admin&notice_sent=2&sent='.urlencode(APIAP_HELP_EMAIL)));
						exit;
					} else {
						wp_redirect( admin_url('admin.php?page=apipp_plugin_admin&notice_sent=0&sent=Failed+to+send.'));
						exit;
					}
				} else {
					wp_redirect( admin_url('admin.php?page=apipp_plugin_admin&notice_sent=0&sent=noemail'));
					exit;
				}
			}
		} while ( 0 ); 
		$buf = "<ul class='appip_debug_settings'>\n{$page_text}\n</ul>\n";
		if($send == false)
			echo $buf;
		else
			return $buf;
	}

	public static function get_email_input() {
		$nonce = wp_create_nonce( 'appip-debug-nonce' );
		$buf   = '
			<input name="appip_debug_send_email" type="text" value="" style="width:250px;" placeholder="' . __( 'E-mail debug information', 'amazon-product-in-a-post-plugin' ) . '">
			<input name="appip_debug_nonce" type="hidden" value="' . $nonce . '">
			<input name="appip_debug_submit" type="submit" value="' . __( 'Send Debug Info', 'amazon-product-in-a-post-plugin' ) . '" class="button-primary"><span style="font-size:10px;"> </span><input name="appip_debug_submit_all" type="submit" value="' . __( 'Copy Developers', 'amazon-product-in-a-post-plugin' ) . '" class="button-primary"><span style="font-size:10px;"> or </span><input name="appip_debug_submit_dev" type="submit" value="' . __( 'Send to Developers Only', 'amazon-product-in-a-post-plugin' ) . '" class="button-primary"><br/><br/>
			<textarea name="appip_debug_notes" style="height:50px;" placeholder="' . __( 'Brief Notes or Issues, if sending to Developers', 'amazon-product-in-a-post-plugin' ) . '"></textarea>
			<span class="appip_debug_settings_click">Show Debug Info</span>
		';
		return $buf;
	}
}
add_filter( 'parse_request', 'apipp_parse_debug' );

function apipp_parse_debug($query){
	$amzdubug =  isset( $_GET[ 'appip_debug' ] ) && $_GET[ 'appip_debug' ] != '' ?  $_GET[ 'appip_debug' ] : '';
	$amzdubug =  isset( $_GET[ 'amazon-debug' ] ) && $_GET[ 'amazon-debug' ] != '' ?  $_GET[ 'amazon-debug' ] : $amzdubug;
	$debugkey = $amzdubug != '' ? get_option( 'apipp_amazon_debugkey', '' ) : '';
	
	if ( $amzdubug != '' && $amzdubug === $debugkey ) {
		$siteKey 	= get_bloginfo( 'url' ) . '/?appip_debug=' . $debugkey;
		$addlCheck 	= md5( $siteKey );
		$keycheck	= isset( $_GET[ 'keycheck' ] ) &&  $_GET[ 'keycheck' ] !== '' ? $_GET[ 'keycheck' ] : '';
		if ( $keycheck !== $addlCheck ) {
			wp_die( 'No Permission', 'You do not have permission to access this page.' );
			exit;
		}
		global $debuggingAPPIP;
		global $wpdb;
		$phpinfo	= isset( $_GET[ 'phpinfo' ] ) && (int) $_GET[ 'phpinfo' ] === 1 ? true : false;
		$debuggingAPPIP = true;
		echo '
			<!doctype html>
			<html>
			<head>
			<meta charset="utf-8">
			<title>Amazon Product In a Post Debug</title>
			</head>
			<body style="background-color:#fff;font-size:16px;font-family:sans-serif;">
			';
		$debugdata = Amazon_Product_Debug_Info::generate_server_data(false, true);
		echo $debugdata;
		$sample = new Amazon_Product_New_Request( 'debug' );
		echo '<h2 class="debug_title">Sample Request:</h2>';
		echo '<ul class="debug_settings"><li styke="font-size: 13px;font-weight: normal;font-family: monospace;">';
		$sample->appip_do_settings_test_debug();
		echo '</li></ul>';
		$checksql = "SELECT Body, ( NOW() - Updated ) as Age FROM " . $wpdb->prefix . "amazoncache ORDER BY Updated DESC;";
		$result = $wpdb->get_results( $checksql );
		echo '<div style="border:1px solid #cccccc; padding:10px; margin:10px 0;font-family:courier; font-size:12px;overflow: auto;">';
		echo '<h2>Amazon Product CACHE</h2>';
		if ( !empty( $result ) ) {
			echo '<pre>';
			foreach ( $result as $psxml ) {
				echo '[Body]: ' . esc_attr( htmlspecialchars( $psxml->Body ) ) . '<br/>';
				echo '[Age]: ' . $psxml->Age . '<br/>';
			}
			echo '</pre>';		}
		echo '</div>';
		if($phpinfo){
			echo '<div style="border:1px solid #cccccc; padding:10px; margin:10px 0;">';
			echo '<h2>PHP Info</h2>';
			phpinfo();
			echo '</div>';
		}
		echo '</body>
		</html>';
		exit;
	}
	return $query;
}