<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<table class="form-table">
  <tr>
    <th scope="row">
      <?php esc_html_e('No Follow', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-nofollow',
              esc_html__('Nofollow Link', 'pretty-link'),
              esc_html__('Add a nofollow and noindex to this link\'s http redirect header. (Recommended)', 'pretty-link')
            ); ?>
    </th>
    <td>
      <input type="checkbox" name="nofollow" <?php checked($values['nofollow']); ?>/>
    </td>
  </tr>
  <tr>
    <th scope="row">
      <?php esc_html_e('Sponsored', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-sponsored',
              esc_html__('Sponsored Link', 'pretty-link'),
              esc_html__('Add a sponsored attribute to this link\'s http redirect header. Recommended if this is an affiliate link.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <input type="checkbox" name="sponsored" <?php checked($values['sponsored']); ?>/>
    </td>
  </tr>
  <tr id="prli_time_delay" style="display: none">
    <th scope="row">
      <?php esc_html_e('Delay Redirect', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-delay-redirect',
              esc_html__('Delay Redirect', 'pretty-link'),
              esc_html__('Time in seconds to wait before redirecting', 'pretty-link')
            ); ?>
    </th>
    <td>
      <input type="number" name="delay" class="small-text" value="<?php echo esc_attr($values['delay']); ?>" />
    </td>
  </tr>
  <?php if(!$values['prettypay_link']) : ?>
    <tr>
      <th scope="row">
        <?php esc_html_e("Parameter Forwarding", 'pretty-link') ?>
        <?php PrliAppHelper::info_tooltip(
                'prli-link-parameter-forwarding',
                esc_html__('Parameter Forwarding', 'pretty-link'),
                esc_html__('Forward parameters passed to this link onto the Target URL', 'pretty-link')
              ); ?>
      </th>
      <td>
        <input type="checkbox" name="param_forwarding" id="param_forwarding" <?php checked($values['param_forwarding']); ?> />
      </td>
    </tr>
  <?php endif; ?>
  <tr>
    <th scope="row">
      <?php esc_html_e("Tracking", 'pretty-link') ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-tracking-options',
              esc_html__('Tracking', 'pretty-link'),
              esc_html__('Enable Pretty Link\'s built-in hit (click) tracking', 'pretty-link')
            ); ?>
    </th>
    <td>
      <input type="checkbox" name="track_me" <?php checked($values['track_me']); ?> />
    </td>
  </tr>
  <?php /*<tr id="prli_google_analytics" style="display: none">
    <th scope="row">
      <?php esc_html_e('Google Analytics', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-ga',
              esc_html__('Google Analytics', 'pretty-link'),
              esc_html__('Requires the Google Analyticator plugin be installed and configured. This setting has no impact on the MonsterInsights integration with Pretty Links.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <?php
      global $plp_update;
      if($plp_update->is_installed()):
        if(($ga_info = PlpUtils::ga_installed())):
          ?>
          <input type="checkbox" name="google_tracking" <?php checked($values['google_tracking']); ?> />
          <p class="description">
            <?php
              printf(
                esc_html__('It appears that %s is currently installed. Pretty Link will attempt to use its settings to track this link.', 'pretty-link'),
                '<strong>' . esc_html($ga_info['name']) . '</strong>'
              );
            ?>
          </p>
          <?php
        else:
          ?>
            <input type="hidden" name="google_tracking" value="" />
            <p class="description"><strong><?php esc_html_e('No Google Analytics Plugin is currently installed. Pretty Link cannot track links using Google Analytics until one is.', 'pretty-link'); ?></strong></p>
          <?php
        endif;
      endif;
      ?>
    </td>
  </tr> */ ?>
  <?php do_action('prli_link_form_advanced'); ?>
</table>

