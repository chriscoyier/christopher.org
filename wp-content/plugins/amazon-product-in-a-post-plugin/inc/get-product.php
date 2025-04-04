<?php
//Single Product API Call - Returns One Product Data

if(!function_exists('getSingleAmazonProduct')){
	/**
	 * Main product call for the plugin.
	 * Processes the returned or Cached JSON data into the HTML output.
	 *
	 * @method getSingleAmazonProduct
	 * @param  string                 $asin         ASIN number(s) to process. Can be single or comma separated ASINs.
	 * @param  string                 $extratext    Any extra text to output into the HTML.
	 * @param  integer                $extrabutton  Show extra button. 1 for true or 0 for false.
	 * @param  array                  $manual_array An array of manual settings.
	 * @return [string]                             HTML of product for output.
	 */
	function getSingleAmazonProduct( $asin='', $extratext='', $extrabutton = 0, $manual_array = array(), $desc = 0 ){
		$returnval 		= '';
		/* check if we even need the product; */
		$single_only	= (isset($manual_array['single_only']) && (int)$manual_array['single_only'] == 1) || (bool) get_option('apipp_show_single_only', false ) === true ? 1 : 0 ;
		if( appip_check_blockEditor_is_active() ){
			if( defined('REST_REQUEST') && (bool) REST_REQUEST ) {
				// we are in the admin and in the block editor most likely so show things normally
				// unless the ASIN is blank and this is a Gutenberg Block.
				if($asin == '' && isset($manual_array['is_block']) &&  (bool)$manual_array['is_block'])
					return '<div style="text-align:center;background: #f5f5f5;padding: 10px 5px;border: 1px solid #f48db0;"><strong>'.__('Amazon Product Block','amazon-product-in-a-post-plugin').'</strong><br>'.__('Please add at least one ASIN.','amazon-product-in-a-post-plugin').'</div>';
			}else{
				// in front end and not on a singlular page but show on sigle page only if checked
				// so return nothing (no need to display)
				if( (bool) $single_only === true && !is_singular() )
					return $returnval;
			}
		}elseif( !is_admin() && (bool) $single_only && !is_singular()) {
			//is not a single page, is on the front end and no Gutenberg installed or prior to WP 5.0
			return $returnval;
		}
		/* end check */

		global $amazonhiddenmsg;
		global $amazonerrormsg;
		global $apippopennewwindow;
		global $apippnewwindowhtml;
		global $addestrabuybutton,$buyamzonbutton;
		global $encodemode;
		global $post;
		global $validEncModes;
		global $appip_templates;
		global $appipTimestampMsgPrinted;

		$extratext 			= apply_filters('getSingleAmazon_Product_extratext',$extratext);
		$extrabutton		= apply_filters('getSingleAmazon_Product_extrabutton',$extrabutton);
		$manual_array		= apply_filters('getSingleAmazon_Product_manual_array',$manual_array);
		$manual_public_key 	= isset($manual_array['public_key'])	&& $manual_array['public_key'] !='' 	? $manual_array['public_key'] 	: APIAP_PUB_KEY ;
		$manual_private_key	= isset($manual_array['private_key'])	&& $manual_array['private_key'] !='' 	? $manual_array['private_key'] 	: APIAP_SECRET_KEY ;
		$manual_locale 		= isset($manual_array['locale']) 		&& $manual_array['locale']!='' 			? $manual_array['locale'] 		: APIAP_LOCALE ;
		$manual_partner_id	= isset($manual_array['partner_id']) 	&& $manual_array['partner_id'] !='' 	? $manual_array['partner_id'] 	: APIAP_ASSOC_ID ;
		$manual_new_window	= isset($manual_array['newwindow'])		&& (bool) $manual_array['newwindow'] !== 0 ? true : (bool) $apippopennewwindow;
		$manual_align		= isset($manual_array['align'])			&& in_array($manual_array['align'], array('alignleft','alignright','aligncenter')) ? $manual_array['align'] : '';
		$new_button_arr 	= amazon_product_get_new_button_array($manual_locale);
		$apippopennewwindow = $manual_new_window;
		$apippnewwindowhtml	= $manual_new_window ? ' target="amazonwin" ' : '';
		if($manual_partner_id == ''){$manual_partner_id = 'wolvid-20';} //have to give it some user id or it will fail.
		$errors 				= '';
		$appip_responsegroup 	= apply_filters('getSingleAmazon_Product_response_group',"Large,Reviews,Offers,Variations");
		$appip_operation 		= apply_filters('getSingleAmazon_Product_operation',"ItemLookup");
		$appip_idtype	 		= apply_filters('getSingleAmazon_Product_type',"ASIN");
		$appip_text_lgimage 	= apply_filters('appip_text_lgimage', __("See larger image",'amazon-product-in-a-post-plugin'));
		$appip_text_listprice 	= apply_filters('appip_text_listprice',__("List Price:",'amazon-product-in-a-post-plugin'));
		$appip_text_newfrom 	= apply_filters('appip_text_newfrom',__("New From:",'amazon-product-in-a-post-plugin'));
		$appip_text_usedfrom 	= apply_filters('appip_text_usedfrom', __("Used from:",'amazon-product-in-a-post-plugin'));
		$appip_text_instock	 	= apply_filters('appip_text_instock', __("In Stock",'amazon-product-in-a-post-plugin'));
		$appip_text_outofstock 	= apply_filters('appip_text_outofstock',__("Out of Stock",'amazon-product-in-a-post-plugin'));
		$appip_text_author 		= apply_filters('appip_text_author', __("By (author):",'amazon-product-in-a-post-plugin'));
		$appip_text_starring 	= apply_filters('appip_text_starring', __("Starring:",'amazon-product-in-a-post-plugin'));
		$appip_text_director 	= apply_filters('appip_text_director', __("Director:",'amazon-product-in-a-post-plugin'));
		$appip_text_reldate 	= apply_filters('appip_text_reldate', __("Release date:",'amazon-product-in-a-post-plugin'));
		$appip_text_preorder 	= apply_filters('appip_text_preorder', __("Preorder:",'amazon-product-in-a-post-plugin'));
		$appip_text_notavalarea = apply_filters('appip_text_notavalarea', __("This item is may not be available in your area. Please click the image or title of product to check pricing.",'amazon-product-in-a-post-plugin'));
		$appip_text_releasedon 	= apply_filters('appip_text_releasedon', __("This title will be released on",'amazon-product-in-a-post-plugin'));
		$appip_text_manufacturer= apply_filters('appip_text_manufacturer',__("Manufacturer:",'amazon-product-in-a-post-plugin'));
		$appip_text_ESRBAgeRating= apply_filters('appip_text_ESRBAgeRating',__("ESRB Rating:",'amazon-product-in-a-post-plugin'));
		$appip_text_feature 	= apply_filters('appip_text_feature', __("Features:",'amazon-product-in-a-post-plugin'));
		$appip_text_platform	= apply_filters('appip_text_platform', __("Platform:",'amazon-product-in-a-post-plugin'));
		$appip_text_genre		= apply_filters('appip_text_genre', __("Genre:",'amazon-product-in-a-post-plugin'));
		$appip_text_rating		= apply_filters('appip_text_rating', __("Rating:",'amazon-product-in-a-post-plugin'));

		// Main Amazon API Call
		if ( $asin != '' && $manual_public_key != '' && $manual_private_key != ''){
			$ASIN 					= apply_filters('getSingleAmazon_Product_asin',(is_array($asin) ? implode(',',$asin) : $asin)); //valid ASIN or ASINs
			$asinR					= explode(",",$ASIN);
			$description			= isset($manual_array['desc'])? (int) $manual_array['desc'] : 0 ; //set to no by default - too many complaints!
			$show_list				= isset($manual_array['listprice'])? (int) $manual_array['listprice'] : 0 ;
			$show_new				= isset($manual_array['new_price']) ? (int) $manual_array['new_price'] : 1 ;
			$show_used				= isset($manual_array['used_price'])? (int) $manual_array['used_price'] : 0 ;
			$show_used_price		= $show_used;
			$show_saved_amt			= isset($manual_array['saved_amt'])? (int) $manual_array['saved_amt'] : 0 ;
			$show_features			= isset($manual_array['features'])? (int) $manual_array['features'] : 0 ;
			$show_gallery			= isset($manual_array['gallery'])? (int) $manual_array['gallery'] : 0 ;
			$replace_titleS			= isset($manual_array['replace_title']) && $manual_array['replace_title'] != '' ? esc_attr($manual_array['replace_title']) : '' ;
			$template				= isset($manual_array['template']) && $manual_array['template'] != '' ? esc_attr($manual_array['template']) : 'default' ;
			$show_timestamp			= isset($manual_array['timestamp'])? (int) $manual_array['timestamp'] : 0 ;
			$title_wrap				= isset($manual_array['title_wrap'])? (int) $manual_array['title_wrap'] : 0 ;
			$hide_title				= isset($manual_array['hide_title'])? (int) $manual_array['hide_title'] : 0 ;
			$className				= isset($manual_array['className'])? esc_attr($manual_array['className']) : '' ;
			$button					= isset($manual_array['button'])? esc_attr($manual_array['button']) : '' ;
			$hide_lg_img_text		= isset($manual_array['hide_lg_img_text'])? (int) $manual_array['hide_lg_img_text'] : 0 ;
			$image_count			= isset($manual_array['image_count']) && ((int) $manual_array['image_count'] >= -1 && (int) $manual_array['image_count'] <= 10) ? (int) $manual_array['image_count'] : -1;
			$hide_release_date		= isset($manual_array['hide_release_date'])? (int) $manual_array['hide_release_date'] : 0 ;
			$is_block				= isset($manual_array['is_block'])? (int) $manual_array['is_block'] : 0 ;
			$hide_image				= isset($manual_array['hide_image'])? (int) $manual_array['hide_image'] : 0 ;
			$title_charlen			= isset($manual_array['title_charlen'])? (int) $manual_array['title_charlen'] : 0 ;
			$useCartURL				= isset($manual_array['use_carturl']) ?  $manual_array['use_carturl'] : '0' ;
			$useButtonCartURL		= isset($manual_array['use_button_carturl']) ? $manual_array['use_button_carturl'] : '0' ;
			$replace_title = array();
			if(strpos($replace_titleS,"::")!== false){
				$replace_title = explode("::",$replace_titleS);
			}else{
				$replace_title[] = $replace_titleS;
			}

			$array_for_templates	= array(  //these are shortcode variables to pass to template functions
				'apippnewwindowhtml'		=> $apippnewwindowhtml,
				'amazonhiddenmsg'			=> $amazonhiddenmsg,
				'amazonerrormsg'			=> $amazonerrormsg,
				'apippopennewwindow'		=> $apippopennewwindow,
				'appip_text_lgimage'		=> $appip_text_lgimage,
				'appip_text_listprice'		=> $appip_text_listprice,
				'appip_text_newfrom'		=> $appip_text_newfrom,
				'appip_text_usedfrom'		=> $appip_text_usedfrom,
				'appip_text_instock'		=> $appip_text_instock,
				'appip_text_outofstock'		=> $appip_text_outofstock,
				'appip_text_author'			=> $appip_text_author,
				'appip_text_starring'		=> $appip_text_starring,
				'appip_text_director'		=> $appip_text_director,
				'appip_text_reldate'		=> $appip_text_reldate,
				'appip_text_preorder'		=> $appip_text_preorder,
				'appip_text_releasedon'		=> $appip_text_releasedon,
				'appip_text_notavalarea'	=> $appip_text_notavalarea,
				'appip_text_manufacturer'	=> $appip_text_manufacturer,
				'appip_text_ESRBAgeRating'	=> $appip_text_ESRBAgeRating,
				'appip_text_feature'		=> $appip_text_feature,
				'appip_text_platform'		=> $appip_text_platform,
				'appip_text_genre'			=> $appip_text_genre,
				'appip_text_rating'			=> $appip_text_rating,
				'buyamzonbutton'			=> $buyamzonbutton,
				'addestrabuybutton'			=> $addestrabuybutton,
				'description'				=> $description,
				'encodemode'				=> $encodemode,
				'replace_title'				=> $replace_title,
				'show_list'					=> $show_list,
				'show_features'				=> $show_features,
				'show_used_price'			=> $show_used_price,
				'show_new_price'			=> $show_new,
				'show_saved_amt'			=> $show_saved_amt,
				'show_timestamp'			=> $show_timestamp,
				'show_gallery'				=> $show_gallery,
				'hide_title'				=> $hide_title,
				'template'					=> $template,
				'title_wrap'				=> $title_wrap,
				'validEncModes'				=> $validEncModes,
				'align'						=> $manual_align,
				'button'					=> $button,
				'hide_lg_img_text'			=> $hide_lg_img_text,
				'image_count'				=> $image_count,
				'hide_release_date'			=> $hide_release_date,
				'hide_image'				=> $hide_image,
				'use_cart_URL'				=> $useCartURL,
				'use_button_carturl'		=> $useButtonCartURL,
				'public_key' 				=> $manual_public_key,
				'private_key'				=> $manual_private_key,
				'locale' 					=> $manual_locale,
				'partner_id'				=> $manual_partner_id,
				'className'					=> $className,
				'single_only'				=> $single_only,
				'is_block'					=> $is_block,
				'title_charlen'				=> $title_charlen,
				'use_carturl'				=> $useCartURL, //alias for use_cart_URL
			);
			$set_array				= array("Operation" => $appip_operation,"ItemId" => $ASIN,"ResponseGroup" => $appip_responsegroup,"IdType" => $appip_idtype,"AssociateTag" => $manual_partner_id );
			$api_request_array		= array("RequestBy" => 'main-call-getSingleAmazonProduct-91','locale'=>$manual_locale,'public_key'=>$manual_public_key,'private_key'=>$manual_private_key, "partner_id" => $manual_partner_id, 'api_request_array'=>$set_array);
			$request_array			= apply_filters('appip_pre_request_array',$api_request_array);
			/* NEW */
			$Regions = __getAmz_regions();
			$region = $Regions[ $manual_locale ][ 'RegionCode' ];
			$host = $Regions[ $manual_locale ][ 'Host' ];
			$accessKey = $manual_public_key;
			$secretKey = $manual_private_key;
			$payloadArr = array();
			$payloadArr[ 'ItemIds' ] = $asinR;
			$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
			$payloadArr[ 'PartnerTag' ] = $manual_partner_id;
			$payloadArr[ 'PartnerType' ] = 'Associates';
			$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$manual_locale;
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
			/* END NEW */
			$skipCache = false;
			$pxmlNew = amazon_plugin_aws_signed_request( $manual_locale, array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $asinR, "AssociateTag" => $manual_partner_id, "RequestBy" => 'amazon-products' ), $manual_public_key, $manual_private_key, ($skipCache ? true : false) );

			$totalResult2 = array();
			$totalResult3 = array();
			$errorsArr = array();
			$er1Arr = array();
			$er2Arr = array();
			$pxmle = array();
			if ( is_array( $pxmlNew ) && !empty( $pxmlNew ) ) {
				foreach ( $pxmlNew as $pxmlkey => $pxml ) {
					if ( !is_array( $pxml ) ) {
						//nothing
						$errorsArr = $pxml;
					}elseif(is_array( $pxml ) && isset($pxml['Errors']) && !isset($pxml['Items'])){
						// only an error and no items
						$errorsArr = $pxml['Errors'];
					} else {
						if(isset($pxml['Errors']) && isset($pxml['Items'])){
						//itmes and erroros - grab the errors
							$er2Arr[] = $pxml['Errors'];
							unset($pxml['Errors']);
							$r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, $asinR, $pxmlkey);
							if ( is_array( $r2 ) && !empty( $r2 ) ) {
								foreach ( $r2 as $ritem2 ) {
									$totalResult2[] = $ritem2;
								}
							}
							$r3 = $pxml['Items'];
							if(is_array( $r3 ) && !empty( $r3 )){
								foreach ( $r3 as $ke => $ritem3 ) {
									$totalResult3[] = $ritem3;
								}
							}
						}elseif(isset($pxml['Items'])){
							//only items
							$r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, $asinR, $pxmlkey);
							if ( is_array( $r2 ) && !empty( $r2 ) ) {
								foreach ( $r2 as $ritem2 ) {
									$totalResult2[] = $ritem2;
								}
							}
							$r3 = $pxml['Items'];
							if(is_array( $r3 ) && !empty( $r3 )){
								foreach ( $r3 as $ke => $ritem3 ) {
									$totalResult3[] = $ritem3;
								}
							}
						}
					}
				}
			}
			$itemErrors = false;
			$errmsgBlock = array();
			if(!empty($errorsArr) && !empty($totalResult2)){
				//errors and items
				$itemErrors = true;
				/*
				loop the errors- and looks for item errors only,
				then put into the return array for that ASIN (even though it is invalid).
				This will output the error into the HTML as a comment so user can see what is going on
				when no product is displayed.
				*/
				foreach($errorsArr as $k => $v){
					$code = isset($v['Code']) ? $v['Code']: '';
					$msg = isset($v['Message']) ? $v['Message'] : '';
					$errasin = '';
					if($code == 'InvalidParameterValue' && $msg != ''){
						$errasin = str_replace(array('The value [','] provided in the request for ItemIds is invalid.'),array('',''),$msg);
						$errmsg[] = $code . "|" . $msg;
						$errmsgBlock[$errasin] = $msg;
					}
				}
			}elseif(!empty($errorsArr)){
				$pxmle = $errorsArr;
			}
			$resultarr = array();
			if ( !empty( $pxmle ) ) {
				$pxmle;
				$errmsg = array();
				$errmsgBlock = array();
				foreach($pxmle as $k => $v){
					$code = isset($v['Code']) ? $v['Code']: 'code';
					$msg = isset($v['Message']) ? $v['Message'] : 'message';
					$errmsg[] = $code . "|" . $msg;
					$errmsgBlock[] = '<div class="block-error-code" style="font-weight:bold;">'.$code . ':</div><div class="block-error-message">' . $msg.'</div>';
				}
				if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
					return '<div class="appip-block-wrapper appip-block-wrapper-error" style="border:1px solid #f48db0;padding:15px;text-align:center;background:#f5f5f5;"><div style="text-align:center;color:#f00;font-weight:bold;padding:0 0 10px;">Amazon Products Block Errors</div>' . implode( "<br>", $errmsgBlock ) .'<div style="color:#aaa;font-size:.75em;font-style:italic;">This block will not be displayed on the front end of the website until the error is fixed.</div></div>';
				}else{
					return '<pre style="display:none;" class="appip-errors">APPIP ERROR: amazonproducts['."\n" . implode( "\n", $errmsg ) ."\n". ']</pre>';
				}
			}else{
				$resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array();
				$resultarr3 = isset($totalResult3) && !empty($totalResult3) ? $totalResult3 : array();

				if( !is_array( $resultarr ) )
					$resultarr = (array) $resultarr;
				if( !empty( $resultarr ) ):
					$array_for_templates['timestamp_printed'] = $appipTimestampMsgPrinted;
					if($show_timestamp!=0 && $appipTimestampMsgPrinted != 1){
						$appipTimestampMsgPrinted = 1;
						$array_for_templates['timestamp_printed'] = $appipTimestampMsgPrinted;
					}
					$thedivider = '';
					if(count($resultarr) >=1)
						$thedivider = '<div class="appip-multi-divider"></div>';
					$array_for_templates['product_count'] = count($resultarr);
					/* New Button functionality */
					if($button != ''){
						$buttonstemp = explode(',', $button );
						unset($button);
						if( count($buttonstemp) === 1 && count($resultarr) > 1){
							foreach($asinR as $kba => $kbv ){
								$button[] = $buttonstemp[0];
							}
						}else{
							foreach($buttonstemp as $buttona){
								if(!empty($buttona)){
									$button[] = $buttona;
								}
							}
						}
					}else{
						$button = array();
					}
					$array_for_templates['button'] = $button;
					/* END New Button functionality */
					if($itemErrors && !empty($errmsgBlock)){
						foreach($errmsgBlock as $ekey => $eval){
							//$thenewret[] = '<!--'.$eval.'-->';
						}
					}

					$arr_position = 0;
					foreach($resultarr as $key => $result):
						$result = (array) $result;
						$Errors = array();
						$result3 = $awsv5->GetAPPIPReturnVals_V5(( array ) $result, $totalResult3[$arr_position], $Errors );
						$result = array_merge((array)$result,$result3);
						$currasin = $result[ 'ASIN' ];
						$result = has_filter('appip_product_array_processed') ? apply_filters('appip_product_array_processed',$result,$apippnewwindowhtml,$resultarr,$resultarr3,$template) : $result;
						if ( isset( $result[ 'NoData' ] ) && $result[ 'NoData' ] == '1' ):
							$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:none;" class="appip-errors">APPIP ERROR:nodata_product[' . print_r($result[ 'Errors' ], true ) . '</'.$wrap.'>';
						elseif ( empty( $result[ 'ASIN' ] ) || $result[ 'ASIN' ] == 'Array' ):
							$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:none;" class="appip-errors">APPIP ERROR:nodata_product_ASIN[ (' . $key . ')]</'.$wrap.'>';
						else :
							$array_for_templates['addl_image_array'] = $result['AddlImagesArr'];
							$linkURL = $useCartURL == '1' ? str_replace(array('##REGION##','##AFFID##','##SUBSCRIBEID##'),array($manual_locale,$manual_partner_id,$manual_public_key), $result['CartURL'] ) : $result['URL'];
							$btnlinkURL = $useButtonCartURL == '1' ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $manual_locale,$manual_partner_id,$manual_public_key ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
							unset($temppart);
							$blockClass = '';
							if((bool) $is_block)
								$blockClass = ' amazon--is_block';
							$temppart[] = '<div class="appip-block-wrapper">';
							$temppart[] = '<div class="amazon-template--fluffy'.$blockClass.'">';
							$temppart[] = '	<div class="amazon-image-wrapper">';
                            if((bool) $array_for_templates['hide_image'] === false )
    							$temppart[] = '		<a href="[!URL!]" [!TARGET!]>[!IMAGE!]</a>';
							if((bool) $array_for_templates['hide_lg_img_text'] === false )
								$temppart[] = '	<a rel="appiplightbox-[!ASIN!]" href="#" data-appiplg="[!LARGEIMAGE!]" target="amazonwin"><span class="amazon-tiny">[!LARGEIMAGETXT!]</span></a>';
							if($result['AddlImages']!='' && (bool) $array_for_templates['show_gallery'])
								$temppart[] = '	<div class="amazon-additional-images-wrapper"><span class="amazon-additional-images-text">[!LBL-ADDL-IMAGES!]:</span>[!ADDL-IMAGES!]</div>';
							$temppart[] = '	</div>';
							$temppart[] = '	<div class="amazon-section-wrapper">';
							if((bool) $array_for_templates['hide_title'] === false )
								$temppart[] = '	<h2 class="amazon-asin-title"><a href="[!URL!]" [!TARGET!]><span class="asin-title">[!TITLE!]</span></a></h2>';
							if((bool) $array_for_templates['description'])
								$temppart[] = '		<div class="amazon-description">[!CONTENT!]</div>';
							if(($result["Department"]=='Video Games') && (bool) $array_for_templates['show_features']){
								$temppart[] = '	<div class="amazon-game-features">';
								$temppart[] = '		<span class="amazon-manufacturer"><span class="appip-label">[!LBL-MANUFACTURER!]:</span> [!MANUFACTURER!]</span><br />';
								$temppart[] = '		<span class="amazon-ESRB"><span class="appip-label">[!LBL-ESRBA!]:</span> [!ESRBA!]</span><br />';
								$temppart[] = '		<span class="amazon-platform"><span class="appip-label">[!LBL-PLATFORM!]:</span> [!PLATFORM!]</span><br />';
								$temppart[] = '		<span class="amazon-system"><span class="appip-label">[!LBL-GENRE!]:</span> [!GENRE!]</span><br />';
								$temppart[] = '		<span class="amazon-feature"><span class="appip-label">[!LBL-FEATURE!]:</span> [!FEATURE!]</span><br />';
								$temppart[] = '	</div>';
							}elseif($show_features != 0 && $result["Feature"] != ''){
								$temppart[] = '		<span class="amazon-feature"><span class="appip-label">[!LBL-FEATURE!]:</span> [!FEATURE!]</span><br />';
							}
							if($result["ReleaseDate"] != ''){
								$nowdatestt = strtotime(date("Y-m-d",time()));
								$nowminustt = strtotime("-7 days");
								$reldatestt = strtotime($result["ReleaseDate"]);
								if($reldatestt > $nowdatestt){
									$temppart[] = '<span class="amazon-preorder"><br />[!LBL-RELEASED-ON-DATE!] [!RELEASE-DATE!]</span>';
								}elseif($reldatestt >= $nowminustt){
									$temppart[] = '<span class="amazon-release-date">[!LBL-RELEASE-DATE!] [!RELEASE-DATE!]</span>';
								}
							}
							$temppart[] = '<div>[!AMZ-BUTTON!]</div>';
							$temppart[] = '</div>';
							$temppart[] = '</div>';
							$temppart[] = '<div><hr></div>';
							$temppart[] = '</div>';
							$appip_templates['fluffy'] = implode("\n",$temppart);
							$appip_templates = apply_filters('appip-template-filter',$appip_templates, $result, $array_for_templates);

                            // Product title
                            if(isset($replace_title[$arr_position]) && $replace_title[$arr_position]!=''){
                                $title = $replace_title[$arr_position];
                            }else{
                                $title = Amazon_Product_Shortcode::appip_do_charlen(maybe_convert_encoding($result["Title"]),$title_charlen);
                            }

							if( $template != 'default' && isset($appip_templates[$template])){
								$nofollow = ' rel="nofollow"';
								if( (bool) $apippopennewwindow )
									$nofollow = ' rel="nofollow noopener"';
								$nofollow = apply_filters( 'appip_template_add_nofollow', $nofollow,$result );

								if(isset($button[$arr_position])){
									$bname 		= $button[$arr_position];
									$brounded 	= strpos($bname,'rounded') !== false ? true : false;
									$bclass 	= isset($new_button_arr[$bname]['color']) ? 'amazon__btn'.$new_button_arr[$bname]['color'].' amazon__price--button--style'.( $brounded ? ' button-rounded' : '') : 'amazon__btn amazon__price--button--style';
									$bclass 	= isset($new_button_arr[$bname]['custom']) && $new_button_arr[$bname]['custom'] == 'true' ? 'amazon__btn'.$new_button_arr[$bname]['color'].' amazon__price--button--style'.( $brounded ? ' button-rounded' : '') : $bclass;
									$btext 		= isset($new_button_arr[$bname]['text']) ? esc_attr($new_button_arr[$bname]['text']) : _x('Buy Now', 'button text', 'amazon-product-in-a-post-plugin' );
									$BtnHTML  	= '<div class="amazon-price-button-html"><a '.$apippnewwindowhtml.$nofollow.' href="'.$linkURL.'" class="'.$bclass.'">'.$btext.'</a></div>'."\n";
								}else{
									$buttonURL  = apply_filters('appip_amazon_button_url',plugins_url('/images/'.$buyamzonbutton,dirname(__FILE__)),$buyamzonbutton,$manual_locale);
									$BtnHTML 	= '<div class="amazon-price-button"><a '. $apippnewwindowhtml .$nofollow.' href="' . $linkURL .'"><img class="amazon-price-button-img" src="'.$buttonURL.'" alt="'.apply_filters('appip_amazon_button_alt_text', __('buy now','amazon-product-in-a-post-plugin'),$result['ASIN']).'"/></a></div>'."\n";
								}
								$newdesc 	= '';
								if(is_array($result["ItemDesc"]) && $description == 1 && isset($result["ItemDesc"][0])){
									$desc 	= preg_replace('/^\s*\/\/<!\[CDATA\[([\s\S]*)\/\/\]\]>\s*\z/','$1', $result["ItemDesc"][0]);
									$newdesc = isset($desc['Content']) && $desc['Content'] != '' ? maybe_convert_encoding($desc['Content']) : '';
								}
								$findarr 	= array(
									'[!URL!]',
									'[!TARGET!]',
									'[!IMAGE!]',
									'[!TITLE!]',
									'[!LARGEIMAGE!]',
									'[!LARGEIMAGETXT!]',
									'[!ASIN!]',
									'[!CONTENT!]',
									'[!LBL-MANUFACTURER!]',
									'[!MANUFACTURER!]',
									'[!LBL-ESRBA!]',
									'[!ESRBA!]',
									'[!LBL-PLATFORM!]',
									'[!PLATFORM!]',
									'[!LBL-GENRE!]',
									'[!GENRE!]',
									'[!LBL-FEATURE!]',
									'[!FEATURE!]',
									'[!AMZ-BUTTON!]',
									'[!LBL-RELEASED-ON-DATE!]',
									'[!LBL-RELEASE-DATE!]',
									'[!RELEASE-DATE!]',
									'[!LBL-ADDL-IMAGES!]',
									'[!ADDL-IMAGES!]',
								);
								$replacearr = array(
									$linkURL,
									$apippnewwindowhtml,
									checkSSLImages_tag($result['LargeImage'],'amazon-image amazon-image-large',$result['ASIN'], $title),
									$title,
									checkSSLImages_url($result['LargeImage']),
									$appip_text_lgimage,
									$result['ASIN'],
									$newdesc,
									$appip_text_manufacturer,
									maybe_convert_encoding($result["Manufacturer"]),
									$appip_text_ESRBAgeRating,
									maybe_convert_encoding($result["ESRBAgeRating"]),
									$appip_text_platform,
									maybe_convert_encoding($result["Platform"]),
									$appip_text_genre,
									maybe_convert_encoding($result["Genre"]),
									$appip_text_feature,
									maybe_convert_encoding($result["Feature"]),
									$BtnHTML,
									$appip_text_releasedon,
									$appip_text_reldate,
									date("F j, Y", strtotime($result["ReleaseDate"])),
									__('Additional Images','amazon-product-in-a-post-plugin'),
									$result['AddlImages'],

								);

								$findarr 	= apply_filters('appip_template_find_array',$findarr,$template,$result);
								$replacearr = apply_filters('appip_template_replace_array',$replacearr,$template,$result,$title,$desc);
								$returnval	.=str_replace($findarr,$replacearr,$appip_templates[$template]);
							}else{
								$nofollow = ' rel="nofollow"';
								if( (bool) $apippopennewwindow )
									$nofollow = ' rel="nofollow noopener"';
								$nofollow = apply_filters( 'appip_template_add_nofollow', $nofollow,$result );
								$blockClass = ' amazon-template--product-default';
								if((bool) $is_block)
									$blockClass = ' amazon--is_block amazon-template--product-default';
								$addAlign = $manual_align != '' ? ' '.$manual_align : '';
								$returnval .= '<div class="appip-block-wrapper">';
								$returnval .= '	<br><table cellpadding="0" class="amazon-product-table'.$addAlign.$blockClass.'">'."\n";
								$returnval .= '		<tr>'."\n";
								$returnval .= '			<td valign="top">'."\n";
								$returnval .= '				<div class="amazon-image-wrapper">'."\n";

                                if((bool) $hide_image === false){
                                    $img = isset($result['MediumImage']) ? $result['MediumImage'] : '';
                                    $img = $img == '' && isset($result['LargeImage']) ? $result['LargeImage'] : $img;
                                    $returnval .= '<a href="' . $linkURL . '" '.$apippnewwindowhtml.$nofollow.'>' . checkSSLImages_tag($img ,'amazon-image amazon-image-medium',$result['ASIN'], $title). '</a><br />'."\n";

                                    if( !empty($result['LargeImage']) && (bool) $hide_lg_img_text === false )
                                        $returnval .= '<a rel="appiplightbox-'.$result['ASIN'].'" href="#" data-appiplg="'.checkSSLImages_url($result['LargeImage']) .'" target="amazonwin"><span class="amazon-tiny">'.$appip_text_lgimage.'</span></a>'."\n";
                                }

								if ( ( int )$array_for_templates[ 'image_count' ] >= 1 && ( int )$array_for_templates[ 'image_count' ] <= 10 && is_array( $result[ 'AddlImagesArr' ] ) && !empty( $result[ 'AddlImagesArr' ] ) ) {
									$result[ 'AddlImages' ] = implode( '', array_slice( $result[ 'AddlImagesArr' ], 0, ( int )$array_for_templates[ 'image_count' ] ) );
								} elseif ( ( int )$array_for_templates[ 'image_count' ] == 0 ) {
									$result[ 'AddlImages' ] = array();
									$show_gallery = 0;
								}

								if($result['AddlImages'] != '' && $show_gallery == 1)
									$returnval .= '<div class="amazon-additional-images-wrapper"><span class="amazon-additional-images-text">'.__( 'Additional Images', 'amazon-product-in-a-post-plugin' ).':</span>'.$result['AddlImages'].'</div>';

								$returnval .= '</div>'."\n";
								$returnval .= '<div class="amazon-buying">'."\n";

								if(strtolower($title) != 'null' && (bool) $hide_title !== true )
									$returnval .= '	<h2 class="amazon-asin-title"><a href="' . $linkURL . '" '. $apippnewwindowhtml .$nofollow.'><span class="asin-title">'.$title.'</span></a></h2>'."\n";
								if(!empty($result["ItemDesc"])  && $description == 1){
									if( isset($result["ItemDesc"][0]['Content']) && $result["ItemDesc"][0]['Content'] != '' ){
										$desc 		= str_replace('<![CDATA[','', $result["ItemDesc"][0]['Content'] );
										$desc 		= str_replace(']]>','', $desc );
										$desc 		= str_replace(']]&gt;','', $desc );
										$returnval .= '				<div class="amazon-description">'.maybe_convert_encoding($desc).'</div>'."\n";
									}elseif( isset($result["ItemDesc"]['Content']) && $result["ItemDesc"]['Content'] != ''){
										$desc 		= str_replace('<![CDATA[','', $result["ItemDesc"]['Content'] );
										$desc 		= str_replace(']]>','', $desc );
										$desc 		= str_replace(']]&gt;','', $desc );
										$returnval .= '				<div class="amazon-description">'.maybe_convert_encoding($desc).'</div>'."\n";
									}
								}
								if((bool) $hide_title !== true )
									$returnval .= '<div class="amazon-divider"></div>'."\n";
								if($result["Department"]=='Video Games' || $result["ProductGroup"]=='Video Games'){
									$returnval .= '<span class="amazon-manufacturer"><span class="appip-label">'.$appip_text_manufacturer .'&nbsp;</span> '.maybe_convert_encoding($result["Manufacturer"]).'</span><br />'."\n";
									$returnval .= '<span class="amazon-ESRB"><span class="appip-label">'.$appip_text_ESRBAgeRating .'&nbsp;</span> '.maybe_convert_encoding($result["ESRBAgeRating"]).'</span><br />'."\n";
									$returnval .= '<span class="amazon-platform"><span class="appip-label">'.$appip_text_platform .'&nbsp;</span> '.maybe_convert_encoding($result["Platform"]).'</span><br />'."\n";
									$returnval .= '<span class="amazon-system"><span class="appip-label">'.$appip_text_genre.'&nbsp;</span> '.maybe_convert_encoding($result["Genre"]).'</span><br />'."\n";
									if($show_features != 0){
										$returnval .= '<span class="amazon-feature"><span class="appip-label">'.$appip_text_feature .'&nbsp;</span> '.maybe_convert_encoding($result["Feature"]).'</span>'."\n";
									}
								}elseif($show_features != 0 && $result["Feature"] != ''){
									$returnval .= '<span class="amazon-feature"><span class="appip-label">'.$appip_text_feature .'&nbsp;</span> '.maybe_convert_encoding($result["Feature"]).'</span>'."\n";
								}
								if($show_features != 0){
									if(trim($result["Author"])!=''){
										$returnval .= '<span class="amazon-author">'.$appip_text_author .'&nbsp;</span> '.maybe_convert_encoding($result["Author"]).'</span><br />'."\n";
									}
									if(trim($result["Director"])!=''){
										$returnval .= '<span class="amazon-director-label">'.$appip_text_director.'&nbsp;</span><span class="amazon-director">'.maybe_convert_encoding($result["Director"]).'</span><br />'."\n";
									}
									if(trim($result["Actor"])!=''){
										$returnval .= '<span class="amazon-starring-label">'.$appip_text_starring.'&nbsp;</span><span class="amazon-starring">'.maybe_convert_encoding($result["Actor"]).'</span><br />'."\n";
									}
									if(trim($result["AudienceRating"])!=''){
										$returnval .= '<span class="amazon-rating-label">'.$appip_text_rating.'&nbsp;</span><span class="amazon-rating">'.$result["AudienceRating"].'</span><br />'."\n";
									}
								}
								if($extratext != '')
									$returnval .= '<div class="amazon-post-text">'.$extratext.'</div>'."\n";
								$returnval .= '<div align="left" class="amazon-product-pricing-wrap">'."\n";
								$returnval .= '<table class="amazon-product-price" cellpadding="0" style="width:100%;">'."\n";
								if((bool)$show_list){
									if($result["PriceHidden"]== '1' ){
										$returnval .= '						<tr>'."\n";
										$returnval .= '							<td class="amazon-list-price-label">'.$appip_text_listprice.'</td>'."\n";
										$returnval .= '							<td class="amazon-list-price-label">'.$amazonhiddenmsg.'</td>'."\n";
										$returnval .= '						</tr>'."\n";
									}elseif($result["ListPrice"] != '0' || $result["NewAmazonPricing"]["New"]["List"] != '0'){
										$returnval .= '						<tr>'."\n";
										$returnval .= '							<td class="amazon-list-price-label">'.$appip_text_listprice.'</td>'."\n";
										if(isset($result["NewAmazonPricing"]["New"]["List"]) && $result["NewAmazonPricing"]["New"]["List"] != '' )
											$returnval .= '							<td class="amazon-list-price">'.  maybe_convert_encoding($result["NewAmazonPricing"]["New"]["List"]) .'</td>'."\n";
										else
											$returnval .= '							<td class="amazon-list-price">'.  maybe_convert_encoding($result["ListPrice"]) .'</td>'."\n";
										$returnval .= '						</tr>'."\n";
									}
								}
								if(isset($result["LowestNewPrice"]) && (bool)$show_new ){
										$amz_pricing_text = __('Check Amazon For Pricing','amazon-product-in-a-post-plugin');
										$hide_stock_msg = isset( $result[ "HideStockMsg" ] ) && (int) $result[ "HideStockMsg" ]  == 1 ? true : false;
										if($result["LowestNewPrice"] == 'Too low to display'){
											$newPrice = $amz_pricing_text;
											$hide_stock_msg = true;
										}else{
											if(isset($result["NewAmazonPricing"]["New"]["SalePrice"]) && $result["NewAmazonPricing"]["New"]["SalePrice"] != '' )
												$newPrice = $result["NewAmazonPricing"]["New"]["SalePrice"];
											elseif(isset($result["NewAmazonPricing"]["New"]["Price"]) && $result["NewAmazonPricing"]["New"]["Price"] != ''  )
												$newPrice = $result["NewAmazonPricing"]["New"]["Price"];
											else
												$newPrice = $result["LowestNewPrice"];
										}
										$returnval .= '<tr>'."\n";
										if($newPrice == '0'){
											$newPrice = $amz_pricing_text;
											$hide_stock_msg = true;
										}
										if(!$hide_stock_msg){
											$returnval .= '<td class="amazon-new-label">'. $appip_text_newfrom .'</td>'."\n";
										}
										if ( ! $hide_stock_msg ) {
											$stockIn = ' <span class="instock">'.$appip_text_instock.'</span>';
											$stockOut = ' <span class="outofstock">'.$appip_text_outofstock.'</span>';
										} else {
											$stockIn = '';
											$stockOut = '';
										}
										if($result["TotalNew"]>0){
											$returnval .= '<td class="amazon-new">'. maybe_convert_encoding($newPrice).$stockIn.'</td>'."\n";
										}else{
											$returnval .= '<td class="amazon-new">'. maybe_convert_encoding($newPrice).$stockOut.'</td>'."\n";
										}
										$returnval .= '</tr>'."\n";
								}
								if($show_used == 1){
									if(isset($result["LowestUsedPrice"]) && $result["Binding"] != 'Kindle Edition'){
										if(!(isset($result["HideStockMsgUsed"]) && isset($result["HideStockMsgUsed"]) == '1')){
											$stockIn = ' <span class="instock">'.$appip_text_instock.'</span>';
											$stockOut = ' <span class="outofstock">'.$appip_text_outofstock.'</span>';
										}else{
											$stockIn = '';
											$stockOut = '';
										}
										$returnval .= '<tr>'."\n";
										$returnval .= '<td class="amazon-used-label">'.$appip_text_usedfrom.'</td>'."\n";
										if($result["TotalUsed"] > 0){
											if(isset($result["NewAmazonPricing"]["Used"]["Price"]) && $result["NewAmazonPricing"]["Used"]["Price"] != '' && $result["NewAmazonPricing"]["Used"]["Price"] != '0')
												$usedPrice = $result["NewAmazonPricing"]["Used"]["Price"];
											else
												$usedPrice = $result["LowestNewPrice"];

											$returnval .= '						<td class="amazon-used">'.maybe_convert_encoding($usedPrice) .$stockIn.'</td>'."\n";
										}else{
											if(isset($result["NewAmazonPricing"]["Used"]["Price"]) && $result["NewAmazonPricing"]["Used"]["Price"] != '' && $result["NewAmazonPricing"]["Used"]["Price"] != '0')
												$usedPrice = $result["NewAmazonPricing"]["Used"]["Price"];
											else
												$usedPrice = '';
											$returnval .= '<td class="amazon-used">'. maybe_convert_encoding($usedPrice) . $stockOut.'</td>'."\n";
										}
										$returnval .= '</tr>'."\n";
									}
								}
								if(isset($result["VariantHTML"]) && $result["VariantHTML"] != ''){
									$returnval .= '<tr>'."\n";
									$returnval .= '<td colspan="2" class="amazon-list-variants">'.$result["VariantHTML"].'</td>'."\n";
									$returnval .= '</tr>'."\n";
								}
								$returnval .= '<tr>'."\n";
								$returnval .= '<td valign="top" colspan="2">'."\n";
								$returnval .= '<div class="amazon-dates">'."\n";
								if($result["ReleaseDate"] != '' && ( bool )$array_for_templates[ 'hide_release_date' ] !== true ){
									$nowdatestt = strtotime(date("Y-m-d",time()));
									$nowminustt = strtotime("-7 days");
									$reldatestt = strtotime($result["ReleaseDate"]);
									if($reldatestt > $nowdatestt){
										$returnval .= '<span class="amazon-preorder"><br />'.$appip_text_releasedon.' '.date("F j, Y", strtotime($result["ReleaseDate"])).'.</span>'."\n";
									}elseif($reldatestt >= $nowminustt){
										$returnval .= '<span class="amazon-release-date">'.$appip_text_reldate.' '.date("F j, Y", strtotime($result["ReleaseDate"])).'.</span>'."\n";
									}
								}
								$htmlButton = ((bool) apply_filters('appip_amazon_button_html',false) === true ? true : false);
								if($htmlButton){
									$returnval .= '<div class="amazon-price-button-html"><a class="amazon__price--button--style amazon__price--button--single--'.$result['ASIN'].'" '. $apippnewwindowhtml .$nofollow.' href="' . $linkURL .'">'.apply_filters('appip_amazon_button_html_text','Buy from Amazon.com').'</a></div>'."\n";
								}else{
									if(isset($button[$arr_position])){
										$bname 		= $button[$arr_position];
										$brounded 	= strpos($bname,'rounded') !== false ? true : false;
										$bclass 	= isset($new_button_arr[$bname]['color']) ? 'amazon__btn'.$new_button_arr[$bname]['color'].' amazon__price--button--style'.( $brounded ? ' button-rounded' : '') : 'amazon__btn amazon__price--button--style';
										$btext 		= isset($new_button_arr[$bname]['text']) ? esc_attr($new_button_arr[$bname]['text']) : _x('Buy Now', 'button text', 'amazon-product-in-a-post-plugin' );
										$returnval .= '<div class="amazon-price-button-html"><a '.$apippnewwindowhtml.$nofollow.' href="'.$linkURL.'" class="'.$bclass.'">'.$btext.'</a></div>'."\n";
									}else{
										$buttonURL  = apply_filters('appip_amazon_button_url',plugins_url('/images/'.$buyamzonbutton,dirname(__FILE__)),$buyamzonbutton,$manual_locale);
										$returnval .= '<div class="amazon-price-button"><a '. $apippnewwindowhtml .$nofollow.' href="' . $linkURL .'"><img class="amazon-price-button-img" src="'.$buttonURL.'" alt="'.apply_filters('appip_amazon_button_alt_text', __('buy now','amazon-product-in-a-post-plugin'),$result['ASIN']).'"/></a></div>'."\n";
									}
								}
								$returnval .= '								</div>'."\n";
								$returnval .= '							</td>'."\n";
								$returnval .= '						</tr>'."\n";
								if(!isset($result["LowestUsedPrice"]) && !isset($result["LowestNewPrice"]) && !isset($result["ListPrice"])){
									$returnval .= '						<tr>'."\n";
									$returnval .= '							<td class="amazon-price-save-label" colspan="2">'.$appip_text_notavalarea.'</td>'."\n";
									$returnval .= '						</tr>'."\n";
								}
								$returnval .= '					</table>'."\n";
								$returnval .= '					</div>'."\n";
								$returnval .= '				</div>'."\n";
								$returnval .= '			</td>'."\n";
								$returnval .= '		</tr>'."\n";
								$returnval .= '	</table>'."\n";
								$returnval .= '</div>'."\n";
								if($result["CachedAPPIP"] !=''){
									$returnval .= '<'.'!-- APPIP Item Cached ['.$result["CachedAPPIP"].'] -->'."\n";
								}
								$returnval .= $thedivider;
							}//template
						endif;
						$arr_position++;
					endforeach;
				endif;
				if($template != 'default')
					$returnval = $returnval . '<div class="appip-div-clear"></div>';
				return apply_filters('appip_single_product_filter',$returnval,$resultarr,$manual_array);
			}
		}
	}
}
