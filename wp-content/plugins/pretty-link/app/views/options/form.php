<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <?php PrliAppHelper::page_title(esc_html__('Options', 'pretty-link')); ?>
  <a href="https://prettylinks.com/user-manual-2" class="page-title-action"><?php esc_html_e('User Manual', 'pretty-link'); ?></a>
  <hr class="wp-header-end">

  <?php
    $permalink_structure = get_option('permalink_structure');
    if(!$permalink_structure or empty($permalink_structure)) {
      global $prli_siteurl;
      ?>
        <div class="error">
          <p>
            <?php
              printf(
              // translators: %1$s: open strong tag, %2$s: close strong tag
                esc_html__('%1$sWordPress Must be Configured:%2$s Pretty Links won\'t work until you select a Permalink Structure other than \'Default\'', 'pretty-link'),
                '<strong>',
                '</strong>'
              );
            ?>
            ... <a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>"><?php esc_html_e('Permalink Settings', 'pretty-link'); ?></a>
          </p>
        </div>
      <?php
    }

    do_action('prli-options-message');
  ?>

  <?php if($update_message): ?>
    <div class="updated notice notice-success is-dismissible"><p><strong><?php echo esc_html($update_message); ?></strong></p></div>
  <?php endif; ?>

  <form name="form1" id="prli-options" method="post" action="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options')); ?>">
    <input type="hidden" name="<?php echo esc_attr($hidden_field_name); ?>" value="Y">
    <?php wp_nonce_field('update-options'); ?>

    <table class="prli-settings-table">
      <tr class="prli-mobile-nav">
        <td colspan="2">
          <a href="" class="prli-toggle-nav"><i class="pl-icon-menu"> </i></a>
        </td>
      </tr>
      <tr>
        <td class="prli-settings-table-nav">
          <ul class="prli-sidebar-nav">
            <?php if($plp_update->is_installed()): ?>
              <li><a data-id="general"><?php esc_html_e('General', 'pretty-link'); ?></a></li>
            <?php endif; ?>
            <li><a data-id="links"><?php esc_html_e('Links', 'pretty-link'); ?></a></li>
            <li><a data-id="reporting"><?php esc_html_e('Reporting', 'pretty-link'); ?></a></li>
            <li><a data-id="replacements"><?php esc_html_e('Replacements', 'pretty-link'); ?></a></li>
            <li><a data-id="auto-create"><?php esc_html_e('Auto-Create Links', 'pretty-link'); ?></a></li>
            <?php if($plp_update->is_installed() && get_option('prlipro_prettybar_active')): ?>
              <li><a data-id="prettybar"><?php esc_html_e('Pretty Bar', 'pretty-link'); ?></a></li>
            <?php endif; ?>
            <li><a data-id="social"><?php esc_html_e('Social', 'pretty-link'); ?></a></li>
            <li><a data-id="public-links"><?php esc_html_e('Public', 'pretty-link'); ?></a></li>
            <?php if(!$plp_update->is_installed() || $plp_update->is_installed() && is_plugin_active('pretty-link-product-displays/pretty-link-product-displays.php')): ?>
              <li><a data-id="product-display"><?php esc_html_e('Product Display', 'pretty-link'); ?></a></li>
            <?php endif; ?>
            <li><a data-id="payments"><?php esc_html_e('Payments', 'pretty-link'); ?></a></li>
            <?php do_action('prli_admin_options_nav'); ?>
          </ul>
        </td>
        <td class="prli-settings-table-pages">
          <?php if($plp_update->is_installed()): ?>
            <div class="prli-page" id="general">
              <div class="prli-page-title"><?php esc_html_e('General Options', 'pretty-link'); ?></div>
              <?php do_action('prli_admin_general_options'); ?>
            </div>
          <?php endif; ?>

          <div class="prli-page" id="links">
            <div class="prli-page-title"><?php esc_html_e('Default Link Options', 'pretty-link'); ?></div>
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($link_redirect_type); ?>"><?php esc_html_e('Redirection', 'pretty-link') ?></label>
                    <?php PrliAppHelper::info_tooltip('prli-options-default-link-redirection',
                                                      esc_html__('Redirection Type', 'pretty-link'),
                                                      esc_html__('Select the type of redirection you want your newly created links to have.', 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <?php PrliLinksHelper::redirect_type_dropdown($link_redirect_type, $prli_options->link_redirect_type); ?>
                    <?php
                      global $plp_update;
                      if(!$plp_update->is_installed()) {
                        ?>
                        <p class="description"><?php printf(esc_html__('Get cloaked redirects, Javascript redirects and more when you %1$sUpgrade to PRO%2$s', 'pretty-link'),'<a href="https://prettylinks.com/pl/link-form/upgrade" target="_blank">','</a>') ?></p>
                        <?php
                      }
                    ?>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($link_track_me); ?>"><?php esc_html_e('Enable Tracking', 'pretty-link'); ?></label>
                    <?php PrliAppHelper::info_tooltip('prli-options-track-link',
                                                      esc_html__('Enable Tracking', 'pretty-link'),
                                                      esc_html__('Default all new links to be tracked.', 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo esc_attr($link_track_me); ?>" <?php checked($prli_options->link_track_me != 0); ?>/>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($link_nofollow); ?>"><?php esc_html_e('Enable No Follow', 'pretty-link'); ?></label>
                    <?php PrliAppHelper::info_tooltip('prli-options-add-nofollow',
                                                      esc_html__('Add No Follow', 'pretty-link'),
                                                      esc_html__('Defaults \'No Follow\' to be enabled on new links. This will add the nofollow and noindex in the HTTP Reponse headers when enabled on the link.', 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo esc_attr($link_nofollow); ?>" <?php checked($prli_options->link_nofollow != 0); ?>/>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($link_sponsored); ?>"><?php esc_html_e('Enable Sponsored', 'pretty-link'); ?></label>
                    <?php PrliAppHelper::info_tooltip('prli-options-add-sponsored',
                                                      esc_html__('Add Sponsored', 'pretty-link'),
                                                      esc_html__('Add the \'sponsored\' attribute by default to new links. This will add sponsored in the HTTP Response headers when enabled.', 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo esc_attr($link_sponsored); ?>" <?php checked($prli_options->link_sponsored != 0); ?>/>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($link_prefix); ?>"><?php esc_html_e('Enable Permalink Fix', 'pretty-link'); ?></label>
                    <?php PrliAppHelper::info_tooltip('prli-options-use-prefix-permalinks',
                                                      esc_html__('Use fix for index.php Permalink Structure', 'pretty-link'),
                                                      esc_html__("This option should ONLY be checked if you have elements in your permalink structure that must be present in any link on your site. For example, some WordPress installs don't have the benefit of full rewrite capabilities and in this case you'd need an index.php included in each link (http://example.com/index.php/mycoolslug instead of http://example.com/mycoolslug). If this is the case for you then check this option but the vast majority of users will want to keep this unchecked.", 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo esc_attr($link_prefix); ?>" <?php checked($prli_options->link_prefix != 0); ?>/>
                  </td>
                </tr>

                <?php if(!$plp_update->is_installed()): ?>

                  <tbody class="pretty-link-blur-wrap">
                    <tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label><?php esc_html_e('Base Slug Prefix', 'pretty-link'); ?></label>
                        <?php PrliAppHelper::info_tooltip('prli-base-slug-prefix',
                          esc_html__('Base Slug Prefix', 'pretty-link'),
                          sprintf(
                            // translators: %1$s: open b tag, %2$s close b tag
                            esc_html__('Use this to prefix all newly generated pretty links with a directory of your choice. For example set to %1$sout%2$s to make your pretty links look like http://site.com/%1$sout%2$s/xyz. Changing this option will NOT affect existing pretty links. If you do not wish to use a directory prefix, leave this text field blank. Whatever you type here will be sanitized and modified to ensure it is URL-safe. So %1$sHello World%2$s might get changed to something like %1$shello-world%2$s instead. Lowercase letters, numbers, dashes, and underscores are allowed.', 'pretty-link'),
                            '<b>',
                            '</b>'
                          ));
                        ?>
                        <?php // echo PrliAppHelper::pro_only_feature_indicator('option-base-slug-prefix'); ?>
                      </th>
                      <td>
                        <input type="text" class="regular-text" disabled />
                      </td>
                    </tr>

                    <tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label><?php esc_html_e('Slug Character Count', 'pretty-link'); ?></label>
                        <?php PrliAppHelper::info_tooltip('prli-num-slug-chars',
                          esc_html__('Slug Character Count', 'pretty-link'),
                          esc_html__("The number of characters to use when auto-generating a random slug for pretty links. The default is 4. You cannot use less than 2.", 'pretty-link'));
                        ?>
                        <?php // echo PrliAppHelper::pro_only_feature_indicator('option-slug-character-count'); ?>
                      </th>
                      <td>
                        <input type="number" min="2" disabled value="4" />
                      </td>
                    </tr>

                    <?php /*<tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label><?php esc_html_e('Enable Google Analytics', 'pretty-link') ?></label>
                        <?php PrliAppHelper::info_tooltip('prli-options-use-ga', esc_html__('Enable Google Analytics', 'pretty-link'),
                          esc_html__('Requires the Google Analyticator plugin be installed. If you rely on MonsterInsights plugin for Google Analytics, leave this setting disabled as it has no affect.', 'pretty-link'));
                        ?>
                        <?php // echo PrliAppHelper::pro_only_feature_indicator('option-google-analytics'); ?>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr> */ ?>

                    <tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label><?php printf(esc_html__('Enable %sQR Codes%s', 'pretty-link'), '<a href="http://en.wikipedia.org/wiki/QR_code">', '</a>'); ?></label>
                        <?php PrliAppHelper::info_tooltip('prli-options-generate-qr-codes',
                          esc_html__('Generate QR Codes', 'pretty-link'),
                          esc_html__("This will enable a link in your pretty link admin that will allow you to automatically download a QR Code for each individual Pretty Link.", 'pretty-link'));
                        ?>
                        <?php // echo PrliAppHelper::pro_only_feature_indicator('option-qr-codes'); ?>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr>

                    <tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Enable Link Health', 'pretty-link'); ?>
                          <?php
                          PrliAppHelper::info_tooltip("prli-options-enable-link-health",
                            esc_html__('Enable Link Health', 'pretty-link'),
                            esc_html__('Enable this option to be notified when your links are broken.', 'pretty-link')
                          );
                          ?>
                        </label>
                      </th>
                      <td>
                        <input class="prli-toggle-checkbox" type="checkbox" disabled />
                      </td>
                    </tr>

                    <tr valign="top" class="prli-pro-only pretty-link-blur">
                      <th scope="row">
                        <label><?php esc_html_e('Global Head Scripts', 'pretty-link'); ?></label>
                        <?php PrliAppHelper::info_tooltip('prli-options-global-head-scripts',
                          esc_html__('Global Head Scripts', 'pretty-link'),
                          sprintf(
                            // translators: %1$s: br tag, %2$s: open b tag, %3$s close b tag
                            esc_html__('Useful for adding Google Analytics tracking, Facebook retargeting pixels, or any other kind of tracking script to the HTML head.%1$s%1$sWhat you enter in this box will be applied to all supported pretty links.%1$s%1$s%2$sNOTE:%3$s This does NOT work with 301, 302 and 307 type redirects.', 'pretty-link'),
                            '<br>',
                            '<b>',
                            '</b>'
                          ));
                        ?>
                        <?php // echo PrliAppHelper::pro_only_feature_indicator('option-global-head-scripts'); ?>
                      </th>
                      <td>
                        <textarea class="large-text" disabled></textarea>
                      </td>
                    </tr>
                    <?php $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?link-options'; ?>
                    <tr valign="top" class="pretty-link-upgrade-tr condensed">
                      <th></th>
                      <td><?php include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php"; ?></td>
                    </tr>
                  </tbody>

                <?php endif; ?>

                <?php do_action('prli_custom_link_options'); ?>
              </tbody>
            </table>
          </div>

          <div class="prli-page" id="reporting">
            <div class="prli-page-title"><?php esc_html_e('Reporting Options', 'pretty-link'); ?></div>
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <th scope="row">
                    <?php esc_html_e('Tracking Style', 'pretty-link'); ?>
                    <?php PrliAppHelper::info_tooltip('prli-options-tracking-style',
                                                      esc_html__('Tracking Style', 'pretty-link'),
                                                      esc_html__("Changing your tracking style can affect the accuracy of your existing statistics. Extended mode must be used for Conversion reporting.", 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="radio" name="<?php echo esc_attr($extended_tracking); ?>" value="normal" <?php checked($prli_options->extended_tracking,'normal'); ?>/><span class="prli-radio-text"><?php esc_html_e('Normal Tracking', 'pretty-link'); ?></span><br/><br/>
                    <input type="radio" name="<?php echo esc_attr($extended_tracking); ?>" value="extended"<?php checked($prli_options->extended_tracking,'extended'); ?>/><span class="prli-radio-text"><?php esc_html_e('Extended Tracking (more stats / slower performance)', 'pretty-link'); ?></span><br/><br/>
                    <input type="radio" name="<?php echo esc_attr($extended_tracking); ?>" value="count"<?php checked($prli_options->extended_tracking,'count'); ?>/><span class="prli-radio-text"><?php esc_html_e('Simple Click Count Tracking (less stats / faster performance)', 'pretty-link'); ?></span><br/>
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <label for="<?php echo esc_attr($prli_exclude_ips); ?>">
                      <?php esc_html_e('Excluded IP Addresses:', 'pretty-link'); ?>
                      <?php PrliAppHelper::info_tooltip('prli-options-excluded-ips',
                                                        esc_html__('Excluded IP Addresses', 'pretty-link'),
                                                        esc_html__('Enter IP Addresses or IP Ranges you want to exclude from your Click data and Stats. Each IP Address should be separated by commas. Example: 192.168.0.1, 192.168.2.1, 192.168.3.4 or 192.168.*.*', 'pretty-link') .
                                                        sprintf(
                                                          '<br/><br/><strong>%s</strong>',
                                                          esc_html(sprintf(__('FYI, your current IP address is %s.', 'pretty-link'), $prli_utils->get_current_client_ip()))
                                                        ));
                      ?>
                    </label>
                  </th>
                  <td>
                    <input type="text" name="<?php echo esc_attr($prli_exclude_ips); ?>" class="regular-text" value="<?php echo esc_attr($prli_options->prli_exclude_ips); ?>">
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <?php esc_html_e('Auto-Trim Clicks', 'pretty-link'); ?>
                    <?php PrliAppHelper::info_tooltip('prli-options-auto-trim-clicks',
                                                      esc_html__('Automatically Trim Clicks', 'pretty-link'),
                                                      esc_html__("Will automatically delete all hits older than 90 days. We strongly recommend doing this to keep your database performance up. This will permanently delete this click data, and is not undo-able. ", 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo esc_attr($auto_trim_clicks); ?>" <?php checked($prli_options->auto_trim_clicks != 0); ?> />
                  </td>
                </tr>
                <tr valign="top">
                  <th scope="row">
                    <?php esc_html_e('Filter Robots', 'pretty-link'); ?>
                    <?php PrliAppHelper::info_tooltip('prli-options-filter-robots',
                                                      esc_html__('Filter Robots', 'pretty-link'),
                                                      esc_html__("Filter known Robots and unidentifiable browser clients from your click data, stats and reports. Works best if Tracking Style above is set to 'Extended Tracking'.", 'pretty-link'));
                    ?>
                  </th>
                  <td>
                    <input type="checkbox" class="prli-toggle-checkbox" data-box="prli-whitelist-ips" name="<?php echo esc_attr($filter_robots); ?>" <?php checked($prli_options->filter_robots != 0); ?> />
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="prli-sub-box prli-whitelist-ips">
              <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
              <table class="form-table">
                <tbody>
                  <tr valign="top">
                    <th scope="row">
                      <label for="<?php echo esc_attr($whitelist_ips); ?>">
                        <?php esc_html_e('Whitelist IP Addresses', 'pretty-link'); ?>
                        <?php PrliAppHelper::info_tooltip('prli-options-whitelist-ips',
                                                          esc_html__('Whitelist IP Addresses', 'pretty-link'),
                                                          esc_html__("Enter IP Addresses or IP Ranges you want to always include in your Click data and Stats even if they are flagged as robots. Each IP Address should be separated by commas. Example: 192.168.0.1, 192.168.2.1, 192.168.3.4 or 192.168.*.*", 'pretty-link'));
                        ?>
                      </label>
                    </th>
                    <td><input type="text" name="<?php echo esc_attr($whitelist_ips); ?>" class="regular-text" value="<?php echo esc_attr($prli_options->whitelist_ips); ?>"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <?php if(!$plp_update->is_installed()): ?>

            <div class="prli-page " id="replacements">
              <div class="prli-page-title"><?php esc_html_e('Keyword &amp; URL Auto Replacements Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <table class="form-table">
                    <tbody>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Enable Keyword Replacements', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-keyword-replacement',
                            esc_html__('Enable Keyword Auto Replacement', 'pretty-link'),
                            esc_html__('If checked, this will enable you to automatically replace keywords on your blog with pretty links. You will specify the specific keywords from your Pretty Link edit page.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-enable-replacements'); ?>
                        </label>
                      </th>
                      <td>
                        <input class="prli-toggle-checkbox" type="checkbox" checked disabled />
                      </td>
                    </tr>
                    </tbody>
                  </table>

                  <div class="prli-sub-box pretty-link-keyword-replacement-options">
                    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
                    <table class="form-table">
                      <tbody>
                      <tr valign="top" class="prli-pro-only">
                        <th scope="row">
                          <label>
                            <?php esc_html_e('Enable Thresholds', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('prli-keyword-replacement-thresholds',
                              esc_html__('Set Keyword Replacement Thresholds', 'pretty-link'),
                              esc_html__('Don\'t want to have too many keyword replacements per page? Select to set some reasonable keyword replacement thresholds.', 'pretty-link'));
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-thresholds'); ?>
                          </label>
                        </th>
                        <td>
                          <input class="prli-toggle-checkbox" type="checkbox" disabled />
                        </td>
                      </tr>
                      </tbody>
                    </table>
                    <table class="form-table">
                      <tbody>
                        <tr valign="top" class="prli-pro-only">
                          <th valign="row">
                            <label>
                              <?php esc_html_e('Keyword Disclosure', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip(
                                'prlipro-enable-keyword-link-disclosures',
                                esc_html__('Automatically Add Affiliate Link Disclosures to Keyword Replacements', 'pretty-link'),
                                sprintf(
                                  // translators: %1$s: open b tag, %2$s close b tag
                                  esc_html__('When enabled, this will add an affiliate link disclosure next to each one of your keyword replacements. %1$sNote:%2$s This does not apply to url replacements--only keyword replacements.', 'pretty-link'),
                                  '<b>',
                                  '</b>'
                                )
                              );
                              ?>
                              <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-keyword-disclosures'); ?>
                            </label>
                          </th>
                          <td>
                            <input type="checkbox" class="prli-toggle-checkbox" disabled />
                          </td>
                        </tr>
                        <tr valign="top" class="prli-pro-only">
                          <th scope="row">
                            <label>
                              <?php esc_html_e('Open in New Window', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip('prli-keyword-replacement-thresholds',
                                esc_html__('Open Keyword Replacement Links in New Window', 'pretty-link'),
                                esc_html__('Ensure that these keyword replacement links are opened in a separate window.', 'pretty-link'));
                              ?>
                              <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-new-window'); ?>
                            </label>
                          </th>
                          <td>
                            <input type="checkbox" disabled />
                          </td>
                        </tr>
                        <tr valign="top" class="prli-pro-only">
                          <th scope="row">
                            <label>
                              <?php esc_html_e('Add Nofollow', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip('prli-keyword-links-nofollow',
                                esc_html__('Add \'nofollow\' attribute to all Keyword Replacement Pretty Links', 'pretty-link'),
                                sprintf(
                                  // translators: %1$s: open code tag, %2$s: close code tag
                                  esc_html__('This adds the html %1$sNOFOLLOW%2$s attribute to all keyword replacement links.', 'pretty-link'),
                                  '<code>',
                                  '</code>'
                                ));
                              ?>
                              <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-no-follows'); ?>
                            </label>
                          </th>
                          <td>
                            <input type="checkbox" disabled />
                          </td>
                        </tr>
                        <tr valign="top" class="prli-pro-only">
                          <th scope="row">
                            <label>
                              <?php esc_html_e('Add Sponsored', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip('prli-keyword-links-sponsored',
                                esc_html__('Add \'sponsored\' attribute to all Keyword Replacement Pretty Links', 'pretty-link'),
                                sprintf(
                                  // translators: %1$s: open code tag, %2$s: close code tag
                                  esc_html__('This adds the html %1$sSPONSORED%2$s attribute to all keyword replacement links.', 'pretty-link'),
                                  '<code>',
                                  '</code>'
                                ));
                              ?>
                              <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-no-follows'); ?>
                            </label>
                          </th>
                          <td>
                            <input type="checkbox" disabled />
                          </td>
                        </tr>
                        <tr valign="top" class="prli-pro-only">
                          <th valign="row">
                            <label>
                              <?php esc_html_e('Keyword Post Types', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip(
                                'prlipro-keyword-replacement-cpts',
                                esc_html__('Keyword Post Types', 'pretty-link'),
                                esc_html__('Select the post types you\'d like keywords to be replaced in.', 'pretty-link'));
                              ?>
                            </label>
                          </th>
                          <td class="prlipro-chip-field">
                            <div class="prlipro-chip selected">
                              <input type="checkbox" checked disabled>
                              <label><?php esc_html_e('Posts', 'pretty-link'); ?></label>
                            </div>
                            <div class="prlipro-chip selected">
                              <input type="checkbox" checked disabled>
                              <label><?php esc_html_e('Pages', 'pretty-link'); ?></label>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <table class="form-table">
                    <tbody>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Enable URL Replacements', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-url-replacement',
                            esc_html__('Enable URL Auto Replacement', 'pretty-link'),
                            esc_html__('If checked, this will enable you to automatically replace URLs on your blog with pretty links. You will specify the specific URLs from your Pretty Link edit page.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-enable-replacements'); ?>
                        </label>
                      </th>
                      <td>
                        <input class="prli-toggle-checkbox" type="checkbox" checked disabled />
                      </td>
                    </tr>
                    </tbody>
                  </table>

                  <div class="prli-sub-box pretty-link-url-replacement-options">
                    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
                    <table class="form-table">
                      <tbody>
                        <tr valign="top" class="prli-pro-only">
                          <th scope="row">
                            <label>
                              <?php esc_html_e('Replace All URLs', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip('prli-replace-urls',
                                esc_html__('Replace All non-Pretty Link URLs With Pretty Link URLs', 'pretty-link'),
                                esc_html__('This feature will take each url it finds and create or use an existing pretty link pointing to the url and replace it with the pretty link.', 'pretty-link'));
                              ?>
                            </label>
                          </th>
                          <td>
                            <input type="checkbox" disabled />
                          </td>
                        </tr>
                        <tr valign="top" class="prli-pro-only">
                          <th scope="row">
                            <label>
                              <?php esc_html_e('URL Post Types', 'pretty-link'); ?>
                              <?php PrliAppHelper::info_tooltip('prlipro-url-replacement-cpts',
                                esc_html__('URL Post Types', 'pretty-link'),
                                esc_html__('Select the post types you\'d like URLs to be replaced in.', 'pretty-link'));
                              ?>
                            </label>
                          </th>
                          <td class="prlipro-chip-field">
                            <div class="prlipro-chip selected">
                              <input type="checkbox" checked disabled>
                              <label><?php esc_html_e('Posts', 'pretty-link'); ?></label>
                            </div>
                            <div class="prlipro-chip selected">
                              <input type="checkbox" checked disabled>
                              <label><?php esc_html_e('Pages', 'pretty-link'); ?></label>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <table class="form-table">
                    <tbody>
                      <tr valign="top" class="prli-pro-only">
                        <th valign="row">
                          <label>
                            <?php esc_html_e('Replace in Comments', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('prli-replace-in-comments',
                              esc_html__('Replace Keywords and URLs in Comments', 'pretty-link'),
                              esc_html__('This option will enable the keyword / URL replacement routine to run in Comments.', 'pretty-link'));
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-replace-in-comments'); ?>
                          </label>
                        </th>
                        <td>
                          <select disabled>
                            <option value="none" selected><?php esc_html_e('None', 'pretty-link'); ?></option>
                          </select>
                        </td>
                      </tr>
                      <tr valign="top" class="prli-pro-only">
                        <th valign="row">
                          <label>
                            <?php esc_html_e('Replace in Feeds', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('prli-replace-in-feeds',
                              esc_html__('Replace Keywords and URLs in Feeds', 'pretty-link'),
                              sprintf(
                                // translators: %1$s: br tag, %2$s open strong tag, %3$s: close strong tag
                                esc_html__('This option will enable the keyword / URL replacement routine to run in RSS Feeds.%1$s%2$sNote:%3$s This option can slow the load speed of your RSS feed -- unless used in conjunction with a caching plugin like W3 Total Cache or WP Super Cache.%1$s%2$sNote #2%3$s This option will only work if you have "Full Text" selected in your General WordPress Reading settings.%1$s%2$sNote #3:%3$s If this option is used along with "Replace Keywords and URLs in Comments" then your post comment feeds will have keywords replaced in them as well.', 'pretty-link'),
                                '<br>',
                                '<strong>',
                                '</strong>'
                              ));
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-replace-in-feeds'); ?>
                          </label>
                        </th>
                        <td>
                          <select disabled>
                            <option value="none" selected><?php esc_html_e('None', 'pretty-link'); ?></option>
                          </select>
                        </td>
                      </tr>
                      <tr valign="top" class="prli-pro-only">
                        <th valign="row">
                          <label>
                            <?php esc_html_e('Disclosure Notice', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('prlipro-link-to-disclosures',
                              esc_html__('Automatically Add a Link to Disclosures', 'pretty-link'),
                              esc_html__('When enabled, this will add a link to your official affiliate link disclosure page to any page, post or custom post type that have any keyword or URL replacements. You\'ll also be able to customize the URL and position of the disclosure link.', 'pretty-link'));
                            ?>
                          </label>
                        </th>
                        <td>
                          <input type="checkbox" class="prli-toggle-checkbox" disabled />
                        </td>
                      </tr>
                      <tr valign="top" class="prli-pro-only">
                        <th valign="row">
                          <label>
                            <?php esc_html_e('Enable Replacement Indexing', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('plp-index-keywords',
                              esc_html__('Enable Replacement Indexing', 'pretty-link'),
                              sprintf(
                                // translators: %1$s: br tag, %2$s open strong tag, %3$s: close strong tag
                                esc_html__('This feature will index all of your keyword & URL replacements to dramatically improve performance.%1$s%1$sIf your site has a large number of replacements and/or posts then this feature may increase the load on your server temporarily and your replacements may not show up on your posts for a day or two initially (until all posts are indexed).%1$s%1$s%2$sNote:%3$s this feature requires the use of wp-cron.', 'pretty-link'),
                                '<br>',
                                '<strong>',
                                '</strong>'
                              ));
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-replacement-index'); ?>
                          </label>
                        </th>
                        <td>
                          <input type="checkbox" class="prli-toggle-checkbox" disabled />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?replacement-options';
                  $section_title = esc_html__( 'Keyword &amp; URL Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>
            </div>

            <div class="prli-page" id="auto-create">
              <div class="prli-page-title"><?php esc_html_e('Auto-Create Shortlink Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <table class="form-table">
                    <tbody>
                      <tr valign="top" class="prli-pro-only">
                        <th scope="row">
                          <label>
                            <?php esc_html_e('Post Shortlinks', 'pretty-link'); ?>
                            <?php
                            PrliAppHelper::info_tooltip("prli-post-auto",
                              esc_html__('Create Pretty Links for Posts', 'pretty-link'),
                              esc_html__('Automatically Create a Pretty Link for each of your published Posts', 'pretty-link')
                            );
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-auto-create-post'); ?>
                          </label>
                        </th>
                        <td>
                          <input class="prli-toggle-checkbox" type="checkbox" disabled />
                        </td>
                      </tr>
                      <tr valign="top" class="prli-pro-only">
                        <th scope="row">
                          <label>
                            <?php esc_html_e('Page Shortlinks', 'pretty-link'); ?>
                            <?php
                            PrliAppHelper::info_tooltip("prli-page-auto",
                              esc_html__('Create Pretty Links for Pages', 'pretty-link'),
                              esc_html__('Automatically Create a Pretty Link for each of your published Pages', 'pretty-link')
                            );
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-auto-create-page'); ?>
                          </label>
                        </th>
                        <td>
                          <input class="prli-toggle-checkbox" type="checkbox" disabled />
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?auto-create-options';
                  $section_title = esc_html__( 'Auto-Create Shortlink Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>
            </div>

            <?php /*<div class="prli-page" id="prettybar">
              <div class="prli-page-title"><?php esc_html_e('Pretty Bar Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <table class="form-table">
                    <tbody>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Image URL', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-image-url',
                            esc_html__('Pretty Bar Image URL', 'pretty-link'),
                            esc_html__('If set, this will replace the logo image on the Pretty Bar. The image that this URL references should be 48x48 Pixels to fit.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-image'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="large-text" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Background Image URL', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-background-image-url',
                            esc_html__('Pretty Bar Background Image URL', 'pretty-link'),
                            esc_html__('If set, this will replace the background image on Pretty Bar. The image that this URL references should be 65px tall - this image will be repeated horizontally across the bar.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-background-image'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="large-text" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Background Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-color',
                            esc_html__('Pretty Bar Background Color', 'pretty-link'),
                            esc_html__('This will alter the background color of the Pretty Bar if you haven\'t specified a Pretty Bar background image.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-background-color'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="plp-colorpicker" size="8" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Text Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-text-color',
                            esc_html__('Pretty Bar Text Color', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If not set, this defaults to black (RGB value %1$s#000000%2$s) but you can change it to whatever color you like.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-text-color'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="plp-colorpicker" size="8" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Link Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-link-color',
                            esc_html__('Pretty Bar Link Color', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If not set, this defaults to blue (RGB value %1$s#0000ee%2$s) but you can change it to whatever color you like.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-link-color'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="plp-colorpicker" size="8" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Link Hover Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-link-hover-color',
                            esc_html__('Pretty Bar Link Hover Color', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If not set, this defaults to RGB value %1$s#ababab%2$s but you can change it to whatever color you like.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-link-hover-color'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="plp-colorpicker" size="8" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Visited Link Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-visited-link-color',
                            esc_html__('Pretty Bar Visited Link Color', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If not set, this defaults to RGB value %1$s#551a8b%2$s but you can change it to whatever color you like.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-link-visited-color'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="plp-colorpicker" size="8" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Title Char Limit', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-title-char-limit',
                            esc_html__('Pretty Bar Title Char Limit', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If your Website has a long title then you may need to adjust this value so that it will all fit on the Pretty Bar. It is recommended that you keep this value to %1$s30%2$s characters or less so the Pretty Bar\'s format looks good across different browsers and screen resolutions.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-title-char-limit'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" size="4" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Description Char Limit', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-desc-char-limit',
                            esc_html__('Pretty Bar Description Char Limit', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If your Website has a long Description (tagline) then you may need to adjust this value so that it will all fit on the Pretty Bar. It is recommended that you keep this value to %1$s40%2$s characters or less so the Pretty Bar\'s format looks good across different browsers and screen resolutions.', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-description-char-limit'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" size="4" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Target URL Char Limit', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-target-url-char-limit',
                            esc_html__('Pretty Bar Target URL Char Limit', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: open code tag, %2$s: close code tag
                              esc_html__('If you link to a lot of large Target URLs you may want to adjust this value. It is recommended that you keep this value to %1$s40%2$s or below so the Pretty Bar\'s format looks good across different browsers and URL sizes', 'pretty-link'),
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-target-url-char-limit'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" size="4" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Show Title', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-show-title',
                            esc_html__('Pretty Bar Show Title', 'pretty-link'),
                            esc_html__('Make sure this is checked if you want the title of your blog (and link) to show up on the Pretty Bar.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-show-title'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Show Description', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-show-description',
                            esc_html__('Pretty Bar Show Description', 'pretty-link'),
                            esc_html__('Make sure this is checked if you want your site description to show up on the Pretty Bar.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-show-description'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Show Share Links', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-show-share-links',
                            esc_html__('Pretty Bar Show Share Links', 'pretty-link'),
                            esc_html__('Make sure this is checked if you want "share links" to show up on the Pretty Bar.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-show-share-links'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Show Target URL', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-show-target-url-links',
                            esc_html__('Pretty Bar Show Target URL Links', 'pretty-link'),
                            esc_html__('Make sure this is checked if you want a link displaying the Target URL to show up on the Pretty Bar.', 'pretty-link'));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-show-target-url'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="checkbox" disabled />
                      </td>
                    </tr>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Hide Attribution Link', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-prettybar-hide-attrib-link',
                            esc_html__('Hide Attribution Link', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: br tag, %2$s: open strong tag, %3$s close strong tag, %4$s open em tag, %5$s close em tag, %6$s open link tag, %7$s close link tag
                              esc_html__('Check this to hide the pretty link attribution link on the pretty bar.%1$s%1$s%2$sWait, before you do this, you might want to leave this un-checked and set the alternate URL of this link to your %4$sPretty Links Pro%5$s %6$sAffiliate URL%7$s to earn a few bucks while you are at it.%3$s', 'pretty-link'),
                              '<br>',
                              '<strong>',
                              '</strong>',
                              '<em>',
                              '</em>',
                              '<a href="https://prettylinks.com/plp/options/aff-attribution">',
                              '</a>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-hide-attrib-link'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="checkbox" class="prli-toggle-checkbox" disabled />
                      </td>
                    </tr>
                    </tbody>
                  </table>

                  <div class="prli-sub-box prettybar-attrib-url">
                    <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
                    <table class="form-table">
                      <tbody>
                      <tr valign="top" class="prli-pro-only">
                        <th scope="row">
                          <label>
                            <?php esc_html_e('Attribution URL', 'pretty-link'); ?>
                            <?php PrliAppHelper::info_tooltip('prli-prettybar-attribution-url',
                              esc_html__('Alternate Pretty Bar Attribution URL', 'pretty-link'),
                              sprintf(
                                // translators: %1$s open em tag, %2$s close em tag, %3$s open link tag, %4$s close link tag
                                esc_html__('If set, this will replace the Pretty Bars attribution URL. This is a very good place to put your %1$sPretty Links Pro%2$s %3$sAffiliate Link%4$s.', 'pretty-link'),
                                '<em>',
                                '</em>',
                                '<a href="https://prettylinks.com/plp/options/aff-attribution-2">',
                                '</a>'
                              ));
                            ?>
                            <?php // echo PrliAppHelper::pro_only_feature_indicator('option-prettybar-attrib-url'); ?>
                          </label>
                        </th>
                        <td>
                          <input type="text" class="regular-text" disabled />
                        </td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?pretty-bar-options';
                  $section_title = esc_html__( 'Pretty Bar Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>
            </div>*/ ?>

            <div class="prli-page" id="social">
              <div class="prli-page-title"><?php esc_html_e('Social Buttons Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <label class="prli-label prli-pro-only">
                    <?php esc_html_e('Buttons', 'pretty-link'); ?>
                    <?php PrliAppHelper::info_tooltip('prli-social-buttons',
                      esc_html__('Social Buttons', 'pretty-link'),
                      sprintf(
                        // translators: %1$s: br tag, %2$s open code tag, %3$s close code tag
                        esc_html__('Select which buttons you want to be visible on the Social Buttons Bar.%1$s%1$s%2$sNote:%3$s In order for the Social Buttons Bar to be visible on Pages and or Posts, you must first enable it in the "Page &amp; Post Options" section above.', 'pretty-link'),
                        '<br>',
                        '<code>',
                        '</code>'
                      ));
                    ?>
                    <?php // echo PrliAppHelper::pro_only_feature_indicator('option-social-buttons'); ?>
                  </label>
                  <ul class="prli-social-button-checkboxes">
                    <?php foreach(array('facebook', 'twitter', 'gplus', 'pinterest', 'linkedin', 'reddit', 'stumbleupon', 'digg', 'email') as $b) : ?>
                      <li class="pl-social-<?php echo esc_attr($b); ?>-button">
                        <input type="checkbox" disabled />
                        <i class="pl-icon-<?php echo esc_attr($b); ?>"> </i>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                  <br/>
                  <table class="form-table">
                    <tbody>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Buttons Placement', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-social-buttons-placement',
                            esc_html__('Social Buttons Placement', 'pretty-link'),
                            sprintf(
                              // translators: %1$s: br tag, %2$s open code tag, %3$s close code tag
                              esc_html__('This determines where your Social Buttons Placement should appear in relation to content on Pages and/or Posts.%1$s%1$s%2$sNote:%3$s If you want this bar to appear then you must enable it in the "Auto-Create Links" section above.', 'pretty-link'),
                              '<br>',
                              '<code>',
                              '</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-social-buttons-placement'); ?>
                        </label>
                      </th>
                      <td>
                        <input type="radio" value="top" disabled /><span class="prli-radio-text"><?php esc_html_e('Top', 'pretty-link'); ?></span><br/><br/>
                        <input type="radio" value="bottom" checked disabled /><span class="prli-radio-text"><?php esc_html_e('Bottom', 'pretty-link'); ?></span><br/><br/>
                        <input type="radio" value="top-and-bottom" disabled /><span class="prli-radio-text"><?php esc_html_e('Top and Bottom', 'pretty-link'); ?></span><br/><br/>
                        <input type="radio" value="none" disabled /><span class="prli-radio-text"><?php esc_html_e('None', 'pretty-link'); ?></span>
                        <?php PrliAppHelper::info_tooltip('prli-social-buttons-placement-none',
                          esc_html__('Social Buttons Manual Placement', 'pretty-link'),
                          sprintf(
                            // translators: %1$s: example shortcode, %2$s: example template tag
                            esc_html__('If you select none, you can still show your Social Buttons by manually adding the %1$s shortcode to your blog posts or %2$s template tag to your WordPress Theme.', 'pretty-link'),
                            '<code>[social_buttons_bar]</code>',
                            '<code>&lt;?php the_social_buttons_bar(); ?&gt;</code>'
                          ));
                        ?>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?social-buttons-options';
                  $section_title = esc_html__( 'Social Buttons Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>

            </div>

            <div class="prli-page" id="public-links">
              <div class="prli-page-title"><?php esc_html_e('Public Links Creation Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <table class="form-table">
                    <tbody>
                    <tr valign="top" class="prli-pro-only">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Enable Public Links', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-enable-public-link-creation',
                            esc_html__('Enable Public Link Creation on this Site', 'pretty-link'),
                            sprintf(
                              esc_html__('This option will give you the ability to turn your website into a link shortening service for your users. Once selected, you can enable the Pretty Links Pro Sidebar Widget or just display the link creation form with the %s shortcode in any post or page on your website.', 'pretty-link'),
                              '<code>[prli_create_form]</code>'
                            ));
                          ?>
                          <?php // echo PrliAppHelper::pro_only_feature_indicator('option-enable-public-link-creation'); ?>
                        </label>
                      </th>
                      <td>
                        <input class="prli-toggle-checkbox" type="checkbox" disabled />
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?public-links-options';
                  $section_title = esc_html__( 'Public Link Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>
            </div>

            <div class="prli-page" id="product-display">
              <div class="prli-page-title"><?php esc_html_e('Product Display Options', 'pretty-link'); ?></div>
              <div class="pretty-link-blur-wrap">
                <div class="pretty-link-blur">
                  <table class="form-table">
                    <tbody>
                    <tr valign="top">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Button Background Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-pd-button-bg-color',
                                              esc_html__('Button Background Color', 'pretty-link'),
                                              esc_html__('Background color for the two buttons used in the displays.', 'pretty-link'));
                          ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="prli-color-picker" value="#115e8c" disabled />
                      </td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Button Text Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-pd-button-text-color',
                                              esc_html__('Button Text Color', 'pretty-link'),
                                              esc_html__('Text color for the two buttons used in the displays.', 'pretty-link'));
                          ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="prli-color-picker" value="#fff" disabled />
                      </td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Button Hover Background Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-pd-button-hover-bg-color',
                                              esc_html__('Button Hover Background Color', 'pretty-link'),
                                              esc_html__('Background color for the two buttons used in the displays when hovered over.', 'pretty-link'));
                          ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="prli-color-picker" value="#6b98bf" disabled />
                      </td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Button Hover Text Color', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-pd-button-hover-text-color',
                                              esc_html__('Button Hover Text Color', 'pretty-link'),
                                              esc_html__('Text color for the two buttons used in the displays when hovered over.', 'pretty-link'));
                          ?>
                        </label>
                      </th>
                      <td>
                        <input type="text" class="prli-color-picker" value="#fff" disabled />
                      </td>
                    </tr>
                    <tr valign="top">
                      <th scope="row">
                        <label>
                          <?php esc_html_e('Affiliate Disclosure', 'pretty-link'); ?>
                          <?php PrliAppHelper::info_tooltip('prli-pd-affiliate-disclosure',
                                            esc_html__('Affiliate Disclosure', 'pretty-link'),
                                            esc_html__('Disclosure to show for the display.', 'pretty-link'));
                          ?>
                        </label>
                      </th>
                      <td>
                        <textarea rows="5" disabled></textarea>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <?php
                  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?product-displays';
                  $section_title = esc_html__( 'Product Display Options', 'pretty-link' );
                  include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
                ?>
              </div>
            </div>

          <?php endif; ?>

          <?php require_once PRLI_VIEWS_PATH . '/admin/payments/options.php'; ?>

          <?php do_action('prli_admin_options_pages'); ?>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Update', 'pretty-link') ?>" />
    </p>

  </form>
</div>
<script>
  jQuery(document).ready(function($) {
    function repositionUpgrade(dataId) {
      var wrap = $('#' + dataId + ' .pretty-link-blur-wrap');
      var row = $('#' + dataId + ' .pretty-link-upgrade-tr');
      var dialog = row.find('.pretty-link-popup');
      if ( dialog.data('offset') ) {
        dialog.css('top', '-' + dialog.data('offset') + 'px');
      } else if(row.is(':visible')) {
        var moveUp = row.offset().top - wrap.offset().top;
        dialog.data('offset', moveUp);
        dialog.css('top', '-' + moveUp + 'px');
      }
    }
    $('.prli-sidebar-nav a').click(function(event) {
      var dataId = $(this).data('id');
      repositionUpgrade(dataId);
    });
    repositionUpgrade('links');
  });
</script>
