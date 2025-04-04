<?php
/*  NOTES for Updates to this file (12/08/18):
If you need to add a new Gutenberg Block options, you need to add it to the following sections:
** SECTION 1: If the attribute needs cleaning (i.e., casting between types or sanitizing), clean it first (needed for bool/int attributes)
** SECTION 2: Add to the default attributes for Shorcode (Gutenberg Block is rendered using Shorcode functionality)
** SECTION 3: Add to the Shortcode array that is passed to the API Call.
** SECTION 4: Add to register_block_type vunction so it can be used in the block inspector panel.
** SECTION 5: Add to template rendering if applicable
** SECTION 6: In php-block-{shortcode}.js file, add to inspector panel elements.
** Also be sure to localize any Text in js file using __() funcitons.
**
** Blocks and shorcodes use the same function to render, so anything you add to a block
** is available in the shortcode parameters by default, so remember to update shortcode
** documentation pages with new parameters so those using shortcodes will know how to use them.
*/
class Amazon_Product_Shortcode_Products extends Amazon_Product_Shortcode {
	static function _setup() {}
	static function do_shortcode( $atts, $content = '' ) {
		/* SECTION 1 - ATTS Cleaning */
		$atts[ 'features' ] = isset( $atts[ 'features' ] ) && ($atts[ 'features' ] == 'true' || $atts[ 'features' ] == '1') ? 1 : 0;
		$atts[ 'desc' ] = isset( $atts[ 'desc' ] ) && ($atts[ 'desc' ] == 'true' || $atts[ 'desc' ] == '1') ? 1 : 0;
		if(isset( $atts[ 'new_price' ] ) && ($atts[ 'new_price' ] == 'true' || $atts[ 'new_price' ] == '1')){
			$atts[ 'new_price' ] =  1;
		}elseif(isset( $atts[ 'new_price' ] ) && ($atts[ 'new_price' ] == 'false' || $atts[ 'new_price' ] == '0')){
			$atts[ 'new_price' ] = 0;
		}else{
			$atts[ 'new_price' ] = 1;
		}
		$atts[ 'listprice' ] = isset($atts[ 'listprice' ] ) && ($atts[ 'listprice' ] == 'true' || $atts[ 'listprice' ] == '1') ? 1 : 0;
		//$atts[ 'new_price' ] = isset( $atts[ 'new_price' ] ) && ($atts[ 'new_price' ] == 'true' || $atts[ 'new_price' ] == '1') ? 1 : 1;
		$atts[ 'used_price' ] = isset( $atts[ 'used_price' ] ) && ($atts[ 'used_price' ] == 'true' || $atts[ 'used_price' ] == '1') ? 1 : 0;
		$atts[ 'gallery' ] = isset( $atts[ 'gallery' ] ) && ($atts[ 'gallery' ] == 'true' || $atts[ 'gallery' ] == '1' ) ? 1 : 0;
		$atts[ 'hide_title' ] = isset( $atts[ 'hide_title' ] ) && ($atts[ 'hide_title' ] == 'true' || $atts[ 'hide_title' ] == '1' ) ? 1 : 0;
		$atts[ 'hide_image' ] = isset( $atts[ 'hide_image' ] ) && ($atts[ 'hide_image' ] == 'true' || $atts[ 'hide_image' ] == '1'  ) ? 1 : 0;
		$atts[ 'hide_lg_img_text' ] = isset( $atts[ 'hide_lg_img_text' ] ) && ($atts[ 'hide_lg_img_text' ] == 'true' || $atts[ 'hide_lg_img_text' ] == '1' ) ? 1 : 0;
		$atts[ 'hide_release_date' ] = isset( $atts[ 'hide_release_date' ] ) && ($atts[ 'hide_release_date' ] == 'true' || $atts[ 'hide_release_date' ] == '1' ) ? 1 : 0;
		$atts[ 'title_charlen' ] = isset( $atts[ 'title_charlen' ] ) && ((int) $atts[ 'title_charlen' ] >= 0 && (int) $atts[ 'title_charlen' ] <= 150  ) ? (int) $atts[ 'title_charlen' ] : 0;
		$atts[ 'single_only' ] = isset( $atts[ 'single_only' ] ) && ($atts[ 'single_only' ] == 'true' || $atts[ 'single_only' ] == '1' ) ? 1 : 0;
		$atts[ 'is_block' ] = isset( $atts[ 'is_block' ] ) && ($atts[ 'is_block' ] == 'true' || $atts[ 'is_block' ] == '1' ) ? 1 : 0;
		$atts[ 'image_count' ] = isset( $atts[ 'image_count' ] ) && (( int )$atts[ 'image_count' ] <= 10 || ( int )$atts[ 'image_count' ] >= -1 ) ? ( int )$atts[ 'image_count' ] : -1;
		$atts[ 'target' ] = isset( $atts[ 'target' ] ) && (esc_attr( $atts[ 'target' ] ) != '' ) ? esc_attr( $atts[ 'target' ] ) : '_blank';

		/* SECTION 2 - ATTS Defaults */
		$defaults = array(
			'asin' => '',
			'locale' => APIAP_LOCALE,
			'partner_id' => APIAP_ASSOC_ID,
			'private_key' => APIAP_SECRET_KEY,
			'public_key' => APIAP_PUB_KEY,
			'template' => 'default', //future feature
			'replace_title' => '', //replace with your own title
			'desc' => 0, //set to 1 to show or 0 to hide description if avail
			'listprice' => 1, //set to 0 to hide list price
			'used_price' => 1, //set to 0 to hide used price
			'new_price' => 1, //Show New Price
			'gallery' => 0, //set to 1 to show extra photos
			'features' => 0, //set to 1 to show or 0 to hide features if avail
			'hide_title' => 0, //set to 1 to hide or 0 to show (default)
			'hide_image' => 0, //set to 1 to hide or 0 to show (default)
			'hide_lg_img_text' => 0, //Hides the "See larger Image" Link
			'hide_release_date' => 0, //Hide release date for Game or Pre-orders
			'single_only' => 0, //show on Single Only
			'image_count' => -1, //The number of Images in the Gallery. -1 = all, or 0-10
			'is_block' => 0, //Special attribute to tell if this is a Block element or a shortcode.
			'use_carturl' => 'false', //set to 1 use Cart URL
			'button' => '', //New HTML Button attribute. Any valid registered HTML Button
			'list_price' => null, //added only as a secondary use of $listprice
			'show_list' => null, //added only as a secondary use of $listprice
			'show_used' => null, //added only as a secondary use of $used_price
			'usedprice' => null, //added only as a secondary use of $used_price
			'show_new' => null, //Show New Price (secondary use for new_price)
			'align' => 'none', //'alignleft', 'alignright', 'aligncenter', 'none' (default) - 4.0 no longer used
			'className' => '', //Gutenberg Additional className attribute.
			'title_charlen' => 0, // if greater than 0 will concat text fileds
		);

		$origatts = $atts;
		$atts = shortcode_atts( $defaults, $atts );
		if ( array_key_exists( '0', $origatts ) )
			$atts[ 'asin' ] = str_replace( '=', '', $origatts[ 0 ] );
		if ( strpos( $atts[ 'asin' ], ',' ) !== false )
			$atts[ 'asin' ] = explode( ',', str_replace( ' ', '', $atts[ 'asin' ] ) );
		$atts[ 'listprice' ] = is_null( $atts[ 'list_price' ] ) ? $atts[ 'listprice' ] : ( int )$atts[ 'list_price' ];
		$atts[ 'listprice' ] = is_null( $atts[ 'show_list' ] ) ? $atts[ 'listprice' ] : ( int )$atts[ 'show_list' ];
		$atts[ 'used_price' ] = is_null( $atts[ 'usedprice' ] ) ? $atts[ 'used_price' ] : ( int )$atts[ 'usedprice' ];
		$atts[ 'used_price' ] = is_null( $atts[ 'show_used' ] ) ? $atts[ 'used_price' ] : ( int )$atts[ 'show_used' ];
		$atts[ 'new_price' ] = is_null( $atts[ 'show_new' ] ) ? $atts[ 'new_price' ] : ( int )$atts[ 'show_new' ];
		$use_carturl =  isset( $atts[ 'use_carturl' ]) && $atts[ 'use_carturl' ] == '1' ? '1' : '0';
		$button_carturl = isset( $atts['button_use_carturl']) && $atts[ 'button_use_carturl' ] == '1'  ? '1'  : $use_carturl ;
		// fix spaces, returns, double spaces and new lines in ASINs
		if(isset($atts[ 'asin' ]) && is_array($atts[ 'asin' ])){
			$ak = implode(",",$atts[ 'asin' ]);
			$ak = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$ak);
			$atts[ 'asin' ] = explode(",",$ak);
		}

		/* SECTION 3 - Shortcode Attrs Array (for render) */
		$amazon_array = array(
			'locale' => $atts[ 'locale' ],
			'partner_id' => $atts[ 'partner_id' ],
			'private_key' => $atts[ 'private_key' ],
			'public_key' => $atts[ 'public_key' ],
			'gallery' => $atts[ 'gallery' ],
			'features' => $atts[ 'features' ],
			'new_price' => $atts[ 'new_price' ],
			'listprice' => $atts[ 'listprice' ],
			'used_price' => $atts[ 'used_price' ],
			'desc' => $atts[ 'desc' ],
			'replace_title' => $atts[ 'replace_title' ],
			'template' => $atts[ 'template' ],
			'usecarturl' => $use_carturl,
			'use_carturl' => $use_carturl,
			'button_use_carturl' => $button_carturl,
			'align' => $atts[ 'align' ],
			'button' => $atts[ 'button' ],
			'asins' => $atts[ 'asin' ],
			'hide_title' => $atts[ 'hide_title' ],
			'hide_image' => $atts[ 'hide_image' ],
			'hide_lg_img_text' => $atts[ 'hide_lg_img_text' ],
			'hide_release_date' => $atts[ 'hide_release_date' ],
			'image_count' => $atts[ 'image_count' ],
			'className' => $atts[ 'className' ], // Gutenberg Additional className attribute.
			'single_only' => $atts[ 'single_only' ],
			'is_block' => $atts[ 'is_block' ],
			'title_charlen' => $atts[ 'title_charlen' ]	,
		);
		$amazon_array = apply_filters( 'appip_shortcode_atts_array', $amazon_array );
		return getSingleAmazonProduct( $atts[ 'asin' ], $content, 0, $amazon_array, $atts[ 'desc' ] );
	}
}
new Amazon_Product_Shortcode_Products( array( 'amazonproduct', 'amazonproducts', 'AMAZONPRODUCT', 'AMAZONPRODUCTS' ) );

function appip_products_php_block_init() {
	global $amazon_styles_enqueued;
	if ( function_exists( 'register_block_type' ) ) {
		add_filter( 'appip-register-templates', function ( $appip_template_array ) {
			$appip_template_array[] = array('location' => 'products','name' => __('Alternate','amazon-product-in-a-post-plugin'),'ID' => 'fluffy');
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Dark', 'amazon-product-in-a-post-plugin' ), 'ID' => 'dark' );
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Dark: Image Right', 'amazon-product-in-a-post-plugin' ), 'ID' => 'dark-reversed' );
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Dark: Image Top', 'amazon-product-in-a-post-plugin' ), 'ID' => 'dark-image-top' );
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Light', 'amazon-product-in-a-post-plugin' ), 'ID' => 'light' );
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Light: Image Right', 'amazon-product-in-a-post-plugin' ), 'ID' => 'light-reversed' );
			$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Light: Image Top', 'amazon-product-in-a-post-plugin' ), 'ID' => 'light-image-top' );
			return $appip_template_array;
		}, 10, 1 );
		$pluginStyles = array('amazon-theme-styles');
		$pluginScripts = array('amazon-product');

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
			'amazon-product',
			plugins_url( '/blocks/php-block-product.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'blocks/php-block-product.js' )
		);
		/* SECTION 4 - PHP register_block_type Attributes */
		register_block_type( 'amazon-pip/amazon-product', array(
			'attributes' => array(
				'asin' => array(
					'type' => 'string',
				),
				'replace_title' => array(
					'type' => 'string',
				),
				'button' => array(
					'type' => 'string',
					'default' => '',
				),
				'hide_title' => array(
					'type' => 'bool',
					'default' => false
				),
				'hide_image' => array(
					'type' => 'bool',
					'default' => false
				),
                'hide_lg_img_text' => array(
                    'type' => 'bool',
                    'default' => false
                ),
				'desc' => array(
					'type' => 'bool',
				),
				'used_price' => array(
					'type' => 'bool',
					'default' => true,
				),
				'listprice' => array(
					'type' => 'bool',
					'default' => true,
				),
				'new_price' => array(
					'type' => 'bool',
					'default' => true,
				),
				'template' => array(
					'type' => 'string',
					'default' => 'default',
				),
				'use_carturl' => array(
					'type' => 'bool',
				),
				'gallery' => array(
					'type' => 'bool',
				),
				'features' => array(
					'type' => 'bool',
				),
				'hide_release_date' => array(
					'type' => 'bool',
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
				// FUTURE UPDATES - no time to add these now with the rushed update to 5.0
				//'partner_id'=> array('type' => 'string',),
				//'private_key'=> array('type' => 'string',),
				//'public_key' => array('type' => 'string',),
			),
			'editor_style' => $pluginStyles,
			'editor_script' => $pluginScripts,
			// This lets us take advantage of the Shorcode funcitonality we already have in place.
			'render_callback' => array( 'Amazon_Product_Shortcode_Products', 'do_shortcode' ),
		) );
	}
}
add_action( 'init', 'appip_products_php_block_init' );
function appip_products_templates( $appip_templates, $result, $array_for_templates ) {
	/* SECTION 5 - Template Rendering */
	$temppart = array();

	/* DARK TEMPLATE */
	if ( ( int )$array_for_templates[ 'image_count' ] >= 1 && ( int )$array_for_templates[ 'image_count' ] <= 10 && is_array( $result[ 'AddlImagesArr' ] ) && !empty( $result[ 'AddlImagesArr' ] ) ) {
		$array_for_templates[ 'has_addl_images' ] = 1;
		$result[ 'AddlImages' ] = implode( '', array_slice( $result[ 'AddlImagesArr' ], 0, ( int )$array_for_templates[ 'image_count' ] ) );
	} elseif ( ( int )$array_for_templates[ 'image_count' ] == 0 ) {
		$result[ 'AddlImages' ] = array();
		$array_for_templates[ 'show_gallery' ] = 0;
	}
	$className = '';
	if ( $array_for_templates[ 'className' ] != '' )
		$className = ' ' . implode( ' ', explode( ',', str_replace( array( ', ', ' ' ), array( ',', ',' ), esc_attr($array_for_templates[ 'className' ]  ) ) ) );
	if ( ( bool )$array_for_templates[ 'is_block' ] )
		$className = ' amazon--is_block' . $className;
	$temppart[] = '<div class="appip-block-wrapper">';
	$temppart[] = '<div class="amazon-template--product-dark' . $className . '">';
	if ( ( bool )$array_for_templates[ 'hide_image' ] === false ) {
		$temppart[] = '	<div class="amazon-image-wrapper">';
		$temppart[] = '		<a href="[!URL!]" [!TARGET!]>[!IMAGE!]</a>';
		if ( ( bool )$array_for_templates[ 'hide_lg_img_text' ] === false )
			$temppart[] = '		<a rel="appiplightbox-[!ASIN!]" href="#" data-appiplg="[!LARGEIMAGE!]" target="amazonwin"><span class="amazon-tiny">[!LARGEIMAGETXT!]</span></a>';
		if ( !empty($result[ 'AddlImages' ]) && $array_for_templates[ 'show_gallery' ] == 1 )
			$temppart[] = '	<div class="amazon-additional-images-wrapper"><span class="amazon-additional-images-text">[!LBL-ADDL-IMAGES!]</span>' . $result[ 'AddlImages' ] . '</div>';
		$temppart[] = '	</div>';
	}
	$temppart[] = '	<div class="amazon-section-wrapper' . ( ( bool )$array_for_templates[ 'hide_image' ] ? ' amazon-image-hidden' : '' ) . '">';
	if ( ( bool )$array_for_templates[ 'hide_title' ] === false )
		$temppart[] = '		<h2 class="amazon-asin-title"><a href="[!URL!]" [!TARGET!]><span class="asin-title">[!TITLE!]</span></a></h2>';
	if ( ( bool )$array_for_templates[ 'description' ] )
		$temppart[] = '		<div class="amazon-description">[!CONTENT!]</div>';
	if ( ( $result[ "Department" ] === 'Video Games' ) && ( bool )$array_for_templates[ 'show_features' ] ) {
		$temppart[] = '	<div class="amazon-game-features">';
		$temppart[] = '		<span class="amazon-manufacturer"><span class="appip-label">[!LBL-MANUFACTURER!]</span> [!MANUFACTURER!]</span><br />';
		$temppart[] = '		<span class="amazon-ESRB"><span class="appip-label">[!LBL-ESRBA!]</span> [!ESRBA!]</span><br />';
		$temppart[] = '		<span class="amazon-platform"><span class="appip-label">[!LBL-PLATFORM!]</span> [!PLATFORM!]</span><br />';
		$temppart[] = '		<span class="amazon-system"><span class="appip-label">[!LBL-GENRE!]</span> [!GENRE!]</span><br />';
		if ( ( bool )$array_for_templates[ 'show_features' ] && $result[ "Feature" ] != '' ) {
			$temppart[] = '		<span class="amazon-feature"><span class="appip-label">[!LBL-FEATURE!]</span> [!FEATURE!]</span><br />';
		}
		$temppart[] = '	</div>';
	} elseif ( ( bool )$array_for_templates[ 'show_features' ] && $result[ "Feature" ] != '' ) {
		$temppart[] = '		<span class="amazon-feature"><span class="appip-label">[!LBL-FEATURE!]</span> [!FEATURE!]</span><br />';
	}
	if ( ( bool )$array_for_templates[ 'show_list' ] ) {
		if ( $result[ "PriceHidden" ] == '1' ) {
			$temppart[] = '						<div class="amazon-pricing--wrap">';
			$temppart[] = '							<div class="amazon-list-price-label">' . $array_for_templates[ 'appip_text_listprice' ] . '</div>';
			$temppart[] = '							<div class="amazon-list-price-label">' . $array_for_templates[ 'appip_text_notavalarea' ] . '</div>';
			$temppart[] = '						</div>';
		} elseif ( $result[ "ListPrice" ] != '0' || $result[ "NewAmazonPricing" ][ "New" ][ "List" ] != '0' ) {
			$temppart[] = '						<div class="amazon-pricing--wrap">';
			$temppart[] = '							<div class="amazon-list-price-label">' . $array_for_templates[ 'appip_text_listprice' ] . '</div>';
			if ( isset( $result[ "NewAmazonPricing" ][ "New" ][ "List" ] ) && $result[ "NewAmazonPricing" ][ "New" ][ "List" ] != '' )
				$temppart[] = '							<div class="amazon-list-price">' . maybe_convert_encoding( $result[ "NewAmazonPricing" ][ "New" ][ "List" ] ) . '</div>';
			else
				$temppart[] = '							<div class="amazon-list-price">' . maybe_convert_encoding( $result[ "ListPrice" ] ) . '</div>';
			$temppart[] = '						</div>';
		}
	}
	if ( isset( $result[ "LowestNewPrice" ] ) && ( bool )$array_for_templates[ 'show_new_price' ] ) {
			$amz_pricing_text = __('Check Amazon For Pricing','amazon-product-in-a-post-plugin');
			$hide_stock_msg = isset( $result[ "HideStockMsg" ] ) && (int) $result[ "HideStockMsg" ]  == 1 ? true : false;
			if ( $result[ "LowestNewPrice" ] == 'Too low to display' || $result[ "LowestNewPrice" ] == '0' ) {
				$newPrice = $amz_pricing_text;
				$hide_stock_msg = true;
			} else {
				if ( isset( $result[ "NewAmazonPricing" ][ "New" ][ "SalePrice" ] ) && $result[ "NewAmazonPricing" ][ "New" ][ "SalePrice" ] != '' )
					$newPrice = $result[ "NewAmazonPricing" ][ "New" ][ "SalePrice" ];
				elseif ( isset( $result[ "NewAmazonPricing" ][ "New" ][ "Price" ] ) && $result[ "NewAmazonPricing" ][ "New" ][ "Price" ] != '' )
					$newPrice = $result[ "NewAmazonPricing" ][ "New" ][ "Price" ];
				else
					$newPrice = $result[ "LowestNewPrice" ];
			}
			if($newPrice == '0'){
				$newPrice = $amz_pricing_text;
				$hide_stock_msg = true;
			}
			$temppart[] = '<div class="amazon-pricing--wrap">';
			if(!$hide_stock_msg){
				$temppart[] = ' <div class="amazon-new-label">' . $array_for_templates[ 'appip_text_newfrom' ] . '</div>';
			}
			if ( ! $hide_stock_msg ) {
				$stockIn = isset($array_for_templates[ 'appip_text_instock' ]) ? ' <span class="instock">'.$array_for_templates[ 'appip_text_instock' ].'</span>' : '';
				$stockOut = isset($array_for_templates[ 'appip_text_outofstock' ]) ? ' <span class="outofstock">' .$array_for_templates[ 'appip_text_outofstock' ].'</span>' : '';
			} else {
				$stockIn = '';
				$stockOut = '';
			}
			if ( $result[ "TotalNew" ] > 0 ) {
				$temppart[] = ' <div class="amazon-new">' . maybe_convert_encoding( $newPrice ) .  $stockIn . '</div>';
			} else {
				$temppart[] = ' <div class="amazon-new">' . maybe_convert_encoding( $newPrice ) . $stockOut . '</div>';
			}
			$temppart[] = '</div>';
	}
	if ( ( bool )$array_for_templates[ 'show_used_price' ] ) {
		if ( isset( $result[ "LowestUsedPrice" ] ) && $result[ "Binding" ] != 'Kindle Edition' ) {
			if ( !( isset( $result[ "HideStockMsgUsed" ] ) && isset( $result[ "HideStockMsgUsed" ] ) == '1' ) ) {
				$stockIn = isset($array_for_templates[ 'appip_text_instock' ]) ? $array_for_templates[ 'appip_text_instock' ] : '';
				$stockOut = isset($array_for_templates[ 'appip_text_outofstock' ]) ? $array_for_templates[ 'appip_text_outofstock' ] : '';
			} else {
				$stockIn = '';
				$stockOut = '';
			}
			if ( $result[ "TotalUsed" ] > 0 ) {
				$temppart[] = '<div class="amazon-pricing--wrap">';
				$temppart[] = '	<div class="amazon-used-label">' . $array_for_templates[ 'appip_text_usedfrom' ] . '</div>';
				if ( $result[ "TotalUsed" ] >= 1 ) {
					if ( isset( $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] ) && $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] != '' && $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] != '0' )
						$usedPrice = $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ];
					else
						$usedPrice = $result[ "LowestNewPrice" ];
					if ( $usedPrice != '' )
						$temppart[] = '<div class="amazon-used">' . maybe_convert_encoding( $usedPrice ) . $stockIn . '</div>';
				} else {
					if ( isset( $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] ) && $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] != '' && $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ] != '0' )
						$usedPrice = $result[ "NewAmazonPricing" ][ "Used" ][ "Price" ];
					else
						$usedPrice = '';
					if ( $usedPrice != '' )
						$temppart[] = '	<div class="amazon-used">' . maybe_convert_encoding( $usedPrice ) . $stockOut . '</div>';
				}
				$temppart[] = '</div>';
			}
		}
	}
	if ( ( bool )$array_for_templates[ 'hide_release_date' ] !== true ) {
		if ( $result[ "ReleaseDate" ] != '' ) {
			$nowdatestt = strtotime( date( "Y-m-d", time() ) );
			$nowminustt = strtotime( "-7 days" );
			$reldatestt = strtotime( $result[ "ReleaseDate" ] );
			if ( $reldatestt > $nowdatestt ) {
				$temppart[] = '<span class="amazon-preorder"><br />[!LBL-RELEASED-ON-DATE!] [!RELEASE-DATE!]</span>';
			} elseif ( $reldatestt >= $nowminustt ) {
				$temppart[] = '<span class="amazon-release-date">[!LBL-RELEASE-DATE!] [!RELEASE-DATE!]</span>';
			}
		}
	}
	$temppart[] = '	<div>[!AMZ-BUTTON!]</div>';
	$temppart[] = '</div>';
	$temppart[] = '</div>';
	if ( $array_for_templates[ 'product_count' ] > 1 )
		$temppart[] = '<div><hr></div>';
	else
		$temppart[] = '<div>&nbsp;</div>';
	$temppart[] = '</div>';
	$appip_templates[ 'dark' ] = implode( "\n", $temppart );

	/* DARK TEMPLATE REVERSED (image on right)*/
	$appip_templates[ 'dark-reversed' ] = str_replace( 'amazon-template--product-dark', 'amazon-template--product-dark template-reversed', $appip_templates[ 'dark' ] );
	/* DARK TEMPLATE IMG TOP (image on top)*/
	$appip_templates[ 'dark-image-top' ] = str_replace( 'amazon-template--product-dark', 'amazon-template--product-dark template-img-top', $appip_templates[ 'dark' ] );
	/* LIGHT TEMPLATE */
	$appip_templates[ 'light' ] = str_replace( 'amazon-template--product-dark', 'amazon-template--light', $appip_templates[ 'dark' ] );
	/* LIGHT TEMPLATE REVERSED (image on right)*/
	$appip_templates[ 'light-reversed' ] = str_replace( 'amazon-template--product-dark', 'amazon-template--light template-reversed', $appip_templates[ 'dark' ] );
	/* LIGHT TEMPLATE IMG TOP (image on top)*/
	$appip_templates[ 'light-image-top' ] = str_replace( 'amazon-template--product-dark', 'amazon-template--light template-img-top', $appip_templates[ 'dark' ] );

	return $appip_templates;
}
add_filter( 'appip-template-filter', 'appip_products_templates', 10, 3 );

add_filter( 'appip-template-filter', 'appip_products_templates_amzlayout', 10, 3 );
function appip_products_templates_amzlayout( $appip_templates, $result, $array_for_templates ) {
	/* SECTION 5 - Template Rendering */
	$link = $result['URL'];
	$ASIN = $result['ASIN'];
	$title = esc_attr($result['Title']);
	$image = esc_attr($result['LargeImage']);
	$price = isset($result['NewAmazonPricing']['New']['Price']) ? esc_attr($result['NewAmazonPricing']['New']['Price']) : '';
	$sprice = isset($result['NewAmazonPricing']['New']['SalePrice']) ? esc_attr($result['NewAmazonPricing']['New']['SalePrice']): '';
	$saved = isset($result['NewAmazonPricing']['New']['SavedPercent']) ? esc_attr($result['NewAmazonPricing']['New']['SavedPercent']): '0';
	$lprice = isset($result['NewAmazonPricing']['New']['List']) ? esc_attr($result['NewAmazonPricing']['New']['List']): '';
	$prime = isset($result['NewAmazonPricing']['New']['IsPrimeEligible']) && (int)$result['NewAmazonPricing']['New']['IsPrimeEligible'] == 1 ? true : false;
	$tlprice = $lprice;

	if( $sprice != $price && $sprice != '' && $price != '' && (int) $sprice != 0 ){
		$lprice = '';
	}else if( $lprice == $sprice ){
		$lprice = '';
	}else{}

	$temppart = array();
	$temppart[] = '<!-- HTML code for ASIN : '.$ASIN.' -->';
	$temppart[] = '    <div class="paapi5-pa-ad-unit pull-left">';
	$temppart[] = '        <div class="paapi5-pa-product-container">';
	$temppart[] = '            <div class="paapi5-pa-product-image">';
	$temppart[] = '                <div class="paapi5-pa-product-image-wrapper">';
	$temppart[] = '                    <a class="paapi5-pa-product-image-link" href="'.$link.'" title="'.$title.'" target="_blank">';
	$temppart[] = '                        <img class="paapi5-pa-product-image-source" src="'.$image.'" alt="'.$title.'" />';
	if($saved != '0')
		$temppart[] = '                        <span class="paapi5-pa-percent-off">'.$saved .'%</span>';
	$temppart[] = '                   </a>';
	$temppart[] = '               </div>';
	$temppart[] = '            </div>';
	$temppart[] = '            <div class="paapi5-pa-product-details">';
	$temppart[] = '               <div class="paapi5-pa-product-title">';
	$temppart[] = '                    <a href="'.$link.'" title="'.$title.'" target="_blank">'.$title.'</a>';
	$temppart[] = '                </div>';
	if($sprice != '')
		$temppart[] = '                <div class="paapi5-pa-product-offer-price"><span class="paapi5-pa-product-offer-price-value">'.$sprice.'</span></div>';
	else
		$temppart[] = '                <div class="paapi5-pa-product-offer-price"><span class="paapi5-pa-product-offer-price-value">'.$price.'</span></div>';
	if($lprice != '')
		$temppart[] = '	               <div class="paapi5-pa-product-list-price"><span class="paapi5-pa-product-list-price-value">'.$lprice .'</span></div>';
	if($prime)
		$temppart[] = '           <div class="paapi5-pa-product-prime-icon"><i class="icon-prime-all"></i></div>';
	$temppart[] = '       </div>';
	$temppart[] = '        </div>';
	$temppart[] = '    </div>';
	/*
	if ( $array_for_templates[ 'product_count' ] > 1 )
		$temppart[] = '<div><hr></div>';
	else
		$temppart[] = '<div>&nbsp;</div>';
	*/
	$appip_templates[ 'amazon-layout' ] = implode( "\n", $temppart );
	return $appip_templates;
}

add_filter( 'appip-register-templates', function ( $appip_template_array ) {
	$appip_template_array[] = array( 'location' => 'product', 'name' => __( 'Amazon Layout', 'amazon-product-in-a-post-plugin' ), 'ID' => 'amazon-layout' );
	return $appip_template_array;
}, 10, 1 );

