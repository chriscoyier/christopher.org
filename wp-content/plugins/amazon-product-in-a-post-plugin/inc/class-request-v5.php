<?php
function __getAmz_regions() {
  return array(
    "com.au" => array( "RegionName" => "Australia",            "Host" => "webservices.amazon.com.au", "RegionCode" => "us-west-2" ),
    "com.br" => array( "RegionName" => "Brazil",               "Host" => "webservices.amazon.com.br", "RegionCode" => "us-east-1" ),
    "ca"     => array( "RegionName" => "Canada",               "Host" => "webservices.amazon.ca",     "RegionCode" => "us-east-1" ),
    "cn"     => array( "RegionName" => "China",                "Host" => "webservices.amazon.cn",     "RegionCode" => "us-west-2" ),
    "fr"     => array( "RegionName" => "France",               "Host" => "webservices.amazon.fr",     "RegionCode" => "eu-west-1" ),
    "de"     => array( "RegionName" => "Germany",              "Host" => "webservices.amazon.de",     "RegionCode" => "eu-west-1" ),
    "in"     => array( "RegionName" => "India",                "Host" => "webservices.amazon.in",     "RegionCode" => "eu-west-1" ),
    "it"     => array( "RegionName" => "Italy",                "Host" => "webservices.amazon.it",     "RegionCode" => "eu-west-1" ),
    "jp"     => array( "RegionName" => "Japan",                "Host" => "webservices.amazon.co.jp",  "RegionCode" => "us-west-2" ),
    "mx"     => array( "RegionName" => "Mexico",               "Host" => "webservices.amazon.com.mx", "RegionCode" => "us-east-1" ),
    "nl"     => array( "RegionName" => "Netherlands",          "Host" => "webservices.amazon.nl",     "RegionCode" => "eu-west-1" ),
    "sa"     => array( "RegionName" => "Saudi Arabia",         "Host" => "webservices.amazon.sa",     "RegionCode" => "eu-west-1" ),
    "sg"     => array( "RegionName" => "Singapore",            "Host" => "webservices.amazon.sg",     "RegionCode" => "us-west-2" ),
    "es"     => array( "RegionName" => "Spain",                "Host" => "webservices.amazon.es",     "RegionCode" => "eu-west-1" ),
    "se"     => array( "RegionName" => "Sweden",               "Host" => "webservices.amazon.se",     "RegionCode" => "eu-north-1" ),
//    "tr"     => array( "RegionName" => "Turkey",               "Host" => "webservices.amazon.com.tr", "RegionCode" => "eu-west-1" ),
    "ae"     => array( "RegionName" => "United Arab Emirates", "Host" => "webservices.amazon.ae",     "RegionCode" => "eu-west-1" ),
    "co.uk"  => array( "RegionName" => "United Kingdom",       "Host" => "webservices.amazon.co.uk",  "RegionCode" => "eu-west-1" ),
    "com"    => array( "RegionName" => "United States",        "Host" => "webservices.amazon.com",    "RegionCode" => "us-east-1" )
  );
}
function __getAmz_errors() {
  return array(
    'AccessDenied' => 'The Access Key is not enabled for accessing Product Advertising API. For information on registering for Product Advertising API, see Register for Product Advertising API.',
    'AccessDeniedAwsUsers' => 'The Access Key is not enabled for accessing this version of Product Advertising API. Please migrate your credentials as referred here Managing your Existing AWS Security Credentials for the Product Advertising API.',
    'InvalidAssociate' => 'Your access key [Access Key] is not mapped to primary of approved associate store. Please visit associate central at [associate central link for requested marketplace]',
    'IncompleteSignature' => 'The request signature did not include all of the required components. If you are using an AWS SDK, requests are signed for you automatically; otherwise, go to the Signature Version 4 Signing Process Guide in the Sending a Request section.',
    'InvalidPartnerTag' => 'The partner tag is not mapped to a valid associate store with your access key [Access Key]. Please visit associates central at [associate central link for requested marketplace]',
    'InvalidSignature' => 'The request has not been correctly signed. If you are using an AWS SDK, requests are signed for you automatically; otherwise, go to Signing a Request Guide.',
    'TooManyRequests' => 'The request was denied due to request throttling. Please verify the number of requests made per second to the Amazon Product Advertising API.',
    'RequestExpired' => 'The request is past expiry date or the request date (either with 15 minute padding), or the request date occurs more than 15 minutes in the future.',
    'InvalidParameterValue' => 'Input parameter(s) relating to request is invalid.',
    'MissingParameter' => 'Input parameter(s) relating to request is missing.',
    'UnknownOperation' => 'The operation requested is invalid. Please verify that the operation name is typed correctly.',
    'UnrecognizedClient' => 'The Access Key or security token included in the request is invalid.',
  );
}

class Amazon_Product_Request_V5 {
  private $accessKey = null;
  private $secretKey = null;
  private $path = null;
  private $regionName = null;
  private $serviceName = 'ProductAdvertisingAPI';
  private $httpMethodName = null;
  private $awsHeaders = array();
  private $payload = "";
  private $HMACAlgorithm = "AWS4-HMAC-SHA256";
  private $aws4Request = "aws4_request";
  private $strSignedHeader = null;
  private $xAmzDate = null;
  private $currentDate = null;
  private $requestPath = null;

  public function __construct( $accessKey, $secretKey, $region, $host, $requestType = 'getitems' ) {
	if($requestType === 'single'){
		// do nothing - not a full request
	} else{
		$requestTypes = array(
		  'getbrowsenodes' => 'GetBrowseNodes', //Lookup information for a Browse Node
		  'getitems' => 'GetItems', //	Lookup item information for an item
		  'getvariations' => 'GetVariations', //	Lookup information for variations
		  'searchitems' => 'SearchItems' //Searches for items on Amazon
		);
		$this->accessKey = $accessKey;
		$this->secretKey = $secretKey;
		$this->xAmzDate = $this->getTimeStamp();
		$this->currentDate = $this->getDate();
		$this->regionName = $region;
		$this->path = '/paapi5/' . strtolower( $requestType );
		$this->requestPath = 'https://' . $host . '/paapi5/' . strtolower( $requestType );
		$this->httpMethodName = 'POST';
		$this->addHeader( 'content-encoding', 'amz-1.0' );
		$this->addHeader( 'content-type', 'application/json; charset=utf-8' );
		$this->addHeader( 'host', $host );
		$this->addHeader( 'x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.' . $requestTypes[ $requestType ] );
	}
  }
  public function do_request() {
    $headers = $this->getHeaders();
    $headerString = "";
    foreach ( $headers as $key => $value ) {
      $headerString .= $key . ': ' . $value . "\r\n";
    }
    $args = array(
      'headers' => $headerString,
      'method' => 'POST',
      'body' => $this->payload
    );

	$fp = wp_remote_request( $this->requestPath, $args );
    if ( !$fp )
      error_log( "Connection WP_REMOTE Exception Occured" );
    return $fp;
  }
  public function setPayload( $payload ) {
    $this->payload = $payload;
  }
  public function addHeader( $headerName, $headerValue ) {
    $this->awsHeaders[ $headerName ] = $headerValue;
  }
  private function prepareCanonicalRequest() {
    $canonicalURL = "";
    $canonicalURL .= $this->httpMethodName . "\n";
    $canonicalURL .= $this->path . "\n" . "\n";
    $signedHeaders = '';
    foreach ( $this->awsHeaders as $key => $value ) {
      $signedHeaders .= $key . ";";
      $canonicalURL .= $key . ":" . $value . "\n";
    }
    $canonicalURL .= "\n";
    $this->strSignedHeader = substr( $signedHeaders, 0, -1 );
    $canonicalURL .= $this->strSignedHeader . "\n";
    $canonicalURL .= $this->generateHex( $this->payload );
    return $canonicalURL;
  }
  private function prepareStringToSign( $canonicalURL ) {
    $stringToSign = '';
    $stringToSign .= $this->HMACAlgorithm . "\n";
    $stringToSign .= $this->xAmzDate . "\n";
    $stringToSign .= $this->currentDate . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "\n";
    $stringToSign .= $this->generateHex( $canonicalURL );
    return $stringToSign;
  }
  private function calculateSignature( $stringToSign ) {
    $signatureKey = $this->getSignatureKey( $this->secretKey, $this->currentDate, $this->regionName, $this->serviceName );
    $signature = hash_hmac( "sha256", $stringToSign, $signatureKey, true );
    $strHexSignature = strtolower( bin2hex( $signature ) );
    return $strHexSignature;
  }
  public function getHeaders() {
    $this->awsHeaders[ 'x-amz-date' ] = $this->xAmzDate;
    ksort( $this->awsHeaders );
    // Step 1: CREATE A CANONICAL REQUEST
    $canonicalURL = $this->prepareCanonicalRequest();
    // Step 2: CREATE THE STRING TO SIGN
    $stringToSign = $this->prepareStringToSign( $canonicalURL );
    // Step 3: CALCULATE THE SIGNATURE
    $signature = $this->calculateSignature( $stringToSign );
    // Step 4: CALCULATE AUTHORIZATION HEADER
    if ( $signature ) {
      $this->awsHeaders[ 'Authorization' ] = $this->buildAuthorizationString( $signature );
      return $this->awsHeaders;
    }
  }
  private function buildAuthorizationString( $strSignature ) {
    return $this->HMACAlgorithm . " " . "Credential=" . $this->accessKey . "/" . $this->getDate() . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "," . "SignedHeaders=" . $this->strSignedHeader . "," . "Signature=" . $strSignature;
  }
  private function generateHex( $data ) {
    return strtolower( bin2hex( hash( "sha256", $data, true ) ) );
  }
  private function getSignatureKey( $key, $date, $regionName, $serviceName ) {
    $kSecret = "AWS4" . $key;
    $kDate = hash_hmac( "sha256", $date, $kSecret, true );
    $kRegion = hash_hmac( "sha256", $regionName, $kDate, true );
    $kService = hash_hmac( "sha256", $serviceName, $kRegion, true );
    $kSigning = hash_hmac( "sha256", $this->aws4Request, $kService, true );
    return $kSigning;
  }
  private function getTimeStamp() {
    return gmdate( "Ymd\THis\Z" );
  }
  private function getDate() {
    return gmdate( "Ymd" );
  }
  public function appip_plugin_FormatASINResult( $Result, $cResult = 1, $asinsR = array(), $pxmlkey = '' ) {
    global $formatted;
    $asins = array();
    if ( is_array( $asinsR ) )
      $asins = $asinsR;
    else
      $asins[] = $asinsR;
    if ( isset( $formatted[ implode( "|", $asins ) . '-' . $cResult ] ) ) {
      return $formatted[ implode( "|", $asins ) . '-' . $cResult ];
    }

    $newErr = '';
    $RetValNew = array();
    if ( $pxmlkey == 'SearchResult' ){
      $requestType = 2;
	}elseif ( $pxmlkey == 'VariationsResult' ){
      $requestType = 3;
	}elseif ( $pxmlkey == 'BrowseNodesResult' ){
      $requestType = 4;
	}elseif( $pxmlkey == 'ItemsResult'  ){
	  $requestType = 1;
	}else{
      $requestType = 0;
	}
	if ( $requestType == 2 ) {
      $Items = isset( $Result[ 'Items' ] ) ? $Result[ 'Items' ] : false;
      $cache = isset( $Result[ 'CachedAPPIP' ] ) ? $Result[ 'CachedAPPIP' ] : 0;
      $errors = array();
      if ( isset( $Result[ 'Errors' ] ) ) {
        foreach ( $Result[ 'Errors' ] as $k=> $temperr )
          $errors[$k][ $temperr[ 'Code' ] ] = $temperr[ 'Code' ] . ":\n" . $temperr[ 'Message' ];
		unset($Result[ 'Errors' ]);
      }
      if ( $Items !== false && count( $Items ) > 0 ) {
        if ( is_array( $Items ) && !empty( $Items ) ) {
          foreach ( $Items as $ki => $Item ) {
            $RetValNew[] = ( object )$this->appip_blowoffarr( $Item, $cache );
         }
        }
      } elseif ( $Items !== false ) {
        $RetValNew[] = ( object )$this->appip_blowoffarr( $Items, $cache );
      } else {
        $RetValNew[] = array( 'Error' => "{$newErr}", 'NoData' => 1 );
      }
    } elseif($requestType == 3 ) {
		// TODO Add Request Type 3 (VariationsResult)
		$Items = false;
    } elseif($requestType == 4 ) {
		// TODO Add Request Type 4 (BrowseNodesResult)
		$Items = false;
    } else {
	// get items
      $Items = isset( $Result[ 'Items' ] ) ? $Result[ 'Items' ] : false;
      $cache = isset( $Result[ 'CachedAPPIP' ] ) ? $Result[ 'CachedAPPIP' ] : 0;
	  $errors = array();
      // this is how errors are returned for invalid products
	  /*
      if ( isset( $Result[ 'Errors' ] ) && is_array( $Result[ 'Errors' ] ) && !empty( $Result[ 'Errors' ] ) ) {
        foreach ( $Result[ 'Errors' ] as $k => $temperr ) {
          $errors[ $k ][ $temperr[ 'Code' ] ] = $temperr[ 'Code' ] . ": " . $temperr[ 'Message' ];
        }
		unset($Result[ 'Errors' ]);
      }
	  */
      if ( $Items !== false && count( $Items ) > 0 ) {
        if ( is_array( $Items ) && !empty( $Items ) ) {
          foreach ( $Items as $key => $Item ) {
            if ( empty( $Item ) ) {
              $RetValNew[] = array( 'Errors' => $errors, 'NoData' => 1 );
            } else {
              if ( isset( $Item[ 'ASIN' ] ) && in_array( $Item[ 'ASIN' ], $asins ) ) {
              	$RetValNew[] = ( object )$this->appip_blowoffarr( $Item, $cache );
              }
            }
          }
        }
      } elseif ( $Items != false ) {
        if ( isset( $Items[ 'ASIN' ] ) && in_array( $Items[ 'ASIN' ], $asins ) ) {
          $RetValNew[] = ( object )$this->appip_blowoffarr( $Items, $cache );
        }
      } else {
        $RetValNew[] = array( 'Errors' => $errors, 'NoData' => 1 );
      }
    }
	if(is_array($errors) && !empty($errors))
		$RetValNew['Errors'] = $errors;
    $formatted[ implode( "|", $asins ) . '-' . $cResult ] = $RetValNew;
    return $RetValNew;
  }
  private function appip_blowoffarr( $Item, $key = "", $blowoffArr = array(), $cache = 0 ) {
   $dontuse = apply_filters( 'amazon_product_in_a_post_blowoffarr_dontuse', array( 'BrowseNodes', 'SimilarProducts' ) );
   $blowoffArr[ 'CachedAPPIP' ] = $cache;
   foreach ( $Item as $var => $val ) {
     if ( !in_array(  (string) $var, $dontuse ) ) {
		 if ( $key == "" ) {
          if ( is_array( $val ) ) {
            $blowoffArr = $this->appip_blowoffarr2( $val, $var, $blowoffArr );
          } else {
            $blowoffArr[ $var ] = $val;
          }
        } else {
		  if(count($val) > 1 && (string) $var == '0'){
			$blowoffArr = $this->appip_blowoffarr2( $val, $key, $blowoffArr );
		  }else{
			$blowoffArr = $this->appip_blowoffarr2( $val, $key . '_' . $var, $blowoffArr );
		  }
        }
      }
    }
    return $blowoffArr;
  }
  private function appip_blowoffarr2( $Item, $key = "", $blowoffArr = array() ) {
   $dontuse = apply_filters( 'amazon_product_in_a_post_blowoffarr_dontuse', array( 'BrowseNodes', 'SimilarProducts' ) );
   foreach ( $Item as $var => $val ) {
      if (!in_array( (string) $var, $dontuse ) ) {
          if ( is_array( $val ) ) {
			  if(count($val) > 1 && (string) $var == '0'){
				$blowoffArr = $this->appip_blowoffarr2( $val, $key, $blowoffArr );
			  }else{
				$blowoffArr = $this->appip_blowoffarr2( $val, $key . '_' . $var, $blowoffArr );
			  }
          } else {
            $blowoffArr[ $key . '_' . $var] = $val;
          }
      }
    }
    return $blowoffArr;
  }
  public function checkSSLImages_url( $img_URL ) {
    if ( amazon_check_SSL_on() )
      return $this->img_filter_text( $img_URL );
    return $img_URL;
  }
  public function img_filter_text( $content ) {
    return str_replace( array( 'http://ecx.images-amazon', 'https://ecx.images-amazon' ), array( 'https://images-na.ssl-images-amazon', 'https://images-na.ssl-images-amazon' ), $content );
  }
  private function checkImplodeValues( $value, $impval = ',', $rerun = 0 ) {
	$isli = $impval == 'ul' || $impval == 'ol' ? true : false;

	if ( !empty( $value ) && is_array( $value ) ) {
		$value2 = array();
		foreach ( $value as $key => $val ) {
			if ( !empty( $val ) && is_array( $val ) ) {
				$value2[] = $this->checkImplodeValues( $val, ',', $rerun );
				$rerun++;
			} else {
				$value2[] = $val;
				$rerun = 0;
			}
		}
		if ( $rerun == 0 ) {
			if($isli){
				$temp = "<{$impval}><li>";
				$temp .= implode( "</li><li>", $value2 );
				$temp .= "</li></{$impval}>";
				return $temp;
			}else{
				return implode( "{$impval} ", $value2 );
			}
		} elseif ( $rerun == 1 ) {
			if($isli){
				$temp = "<{$impval}><li>";
				$temp .= implode( "</li><li>", $value2 );
				$temp .= "</li></{$impval}>";
				return $temp;
			}else{
				return implode( "{$impval} ", $value2 );
			}
		} else {
			if($isli){
				$temp = "<{$impval}><li>";
				$temp .= implode( "</li><li>", $value2 );
				$temp .= "</li></{$impval}>";
				return $temp;
			}else{
				return implode( "{$impval} ", $value2 );
			}
		}
	} else {
		return $value;
	}
}
  private function get_appipCurrCode( $field = '' ) {
	$allowed = array( 'USD', 'GBP' );
	if ( isset( $field ) && $field != '' && in_array( $field, $allowed ) ) {
		return ' ' . $field;
	}
	return '';
}
  private function getBylineValues( $role = "",  $ContData = array()){
	  if(!is_array($ContData) || empty($ContData) || $role == "" )
		  return '';
	  $newCArr = array();
	  foreach($ContData as $ck => $cv){
		  if($cv['Role'] === $role){
			  $newCArr[] = $cv['Name'];
		  }
	  }
	  if(!empty($newCArr))
		  return implode(", ",$newCArr);
	  return '';
  }
  public function process_amz_posts($getArr){
		if(!is_array($getArr) || empty($getArr))
			return array('requests','asins','updates');
		$amz_posts 		= $getArr['amz_posts'];
		$ID_matches 	= $getArr['id_matches'];
		$request 		= array();
		$defaults 		= array('private_key' => '','method' => 'GET','protocol' => 'https://','host' => '','uri' => '','asins' => array(),'params' => array(),'reqtype' => 'asin',);
		extract( shortcode_atts( $defaults, $getArr ) );
		$private_key 	= isset($private_key) ? $private_key : '';
		$public_key 	= isset($params['AWSAccessKeyId']) ? $params['AWSAccessKeyId'] : '';
		$temp 			= array_chunk(array_unique($asins), 10);
		$asins			= array_chunk($asins, 10);

		if( $reqtype == 'asin'){
			$requestedASINs = array();
			if( is_array( $asins ) && !empty( $asins ) ){
				foreach($asins as $akey => $aval){
					$pairs 					= array();
					$params['ItemId']		= implode(',',$aval);
					foreach ($params as $key => $value)
						array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
					$canonical_query_string = join("&", $pairs);
					$string_to_sign 		= $method."\n".$host."\n".$uri."\n".$canonical_query_string;
					$signature				= base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, true));
					$request[] 				= $protocol.$host.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
					$requestedASINs[] 		= $aval;
				}
			}
			return array( 'requests' => $request, 'asins' => $requestedASINs, 'updates' => $requestedASINs );
		}else{
			//search
			$pairs 					= array();
			foreach ($req_params as $key => $value)
				array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
			$canonical_query_string = join("&", $pairs);
			$string_to_sign 		= $method."\n".$host."\n".$uri."\n".$canonical_query_string;
			$signature				= base64_encode(hash_hmac("sha256", $string_to_sign, $private_key, true));
			$request[] 				= $protocol.$host.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
			return array( 'requests' => $request, 'asins' => array(), 'updates' => array() );
		}
	}
  public function appip_get_JSON_structure ( $xmldata ){
		if( $xmldata == '' )
			return false;
		return json_decode($xmldata,TRUE);
	}
  public function do_request_update( $data , $ajax = false ){
		if( !is_array($data) || empty($data))
			return false;
		global $wpdb;
		$cached				 	= array();
		$aws_secret_key 		= !isset($data["private_key"]) ? apply_filters( 'amazon_product_private_key', get_option( 'apipp_amazon_secretkey', '' ), 'amazon-ad-link-lr-plugin' ) : $data["private_key"];
		$locale					= !isset($data["Locale"]) ? apply_filters( 'amazon_product_locale', get_option( 'apipp_amazon_locale', 'com' ),'amazon-ad-link-lr-plugin' ) : $data["Locale"];
		$req_params = array(
			"AWSAccessKeyId"	=> (!isset($data["AWSAccessKeyId"]) ? apply_filters( 'amazon_product_public_key', get_option( 'apipp_amazon_publickey', '' ), 'amazon-ad-link-lr-plugin' ) 	: $data["AWSAccessKeyId"]),
			"AssociateTag" 		=> (!isset($data["AssociateTag"]) 	? apply_filters( 'amazon_product_partner_id', get_option( 'apipp_amazon_associateid', '' ), 'amazon-ad-link-lr-plugin' )	: $data["AssociateTag"]),
			"ItemId" 			=> (!isset($data["ItemId"]) ? '' : $data["ItemId"]),
		);
		ksort($req_params);
		$newpxml 				= array();
		$body 					= "";
		$maxage 				= 1;
		$cachetime 				= (int) apply_filters('amazon_product_post_cache',3600);
		$asinArr				= isset($req_params["ItemId"]) ? explode(',',$req_params["ItemId"]) : array();
		$IDMatches				= isset($data["posts"]) && is_array($data["posts"]) ? array_flip($data["posts"]): array();
		$allASINs				= array();
		$allASINs['requested'] 	= !empty($asinArr) ? $asinArr : array();
		$allASINs['togetAPI'] 	= array();
		$allASINs['cached'] 	= array();
		$allASINs['toget'] 		= array();
		$needed 				= $allASINs['requested'];
		$reqAmzASINs 			= !empty( $allASINs['requested'] ) ? array_map( array( $this, 'add_lower_amazon' ), $allASINs['requested'] ) : array();
		$needed 				= array_values($needed);
		$getArr					= array('private_key'=>$aws_secret_key,'region' => $locale,'asins' =>  $needed,'params' => $req_params,'amz_posts' => $amz_posts,'id_matches' => $IDMatches,);
		$tempRequest 			= $this->process_amz_posts($getArr);
		$request				= $tempRequest['requests'];
		$requestedASINs 		= $tempRequest['asins'];
		$updateIDs 				= $tempRequest['updates'];
		$amzArr					= array();
		$amzItems 				= array();
		remove_action( 'save_post', array($this,'save_post_process_amazon'), 10);
		if(is_array($request) && !empty($request)){
			$io= 0;
			foreach( $request as $k => $v ):
				$cycleErrors = array();
				$amzArr 	= array();
				$continue 	= true;
				$skip 		= 2;
				usleep( 1000000 * $skip );
			/* NEW */
				$Regions = __getAmz_regions();
				$region = $Regions[ $locale ][ 'RegionCode' ];
				$host = $Regions[ $locale ][ 'Host' ];
				$accessKey = $req_params["AWSAccessKeyId"];
				$secretKey = $aws_secret_key ;
				$payloadArr = array();
				$payloadArr[ 'ItemIds' ] = $requestedASINs[$k];
				$payloadArr[ 'Resources' ] = array( 'CustomerReviews.Count', 'CustomerReviews.StarRating', 'Images.Primary.Small', 'Images.Primary.Medium', 'Images.Primary.Large', 'Images.Variants.Small', 'Images.Variants.Medium', 'Images.Variants.Large', 'ItemInfo.ByLineInfo', 'ItemInfo.ContentInfo', 'ItemInfo.ContentRating', 'ItemInfo.Classifications', 'ItemInfo.ExternalIds', 'ItemInfo.Features', 'ItemInfo.ManufactureInfo', 'ItemInfo.ProductInfo', 'ItemInfo.TechnicalInfo', 'ItemInfo.Title', 'ItemInfo.TradeInInfo', 'Offers.Listings.Availability.MaxOrderQuantity', 'Offers.Listings.Availability.Message', 'Offers.Listings.Availability.MinOrderQuantity', 'Offers.Listings.Availability.Type', 'Offers.Listings.Condition', 'Offers.Listings.Condition.SubCondition', 'Offers.Listings.DeliveryInfo.IsAmazonFulfilled', 'Offers.Listings.DeliveryInfo.IsFreeShippingEligible', 'Offers.Listings.DeliveryInfo.IsPrimeEligible', 'Offers.Listings.DeliveryInfo.ShippingCharges', 'Offers.Listings.IsBuyBoxWinner', 'Offers.Listings.LoyaltyPoints.Points', 'Offers.Listings.MerchantInfo', 'Offers.Listings.Price', 'Offers.Listings.ProgramEligibility.IsPrimeExclusive', 'Offers.Listings.ProgramEligibility.IsPrimePantry', 'Offers.Listings.Promotions', 'Offers.Listings.SavingBasis', 'Offers.Summaries.HighestPrice', 'Offers.Summaries.LowestPrice', 'Offers.Summaries.OfferCount', 'ParentASIN' );
				$payloadArr[ 'PartnerTag' ] = $req_params["AssociateTag"];
				$payloadArr[ 'PartnerType' ] = 'Associates';
				$payloadArr[ 'Marketplace' ] = 'www.amazon.com';
				$payload = json_encode( $payloadArr );
				$awsv5 = new Amazon_Product_Request_V5( $accessKey, $secretKey, $region, $host, 'getitems' );
				$awsv5->setPayload( $payload );
				$test = $awsv5->do_request();
			/* END NEW */
				$badIDs 	= array();
				//wait and try again if throttled
				if( is_array($test)){
					if( strpos( $test['body'], 'RequestThrottled') !== false ){
						for($i=0;$i<=3;$i++){
							usleep( 1000000 * $skip ); // sleep for set # of seconde and then try again.
							$test = $awsv5->do_request();
							if(strpos($test['body'], 'RequestThrottled') === false )
								$i= 10;
						}
					}
					$amzArr		= $this->appip_get_JSON_structure ( $test['body'] );
					$errArr		= isset($amzArr['Errors']) ? $amzArr['Errors'] : array();
					$allErrs	= array();
					$amzItems 	= isset($amzArr['ItemsResult']['Items']) ? ( isset( $amzArr['ItemsResult']['Items']['ASIN'] ) ?  array( $amzArr['ItemsResult']['Items'] ) :  $amzArr['ItemsResult']['Items'] ) : array();
					if(is_array($errArr) && !empty($errArr) ){
						foreach($errArr as $err ){
							if($err['Code'] == 'InvalidParameterValue'){
								$msg = str_replace(array('The ItemId ',' provided in the request is invalid.'),array('',''),$err['Message']);
								$cycleErrors['Errora'][$IDMatches[$msg]] = $err;
								$badIDs[] 	= $msg;
								$allErrs[] = array('Code' => $err['Code'], 'Message' => $err['Message']);
							}else{
								$allErrs[] = array('Code' => $err['Code'], 'Message' => $err['Message']);
							}
						}
						if(!empty($badIDs))
							$cycleErrors['BadIDs'] = $badIDs;
						if(!empty($cycleErrors)){
							if($ajax){
								$ajax_response['error'][] = $cycleErrors;
								$ajax_response['Code'] = 'InvalidParameterValue';
							}else{
								error_log('ERRORS[InvalidParameterValue]:'.print_r($cycleErrors, true));
							}
						}
					}
				}else{
					$continue = false;
				}

				if ( is_wp_error( $test ) ) {
				   	$error_message = $test->get_error_message();
					if($ajax)
						$ajax_response['error'][] = $error_message;
					else
				 		error_log( 'do_request_update/Something went wrong: '.$error_message);
					$continue = false;
				}elseif(!empty($allErrs) ){
					foreach($allErrs as $ek => $et){
						if($et['Code'] == 'TooManyRequests'){
							if($ajax)
								$ajax_response['error'][] = $et['Code'];
							else
								error_log( 'do_request_update/Something went wrong: TooManyRequests');
						}else{
							if($ajax){
								$ajax_response['error'][] = $et['Code'];
							}else{
								error_log( 'do_request_update/Something went wrong: '.$et['Message']);
							}
						}
					}
					$continue = false;
				}

				if($continue == true){
					$i 			= 0;
					if( !empty( $amzItems ) ){
						foreach($amzItems as $k => $Item):
							$io++;
							$asin  = $Item['ASIN'];
							$newID = $IDMatches[$asin];
							if($ajax)
								$ajax_response['status'][$newID] = $Item;
							if( $newID != 0 ){
								delete_post_meta($newID,'_amazon-response-com');
								$temo = add_post_meta($newID,'_amazon-response-com',json_encode($Item), true);
								remove_all_filters('wp_insert_post_data');
								remove_all_filters('wp_insert_post');
								remove_all_filters('save_post');
								$pst_arr = array(
									'ID' => $newID,
									'post_type'=> 'amz-product',
									'post_title' => $Prods['Title'],
									'post_modified' => date( 'Y-m-d H:i:s' ),
									'post_modified_gmt' => date( 'Y-m-d H:i:s' )
								);
								$post_id = wp_update_post( $pst_arr, true );
								if (is_wp_error($post_id)) {
									$errors = $post_id->get_error_messages();
									foreach ($errors as $error) {
										error_log($error);
									}
								}
								clean_post_cache($newID);
								if($ajax){
									$ajax_response['update'][$newID] = $newID;
								}else{
								}
								$cached[] = $newID;
							}
							$i++;
						endforeach;
					}else{
						// more work here.
						if(isset($amzArr['Errors'])){
							$asin = $amzArr['Errors'];
						}
						if(is_array($asin)){
							foreach($asin as $k => $a){
								$io++;
								$newID  = $IDMatches[$a];
								if( $newID != 0 ){
									$cached[] = $newID;
									add_post_meta($newID,'_amazon-response-com','', true);
									remove_all_filters('wp_insert_post_data');
									remove_all_filters('wp_insert_post');
									remove_all_filters('save_post');
									$pst_Arr = array(
										'ID' => $newID,
										'post_type'=> 'amz-product',
										'post_title'=> '- Invalid ['.$a.']',
										'post_modified' => date( 'Y-m-d H:i:s' ),
										'post_modified_gmt' => date( 'Y-m-d H:i:s' )
									);
									$post_id = wp_update_post( $pst_Arr, true);
									if (is_wp_error($post_id)) {
										$errors = $post_id->get_error_messages();
										foreach ($errors as $error) {
											error_log($error);
										}
									}
									clean_post_cache($newID);
									if($ajax){
										$ajax_response['update'][$newID] = $newID;
									}else{
									}
								}
							}
						}
					}
				}
			endforeach;
		}
		if($ajax)
			return $ajax_response;
		return $cached;
	}
  public function GetAPPIPReturnVals_V5( $Item = array(), $ItemB = array(), $Errors = array() ) {
    //processor function for product
    $ItemInfo = isset( $ItemB[ 'ItemInfo' ] ) ? $ItemB[ 'ItemInfo' ] : array();
    $ItemOffers = isset( $ItemB[ 'Offers' ] ) ? $ItemB[ 'Offers' ] : array();
    $ItemOffSum = isset( $ItemOffers['Summaries' ] ) ? $ItemOffers['Summaries' ] : array();
	$ItemImages = isset( $ItemB['Images' ] ) ? $ItemB['Images' ] : array();
	$contributors = isset( $ItemInfo['ByLineInfo']['Contributors']) ? $ItemInfo['ByLineInfo']['Contributors'] : array();
	/* Unavailable */
		//ItemLinks
		//PacakgeQuantity
		//Publisher
		$appGenre = '';
		$appHardwarePlatform = '';
		$appHazardousMaterialType = '';
		$appIsAutographed = '';
		$appIsMemorabilia = '';
		$appIssuesPerYear = '';
		$appAspectRatio = '';
		$appAudioFormat = '';
		$appEISBN = '';
		$appESRBAgeRating = '';
		$appCEROAgeRating = '';
		$appEpisodeSequence = '';
		$appDepartment = '';
		$appNumberOfDiscs = '';
		$appNumberOfIssues = '';
		$appNumberOfTracks = '';
		$appOperatingSystem = '';
		$appLegalDisclaimer = '';
		$appManufacturerMaximumAge = '';
		$appManufacturerMinimumAge = '';
		$appMediaType = '';
		$appPlatform = '';
		$appProductTypeSubcategory = '';
		$appRegionCode = '';
		$appSeikodoProductCode = '';
		$appSKU = '';
		$appSubscriptionLength = '';
		$appWEEETaxValue = '';
		$appMagazineType = '';
		$appModelYear = '';
		$appItemDimensions = '';
		$appPackageQuantity = '';
		$appPictureFormat = '';
		$appProductTypeName = '';
		$appRunningTime = '';
		$appSubscriptionUnit = '';
		$appTrackSequence = '';

	/* End Unavailable */
	/* DONE Converting */
		$ASIN = isset( $Item[ 'ASIN' ] ) ? $Item[ 'ASIN' ] : '';
		$ImageSM = isset( $Item['Images_Primary_Small_URL' ] ) ? $Item['Images_Primary_Small_URL' ] : '';
		$ImageMD = isset( $Item['Images_Primary_Medium_URL' ] ) ? $Item['Images_Primary_Medium_URL' ] : '';
		$ImageLG = isset( $Item['Images_Primary_Large_URL' ] ) ? $Item['Images_Primary_Large_URL' ] : '';
		$ImageHiRes = isset( $Item['Images_Primary_HiRes_URL' ] ) ? $Item['Images_Primary_HiRes_URL' ] : $ImageLG;
	  	$ImageSets = isset( $ItemImages[ 'Variants' ] ) ? $ItemImages[ 'Variants' ] : array();
		$ImageSetsArray = array();
		if ( !empty( $ImageSets ) ) {
			foreach ( $ImageSets as $imgset ) {
				if ( isset( $imgset[ 'Large' ][ 'URL' ] ) && $imgset[ 'Large' ][ 'URL' ] != $ImageLG ) {
				  $ImageSetsArray[] = '<a rel="appiplightbox-' . $ASIN . '" href="#" data-appiplg="' . $this->checkSSLImages_url( $imgset[ 'Large' ][ 'URL' ] ) . '"><img src="' . $this->checkSSLImages_url( $imgset[ 'Small' ][ 'URL' ] ) . '" alt="' . ( apply_filters( 'appip_alt_text_gallery_img', 'Img - ' . $ASIN, $ASIN ) ) . '" class="apipp-additional-image"/></a>' . "\n";
				}
			}
		}
		$DetailPageURL = isset( $Item[ 'DetailPageURL' ] ) ? $Item[ 'DetailPageURL' ] : '';
		$cached = isset( $Item[ "CachedAPPIP" ] ) ? $Item[ "CachedAPPIP" ] : 0;
    	$appReleaseDate = isset( $Item["ItemInfo_ProductInfo_ReleaseDate_DisplayValue"] ) ? $Item["ItemInfo_ProductInfo_ReleaseDate_DisplayValue"] : '';
		$appShoeSize = isset( $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] : '';
	  	if(is_array($contributors) && !empty($contributors)){
			$appActor =  $this->getBylineValues(  "Actor" ,  $contributors);
			$appArtist = $this->getBylineValues(  "Artist" ,  $contributors);
			$appAuthor = $this->getBylineValues(  "Author" ,  $contributors);
			$appDirector = $this->getBylineValues(  "Director" ,  $contributors);
			$appWriter = $this->getBylineValues(  "Writer" ,  $contributors);
			$appCreator = $this->getBylineValues(  "Creator" ,  $contributors);
			$appProducer = $this->getBylineValues(  "Producer" ,  $contributors);
    		$appPublisher = $this->getBylineValues(  "Publisher" ,  $contributors);
		}else{
			$appActor =
			$appArtist =
			$appAuthor =
			$appDirector =
			$appWriter =
			$appCreator =
			$appProducer =
			$appPublisher = '';
		}

    	$appIsAdultProduct = isset( $ItemInfo["ItemInfo_ProductInfo_IsAdultProduct_DisplayValue"] ) && (bool) $ItemInfo["ItemInfo_ProductInfo_IsAdultProduct_DisplayValue"] === true  ? true : false;
    	$appRating = isset( $Item["ItemInfo_ContentRating_AudienceRating_DisplayValue"] ) ? $Item["ItemInfo_ContentRating_AudienceRating_DisplayValue"] : '';
		$appAudienceRating = $appRating;
    	$appFeature = isset( $ItemInfo["Features"]["DisplayValues"]) ? $this->checkImplodeValues( (array) $ItemInfo["Features"]["DisplayValues"],'ul' ) : '';

// check these:
$appLanguages = isset( $ItemInfo["ContentInfo"]["Languages"]["DisplayValues"] ) ? $ItemInfo["ContentInfo"]["Languages"]["DisplayValues"] : '';
// end to check

		$appBinding = isset( $Item[ 'ItemInfo_Classifications_Binding_DisplayValue' ] ) ? $Item[ 'ItemInfo_Classifications_Binding_DisplayValue' ] : '';
		$hideBinding = ( bool )apply_filters( 'amazon-hide-binding-in-title', false, $appBinding );
		$appTitle = isset( $ItemInfo[ 'Title' ]["DisplayValue"] ) ?  $ItemInfo[ "Title" ]["DisplayValue"] : '';
		if ( !$hideBinding && $appBinding != '' ){
			$appTitle = $appTitle . ' (' . $appBinding . ')';
		}
		$appEAN = isset( $Item[ 'ItemInfo_ExternalIds_EANs_DisplayValues_0' ] ) ?  $Item[ "ItemInfo_ExternalIds_EANs_DisplayValues_0" ] : '';
		$appEANList = isset( $ItemInfo[ 'ExternalIds']['EANs'][ 'DisplayValues' ] ) ? ( is_array( $ItemInfo[ 'ExternalIds']['EANs'][ 'DisplayValues' ] ) ? $this->checkImplodeValues( $ItemInfo[ 'ExternalIds']['EANs'][ 'DisplayValues' ] ) : $ItemInfo[ 'ExternalIds']['EANs'][ 'DisplayValues' ]) : '';
		$appFormat = isset( $ItemInfo[ "TechnicalInfo"]["Formats"]["DisplayValues"]['0'] ) ? $ItemInfo[ "TechnicalInfo"]["Formats"]["DisplayValues"]['0'] : '';
		$appBrand = isset( $Item['ItemInfo_ByLineInfo_Brand_DisplayValue'] ) ? $Item['ItemInfo_ByLineInfo_Brand_DisplayValue'] : '';
		$appLabel = isset( $Item[ 'ItemInfo_ByLineInfo_Brand_DisplayValue' ] ) ? $Item[ 'ItemInfo_ByLineInfo_Brand_DisplayValue' ] : '';
		$appClothingSize = isset( $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] : '';
		$appColor = isset( $Item[ 'ItemInfo_ProductInfo_Color_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_Color_DisplayValue' ] : '';
		$appEdition = isset( $Item[ 'ItemInfo_ContentInfo_Edition_DisplayValue' ] ) ? $Item[ 'ItemInfo_ContentInfo_Edition_DisplayValue' ] : '';
		$appNumberOfItems = isset( $Item[ 'ItemInfo_ProductInfo_UnitCount_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_UnitCount_DisplayValue' ] : '';
		$appNumberOfPages = isset( $Item[ 'ItemInfo_ContentInfo_PagesCount_DisplayValue' ] ) ? $Item[ 'ItemInfo_ContentInfo_PagesCount_DisplayValue' ] : '';
		$appManufacturerPartsWarrantyDescription = isset( $Item[ 'ItemInfo_ManufactureInfo_Warranty_DisplayValue' ] ) ? $Item[ 'ItemInfo_ManufactureInfo_Warranty_DisplayValue' ] : '';
	    $appWarranty = $appManufacturerPartsWarrantyDescription;

		$appModel = isset( $Item[ 'ItemInfo_ManufactureInfo_Model_DisplayValue' ] ) ? $Item[ 'ItemInfo_ManufactureInfo_Model_DisplayValue' ] : '';
		$appMPN = isset( $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] ) ? $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] : '';
	  	$appISBN = isset( $Item[ 'ItemInfo_ExternalIds_ISBNs_DisplayValues_0' ] ) ? $Item[ 'ItemInfo_ExternalIds_ISBNs_DisplayValues_0' ] : '';
		$appIsEligibleForTradeIn = isset( $Item[ 'ItemInfo_TradeInInfo_IsEligibleForTradeIn' ] ) && (bool) $Item[ 'ItemInfo_TradeInInfo_IsEligibleForTradeIn' ] === true ? true : false;
		$appItemPartNumber = isset( $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] ) ? $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] : '';
		$appPackageDimensionsWidth = isset( $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Width_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Width_DisplayValue' ] .' '. $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Width_Unit' ]: '';
		$appPackageDimensionsHeight = isset( $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Height_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Height_DisplayValue' ] .' '. $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Height_Unit' ] : '';
		$appPackageDimensionsLength = isset( $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Length_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Length_DisplayValue' ] .' '. $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Length_Unit' ] : '';
		$appPackageDimensionsWeight = isset( $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Weight_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Weight_DisplayValue' ] .' '. $Item[ 'ItemInfo_ProductInfo_ItemDimensions_Weight_Unit' ] : '';
		$appPackageDimensions = array();
		$appPartNumber = isset( $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] ) ? $Item[ 'ItemInfo_ManufactureInfo_ItemPartNumber_DisplayValue' ] : '';
		$appProductGroup = isset( $Item[ 'ItemInfo_Classifications_ProductGroup_DisplayValue' ] ) ? $Item[ 'ItemInfo_Classifications_ProductGroup_DisplayValue' ]  : '';
		$appPublicationDate = isset( $Item[ 'ItemInfo_ContentInfo_PublicationDate_DisplayValue' ] ) ? $Item[ 'ItemInfo_ContentInfo_PublicationDate_DisplayValue' ] : '';
		$appSize = isset( $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] ) ? $Item[ 'ItemInfo_ProductInfo_Size_DisplayValue' ] : '';
		$appStudio = isset( $Item[ 'ItemInfo_ByLineInfo_Manufacturer_DisplayValue' ] ) ? $Item[ 'ItemInfo_ByLineInfo_Manufacturer_DisplayValue' ] : '';
		$appTradeInValue = isset( $Item[ 'ItemInfo_TradeInInfo_Price_DisplayAmount' ] ) ? $Item[ 'ItemInfo_TradeInInfo_Price_DisplayAmount' ] : '';
		$appTradeInValueCurrency = isset( $Item[ 'ItemInfo_TradeInInfo_Price_Currency' ] ) ? $Item[ 'ItemInfo_TradeInInfo_Price_Currency' ] : '';
		$appUPC = isset( $Item[ 'ItemInfo_ExternalIds_UPCs_DisplayValues_0' ] ) ? $Item[ 'ItemInfo_ExternalIds_UPCs_DisplayValues_0' ] : '';
		$appUPCList = isset( $ItemInfo[ 'ExternalIds']['UPCs'][ 'DisplayValues' ]  ) ? ( is_array( $ItemInfo[ 'ExternalIds']['UPCs'][ 'DisplayValues' ] ) ? $this->checkImplodeValues( $ItemInfo[ 'ExternalIds']['UPCs'][ 'DisplayValues' ] ) : '' ) : '';
		$appManufacturer = isset($Item[ 'ItemInfo_ByLineInfo_Manufacturer_DisplayValue' ]) ? $Item[ 'ItemInfo_ByLineInfo_Manufacturer_DisplayValue' ] : '';

	  /* API V5 No Longer Have Reviews */
		$appHasReviews = 'false';
		$appCustomerReviews = '';
		$showCurCodes = apply_filters( 'amazon_product_show_curr_codes', true );

		$newAmzPricing = array();
		if(isset($ItemOffers['Listings']) && is_array($ItemOffers['Listings']) && !empty($ItemOffers['Listings'])){
			foreach($ItemOffers['Listings'] as $Okey => $Oval ){
				$isBuyBox = isset($Oval['IsBuyBoxWinner']) && (bool) $Oval['IsBuyBoxWinner'] === true ? true : false;
				$atype = $Oval["Condition"]["Value"];
				$price = isset($Oval["Price"]) ? ( $Oval["Price"]["DisplayAmount"].( $showCurCodes ? ( isset($Oval["Price"]["CurrencyCode"]) ? ' '.$Oval["Price"]["CurrencyCode"] : '') : '') ):'';
				$freeShip = isset( $Oval["DeliveryInfo"]["IsFreeShippingEligible"] ) ? (bool)$Oval["DeliveryInfo"]["IsFreeShippingEligible"] : false;
				$AnazonFill = isset( $Oval["DeliveryInfo"]["IsAmazonFulfilled"] ) ? (bool)$Oval["DeliveryInfo"]["IsAmazonFulfilled"] : false ;
				$PrimeElig = isset($Oval["DeliveryInfo"]["IsPrimeEligible"]) ? (bool) $Oval["DeliveryInfo"]["IsPrimeEligible"] : false;
				$onsale = isset($Oval["SavingBasis"]) ? true : false;
				$avail = isset($Oval["Availability"]["Message"]) && $Oval["Availability"]["Message"] == "In Stock" ? true : false;
				if(isset($Oval["SavingBasis"])){
					$list_price = $Oval["SavingBasis"]["DisplayAmount"].( $showCurCodes ? ( isset($Oval["SavingBasis"]["DisplayAmount"]["CurrencyCode"]) ? ' '.$Oval["SavingBasis"]["DisplayAmount"]["CurrencyCode"] : '') : '');
					$sale_price = $price;
					$savings_perc = $Oval["Price"]["Savings"]["Percentage"];
					$saved_amt = $Oval["Price"]["Savings"]["DisplayAmount"].( $showCurCodes ? ( isset($Oval["Price"]["Savings"]["CurrencyCode"]) ? ' '.$Oval["Price"]["Savings"]["CurrencyCode"] : '') : '');
				}else{
					$list_price = $price;
					$sale_price = $price;
					$savings_perc = 0;
					$saved_amt = 0;
					$onsale = false;
				}
				if($isBuyBox){
					$newAmzPricing[ $atype ][ 'List' ] = $list_price;
					$newAmzPricing[ $atype ][ 'Price' ] = $price;
					$newAmzPricing[ $atype ][ 'SalePrice' ] = $sale_price;
					$newAmzPricing[ $atype ][ 'Saved' ] = $saved_amt;
					$newAmzPricing[ $atype ][ 'SavedPercent' ] = $savings_perc;
					$newAmzPricing[ $atype ][ 'IsEligibleForSuperSaverShipping' ] = $freeShip;
					$newAmzPricing[ $atype ][ 'Onsale' ] = $onsale;
					$newAmzPricing[ $atype ][ 'IsPrimeEligible' ] = $PrimeElig;
					$newAmzPricing[ $atype ][ 'IsAmazonFulfilled' ] = $AnazonFill;
					$newAmzPricing[ $atype ][ 'InStock' ] = $avail;
				}else{
					$newAmzPricing[ $atype ][ 'List' ] = $list_price;
					$newAmzPricing[ $atype ][ 'Price' ] = $price;
					$newAmzPricing[ $atype ][ 'SalePrice' ] = $sale_price;
					$newAmzPricing[ $atype ][ 'Saved' ] = $saved_amt;
					$newAmzPricing[ $atype ][ 'SavedPercent' ] = $savings_perc;
					$newAmzPricing[ $atype ][ 'IsEligibleForSuperSaverShipping' ] = $freeShip;
					$newAmzPricing[ $atype ][ 'Onsale' ] = $onsale;
					$newAmzPricing[ $atype ][ 'IsPrimeEligible' ] = $PrimeElig;
					$newAmzPricing[ $atype ][ 'IsAmazonFulfilled' ] = $AnazonFill;
					$newAmzPricing[ $atype ][ 'InStock' ] = $avail;
				}
			}
		}
		if(isset($ItemOffers['Summaries']) && is_array($ItemOffers['Summaries']) && !empty($ItemOffers['Summaries'])){
			foreach($ItemOffers['Summaries'] as $Okey => $Oval ){
				$atype = $Oval["Condition"]["Value"];
				$OfferCount = (int) $Oval["OfferCount"];
				$newAmzPricing[ $atype.'From' ][ 'List' ] = isset($newAmzPricing[ $atype ][ 'List' ]) ? $newAmzPricing[ $atype ][ 'List' ] : '';
				$newAmzPricing[ $atype.'From' ][ 'Price' ] = isset($Oval["LowestPrice"]["DisplayAmount"]) ? $atype.' From' . $Oval["LowestPrice"]["DisplayAmount"] : '';
				$newAmzPricing[ $atype ][ 'OfferCount' ] = $OfferCount;
				$newAmzPricing[ $atype ][ 'HighestPrice' ] = isset($Oval["HighestPrice"]["DisplayAmount"]) ? $Oval["HighestPrice"]["DisplayAmount"] : '';
				$newAmzPricing[ $atype ][ 'LowestPrice' ] = isset($Oval["LowestPrice"]["DisplayAmount"]) ? $Oval["LowestPrice"]["DisplayAmount"] : '';
			}
			$appTotalOffers = isset($ItemOffers['Summaries']);
		}
		$appLowestNewPrice = isset($newAmzPricing[ "New" ][ 'LowestPrice' ] ) ? $newAmzPricing[ "New" ][ 'LowestPrice' ] : '';
		$appHighestNewPrice = isset($newAmzPricing[ "New" ][ 'HighestPrice' ] ) ? $newAmzPricing[ "New" ][ 'HighestPrice' ] : '';
		$appLowestUsedPrice = isset($newAmzPricing[ "Used" ][ 'LowestPrice' ] ) ? $newAmzPricing[ "Used" ][ 'LowestPrice' ] : '';
		$appHighestUsedPrice = isset($newAmzPricing[ "Used" ][ 'HighestPrice' ] ) ? $newAmzPricing[ "Used" ][ 'HighestPrice' ] : '';
		$appLowestRefurbishedPrice = isset($newAmzPricing[ "Refurbished" ][ 'LowestPrice' ] ) ? $newAmzPricing[ "Refurbished" ][ 'LowestPrice' ] : '';
		$appHighestRefurbishedPrice = isset($newAmzPricing[ "Refurbished" ][ 'HighestPrice' ] ) ? $newAmzPricing[ "Refurbished" ][ 'HighestPrice' ] : '';
		$appLowestCollectiblePrice = isset($newAmzPricing[ "Collectible" ][ 'LowestPrice' ] ) ? $newAmzPricing[ "Collectible" ][ 'LowestPrice' ] : '';
		$appHighestCollectiblePrice = isset($newAmzPricing[ "Collectible" ][ 'HighestPrice' ] ) ? $newAmzPricing[ "Collectible" ][ 'HighestPrice' ] : '';
		$appListPrice = isset( $newAmzPricing[ "New" ][ 'List' ] ) ? $newAmzPricing[ "New" ][ 'List' ] : '';
	    $appTotalNew = isset( $newAmzPricing[ "New" ][ 'OfferCount' ] ) ? $newAmzPricing[ "New" ][ 'OfferCount' ] : 0;
		$appTotalUsed = isset( $newAmzPricing[ "Used" ][ 'OfferCount' ] ) ? $newAmzPricing[ "Used" ][ 'OfferCount' ] : 0;
		$appTotalRefurbished = isset( $newAmzPricing[ "Refurbished" ][ 'OfferCount' ] ) ? $newAmzPricing[ "Refurbished" ][ 'OfferCount' ] : 0;
		$appTotalCollectible = isset( $newAmzPricing[ "Collectible" ][ 'OfferCount' ] ) ? $newAmzPricing[ "Collectible" ][ 'OfferCount' ] : 0;

		$isPriceHidden = ( $appLowestNewPrice == 'Too low to display' ) ? 1 : 0;

		$appTotalOffers = '';
		$appMoreOffersUrl = '';
		$appTotalOfferPages = '';
	  	$DescriptionAmz = '';
	    $EDescprition = array( 'features' => $appFeature );
	  /* TO BE Converted */

	  /* Net Set at the moment */
	  $variations = '';
	  $appCatalogNumberList = '';
	  $appCategory = '';
	  $appvHighestPrice = '';
      $appvLowestPrice = '';
      $appvLowestSalePrice = '';
      $appvHighestSalePrice = '';
	  /* end not set */

	  $Errors = empty($Errors) ? '' : $Errors;
	  $imgArr = array( 'sm' => $ImageSM, 'med' => $ImageMD, 'lg' => $ImageLG, 'hi' => $ImageHiRes );
    $RetVal = array(
      //default items
      'ASIN' => "{$ASIN}",
      'Errors' => "{$Errors}",
      'URL' => "{$DetailPageURL}",
      'CartURL' => "https://www.amazon.##REGION##/gp/aws/cart/add.html?AssociateTag=##AFFID##&SubscriptionId=##SUBSCRIBEID##&ASIN.1={$ASIN}&Quantity.1=1",
      'Title' => "{$appTitle}",
'Variations' => $variations, //??

      'SmallImage' => apply_filters( 'amazon-product-main-image-sm', "{$ImageSM}", $imgArr ),
      'MediumImage' => apply_filters( 'amazon-product-main-image-md', "{$ImageMD}", $imgArr ),
      'LargeImage' => apply_filters( 'amazon-product-main-image-lg', "{$ImageLG}", $imgArr ),
      'ImageHiRes' => apply_filters( 'amazon-product-main-image-hi', "{$ImageHiRes}", $imgArr ),
      'HiResImage' => apply_filters( 'amazon-product-main-image-hi', "{$ImageHiRes}", $imgArr ),

'AddlImages' => implode( '', $ImageSetsArray ), //??
'AddlImagesArr' => $ImageSetsArray, //??
      'PriceHidden' => "{$isPriceHidden}",
'CustomerReviews' => "{$appCustomerReviews}", //??

      //item attribs
      "Actor" => "{$appActor}",
      "Artist" => "{$appArtist}",
      "AspectRatio" => "{$appAspectRatio}",
      "AudienceRating" => "{$appAudienceRating}",
      "AudioFormat" => "{$appAudioFormat}",
      "Author" => "{$appAuthor}",
      "Binding" => "{$appBinding}",
      "Brand" => "{$appBrand}",
      "CatalogNumberList" => "{$appCatalogNumberList}",
      "Category" => "{$appCategory}",
      "CEROAgeRating" => "{$appCEROAgeRating}",
      "ClothingSize" => "{$appClothingSize}",
      "Color" => "{$appColor}",
      "Creator" => "{$appCreator}",
      "Department" => "{$appDepartment}",
      "Director" => "{$appDirector}",
      "EAN" => "{$appEAN}",
      "EANList" => "{$appEANList}",
      "Edition" => "{$appEdition}",
      "EISBN" => "{$appEISBN}",
      "EpisodeSequence" => "{$appEpisodeSequence}",
      "ESRBAgeRating" => "{$appESRBAgeRating}",
      "Feature" => "{$appFeature}",
      "Format" => "{$appFormat}",
      "Genre" => "{$appGenre}",
      "HardwarePlatform" => "{$appHardwarePlatform}",
      "HazardousMaterialType" => "{$appHazardousMaterialType}",
      "IsAdultProduct" => "{$appIsAdultProduct}",
      "IsAutographed" => "{$appIsAutographed}",
      "ISBN" => "{$appISBN}",
      "IsEligibleForTradeIn" => "{$appIsEligibleForTradeIn}",
      "IsMemorabilia" => "{$appIsMemorabilia}",
      "IssuesPerYear" => "{$appIssuesPerYear}",
      'ItemDesc' => $EDescprition,
      "ItemDimensions" => $appItemDimensions,
      "ItemPartNumber" => "{$appItemPartNumber}",
      "Label" => "{$appLabel}",
      "Languages" => $appLanguages,
      "LegalDisclaimer" => "{$appLegalDisclaimer}",
      "ListPrice" => "{$appListPrice}",
      "MagazineType" => "{$appMagazineType}",
      "Manufacturer" => "{$appManufacturer}",
      "ManufacturerMaximumAge" => "{$appManufacturerMaximumAge}",
      "ManufacturerMinimumAge" => "{$appManufacturerMinimumAge}",
      "ManufacturerPartsWarrantyDescription" => "{$appManufacturerPartsWarrantyDescription}",
      "MediaType" => "{$appMediaType}",
      "Model" => "{$appModel}",
      "ModelYear" => "{$appModelYear}",
      "MPN" => "{$appMPN}",
      "NumberOfDiscs" => "{$appNumberOfDiscs}",
      "NumberOfIssues" => "{$appNumberOfIssues}",
      "NumberOfItems" => "{$appNumberOfItems}",
      "NumberOfPages" => "{$appNumberOfPages}",
      "NumberOfTracks" => "{$appNumberOfTracks}",
      "OperatingSystem" => "{$appOperatingSystem}",
      "PackageDimensions" => $appPackageDimensions,
      "PackageDimensionsWidth" => "{$appPackageDimensionsWidth}",
      "PackageDimensionsHeight" => "{$appPackageDimensionsHeight}",
      "PackageDimensionsLength" => "{$appPackageDimensionsLength}",
      "PackageDimensionsWeight" => "{$appPackageDimensionsWeight}",
      "PackageQuantity" => "{$appPackageQuantity}",
      "PartNumber" => "{$appPartNumber}",
      "PictureFormat" => "{$appPictureFormat}",
      "Platform" => "{$appPlatform}",
      "ProductGroup" => "{$appProductGroup}",
      "ProductTypeName" => "{$appProductTypeName}",
      "ProductTypeSubcategory" => "{$appProductTypeSubcategory}",
      "PublicationDate" => "{$appPublicationDate}",
      "Publisher" => "{$appPublisher}",
      "RegionCode" => "{$appRegionCode}",
      "Rating" => "{$appRating}",
      "ReleaseDate" => "{$appReleaseDate}",
      "RunningTime" => "{$appRunningTime}",
      "SeikodoProductCode" => "{$appSeikodoProductCode}",
      "ShoeSize" => "{$appShoeSize}",
      "Size" => "{$appSize}",
      "SKU" => "{$appSKU}",
      "Studio" => "{$appStudio}",
      "SubscriptionLength" => "{$appSubscriptionLength}",
      "SubscriptionLengthUnits" => "{$appSubscriptionUnit}",
      "TrackSequence" => "{$appTrackSequence}",
      "TradeInValue" => "{$appTradeInValue}",
      "UPC" => "{$appUPC}",
      "UPCList" => "{$appUPCList}",
      "Warranty" => "{$appWarranty}",
      "WEEETaxValue " => "{$appWEEETaxValue}",

      // Offers
      "LowestNewPrice" => "{$appLowestNewPrice}",
      "LowestUsedPrice" => "{$appLowestUsedPrice}",
      "LowestRefurbishedPrice" => "{$appLowestRefurbishedPrice}",
      "LowestCollectiblePrice" => "{$appLowestCollectiblePrice}",
      "TotalCollectible" => "{$appTotalCollectible}",
      "TotalNew" => "{$appTotalNew}",
      //"TotalOfferPages" => "{$appTotalOfferPages}",
      "TotalOffers" => "{$appTotalOffers}",
      "TotalRefurbished" => "{$appTotalRefurbished}",
      "TotalUsed" => "{$appTotalUsed}",
      "NewAmazonPricing" => $newAmzPricing,
      "TotalAmzOffers" => $appTotalOffers,
      "VarHighestPrice" => $appvHighestPrice,
      "VarLowestPrice" => $appvLowestPrice,
      "VarLowestSalePrice" => $appvLowestSalePrice,
      "VarHighestSalePrice" => $appvHighestSalePrice,
      "MoreOffersUrl" => $appMoreOffersUrl,
      "TotalOfferPages" => $appTotalOfferPages,
      "CachedAPPIP" => $cached,
    );

    foreach ( $RetVal as $key => $val ) {
      $RetVal[ $key ] = apply_filters( "amazon_product_array_{$key}", $val, $ASIN, $RetVal );
    }
    return apply_filters( 'apipp_amazon_product_array_filter', $RetVal, $Item );
  }
}

