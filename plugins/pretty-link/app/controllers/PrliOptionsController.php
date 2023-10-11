<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliOptionsController extends PrliBaseController {
  public $opt_fields;

  public function __construct() {
    $this->opt_fields = array(
      'prli_exclude_ips' => 'prli_exclude_ips',
      'whitelist_ips' => 'prli_whitelist_ips',
      'filter_robots' => 'prli_filter_robots',
      'extended_tracking' => 'prli_extended_tracking',
      'link_track_me' => 'prli_link_track_me',
      'link_prefix' => 'prli_link_prefix',
      'auto_trim_clicks' => 'prli_auto_trim_clicks',
      'link_nofollow' => 'prli_link_nofollow',
      'link_sponsored' => 'prli_link_sponsored',
      'link_redirect_type' => 'prli_link_redirect_type',
      'hidden_field_name' => 'prli_update_options'
    );
  }

  public function load_hooks() {
    // nothing yet
  }

  public function route() {
    global $prli_options, $prli_utils, $plp_update;

    extract( $this->opt_fields );
    $errors = array();

    $update_message = false;

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_REQUEST[ $hidden_field_name ]) && $_REQUEST[ $hidden_field_name ] == 'Y' ) {
      $update_message = $this->update();
    }

    $update_message = apply_filters('prli_options_update_message', $update_message);

    require_once(PRLI_VIEWS_PATH.'/options/form.php');
  }

  private function update() {
    global $prli_options;

    $update_message = '';

    $errors = $this->validate(array(),$_POST);
    $this->update_attrs($_POST);

    if( empty($errors) ) {
      // Save the posted value in the database
      //update_option( 'prli_options', $prli_options );
      $prli_options->store();

      do_action('prli-store-options');

      // Put an options updated message on the screen
      $update_message = __('Options saved.', 'pretty-link');
    }
    else {
      require(PRLI_VIEWS_PATH.'/shared/errors.php');
    }

    return $update_message;
  }

  private function validate($errors, $params) {
    extract( $this->opt_fields );

    // Validate This
    if( !empty($params[ $prli_exclude_ips ]) && !preg_match( "#^[ \t]*((\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)|([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*))([ \t]*,[ \t]*((\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)|([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*)))*$#", $params[ $prli_exclude_ips ] ) ) {
      $errors[] = __('Excluded IP Addresses must be a comma separated list of IPv4 or IPv6 addresses or ranges.', 'pretty-link');
    }

    if( !empty($params[ $whitelist_ips ]) && !preg_match( "#^[ \t]*((\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)|([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*))([ \t]*,[ \t]*((\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)\.(\d{1,3}|\*)|([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*):([0-9a-fA-F]{1,4}|\*)))*$#", $params[ $whitelist_ips ] ) ) {
      $errors[] = __('Whitelist IP Addresses must be a comma separated list of IPv4 or IPv6 addresses or ranges.', 'pretty-link');
    }

    return apply_filters( 'prli-validate-options', $errors, $params );
  }

  private function update_attrs($params) {
    global $prli_options;

    extract( $this->opt_fields );

    // Read their posted value
    $prli_options->prli_exclude_ips = isset($params[ $prli_exclude_ips ]) && is_string($params[ $prli_exclude_ips ]) ? sanitize_text_field(stripslashes($params[ $prli_exclude_ips ])) : '';
    $prli_options->whitelist_ips = isset($params[ $whitelist_ips ]) && is_string($params[ $whitelist_ips ]) ? sanitize_text_field(stripslashes($params[ $whitelist_ips ])) : '';
    $prli_options->filter_robots = (int)isset($params[ $filter_robots ]);
    $prli_options->extended_tracking = isset($params[ $extended_tracking ]) && is_string($params[ $extended_tracking ]) ? sanitize_key(stripslashes($params[ $extended_tracking ])) : 'normal';
    $prli_options->link_track_me = (int)isset($params[ $link_track_me ]);
    $prli_options->link_prefix = (int)isset($params[ $link_prefix ]);
    $prli_options->auto_trim_clicks = (int)isset($params[ $auto_trim_clicks ]);
    $prli_options->link_nofollow = (int)isset($params[ $link_nofollow ]);
    $prli_options->link_sponsored = (int)isset($params[ $link_sponsored ]);
    $prli_options->link_redirect_type = isset($params[ $link_redirect_type ]) && is_string($params[ $link_redirect_type ]) ? sanitize_key(stripslashes($params[ $link_redirect_type ])) : '307';

    do_action('prli-update-options', $params);
  }
}

