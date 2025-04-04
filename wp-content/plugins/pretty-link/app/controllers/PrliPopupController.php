<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliPopupController extends PrliBaseController {
  public $popups;
  public function __construct() {
    // This is an array of the currently defined popups ...
    // used to validate that the popup specified actually exists
    $this->popups = array(
      /*'rating' => array(
        'after_usage' => array(
          'links' => 1,
          'clicks' => 5
        ),
        'user_popup' => true,
        'lite_only_popup' => false,
        'delay' => WEEK_IN_SECONDS,
        'delay_after_last_popup' => DAY_IN_SECONDS,
      ),
      'upgrade' => array(
        'after_usage' => array(
          'links' => 3,
          'clicks' => 5
        ),
        'user_popup' => true,
        'lite_only_popup' => true,
        'delay' => 2*WEEK_IN_SECONDS,
        'delay_after_last_popup' => DAY_IN_SECONDS,
      ),*/
    );
  }

  public function load_hooks() {
    // This is a hidden option to help support in case
    // there's a problem stopping or delaying a popup
    $dap = get_option('prli_disable_all_popups');
    if($dap) { return; }

    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('wp_ajax_prli_stop_popup', array($this, 'ajax_stop_or_delay_popup'));
    add_action('wp_ajax_prli_delay_popup', array($this, 'ajax_stop_or_delay_popup'));
    add_action('admin_notices', array($this,'display_popups'));
  }

  public function enqueue_admin_scripts($hook) {
    if($this->on_pretty_link_page()) {
      wp_register_style('prli-magnific-popup', PRLI_VENDOR_LIB_URL . '/magnific-popup/magnific-popup.min.css', array(), '1.1.0');
      wp_enqueue_style(
        'prli-admin-popup',
        PRLI_CSS_URL.'/admin_popup.css',
        array('prli-magnific-popup'),
        PRLI_VERSION
      );

      wp_register_script('prli-magnific-popup', PRLI_VENDOR_LIB_URL . '/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), '1.1.0', true);
      wp_enqueue_script(
        'prli-admin-popup',
        PRLI_JS_URL.'/admin_popup.js',
        array('jquery','prli-magnific-popup'),
        PRLI_VERSION
      );
      $loc = array(
        'security' => wp_create_nonce('prli-admin-popup'),
        'error' => __('An unknown error occurred.', 'pretty-link')
      );
      wp_localize_script('prli-admin-popup','PrliPopup', $loc);

      do_action('prli_admin_enqueue_popup_scripts', $hook);
    }
  }

  private function on_pretty_link_page() {
    global $current_screen;
    return (isset($current_screen->id) && strpos($current_screen->id,'pretty-link') !== false);
  }

  public function display_popups() {
    if(!$this->on_pretty_link_page()) { return; }

    // Bail on update/welcome page
    if (isset($_GET['page']) && ($_GET['page'] == 'pretty-link-welcome' || $_GET['page'] == 'pretty-link-updates')) { return;}

    // If this isn't a Pretty Link authorized user then bail
    if(!PrliUtils::is_authorized()) { return; }

    foreach($this->popups as $popup => $settings) {
      $this->maybe_show_popup($popup);
    }
  }

  public function ajax_stop_or_delay_popup() {
    PrliUtils::check_ajax_referer('prli-admin-popup','security');

    // If this isn't a Pretty Link authorized user then bail
    if(!PrliUtils::is_authorized()) {
      PrliUtils::exit_with_status(403,json_encode(array('error'=>__('Forbidden', 'pretty-link'))));
    }

    if(!isset($_POST['popup'])) {
      PrliUtils::exit_with_status(400,json_encode(array('error'=>__('Must specify a popup', 'pretty-link'))));
    }

    $popup = sanitize_text_field($_POST['popup']);

    if(!$this->is_valid_popup($popup)) {
      PrliUtils::exit_with_status(400,json_encode(array('error'=>__('Invalid popup', 'pretty-link'))));
    }

    if($_POST['action']=='prli_delay_popup') {
      $this->delay_popup($popup);
      $message = __('The popup was successfully delayed', 'pretty-link');
    }
    else {
      $this->stop_popup($popup); // TODO: Error handling
      $message = __('The popup was successfully stopped', 'pretty-link');
    }

    PrliUtils::exit_with_status(200,json_encode(compact('message')));
  }

  private function is_valid_popup($popup) {
    return in_array($popup,array_keys($this->popups));
  }

  private function stop_popup($popup) {
    // TODO: Should we add some error handling?
    if(!$this->is_valid_popup($popup)) { return; }

    if($this->popups[$popup]['user_popup']) {
      $user_id = PrliUtils::get_current_user_id();
      update_user_meta($user_id, $this->popup_stop_key($popup), 1);
    }
    else {
      update_option($this->popup_stop_key($popup), 1);
      wp_cache_delete('alloptions', 'options');
    }
  }

  private function delay_popup($popup) {
    // TODO: Should we add some error handling?
    if(!$this->is_valid_popup($popup)) { return; }
    set_transient(
      $this->popup_delay_key($popup,$this->popups[$popup]['user_popup']),
      1,
      $this->popups[$popup]['delay']
    );
  }

  private function is_popup_delayed($popup) {
    if(!$this->is_valid_popup($popup)) { return; }

    if($this->popups[$popup]['user_popup']) {
      // check if it's been delayed or stopped
      $user_id = PrliUtils::get_current_user_id();
      return get_transient($this->popup_delay_key($popup));
    }

    return get_transient($this->popup_delay_key($popup));
  }

  private function is_popup_stopped($popup) {
    if(!$this->is_valid_popup($popup)) { return; }

    if($this->popups[$popup]['user_popup']) {
      $user_id = PrliUtils::get_current_user_id();
      return get_user_meta($user_id, $this->popup_stop_key($popup), true);
    }

    return get_option($this->popup_stop_key($popup));
  }

  private function set_popup_last_viewed_timestamp($popup) {
    $timestamp = time();
    update_option('prli-popup-last-viewed', compact('popup','timestamp'));
    wp_cache_delete('alloptions', 'options');
  }

  private function get_popup_last_viewed_timestamp() {
    $default = array('popup'=>false,'timestamp'=>false);
    return get_option('prli-popup-last-viewed',$default);
  }

  private function maybe_show_popup($popup) {
    if($this->popup_visible($popup)) {
      $this->increment_popup_display_count($popup);
      $this->set_popup_last_viewed_timestamp($popup);
      require(PRLI_VIEWS_PATH."/admin/popups/{$popup}.php");
    }
  }

  private function popup_visible($popup) {
    // ensure popup only shows up in lite
    $prli_update = new PrliUpdateController();
    if( ( $this->popups[$popup]['lite_only_popup'] &&
          $prli_update->is_installed_and_activated()) ||
        !$this->is_valid_popup($popup)) {
      return false;
    }

    if(false !== $this->popups[$popup]['after_usage']) {
      $usage = $this->popups[$popup]['after_usage'];
      $click_count = PrliClick::get_count();
      $link_count = PrliLink::get_count();

      if( $click_count < $usage['clicks'] || $link_count < $usage['links'] ) {
        return false;
      }
    }

    // If we're not yet past the delay threshold for the last viewed popup then don't show it
    $last_viewed = $this->get_popup_last_viewed_timestamp();
    if( !empty($last_viewed) &&
        $last_viewed['popup']!=$popup &&
        ((int)$last_viewed['timestamp'] + (int)$this->popups[$popup]['delay_after_last_popup']) > time() ) {
      return false;
    }

    // This is for popups that should be displayed and resolved for each individual admin user
    $delayed = $this->is_popup_delayed($popup);

    // Popups displayed and resolved for any admin user in the system
    $stopped = $this->is_popup_stopped($popup);

    return (!$delayed && !$stopped);
  }

  private function increment_popup_display_count($popup) {
    $user_id = PrliUtils::get_current_user_id();
    $count = (int)get_user_meta($user_id, $this->popup_display_count_key($popup), true);
    update_user_meta($user_id, $this->popup_display_count_key($popup), ++$count);
  }

  private function popup_display_count_key($popup) {
    return "prli-{$popup}-popup-display-count";
  }

  private function popup_delay_key($popup) {
    if($this->is_valid_popup($popup) && $this->popups[$popup]['user_popup']) {
      $user_id = PrliUtils::get_current_user_id();
      return "prli-delay-{$popup}-popup-for-{$user_id}";
    }
    else {
      return "prli-delay-{$popup}-popup";
    }
  }

  private function popup_stop_key($popup) {
    return "prli-stop-{$popup}-popup";
  }
}

