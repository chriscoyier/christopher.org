<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php $li = get_site_transient('prli_license_info'); ?>

<?php if(!$li): ?>
  <div id="prli-wizard-license-nav-skip">
    <button type="button" class="prli-wizard-button-link prli-wizard-go-to-step" data-step="2" data-context="skip"><span><?php esc_html_e('Skip', 'pretty-link'); ?></span></button>
  </div>
<?php else: ?>
  <div id="prli-wizard-license-nav-continue">
    <button type="button" class="prli-wizard-button-blue prli-wizard-go-to-step" data-step="2" data-context="continue"><?php esc_html_e('Continue', 'pretty-link'); ?></button>
  </div>
<?php endif; ?>