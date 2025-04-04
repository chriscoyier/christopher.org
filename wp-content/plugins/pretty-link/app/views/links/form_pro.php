<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<?php
  global $plp_update;
  if($plp_update->is_installed()) {
    $id = isset($id)?$id:false;
    // Add stuff to the form here
    do_action('prli_link_fields',$id);
  }
  else {
  ?>
    <div class="pretty-link-blur-wrap">
      <div class="pretty-link-blur">
        <table class="form-table">
          <tr class="prli-pro-only">
            <th scope="row">
              <?php esc_html_e('Expire', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'plp-expire',
                esc_html__('Expire Link', 'pretty-link'),
                esc_html__('Set this link to expire after a specific date or number of clicks.', 'pretty-link')
              ); ?>
              <?php // echo PrliAppHelper::pro_only_feature_indicator('link-expire'); ?>
            </th>
            <td>
              <input class="prli-toggle-checkbox" type="checkbox" disabled />
            </td>
          </tr>

          <tr class="prli-pro-only">
            <th scope="row">
              <?php esc_html_e('Keywords', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prli-link-pro-options-keywords',
                esc_html__('Auto-Replace Keywords', 'pretty-link'),
                esc_html__('Enter a comma separated list of keywords / keyword phrases that you\'d like to replace with this link in your Posts & Pages.', 'pretty-link')); ?>
              <?php // echo PrliAppHelper::pro_only_feature_indicator('link-keywords'); ?>
            </th>
            <td>
              <input type="text" class="large-text" disabled />
            </td>
          </tr>

          <tr class="prli-pro-only">
            <th scope="row">
              <?php esc_html_e('URL Replacements', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prli-link-pro-options-url-replacements',
                esc_html__('Auto-Replace URLs', 'pretty-link'),
                sprintf(
                  // translators: %1$s: open code tag, %2$s: close code tag
                  esc_html__('Enter a comma separated list of the URLs that you\'d like to replace with this Pretty Link in your Posts & Pages. These must be formatted as URLs for example: %1$shttp://example.com%2$s or %1$shttp://example.com?product_id=53%2$s', 'pretty-link'),
                  '<code>',
                  '</code>'
                )
              ); ?>
              <?php // echo PrliAppHelper::pro_only_feature_indicator('link-url-replacements'); ?>
            </th>
            <td>
              <input type="text" class="large-text" disabled />
            </td>
          </tr>

          <tr class="prli-pro-only">
            <th scope="row">
              <?php esc_html_e('Head Scripts', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prli-link-pro-options-head-scripts',
                esc_html__('Head Scripts', 'pretty-link'),
                sprintf(
                  // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                  esc_html__('Useful for adding Google Analytics tracking, Facebook retargeting pixels, or any other kind of tracking script to the HTML head for this pretty link.%1$s%1$sThese scripts will be in addition to any global one\'s you\'ve defined in the options.%1$s%1$s%2$sNOTE:%3$s This does NOT work with 301, 302 and 307 type redirects.', 'pretty-link'),
                  '<br>',
                  '<b>',
                  '</b>'
                )
              ); ?>
            </th>
            <td>
              <textarea class="large-text" disabled></textarea>
            </td>
          </tr>

          <tr class="prli-pro-only">
            <th scope="row">
              <?php esc_html_e('Dynamic Redirection', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip(
                'prli-link-pro-options-dynamic-redirection-options',
                esc_html__('Dynamic Redirection Options', 'pretty-link'),
                esc_html__('These powerful options are available to give you dynamic control over redirection for this pretty link.', 'pretty-link')
              ); ?>
              <?php // echo PrliAppHelper::pro_only_feature_indicator('dynamic-redirection'); ?>
            </th>
            <td>
              <select disabled>
                <option value="none"><?php esc_html_e('None', 'pretty-link'); ?></option>
                <option value="rotate"><?php esc_html_e('Rotation', 'pretty-link'); ?></option>
                <option value="geo"><?php esc_html_e('Geographic', 'pretty-link'); ?></option>
                <option value="tech"><?php esc_html_e('Technology', 'pretty-link'); ?></option>
                <option value="time"><?php esc_html_e('Time', 'pretty-link'); ?></option>
              </select>
            </td>
          </tr>
        </table>
      </div>
      <?php
        $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?pro-settings';
        $section_title = '';
        include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
      ?>
    </div>
  <?php
  }
