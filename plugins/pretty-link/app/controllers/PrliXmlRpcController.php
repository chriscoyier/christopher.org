<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

/**
 * Pretty Link WordPress Plugin API export via XML-RPC
 *
 * The first 2 arguments to each of these methods are username and password.
 */
require_once(ABSPATH . '/wp-includes/class-IXR.php');

class PrliXmlRpcController extends PrliBaseController {
  public function load_hooks() {
    add_filter('xmlrpc_methods', array($this, 'export_api'));
  }

  /********* EXPORT PRETTY LINK API VIA XML-RPC ***********/
  public function export_api($api_methods) {
    $api_methods['prli.create_pretty_link']  = array($this,'create_pretty_link');
    $api_methods['prli.get_all_groups']      = array($this,'get_all_groups');
    $api_methods['prli.get_all_links']       = array($this,'get_all_links');
    $api_methods['prli.get_link']            = array($this,'get_link');
    $api_methods['prli.get_link_from_slug']  = array($this,'get_link_from_slug');
    $api_methods['prli.get_pretty_link_url'] = array($this,'get_pretty_link_url');
    $api_methods['prli.api_version']         = array($this,'api_version');

    return $api_methods;
  }

  /**
   * Returns the API Version as a string.
   */
  public function api_version($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    return prli_api_version();
  }

  /**
   * Get a Pretty Link for a long, ugly URL.
   *
   * @param string $username Required, an admin user of this blog
   *
   * @param string $password Required, the password for this user
   *
   * @param string $target_url Required, it is the value of the Target URL you
   *                           want the Pretty Link to redirect to
   *
   * @param string $slug Optional, slug for the Pretty Link (string that comes
   *                     after the Pretty Link's slash) if this value isn't set
   *                     then a random slug will be automatically generated.
   *
   * @param string $name Optional, name for the Pretty Link. If this value isn't
   *                     set then the name will be the slug.
   *
   * @param string $description Optional, description for the Pretty Link.
   *
   * @param integer $group_id Optional, the group that this link will be placed in.
   *                          If this value isn't set then the link will not be
   *                          placed in a group.
   *
   * @param boolean $link_track_me Optional, If true the link will be tracked,
   *                               if not set the default value (from the pretty
   *                               link option page) will be used
   *
   * @param boolean $link_nofollow Optional, If true the nofollow attribute will
   *                               be set for the link, if not set the default
   *                               value (from the pretty link option page) will
   *                               be used
   *
   * @param boolean $link_sponsored Optional, If true the sponsored attribute will
   *                               be set for the link, if not set the default
   *                               value (from the pretty link option page) will
   *                               be used
   *
   * @param string $link_redirect_type Optional, valid values include '307', '302', or '301',
   *                                   if not set the default value (from the pretty
   *                                   link option page) will be used
   *
   * @return boolean / string The Full Pretty Link if Successful and false for Failure.
   *                          This function will also set a global variable named
   *                          $prli_pretty_slug which gives the slug of the link
   *                          created if the link is successfully created -- it will
   *                          set a variable named $prli_error_messages if the link
   *                          was not successfully created.
   */
  public function create_pretty_link( $args ) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    // Target URL Required
    if(!isset($args[2]))
      return new IXR_Error( 401, __( 'You must provide a target URL' , 'pretty-link') );

    $target_url = $args[2];

    $slug             = (isset($args[3])?$args[3]:'');
    $name             = (isset($args[4])?$args[4]:'');
    $description      = (isset($args[5])?$args[5]:'');
    $group_id         = (isset($args[6])?$args[6]:'');
    $track_me         = (isset($args[7])?$args[7]:'');
    $nofollow         = (isset($args[8])?$args[8]:'');
    $sponsored        = (isset($args[9])?$args[9]:'');
    $redirect_type    = (isset($args[10])?$args[10]:'');
    $param_forwarding = (isset($args[11]) && !empty($args[11]) && $args[11] != 'off');
    $param_struct     = (isset($args[12])?$args[12]:'');

    if( $link = prli_create_pretty_link( $target_url,
                                         $slug,
                                         $name,
                                         $description,
                                         $group_id,
                                         $track_me,
                                         $nofollow,
                                         $sponsored,
                                         $redirect_type,
                                         $param_forwarding,
                                         $param_struct ) )
      return $link;
    else
      return new IXR_Error( 401, __( 'There was an error creating your Pretty Link' , 'pretty-link') );
  }

  public function update_pretty_link( $args ) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    // Target URL Required
    if(!isset($args[2]))
      return new IXR_Error( 401, __( 'You must provide the id of the link you want to update' , 'pretty-link') );

    $id               = $args[2];
    $target_url       = (isset($args[3])?$args[3]:'');
    $slug             = (isset($args[4])?$args[4]:'');
    $name             = (isset($args[5])?$args[5]:'');
    $description      = (isset($args[6])?$args[6]:'');
    $group_id         = (isset($args[7])?$args[7]:'');
    $track_me         = (isset($args[8])?$args[8]:'');
    $nofollow         = (isset($args[9])?$args[9]:'');
    $sponsored        = (isset($args[10])?$args[10]:'');
    $redirect_type    = (isset($args[11])?$args[11]:'');
    $param_forwarding = (isset($args[12])?$args[12]:'');
    $param_struct     = (isset($args[13])?$args[13]:'');

    if( $link = prli_update_pretty_link( $id,
                                         $target_url,
                                         $slug,
                                         $name,
                                         $description,
                                         $group_id,
                                         $track_me,
                                         $nofollow,
                                         $sponsored,
                                         $redirect_type,
                                         $param_forwarding,
                                         $param_struct ) )
      return $link;
    else
      return new IXR_Error( 401, __( 'There was an error creating your Pretty Link' , 'pretty-link') );
  }

  /**
   * Get all the pretty link groups in an array suitable for creating a select box.
   *
   * @return bool (false if failure) | array A numerical array of associative arrays
   *                                         containing all the data about the pretty
   *                                         link groups.
   */
  public function get_all_groups($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    if( $groups = prli_get_all_groups())
      return $groups;
    else
      return new IXR_Error( 401, __( 'There was an error fetching the Pretty Link Groups' , 'pretty-link') );
  }

  /**
   * Get all the pretty links in an array suitable for creating a select box.
   *
   * @return bool (false if failure) | array A numerical array of associative arrays
   *                                         containing all the data about the pretty
   *                                         links.
   */
  public function get_all_links($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    if( $links = prli_get_all_links())
      return $links;
    else
      return new IXR_Error( 401, __( 'There was an error fetching the Pretty Links' , 'pretty-link') );
  }

  /**
   * Gets a specific link from a slug and returns info about it in an array
   *
   * @return bool (false if failure) | array An associative array with all the
   *                                         data about the given pretty link.
   */
  public function get_link_from_slug($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    if(!isset($args[2]))
      return new IXR_Error( 401, __( 'Sorry, you must provide a slug to lookup' , 'pretty-link') );

    $slug = $args[2];

    if( $link = prli_get_link_from_slug($slug) )
      return $link;
    else
      return new IXR_Error( 401, __( 'There was an error fetching your Pretty Link' , 'pretty-link') );
  }

  /**
   * Gets a specific link from an id and returns info about it in an array
   *
   * @return bool (false if failure) | array An associative array with all the
   *                                         data about the given pretty link.
   */
  public function get_link($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    if(!isset($args[2]))
      return new IXR_Error( 401, __( 'Sorry, you must provide an id to lookup' , 'pretty-link') );

    $id = $args[2];

    if( $link = prli_get_link($id) )
      return $link;
    else
      return new IXR_Error( 401, __( 'There was an error fetching your Pretty Link' , 'pretty-link') );
  }

  /**
   * Gets the full Pretty Link URL from a link id
   *
   * @return bool (false if failure) | string containing the pretty link url
   */
  public function get_pretty_link_url($args) {
    $username = $args[0];
    $password = $args[1];

    if ( !get_option( 'enable_xmlrpc' ) )
      return new IXR_Error( 401, __( 'Sorry, XML-RPC Not enabled for this website' , 'pretty-link') );

    if (!user_pass_ok($username, $password))
      return new IXR_Error( 401, __( 'Sorry, Login failed' , 'pretty-link') );

    // make sure user is an admin
    $userdata = get_userdatabylogin( $username );
    if( !isset($userdata->user_level) or
        (int)$userdata->user_level < 8 )
      return new IXR_Error( 401, __( 'Sorry, you must be an administrator to access this resource' , 'pretty-link') );

    if(!isset($args[2]))
      return new IXR_Error( 401, __( 'Sorry, you must provide an id to lookup' , 'pretty-link') );

    $id = $args[2];

    if( $url = prli_get_pretty_link_url($id) )
      return $url;
    else
      return new IXR_Error( 401, __( 'There was an error fetching your Pretty Link URL' , 'pretty-link') );
  }
}

