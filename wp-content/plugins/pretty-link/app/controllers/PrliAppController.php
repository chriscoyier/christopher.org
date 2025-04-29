<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAppController extends PrliBaseController {
  public $screens;

  public function __construct() {
    $this->screens = array(
      'add-edit' => 'pretty-link',
      'list'     => 'edit-pretty-link',
      'category' => 'edit-pretty-link-category',
      'tag'      => 'edit-pretty-link-tag',
      'clicks'   => 'pretty-links_page_pretty-link-clicks',
      'reports'  => 'pretty-links_page_plp-reports',
      'tools'    => 'pretty-links_page_pretty-link-tools',
      'options'  => 'pretty-links_page_pretty-link-options',
      'imp-exp'  => 'pretty-links_page_plp-import-export',
      'activate' => 'pretty-links_page_pretty-link-updates'
    );
  }

  public function load_hooks() {
    global $prli_options;

    add_action('init', array($this, 'parse_standalone_request'), 15); // Later so that the category taxonomy exists for the custom bookmarklet
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('admin_menu', array($this, 'menu'), 3); //Hooking in earlier - there's a plugin out there somewhere breaking this action for later plugins

    add_filter('custom_menu_order', '__return_true');
    add_filter('menu_order', array($this, 'admin_menu_order'));
    add_filter('menu_order', array($this, 'admin_submenu_order'));
    add_filter('display_post_states', array($this, 'add_post_states'), 10, 2);

    //Where the magic happens when not in wp-admin nor !GET request
    if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == 'GET' && !is_admin()) {
      add_action('init', array($this, 'redirect'), 1); // Redirect
    }

    // Hook into the 'wp_dashboard_setup' action to register our other functions
    add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));

    add_action('after_plugin_row', array($this, 'pro_action_needed'));
    add_action('admin_notices', array($this, 'pro_get_started_headline'));

    // DB upgrades/installs will happen here, as a non-blocking process hopefully
    add_action('init', array($this, 'install'));

    add_filter('plugin_action_links_' . PRLI_PLUGIN_SLUG, array($this,'add_plugin_action_links'));

    add_action('in_admin_header', array($this,'pl_admin_header'), 0);

    add_action('wp_ajax_pl_dismiss_upgrade_header', array($this, 'dismiss_upgrade_header'));
    add_action('wp_ajax_prli_dismiss_notice', array($this, 'dismiss_notice'));
    add_action('wp_ajax_prli_dismiss_daily_notice', array($this, 'dismiss_daily_notice'));
    add_action('wp_ajax_prli_dismiss_monthly_notice', array($this, 'dismiss_monthly_notice'));

    // Admin footer text.
    add_filter('admin_footer_text', array($this, 'admin_footer'), 1, 2);
    add_action('in_admin_footer', array($this, 'promote_pretty_links'));
  }

  /**
   * Dismisses the PL upgrade header bar.
   *
   * @return void
   */
  public function dismiss_upgrade_header() {

    // Security check
    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pl_dismiss_upgrade_header' ) ) {
      die();
    }

    update_option( 'pl_dismiss_upgrade_header', true );
  }


  public function pl_admin_header() {
    global $plp_update;

    if($this->on_pretty_link_page()) {
      $dismissed = get_option( 'pl_dismiss_upgrade_header', false );

      if(empty($dismissed) && !$plp_update->is_installed()) : ?>
        <div class="pl-upgrade-header" id="pl-upgrade-header">
          <span id="close-pl-upgrade-header">X</span>
          <?php _e( 'You\'re using Pretty Links Lite. To unlock more features, consider <a href="https://prettylinks.com/pl/main-menu/upgrade?plugin-upgrade-header">upgrading to Pro.</a>', 'pretty-link' ); ?>
        </div>
        <script>
          jQuery(document).ready(function($) {
            $('#close-pl-upgrade-header').on('click', function() {
              var upgradeHeader = $('#pl-upgrade-header');
              upgradeHeader.fadeOut();
              $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                  action: 'pl_dismiss_upgrade_header',
                  nonce: "<?php echo wp_create_nonce( 'pl_dismiss_upgrade_header' ); ?>"
                },
              })
              .done(function() {
                console.log("success");
              })
              .fail(function() {
                console.log("error");
              })
              .always(function() {
                console.log("complete");
              });
            });
          });
        </script>
      <?php endif; ?>
      <div id="pl-admin-header">
          <img class="pl-logo" src="<?php echo PRLI_IMAGES_URL . '/pretty-links-logo-color-white.svg'; ?>" />
          <div class="pl-admin-header-actions">
              <?php do_action('prli_admin_header_actions'); ?>
          </div>
          <?php do_action('prli_admin_header'); ?>
      </div>
      <?php
    }
  }

  public function dismiss_notice() {
    if(check_ajax_referer('prli_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
      $notice = sanitize_key($_POST['notice']);
      update_option("prli_dismiss_notice_$notice", true);
    }

    wp_send_json_success();
  }

  public function dismiss_daily_notice() {
    if(check_ajax_referer('prli_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
      $notice = sanitize_key($_POST['notice']);
      set_transient("prli_dismiss_notice_{$notice}", true, DAY_IN_SECONDS);
    }

    wp_send_json_success();
  }

  public function dismiss_monthly_notice() {
    if(check_ajax_referer('prli_dismiss_notice', false, false) && isset($_POST['notice']) && is_string($_POST['notice'])) {
      $notice = sanitize_key($_POST['notice']);
      set_transient("prli_dismiss_notice_{$notice}", true, DAY_IN_SECONDS * 30);
    }

    wp_send_json_success();
  }

  private function on_pretty_link_page() {
    global $current_screen;
    return (isset($current_screen->id) && strpos($current_screen->id,'pretty-link') !== false);
  }

  public function menu() {
    global $prli_options, $plp_options, $plp_update;

    $this->admin_separator();

    $role = PrliUtils::get_minimum_role();

    $pl_link_cpt = PrliLink::$cpt;

    add_submenu_page(
      "edit.php?post_type={$pl_link_cpt}",
      esc_html__('PrettyPay™ Links', 'pretty-link'),
      esc_html__('PrettyPay™ Links', 'pretty-link') . PrliUtils::new_badge(),
      $role,
      'prettypay-links',
      PrliStripeHelper::is_connection_active() ? '__return_empty_string' : array(PrliLinksController::class, 'show_prettypay_popup')
    );

    if(!$plp_update->is_installed()) {
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Link Categories', 'pretty-link'),
        esc_html__('Categories', 'pretty-link'),
        $role,
        "pretty-link-upgrade-categories",
        array( $plp_update, 'upgrade_categories' )
      );
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Link Tags', 'pretty-link'),
        esc_html__('Tags', 'pretty-link'),
        $role,
        "pretty-link-upgrade-tags",
        array( $plp_update, 'upgrade_tags' )
      );
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Link Reports', 'pretty-link'),
        esc_html__('Reports', 'pretty-link'),
        $role,
        "pretty-link-upgrade-reports",
        array( $plp_update, 'upgrade_reports' )
      );
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Display Groups', 'pretty-link'),
        esc_html__('Display Groups', 'pretty-link'),
        $role,
        "pretty-link-upgrade-groups",
        array( $plp_update, 'upgrade_groups' )
      );
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Product Displays', 'pretty-link'),
        esc_html__('Product Displays', 'pretty-link'),
        $role,
        "pretty-link-upgrade-products",
        array( $plp_update, 'upgrade_products' )
      );
      add_submenu_page(
        "edit.php?post_type={$pl_link_cpt}",
        esc_html__('Import / Export', 'pretty-link'),
        esc_html__('Import / Export', 'pretty-link'),
        $role,
        "pretty-link-upgrade-import-export",
        array( $plp_update, 'upgrade_import_export' )
      );
    }

    if( isset($prli_options->extended_tracking) and $prli_options->extended_tracking != 'count' ) {
      $clicks_ctrl = new PrliClicksController();
      add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Pretty Links | Clicks', 'pretty-link'), esc_html__('Clicks', 'pretty-link'), $role, 'pretty-link-clicks', array( $clicks_ctrl, 'route' ) );
    }

    $routes_ctrl = new PrliToolsController();
    add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Pretty Links | Tools', 'pretty-link'), esc_html__('Tools', 'pretty-link'), $role, 'pretty-link-tools', array($routes_ctrl,'route') );

    $options_ctrl = new PrliOptionsController();
    add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Pretty Links | Options', 'pretty-link'), esc_html__('Options', 'pretty-link'), $role, 'pretty-link-options', array( $options_ctrl, 'route' ));

    if(!defined('PRETTYLINK_LICENSE_KEY') && class_exists('PrliUpdateController')) {
      if($plp_update->is_installed_and_activated()) {
        add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Activate', 'pretty-link'), esc_html__('Activate', 'pretty-link'), $role, 'pretty-link-updates', array($plp_update, 'route'));
      }
      else if($plp_update->is_installed()) {
        add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Activate', 'pretty-link'), '<span class="prli-menu-red"><b>'.esc_html__('Activate', 'pretty-link').'</b></span>', $role, 'pretty-link-updates', array($plp_update, 'route'));
      }
      else {
        add_submenu_page( "edit.php?post_type={$pl_link_cpt}", esc_html__('Upgrade', 'pretty-link'), '<span class="prli-menu-red"><b>'.esc_html__('Upgrade', 'pretty-link').'</b></span>', $role, 'pretty-link-updates', array($plp_update, 'route'));
      }
    }

    add_submenu_page('options.php', __('Welcome', 'pretty-link'), null, $role, 'pretty-link-onboarding', 'PrliOnboardingController::route');

    $addons_ctrl = new PrliAddonsController();
    add_submenu_page("edit.php?post_type={$pl_link_cpt}", esc_html__('Add-ons', 'pretty-link'), '<span style="color:#8CBD5A;">' . esc_html__('Add-ons', 'pretty-link') . '</span>', $role, 'pretty-link-addons', array($addons_ctrl, 'route'));
  }

  /**
   * Add a separator to the WordPress admin menus
   */
  public function admin_separator()
  {
    global $menu;

    // Prevent duplicate separators when no core menu items exist
    if(!PrliUtils::is_authorized()) { return; }

    $menu[] = array('', 'read', 'separator-pretty-link', '', 'wp-menu-separator pretty-link');
  }

  /*
   * Move our custom separator above our admin menu
   *
   * @param array $menu_order Menu Order
   * @return array Modified menu order
   */
  public function admin_menu_order($menu_order) {
    if(!is_array($menu_order)) {
      return $menu_order;
    }

    // Initialize our custom order array
    $new_menu_order = array();

    // Menu values
    $second_sep   = 'separator2';
    $pl_link_cpt = PrliLink::$cpt;
    $custom_menus = array('separator-pretty-link', "edit.php?post_type={$pl_link_cpt}");

    // Loop through menu order and do some rearranging
    foreach($menu_order as $item) {
      // Position Pretty Links menu above appearance
      if($second_sep == $item) {
        // Add our custom menus
        foreach($custom_menus as $custom_menu) {
          if(array_search($custom_menu, $menu_order)) {
            $new_menu_order[] = $custom_menu;
          }
        }
        // Add the appearance separator
        $new_menu_order[] = $second_sep;
      // Skip our menu items down below
      }
      elseif(!in_array($item, $custom_menus)) {
        $new_menu_order[] = $item;
      }
    }

    // Return our custom order
    return $new_menu_order;
  }

  //Organize the CPT's in our submenu
  public function admin_submenu_order($menu_order) {
    global $submenu;

    static $run = false;

    //no sense in running this everytime the hook gets called
    if($run) { return $menu_order; }

    $pl_link_cpt = PrliLink::$cpt;
    $slug = "edit.php?post_type={$pl_link_cpt}";

    //just return if there's no pretty-link menu available for the current screen
    if(!isset($submenu[$slug])) { return $menu_order; }

    $run = true;
    $new_order = array();

    $categories_ctax = class_exists('PlpLinkCategoriesController') ? PlpLinkCategoriesController::$ctax : 'pretty-link-category';
    $tags_ctax = class_exists('PlpLinkTagsController') ? PlpLinkTagsController::$ctax : 'pretty-link-tag';
    $groups_cpt = class_exists('\Pretty_Link\Product_Displays\Controllers\GroupsCtrl') ? \Pretty_Link\Product_Displays\Models\Group::$cpt : 'pretty-link-groups';
    $products_cpt = class_exists('\Pretty_Link\Product_Displays\Controllers\ProductsCtrl') ? \Pretty_Link\Product_Displays\Models\Product::$cpt : 'pretty-link-products';

    $include_array = array(
      $slug,
      'prettypay-links',
      "post-new.php?post_type={$pl_link_cpt}",
      "edit.php?post_type={$pl_link_cpt}&page=pretty-link-upgrade-categories",
      "edit.php?post_type={$pl_link_cpt}&page=pretty-link-upgrade-tags",
      "edit-tags.php?taxonomy={$categories_ctax}&amp;post_type={$pl_link_cpt}",
      'https://prettylinks.com/pl/main-menu/upgrade?categories',
      "edit-tags.php?taxonomy={$tags_ctax}&amp;post_type={$pl_link_cpt}",
      'https://prettylinks.com/pl/main-menu/upgrade?tags',
      'pretty-link-clicks',
      'plp-reports',
      'https://prettylinks.com/pl/main-menu/upgrade?reports',
      "edit.php?post_type={$groups_cpt}",
      "post-new.php?post_type={$groups_cpt}",
      "edit.php?post_type={$products_cpt}",
      "post-new.php?post_type={$products_cpt}",
      'pretty-link-tools',
      'pretty-link-options',
      'plp-import-export',
      'https://prettylinks.com/pl/main-menu/upgrade?import-export',
      'pretty-link-updates'
    );

    $i = count($include_array);

    foreach($submenu[$slug] as $sub) {
      $include_order = array_search($sub[2], $include_array);

      if($sub[2] == $slug && !empty($_REQUEST['prettypay'])) {
        $sub[0] = esc_html__('Pretty Links', 'pretty-link');
      }
      elseif($sub[2] == 'prettypay-links' && PrliStripeHelper::is_connection_active()) {
        $sub[2] = $slug . '&amp;prettypay=1';
      }

      if(false !== $include_order) {
        $new_order[$include_order] = $sub;
      }
      else {
        $new_order[$i++] = $sub;
      }
    }

    ksort($new_order);

    $submenu[$slug] = $new_order;

    return $menu_order;
  }

  public function add_plugin_action_links($links) {
    global $plp_update;

    $pllinks = array();

    if($plp_update->is_installed_and_activated()) {
      $pllinks[] = '<a href="https://prettylinks.com/pl/plugin-actions/activated/docs" target="_blank">'.esc_html__('Docs', 'pretty-link').'</a>';
      $pllinks[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=pretty-link-updates') ) .'">'.esc_html__('Manage License', 'pretty-link').'</a>';
    }
    else if($plp_update->is_installed()) {
      $pllinks[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=pretty-link-updates') ) .'" class="prli-menu-green">'.esc_html__('Activate Pro License', 'pretty-link').'</a>';
      $pllinks[] = '<a href="https://prettylinks.com/pl/plugin-actions/installed/buy" target="_blank" class="prli-menu-red">'.esc_html__('Buy', 'pretty-link').'</a>';
      $pllinks[] = '<a href="https://prettylinks.com/pl/plugin-actions/installed/docs" target="_blank">'.esc_html__('Docs', 'pretty-link').'</a>';
    }
    else {
      $pllinks[] = '<a href="https://prettylinks.com/pl/plugin-actions/lite/upgrade" class="prli-menu-red" target="_blank">'.esc_html__('Upgrade to Pro', 'pretty-link').'</a>';
      $pllinks[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=pretty-link-updates') ) .'" class="prli-menu-green">'.esc_html__('Activate Pro License', 'pretty-link').'</a>';
      $pllinks[] = '<a href="https://prettylinks.com/pl/plugin-actions/lite/docs" target="_blank">'.esc_html__('Docs', 'pretty-link').'</a>';
    }

    return array_merge($pllinks, $links);
  }

  public function enqueue_admin_scripts($hook) {
    global $wp_version, $current_screen, $plp_update;

    wp_enqueue_style( 'prli-fontello-pretty-link',
                      PRLI_VENDOR_LIB_URL.'/fontello/css/pretty-link.css',
                      array(), PRLI_VERSION );

    if($this->should_enqueue_block_editor_scripts()) {
      $asset = include PRLI_JS_PATH . '/build/editor.asset.php';

      wp_enqueue_script(
        'pretty-link-richtext-format',
        PRLI_JS_URL . '/build/editor.js',
        $asset['dependencies'],
        $asset['version'],
        true
      );

      wp_localize_script('pretty-link-richtext-format', 'plEditor', array(
        'homeUrl' => trailingslashit(get_home_url()),
        'prli_create_link_nonce' => wp_create_nonce('prli_create_link_nonce')
      ));

      do_action('prli_enqueue_block_scripts', $asset['dependencies']);
    }

    // If we're in 3.8 now then use a font for the admin image
    if( version_compare( $wp_version, '3.8', '>=' ) ) {
      wp_enqueue_style( 'prli-menu-styles', PRLI_CSS_URL.'/menu-styles.css',
                        array('prli-fontello-pretty-link'), PRLI_VERSION );
    }

    wp_enqueue_style('prli-admin-global', PRLI_CSS_URL . '/admin_global.css', array(), PRLI_VERSION);
    wp_enqueue_script('prli-admin-global', PRLI_JS_URL . '/admin_global.js', array('jquery'), PRLI_VERSION);

    wp_localize_script('prli-admin-global', 'PrliAdminGlobal', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'dismiss_notice_nonce' => wp_create_nonce('prli_dismiss_notice')
    ));

    $is_pl_page           = $this->is_pretty_link_page();
    $is_link_page         = $this->is_pretty_link_link_page();
    $is_link_listing_page = $this->is_pretty_link_listing_page();
    $is_link_edit_page    = $this->is_pretty_link_edit_page();
    $is_link_new_page     = $this->is_pretty_link_new_page();

    if( $is_pl_page || $is_link_page ) {
      $prli_admin_shared_prereqs = array( 'wp-pointer' );

      if(!$is_link_listing_page) {
        wp_register_style('pl-ui-smoothness', PRLI_VENDOR_LIB_URL.'/jquery-ui/jquery-ui.min.css', array(), '1.11.4');
        wp_register_style('prli-social', PRLI_CSS_URL.'/social_buttons.css', array(), PRLI_VERSION);

        $prli_admin_shared_prereqs = array_merge(
          $prli_admin_shared_prereqs,
          array(
            'pl-ui-smoothness',
            'prli-social',
          )
        );
      }

      wp_enqueue_style(
        'prli-admin-shared',
        PRLI_CSS_URL.'/admin_shared.css',
        $prli_admin_shared_prereqs,
        PRLI_VERSION
      );

      wp_enqueue_script(
        'prli-admin-shared',
        PRLI_JS_URL.'/admin_shared.js',
        array(
          'jquery',
          'jquery-ui-datepicker',
          'jquery-ui-sortable',
        ),
        PRLI_VERSION
      );

      if($is_link_edit_page || $is_link_new_page) {
        global $prli_link, $post, $prli_blogurl;

        $link_id = $prli_link->get_link_from_cpt($post->ID);

        $args = array(
          'args' => array(
            'id' => $link_id,
            'action' => 'validate_pretty_link',
            'security' => wp_create_nonce( 'validate_pretty_link' ),
            'update' => __('Update', 'pretty-link')
          ),
          'copy_text' => __('Copy to Clipboard', 'pretty-link'),
          'copied_text' => __('Copied!', 'pretty-link'),
          'copy_error_text' => __('Oops, Copy Failed!', 'pretty-link'),
          'blogurl' => $prli_blogurl,
          'permalink_pre_slug_uri' => PrliUtils::get_permalink_pre_slug_uri()
        );

        wp_enqueue_script( 'prli-link-form', PRLI_JS_URL . '/admin_link_form.js', array(), PRLI_VERSION);
        wp_localize_script( 'prli-link-form', 'PrliLinkValidation', $args );

        wp_dequeue_script('autosave'); // Disable auto-saving
      }
    }

    if($current_screen->post_type == PrliLink::$cpt) {
      wp_enqueue_style( 'prli-admin-links', PRLI_CSS_URL . '/prli-admin-links.css', array(), PRLI_VERSION );
      //wp_enqueue_script( 'jquery-clippy', PRLI_JS_URL . '/jquery.clippy.js', array('jquery'), PRLI_VERSION );
      wp_enqueue_script( 'clipboard-js', PRLI_JS_URL . '/clipboard.min.js', null, PRLI_VERSION );
      wp_enqueue_script( 'jquery-tooltipster', PRLI_JS_URL . '/tooltipster.bundle.min.js', array('jquery'), PRLI_VERSION );
      wp_enqueue_style( 'clipboardtip', PRLI_CSS_URL . '/tooltipster.bundle.min.css', null, PRLI_VERSION );
      wp_enqueue_style( 'clipboardtip-borderless', PRLI_CSS_URL . '/tooltipster-sideTip-borderless.min.css', array('clipboardtip'), PRLI_VERSION );

      wp_enqueue_script( 'prli-admin-links', PRLI_JS_URL . '/prli-admin-links.js', array('jquery','clipboard-js','jquery-tooltipster'), PRLI_VERSION );

      wp_enqueue_script( 'prli-admin-link-list', PRLI_JS_URL . '/admin_link_list.js', array('jquery','clipboard-js','jquery-tooltipster'), PRLI_VERSION );
      $links_js_obj = array(
        'reset_str' => __('Are you sure you want to reset your Pretty Link? This will delete all of the statistical data about this Pretty Link in your database.', 'pretty-link'),
        'reset_security' => wp_create_nonce('reset_pretty_link'),
        'broken_link_text' => sprintf(
          __('To access Link Health, upgrade to <a href="%s" class="prli-link-health-upgrade">Pretty Links Pro.</a>', 'pretty-link'),
          esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-updates#prli_upgrade'))
        ),
        'nonce' => wp_create_nonce('prli_admin_link_list_nonce')
      );
      wp_localize_script( 'prli-admin-link-list', 'PrliLinkList', $links_js_obj );

      if($is_link_edit_page) {
        wp_enqueue_style('fontello-animation', PRLI_VENDOR_LIB_URL.'/fontello/css/animation.css', array(), PRLI_VERSION);
        wp_enqueue_style('select2', PRLI_JS_URL . '/vendor/select2/select2.min.css', array(), '4.0.13');
        wp_enqueue_style('select2-prli-theme', PRLI_CSS_URL . '/select2-prli-theme.css', array(), PRLI_VERSION);
        wp_enqueue_script('select2', PRLI_JS_URL . '/vendor/select2/select2.min.js', array(), '4.0.13', true);
        wp_enqueue_script('pl-prettypay-link-stripe', PRLI_JS_URL . '/admin_prettypay_link_stripe.js', array('jquery'), PRLI_VERSION, true);

        wp_localize_script('pl-prettypay-link-stripe', 'PrliPrettyPayLinkStripe', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'find_or_add_product' => __('Find or add a product...', 'pretty-link'),
          'search_stripe_prices_nonce' => wp_create_nonce('prli_search_stripe_prices'),
          'select_shipping_countries' => __('Select shipping countries', 'pretty-link') . '*',
          'add_product_nonce' => wp_create_nonce('prli_stripe_add_product'),
          'this_field_is_required' => __('This field is required', 'pretty-link'),
        ));
      }
    }

    if( preg_match('/_page_pretty-link-options$/', $hook) ) {
      wp_enqueue_style('fontello-animation', PRLI_VENDOR_LIB_URL.'/fontello/css/animation.css', array(), PRLI_VERSION);
      wp_enqueue_style('select2', PRLI_JS_URL . '/vendor/select2/select2.min.css', array(), '4.0.13');
      wp_enqueue_style('select2-prli-theme', PRLI_CSS_URL . '/select2-prli-theme.css', array(), PRLI_VERSION);
      wp_enqueue_script('select2', PRLI_JS_URL . '/vendor/select2/select2.min.js', array(), '4.0.13', true);
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style('pl-options', PRLI_CSS_URL.'/admin_options.css', null, PRLI_VERSION);
      wp_enqueue_script('pl-options', PRLI_JS_URL.'/admin_options.js', array('jquery'), PRLI_VERSION);
    }

    if( preg_match('/_page_pretty-link-tools$/', $hook) ||
        preg_match('/_page_pretty-link-options$/', $hook) ||
        $current_screen->post_type == PrliLink::$cpt ) {
      wp_enqueue_style('pl-settings-table', PRLI_CSS_URL.'/settings_table.css', null, PRLI_VERSION);
      wp_enqueue_script('pl-settings-table', PRLI_JS_URL.'/settings_table.js', array('jquery'), PRLI_VERSION);
    }

    if( preg_match('/_page_pretty-link-clicks$/', $hook) ) {
      wp_enqueue_script('google-visualization-api', 'https://www.gstatic.com/charts/loader.js', null, PRLI_VERSION);
      wp_enqueue_style('pl-reports', PRLI_CSS_URL.'/admin_reports.css', null, PRLI_VERSION);
      wp_enqueue_script('pl-reports', PRLI_JS_URL.'/admin_reports.js', array('jquery','google-visualization-api'), PRLI_VERSION);
      wp_localize_script('pl-reports', 'PrliReport', PrliReportsController::chart_data());
    }

    if ( $current_screen->post_type == PrliLink::$cpt || preg_match('/_page_pretty-link-(tools|options|clicks)$/', $hook ) ){
      wp_enqueue_script('prli-popper-js', PRLI_VENDOR_LIB_URL . '/popperjs/popper.min.js', array('jquery'), PRLI_VERSION, true);
      wp_enqueue_script('prli-tippy-js', PRLI_VENDOR_LIB_URL . '/tippy.js/tippy-bundle.umd.min.js', array('jquery', 'prli-popper-js'), PRLI_VERSION, true);
      wp_enqueue_script(
          'prli-tooltip',
          PRLI_JS_URL.'/tooltip.js',
          array('jquery'),
          PRLI_VERSION
        );
    }

    $page_vars = compact('is_pl_page', 'is_link_page', 'is_link_listing_page', 'is_link_edit_page', 'is_link_new_page');
    do_action('prli_load_admin_scripts', $hook, $page_vars);
  }

  /**
   * Should we enqueue the block editor scripts?
   *
   * @return bool
   */
  private function should_enqueue_block_editor_scripts() {
    global $wp_version;

    if (version_compare($wp_version, '5.2', '>=')) {
      $screen = get_current_screen();

      if ($screen instanceof WP_Screen && method_exists($screen, 'is_block_editor')) {
        return $screen->is_block_editor();
      }
    }

    return false;
  }

  public function parse_standalone_request() {
    if( !empty($_REQUEST['plugin']) and $_REQUEST['plugin'] == 'pretty-link' and
        !empty($_REQUEST['controller']) and !empty($_REQUEST['action']) ) {
      $this->standalone_route($_REQUEST['controller'], $_REQUEST['action']);
      do_action('prli-standalone-route');
      exit;
    }
    else if( !empty($_GET['action']) and $_GET['action']=='prli_bookmarklet' ) {
      PrliToolsController::standalone_route();
      exit;
    }
  }

  public function standalone_route($controller, $action) {
    return; // Nothing here now that we've moved DB upgrade out of here
  }

  public static function install() {
    global $plp_update, $prli_utils;
    $prli_db = new PrliDb();

    if($prli_db->should_install()) {
      // For some reason, install gets called multiple times so we're basically
      // adding a mutex here (ala a transient) to ensure this only gets run once
      $is_installing = get_transient('prli_installing');
      if($is_installing) {
        return;
      }
      else {
        // 30 minutes
        set_transient('prli_installing', 1, 60*30);
      }

      @ignore_user_abort(true);

      if(function_exists('set_time_limit')) {
        @set_time_limit(0);
      }

      $prli_db->prli_install();

      delete_transient('prli_installing');
    }
  }

  public function pro_settings_submenu() {
    global $wpdb, $prli_utils, $plp_update, $prli_db_version;

    if(isset($_GET['action']) && $_GET['action'] == 'force-pro-reinstall') {
      // Queue the update and auto upgrade
      $plp_update->manually_queue_update();
      $reinstall_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=pretty-link/pretty-link.php', 'upgrade-plugin_pretty-link/pretty-link.php');
      ?>

      <div class="updated notice notice-success">
        <p>
          <strong>
            <?php
              printf(
                // translators: %1$s: br tag, %2$s: open link tag, %3$s: close link tag
                esc_html__('You\'re almost done!%1$s%2$sFinish your Re-Install of Pretty Links Pro%3$s', 'pretty-link'),
                '<br>',
                '<a href="'.esc_url($reinstall_url).'">',
                '</a>'
              );
            ?>
          </strong>
        </p>
      </div>
      <?php
    }

    if(isset($_GET['action']) and $_GET['action'] == 'pro-uninstall') {
      $prli_utils->uninstall_pro();
      ?>
      <div class="updated notice notice-success is-dismissible"><p><strong><?php esc_html_e('Pretty Links Pro Successfully Uninstalled.' , 'pretty-link'); ?></strong></p></div>
      <?php
    }

    require_once(PRLI_VIEWS_PATH.'/options/pro-settings.php');
  }

  /********* ADD REDIRECTS FOR STANDARD MODE ***********/
  public function redirect() {
    // Bail out early if we're performing a search.
    if(isset($_GET['s']) && !empty($_GET['s'])) {
      return;
    }

    global $prli_link;

    // Remove the trailing slash if there is one
    $request_uri = preg_replace('#/(\?.*)?$#', '$1', rawurldecode($_SERVER['REQUEST_URI']));

    if($link_info = $prli_link->is_pretty_link($request_uri,false)) {
      $params = (isset($link_info['pretty_link_params'])?$link_info['pretty_link_params']:'');
      $this->link_redirect_from_slug( $link_info['pretty_link_found']->slug, $params );
    }
  }

  // For use with the redirect function
  public function link_redirect_from_slug($slug,$param_str) {
    global $prli_link, $prli_utils;

    $link = $prli_link->getOneFromSlug(rawurldecode($slug));

    if(isset($link->slug) and !empty($link->slug)) {
      $custom_get = $_GET;

      /* Don't do any custom param forwarding now
        if(isset($link->param_forwarding) and $link->param_forwarding == 'custom')
          $custom_get = $prli_utils->decode_custom_param_str($link->param_struct, $param_str);
      */

      $success = $prli_utils->track_link($link->slug, $custom_get);

      if($success) { exit; }
    }
  }

  /********* DASHBOARD WIDGET ***********/
  public function dashboard_widget_function() {
    global $prli_link, $prli_blogurl;

    wp_enqueue_script('prli-quick-create', PRLI_JS_URL . '/quick_create.js', array('jquery'), PRLI_VERSION, true);

    wp_localize_script('prli-quick-create', 'PrliQuickCreate', array(
      'nonce' => wp_create_nonce('prli_quick_create'),
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'invalidServerResponse' => __('Invalid server response', 'pretty-link'),
      'ajaxError' => __('Ajax error', 'pretty-link')
    ));

    require_once(PRLI_VIEWS_PATH . '/widgets/widget.php');
  }

  // Create the function use in the action hook
  public function add_dashboard_widgets() {
    global $plp_options;
    $current_user = PrliUtils::get_currentuserinfo();

    $role = 'administrator';
    if(isset($plp_options->min_role)) {
      $role = $plp_options->min_role;
    }

    if(current_user_can($role)) {
      wp_add_dashboard_widget('prli_dashboard_widget', esc_html__('Pretty Link Quick Add', 'pretty-link'), array($this,'dashboard_widget_function'));

      // Globalize the metaboxes array, this holds all the widgets for wp-admin
      global $wp_meta_boxes;

      // Get the regular dashboard widgets array
      $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

      // Backup and delete our new dashbaord widget from the end of the array
      $prli_widget_backup = array('prli_dashboard_widget' => $normal_dashboard['prli_dashboard_widget']);
      unset($normal_dashboard['prli_dashboard_widget']);

      // Merge the two arrays together so our widget is at the beginning
      $i = 0;
      foreach($normal_dashboard as $key => $value) {
        if($i == 1 or (count($normal_dashboard) <= 1 and $i == count($normal_dashboard) - 1)) {
          $sorted_dashboard['prli_dashboard_widget'] = $prli_widget_backup['prli_dashboard_widget'];
        }

        $sorted_dashboard[$key] = $normal_dashboard[$key];
        $i++;
      }

      // Save the sorted array back into the original metaboxes
      $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }
  }

  public function pro_action_needed( $plugin ) {
    global $plp_update;

    if( $plugin == 'pretty-link/pretty-link.php' && $plp_update->is_activated() && !$plp_update->is_installed() ) {
      $plp_update->manually_queue_update();
      $inst_install_url = $plp_update->update_plugin_url();

      ?>
        <tr class="plugin-update-tr active" id="pretty-link-upgrade" data-slug="pretty-link" data-plugin="pretty-link/pretty-link.php">
          <td colspan="3" class="plugin-update colspanchange">
            <div class="update-message notice inline notice-error notice-alt">
              <p><?php printf(__('Your Pretty Links Pro installation isn\'t quite complete yet. %1$sAutomatically Upgrade to Enable Pretty Links Pro%2$s', 'pretty-link'), '<a href="'.$inst_install_url.'">', '</a>'); ?></p>
            </div>
          </td>
        </tr>
      <?php
    }
  }

  public function pro_get_started_headline() {
    global $plp_update;

    // Don't display this error as we're upgrading the thing... cmon
    if(isset($_GET['action']) && $_GET['action'] == 'upgrade-plugin') {
      return;
    }

    if( $plp_update->is_activated() && !$plp_update->is_installed()) {
      $plp_update->manually_queue_update();
      $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . PRLI_PLUGIN_SLUG, 'upgrade-plugin_' . PRLI_PLUGIN_SLUG);

      ?>
        <div class="error" style="padding-top: 5px; padding-bottom: 5px;"><?php printf(__('Your Pretty Links Pro installation isn\'t quite complete yet.<br/>%1$sAutomatically Upgrade to Enable Pretty Links Pro%2$s', 'pretty-link'), '<a href="'.$inst_install_url.'">','</a>'); ?></div>
      <?php
    }
  }

  public function show_about_notice() {
    $last_shown_notice = get_option('prli_about_notice_version');
    $version_str = preg_replace('/\./','-',PRLI_VERSION);
    return ( $last_shown_notice != PRLI_VERSION and
             file_exists( PRLI_VIEWS_PATH . "/about/{$version_str}.php" ) );
  }

  public function about_notice() {
    $version_str  = preg_replace('/\./','-',PRLI_VERSION);
    $version_file = PRLI_VIEWS_PATH . "/about/{$version_str}.php";

    if( file_exists( $version_file ) ) {
      ob_start();
      require_once($version_file);
      return ob_get_clean();
    }

    return '';
  }

  public static function close_about_notice() {
    update_option('prli_about_notice_version',PRLI_VERSION);
    wp_cache_delete('alloptions', 'options');
  }

  /**
   * When user is on a Pretty Links related admin page, display footer text
   * that graciously asks them to rate us.
   *
   * @since 1.4.0
   *
   * @param string $text
   *
   * @return string
   */
  public function admin_footer($text) {
    global $current_screen;

    if(!empty($current_screen->id) && $this->is_pretty_link_page()) {
      $url  = 'https://prettylinks.com/pl/footer/review';
      $text = sprintf(
        wp_kses(
          /* translators: $1$s - Pretty Links plugin name; $2$s - WP.org review link; $3$s - WP.org review link. */
          __('Enjoying %1$s? Please rate <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thanks from the Pretty Links team!', 'pretty-link'),
          array(
            'a' => array(
              'href'   => array(),
              'target' => array(),
              'rel'    => array(),
            ),
          )
        ),
        '<strong>Pretty Links</strong>',
        $url,
        $url
      );
    }

    return $text;
  }

  /**
   * Pre-footer promotion block displayed on all Pretty Links admin pages.
   *
   * @access public
   * @return void
   */
  public function promote_pretty_links() {
    global $current_screen, $plp_update;

    if(empty($current_screen->id) || !$this->is_pretty_link_page()) {
      return;
    }

    $links = array(
      array(
        'url' => $plp_update->is_installed() ? 'https://prettylinks.com/premium-support-request/' : 'https://wordpress.org/support/plugin/pretty-link/',
        'text' => __('Support', 'pretty-link'),
        'target' => '_blank'
      ),
      array(
        'url' => 'https://prettylinks.com/docs/',
        'text' => __('Docs', 'pretty-link'),
        'target' => '_blank'
      )
    );

    $title = __('Made with ♥ by the Pretty Links Team', 'pretty-link');

    require_once(PRLI_VIEWS_PATH . '/admin/promotion.php');
  }

  private function get_screen_id($hook=null) {
    if(is_null($hook)) {
      $screen = get_current_screen();
      $hook = $screen->id;
    }

    return $hook;
  }

  public function is_pretty_link_page() {
    $hook = $this->get_screen_id();
    return (strstr($hook, 'pretty-link') !== false);
  }

  public function is_pretty_link_link_page() {
    $hook = $this->get_screen_id();
    return in_array($hook, array($this->screens['add-edit'],$this->screens['list']));
  }

  public function is_pretty_link_listing_page() {
    $hook = $this->get_screen_id();
    return ($hook == $this->screens['list']);
  }

  public function is_pretty_link_edit_page() {
    $hook = $this->get_screen_id();
    return ($hook == $this->screens['add-edit']);
  }

  public function is_pretty_link_edit_tags() {
    $hook = $this->get_screen_id();
    return ($hook == $this->screens['tag']);
  }

  public function is_pretty_link_new_page() {
    $hook = $this->get_screen_id();
    return ($hook == $this->screens['add-edit']);
  }

  public function add_post_states($post_states, $post) {
    global $prli_options;

    if($prli_options->prettypay_thank_you_page_id == $post->ID) {
      $post_states['prli_prettypay_thank_you_page'] = __('PrettyPay™ Thank You Page', 'pretty-link');
    }

    return $post_states;
  }
}
