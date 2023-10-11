<?php

class Amazon_Product_Shortcode_Search extends Amazon_Product_Shortcode{
	static function _setup( ){}
	static function do_shortcode($atts, $content = ''){
		global $amazonhiddenmsg, $amazonerrormsg, $apippopennewwindow, $apippnewwindowhtml, $post;
		$thenewret = array();
		$atts[ 'single_only' ] = isset( $atts[ 'single_only' ] ) && ($atts[ 'single_only' ] == 'true' || $atts[ 'single_only' ] == '1') ? 1 : 0;
		$atts[ 'search_title' ] = isset( $atts[ 'search_title' ] ) && ($atts[ 'search_title' ] == 'true' || $atts[ 'search_title' ] == '1') ? 1 : 0;
		$atts[ 'is_block' ] = isset( $atts[ 'is_block' ] ) && ($atts[ 'is_block' ] == 'true' || $atts[ 'is_block' ] == '1') ? 1 : 0;
		$atts[ 'title_charlen' ] = isset( $atts[ 'title_charlen' ] ) && (( int )$atts[ 'title_charlen' ] >= 0 && ( int )$atts[ 'title_charlen' ] <= 150) ? ( int )$atts[ 'title_charlen' ] : 0;
		$atts[ 'newWindow' ] = isset( $atts[ 'newWindow' ] ) && ($atts[ 'newWindow' ] == 'true' || $atts[ 'newWindow' ] == '1') ? 1 : 0;
		$atts[ 'image_count' ] = isset( $atts[ 'image_count' ] ) && (( int )$atts[ 'image_count' ] <= 10 || ( int )$atts[ 'image_count' ] >= -1) ? ( int )$atts[ 'image_count' ] : -1;
		$atts[ 'item_count' ] = isset( $atts[ 'item_count' ] ) && (( int )$atts[ 'item_count' ] <= 10 || ( int )$atts[ 'image_count' ] >= 1) ? ( int )$atts[ 'item_count' ] : 10;
		$atts[ 'item_page' ] = isset( $atts[ 'item_page' ] ) && (( int )$atts[ 'item_page' ] <= 10 || ( int )$atts[ 'item_page' ] >= 1) ? ( int )$atts[ 'item_page' ] : 1;
		$atts[ 'target' ] = isset( $atts[ 'target' ] ) && (esc_attr( $atts[ 'target' ] ) != '') ? esc_attr( $atts[ 'target' ] ) : '_blank';
		$appip_text_lgimage = apply_filters( 'appip_text_lgimage', __( "See larger image", 'amazon-product-in-a-post-plugin' ) );
		$defaults = array(
			'keywords'		=> '',
			'search_index'	=> 'All',
			'search_title'  => 0,
			'sort'			=> 'Relevance',
			'item_page'		=> '1',
			'page'			=> '1',
			'locale' 		=> APIAP_LOCALE,
			'partner_id' 	=> APIAP_ASSOC_ID,
			'private_key' 	=> APIAP_SECRET_KEY,
			'public_key' 	=> APIAP_PUB_KEY, 
			'item_count'	=> 10,
			'fields'		=> apply_filters( 'amazon-search-fields', 'image,title,button', $post ),
			'field'			=> '',
			'button' 		=> '',
			'listprice' 	=> 1, 
			'used_price' 	=> 1,
			'browse_node' 	=> '',
			'condition' 	=> 'New',
			'replace_title' => '', 
			'template' 		=> 'default',
			'msg_instock' 	=> 'In Stock',
			'msg_outofstock'=> 'Out of Stock',
			'image_count' => -1, //The number of Images in the Gallery. -1 = all, or 0-10
			'single_only' => 0, //show on Single Only
			'className' => '', //Gutenberg Additional className attribute.
			'is_block' => 0, //Special attribute to tell if this is a Block element or a shortcode.
			'title_charlen' => 0, // if greater than 0 will concat text fileds
			'charlen' => 0, // if greater than 0 will concat text fileds
			'target' 		=> '_blank',
			'button_url' 	=> '',
			'columns' => ( int )apply_filters( 'amazon-search-columns', 3, $post ),
			'container' 	=> apply_filters('amazon-elements-container','div'),
			'container_class' => apply_filters('amazon-elements-container-class','amazon-element-wrapper'),
			'labels' 		=> '',
			'button_use_carturl' => '',	
			'use_carturl' 	=> false,
			'list_price' 	=> null, 		//added only as a secondary use of $listprice
			'show_list' 	=> null,		//added only as a secondary use of $listprice 
			'show_used'		=> null,		//added only as a secondary use of $used_price
			'usedprice' 	=> null,		//added only as a secondary use of $used_price
			'newWindow'		=> '',
			'availability'	=> 'Available', // Available or IncludeOutOfStock
		);

		//For Available Search index, condition and Sort parameters by Locale:
		//https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
		
		$atts = shortcode_atts( $defaults, $atts );
		$atts[ 'condition' ] = isset($atts[ 'condition' ]) && (in_array($atts[ 'condition' ], array('New', 'Used', 'Collectible', 'Refurbished', 'All'))) ? esc_attr($atts[ 'condition' ]) : '';
		$atts[ 'template' ] = isset($atts[ 'template' ]) && $atts[ 'template' ] == '' ? 'default' : esc_attr($atts[ 'template' ]);
		$atts[ 'container_class' ] = isset($atts[ 'container_class' ]) ? esc_attr($atts[ 'container_class' ]) : '';
		if ( $atts[ 'template' ] != '' )
			$atts[ 'container_class' ] = $atts[ 'container_class' ] . ' amazon-template--search-' . $atts[ 'template' ];
		extract( $atts );
		$single_only = isset($atts[ 'single_only' ]) && (( int )$atts[ 'single_only' ] == 1 || ( bool )get_option( 'apipp_show_single_only', false ) === true) ? 1 : 0;
		
		if ( appip_check_blockEditor_is_active() ) {
			if ( defined( 'REST_REQUEST' ) && ( bool )REST_REQUEST ) {
				if ( $atts[ 'keywords' ] == '' && ( bool )$atts[ 'is_block' ] )
					return '<div style="text-align:center;background: #f5f5f5;padding: 10px 5px;border: 1px solid #f48db0;"><strong>' . __( 'Amazon Search Block', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'Please add at least one Keyword.', 'amazon-product-in-a-post-plugin' ) . '</div>';
			} else {
				if ( ( bool )$single_only === true && !is_singular() )
					return '';
			}
		} elseif ( !is_admin() && ( bool )$single_only && !is_singular() ) {
			return '';
		}
		$origatts = $atts;
		$use_carturl =  isset( $atts[ 'use_carturl' ]) && (bool) $atts[ 'use_carturl' ] == true ? '1' : '0';
		$button_carturl = isset( $atts['button_use_carturl']) && $atts[ 'button_use_carturl' ] == '1'  ? '1'  : $use_carturl ; 
		$button_url = isset($atts[ 'button_url' ]) && $atts[ 'button_url' ] != '' ? explode( ",", str_replace( ', ', ',', $atts[ 'button_url' ] ) ) : array();
		$wrap = str_replace( array( '<', '>' ), array( '', '' ), $atts[ 'container' ] );
		$atts[ 'title_charlen' ] = isset($atts[ 'title_charlen' ]) && (int) $atts[ 'title_charlen' ] >= 0 && (int) $atts[ 'title_charlen' ] <= 150 ? (int) $atts[ 'title_charlen' ] : 0;
		$atts[ 'title_charlen' ] = isset($atts[ 'charlen' ]) && $atts[ 'title_charlen' ] == 0 && (int) $atts[ 'charlen' ] > 0 && (int) $atts[ 'charlen' ] <= 150 ? (int) $atts[ 'charlen' ] : $atts[ 'title_charlen' ];
		$atts[ 'keywords'] 	= str_replace(", ",",", $atts[ 'keywords']);
		$keywords = '';
		if($atts[ 'keywords'] != '')
			 $keywords = explode(',',$atts[ 'keywords']);
		$atts[ 'field'] = $atts[ 'field'] == '' && $atts[ 'fields'] !='' ? $atts[ 'fields'] : $atts[ 'field'];
		$field = $atts[ 'field'];
		$target = $atts[ 'target' ];
		if ( $atts[ 'target' ] != '' && ( bool )$atts[ 'newWindow' ] === false )
			$target = '';
		$target = $target != '' ? ' target="' . $target . '" ': '';
		$target = $target === '' && ( bool )$apippopennewwindow ? $apippnewwindowhtml : $target;
		$item_page		= (int) $item_page;
		if((int)$page > 1)
			$item_page		= (int) $page;
		//'All','Wine','Wireless','ArtsAndCrafts','Miscellaneous','Electronics','Jewelry','MobileApps','Photo','Shoes','KindleStore','Automotive','MusicalInstruments','DigitalMusic','GiftCards','FashionBaby','FashionGirls','GourmetFood','HomeGarden','MusicTracks','UnboxVideo','FashionWomen','VideoGames','FashionMen','Kitchen','Video','Software','Beauty','Grocery',,'FashionBoys','Industrial','PetSupplies','OfficeProducts','Magazines','Watches','Luggage','OutdoorLiving','Toys','SportingGoods','PCHardware','Movies','Books','Collectibles','VHS','MP3Downloads','Fashion','Tools','Baby','Apparel','Marketplace','DVD','Appliances','Music','LawnAndGarden','WirelessAccessories','Blended','HealthPersonalCare','Classical'	
		$charlen 		= $atts[ 'title_charlen' ];
		$new_button_arr = amazon_product_get_new_button_array($locale);

		if($labels != ''){
			$labelstemp = explode(',',$labels);
			unset($labels);
			foreach($labelstemp as $lab){
				$keytemp = explode('::',$lab);
				if(isset($keytemp[0]) && isset($keytemp[1])){
					$labels[$keytemp[0]] = $keytemp[1];
				}
			}
		}else{
			$labels = array();
		}
		if ( (is_array( $keywords ) && !empty( $keywords )) || ($search_index !== 'All' && $browse_node !== '' )  ){
			$errors = '';
			$search_kw = is_array( $keywords ) && !empty( $keywords ) ? str_replace( " ", '+', implode( ",", str_replace( '`','"',$keywords ) ) ) : '';
			$search_title = (int) $atts[ 'search_title'] == 1 ? $search_kw : '';
			$Available = $availability != '' && in_array($availability, array('IncludeOutOfStock', 'Available')) ? $availability : "Available";
			$SortBy = $sort != '' && in_array($sort, array( "Relevance","AvgCustomerReviews","Featured","NewArrivals","Price:HighToLow","Price:LowToHigh")) ? $sort : '';
			/* NEW */
			$Regions = __getAmz_regions();
			$region = $Regions[ $atts[ 'locale' ] ][ 'RegionCode' ];
			$host = $Regions[ $atts[ 'locale' ] ][ 'Host' ];
			$accessKey = $atts[ 'public_key' ];
			$secretKey = $atts[ 'private_key' ];
			$payloadArr = array();
			$payloadArr[ 'Keywords' ] = $search_kw;
			$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN','SearchRefinements' );
			if($condition !== '')
				$payloadArr[ "Condition" ] = $condition;	
			if((int) $item_page >= 1 &&  (int)$item_page <= 10)
				$payloadArr[ "ItemPage" ] = (int) $item_page;
			if($Available !== '')
				$payloadArr[ "Availability" ] = $Available;	
			if($SortBy !== '')
				$payloadArr[ "SortBy" ] = $SortBy;	
			if($search_index !== '')
				$payloadArr[ "SearchIndex" ] = $search_index;
			if($browse_node !== '')
				$payloadArr[ "BrowseNodeId" ] = (int) $browse_node;	
			if($item_count !== '')
				$payloadArr[ "ItemCount" ] = (int)$item_count;	
			if($search_title != '')
				$payloadArr[ "Title" ] = $search_title;	
			$payloadArr[ 'PartnerTag' ] = $atts[ 'partner_id' ];
			$payloadArr[ 'PartnerType' ] = 'Associates';
			$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$atts[ 'locale' ];
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
			/* END NEW */
			$skipCache = false;
			$pxmlNew = amazon_plugin_aws_signed_request( $atts[ 'locale' ], array( "Operation" => "SearchItems", "payload" => $payloadArr, "ItemId" => array(), "AssociateTag" => $atts[ 'partner_id' ], "RequestBy" => 'amazon-search' ), $atts[ 'public_key' ], $atts[ 'private_key' ], ($skipCache ? true : false) );

			$resultarr = array();
			$totalResult2 = array();
			$totalResult3 = array();
			$errorsArr = array();
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
					}elseif(is_array( $pxml ) && isset($pxml['0']['__type']) && !isset($pxml['Items'])){
						// only an error and no items
						$errorsArr[] = $pxml['0'];
					}elseif(is_array( $pxml ) && isset($pxml['Errors']) && isset($pxml['Items'])){
						//itmes and errors - grab the errors
						$er2Arr[] = $pxml['Errors'];
						unset($pxml['Errors']);
						$r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, array($search_kw), $pxmlkey);
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
					}elseif(is_array( $pxml ) && isset($pxml['Items'])){
						//only items
						$r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, array($search_kw), $pxmlkey);
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
			$itemErrors = false;
			$errmsgBlock = array();
			$skip = false;
			if(!empty($errorsArr) && !empty($totalResult2) && $skip){
				//errors and items
				$itemErrors = true;
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
					return '<div class="appip-block-wrapper appip-block-wrapper-error" style="border:1px solid #f48db0;padding:15px;text-align:center;background:#f5f5f5;"><div style="text-align:center;color:#f00;font-weight:bold;padding:0 0 10px;">Amazon Search Block Errors</div>' . implode( "<br>", $errmsgBlock ) .'<div style="color:#aaa;font-size:.75em;font-style:italic;">This block will not be displayed on the front end of the website until the error is fixed.</div></div>';
				}else{
					return '<pre style="display:none;" class="appip-errors">APPIP ERROR: amazon-search['."\n" . implode( "\n", $errmsg ) ."\n". ']</pre>';
				}
			} else {
				$resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array(); 
				$resultarr3 = isset( $totalResult3 ) && !empty( $totalResult3 ) ? $totalResult3 : array(); 
				$errors_prod = array();
				if(!empty($er2Arr)){
					$errmsg = array();
					foreach($er2Arr as $k => $v){
						$code = isset($v['Code']) ? $v['Code']: 'code';
						$msg = isset($v['Message']) ? $v['Message'] : 'message';
						$errmsg[] = $code . "|" . $msg;
					}
					$errors_prod[] = '<pre style="display:none;" class="appip-errors">APPIP ERROR:amazon-search[Items]['."\n". implode("\n", $errmsg)."\n". ']</pre>';
					if(empty($resultarr)){
						return implode("\n", $errors_pro);
					}
				}
				$arr_position = 0;
				
				if((int) $item_count < 10)
					$resultarr = array_slice($resultarr, 0, $item_count);
				if( is_array( $resultarr ) ):
					$retarr = array();
					$newErr = '';
					/* New Button functionality */
					$button = array();
					if ( $atts[ 'button' ] != '' ) {
						$buttonstemp = explode( ',', $atts[ 'button' ] );
						if ( count( $buttonstemp ) === 1 && count($resultarr) > 1) {
							foreach ( $resultarr as $kba => $kbv ) {
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
					$region = $atts[ 'locale' ];
					foreach($resultarr as $kr => $result):
						$result = (array) $result;
						$Errors = array();
						$result3 = $awsv5->GetAPPIPReturnVals_V5( $result, $totalResult3[$arr_position], $Errors );
						$result = array_merge($result,$result3);
						$currasin = isset($result['ASIN']) ? $result['ASIN']:'';
						if(isset($result['NoData']) && $result['NoData'] == '1'):
							return '<!-- APPIP ERROR S2['."\n".str_replace('-->','->',$result['Error']).']-->';
						else:
							if ( ( int )$atts[ 'image_count' ] >= 1 && ( int )$atts[ 'image_count' ] <= 10 && is_array( $result[ 'AddlImagesArr' ] ) && !empty( $result[ 'AddlImagesArr' ] ) ) {
								$result[ 'AddlImages' ] = implode( '', array_slice( $result[ 'AddlImagesArr' ], 0, ( int )$atts[ 'image_count' ] ) );
							} elseif ( ( int )$atts[ 'image_count' ] == 0 ) {
								$result[ 'AddlImages' ] == '';
							}							
							$linkURL = ( $use_carturl ) ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
							$btnlinkURL = ( $button_carturl ) ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
							if( (bool) $apippopennewwindow )
								$nofollow = ' rel="nofollow noopener"';
							else
								$nofollow = ' rel="nofollow"';
							$nofollow = apply_filters( 'appip_template_add_nofollow', $nofollow, $result );
							if(is_array($field))
								$fielda = $field;
							else
								$fielda = explode(',',str_replace(' ','',$field));
							if($result[ 'Errors' ] != '' || (is_array($result[ 'Errors' ]) && !empty($result[ 'Errors' ]) ) ){
								$newErr = "<!-- HIDDEN APIP ERROR(S): ".$result['Errors']." -->\n";
							}
							foreach($fielda as $fieldarr){
								switch(strtolower($fieldarr)){
									case 'title_clean':
										$NewTitle = Amazon_Product_Shortcode::appip_do_charlen( maybe_convert_encoding( $result[ "Title" ] ), $atts[ 'title_charlen' ] );
										$retarr[$currasin][$fieldarr] = $NewTitle;
										break;
									case 'author_clean':
										$retarr[$currasin][$fieldarr] = $result["Author"];
										break;
									case 'desc_clean':
									case 'description_clean':
										if(is_array($result["ItemDesc"])){
											$desc 	= preg_replace('/^\s*\/\/<!\[CDATA\[([\s\S]*)\/\/\]\]>\s*\z/','$1', $result["ItemDesc"][0] );
											$retarr[$currasin][$fieldarr] = $desc['Content'];
										}
										break;
									case 'price_clean':
									case 'new-price_clean':
									case 'new price_clean':
										//if("Kindle Edition" == $result["Binding"]){
											//$retarr[$currasin][$fieldarr] = 'Check Amazon for Pricing [Digital Only - Kindle]';
										//}else{
											if( $result["LowestNewPrice"] == 'Too low to display' ){
												$newPrice = 'Check Amazon For Pricing';
											}else{
												$newPrice = $result["LowestNewPrice"];
											}
											if($result["TotalNew"]>0){
												$retarr[$currasin][$fieldarr] = maybe_convert_encoding($newPrice).' - '.$msg_instock;
											}else{
												$retarr[$currasin][$fieldarr] = maybe_convert_encoding($newPrice).' - '.$msg_instock;
											}
										//}
										break;
									case 'image_clean':
									case 'MediumImage_clean':
									case 'med-image_clean':
										if(isset($result['MediumImage']))
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['MediumImage']);
										else 
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['SmallImage']);
										break;
									case 'SmallImage_clean':
									case 'sm-image_clean':
										$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['SmallImage']);
										break;
									case 'LargeImage_clean':
									case 'lg-image_clean':
										$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['LargeImage']);
										break;
									case 'HiResImage_clean':
									case 'full-image_clean':
										if( isset($result['HiResImage']) ) 
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['HiResImage']);
										else
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['LargeImage']);
										break;
									case 'large-image-link_clean':
									case 'LargeImage-link_clean':
									case 'HiResImage-link_clean':
										if( isset($result['HiResImage']) )
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['HiResImage']);
										else 
											$retarr[$currasin][$fieldarr] =  checkSSLImages_url($result['LargeImage']);
										break;
									case 'features_clean':
										$retarr[$currasin][$fieldarr] = maybe_convert_encoding($result["Feature"]);
										break;
									case 'link_clean':
										$retarr[$currasin][$fieldarr] = $linkURL;
										break;
									case 'button_clean':
										if(isset($button_url))
											$retarr[$currasin][$fieldarr] = $button_url;
										else
											$buttonURL  = apply_filters('appip_amazon_button_url',plugins_url('/images/generic-buy-button.png',dirname(__FILE__)),'generic-buy-button.png',$region);
											$retarr[$currasin][$fieldarr] =$buttonURL;
										break;
									case 'customerreviews_clean':
										//$retarr[$currasin][$fieldarr] = $result['CustomerReviews'];
										break;
									case 'title':

										$NewTitle = Amazon_Product_Shortcode::appip_do_charlen( maybe_convert_encoding( $result[ "Title" ] ), $atts[ 'title_charlen' ] );
										if(!isset($labels['title-wrap']) && !isset($labels['title'])){
											$temptitle = '<div class="appip-title first-spot"><a href="'.$linkURL.'"'.$target.$nofollow.'>'. $NewTitle.'</a></div>';
										}elseif(!isset($labels['title-wrap']) && isset($labels['title'])){
											$temptitle= '<h2 class="appip-title second-spot"><a href="'.$linkURL.'"'.$target.$nofollow.'>'.$NewTitle.'</a></h2>';
										}elseif(isset($labels['title-wrap']) && isset($labels['title'])){
											$temptitle= "<{$labels['title-wrap']} class='appip-title third-spot'>".$NewTitle."</a></{$labels['title-wrap']}>";
										}elseif(isset($labels['title-wrap']) && !isset($labels['title'])){
											$temptitle = '<'.$labels['title-wrap'].' class="appip-title fourth-spot">'. $NewTitle.'</'.$labels['title-wrap'].'>';
										}else{
											$temptitle = '<div class="appip-title default-spot"><a href="'.$linkURL.'"'.$target.$nofollow.'>'. $NewTitle.'</a></div>';
										}
										$retarr[$currasin][$fieldarr] = $temptitle;
										break;
									case 'desc':
									case 'description':
										/* Not Available in V5.0 API */
										/*
										if(isset($labels['desc'])){
											$labels['desc'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['desc'].' </span>';
										}elseif(isset($labels['description'])){
											$labels['desc'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['description'].' </span>';
										}else{
											$labels['desc'] = '';
										}
										if(is_array($result["ItemDesc"])){
											$desc = $result["ItemDesc"][0];
											$retarr[$currasin][$fieldarr] = maybe_convert_encoding($labels['desc'].$desc['Content']);
										}
										*/
										break;
									case 'gallery':
									case 'imagesets':
										if(!isset($labels[$fieldarr])){
											$tempLabel = __("Additional Images:",'amazon-product-in-a-post-plugin');
										}else{
											$tempLabel = '<span class="appip-label label-'.$fieldarr.'">'.$labels[$fieldarr].' </span>';
										}
										if($result['AddlImages']!=''){
											$retarr[$currasin][$fieldarr] = '<div class="amazon-additional-images-wrapper"><span class="amazon-additional-images-text">'.$tempLabel.'</span><br/>'.$result['AddlImages'].'</div>';
										}	
										break;
									case 'list':
										$listLabel = '';
										$listPrice = '';
										if(isset($result["Binding"]) && "Kindle Edition" == $result["Binding"]){
											$listLabel = '';
											$listPrice = '';//'N/A';
										}elseif(isset($result["NewAmazonPricing"]["New"]["List"])){
											$listPrice = $result["NewAmazonPricing"]["New"]["List"];
										}
										$listLabel = $listLabel == '' && isset($labels['list']) ? $labels['list']: $listLabel;
										if($listPrice != ''){
											if($listLabel != '')
												$retarr[$currasin][$fieldarr] = '<span class="label">'.$listLabel.'</span> '.$listPrice;
											else
												$retarr[$currasin][$fieldarr] = $listPrice;
										}
										break;
									case 'price+list':
										$listLabel = '';
										$newLabel = '';
										$newPrice = '';
										//if(isset($result["Binding"]) && "Kindle Edition" == $result["Binding"]){
											//$newLabel = $result["Binding"].':';
											//$listLabel = '';
											//$listPrice	= '';//'N/A';
											//$newPrice 	= ' Check Amazon for Pricing <span class="instock">Digital Only</span>';
										//}else
									   if(isset($result["NewAmazonPricing"]["New"]["Price"])){
											$newPrice = $result["NewAmazonPricing"]["New"]["Price"];
											$listPrice = $result["NewAmazonPricing"]["New"]["List"];
										}
										$listLabel = $listLabel == '' && isset($labels['list']) ? $labels['list']: $listLabel;
										if($listPrice != ''){
											if($listLabel != '')
												$retarr[$currasin][$fieldarr] = '<span class="label">'.$listLabel.'</span> '.$listPrice;
											else
												$retarr[$currasin][$fieldarr] = $listPrice;
										}
										$newLabel = $newLabel == '' && isset($labels['price']) ? $labels['price']: $newLabel;

										if($newPrice != ''){
											if($newLabel != '')
												$retarr[$currasin][$fieldarr] = '<span class="label">'.$newLabel.'</span> '.$newPrice;
											else
												$retarr[$currasin][$fieldarr] = $newPrice;
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
										//if ( isset( $result[ "Binding" ] ) && "Kindle Edition" == $result[ "Binding" ] ) {
											//$newLabel = $result[ "Binding" ] . ': ';
											//$newPrice = __( 'Check Amazon for Pricing', 'amazon-product-in-a-post-plugin' ) . ' <span class="instock">' . __( 'Digital Only', 'amazon-product-in-a-post-plugin' ) . '</span>';
										//} else
									    if ( isset( $result[ "NewAmazonPricing" ][ "New" ][ "Price" ] ) ) {
											$newPrice = $result[ "NewAmazonPricing" ][ "New" ][ "Price" ];
										}
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
									case 'old new price':
										/*if("Kindle Edition" == $result["Binding"]){
											if(isset($labels['price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['price'].' </span>';
											}elseif(isset($labels['new-price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['new-price'].' </span>';
											}elseif(isset($labels['new price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['new price'].' </span>';
											}else{
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.'Kindle Edition:'.' </span>';
											}
											$retarr[$currasin][$fieldarr] = $labels['price-new'].' Check Amazon for Pricing <span class="instock">Digital Only</span>';
										}else{*/
											if(isset($labels['price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['price'].' </span>';
											}elseif(isset($labels['new-price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['new-price'].' </span>';
											}elseif(isset($labels['new price'])){
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels['new price'].' </span>';
											}else{
												$labels['price-new'] = '<span class="appip-label label-'.$fieldarr.'">'.'New From:'.' </span>';
											}
											$correctedPrice = isset($result["Offers_Offer_OfferListing_Price_FormattedPrice"]) ? $result["Offers_Offer_OfferListing_Price_FormattedPrice"] : $result["LowestNewPrice"];
											if($correctedPrice=='Too low to display'){
												$newPrice = 'Check Amazon For Pricing';
											}else{
												$newPrice = $correctedPrice;
											}
											if($result["TotalNew"]>0){
												$retarr[$currasin][$fieldarr] = $labels['price-new'].maybe_convert_encoding($newPrice).' <span class="instock">'.$msg_instock.'</span>';
											}else{
												$retarr[$currasin][$fieldarr] = $labels['price-new'].maybe_convert_encoding($newPrice).' <span class="outofstock">'.$msg_instock.'</span>';
											}
										//}
										break;
									case 'image':
									case 'med-image':
									case 'MediumImage':
										$noimage = plugins_url( 'images/noimage.jpg', dirname( __FILE__ ) );
										if( isset($result['MediumImage']) && $result['MediumImage'] != '' ){
											$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($result['MediumImage'],'amazon-image amazon-image-medium',$currasin).'</a>';
											$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
											$retarr[$currasin][$fieldarr] = $imgtemp;
										}else{
											$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($noimage,'amazon-image amazon-image-medium',$currasin).'</a>';
											$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
											$retarr[$currasin][$fieldarr] = $imgtemp;
										}
										break;
									case 'sm-image':
									case 'SmallImage':
										$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($result['SmallImage'],'amazon-image amazon-image-small',$currasin).'</a>';
										$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
										$retarr[$currasin][$fieldarr] = $imgtemp;
										break;
									case 'lg-image':
									case 'LargeImage':
										$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($result['LargeImage'],'amazon-image amazon-image-large',$currasin).'</a>';
										$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
										$retarr[$currasin][$fieldarr] = $imgtemp;
										break;
									case 'full-image':
									case 'HiResImage':
										if( isset($result['HiResImage']) ) {// if there is a hires image by chance, give that
											$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($result['HiResImage'],'amazon-image amazon-image-hires',$currasin).'</a>';
											$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
											$retarr[$currasin][$fieldarr] = $imgtemp;
										}else{
											$imgtemp = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.checkSSLImages_tag($result['LargeImage'],'amazon-image amazon-image-large',$currasin).'</a>';
											$imgtemp = strtolower($atts['template']) === 'grid' ? $imgtemp : '<div class="amazon-image-wrapper">'.$imgtemp.'</div>';
											$retarr[$currasin][$fieldarr] = $imgtemp;
										}
										break;
									case 'large-image-link':
									case 'HiResImage-link':
									case 'LargeImage-link':
										if(!isset($labels['large-image-link'])){
											$labels['large-image-link'] = $appip_text_lgimage;
										}else{
											$labels['large-image-link'] = $labels[$fieldarr].' ';
										}
										if(isset($result['LargeImage']) && $result['LargeImage'] != '' ){
											$retarr[$currasin][$fieldarr] = '<div class="amazon-image-link-wrapper"><a rel="appiplightbox-'.$result['ASIN'].'" href="#" data-appiplg="'. checkSSLImages_url($result['LargeImage']) .'"><span class="amazon-element-large-img-link">'.$labels['large-image-link'].'</span></a></div>';
										}
										break;
									case 'features':
										if(!isset($labels['features'])){
											$labels['features'] = '';
										}else{
											$labels['features'] = '<span class="appip-label label-'.$fieldarr.'">'.$labels[$fieldarr].' </span>';
										}
										$retarr[$currasin][$fieldarr] = $labels['features'].maybe_convert_encoding($result["Feature"]);
										break;
									case 'link':
										$retarr[$currasin][$fieldarr] = '<a href="'.$linkURL.'"'.$target.$nofollow.'>'.$linkURL.'</a>';
										break;
									case 'new-button':
										$button_class = ' class="btn btn-primary"';
										$button_txt = __('Read More','amazon-product-in-a-post-plugin');
										$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . $nofollow . $button_class . ' href="' . $linkURL . '">' . $button_txt . '</a>';
										break;
									case 'button':
										if(isset($button_url[$arr_position])){
											$retarr[$currasin][$fieldarr] = '<a '.$target.$nofollow.' href="'.$linkURL.'"><img src="'.$button_url[$arr_position].'" border="0" /></a>';
										}else{
											if(isset($button[$arr_position])){
												$bname 		= $button[$arr_position];
												$brounded 	= strpos($bname,'rounded') !== false ? true : false;
												$bclass 	= isset($new_button_arr[$bname]['color']) ? 'amazon__btn'.$new_button_arr[$bname]['color'].' amazon__price--button--style'.( $brounded ? ' button-rounded' : '') : 'amazon__btn amazon__price--button--style';
												$btext 		= isset($new_button_arr[$bname]['text']) ? esc_attr($new_button_arr[$bname]['text']) : _x('Buy Now', 'button text', 'amazon-product-in-a-post-plugin' );
												$retarr[$currasin][$fieldarr] = '<a '.$target.' href="'.$linkURL.'"'.$nofollow.' class="'.$bclass.'">'.$btext.'</a>';
											}else{
												$buttonURL  = apply_filters('appip_amazon_button_url',plugins_url('/images/generic-buy-button.png',dirname(__FILE__)),'generic-buy-button.png',$region);
												$retarr[$currasin][$fieldarr] = '<a '.$target . $nofollow.' href="'.$linkURL.'"><img class="amazon-price-button-img" src="'.$buttonURL.'" alt="'.apply_filters('appip_amazon_button_alt_text', __('buy now','amazon-product-in-a-post-plugin'),$currasin).'" border="0" /></a>';
											}
										}
										break;
									case 'customerreviews':
										/* Not Available in V5.0 API */
										//$retarr[$currasin][$fieldarr] = '<iframe src="'.$result['CustomerReviews'].'" class="amazon-customer-reviews" width="100%" seamless="seamless"></iframe>';
										break;
									default:
										if(isset($result[$fieldarr]) && $result[$fieldarr]!=''){
											if(!isset($labels[$fieldarr])){
												$labels[$fieldarr] = '';
											}else{
												$labels[$fieldarr] = '<span class="appip-label label-'.str_replace(' ','-',$fieldarr).'">'.$labels[$fieldarr].' </span>';
											}
											$retarr[$currasin][$fieldarr] = $labels[$fieldarr].$result[$fieldarr];
										}else{
											$retarr[$currasin][$fieldarr] = '';
										}
										break;
								}
							}
						endif;
						/* NEW Filter Version - only applies filter to current ASIN while not breaking the filter.	*/
						$temparr 	= array('temp' => $retarr[ $currasin ] );
						$temparr	= apply_filters( 'amazon_product_in_a_post_plugin_search_filter', $temparr );
						$retarr[ $currasin ] = $temparr['temp'];

						$template_is_grid = $atts['template'] == 'grid' ? true : false;
						$template_wrap_class = $template_is_grid ? ' amazon-grid-element amz-grid-' . $atts[ 'columns' ]: '';
						$template_element_class	= $template_is_grid ? 'grid' : 'element';
						$template_block_class = $template_is_grid ? ' amazon-grid-wrapper': '';
						$wrap = str_replace( array( '<', '>' ), array( '', '' ), $container );
						if($wrap != '')
							$thenewret[] = '<' . $wrap . ' class="' . $atts['container_class'] . $template_wrap_class . '">';
						if(is_array($retarr[$currasin]) && !empty($retarr[$currasin])){
							foreach( $retarr[$currasin] as $key => $val ){
								if($key != '' ){
									if( preg_match( '/\_clean$/', $key )){
										$thenewret[] =  $val;
									}else{
										$thenewret[] =  '<div class="amazon-' . $template_element_class . '-' . $key . '">' . $val . '</div>';
									}
								}
							}
						}
						if($wrap != ''){
							$thenewret[] = "</{$wrap}>";
						}
						$arr_position++;
					endforeach;
				if ( is_array($errors_prod) && !empty($errors_prod) )
					return '<div class="appip-block-wrapper error-block">' . implode( "\n", $errors_prod ) . '</div>';
				if( is_array($thenewret)){
						if ( $atts[ 'className' ] != '' )
							$className = ' ' . implode( ' ', explode( ',', str_replace( array( ', ', ' ' ), array( ',', ',' ), esc_attr($atts[ 'className' ] ) ) ) );
						if( $atts['template'] == 'grid')
							$template_block_class = ' amazon-grid-wrapper';
						return '<div class="appip-block-wrapper' . $template_block_class . $className . '">' . implode( "\n", $thenewret ) . '</div>';
					}
					return false;
				endif;
			}
		}else{
			return false;
		}
	}
}
new Amazon_Product_Shortcode_Search('amazon-product-search');

function appip_search_php_block_init() {
	if ( function_exists( 'register_block_type' ) ) {
		global $apippopennewwindow,$amazon_styles_enqueued;
		add_filter( 'appip-register-templates', function ( $appip_template_array ) {
			$appip_template_array[] = array( 'location' => 'search', 'name' => __( 'Grid Layout', 'amazon-product-in-a-post-plugin' ), 'ID' => 'grid' );
			//$appip_template_array[] = array( 'location' => 'search', 'name' => __( 'Amazon Layout', 'amazon-product-in-a-post-plugin' ), 'ID' => 'amazon-layout' );
			return $appip_template_array;
		}, 10, 1 );
		$pluginStyles = array( 'amazon-theme-styles' );
		$pluginScripts = array( 'amazon-search-block' );
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
		if ( $usemine && !$amazon_styles_enqueued ) {
			$data = wp_kses( get_option( 'apipp_product_styles', '' ), array( "\'", '\"' ) );
			if ( $data != '' )
				wp_add_inline_style( 'amazon-frontend-styles', $data );
			$amazon_styles_enqueued = true;
		}
		wp_register_script('amazon-search-block', plugins_url( '/blocks/php-block-search.js', __FILE__ ), array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), filemtime( plugin_dir_path( __FILE__ ) . 'blocks/php-block-search.js' ));

		register_block_type( 'amazon-pip/amazon-search', array(
			'attributes' => array(
				'fields' => array(
					'type' => 'string',
					'default' => 'image,title,button',
				),
				'keywords' => array(
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
				'columns' => array(
					'type' => 'int',
					'default' => 3,
				),
				'item_page' => array(
					'type' => 'number',
					'default' => 1,
					'min' => 1,
					'max' => 10,
				),
				'item_count' => array(
					'type' => 'number',
					'default' => 10,
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
				),
				'search_title' => array(
					'type' => 'bool',
					'default'=> false,
				),
				'button' => array(
					'type' => 'string',
					'default' => 'read-more-blue-rounded',
				),
				'search_index' => array(
					'type' => 'string',
					'default' => 'All',
				),
				'availability' => array(
					'type' => 'string',
					'default' => 'Available',
				),
				'sort' => array(
					'type' => 'string',
					'default' => 'Relevance',
				),
				'labels' => array(
					'type' => 'string',
					'placeholder' => __( 'Labels (optional)', 'amazon-product-in-a-post-plugin' ),
					'default' => '',
				),
				'button_url' => array(
					'type' => 'string',
				),
				'browse_node' => array(
					'type' => 'string',
				),
				'condition' => array(
					'type' => 'string',
					'default' => "New",
				),
				'container' => array(
					'type' => 'string',
					'default' => apply_filters( 'amazon-search-container', 'div' ),
				),
				'container_class' => array(
					'type' => 'string',
					'default' => apply_filters( 'amazon-search-container-class', 'amazon-search-wrapper' ),
				),
			),
			'editor_style' => $pluginStyles,
			'editor_script' => $pluginScripts,
			'render_callback' => array( 'Amazon_Product_Shortcode_Search', 'do_shortcode' ),
		) );
	}
}
add_action( 'init', 'appip_search_php_block_init' );