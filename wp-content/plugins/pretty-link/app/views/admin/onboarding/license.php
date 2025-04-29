<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
  global $plp_update;
  $li = $plp_update->get_license_info();
?>

<?php if($li): ?>
<div id="prli-wizard-license-wrapper" class="prli-hidden">
  <h2 class="prli-wizard-step-title"><?php esc_html_e('Your License', 'pretty-link'); ?></h2>
  <?php require_once PRLI_VIEWS_PATH . '/admin/onboarding/active_license.php'; ?>
</div>
<?php else: ?>
  <h2 class="prli-wizard-step-title"><?php esc_html_e('Activate License', 'pretty-link'); ?></h2>
  <p class="prli-wizard-step-description"><?php esc_html_e("Let's kick things off by activating your license.", 'pretty-link'); ?></p>
  <p>
    <a href="<?php echo esc_url(PrliAuthenticatorController::get_auth_connect_url(admin_url('admin.php?page=pretty-link-onboarding&step=1'), array('onboarding' => 'true'))); ?>" class="prli-wizard-button-blue"><?php esc_html_e('Activate', 'pretty-link'); ?></a>
    <button type="button" class="prli-wizard-button-link prli-wizard-go-to-step" data-step="2" data-context="skip"><span><?php esc_html_e('Skip', 'pretty-link'); ?></span></button>
  </p>
  <?php if(isset($_GET['license_error'])) : ?>
    <div class="notice notice-error inline">
      <p><?php echo esc_html(sanitize_text_field(wp_unslash($_GET['license_error']))); ?></p>
    </div>
  <?php endif; ?>
<?php endif; ?>
