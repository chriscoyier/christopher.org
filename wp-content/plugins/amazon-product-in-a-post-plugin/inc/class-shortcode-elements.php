<?php

class Amazon_Product_Shortcode_Elements extends Amazon_Product_Shortcode {
	static function _setup() {}
	/*
	static function appip_do_charlen( $text = '', $charlen = 0 ) {
		if ( $text == '' || ( int )$charlen == 0 )
			return $text;
		return Amazon_Product_Shortcode::amazon_appip_truncate( $text, $charlen );
	}
	*/
	static function do_shortcode( $atts, $content = '' ) {
		global $amazonhiddenmsg, $amazonerrormsg, $apippopennewwindow, $apippnewwindowhtml, $post;
		$thenewret = array();
		// do some adjustements to make sure some attribs are set a vertain way.
		$atts[ 'single_only' ] = isset( $atts[ 'single_only' ] ) && $atts[ 'single_only' ] == 'true' ? 1 : 0;
		$atts[ 'is_block' ] = isset( $atts[ 'is_block' ] ) && $atts[ 'is_block' ] == 'true' ? 1 : 0;
		$atts[ 'title_charlen' ] = isset( $atts[ 'title_charlen' ] ) && (( int )$atts[ 'title_charlen' ] >= 0 && ( int )$atts[ 'title_charlen' ] <= 150) ? ( int )$atts[ 'title_charlen' ] : 0;
		$atts[ 'newWindow' ] = isset( $atts[ 'newWindow' ] ) && $atts[ 'newWindow' ] == 'true' ? 1 : 0;
		$atts[ 'image_count' ] = isset( $atts[ 'image_count' ] ) && (( int )$atts[ 'image_count' ] <= 10 || ( int )$atts[ 'image_count' ] >= -1) ? ( int )$atts[ 'image_count' ] : -1;
		$atts[ 'target' ] = isset( $atts[ 'target' ] ) && esc_attr( $atts[ 'target' ] ) != '' ? esc_attr( $atts[ 'target' ] ) : '_blank';
		$appip_text_lgimage = apply_filters( 'appip_text_lgimage', __( "See larger image", 'amazon-product-in-a-post-plugin' ) );
		if(isset($atts[ 'container']) && ($atts[ 'container'] == 'null' || $atts[ 'container'] ==''))
			$atts[ 'container'] = 'null';
		
		$defaults = array(
			'asin' => '',
			'locale' => APIAP_LOCALE,
			'partner_id' => APIAP_ASSOC_ID,
			'private_key' => APIAP_SECRET_KEY,
			'public_key' => APIAP_PUB_KEY,
			'fields' => '',
			'field' => '',
			'listprice' => 1,
			'used_price' => 1,
			'replace_title' => '',
			'template' => 'default',
			'msg_instock' => 'In Stock',
			'msg_outofstock' => 'Out of Stock',
			'newWindow' => 0,
			'image_count' => -1, //The number of Images in the Gallery. -1 = all, or 0-10
			'single_only' => 0, //show on Single Only
			'className' => '', //Gutenberg Additional className attribute.
			'is_block' => 0, //Special attribute to tell if this is a Block element or a shortcode.
			'title_charlen' => 0, // if greater than 0 will concat text fileds
			'replace_title' => '',
			'charlen' => 0, // if greater than 0 will concat text fileds
			'target' => '_blank',
			'button_url' => '',
			'button' => '',
			'container' => apply_filters( 'amazon-elements-container', 'div' ),
			'container_class' => apply_filters( 'amazon-elements-container-class', 'amazon-element-wrapper' ),
			'labels' => '',
			'use_carturl' => false,
			'list_price' => null, //added only as a secondary use of $listprice
			'show_list' => null, //added only as a secondary use of $listprice 
			'show_used' => null, //added only as a secondary use of $used_price
			'usedprice' => null, //added only as a secondary use of $used_price
			'className' => '', //Gutenberg Additional className attribute.
			'button_use_carturl' => false,
		);
		$atts = shortcode_atts( $defaults, $atts );
		// fix spaces, returns, double spaces and new lines in ASINs
		if(isset($atts[ 'asin' ]) && $atts[ 'asin' ] != ''){
			$atts[ 'asin' ] = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$atts[ 'asin' ]);
		}
		$use_carturl =  isset( $atts[ 'use_carturl' ]) && $atts[ 'use_carturl' ] == '1' ? '1' : '0';
		$button_carturl = isset( $atts['button_use_carturl']) && $atts[ 'button_use_carturl' ] == '1'  ? '1'  : $use_carturl ; 
		$atts[ 'title_charlen' ] = ( int )$atts[ 'title_charlen' ];
		$atts[ 'charlen' ] = ( int )$atts[ 'charlen' ];
		$single_only = ( int )$atts[ 'single_only' ] == 1 || ( bool )get_option( 'apipp_show_single_only', false ) === true ? 1 : 0;
		if ( appip_check_blockEditor_is_active() ) {
			if ( defined( 'REST_REQUEST' ) && ( bool )REST_REQUEST ) {
				if ( $atts[ 'asin' ] == '' && ( bool )$atts[ 'is_block' ] )
					return '<div style="text-align:center;background: #f5f5f5;padding: 10px 5px;border: 1px solid #f48db0;"><strong>' . __( 'Amazon Elements Block', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'Please add at least one ASIN.', 'amazon-product-in-a-post-plugin' ) . '</div>';
			} else {
				if ( ( bool )$single_only === true && !is_singular() )
					return '';
			}
		} elseif ( !is_admin() && ( bool )$single_only && !is_singular() ) {
			return '';
		}
		if ( $atts[ 'template' ] != '' )
			$atts[ 'container_class' ] = $atts[ 'container_class' ] . ' amazon-template--elements-' . $atts[ 'template' ];
		$origatts = $atts;
		$asin = $atts[ 'asin' ];
		if ( strpos( $atts[ 'asin' ], ',' ) !== false )
			$asin = explode( ',', str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$atts[ 'asin' ] ) );
		else
			$asin = array($atts[ 'asin' ]);
		$button_url = $atts[ 'button_url' ] != '' ? explode( ",", str_replace( ', ', ',', $atts[ 'button_url' ] ) ) : array();
		$wrap = str_replace( array( '<', '>' ), array( '', '' ), ($atts[ 'container' ] == 'null' ? 'div' : esc_attr($atts[ 'container' ]) ));
		$atts[ 'title_charlen' ] = $atts[ 'title_charlen' ] >= 0 && $atts[ 'title_charlen' ] <= 150 ? $atts[ 'title_charlen' ] : 0;
		$atts[ 'title_charlen' ] = $atts[ 'title_charlen' ] == 0 && $atts[ 'charlen' ] > 0 && $atts[ 'charlen' ] <= 150 ? $atts[ 'charlen' ] : $atts[ 'title_charlen' ];
		
		$replace_titleA = array();
		if(strpos($atts[ 'replace_title' ],"::")!== false){
			$replace_titleA = explode("::",$atts[ 'replace_title' ]);
		}else{
			$replace_titleA[] = $atts[ 'replace_title' ];
		}
		$containerWrap = $atts[ 'container' ] == 'null' ? false : true; 

		extract( shortcode_atts( $defaults, $atts ) );
		$prodLinkField = apply_filters( 'amazon-grid-link', 'DetailPageURL', $post ); //CartURL
		$target = $atts[ 'target' ];
		if ( $atts[ 'target' ] != '' && ( bool )$atts[ 'newWindow' ] === false )
			$target = '';
		$target = $target != '' ? ' target="' . $target . '" ': '';
		$target = $target === '' && ( bool )$apippopennewwindow ? $apippnewwindowhtml : $target;
		$new_button_arr = amazon_product_get_new_button_array( $atts[ 'locale' ] );
		if ( $atts[ 'field' ] == '' && $atts[ 'fields' ] != '' )
			$atts[ 'field' ] = $atts[ 'fields' ];
		$labels = array();
		if ( $atts[ 'labels' ] != '' ) {
			$labelstemp = explode( ',', $atts[ 'labels' ] );
			foreach ( $labelstemp as $k => $lab ) {
				$keytemp = explode( '::', $lab );
				if ( isset( $keytemp[ 0 ] ) && isset( $keytemp[ 1 ] ) ) {
					// this takes care of alias fields.
					$lbltemp = '';
					switch ( strtolower( $keytemp[ 0 ] ) ) {
						case 'list-price': // alias for 'list'
						case 'list price': // alias for 'list'
							$lbltemp = 'list';
							break;
						case 'description': // alias for 'desc'
							$lbltemp = 'desc';
							break;
						case 'new-price': // alias for 'price'
						case 'new price': // alias for 'price'
							$lbltemp = 'price';
							break;
						default:
							$lbltemp = $keytemp[ 0 ];
							break;
					}
					$labels[ $lbltemp ][] = esc_attr( apply_filters( 'appip_label_text_' . str_replace( ' ', '-', strtolower( $keytemp[ 1 ] ) ), $keytemp[ 1 ] /*value*/ , $lbltemp /*field*/ , 'amazon-element' ) );
				}
			}
		}
		$noimage = plugins_url( 'images/noimage.jpg', dirname( __FILE__ ) );
		if ( $asin != '' ) {
			$aws_id = $atts[ 'partner_id' ];
			$_asins = ( is_array( $asin ) && !empty( $asin ) ) ? implode( ',', $asin ) : $asin; //valid ASIN or ASINs 
			$asinR = explode( ",", trim( str_replace( ', ', ',', $_asins ) ) );
			/* New Button functionality */
			$button = array();
			if ( $atts[ 'button' ] != '' ) {
				$buttonstemp = explode( ',', $atts[ 'button' ] );
				if ( count( $buttonstemp ) === 1 && count( $asinR ) > 1 ) {
					foreach ( $asinR as $kba => $kbv ) {
						$button[] = $buttonstemp[ 0 ];
					}
				} else {
					foreach ( $buttonstemp as $buttona ) {
						if ( !empty( $buttona ) ) {
							$button[] = $buttona;
						}
					}
				}
			}
			/* END New Button functionality */
			$errors = '';
			/* NEW */
			$Regions = __getAmz_regions();
			$region = $Regions[ $atts[ 'locale' ] ][ 'RegionCode' ];
			$host = $Regions[ $atts[ 'locale' ] ][ 'Host' ];
			$accessKey = $atts[ 'public_key' ];
			$secretKey = $atts[ 'private_key' ];
			$payloadArr = array();
			$payloadArr[ 'ItemIds' ] = $asinR;
			$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
			$payloadArr[ 'PartnerTag' ] = $atts[ 'partner_id' ];
			$payloadArr[ 'PartnerType' ] = 'Associates';
			$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$atts[ 'locale' ];
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
			/* END NEW */
			$skipCache = false;
			$pxmlNew = amazon_plugin_aws_signed_request( $atts[ 'locale' ], array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $asinR, "AssociateTag" => $atts[ 'partner_id' ], "RequestBy" => 'amazon-elements' ), $atts[ 'public_key' ], $atts[ 'private_key' ], ($skipCache ? true : false) );
			$totalResult2 = array();
			$totalResult3 = array();
			$er2Arr = array();
			$pxmle = array();
			if ( is_array( $pxmlNew ) && !empty( $pxmlNew ) ) {
				$errorsArr = array();
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
					return '<div class="appip-block-wrapper appip-block-wrapper-error" style="border:1px solid #f48db0;padding:15px;text-align:center;background:#f5f5f5;"><div style="text-align:center;color:#f00;font-weight:bold;padding:0 0 10px;">Amazon Element Block Errors</div>' . implode( "<br>", $errmsgBlock ) .'<div style="color:#aaa;font-size:.75em;font-style:italic;">This block will not be displayed on the front end of the website until the error is fixed.</div></div>';
				}else{
					return ''; //'<pre style="display:none;" class="appip-errors">APPIP ERROR: amazon-elements['."\n" . implode( "\n", $errmsg ) ."\n". ']</pre>';
				}
			} else {
				$resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array(); 
				$resultarr3 = isset( $totalResult3 ) && !empty( $totalResult3 ) ? $totalResult3 : array(); 
				$errors_prod = array();
				$arr_position = 0;
				if ( is_array( $resultarr ) ):
					$retarr = array();
					if($itemErrors && !empty($errmsgBlock)){
						foreach($errmsgBlock as $ekey => $eval){
							//$thenewret[] = '<!--'.$eval.'-->';
						}
					}
					$usSSL = amazon_check_SSL_on();
					$region = $atts[ 'locale' ];
					foreach ( $resultarr as $key =>  $result ):
						$result = (array) $result;
						$Errors = array();
						$result3 = $awsv5->GetAPPIPReturnVals_V5( $result, $totalResult3[$arr_position], $Errors );
						$result = array_merge($result,$result3);
						$currasin = isset( $result[ 'ASIN' ] ) ? $result[ 'ASIN' ] : '';
						if ( isset( $result[ 'NoData' ] ) && $result[ 'NoData' ] == '1' ):
							$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:block;" class="appip-errors">APPIP ERROR:nodata_elements[' . print_r($result[ 'Errors' ], true ) . '</'.$wrap.'>';
						elseif ( empty( $result[ 'ASIN' ] ) || $result[ 'ASIN' ] == 'Array' ):
							$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:block;" class="appip-errors">APPIP ERROR:nodata_elements_asin[ (' . $key . ')]</'.$wrap.'>';
						else :
						if ( ( int )$atts[ 'image_count' ] >= 1 && ( int )$atts[ 'image_count' ] <= 10 && is_array( $result[ 'AddlImagesArr' ] ) && !empty( $result[ 'AddlImagesArr' ] ) ) {
							$result[ 'AddlImages' ] = implode( '', array_slice( $result[ 'AddlImagesArr' ], 0, ( int )$atts[ 'image_count' ] ) );
						} elseif ( ( int )$atts[ 'image_count' ] == 0 ) {
							$result[ 'AddlImages' ] == '';
						}
						$linkURL = ( $use_carturl == '1' ) ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
						$btnlinkURL = ( $button_carturl == '1') ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
						$nofollow = ' rel="nofollow"';
						$nofollow = apply_filters( 'appip_template_add_nofollow', $nofollow, $result );
						$buttonURL = apply_filters( 'appip_amazon_button_url', plugins_url( '/images/generic-buy-button.png', dirname( __FILE__ ) ), 'generic-buy-button.png', $region );
						
						/*
						if ( $result[ 'Errors' ] != '' || (is_array($result[ 'Errors' ]) && !empty($result[ 'Errors' ]) ) ){
							$newErr = '<' . $wrap . ' style="display:none;" class="appip-errors">HIDDEN APIP ERROR(S): ' . $result[ 'Errors' ] . '</' . $wrap . '>';
						}
						*/
						//echo '<pre style="display:block;">$result:' . print_r( $result, true ).'</pre>';
						$fielda = is_array( $atts[ 'field' ] ) ? $atts[ 'field' ] : explode( ',', str_replace( ' ', '', $atts[ 'field' ] ) );
						foreach ( $fielda as $fieldarr ) {
							switch ( strtolower( $fieldarr ) ) {
								case 'title_clean':
									if(isset($replace_titleA[$arr_position]) && $replace_titleA[$arr_position]!=''){
										$NewTitle = $replace_titleA[$arr_position];
									}else{
										$NewTitle = Amazon_Product_Shortcode::appip_do_charlen(maybe_convert_encoding($result["Title"]),$atts[ 'title_charlen' ]);
									}
									//$NewTitle = Amazon_Product_Shortcode::appip_do_charlen( maybe_convert_encoding( $result[ "Title" ] ), $atts[ 'title_charlen' ] );
									$retarr[ $currasin ][ $fieldarr ] = $NewTitle;
									break;
								case 'author_clean':
									$retarr[ $currasin ][ $fieldarr ] = $result[ "Author" ];
									break;
								case 'desc_clean':
								case 'description_clean':
									/* NO AVAILABLE in PA API 5 */
									/*
									if ( is_array( $result[ "ItemDesc" ] ) && isset($result[ "ItemDesc" ][ 0 ] ) ) {
										$desc = preg_replace( '/^\s*\/\/<!\[CDATA\[([\s\S]*)\/\/\]\]>\s*\z/', '$1', $result[ "ItemDesc" ][ 0 ] );
										$retarr[ $currasin ][ $fieldarr ] = $desc[ 'Content' ];
									}
									*/
									break;
								case 'price_clean':
								case 'new-price_clean':
								case 'new price_clean':
									if ( $result[ "LowestNewPrice" ] == 'Too low to display' ) {
										$newPrice = 'Check Amazon For Pricing';
									} else {
										$newPrice = $result[ "LowestNewPrice" ];
									}
									if ( $result[ "TotalNew" ] > 0 ) {
										$retarr[ $currasin ][ $fieldarr ] = maybe_convert_encoding( $newPrice ) . ' - ' . $atts[ 'msg_instock' ];
									} else {
										$retarr[ $currasin ][ $fieldarr ] = maybe_convert_encoding( $newPrice ) . ' - ' . $atts[ 'msg_outofstock' ];
									}
									break;
								case 'image_clean':
								case 'med-image_clean':
								case 'mediumimage_clean':
									$retarr[ $currasin ][ $fieldarr ] = checkSSLImages_url( $result[ 'MediumImage' ] );
									break;
								case 'smallimage_clean':
								case 'sm-image_clean':
									$retarr[ $currasin ][ $fieldarr ] = checkSSLImages_url( $result[ 'SmallImage' ] );
									break;
								case 'LargeImage_clean':
								case 'lg-image_clean':
									$retarr[ $currasin ][ $fieldarr ] = checkSSLImages_url( $result[ 'LargeImage' ] );
									break;
								case 'hiresimage_clean':
								case 'full-image_clean':
								case 'large-image-link_clean':
									$retarr[ $currasin ][ $fieldarr ] = checkSSLImages_url( $result[ 'HiResImage' ] );
									break;
								case 'features_clean':
									$retarr[ $currasin ][ $fieldarr ] = maybe_convert_encoding( $result[ "Feature" ] );
									break;
								case 'link_clean':
									$retarr[ $currasin ][ $fieldarr ] = $linkURL;
									break;
								case 'button_clean':
									if ( is_array($button_url) && isset( $button_url[ $arr_position ] ) )
										$retarr[ $currasin ][ $fieldarr ] = $button_url[ $arr_position ];
									else
										$buttonURL = apply_filters( 'appip_amazon_button_url', plugins_url( '/images/generic-buy-button.png', dirname( __FILE__ ) ), 'generic-buy-button.png', $region );
									$retarr[ $currasin ][ $fieldarr ] = $buttonURL;
									break;
								case 'customerreviews_clean':
									/* NOT AVAILABLE RIGHT NOW - Not in Amazon PA API 5 */
									//$retarr[ $currasin ][ $fieldarr ] = $result[ 'CustomerReviews' ];
									break;
								case 'author':
									$retarr[ $currasin ][ $fieldarr ] = $result[ 'Author' ];
									break;
								case 'title':
									if(isset($replace_titleA[$arr_position]) && $replace_titleA[$arr_position]!=''){
										$NewTitle = $replace_titleA[$arr_position];
									}else{
										$NewTitle = Amazon_Product_Shortcode::appip_do_charlen(maybe_convert_encoding($result["Title"]),$title_charlen);
									}
									//$NewTitle = Amazon_Product_Shortcode::appip_do_charlen( maybe_convert_encoding( $result3[ "Title" ] ), $atts[ 'title_charlen' ] );
									if ( !isset( $labels[ 'title-wrap' ][ $arr_position ] ) && !isset( $labels[ 'title' ][ $arr_position ] ) ) {
										$labels[ 'title' ][ $arr_position ] = '<' . $wrap . ' class="appip-title"><a href="' . $linkURL . '"' . $target . $nofollow . '>' . $NewTitle . '</a></' . $wrap . '>';
									} elseif ( !isset( $labels[ 'title-wrap' ][ $arr_position ] ) && isset( $labels[ 'title' ][ $arr_position ] ) ) {
										$labels[ 'title' ][ $arr_position ] = '<' . $wrap . ' class="appip-title"><a href="' . $linkURL . '"' . $target . $nofollow . '>' . $labels[ 'title' ][ $arr_position ] . '</a></' . $wrap . '>';
									} elseif ( isset( $labels[ 'title-wrap' ][ $arr_position ] ) && isset( $labels[ 'title' ][ $arr_position ] ) ) {
										$labels[ 'title' ][ $arr_position ] = "<{$labels['title-wrap'][$arr_position]} class='appip-title'>{$labels['title'][$arr_position]}</{$labels['title-wrap'][$arr_position]}>";
									} elseif ( isset( $labels[ 'title-wrap' ][ $arr_position ] ) && !isset( $labels[ 'title' ][ $arr_position ] ) ) {
										$labels[ 'title' ][ $arr_position ] = '<' . $labels[ 'title-wrap' ][ $arr_position ] . ' class="appip-title">' . $NewTitle . '</' . $labels[ 'title-wrap' ][ $arr_position ] . '>';
									} else {
										$labels[ 'title' ][ $arr_position ] = '<' . $wrap . ' class="appip-title"><a href="' . $linkURL . '"' . $target . $nofollow . '>' . $NewTitle . '</a></' . $wrap . '>';
									}
									$retarr[ $currasin ][ $fieldarr ] = $labels[ 'title' ][ $arr_position ];
									break;
								case 'desc':
								case 'description':
									/* NOT AVAILABLE RIGHT NOW - Not in Amazon PA API 5 */
									/*
									$desc_lables = isset( $labels[ 'desc' ][ $arr_position ] ) ? $labels[ 'desc' ][ $arr_position ] : '';
									if ( $desc_lables !== '' ) {
										$desc_lables = '<span class="appip-label label-' . $fieldarr . '">' . $desc_lables . ' </span>';
									}
									if ( is_array( $result[ "ItemDesc" ] ) && isset($result[ "ItemDesc" ][ 0 ])) {
										$desc = preg_replace( '/^\s*\/\/<!\[CDATA\[([\s\S]*)\/\/\]\]>\s*\z/', '$1', $result[ "ItemDesc" ][ 0 ] );
										$retarr[ $currasin ][ $fieldarr ] = maybe_convert_encoding( $desc_lables . $desc[ 'Content' ] );
									}
									*/
									break;
								case 'gallery':
								case 'imagesets':
									if ( $result[ 'AddlImages' ] != '' ) {
										if ( !isset( $labels[ 'gallery' ][ $arr_position ] ) )
											$labels[ $fieldarr ][ $arr_position ] = __( 'Additional Images:', 'amazon-product-in-a-post-plugin' );
										else
											$labels[ $fieldarr ][ $arr_position ] = '<' . $wrap . ' class="appip-label label-' . $fieldarr . '">' . $labels[ $fieldarr ][ $arr_position ] . ' </' . $wrap . '>';
										$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><span class="amazon-additional-images-text">' . $labels[ $fieldarr ][ $arr_position ] . '</span><br/>' . $result[ 'AddlImages' ] . '</' . $wrap . '>';
									}
									break;
								case 'list':
								case 'list-price':
									$listLabel = '';
									$listPrice = '';
									if ( isset( $result[ "Binding" ] ) && "Kindle Edition" == $result[ "Binding" ] ) {
										$listLabel = '';
										$listPrice = '';
									} elseif ( isset( $result[ "NewAmazonPricing" ][ "New" ][ "List" ] ) ) {
										$listPrice = $result[ "NewAmazonPricing" ][ "New" ][ "List" ];
									}
									$listLabel = $listLabel == '' && isset( $labels[ $fieldarr ][ $arr_position ] ) ? $labels[ $fieldarr ][ $arr_position ] : $listLabel;
									if ( $listPrice != '' ) {
										if ( $listLabel != '' )
											$retarr[ $currasin ][ $fieldarr ] = '<span class="appip-label label-list">' . $listLabel . '</span> ' . $listPrice;
										else
											$retarr[ $currasin ][ $fieldarr ] = $listPrice;
									}
									break;
								case 'price+list':
									$listLabel = '';
									$newLabel = '';
									$newPrice = $result[ "NewAmazonPricing" ][ "New" ][ "Price" ];
									$listPrice = $result[ "NewAmazonPricing" ][ "New" ][ "List" ];
									$listLabel = $listLabel == '' && isset( $labels[ 'list' ][ $arr_position ] ) ? $labels[ 'list' ][ $arr_position ] : $listLabel;
									if ( $listPrice != '' ) {
										if ( $listLabel != '' )
											$retarr[ $currasin ][ $fieldarr ] = '<span class="appip-label label-list">' . $listLabel . '</span> ' . $listPrice;
										else
											$retarr[ $currasin ][ $fieldarr ] = $listPrice;
									}
									$newLabel = $newLabel == '' && isset( $labels[ 'price' ][ $arr_position ] ) ? $labels[ 'price' ][ $arr_position ] : $newLabel;
									$stockIn = ( $result[ "TotalNew" ] > 0 && $atts[ 'msg_instock' ] != '' && !( isset( $result[ "HideStockMsg" ] ) && ( bool )$result[ "HideStockMsg" ] == 1 ) ) ? ' <span class="instock">' . $atts[ 'msg_instock' ] . '</span>': '';
									$stockIn = ( $result[ "TotalNew" ] == 0 && $atts[ 'msg_outofstock' ] != '' && !( isset( $result[ "HideStockMsg" ] ) && ( bool )$result[ "HideStockMsg" ] == 1 ) ) ? ' <span class="outofstock">' . $atts[ 'msg_outofstock' ] . '</span>': $stockIn;
									if ( $newPrice != '' ) {
										if ( $newLabel != '' )
											$retarr[ $currasin ][ $fieldarr ] = '<span class="appip-label label-price">' . $newLabel . '</span> ' . $newPrice . $stockIn;
										else
											$retarr[ $currasin ][ $fieldarr ] = $newPrice;
									}
									break;
								case 'new-price':
								case 'new price':
								case 'price':
									$newLabel = '';
									$newPrice = '';
									if ( isset( $labels[ 'price' ][ $arr_position ] ) )
										$newLabel = $labels[ 'price' ][ $arr_position ];
									elseif ( isset( $labels[ 'subscription' ][ $arr_position ] ) )
										$newLabel = $labels[ 'subscription' ][ $arr_position ];
									$newPrice = isset($result[ "NewAmazonPricing" ][ "New" ][ "Price" ]) ? $result[ "NewAmazonPricing" ][ "New" ][ "Price" ] : '';
									$subscription = isset( $result[ 'SubscriptionLength' ] ) && $result[ 'SubscriptionLength' ] !== '' ? $result[ 'SubscriptionLength' ] : '';
									if ( $subscription !== '' ) {
										if ( $result[ 'SubscriptionLengthUnits' ] === 'days' && $subscription === '36599999' )
											$subscription = '365';
										if ( $subscription === '1' )
											$result[ 'SubscriptionLengthUnits' ] = str_replace( array( 'days', 'months', 'years', 'weeks' ), array( 'day', 'month', 'year', 'week' ), $result[ 'SubscriptionLengthUnits' ] );
										if ( $newLabel != '' )
											$retarr[ $currasin ][ $fieldarr ] = '<span class="appip-label label-subscription">' . $newLabel . '</span> ' . $newPrice . _x( ' for ', 'Label Text for Subscriptions, i.e., $12.00 FOR 12 months.', 'amazon-product-in-a-post-plugin' ) . $subscription . ' ' . $result[ 'SubscriptionLengthUnits' ];
										else
											$retarr[ $currasin ][ $fieldarr ] = '<span class="label">' . __( 'Subscription: ', 'amazon-product-in-a-post-plugin' ) . '</span> ' . $newPrice . _x( ' for ', 'Label Text for Subscriptions, i.e., $12.00 FOR 12 months.', 'amazon-product-in-a-post-plugin' ) . $subscription . ' ' . $result[ 'SubscriptionLengthUnits' ];
									} elseif ( $newPrice !== '' ) {
										if ( $newLabel != '' )
											$retarr[ $currasin ][ $fieldarr ] = '<span class="appip-label label-price">' . $newLabel . '</span> ' . $newPrice;
										else
											$retarr[ $currasin ][ $fieldarr ] = $newPrice;
									}
									break;
								case 'old-new-price':
								case 'old-new price':
									if ( isset( $labels[ 'price' ][ $arr_position ] ) ) {
										$labels[ 'price-new' ][ $arr_position ] = '<span class="appip-label label-' . $fieldarr . '">' . $labels[ 'price' ][ $arr_position ] . ' </span>';
									} elseif ( isset( $labels[ 'new-price' ][ $arr_position ] ) ) {
										$labels[ 'price-new' ][ $arr_position ] = '<span class="appip-label label-' . $fieldarr . '">' . $labels[ 'new-price' ][ $arr_position ] . ' </span>';
									} elseif ( isset( $labels[ 'new price' ][ $arr_position ] ) ) {
										$labels[ 'price-new' ][ $arr_position ] = '<span class="appip-label label-' . $fieldarr . '">' . $labels[ 'new price' ][ $arr_position ] . ' </span>';
									} else {
										$labels[ 'price-new' ][ $arr_position ] = '<span class="appip-label label-' . $fieldarr . '">' . 'New From:' . ' </span>';
									}
									$correctedPrice = isset( $result[ "Offers_Offer_OfferListing_Price_FormattedPrice" ] ) ? $result[ "Offers_Offer_OfferListing_Price_FormattedPrice" ] : $result[ "LowestNewPrice" ];
									if ( $correctedPrice == 'Too low to display' ) {
										$newPrice = __( 'Check Amazon For Pricing', 'amazon-product-in-a-post-plugin' );
									} else {
										$newPrice = $correctedPrice;
									}
									if ( ( int )$newPrice != 0 ) {
										if ( $result[ "TotalNew" ] > 0 ) {
											$retarr[ $currasin ][ $fieldarr ] = $labels[ 'price-new' ][ $arr_position ] . maybe_convert_encoding( $newPrice ) . ' <span class="instock">' . $atts[ 'msg_instock' ] . '</span>';
										} else {
											$retarr[ $currasin ][ $fieldarr ] = $labels[ 'price-new' ][ $arr_position ] . maybe_convert_encoding( $newPrice ) . ' <span class="outofstock">' . $atts[ 'msg_outofstock' ] . '</span>';
										}
									}
									break;
								case 'image':
								case 'MediumImage':
								case 'med-image':
									$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><a href="' . $linkURL . '"' . $target . '>' . checkSSLImages_tag( $result[ 'MediumImage' ], 'amazon-image amazon-image-medium', $currasin ) . '</a></' . $wrap . '>';
									break;
								case 'SmallImage':
								case 'sm-image':
									$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><a href="' . $linkURL . '"' . $target . '>' . checkSSLImages_tag( $result[ 'SmallImage' ], 'amazon-image amazon-image-small', $currasin ) . '</a></' . $wrap . '>';
									break;
								case 'lg-image':
								case 'LargeImage':
									$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><a href="' . $linkURL . '"' . $target . '>' . checkSSLImages_tag( $result[ 'LargeImage' ], 'amazon-image amazon-image-large', $currasin ) . '</a></' . $wrap . '>';
									break;
								case 'full-image':
								case 'FullImage':
									if ( isset( $result[ 'HiResImage' ] ) ) // if there is a hires image by chance, give that
										$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><a href="' . $linkURL . '"' . $target . '>' . checkSSLImages_tag( $result[ 'HiResImage' ], 'amazon-image amazon-image-hires', $currasin ) . '</a></' . $wrap . '>';
									else
										$retarr[ $currasin ][ $fieldarr ] = '<' . $wrap . ' class="amazon-image-wrapper"><a href="' . $linkURL . '"' . $target . '>' . checkSSLImages_tag( $result[ 'LargeImage' ], 'amazon-image amazon-image-large', $currasin ) . '</a></' . $wrap . '>';
									break;
								case 'large-image-link':
									if ( !isset( $labels[ 'large-image-link' ][ $arr_position ] ) ) {
										$labels[ 'large-image-link' ][ $arr_position ] = $appip_text_lgimage;
									} else {
										$labels[ 'large-image-link' ][ $arr_position ] = $labels[ $fieldarr ][ $arr_position ] . ' ';
									}
									if ( isset( $result[ 'LargeImage' ] ) && $result[ 'LargeImage' ] != '' ) {
										$retarr[ $currasin ][ $fieldarr ] = '<div class="amazon-image-link-wrapper"><a rel="appiplightbox-' . $result[ 'ASIN' ] . '" href="#" data-appiplg="' . checkSSLImages_url( $result[ 'LargeImage' ] ) . '"><span class="amazon-element-large-img-link">' . $labels[ 'large-image-link' ][ $arr_position ] . '</span></a></div>';
									}
									break;
								case 'features':
									if ( !isset( $labels[ 'features' ][ $arr_position ] ) ) {
										$labels[ 'features' ][ $arr_position ] = '';
									} else {
										$labels[ 'features' ][ $arr_position ] = '<span class="appip-label label-' . $fieldarr . '">' . $labels[ $fieldarr ][ $arr_position ] . ' </span>';
									}
									$retarr[ $currasin ][ $fieldarr ] = $labels[ 'features' ][ $arr_position ] . maybe_convert_encoding( $result[ "Feature" ] );
									break;
								case 'link':
									$retarr[ $currasin ][ $fieldarr ] = '<a href="' . $linkURL . '"' . $target . '>' . $linkURL . '</a>';
									break;
								case 'new-button':
									$button_class = ' class="btn btn-primary"';
									$button_txt = __( 'Read More', 'amazon-product-in-a-post-plugin' );
									$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . $nofollow . $button_class . ' href="' . $btnlinkURL . '">' . $button_txt . '</a>';
									break;
								case 'button':
									if ( is_array($button_url) &&  isset( $button_url[ $arr_position ] ) ) {
										$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . ' href="' . $btnlinkURL . '"' . $nofollow . '><img src="' . $button_url[ $arr_position ] . '" border="0" /></a>';
									} else {
										if ( isset( $button[ $arr_position ] ) ) {
											$bname = $button[ $arr_position ];
											$brounded = strpos( $bname, 'rounded' ) !== false ? true : false;
											$bclass = isset( $new_button_arr[ $bname ][ 'color' ] ) ? 'amazon__btn' . $new_button_arr[ $bname ][ 'color' ] . ' amazon__price--button--style' . ( $brounded ? ' button-rounded' : '' ): 'amazon__btn amazon__price--button--style';
											$btext = isset( $new_button_arr[ $bname ][ 'text' ] ) ? esc_attr( $new_button_arr[ $bname ][ 'text' ] ) : _x( 'Buy Now', 'button text', 'amazon-product-in-a-post-plugin' );
											$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . ' href="' . $btnlinkURL . '"' . $nofollow . ' class="' . $bclass . '">' . $btext . '</a>';
										} else {
											$buttonURL = apply_filters( 'appip_amazon_button_url', plugins_url( '/images/generic-buy-button.png', dirname( __FILE__ ) ), 'generic-buy-button.png', $region );
											$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . ' href="' . $btnlinkURL . '"' . $nofollow . '><img class="amazon-price-button-img" src="' . $buttonURL . '" alt="' . apply_filters( 'appip_amazon_button_alt_text', __( 'buy now', 'amazon-product-in-a-post-plugin' ), $currasin ) . '" border="0" /></a>';
										}
									}
									break;
								case 'customerreviews':
									//$retarr[ $currasin ][ $fieldarr ] = '<iframe src="' . $result[ 'CustomerReviews' ] . '" class="amazon-customer-reviews" width="100%" seamless="seamless" scrolling="no"></iframe>';
									break;
								default:
									if ( preg_match( '/\_clean$/', $fieldarr ) ) {
										$tempfieldarr = str_replace( '_clean', '', $fieldarr );
										$retarr[ $currasin ][ $fieldarr ] = isset( $result[ $tempfieldarr ] ) && $result[ $tempfieldarr ] != '' ? $result[ $tempfieldarr ] : '';
									} else {
										if ( isset( $result[ $fieldarr ] ) && $result[ $fieldarr ] != '' && $result[ $fieldarr ] != '0' ) {
											if ( !isset( $labels[ $fieldarr ][ $arr_position ] ) ) {
												$labels[ $fieldarr ][ $arr_position ] = '';
											} else {
												$labels[ $fieldarr ][ $arr_position ] = '<span class="appip-label label-' . str_replace( ' ', '-', $fieldarr ) . '">' . $labels[ $fieldarr ][ $arr_position ] . ' </span>';
											}
											$retarr[ $currasin ][ $fieldarr ] = $labels[ $fieldarr ][ $arr_position ] . $result[ $fieldarr ];
										} else {
											$retarr[ $currasin ][ $fieldarr ] = '';
										}
									}
									break;
							}
						}
					endif;

					/* 
					NEW Filter Version - only applies filter to current ASIN
					while not breaking the filter.
					*/
					$temparr = isset($retarr[ $currasin ]) ? array( 'temp' => $retarr[ $currasin ] ) : array('temp' => '');
					$temparr = apply_filters( 'amazon_product_in_a_post_plugin_elements_filter', $temparr );
					$retarr[ $currasin ] = $temparr[ 'temp' ];
					$containerWrap = $atts[ 'container' ] == 'null' ? false : true; 
					if ( $wrap != '' && $containerWrap) {
						$thenewret[] = '<div class="appip-block-wrapper  block-there"><' . $wrap . ' class="' . $atts[ 'container_class' ] . '">';
					}
					if ( isset( $retarr[ $currasin ] ) && is_array( $retarr[ $currasin ] ) && !empty( $retarr[ $currasin ] ) ) {
						foreach ( $retarr[ $currasin ] as $key => $val ) {
							if ( $key != '' ) {
								if ( preg_match( '/\_clean$/', $key ) ){
									$thenewret[] = $val;
									$containerWrap = false;
								}else{
									$thenewret[] = '<' . $wrap . ' class="amazon-element-' . $key . '">' . $val . '</' . $wrap . '>';
								}
							}
						}
					}
					if ( $wrap != '' && $containerWrap) {
						$thenewret[] = "</{$wrap}></div>";
					}
					$arr_position++;
				endforeach;

				if ( is_array($errors_prod) && !empty($errors_prod) )
					return '<div class="appip-block-wrapper error-block">' . implode( "\n", $errors_prod ) . '</div>';
				if ( is_array( $thenewret ) ) {
					if ( $atts[ 'className' ] != '' )
						$className = ' ' . implode( ' ', explode( ',', str_replace( array( ', ', ' ' ), array( ',', ',' ), esc_attr($atts[ 'className' ]) ) ) );
					if($containerWrap){
						return '<div class="appip-block-wrapper' . $className . '">' . implode( "\n", $thenewret ) . '</div>';
					}else{
						return implode( "\n", $thenewret );
					}
				}
				return false;
				endif;
			}
		} else {
			return false;
		}
	}
}
$AppipShortcodeElements = new Amazon_Product_Shortcode_Elements( array( 'amazon-element', 'amazon-elements' ) );

function appip_elements_php_block_init() {
	if ( function_exists( 'register_block_type' ) ) {
		global $apippopennewwindow,$amazon_styles_enqueued;
		$pluginStyles = array( 'amazon-theme-styles' );
		$pluginScripts = array( 'amazon-elements-block' );
		$wheretoenqueue = 'amazon-theme-styles';
		if ( file_exists( get_stylesheet_directory() . '/appip-styles.css' ) ) {
			wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/appip-styles.css', array(), null );
		} elseif ( file_exists( get_stylesheet_directory() . '/css/appip-styles.css' ) ) {
			wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/css/appip-styles.css', array(), null );
		} else {
			$wheretoenqueue = 'amazon-default-styles';
			wp_enqueue_style( 'amazon-default-styles', plugins_url( 'css/amazon-default-plugin-styles.css', dirname( __FILE__ ) ), array(), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-default-plugin-styles.css' ) );
		}
		wp_enqueue_style( 'amazon-frontend-styles', plugins_url( 'css/amazon-frontend.css', dirname( __FILE__ ) ), array( $wheretoenqueue ), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-frontend.css' ) );
		$pluginStyles[] = 'amazon-frontend-styles';
		$usemine = get_option( 'apipp_product_styles_mine', false );
		if ( $usemine && !$amazon_styles_enqueued) {
			$data = wp_kses( get_option( 'apipp_product_styles', '' ), array( "\'", '\"' ) );
			if ( $data != '' )
				wp_add_inline_style( 'amazon-frontend-styles', $data );
			$amazon_styles_enqueued = true;
		}

		wp_register_script(
			'amazon-elements-block',
			plugins_url( '/blocks/php-block-elements.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'blocks/php-block-elements.js' )
		);

		register_block_type( 'amazon-pip/amazon-elements', array(
			'attributes' => array(
				'fields' => array(
					'type' => 'string',
					'default' => 'image,title,price,button',
				),
				'asin' => array(
					'type' => 'string',
				),
				'newWindow' => array(
					'type' => 'bool',
					'default' => ( bool )$apippopennewwindow,
				),
				'single_only' => array(
					'type' => 'bool',
					'default' => false,
				),
				'image_count' => array(
					'type' => 'number',
					'default' => -1,
				),
				'className' => array(
					'type' => 'string',
				),
				'is_block' => array(
					'type' => 'bool',
					'default' => true,
				),
				'title_charlen' => array(
					'type' => 'number',
					'default' => 0,
				),
				'template' => array(
					'type' => 'string',
					'default' => 'default',
				),
				'use_carturl' => array(
					'type' => 'bool',
					'default' => false,
				),
				'button' => array(
					'type' => 'string',
					'default' => 'read-more-green-rounded',
				),
				'labels' => array(
					'type' => 'string',
					'placeholder' => __( 'Labels (optional)', 'amazon-product-in-a-post-plugin' ),
					'default' => '',
				),
				'button_url' => array(
					'type' => 'string',
				),
				'container' => array(
					'type' => 'string',
					'default' => apply_filters( 'amazon-elements-container', 'div' ),
				),
				'container_class' => array(
					'type' => 'string',
					'default' => apply_filters( 'amazon-elements-container-class', 'amazon-element-wrapper' ),
				),
			),
			'editor_style' => $pluginStyles,
			'editor_script' => $pluginScripts,
			'render_callback' => array( 'Amazon_Product_Shortcode_Elements', 'do_shortcode' ),
		) );
	}
}
add_action( 'init', 'appip_elements_php_block_init' );