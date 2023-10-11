<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="prli-upgrade-popup" class="mfp-hide prli-popup prli-auto-open">
  <p><img src="<?php echo esc_url(PRLI_IMAGES_URL . '/pl-logo-horiz-RGB.svg'); ?>" width="200" height="32" /></p>
  <div>&nbsp;</div>
  <h2><?php esc_html_e('Upgrade to Pretty Links PRO', 'pretty-link'); ?></h2>
  <p><?php esc_html_e('Unlock Pretty Links\' PRO features and you\'ll be able to:', 'pretty-link'); ?><br/>
    <ul>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('Monetize existing content with automated Pretty Link placement', 'pretty-link'); ?></li>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('Redirect with cloaking, Javascript or Meta-refresh', 'pretty-link'); ?></li>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('Redirect by location, time, device or rotation', 'pretty-link'); ?></li>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('Expire your Pretty Links', 'pretty-link'); ?></li>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('Split-test your Pretty Links', 'pretty-link'); ?></li>
      <li>&nbsp;&nbsp;&nbsp;&bullet;&nbsp;<?php esc_html_e('And much, much more!', 'pretty-link'); ?></li>
    </ul>
  </p>
  <p><?php esc_html_e('Plus, upgrading is fast, easy and won\'t disrupt any of your existing links or data.', 'pretty-link'); ?></p>
  <p><?php esc_html_e('We think you\'ll love it!', 'pretty-link'); ?></p>
  <div>&nbsp;</div>
  <div>&nbsp;</div>

  <center>
    <button data-popup="upgrade" data-href="https://prettylinks.com/pl/popup/upgrade" class="prli-delay-popup button-primary"><?php esc_html_e('Upgrade to Pretty Links Pro', 'pretty-link'); ?></button>
    <button data-popup="upgrade" class="prli-delay-popup button"><?php esc_html_e('Maybe Later', 'pretty-link'); ?></button>
  </center>

</div>

