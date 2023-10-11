<?php
if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' );}

class PrliOnboardingHelper {
  public static function maybe_set_steps_completed($step) {
    $steps_completed = self::get_steps_completed();
    if( $step > $steps_completed ){
      self::set_steps_completed($step);
    }
  }

  public static function set_steps_completed($step) {
    update_option( 'prli_onboarding_steps_completed', $step, false );

    if( $step == 0 ){
      self::unmark_content_steps_skipped();
    }
  }

  public static function get_steps_completed() {
    return get_option( 'prli_onboarding_steps_completed', 0 );
  }

  public static function set_selected_features($features) {
    update_option('prli_onboarding_features', $features, false);
  }

  public static function get_selected_features_data() {
    $metadata = get_option('prli_onboarding_features', true);
    $data = is_array($metadata) ? $metadata : array();
    return $data;
  }

  public static function get_selected_features() {
    $data = self::get_selected_features_data();
    $features = (isset($data['features']) && is_array($data['features'])) ? $data['features'] : array();
    return $features;
  }

  public static function is_addon_selectable($plugin_slug) {
    $plugins = get_plugins();
    $plugin_file_slug = $plugin_slug . '.php';
    $is_installed = ! empty($plugins[$plugin_file_slug]);
    $selectable = true;
    if($is_installed){
      if(is_plugin_active($plugin_file_slug)){ // if addon is already installed and active, it must not be selectable.
          $selectable = false;
      }
    }

    return $selectable;
  }

  public static function features_addons_selectable_list() {
    return array(
      'pretty-link-product-displays' => PrliOnboardingHelper::is_addon_selectable('pretty-link-product-displays/pretty-link-product-displays'),
      'monsterinsights'              => PrliOnboardingHelper::is_addon_selectable('google-analytics-for-wordpress/googleanalytics'),
    );
  }

  public static function set_link_id($id) {
    update_option( 'prli_onboarding_link_id', $id, false );

    if(count(self::get_skipped_steps())) {
      self::unmark_content_steps_skipped();
      self::set_steps_completed(3);
    }
  }

  public static function get_link_id() {
    return get_option( 'prli_onboarding_link_id', 0 );
  }

  public static function set_has_imported_links($value) {
    update_option('prli_onboarding_has_imported_links', 1, false);

    if(count(self::get_skipped_steps())) {
      self::unmark_content_steps_skipped();
      self::set_steps_completed(3);
    }
  }

  public static function get_has_imported_links() {
    return get_option('prli_onboarding_has_imported_links', 0);
  }

  public static function mark_content_steps_skipped() {
    update_option( 'prli_onboarding_content_steps_skipped', 1, false );
  }

  public static function unmark_content_steps_skipped() {
    update_option( 'prli_onboarding_content_steps_skipped', 0, false );
  }

  public static function set_category_id($id) {
    update_option('prli_onboarding_category_id', $id, false);

    if(count(self::get_skipped_steps()) && $id > 0) {
      self::unmark_content_steps_skipped();
      $content_id = self::get_link_id();
      if($content_id > 0) {
        self::set_steps_completed(3);
      }
    }
  }

  public static function get_category_id() {
    return get_option( 'prli_onboarding_category_id', 0 );
  }

  public static function get_skipped_steps() {
    $is_skipped = get_option( 'prli_onboarding_content_steps_skipped', 0 );

    if($is_skipped) {
      return array(3, 4);
    }
    return array();
  }

  public static function features_list() {
    return array(
      'pretty-link-qr-codes' => esc_html__('QR Codes', 'pretty-link'),
      'pretty-link-link-health' => esc_html__('Link Health', 'pretty-link'),
      'pretty-link-replacements' => esc_html__('Replacements', 'pretty-link'),
      'pretty-link-import-export' => esc_html__('Import/Export Links', 'pretty-link'),
      'pretty-link-product-displays' => esc_html__('Pretty Links Product Displays', 'pretty-link'),
      'monsterinsights' => esc_html__('MonsterInsights', 'pretty-link')
    );
  }

  public static function get_license_type() {
    $li = get_site_transient('prli_license_info');

    if($li) {
      return $li['product_slug'];
    }

    return false;
  }

  public static function get_completed_step_urls_html() {
    ob_start();
    ?>
    <?php if((int) get_option('prli_onboarding_content_steps_skipped') !== 1): ?>
      <h2 class="prli-wizard-step-title"><?php esc_html_e('Check out what you set up...', 'pretty-link'); ?></h2>
      <div class="prli-wizard-selected-content prli-wizard-selected-content-full-scape">
        <div id="prli-wizard-completed-step-urls">
          <?php
            global $prli_link;
            $category_id = PrliOnboardingHelper::get_category_id();
            $link_id = PrliOnboardingHelper::get_link_id();
            $has_imported_links = PrliOnboardingHelper::get_has_imported_links();
          ?>
          <?php
            if($link_id > 0 && !$has_imported_links):
              $pretty_link_id = $prli_link->get_link_from_cpt($link_id);
              $pretty_link = $prli_link->getOne($pretty_link_id);
              $pretty_url = $pretty_link->pretty_url;
          ?>
            <div class="prli-wizard-selected-content-column">
              <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Pretty Link', 'pretty-link'); ?></div>
              <div class="prli-wizard-selected-content-name"><a href="<?php echo esc_url($pretty_url); ?>"><?php echo esc_html($pretty_url); ?></a></div>
            </div>
          <?php endif; ?>

          <?php if($has_imported_links): ?>
            <div class="prli-wizard-selected-content-column">
              <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Pretty Links', 'pretty-link'); ?></div>
              <div class="prli-wizard-selected-content-name">
                <?php printf(
                  __('View your imported pretty links on the <a href="%1$s" target="_blank">Pretty Links page</a>', 'pretty-link'),
                  esc_url(admin_url('edit.php?post_type=pretty-link'))
                ); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if($category_id > 0):
            $category = get_term($category_id);
          ?>
            <hr>
            <div class="prli-wizard-selected-content-column">
              <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Link Category', 'pretty-link'); ?></div>
            <div class="prli-wizard-selected-content-name"><?php echo esc_html($category->name); ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
  }

  public static function is_upgrade_required($atts) {
    $addons_installed = isset($atts['addons_installed']) ? $atts['addons_installed'] : array();
    $addons_not_installed = isset($atts['addons_not_installed']) ? $atts['addons_not_installed'] : array();
    $license_type = self::get_license_type();
    $features_not_enabled = isset($atts['features_not_enabled']) ? $atts['features_not_enabled'] : array();

    if(!is_array($addons_installed)) {
      $addons_installed = array();
    }

    if(!is_array($addons_not_installed)) {
      $addons_not_installed = array();
    }

    // If there are no addons or features bail out.
    if(empty($addons_not_installed) && empty($features_not_enabled)) {
      return false;
    }

    foreach($addons_not_installed as $k => $addon_slug) {
      if(in_array($addon_slug,$addons_installed, true)) {
        unset($addons_not_installed[$k]); // already installed.
      }
    }

    // If there are no more add-ons requiring installation and there's no features enabled, bail out.
    if(empty($addons_not_installed) && empty($features_not_enabled)) {
      return false;
    }

    // If there are missing features, then we know the user isn't on a Pro plan.
    if(!empty($features_not_enabled)) {
      // The Product Displays add-on was selected, so we'll have them upgrade to the Super Affiliate plan.
      if(in_array('pretty-link-product-displays', $addons_not_installed)) {
        return 'pretty-link-executive';
      } else {
        return 'pretty-link-beginner';
      }
    }

    return 'pretty-link-executive'; // Upgrade to Super Affiliate required.
  }

  public static function get_upgrade_cta_data($type) {
    $data = array(
      'pretty-link-beginner' => array(
        'token' => esc_html__('Pro', 'pretty-link'),
        'url' => 'https://prettylinks.com/register/beginner',
        'label' => esc_html__('Upgrade to Pro', 'pretty-link'),
        'heading' => esc_html__('Looks like you selected one of our premium features – you mean business! Kick things into high gear by upgrading to one of our pro plan options now.', 'pretty-link')
      ),
      'pretty-link-executive' => array(
        'token' => esc_html__('Super Affiliate', 'pretty-link'),
        'url' => 'https://prettylinks.com/register/executive',
        'label' => esc_html__('Upgrade to Super Affiliate','pretty-link'),
        'heading' => esc_html__("Looks like you selected the Product Display feature – you've got great instincts! Supercharge your blog revenue from the start by upgrading your plan now.", 'pretty-link')
      )
    );

    $data = apply_filters('prli_onboarding_cta_data', $data);

    $cta_data = array();
    if(isset($data[$type])) {
      $cta_data = $data[$type];
    }

    return $cta_data;
  }

  public static function get_upgrade_pricing_url() {
    $features_enabled = self::get_selected_features(get_current_user_id());

    $edition_slug = in_array('pretty-link-product-displays', $features_enabled) ? 'pretty-link-executive' : 'pretty-link-beginner';
    $cta_data = self::get_upgrade_cta_data($edition_slug);
    $pricing_url = $cta_data['url'];

    $pricing_url = add_query_arg(
      array(
        'onboarding' => 1,
        'return_url' => urlencode(admin_url('admin.php?page=pretty-link-onboarding&step=1&onboarding=1')),
      ),
      $pricing_url
    );

    return $pricing_url;
  }
} //End class
