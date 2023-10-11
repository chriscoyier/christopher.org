<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php global $plp_update; ?>
<?php if(defined('PRETTYLINK_LICENSE_KEY') && isset($error)): ?>
  <div class="prli-red-notice">
    <?php
      printf(
        esc_html__('Error with PRETTYLINK_LICENSE_KEY: %s', 'pretty-link'),
        esc_html($error)
      );
    ?>
  </div>
<?php elseif($plp_update->was_activated_with_username_and_password()): ?>
  <div class="prli-red-notice">
    <h2><?php esc_html_e('Pretty Links Pro isn\'t able to get critical automatic updates', 'pretty-link'); ?></h2>
    <p><?php esc_html_e('It looks like you used to have Pretty Links Pro activated with a username and password but now you need a license key to activate it.', 'pretty-link'); ?></p>
    <p>
      <strong>
        <?php
          printf(
            // translators: %1$s: open link tag, %2$s: close link tag
            esc_html__('You can get your license key by logging in at %1$sPrettyLinkPro.com.%2$s', 'pretty-link'),
            '<a href="https://prettylinks.com/activation-warning/account" target="_blank">',
            '</a>'
          );
        ?>
      </strong>
    </p>
    <p>
      <?php
        printf(
          // translators: %1$s: open link tag, %2$s: close link tag
          esc_html__('After you paste your license key on the %1$s"Pretty Link" -> "Activate Pro" admin page,%2$s you\'ll start getting updates again.', 'pretty-link'),
          sprintf('<a href="%s">', esc_url($plp_update->activate_page_url())),
          '</a>'
        );
      ?>
    </p>
  </div>
<?php else: ?>
  <div class="prli-red-notice">
    <h2><?php esc_html_e('Pretty Links Pro isn\'t able to get critical automatic updates', 'pretty-link'); ?></h2>
    <p>
      <strong>
        <?php
          printf(
            // translators: %1$s: open link tag, %2$s: close link tag
            esc_html__('You can retrieve or purchase a license key at %1$sPrettyLinks.com%2$s to enable automatic updates today.', 'pretty-link'),
            '<a href="https://prettylinks.com/pl/activation-warning/buy" target="_blank">',
            '</a>'
          );
        ?>
      </strong>
    </p>
    <p>
      <?php
        printf(
          // translators: %1$s: open link tag, %2$s: close link tag
          esc_html__('After you paste your license key on the %1$s"Pretty Links" -> "Activate Pro" admin page,%2$s you\'ll start getting automatic updates.', 'pretty-link'),
          sprintf('<a href="%s">', esc_url($plp_update->activate_page_url())),
          '</a>'
        );
      ?>
    </p>
  </div>
<?php endif; ?>
