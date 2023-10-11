<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<p class="description">
  <?php
    printf(
      /* translators: %1$s: link to pro site, %2$s: link to pro site login page */
      esc_html__('You must have a License Key to enable automatic updates for Pretty Links Pro. If you don\'t have a License please go to %1$s to get one. If you do have a license you can login at %2$s to manage your licenses and site activations.', 'pretty-link'),
      '<a href="https://prettylinks.com/pl/license-alert/buy">prettylinks.com</a>',
      '<a href="https://prettylinks.com/pl/license-alert/login">prettylinks.com/login</a>'
    );
  ?>
</p>

<table class="form-table">
  <tr class="form-field">
    <td valign="top" width="225px"><label for="prli-license-key"><?php esc_html_e('Enter Your Pretty Links Pro License Key:', 'pretty-link'); ?></label></td>
    <td>
      <input type="text" id="prli-license-key" value="<?php echo esc_attr($this->mothership_license); ?>" />
    </td>
  </tr>
</table>
<p class="submit">
  <button type="button" id="prli-activate-license-key" class="button button-primary"><?php echo esc_html(sprintf(__('Activate License Key on %s', 'pretty-link'), PrliUtils::site_domain())); ?></button>
</p>

<?php if(!$this->is_installed()): ?>
  <div>&nbsp;</div>

  <div class="prli-page-title" id="prli_upgrade"><?php esc_html_e('Upgrade to Pro', 'pretty-link'); ?></div>

  <div>
    <?php
      printf(
        /* translators: %1$s: open link tag, %2$s: close link tag */
        esc_html__('It looks like you haven\'t %1$supgraded to Pretty Links Pro%2$s yet. Here are just a few things you could be doing with pro:', 'pretty-link'),
        '<a href="https://prettylinks.com/pl/license-alert/upgrade" target="_blank">',
        '</a>'
      );
    ?>
  </div>

  <div>&nbsp;</div>

  <ul style="padding-left: 25px;">
    <li>&bullet; <?php esc_html_e('Auto-replace keywords throughout your site with Pretty Links', 'pretty-link'); ?></li>
    <li>&bullet; <?php esc_html_e('Protect your affiliate links by using Cloaked Redirects', 'pretty-link'); ?></li>
    <li>&bullet; <?php esc_html_e('Redirect based on a visitor\'s location', 'pretty-link'); ?></li>
    <li>&bullet; <?php esc_html_e('Auto-prettylink your Pages &amp; Posts', 'pretty-link'); ?></li>
    <li>&bullet; <?php esc_html_e('Find out what works and what doesn\'t by split testing your links', 'pretty-link'); ?></li>
    <li>&bullet; <?php esc_html_e('And much, much more!', 'pretty-link'); ?></li>
  </ul>

  <div>&nbsp;</div>
  <div><?php esc_html_e('Plus, upgrading is fast, easy and won\'t disrupt any of your existing links or data. And there\'s even a 14 day money back guarantee.', 'pretty-link'); ?></div>
  <div>&nbsp;</div>
  <div><?php esc_html_e('We think you\'ll love it!', 'pretty-link'); ?></div>
  <div>&nbsp;</div>
  <div><a href="https://prettylinks.com/pl/license-alert/upgrade-1" class="button button-primary"><?php esc_html_e('Upgrade to Pro today!', 'pretty-link'); ?></a></div>
<?php endif; ?>
