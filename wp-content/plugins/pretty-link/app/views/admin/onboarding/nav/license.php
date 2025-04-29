<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php $li = get_site_transient('prli_license_info'); ?>

<?php if($li): ?>
  <div id="prli-wizard-license-nav-continue">
    <button type="button" class="prli-wizard-button-blue prli-wizard-go-to-step" data-step="2" data-context="continue"><?php esc_html_e('Continue', 'pretty-link'); ?></button>
  </div>
<?php endif; ?>