<?php
class Amazon_Product_Setup_AltDB_hf87 {
	private $amzwpdb;
	private $amzDBSaves = array();
	private $has_DBSaves = false;
	public function __construct() {
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}
	public function addDBSave( $item ) {
		if ( $item != '' ) {
			$this->amzDBSaves[] = ( substr( $item, -1 ) == ';' ? '' : ';' ) . $item;
			$this->has_DBSaves = true;
		}
	}
	public function shutdown() {
		global $wpdb;
		if ( $this->has_DBSaves && is_array( $this->amzDBSaves ) && !empty( $this->amzDBSaves ) ) {
			foreach ( $this->amzDBSaves as $qry ) {
				$tesr = $wpdb->query( $qry );
			}
		}
	}
}

global $amz_wpdb;
global $amazonCache;
global $cacheArrayAPPIP;
$amz_wpdb = new Amazon_Product_Setup_AltDB_hf87();

function appip_blowoffarr( $Item, $key = "", $blowoffArr = array() ) {
	$dontuse = apply_filters( 'amazon_product_in_a_post_blowoffarr_dontuse', array( 'BrowseNodes', 'SimilarProducts' ) );
	foreach ( $Item as $var => $val ) {
		if ( !in_array( $var, $dontuse ) ) {
			if ( $key == "" ) {
				if ( is_array( $val ) ) {
					$blowoffArr = appip_blowoffarr2( $val, $var, $blowoffArr );
				} else {
					$blowoffArr[ $var ] = $val;
				}
			} else {
				if ( is_array( $val ) ) {
					$blowoffArr = appip_blowoffarr2( $val, $key . '_' . $var, $blowoffArr );
				} else {
					$blowoffArr[ $key . '_' . $var ] = $val;
				}
			}
		}
	}
	return $blowoffArr;
}

function appip_blowoffarr2( $Item, $key = "", $blowoffArr = array() ) {
	$dontuse = apply_filters( 'amazon_product_in_a_post_blowoffarr_dontuse', array( 'BrowseNodes', 'SimilarProducts' ) );
	foreach ( $Item as $var => $val ) {
		if ( !in_array( $var, $dontuse ) ) {
			if ( $key == "" ) {
				if ( is_array( $val ) ) {
					$blowoffArr = appip_blowoffarr2( $val, $var, $blowoffArr );
				} else {
					$blowoffArr[ $var ] = $val;
				}
			} else {
				if ( is_array( $val ) ) {
					$blowoffArr = appip_blowoffarr2( $val, $key . '_' . $var, $blowoffArr );
				} else {
					$blowoffArr[ $key . '_' . $var ] = $val;
				}
			}
		}
	}
	return $blowoffArr;
}

function checkImplodeValues( $value, $impval = ',', $rerun = 0 ) {
	$isli = $impval == 'ul' || $impval == 'ol' ? true : false;

	if ( !empty( $value ) && is_array( $value ) ) {
		$value2 = array();
		foreach ( $value as $key => $val ) {
			if ( !empty( $val ) && is_array( $val ) ) {
				$value2[] = checkImplodeValues( $val, ',', $rerun );
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

function get_appipCurrCode( $field = '' ) {
	$allowed = array( 'USD', 'GBP' );
	if ( isset( $field ) && $field != '' && in_array( $field, $allowed ) ) {
		return ' ' . $field;
	}
	return '';
}

function appip_get_JSON_structure( $xmldata, $cached = 0, $url = '' ) {
	global $cacheArrayAPPIP;
	if ( $xmldata == '' ){
		return false;
	}
	
	if ( $url != '' && isset( $cacheArrayAPPIP[ $url ] ) ){
		return $cacheArrayAPPIP[ $url ];
	}
	// for searches - the db escape like adds escapes to % and _ characters when added to DB.
	$xmldata = str_replace(array('\\%','\\_'),array('%','_'),$xmldata);
	$json = json_decode( $xmldata, TRUE );
	$cacheArrayAPPIP[ $url ] = $json; 				
	if(is_array($json) && !empty($json)){
		
	}else{
		trigger_error('Invalid JSON structure.', E_USER_WARNING);
	}
	return $json; 
}

function amazon_plugin_aws_signed_request( $region = '', $params = array(), $publickey = '', $privatekey = '', $skip = false) {
	global $wpdb, $amazonCache, $amz_wpdb;
	$newpxml = array();
	$cachetime = ( int )apply_filters( 'amazon_product_post_cache', 3600 );
	$publickey = $publickey == '' ? APIAP_PUB_KEY : $publickey;
	$privatekey = $privatekey == '' ? APIAP_SECRET_KEY : $privatekey;
	$region = $region == '' ? APIAP_LOCALE : $region;
	$params[ 'RequestBy' ] = !isset( $params[ 'RequestBy' ] ) ? '' : $params[ 'RequestBy' ];
	$params[ 'Locale' ] = $region;
	$params[ 'AssociateTag' ] = !isset( $params[ 'AssociateTag' ] ) ? APIAP_ASSOC_ID : $params[ 'AssociateTag' ];
	$params[ 'Operation' ] = !isset( $params[ 'Operation' ] ) || $params[ 'Operation' ] == '' ? 'GetItems' : $params[ 'Operation' ];
	$params[ 'IdType' ] = !isset( $params[ 'IdType' ] ) ? 'ASIN' : $params[ 'IdType' ];
	$params[ 'publickey' ] = $publickey;
	$params[ 'privatekey' ] = $privatekey;
	$allASINs = array( 'requested' => array(), 'togetAPI' => array(), 'cached' => array(), 'needed' => array() );
	$body = "";
	$cacheOnly = isset( $params[ "CacheOnly" ] ) && ( bool )$params[ "CacheOnly" ] === true ? true : false;
	$isSearch = isset( $params[ 'Operation' ] ) && $params[ 'Operation' ] == 'SearchItems' ? true : false;
	$item_count = isset($params[ 'item_count' ]) ? (int)$params[ 'item_count' ] : 10;
	$asinArrTemp = isset( $params[ "ItemId" ] ) ? ( is_array( $params[ "ItemId" ] ) ? $params[ "ItemId" ] : explode( ',', $params[ "ItemId" ] ) ) : array();
	$asinArr = array();
	//fix potential spaces in ASINs
	if( !empty( $asinArrTemp ) ){
		foreach( $asinArrTemp as $ak => $av ){
			$ak = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$ak);
			$av = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$av);
			$asinArr[$ak] = $av;
		}
	}
	$allASINs[ 'requested' ] = !empty( $asinArr ) ? $asinArr : array();
	$allASINs[ 'needed' ] = $allASINs[ 'requested' ];
	$start = 1;
	$indx = 0;
	if ( $isSearch )
		unset( $params[ 'IdType' ], $params[ 'item_count' ] );
	unset( $params[ "CacheOnly" ], $params[ 'RequestBy' ] );
	ksort( $params );
	/***************
	 * Check if ASINs are in CACHE already
	 ***************/
	$userauth = 'spade';
	$skip_cache = isset( $_GET[ 'purge-cache' ] ) && isset( $_GET[ 'auth' ] ) && $_GET[ 'purge-cache' ] == '1' && $_GET[ 'auth' ] == $userauth ? true : false;
	$oldCacheDel = apply_filters( 'amazon-product-delete-old-cache', false );
	//take the time to delete old cache files when doing just a cache only request.
	if ( $cacheOnly && !$oldCacheDel ) {
		$checksql = "DELETE FROM {$wpdb->prefix}amazoncache WHERE (NOW() - `updated`) > '{$cachetime}';";
		$wpdb->get_results( $checksql );
		$oldCacheDel = true;
		if ( $skip_cache )
			return array();
	}
	if ( $skip === false ) {
		/* Requests are slightly different for SearchItmes and GetItmes, so split them out. */
		if ( isset($params[ 'Operation' ]) && $params[ 'Operation' ] === 'SearchItems' ) {
			// Search Items Request
			$addlResponse = array();
			$result = $amazonCache->get_amazon_plugin_cache( 'search', $params );
			$completeCache = $result;
			$needed = array();
			if(!empty($result) && is_array($result)){
				$payloads= $params['payload'];
				foreach ( $result as $resultkey => $resultvalue ) {
					$plARR = array();
					foreach($payloads as $plk => $plv){
						if($plk == "Resources"){
							$plARR[$plk] = md5(implode(",",$plv));
						}else{
							$plARR[$plk] = $plv;
						}
					}
					ksort($plARR);
					$thisRequestStr = 'cache_search_'.implode('_',$plARR);
					$cachedRequestStr = $resultvalue->URL;
					if($thisRequestStr === $cachedRequestStr ){
						$needed = array();
						break;
					}else{
						$needed[] = $thisRequestStr;
					}
				}
			}
			if ( is_array( $result ) && !empty( $result ) && empty($needed) ) {
				//return cache
				$newpxml = array();
				$cachedASINs = array();
				foreach ( $completeCache as $resultkey => $resultvalue ) {
					$cachedRequestStr = $resultvalue->URL;
					$plARR = array();
					foreach($payloads as $plk => $plv){
						if($plk == "Resources"){
							$plARR[$plk] = md5(implode(",",$payloads['Resources']));
						}else{
							$plARR[$plk] = $plv;
						}
					}
					ksort($plARR);
					$thisRequestStr = 'cache_search_'.implode('_',$plARR);
					if ( $cachedRequestStr === $thisRequestStr ){
						$cachedASINs[ $resultvalue->URL ] =  $resultvalue;
						break;
					}
				}
				if(is_array($cachedASINs)){
					foreach ( $cachedASINs as $resultkey => $resultvalue ) {
						$pxml = appip_get_JSON_structure( $resultvalue->body, $resultvalue->Age, $resultvalue->URL );
						$newpxml[] = $pxml;
					}
					return $newpxml[0];
				}else{
					return false;
				}
			} else {
				//get new request
				$payloads = array();
				$getArr = array( 'region' => $region, 'asins' => array(), 'params' => $params, 'privatekey' => $privatekey,'publickey' => $publickey, 'reqtype' => 'search', );
				$getArr['AWSAccessKeyId'] = $publickey;
				$getArr['AWSSecreyKey'] = $privatekey;
				$getArr['AWSPartnerID'] = $getArr['params']['AssociateTag'];
				$getArr['AWSRegion'] = $region;
				$getArr['ItemId'] = array();
				$getArr['AWSRequestType'] = 'SearchItems';
				unset($getArr['params'],$getArr['privatekey'],$getArr['region']);
				$payload = $params['payload'];
				if(!empty($payload) )
					$getArr['payloads'][] = $payload;
				ksort($getArr);
				$tempRequest = get_appip_signature_requests( $getArr );
				$request = $tempRequest[ 'requests' ];
				$requestedASINs = array();
				$cache_keys = $tempRequest[ 'cache_keys' ];

				/** DO THE REQUEST **/
				if ( is_array( $request ) && !empty( $request ) ) {
					$newREQ = amazon_product_do_API_request( $request, $tempRequest['cache_keys'], $requestedASINs, $getArr  );
					if ( is_array( $newREQ ) && !empty( $newREQ ) ) {
						foreach ( $newREQ as $rk => $rv ) {
							$amazonCache->addto_amazon_plugin_cache( 'search', ( object )array( 'body' => $rv[ 'body' ], 'Age' => $rv[ 'Age' ], 'URL' => $rk ) );
							$completeCache[] = (object) array('body' => stripslashes($rv[ 'body' ]), 'Age' => $rv[ 'Age' ],'URL' => $rk );
						}
					}
				}
				/** END DO THE REQUEST **/
				if ( is_array( $newREQ ) && !empty( $newREQ ) ) {
					foreach ( $newREQ as $cCkey => $cCvalue ) {
						$cCvalue = (object) $cCvalue;
						$pxml = appip_get_JSON_structure( stripslashes($cCvalue->body), $cCvalue->Age, stripslashes($cCkey) );
					}
				}
				return $pxml;
			}
			return false;
		} else {
			// Get Items Request
			$newpxml = array();
			if ( !$skip_cache ) {
				// get the cached items
				$completeCache = $amazonCache->get_amazon_plugin_cache( 'product', $params );
				$cachedASINs = array();
				$payloads= $params['payload'];
				if ( ( is_array( $completeCache ) || is_object( $completeCache ) )&& !empty( $completeCache ) ) {
					foreach ( $completeCache as $resultkey => $resultvalue ) {
						$tempURL = explode( ":", $resultvalue->URL );
						$cachedRequestStr = isset($tempURL[1]) ? $tempURL[1] :'';
						$thisRequestStr = '_'.$payloads['Marketplace'].'_'.$payloads['PartnerTag'].'_'.$payloads['PartnerType'].'_'.md5(implode(",",$payloads['Resources']));
						if ( !empty( $tempURL[ 0 ] ) && $cachedRequestStr === $thisRequestStr ){
							$tempASINs = explode( ',', str_replace('cache_','',$tempURL[ 0 ]) );
							$cachedASINs = array_unique( array_merge( $cachedASINs, $tempASINs ) );
						}
					}
				}
				$mightNeed = is_array( $allASINs[ 'needed' ] ) && !empty( $allASINs[ 'needed' ] ) ? $allASINs[ 'needed' ] : array();
				$doNeed = array_diff( $mightNeed, $cachedASINs );
			}
			if ( empty( $doNeed ) ) {
				//do nothing if we don't need to cache anything
				//and  silently return if this is just a cache request and not a product grab
				if ( $cacheOnly )
					return true;
			} else {
				//need to cache something so process needed items
				$allASINs[ 'needed' ] = $doNeed; 
				$allASINs[ 'toget' ] = array_chunk( $doNeed, 10 ); 
				$getArr = array( 'region' => $region, 'asins' => $allASINs[ 'toget' ], 'params' => $params, 'privatekey' => $privatekey, 'publickey' => $publickey );
				$payloads = array();
				// set up payload for Amazon request
				if(is_array($allASINs[ 'toget' ]) && !empty($allASINs[ 'toget' ])){
					$getArr['AWSAccessKeyId'] = $publickey;
					$getArr['AWSPartnerID'] = $getArr['params']['AssociateTag'];
					$getArr['ItemId'] = $getArr['params']['ItemId'];
					$getArr['AWSRequestType'] = 'GetItems';
					$getArr['AWSRegion'] = $region;
					$getArr['AWSSecreyKey'] = $privatekey;
					unset($getArr['params'],$getArr['privatekey'],$getArr['region']);
					$payload = $params['payload'];
					foreach($allASINs[ 'toget' ] as $bk => $bv){
						$tempPayload = $payload;
						$tempPayload['ItemIds'] = $bv;
						$payloads[] = $tempPayload;
					}
					if(!empty($payloads) )
						$getArr['payloads'] = $payloads;
				}
				ksort($getArr);
				// get needed requests array
				$tempRequest = get_appip_signature_requests( $getArr );
				$request = $tempRequest[ 'requests' ];
				$requestedASINs = $tempRequest[ 'asins' ];
				$cache_keys = $tempRequest[ 'cache_keys' ];
				/** DO THE REQUEST **/
				if ( is_array( $request ) && !empty( $request ) ) {
					$newREQ = amazon_product_do_API_request( $request, $tempRequest['cache_keys'], $requestedASINs, $getArr  );
					if ( is_array( $newREQ ) && !empty( $newREQ ) ) {
						foreach ( $newREQ as $rk => $rv ) {
							$amazonCache->addto_amazon_plugin_cache(  'product', ( object )array( 'body' => $rv[ 'body' ], 'Age' => $rv[ 'Age' ], 'URL' => $rk ) );
							$completeCache[] = (object) array( 'body' => stripslashes($rv[ 'body' ]), 'Age' => $rv[ 'Age' ],'URL' => $rk );
						}
					}
				}
				/** END DO THE REQUEST **/
			}
		}
	}
	/**************
	 * END Check if ASINs are in CACHE already
	 **************/

	/**************
	 * Return needed products
	 **************/
	// this part is the same for SearchItems or GetItmes
	if ( ( is_array( $completeCache ) || is_object( $completeCache ) ) && !empty( $completeCache ) ) {
		$pxmlerrors = array();
		$addlResponse = array();
		$newre = array();
		foreach ( $completeCache as $cCkey => $cCvalue ) {
			$DoContinue = false;
			if ( is_array( $allASINs[ 'requested' ] ) && !empty( $allASINs[ 'requested' ] ) ) {
				foreach ( $allASINs[ 'requested' ] as $k => $r ) {
					if ( $r != '' && strpos($cCvalue->URL, $r ) !== false ){
						$DoContinue = true;
					}
				}
			}
			if ( $DoContinue ) {
				$pxml = appip_get_JSON_structure( $cCvalue->body, $cCvalue->Age, $cCvalue->URL );
				$newpxml[ $cCkey ] = $pxml;
			} 
		}
	}
	$newPXTemp = array();
	$newPX = array();
	$newre = array();
	$copy_allASINs = $allASINs[ 'requested' ];
	if ( is_array( $newpxml ) && !empty( $newpxml ) ) {
		foreach ( $newpxml as $key => $val ) {
			$z=0;
			$errors = array();
			if ( !is_array( $val ) ) {
				//nothing
				$errorsArr = $val;
			}elseif(is_array( $val ) && isset($val['Errors']) && !isset($val['ItemsResult']["Items"]) && !isset($val['SearchResult']["Items"]) && !isset($val['VariationsResult']["Items"]) && !isset($val['BrowseNodesResult']['BrowseNodes'])){
				// only an error and no items
				$errorsArr = $val['Errors'];
				$newPXTemp[$key]['Errors'] = $errorsArr;
			} else {
				//grab the errors
				if(isset($val['Errors']) && !isset($val['ItemsResult']) && !isset($val['SearchResult']) && !isset($val['BrowseNodesResult']) && !isset($val['VariationsResult'])){
					$er2Arr[] = $val['Errors'];
				}elseif(isset($val['Errors']) && (isset($val['ItemsResult']["Items"]) || isset($val['SearchResult']["Items"]) || isset($val['VariationsResult']["Items"]))){
					foreach ( $val as $ikey => $ival ) {
						$newPXTemp[$ikey]['errors'] = $val['Errors'];
						$item = isset( $ival[ 'Items' ] ) && !empty( $ival[ 'Items' ] ) ? $ival[ 'Items' ] : array();
						if(!empty($item) && is_array($item)) {
							foreach($item as $fk => $fv){
								if(isset($fv['ASIN'])){
									$fv['cached'] = 'true';
									$newPXTemp[$ikey]['Items'][$fv['ASIN']] = $fv;
								}
							}
						}
						$z++;
					}
				}elseif(isset($val['ItemsResult']["Items"]) || isset($val['SearchResult']["Items"]) || isset($val['VariationsResult']["Items"])){
					foreach ( $val as $ikey => $ival ) {
						$item = isset( $ival[ 'Items' ] ) && !empty( $ival[ 'Items' ] ) ? $ival[ 'Items' ] : array();
						if(!empty($item) && is_array($item)) {
							foreach($item as $fk => $fv){
								if(isset($fv['ASIN'])){
									$fv['cached'] = 'true';
									$newPXTemp[$ikey]['Items'][$fv['ASIN']] = $fv;
								}
							}
						}
						$z++;
					}					
				}elseif(isset($val['BrowseNodesResult']['BrowseNodes'])){
					foreach ( $val as $ikey => $ival ) {
						$item = isset( $ival[ 'BrowseNodes' ] ) && !empty( $ival[ 'BrowseNodes' ] ) ? $ival[ 'BrowseNodes' ] : array();
						if(!empty($item) && is_array($item)) {
							foreach($item as $fk => $fv){
								$fv['cached'] = 'true';
								$newPXTemp[$ikey]['BrowseNodes'][] = $fv;
							}
						}
						$z++;
					}					
				}
			}
		}
	}
	if ( is_array( $copy_allASINs ) && !empty( $copy_allASINs ) ) {
		foreach ( $copy_allASINs as $caVAL ) {
			if(!empty($newPXTemp)){
				foreach($newPXTemp as $kj => $vj){
					if ( isset( $vj['Items'][ $caVAL ] ) ){
						$newPX[$kj]['Items'][] = $vj['Items'][ $caVAL ];
						//break;
					}
					if( isset( $vj['Errors'])){
						$newPX[$kj]['Errors'] = $vj['Errors'];
					}
				}
			}
		}
	}
	unset( $newpxml );
	return $newPX;
	/**************
	 * END Return needed products
	 **************/
}

function get_appip_signature_requests( $getArr ) {
	if ( !is_array( $getArr ) || empty( $getArr ) )
		return array( 'requests', 'asins' );
	$payloads = $getArr['payloads'];
	$AWSRequestType = $getArr['AWSRequestType'];
	if ( $AWSRequestType == 'GetItems' ) {
		$asins = $getArr['asins'];
		$requestedASINs = array();
		$request = array();
		$cache_key = array();
		//$asins = $getArr['asins'];
		if ( is_array( $payloads ) && !empty( $payloads) ) {
			foreach ( $payloads as $plkey => $payload ) {
				if(is_array( $payload ) && !empty( $payload )){
					$canqueryKey = array();
					foreach($payload as $pk => $pv){
						if ( $pk == 'Resources' )
							$canqueryKey[ $pk ] =  md5(implode(",",$pv));
						elseif ( $pk == 'ItemIds' && !empty($pv))
							$canqueryKey[ $pk ] = implode(",",str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$pv)).':';
						else
							$canqueryKey[ $pk ] = $pv;
					}
					ksort( $canqueryKey );
					$payload['AWSAccessKeyId'] = $getArr['AWSAccessKeyId'];
					$payload['AWSSecreyKey'] = $getArr['AWSSecreyKey'];
					$payload['AWSRegion'] = $getArr['AWSRegion'];
					$payload['AWSRequestType'] = $getArr['AWSRequestType'];
					$request[] = $payload;
					$cache_key[] = 'cache_' . implode( "_", $canqueryKey );
					if( is_array($payload['ItemIds']) && !empty( $payload['ItemIds'] ) ){
						foreach( $payload['ItemIds'] as $ak => $av ){
							$av = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$av);
							$TAs[] = $av;
						}
					}
					$requestedASINs[] = implode( ',', $TAs);
				}
			}
		}
		return array( 'requests' => $request, 'asins' => $requestedASINs, 'cache_keys' => $cache_key );
	}elseif($AWSRequestType == 'GetBrowseNodes') {
		//Browse Nodes
		return array( 'requests' => array(), 'asins' => array(), 'cache_keys' => array() );
	}elseif($AWSRequestType == 'GetVariations') {
		//Get Variations
		return array( 'requests' => array(), 'asins' => array(), 'cache_keys' => array() );
	}elseif($AWSRequestType == 'SearchItems'){
		//search
		$request = array();
		$cache_key = array();
		if ( is_array( $payloads ) && !empty( $payloads) ) {
			foreach ( $payloads as $plkey => $payload ) {
				if(is_array( $payload ) && !empty( $payload )){
					$canqueryKey = array();
					foreach($payload as $pk => $pv){
						if ( $pk == 'Resources' )
							$canqueryKey[ $pk ] =  md5(implode(",",$pv));
						else
							$canqueryKey[ $pk ] = $pv;
					}
					ksort( $canqueryKey );
					$payload['AWSAccessKeyId'] = $getArr['AWSAccessKeyId'];
					$payload['AWSSecreyKey'] = $getArr['AWSSecreyKey'];
					$payload['AWSRegion'] = $getArr['AWSRegion'];
					$payload['AWSRequestType'] = $getArr['AWSRequestType'];
					$request[] = $payload;
					$cache_key[] = 'cache_search_' . implode( "_", $canqueryKey );
				}
			}
		}
		return array( 'requests' => $request, 'asins' => array(), 'cache_keys' => $cache_key );
	}
}
/*
$usefgc Deprecated 3.7.1
$usecurl Deprecated 3.7.1
*/
function amazon_product_do_API_request( $request = array(), $keystr = array(), $requestedASINs = array(),$getarr = array() ) {
	$newpxml = array();
	//unset($request);
	if ( !empty( $request ) ) {
		global $wpdb, $amz_wpdb;
		$start = 0;
		foreach ( $request as $rkey => $sReqA ) {
			// New Transport (wp_remote request);
			/* NEW */
			if( $start > 1 )
				usleep( 1000000 * 2 ); // had to up this to stop throttling with new Amazon Limits
			$Regions = __getAmz_regions();
			$region = $Regions[ $sReqA[ 'AWSRegion' ] ][ 'RegionCode' ];
			$host = $Regions[ $sReqA[ 'AWSRegion' ] ][ 'Host' ];
			$accessKey = $sReqA[ 'AWSAccessKeyId' ];
			$secretKey = $sReqA[ 'AWSSecreyKey' ];
			$reqType = strtolower($sReqA['AWSRequestType']);
			unset($sReqA[ 'AWSAccessKeyId' ],$sReqA[ 'AWSSecreyKey' ],$sReqA[ 'AWSRegion' ],$sReqA['AWSRequestType']);
			$payloadArr = $sReqA;
			if(isset($payloadArr['ItemIds']) && is_array($payloadArr['ItemIds']) && !empty($payloadArr['ItemIds'])){
				$tkASINs = implode(",",$payloadArr['ItemIds']);
				$tkASINs = str_replace(array("<br/>","<br>","\r","\n","\r\n","\t",", ","  "," ",",,"),array('','','','','','',',','','',','),$tkASINs);
				$payloadArr['ItemIds'] = explode(",",$tkASINs);
			}
			$payload = json_encode( $payloadArr );
			$awsv5 = new Amazon_Product_Request_V5( $accessKey, $secretKey, $region, $host, $reqType );
			$awsv5->setPayload( $payload );
			$response = $awsv5->do_request();
			/* END NEW */

			if (!is_wp_error($response)) {
				$xbody = str_replace( "'","\\'", $wpdb->esc_like(  $response[ 'body' ] ));
				if ( $xbody == '' ) {
					return array( 'Error' => array( 'Age' => 0, 'body' => 'Error: Empty Result.<br/>Something when wrong with the request. If you continue to have this problem, check your API keys for accuracy. If you still have the issue, send your Debug key and site URL to '.APIAP_HELP_EMAIL.' for help.' ) );
				} else {
					$keystrTemp = $wpdb->_escape( $keystr[$start]);
					$newpxml[ $keystrTemp ] = array( 'body' => $xbody, 'Age' => 0 );
					$updatesql = "INSERT IGNORE INTO {$wpdb->prefix}amazoncache (`URL`, `body`, `updated`) VALUES ('{$keystrTemp}', '{$xbody}', NOW()) ON DUPLICATE KEY UPDATE `body`='{$xbody}', `updated`=NOW();";
					$amz_wpdb->addDBSave( $updatesql );
				}
			}else{
				$status = 'unknown error';
				if (isset($response->status))
					$status = $response->status;
				return array( 'Error' => array( 'Age' => 0, 'body' => $status ) );
			}
			$start++;
		}
	}
	return $newpxml;
}

class Amazon_Product_Cache_Oct_One {
	var $amazon_cache = array();
	var $amazon_search_cache = array();

	function __construct() {
		// nothing
	}
	public function addto_amazon_plugin_cache( $type = '', $obj = object ) {
		if ( strtolower( $type ) == 'search' ) {
			$this->amazon_search_cache[] = ( object )$obj;
		} else {
			$this->amazon_cache[] = ( object )$obj;
		}
		return;
	}
	public function get_amazon_plugin_cache( $type = '', $params = array() ) {
		if ( strtolower( $type ) == 'search' && is_array( $this->amazon_search_cache ) && !empty( $this->amazon_search_cache ) ) {
			return $this->amazon_search_cache;
		} elseif ( strtolower( $type ) != 'search' && is_array( $this->amazon_cache ) && !empty( $this->amazon_cache ) ) {
			return $this->amazon_cache;
		}
		global $wpdb;
		$newpxml = array();
		$cachelen = ( int )apply_filters( 'amazon_product_post_cache', get_option( 'apipp_amazon_cache_sec', 3600 ) ) ;/// 60;
		$cachetime = ( int )$cachelen;
		if ( $type == 'search' ) {
			$checksql = "SELECT `body`, ( NOW() - `updated` ) as Age, `URL` FROM {$wpdb->prefix}amazoncache WHERE `URL` like 'cache_search%' AND NOT( `body` LIKE '%AccessDenied%') AND NOT( `body` LIKE '%AccessDeniedAwsUsers%') AND NOT( `body` LIKE '%InvalidAssociate%') AND NOT( `body` LIKE '%IncompleteSignature%') AND NOT( `body` LIKE '%InvalidSignature%') AND NOT( `body` LIKE '%InvalidPartnerTag%') AND NOT( `body` LIKE '%TooManyRequests%') AND NOT( `body` LIKE '%RequestExpired%') AND NOT( `body` LIKE '%InvalidParameterValue%') AND NOT( `body` LIKE '%MissingParameter%') AND NOT( `body` LIKE '%UnknownOperation%') AND NOT( `body` LIKE '%UnrecognizedClient%') AND (NOW() - `updated`) <= '{$cachetime}';";
			$result = $wpdb->get_results( $checksql );
			$this->amazon_search_cache = $result; //amazon_make_cache_array($result, $params);
			return $this->amazon_search_cache;
		} else {
			$userauth = apply_filters( 'amazon_product_skip_cache_auth', 'spade' );
			$skip_cache = isset( $_GET[ 'purge-cache' ] ) && isset( $_GET[ 'auth' ] ) && ( int )$_GET[ 'purge-cache' ] == 1 && esc_attr( $_GET[ 'auth' ] ) == $userauth ? true : false;
			if ( !$skip_cache ) {
				$checksql = "SELECT `body`, ( NOW() - `updated` ) as Age, `URL` FROM {$wpdb->prefix}amazoncache WHERE NOT( `body` LIKE '%AccountLimitExceeded%') AND `body` != '' AND NOT( `body` LIKE '%SignatureDoesNotMatch%') AND (NOW() - `updated`) <= '{$cachetime}';";
				$result = $wpdb->get_results( $checksql );
				$this->amazon_cache = $result; //amazon_make_cache_array($result, $params);
			}
			return $this->amazon_cache;
		}
	}
}

$amazonCache = new Amazon_Product_Cache_Oct_One();
