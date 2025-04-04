<?php

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAddonsController extends PrliBaseController {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('in_admin_header', array($this, 'hide_admin_notices'), 9999);
    add_action('wp_ajax_prli_addon_activate', array($this, 'ajax_addon_activate'));
    add_action('wp_ajax_prli_addon_deactivate', array($this, 'ajax_addon_deactivate'));
    add_action('wp_ajax_prli_addon_install', array($this, 'ajax_addon_install'));
  }

  public function enqueue_scripts($hook) {
    global $plp_update;

    if(preg_match('/_page_pretty-link-addons$/', $hook)) {
      wp_enqueue_style('prli-addons-css', PRLI_CSS_URL . '/admin_addons.css', array(), PRLI_VERSION);

      if($plp_update->is_installed()) {
        wp_enqueue_script('list-js', PRLI_JS_URL . '/list.min.js', array(), '1.5.0');
        wp_enqueue_script('jquery-match-height', PRLI_JS_URL . '/jquery.matchHeight-min.js', array(), '0.7.2');
        wp_enqueue_script('prli-addons-js', PRLI_JS_URL . '/admin_addons.js', array('list-js', 'jquery-match-height'), PRLI_VERSION);

        wp_localize_script('prli-addons-js', 'PrliAddons', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'nonce' => wp_create_nonce('prli_addons'),
          'active' => __('Active', 'pretty-link'),
          'inactive' => __('Inactive', 'pretty-link'),
          'activate' => __('Activate', 'pretty-link'),
          'deactivate' => __('Deactivate', 'pretty-link'),
          'install_failed' => __('Could not install add-on.', 'pretty-link'),
          'plugin_install_failed' => __('Could not install plugin.', 'pretty-link'),
        ));
      }
    }
  }

  public function route() {
    global $plp_update;

    if($plp_update->is_installed()) {
      $force = isset($_GET['refresh']) && $_GET['refresh'] == 'true';
      $addons = $plp_update->addons(true, $force, true);
      $plugins = get_plugins();

      require_once(PRLI_VIEWS_PATH . '/admin/addons/addon-page-pro.php');
    } else {
      $section_title = esc_html__('Add-ons', 'pretty-link');
      $upgrade_link = 'https://prettylinks.com/pl/main-menu/upgrade?add-ons';

      require_once(PRLI_VIEWS_PATH . '/admin/addons/addon-page-lite.php');
    }
  }

  public function ajax_addon_activate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $result = activate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if(is_wp_error($result)) {
      if($type == 'plugin') {
        wp_send_json_error(__('Could not activate plugin. Please activate from the Plugins page manually.', 'pretty-link'));
      } else {
        wp_send_json_error(__('Could not activate add-on. Please activate from the Plugins page manually.', 'pretty-link'));
      }
    }

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin activated.', 'pretty-link'));
    } else {
      wp_send_json_success(__('Add-on activated.', 'pretty-link'));
    }
  }

  // Removing admin notices here as they get hidden behind the blurred screen for Lite versions.
  public function hide_admin_notices() {
    $screen = get_current_screen();

    if(!$screen || $screen->id != 'pretty-link_page_pretty-link-addons') {
      return;
    }

    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
  }

  public function ajax_addon_deactivate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('deactivate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    deactivate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin deactivated.', 'pretty-link'));
    } else {
      wp_send_json_success(__('Add-on deactivated.', 'pretty-link'));
    }
  }

  public function ajax_addon_install() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      $error = esc_html__('Could not install plugin.', 'pretty-link');
    } else {
      $error = esc_html__('Could not install add-on.', 'pretty-link');
    }

    // Set the current screen to avoid undefined notices
    set_current_screen('pretty-link_page_pretty-link-addons');

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        array(
          'page' => 'pretty-link-addons',
        ),
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      wp_send_json_error($error);
    }

    if(!WP_Filesystem($creds)) {
      wp_send_json_error($error);
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

    // Create the plugin upgrader with our custom skin
    $installer = new Plugin_Upgrader(new PrliAddonInstallSkin());

    $plugin = wp_unslash($_POST['plugin']);
    $installer->install($plugin);

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        wp_send_json_success(
          array(
            'message'   => $type == 'plugin' ? __('Plugin installed & activated.', 'pretty-link') : __('Add-on installed & activated.', 'pretty-link'),
            'activated' => true,
            'basename'  => $plugin_basename
          )
        );
      } else {
        wp_send_json_success(
          array(
            'message'   => $type == 'plugin' ? __('Plugin installed.', 'pretty-link') : __('Add-on installed.', 'pretty-link'),
            'activated' => false,
            'basename'  => $plugin_basename
          )
        );
      }
    }

    wp_send_json_error($error);
  }
}
