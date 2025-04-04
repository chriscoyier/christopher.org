<?php
class Amazon_Product_Shortcode{
	public $shortcode;
	
	public function __construct( $scode = '' ){
		$this->shortcode = $scode;
		$this->_setup();
		add_action( 'init', array( $this, 'admin_init' ) );
		add_filter( 'the_content', array($this, 'case_insensitive_shortcode'), 1 );	
		add_filter( 'the_excerpt', array($this, 'case_insensitive_shortcode'), 1 );	
		add_filter( 'widget_text', array($this, 'case_insensitive_shortcode'), 1 );	
	}
	
	public function admin_init(){
		$shortcodes = $this->shortcode;
		if( is_array( $shortcodes ) && !empty( $shortcodes ) ){
			foreach( $shortcodes as $shortcode ){
				add_shortcode( $shortcode, array( $this, 'do_shortcode' ) );
			}
		}elseif( $shortcodes != '' ){
			add_shortcode( $shortcodes, array( $this, 'do_shortcode' ) );
		}
		if( !is_admin() )
			add_filter( 'widget_text', 'do_shortcode', 10);

	}
	public function case_insensitive_shortcode( $content ){
		$shortcodes = $this->shortcode;
		if( is_array( $shortcodes ) && !empty( $shortcodes ) ){
			foreach( $shortcodes as $shortcode ){
				$from = '['. $shortcode ; 
				$to   = $from;
				if( stristr( $content, $from ) )    
					$content = str_ireplace( $from, $to, $content );
			}
		}elseif( $shortcodes != '' ){
			$from = '['. $shortcodes; 
			$to   = $from;
			if( stristr( $content, $from ) )    
				$content = str_ireplace( $from, $to, $content );
		}
		return $content;
	}

	static function do_shortcode( $atts, $content = '' ){
		die( 'function Amazon_Product_Shortcode::do_shortcde() must be over-ridden in a sub-class.' );
	}
	
	static function _setup( ){
		die( 'function Amazon_Product_Shortcode::_setup() must be over-ridden in a sub-class.' );
	}
	
	static function appip_do_charlen( $text ='', $charlen = 0 ){
		if( $text == '' || $charlen == 0 )
			return $text;
		return SELF::amazon_appip_truncate( $text, $charlen, array('exact' => true, 'html' => false) );
	}
	
	static function appip_has_shortcode( $content, $tag ) {
		if ( false === strpos( $content, '[' ) )
			return false;
			preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
			if ( empty( $matches ) )
					return false;
			foreach ( $matches as $shortcode ) {
			   if ( $tag === $shortcode[2] ) {
					return true;
				} elseif ( ! empty( $shortcode[5] ) && has_shortcode( $shortcode[5], $tag ) ) {
					return true;
				}
			}
		return false;
	}
	
	static function amazon_appip_truncate($text, $length = 150, $options = array()) {
		if($text == '' || (int) $length == 0)
		   return $text;
		$default = array('ending' => '...', 'exact' => true, 'html' => false );
		$options = array_merge($default, $options);
		extract($options);
		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
				return $text;
			$totalLength = mb_strlen(strip_tags($ending));
			$openTags = array();
			$truncate = '';
			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];
				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length + mb_strlen($ending)) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length /* - mb_strlen($ending)*/);
			}
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if (isset($spacepos)) {
				if ($html) {
					$bits = mb_substr($truncate, $spacepos);
					preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
					if (!empty($droppedTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					}
				}
				$truncate = mb_substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;
		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</'.$tag.'>';
			}
		}
	
		return $truncate;
	}
}