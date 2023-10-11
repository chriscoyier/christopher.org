<?php
function register_plugin_meta_for_Gutentaug() {
	$metafields = array(
		'amazonpipp_noncename' => 'string',
		'amazon-product-isactive' => 'string',
		'amazon-product-single-asin' => 'string',
		'amazon-product-content-location'=> 'string',
		'amazon-product-content-hook-override'=> 'string',
		'amazon-product-excerpt-hook-override'=> 'string',
		'amazon-product-singular-only'=> 'string',
		'amazon-product-amazon-desc'=> 'string',
		'amazon-product-show-gallery'=> 'string',
		'amazon-product-show-features'=> 'string',
		'amazon-product-newwindow'=> 'string',
		'amazon-product-show-list-price'=> 'string',
		'amazon-product-show-used-price'=> 'string',
		'amazon-product-show-saved-amt'=> 'string',
		'amazon-product-timestamp'=> 'string',
		'amazon-product-new-title' => 'string',
		'amazon-product-use-cartURL'=> 'string',
		'amazon_featured_post_meta_key'=> 'string',
		'_amazon_featured_alt' => 'string',
		'amazon-product-template' => 'string',
	);
	if(function_exists('register_post_meta')){
		foreach($metafields as $mf => $mv){
			register_post_meta( '', $mf, array('show_in_rest' => true,'single' => true,'type' => $mv,) );
		}
	}
}
add_action( 'init', 'register_plugin_meta_for_Gutentaug' );

/**/
/**
 * Check if Block Editor is active.
 * Must only be used after plugins_loaded action is fired.
 *
 * @return bool
 */
function appip_check_blockEditor_is_active() {
    // Gutenberg plugin is installed and activated.
    $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );
    // Block editor since 5.0.
    $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );
    if ( ! $gutenberg && ! $block_editor )
        return false;
    if ( ! function_exists( 'is_plugin_active' ) )
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) )
        return true;
    $use_block_editor = get_option( 'classic-editor-replace','' ) === 'no-replace' ||  get_option( 'classic-editor-replace' , '') === 'block';
    return $use_block_editor;
}

if (!function_exists('appip_write_log')) {
    function appip_write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}
add_action('init',function(){
	if( function_exists('register_block_type') ){
		add_filter( 'appip-register-templates', function($appip_template_array){
			$appip_template_array[] = array('location' => 'core', 'name' => __('Default','amazon-product-in-a-post-plugin'),'ID' => 'default');
			return $appip_template_array;
		},5,1);
	}
});
function amazon_product_get_new_button_array( $locale, $trigger = ''  ){
	$btn_arr = array(
		"read-more" => array('color'=> '', 'text' => __('Read More','amazon-product-in-a-post-plugin'),'dropdown_title'=> __('Read More - Dk Gray','amazon-product-in-a-post-plugin') ),
		"read-more-rounded" =>  array('color'=> '', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Dk Gray Rounded' ,'amazon-product-in-a-post-plugin')),
		"read-more-red" => array('color'=> '--red', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Red','amazon-product-in-a-post-plugin') ),
		"read-more-red-rounded" =>  array('color'=> '--red', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Red Rounded','amazon-product-in-a-post-plugin') ),
		"read-more-blue" => array('color'=> '--blue', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Blue','amazon-product-in-a-post-plugin')  ),
		"read-more-blue-rounded" =>  array('color'=> '--blue', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Blue Rounded','amazon-product-in-a-post-plugin')  ),
		"read-more-green" => array('color'=> '--green', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Green','amazon-product-in-a-post-plugin')  ),
		"read-more-green-rounded" =>  array('color'=> '--green', 'text' => __('Read More','amazon-product-in-a-post-plugin'), 'dropdown_title'=> __('Read More - Green Rounded','amazon-product-in-a-post-plugin')  ),
		"buy-from" => array('color'=> '','text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Dk Gray','amazon-product-in-a-post-plugin')  ),
		"buy-from-rounded" => array('color'=> '', 'text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Dk Gray Rounded','amazon-product-in-a-post-plugin')  ),
		"buy-from-red" => array('color'=> '--red','text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Red','amazon-product-in-a-post-plugin')  ),
		"buy-from-red-rounded" => array('color'=> '--red', 'text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Red Rounded','amazon-product-in-a-post-plugin')  ),
		"buy-from-blue" => array('color'=> '--blue','text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Blue','amazon-product-in-a-post-plugin')  ),
		"buy-from-blue-rounded" => array('color'=> '--blue', 'text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Blue Rounded','amazon-product-in-a-post-plugin')  ),
		"buy-from-green" => array('color'=> '--green','text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Green','amazon-product-in-a-post-plugin')  ),
		"buy-from-green-rounded" => array('color'=> '--green', 'text' => __('Buy from Amazon','amazon-product-in-a-post-plugin') .'.'. $locale, 'dropdown_title'=> __('Buy From Amazon - Green Rounded','amazon-product-in-a-post-plugin')  ),
	);
	$custom_options = get_option( 'amazon-button-custom','') != '' ? json_decode(get_option( 'amazon-button-custom'),true) : array();
	if(!empty($custom_options)){
		foreach($custom_options as $k => $v ){
			$av = (array) $v;
			$custom_name = isset($av['name ']) ? $av['name '] : $k;
			$custom_class = '-'.$custom_name.' '.$av['class'];
			$custom_button_text = $av['text'];
			$custom_dropdown_text = $av['ddtext'];
			$custom_button_text_reg_color = strpos($av['txt-reg-color'],"#") === false && $av['txt-reg-color']!= '' ? '#'.$av['txt-reg-color'] : $av['txt-reg-color'] ;
			$custom_button_text_hov_color = strpos($av['txt-hov-color'],"#") === false && $av['txt-hov-color']!= '' ? '#'.$av['txt-hov-color'] : $av['txt-hov-color'];
			$custom_button_reg_color = strpos($av['btn-reg-color'],"#") === false && $av['btn-reg-color']!= '' ? '#'.$av['btn-reg-color'] : $av['btn-reg-color'];
			$custom_button_hov_color = strpos($av['btn-hov-color'],"#") === false && $av['btn-hov-color']!= '' ? '#'.$av['btn-hov-color'] : $av['btn-hov-color'];
			$custom_button_round = $av['btn-rnd'];
			$css = '.amazon__btn-'.$custom_name.'{color: '.$custom_button_text_reg_color.' !important; background-color: '.$custom_button_reg_color.' !important;border-radius: '.(int)$custom_button_round.'px !important;}'."\n".'.amazon__btn-'.$custom_name.':hover{color: '.$custom_button_text_hov_color.' !important; background-color: '.$custom_button_hov_color.' !important;}';
			$btn_arr[ $k] = array(
				'color'=> $custom_class,
				'text' => esc_attr($custom_button_text),
				'dropdown_title' => esc_attr($custom_dropdown_text),
				'custom' => isset($av['name ']) ? 'true' :'false',
				'css' => $css,
			);
		}
	}
	if(is_array($btn_arr)){
		// outputs custom CSS
		$cssout = array();
		foreach($btn_arr as $bk => $bv){
			if(isset($bv['css']) && $bv['css'] != '' ){
				$cssout[] = $bv['css'];
			}
		}
		if(!empty($cssout)){
			wp_add_inline_style( 'amazon-frontend-styles', implode("\n",$cssout) );
		}
	}
	$btn_arr = apply_filters('amazon-add-new-button-array',$btn_arr,$locale);
	return $btn_arr;
}

//add_action( 'wp_footer', 'amazon_add_footer_code', 100 );
function amazon_add_footer_code() {
	$is_login = in_array( $GLOBALS[ 'pagenow' ], array( 'wp-login.php', 'wp-register.php' ) ) ? true : false;
	$tracking_id = APIAP_ASSOC_ID; //Amazon Partner ID
	$link_id = get_option( 'apipp_amazon_associate_ad_linkid', '' ); // add link id
	$region = get_option( 'apipp_amazon_associate_ad_region', 'US' ); // add region code
	if ( ( bool )get_option( 'apipp_product_mobile_popover', true ) === true && !is_admin() && !$is_login && $tracking_id !== '' && $link_id !== '' ) {
		echo '
		<script type="text/javascript">
			amzn_assoc_ad_type = "link_enhancement_widget";
			amzn_assoc_tracking_id = "' . $tracking_id . '";
			amzn_assoc_linkid = "' . $link_id . '";
			amzn_assoc_placement = "";
			amzn_assoc_marketplace = "amazon";
			amzn_assoc_region = "' . $region . '";
		</script>
		<script src="//ws-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace=US"></script>
		';
	}
}
function aws_prodinpost_filter_get_excerpt( $text ) {
	global $appip_running_excerpt;
	$appip_running_excerpt = true;
	return $text;
}
add_filter( 'amazon_product_post_cache', function ( $cache_in_sec ) {
	if ( $cache_in_sec == '' || ( int )$cache_in_sec == 0 )
		return 3600;
	return $cache_in_sec;
}, -1 );
if ( !function_exists( 'aws_prodinpost_filter_content_test' ) ) {
	function aws_prodinpost_filter_content_test( $text ) {
		global $post, $apipphookcontent, $apipphookexcerpt,$wp_query;
		//global $appip_running_excerpt;

		if(is_home())
			$appip_running_excerpt = true;
		elseif(is_singular())
			$appip_running_excerpt = false;
		else
			$appip_running_excerpt = true;

		$ActiveProdPostAWS = get_post_meta( $post->ID, 'amazon-product-isactive', true );
		$singleProdPostAWS = get_post_meta( $post->ID, 'amazon-product-single-asin', true );
		$AWSPostLoc = get_post_meta( $post->ID, 'amazon-product-content-location', true );
		$apippContentHookOverride = get_post_meta( $post->ID, 'amazon-product-content-hook-override', true );
		$apippExcerptHookOverride = get_post_meta( $post->ID, 'amazon-product-excerpt-hook-override', true );
		$apippShowSingularonly = get_post_meta( $post->ID, 'amazon-product-singular-only', true );
		$showDesc = get_post_meta( $post->ID, 'amazon-product-amazon-desc', true );
		$showGallery = get_post_meta( $post->ID, 'amazon-product-show-gallery', true );
		$showFeatures = get_post_meta( $post->ID, 'amazon-product-show-features', true );
		$newWindow = get_post_meta( $post->ID, 'amazon-product-newwindow', true );
		$showList = get_post_meta( $post->ID, 'amazon-product-show-list-price', true );
		$showUsed = get_post_meta( $post->ID, 'amazon-product-show-used-price', true );
		$showSaved = get_post_meta( $post->ID, 'amazon-product-show-saved-amt', true );
		$showTimestamp = get_post_meta( $post->ID, 'amazon-product-timestamp', true );
		$newTitle = get_post_meta( $post->ID, 'amazon-product-new-title', true );
		$useCartURL = get_post_meta( $post->ID, 'amazon-product-use-cartURL', true ) == '1' ? 1 : 0;
		$newWindow = $newWindow == '2' ? 1 : 0;
		$apptemplate = get_post_meta( $post->ID, 'amazon-product-template', true );
		$apptemplate = $apptemplate == '' ? 'default': $apptemplate;
		$manualArray = array(
			'desc' => $showDesc,
			'listprice' => $showList,
			'features' => $showFeatures,
			'used_price' => $showUsed,
			'saved_amt' => $showSaved,
			'timestamp' => $showTimestamp,
			'gallery' => $showGallery,
			'replace_title' => $newTitle,
			'usecarturl' => $useCartURL,
			'use_carturl' => $useCartURL,
			'newwindow' => $newWindow,
			'template' => $apptemplate,
		);
		/*
		 * Strip Excerpt Shortcodes:
		 * this strips the shortcodes out of the excerpt in the event
		 * that there is not excerpt and one is created using the content.
		 * otherwise you get nonsense text from removed HTML in product.
		 */
		$stripShortcodes = false;
		if ( $appip_running_excerpt === true ) {
			if ( ( bool )$apipphookexcerpt === false && ((int) $apippExcerptHookOverride === 3 || (bool) $apippExcerptHookOverride === false  ) )
				$stripShortcodes = true;
		}
		/* END Strip Excerpt Shortcodes */
		$scode_attrs = array( 'amazon-element', 'amazon-elements', 'amazonproducts', 'amazonproduct', 'AMAZONPRODUCTS', 'AMAZONPRODUCT' );
		$pattern = get_shortcode_regex();
		$allASIN = $singleProdPostAWS != '' ? explode( ',', str_replace( ', ', ',', $singleProdPostAWS ) ) : array();
		$grASIN = array();
		foreach ( $scode_attrs as $scode ) {
			if ( has_shortcode( $text, $scode ) ) {
				if ( preg_match_all( '/' . $pattern . '/s', $text, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
					foreach ( $matches[ 3 ] as $a ) {
						//$attrs = shortcode_parse_atts( $a );
						//if ( isset( $attrs[ 'asin' ] ) ) {
							//$temp = explode( ',', $attrs[ 'asin' ] );
							//foreach ( $temp as $tempval ) {
								//array_push( $allASIN, $tempval );
							//}
						//}
						$attrs = shortcode_parse_atts( $a );
						if ( isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ) {
							// dont cache - different partner ID
							// todo- add cache ahead for these circumstances
						} elseif ( isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ) {
							// dont cache - different locale
							// todo- add cache ahead for these circumstances
						} elseif ( isset( $attrs[ 'asin' ] ) && $attrs[ 'asin' ] !== '' ) {
							$temp = explode( ',', $attrs[ 'asin' ] );
							foreach ( $temp as $tempval ) {
								if ( !in_array( $tempval, $allASIN ) )
									$allASIN[] = $tempval;
							}
						} elseif ( 'amazon' == $scode && isset( $attrs[ 'template' ] ) && ( isset( $attrs[ 1 ] ) && strpos( $attrs[ 1 ], 'asin=' ) !== false ) ) {
							//this is a special amazon code from old "Amazon Links" users
							$temp = explode( 'asin=', $attrs[ 1 ] );
							if ( isset( $temp[ 1 ] ) ) {
								if ( !in_array( $temp[ 1 ], $allASIN ) )
									$allASIN[] = $temp[ 1 ];
							}
						}
					}
				}
			}
		}

		if ( !empty( $allASIN ) ) {
			foreach ( $allASIN as $asinl ) {
				$grASIN[] = $asinl;
			}
		}
		if ( !empty( $grASIN ) ) {
			$associalTag = isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ? esc_attr($attrs[ 'partner_id' ] ) : APIAP_ASSOC_ID ;
			$locale = isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ? esc_attr($attrs[ 'locale' ] ) : APIAP_LOCALE ;
			$publcKey = isset( $attrs[ 'public_key' ] ) && $attrs[ 'public_key' ] !== APIAP_PUB_KEY ? esc_attr($attrs[ 'public_key' ] ) : APIAP_PUB_KEY;
			$secretKey = isset( $attrs['secret_key' ] ) && $attrs[ 'secret_key' ] !== APIAP_SECRET_KEY ? esc_attr($attrs[ 'secret_key' ] ) : APIAP_SECRET_KEY ;
			/* NEW */
			$Regions = __getAmz_regions();
			$region = $Regions[ $locale ][ 'RegionCode' ];
			$host = $Regions[ $locale ][ 'Host' ];
			$accessKey = $publcKey;
			$secretKey = $secretKey;
			$payloadArr = array();
			$payloadArr[ 'ItemIds' ] = $grASIN;
			$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
			$payloadArr[ 'PartnerTag' ] = $associalTag;
			$payloadArr[ 'PartnerType' ] = 'Associates';
			$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$locale;
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
			/* END NEW */
			$skipCache = false;//cache-only-loop-start
			$pxmlNew = amazon_plugin_aws_signed_request( $locale, array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $grASIN, "AssociateTag" => $associalTag, "RequestBy" => 'cache-only-loop-start' ), $accessKey, $secretKey, ($skipCache ? true : false) );
		}
		$doshort = false;
		foreach ( $scode_attrs as $scode ) {
			if ( has_shortcode( $text, $scode ) ) {
				if ( preg_match_all( '/' . $pattern . '/s', $text, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
					if ( ( $apippShowSingularonly == '1' && !is_singular() ) || $stripShortcodes ) {
						foreach ( $matches[ 0 ] as $scs )
							$text = str_replace( $scs, '', $text );
					} else {
						$doshort = true;
					}
				}
			}
		}
		if ( $stripShortcodes )
			return $text;
		if ( $doshort )
			$text = do_shortcode( $text );
		if ( $apippShowSingularonly == '1' ) {
			if ( is_singular() && ( ( $apipphookcontent == true && $apippContentHookOverride != '3' ) || $apippContentHookOverride == '' || $apipphookcontent == '' ) ) { //if options say to show it, show it
				if ( $singleProdPostAWS != '' && $ActiveProdPostAWS != '' ) {
					if ( $AWSPostLoc == '2' ) {
						//Post Content is the description
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, $text, 0, $manualArray );
					} elseif ( $AWSPostLoc == '3' ) {
						//Post Content before product
						$theproduct = $text . '<br />' . getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray );
					} else {
						//Post Content after product - default
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray ) . '<br />' . $text;
					}
					return $theproduct;
				} else {
					return $text;
				}
			}
		} else {
			if ( ( $apipphookcontent == true && $apippContentHookOverride != '3' ) || $apippContentHookOverride == '' || $apipphookcontent == '' ) { //if options say to show it, show it
				if ( $singleProdPostAWS != '' && $ActiveProdPostAWS != '' ) {
					if ( $AWSPostLoc == '2' ) {
						//Post Content is the description
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, $text, 0, $manualArray );
					} elseif ( $AWSPostLoc == '3' ) {
						//Post Content before product
						$theproduct = $text . '<br />' . getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray );
					} else {
						//Post Content after product - default
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray ) . '<br />' . $text;
					}
					return $theproduct;
				} else {
					return $text;
				}
			}
		}
		return $text;
	}
}
if ( !function_exists( 'aws_prodinpost_filter_excerpt_test' ) ) {
	function aws_prodinpost_filter_excerpt_test( $text ) {
		global $post, $apipphookexcerpt;
		$ActiveProdPostAWS = get_post_meta( $post->ID, 'amazon-product-isactive', true );
		$singleProdPostAWS = get_post_meta( $post->ID, 'amazon-product-single-asin', true );
		$AWSPostLoc = get_post_meta( $post->ID, 'amazon-product-content-location', true );
		$apippExcerptHookOverride = get_post_meta( $post->ID, 'amazon-product-excerpt-hook-override', true );
		$apippShowSingularonly = get_option( 'apipp_show_single_only' ) == '1' ? '1' : '0';
		$apippShowSingularonly2 = get_post_meta( $post->ID, 'amazon-product-singular-only', true );
		$showDesc = get_post_meta( $post->ID, 'amazon-product-amazon-desc', true );
		$showGallery = get_post_meta( $post->ID, 'amazon-product-show-gallery', true );
		$showFeatures = get_post_meta( $post->ID, 'amazon-product-show-features', true );
		$showList = get_post_meta( $post->ID, 'amazon-product-show-list-price', true );
		$showUsed = get_post_meta( $post->ID, 'amazon-product-show-used-price', true );
		$showSaved = get_post_meta( $post->ID, 'amazon-product-show-saved-amt', true );
		$showTimestamp = get_post_meta( $post->ID, 'amazon-product-timestamp', true );
		$useCartURL = get_post_meta( $post->ID, 'amazon-product-use-cartURL', true ) == '1' ? true : false;
		$newTitle = get_post_meta( $post->ID, 'amazon-product-new-title', true );
		$manualArray = array(
			'desc' => $showDesc,
			'listprice' => $showList,
			'features' => $showFeatures,
			'used_price' => $showUsed,
			'saved_amt' => $showSaved,
			'timestamp' => $showTimestamp,
			'gallery' => $showGallery,
			'usecarturl' => $useCartURL,
			'use_carturl' => $useCartURL,
			'replace_title' => $newTitle
		);
		$apippShowSingularonly = $apippShowSingularonly2 == '1' ? '1' : $apippShowSingularonly;
		$scode_attrs = array( 'amazon-element', 'amazon-elements', 'amazonproducts', 'amazonproduct', 'AMAZONPRODUCTS', 'AMAZONPRODUCT' );
		$pattern = get_shortcode_regex();
		$ASINs_Set = $singleProdPostAWS;
		$allASIN = $singleProdPostAWS != '' ? explode( ',', str_replace( ', ', ',', $singleProdPostAWS ) ) : array();
		$grASIN = array();

		if ( ( ( bool )$apipphookexcerpt && $apippExcerptHookOverride != '3' ) ) { //if options say to show it, show it
			foreach ( $scode_attrs as $scode ) {
				if ( has_shortcode( $text, $scode ) ) {
					if ( preg_match_all( '/' . $pattern . '/s', $text, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
						foreach ( $matches[ 3 ] as $a ) {
							$attrs = shortcode_parse_atts( $a );
							if ( isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ) {
								// dont cache - different partner ID
								// todo- add cache ahead for these circumstances
							} elseif ( isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ) {
								// dont cache - different locale
								// todo- add cache ahead for these circumstances
							} elseif ( isset( $attrs[ 'asin' ] ) && $attrs[ 'asin' ] !== '' ) {
								$temp = explode( ',', $attrs[ 'asin' ] );
								foreach ( $temp as $tempval ) {
									if ( !in_array( $tempval, $allASIN ) )
										$allASIN[] = $tempval;
								}
							} elseif ( 'amazon' == $scode && isset( $attrs[ 'template' ] ) && ( isset( $attrs[ 1 ] ) && strpos( $attrs[ 1 ], 'asin=' ) !== false ) ) {
								//this is a special amazon code from old "Amazon Links" users
								if ( isset( $temp[ 1 ] ) ) {
									$temp = explode( 'asin=', $attrs[ 1 ] );
									if ( !in_array( $temp[ 1 ], $allASIN ) )
										$allASIN[] = $temp[ 1 ];
								}
							}
						}
					}
				}
			}
			if ( !empty( $allASIN ) ) {
				foreach ( $allASIN as $asinl ) {
					$grASIN[ $asinl ] = $asinl;
				}
			}

			if ( !empty( $grASIN ) ) {
				$associalTag = isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ? esc_attr($attrs[ 'partner_id' ] ) : APIAP_ASSOC_ID ;
				$locale = isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ? esc_attr($attrs[ 'locale' ] ) : APIAP_LOCALE ;
				$publcKey = isset( $attrs[ 'public_key' ] ) && $attrs[ 'public_key' ] !== APIAP_PUB_KEY ? esc_attr($attrs[ 'public_key' ] ) : APIAP_PUB_KEY;
				$secretKey = isset( $attrs['secret_key' ] ) && $attrs[ 'secret_key' ] !== APIAP_SECRET_KEY ? esc_attr($attrs[ 'secret_key' ] ) : APIAP_SECRET_KEY ;
				/* NEW */
				$Regions = __getAmz_regions();
				$region = $Regions[ $locale ][ 'RegionCode' ];
				$host = $Regions[ $locale ][ 'Host' ];
				$accessKey = $publcKey;
				$secretKey = $secretKey;
				$payloadArr = array();
				$payloadArr[ 'ItemIds' ] = $grASIN;
				$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
				$payloadArr[ 'PartnerTag' ] = $associalTag;
				$payloadArr[ 'PartnerType' ] = 'Associates';
				$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$locale;
				$payload = json_encode( $payloadArr );
				$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
				/* END NEW */
				$skipCache = false;//cache-only-loop-start
				$pxmlNew = amazon_plugin_aws_signed_request( $locale, array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $grASIN, "AssociateTag" => $associalTag, "RequestBy" => 'cache-only-loop-start' ), $accessKey, $secretKey, ($skipCache ? true : false) );
			}

			//replace short tag here. Handle a bit different than content so they get stripped if they don't want to hook excerpt we don't want to show the [AMAZON-PRODUCT=XXXXXXXX] tag in the excerpt text!
			$doshort = false;
			foreach ( $scode_attrs as $scode ) {
				if ( has_shortcode( $text, $scode ) ) {
					if ( preg_match_all( '/' . $pattern . '/s', $text, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
						if ( $apippShowSingularonly == '1' && !is_singular() ) {
							foreach ( $matches[ 0 ] as $scs )
								$text = str_replace( $scs, '', $text );
						} else {
							$doshort = true;
						}
					}
				}
			}
			if ( $doshort )
				$text = do_shortcode( $text );

			if ( $apippShowSingularonly == '1' ) {
				if ( is_singular() && ( $singleProdPostAWS != '' && $ActiveProdPostAWS != '' ) ) {
					if ( $AWSPostLoc == '2' ) {
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, $text, 0, $manualArray );
					} elseif ( $AWSPostLoc == '3' ) {
						$theproduct = $text . '<br />' . getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray );
					} else {
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray ) . '<br />' . $text;
					}
					return $theproduct;
				} else {
					return $text;
				}
			} else {
				if ( $singleProdPostAWS != '' && $ActiveProdPostAWS != '' ) {
					if ( $AWSPostLoc == '2' ) {
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, $text, 0, $manualArray );
					} elseif ( $AWSPostLoc == '3' ) {
						$theproduct = $text . '<br />' . getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray );
					} else {
						$theproduct = getSingleAmazonProduct( $singleProdPostAWS, '', 0, $manualArray ) . '<br />' . $text;
					}
					return $theproduct;
				} else {
					return $text;
				}
			}
		} else {
			foreach ( $scode_attrs as $scode ) {
				if ( has_shortcode( $text, $scode ) ) {
					if ( preg_match_all( '/' . $pattern . '/s', $text, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
						foreach ( $matches[ 0 ] as $scs )
							$text = str_replace( $scs, '', $text ); //take the darn thing out!
					}
				}
			}
		}
		return $text;
	}
}
add_filter( 'amz_get_fileds_to_cache', function ( $arr ) {
	$arr[] = 'itemasin';
	$arr[] = 'amazon-product-single-asin';
	return $arr;
} );

/* **********
** Filter to add layout to features.
**
** **********/
add_filter( 'apipp_amazon_product_array_filter', function ( $RetVal, $Item ){
	if( isset( $Item['ItemAttributes']['Feature'] ) ){
		$FeatNew = array();
		$newFeat = $RetVal['Feature'];
		if(is_array($Item['ItemAttributes']['Feature']) && !empty($Item['ItemAttributes']['Feature'])){
			foreach($Item['ItemAttributes']['Feature'] as $feat){
				if(strpos( $feat,'<li class="amazon-item-feature">') === false)
					$FeatNew[] = '<li class="amazon-item-feature">'.$feat.'</li>';
			}
			if(!empty($FeatNew))
				$newFeat = '<ul>'."\n".implode("\n",$FeatNew).'</ul>'."\n";
		}else{
			if(!empty($Item['ItemAttributes']['Feature']) && strpos( $Item['ItemAttributes']['Feature'],'<li class="amazon-item-feature">' ) === false)
				$newFeat = '<ul><li class="amazon-item-feature">'.$Item['ItemAttributes']['Feature'].'</li></ul>'."\n";
		}
		$RetVal['Feature'] = $newFeat;
	}
	return $RetVal;
},
10, 2 );

function amazon_plugin_postlist_detect_and_cache_ASINs( $posts ) {
	$cache_ahead = get_option( 'apipp_amazon_cache_ahead', '0' );
	if ( !empty( $posts ) && $cache_ahead ) {
		$scode_attrs = array( 'amazon-element', 'amazon-elements', 'amazonproducts', 'amazonproduct', 'AMAZONPRODUCTS', 'AMAZONPRODUCT', 'amazon-product-search', 'amazon-grid' );
		$pattern = get_shortcode_regex();
		$allASIN = array();
		$grASIN = array();
		foreach ( $posts as $apposts ) {
			$mActv = get_post_meta( $apposts->ID, 'amazon-product-isactive', true );
			$mASIN = get_post_meta( $apposts->ID, 'amazon-product-single-asin', true );
			if ( $mActv == '1' && $mASIN != '' ) {
				$newASN = explode( ',', str_replace( ', ', ',', $mASIN ) );
				if ( is_array( $newASN ) && !empty( $newASN ) ) {
					foreach ( $newASN as $Aval ) {
						$allASIN[] = $Aval;
					}
				}
			}
			$other_fields = apply_filters( 'amz_get_fileds_to_cache', array() );
			if ( !empty( $other_fields ) ) {
				foreach ( $other_fields as $k => $v ) {
					if ( function_exists( 'get_field' ) ) {
						$meta_val = get_field( $v, $apposts->ID, true );
					} else {
						//regular meta key
						$meta_val = get_post_meta( $apposts->ID, '_' . $v, true );
					}
					if ( $meta_val != '' ) {
						$newASN = explode( ',', trim( str_replace( ', ', ',', $meta_val ) ) );
						if ( is_array( $newASN ) && !empty( $newASN ) ) {
							foreach ( $newASN as $Aval ) {
								$allASIN[] = $Aval;
							}

						}
					}
				}
			}
			//get scodes
			foreach ( $scode_attrs as $scode ) {
				if ( Amazon_Product_Shortcode::appip_has_shortcode( $apposts->post_content, $scode ) ) {
					if ( preg_match_all( '/' . $pattern . '/s', $apposts->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( $scode, $matches[ 2 ] ) ) {
						foreach ( $matches[ 3 ] as $a ) {
							$attrs = shortcode_parse_atts( $a );
							if ( isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ) {
								// dont cache - different partner ID
								// todo- add cache ahead for these circumstances
							} elseif ( isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ) {
								// dont cache - different locale
								// todo- add cache ahead for these circumstances
							} elseif ( isset( $attrs[ 'asin' ] ) && $attrs[ 'asin' ] !== '' ) {
								$temp = explode( ',', $attrs[ 'asin' ] );
								foreach ( $temp as $tempval ) {
									if ( !in_array( $tempval, $allASIN ) )
										$allASIN[] = $tempval;
								}
							} elseif ( 'amazon' == $scode && isset( $attrs[ 'template' ] ) && ( isset( $attrs[ 1 ] ) && strpos( $attrs[ 1 ], 'asin=' ) !== false ) ) {
								//this is a special amazon code from old "Amazon Links" users
								if ( isset( $temp[ 1 ] ) ) {
									$temp = explode( 'asin=', $attrs[ 1 ] );
									if ( !in_array( $temp[ 1 ], $allASIN ) )
										$allASIN[] = $temp[ 1 ];
								}
							}
						}
					}
				}
			}
		}
		if ( !empty( $allASIN ) ) {
			foreach ( $allASIN as $asinl ) {
				$grASIN[ $asinl ] = $asinl;
			}
		}
		if ( !empty( $grASIN ) ) {
			//cache all the ones on the page if possible.
			$associalTag = isset( $attrs[ 'partner_id' ] ) && $attrs[ 'partner_id' ] !== APIAP_ASSOC_ID ? esc_attr($attrs[ 'partner_id' ] ) : APIAP_ASSOC_ID ;
			$locale = isset( $attrs[ 'locale' ] ) && $attrs[ 'locale' ] !== APIAP_LOCALE ? esc_attr($attrs[ 'locale' ] ) : APIAP_LOCALE ;
			$publcKey = isset( $attrs[ 'public_key' ] ) && $attrs[ 'public_key' ] !== APIAP_PUB_KEY ? esc_attr($attrs[ 'public_key' ] ) : APIAP_PUB_KEY;
			$secretKey = isset( $attrs['secret_key' ] ) && $attrs[ 'secret_key' ] !== APIAP_SECRET_KEY ? esc_attr($attrs[ 'secret_key' ] ) : APIAP_SECRET_KEY ;
			/* NEW */
			$Regions = __getAmz_regions();
			$region = $Regions[ $locale ][ 'RegionCode' ];
			$host = $Regions[ $locale ][ 'Host' ];
			$accessKey = $publcKey;
			$secretKey = $secretKey;
			$payloadArr = array();
			$payloadArr[ 'ItemIds' ] = $grASIN;
			$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
			$payloadArr[ 'PartnerTag' ] = $associalTag;
			$payloadArr[ 'PartnerType' ] = 'Associates';
			$payloadArr[ 'Marketplace' ] = 'www.amazon.'.$locale;
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
			/* END NEW */
			$skipCache = false;//cache-only-loop-start
			$pxmlNew = amazon_plugin_aws_signed_request( $locale, array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $grASIN, "AssociateTag" => $associalTag, "RequestBy" => 'cache-only-loop-start' ), $accessKey, $secretKey, ($skipCache ? true : false) );
		}
	}
	return $posts;
}
//updated to production 4.0.0
add_filter( 'the_posts', 'amazon_plugin_postlist_detect_and_cache_ASINs' );
function maybe_convert_encoding( $text ) {
	$encmode_temp = mb_detect_encoding( "aeiou�", mb_detect_order() );
	$encodemode = get_bloginfo( 'charset' );
	if ( $encmode_temp != $encodemode ) {
		return mb_convert_encoding( $text, $encodemode, $encmode_temp );
	}
	return $text;
}
function appip_product_array_processed_add_variants( $resultarr, $newWin = '' ) {
	$resultArrNew = array();
	$hideVariants = apply_filters('amazon_hide_parent_variants', false );
	$variantDepth = apply_filters('amazon_depth_parent_variant', 'summary' ); // full
	if ( !( is_array( $resultarr ) && !empty( $resultarr ) ) || $hideVariants === true )
		return $resultArrNew;
	foreach ( $resultarr as $key => $val ) {
		if ( isset( $val[ 'Offers_TotalOffers' ] ) && $val[ 'Offers_TotalOffers' ] == '0' ) {
			$varLowPrice = isset( $val[ 'VariationSummary_LowestSalePrice_FormattedPrice' ] ) ? $val[ 'VariationSummary_LowestSalePrice_FormattedPrice' ] : ( isset( $val[ 'VariationSummary_LowestPrice_FormattedPrice' ] ) ? $val[ 'VariationSummary_LowestPrice_FormattedPrice' ] : '' );
			$varHiPrice = isset( $val[ 'VariationSummary_HighestPrice_FormattedPrice' ] ) ? $val[ 'VariationSummary_HighestPrice_FormattedPrice' ] : '';
			$varTotalNum = isset( $val[ 'Variations_TotalVariations' ] ) ? ( int )$val[ 'Variations_TotalVariations' ] : 0;
			$hasMainList = isset( $val[ 'ItemAttributes_ListPrice_FormattedPrice' ] ) ? 1 : 0;
			if ( $hasMainList == 1 ) {
				$val[ 'ListPrice' ] = isset( $val[ 'ItemAttributes_ListPrice_FormattedPrice' ] ) ? $val[ 'ItemAttributes_ListPrice_FormattedPrice' ] : '';
			}
			if ( $varTotalNum > 0 ) {
				if ( $varTotalNum == 1 ) {
					//Set Main Image as first variant Image if product does not have Image
					if( $val[ 'MediumImage' ] === '')
						$val[ 'MediumImage' ] = isset( $val[ 'LargeImage_URL' ] ) && $val[ 'LargeImage_URL' ] != '' ? $val[ 'LargeImage_URL' ] : ( isset( $val[ 'Variations_Item_LargeImage_URL' ] ) ? $val[ 'Variations_Item_LargeImage_URL' ] : '' );
					if( $val[ 'LargeImage' ] === '')
						$val[ 'LargeImage' ] = isset( $val[ 'LargeImage_URL' ] ) && $val[ 'LargeImage_URL' ] != '' ? $val[ 'LargeImage_URL' ] : ( isset( $val[ 'Variations_Item_LargeImage_URL' ] ) ? $val[ 'Variations_Item_LargeImage_URL' ] : '' );;
				} else {
					//Set Main Image as first variant Image if product does not have Image
					if( $val[ 'MediumImage' ] === '')
						$val[ 'MediumImage' ] = isset( $val[ 'LargeImage_URL' ] ) && $val[ 'LargeImage_URL' ] != '' ? $val[ 'LargeImage_URL' ] : ( isset( $val[ 'Variations_Item_0_LargeImage_URL' ] ) ? $val[ 'Variations_Item_0_LargeImage_URL' ] : '' );
					if( $val[ 'LargeImage' ] === '')
						$val[ 'LargeImage' ] = isset( $val[ 'LargeImage_URL' ] ) && $val[ 'LargeImage_URL' ] != '' ? $val[ 'LargeImage_URL' ] : ( isset( $val[ 'Variations_Item_0_LargeImage_URL' ] ) ? $val[ 'Variations_Item_0_LargeImage_URL' ] : '' );;
				}
				//Set New price for "from X to Y"
				if ( $varLowPrice != '' && $varHiPrice != '' ) {
					$val[ 'LowestNewPrice' ] = $varLowPrice . ' &ndash; ' . $varHiPrice;
				}

				//Set Total New
				$val[ "TotalNew" ] = 1; //needs to be at least one to not show "Out of Stock".
				$val[ "PriceHidden" ] = 0;
				$val[ "HideStockMsg" ] = 1;
				//List Varients
				$vartype = isset( $val[ 'Variations_VariationDimensions_VariationDimension' ] ) ? $val[ 'Variations_VariationDimensions_VariationDimension' ] : '';
				if ( $vartype != '' ) {
					$val[ 'VariantHTML' ] = '<div class="amazon_variations_wrapper">' . __( 'Variations', 'amazon-product-in-a-post-plugin' ) . ' (' . $vartype . '):';
				} else {
					$val[ 'VariantHTML' ] = '<div class="amazon_variations_wrapper">' . __( 'Variations', 'amazon-product-in-a-post-plugin' ) . ':';
				}
				$target = $newWin == '' ? '' : $newWin;
				$ImageSetsArray = array();
				if ( $varTotalNum == 1 ) {
					$varASIN = isset( $val[ 'Variations_Item_ASIN' ] ) ? $val[ 'Variations_Item_ASIN' ] : '';
					if ( $hasMainList == 0 && isset( $val[ 'Variations_Item_ItemAttributes_ListPrice_FormattedPrice' ] ) ) {
						$val[ 'ListPrice' ] = $val[ 'Variations_Item_ItemAttributes_ListPrice_FormattedPrice' ];
					}
					//for image sets
					for ( $y = 0; $y < 10; $y++ ) {
						if ( isset( $val[ 'Variations_Item_ImageSets_ImageSet_' . $y . '_LargeImage_URL' ] ) && isset( $val[ 'Variations_Item_ImageSets_ImageSet_' . $y . '_SmallImage_URL' ] ) ) {
							$lgImg = $val[ 'Variations_Item_ImageSets_ImageSet_' . $y . '_LargeImage_URL' ];
							$swImg = $val[ 'Variations_Item_ImageSets_ImageSet_' . $y . '_SmallImage_URL' ];
							if ( $lgImg != '' && $swImg != '' ) {
								$ImageSetsArray[] = '<a rel="appiplightbox-' . $val[ 'ASIN' ] . '" href="#" data-appiplg="' . $lgImg . '" target="amazonwin"><img src="' . $swImg . '" alt="'.(apply_filters('appip_alt_text_gallery_img','Img - '.$val[ 'ASIN' ],$val[ 'ASIN' ])).'" class="apipp-additional-image" target="amazonwin"/></a>' . "\n";
							}
						} else {
							if ( $y > 9 ) {
								break 1;
							}
						}
					}
					$varT = isset( $val[ 'Variations_Item_VariationAttributes_VariationAttribute_Value' ] ) ? $val[ 'Variations_Item_VariationAttributes_VariationAttribute_Value' ] : '';
					$varC = isset( $val[ 'Variations_Item_Offers_Offer_OfferAttributes_Condition' ] ) ? $val[ 'Variations_Item_Offers_Offer_OfferAttributes_Condition' ] : '';
					$varD = isset( $val[ 'Variations_Item_Offers_Offer_OfferListing_SalePrice_CurrencyCode' ] ) ? get_appipCurrCode( $val[ 'Variations_Item_Offers_Offer_OfferListing_SalePrice_CurrencyCode' ] ) : ( isset( $val[ 'Variations_Item_Offers_Offer_OfferListing_Price_CurrencyCode' ] ) ? get_appipCurrCode( $val[ 'Variations_Item_Offers_Offer_OfferListing_Price_CurrencyCode' ] ) : '' );
					$varP = isset( $val[ 'Variations_Item_Offers_Offer_OfferListing_SalePrice_FormattedPrice' ] ) ? $val[ 'Variations_Item_Offers_Offer_OfferListing_SalePrice_FormattedPrice' ] : ( isset( $val[ 'Variations_Item_Offers_Offer_OfferListing_Price_FormattedPrice' ] ) ? $val[ 'Variations_Item_Offers_Offer_OfferListing_Price_FormattedPrice' ] : '' );
					$linkStart = $varASIN != '' ? '<a href="' . str_replace( $val[ 'ASIN' ], $varASIN, $val[ 'URL' ] ) . '"' . $target . '>': '';
					$linkEnd = $linkStart != '' ? '</a>' : '';
					$varL = $linkStart != '' ? ( $linkStart . $varT . $linkEnd ) : $varT;
					$photo = isset( $val[ 'Variations_Item_SmallImage_URL' ] ) ? $linkStart . '<img class="amazon-varient-image" src="' . $val[ 'Variations_Item_SmallImage_URL' ] . '" />' . $linkEnd: '';
					if ( $varT != '' && $varC != '' && $varP != '' ) {
						$val[ 'VariantHTML' ] .= '<div class="amazon_varients">' . $photo . '<span class="amazon-varient-type-link">' . $varL . '</span> &mdash; <span class="amazon-varient-type-price"><span class="amazon-variant-price-text">' . $varC . ' ' . __( 'from', 'amazon-product-in-a-post-plugin' ) . '</span> ' . $varP . $varD . '</span></div>' . "\n";
					}
					$val[ 'VariantHTML' ] .= '</div>';

					//Make Image Set from the first image for each varient
					if ( !empty( $ImageSetsArray ) ) {
						if ( count( $ImageSetsArray ) > 10 )
							$ImageSetsArray = array_slice( $ImageSetsArray, 0, 10 );
						$val[ 'AddlImages' ] = implode( "\n", $ImageSetsArray );
					}

				} else {
					for ( $x = 0; $x <= ( $varTotalNum - 1 ); $x++ ) {
						$varASIN = isset( $val[ 'Variations_Item_' . $x . '_ASIN' ] ) ? $val[ 'Variations_Item_' . $x . '_ASIN' ] : '';
						if ( $x == 0 && $hasMainList == 0 && isset( $val[ 'Variations_Item_' . $x . '_ItemAttributes_ListPrice_FormattedPrice' ] ) ) {
							$val[ 'ListPrice' ] = $val[ 'Variations_Item_' . $x . '_ItemAttributes_ListPrice_FormattedPrice' ];
						}
						//for image sets
						for ( $y = 0; $y < 10; $y++ ) {
							if ( isset( $val[ 'Variations_Item_' . $x . '_ImageSets_ImageSet_' . $y . '_LargeImage_URL' ] ) && isset( $val[ 'Variations_Item_' . $x . '_ImageSets_ImageSet_' . $y . '_SmallImage_URL' ] ) ) {
								$lgImg = $val[ 'Variations_Item_' . $x . '_ImageSets_ImageSet_' . $y . '_LargeImage_URL' ];
								$swImg = $val[ 'Variations_Item_' . $x . '_ImageSets_ImageSet_' . $y . '_SmallImage_URL' ];
								if ( $lgImg != '' && $swImg != '' ) {
									$ImageSetsArray[] = '<a rel="appiplightbox-' . $val[ 'ASIN' ] . '" href="#" data-appiplg="' . $lgImg . '" target="amazonwin"><img src="' . $swImg . '" alt="'.(apply_filters('appip_alt_text_gallery_img','Img - '.$val[ 'ASIN' ],$val[ 'ASIN' ])).'" class="apipp-additional-image"/></a>' . "\n";
								}
							} else {
								if ( $y > 9 ) {
									break 1;
								}
							}
						}

						$varSumm = array();
						if( isset($val['Variations'][$x - 1]['VariationAttributes']['VariationAttribute']) && is_array($val['Variations'][$x - 1]['VariationAttributes']['VariationAttribute'])){
							$temp = array();
							foreach($val['Variations'][$x - 1]['VariationAttributes']['VariationAttribute'] as $k => $v ){
								$temp[] = $v['Value'];
								$varSumm[$v['Name']][] = $v['Value'];
							}
							if(!empty($temp)){
								$varT = implode(", ", $temp);
							}
						}else{
							$varT = isset( $val[ 'Variations_Item_' . $x . '_VariationAttributes_VariationAttribute_Value' ] ) ? $val[ 'Variations_Item_' . $x . '_VariationAttributes_VariationAttribute_Value' ] : '';
						}
						$varC = isset( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferAttributes_Condition' ] ) ? $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferAttributes_Condition' ] : '';
						$varD = isset( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_SalePrice_CurrencyCode' ] ) ? get_appipCurrCode( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_SalePrice_CurrencyCode' ] ) : ( isset( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_Price_CurrencyCode' ] ) ? get_appipCurrCode( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_Price_CurrencyCode' ] ) : '' );
						$varP = isset( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_SalePrice_FormattedPrice' ] ) ? $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_SalePrice_FormattedPrice' ] : ( isset( $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_Price_FormattedPrice' ] ) ? $val[ 'Variations_Item_' . $x . '_Offers_Offer_OfferListing_Price_FormattedPrice' ] : '' );

						$linkStart = $varASIN != '' ? '<a href="' . str_replace( $val[ 'ASIN' ], $varASIN, $val[ 'URL' ] ) . '"' . $target . '>': '';
						$linkEnd = $linkStart != '' ? '</a>' : '';
						$varL = $linkStart != '' ? ( $linkStart . $varT . $linkEnd ) : $varT;
						$photo = isset( $val[ 'Variations_Item_' . $x . '_SmallImage_URL' ] ) ? $linkStart . '<img class="amazon-varient-image" src="' . $val[ 'Variations_Item_' . $x . '_SmallImage_URL' ] . '" />' . $linkEnd: '';
						if($variantDepth != 'summary'){
							if ( $varT != '' && $varC != '' && $varP != '' ) {
								$val[ 'VariantHTML' ] .= '<div class="amazon_varients">' . $photo . '<span class="amazon-varient-type-link">' . $varL . '</span> &mdash; <span class="amazon-varient-type-price"><span class="amazon-variant-price-text">' . $varC . ' ' . __( 'from', 'amazon-product-in-a-post-plugin' ) . '</span> ' . $varP . $varD . '</span></div>' . "\n";
							}
						}
					}
					if($variantDepth == 'summary'){
						if(!empty($varSumm)){
							$val[ 'VariantHTML' ] .= '<div class="amazon_varient_summary_total">Total Variants: '.$varTotalNum.'</div>';
							foreach($varSumm as $vn => $vv){
								$val[ 'VariantHTML' ] .= '<div class="amazon_varient_summary"><strong>'.$vn.':</strong>'. implode(", ",$vv).'</div>';
							}
						}
					}
					$val[ 'VariantHTML' ] .= '</div>';

					//Make Image Set from the first image for each varient
					if ( !empty( $ImageSetsArray ) ) {
						if ( count( $ImageSetsArray ) > 10 )
							$ImageSetsArray = array_slice( $ImageSetsArray, 0, 10 );
						$val[ 'AddlImages' ] = implode( "\n", $ImageSetsArray );
					}
				}

			}

		}
		$resultArrNew[] = $val;
	}
	return $resultArrNew;
}
//add_filter( 'appip_product_array_processed', 'appip_product_array_processed_add_variants', 10, 2 );
if ( !function_exists( 'awsImageGrabber' ) ) {
	//Amazon Product Image from ASIN function - Returns HTML Image Code
	function awsImageGrabber( $imgurl, $class = "" ) {
		if ( $imgurl != '' ) {
			return '<img src="' . $imgurl . '" class="amazon-image ' . $class . '" />';
		} else {
			return '<img src="' . plugins_url( '/images/noimage.jpg', dirname( __FILE__ ) ) . '" class="amazon-image ' . $class . '" />';
		}
	}
}

/*
To filter labels:
add_filter('appip_text_newfrom', '_clear_appip_text');
function _clear_appip_text($val=''){
	return 'Your Text Label Here';
}
*/
if ( !function_exists( 'checkSSLImages_tag' ) ) {
	function checkSSLImages_tag( $img_URL, $class = '', $ASIN = '', $alt_tag = '') {
	    if (empty($alt_tag))
            $alt_tag = __('Buy Now','amazon-product-in-a-post-plugin');

		if ( amazon_check_SSL_on() )
			return '<img src="' . plugin_aws_prodinpost_filter_text( $img_URL ) . '" alt="'. apply_filters('appip_alt_text_main_img',$alt_tag,$ASIN) .'" class="' . $class . '">';

		return '<img src="' . $img_URL . '" alt="' . apply_filters('appip_alt_text_main_img',$alt_tag,$ASIN) . '" class="' . $class . '">';
	}
}
if ( !function_exists( 'checkSSLImages_url' ) ) {
	function checkSSLImages_url( $img_URL ) {
		if ( amazon_check_SSL_on() )
			return plugin_aws_prodinpost_filter_text( $img_URL );
		return $img_URL;
	}
}
if ( !function_exists( 'awsImageGrabberURL' ) ) {
	//Amazon Product Image from ASIN function - Returns URL only
	function awsImageGrabberURL( $asin, $size = "M" ) {
		$usSSL = amazon_check_SSL_on();
		if ( $usSSL ) {
			//http://images.amazon.com/images/P/B004RMK4BC.01._AA300_SCLZZZZZZZ_.jpg
			$base_url = 'https://images-na.ssl-images-amazon.com/images/P/' . $asin . '.01.';
		} else {
			$base_url = 'http://images.amazon.com/images/P/' . $asin . '.01.';
		}
		if ( strcasecmp( $size, 'S' ) == 0 ) {
			$base_url .= '_AA200_SCLZZZZZZZ_';
		} else if ( strcasecmp( $size, 'L' ) == 0 ) {
			$base_url .= '_AA450_SCSCRM_';
		} else if ( strcasecmp( $size, 'H' ) == 0 ) { //huge
			$base_url .= '_SCRM_';
		} else if ( strcasecmp( $size, 'P' ) == 0 ) { //pop
			$base_url .= '_AA800_SCRM_';
		} else {
			$base_url .= '_AA300_SCLZZZZZZZ_';
		}
		$base_url .= '.jpg';
		return $base_url;
	}
}
function amazon_check_SSL_on() {
	$check = false;
	if ( ( bool )get_option( 'apipp_ssl_images', false ) === true || strpos( get_bloginfo( 'url' ), 'https://' ) !== false ) {
		$check = true;
	}
	return apply_filters( 'appip_use_ssl_images', $check );
}
function plugin_aws_prodinpost_filter_text( $content ) {
	return str_replace( array( 'http://ecx.images-amazon', 'https://ecx.images-amazon' ), array( 'https://images-na.ssl-images-amazon', 'https://images-na.ssl-images-amazon' ), $content );
}
if ( !function_exists( 'awsImageURLModify' ) ) {
	//Amazon Product Image from ASIN function - Returns URL only
	function awsImageURLModify( $imgurl, $size = "P" ) {
		//http://ecx.images-amazon.com/images/I/
		$usSSL = amazon_check_SSL_on();
		if ( $usSSL )
			$imgurl = str_replace( 'http://ecx.images-amazon.com/', 'https://images-na.ssl-images-amazon.com/', $imgurl );
		$base_url = str_replace( '.jpg', '.', $imgurl );
		if ( strcasecmp( $size, 'S' ) == 0 ) {
			$base_url .= '_SY200_';
		} else if ( strcasecmp( $size, 'L' ) == 0 ) {
			$base_url .= '_SY450_';
		} else if ( strcasecmp( $size, 'H' ) == 0 ) { //huge
			$base_url .= '_SY1200_';
		} else if ( strcasecmp( $size, 'P' ) == 0 ) { //pop
			$base_url .= '_SY800_';
		} else {
			$base_url .= '_SY300_';
		}
		$base_url .= '.jpg';
		return $base_url;
	}
}
function appip_delete_cache_ajax() {
	check_ajax_referer( 'appip_cache_delete_nonce_ji9osdjfkjl', 'appip_nonce', true );
	if ( !isset( $_POST[ 'appip-cache-id' ] ) || !current_user_can( 'manage_options' ) ) {
		echo 'error';
		exit;
	}
	// removed direct use of post value.
	$cacheid = isset( $_POST[ 'appip-cache-id' ] ) && ( int )$_POST[ 'appip-cache-id' ] != 0 ? ( int )$_POST[ 'appip-cache-id' ] : 0;
	global $wpdb;
	if ( $cacheid == 0 ) {
		$tempswe = $wpdb->query( "DELETE FROM {$wpdb->prefix}amazoncache;" );
	} else {
		$tempswe = $wpdb->query( "DELETE FROM {$wpdb->prefix}amazoncache WHERE Cache_id ='{$cacheid}' LIMIT 1;" );
	}
	if ( $tempswe ) {
		echo 'deleted';
	} else {
		echo 'error';
	}
	exit;
}
add_action( 'wp_ajax_appip-cache-del', 'appip_delete_cache_ajax' );

/**
 * Delete All product Cache Files.
 * Delete all cache files on options update, so nothing is cached with old variables.
 *
 * @since 3.6.2
 * @global $wpdb
 * @param string	$reason	allowed value is 'option-update'
 */
function amazon_product_delete_all_cache( $reason = '' ) {
	if ( $reason == 'option-update' ) {
		global $wpdb;
		$tempswe = $wpdb->query( "DELETE FROM {$wpdb->prefix}amazoncache;" );
	}
}

/**
 * Enqueue styles for plugin.
 * Replaces previous function aws_prodinpost_addhead().
 *
 * @since 3.5.3
 *
 * @return none.
 */
function appip_addhead_new_ajax() {
	global $amazon_styles_enqueued;
	$wheretoenqueue = 'amazon-theme-styles';
	if ( file_exists( get_stylesheet_directory() . '/appip-styles.css' ) ) {
		wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/appip-styles.css', array(), null );
	} elseif ( file_exists( get_stylesheet_directory() . '/css/appip-styles.css' ) ) {
		wp_enqueue_style( 'amazon-theme-styles', get_stylesheet_directory_uri() . '/css/appip-styles.css', array(), null );
	} else {
		$wheretoenqueue = 'amazon-default-styles';
		wp_enqueue_style( 'amazon-default-styles', plugins_url( 'css/amazon-default-plugin-styles.css', dirname( __FILE__ ) ), array(), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-default-plugin-styles.css' ) );
	}
	$usemine = get_option( 'apipp_product_styles_mine', false );
	$template_array = apply_filters( 'appip-register-templates', array());
	$button_array = apply_filters( 'appip-register-html-buttons', array());

	wp_localize_script('jquery', 'appipTemplates', array('templates' => $template_array));
	$uselightbox = ( bool )get_option( 'apipp_amazon_use_lightbox', false ) === true ? true : false;
	if ( $uselightbox ) {
		wp_enqueue_style( 'amazon-lightbox', plugins_url( 'css/amazon-lightbox.css', dirname( __FILE__ ) ), array($wheretoenqueue), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-lightbox.css' ) );
		wp_enqueue_script( 'amazon-lightbox', plugins_url( 'js/amazon-lightbox.js', dirname( __FILE__ ) ), array( 'jquery' ), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/js/amazon-lightbox.js' ) );
	}
	wp_enqueue_style( 'amazon-frontend-styles', plugins_url( 'css/amazon-frontend.css', dirname( __FILE__ ) ), array($wheretoenqueue), filemtime( dirname( plugin_dir_path( __FILE__ ) ) . '/css/amazon-frontend.css' ) );
	if($usemine && !$amazon_styles_enqueued) {
		$data = wp_kses( get_option( 'apipp_product_styles', '' ), array( "\'", '\"' ) );
		if($data != '')
			wp_add_inline_style( 'amazon-frontend-styles', $data );
		$amazon_styles_enqueued = true;
	}
}
add_action( 'wp_enqueue_scripts', 'appip_addhead_new_ajax', 101 );
function amazon_product_check_in_cache( $items ) {
	global $amazonCache, $cacheClean;
	if ( is_array( $cacheClean ) && !empty( $cacheClean ) ) {
		$cachedASINs = $cacheClean;
	} else {
		$completeCache = $amazonCache->get_amazon_plugin_cache( 'product' );
		$cachedASINs = array();
		if ( is_array( $completeCache ) && !empty( $completeCache ) ) {
			foreach ( $completeCache as $resultkey => $resultvalue ) {
				$tempURL = explode( ":", $resultvalue->URL );
				$tempASINs = isset($tempURL[ 1 ]) ? explode( ',', $tempURL[ 1 ] ) : array();
				if ( isset($tempURL[ 1 ]) && !empty( $tempURL[ 1 ] ) )
					$cachedASINs = array_unique( array_merge( $cachedASINs, $tempASINs ) );
			}
		}
	}
	$final = true;
	if ( is_array( $items ) ) {
		foreach ( $items as $k => $item ) {
			if ( in_array( $item, $cachedASINs ) )
				$final = false;
		}
		return $final;
	} else {
		if ( in_array( $items, $cachedASINs ) )
			return true;
	}
	return false;
}
function get_appip_request_array( $getArr ) {
	if ( !is_array( $getArr ) || empty( $getArr ) )
		return array( 'requests', 'asins' );
	$request = array();
	$cache_key = array();
	$defaults = array(
		'asins' => array(),
		'params' => array(),
		'privatekey' => '',
		'privatekey' => '',
		'reqtype' => 'asin',
	);
	extract( shortcode_atts( $defaults, $getArr ) );
	if ( $reqtype == 'asin' ) {
		$requestedASINs = array();
		if ( is_array( $asins ) && !empty( $asins ) ) {
			foreach ( $asins as $akey => $aval ) {
				$canquery = array();
				foreach ( $params as $param => $value ) {
					$fValue = str_replace( "%7E", "~", rawurlencode( ( $param == 'ItemId' ? implode( ',', $aval ) : $value ) ) );
					$fParam = str_replace( "%7E", "~", rawurlencode( $param ) );
					$canquery[] = $fParam . "=" . $fValue;
					$canqueryKey[ $fParam ] = $fValue;
				}
				ksort( $canqueryKey );
				$request[] = implode( "&", $canquery );
				$cache_key[] = 'cache_' . implode( "_", $canqueryKey );
				$requestedASINs[] = implode( ',', $aval );
			}
		}
		return array( 'requests' => $getArr, 'asins' => $requestedASINs, 'cache_keys' => $cache_key );
	} else {
		//search
		$canquery = array();
		foreach ( $params as $param => $value ) {
			$fValue = str_replace( "%7E", "~", rawurlencode( $value ) );
			$fParam = str_replace( "%7E", "~", rawurlencode( $param ) );
			$canquery[] = $fParam . "=" . $fValue;
		}
		$request[] = implode( "&", $canquery );
		return array( 'requests' => $getArr, 'asins' => array() );
	}
}
function amazon_appip_register_button( $buttons ) {
	array_push( $buttons, "|", "amazon_products" );
	return $buttons;
}
function amazon_appip_add_plugin( $plugin_array ) {
	$plugin_array[ 'amazon_products' ] = plugins_url( '/js/wysiwyg/amazon_editor.js', dirname( __FILE__ ) );
	return $plugin_array;
}
function amazon_appip_editor_button() {
	if ( is_admin() ) {
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', 'amazon_appip_add_plugin' );
			add_filter( 'mce_buttons', 'amazon_appip_register_button' );
		}
	}
}
class amazonAPPIP_ButtonURLFix2016 {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'apipp_plugin_menu' ), 11 );
		/* uploaded button overeride */
		add_filter( 'appip_amazon_button_url', array( $this, 'change_the_button' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'init', array( $this, 'appip_parse' ) );
		/* Main Override to fix new Buttons - This can be overridden if a button is uploaded */
		add_filter( 'appip_amazon_button_url', array( $this, 'button_url_for_locale' ), 5, 3 );
	}

	public function apipp_plugin_menu() {
		add_submenu_page( 'apipp-main-menu', __( "Button Settings", 'amazon-product-in-a-post-plugin' ), __( 'Button Settings', 'amazon-product-in-a-post-plugin' ), 'manage_options', 'apipp_plugin-button-url', array( $this, 'apipp_options_button_url_page' ) );
	}

	public function change_the_button( $url ) {
		$button_URL = get_option( 'amazon-button-image', '' );
		if ( $button_URL != '' )
			return $button_URL;
		return $url;
	}

	public function admin_enqueue( $hook ) {
		if ( strpos($hook, 'page_apipp_plugin-button-url') !== false ) {
			wp_enqueue_media();
			//wp_enqueue_style( 'wp-color-picker');
			wp_enqueue_script( 'appip_admin_buttons', plugin_dir_url( dirname( __FILE__ ) ) . 'js/amazon-button-admin.js', array( 'media-upload' ) );
		}
	}

	public function button_url_for_locale( $url = '', $button = '', $locale = '' ) {
		$button = 'new-buyamzon-button-' . $locale . '.png';
		$newurl = plugins_url( '/images/' . $button, dirname( __FILE__ ) );
		return $newurl;
	}
	public function add_custom_button(){

	}

	public function appip_parse() {
		if ( is_admin() && isset( $_POST[ 'amazon-button-image' ] ) && $_POST[ 'amazon-button-image' ] !== '' ) {
			check_admin_referer( 'appip_admin_button_url', 'security' );
			update_option( 'amazon-button-image', esc_url_raw( $_POST[ 'amazon-button-image' ] ) );
			wp_redirect( admin_url( 'admin.php?page=apipp_plugin-button-url' ), 301 );
			exit;
		} elseif ( is_admin() && isset( $_POST[ 'amazon-button-image' ] ) && $_POST[ 'amazon-button-image' ] === '' ) {
			check_admin_referer( 'appip_admin_button_url', 'security' );
			update_option( 'amazon-button-image', '' );
			wp_redirect( admin_url( 'admin.php?page=apipp_plugin-button-url' ), 301 );
			exit;
		} elseif ( is_admin() && isset( $_REQUEST[ 'azact' ] ) && isset( $_REQUEST[ 'cbutid' ] ) && $_REQUEST[ 'azact' ] == 'delete' && $_REQUEST[ 'cbutid' ] != '' ) {
			$delbuttid = sanitize_text_field($_REQUEST[ 'cbutid' ]);
			$custom_options = get_option( 'amazon-button-custom','') != '' ? json_decode(get_option( 'amazon-button-custom'),true) : array();
			if(isset($custom_options[$delbuttid])){
				unset($custom_options[$delbuttid]);
				update_option( 'amazon-button-custom',wp_json_encode($custom_options));
				$edit_url_string = '&btn_del=true';
				wp_redirect( admin_url( 'admin.php?page=apipp_plugin-button-url'.$edit_url_string  ), 301 );
			}else{
				$edit_url_string = '&btn_del_err=true';
				wp_redirect( admin_url( 'admin.php?page=apipp_plugin-button-url'.$edit_url_string  ), 301 );
			}
			exit;
		} elseif ( is_admin() && isset( $_POST[ 'submit-button-custom' ] ) && $_POST[ 'submit-button-custom' ] !== '' ) {
			check_admin_referer( 'appip_admin_button_custom', 'custom_security' );
			$custom_options = get_option( 'amazon-button-custom','') != '' ? json_decode(get_option( 'amazon-button-custom'),true) : array();
			$num = count($custom_options);
			$edit_url_string = '';
			if(isset($_POST[ 'amazon-button-custom_name' ]) && isset($_POST[ 'amazon-button-custom_name' ]) != ''){
				$orig_name = isset($_POST[ 'custom_button_orig_id' ]) && $_POST[ 'custom_button_orig_id' ] != '' ? sanitize_text_field($_POST[ 'custom_button_orig_id' ]) : ''; // orig name
				$custom_name = isset($_POST[ 'amazon-button-custom_name' ]) ? sanitize_title($_POST[ 'amazon-button-custom_name' ]) : 'custom-button-'.$num; // name
				$custom_class = isset($_POST[ 'amazon-button-custom_class' ]) ? sanitize_text_field($_POST[ 'amazon-button-custom_class' ]) : ''; // class name
				$custom_button_text = isset($_POST[ 'amazon-button-custom_text' ]) ? sanitize_text_field($_POST[ 'amazon-button-custom_text' ]) : 'Read More'; // string
				$custom_dropdown_text = isset($_POST[ 'amazon-button-custom_ddtext' ]) ? sanitize_text_field($_POST[ 'amazon-button-custom_ddtext' ]) : 'Custom Button '.$num; // string
				$custom_button_text_reg_color = isset($_POST[ 'amazon-button-custom_txt-reg-color' ]) && $_POST[ 'amazon-button-custom_txt-reg-color' ] != '' ? sanitize_text_field($_POST[ 'amazon-button-custom_txt-reg-color' ]) : '#ffffff'; // hex
				$custom_button_text_hov_color = isset($_POST[ 'amazon-button-custom_txt-hov-color' ]) && $_POST[ 'amazon-button-custom_txt-hov-color' ] != '' ? sanitize_text_field($_POST[ 'amazon-button-custom_txt-hov-color' ]) : '#ffffff'; // hex
				$custom_button_reg_color = isset($_POST[ 'amazon-button-custom_btn-reg-color' ]) && $_POST[ 'amazon-button-custom_btn-reg-color' ] != '' ? sanitize_text_field($_POST[ 'amazon-button-custom_btn-reg-color' ]) : '#444444'; // hex
				$custom_button_hov_color = isset($_POST[ 'amazon-button-custom_btn-hov-color' ]) && $_POST[ 'amazon-button-custom_btn-hov-color' ] != '' ? sanitize_text_field($_POST[ 'amazon-button-custom_btn-hov-color' ]) : '#666666'; // hex
				$custom_button_round = isset($_POST[ 'amazon-button-custom_btn-rnd' ]) && $_POST[ 'amazon-button-custom_btn-rnd' ] != '' ? sanitize_text_field($_POST[ 'amazon-button-custom_btn-rnd' ]) : '2'; // in px
				$azm_isedit = $orig_name != '' ? true : false;
				if(isset($custom_options[$custom_name]) && !$azm_isedit ) // create unique if present
					$custom_name = $custom_name .'_'. md5($custom_name . date('Y-m-d H:i:s'));
				if($azm_isedit){
					$edit_url_string = '&btn_edit=true';
					if($orig_name != $custom_name )
						unset($custom_options[$orig_name]);
				}
				$custom_options[$custom_name] = array(
					'name' => $custom_name,
					'class' => $custom_class,
					'text' => $custom_button_text,
					'ddtext' => $custom_dropdown_text,
					'txt-reg-color' => $custom_button_text_reg_color,
					'txt-hov-color' => $custom_button_text_hov_color,
					'btn-reg-color' => $custom_button_reg_color,
					'btn-hov-color' => $custom_button_hov_color,
					'btn-rnd' => $custom_button_round,
				);
				update_option( 'amazon-button-custom', wp_json_encode($custom_options) );
			}else{
				$edit_url_string = '&btn_edit_err=true';
			}
			wp_redirect( admin_url( 'admin.php?page=apipp_plugin-button-url'.$edit_url_string ), 301 );
			exit;
		}
	}

	public function apipp_options_button_url_page() {
		$button = esc_url_raw( get_option( 'amazon-button-image', '' ) );
		$buttonImg = '';
		if ( $button != '' ) {
			$buttonImg = '<p><strong>' . __( 'Current Button Image:', 'amazon-product-in-a-post-plugin' ) . '</strong></p><p id="selected-image"><img src="' . $button . '" alt="amazon-button"></p>';
		} else {
			$buttonImg = '<p id="selected-image"></p>';
		}
		$radmore_text = _x('Read More','Button text','amazon-product-in-a-post-plugin');
		$buyfrom_text= _x('Buy from Amazon','Button text - Locale will be appended to this text. i.e., Buy From Amazon.com','amazon-product-in-a-post-plugin').'.'.APIAP_LOCALE;
		$custom_options = get_option( 'amazon-button-custom','') != '' ? json_decode(get_option( 'amazon-button-custom'),true) : array();
		add_thickbox();
		$css = array();
		$cbut = array();
		$btnID = isset($_GET['cbutid']) && $_GET['cbutid'] != '' ? sanitize_text_field($_GET['cbutid']) : '';
		$buttonAction = '';
		$buttonmsg = isset($_GET['btn_edit']) && $_GET['btn_edit'] == 'true' ? true : false;
		$delmsg = isset($_GET['btn_del']) && $_GET['btn_del'] == 'true' ? true : false;
		$delerrmsg = isset($_GET['btn_del_err']) && $_GET['btn_del_err'] == 'true' ? true : false;
		$editerrmsg = isset($_GET['btn_edit_err']) && $_GET['btn_edit_err'] == 'true' ? true : false;
		if(isset($_GET['azact'])){
			$ba = sanitize_text_field($_GET['azact']);
			if( $ba === 'edit'){
				$buttonAction = 'edit';
			}elseif($ba === 'delete'){
				$buttonAction = 'delete';
			}
		}
		if(is_array($custom_options) && ! empty($custom_options)){
			$cbuttons = array();
			$cbnames = array();
			$cscodes = array();
			foreach($custom_options as $cok => $cov){
				$cov['name'] = isset($cov['name '] ) && $cov['name '] != ''  ? $cov['name '] : $cov['name'];
				$btrxt = isset($cov['ddtext']) != '' && $cov['ddtext'] != '' ? $cov['ddtext'] : $cov['name'];
				$cbnames[] = '<span class="dashicons dashicons-edit" data-az_button_id="'.$cov['name'].'" title="'.__( 'edit', 'amazon-product-in-a-post-plugin' ).'"></span> '. esc_attr($btrxt).' <span class="dashicons dashicons-trash" data-az_button_id="'.$cov['name'].'" title="'.__( 'delete', 'amazon-product-in-a-post-plugin' ).'"></span>';
				$cscodes[] = 'button="'.$cok.'"';
				$cbuttons[] = '<span class="amazon__btn-'.$cok.(isset($cov['class'] ) && $cov['class'] != '' ? ' '.$cov['class']  : '').' amazon__price--button--style">'.$cov['text'].'</span>';
				$css[] = '.amazon__btn-'.$cok.' {color: '.$cov['txt-reg-color'].';background-color: '.$cov['btn-reg-color'].';border-radius: '.$cov['btn-rnd'].'px;}';
				$css[] = '.amazon__btn-'.$cok.':hover {color: '.$cov['txt-hov-color'].';background-color: '.$cov['btn-hov-color'].';}';
			}
			$cbut[] = '<tr>';
			$cbut[] = '<td><strong>'.__( 'Custom Buttons', 'amazon-product-in-a-post-plugin' ).':</strong></td><td colspan="2"></td>';
			$cbut[] = '</tr>';
			$cbut[] = '<tr>';
			$cbut[] = '<td class="button-names">'.implode("<br>",$cbnames).'</td>';
			$cbut[] = '<td class="button-name">'.implode("<br>",$cscodes).'</td>';
			$cbut[] = '<td class="examples">'.implode("<br>",$cbuttons).'</td>';
			$cbut[] = '</tr>';
		}
		echo '<div class="wrap">';
		echo '<h1>' . __( 'Amazon Button Settings', 'amazon-product-in-a-post-plugin' ) . '</h1>';
		if($editerrmsg === true)
			echo '<div id="setting-error-settings_error" class="notice notice-error settings-error is-dismissible"><p><strong>'.__( 'Error. Button changes could not be saved.', 'amazon-product-in-a-post-plugin' ).'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		if($delerrmsg === true)
			echo '<div id="setting-error-settings_error" class="notice notice-error settings-error is-dismissible"><p><strong>'.__( 'Error. Button could not be deleted.', 'amazon-product-in-a-post-plugin' ).'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		if($buttonmsg === true)
			echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"><p><strong>'.__( 'Custom button settings saved.', 'amazon-product-in-a-post-plugin' ).'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		if($delmsg === true)
			echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"><p><strong>'.__( 'Custom button deleted.', 'amazon-product-in-a-post-plugin' ).'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		echo '	<div id="wpcontent-inner" class="amazon-button-page">';
		if($buttonAction == 'edit'){
			echo '	<p>' . __( 'Edit button settings below and save changes.', 'amazon-product-in-a-post-plugin' ) . '</p>';
		}else{
			echo '<h2 class="separator">' . __( 'Custom Image Button', 'amazon-product-in-a-post-plugin' ) . '</h2>';
			echo '	<p>' . __( 'Set New Default button to use with all Amazon products. Clear the upload field and save blank to delete button.', 'amazon-product-in-a-post-plugin' ) . '</p>';
		}
		echo '
		'.($buttonAction == 'edit' ? '' : '
		<form action="" method="post" id="amz_button_form">
			' . $buttonImg . '
			<input type="hidden" name="security" value="' . wp_create_nonce( 'appip_admin_button_url' ) . '"/>
			<p><input id="amz-button-image" name="amazon-button-image" value="' . $button . '" class="regular-text" type="text">&nbsp;<input type="button" class="upload_image_button button-secondary' . ( $button !== '' ? ' hidden' : '' ) . '" name="amazon-button-upload-image-button" value="' . __( 'Upload Image', 'amazon-product-in-a-post-plugin' ) . '"> <input type="button" style="margin-left: 5px;" class="delete_image_button button-secondary' . ( $button === '' ? ' hidden' : '' ) . '" name="amazon-button-delete-image-button" value="' . __( 'Remove Image', 'amazon-product-in-a-post-plugin' ) . '"></p>
			<p class="separator"><input type="submit" name="submit-button-image" id="submit-button-image" value="' . __( 'Save Changes', 'amazon-product-in-a-post-plugin' ) . '" class="button button-primary" /></p>
		</form>
		').'
		<script>
		jQuery("document").ready(function(){
			jQuery(".button-names span.dashicons-edit").on( "click", function(){
				var $butid = jQuery(this).data("az_button_id");
				window.location.replace("admin.php?page=apipp_plugin-button-url&cbutid="+$butid+"&azact=edit&nc='.md5(date("Y-m-d H:i:s")).'");
			});
			jQuery(".button-names span.dashicons-trash").on( "click", function(){
				var $butidd = jQuery(this).data("az_button_id");
				window.location.replace("admin.php?page=apipp_plugin-button-url&cbutid="+$butidd+"&azact=delete&nc='.md5(date("Y-m-d H:i:s")).'");
			});
			jQuery("#add-custom").on( "click", function(){
				jQuery("form.add-custom-button").toggle();
			});
		});
		</script>
		'.( $buttonAction == 'edit' ? '<h2 class="separator">' . __( 'Edit Custom Button', 'amazon-product-in-a-post-plugin' ) . '</h2>' : '<h2 class="separator">' . __( 'Other Button Options', 'amazon-product-in-a-post-plugin' ) . '</h2>').'
		<style>
			.button-rounded{-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;}
			/* default */	
			.amazon__price--button--style, 
			.amazon__price--button--style:visited {background-color: #444;padding: 10px 20px;margin: 5px 0;display: inline-block;text-decoration: none;color: #fff;-moz-transition: all .5s ease;-webkit-transition: all .5s ease;transition: all .5s ease;}
			.amazon__price--button--style:hover {cursor:pointer;background-color: #666;text-decoration: none;color: #fff;}
			.amazon__price--button--style:focus {color: #fff;background-color: #595959;outline: 0;-moz-box-shadow: 0 0 5px #9c9c9c;-webkit-box-shadow: 0 0 5px #9c9c9c;box-shadow: 0 0 5px #9c9c9c;text-decoration: none;}
			.amazon__price--button--style:active {color: #fff;background-color: #595959;outline: 0;text-decoration: none;-moz-box-shadow:0px 3px 9px rgba(0, 0, 0, 0.43) inset;-webkit-box-shadow:0px 3px 9px rgba(0, 0, 0, 0.43) inset;box-shadow:0px 3px 9px rgba(0, 0, 0, 0.43) inset;}
			/* blue */
			.amazon__btn--blue,
			.amazon__btn--blue:visited{color: #fff;background-color: #0085ba;}
			.amazon__btn--blue:hover {color: #fff;background-color: #008ec2;}
			.amazon__btn--blue:focus {color: #fff;background-color: #0073aa;-moz-box-shadow: 0 0 5px #2196F3;-webkit-box-shadow: 0 0 5px #2196F3;box-shadow: 0 0 5px #2196F3;}
			.amazon__btn--blue:active{color: #fff;background-color: #0073aa;}
			/* red */
			.amazon__btn--red,
			.amazon__btn--red:visited{color: #fff;background-color: #e10505;}
			.amazon__btn--red:hover {color: #fff;background-color: #f00;}
			.amazon__btn--red:focus {color: #fff;background-color: #a70707;-moz-box-shadow: 0 0 5px #ff338e;-webkit-box-shadow: 0 0 5px #ff338e;box-shadow: 0 0 5px #ff338e;}
			.amazon__btn--red:active{color: #fff;background-color: #a70707;}
			/* green */
			.amazon__btn--green,
			.amazon__btn--green:visited{color: #fff;background-color: #4aa74e;}
			.amazon__btn--green:hover {color: #fff;background-color: #2f8d33;}
			.amazon__btn--green:focus {color: #fff;background-color: #17851c;-moz-box-shadow: 0 0 5px #8BC34A;-webkit-box-shadow: 0 0 5px #8BC34A;box-shadow: 0 0 5px #8BC34A;} 
			.amazon__btn--green:active{color: #fff;background-color: #17851c;}
			/* Custom */
			'.implode("\n",$css).'
			.button-sel-table td, 
			.button-sel-table th{padding:1px 4px;border:1px solid #ccc;vertical-align:top;background-color:#f5f5f5;} 
			.button-sel-table td.examples{text-align:center;}
			.button-sel-table td.button-name {font-family:monospace;}
			span.dashicons[data-az_button_id] {font-size: 17px;cursor: pointer;}
			span.dashicons[data-az_button_id]:hover {color:#007cba;}
			.hidden{display: none;}
			form#amz_button_form .separator{display: block !important;padding-bottom: 20px;margin-bottom: 20px;border-bottom: 1px solid #ddd;}
			form#amz_button_form.add-custom-button span.btn-label-add-new {width: 30%;display: table-cell;padding:2px;}
			form#amz_button_form.add-custom-button span.btn-input-add-new {width: auto;display: table-cell;padding:2px;}
			form#amz_button_form.add-custom-button span.btn-desc-add-new {width: auto;display: table-cell;padding:2px;}
			form#amz_button_form.add-custom-button {max-width: 800px;display: table;margin: 0 0 1em 0}
			form#amz_button_form p{display: table-row;}
			form#amz_button_form.add-custom-button input[type="number"]{width:60px;}
			form#amz_button_form.add-custom-button input[name$="-color"]{width:80px;}

			@media screen and (max-width:550px) {
				a.amazon__price--button--style {max-width: 90%;margin: 5px auto;}
			}
		</style>
		<p>' . __( 'The buttons below can be used in blocks and shortcodes. You can also add your own custom buttons to use in any shortcode or block by clicking "Add Custom Button".', 'amazon-product-in-a-post-plugin' ) . '</p>		<input type="button" style="'.( $buttonAction == 'edit' ? 'display:none;' : '').'margin-bottom:1em;" name="add-custom" id="add-custom" value="' . __( 'Add Custom Button', 'amazon-product-in-a-post-plugin' ) . '" class="button button-primary" />
		<form action="" method="post" id="amz_button_form" class="add-custom-button" '.( $buttonAction == 'edit' ? '' : 'style="display:none;"').'>
			<input type="hidden" name="custom_security" value="' . wp_create_nonce( 'appip_admin_button_custom' ) . '"/><input type="hidden" name="custom_button_orig_id" value="'.( $buttonAction == 'edit' ? esc_attr($btnID) : '').'">
			<p><span class="btn-label-add-new"><strong>' . __( 'Name ID', 'amazon-product-in-a-post-plugin' ) . ':</strong><span style="color:#f00;">*</span> </span><span class="btn-input-add-new"><input id="amazon-button-custom_name" name="amazon-button-custom_name" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['name ']) ? esc_attr($custom_options[$btnID]['name ']) :(isset( $custom_options[$btnID]['name']) ? esc_attr( $custom_options[$btnID]['name']): '')).'" placeholder="my-custom-button" class="regular-text" type="text" required> <em>(required) no spaces</em></span></p>
			<p><span class="btn-label-add-new"><strong>' . __( 'Name/Dropdown Name', 'amazon-product-in-a-post-plugin' ) . ':</strong><span style="color:#f00;">*</span> </span><span class="btn-input-add-new"><input id="amazon-button-custom_ddtext" name="amazon-button-custom_ddtext" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['ddtext']) ? esc_attr($custom_options[$btnID]['ddtext']) : '').'" placeholder="i.e., Custom Button Green Rounded" class="regular-text" type="text" required> <em>(required)</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Text', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_text" name="amazon-button-custom_text" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['text']) ? esc_attr($custom_options[$btnID]['text']) : '').'" placeholder="Read More" class="regular-text" type="text"> <em>(optional)</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'CSS Class', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_class" name="amazon-button-custom_class" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['class']) ? esc_attr($custom_options[$btnID]['class']) : '').'" placeholder="i.e., custom-button-green" class="regular-text" type="text"> <em>(optional)</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Text Color', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_txt-reg-color" name="amazon-button-custom_txt-reg-color" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['txt-reg-color']) ? esc_attr($custom_options[$btnID]['txt-reg-color']) : '').'" placeholder="#FFFFFF" class="regular-text" type="text"> <em>default #ffffff</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Text Hover Color', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_txt-hov-color" name="amazon-button-custom_txt-reg-color" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['txt-hov-color']) ? esc_attr($custom_options[$btnID]['txt-hov-color']) : '').'" placeholder="#FFFFFF" class="regular-text" type="text"> <em>default #ffffff</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Color', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_btn-reg-color" name="amazon-button-custom_btn-reg-color" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['btn-reg-color']) ? esc_attr($custom_options[$btnID]['btn-reg-color']) : '').'" placeholder="#444444" class="regular-text" type="text"> <em>default #444444</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Hover Color', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_btn-hov-color" name="amazon-button-custom_btn-hov-color" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['btn-hov-color']) ? esc_attr($custom_options[$btnID]['btn-hov-color']) : '').'" placeholder="#666666" class="regular-text" type="text"> <em>default #666666</em></span></p>
			<p><span class="btn-label-add-new">' . __( 'Button Border Radius', 'amazon-product-in-a-post-plugin' ) . ': </span><span class="btn-input-add-new"><input id="amazon-button-custom_btn-rndr" name="amazon-button-custom_btn-rnd" value="'.( $buttonAction == 'edit' && isset( $custom_options[$btnID]['btn-rnd']) ? esc_attr($custom_options[$btnID]['btn-rnd']) : '').'" placeholder="2" class="regular-text" type="number">px <em>default 2px (put 0 for no rounding)</em></span></p><br>
			<p class="separator"><input type="submit" name="submit-button-custom" id="submit-button-custom" value="' .( $buttonAction == 'edit' ? __( 'Save Button Changes', 'amazon-product-in-a-post-plugin' ) : __( 'Add Button', 'amazon-product-in-a-post-plugin' ) ). '" class="button button-primary" /></p>
		</form>

		<div align="left">
			<table class="button-sel-table" border="0" cellpadding="0" style="border-collapse: collapse;'.( $buttonAction == 'edit' ? 'display:none;' : '').'" width="auto">
				<tr>
					<th style="width:25%;text-align:left;">'.__('Button Name','amazon-product-in-a-post-plugin').'</th>
					<th style="width:50%;text-align:left;">'.__('Shortcode parameter to use:','amazon-product-in-a-post-plugin').'</th>
					<th style="width:25%;text-align:center;">'.__('Output','amazon-product-in-a-post-plugin').'</th>
				</tr>
				'.implode("\n",$cbut).'
				<tr>
					<td rowspan="4">'.$radmore_text.'</td>
					<td rowspan="4" class="button-name">button="read-more"<br>button="read-more-red"<br>button="read-more-blue"<br>button="read-more-green"</td>
					<td class="examples"><span class="amazon__btn amazon__price--button--style">'.$radmore_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--red amazon__price--button--style">'.$radmore_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--blue amazon__price--button--style">'.$radmore_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--green amazon__price--button--style">'.$radmore_text.'</span></td>
				</tr>
				<tr>
					<td rowspan="4">'.$buyfrom_text.'</td>
					<td rowspan="4" class="button-name">button="buy-from"<br>button="buy-from-red"<br>button="buy-from-blue"<br>button="buy-from-green"</td>
					<td class="examples"><span class="amazon__btn amazon__price--button--style">'.$buyfrom_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--red amazon__price--button--style">'.$buyfrom_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--blue amazon__price--button--style">'.$buyfrom_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--green amazon__price--button--style">'.$buyfrom_text.'</span></td>
				</tr>
				<tr>
					<td rowspan="2">'.__('rounded buttons','amazon-product-in-a-post-plugin').'</td>
					<td rowspan="2" class="button-name">add "rounded" to end of code<br>i.e., button="read-more-rounded"<br>button="buy-from-red-rounded"</td>
					<td class="examples"><span class="amazon__btn amazon__price--button--style button-rounded">'.$radmore_text.'</span></td>
				</tr>
				<tr>
					<td class="examples"><span class="amazon__btn--red amazon__price--button--style button-rounded">'.$buyfrom_text.'</span></td>
				</tr>
			</table>
		</div>
		';

		echo '	</div>';
		echo '</div>';
	}
}
new amazonAPPIP_ButtonURLFix2016();