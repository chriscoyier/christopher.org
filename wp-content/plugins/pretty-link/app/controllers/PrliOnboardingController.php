<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class PrliOnboardingController extends PrliBaseController {
  public function load_hooks() {
    add_filter('submenu_file', 'PrliOnboardingController::highlight_menu_item');
    add_action('admin_enqueue_scripts', 'PrliOnboardingController::admin_enqueue_scripts');
    add_action('admin_notices', 'PrliOnboardingController::remove_all_admin_notices', 0);
    add_action('wp_ajax_prli_onboarding_save_features', 'PrliOnboardingController::save_features');
    add_action('wp_ajax_prli_onboarding_save_new_link', 'PrliOnboardingController::save_new_link');
    add_action('wp_ajax_prli_onboarding_save_new_category', 'PrliOnboardingController::save_new_category');
    add_action('wp_ajax_prli_onboarding_get_category', 'PrliOnboardingController::get_category');
    add_action('wp_ajax_prli_onboarding_set_content', 'PrliOnboardingController::set_content');
    add_action('wp_ajax_prli_onboarding_unset_content', 'PrliOnboardingController::unset_content');
    add_action('wp_ajax_prli_onboarding_import_links', 'PrliOnboardingController::import_links');
    add_action('wp_ajax_prli_onboarding_mark_content_steps_skipped', 'PrliOnboardingController::mark_content_steps_skipped');
    add_action('wp_ajax_prli_onboarding_mark_steps_complete', 'PrliOnboardingController::mark_steps_complete');
    add_action('wp_ajax_prli_onboarding_unset_category', 'PrliOnboardingController::unset_category');
    add_action('wp_ajax_prli_onboarding_install_correct_edition', 'PrliOnboardingController::install_correct_edition');
    add_action('wp_ajax_prli_onboarding_install_addons', 'PrliOnboardingController::install_addons');
    add_action('wp_ajax_prli_onboarding_load_complete_step', 'PrliOnboardingController::load_complete_step');
    add_action('wp_ajax_prli_onboarding_load_create_new_content', 'PrliOnboardingController::load_create_new_content');
    add_action('wp_ajax_prli_onboarding_load_link_step_content', 'PrliOnboardingController::load_link_step_content');
    add_action('wp_ajax_prli_onboarding_re_render_links_list', 'PrliOnboardingController::re_render_links_list');
    add_action('wp_ajax_prli_onboarding_load_finish_step', 'PrliOnboardingController::load_finish_step');
    add_action('wp_ajax_prli_onboarding_finish', 'PrliOnboardingController::finish');
    add_action('prli_license_activated', 'PrliOnboardingController::license_activated');
    add_action('prli_license_deactivated', 'PrliOnboardingController::license_deactivated');
    add_action('admin_menu', 'PrliOnboardingController::validate_step');
    add_action('load-admin_page_pretty-link-onboarding', 'PrliOnboardingController::settings_redirect');
    add_action('admin_notices', 'PrliOnboardingController::admin_notice');
    add_filter('monsterinsights_shareasale_id', 'PrliOnboardingController::monsterinsights_shareasale_id');
    add_action('activated_plugin', 'PrliOnboardingController::activated_plugin');
  }

  public static function route() {
    global $wpdb;

    $wpdb->query("INSERT INTO {$wpdb->options} (option_name, option_value) VALUES('prli_onboarded', '1') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value);");

    $step = isset($_GET['step']) ? (int) $_GET['step'] : 0;

    if($step) {
      $steps = array(
        array(
          'title' => __('Activate License', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/license.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/license.php',
          'step' => 1,
        ),
        array(
          'title' => __('Enable Features', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/features.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/features.php',
          'step' => 2,
        ),
        array(
          'title' => __('Creating Your Pretty Link', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/pretty-link.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/pretty-link.php',
          'step' => 3,
        ),
        array(
          'title' => __('Categorize Your Pretty Link', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/category.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/category.php',
          'step' => 4,
        ),
        array(
          'title' => __('Finish Setup', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/finish.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/finish.php',
          'step' => 5,
        ),
        array(
          'title' => __('Complete', 'pretty-link'),
          'content' => PRLI_VIEWS_PATH . '/admin/onboarding/complete.php',
          'nav' => PRLI_VIEWS_PATH . '/admin/onboarding/nav/complete.php',
          'step' => 6,
        ),
      );

      require_once PRLI_VIEWS_PATH . '/admin/onboarding/wizard.php';
    }
    else {
      require_once PRLI_VIEWS_PATH . '/admin/onboarding/welcome.php';
    }
  }

  public static function admin_enqueue_scripts() {
    global $plp_update, $prli_link;

    if(self::is_onboarding_page()) {
      wp_enqueue_style('pretty-link-fontello-animation', PRLI_VENDOR_LIB_URL . '/fontello/css/animation.css', array(), PRLI_VERSION);
      wp_enqueue_style('pretty-link-onboarding', PRLI_CSS_URL . '/admin_onboarding.css', array(), PRLI_VERSION);
      wp_enqueue_script('pretty-link-onboarding', PRLI_JS_URL . '/admin_onboarding.js', array('jquery'), PRLI_VERSION, true);
      wp_localize_script('pretty-link-onboarding', 'PrliOnboardingL10n', array(
        'step' => isset($_GET['step']) ? (int) $_GET['step'] : 0,
        'ajax_url' => admin_url('admin-ajax.php'),
        'onboarding_url' => admin_url('admin.php?page=pretty-link-onboarding'),
        'is_pro_user' => $plp_update->is_installed_and_activated(),
        'features' => self::get_features(),
        'save_features_nonce' => wp_create_nonce('prli_onboarding_save_features'),
        'save_new_link_nonce' => wp_create_nonce('prli_onboarding_save_new_link'),
        'save_new_category_nonce' => wp_create_nonce('prli_onboarding_save_new_category'),
        'get_category_nonce' => wp_create_nonce('prli_onboarding_get_category'),
        'install_correct_edition' => wp_create_nonce('prli_onboarding_install_correct_edition'),
        'install_license_edition' => wp_create_nonce('prli_install_license_edition'),
        'install_addons' => wp_create_nonce('prli_onboarding_install_addons'),
        'load_complete_step' => wp_create_nonce('prli_onboarding_load_complete_step'),
        'load_create_new_content' => wp_create_nonce('prli_onboarding_load_create_new_content'),
        'load_finish_step' => wp_create_nonce('prli_onboarding_load_finish_step'),
        'load_link_step_content' => wp_create_nonce('prli_onboarding_load_link_step_content'),
        're_render_links_list' => wp_create_nonce('prli_onboarding_re_render_links_list'),
        'set_content_nonce' => wp_create_nonce('prli_onboarding_set_content'),
        'unset_content_nonce' => wp_create_nonce('prli_onboarding_unset_content'),
        'import_links_nonce' => wp_create_nonce('prli_onboarding_import_links'),
        'unset_category_nonce' => wp_create_nonce('prli_onboarding_unset_category'),
        'mark_content_steps_skipped_nonce' => wp_create_nonce('prli_onboarding_mark_content_steps_skipped'),
        'mark_steps_complete_nonce' => wp_create_nonce('prli_onboarding_mark_steps_complete'),
        'deactivate_confirm' => __('Are you sure? You will no longer receive updates for Pretty Links if this License Key is deactivated.', 'pretty-link'),
        'activate_license_nonce' => wp_create_nonce('prli_activate_license'),
        'deactivate_license_nonce' => wp_create_nonce('prli_deactivate_license'),
        'an_error_occurred' => __('An error occurred', 'pretty-link'),
        'content_id' => PrliOnboardingHelper::get_link_id(),
        'has_imported_links' => PrliOnboardingHelper::get_has_imported_links(),
        'category_id' => PrliOnboardingHelper::get_category_id(),
        'course_name' => __('Course Name', 'pretty-link'),
        'link_name' => __('Link Name', 'pretty-link'),
        'course' => __('Course', 'pretty-link'),
        'page' => __('Page', 'pretty-link'),
        'may_take_couple_minutes' => __('This may take a couple of minutes', 'pretty-link'),
        'finish_nonce' => wp_create_nonce('prli_onboarding_finish'),
        'pretty_links_url' => admin_url('edit.php?post_type=pretty-link'),
        'error_installing_addon' => __('An error occurred when installing an add-on, please download and install the add-ons manually.', 'pretty-link'),
        'edition_url_param' => isset($_GET['edition']) ? sanitize_text_field(wp_unslash($_GET['edition'])) : '',
        'license_url_param' => isset($_GET['license']) ? sanitize_text_field(wp_unslash($_GET['license'])) : '',
        'images_url' => esc_url(PRLI_IMAGES_URL . '/onboarding/'),
        'remove' => __('Remove', 'pretty-link'),
        'link_count' => $prli_link->get_count()
      ));
    }
  }

  private static function get_features() {
    return array(
      'features' => array(
        'pretty-link-link-tracking'    => 'Link Tracking',
        'pretty-link-no-follow'        => 'No Follow',
        'pretty-link-sponsored'        => 'Sponsored',
        'pretty-link-qr-codes'         => 'QR Codes',
        'pretty-link-link-health'      => 'Link Health',
        'pretty-link-replacements'     => 'Replacements',
        'pretty-link-import-export'    => 'Import/Export Links'
      ),
      'addons'   => array(
        'pretty-link-product-displays' => 'Pretty Links Product Displays',
        'monsterinsights'              => 'MonsterInsights'
      )
    );
  }

  private static function get_pro_features() {
    return array(
      'pretty-link-qr-codes',
      'pretty-link-link-health',
      'pretty-link-replacements',
      'pretty-link-import-export'
    );
  }

  public static function remove_all_admin_notices() {
    if(self::is_onboarding_page()) {
      remove_all_actions('admin_notices');
    }
  }

  public static function highlight_menu_item($submenu_file) {
    remove_submenu_page('pretty-link', 'pretty-link-onboarding');

    if(self::is_onboarding_page()) {
      $submenu_file = 'edit.php?post_type=pretty-link';
    }

    return $submenu_file;
  }

  public static function is_onboarding_page() {
    $screen = get_current_screen();

    return ($screen && is_string($screen->id) && preg_match('/_page_pretty-link-onboarding$/', $screen->id));
  }

  private static function validate_request($nonce_action) {
    if(!PrliUtils::is_post_request()) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!PrliUtils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer($nonce_action, false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }
  }

  private static function get_request_data($nonce_action) {
    self::validate_request($nonce_action);

    if(!isset($_POST['data']) || !is_string($_POST['data'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    $data = json_decode(wp_unslash($_POST['data']), true);

    if(!is_array($data)) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    return $data;
  }

  public static function save_features() {
    global $plp_update, $plp_options, $prli_options;

    $data = self::get_request_data('prli_onboarding_save_features');

    $valid_features = self::get_features();
    $pro_features = self::get_pro_features();
    $features = array();
    $addons = array();

    foreach($data as $feature) {
      if(array_key_exists($feature, $valid_features['features'])) {
        $features[] = $feature;
      }

      if(array_key_exists($feature, $valid_features['addons'])) {
        $addons[] = $feature;
      }
    }

    // Figure out which features were left disabled.
    $missing_keys = array_diff_key($valid_features['features'], array_flip($features));
    $features_disabled = array_keys($missing_keys);

    $addons_installed = array();
    $all_features = array_merge($features, $features_disabled);

    $data = array();
    $data['features'] = array_merge($features, $addons);
    $data['addons_not_installed'] = array();
    $data['features_not_enabled'] = array();

    // Loop over each add-on then attempt to install and activate them.
    if(!empty($addons)) {
      $license_addons = $plp_update->addons(true, true, true);

      // lets try to install and activate add-on.
      foreach( $addons as $addon_slug ) {
        $response = self::maybe_install_activate_addons($license_addons, $addon_slug);
        if( -1 === (int) $response ) {
          $data['addons_not_installed'][] = $addon_slug;
        }
      }
    }

    // Enable or disable any features that the user enabled/disabled.
    if(!empty($all_features)) {
      foreach($all_features as $feature) {
        $should_enable = in_array($feature, $features) ? 1 : 0;

        if(!$plp_update->is_installed_and_activated() && in_array($feature, $pro_features) && $should_enable) {
          $data['features_not_enabled'][] = $feature;
        } else {
          self::enable_disable_feature($feature, $should_enable);
        }
      }

      $prli_options->store();

      if(isset($plp_options)) {
        $plp_options->store();
      }
    }

    PrliOnboardingHelper::set_selected_features($data);
    PrliOnboardingHelper::maybe_set_steps_completed(2);

    wp_send_json_success($data);
  }

  public static function enable_disable_feature($feature, $value) {
    global $plp_options, $prli_options;

    switch($feature) {
      case 'pretty-link-link-tracking':
        $prli_options->link_track_me = $value;
        break;
      case 'pretty-link-no-follow':
        $prli_options->link_nofollow = $value;
        break;
      case 'pretty-link-sponsored':
        $prli_options->link_sponsored = $value;
        break;
      case 'pretty-link-qr-codes':
        if(isset($plp_options)) {
          $plp_options->generate_qr_codes = $value;
        }
        break;
      case 'pretty-link-link-health':
        if(isset($plp_options)) {
          $plp_options->enable_link_health = $value;
        }
        break;
      case 'pretty-link-replacements':
        if(isset($plp_options)) {
          $plp_options->keyword_replacement_is_on = $value;
        }
        break;
      default:
        break;
    }
  }

  public static function maybe_install_activate_addons($license_addons, $addon_slug) {
    $return_value = -1;

    if(isset($license_addons->$addon_slug)) {
      $addon_info = $license_addons->$addon_slug;

      $plugin_url = $addon_info->url;

      $installed = isset($addon_info->extra_info->directory) && is_dir(WP_PLUGIN_DIR . '/' . $addon_info->extra_info->directory);
      $active = isset($addon_info->extra_info->main_file) && is_plugin_active($addon_info->extra_info->main_file);

      if($installed && $active) { // already installed and active.
        return 1;
      }
      elseif($installed && !$active) { // already installed and inactive.

        if(isset($addon_info->extra_info->main_file)) {
          self::maybe_install_dependent_plugin($addon_slug);
          $result = activate_plugins(wp_unslash($addon_info->extra_info->main_file));
          return (int) is_wp_error($result);
        }
        else {
          return 0;
        }
      }
      else {
        return (int) self::download_and_activate_addon($addon_info, $plugin_url, $addon_slug);
      }
    }

    // Install/activate MI if applicable.
    if($addon_slug == 'monsterinsights') {
      return self::maybe_install_dependent_plugin($addon_slug);
    }

    return $return_value;
  }

  public static function maybe_install_dependent_plugin($addon_slug) {
    if('monsterinsights' === (string)$addon_slug){
      $plugin = 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.latest-stable.zip';
      $main_file = 'google-analytics-for-wordpress/googleanalytics.php';

      $installed = is_dir(WP_PLUGIN_DIR . '/' . 'google-analytics-for-wordpress');
      $active = is_plugin_active($main_file);

      if($installed && $active) {
        return 1;
      }

      // If MI is installed but not active, let's activate.
      if($installed && !$active) {
        $result = activate_plugins(wp_unslash($main_file));
        return $result;
      } else {
        $result = (int) self::download_and_activate_plugin($plugin);
        return $result;
      }
    }
  }

  public static function save_new_link() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_save_new_link');

    if(empty($data['target_url']) || empty($data['slug']) || empty($data['redirection'])) {
      wp_send_json_error(esc_html__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $target_url = sanitize_text_field($data['target_url']);
    $slug = sanitize_text_field($data['slug']);
    $redirection = sanitize_text_field($data['redirection']);

    // Attempt to create the pretty link.
    $id = prli_create_pretty_link(
      $target_url,
      $slug,
      '',
      '',
      0,
      '',
      '',
      '',
      $redirection
    );

    if(!$id) {
      wp_send_json_error(esc_html__('There was a problem attempting to create your pretty link. Please try again.', 'pretty-link'));
    }

    $pretty_link = $prli_link->getOne($id);

    if(!$pretty_link) {
      wp_send_json_error(esc_html__('There was a problem locating your newly created pretty link.', 'pretty-link'));
    }

    PrliOnboardingHelper::set_link_id($pretty_link->link_cpt_id);
    PrliOnboardingHelper::maybe_set_steps_completed(2);

    wp_send_json_success(array(
      'heading' => esc_html__('Pretty Link', 'pretty-link'),
      'link' => $pretty_link
    ));
  }

  public static function render_links_list() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_load_link_step_content');

    $pretty_links = $prli_link->getAll('', ' ORDER BY li.created_at DESC');
    $current_page = isset($data['link_page']) ? absint($data['link_page']) : 1;

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/onboarding/created-links-list.php';
    $html = ob_get_clean();

    return $html;
  }

  public static function license_activated() {
    global $plp_update;

    if(!isset($_GET['page']) || !isset($_GET['step'])) {
      return;
    }

    PrliOnboardingHelper::maybe_set_steps_completed(1);

    if((string) $_GET['page'] === 'pretty-link-onboarding' && (int) $_GET['step'] === 1) {
      // to rebuild the prli_license_info transient.
      $plp_update->manually_queue_update();

      $li = get_site_transient('prli_license_info');

      $editions = PrliUtils::is_incorrect_edition_installed();

      if(is_array($editions) && $editions['license']['index'] > $editions['installed']['index'] ||
        $plp_update->is_activated() && !$plp_update->is_installed() && !empty($li) && is_array($li) && !empty($li['url']) && PrliUtils::is_url($li['url'])) {
        $result = self::install_plugin_silently($li['url'], array('overwrite_package' => true));
        if($result === true) {
          do_action('prli_plugin_edition_changed');
        }
      }
    }
  }

  public static function license_deactivated() {
    PrliOnboardingHelper::set_steps_completed(0);
  }

  public static function validate_step() {
    if(!isset($_GET['page']) || !isset($_GET['step'])) {
      return;
    }

    global $prli_link;

    $current_step = (int) $_GET['step'];

    if('pretty-link-onboarding' === (string) $_GET['page'] && 0 < $current_step) {
       if($current_step == 4) {
        $content_id = PrliOnboardingHelper::get_link_id();
        $has_imported_links = PrliOnboardingHelper::get_has_imported_links();

        if((int) $content_id === 0 && (int) $has_imported_links === 0 || (int) $has_imported_links === 1 && $prli_link->get_count() <= 0) {
          wp_safe_redirect(admin_url('admin.php?page=pretty-link-onboarding&step=3'));
          return;
        }
      }

      $steps_completed = PrliOnboardingHelper::get_steps_completed();
      $next_applicable_step = $steps_completed + 1;

      if($current_step > $next_applicable_step) {
        $link_step = $steps_completed + 1;
        wp_safe_redirect(admin_url('admin.php?page=pretty-link-onboarding&step=' . (int) $link_step));
      }
    }
  }

  private static function download_and_activate_plugin($plugin_url) {

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        array(
          'page' => 'pretty-link-addons',
          'onboarding' => '1',
        ),
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      return false;
    }

    if(!WP_Filesystem($creds)) {
      return false;
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

    // Create the plugin upgrader with our custom skin
    $installer = new Plugin_Upgrader(new PrliAddonInstallSkin());

    $plugin = wp_unslash($plugin_url);
    $installer->install($plugin);

    if($plugin == 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.latest-stable.zip') {
      update_option('pretty_links_installed_monsterinsights', true);
    }

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        return true;
      } else {
        return false;
      }
    }

    return false;
  }

  private static function download_and_activate_addon($addon_info,$plugin_url, $addon_slug = '') {

    if(!$addon_info->installable){
      return -1; // upgrade required.
    }

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        array(
          'page' => 'pretty-link-addons',
          'onboarding' => '1',
        ),
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      return false;
    }

    if(!WP_Filesystem($creds)) {
      return false;
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

    // Create the plugin upgrader with our custom skin
    $installer = new Plugin_Upgrader(new PrliAddonInstallSkin());

    $plugin = wp_unslash($plugin_url);
    $installer->install($plugin);

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      self::maybe_install_dependent_plugin($addon_slug);

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        return true;
      } else {
        return false;
      }
    }

    return false;
  }

  public static function set_content() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_set_content');

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(empty($data['content_id'])) {
      wp_send_json_error(esc_html__('Bad request.', 'pretty-link'));
    }

    $content_id = absint($data['content_id']);
    $post = get_post($content_id);

    if(!$post instanceof WP_Post){
      wp_send_json_error(esc_html__('Invalid request.', 'pretty-link'));
    }

    $pretty_link_id = $prli_link->get_link_from_cpt($content_id);

    PrliOnboardingHelper::set_link_id($content_id);
    PrliOnboardingHelper::maybe_set_steps_completed(3);

    wp_send_json_success(array(
      'link_data' => $prli_link->getOne($pretty_link_id)
    ));
  }

  public static function import_links() {
    self::validate_request('prli_onboarding_import_links');

    $csv_file = isset($_FILES['importedfile']) ? $_FILES['importedfile'] : '';

    if(empty($csv_file)) {
      wp_send_json_error(esc_html__('There was a problem processing your CSV file. Please try the import again.', 'pretty-link'));
    }

    $plp_import_controller = new PlpImportExportController();

    $result = $plp_import_controller->import();

    PrliOnboardingHelper::set_has_imported_links(1);

    wp_send_json_success($result);
  }

  public static function unset_content() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_unset_content');

    $imported_links = $data['imported_links'];
    $link_id = $prli_link->get_link_from_cpt($data['content_id']);

    if($link_id) {
      $prli_link->destroy($link_id);
    }

    PrliOnboardingHelper::set_link_id(0);

    if($imported_links) {
      wp_send_json_success(array('count' => $prli_link->get_count()));
    } else {
      wp_send_json_success();
    }
  }

  public static function unset_category() {
    $data = self::get_request_data('prli_onboarding_unset_category');
    $taxonomy = 'pretty-link-category';

    wp_delete_term($data['category_id'], $taxonomy);

    PrliOnboardingHelper::set_category_id(0);
  }

  public static function mark_content_steps_skipped() {
    $data = self::get_request_data('prli_onboarding_mark_content_steps_skipped');
    PrliOnboardingHelper::mark_content_steps_skipped();
    PrliOnboardingHelper::maybe_set_steps_completed(4);
  }

  public static function mark_steps_complete() {
    $data = self::get_request_data('prli_onboarding_mark_steps_complete');
    PrliOnboardingHelper::maybe_set_steps_completed($data['step']);
  }

  public static function save_new_category() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_save_new_category');

    if(empty($data['name'])) {
      wp_send_json_error(esc_html__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $name = sanitize_text_field($data['name']);
    $link_ids = isset($data['link_ids']) && !empty($data['link_ids']) ? array_map('absint', $data['link_ids']) : '';

    $taxonomy = 'pretty-link-category';

    $result = wp_insert_term($name, $taxonomy);

    if(is_wp_error($result)) {
      wp_send_json_error($result->get_error_message());
    }

    $term_id = $result['term_id'];

    if(empty($link_ids)) {
      $link_id = PrliOnboardingHelper::get_link_id();

      wp_set_post_terms($link_id, $term_id, $taxonomy);
    } else {
      foreach($link_ids as $link_id) {
        $pretty_link = $prli_link->getOne($link_id);

        wp_set_post_terms($pretty_link->link_cpt_id, $term_id, $taxonomy);
      }
    }

    $term = get_term($term_id);

    PrliOnboardingHelper::set_category_id($term_id);
    PrliOnboardingHelper::maybe_set_steps_completed(4);

    wp_send_json_success(array('term' => $term));
  }

  public static function get_category() {
    $data = self::get_request_data('prli_onboarding_get_category');

    if(empty($data['category_id'])) {
      wp_send_json_error(esc_html__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(esc_html__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $term_id = sanitize_text_field($data['category_id']);
    $term = get_term($term_id);

    wp_send_json_success(array('term' => $term));
  }

  public static function install_correct_edition() {
    self::validate_request('prli_onboarding_install_correct_edition');
    $li = get_site_transient('prli_license_info');

    if(!empty($li) && is_array($li) && !empty($li['url']) && PrliUtils::is_url($li['url'])) {
      $result = self::install_plugin_silently($li['url'], array('overwrite_package' => true));

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

  private static function install_plugin_silently($url, $args) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    if(!function_exists('request_filesystem_credentials')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $skin = new Automatic_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);

    if(!$skin->request_filesystem_credentials(false, WP_PLUGIN_DIR)) {
      return new WP_Error('no_filesystem_access', __('Failed to get filesystem access', 'pretty-link'));
    }

    return $upgrader->install($url, $args);
  }

  public static function install_addons() {
    global $plp_update;

    $data = self::get_request_data('prli_onboarding_install_addons');

    if(empty($data['addon_slug'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('publish_posts')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $features_data = PrliOnboardingHelper::get_selected_features_data();
    if(!isset($features_data['addons_installed'])){
      $features_data['addons_installed'] = array();
    }

    if(!isset($features_data['addons_upgrade_failed'])){
      $features_data['addons_upgrade_failed'] = array();
    }

    if(!empty($features_data['addons_not_installed'])) {
      if(in_array($data['addon_slug'], $features_data['addons_not_installed'], true)) {
        $license_addons = $plp_update->addons(true, true, true);

        // lets try to install and activate add-on.
        foreach ($features_data['addons_not_installed'] as $i => $addon_slug) {
          if($addon_slug == $data['addon_slug']) {
            $response = self::maybe_install_activate_addons($license_addons, $addon_slug);
            $next_addon = isset($features_data['addons_not_installed'][$i + 1]) ? $features_data['addons_not_installed'][$i + 1] : '';

            if(1 === (int) $response) {
              $features_data['addons_installed'][] = $addon_slug;
              $features_data['addons_installed'] = array_unique($features_data['addons_installed']);

              PrliOnboardingHelper::set_selected_features($features_data);
              wp_send_json_success(array('addon_slug' => $addon_slug, 'message' => '', 'status' => 1, 'next_addon' => $next_addon));
            }
            else {
              $features_data['addons_upgrade_failed'][] = $addon_slug;
              $features_data['addons_upgrade_failed'] = array_unique($features_data['addons_upgrade_failed']);

              PrliOnboardingHelper::set_selected_features($features_data);
              wp_send_json_success(array('addon_slug' => $addon_slug, 'message' => esc_html__('Unable to install. Please download and install manually.', 'pretty-link'), 'status' => 0, 'next_addon' => $next_addon));
            }
          }
        }
      }
    }
  }

  public static function load_complete_step() {
    $data = self::get_request_data('prli_onboarding_load_complete_step');

    wp_send_json_success(array('html' => PrliOnboardingHelper::get_completed_step_urls_html()));
  }

  public static function load_create_new_content() {
    global $plp_update;

    $data = self::get_request_data('prli_onboarding_load_create_new_content');

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/onboarding/parts/link_popup.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public static function load_link_step_content() {
    $data = self::get_request_data('prli_onboarding_load_link_step_content');

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/onboarding/parts/pretty-link.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public static function re_render_links_list() {
    global $prli_link;

    $data = self::get_request_data('prli_onboarding_re_render_links_list');

    $pretty_links = $prli_link->getAll('', ' ORDER BY li.created_at DESC');
    $current_page = isset($data['page']) && $data['page'] > 1 ? absint($data['page']) : 1;

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/onboarding/created-links-list.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public static function load_finish_step() {
    $data = self::get_request_data('prli_onboarding_load_finish_step');

    ob_start();
    require PRLI_VIEWS_PATH . '/admin/onboarding/parts/finish.php';
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
  }

  public static function finish() {
    self::validate_request('prli_onboarding_finish');

    update_option('prli_onboarding_complete', '1');

    wp_send_json_success();
  }

  public static function settings_redirect() {
    if(!is_user_logged_in() || wp_doing_ajax() || !is_admin() || is_network_admin() || !PrliUtils::is_authorized() || PrliUtils::is_post_request()) {
      return;
    }

    global $wpdb;

    wp_cache_flush();
    $wpdb->flush();

    $onboarding_complete = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'prli_onboarding_complete'");

    if($onboarding_complete === '1') {
      nocache_headers();
      wp_redirect(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options'), 307);
      exit;
    }
  }

  public static function admin_notice() {
    global $prli_app_controller;

    if(!$prli_app_controller->is_pretty_link_page() || !PrliUtils::is_logged_in_and_an_admin()) {
      return;
    }

    if(!get_option('prli_onboarded') || get_option('prli_onboarding_complete') == '1' || get_transient('prli_dismiss_notice_continue_onboarding')) {
      return;
    }
    ?>
    <div class="notice notice-info prli-notice-dismiss-daily is-dismissible" data-notice="continue_onboarding">
      <p>
        <?php
        printf(
          // translators: %1$s open link tag, %2$s: close link tag
          esc_html__("Welcome back! It seems the excitement of setting up Pretty Links got the best of you. No worries! %1\$sClick here%2\$s and we'll whisk you back to where you left off.", 'pretty-link'),
          '<a href="' . esc_url(admin_url('admin.php?page=pretty-link-onboarding&step=1')) . '">',
          '</a>'
        );
        ?>
      </p>
    </div>
    <?php
  }

  public static function monsterinsights_shareasale_id($id) {
    if(get_option('pretty_links_installed_monsterinsights')) {
      $id = '409876';
    }

    return $id;
  }

  public static function activated_plugin($plugin) {
    if($plugin != PRLI_PLUGIN_SLUG) {
      return;
    }

    if(!is_user_logged_in() || wp_doing_ajax() || !is_admin() || is_network_admin() || !PrliUtils::is_admin()) {
      return;
    }

    if(PrliUtils::is_post_request() && (isset($_POST['action']) || isset($_POST['action2']))) {
      return; // Don't redirect on bulk activation.
    }

    global $wpdb, $prli_link;

    $prli_db = new PrliDb();

    if($prli_db->table_exists($prli_db->links) && $prli_link->get_count() > 0) {
      return;
    }

    wp_cache_flush();
    $wpdb->flush();

    $onboarded = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'prli_onboarded'");

    if($onboarded === null) {
      nocache_headers();
      wp_redirect(admin_url('admin.php?page=pretty-link-onboarding'), 307);
      exit;
    }
  }
}
