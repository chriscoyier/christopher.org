<?php
// if someone has the shortcode plugin add-on installed, let it be - otherwise:
if ( !class_exists( 'amazonAPPIP_ShortcodeGrid_plugin' ) ) {
	class Amazon_Product_Shortcode_Grid extends Amazon_Product_Shortcode {

		static function _setup() {
			//add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue' ), 100 );
			add_filter( 'amazon-grid-fields',                             array( 'Amazon_Product_Shortcode_Grid', 'add_fields' ) );
			add_filter( 'amazon-grid-columns',                            array( 'Amazon_Product_Shortcode_Grid', 'grid_columns' ) );
			add_filter( 'amazon_product_shortcode_help_content',          array( 'Amazon_Product_Shortcode_Grid', 'do_added_shortcode_help_content' ), 100, 2 );
			add_filter( 'amazon_product_shortcode_help_tabs',             array( 'Amazon_Product_Shortcode_Grid', 'do_added_shortcode_help_tab' ), 100, 2 );
			add_filter( 'amazon_product_in_a_post_plugin_shortcode_list', array( 'Amazon_Product_Shortcode_Grid','shortcode_list') );
		}

		public static function shortcode_list($text = array()){
			$text[] = '<li><a href="?page=apipp_plugin-shortcode&tab=amazon-product-grid" class="amazon-product-grid"><strong>amazon-grid</strong></a><br/>' . __( 'A Shortcode for displaying Amazon Prodcts in a Grid.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			return $text;
		}

		static function do_shortcode( $atts, $content = '' ) {
			global $post;
			global $apippopennewwindow;
			global $apippnewwindowhtml;
			$atts[ 'single_only' ] = isset( $atts[ 'single_only' ] ) && ($atts[ 'single_only' ] == 'true'  || (int) $atts[ 'single_only' ] == 1) ? 1 : 0;
			$atts[ 'is_block' ] = isset( $atts[ 'is_block' ] ) && ($atts[ 'is_block' ] == 'true' || (int) $atts[ 'is_block' ] == 1) ? 1 : 0;
			$atts[ 'title_charlen' ] = isset( $atts[ 'title_charlen' ] ) && ((int) $atts[ 'title_charlen' ] >= 0 && (int) $atts[ 'title_charlen' ] <= 150)  ? (int) $atts[ 'title_charlen' ] : 0;
			$atts[ 'newWindow' ] = isset( $atts[ 'newWindow' ] ) && ($atts[ 'newWindow' ] == 'true' || (int) $atts[ 'newWindow' ] == 1) ? 1 : 0;
			$atts[ 'image_count' ] = isset( $atts[ 'image_count' ] ) && (( int )$atts[ 'image_count' ] <= 10 || ( int )$atts[ 'image_count' ] >= -1) ? ( int )$atts[ 'image_count' ] : -1;
			$atts[ 'target' ] = isset( $atts[ 'target' ] ) && esc_attr( $atts[ 'target' ] ) != '' ? esc_attr( $atts[ 'target' ] ) : '_blank';

			$lgimg_txt = apply_filters( 'amazon-grid-seelgimg-text', __( 'see larger image', 'amazon-product-in-a-post-plugin' ) );
			$defaults = array(
				'asin' => '',
				'locale' => APIAP_LOCALE,
				'partner_id' => APIAP_ASSOC_ID,
				'private_key' => APIAP_SECRET_KEY,
				'public_key' => APIAP_PUB_KEY,
				'fields' => apply_filters( 'amazon-grid-fields', 'image,title,price,button', $post ),
				'target' => apply_filters( 'amazon-grid-target', '_blank', $post ),
				'button_url' => apply_filters( 'amazon-grid-button-img-url', '', $post ),
				'use_carturl' => apply_filters( 'amazon-grid-carturl', '0', $post ),
				'columns' => ( int )apply_filters( 'amazon-grid-columns', 3, $post ),
				'labels' => '',
				'button' => '',
				'container' => apply_filters('amazon-grid-container','div'),
				'container_class' => apply_filters('amazon-grid-container-class','amazon-grid-wrapper'),
				'template' => 'default',
				'newWindow' => 0,
				'replace_title' => '',
				'title_charlen' => 0,
				'image_count' => -1, //The number of Images in the Gallery. -1 = all, or 0-10
				'single_only' => 0, //show on Single Only
				'className' => '', //Gutenberg Additional className attribute.
				'is_block' => 0, //Special attribute to tell if this is a Block element or a shortcode.
				'title_charlen' => 0, // if greater than 0 will concat text fileds
			);

			if(isset($atts[ 'template' ]) && $atts[ 'template' ] != '')
				$atts[ 'container_class' ] = $atts[ 'container_class' ] .' amazon-template--grid-'. $atts[ 'template' ];
			$single_only = isset($atts[ 'single_only' ]) && (int) $atts[ 'single_only' ] == 1 || (bool) get_option('apipp_show_single_only', false ) === true ? 1 : 0 ;
			if( appip_check_blockEditor_is_active() ){
				if( defined('REST_REQUEST') && (bool) REST_REQUEST ) {
					if($atts[ 'asin' ] == '' && (bool)$atts[ 'is_block' ])
						return '<div style="text-align:center;background: #f5f5f5;padding: 10px 5px;border: 1px solid #f48db0;"><strong>'.__('Amazon Grid Block','amazon-product-in-a-post-plugin').'</strong><br>'.__('Please add at least one ASIN.','amazon-product-in-a-post-plugin').'</div>';
				}else{
					if( (bool) $single_only === true && !is_singular() )
						return '';
				}
			}elseif( !is_admin() && (bool) $single_only && !is_singular()) {
				return '';
			}
			$origatts = $atts;
			$atts = shortcode_atts( $defaults, $atts );
			// fix spaces, returns, double spaces and new lines in ASINs
			if(isset($atts[ 'asin' ]) && $atts[ 'asin' ] != ''){
				$atts[ 'asin' ] = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$atts[ 'asin' ]);
			}
			$replace_title = array();
			if(strpos($atts[ 'replace_title' ],"::")!== false){
				$replace_title = explode("::",$atts[ 'replace_title' ]);
			}else{
				$replace_title[] = $atts[ 'replace_title' ];
			}
			$title_charlen = $atts[ 'title_charlen' ];

			$asin = $atts[ 'asin' ];
			if ( strpos( $atts[ 'asin' ], ',' ) !== false )
				$asin = explode( ',', str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$atts[ 'asin' ] ) );
			//$asin can be array, comma separated string or single ASIN
			$use_carturl =  isset( $atts[ 'use_carturl' ]) && $atts[ 'use_carturl' ] == '1' ? '1' : '0';
			$button_carturl = isset( $atts['button_use_carturl']) && $atts[ 'button_use_carturl' ] == '1'  ? '1'  : $use_carturl ;
			$wrap = str_replace(array('<','>'), array('',''),$atts['container']);
			$prodLinkField = apply_filters( 'amazon-grid-link', ((bool)$atts[ 'use_carturl' ] || (int)$atts[ 'use_carturl' ] == 1 ? 'CartURL' : 'DetailPageURL'), $post ); //CartURL
			$target = $atts['target'];
			if($atts['target'] != '' && (bool) $atts['newWindow'] === false )
				$target = '';
			$target = $target != '' ? ' target="' . $target . '" ': '';
			$target = $target === '' && (bool) $apippopennewwindow ?  $apippnewwindowhtml : $target;
			$new_button_arr = amazon_product_get_new_button_array($atts['locale']);
			$labels = array();
			if($atts[ 'labels' ] != ''){
				$labelstemp = explode(',',$atts[ 'labels' ]);
				foreach( $labelstemp as $k => $lab ){
					$keytemp = explode( '::', $lab );
					if( isset( $keytemp[0] ) && isset( $keytemp[1] ) ){
						// this takes care of alias fields.
						$lbltemp = '';
						switch(strtolower($keytemp[0])){
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
								$lbltemp = $keytemp[0];
								break;
						}
						$labels[$lbltemp][] = esc_attr(apply_filters('appip_label_text_'.str_replace(' ','-',strtolower($keytemp[1])), $keytemp[1] /*value*/, $lbltemp /*field*/, 'amazon-element' ));
					}
				}
			}
			$noimage =  plugins_url( 'images/noimage.jpg',dirname(__FILE__));
			if ( $asin != '' ) {
				$aws_id = $atts[ 'partner_id' ];
				$ASIN = ( is_array( $asin ) && !empty( $asin ) ) ? implode( ',', $asin ) : $asin;
				$asinR = explode( ",", trim( str_replace( array(' ','  ',' '), '', $ASIN ) ) );
				/* New Button functionality */
				$button = array();
				if($atts[ 'button' ] != ''){
					$buttonstemp = explode(',', $atts[ 'button' ] );
					if( count($buttonstemp) === 1 && count($asinR) > 1){
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
				}
				/* END New Button functionality */

				$buyamzonbutton = apply_filters( 'appip_amazon_button_url', $atts[ 'button_url' ], '', $atts[ 'locale' ] );

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
				$pxmlNew = amazon_plugin_aws_signed_request( $atts[ 'locale' ], array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $asinR, "AssociateTag" => $atts[ 'partner_id' ], "RequestBy" => 'amazon-grid' ), $atts[ 'public_key' ], $atts[ 'private_key' ], ($skipCache ? true : false) );
				$totalResult2 = array();
				$totalResult3 = array();
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
						return '<div class="appip-block-wrapper appip-block-wrapper-error" style="border:1px solid #f48db0;padding:15px;text-align:center;background:#f5f5f5;"><div style="text-align:center;color:#f00;font-weight:bold;padding:0 0 10px;">Amazon Grid Block Errors</div>' . implode( "<br>", $errmsgBlock ) .'<div style="color:#aaa;font-size:.75em;font-style:italic;">This block will not be displayed on the front end of the website until the error is fixed.</div></div>';
					}else{
						return '<pre style="display:none;" class="appip-errors">APPIP ERROR: amazon-grid['."\n" . implode( "\n", $errmsg ) ."\n". ']</pre>';
					}
				} else {
					$resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array();
					$resultarr3 = isset( $totalResult3 ) && !empty( $totalResult3 ) ? $totalResult3 : array();
					$errors_prod = array();
					$arr_position = 0;

				if ( is_array( $resultarr ) ):
					$retarr = array();
					$newErr = '';
					$usSSL = amazon_check_SSL_on();
					if($itemErrors && !empty($errmsgBlock)){
						foreach($errmsgBlock as $ekey => $eval){
							//$thenewret[] = '<!--'.$eval.'-->';
						}
					}
					$region = $atts[ 'locale' ];
					$thenewret = array();
						foreach ( $resultarr as $key => $result ):
							$result = (array) $result;
							$Errors = array();
							$result3 = $awsv5->GetAPPIPReturnVals_V5( $result, $totalResult3[$arr_position], $Errors );
							$result = array_merge($result,$result3);
							$currasin = $result[ 'ASIN' ];

							if ( isset( $result[ 'NoData' ] ) && $result[ 'NoData' ] == '1' ):
								$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:none;" class="appip-errors">APPIP ERROR:nodata_grid[' . print_r($result[ 'Errors' ], true ) . '</'.$wrap.'>';
							elseif ( empty( $result[ 'ASIN' ] ) || $result[ 'ASIN' ] == 'Array' ):
								$retarr[ $currasin ]['Errors'] = '<'.$wrap.' style="display:none;" class="appip-errors">APPIP ERROR:nodata_grid_asin[ (' . $key . ')]</'.$wrap.'>';
							else :
								if ( ( int )$atts[ 'image_count' ] >= 1 && ( int )$atts[ 'image_count' ] <= 10 && is_array( $result[ 'AddlImagesArr' ] ) && !empty( $result[ 'AddlImagesArr' ] ) ) {
									$result[ 'AddlImages' ] = implode( '', array_slice( $result[ 'AddlImagesArr' ], 0, ( int )$atts[ 'image_count' ] ) );
								} elseif ( ( int )$atts[ 'image_count' ] == 0 ) {
									unset( $result[ 'AddlImages' ] );
								}
								$img2 = '';
								$linkURL = $use_carturl == '1'  ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
								$btnlinkURL =  $button_carturl == '1' ? str_replace( array( '##REGION##', '##AFFID##', '##SUBSCRIBEID##' ), array( $atts[ 'locale' ], $atts[ 'partner_id' ], $atts[ 'public_key' ] ), $result[ 'CartURL' ] ) : $result[ 'URL' ];
								if ( $result[ 'Errors' ] != '' || (is_array($result[ 'Errors' ]) && !empty($result[ 'Errors' ]) )){
									$newErr = '<'.$wrap.' style="display:none;" class="appip-errors">HIDDEN APIP ERROR(S): ' . $result[ 'Errors' ] . '</'.$wrap.'>';
								}
								$fielda = is_array( $atts[ 'fields' ] ) ? $atts[ 'fields' ] : explode( ',', str_replace( ' ', '', $atts[ 'fields' ] ) );
								if( (bool) $apippopennewwindow )
									$nofollow = ' rel="nofollow noopener"';
								else
									$nofollow = ' rel="nofollow"';
								$nofollow = apply_filters( 'appip_template_add_nofollow', $nofollow, $result );

								// Product title
                                if(isset($replace_title[$arr_position]) && $replace_title[$arr_position]!=''){
                                    $NewTitle = $replace_title[$arr_position];
                                }else{
                                    $NewTitle = Amazon_Product_Shortcode::appip_do_charlen(maybe_convert_encoding($result["Title"]),$title_charlen);
                                }

                                // Product image "Alt" tag
                                $appip_alt_text_main_img = apply_filters('appip_alt_text_main_img',$NewTitle,$currasin);

								foreach ( $fielda as $fieldarr ) {
									switch ( strtolower( $fieldarr ) ) {
										case 'title':
											//$NewTitle = Amazon_Product_Shortcode::appip_do_charlen(maybe_convert_encoding($result["Title"]), $atts['title_charlen']);
											$titleWrap = isset($labels['title-wrap'][$arr_position]) && $labels['title-wrap'][$arr_position]!= '' ? $labels['title-wrap'][$arr_position] : 'h3';
											$titleLabel = isset($labels['title'][$arr_position]) && $labels['title'][$arr_position]!= '' ? $labels['title'][$arr_position].': ' : '';
											$retarr[ $currasin ][ $fieldarr ] = '<' . $titleWrap . ' class="amazon-grid-title-' . $titleWrap . '"><a href="' . $linkURL.'"' . $target . $nofollow . '>' . $titleLabel . $NewTitle . '</a></' . $titleWrap . '>';
											break;
										case 'med-image':
										case 'MediumImage':
											$img1 = isset( $result[ "MediumImage" ] ) && $result[ "MediumImage" ] != '' ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $result[ "MediumImage" ] . '" alt="'.$appip_alt_text_main_img.'" ></a>': '';
											$img2 = $img1 == '' && isset( $result[ "LargeImage" ] ) ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $result[ "LargeImage" ] . '" alt="'.$appip_alt_text_main_img.'" ></a>': $img1;
											$img2 = $img2 == ''  ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $noimage . '" alt="'.$appip_alt_text_main_img.'" ></a>': $img1;
											$retarr[$currasin][$fieldarr] = $img2;
											break;
										case 'image':
										case 'lg-image':
										case 'LargeImage':
										case 'HiResImage':
											$img1 = isset( $result[ "LargeImage" ] ) && $result[ "LargeImage" ] != '' ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $result[ "LargeImage" ] . '" alt="'.$appip_alt_text_main_img.'" ></a>': '';
											$img2 = $img1 == '' ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $noimage . '" alt="'.$appip_alt_text_main_img.'" ></a>': $img1;
											$retarr[ $currasin ][ $fieldarr ] = $img2;
											break;
										case 'sm-image':
										case 'SmallImage':
											$img1 = isset( $result[ "SmallImage" ] ) && $result[ "SmallImage" ] != '' ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $result[ "SmallImage" ] . '" alt="'.$appip_alt_text_main_img.'" ></a>': '';
											$img2 = $img1 == '' && isset( $result[ "SmallImage" ] ) ? '<a href="' . $linkURL . '" ' . $target. $nofollow . '><img src="' . $result[ "SmallImage" ] . '" alt="'.$appip_alt_text_main_img.'" ></a>': $img1;
											$img2 = $img2 == ''  ? '<a href="' . $linkURL . '" ' . $target . $nofollow . '><img src="' . $noimage . '" alt="'.$appip_alt_text_main_img.'" ></a>': $img1;
											$retarr[ $currasin ][ $fieldarr ] = $img2;
											break;
										case 'author':
											$author = isset( $result[ "ItemAttributes_Author" ] ) && !empty( $result[ "ItemAttributes_Author" ] ) ? : '';
											$authorLabel = isset($labels['author'][$arr_position]) && $labels['author'][$arr_position]!= '' ? '<span class="amazon-grid-label">'.$labels['author'][$arr_position].': </span>' : '';
											if( $author !== '' )
												$retarr[ $currasin ][ $fieldarr ] =  $authorLabel . $author;
											break;
										case 'new-price':
										case 'new_price':
										case 'price':
											$newPrice = isset($result["NewAmazonPricing"]['New']['Price'] ) ? $result[ "NewAmazonPricing" ]['New']['Price'] : '' ;
											//if ( isset( $result["Binding"] ) && "Kindle Edition" === $result["Binding"] ) {
												//$newPrice = $result["Offers_Offer_OfferListing_Price_FormattedPrice"];
											//} elseif ( isset( $result['NewAmazonPricing']['New']['Price'] ) ) {
												//$newPrice = $result["NewAmazonPricing"]["New"]["Price"];
											//}
											$subscription = $result['SubscriptionLength'] != '' ? $result['SubscriptionLength'] : '' ;
											if( $subscription !== '' ){
												if($result['SubscriptionLengthUnits'] === 'days' && $subscription === '36599999' )
													$subscription = '365';
												if($subscription === '1')
													$result['SubscriptionLengthUnits'] = str_replace(array('days','months','years','weeks'),array('day','month','year','week'),$result['SubscriptionLengthUnits']);
												$retarr[$currasin][$fieldarr] = '<span class="amazon-grid-label label">'.__('Subscription: ','amazon-product-in-a-post-plugin').'</span> ' . $newPrice . ' for '. $subscription . ' ' . $result['SubscriptionLengthUnits'];
											}elseif($newPrice !== ''){
												$retarr[$currasin][$fieldarr] = '<span class="amazon-grid-label label">'.__('New:','amazon-product-in-a-post-plugin').'</span> ' . $newPrice;
											}else{
												$retarr[$currasin][$fieldarr] = __('Check Amazon for Pricing','amazon-product-in-a-post-plugin');
											}
											break;
										case 'used':
											$usedPrice = isset( $result[ "OfferSummary_LowestUsedPrice_FormattedPrice" ] ) ? $result[ "OfferSummary_LowestUsedPrice_FormattedPrice" ] : '';
											if ( isset( $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] ) ) {
												$usedPrice = $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ];
											}
											if ( $usedPrice != '' ){
												$retarr[ $currasin ][ $fieldarr ] = '<span class="amazon-grid-label label">'.__('Used:','amazon-product-in-a-post-plugin').'</span> ' . $newPrice;
											}
											break;
										case 'new-button':
											$button_class = ' class="btn btn-primary"';
											$button_txt = __('Read More','amazon-product-in-a-post-plugin');
											$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . $nofollow . $button_class . ' href="' . $linkURL . '">' . $button_txt . '</a>';
											break;
										case 'button':
											if ( isset( $button_url[ $arr_position ] ) ) {
												$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . $nofollow . ' href="' . $linkURL . '"><img src="' . $button_url[ $arr_position ] . '" alt="'.(apply_filters('appip_amazon_button_alt_text',__('Buy Now','amazon-product-in-a-post-plugin'),$currasin)).'" border="0" /></a>';
											} else {
												if(isset($button[$arr_position])){
													$bname 		= $button[$arr_position];
													$brounded 	= strpos($bname,'rounded') !== false ? true : false;
													$bclass 	= isset($new_button_arr[$bname]['color']) ? 'amazon__btn'.$new_button_arr[$bname]['color'].' amazon__price--button--style'.( $brounded ? ' button-rounded' : '') : 'amazon__btn amazon__price--button--style';
													$btext 		= isset($new_button_arr[$bname]['text']) ? esc_attr($new_button_arr[$bname]['text']) : _x('Buy Now', 'button text', 'amazon-product-in-a-post-plugin' );
													$retarr[$currasin][$fieldarr] = '<a ' . $target . $nofollow . ' href="' . $linkURL . '" class="' . $bclass . '">' . $btext . '</a>';
												}else{
													$retarr[ $currasin ][ $fieldarr ] = '<a ' . $target . $nofollow . ' href="' . $linkURL . '"><img src="' . $buyamzonbutton . '" border="0" alt="'.(apply_filters('appip_amazon_button_alt_text',__('Buy Now','amazon-product-in-a-post-plugin'),$currasin)).'" /></a>';
												}
											}
											break;
										case 'large-image-link':
											$appip_text_lgimage = apply_filters('appip_text_lgimage', __( "See larger image", 'amazon-product-in-a-post-plugin' ));
											if(isset($result['LargeImage']) && $result['LargeImage'] !== '' ){
												$retarr[$currasin][$fieldarr] = '<'.$wrap.' class="amazon-image-link-wrapper"><a rel="appiplightbox-'.$result['ASIN'].'" href="#" data-appiplg="'. checkSSLImages_url($result['LargeImage']) .'"><span class="amazon-element-large-img-link">'.$appip_text_lgimage.'</span></a></'.$wrap.'>';
											}
											break;
										case 'gallery':
											if ( $result[ 'AddlImages' ] != '' )
											$retarr[$currasin][$fieldarr] = '	<div class="amazon-additional-images-wrapper"><span class="amazon-additional-images-text">'.__('Additional Images','amazon-product-in-a-post-plugin').'</span>' . $result[ 'AddlImages' ] . '</div>';
											break;
										default:
											if ( preg_match( '/\_clean$/', $fieldarr ) ) {
												$tempfieldarr = str_replace( '_clean', '', $fieldarr );
												$retarr[ $currasin ][ $fieldarr ] = isset( $result[ $tempfieldarr ] ) && $result[ $tempfieldarr ] != '' ? $result[ $tempfieldarr ] : '';
											} else {
												if ( isset( $result[ $fieldarr ] ) && $result[ $fieldarr ] != '' && $result[ $fieldarr ] != '0' ) {
													$retarr[ $currasin ][ $fieldarr ] = $result[ $fieldarr ];
												} else {
													$retarr[ $currasin ][ $fieldarr ] = '';
												}
											}
											break;
									}
								}
							endif;
							$temparr 	= array('temp' => $retarr[ $currasin ] );
							$temparr	= apply_filters( 'amazon-grid-elements-filter', $temparr );
							$retarr[ $currasin ] = $temparr['temp'];
							if ( is_array( $retarr[ $currasin ] ) && !empty( $retarr[ $currasin ] ) ) {
								$thenewret[] = '<'.$wrap.' class="amazon-grid-element amz-grid-' . $atts[ 'columns' ] . '">';
								foreach ( $retarr[ $currasin ] as $key => $val ) {
									if ( $key != '' ) {
										if ( preg_match( '/\_clean$/', $key ) )
											$thenewret[] = $val;
										else
											$thenewret[] = '<'.$wrap.' class="amazon-grid-' . $key . '">' . $val . '</'.$wrap.'>';
									}
								}
								$thenewret[] = '</'.$wrap.'>';

							}
							$arr_position++;
						endforeach;

						if ( $newErr != '' )
							echo $newErr;
						if ( is_array( $thenewret ) ) {
							$final = '<div class="appip-block-wrapper"><'.$wrap.' class="'.$atts[ 'container_class' ].'">' . implode( "\n", $thenewret ) . '</'.$wrap.'></div>';
							return $final;
						}
						return false;
					endif;
				}
			} else {
				return false;
			}
		}

		public static function do_added_shortcode_help_content( $content = array(), $current_tab = '' ) {
			$pageTxtArr = array();
			$pageTxtArr[] = '		<div id="amazon-product-grid-content" class="nav-tab-content' . ( $current_tab == 'amazon-product-grid' ? ' active' : '' ) . '" style="' . ( $current_tab == 'amazon-product-grid' ? 'display:block;' : 'display:none;' ) . '">';
			$pageTxtArr[] = '			<h2>[amazon-grid] ' . __( 'Shortcode', 'amazon-product-in-a-post-plugin' ) . '</h2>';
			$pageTxtArr[] = '			<p>' . __( 'Shortcode implementation for a grid style layout &mdash; for when you may only want rows of products in set columns.', 'amazon-product-in-a-post-plugin' ) . '</p>';
			$pageTxtArr[] = '			<p>' . __( 'Available Shortcode and Gutenberg Block Parameters:', 'amazon-product-in-a-post-plugin' ) . '</p>';
			$pageTxtArr[] = '			<ul>';
			$pageTxtArr[] = '				<li><code>asin</code> &mdash; ' . __( '<span style="color:#ff0000;">Required</span>. The Amazon ASIN or ASINs (add multiple by separating with a comma).', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>target</code> &mdash; ' . __( '(optional) Default is &quot;_blank&quot;. Applies to all ASINs in list.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>fields</code> &mdash; ' . __( '(optional) Fields you want to return. Any valid return field from Amazon API (see API for list) - default fields: image, title, author, price, button. Applies to all ASINs in "asin" field.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>labels</code> &mdash; ' . __( '(optional) Labels that correspond to the fields, if you want custom labels. See amazon-elements shortcode tab for more info on labels as they will function the same.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>columns</code> &mdash; ' . __( '(optional) Number of columns in your grid. Default is 3.', 'amazon-product-in-a-post-plugin' ) . '</li>';;
			$pageTxtArr[] = '				<li><code>button_url</code> &mdash; ' . __( '(optional) URL for a different button image if you desired. See amazon-elements shortcode tab for more info on button URLs.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>use_carturl</code> &mdash; ' . __( '(optional) Set to &quot;1&quot; for "Add to Cart URL" instead of product page for Amazon links. Default is &quot;0&quot;.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>newWindow</code> &mdash; ' . __( '(optional) Set to &quot;1&quot; to open Amazon links in a New Window. Defult is &quot;0&quot;.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>image_count</code> &mdash; ' . __( '(optional) If using the `gallery` parameter, you can set the number of additional images in the gallery (`1` to `10`). Default is `-1` (all). `0` will show no additional images.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>single_only</code> &mdash; ' . __( '(optional) Show product only on a single page. Set to `1` to show onl when the page/post is sungular (product will not show on blogroll or archive pages). Default is `0`.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>button</code> &mdash; ' . __( '(optional) If you want to use an HTML button, pass one of the button IDs. Default is none (uses the default button). For this to work, you must also use `button` in the `fields` paramater. See Button Settings page for a list of available button IDs.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>template</code> &mdash; ' . __( '(optional - future) Set to template ID name for registered templates (coming soon). Default is `dafault`.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>container</code> &mdash; ' . __( '(optional) Set to the container HTML element to use to warp the output of the product. Default is `div`. Do not use &ltl; or &gt;.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>container_class</code> &mdash; ' . __( '(optional) Main class name for the wrapper element. Default is `amazon-grid-wrapper`. Please note that if you change this, you will have to create your own set of CSS styles for the grid products layout.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>className</code> &mdash; ' . __( '(optional) Used for adding an additional class or classes to the wrapper element. Also used by the Gutenberg block to add additional CSS class to the wrapper element. Default is none.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>title_charlen</code> &mdash; ' . __( '(optional) Trim the product title(s) by setting a value from `1` to `150`. Default is `0` (show full title). Also note that a number less than `0` or greater than `150` will also show the full title.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>locale</code> &mdash; ' . __( '(optional) The amazon locale, i.e., co.uk, es. This is handy of you need a product from a different locale than your default one. Applies to all ASINs in list.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>partner_id</code> &mdash; ' . __( '(optional) Your amazon partner id. default is the one in the options. You can set a different one here if you have a different one for another locale or just want to split them up between multiple ids. Applies to all ASINs in list.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>private_key</code> &mdash; ' . __( '(optional) Amazon private key. Default is one set in options. You can set a different one if needed for another locale. Applies to all ASINs in list.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li><code>public_key</code> &mdash; ' . __( '(optional) Amazon public key. Default is one set in options. You can set a different one if needed for another locale. Applies to all ASINs in list.', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '			</ul>';
			$pageTxtArr[] = '			<p>' . __( 'Example of the amazon-grid shortcode usage:', 'amazon-product-in-a-post-plugin' ) . '</p>';
			$pageTxtArr[] = '			<ul>';
			$pageTxtArr[] = '				<li>' . __( 'if you want to have a product with only a large image, the title and button, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
			$pageTxtArr[] = '					<code>[amazon-grid asin=&quot;0753515032,0753515055,0753515837,&quot; fields=&quot;title,lg-image,button&quot;]</code></li>';
			$pageTxtArr[] = '				<li>' . __( 'If you want that same product to have the list price and the new price, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
			$pageTxtArr[] = '					<code>[amazon-grid asin=&quot;0753515032,0753515055,0753515837&quot; fields=&quot;title,lg-image,<span style="color:#FF0000;">ListPrice,new-price,button&quot;</span>]</code></li>';
			$pageTxtArr[] = '				<li>' . __( 'If you want 5 columns and default fields, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
			$pageTxtArr[] = '					<code>[amazon-grid asin=&quot;0753515032,0753515055,0753515837&quot; <span style="color:#FF0000;">columns=&quot;5&quot;</span>]</code></li>';
			$pageTxtArr[] = '			</ul>';
			$pageTxtArr[] = '		<hr/>';
			$pageTxtArr[] = '			<h4>' . __( 'Available Fields for the shortcode:', 'amazon-product-in-a-post-plugin' ) . '</h4>';
			$pageTxtArr[] = '			<h3>' . __( 'Common Items', 'amazon-product-in-a-post-plugin' ) . '</h3>';
			$pageTxtArr[] = '			'.__( 'These are generally common in all products (if available)', 'amazon-product-in-a-post-plugin' );
			$pageTxtArr[] = '			<ul class="as_code">';
			$pageTxtArr[] = '				<li>' .'<code>title</code> - <span class="small-text">'. __( 'Product Title.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>desc</code> or <code>description</code> - <span class="small-text">'. __( 'Product Description.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>price</code> or <code>new-price</code> or <code>new price</code> - <span class="small-text">'. __( 'Product Price.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>price+list</code> - <span class="small-text">'. __( 'Shows both the Product list price and sale price.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>image</code> - <span class="small-text">'. __( 'Product Image (Medium Image).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>sm-image</code> - <span class="small-text">'. __( 'Product Small Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>med-image</code> - <span class="small-text">' .__( 'Product Medium Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>lg-image</code> - <span class="small-text">'. __( 'Product Large Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>full-image</code> - <span class="small-text">'. __( 'Product Full Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>large-image-link</code> - <span class="small-text">'. __( 'Large Image Link (shows "See Larger Image" link).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>link</code> - <span class="small-text">'. __( 'Product Page Link (shows full link in anchor tag).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>AddlImages</code> or <code>gallery</code> or <code>imagesets</code> - <span class="small-text">'. __( 'Product Additional Images.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>features</code> - <span class="small-text">'. __( 'Product Featured Items Text.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>ListPrice</code> or <code>list</code> - <span class="small-text">'. __( 'Product Manufacturer\'s Suggested Retail Price (SRP).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>new-button</code> - <span class="small-text">'. __( 'No Longer Used.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>button</code> - <span class="small-text">'. __( 'Displays default Image button or HTML button if a button templte was passed as a shortcode parameter.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '				<li>' .'<code>customerreviews</code> - <span class="small-text">'. __( 'Product Customer Reviews (shown in an iframe only). Not ideal for Grid layout.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
			$pageTxtArr[] = '			</ul>';
			$pageTxtArr[] = '			<p>' .__( 'There are other fields available - bascially any field returned in the API can be used. For a more complete list, see', 'amazon-product-in-a-post-plugin' ) .' <a href="?page=apipp_plugin-shortcode&tab=amazonelements" class="amazonelements"><strong>amazon-elements</strong></a> '. __('shortcode page.','amazon-product-in-a-post-plugin').'</p>';
			$pageTxtArr[] = '			<ul class="as_code">';

			$pageTxtArr[] = '				<li>' . __( 'LowestNewPrice', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'LowestUsedPrice', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'LowestRefurbishedPrice', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'LowestCollectiblePrice', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'MoreOffersUrl', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'NewAmazonPricing', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'TotalCollectible', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'TotalNew', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'TotalOffers', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'TotalRefurbished', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '				<li>' . __( 'TotalUsed', 'amazon-product-in-a-post-plugin' ) . '</li>';
			$pageTxtArr[] = '			</ul>';
			$pageTxtArr[] = '		<hr/>';
			$pageTxtArr[] = '		</div>';
			$content[] = implode( "\n", $pageTxtArr );
			return $content;
		}

		public static function do_added_shortcode_help_tab($tab = array(), $current_tab = '' ) {
			$tab[] = '<a id="amazon-product-grid" class="appiptabs nav-tab ' . ( $current_tab == 'amazon-product-grid' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=amazon-product-grid">' . __( 'Product Grid', 'amazon-product-in-a-post-plugin' ) . '</a>';
			return $tab;
		}

		public static function grid_columns( $count = 3 ) {
			if ( $count == 0 )
				$count = 3;
			return ( int )$count;
		}

		public static function add_fields( $fields = '' ) {
			$tempfields = is_array( $fields ) && !empty( $fields ) ? explode( ',', $fields ) : array();
			if ( is_array( $tempfields ) && !empty( $tempfields ) )
				return $tempfields; //this is an override for shortcode if nothing present
			$tempfields[] = 'image';
			$tempfields[] = 'title';
			$tempfields[] = 'author';
			$tempfields[] = 'price';
			$tempfields[] = 'button';
			return implode( ",", $tempfields );
		}

	}

	new Amazon_Product_Shortcode_Grid( 'amazon-grid' );

	function appip_grid_php_block_init() {
		if( function_exists('register_block_type') ){
			global $apippopennewwindow,$amazon_styles_enqueued;
			$pluginStyles = array('amazon-theme-styles');
			$pluginScripts = array('amazon-grid-block');
			$wheretoenqueue = 'amazon-theme-styles';
			if ( file_exists( get_stylesheet_directory() . '/appip-styles.css' ) ) {
				wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/appip-styles.css', array(), null );
			} elseif ( file_exists( get_stylesheet_directory() . '/css/appip-styles.css' ) ) {
				wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/css/appip-styles.css', array(), null );
			} else {
				$wheretoenqueue = 'amazon-default-styles';
				wp_enqueue_style( 'amazon-default-styles', plugins_url( 'css/amazon-default-plugin-styles.css', dirname( __FILE__ ) ), array(), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-default-plugin-styles.css' ) );
			}
			wp_enqueue_style( 'amazon-frontend-styles', plugins_url( 'css/amazon-frontend.css', dirname( __FILE__ ) ), array($wheretoenqueue), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-frontend.css' ) );
			$pluginStyles[] = 'amazon-frontend-styles';
			$usemine = get_option( 'apipp_product_styles_mine', false );
			if ( $usemine && !$amazon_styles_enqueued ) {
				$data = wp_kses( get_option( 'apipp_product_styles', '' ), array( "\'", '\"' ) );
				if($data != '')
					wp_add_inline_style( 'amazon-frontend-styles', $data );
				$amazon_styles_enqueued = true;
			}

			wp_register_script(
				'amazon-grid-block',
				plugins_url( '/blocks/php-block-grid.js', __FILE__ ),
				array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
				filemtime( plugin_dir_path( __FILE__ ) . 'blocks/php-block-grid.js' )
			);

			register_block_type( 'amazon-pip/amazon-grid', array(
				'attributes'      => array(
					'fields' => array(
						'type' => 'string',
						'default' => 'image,title,price,button',
					),
					'asin' => array(
						'type' => 'string',
						'placeholder'=> __('Enter ASIN or ASINs','amazon-product-in-a-post-plugin'),
					),
					'columns' => array(
						'type' => 'int',
						'default' => 3,
					),
					'newWindow' => array(
						'type' => 'bool',
						'default' => (bool) $apippopennewwindow,
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
					),
					'button' => array(
						'type' => 'string',
						'default' => 'read-more-green-rounded',
					),
					'labels'=> array(
						'type' => 'string',
						'placeholder' => __('Labels (optional)','amazon-product-in-a-post-plugin'),
						'default' => '',
					),
					'button_url'=> array(
						'type' => 'string',
					),
					'container'=> array(
						'type' => 'string',
						'default' => apply_filters('amazon-grid-container','div'),
					),
					'container_class'=> array(
						'type' => 'string',
						'default' => apply_filters('amazon-grid-container-class','amazon-grid-wrapper'),
					),
				),
				'editor_style' => $pluginStyles,
				'editor_script' => $pluginScripts,
				'render_callback' => array('Amazon_Product_Shortcode_Grid', 'do_shortcode'),
			) );
		}
	}
	add_action( 'init', 'appip_grid_php_block_init');
}