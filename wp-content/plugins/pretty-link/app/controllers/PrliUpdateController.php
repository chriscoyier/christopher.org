<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class PrliUpdateController {
  public $mothership_license, $edge_updates, $mothership_license_str, $edge_updates_str, $pro_script, $plugin_slug;

  public function __construct() {
    $this->mothership_license_str = 'plp_mothership_license';
    $this->mothership_license     = get_option($this->mothership_license_str);
    $this->edge_updates_str       = 'plp_edge_updates';
    $this->edge_updates           = get_option($this->edge_updates_str);
    $this->plugin_slug            = PRLI_PLUGIN_SLUG;

    $this->pro_script = PRLI_PATH . '/pro/pretty-link-pro.php';
  }

  public function load_hooks() {
    if(!empty($this->mothership_license)) {
      add_filter('pre_set_site_transient_update_plugins', array($this, 'queue_update'));
      add_action('admin_init', array($this, 'maybe_activate'));
      add_action('wp_ajax_plp_edge_updates', array($this, 'plp_edge_updates'));
      add_filter('plugins_api', array($this, 'plugin_info'), 11, 3);
    }

    add_action('admin_notices', array($this, 'activation_warning'));
    add_action('admin_init', array($this, 'activate_from_define'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('wp_ajax_prli_activate_license', array($this, 'ajax_activate_license'));
    add_action('wp_ajax_prli_deactivate_license', array($this, 'ajax_deactivate_license'));
    add_action('wp_ajax_prli_install_license_edition', array($this, 'ajax_install_license_edition'));
    add_action('in_plugin_update_message-pretty-link/pretty-link.php', array($this, 'check_incorrect_edition'));
    add_action('prli_plugin_edition_changed', array($this, 'clear_update_transients'));
    //add_action('prli_display_options', array($this, 'queue_button'));
  }

  public function route() {
    $this->display_form();
  }

  public function set_edge_updates($updates) {
    update_option('plp_edge_updates', $updates);
    wp_cache_delete('alloptions', 'options');
    $this->edge_updates = $updates;
  }

  public function set_mothership_license($license) {
    update_option('plp_mothership_license', $license);
    wp_cache_delete('alloptions', 'options');
    $this->mothership_license = $license;
  }

  public function display_form($message='', $errors=array()) {
    // We just force the queue to update when this page is visited
    // that way we ensure the license info transient is set
    $this->manually_queue_update();

    if(!empty($this->mothership_license) && empty($errors)) {
      $li = get_site_transient( 'prli_license_info' );
    }

    require(PRLI_VIEWS_PATH.'/admin/update/ui.php');
  }

  public function ajax_activate_license() {
    if(!PrliUtils::is_post_request() || !isset($_POST['key']) || !is_string($_POST['key'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!PrliUtils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_activate_license', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $license_key = sanitize_text_field(wp_unslash($_POST['key']));

    if(empty($license_key)) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    try {
      $act = $this->activate_license($license_key);
      $li = get_site_transient('prli_license_info');
      $output = sprintf('<div class="notice notice-success inline"><p>%s</p></div>', esc_html($act['message']));

      if(is_array($li)) {
        $editions = PrliUtils::is_incorrect_edition_installed();

        if(is_array($editions) && $editions['license']['index'] > $editions['installed']['index']) {
          // The installed plugin is a lower edition, try to upgrade to the higher license edition
          if(!empty($li['url']) && PrliUtils::is_url($li['url'])) {
            $result = $this->install_plugin_silently($li['url'], array('overwrite_package' => true));

            if($result === true) {
              do_action('prli_plugin_edition_changed');
              wp_send_json_success(true);
            }
          }
        }

        ob_start();
        require PRLI_VIEWS_PATH . '/admin/update/active_license.php';
        $output .= ob_get_clean();
      }
      else {
        $output .= sprintf('<div class="notice notice-warning"><p>%s</p></div>', esc_html__('The license information is not available, try refreshing the page.', 'pretty-link'));
      }

      wp_send_json_success($output);
    }
    catch(Exception $e) {
      try {
        $expires = $this->send_mothership_request("/license_keys/expires_at/$license_key");

        if(isset($expires['expires_at'])) {
          $expires_at = strtotime($expires['expires_at']);

          if($expires_at && $expires_at < time()) {
            $licenses = $this->send_mothership_request("/license_keys/list_keys/$license_key");

            if(!empty($licenses) && is_array($licenses)) {
              $highest_edition_index = -1;
              $highest_license = null;

              foreach($licenses as $license) {
                $edition = PrliUtils::get_edition($license['product_slug']);

                if(is_array($edition) && $edition['index'] > $highest_edition_index) {
                  $highest_edition_index = $edition['index'];
                  $highest_license = $license;
                }
              }

              if(is_array($highest_license)) {
                wp_send_json_error(
                  sprintf(
                    /* translators: %1$s: the product name, %2$s: open link tag, %3$s: close link tag */
                    esc_html__('This License Key has expired, but you have an active license for %1$s, %2$sclick here%3$s to activate using this license instead.', 'pretty-link'),
                    '<strong>' . esc_html($highest_license['product_name']) . '</strong>',
                    sprintf('<a href="#" id="prli-activate-new-license" data-license-key="%s">', esc_attr($highest_license['license_key'])),
                    '</a>'
                  )
                );
              }
            }
          }
        }
      }
      catch(Exception $ignore) {
        // Nothing we can do, let it fail.
      }

      wp_send_json_error($e->getMessage());
    }
  }

  public function ajax_deactivate_license() {
    if(!PrliUtils::is_post_request()) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!PrliUtils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_deactivate_license', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $act = $this->deactivate_license();

    PrliAuthenticatorController::clear_connection_data();

    $output = sprintf('<div class="notice notice-success"><p>%s</p></div>', esc_html($act['message']));

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/update/inactive_license.php';
    $output .= ob_get_clean();

    wp_send_json_success($output);
  }

  public function install_plugin_silently($url, $args) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    $skin = new Automatic_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);

    if(!$skin->request_filesystem_credentials(false, WP_PLUGIN_DIR)) {
      return new WP_Error('no_filesystem_access', __('Failed to get filesystem access', 'pretty-link'));
    }

    return $upgrader->install($url, $args);
  }

  public function ajax_install_license_edition() {
    if(!PrliUtils::is_post_request()) {
      wp_send_json_error(__('Bad request', 'pretty-link'));
    }

    if(!current_user_can('update_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_install_license_edition', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $li = get_site_transient('prli_license_info');

    if(!empty($li) && is_array($li) && !empty($li['url']) && PrliUtils::is_url($li['url'])) {
      $result = $this->install_plugin_silently($li['url'], array('overwrite_package' => true));

      if($result instanceof WP_Error) {
        wp_send_json_error($result->get_error_message());
      }
      elseif($result === true) {
        do_action('prli_plugin_edition_changed');
        wp_send_json_success(__('The correct edition of Pretty Links has been installed successfully.', 'pretty-link'));
      }
      else {
        wp_send_json_error(__('Failed to install the correct edition of Pretty Links, please download it from prettylinks.com and install it manually.', 'pretty-link'));
      }
    }

    wp_send_json_error(__('License data not found', 'pretty-link'));
  }

  public function is_activated() {
    $activated = get_option('prli_activated');
    return (!empty($this->mothership_license) && !empty($activated));
  }

  public function was_activated_with_username_and_password() {
    $credentials = get_option('prlipro-credentials');
    $authorized  = get_option('prlipro_activated');

    return (($credentials && is_array($credentials)) &&
            (isset($credentials['username']) && !empty($credentials['username'])) &&
            (isset($credentials['password']) && !empty($credentials['password'])) &&
            ($authorized && $authorized=='true'));
  }

  public function is_installed() {
    return file_exists($this->pro_script);
  }

  public function is_installed_and_activated() {
    return ($this->is_installed() && $this->is_activated());
  }

  public function check_license_activation() {
    $aov = get_option('prli_activation_override');

    if(!empty($aov)) {
      update_option('prli_activated', true);
      do_action('prli_license_activated', array('aov' => 1));
      return;
    }

    if(empty($this->mothership_license)) {
      return;
    }

    // Only check the key once per day
    $option_key = "prli_license_check_{$this->mothership_license}";

    if(get_site_transient($option_key)) {
      return;
    }

    $check_count = get_option($option_key, 0) + 1;
    update_option($option_key, $check_count);

    set_site_transient($option_key, true, ($check_count > 3 ? 72 : 24) * HOUR_IN_SECONDS);

    $domain = urlencode(PrliUtils::site_domain());
    $args = compact('domain');

    try {
      $act = $this->send_mothership_request("/license_keys/check/{$this->mothership_license}", $args);

      if(!empty($act) && is_array($act)) {
        $license_expired = false;

        if(isset($act['expires_at'])) {
          $expires_at = strtotime($act['expires_at']);

          if($expires_at && $expires_at < time()) {
            $license_expired = true;
            update_option('prli_activated', false);
            do_action('prli_license_expired', $act);
          }
        }

        if(isset($act['status']) && !$license_expired) {
          if($act['status'] == 'enabled') {
            update_option($option_key, 0);
            update_option('prli_activated', true);
            do_action('prli_license_activated', $act);
          }
          elseif($act['status'] == 'disabled') {
            update_option('prli_activated', false);
            do_action('prli_license_invalidated', $act);
          }
        }
      }
    }
    catch(Exception $e) {
      if($e->getMessage() == 'Not Found') {
        update_option('prli_activated', false);
        do_action('prli_license_invalidated');
      }
    }
  }

  public function maybe_activate() {
    if($this->is_installed()) {
      $activated = get_option('prli_activated');

      if(!$activated) {
        $this->check_license_activation();
      }
    }
  }

  public function activate_from_define() {
    if(!$this->is_installed()) {
      return;
    }

    if(defined('PRETTYLINK_LICENSE_KEY') && $this->mothership_license != PRETTYLINK_LICENSE_KEY) {
      try {
        if(!empty($this->mothership_license)) {
          // Deactivate the old license key
          $this->deactivate_license();
        }

        // If we're using defines then we have to do this with defines too
        $this->set_edge_updates(false);

        $act = $this->activate_license(PRETTYLINK_LICENSE_KEY);

        $message = $act['message'];
        $callback = function() use ($message) { require(PRLI_VIEWS_PATH."/admin/errors.php"); };
      }
      catch(Exception $e) {
        $error = $e->getMessage();
        $callback = function() use ($error) { require(PRLI_VIEWS_PATH."/admin/update/activation_warning.php"); };
      }

      add_action( 'admin_notices', $callback );
    }
  }

  /**
   * Activate the license with the given key
   *
   * @param string $license_key The license key
   * @return array The license data
   * @throws Exception If there was an error activating the license
   */
  public function activate_license($license_key) {
    $args = array(
      'domain' => urlencode(PrliUtils::site_domain()),
      'product' => PRLI_EDITION,
    );

    $act = $this->send_mothership_request("/license_keys/activate/{$license_key}", $args, 'post');

    $this->set_mothership_license($license_key);
    update_option('prli_activated', true); //If we have made it here we are activated

    $option_key = "prli_license_check_{$license_key}";
    delete_site_transient($option_key);
    delete_option($option_key);

    delete_site_transient('prli_update_info');

    do_action('prli_license_activated_before_queue_update');

    $this->manually_queue_update();

    // Clear the cache of add-ons
    delete_site_transient('prli_addons');
    delete_site_transient('prli_all_addons');

    do_action('prli_license_activated', $act);

    return $act;
  }

  /**
   * Deactivate the license
   *
   * @return array
   */
  public function deactivate_license() {
    $license_key = $this->mothership_license;
    $act = array('message' => __('License key deactivated', 'pretty-link'));

    if(!empty($this->mothership_license)) {
      try {
        $args = array(
          'domain' => urlencode(PrliUtils::site_domain())
        );

        $act = $this->send_mothership_request("/license_keys/deactivate/{$this->mothership_license}", $args, 'post');
      }
      catch(Exception $e) {
        // Catching here to allow invalid license keys to be deactivated
      }
    }

    $this->set_mothership_license('');

    $option_key = "prli_license_check_{$license_key}";
    delete_site_transient($option_key);
    delete_option($option_key);

    delete_site_transient('prli_update_info');

    do_action('prli_license_deactivated_before_queue_update');

    $this->manually_queue_update();

    // Don't need to check the mothership for this one ... we just deactivated
    update_option('prli_activated', false);

    // Clear the cache of the license and add-ons
    delete_site_transient('prli_license_info');
    delete_site_transient('prli_addons');
    delete_site_transient('prli_all_addons');

    do_action('prli_license_deactivated', $act);

    return $act;
  }

  public function deactivate() {
    $act = $this->deactivate_license();
    $this->display_form($act['message']);
  }

  public function queue_update($transient, $force=false) {
    if(empty($transient) || !is_object($transient)) {
      return $transient;
    }

    if(!$this->is_installed() && !$this->is_activated()) { return $transient; }

    if($force || (false === ($update_info = get_site_transient('prli_update_info')))) {
      if(empty($this->mothership_license)) {
        // Just here to query for the current version
        $args = array();
        if( $this->edge_updates || ( defined( "PRETTYLINK_EDGE" ) && PRETTYLINK_EDGE ) ) {
          $args['edge'] = 'true';
        }

        $version_info = $this->send_mothership_request( "/versions/latest/pretty-link-pro-developer", $args );
        $curr_version = $version_info['version'];
        $download_url = '';
      }
      else {
        try {
          $domain = urlencode(PrliUtils::site_domain());
          $args = compact('domain');

          if( $this->edge_updates || ( defined( "PRETTYLINK_EDGE" ) && PRETTYLINK_EDGE ) ) {
            $args['edge'] = 'true';
          }

          $license_info = $this->send_mothership_request("/versions/info/{$this->mothership_license}", $args, 'post');
          $curr_version = $license_info['version'];
          $download_url = $license_info['url'];

          set_site_transient(
            'prli_license_info',
            $license_info,
            (24*HOUR_IN_SECONDS)
          );

          if(PrliUtils::is_incorrect_edition_installed()) {
            $download_url = '';
          }
        }
        catch(Exception $e) {
          try {
            // Just here to query for the current version
            $args = array();
            if( $this->edge_updates || ( defined( "PRETTYLINK_EDGE" ) && PRETTYLINK_EDGE ) ) {
              $args['edge'] = 'true';
            }

            $version_info = $this->send_mothership_request("/versions/latest/pretty-link-pro-developer", $args);
            $curr_version = $version_info['version'];
            $download_url = '';
          }
          catch(Exception $e) {
            if(isset($transient->response[PRLI_PLUGIN_SLUG])) {
              unset($transient->response[PRLI_PLUGIN_SLUG]);
            }

            $this->check_license_activation();
            return $transient;
          }
        }
      }

      set_site_transient(
        'prli_update_info',
        compact('curr_version', 'download_url'),
        (12*HOUR_IN_SECONDS)
      );
    }
    else {
      extract( $update_info );
    }

    if(($this->is_activated() && !$this->is_installed()) || (isset($curr_version) && version_compare($curr_version, PRLI_VERSION, '>'))) {
      $transient->response[PRLI_PLUGIN_SLUG] = (object)array(
        'id'          => $curr_version,
        'plugin'      => PRLI_PLUGIN_SLUG,
        'slug'        => 'pretty-link',
        'new_version' => $curr_version,
        'url'         => 'https://prettylinks.com/pl/update/url',
        'package'     => $download_url
      );
    }
    elseif(isset($transient->response[PRLI_PLUGIN_SLUG])) {
      unset($transient->response[PRLI_PLUGIN_SLUG]);
    }

    $this->check_license_activation();
    return $transient;
  }

  public function manually_queue_update() {
    $transient = get_site_transient('update_plugins');
    set_site_transient('update_plugins', $this->queue_update($transient, true));
  }

  public function queue_button() {
    ?>
    <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-options&action=queue&_wpnonce=' . wp_create_nonce('PrliUpdateController::manually_queue_update'))); ?>" class="button"><?php esc_html_e('Check for Update', 'pretty-link')?></a>
    <?php
  }

  // Return up-to-date addon info for pretty-link & its addons
  public function plugin_info($api, $action, $args) {
    global $wp_version;

    if(!isset($action) ||
       $action != 'plugin_information' ||
       (isset($args->slug) &&
        !preg_match("#^pretty-link-(basic|plus|pro)$#", $args->slug))) {
      return $api;
    }

    // Any addons should accept the pretty-link license for now
    if(!empty($this->mothership_license)) {
      try {
        $domain = urlencode(PrliUtils::site_domain());
        $params = compact('domain');

        if($this->edge_updates || (defined('PRETTYLINK_EDGE') && PRETTYLINK_EDGE)) {
          $params['edge'] = 'true';
        }

        $plugin_info = $this->send_mothership_request(
          "/versions/plugin_information/{$args->slug}/{$this->mothership_license}",
          $params,
          'get'
        );

        if(isset($plugin_info['requires'])) { $plugin_info['requires'] = $wp_version; }
        if(isset($plugin_info['tested']))   { $plugin_info['tested']   = $wp_version; }
        if(isset($plugin_info['compatibility'])) { $plugin_info['compatibility'] = array($wp_version => array($wp_version => array(100, 0, 0))); }

        return (object)$plugin_info;
      }
      catch(Exception $e) {
        // Fail silently for now
      }
    }

    return $api;
  }

  public function send_mothership_request( $endpoint,
                                           $args=array(),
                                           $method='get',
                                           $blocking=true ) {
    $domain = defined('PRLI_MOTHERSHIP_DOMAIN') ? PRLI_MOTHERSHIP_DOMAIN : 'http://mothership.caseproof.com';
    $uri = "{$domain}{$endpoint}";

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'sslverify' => false
    );

    $resp = wp_remote_request($uri, $arg_array);

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if($blocking == false) {
      return true;
    }

    if(is_wp_error($resp)) {
      throw new Exception(__('You had an HTTP error connecting to Caseproof\'s Mothership API', 'pretty-link'));
    }
    else {
      if(null !== ($json_res = json_decode($resp['body'], true))) {
        if(isset($json_res['error'])) {
          throw new Exception($json_res['error']);
        }
        else {
          return $json_res;
        }
      }
      else {
        throw new Exception(__( 'Your License Key was invalid', 'pretty-link'));
      }
    }

    return false;
  }

  public function enqueue_scripts($hook) {
    if(preg_match('/_page_pretty-link-updates$/', $hook)) {
      wp_register_style('prli-settings-table', PRLI_CSS_URL.'/settings_table.css', array(), PRLI_VERSION);
      wp_enqueue_style('prli-activate-css', PRLI_CSS_URL.'/admin-activate.css', array('prli-settings-table'), PRLI_VERSION);

      wp_register_script('prli-settings-table', PRLI_JS_URL.'/settings_table.js', array(), PRLI_VERSION);
      wp_enqueue_script('prli-activate-js', PRLI_JS_URL.'/admin_activate.js', array('prli-settings-table'), PRLI_VERSION);
      wp_localize_script('prli-activate-js', 'PrliActivateL10n', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'activate_license_nonce' => wp_create_nonce('prli_activate_license'),
        'loading_image' => sprintf('<img src="%1$s" alt="%2$s" />', esc_url(PRLI_IMAGES_URL . '/square-loader.gif'), esc_attr__('Loading...', 'pretty-link')),
        'activation_error' => __('An error occurred during activation: %s', 'pretty-link'),
        'invalid_response' => __('Invalid response.', 'pretty-link'),
        'ajax_error' => __('Ajax error.', 'pretty-link'),
        'deactivate_confirm' => sprintf(__('Are you sure? Pretty Links will not be functional on %s if this License Key is deactivated.', 'pretty-link'), PrliUtils::site_domain()),
        'deactivate_license_nonce' => wp_create_nonce('prli_deactivate_license'),
        'deactivation_error' => __('An error occurred during deactivation: %s', 'pretty-link'),
        'install_license_edition_nonce' => wp_create_nonce('prli_install_license_edition'),
        'error_installing_license_edition' => __('An error occurred while installing the correct edition.', 'pretty-link'),
      ));
    }
  }

  public function activation_warning() {
    if($this->is_installed() && empty($this->mothership_license) &&
       (!isset($_REQUEST['page']) || !($_REQUEST['page']=='pretty-link-updates'))) {
      require(PRLI_VIEWS_PATH.'/admin/update/activation_warning.php');
    }
  }

  public function plp_edge_updates() {
    if(!PrliUtils::is_prli_admin() || !wp_verify_nonce($_POST['wpnonce'],'wp-edge-updates')) {
      die(json_encode(array('error' => __('You do not have access.', 'pretty-link'))));
    }

    if(!isset($_POST['edge'])) {
      die(json_encode(array('error' => __('Edge updates couldn\'t be updated.', 'pretty-link'))));
    }

    $this->set_edge_updates($_POST['edge']=='true');

    // Re-queue updates when this is checked
    $this->manually_queue_update();

    die(json_encode(array('state' => ($this->edge_updates ? 'true' : 'false'))));
  }

  public function addons($return_object=false, $force=false, $all = false) {
    $license = $this->mothership_license;
    $transient = $all ? 'prli_all_addons' : 'prli_addons';

    if($force) {
      delete_site_transient($transient);
    }

    if(($addons = get_site_transient($transient))) {
      $addons = json_decode($addons);
    }
    else {
      $addons = array();

      if(!empty($license)) {
        try {
          $domain = urlencode(PrliUtils::site_domain());
          $args = compact('domain');

          if($all) {
            $args['all'] = 'true';
          }

          if(defined('PRETTYLINK_EDGE') && PRETTYLINK_EDGE) { $args['edge'] = 'true'; }
          $addons = $this->send_mothership_request('/versions/addons/'.PRLI_EDITION."/{$license}", $args);
        }
        catch(Exception $e) {
          // fail silently
        }
      }

      $json = json_encode($addons);
      set_site_transient($transient, $json, (HOUR_IN_SECONDS * 12));

      if($return_object) {
        $addons = json_decode($json);
      }
    }

    return $addons;
  }

  public function activate_page_url() {
    return admin_url('admin.php?page=pretty-link-updates');
  }

  public function update_plugin_url() {
    return admin_url('update.php?action=upgrade-plugin&plugin=' . urlencode($this->plugin_slug) . '&_wpnonce=' . wp_create_nonce('upgrade-plugin_' . $this->plugin_slug));
  }

  public function update_plugin() {
    $this->manually_queue_update();
    wp_redirect($this->update_plugin_url());
    exit;
  }

  public function upgrade_categories() {
    $section_title = esc_html__( 'Link Categories', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?categories';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/categories.php";
  }

  public function upgrade_tags() {
    $section_title = esc_html__( 'Link Tags', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?tags';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/tags.php";
  }

  public function upgrade_reports() {
    $section_title = esc_html__( 'Link Reports', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?reports';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/reports.php";
  }

  public function upgrade_groups() {
    $section_title = esc_html__( 'Display Groups', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?groups';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/groups.php";
  }

  public function upgrade_import_export() {
    $section_title = esc_html__( 'Import / Export', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?import-export';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/import-export.php";
  }

  public static function check_incorrect_edition() {
    if(PrliUtils::is_incorrect_edition_installed()) {
      printf(
        /* translators: %1$s: open link tag, %2$s: close link tag */
        ' <strong>' . esc_html__('To restore automatic updates, %1$sinstall the correct edition%2$s of Pretty Links.', 'pretty-link') . '</strong>',
        sprintf('<a href="%s">', esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-updates'))),
        '</a>'
      );
    }
  }

  public function clear_update_transients() {
    delete_site_transient('update_plugins');
    delete_site_transient('prli_update_info');
    delete_site_transient('prli_addons');
    delete_site_transient('prli_all_addons');
  }

  public function get_license_info() {
    $license_info = get_site_transient('prli_license_info');
    $mothership_license = $this->mothership_license;

    if(!$license_info && !empty($mothership_license)) {
      try {
        $domain = urlencode(PrliUtils::site_domain());
        $args = compact('domain');
        $license_info = $this->send_mothership_request("/versions/info/{$mothership_license}", $args, 'post');

        set_site_transient('prli_license_info', $license_info, (24*HOUR_IN_SECONDS));
      } catch (Exception $e) {
        // Fail silently, license info will return false.
      }
    }

    return $license_info;
  }
  public function upgrade_products() {
    $section_title = esc_html__( 'Products Display', 'pretty-link' );
    $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?products';
    include_once PRLI_VIEWS_PATH . "/admin/upgrade/products.php";
  }

} //End class
