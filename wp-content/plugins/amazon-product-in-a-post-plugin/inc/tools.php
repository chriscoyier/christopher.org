<?php
// Tools
global $appipBulidBox;
//ACTIONS
add_action( 'init', 'apipp_parse_new', 1 );
add_action( 'admin_menu', 'apipp_plugin_menu' );
add_action( 'network_admin_notices', 'appip_warning_notice' );
add_action( 'admin_notices', 'appip_warning_notice' );
//add_filter( 'contextual_help', 'appip_plugin_help', 10, 3 ); // OLD HOOK
add_filter( 'current_screen', 'appip_plugin_help', 10 ); // NEW HOOK

//FUNCTIONS
function is_gutenberg_page_appip() {
  if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
    // before WP 5.0 or using plugin.
    return true;
  }
  $current_screen = get_current_screen();
  if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
    // Gutenberg page on 5+.
    return true;
  }
  return false;
}

function add_appip_meta_posttype_support( $posttypes = array() ) {
  if ( function_exists( 'add_meta_box' ) && function_exists( 'amazonProductInAPostBox1' ) ) {
    if ( is_array( $posttypes ) && !empty( $posttypes ) ) {
      //$context = apply_filters( 'appip_metabox_context', ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) ? 'side' : 'normal' );
      //$priority = apply_filters( 'appip_metabox_priority', ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) ? 'default' : 'high' );
      $context = apply_filters( 'appip_metabox_context', ( is_gutenberg_page_appip() ? 'side' : 'normal' ) );
      $priority = apply_filters( 'appip_metabox_priority', ( is_gutenberg_page_appip() ? 'default' : 'high' ) );
      foreach ( $posttypes as $key => $type ) {
        add_meta_box( 'amazonProductInAPostBox_' . $type, __( 'Amazon Product In a Post Settings', 'amazon-product-in-a-post-plugin' ), 'amazonProductInAPostBox1', $type, $context, $priority );
      }
    }
  }
}

function appip_plugin_block_categories( $categories, $post ) {
  //if ( $post->post_type !== 'post' ) {
  //return $categories;
  // }
  return array_merge(
    $categories,
    array(
      array(
        'slug' => 'amazon-product-category',
        'title' => __( 'Amazon Products', 'my-plugin' ),
        'icon' => 'store',
      ),
    )
  );
}
add_filter( 'block_categories', 'appip_plugin_block_categories', 10, 2 );

function appip_post_type_support() {
  global $APIAP_USE_GUTENBERG;
  $METABOXsettings = ( bool )get_option( 'apipp_show_metaboxes', false );
  if ( $METABOXsettings || ( !$METABOXsettings && !$APIAP_USE_GUTENBERG ) ) {
    $types = apply_filters( 'appip_meta_posttypes_support', array( 'page', 'post' ) );
    add_appip_meta_posttype_support( $types );
  }
}
add_filter( 'admin_enqueue_scripts', 'appip_post_type_support', 20 );

function appip_add_product_meta_support( $types = array() ) {
  $types[] = 'product';
  return $types;
}
add_filter( 'appip_meta_posttypes_support', 'appip_add_product_meta_support', 10 );


function appip_plugin_help( $screen ) {
  $plugin_donate = 0;
  $screen_id = $screen->id;
  $title = '';
  $contextual_help = '';
  $title2 = '';
  $contextual_help2 = '';
  switch ( $screen_id ) {
    case 'toplevel_page_apipp-main-menu':
      $title = __( 'Plugin Settings', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Contextual Help Coming Soon.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp-add-new':
      $title = __( 'New Product', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Contextual Help Coming Soon.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp-main-menu':
      $title = __( 'Plugin Settings', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Contextual Help Coming Soon.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp_plugin_admin':
      $title = __( 'Plugin Settings', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Contextual Help Coming Soon.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp_plugin-shortcode':
      $title = __( 'Shortcode Usage', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Shortcodes have been a WordPress staple for many years. They are designed to be used as "placeholder" content and are rendered on the front of the site when it is viewed. The plugin has several shortcodes available. See the tabs below for more detailed information about each shortcode and their usage.', 'amazon-product-in-a-post-plugin' );
      $title2 = __( 'Block Usage', 'amazon-product-in-a-post-plugin' );
      $contextual_help2 = __( 'Gutenberg Blocks make the plugin extremely useful for those using the Gutenberg Editor in WordPress. The blocks are designed to make it easy for you to customizing settings without the need to use shortcodes. If you have issues with any block, please see the "Using Amazon Product In A Post Blocks" under the "Gutenberg Blocks" tab for troubleshooting instructions.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp_plugin-faqs':
      $title = __( 'FAQs/Help', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'Contextual Help Coming Soon.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
    case 'amazon-product_page_apipp-cache-page':
      $title = __( 'Product Cache', 'amazon-product-in-a-post-plugin' );
      $contextual_help = __( 'The Product Cache page is designed to show you the response from the Amazon Product Advertising API (PA API). If there are errors, the cache line will show the errors for that requast. In the event of an error, try to fix the issues it references and then clear the cache and try the request again.', 'amazon-product-in-a-post-plugin' );
      $plugin_donate = 1;
      break;
  }
  if ( $contextual_help != '' && $title != '' ) {
    $screen->add_help_tab( array( 'id' => 'appip_help', 'title' => $title, 'content' => '<p>' . $contextual_help . '</p>' ) );
  }
  if ( $contextual_help2 != '' && $title2 != '' ) {
    $screen->add_help_tab( array( 'id' => 'appip_help2', 'title' => $title2, 'content' => '<p>' . $contextual_help2 . '</p>' ) );
  }
  if ( $plugin_donate == 1 ) {
    $screen->add_help_tab( array( 'id' => 'appip_aboutus', 'title' => __( 'About Us', 'amazon-product-in-a-post-plugin' ), 'content' => '<p>'. APIAP_OWNER_BUSINESS . __( ' develops custom WordPress Themes and Plugins for clients who need a more individualized look, but still want the simplicity of a WordPress website.', 'amazon-product-in-a-post-plugin' ) ) );
    $screen->set_help_sidebar(
      '<p><strong>' . __( 'Help Us Out:', 'amazon-product-in-a-post-plugin' ) . '</strong></p>' .
      '<a href="'.APIAP_DONATION_URL.'" target="_blank">' . __( 'Donate to this Plugin', 'amazon-product-in-a-post-plugin' ) . '</a><br>' .
      '<a href="'.APIAP_OTHER_PLUGINS_URL.'" target="_blank">' . __( 'Other Plugins', 'amazon-product-in-a-post-plugin' ) . '</a>'
    );
  }
  return $screen;
}

function appip_warning_notice() {
  if ( isset( $_REQUEST[ 'dismissmsg' ] ) && $_REQUEST[ 'dismissmsg' ] == '1' ) {
    update_option( 'appip_dismiss_msg', 1 );
  }
  $appip_publickey = APIAP_PUB_KEY;
  $appip_privatekey = APIAP_SECRET_KEY;
  $appip_partner_id = APIAP_ASSOC_ID;
  $appip_dismiss = get_option( 'appip_dismiss_msg', 0 );
  if ( $appip_dismiss == 0 ) {
    if ( $appip_publickey == '' || $appip_privatekey == '' ) {
      echo '<div class="error" style="position:relative;"><h2><strong>' . __( 'Amazon Product in a Post Important Message!', 'amazon-product-in-a-post-plugin' ) . '</strong></h2><p>' . __( 'Please note: You need to add your Access Key ID and Secrect Access Key to the <a href="admin.php?page=apipp_plugin_admin">options page</a> before the plugin will display any Amazon Products!<a href="admin.php?page=apipp_plugin_admin&dismissmsg=1" style="position:absolute;top:0;right:0;padding:3px 10px;display:block;">dismiss</a>', 'amazon-product-in-a-post-plugin' ) . '</p></div>';
    } elseif ( $appip_partner_id == '' ) {
      echo '<div class="error" style="position:relative;"><h2><strong>' . __( 'Amazon Product in a Post Important Message!', 'amazon-product-in-a-post-plugin' ) . '</strong></h2><p>' . __( 'You need to enter your Amazon Partner ID in order to get credit for any products sold. <a href="admin.php?page=apipp_plugin_admin">enter your partner id here</a><a href="admin.php?page=apipp_plugin_admin&dismissmsg=1" style="position:absolute;top:0;right:0;padding:3px 10px;display:block;">dismiss</a>', 'amazon-product-in-a-post-plugin' ) . '</p></div>';
    }
  }
}

function apipp_parse_new() { //Custom Save Post items for Quick Add
  if ( isset( $_POST[ 'createpost' ] ) || isset( $_POST[ 'createpost_edit' ] ) ) { //form saved
    global $post;
    $teampappcats = array();
    $totalcategories = isset( $_POST[ 'post_category_count' ] ) ? absint( $_POST[ 'post_category_count' ] ) : 0;
    $post_stat = isset( $_POST[ 'post_status' ] ) ? sanitize_text_field( $_POST[ 'post_status' ] ) : 'draft';
    $post_type = isset( $_POST[ 'post_type' ] ) ? sanitize_text_field( $_POST[ 'post_type' ] ) : 'post';
    $splitASINs = isset( $_POST[ 'split_asins' ] ) && ( int )$_POST[ 'split_asins' ] == 1 ? true : false;
    $allowed_tags = wp_kses_allowed_html( $post_type );
    $ASIN = isset( $_POST[ 'amazon-product-single-asin' ] ) ? str_replace( ', ', ',', sanitize_text_field( $_POST[ 'amazon-product-single-asin' ] ) ) : '';
    $amzArr = array();
    $amzreq = '';

    if ( $ASIN != '' ) {
      $ASIN = ( is_array( $ASIN ) && !empty( $ASIN ) ) ? implode( ',', $ASIN ) : $ASIN; //valid ASIN or ASINs
      $asinR = explode( ",", $ASIN );
      $asinArr = $asinR;
      $appip_publickey = APIAP_PUB_KEY;
      $appip_privatekey = APIAP_SECRET_KEY;
      $appip_partner_id = APIAP_ASSOC_ID;
      $locale = APIAP_LOCALE;

      /* NEW */
      $Regions = __getAmz_regions();
      $region = $Regions[ $locale ][ 'RegionCode' ];
      $host = $Regions[ $locale ][ 'Host' ];
      $accessKey = $appip_publickey;
      $secretKey = $appip_privatekey;
      $payloadArr = array();
      $payloadArr[ 'ItemIds' ] = $asinR;
      $payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
      $payloadArr[ 'PartnerTag' ] = $appip_partner_id;
      $payloadArr[ 'PartnerType' ] = 'Associates';
      $payloadArr[ 'Marketplace' ] = 'www.amazon.' . $locale;
      $payload = json_encode( $payloadArr );
      $awsv5 = new Amazon_Product_Request_V5( null, null, null, null, 'single' );
      /* END NEW */

      $skipCache = false;
      $pxmlNew = amazon_plugin_aws_signed_request( $locale, array( "Operation" => "GetItems", "payload" => $payloadArr, "ItemId" => $asinR, "AssociateTag" => $appip_partner_id, "RequestBy" => 'amazon-create-post' ), $appip_publickey, $appip_privatekey, ( $skipCache ? true : false ) );
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
          } elseif ( is_array( $pxml ) && isset( $pxml[ 'Errors' ] ) && !isset( $pxml[ 'Items' ] ) ) {
            // only an error and no items
            $errorsArr = $pxml[ 'Errors' ];
          } else {
            if ( isset( $pxml[ 'Errors' ] ) && isset( $pxml[ 'Items' ] ) ) {
              //itmes and erroros - grab the errors
              $er2Arr[] = $pxml[ 'Errors' ];
              unset( $pxml[ 'Errors' ] );
              $r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, $asinR, $pxmlkey );
              if ( is_array( $r2 ) && !empty( $r2 ) ) {
                foreach ( $r2 as $ritem2 ) {
                  $totalResult2[] = $ritem2;
                }
              }
              $r3 = $pxml[ 'Items' ];
              if ( is_array( $r3 ) && !empty( $r3 ) ) {
                foreach ( $r3 as $ke => $ritem3 ) {
                  $totalResult3[] = $ritem3;
                }
              }
            } elseif ( isset( $pxml[ 'Items' ] ) ) {
              //only items
              $r2 = $awsv5->appip_plugin_FormatASINResult( $pxml, 1, $asinR, $pxmlkey );
              if ( is_array( $r2 ) && !empty( $r2 ) ) {
                foreach ( $r2 as $ritem2 ) {
                  $totalResult2[] = $ritem2;
                }
              }
              $r3 = $pxml[ 'Items' ];
              if ( is_array( $r3 ) && !empty( $r3 ) ) {
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
      if ( !empty( $errorsArr ) && !empty( $totalResult2 ) ) {
        //errors and items
        $itemErrors = true;

        /*
        loop the errors- and looks for item errors only,
        then put into the return array for that ASIN (even though it is invalid).
        This will output the error into the HTML as a comment so user can see what is going on
        when no product is displayed.
        */

        foreach ( $errorsArr as $k => $v ) {
          $code = isset( $v[ 'Code' ] ) ? $v[ 'Code' ] : '';
          $msg = isset( $v[ 'Message' ] ) ? $v[ 'Message' ] : '';
          $errasin = '';
          if ( $code == 'InvalidParameterValue' && $msg != '' ) {
            $errasin = str_replace( array( 'The value [', '] provided in the request for ItemIds is invalid.' ), array( '', '' ), $msg );
            $errmsg[] = $code . "|" . $msg;
            $errmsgBlock[ $errasin ] = $msg;
          }
        }
      } elseif ( !empty( $errorsArr ) ) {
        $pxmle = $errorsArr;
      }

      $resultarr = array();
      $doedit = isset( $_REQUEST[ 'createpost_edit' ] ) && $_REQUEST[ 'createpost_edit' ] != '' ? true : false;
      $red_location = '';

      if ( !empty( $pxmle ) ) {
        $pxmle;
        $errmsg = array();
        $errmsgBlock = array();
        foreach ( $pxmle as $k => $v ) {
          $code = isset( $v[ 'Code' ] ) ? $v[ 'Code' ] : 'code';
          $msg = isset( $v[ 'Message' ] ) ? $v[ 'Message' ] : 'message';
          $errmsg[] = $code . "|" . $msg;
          $errmsgBlock[] = '<div class="block-error-code" style="font-weight:bold;">' . $code . ':</div><div class="block-error-message">' . $msg . '</div>';
        }
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
          return '<div class="appip-block-wrapper appip-block-wrapper-error" style="border:1px solid #f48db0;padding:15px;text-align:center;background:#f5f5f5;"><div style="text-align:center;color:#f00;font-weight:bold;padding:0 0 10px;">Amazon Element Block Errors</div>' . implode( "<br>", $errmsgBlock ) . '<div style="color:#aaa;font-size:.75em;font-style:italic;">This block will not be displayed on the front end of the website until the error is fixed.</div></div>';
        } else {
          return '<pre style="display:none;" class="appip-errors">APPIP ERROR: amazon-elements[' . "\n" . implode( "\n", $errmsg ) . "\n" . ']</pre>';
        }
      } else {
        $resultarr = isset( $totalResult2 ) && !empty( $totalResult2 ) ? $totalResult2 : array();
        $resultarr3 = isset( $totalResult3 ) && !empty( $totalResult3 ) ? $totalResult3 : array();
        $errors_prod = array();
        $arr_position = 0;
        if ( is_array( $asinArr ) && !empty( $asinArr ) ) {
          foreach ( $asinArr as $checkASIN ) {
            foreach ( $resultarr as $resV ) {
              $resV = ( array )$resV;
              $Errors = array();
              $result3 = $awsv5->GetAPPIPReturnVals_V5( $resV, $totalResult3[ $arr_position ], $Errors );
              $resV = array_merge( $resV, $result3 );
              if ( isset( $resV[ 'ASIN' ] ) ) {
                //uses Amazon Title & Content if Title & Content is empty.
                $tasin = $resV[ 'ASIN' ];
                $tempCS = $tempCT = '';
                /* Item Description is not available in API 5.0 */
                /*
                $tempCS = isset( $resV[ 'ItemDesc' ][ 0 ][ 'Source' ] ) ? $resV[ 'ItemDesc' ][ 0 ][ 'Source' ] : '';
                $tempCS = $tempCS == '' && isset( $resV[ 'ItemDesc' ][ 'Source' ] ) ? $resV[ 'ItemDesc' ][ 'Source' ] : $tempCS;
                $tempCT = isset( $resV[ 'ItemDesc' ][ 0 ][ 'Content' ] ) && $tempCS == 'Product Description' ? str_replace( array( '<![CDATA[', ']]>' ), array( '', '' ), $resV[ 'ItemDesc' ][ 0 ][ 'Content' ] ) : '';
                $tempCT = $tempCT == '' && isset( $resV[ 'ItemDesc' ][ 'Content' ] ) && $tempCS == 'Product Description' ? str_replace( array( '<![CDATA[', ']]>' ), array( '', '' ), $resV[ 'ItemDesc' ][ 'Content' ] ) : $tempCT;
                */
                $temptitle = isset( $resV[ 'Title' ] ) ? $resV[ 'Title' ] : 'Product ' . $tasin;
                if ( $splitASINs && count( $asinArr ) > 1 ) {
                  $postTitle = isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] != '' ? sanitize_text_field( $_POST[ 'post_title' ] ) . ' (' . $tasin . ')': $temptitle;
                  $asinContent = $tempCT != '' ? wp_kses( $tempCT, $allowed_tags ) : '';
                  $manualContent = isset( $_POST[ 'post_content' ] ) && $_POST[ 'post_content' ] != '' ? wp_kses( $_POST[ 'post_content' ], $allowed_tags ) : '';
                  $postContent = $manualContent != '' ? $manualContent : '';
                  $postContent = $asinContent != '' ? $postContent . "\n" . $asinContent: $postContent;
                  $postContent = $postContent == '' ? 'Content ' . $tasin : $postContent;
                } else {
                  $postTitle = isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] != '' ? sanitize_text_field( $_POST[ 'post_title' ] ) : $temptitle;
                  $asinContent = $tempCT != '' ? wp_kses( $tempCT, $allowed_tags ) : '';
                  $manualContent = isset( $_POST[ 'post_content' ] ) && $_POST[ 'post_content' ] != '' ? wp_kses( $_POST[ 'post_content' ], $allowed_tags ) : '';
                  $postContent = $manualContent != '' ? $manualContent : '';
                  $postContent = $asinContent != '' ? $postContent . "\n" . $asinContent: $postContent;
                  $postContent = $postContent == '' ? 'Content ' . $tasin : $postContent;
                }
                $newProds[ $tasin ][ 'Title' ] = $postTitle;
                $newProds[ $tasin ][ 'Content' ] = $postContent;
                $postImage = isset( $resV[ 'LargeImage_URL' ] ) ? $resV[ 'LargeImage_URL' ] : '';
                $postImageH = $postImage != '' && isset( $resV[ 'LargeImage_Height_value' ] ) ? $resV[ 'LargeImage_Height_value' ] . 'px': '';
                $postImageH = $postImageH == '' && $postImage != '' && isset( $resV[ 'LargeImage_Height' ] ) ? $resV[ 'LargeImage_Height' ] . 'px': '';
                $postImageW = $postImage != '' && isset( $resV[ 'LargeImage_Width_value' ] ) ? $resV[ 'LargeImage_Width_value' ] . 'px': '';
                $postImageW = $postImageW == '' && $postImage != '' && isset( $resV[ 'LargeImage_Width' ] ) ? $resV[ 'LargeImage_Width' ] . 'px': '';
                $newProds[ $tasin ][ 'LargeImage_URL' ] = $postImage;
                $newProds[ $tasin ][ 'LargeImage_Height_value' ] = $postImageH;
                $newProds[ $tasin ][ 'LargeImage_Width_value' ] = $postImageW;
              }
            }
          }
        } else {
          $tasin = $ASIN;
          $tempCS = $tempCT = '';
          /* Not Available in API 5.0 */
          /*
          $tempCS = isset( $resultarr[ 'ItemDesc' ][ 0 ][ 'Source' ] ) ? $resultarr[ 'ItemDesc' ][ 0 ][ 'Source' ] : '';
          $tempCS = $tempCS == '' && isset( $resultarr[ 'ItemDesc' ][ 'Source' ] ) ? $resultarr[ 'ItemDesc' ][ 'Source' ] : '';
          $tempCT = isset( $resultarr[ 'ItemDesc' ][ 0 ][ 'Content' ] ) && $tempCS == 'Product Description' ? str_replace( array( '<![CDATA[', ']]>' ), array( '', '' ), $resultarr[ 'ItemDesc' ][ 0 ][ 'Content' ] ) : '';
          $tempCT = $tempCT == '' && isset( $resultarr[ 'ItemDesc' ][ 'Content' ] ) && $tempCS == 'Product Description' ? str_replace( array( '<![CDATA[', ']]>' ), array( '', '' ), $resultarr[ 'ItemDesc' ][ 'Content' ] ) : '';
          */
          $temptitle = isset( $resultarr[ 'Title' ] ) ? $resultarr[ 'Title' ] : 'Product ' . $tasin;
          $postTitle = isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] != '' ? sanitize_text_field( $_POST[ 'post_title' ] ) : $temptitle;
          $asinContent = $tempCT != '' ? wp_kses( $tempCT, $allowed_tags ) : '';
          $manualContent = isset( $_POST[ 'post_content' ] ) && $_POST[ 'post_content' ] != '' ? wp_kses( $_POST[ 'post_content' ], $allowed_tags ) : '';
          $postContent = $manualContent != '' ? $manualContent : '';
          $postContent = $asinContent != '' ? $postContent . "\n" . $asinContent: $postContent;
          $postContent = $postContent == '' ? 'Content ' . $tasin : $postContent;
          $newProds[ $resultarr[ 'ASIN' ] ][ 'Title' ] = $postTitle;
          $newProds[ $resultarr[ 'ASIN' ] ][ 'Content' ] = $postContent;
          $postImage = isset( $resV[ 'LargeImage_URL' ] ) ? $resV[ 'LargeImage_URL' ] : '';
          $postImageH = $postImage != '' && isset( $resV[ 'LargeImage_Height_value' ] ) ? $resV[ 'LargeImage_Height_value' ] . 'px': '';
          $postImageH = $postImageH == '' && $postImage != '' && isset( $resV[ 'LargeImage_Height' ] ) ? $resV[ 'LargeImage_Height' ] . 'px': '';
          $postImageW = $postImage != '' && isset( $resV[ 'LargeImage_Width_value' ] ) ? $resV[ 'LargeImage_Width_value' ] . 'px': '';
          $postImageW = $postImageW == '' && $postImage != '' && isset( $resV[ 'LargeImage_Width' ] ) ? $resV[ 'LargeImage_Width' ] . 'px': '';
          $newProds[ $resultarr[ 'ASIN' ] ][ 'LargeImage_URL' ] = $postImage;
          $newProds[ $resultarr[ 'ASIN' ] ][ 'LargeImage_Height_value' ] = $postImageH;
          $newProds[ $resultarr[ 'ASIN' ] ][ 'LargeImage_Width_value' ] = $postImageW;
        }
        if ( $splitASINs && count( $asinArr ) > 1 ) {
          $createdpostid = array();
          foreach ( $asinArr as $checkASIN ) {
            $postTitle = isset( $newProds[ $checkASIN ][ 'Title' ] ) ? $newProds[ $checkASIN ][ 'Title' ] : 'Product ' . $checkASIN;
            $postContent = isset( $newProds[ $checkASIN ][ 'Content' ] ) ? '<!-- Amazon Product Description-->[amazon-element asin="' . $checkASIN . '" field="desc"]' /*$newProds[$checkASIN]['Content'] */: '<!-- Amazon Description could not be added for: ' . $checkASIN . '-->'; /*'Content '. $checkASIN;*/
            $postImage = isset( $newProds[ $checkASIN ][ 'LargeImage_URL' ] ) ? $newProds[ $checkASIN ][ 'LargeImage_URL' ] : '';
            $postImageH = $postImage != '' && isset( $newProds[ $checkASIN ][ 'LargeImage_Height_value' ] ) ? $newProds[ $checkASIN ][ 'LargeImage_Height_value' ] . 'px': '';
            $postImageW = $postImage != '' && isset( $newProds[ $checkASIN ][ 'LargeImage_Width_value' ] ) ? $newProds[ $checkASIN ][ 'LargeImage_Width_value' ] . 'px': '';
            if ( isset( $_POST[ 'post_category' ][ $post_type ] ) && is_array( $_POST[ 'post_category' ][ $post_type ] ) && !empty( $_POST[ 'post_category' ][ $post_type ] ) ) {
              foreach ( $_POST[ 'post_category' ][ $post_type ] as $key => $val ) {
                $post_array = array(
                  'post_author' => ( isset( $_POST[ 'post_author' ] ) ? absint( $_POST[ 'post_author' ] ) : '' ),
                  'post_title' => $postTitle,
                  'post_status' => $post_stat,
                  'post_type' => $post_type,
                  'post_content' => $postContent,
                );
                $createdpostid[ $checkASIN ] = wp_insert_post( $post_array );
                $val = array_unique( array_map( 'intval', $val ) );
                if ( $postImage != '' ) {
                  $featured_key = apply_filters( 'amazon_featured_post_meta_key', '_amazon_featured_url' );
                  $featured_h_key = '_amazon_featured_height';
                  $featured_w_key = '_amazon_featured_width';
                  $alt_key = '_amazon_featured_alt';
                  $postid = $createdpostid[ $checkASIN ];
                  delete_post_meta( $postid, $featured_key );
                  delete_post_meta( $postid, $featured_h_key );
                  delete_post_meta( $postid, $featured_w_key );
                  delete_post_meta( $postid, $alt_key );
                  update_post_meta( $postid, $featured_key, $postImage, true );
                  if ( $postImageH != '' )
                    update_post_meta( $postid, $featured_h_key, $postImageH, true );
                  if ( $postImageW != '' )
                    update_post_meta( $postid, $featured_w_key, $postImageW, true );
                  if ( $postTitle != 'Product ' . $checkASIN )
                    update_post_meta( $postid, $alt_key, $postTitle, true );
                }
                $tesrr = wp_set_post_terms( $createdpostid, $val, $key, false );
              }
            } else {
              $post_array = array(
                'post_author' => sanitize_text_field( $_POST[ 'post_author' ] ),
                'post_title' => $postTitle,
                'post_status' => $post_stat,
                'post_type' => $post_type,
                'post_content' => $postContent,
                'post_parent' => 0,
                'post_category' => ''
              );
              $createdpostid[ $checkASIN ] = wp_insert_post( $post_array, 'false' );
              if ( $postImage != '' ) {
                $featured_key = apply_filters( 'amazon_featured_post_meta_key', '_amazon_featured_url' );
                $featured_h_key = '_amazon_featured_height';
                $featured_w_key = '_amazon_featured_width';
                $alt_key = '_amazon_featured_alt';
                $postid = $createdpostid[ $checkASIN ];
                delete_post_meta( $postid, $featured_key );
                delete_post_meta( $postid, $featured_h_key );
                delete_post_meta( $postid, $featured_w_key );
                delete_post_meta( $postid, $alt_key );
                update_post_meta( $postid, $featured_key, $postImage, true );
                if ( $postImageH != '' )
                  update_post_meta( $postid, $featured_h_key, $postImageH, true );
                if ( $postImageW != '' )
                  update_post_meta( $postid, $featured_w_key, $postImageW, true );
                if ( $postTitle != 'Product ' . $checkASIN )
                  update_post_meta( $postid, $alt_key, $postTitle, true );
              }
            }
          }
          if ( is_array( $createdpostid ) && !empty( $createdpostid ) ) {
            foreach ( $createdpostid as $key => $pid ) {
              $newpost = get_post( $pid );
              ini_set( 'display_errors', 0 );
              amazonProductInAPostSavePostdata( $pid, $newpost, $key );
            }
            $red_location = "Location: admin.php?page=apipp-add-new&appmsg=1&qty=" . count( $createdpostid );
            $red_pid = $createdpostid;
          } else {
            $red_location = "Location: admin.php?page=apipp-add-new&appmsg=2";
            $red_pid = 0;

          }

        } else {
          $postTitle = isset( $newProds[ $asinArr[ 0 ] ][ 'Title' ] ) ? $newProds[ $asinArr[ 0 ] ][ 'Title' ] : '';
          $postContent = isset( $newProds[ $asinArr[ 0 ] ][ 'Content' ] ) ? '<!-- Amazon Product Description-->[amazon-element asin="' . $asinArr[ 0 ] . '" field="desc"]' /*$newProds[$checkASIN]['Content'] */: '<!-- Amazon Description could not be added for: ' . $asinArr[ 0 ] . '-->'; /*'Content '. $checkASIN;*/
          //$postContent 	= isset($newProds[$asinArr[0]]['Content']) ? $newProds[$asinArr[0]]['Content'] : 'Content '. $asinArr[0];
          $manualContent = isset( $_POST[ 'post_content' ] ) && $_POST[ 'post_content' ] != '' ? wp_kses( $_POST[ 'post_content' ], $allowed_tags ) : '';
          $postContent = $manualContent != '' ? $manualContent : '<!-- no content available -->';
          $postImage = isset( $newProds[ $asinArr[ 0 ] ][ 'LargeImage_URL' ] ) ? $newProds[ $asinArr[ 0 ] ][ 'LargeImage_URL' ] : '';
          $postImageH = $postImage != '' && isset( $newProds[ $asinArr[ 0 ] ][ 'LargeImage_Height_value' ] ) ? $newProds[ $asinArr[ 0 ] ][ 'LargeImage_Height_value' ] . 'px': '';
          $postImageW = $postImage != '' && isset( $newProds[ $asinArr[ 0 ] ][ 'LargeImage_Width_value' ] ) ? $newProds[ $asinArr[ 0 ] ][ 'LargeImage_Width_value' ] . 'px': '';
          if ( isset( $_POST[ 'post_category' ][ $post_type ] ) ) {
            foreach ( $_POST[ 'post_category' ][ $post_type ] as $key => $val ) {
              $post_array = array(
                'post_author' => ( isset( $_POST[ 'post_author' ] ) ? absint( $_POST[ 'post_author' ] ) : '' ),
                'post_title' => $postTitle,
                'post_status' => $post_stat,
                'post_type' => $post_type,
                'post_content' => $postContent,
              );
              $createdpostid = wp_insert_post( $post_array );
              $val = array_unique( array_map( 'intval', $val ) );
              if ( $postImage != '' ) {
                $featured_key = apply_filters( 'amazon_featured_post_meta_key', '_amazon_featured_url' );
                $featured_h_key = '_amazon_featured_height';
                $featured_w_key = '_amazon_featured_width';
                $alt_key = '_amazon_featured_alt';
                $postid = $createdpostid;
                delete_post_meta( $postid, $featured_key );
                delete_post_meta( $postid, $featured_h_key );
                delete_post_meta( $postid, $featured_w_key );
                delete_post_meta( $postid, $alt_key );
                update_post_meta( $postid, $featured_key, $postImage, true );
                if ( $postImageH != '' )
                  update_post_meta( $postid, $featured_h_key, $postImageH, true );
                if ( $postImageW != '' )
                  update_post_meta( $postid, $featured_w_key, $postImageW, true );
                if ( $postTitle != 'Product ' . $asinArr[ 0 ] )
                  update_post_meta( $postid, $alt_key, $postTitle, true );
              }
              $tesrr = wp_set_post_terms( $createdpostid, $val, $key, false );
            }
          } else {
            $post_array = array(
              'post_author' => sanitize_text_field( $_POST[ 'post_author' ] ),
              'post_title' => $postTitle,
              'post_status' => $post_stat,
              'post_type' => $post_type,
              'post_content' => $postContent,
              'post_parent' => 0,
              'post_category' => ''
            );
            $createdpostid = wp_insert_post( $post_array, 'false' );
            if ( $postImage != '' ) {
              $featured_key = apply_filters( 'amazon_featured_post_meta_key', '_amazon_featured_url' );
              $featured_h_key = '_amazon_featured_height';
              $featured_w_key = '_amazon_featured_width';
              $alt_key = '_amazon_featured_alt';
              $postid = $createdpostid;
              delete_post_meta( $postid, $featured_key );
              delete_post_meta( $postid, $featured_h_key );
              delete_post_meta( $postid, $featured_w_key );
              delete_post_meta( $postid, $alt_key );
              update_post_meta( $postid, $featured_key, $postImage, true );
              if ( $postImageH != '' )
                update_post_meta( $postid, $featured_h_key, $postImageH, true );
              if ( $postImageW != '' )
                update_post_meta( $postid, $featured_w_key, $postImageW, true );
              if ( $postTitle != 'Product ' . $asinArr[ 0 ] )
                update_post_meta( $postid, $alt_key, $postTitle, true );
            }
          }
          if ( $createdpostid != '' ) {
            $newpost = get_post( $createdpostid );
            ini_set( 'display_errors', 0 );
            amazonProductInAPostSavePostdata( $createdpostid, $newpost, $asinArr[ 0 ] );
            $red_location = "Location: admin.php?page=apipp-add-new&appmsg=1&asins=" . implode( ",", $asinArr );
            $red_pid = $createdpostid;
          } else {
            $red_location = "Location: admin.php?page=apipp-add-new&appmsg=2";
            $red_pid = 0;
          }
        }

        if ( is_array( $red_pid ) && $red_location != '' ) {
          header( $red_location );
          exit();
        } elseif ( $red_pid != 0 ) {
          if ( $doedit ) {
            $red_location = "Location: post.php?post={$red_pid}&action=edit";
            header( $red_location );
            exit();
          } else {
            header( $red_location );
            exit();
          }
        } else {

        }
      }
    } else {
      add_action( 'save_post', 'amazonProductInAPostSavePostdata', 1, 2 ); // save the custom fields
    }
  }
}
/* When the post is saved, saves our custom data */

add_action( 'save_post', 'amazonProductInAPostSavePostdata', 1, 2 ); // save the custom fields

function amazonProductInAPostSavePostdata( $post_id, $post, $asin = '' ) {
  if ( !isset( $_POST[ 'post_save_type_apipp' ] ) )
    return;
  $post_id = $post_id == '' ? $post->ID : $post_id;
  $mydata = array();
  $mydata[ 'amazon-product-isactive' ] = isset( $_POST[ 'amazon-product-isactive' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-isactive' ] ) : '0';
  $mydata[ 'amazon-product-content-location' ] = isset( $_POST[ 'amazon-product-content-location' ][ 1 ][ 0 ] ) ? sanitize_text_field( $_POST[ 'amazon-product-content-location' ][ 1 ][ 0 ] ) : '1';
  $mydata[ 'amazon-product-single-asin' ] = $asin != '' ? $asin : ( isset( $_POST[ 'amazon-product-single-asin' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-single-asin' ] ) : '' );
  $mydata[ 'amazon-product-excerpt-hook-override' ] = isset( $_POST[ 'amazon-product-excerpt-hook-override' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-excerpt-hook-override' ] ) : '3';
  $mydata[ 'amazon-product-content-hook-override' ] = isset( $_POST[ 'amazon-product-content-hook-override' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-content-hook-override' ] ) : '3';
  $mydata[ 'amazon-product-newwindow' ] = isset( $_POST[ 'amazon-product-newwindow' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-newwindow' ] ) : '3';
  $mydata[ 'amazon-product-singular-only' ] = isset( $_POST[ 'amazon-product-singular-only' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-singular-only' ] ) : '0';
  $mydata[ 'amazon-product-amazon-desc' ] = isset( $_POST[ 'amazon-product-amazon-desc' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-amazon-desc' ] ) : '0';
  $mydata[ 'amazon-product-show-gallery' ] = isset( $_POST[ 'amazon-product-show-gallery' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-show-gallery' ] ) : '0';
  $mydata[ 'amazon-product-show-features' ] = isset( $_POST[ 'amazon-product-show-features' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-show-features' ] ) : '0';
  $mydata[ 'amazon-product-show-list-price' ] = isset( $_POST[ 'amazon-product-show-list-price' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-show-list-price' ] ) : '0';
  $mydata[ 'amazon-product-template' ] = isset( $_POST[ 'amazon-product-template' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-template' ] ) : 'default';
  $mydata[ 'amazon-product-show-used-price' ] = isset( $_POST[ 'amazon-product-show-used-price' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-show-used-price' ] ) : '0';
  //$mydata['amazon-product-show-saved-amt'] 			= isset($_POST['amazon-product-show-saved-amt']) ? sanitize_text_field($_POST['amazon-product-show-saved-amt']) : '0';
  //$mydata['amazon-product-timestamp'] 				= isset($_POST['amazon-product-timestamp']) ? sanitize_text_field($_POST['amazon-product-timestamp']) : '0';
  $mydata[ 'amazon-product-new-title' ] = isset( $_POST[ 'amazon-product-new-title' ] ) ? sanitize_text_field( $_POST[ 'amazon-product-new-title' ] ) : '';
  $mydata[ 'amazon-product-use-cartURL' ] = isset( $_POST[ 'amazon-product-use-cartURL' ] ) && ( int )$_POST[ 'amazon-product-use-cartURL' ] == 1 ? '1' : '';

  if ( $mydata[ 'amazon-product-isactive' ] == '' && $mydata[ 'amazon-product-single-asin' ] == "" ) {
    $mydata[ 'amazon-product-content-location' ] = '';
  }
  if ( $mydata[ 'amazon-product-excerpt-hook-override' ] == '' ) {
    $mydata[ 'amazon-product-excerpt-hook-override' ] = '3';
  }
  if ( $mydata[ 'amazon-product-content-hook-override' ] == '' ) {
    $mydata[ 'amazon-product-content-hook-override' ] = '3';
  }
  if ( $mydata[ 'amazon-product-newwindow' ] == '' ) {
    $mydata[ 'amazon-product-newwindow' ] = '3';
  }
  $mydata = apply_filters( 'amazon_product_in_a_post_plugin_meta_presave', $mydata );

  foreach ( $mydata as $key => $value ) {
    if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {
      return;
    }
    $value = implode( ',', ( array )$value );
    if ( get_post_meta( $post_id, $key, FALSE ) ) {
      update_post_meta( $post_id, $key, $value );
    } else {
      add_post_meta( $post_id, $key, $value );
    }
    if ( !$value )delete_post_meta( $post_id, $key ); //delete if blank
  }
}

/* Prints the inner fields for the custom post/page section */
function amazonProductInAPostBox1() {
  global $post;
  $appASIN = get_post_meta( $post->ID, 'amazon-product-single-asin', true );
  $appnewwin = get_post_meta( $post->ID, 'amazon-product-newwindow', true );
  $appsingle = get_post_meta( $post->ID, 'amazon-product-singular-only', true );
  $appnewinO = get_option( 'apipp_open_new_window' ) == true ? 1 : 0;
  $apphookO = get_option( 'apipp_hook_excerpt' ) == true ? 1 : 0;
  $apphook = get_post_meta( $post->ID, 'amazon-product-excerpt-hook-override', true );
  $appcont = get_post_meta( $post->ID, 'amazon-product-content-hook-override', true );
  $appcontO = get_option( 'apipp_hook_content' ) == true ? 1 : 0;
  $appactive = get_post_meta( $post->ID, 'amazon-product-isactive', true );
  $apptemplate = get_post_meta( $post->ID, 'amazon-product-template', true );
  $appaffidO = APIAP_ASSOC_ID;
  $appnoonce = wp_create_nonce( plugin_basename( __FILE__ ) ); // Use nonce for verification ... ONLY USE ONCE!
  $appconloc = get_post_meta( $post->ID, 'amazon-product-content-location', true );
  $amazondesc = get_post_meta( $post->ID, 'amazon-product-amazon-desc', true );
  $amazongallery = get_post_meta( $post->ID, 'amazon-product-show-gallery', true );
  $amazonfeatures = get_post_meta( $post->ID, 'amazon-product-show-features', true );
  $amazontstamp = get_post_meta( $post->ID, 'amazon-product-timestamp', true );
  $appipnewtitle = get_post_meta( $post->ID, 'amazon-product-new-title', true );
  $amazonused = get_post_meta( $post->ID, 'amazon-product-show-used-price', true );
  $amazonlist = get_post_meta( $post->ID, 'amazon-product-show-list-price', true );
  $amazonsaved = get_post_meta( $post->ID, 'amazon-product-show-saved-amt', true );
  $useCartURL = get_post_meta( $post->ID, 'amazon-product-use-cartURL', true );

  $menuhide = ( $appactive != '' ) ? ' checked="checked"' : '';
  $hookcontent = ( $appcont == '2' || ( $appcont == '' && $appcontO ) ) ? ' checked="checked"' : "";
  $hookexcerpt = ( $apphook == '2' || ( $apphook == '' && $apphookO ) ) ? ' checked="checked"' : "";
  $singleonly = ( $appsingle == '1' ) ? ' checked="checked"' : "";
  $newwin = ( $appnewwin == '2' || ( $appnewwin == '' && $appnewinO ) ) ? ' checked="checked"' : "";
  $amazontstamp = $amazontstamp != '' ? ' checked="checked"' : '';
  $amazondesc = $amazondesc != '' ? ' checked="checked"' : '';
  $amazongallery = $amazongallery != '' ? ' checked="checked"' : '';
  $amazonfeatures = $amazonfeatures != '' ? ' checked="checked"' : '';
  $amazonused = $amazonused != '' ? ' checked="checked"' : '';
  $amazonlist = $amazonlist != '' ? ' checked="checked"' : '';
  $amazonsaved = $amazonsaved != '' ? ' checked="checked"' : '';
  $useCartURL = $useCartURL != '' ? ' checked="checked"' : '';
  $apptemplate = $apptemplate != '' ? esc_attr( $apptemplate ) : 'default';

  if ( $appconloc === '3' ) {
    $appeampleimg = 'example-layout-3.png';
  } elseif ( $appconloc === '2' ) {
    $appeampleimg = 'example-layout-2.png';
  } else {
    $appeampleimg = 'example-layout-1.png';
  }
  $selectTxt = '';
  $templates = apply_filters( 'appip-register-templates', array() );
  if ( is_array( $templates ) && !empty( $templates ) ) {
    $selArr = array();
    $slval = 'default';
    foreach ( $templates as $tk => $tv ) {
      $location = isset( $tv[ 'location' ] ) ? $tv[ 'location' ] : '';
      $name = isset( $tv[ 'name' ] ) ? $tv[ 'name' ] : '';
      $TID = isset( $tv[ 'ID' ] ) ? $tv[ 'ID' ] : '';
      if ( $location != '' && $name != '' && $TID != '' && ( $location === 'product' || $location === 'products' || $location === 'core' ) ) {
        $slval = $apptemplate == $TID && $apptemplate != '' ? ' selected' : '';
        $selArr[] = '<option value="' . $TID . '" ' . $slval . '>' . $name . '</option>';
      }
    }
    if ( !empty( $selArr ) ) {
      $selectTxt = '<select name="amazon-product-template" id="amazon-product-template">' . "\n" . implode( "\n", $selArr ) . '</select>' . "\n";
    } else {
      $selectTxt = '<select name="amazon-product-template" id="amazon-product-template">' . "\n" . '</select>' . "\n";
    }
  }

  $noaffidmsg = '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade below-h2"><p><strong>' . __( 'WARNING:', 'amazon-product-in-a-post-plugin', 'amazon-product-in-a-post-plugin' ) . '</strong> ' . __( 'You will not get credit for Amazon purchases until you add your Amazon Affiliate ID on the <a href="admin.php?page=apipp_plugin_admin">options</a> page.', 'amazon-product-in-a-post-plugin' ) . '</p></div>';
  if ( $appaffidO == '' ) {
    echo $noaffidmsg;
  }
  echo '<p><input type="checkbox" name="amazon-product-isactive" value="1" ' . $menuhide . ' /> <label for="amazon-product-isactive"><strong>' . __( "Product is Active?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if checked the product will be live', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><label for="amazon-product-single-asin"><strong>' . __( "Amazon Product ASIN(s)", 'amazon-product-in-a-post-plugin' ) . '</strong></label><br /><input type="text" name="amazon-product-single-asin" id="amazon-product-single-asin" size="25" value="' . $appASIN . '" /><em>' . __( 'For multiple, separate with a comma. You will need to get ASINs from <a href="http://amazon.com/">Amazon</a>', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><label for="amazon-product-new-title"><strong>' . __( "Replace Amazon Title With Below Title:", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'Optional. To hide title all together, type "null". No HTML, plain text only. Use this if you want your own title to show instead of Amazon\'s title.', 'amazon-product-in-a-post-plugin' ) . '</em><input type="text" class="amazon-product-new-title" name="amazon-product-new-title" id="amazon-product-new-title" size="35" value="' . $appipnewtitle . '" /></p>';
  echo '<input type="hidden" name="amazonpipp_noncename" id="amazonpipp_noncename" value="' . $appnoonce . '" /><input type="hidden" name="post_save_type_apipp" id="post_save_type_apipp" value="1" />';
  echo '<p><input type="checkbox" name="amazon-product-content-hook-override" value="2" ' . $hookcontent . ' /> <label for="amazon-product-content-hook-override"><strong>' . __( "Hook into Content?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'Product will show when full content is used (when <code>the_content()</code> template tag). On by default.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-excerpt-hook-override" value="2" ' . $hookexcerpt . ' /> <label for="amazon-product-excerpt-hook-override"><strong>' . __( "Hook into Excerpt?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'Product will show when partial excerpt content is used(when <code>the_excerpt()</code> is used. Off by default.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-singular-only" value="1" ' . $singleonly . ' /> <label for="amazon-product-singular-only"><strong>' . __( "Show Only on Single Page?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if checked the product will only show when on single page. Off by default.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-newwindow" value="2" ' . $newwin . ' /> <label for="amazon-product-newwindow"><strong>' . __( "Open Product Link in New Window?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if checked the product will open a new browser window. Off by default unless set in options.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<div class="appip-example-image"><img data="' . plugins_url( '/images/', dirname( __FILE__ ) ) . '" src="' . plugins_url( '/images/' . $appeampleimg, dirname( __FILE__ ) ) . '" class="appipexampleimg" alt=""/></div>';
  echo '<p><label for="amazon-product-content-location"><strong>' . __( "Where would you like your product to show within the post?", 'amazon-product-in-a-post-plugin' ) . '</strong></label></p>';
  echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;<input class="appip-content-type" type="checkbox" name="amazon-product-content-location[1][]" value="1" ' . ( ( $appconloc === '1' ) || ( $appconloc == '' ) ? ' checked="checked"' : '' ) . ' /> ' . __( '<strong>Above Post Content</strong> - <em>Default - Product will be first then post text</em>', 'amazon-product-in-a-post-plugin' ) . '<br/>';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input class="appip-content-type" type="checkbox" name="amazon-product-content-location[1][]" value="3" ' . ( ( $appconloc === '3' ) ? ' checked="checked"' : '' ) . ' /> ' . __( '<strong>Below Post Content</strong> - <em>Post text will be first then the Product</em>', 'amazon-product-in-a-post-plugin' ) . '<br/>';
  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input class="appip-content-type" type="checkbox" name="amazon-product-content-location[1][]" value="2" ' . ( ( $appconloc === '2' ) ? ' checked="checked"' : '' ) . ' /> ' . __( '<strong>Post Text becomes Description</strong> - <em>Post text will become part of the Product layout</em>', 'amazon-product-in-a-post-plugin' ) . '</p>';
  echo '<h2>Additional Features:</h2>';
  echo '<p><label for="amazon-product-template"><strong>' . __( "Use the following Template:", 'amazon-product-in-a-post-plugin' ) . ' </strong></label>' . $selectTxt . '</p>';
  echo '<p><input type="checkbox" name="amazon-product-use-cartURL" value="1" ' . $useCartURL . ' /> <label for="amazon-product-use-cartURL"><strong>' . __( "Use Add to Cart URL?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'Uses Add to Cart URL instead of product page link. helps with 90 day conversion cookie.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-amazon-desc" disabled value="1" ' . $amazondesc . ' /> <label for="amazon-product-amazon-desc" title="not available"><strong><span style="color:#888;font-style:italic;">' . __( "Show Amazon Description?", 'amazon-product-in-a-post-plugin' ) . '</span></strong></label> <em style="color:#f00;">No longer available.<!--' . __( 'if available. This will be IN ADDITION TO your own content.', 'amazon-product-in-a-post-plugin' ) . '--></em></p>';
  echo '<p><input type="checkbox" name="amazon-product-show-gallery" value="1" ' . $amazongallery . ' /> <label for="amazon-product-show-gallery"><strong>' . __( "Show Image Gallery?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available (Consists of Amazon Approved images only). Not all products have an Amazon Image Gallery.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-show-features" value="1" ' . $amazonfeatures . ' /> <label for="amazon-product-show-features"><strong>' . __( "Show Amazon Features?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-show-used-price" value="1" ' . $amazonused . ' /> <label for="amazon-product-show-used-price"><strong>' . __( "Show Amazon Used Price?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  echo '<p><input type="checkbox" name="amazon-product-show-list-price" value="1" ' . $amazonlist . ' /> <label for="amazon-product-show-list-price"><strong>' . __( "Show Amazon List Price?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  /* Possibly Remove */
  //echo '<p><input type="checkbox" name="amazon-product-show-saved-amt" value="1" '.$amazonsaved.' /> <label for="amazon-product-show-saved-amt"><strong>' . __("Show Saved Amount?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>'.__('if available. Not all items have this feature.','amazon-product-in-a-post-plugin').'</em></p>';
  //echo '<p><input type="checkbox" name="amazon-product-timestamp" value="1" '.$amazontstamp.' /> <label for="amazon-product-show-timestamp"><strong>' . __("Show Price Timestamp?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>'.__('for example:','amazon-product-in-a-post-plugin').'</em>'.__('<div class="appip-em-sample">&nbsp;&nbsp;Amazon.com Price: $32.77 (as of 01/07/2008 14:11 PST - <span class="appip-tos-price-cache-notice-tooltip" title="">Details</span>)<br/>&nbsp;&nbsp;Amazon.com Price: $32.77 (as of 14:11 PST - <span class="appip-tos-price-cache-notice-tooltip" title="">More info</span>)</div>','amazon-product-in-a-post-plugin').'</p>';
  //echo '<span style="display:none;" class="appip-tos-price-cache-notice">' . __( 'Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on amazon.' . APIAP_LOCALE . ' at the time of purchase will apply to the purchase of this product.', 'amazon-product-in-a-post-plugin' ) . '</span>';
  echo '<div style="clear:both;"></div>';
}


/* When the post is saved, saves our custom data */
function amazonProductInAPostSavePostdataForm( $post_id, $post ) {
  if ( $post_id == '' ) {
    $post_id = $post->ID;
  }
  if ( !isset( $post[ 'post_save_type_apipp' ] ) ) {
    return;
  }
  $mydata = array();
  $mydata[ 'amazon-product-isactive' ] = sanitize_text_field( $post[ 'amazon-product-isactive' ] );
  $mydata[ 'amazon-product-content-location' ] = sanitize_text_field( $post[ 'amazon-product-content-location' ] );
  $mydata[ 'amazon-product-single-asin' ] = sanitize_text_field( $post[ 'amazon-product-single-asin' ] );
  $mydata[ 'amazon-product-excerpt-hook-override' ] = sanitize_text_field( $post[ 'amazon-product-excerpt-hook-override' ] );
  $mydata[ 'amazon-product-content-hook-override' ] = sanitize_text_field( $post[ 'amazon-product-content-hook-override' ] );
  $mydata[ 'amazon-product-newwindow' ] = sanitize_text_field( $post[ 'amazon-product-newwindow' ] );
  $mydata[ 'amazon-product-singular-only' ] = sanitize_text_field( $post[ 'amazon-product-singular-only' ] );
  $mydata[ 'amazon-product-amazon-desc' ] = sanitize_text_field( $post[ 'amazon-product-amazon-desc' ] );
  $mydata[ 'amazon-product-show-gallery' ] = sanitize_text_field( $post[ 'amazon-product-show-gallery' ] );
  $mydata[ 'amazon-product-show-features' ] = sanitize_text_field( $post[ 'amazon-product-show-features' ] );
  $mydata[ 'amazon-product-show-list-price' ] = sanitize_text_field( $post[ 'amazon-product-show-list-price' ] );
  $mydata[ 'amazon-product-template' ] = sanitize_text_field( $_POST[ 'amazon-product-template' ] );
  $mydata[ 'amazon-product-show-used-price' ] = sanitize_text_field( $post[ 'amazon-product-show-used-price' ] );
  //$mydata['amazon-product-show-saved-amt']		= sanitize_text_field($post['amazon-product-show-saved-amt']);
  //$mydata['amazon-product-timestamp'] 			= sanitize_text_field($post['amazon-product-timestamp']);
  $mydata[ 'amazon-product-new-title' ] = sanitize_text_field( $post[ 'amazon-product-new-title' ] );
  $mydata[ 'amazon-product-use-cartURL' ] = sanitize_text_field( $post[ 'amazon-product-use-cartURL' ] );

  if ( $mydata[ 'amazon-product-isactive' ] == '' && $mydata[ 'amazon-product-single-asin' ] == "" ) {
    $mydata[ 'amazon-product-content-location' ] = '';
  }
  if ( $mydata[ 'amazon-product-excerpt-hook-override' ] == '' ) {
    $mydata[ 'amazon-product-excerpt-hook-override' ] = '3';
  }
  if ( $mydata[ 'amazon-product-content-hook-override' ] == '' ) {
    $mydata[ 'amazon-product-content-hook-override' ] = '3';
  }
  if ( $mydata[ 'amazon-product-newwindow' ] == '' ) {
    $mydata[ 'amazon-product-newwindow' ] = '3';
  }
  $mydata = apply_filters( 'amazon_product_in_a_post_plugin_meta_presave', $mydata );

  foreach ( $mydata as $key => $value ) {
    if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {
      return;
    }
    $value = implode( ',', ( array )$value );
    if ( get_post_meta( $post_id, $key, FALSE ) ) {
      update_post_meta( $post_id, $key, $value );
    } else {
      add_post_meta( $post_id, $key, $value );
    }
    if ( !$value )delete_post_meta( $post_id, $key ); //delete if blank
  }
}

function apipp_plugin_menu() {
  global $fullname_apipp, $shortname_apipp, $options_apipp;
  apipp_options_add_admin_page( $fullname_apipp, $shortname_apipp, $options_apipp );
  add_menu_page( __( 'Amazon Product In a Post Plugin', 'amazon-product-in-a-post-plugin' ), _x( 'Amazon Product', 'Main Menu Title', 'amazon-product-in-a-post-plugin' ), 'edit_posts', 'apipp-main-menu', 'apipp_main_page', 'dashicons-amazon' ); //toplevel_page_apipp-main-menu
  add_submenu_page( 'apipp-main-menu', _x( "Getting Started", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( "Getting Started", 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'edit_posts', 'apipp-main-menu', 'apipp_main_page' );
  add_submenu_page( 'apipp-main-menu', _x( "New Amazon Post", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( "New Amazon Post", 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'edit_posts', "apipp-add-new", 'apipp_add_new_post' ); //amazon-product_page_apipp-add-new
  add_submenu_page( 'apipp-main-menu', _x( "Amazon Product in a Post Options", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( "Plugin Settings", 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'manage_options', "apipp_plugin_admin", 'apipp_options_add_subpage' );
  //add_submenu_page( 'apipp-main-menu', _x( "Cached Products",'Page Title', 'amazon-product-in-a-post-plugin' ), _x( "Cached Products", 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'edit_posts', "edit.php?post_type=amz-product", '' );
  add_submenu_page( 'apipp-main-menu', _x( "Product Cache", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( "Product Cache", 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'edit_posts', "apipp-cache-page", 'apipp_cache_page' );
  add_submenu_page( 'apipp-main-menu', _x( "Shortcodes & Blocks Usage", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( 'Shortcodes/Blocks', 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'manage_options', 'apipp_plugin-shortcode', 'apipp_shortcode_help_page' );
  add_submenu_page( 'apipp-main-menu', _x( "FAQs/Help", 'Page Title', 'amazon-product-in-a-post-plugin' ), _x( 'FAQs/Help', 'Menu Title', 'amazon-product-in-a-post-plugin' ), 'manage_options', 'apipp_plugin-faqs', 'apipp_options_faq_page' );
  //add_submenu_page( 'apipp-main-menu', __('Layout Styles', 'amazon-product-in-a-post-plugin'), __('Layout Styles', 'amazon-product-in-a-post-plugin'), 'manage_options', 'appip-layout-styles', 'apipp_templates');
}

function apipp_cache_page() {
  global $current_user, $wpdb;
  if ( !current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'amazon-product-in-a-post-plugin' ) );
  }
  echo '<div class="wrap">';
  echo '<h2>' . __( 'Amazon Product In A Post CACHE', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  if ( isset( $_GET[ 'appmsg' ] ) && ( int )$_GET[ 'appmsg' ] == 1 ) {
    if ( isset( $_GET[ 'qty' ] ) && ( int )$_GET[ 'qty' ] > 0 ) {
      echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade below-h2"><p><b>' . esc_attr( ( int )$_GET[ 'qty' ] ) . ' ' . __( 'Product post(s) have been saved. To edit, use the standard Post Edit options.', 'amazon-product-in-a-post-plugin' ) . '</b></p></div>';
    } else {
      echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade below-h2"><p><b>' . __( 'Product post has been saved. To edit, use the standard Post Edit options.', 'amazon-product-in-a-post-plugin' ) . '</b></p></div>';
    }
  }
  echo '	<div class="wrapper">';
  $paged = isset( $_GET[ 'paged' ] ) && ( int )$_GET[ 'paged' ] != 0 ? ( int )$_GET[ 'paged' ] : 1;
  $limit = 50;
  $offset = ( $paged - 1 ) * $limit;
  $ccountsql = "SELECT count(Cache_id) FROM " . $wpdb->prefix . "amazoncache;";
  $max_pages = $wpdb->get_var( $ccountsql );
  $num_pages = round( ( int )$max_pages / $limit, 0 );
  $checksql = "SELECT body,Cache_id,URL,updated,( NOW() - Updated )as Age FROM " . $wpdb->prefix . "amazoncache ORDER BY Updated DESC LIMIT {$offset},{$limit};";
  $result = $wpdb->get_results( $checksql );
  $cacheSec = ( int )apply_filters( 'amazon_product_post_cache', get_option( 'apipp_amazon_cache_sec', 3600 ) ) / 60;
  $foundPage = count( $result );
  echo '<p>' . __( 'The product cache is stored for ' . $cacheSec . ' minutes and then deleted automatically. To refetch a product, delete the cache and it will be updated on the next product load.', 'amazon-product-in-a-post-plugin' ) . ' ';
  echo __( 'The displayed JSON cache data comes directly from the Amazon API requests.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  echo '<div style="text-align:right;margin:15px"><a href="#" class="button appip-cache-del button-primary" id="appip-cache-0">' . __( 'Delete Cache For ALL Products', 'amazon-product-in-a-post-plugin' ) . '</a></div>';
  $page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => _x( '&laquo;', 'Previous Page Character', 'amazon-product-in-a-post-plugin' ),
    'next_text' => _x( '&raquo;', 'Next Page Character', 'amazon-product-in-a-post-plugin' ),
    'total' => $num_pages,
    'current' => $paged
  ) );
  echo '<style>.appip-cache-errors span:nth-child(odd) {border: 1px solid #c7c7c7;border-width: 0 1px 1px 1px;}.appip-cache-errors span:nth-child(even) {background: #e8e8e8;border: 1px solid #c7c7c7;border-width: 0 1px 1px 1px;}span.page-numbers.current {display: inline-block;min-width: 17px;border: 1px solid rgb(177, 177, 177);padding: 3px 5px 7px;background: #ffffff;font-size: 16px;line-height: 1;font-weight: 400;text-align: center;}</style><div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0;"><span style="text-align:right;font-style:italic;font-size:14px;color:#666;padding-right: 10px;">Total Results: ' . $max_pages . '</span> ' . $page_links . '</div></div>';
  echo '<table class="wp-list-table widefat fixed" cellspacing="0">';
  echo '<thead><tr><th class="manage-column manage-cache-id" style="width:75px;">' . __( 'Cache ID', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-call-ui">' . __( 'Unique Call UI', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-updated" style="width:150px;">' . __( 'Last Updated', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-last-col" style="width:100px;"></th></tr></thead>';
  echo '<tfoot><tr><th class="manage-column manage-cache-id" style="width:75px;">' . __( 'Cache ID', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-call-ui">' . __( 'Unique Call UI', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-updated" style="width:150px;">' . __( 'Last Updated', 'amazon-product-in-a-post-plugin' ) . '</th><th class="manage-column manage-last-col" style="width:100px;"></th></tr></tfoot>';
  if ( !empty( $result ) && is_array( $result ) ) {
    echo '<tbody id="the-list">';
    $appct = 0;
    foreach ( $result as $psxml ) {
      $errors = array();
      $bod = str_replace( array( '\\_', '\\%' ), array( '_', '%' ), $psxml->body );
      $tempBod = json_decode( $bod, true );
      $errors = array();
      if ( is_array( $tempBod ) && !empty( $tempBod ) && isset( $tempBod[ 'Errors' ] ) ) {
        foreach ( $tempBod[ 'Errors' ] as $ek => $ev ) {
          $errors[] = '<span style="color: #444; font-size: 12px; font-style: italic; display: block; padding-left: 1.5em;"><strong>' . $ev[ 'Code' ] . '</strong> - ' . $ev[ 'Message' ] . '</span>';
        }
      }
      $errtxt = !empty( $errors ) ? '<div class="appip-cache-errors"><strong style="margin-top: 8px;color: #fff;background: #ff0000;display: block;padding: 0 .5em;">ERRORS:</strong> ' . implode( "\n", $errors ) . '</div>': '';
      if ( $appct & 1 ) {
        echo '<tr class="alternate iedit appip-cache-' . $psxml->Cache_id . '-row">';
      } else {
        echo '<tr class="iedit appip-cache-' . $psxml->Cache_id . '-row">';
      }
      echo '<td class="manage-column manage-cache-id">' . $psxml->Cache_id . '</td>';
      echo '<td class="manage-column manage-call-ui">' . $psxml->URL . ' ( <a href="#" class="xml-show">show JSON cache data</a> )<textarea style="display:none;width:100%;height:150px;">' . htmlspecialchars( $bod ) . '</textarea>' . $errtxt . '</td>';
      echo '<td class="manage-column manage-updated">' . $psxml->updated . '</td>';
      echo '<td class="manage-column manage-last-col"><a href="#" class="button appip-cache-del" id="appip-cache-' . $psxml->Cache_id . '">' . __( 'delete cache', 'amazon-product-in-a-post-plugin' ) . '</a></td>';
      echo '</tr>';
      $appct++;
    }
  } else {
    echo '<tbody id="the-list"><tr class="alternate iedit appip-cache-0-row"><td colspan="4">' . __( 'no cached products at this time.', 'amazon-product-in-a-post-plugin' ) . '</td></tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '		<div style="text-align:right;margin:15px"><a href="#" class="button appip-cache-del button-primary" id="appip-cache-0">' . __( 'Delete Cache For ALL Products', 'amazon-product-in-a-post-plugin' ) . '</a></div>';
  echo '	</div>';
  echo '</div>';
}

function apipp_shortcode_help_page() {
  if ( !current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'amazon-product-in-a-post-plugin' ) );
  }
  $current_tab = isset( $_GET[ 'tab' ] ) ? esc_attr( $_GET[ 'tab' ] ) : 'blocks';
  $pageTxtArr = array();
  $pageTxtArr[] = '<div class="wrap">';
  $pageTxtArr[] = '	<h2>' . __( 'Amazon Product In a Post Shortcode Usage', 'amazon-product-in-a-post-plugin' ) . '</h2>';

  $pageTxtArr[] = '<h2 class="nav-tab-wrapper">';
  $pageTxtArr[] = '	<a id="blocks" class="appiptabs nav-tab ' . ( $current_tab == 'blocks' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=blocks">' . __( 'Gutenberg Blocks', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="basics" class="appiptabs nav-tab ' . ( $current_tab == 'basics' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=basics">' . __( 'Shortcode Basics', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="amazonproducts" class="appiptabs nav-tab ' . ( $current_tab == 'amazonproducts' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=amazonproducts">' . __( 'Product Shortcode', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="amazonelements" class="appiptabs nav-tab ' . ( $current_tab == 'amazonelements' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=amazonelements">' . __( 'Elements Shortcode', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="amazon-product-search" class="appiptabs nav-tab ' . ( $current_tab == 'amazon-product-search' ? 'nav-tab-active' : '' ) . '" href="?page=apipp_plugin-shortcode&tab=amazon-product-search">' . __( 'Search Shortcode', 'amazon-product-in-a-post-plugin' ) . '</a>';
  if ( has_filter( 'amazon_product_shortcode_help_tabs' ) ) {
    $newtabs = apply_filters( 'amazon_product_shortcode_help_tabs', array(), $current_tab );
    if ( is_array( $newtabs ) && !empty( $newtabs ) )
      $pageTxtArr[] = implode( "\n", $newtabs );
  }
  $pageTxtArr[] = '</h2>';

  $pageTxtArr[] = '	<div class="tab-content wrapper appip_shortcode_help">';
  $pageTxtArr[] = '		<div id="blocks-content" class="nav-tab-content' . ( $current_tab == 'blocks' ? ' active' : '' ) . '" style="' . ( $current_tab == 'blocks' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Gutenberg Blocks', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'WordPress 5.0 introduced the new Gutenberg Editor. With the new editor, comes the addition of a new way to display content in the editor. This new mothod uses what is called "Blocks" to allow the user to visually see how the content will be rendered and how it will layout on the page.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'This plugin has several pre-defined blocks available for use in the Gutenberg Editor. If you decide to not use Gutenberg, and want to use the Classic Editor, you can still use shortcodes to render your products. See the Shortcode Basic Tab for more information on Shortcodes.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<h3>' . __( 'Gutenberg Blocks', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '			<p>' . __( 'A block is just a template of sorts, that allows you to easily add content to the editor. This plugin has the following blocks available:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Amazon Product Block', 'amazon-product-in-a-post-plugin' ) . '</strong></a><br/>' . __( 'This is the main block you would use if you want to add a pre-defined looking product or products to your page/post. This will output an entirely formatted Amazon product or products. You have the ability to choose some options and settings to customize the layout a little, but the main layout is set up for most sites to use without much of a hassle.', 'amazon-product-in-a-post-plugin' ) . '</li> ';
  $pageTxtArr[] = '				<li><strong>' . __( 'Amazon Grid Block', 'amazon-product-in-a-post-plugin' ) . '</strong></a><br/>' . __( 'This block will show Amazon products in a grid (column and row) layout. This option requires a little more input from the user, but you can create a pretty nice layout of products. This block has less elements available than the Amazon Product Block and is great for streamlined product layouts.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Amazon Elements Block', 'amazon-product-in-a-post-plugin' ) . '</strong><br/>' . __( 'This block is designed to add single Amazon elements to a page/post. This works great for nesting inside of Column blocks or other blocks where you can nest blocks and content together. You can also use it to simply add a buy button or image to the page (or similar elements).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Amazon Search Block', 'amazon-product-in-a-post-plugin' ) . '</strong><br/>' . __( 'This block allows you to set keywords and display the search results as products. This block is more advanced than the others, requires more user input and requires some knowledge of Amazon\'s Search parameters - but can make adding multiple related products to the page a lot easier than manually adding them.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $addl_lists = apply_filters( 'amazon_product_in_a_post_plugin_blocks_list', array() );
  if ( is_array( $addl_lists ) && !empty( $addl_lists ) )
    $pageTxtArr[] = implode( "\n", $addl_lists );
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '			<h3>' . __( 'Using Amazon Product In A Post Blocks', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '			<p>' . __( 'To use a block, you simply add it to the editor from the blocks list. From there you can adjust the settings to get your product they way you need it. See the videos below, to learn more about each block.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>[video]</p>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="basics-content" class="nav-tab-content' . ( $current_tab == 'basics' ? ' active' : '' ) . '" style="' . ( $current_tab == 'basics' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Shortcode Basics', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'WordPress shortcodes were introduced in WordPress version 2.5. A shortcode is basically a placeholder for content that you want to put in a specific spot in a page or post. The content is usually generated when the page/post is loaded on the front end by the viewer. Shortcodes make it very easy to add all sorts of advanced content without the need to know any programming and without needing to modify any theme or template code.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<h3>' . __( 'Anatomy of a Shortcode', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '			<p>' . __( 'A Shortcode is comprised of a few simple elements. The main thing you will notice is that a shortcode is placed in square brackets', 'amazon-product-in-a-post-plugin' ) . ' ([]). ' . __( 'Inside the brackets you add the <strong>shortcode name</strong> and any <strong>attributes</strong> and values needed to produce the desired effect. The outcome depends on how the shortcode was programmed and the number of attributes can be zero (none) up to an unlimited number - again, depending on how it was programmed.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'In its simplest form, a shortcode is just a name or word inside the brackets and nothing else, like so:', 'amazon-product-in-a-post-plugin' ) . '<br/>';
  $pageTxtArr[] = '			<code>[shortcode]</code></p>';
  $pageTxtArr[] = '			<p>' . __( 'A shortcode can also contain a closing "tag" is you want to include text with the shortcode, like:', 'amazon-product-in-a-post-plugin' ) . '<br/>';
  $pageTxtArr[] = '			<code>[shortcode]' . __( 'Put your content here', 'amazon-product-in-a-post-plugin' ) . '[/shortcode]</code><br/>';
  $pageTxtArr[] = '			' . __( 'Not all shortcodes use closing tags and not all of them allow content text, so check with the documentation when you use one for a specific plugin or theme.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>Most shortcodes have multiple attributes that you can set if you want to have different outcomes when the content is generated. Attributes (also called "Parameters") and their Value are entered in a "keyed pair" type manner, which is <code>attribute="value"</code>. The attributes allowed and their allowed value are all determined by the shortcode creator.<br/>';
  $pageTxtArr[] = '			' . __( 'Examples:', 'amazon-product-in-a-post-plugin' ) . ' <code>[shortcode title="shortcode title" text_color="red"]' . __( 'Your Content Here', 'amazon-product-in-a-post-plugin' ) . '[/shortcode]</code> ' . __( 'or', 'amazon-product-in-a-post-plugin' ) . ' <code>[shortcode title="shortcode title" text_color="green"]</code></p>';
  $pageTxtArr[] = '			<p>' . __( 'Once you know what attributes you can use and the acceptable values, you can add them to do whatever you want - again, depending on what they are for and how they are programmed.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'The Amazon Product In a Post Plugin comes with several shortcodes for you to use. They each have their own set of allowed Attributes/Parameters.', 'amazon-product-in-a-post-plugin' ) . ' <em>' . __( 'Click the name to see how to use each one:', 'amazon-product-in-a-post-plugin' ) . '</em></p>';
  $pageTxtArr[] = '			<ul>';
  $pageTxtArr[] = '				<li><a href="?page=apipp_plugin-shortcode&tab=amazonproducts" class="amazonproducts"><strong>AMAZONPRODUCTS</strong></a><br/>' . __( 'The main Shortcode. You can also use', 'amazon-product-in-a-post-plugin' ) . ' "amazonproducts" ' . __( 'all lowercase. This will output an entirely formatted Amazon product (the same as if you do not use a shortcode for your products).', 'amazon-product-in-a-post-plugin' ) . '</li> ';
  $pageTxtArr[] = '				<li><a href="?page=apipp_plugin-shortcode&tab=amazonelements" class="amazonelements"><strong>amazon-elements</strong></a><br/>' . __( 'A Shortcode specifically designed to make adding individual elements of an Amazon product. Can also use the singular', 'amazon-product-in-a-post-plugin' ) . ' "amazon-element".</li>';
  $pageTxtArr[] = '				<li><a href="?page=apipp_plugin-shortcode&tab=amazon-product-search" class="amazon-product-search"><strong>amazon-product-search</strong></a><br/>' . __( 'A Shortcode for displaying Amazon search results.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $addl_lists = apply_filters( 'amazon_product_in_a_post_plugin_shortcode_list', array() );
  if ( is_array( $addl_lists ) && !empty( $addl_lists ) )
    $pageTxtArr[] = implode( "\n", $addl_lists );
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="amazonproducts-content" class="nav-tab-content' . ( $current_tab == 'amazonproducts' ? ' active' : '' ) . '" style="' . ( $current_tab == 'amazonproducts' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '		<h2><a name="amazonproducts"></a>[amazonproducts] ' . __( 'Shortcode', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '		<p>' . __( 'Usage in the most basic form is simply the Shortcode and the ASIN written as follows (where the XXXXXXXXX is the Amazon ASIN):', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '			<code>[amazonproducts asin="XXXXXXXXXX"]</code>';
  $pageTxtArr[] = '			<p>' . __( 'Please note that the following alias shortcodes where added for backward compatibility: ', 'amazon-product-in-a-post-plugin' ) . '<code>AMAZONPRODUCTS</code>, <code>AMAZONPRODUCT</code>, <code>amazonproduct</code></p>';
  $pageTxtArr[] = '			<p>' . __( 'There are additional parameters that can be added if you need them are listed below with a description of each:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '		<ul>';
  $pageTxtArr[] = '			<li><code>asin</code> &mdash; ' . __( 'this is the ASIN or ASINs comma separated. <em>This is the only required parameter.</em>', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>desc</code> &mdash; ' . __( 'using 1 shows Amazon description (if available) and 0 hides it &mdash; default is 0.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>features</code> &mdash; ' . __( 'using 1 shows Amazon Features (if available) and 0 hides it  &mdash; default is 0.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>show_list</code> &mdash; ' . __( 'using 1 shows the List Price and 0 hides it &mdash; default is 1.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>show_used</code> &mdash; ' . __( 'using 1 shows the Used Price and 0 hides it &mdash; default is 1.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>show_new</code> &mdash; ' . __( 'using 1 shows the New Price and 0 hides it &mdash; default is 1.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>template</code> &mdash; ' . __( 'used mostly for Gutenberg Block Layout. Default is \'default\'. Others include ', 'amazon-product-in-a-post-plugin' ) . '<code>light</code>, <code>light-reversed</code>, <code>light-image-top</code>, <code>dark</code>, <code>dark-reversed</code>, <code>dark-image-top</code></li>';
  $pageTxtArr[] = '			<li><code>gallery</code> &mdash; ' . __( 'using 1 shows extra photos/gallery &mdash; default is 0.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>replace_title</code> &mdash; ' . __( 'Replace product with your own title. If using multiple ASINs, you can separate the titles for each, using \'::\', for example: ', 'amazon-product-in-a-post-plugin' ) . '<code>Title One::Title Two::My Title Three</code>, etc.</li>';
  $pageTxtArr[] = '			<li><code>title_charlen</code> &mdash; ' . __( 'using a number greater than 0 will trim the title to that number of characters. Anything above 150 will show full title. default is 0 (show full title).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>hide_title</code> &mdash; ' . __( 'using 1 hides the title and 0 shows the title &mdash; default is 0 (title will be shown).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>hide_image</code> &mdash; ' . __( 'using 1 hides the Large Product Image and 0 shows it &mdash; default is 0 (large image will be shown).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>hide_lg_img_text</code> &mdash; ' . __( 'using 1 hides the "See larger Image" Link and 0 shows it &mdash; default is 0 (link will be shown).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>hide_release_date</code> &mdash; ' . __( 'using 1 hides the Release Date for Games or Pre-orders and 0 shows it (only when present) &mdash; default is 0 (release date will be shown when available).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>single_only</code> &mdash; ' . __( 'using 1 shows the product on a Single page/post only (not on archive or blogroll) and 0 shows it on single or list pages/posts &mdash; default is 0 (product will be shown on single or list pages).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>image_count</code> &mdash; ' . __( 'this is the number of images to show for the Gallery. Only used when the ', 'amazon-product-in-a-post-plugin' ) . '<code>gallery</code>' . __( ' parameter flag is set to 1 &mdash; default is -1 (show all). Other options are 1 to 10 - anything over 10 shows all.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>is_block</code> &mdash; ' . __( 'this is a special parameter to tell if this is a Block element or a shortcode element. <i>Used internally only.</i> &mdash; default is 0 (0 for shortcode or 1 for block).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>use_carturl</code> &mdash; ' . __( 'using \'true\' will use the Cart URL link and \'false\' will use the product page link &mdash; default is \'false\' (product page link).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>button</code> &mdash; ' . __( 'this is for the new HTML Buttons. can be any valid registered HTML Button &mdash; default is blank. Available core pluign buttons can be seen', 'amazon-product-in-a-post-plugin' ) . ' <a href="?page=apipp_plugin-button-url" class="amazonproducts">here</a>.</li>';
  $pageTxtArr[] = '			<li><code>align</code> &mdash; ' . __( 'not used at this time.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>className</code> &mdash; ' . __( 'this is for the Gutenberg additional className attribute. You could also use it to pass an additional class name to the product wrapper. Comma or space seperate multiple class names.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>locale</code> &mdash; ' . __( 'this is the Amazon locale you want to get the product from, i.e., com, co.uk, fr, etc. default is your plugin setting.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>partner_id</code> &mdash; ' . __( 'allows you to add a different parent ID if different for other locale &mdash; default is ID in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>private_key</code> &mdash; ' . __( 'allows you to add different private key for locale if different &mdash; default is private key in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>public_key</code> &mdash; ' . __( 'allows you to add a different private key for locale if different &mdash; default is public key in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>listprice</code> &mdash; ' . __( 'add for backward compatibily only - see this parameter for usage: ', 'amazon-product-in-a-post-plugin' ) . '<code>show_list</code></li>';
  $pageTxtArr[] = '			<li><code>list_price</code> &mdash; ' . __( 'add for backward compatibily only - see this parameter for usage: ', 'amazon-product-in-a-post-plugin' ) . '<code>show_list</code></li>';
  $pageTxtArr[] = '			<li><code>used_price</code> &mdash; ' . __( 'add for backward compatibily only - see this parameter for usage: ', 'amazon-product-in-a-post-plugin' ) . '<code>show_used</code></li>';
  $pageTxtArr[] = '			<li><code>usedprice</code> &mdash; ' . __( 'add for backward compatibily only - see this parameter for usage: ', 'amazon-product-in-a-post-plugin' ) . '<code>show_used</code></li>';
  $pageTxtArr[] = '			<li><code>new_price</code> &mdash; ' . __( 'add for backward compatibily only - see this parameter for usage: ', 'amazon-product-in-a-post-plugin' ) . '<code>show_new</code></li>';
  $pageTxtArr[] = '		</ul>';
  $pageTxtArr[] = '			<p>' . __( 'Examples of it&rsquo;s usage:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '		<ul>';
  $pageTxtArr[] = '			<li>' . __( 'If you want to add a .com item and you have the same partner id, public key, private key and want the features showing:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM" features="1"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'Show the Description, Features, New Price, Gallery with 4 images, and a Blue Buy from Amazon Button:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM" features="1" desc="1" gallery="1" image_count="4" show_used="0" show_list="0" button="buy-from-blue-rounded"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'If you want to add a .com item and you have a different partner id, public key, private key and want the description showing but features not showing:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM,B005LAIHPE" locale="com" public_key="AKIAJDRNJ6O997HKGXW" private_key="Nzg499eVysc5yjcZwrIV3bhDti/OGyRHEYOWO005" partner_id="mynewid-20"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'If you just want to use your same locale but want 2 items with no list price and features showing:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM,B005LAIHPE" features="1" listprice="0"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'If you just want 2 products with regular settings:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM,B005LAIHPE"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'If you want to add text to a product:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<code>[amazonproducts asin="B0084IG8TM"]your text can go here![/amazonproducts]</code></li>';
  $pageTxtArr[] = '		</ul>';
  $pageTxtArr[] = '		<hr/>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="amazonelements-content" class="nav-tab-content' . ( $current_tab == 'amazonelements' ? ' active' : '' ) . '" style="' . ( $current_tab == 'amazonelements' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '		<div class="appip_elements_code"><a name="amazonelements"></a>';
  $pageTxtArr[] = '			<h2>[amazon-elements] ' . __( 'Shortcode', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'shortcode implementation for elements only &mdash; for when you may only want specific element(s) like the title, price and image or image and description, or the title and the buy now button, etc.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul>';
  $pageTxtArr[] = '			<li><code>asin</code> &mdash; <span style="color:#ff0000;"> ' . __( 'Required', 'amazon-product-in-a-post-plugin' ) . ' </span>' . __( 'the Amazon ASIN (up to 10 comma sep).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>fields</code> &mdash; ' . __( 'the product fields to show for this product, comma separated. Default fields are:', 'amazon-product-in-a-post-plugin' ) . ' <code>image,title,button</code></li>';
  $pageTxtArr[] = '			<li><code>labels</code> &mdash; ' . __( 'Labels that correspond to the fields (if you want custom labels). They should match the fields and be comma separated and :: separated for the field name and value i.e., field name::label text,field-two::value 2, etc. (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>use_carturl</code> &mdash; ' . __( 'using \'true\' will use the Cart URL link and \'false\' will use the product page link &mdash; default is \'false\' (product page link).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>button</code> &mdash; ' . __( 'this is for the new HTML Buttons. can be any valid registered HTML Button &mdash; default is blank. Available core pluign buttons can be seen', 'amazon-product-in-a-post-plugin' ) . ' <a href="?page=apipp_plugin-button-url" class="amazonproducts">here</a>.</li>';
  $pageTxtArr[] = '			<li><code>button_url</code> &mdash; ' . __( 'URL for a button image, if you want to use a different image than the default one. ASIN Specific - separate the list of URLs with a comma to correspond with the ASIN. i.e., if you had 3 ASINs and wanted the first and third to have custom buttons, but the second to have the default button, use <code>button_url="http://first.com/image1.jpg,,http://first.com/image1.jpg"</code> (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>button_use_carturl</code> &mdash; ' . __( 'using \'true\' will make the button use the Cart URL. setting use_carturl to true will make this true as well, unless you specifically set to false.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>image_count</code> &mdash; ' . __( 'this is the number of images to show for the Gallery. Only used when the ', 'amazon-product-in-a-post-plugin' ) . '<code>gallery</code>' . __( ' parameter flag is set to 1 &mdash; default is -1 (show all). Other options are 1 to 10 - anything over 10 shows all.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>msg_instock</code> &mdash; ' . __( 'the in stock message can be overridden here &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>In Stock</code></li>';
  $pageTxtArr[] = '			<li><code>msg_outofstock</code> &mdash; ' . __( 'the out of stock message can be overridden here &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>Out of Stock</code></li>';
  $pageTxtArr[] = '			<li><code>template</code> &mdash; ' . __( 'used mostly for Gutenberg Block Layout. Default is \'default\'. Other option: ', 'amazon-product-in-a-post-plugin' ) . '<code>grid</code></li>';
  $pageTxtArr[] = '			<li><code>container</code> &mdash; ' . __( 'the HTML container wrapper element &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>div</code>' . __( 'Do not use &lt; or &gt;.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>container_class</code> &mdash; ' . __( 'the class name for the outer wapper &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>amazon-element-wrapper</code>.</li>';
  $pageTxtArr[] = '			<li><code>className</code> &mdash; ' . __( 'this is for the Gutenberg additional className attribute. You could also use it to pass an additional class name to the product wrapper. Comma or space seperate multiple class names.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>title_charlen</code> &mdash; ' . __( 'using a number greater than 0 will trim the title to that number of characters. Anything above 150 will show full title. default is 0 (show full title).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>single_only</code> &mdash; ' . __( 'using 1 shows the product on a Single page/post only (not on archive or blogroll) and 0 shows it on single or list pages/posts &mdash; default is 0 (product will be shown on single or list pages).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>target</code> &mdash; ' . __( 'the target name when open in a new window setting is active &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>_blank</code></li>';
  $pageTxtArr[] = '			<li><code>newWindow</code> &mdash; ' . __( 'using 1 will open the product links in a new window &mdash; default is 0 (product open in the same window).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>is_block</code> &mdash; ' . __( 'this is a special parameter to tell if this is a Block element or a shortcode element. <i>Used internally only.</i> &mdash; default is 0 (0 for shortcode or 1 for block).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>locale</code> &mdash; ' . __( 'the amazon locale, i.e., co.uk, es. This is handy of you need a product from a different locale than your default one. Applies to all ASINs in list. (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>partner_id</code> &mdash; ' . __( 'your amazon partner id. default is the one in the options. You can set a different one here if you have a different one for another locale or just want to split them up between multiple ids. Applies to all ASINs in list. (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>private_key</code> &mdash; ' . __( 'amazon private key. Default is one set in options. You can set a different one if needed for another locale. Applies to all ASINs in list. (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>public_key</code> &mdash; ' . __( 'amazon public key. Default is one set in options. You can set a different one if needed for another locale. Applies to all ASINs in list. (optional)', 'amazon-product-in-a-post-plugin' ) . '</li>';
  //$pageTxtArr[] = '			<li><code>field</code> &mdash; ' . __( 'alias of ', 'amazon-product-in-a-post-plugin' ) . '<code>fields</code>.</li>';
  //$pageTxtArr[] = '			<li><code>charlen</code> &mdash; ' . __( 'the max length of text fields &mdash; default is 0 (show all).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  //$pageTxtArr[] = '			<li><code>replace_title</code> &mdash; ' . __( 'Replace product with your own title. You can separate the titles for each, using \'::\', for example: ', 'amazon-product-in-a-post-plugin' ) . '<code>Title One::Title Two::My Title Three</code>, etc.</li>';
  //$pageTxtArr[] = '			<li><code>showformat</code> &mdash; '.__('show or hide the format in the title i.e., &quot;Some Title (DVD)&quot; or &quot;Some Title (BOOK)&quot;. 1 to show 0 to hide. Applies to all ASINs. Default is 1. (optional)', 'amazon-product-in-a-post-plugin').'</li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p>' . __( 'Example of usage:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul>';
  $pageTxtArr[] = '				<li>' . __( 'if you want to have a product with only a large image, the title and button, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '					<code>[amazon-element asin=&quot;0753515032&quot; fields=&quot;title,lg-image,large-image-link,button&quot;]</code></li>';
  $pageTxtArr[] = '				<li>' . __( 'If you want that same product to have the description, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '					<code>[amazon-element asin=&quot;0753515032&quot; fields=&quot;title,lg-image,large-image-link,<span style="color:#FF0000;">desc</span>,button&quot;]</code></li>';
  $pageTxtArr[] = '				<li>' . __( 'If you want that same product to have the list price and the new price, you would use:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '					<code>[amazon-element asin=&quot;0753515032&quot; fields=&quot;title,lg-image,large-image-link,desc,<span style="color:#FF0000;">ListPrice,new-price,button&quot; msg_instock=&quot;in Stock&quot; msg_outofstock=&quot;no more left!&quot;</span>]</code><br>';
  $pageTxtArr[] = '			      ' . __( 'The msg_instock and msg_outofstock are optional fields.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li>' . __( 'If you want to add some of your own text to a product, and makeit part of the post, you could do something like this:<br>', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '					<code>[amazon-element asin=&quot;0753515032&quot; fields=&quot;title,lg-image,large-image-link&quot; labels=&quot;large-image-link::click for larger image:,title-wrap::h2,title::Richard Branson: Business Stripped Bare&quot;]Some normal content text here.[amazon-element asin=&quot;0753515032&quot; fields=&quot;desc,gallery,ListPrice,new-price,LowestUsedPrice,button&quot; labels=&quot;desc::Book Description:,ListPrice::SRP:,new-price::New From:,LowestUsedPrice::Used From:&quot; msg_instock=&quot;Available&quot;]</code></li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<h4>' . __( 'Available Fields for the shortcode:', 'amazon-product-in-a-post-plugin' ) . '</h4>';
  $pageTxtArr[] = '			<h3>' . __( 'Common Items', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '			' . __( 'These are generally common in all products (if available)', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '			<ul class="as_code">';
  $pageTxtArr[] = '				<li>' . 'asin - <span class="small-text">' . __( 'Product Identification Number.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'URL - <span class="small-text">' . __( 'Product page URL on Amazon.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'title - <span class="small-text">' . __( 'Product Title.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'price or new-price - <span class="small-text">' . __( 'Product Sale Price.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'SmallImage or sm-image - <span class="small-text">' . __( 'Product Small Image URL.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'MediumImage or image or med-image - <span class="small-text">' . __( 'Product Medium Image URL.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'LargeImage or lg-image - <span class="small-text">' . __( 'Product Large Image URL.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'AddlImages or gallery- <span class="small-text">' . __( 'Product Additional Images.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'feature - <span class="small-text">' . __( 'Product Featured Items Text.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'Format - <span class="small-text">' . __( 'Product Format. I.e., DVD, Blu-ray, etc.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'PartNumber - <span class="small-text">' . __( 'Product Part Number.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'ProductGroup - <span class="small-text">' . __( 'Product Category. I.e., Books, Sproting Goods, etc.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'ProductTypeName - <span class="small-text">' . __( 'Product Category Name. I.e., CAMERA_DIGITAL', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'ISBN - <span class="small-text">' . __( 'Product ISBN number.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'ItemDesc or desc or description- <span class="small-text">' . __( 'Product Description.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'ListPrice or list - <span class="small-text">' . __( 'Product Manufacturer\'s Suggested Retail Price (SRP).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'SKU - <span class="small-text">' . __( 'Product\'s Unique Stock Keeping Unit (SKU).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'UPC - <span class="small-text">' . __( 'Universal Product Code, which is a 12 digit number, 6 of which represents an item\'s manufacturer. These numbers are translated into a bar code that is printed on an item or its packaging.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . 'CustomerReviews - <span class="small-text">' . __( 'Product Customer Reviews (shown in an iframe only).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<h3>' . __( 'Offer/Pricing Elements', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '<p>' . __( '			These are generally returned for most products.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul class="as_code">';
  $pageTxtArr[] = '				<li>LowestNewPrice</li>';
  $pageTxtArr[] = '				<li>LowestUsedPrice</li>';
  $pageTxtArr[] = '				<li>LowestRefurbishedPrice</li>';
  $pageTxtArr[] = '				<li>LowestCollectiblePrice</li>';
  $pageTxtArr[] = '				<li>MoreOffersUrl</li>';
  $pageTxtArr[] = '				<li>NewAmazonPricing</li>';
  $pageTxtArr[] = '				<li>TotalCollectible</li>';
  $pageTxtArr[] = '				<li>TotalNew</li>';
  $pageTxtArr[] = '				<li>TotalOffers</li>';
  $pageTxtArr[] = '				<li>TotalRefurbished</li>';
  $pageTxtArr[] = '				<li>TotalUsed</li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<h3>' . __( 'Items Attributes', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '<p>' . __( '			Available only to their select product groups and not available in all locales. Try it first to see if it returns a value. For example, the Actor field is not going to be returned if the product is a computer or some form of electronics, but would be returned if the product was a DVD or Blu-ray Movie.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul class="as_code">';
  $pageTxtArr[] = '				<li>Actor</li>';
  $pageTxtArr[] = '				<li>Artist</li>';
  $pageTxtArr[] = '				<li>AspectRatio</li>';
  $pageTxtArr[] = '				<li>AudienceRating</li>';
  $pageTxtArr[] = '				<li>AudioFormat</li>';
  $pageTxtArr[] = '				<li>Author</li>';
  $pageTxtArr[] = '				<li>Binding</li>';
  $pageTxtArr[] = '				<li>Brand</li>';
  $pageTxtArr[] = '				<li>CatalogNumberList</li>';
  $pageTxtArr[] = '				<li>Category</li>';
  $pageTxtArr[] = '				<li>CEROAgeRating</li>';
  $pageTxtArr[] = '				<li>ClothingSize</li>';
  $pageTxtArr[] = '				<li>Color</li>';
  $pageTxtArr[] = '				<li>Creator</li>';
  $pageTxtArr[] = '				<li>Department</li>';
  $pageTxtArr[] = '				<li>Director</li>';
  $pageTxtArr[] = '				<li>EAN</li>';
  $pageTxtArr[] = '				<li>EANList</li>';
  $pageTxtArr[] = '				<li>Edition</li>';
  $pageTxtArr[] = '				<li>EISBN</li>';
  $pageTxtArr[] = '				<li>EpisodeSequence</li>';
  $pageTxtArr[] = '				<li>ESRBAgeRating</li>';
  $pageTxtArr[] = '				<li>Genre</li>';
  $pageTxtArr[] = '				<li>HardwarePlatform</li>';
  $pageTxtArr[] = '				<li>HazardousMaterialType</li>';
  $pageTxtArr[] = '				<li>IsAdultProduct</li>';
  $pageTxtArr[] = '				<li>IsAutographed</li>';
  $pageTxtArr[] = '				<li>IsEligibleForTradeIn</li>';
  $pageTxtArr[] = '				<li>IsMemorabilia</li>';
  $pageTxtArr[] = '				<li>IssuesPerYear</li>';
  $pageTxtArr[] = '				<li>ItemDimensions</li>';
  $pageTxtArr[] = '				<li>ItemPartNumber</li>';
  $pageTxtArr[] = '				<li>Label</li>';
  $pageTxtArr[] = '				<li>Languages</li>';
  $pageTxtArr[] = '				<li>LegalDisclaimer</li>';
  $pageTxtArr[] = '				<li>MagazineType</li>';
  $pageTxtArr[] = '				<li>Manufacturer</li>';
  $pageTxtArr[] = '				<li>ManufacturerMaximumAge</li>';
  $pageTxtArr[] = '				<li>ManufacturerMinimumAge</li>';
  $pageTxtArr[] = '				<li>ManufacturerPartsWarrantyDescription</li>';
  $pageTxtArr[] = '				<li>MediaType</li>';
  $pageTxtArr[] = '				<li>Model</li>';
  $pageTxtArr[] = '				<li>ModelYear</li>';
  $pageTxtArr[] = '				<li>MPN</li>';
  $pageTxtArr[] = '				<li>NumberOfDiscs</li>';
  $pageTxtArr[] = '				<li>NumberOfIssues</li>';
  $pageTxtArr[] = '				<li>NumberOfItems</li>';
  $pageTxtArr[] = '				<li>NumberOfPages</li>';
  $pageTxtArr[] = '				<li>NumberOfTracks</li>';
  $pageTxtArr[] = '				<li>OperatingSystem</li>';
  $pageTxtArr[] = '				<li>PackageDimensions</li>';
  $pageTxtArr[] = '				<li>PackageDimensionsWidth</li>';
  $pageTxtArr[] = '				<li>PackageDimensionsHeight</li>';
  $pageTxtArr[] = '				<li>PackageDimensionsLength</li>';
  $pageTxtArr[] = '				<li>PackageDimensionsWeight</li>';
  $pageTxtArr[] = '				<li>PackageQuantity</li>';
  $pageTxtArr[] = '				<li>PictureFormat</li>';
  $pageTxtArr[] = '				<li>Platform</li>';
  $pageTxtArr[] = '				<li>ProductTypeSubcategory</li>';
  $pageTxtArr[] = '				<li>PublicationDate</li>';
  $pageTxtArr[] = '				<li>Publisher</li>';
  $pageTxtArr[] = '				<li>RegionCode</li>';
  $pageTxtArr[] = '				<li>ReleaseDate</li>';
  $pageTxtArr[] = '				<li>RunningTime</li>';
  $pageTxtArr[] = '				<li>SeikodoProductCode</li>';
  $pageTxtArr[] = '				<li>ShoeSize</li>';
  $pageTxtArr[] = '				<li>Size</li>';
  $pageTxtArr[] = '				<li>Studio</li>';
  $pageTxtArr[] = '				<li>SubscriptionLength</li>';
  $pageTxtArr[] = '				<li>TrackSequence</li>';
  $pageTxtArr[] = '				<li>TradeInValue</li>';
  $pageTxtArr[] = '				<li>UPCList</li>';
  $pageTxtArr[] = '				<li>Warranty</li>';
  $pageTxtArr[] = '				<li>WEEETaxValue </li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		</div>';
  $pageTxtArr[] = '		<div id="amazon-product-search-content" class="nav-tab-content' . ( $current_tab == 'amazon-product-search' ? ' active' : '' ) . '" style="' . ( $current_tab == 'amazon-product-search' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '		<h2><a name="amazon-product-search"></a>[amazon-product-search] ' . __( 'Shortcode', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '		<p>' . __( 'Usage in the most basic form is the Shortcode and the keywords written as follows:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '			<code>[amazon-product-search keywords="Deadpool Mask"]</code>';
  $pageTxtArr[] = '			<p>' . __( 'There are additional parameters that can be added if you need to refine your search. They are listed below with a description of each:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '		<ul>';

  $pageTxtArr[] = '			<li><code>keywords</code> &mdash; ' . __( 'this is keywords to use for the search &mdash; required.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>search_title</code> &mdash; ' . __( 'using 1 will turn the keyword search into a title only search (more narrow search) &mdash; default is 0.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>fields</code> &mdash; ' . __( 'the product fields to show for this product, comma separated. Default fields are:', 'amazon-product-in-a-post-plugin' ) . ' <code>image,title,button</code></li>';
  $pageTxtArr[] = '			<li><code>search_index</code> &mdash; ' . __( 'the search index to use for the Amazon Search &mdash; default is \'All\'. Some options are: ', 'amazon-product-in-a-post-plugin' ) . '<code>All</code>,<code>Blended</code>,<code>DVD</code>,<code>Wireless</code>,<code>Toys</code>,<code>Electronics</code>,<code>Books</code>.';
  $pageTxtArr[] = '			<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . __( 'NOTES:', 'amazon-product-in-a-post-plugin' ) . '</strong> &mdash; ' . __( 'When performing a title search, the search_index cannot be \'All\' or \'Blended\'. ', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>condition</code> &mdash; ' . __( 'The condition of the products to display &mdash; default is \'New\'. Available options: ', 'amazon-product-in-a-post-plugin' ) . '  <code>New</code> | <code>Used</code> | <code>Collectible</code> | <code>Refurbished</code> | <code>All</code></li>';
  $pageTxtArr[] = '			<li><code>sort</code> &mdash; ' . __( 'the sorting column to use for returned products  &mdash; default is \'titlerank\'.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>item_count</code> &mdash; ' . __( 'the number of products to display &mdash; default is 10 which is also the maximum.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>item_page</code> &mdash; ' . __( 'the page of products in the search result to return &mdash; default is 1 (first page of results). Max page is 5 for \'All\' or \'Blended\' search index or 10 for everything else.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>browse_node</code> &mdash; ' . __( 'use this to refine your search. Cannot be used when `search_index` is \'All\' or \'Blended\' &mdash; default is none.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>page</code> &mdash; ' . __( 'alias for item_page.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>template</code> &mdash; ' . __( 'used mostly for Gutenberg Block Layout. Default is \'default\'. Other option: ', 'amazon-product-in-a-post-plugin' ) . '<code>grid</code></li>';
  $pageTxtArr[] = '			<li><code>msg_instock</code> &mdash; ' . __( 'the in stock message can be overridden here &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>In Stock</code></li>';
  $pageTxtArr[] = '			<li><code>msg_outofstock</code> &mdash; ' . __( 'the out of stock message can be overridden here &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>Out of Stock</code></li>';
  $pageTxtArr[] = '			<li><code>button</code> &mdash; ' . __( 'this is for the new HTML Buttons. can be any valid registered HTML Button &mdash; default is blank. Available core pluign buttons can be seen', 'amazon-product-in-a-post-plugin' ) . ' <a href="?page=apipp_plugin-button-url" class="amazonproducts">here</a>.</li>';
  $pageTxtArr[] = '			<li><code>use_cartURL</code> &mdash; ' . __( 'using \'true\' will use the Cart URL link and \'false\' will use the product page link &mdash; default is \'false\' (product page link).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>title_charlen</code> &mdash; ' . __( 'using a number greater than 0 will trim the title to that number of characters. Anything above 150 will show full title. default is 0 (show full title).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>single_only</code> &mdash; ' . __( 'using 1 shows the product on a Single page/post only (not on archive or blogroll) and 0 shows it on single or list pages/posts &mdash; default is 0 (product will be shown on single or list pages).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>image_count</code> &mdash; ' . __( 'this is the number of images to show for the Gallery. Only used when the ', 'amazon-product-in-a-post-plugin' ) . '<code>gallery</code>' . __( ' parameter flag is set to 1 &mdash; default is -1 (show all). Other options are 1 to 10 - anything over 10 shows all.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>is_block</code> &mdash; ' . __( 'this is a special parameter to tell if this is a Block element or a shortcode element. <i>Used internally only.</i> &mdash; default is 0 (0 for shortcode or 1 for block).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>className</code> &mdash; ' . __( 'this is for the Gutenberg additional className attribute. You could also use it to pass an additional class name to the product wrapper. Comma or space seperate multiple class names.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>target</code> &mdash; ' . __( 'the target name when open in a new window setting is active &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>_blank</code></li>';
  $pageTxtArr[] = '			<li><code>button_url</code> &mdash; ' . __( 'use this to set an image button. Add the full URL for the button &mdash; default is blank.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>container</code> &mdash; ' . __( 'the HTML container wrapper element &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>div</code>' . __( 'Do not use &lt; or &gt;.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>container_class</code> &mdash; ' . __( 'the class name for the outer wapper &mdash; default is ', 'amazon-product-in-a-post-plugin' ) . '<code>amazon-element-wrapper</code>.</li>';
  $pageTxtArr[] = '			<li><code>labels</code> &mdash; ' . __( 'the labels to use for selected fields. Use \':\' to sepatate field and label values.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>locale</code> &mdash; ' . __( 'this is the Amazon locale you want to get the product from, i.e., com, co.uk, fr, etc. default is your plugin setting.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>partner_id</code> &mdash; ' . __( 'allows you to add a different parent ID if different for other locale &mdash; default is ID in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>private_key</code> &mdash; ' . __( 'allows you to add different private key for locale if different &mdash; default is private key in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li><code>public_key</code> &mdash; ' . __( 'allows you to add a different private key for locale if different &mdash; default is public key in settings.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  //$pageTxtArr[] = '			<li><code>replace_title</code> &mdash; ' . __( 'Replace product with your own title. You can separate the titles for each, using \'::\', for example: ', 'amazon-product-in-a-post-plugin' ) . '<code>Title One::Title Two::My Title Three</code>, etc.</li>';
  //$pageTxtArr[] = '			<li><code>page</code> &mdash; ' . __( 'alias of ', 'amazon-product-in-a-post-plugin' ) . '<code>item_page</code>.</li>';
  //$pageTxtArr[] = '			<li><code>field</code> &mdash; ' . __( 'alias of ', 'amazon-product-in-a-post-plugin' ) . '<code>fields</code>.</li>';
  //$pageTxtArr[] = '			<li><code>availability</code> &mdash; ' . __( 'the only options for this are \'Available\' or none.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  //$pageTxtArr[] = '			<li><code>charlen</code> &mdash; ' . __( 'the max length of text fields &mdash; default is 0 (show all).', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p>' . __( 'Examples of it&rsquo;s usage:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '		<ul>';
  $pageTxtArr[] = '			<li>' . __( 'Title Search with red Buy From button:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '				<code>[amazon-product-search keywords="Deadpool Mask" title_search="1" button="buy-from-red-rounded"]</code></li>';
  $pageTxtArr[] = '			<li>' . __( 'Keyword Search with Grid Layout, blue Buy From button and price:', 'amazon-product-in-a-post-plugin' ) . '<br>';
  $pageTxtArr[] = '				<code>[amazon-product-search keywords="Deadpool Mask" template="grid" fields="image,title,price,button" button="buy-from-blue-rounded"]</code></li>';
  $pageTxtArr[] = '		</ul>';
  $pageTxtArr[] = '		<hr/>';
  $pageTxtArr[] = '			<h4>' . __( 'Available Fields for the shortcode:', 'amazon-product-in-a-post-plugin' ) . '</h4>';
  $pageTxtArr[] = '			<h3>' . __( 'Common Items', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '			' . __( 'These are generally common in all products (if available)', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '			<ul class="as_code">';
  $pageTxtArr[] = '				<li>' . '<code>title</code> - <span class="small-text">' . __( 'Product Title.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>desc</code> or <code>description</code> - <span class="small-text">' . __( 'Product Description.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>price</code> or <code>new-price</code> or <code>new price</code> - <span class="small-text">' . __( 'Product Price.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>price+list</code> - <span class="small-text">' . __( 'Shows both the Product list price and sale price.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>image</code> - <span class="small-text">' . __( 'Product Image (Medium Image).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>sm-image</code> - <span class="small-text">' . __( 'Product Small Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>med-image</code> - <span class="small-text">' . __( 'Product Medium Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>lg-image</code> - <span class="small-text">' . __( 'Product Large Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>full-image</code> - <span class="small-text">' . __( 'Product Full Image.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>large-image-link</code> - <span class="small-text">' . __( 'Large Image Link (shows "See Larger Image" link).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>link</code> - <span class="small-text">' . __( 'Product Page Link (shows full link in anchor tag).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>AddlImages</code> or <code>gallery</code> or <code>imagesets</code> - <span class="small-text">' . __( 'Product Additional Images.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>features</code> - <span class="small-text">' . __( 'Product Featured Items Text.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>ListPrice</code> or <code>list</code> - <span class="small-text">' . __( 'Product Manufacturer\'s Suggested Retail Price (SRP).', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>new-button</code> - <span class="small-text">' . __( 'No Longer Used.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>button</code> - <span class="small-text">' . __( 'Displays default Image button or HTML button if a button templte was passed as a shortcode parameter.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '				<li>' . '<code>customerreviews</code> - <span class="small-text">' . __( 'Product Customer Reviews (shown in an iframe only). Not ideal for Grid layout.', 'amazon-product-in-a-post-plugin' ) . '</span></li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p>' . __( 'There are other fields available - bascially any field returned in the API can be used. For a more complete list, see', 'amazon-product-in-a-post-plugin' ) . ' <a href="?page=apipp_plugin-shortcode&tab=amazonelements" class="amazonelements"><strong>amazon-elements</strong></a> ' . __( 'shortcode page.', 'amazon-product-in-a-post-plugin' ) . '</p>';
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
  if ( has_filter( 'amazon_product_shortcode_help_content' ) ) {
    $newcontent = apply_filters( 'amazon_product_shortcode_help_content', array(), $current_tab );
    if ( is_array( $newcontent ) && !empty( $newcontent ) ) {
      $pageTxtArr[] = implode( "\n", $newcontent );
    }
  }

  $pageTxtArr[] = '	</div>';
  $pageTxtArr[] = '</div>';
  echo implode( "\n", $pageTxtArr );
  unset( $pageTxtArr );
}

function apipp_main_page() {
  global $current_user, $wpdb;
  if ( !current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'amazon-product-in-a-post-plugin' ) );
  }
  $current_tab = isset( $_GET[ 'tab' ] ) ? esc_attr( $_GET[ 'tab' ] ) : 'getting-started-one';
  $pageTxtArr = array();
  $pageTxtArr[] = '<div class="wrap">';
  $pageTxtArr[] = '	
	<style type="text/css">
	small{font-size:13px;color:#777;line-height: 19px;}
	.nav-tab-content > div{margin-left:15px;}
	.nav-tab-content > div p{margin-left:25px;}
	.nav-tab-content > div img{margin:20px 10px 20px 10px;}
	.nav-tab-content ul{list-style-type: none;margin: 25px 0 25px 28px;border-left: 10px solid #eaeaea;padding-left: 16px;}
	.nav-tab-content blockquote{font-style: italic;margin-top: 20px; margin-bottom: 20px;border: 1px solid #ccc;padding: 20px;border-width: 1px 0;}
	</style>';
  $pageTxtArr[] = '	<h2>' . __( 'Amazon Product In A Post - GETTING STARTED', 'amazon-product-in-a-post-plugin' ) . '</h2>';

  //echo '	<div class="wrapper">';

  $pageTxtArr[] = '<h2 class="nav-tab-wrapper">';
  $pageTxtArr[] = '	<a id="getting-started-one" class="appiptabs nav-tab ' . ( $current_tab === 'getting-started-one' ? 'nav-tab-active' : '' ) . '" href="?page=apipp-main-menu&tab=getting-started-one">' . __( 'Amazon Affiliate Account', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="getting-started-two" class="appiptabs nav-tab ' . ( $current_tab === 'getting-started-two' ? 'nav-tab-active' : '' ) . '" href="?page=apipp-main-menu&tab=getting-started-two">' . __( 'Amazon Product Advertising API Sign-up', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="getting-started-three" class="appiptabs nav-tab ' . ( $current_tab === 'getting-started-three' ? 'nav-tab-active' : '' ) . '" href="?page=apipp-main-menu&tab=getting-started-three">' . __( 'Next Steps', 'amazon-product-in-a-post-plugin' ) . '</a>';
  $pageTxtArr[] = '	<a id="getting-started-four" class="appiptabs nav-tab ' . ( $current_tab === 'getting-started-four' ? 'nav-tab-active' : '' ) . '" href="?page=apipp-main-menu&tab=getting-started-four">' . __( 'Need Help?', 'amazon-product-in-a-post-plugin' ) . '</a>';
  if ( has_filter( 'amazon_product_getting_started_help_tabs' ) )
    $pageTxtArr[] = apply_filters( 'amazon_product_getting_started_help_tabs', $current_tab );
  $pageTxtArr[] = '</h2>';

  $pageTxtArr[] = '	<div class="tab-content wrapper appip_getting_started_help">';

  $pageTxtArr[] = '		<div id="getting-started-one-content" class="nav-tab-content' . ( $current_tab == 'getting-started-one' ? ' active' : '' ) . '" style="' . ( $current_tab == 'getting-started-one' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Setting Up an Amazon Affiliate Account', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'There are 2 steps to using this plug-in to make additional income as an Amazon Affiliate. The first is to sign up for an Amazon Affiliate Account. The second is to get a set of Product Advertising API keys so the plug-in can access the product API and return the correct products. Both of these steps are a little intense, but if you have about 15-20 minutes, you can set up everything you need to start making money.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<div>';
  $pageTxtArr[] = '				<h3>' . __( 'Step 1 - Getting Your Amazon Affiliate/Partner ID', 'amazon-product-in-a-post-plugin' ) . '</h3>';
  $pageTxtArr[] = '				<p>' . __( 'Sign up for your Amazon Affiliate/Partner account at one of the following URLs (choose the correct link based on your Amazon location):', 'amazon-product-in-a-post-plugin' );
  $pageTxtArr[] = '				<ul>';
  $pageTxtArr[] = '					<li>' . __( 'Australia (com.au):', 'amazon-product-in-a-post-plugin' )        . '<a href="https://affiliate-program.amazon.com.au/">https://affiliate-program.amazon.com.au/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Brazil (com.br):', 'amazon-product-in-a-post-plugin' )           . ' <a href="https://associados.amazon.com.br/">https://associados.amazon.com.br/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Canada (ca):', 'amazon-product-in-a-post-plugin' )               . ' <a href="https://associates.amazon.ca/">https://associates.amazon.ca/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'China (cn):', 'amazon-product-in-a-post-plugin' )                . ' <a href="https://associates.amazon.cn/">https://associates.amazon.cn/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'France (fr):', 'amazon-product-in-a-post-plugin' )               . ' <a href="https://partenaires.amazon.fr/">https://partenaires.amazon.fr/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Germany (de):', 'amazon-product-in-a-post-plugin' )              . ' <a href="https://partnernet.amazon.de/">https://partnernet.amazon.de/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'India (in):', 'amazon-product-in-a-post-plugin' )                . ' <a href="https://affiliate-program.amazon.in/">https://affiliate-program.amazon.in/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Italy (it):', 'amazon-product-in-a-post-plugin' )                . ' <a href="https://programma-affiliazione.amazon.it/">https://programma-affiliazione.amazon.it/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Japan (co.jp):', 'amazon-product-in-a-post-plugin' )             . ' <a href="https://affiliate.amazon.co.jp/">https://affiliate.amazon.co.jp/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Mexico (com.mx):', 'amazon-product-in-a-post-plugin' )           . ' <a href="https://afiliados.amazon.com.mx/">https://afiliados.amazon.com.mx/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Netherlands (nl):', 'amazon-product-in-a-post-plugin' )          . ' <a href="https://partnernet.amazon.nl/">https://partnernet.amazon.nl/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Saudi Arabia (sa):', 'amazon-product-in-a-post-plugin' )         . ' <a href="https://affiliate-program.amazon.sa/">https://affiliate-program.amazon.sa/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Singapore (sg):', 'amazon-product-in-a-post-plugin' )            . ' <a href="https://affiliate-program.amazon.sg/">https://affiliate-program.amazon.sg/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Spain (es):', 'amazon-product-in-a-post-plugin' )                . ' <a href="https://afiliados.amazon.es/">https://afiliados.amazon.es/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'Sweden (se):', 'amazon-product-in-a-post-plugin' )               . ' <a href="https://affiliate-program.amazon.se/">https://affiliate-program.amazon.se/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'United Arab Emirates (ae):', 'amazon-product-in-a-post-plugin' ) . ' <a href="https://affiliate-program.amazon.ae/">https://affiliate-program.amazon.ae/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'United Kingdom (co.uk):', 'amazon-product-in-a-post-plugin' )    . ' <a href="https://affiliate-program.amazon.co.uk/">https://affiliate-program.amazon.co.uk/</a> </li>';
  $pageTxtArr[] = '					<li>' . __( 'United States (com):', 'amazon-product-in-a-post-plugin' )       . ' <a href="https://affiliate-program.amazon.com/">https://affiliate-program.amazon.com/</a> </li>';
  $pageTxtArr[] = '				</ul>';
  $pageTxtArr[] = '				<p>' . __( 'Amazon requires that you have a different affiliate ID for each country (aka, locale).', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '				<p>' . __( 'Since the Affiliate signup has not changed much over the years, and it is not too difficult, I will not go into it in any more detail. Follow the steps until you are issued your affiliate partner ID. Paste that into the plug-in options page.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			</div>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '			<a class="button button-primary" href="?page=apipp-main-menu&tab=getting-started-two">Next Step &raquo;</a>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="getting-started-two-content" class="nav-tab-content' . ( $current_tab == 'getting-started-two' ? ' active' : '' ) . '" style="' . ( $current_tab == 'getting-started-two' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Step 2 - Signing Up for the Amazon Product Advertising API', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'This next step can be a little frustrating and one of the most time consuming. Not for the amount of actual time it takes to sign up, but for the time you may have to wait to get your API approval.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'After you have created your Amazon Affiliate Account, sign in. Then go to "TOOLS" and "Product Advertising API". If your account was approved previously (if you already had an account), then you can move right on to signing up for the Product Advertising API.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p><b>' . __( 'If your account is not yet approved, there are some things you need to be aware of:', 'amazon-product-in-a-post-plugin' ) . '</b></p>';
  $pageTxtArr[] = '			<ul style="list-style-type: disc;border-left: 0 none;"><li>' . __( 'As of May 1, 2018, Amazon now requires complete approval of your affiliate account before you can use the Product Advertising API. This makes it difficult to use this plugin immediately for most people. If you already have an approved Affiliate account from prior to May 1, 2018, then it should be much easer for you.' ) . '</li>';
  $pageTxtArr[] = '			<li>' . __( 'If your account requires approval (most will unless you already have one from prior), you will see an information message like this on the Advertising API page. This means they are still reviewing your site/application for the Amazon Affiliate program and you will have to wait until that process is completed before you can use this plugin fully.' );
  $pageTxtArr[] = '			<br><img border="0" alt="Amazon API Notice" src="' . plugins_url( '/images/api-notice.jpg', dirname( __FILE__ ) ) . '" style="width:100%;max-width:1094px;height:auto;"></li>';
  $pageTxtArr[] = '			<li><b>' . __( 'The approval process takes time!', 'amazon-product-in-a-post-plugin' ) . '</b> ' . __( 'Amazon will not fully approve the affiliate account until after you make a few sales. They require 3 sales in the first 180 days after signup, before they will review your site for complete approval.' ) . '</li>';
  $pageTxtArr[] = '			<li>' . __( 'Using Amazon\'s other link building methods (located on the affiliate site), you will need to create a few links to start generating traffic and get a few sales. You can start setting up your site (if you have not done so already) and add the links into products or sidebars.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li>' . __( 'After you generate a few sales, you will then be able to use the plugin to create actual product layouts on your site.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			<li>' . __( 'See the "Next Steps" tab for some helpful tips on getting approved by Amazon.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p><span class="updated">' . __( 'IMPORTANT! Once you get access to the Amazon Product Advertisig API and receive your API Keys, DO NOT give then outto just anyone. Intentionally disclosing your Secret Key to other parties is against Amazon\'s terms of use and is considered grounds for account suspension or deletion (without payment of any due earnings). They take the key security very seriously and you can be held accountable for any misuse of your keys, should you give them out to anyone. So keep them secret. If you request help from us to solve an issue, we may ask you to change your keys after we are done helping you - just so you can feel safe and secure about the secrecy of your keys.', 'amazon-product-in-a-post-plugin' ) . '</span></p>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '			<a class="button button-primary" href="?page=apipp-main-menu&tab=getting-started-three">Next Steps &raquo;</a>';
  $pageTxtArr[] = '			<div style="margin-top: 40px;margin-left: 0;">';
  $pageTxtArr[] = '				<h2>' . __( 'MISC Information', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '				<p>' . __( 'If already have an approved Amazon Affiliate Account and you are using the Amazon IAM Management Console, your Access Key ID will be located under the "Your Security Credentials" page. They will NOT show you your Secret Access Key here. If you loose it, you MUST generate a new Root Key.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '				<p>' . __( 'After you generate the Root Key, it will serve the browser with a csv file that has both the Access Key ID and the Secret Access Key inside.', 'amazon-product-in-a-post-plugin' ) . '<br><img border="0" src="' . plugins_url( '/images/signup-step-misc1.png', dirname( __FILE__ ) ) . '" width="545" height="360"> <img border="0" src="' . plugins_url( '/images/signup-step-misc2.png', dirname( __FILE__ ) ) . '" width="545" height="358"></p>';
  $pageTxtArr[] = '			</div>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '			<a class="button button-primary" href="?page=apipp-main-menu&tab=getting-started-three">Next Steps &raquo;</a>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="getting-started-three-content" class="nav-tab-content' . ( $current_tab == 'getting-started-three' ? ' active' : '' ) . '" style="' . ( $current_tab == 'getting-started-three' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Next Steps', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'To ensure that your Amazon Affiliate application is accepted, you will need to follow some trusted guidelines:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<ul style="list-style-type: disc;border-left: 0 none;">';
  $pageTxtArr[] = '				<li><strong>' . __( 'Your website needs to be LIVE.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'If your website is not live, or you have an "under construction page" or a Maintenance page displayed, you will not be approved.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Add the required Disclaimer to your site.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'See below for information on the disclaimer. Amazon WILL NOT approve your site without one', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Set up a few products links.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'If you are a new affiliate, your site needs to have some links or buttons to Amazon products. Amazon wants to see that you are linking correctly according to their terms of service agreement.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Have some unique content.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'You need to have some other content besides just Amazon Links or Products. If all you have is a site with links to Amazon, you will not be approved. Add some content to your pages like your own review or even some helpful information about the product you are trying to sell. The most successful Amazon affiliates use products to enhance their unique content.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Limit Banners/Ads on the site.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'If your site uses an lot of advertising but does not have a lot of content, then Amazon will not approve you. They do not like sites that are Ad heavy. You should have more content than you do advertisements. Ad heavy sites look like revenue traps to the visitor and Amazon does not want to be associated with that.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Make your products relevant to your site\'s focus/market', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'Try to use relevant products whenever possible. For example, if you blob about Home Gardening, use products related to Home Gardening - not TVs or Electronics. Amazon will see you are serious about your affiliate account if you have relative products with a good proportion of unique content to products.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'Be Patient.', 'amazon-product-in-a-post-plugin' ) . '</strong><br>' . __( 'It can take several weeks or longer for Amazon to review everything and they will not fully approve the application until after you refer 3 sales in within the first 180 days after you sign up.', 'amazon-product-in-a-post-plugin' ) . '</li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p>' . __( 'After your affiliate account is approved, you can make some "tweaks" if needed - mainly you can start adding products via the plugin to make things easier. Don\'t immediately throw in tons of products or stop adding content and only products. If Amazon gets a complaint or they think you are abusing the terms of use, they can review the site again and decide to revoke your affiliate account. Once they do that, you have very little chance of getting it back.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '			<h2>' . __( 'An Amazon Disclaimer for your site', 'Amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'All Amazon Affiliates that sell products on their websites are required to have a disclaimer on their site in a visible location that states that they are earning money from Amazon sales. We recommend that you add something like the following statement to your site footer or in a sidebar widget near the bottom of your site (change <strong>[Website Name]</strong> to your website name):', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<blockquote>' . __( '[Website Name] is a participant in the Amazon Services LLC Associates Program, an affiliate advertising program designed to provide a means for website owners to earn advertising fees by advertising and linking to amazon.com, audible.com, and any other website that may be affiliated with Amazon Service LLC Associates Program. As an Amazon Associate [I or we] earn from qualifying purchases.</span>', 'amazon-product-in-a-post-plugin' ) . '</blockquote>';
  $pageTxtArr[] = '			<p>' . __( 'If you want to use a shorter disclaimer, you must, at the very least, use something like this:', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<blockquote>' . __( 'As an Amazon Associate [I or we] earn from qualifying purchases.</span>', 'amazon-product-in-a-post-plugin' ) . '</blockquote>';
  $pageTxtArr[] = '			<p>' . __( 'The above disclaimer is the correct minimum according to Amazon\'s Terms of Service/Use as of May 1, 2018. This may change so be sure to check the terms of use regularly.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '		</div>';

  $pageTxtArr[] = '		<div id="getting-started-four-content" class="nav-tab-content' . ( $current_tab == 'getting-started-four' ? ' active' : '' ) . '" style="' . ( $current_tab == 'getting-started-four' ? 'display:block;' : 'display:none;' ) . '">';
  $pageTxtArr[] = '			<h2>' . __( 'Need Help?', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  $pageTxtArr[] = '			<p>' . __( 'If you need help trying to figure out what you need to do to be approved, or you want us to help you set up your site so you will be approved, please let us know.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'We do charge a very modest fee for this service. Costs generally range from about $50 to $250 depending on how much help you need setting everything up.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<p>' . __( 'Please email us at', 'amazon-product-in-a-post-plugin' ) . '<strong> <a href="mailto:'.APIAP_HELP_EMAIL.'">'.APIAP_HELP_EMAIL.'</a></strong></p>';
  $pageTxtArr[] = '			<p><strong>' . __( 'What we CAN help with:', 'amazon-product-in-a-post-plugin' ) . '</strong></p>';
  $pageTxtArr[] = '			<ul style="list-style-type: disc;border-left: 0 none;">';
  $pageTxtArr[] = '				<li><strong>' . __( 'Give you guidance on what you need to do on your site to increase your chances of being approved by Amazon.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We can add disclaimers to your site that comply with Amazon\'s terms of service.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We can add Amazon Banners or Promotions.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We can give suggestions on the type of links or products that are a right fit for your site.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We can help fix general WordPress errors and issues.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p><strong>' . __( 'What we CAN\'T help with:', 'amazon-product-in-a-post-plugin' ) . '</strong></p>';
  $pageTxtArr[] = '			<ul style="list-style-type: disc;border-left: 0 none;">';
  $pageTxtArr[] = '				<li><strong>' . __( 'We cannot sign up for your Affiliate Account or Product Advertising API account for you.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We cannot set up all of your products for you (well, <em>we can</em>, but the costs will be much greater than $250).', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We cannot generate Amazon sales/traffic for you.', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '				<li><strong>' . __( 'We cannot create your website for you (again, <em>we can</em>, but the costs will be much greater than $250).', 'amazon-product-in-a-post-plugin' ) . '</strong></li>';
  $pageTxtArr[] = '			</ul>';
  $pageTxtArr[] = '			<p>' . __( 'If you need help with anything other than plugin related items, please contact us for a quote on our services - i.e., General WordPress consulting, Theme Programming/Modifications, Plugin creation/modification - or just about any WordPress related item.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $pageTxtArr[] = '			<hr/>';
  $pageTxtArr[] = '		</div>';

  if ( has_filter( 'amazon_product_getting_started_help_content' ) )
    $pageTxtArr[] = apply_filters( 'amazon_product_getting_started_help_content', $current_tab );

  $pageTxtArr[] = '	</div>';
  $pageTxtArr[] = '</div>';
  echo implode( "\n", $pageTxtArr );
  unset( $pageTxtArr );
  //echo '	</div>';
  //echo '</div>';
}

function apipp_options_faq_page() {
  echo '
		<div class="wrap">
			<style type="text/css">
				.faq-item{border-bottom:1px solid #CCC;padding-bottom:10px;margin-bottom:10px;}
				.faq-item span.qa{color: #21759B;display: block;float: left;font-family: serif;font-size: 17px;font-weight: bold;margin-left: 0;margin-right: 5px;}
				 h3.qa{color: #21759B;margin:0px 0px 10px 0;font-family: serif;font-size: 17px;font-weight: bold;}
				.faq-item .qa-content p:first-child{margin-top:0;}
				.apipp-faq-links {border-bottom: 1px solid #CCCCCC;list-style-position: inside;margin:10px 0 15px 35px;}
				.apipp-faq-answers{list-style-position: inside;margin:10px 0 15px 35px;}
				.toplink{text-align:left;}
				.qa-content div > code{background: none repeat scroll 0 0 #EFEFEF;border: 1px solid #CCCCCC;display: block;margin-left: 35px;overflow-y: auto;padding: 10px 20px;white-space: nowrap;width: 90%;}
			</style>
			<div class="icon32" style="background: url(' . plugins_url( "/", dirname( __FILE__ ) ) . 'images/aicon.png) no-repeat transparent;"><br/></div>
		 	<h2>' . __( 'Amazon Product in a Post FAQs/Help', 'amazon-product-in-a-post-plugin' ) . '</h2>
			<div align="left"><p>' . sprintf( __( 'The FAQS are now on a feed that can be updated on the fly. If you have a question and don\'t see an answer, please send an email to %1$s and ask your question. If it is relevant to the plugin, it will be added to the FAQs feed so it will show up here. Please be sure to include the plugin you are asking a question about (Amazon Product in a Post Plugin), the Debugging Key (located on the options page) and any other information like your WordPress version and examples if the plugin is not working correctly for you. THANKS!', 'amazon-product-in-a-post-plugin' ), '<a href="mailto:'.APIAP_HELP_EMAIL.'">'.APIAP_HELP_EMAIL.'</a>' ) . '</p>
			<hr noshade color="#C0C0C0" size="1" />
		';
  $faqs = array(
	  [
		  'q' => 'What&#8217;s New in version 5.0',
		  'a' => '<p>The biggest items in version 5.0 are the following:</p>
                <ul>
                    <li>Now uses the new Amazon PA API Version 5.0</li>
                    <li>Gutenberg Blocks (for products, grids, searches and elements)</li>
                    <li>Bug Fixes (a lot of them)</li>
                    <li>Templates for both shortcodes and blocks.</li>
                </ul>
                <p>Most of version 5 of the plugin is a re-write of the core code to accommodate the new Amazon API version. But we did try to get some additional features in there &#8211; most namely the Blocks. We also tried to clean up the Amazon pricing info so it is more consistent and tried to make the shortcode parameters more consistent across all of the shortcodes.</p>'
	  ],

	  [
		  'q' => 'In Version 5.0+ of the plugin, the Description is missing &#8211; can you fix that?',
		  'a' => '<p>Sadly, no. The plugin now uses the PA API V5 and Amazon discontinued several data sets in the new API. Of those, the Description is probably the biggest one that people notice the most.</p>
                <p>They may add it back in the future if enough people complain, but for now, the best thing you can do is to use the &#8216;Features&#8217; field. That is not there 100% of the time, but it does give some product description data when available. </p>'
	  ],

	  [
		  'q' => 'Can I go back the Amazon PA API version 4.0?',
		  'a' => '<p>No. Unfortunately, Amazon has given the 4.0 version API an end of life date as of March 9, 2020. Although the API may work for a little while after this date, there is no guarantee that Amazon will not just shut it off without warning. </p>'
	  ],

	  [
		  'q' => 'Does the plugin support the new Amazon PA API 5.0?',
		  'a' => '<p>Yes, it does. As of the plugin version 5.0, the new API version is part of the plugin.</p>'
	  ],

	  [
		  'q' => 'Do you support Blocks for New Gutenberg Editor?',
		  'a' => '<p>Yes! As of version 5.0, you can use blocks to add Amazon Products, Amazon Product Grids, Amazon Search Results and individual Amazon Elements.</p>'
	  ],

	  [
		  'q' => 'How do I get Access to the Amazon Advertising API?',
		  'a' => '<p>Effective May 1, 2018, your Amazon affiliate account needs to be fully approved before you can gain access to the API. So this means you cannot use the plugin right away if you do not already have an approved affiliate account prior to this date. To be fully approved, you need to have everything set up on your site (it must be live and have some product links already added). Additionally, you must also have the Amazon Affiliate disclaimer and follow all the terms of use.</p>
                <p>For more details, see the &#8220;Getting Started&#8221; page for help on getting approved.</p>'
	  ],

	  [
		  'q' => 'This Seems Like A Lot Of Work, Is It Really Worth It &#8211; How Much Will Really Make?',
		  'a' => '<p>Good question &#8211; and good point. Yes, the plugin is a bit of a pain in the you-know-what to get set up.</p>
                <p>For the first part of the question &#8211; sadly, we don&#8217;t have too much control over the setup process. Amazon has their own way of having affiliates set everything up, and they have dozens of services and APIs. Because of this, it does sometimes get to be a bit of a process to set up. A few key things to remember when setting up a new Amazon affiliate account are:</p>
                <ol>
                    <li>There are multiple steps to set up an account. You need to set up as an affiliate, then you need to register for the Product Advertising API and then you need to get your API keys to add to the plugin. You have to do all three or it will not work.</li>
                    <li>If you already have an Amazon affiliate account, you have to make sure you are registered for the Product Advertising API. It is VERY different from the other APIs and even if you are registered with another one (like amazon AWS or the Marketplace), you STILL need to be signed up for the Product Advertising API.</li>
                    <li>THIS ONE IS VERY IMPORTANT &#8211; and also one of the causes of the most issues. You must (I repeat, MUST) use ROOT keys for the plugin because Amazon Product Advertising API does not allow use of Group or User keys. We did not decide this &#8211; it is an Amazon thing. And when you log into get your keys, no matter how many times you read Amazon&#8217;s suggestion message saying to use identity management and set up groups or user keys  &#8211; you cannot use Group or User Keys for the Product Advertising API &#8211; they DO NOT ALLOW IT.</li>
                    <li>Always check your keys. Make sure when you copy them, you get all the characters (20 for Access Key ID and 40 for Secret Key) and make sure there are no spaces at the beginning or end (this is also a very common cause for errors). You made it that far in setting everything up &#8211; don&#8217;t let a little copy and paste error frustrate the living hell out of you until you give up.</li>
                </ol>
                <p>Ok &#8211; on to the second part of the question &#8211; &#8220;How much will I really make?&#8221;</p>
                <p>That is more of a difficult question to answer. That really depends on several factors &#8211; what products you have, how much traffic your site gets, what type of visitors you get and several other factors. If you just list products hoping people will buy something, then you will probably no make much. But if you review products, for example, and put the product link in the review, you are bound to make more than just throwing a bunch of products on a page. This has more to do with marketing &#8211; which we will not get into here.</p>
                <p>With that said, we do hear from plugin users all the time that tell us they make a lot of money using the plugin. We had someone recently tell us that they made over $20,000 in commissions in 2014 using the plugin. Another said they made almost $7,000 in December alone. So, the potential is there &#8211; it is just up to you to put in the effort to make it happen.</p>
                <p>One last closing note &#8211; Back in 2012-2013 we set up a demo site with examples of how you could use the plugin for various layouts, etc. We never intended to use it for sales of products, just as a reference. In 2013 we had affiliate revenue from the demo site in excess of $8,500 &#8211; and we were not even trying to make a dime on it. We eventually took the site down because we had too many people asking us to create sites for them, or asking for code or styles to do something a certain way &#8211; we just could not keep up with the requests at that time. But that just goes to show you that not only did we create the plugin, we actually made money on it ourselves &#8211; so we KNOW it works like intended.</p>'
	  ],

	  [
		  'q' => 'Why Do I STILL Get Blank Products Even After I Checked Everything Was Correct?',
		  'a' => '<p>This is a difficult question to answer without knowing all the specifics of your site and setup, but generally it means one of a few things is still not setup correctly:</p>
                <ul>
                    <li>Check your Amazon Access Key and Secret Keys!
                        <ul>
                            <li>Check to make sure you have copied ALL of the characters correctly. The Access Key ID is 20 characters long. The Secret Key is 40 characters long. Anything more or less means it is not correct.</li>
                            <li>Check to make sure both keys do not have a space before or after the characters or anywhere in the key. Keys cannot have spaces.</li>
                            <li>This was said before, but make sure your Keys are ROOT level keys. This means that you cannot use User or Group keys. Amazon Product Advertising API uses only the Root keys and will not allow anything else &#8211; BUT Amazon makes a suggestion to set up Identity Management and use the group and user keys. This is great for other APIs, but the suggestion does not apply to the Product Advertising API (not sure if they will ever allow it at this point).</li>
                        </ul>
                    </li>
                    <li>Check that your ASINs for the products you are using are from the locale you are set to use.
                        <ul>
                            <li>You cannot use products from Amazon.de if you have set everything up for Amazon.com. The ASINs are NOT the same in every locale. Some may be, but the details and pricing may not be the same &#8211; and in many cases, the ASIN in one locale can be an entirely different product in another.</li>
                        </ul>
                    </li>
                    <li>How many products are you trying to load on one page?  Amazon has a throttling system in place that blocks API calls if you make too many (both per/second and per/hour), so make sure you are not doing too many requests in your pages.
                        <ul>
                            <li>The Plugin, in it&#8217;s default mode, makes an API call to Amazon for each product you add. 14 products means 14 calls to the API &#8211; all in the first few seconds or micro-seconds before the site loads. Amazon only allows an initial usage limit of 1 request per second &#8211; so 14 uncached product calls in a second will throw an error. Additionally, there is an hourly API request limit of 2000 requests per hour initially for a new account. You earn more requests per hour (and per second) the more sales you refer to Amazon. The exact formula is posted in their terms, but the basic gist of it is:
                                <ul>
                                    <li>Hourly request limit per account = 2,000 + ( 500 * [Average associate revenue driven per day over the past 30 days period]/24). So lets say you refer and sell an average of $50/day over the last 30 days, your request limit would be (2,000 + ((500 * 50) /24)) which equals 3,041 requests per hour. You can earn up to 25,000 additional request per hour based on the sales (which would mean you need to earn $1,200/day average for a 30 day period) to get the most requests allowed by Amazon &#8211; which would be 27,000/hour.</li>
                                    <li>Calls per second limit = 1 + round ([last 30 days shipped Sales])/$4,600). So if you did $5,000 in sales last month on Amazon, you would get an extra 1 request per second ($5,000 / $4,600 )  = 1.08 (rounded to 1)  + your initial 1 request would make 2 requests per second. The maximum requests per hour is 11 (1 plus an additional 10). In order to get that number, you would have to do $46,000 in sales last month!</li>
                                </ul>
                            </li>
                            <li>If possible, use bulk requests. This means, if you want to add 5 products to a page, do them in one request. You can do up to 10 products in one request, just separate the ASINs with a comma. This will limit your requests to 1 and help eliminate the throttling that Amazon does when you reach your limit.</li>
                        </ul>
                    </li>
                    <li>The plugin caches the results for 60 min for every API call. This helps keep the plugin from making too many calls to the API and having Amazon throttle the results. It does not stop it however. You still need to make sure you are keeping the number of products manageable on each page. If you keep trying different things and you do end up making too many calls per second, you may have to clear the product cache in order to see the product again (as the throttled request may be stored in the cache for the next 60 min). Clear the cache whenever you have an error on a page &#8211; but keep in mind that if you exceeded your hourly requests clearing it will not get the products to load. You have to wait for the next hour to be reached until Amazon clears the throttle on their end.</li>
                </ul>
                <p>When we initially created the plugin many years ago, Amazon allowed a developer set of keys to be used and there was no throttling in place &#8211; so users could literally install the plugin, add their affiliate ID and be ready to go with unlimited products. Since then, Amazon has continually changed their API usage terms and made it increasingly difficult to get everything set up.</p>
                <p>Even with the added difficulties, it is still beneficial to add products to your site and a user that manages their products effectively can still make a good some of money using the plugin &#8211; it just takes a little patience and creativity.</p>'
	  ],

	  [
		  'q' => 'What Keys can I use for the plugin, Root or User/Group IAM keys?',
		  'a' => '<p>The only keys that the Product Advertising API allows at this time are the Root AWS/IAM Keys (Access Key ID and Secret Key).</p>
                <p>Although Amazon recommends using Users and Groups for assigning keys, the Product Advertising API will only validate requests made with the ROOT keys. So, although it is a great idea to assign user and group keys for other AWS APIs, you cannot do it for use with this plugin (because this plugin uses the Product Advertising API to get Product Data).</p>
                <p>Amazon has also stopped displaying the Secret Key for any existing Access Key IDs, so if you do not have yours written down, you will need to go to the Security Credentials page in the AWS IAM Console to create a new set of keys (or delete an old set and create a new set). They will give you the secret key when you create a new set and also allow you to download a copy in CSV format.</p>'
	  ],

	  [
		  'q' => 'I Get a Warning Message After I Added a Product, Why?',
		  'a' => '<p><strong>Version 4.0.0+:</strong><br />
                If you get a <strong>Warning</strong> or <strong>Notice</strong> message when the product loads on the page, it could be that you are using an older (or newer) version of PHP on your hosting site. Some versions use either new methods or older outdated methods. Additionally, it is also possible that there is a problem in plugin that need to be fixed. The first thing to do is to make sure you do not have <strong>WP_DEBUG</strong> turned on. You can check this by looking at your sites <code>wp-config.php</code> file in the root of your site (you will need a file manager or FTP client to check this). On a live site, you should have <strong>WP_DEBUG</strong> turned off (or set to false). This usually stops Warnings and Notices, which are not Errors, but information for the developers and testers to let them know something might not be right (so you don&#8217;t want them showing on your site). If you do not have access to the <code>wp-config.php</code> file, or you have already turned it off, or know it is turned off, you can try to turn on the <em><strong>&#8220;Quick Fix&#8221;</strong></em> option in the plugin settings. This just tells your site not to show Warning or Notices, so it usually stops most of those messages.</p>
                <p>If you get a <strong>Fatal Error</strong> or cannot get the <strong>Warnings/Notices</strong> to go away, let us know. Go to the plugin settings page and send us a debug notice and include a note about the errors/warnings you are getting and we will look into the issue and try to help you resolve the problem (or we will put in a bug fix if there is a problem with the plugin itself).</p>
                <p><strong>Version 3.6.4 or less:</strong><br />
                    If you get the following message, or something similar:</p>
                <p><code>Warning: file_get_contents(http://webservices.amazon.com/...) [function.file-get-contents]: failed to open stream: HTTP request failed! HTTP/1.1 403 Forbidden in .../amazon-product-in-a-post-plugin/inc/aws_signed_request.php on line 649</code></p>
                <p>Then most likely your hosting accounts php settings do not allow fopen or remote URL fopen calls to the Amazon API. This can be changed in some cases in your php.ini file, but for the sake of this FAQ, we will assume it cannot (those that know how to change php.ini files, see this post).</p>
                <p>This can usually be overcome by changing your API Get Method in your plugin settings to use the CURL method as opposed the file_get_contents (fopen). Try changing it to CURL and save the options, then clear your product cache (click <strong>AMAZON PRODUCT/PRODUCT CACHE</strong> in the menu and click the <em>&#8216;delete cache&#8217;</em> button next to each cache line listed). Then try again to see if your problem is fixed.</p>'
	  ],

	  [
		  'q' => 'UPDATED: Can I Stop Loading of the DYNAMIC STYLES?',
		  'a' => '<p><strong>UPDATE:</strong> As of Version 4.0.0, we no longer load styles dynamically.<br />
                Yes, you can stop them from loading.<br />
                Many people have emailed us and asked how to stop the loading of Dynamic Styles for the plugin. There is a filter/action method you can use to remove the default and then add your own from a different location (like your theme folder or your style.css file).</p>
                <p>For those asking what Dynamic Styles are &#8211; They are the styles that are loaded for the plugin dynamically when WordPress outputs the wp_head call of the site. These files do not exist as hard CSS files, but instead are created as a file at loading point. This can slow down some sites with themes or plugins that have a lot of wp_head actions and filters firing on start-up.</p>
                <p>Follow this process to stop dynamic loading and use your own static CSS file (which may be faster in some cases):</p>
                <ol>
                    <li>Go to your plugin options and copy the styles from the style box at the bottom of the page into a new CSS file and save it into your theme directory (call it something like <code>myappipstyles.css</code>, etc.)</li>
                    <li>Open your theme&#8217;s <code>functions.php</code> file &#8211; or child theme <code>functions.php</code> file if using child themes or updating themes.</li>
                    <li>AFTER the opening<code> &lt;?php </code> tag, add the following:<br />
                        <code>remove_action(\'wp_head\',\'aws_prodinpost_addhead\',10);<br />
                            add_action(\'wp_head\',\'aws_prodinpost_addhead_new\',10);<br />
                            function aws_prodinpost_addhead_new(){<br />
                            echo \' &lt;link href="\'.plugins_url().\'/amazon-product-in-a-post-plugin/css/amazon-lightbox.css" rel="stylesheet" media="screen" type="text/css" /&gt;\'."\n";<br />
                            echo \' &lt;link href="\'.get_template_directory_uri().\'/myappipstyles.css" rel="stylesheet" media="screen" type="text/css" /&gt;\'."\n";<br />
                            }</code></li>
                    <li>That is it.<br />
                        If you have caching plugins or WP cache enabled, clear the cache so your site updates.<br />
                        Your styles should now be loaded from your file and not dynamically any longer.</li>
                </ol>
                <p>This will let the light-box type modal effect still work for the images as well as add your own style. Be sure to change the <code>myappipstyles.css</code> above to the name of your actual css file. If you use your <code>style.css</code> file to add the styles to, you will not need the second echo line as your style.css style sheet will already be loaded by the site normally.</p>'
	  ],

	  [
		  'q' => 'Is the shortcode for elements &#8220;amazon-element&#8221; or &#8220;amazon-elements&#8221; (plural)?',
		  'a' => '<p>There has been a little bit of confusion on what the new shortcode for elements is supposed to be. In the documentation we say, <code>&#91;amazon-elements&#93;</code> but then in the examples we use <code>&#91;amazon-element&#93;</code> (note that one is plural and one is not).</p>
                <p>The answer is, you can actually use both. It really does not matter as they are both valid shortcodes and do exactly the same thing.</p>
                <p><strong>So why both?</strong><br />
                    Well, we initially started with <code>&#91;amazon-element&#93;</code> as the only one. Then, during testing we found an odd thing happening &#8211; when someone wanted to use more than one element, they would automatically try using <code>&#91;amazon-elements&#93;</code> instead. We figure that happens because out brains try to pluralize anything that has more than one item automatically, so without realizing it, they were using the plural shortcode and running into problems. So to help everyone out, we decided to just add the additional shortcode &#8211; so you can use it however your brain thinks you should. Use singular for one element and plural for multiple &#8211; or just use the plural for everything &#8211; it is up to you.</p>
                <p>As a bonus, we have had several people ask how to do a grid &#8216;related-post&#8217; style layout with the new shortcode. So here is how you can do it:<br />
                    in the editor (text mode) put (change out your ASINs):</p>
                <div class="code-block"><code><br />
                    &#91;amazon-elements asin="B005LAII4E" fields="title,lg-image,large-image-link,imagesets,desc,new-price,button" msg_instock="" target="_blank" labels="title::G.I. Joe: Retaliation, description::Description:"&#93;<br />
                    &lt;div style="clear: both;"&gt;&lt;/div&gt;<br />
                    &lt;div class="related-posts"&gt;<br />
                    &lt;h3&gt;Other Great Titles:&lt;/h3&gt;<br />
                    &#91;amazon-elements asin="B00BUADSMQ,B00C5W3SBE,B00B769XB8,B00BUC4VS4" fields="image,title,new-price,button" msg_instock="" target="_blank" labels="title::Good Day to Die Hard,title::Oz: Great & Powerful,title::Hansel & Gretel,title::Snitch" container="div"&#93;</code></div>
                <p>Then in the styles &#8211; (in the options page, check the &#8216;use my styles&#8217; then enter the following in styles box):</p>
                <div class="code-block"><code>.related-posts .amazon-element-wrapper{width:20%;float:left;text-align:center;padding:1%;margin:1%;border:1px solid #DEDEDE;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);}<br />
                    .related-posts .amazon-element-wrapper .amazon-image-wrapper{float:none;}<br />
                    .related-posts .amazon-element-wrapper .amazon-element-title h2 {font-size:12px;margin: 0;}<br />
                    .related-posts .amazon-element-wrapper .amazon-element-title h2 a{text-decoration:none;}<br />
                    .related-posts .amazon-element-wrapper .amazon-element-new-price{font-size:12px;}<br />
                    .related-posts .amazon-element-wrapper .label-new-price{display:block;}<br />
                    .related-posts .amazon-element-button img{background: transparent;box-shadow: none;border: none;}<br />
                </code></div>
                <p>You may need to adjust the styles based on your theme, but that would be the basic layout.</p>'
	  ],

	  [
		  'q' => 'Why Does the Price on Some Products Say &#8220;Too Low To Display&#8221;?',
		  'a' => '<p>This is not a glitch in the plugin! This is part of the normal Amazon API functionality.</p>
                <p>Here is the official word from Amazon on why that happens:</p>
                <blockquote><p>Some manufacturers have a minimum advertised price (MAP) that can be displayed on Amazon&#8217;s retail website. When the Amazon price is lower than the MAP, the manufacturer does not allow the price to be shown until the customer takes further action, such as placing the item in their shopping cart, or in some cases, proceeding to the final checkout stage.</p>
                    <p>When performing an <code>ItemSearch</code> or <code>ItemLookup</code> operation in these cases, the string &#8220;Too Low to Display&#8221; is returned instead of the actual price. Customers need to go to Amazon to see the price on the retail website, but won&#8217;t be required to purchase the product.</p></blockquote>
                <p>Now, that does not make it anymore acceptable, but that is why it happens.</p>
                <p>What can you do? Nothing at the moment. BUT, we are working on a workaround to add to the plugin that will return the first offer price if there are multiple sellers. Usually the fist one is the actual main seller anyway, so we hope to be able to return at least a price if there is one. We should have it ready VERY soon.</p>'
	  ],

	  [
		  'q' => 'Help! I Can&#8217;t Use The Amazon New Product Feature &#8211; What&#8217;s Up?',
		  'a' => '<p>There is a slight bug in version 3.5.1 where you cannot add a new product using the &#8220;New Amazon Post&#8221; option (formerly New Amazon PIP). This is being addressed and we will have a fix shortly.</p>
                <p>This bug does not effect the shortcode usage or products currently added &#8211; you just can&#8217;t create a new one using that method.</p>'
	  ],

	  [
		  'q' => 'Do I Need To Change Out My Old Shortcodes?',
		  'a' => '<p>No, you don&#8217;t <em>need</em> to. But, we think you will <em>want</em> to.</p>
                <p>With 3.5.1+, the shortcode allows for many additional features. Some of those features are off by default, so you need to turn them on. All of these features are available only with the new shortcode.</p>
                <p>The basic shortcode is <code>[</code><code>AMAZONPRODUCTS asin="XXXXXXXXXX"]</code> (the XXXXXXXXXX is where the ASIN goes).</p>
                <p>The new parameters are as follows:</p>
                <ul>
                    <li><strong>asin</strong> &#8211; The amazon ASIN (can be up to 10, comma separated)</li>
                    <li><strong>gallery</strong> &#8211; Shows Additional Images Gallery if available (from Amazon) &#8211; 1 to show or 0 to hide (default 0)</li>
                    <li><strong>features</strong> &#8211; Shows product features if available &#8211; 1 to show or 0 to hide (default 0)</li>
                    <li><strong>listprice</strong> &#8211; Show or hide the list price &#8211; 1 to show or 0 to hide (default 1)</li>
                    <li><strong>showformat</strong> &#8211; Show or hide the format in the title &#8211; 1 to show or 0 to hide (default 1)</li>
                    <li><strong>desc</strong> &#8211; Show or hide the description if available &#8211; 1 to show or 0 to hide (default 0)</li>
                    <li><strong>replace_title</strong> &#8211; Title Text if you want to replace the Amazon Title with your own.</li>
                </ul>
                <p>Examples:</p>
                <p><code>[</code><code>AMAZONPRODUCTS asin="B00BUADSMQ" desc="1" showformat="0" features="1" replace_title="My New Title - Great!"]</code></p>
                <p><code>[</code><code>AMAZONPRODUCTS asin="B005LAIH2M" desc="1" showformat="0" features="1" gallery="1" replace_title="My Second New Title - Better!"]</code></p>
                <p><code>[</code><code>AMAZONPRODUCTS asin="B00BUADSMQ,B005LAIH2M" desc="0" showformat="0" listprice="0" gallery="1"]</code></p>'
	  ],

	  [
		  'q' => 'I Upgraded and the Description is Showing &#8211; it Wasn&#8217;t Before. Can I Make it NOT Show?',
		  'a' => '<p>If you update from a version lower than 3.5.1, there are several new features that were added. One of the requested feature was adding the Amazon Description to the product. This is on by default for products that use the really old shortcode.</p>
                <p>An additional feature was a NEW replacement shortcode that has many more features. If you switch your old shortcodes to the new shortcode, the display should look more like it did prior to the update (i.e., no description). The old shortcode was set up like so <code>[</code><code>AMAZONPRODUCT=XXXXXXXXXX]</code>. This is not ideal for adding features so the replacement shortcode uses the WordPress Shortcode system to process the shortcode, making it much easier for us to add parameters (or features) to the products via the shortcode. The replacement shortcode should be used like so:</p>
                <p><code>[</code><code>AMAZONPRODUCTS asin="XXXXXXXXXX"]</code></p>
                <p>if you decide you want the description in the new shortcode, you can turn it on by using:</p>
                <p><code>[</code><code>AMAZONPRODUCTS asin="XXXXXXXXXX" desc="1"]</code></p>
                <p>You can also view the <a href="/wp-admin/admin.php?page=apipp_plugin-shortcode">Shortcode Usage</a> page for all the shortcodes and options available.</p>'
	  ],

	  [
		  'q' => 'Why Do I Get Blank/No Products?',
		  'a' => '<p>There are usually a few reasons this happens.</p>
                <ul>
                    <li>The first, happens when your selecting products that do not belong in the locale that your API is registered in &#8211; i.e., you are picking a product from Amazon.com and your API is registered for Amazon.co.uk. Not all products are available for all locales, and even if it is, it MAY have a completely different ASIN number in different locales. If you want to be sure your product is available, always pick the products from your registered locale.</li>
                    <li>The second seems to me happening more, probably because of the way Amazon requires you to get your API keys now. Check to make sure that there are NO spaces in the Access Key or Secret Keys at the beginning or end. This is sometimes not a space, but a hidden return character or something else, so make sure all that is in those fields are the keys and nothing else.</li>
                    <li>The third is more common and happens when an affiliate registered for the Amazon AWS, but did not register for the Advertising API. The Amazon Advertising API is how the plugin gets its data, so if you are not registered for the Advertising API, Amazon will return a blank item with an error message. This is usually something like:<br />
                        <code>Your AccessKey Id is not registered for Product Advertising API. Please use the AccessKey Id obtained after registering at https://affiliate-program.amazon.com/gp/flex/advertising/api/sign-in.html.</code></li>
                </ul>
                <p>So, check that your products are available in your locale and check to make sure you have registered for the Amazon Advertising API, not just Amazon AWS.</p>
                <p>To register for the Advertising API, go to:</p>
                <ul>
                    <li>United States (com): <a href="https://affiliate-program.amazon.com/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.com/</a></li>
                    <li>United Kingdon (co.uk): <a href="https://affiliate-program.amazon.co.uk/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.co.uk/</a></li>
                    <li>Germany (de): <a href="https://partnernet.amazon.de/" target="_blank" rel="noopener noreferrer">https://partnernet.amazon.de/</a></li>
                    <li>France (fr): <a href="https://partenaires.amazon.fr/" target="_blank" rel="noopener noreferrer">https://partenaires.amazon.fr/</a></li>
                    <li>Japan (jp): <a href="https://affiliate.amazon.co.jp/" target="_blank" rel="noopener noreferrer">https://affiliate.amazon.co.jp/</a></li>
                    <li>Canada (ca): <a href="https://associates.amazon.ca/" target="_blank" rel="noopener noreferrer">https://associates.amazon.ca/</a></li>
                    <li>China (cn): <a href="https://associates.amazon.cn/" target="_blank" rel="noopener noreferrer">https://associates.amazon.cn/</a></li>
                    <li>Italy (it): <a href="https://programma-affiliazione.amazon.it/" target="_blank" rel="noopener noreferrer">https://programma-affiliazione.amazon.it/</a></li>
                    <li>Spain (es): <a href="https://afiliados.amazon.es/" target="_blank" rel="noopener noreferrer">https://afiliados.amazon.es/</a></li>
                    <li>India (in): <a href="https://affiliate-program.amazon.in/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.in/</a></li>
                    <li>Brazil (com.br): <a href="https://associados.amazon.com.br/" target="_blank" rel="noopener noreferrer">https://associados.amazon.com.br/</a></li>
                    <li>Mexico (com.mx): <a href="https://afiliados.amazon.com.mx/" target="_blank" rel="noopener noreferrer">https://afiliados.amazon.com.mx/</a></li>
                    <li>Australia (com.au): <a href="https://affiliate-program.amazon.com.au/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.com.au/</a></li>
                    <li>United Arab Emirates (ae): <a href="https://affiliate-program.amazon.ae/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.ae/</a></li>
                    <li>Singapore (sg): <a href="https://affiliate-program.amazon.sg/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.sg/</a></li>
                    <li>Netherlands (nl): <a href="https://partnernet.amazon.nl/" target="_blank" rel="noopener noreferrer">https://partnernet.amazon.nl/</a></li>
                    <li>Saudi Arabia (sa): <a href="https://affiliate-program.amazon.sa/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.sa/</a></li>
                    <li>Sweden (se): <a href="https://affiliate-program.amazon.se/" target="_blank" rel="noopener noreferrer">https://affiliate-program.amazon.se/</a></li>
                </ul>'
	  ]
  );
  $linkfaq = array();
  $linkcontent = array();
  $aqr = 0;
  foreach ( $faqs as $item ) {
    $aqr++;
    $linkfaq[] = '<li class="faq-top-item"><a href="#faq-' . $aqr . '">' . esc_html( $item['q'] ) . '</a></li>';
    $linkcontent[] = '<li class="faq-item"><a name="faq-' . $aqr . '"></a><h3 class="qa"><span class="qa">Q. </span>' . esc_html( $item['q'] ) . '</h3><div class="qa-content"><span class="qa answer">A. </span>' . $item['a'] . '</div><div class="toplink"><a href="#faq-top">top &uarr;</a></li>';
  }
  echo '<a name="faq-top"></a><h2>' . __( 'Table of Contents', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  echo '<ol class="apipp-faq-links">';
  echo implode( "\n", $linkfaq );
  echo '</ol>';
  echo '<h2>' . __( 'Questions/Answers', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  echo '<ul class="apipp-faq-answers">';
  echo implode( "\n", $linkcontent );
  echo '</ul>';
  echo '
			</div>
		</div>';
}

function apipp_templates() {
  echo '<div class="wrap">';
  echo '<h2>' . __( 'Amazon Styling Options', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  echo '<div id="wpcontent-inner">';
  echo 'This is a future feature.';
  echo '</div>';
  echo '</div>';
}

function apipp_add_new_post() {
  global $user_ID;
  global $current_user;
  //get_currentuserinfo();
  //wp_get_current_user();
  $myuserpost = wp_get_current_user()->ID;
  echo '<div class="wrap"><h2>' . __( 'Add New Amazon Product Post', 'amazon-product-in-a-post-plugin' ) . '</h2>';
  if ( isset( $_GET[ 'appmsg' ] ) && $_GET[ 'appmsg' ] == '1' ) {
    echo '<div style="background-color: rgb(255, 251, 204);" id="message" class="updated fade below-h2"><p><b>' . __( 'Product post has been saved. To edit, use the standard Post Edit options.', 'amazon-product-in-a-post-plugin' ) . '</b></p></div>';
  }
  echo '<p>' . __( 'This function will allow you to add a new post for an Amazon Product - no need to create a post then add the ASIN. Once you add a Product Post, you can edit the information with the normal Post Edit options.', 'amazon-product-in-a-post-plugin' ) . '</p>';
  $ptypes = get_post_types( array( 'public' => true ) );
  $ptypeHTML = '<div class="apip-posttypes">';
  $taxonomies = get_taxonomies( array(), 'objects' );
  $section = '';
  $section .= '<tr class="apip-extra-pad-bot taxonomy_blocks taxonomy_block_page"><td align="left" valign="top">' . __( 'Category/Taxonomy for Pages', 'amazon-product-in-a-post-plugin' ) . ':</td><td align="left">';
  $section .= '<div>' . __( 'No Categories/Taxonomy Available for Pages.', 'amazon-product-in-a-post-plugin' ) . '</div>';
  $section .= '</td></tr>';

  if ( !empty( $taxonomies ) ) {
    foreach ( $taxonomies as $key => $taxCat ) {
      if ( isset( $taxCat->object_type ) && is_array( $taxCat->object_type ) ) {
        foreach ( $taxCat->object_type as $tcpost ) {
          if ( in_array( $tcpost, $ptypes ) && ( $tcpost != 'nav_menu_item' && $tcpost != 'attachment' && $tcpost != 'revision' ) ) {
            $argsapp = array( 'taxonomy' => $key, 'orderby' => 'name', 'hide_empty' => 0 );
            $termsapp = get_terms( $key, $argsapp );
            $countapp = count( $termsapp );
            if ( 'post_format' == $key || 'post_tag' == $key ) {} else {
              $section .= '<tr class="apip-extra-pad-bot taxonomy_blocks taxonomy_block_' . $tcpost . '"><td align="left" valign="top">' . __( 'Category/Taxonomy for ', 'amazon-product-in-a-post-plugin' ) . $tcpost . ':</td><td align="left">';
              if ( $countapp > 0 ) {
                foreach ( $termsapp as $term ) {
                  $section .= '<div class="appip-new-post-cat"><input type="checkbox" name="post_category[' . $tcpost . '][' . $key . '][]" value="' . $term->term_id . '" /> ' . $term->name . '</div>';
                }
              } else {
                $section .= '<div>' . __( 'No Categories/Taxonomy Available for this Post type.', 'amazon-product-in-a-post-plugin' ) . '</div>';
              }
              $section .= '</td></tr>';
            }
          }
        }
      }
    }
  }
  if ( !empty( $ptypes ) ) {
    foreach ( $ptypes as $ptype ) {
      if ( $ptype != 'nav_menu_item' && $ptype != 'attachment' && $ptype != 'revision' ) {
        if ( $ptype == 'post' ) {
          $addlpaaiptxt = ' checked="checked"';
        } else {
          $addlpaaiptxt = '';
        }
        $ptypeHTML .= '<div class="apip-ptype"><label><input class="apip-ptypecb" group="appiptypes" type="radio" name="post_type" value="' . $ptype . '"' . $addlpaaiptxt . ' /> ' . $ptype . '</label></div>';
      }
    }
  }
  $ptypeHTML .= '</div>';
  $extrasec = array();
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-use-cartURL" value="1" /> <label for="amazon-product-use-cartURL"><strong>' . __( "Use Add to Cart URL?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'Uses Add to Cart URL instead of product page URL. Heps with 90 day conversion cookie.', 'amazon-product-in-a-post-plugin' ) . '</em><br />';
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-show-gallery" value="1" /> <label for="amazon-product-show-gallery"><strong>' . __( "Show Image Gallery?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available (Consists of Amazon Approved images only). Not all products have an Amazon Image Gallery.', 'amazon-product-in-a-post-plugin' ) . '</em><br />';
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-show-features" value="1" /> <label for="amazon-product-show-features"><strong>' . __( "Show Amazon Features?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em><br />';
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-show-used-price" value="1" /> <label for="amazon-product-show-used-price"><strong>' . __( "Show Amazon Used Price?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em><br />';
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-show-list-price" value="1" /> <label for="amazon-product-show-list-price"><strong>' . __( "Show Amazon List Price?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>' . __( 'if available. Not all items have this feature.', 'amazon-product-in-a-post-plugin' ) . '</em><br />';
  $extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-amazon-desc" disabled value="1" /> <label for="amazon-product-amazon-desc"><strong><em style="color:#888;">' . __( "Show Amazon Description?", 'amazon-product-in-a-post-plugin' ) . '</em></strong></label> <em style="color:#f00;">No longer available.<!--' . __( 'if available. This will be IN ADDITION TO your own content.', 'amazon-product-in-a-post-plugin' ) . '--></em><br />';
  /* possible remove */
  //$extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-show-saved-amt" value="1" /> <label for="amazon-product-show-saved-amt"><strong>' . __("Show Saved Amount?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>'.__('if available. Not all items have this feature.','amazon-product-in-a-post-plugin').'</em><br />';
  //$extrasec[] = '&nbsp;&nbsp;<input type="checkbox" name="amazon-product-timestamp" value="1" /> <label for="amazon-product-show-timestamp"><strong>' . __("Show Price Timestamp?", 'amazon-product-in-a-post-plugin' ) . '</strong></label> <em>'.__('for example:','amazon-product-in-a-post-plugin').'</em><div class="appip-em-sample">&nbsp;&nbsp;'.__('Amazon.com Price: $32.77 (as of 01/07/2018 14:11 PST').' - <span class="appip-tos-price-cache-notice-tooltip" title="">'.__('Details').'</span>)</div>'.'<br />';
  //$extrasec[] = '<span style="display:none;" class="appip-tos-price-cache-notice">' . __( 'Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on amazon.' . APIAP_LOCALE . ' at the time of purchase will apply to the purchase of this product.', 'amazon-product-in-a-post-plugin' ) . '</span>';

  echo '<form method="post" id="appap-add-new-form" action="' . add_query_arg( array( 'page' => 'apipp-add-new' ), admin_url( 'admin.php' ) ) . '">
		<input type="hidden" name="amazon-product-isactive" id="amazon-product-isactive" value="1" />
		<input type="hidden" name="post_save_type_apipp" id="post_save_type_apipp" value="1" />
		<input type="hidden" name="post_author" id="post_author" value="' . $myuserpost . '" />
		<input type="hidden" name="amazon-product-content-hook-override" id="amazon-product-content-hook-override" value="2" />
		<div align="left">
			<table border="0" cellpadding="2" cellspacing="0" class="apip-new-pppy">
				<tr>
					<td align="left" valign="top">' . __( 'Title', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left"><input type="text" name="post_title" size="65" /><br/><em>If you want the post title to be the title of the product, you can leave this blank and the plugin will try to set the product title as the Post title.</em></td>
				</tr>
				<tr>
					<td align="left" valign="top">' . __( 'Post Status', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left"><select size="1" name="post_status" >
					<option selected>draft</option>
					<option>publish</option>
					<option>private</option>
					</select></td>
				</tr>
				<tr>
					<td align="left" valign="top">' . __( 'Post Type', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left">' . $ptypeHTML . '</td>
				</tr>
				<tr>
					<td align="left" valign="top">' . __( 'Amazon ASIN Number', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left"><input type="text" name="amazon-product-single-asin" size="29" />&nbsp;<em>' . __( 'You can use up to 10 comma separated ASINs.', 'amazon-product-in-a-post-plugin' ) . '</em></td>
				</tr>
				<tr>
					<td align="left" valign="top">' . __( 'Split ASINs?', 'amazon-product-in-a-post-plugin' ) . '</td>
					<td align="left"><input type="checkbox" id="split_asins" name="split_asins" value="1"><em>&nbsp;&nbsp;' . __( 'Check to make all ASINs individual posts/pages', 'amazon-product-in-a-post-plugin' ) . '</em></td>
				</tr>
				<tr class="apip-extra-pad-bot">
					<td align="left" valign="top">' . __( 'Post Content', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left">
					<textarea rows="11" name="post_content" id="post_content_app" cols="56"></textarea></td>
				</tr>
				<tr class="apip-extra-pad-bot">
					<td align="left" valign="top">' . __( 'Product Location', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left">
						&nbsp;&nbsp;<input type="radio" name="amazon-product-content-location[1][]" value="1"  checked /> ' . __( '<strong>Above Post Content </strong><em>- Default - Product will be first then post text</em>', 'amazon-product-in-a-post-plugin' ) . '<br />
						&nbsp;&nbsp;<input type="radio" name="amazon-product-content-location[1][]" value="3" /> ' . __( '<strong>Below Post Content</strong><em> - Post text will be first then the Product</em>', 'amazon-product-in-a-post-plugin' ) . '<br />
						&nbsp;&nbsp;<input type="radio" name="amazon-product-content-location[1][]" value="2" /> ' . __( '<strong>Post Text becomes Description</strong><em> - Post text will become part of the Product layout</em>', 'amazon-product-in-a-post-plugin' ) . '<br />
					</td>
				</tr>
				<tr class="apip-extra-pad-bot">
					<td align="left" valign="top">' . __( 'Additional Items', 'amazon-product-in-a-post-plugin' ) . ':</td>
					<td align="left">' . implode( "\n", $extrasec ) . '</td>
				</tr>
				' . $section . '
			</table>
			<br/>
			<div class="createpost-wrapper"><input type="submit" value="' . __( 'Create Product & Return Here', 'amazon-product-in-a-post-plugin' ) . '" name="createpost" class="button-primary create-appip-product" /> <input type="submit" value="' . __( 'Create Product & Edit NOW', 'amazon-product-in-a-post-plugin' ) . '" name="createpost_edit" class="button-primary" /></div>
			<!--div class="createpost-wrapper"><a href="' . add_query_arg( array( 'action' => 'action_appip_do_product', 'security' => wp_create_nonce( 'appip_ajax_do_product' ), 'tab' => 'changelog', 'width' => 600, 'height' => 500, 'plugin' => 'plugin-name', 'section' => 'changelog', 'TB_iframe' => true ), admin_url( 'admin-ajax.php' ) ) . '" name="createpost" class="button-primary create-appip-product">' . __( 'Create Product & Return Here', 'amazon-product-in-a-post-plugin' ) . '</a> <input type="submit" value="' . __( 'Create Amazon Product Post & Edit NOW', 'amazon-product-in-a-post-plugin' ) . '" name="createpost" class="button-primary" /></div-->
		</div>
	</form>
	</div>';
}