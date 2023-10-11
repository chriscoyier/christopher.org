<?php
class Amazon_Product_New_Request{
	var $type;
	function __construct($type ='ajax'){
		$this->type = $type;
		if($type == 'ajax'){
			add_action( 'wp_ajax_action_appip_do_test', array($this,'appip_do_settings_test_ajax') );// register ajax test
		}elseif($type ='parent'){
			add_action( 'wp_ajax_action_appip_do_test', array($this,'appip_do_settings_test_parent') );// register ajax test
		}
	}
	function appip_do_product_ajax(){
		check_ajax_referer( 'appip_ajax_do_product', 'security', true );
		if( current_user_can( 'manage_options' ) ){
			$test = $this->test_API();
			global $wp_scripts;
			global $wp_styles;
			if (is_a($wp_scripts, 'WP_Scripts'))
			  $wp_scripts->queue = array();
			if (is_a($wp_styles, 'WP_Styles'))
			  $wp_styles->queue = array();
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'plugin-install' );
			add_thickbox();
			?>
			<!DOCTYPE html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Test</title>
			<?php wp_print_scripts();wp_print_styles();?>
			<style>
			<?php echo get_option("apipp_product_styles", '');?>  
				.amazon-price-button > a img.amazon-price-button-img:hover {opacity: .75;}
				#plugin-information .appip-multi-divider{border-bottom: 1px solid #EAEAEA;margin: 4% 0 !important;}
				#plugin-information a img.amazon-image.amazon-image {max-width: 100%;border: 1px solid #ccc;box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.24); }
				#plugin-information h2.amazon-asin-title { border-bottom: 1px solid #ccc !important; padding-bottom: 2%; margin-bottom: 3% !important; }
				#plugin-information hr { display: none; }
			</style>
			</head>
			<body id="plugin-information" class="wp-admin wp-core-ui no-js iframe plugin-install-php locale-en-us">
			<div id="plugin-information-scrollable">
				<div id='plugin-information-title'>
					<div class="vignette"></div>
					<h2>Add an Amazon Product</h2>
				</div>
				<div id="plugin-information-tabs" class="without-banner">
					<a name="test" href="<?php echo admin_url('admin.php?page=apipp_plugin_admin&amp;tab=plugin-information&amp;plugin=amazon-product-in-a-post-plugin&amp;section=tab1');?>" class="current">Tab1</a>
					<a name="debug" href="<?php echo admin_url('admin.php?page=apipp_plugin_admin&amp;tab=plugin-information&amp;plugin=amazon-product-in-a-post-plugin&amp;section=tab2');?>">Tab2</a>
				</div>
				<div id="plugin-information-content" class="with-banner">
					<div id="section-holder" class="wrap">
						<div id="section-tab1" class="section" style="display: block;"></div>
						<div id="section-tab2" class="section" style="display: none;"></div>
					</div>
				</div>
			</div>
			</body>
			</html>
			<?php		
		}else{
			echo 'no permission';	
		}
		exit;
	}
	function test_API(){
		$error 			= '';
		$region 		= APIAP_LOCALE; 
		$publickey 		= APIAP_PUB_KEY;
		$privatekey 	= APIAP_SECRET_KEY;
		if( APIAP_ASSOC_ID == '' || $region == '' || $publickey == '' || $privatekey == '' )
			$error .= '<span style="color:red;">Error: Some Required Data is missing.</span><br/>';
		if( strlen($publickey) != 20 )
			$error .= '<span style="color:red;">Error: <strong>Amazon Access Key ID</strong> is not the correct length (should be 20 characters, not '.strlen($publickey).').</span><br/>';
		if( strlen($privatekey) != 40 )
			$error .= '<span style="color:red;">Error: <strong>Amazon Secret Access Key</strong> is not the correct length (should be 40 characters, not '.strlen($privatekey).').</span><br/>';
		if( $publickey == '' && $privatekey == '' )
			$error = '<span style="color:red;">Error: Please SAVE your settings BEFORE testing.</span><br/>';
		if( $error != '' )
			return $error;
		$keyword = array('puppy poster','kitten poster','disney movies','Game of Thrones','kids','funsparks','TV shows on DVD','mickey mouse','donald duck');
		shuffle($keyword);
		$result = $this->get_Result( array() , true, $keyword[0] );
		return $result;
	}
	function appip_do_settings_test_debug(){
		$test = $this->test_API();
		global $wp_scripts,$wp_styles;
		if (is_a($wp_scripts, 'WP_Scripts'))
		  $wp_scripts->queue = array();
		if (is_a($wp_styles, 'WP_Styles'))
		  $wp_styles->queue = array();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'common' );
		?>
<?php wp_print_scripts();wp_print_styles();?>
<style>
<?php echo get_option("apipp_product_styles", '');?>  
	.amazon-product-table{width:auto !important;}
	.amazon-price-button > a img.amazon-price-button-img:hover {opacity: .75;}
	#plugin-information-debug .appip-multi-divider{border-bottom: 1px solid #EAEAEA;margin: 4% 0 !important;}
	#plugin-information-debug a img.amazon-image.amazon-image {max-width: 100%;border: 1px solid #ccc;box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.24); }
	#plugin-information-debug h2.amazon-asin-title { border-bottom: 1px solid #ccc !important; padding-bottom: 2%; margin-bottom: 3% !important; }
	#plugin-information-debug hr { display: none; }
</style>
<div id="plugin-information-debug" class="wp-admin wp-core-ui plugin-install-php locale-en-us">
<div id="plugin-information-scrollable">
	<div id="plugin-information-content" class="with-banner">
		<div id="section-holder" class="wrap">
			<div id="section-test" class="section" style="display: block;">
				<h3>Test Results:</h3>
				<p>If you can see products listed below, then the test was successful.</p>
				<?php echo $test;?>
			</div>
			<div id="section-debug" class="section" style="display: block;">
				<h3>Amazon Product Debug Info</h3>
				<div style="background:#EAEAEA;margin-bottom: 10px;padding: 4px 10px;">
					<p>This plugin uses <code>wp_remote_request</code> to make Amazon API calls.</p>
                    <?php $none = true; ?>
					<?php if(wp_http_supports( array(), 'https://www.example.com/' )){ $none = false; ?>
						<p>Your host allows SSL requests.</p>
					<?php }?>
					<?php if(wp_http_supports( array(), 'http://www.example.com/' )){ $none = false;?>
						<p>Your host allows HTTP requests.</p>
					<?php } ?>
					<?php if($none){ ?>
						<p>You cannot use this plugin until either CURL or fopen are installed and working. Contact your host for help.</p>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php		
	}
	function appip_do_settings_test_ajax(){
		check_ajax_referer( 'appip_ajax_do_settings_test', 'security', true );
		if( current_user_can( 'manage_options' ) ){
			$test = $this->test_API();
			global $wp_scripts;
			global $wp_styles;
			if (is_a($wp_scripts, 'WP_Scripts')) {
			  $wp_scripts->queue = array();
			}	
			if (is_a($wp_styles, 'WP_Styles')) {
			  $wp_styles->queue = array();
			}			
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'plugin-install' );
			add_thickbox();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Test</title>
<?php wp_print_scripts();wp_print_styles();?>
<style>
<?php echo get_option("apipp_product_styles", '');?>  
	.amazon-price-button > a img.amazon-price-button-img:hover {opacity: .75;}
	#plugin-information .appip-multi-divider{border-bottom: 1px solid #EAEAEA;margin: 4% 0 !important;}
	#plugin-information a img.amazon-image.amazon-image {max-width: 100%;border: 1px solid #ccc;box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.24); }
	#plugin-information h2.amazon-asin-title { border-bottom: 1px solid #ccc !important; padding-bottom: 2%; margin-bottom: 3% !important; }
	#plugin-information hr { display: none; }
</style>
</head>
<body id="plugin-information" class="wp-admin wp-core-ui no-js iframe plugin-install-php locale-en-us">
<div id="plugin-information-scrollable">
	<div id='plugin-information-title'>
		<div class="vignette"></div>
		<h2><?php echo __('Amazon Product API Settings Test','amazon-product-in-a-post-plugin');?></h2>
	</div>
	<div id="plugin-information-tabs" class="without-banner">
		<a name="test" href="<?php echo admin_url('admin.php?page=apipp_plugin_admin&amp;tab=plugin-information&amp;plugin=amazon-product-in-a-post-plugin&amp;section=test');?>" class="current">Test Results</a>
		<a name="debug" href="<?php echo admin_url('admin.php?page=apipp_plugin_admin&amp;tab=plugin-information&amp;plugin=amazon-product-in-a-post-plugin&amp;section=debug');?>">Debug</a>
	</div>
	<div id="plugin-information-content" class="with-banner">
		<div id="section-holder" class="wrap">
			<div id="section-test" class="section" style="display: block;">
				<h3 style="margin-top:8px;"><?php echo __('Test Results','amazon-product-in-a-post-plugin');?></h3>
				<!--p><?php echo __('If you see "Connection Test Successful" & products shown below, then the test was successful.','amazon-product-in-a-post-plugin');?></p-->
				<?php echo $test;?>
			</div>
			<div id="section-debug" class="section" style="display: none;">
				<div style="background:#EAEAEA;margin-bottom: 10px;padding: 4px 10px;">
					<p><?php echo sprintf(_x('This plugin uses %s to make Amazon API calls.','Debug testing to check if API is callable.','amazon-product-in-a-post-plugin'), '<code>wp_remote_request</code>');?></p>
                    <?php 
					$none = true; 
					$none2 = false;
					?>
					<?php if(is_callable( 'wp_remote_request' )){ $none = false; ?>
					<p><span class="dashicons dashicons-yes" style="color:rgba(51,153,0,1);"></span><code>wp_remote_request</code> <?php echo _x('is callable.','Response if wp_remote_request function is callable.','amazon-product-in-a-post-plugin');?></p>
					<?php }else{ ?>
					<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><code>wp_remote_request</code> <?php echo _x('is NOT callable.','Response if wp_remote_request function is not callable.','amazon-product-in-a-post-plugin');?></p>
					<?php } ?>
					
					<?php if(wp_http_supports( array(), 'http://www.example.com/' )){ $none = false; ?>
						<p><span class="dashicons dashicons-yes" style="color:rgba(51,153,0,1);"></span><?php echo __('Your host allows HTTP requests.','amazon-product-in-a-post-plugin');?></p>
					<?php }else{ ?>
						<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><?php echo __('Your host does NOT allow HTTP requests.','amazon-product-in-a-post-plugin');?></p>
					<?php } ?>
					
					<?php if(wp_http_supports( array(), 'https://www.example.com/' )){ $none = false;  ?>
						<p><span class="dashicons dashicons-yes" style="color:rgba(51,153,0,1);"></span><?php echo __('Your host allows SSL requests.','amazon-product-in-a-post-plugin');?></p>
					<?php }else{ ?>
						<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><?php echo __('Your host does not allow SSL requests.','amazon-product-in-a-post-plugin');?></p>
					<?php } ?>
					
					<?php /*if(is_callable( 'xml_parser_create' )){ $none2 = false; ?>
					<p><span class="dashicons dashicons-yes" style="color:rgba(51,153,0,1);"></span><code>xml_parser_create</code> <?php echo _x('is callable.','Response if xml_parser_create function is callable.','amazon-product-in-a-post-plugin');?></p>
					<?php }else{ ?>
					<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><code>xml_parser_create</code> <?php echo _x('is callable.','Response if xml_parser_create function is not callable.','amazon-product-in-a-post-plugin');?></p>
					<?php } ?>
					
					<?php if(extension_loaded( 'SimpleXML' )){ $none2 = false; ?>
					<p><span class="dashicons dashicons-yes" style="color:rgba(51,153,0,1);"></span><code>SimpleXML</code> <?php echo _x('Installed.','Response if SimpleXML is installed.','amazon-product-in-a-post-plugin');?></p>
					<?php }else{ ?>
					<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><code>SimpleXML</code> <?php echo _x('is NOT installed.','Response if SimpleXML is not installed.','amazon-product-in-a-post-plugin');?></p>
					<?php } */ ?>
					
					<?php if($none){ ?>
						<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><?php echo __('You cannot use this plugin until either CURL or fopen are installed and working. Contact your host for help.','amazon-product-in-a-post-plugin');?></p>
					<?php } ?>
					
					<?php if($none2){ ?>
					<p><span class="dashicons dashicons-no" style="color:rgba(153,51,0,1);"></span><?php echo sprintf(__('You need to have %s and %s installed for the plugin to function correctly. Contact your host for help.','amazon-product-in-a-post-plugin'),'<code>xml_parser_create</code>','<code>SimpleXML</code>');?></p>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html><?php		
		}else{
			echo 'no permission';	
		}
		exit;
	}
	function get_Result( $canonicalized_query = array(), $test = false, $keyword = ''){
		//if($this->type == 'debug')
			//echo '<!--span style="font-weight:bold;font-family:courier;display:inline-block;width:225px;">Sample Request:</span-->'.$request;
		/* NEW */
		$Regions = __getAmz_regions();
		$region = $Regions[APIAP_LOCALE ][ 'RegionCode' ];
		$host = $Regions[APIAP_LOCALE ][ 'Host' ];
		$accessKey = APIAP_PUB_KEY;
		$secretKey = APIAP_SECRET_KEY;
		$payloadArr = array();
		$payloadArr[ 'Keywords' ] = $keyword;
		$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN','SearchRefinements' );
		$payloadArr[ "SortBy" ] = 'Relevance';	
		$payloadArr[ "Availability" ] = 'Available';	
		$payloadArr[ "ItemCount" ] = 3;
		$pages 	= array('1','2','3');
		shuffle($pages);
		$payloadArr[ "ItemPage" ] = (int) $pages[0];	
		$payloadArr[ "Condition" ] = 'New';	
		$payloadArr[ "SearchIndex" ] = 'All';
		$payloadArr[ 'PartnerTag' ] = APIAP_ASSOC_ID;
		$payloadArr[ 'PartnerType' ] = 'Associates';
		$payloadArr[ 'Marketplace' ] = 'www.amazon.'.APIAP_LOCALE ;
		$payload = json_encode( $payloadArr );
		$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
		/* END NEW */
		$skipCache = false;
		$asinR = null;
		$pxmlNew = amazon_plugin_aws_signed_request( APIAP_LOCALE , array( "Operation" => "SearchItems", "payload" => $payloadArr, "ItemId" => $asinR, "AssociateTag" => APIAP_ASSOC_ID, "RequestBy" => 'amazon-test' ), APIAP_PUB_KEY, APIAP_SECRET_KEY , ($skipCache ? true : false) );
		$totalResult2 = array();
		$totalResult3 = array();
		$errorsArr = array();
		if( $pxmlNew === false ){
			return '<span style="color:red;">Error:<br/>Something went wrong with the request (No Body).</pre>';
		}elseif(empty($pxmlNew)){
			return '<span style="color:red;">Error:<br/>Something went wrong with the request (other).</pre>';
		}
		if ( is_array( $pxmlNew ) && !empty( $pxmlNew ) ) {
			$pxmle = array();
			foreach ( $pxmlNew as $pxmlkey => $pxml ) {
				if ( !is_array( $pxml ) ) {
					$pxmle = $pxml;
				} else {
					if(isset($pxmlNew['Errors']))
						$pxml['Errors'] = $pxmlNew['Errors'];
					$r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, $asinR, $pxmlkey);
					$r3 = $pxml['Items'];
					if ( is_array( $r2 ) && !empty( $r2 ) ) {
						foreach ( $r2 as $ritem2 ) {
							$totalResult2[] = $ritem2;
						}
					}
					if(is_array( $r3 ) && !empty( $r3 )){
						foreach ( $r3 as $ke => $ritem3 ) {
							$totalResult3[] = $ritem3;
						}
					}
				}
			}
		}
		$resultarr = array();
		$returnval = '';
		if ( !empty( $pxmle ) ) {
			$pxml = $pxmle;
			return '<div style="display:none;" class="appip-errors">APPIP ERROR:pxml[' . $pxml . '</div>';
		} else {
			$resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array(); 
			$resultarr3 = isset( $totalResult3 ) && !empty( $totalResult3 ) ? $totalResult3 : array(); 
			$asins = array();
			if((bool)$test === true){
			}
			
			$apippnewwindowhtml = $template = '';
			$thedivider = '';
			$totaldisp  = 4;
			$i  		= 0;
			$returnval 	.= '
			<style type="text/css">
				table.amazon-product-table td { width: 25%; padding: 0; text-align: center; border: 1px solid #ccc; vertical-align: middle; }
				table.amazon-product-table td:hover { border-bottom: 1px solid #ccc; }
				table.amazon-product-table { margin-top: 20px; }
				table.amazon-product-table td:hover div { border: 2px solid #9C27B0; }
				table.amazon-product-table td div { line-height: 0; background-color: #fff; box-sizing: border-box; display: block; padding: 5px; height: 134px; }
				table.amazon-product-table td div img { max-height: 100%; width: auto; max-width: 100%; vertical-align: middle; }
				table.amazon-product-table { padding: 0; }
				amazon-product-table-search{margin-top:20px;}
			</style>
			';
			
			$resultarr 	= has_filter('appip_product_array_processed') ? apply_filters('appip_product_array_processed',$resultarr,$apippnewwindowhtml,$resultarr,$resultarr3,$template) : $resultarr;
			$resultarr 	= !is_array($resultarr) ? (array) $resultarr : $resultarr;
			$returnval 	.= '<span style="color:#390;font-size:16px;font-weight:bold;">&bull; ' . __( 'Connection Test Successful', 'amazon-product-in-a-post-plugin' ) . '!</span><br/>';
			if(is_array($resultarr) && !empty($resultarr)){
				$returnval 	.= '<div style="color:#390;font-size:16px;font-weight:bold;">&bull; ' . __( 'API Transport Successful', 'amazon-product-in-a-post-plugin' ) . '!</div>';
			if($keyword != '')
				$returnval .= '<div class="amazon-product-table-search"><strong> '.__('Keyword:','amazon-product-in-a-post-plugin').'</strong> <em>'. $keyword .'</em></div>'."\n";
				$returnval .= '	<table cellpadding="0" class="amazon-product-table">'."\n";
				$returnval .= '		<tr>'."\n";
				//shuffle($resultarr);
				$i = 0;
				foreach($resultarr as $key => $result):
					$result = (array) $result;
					//$result3 = $awsv5->GetAPPIPReturnVals_V5( $result, $totalResult3[$arr_position], $Errors );
					$result3 = $awsv5->GetAPPIPReturnVals_V5( $result, array(), array() );
					$result = array_merge($result,$result3);

					if($i >= $totaldisp)
						break;
					if(isset($result['NoData']) && $result['NoData'] == '1'):
						$returnval .=  $result['Error'];
						if($extratext != ''):
							$returnval .= $extratext;
						endif;
					else:
						$returnval .= '			<td valign="top">'."\n";
						$returnval .= '				<div class="amazon-image-wrapper-test">'.awsImageGrabber($result['MediumImage'],'amazon-image') . '</div>'."\n";
						$returnval .= '			</td>'."\n";
					endif;
					$i++;
				endforeach;				
				$returnval .= '		</tr>'."\n";
				$returnval .= '	</table>'."\n";
				if($i > 0)
					$returnval 	.= '<div style="color:#390;font-size:16px;font-weight:bold;">&bull; ' . __( 'Product Fetch Successful', 'amazon-product-in-a-post-plugin' ) . '!</div>';
				$returnval 	.= '<div style="font-size:14px;font-weight:normal;">' . __( 'If you see at least one product, then your test was successfully completed', 'amazon-product-in-a-post-plugin' ) . '.</div>';
			}else{
				$returnval 	.= '<span style="color:#C00;font-size:20px;font-weight:bold;">' . __( 'Product Test Failed', 'amazon-product-in-a-post-plugin' ) . '!</span><br/>';
			}
			return $returnval;
		}
		return 'Nothing';
	}
}

new Amazon_Product_New_Request();