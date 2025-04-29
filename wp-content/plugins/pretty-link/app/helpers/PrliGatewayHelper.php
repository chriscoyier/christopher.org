<?php
class PrliGatewayHelper
{
  public static function pretty_permalinks_using_index() {
      $permalink_structure = get_option('permalink_structure');
      return preg_match('!^/index.php!',$permalink_structure);
    }

  /** This returns the structure for all of the gateway notify urls.
  * It can even account for folks unlucky enough to have to prepend
  * their URLs with '/index.php'.
  * NOTE: This function is only applicable if pretty permalinks are enabled.
  */
  public static function gateway_notify_url_structure() {
    $pre_slug_index = '';
    if(self::pretty_permalinks_using_index()) {
      $pre_slug_index = '/index.php';
    }

    return apply_filters(
      'prli_gateway_notify_url_structure',
      "{$pre_slug_index}/prettylinks/notify/%gatewayid%/%action%"
    );
  }

  /** This modifies the gateway notify url structure to be matched against a uri.
  * By default it will generate this: /prettylinks/notify/([^/\?]+)/([^/\?]+)/?
  * However, this could change depending on what gateway_notify_url_structure returns
  */
  public static function gateway_notify_url_regex_pattern() {
    return preg_replace('!(%gatewayid%|%action%)!', '([^/\?]+)', self::gateway_notify_url_structure()) . '/?';
  }

  public static function match_uri($pattern,$uri,&$matches,$include_query_string=false) {
    if($include_query_string) {
      $uri = urldecode($uri);
    }
    else {
      // Remove query string and decode
      $uri = preg_replace('#(\?.*)?$#','',urldecode($uri));
    }

    // Resolve WP installs in sub-directories
    preg_match('!^https?://[^/]*?(/.*)$!', site_url(), $m);

    $subdir = ( isset($m[1]) ? $m[1] : '' );
    $regex = '!^'.$subdir.$pattern.'$!';
    return preg_match($regex, $uri, $matches);
  }
}
