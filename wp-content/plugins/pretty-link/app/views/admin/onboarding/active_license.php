<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div id="prli-license-container" class="prli-wizard-license-container">
  <div class="prli-wizard-license">
    <div class="prli-wizard-license-notice">
      <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/green-check.svg'); ?>" alt="">
      <?php
        $expires_at = null;

        if(isset($li['license_key']['expires_at'])) {
          $expires_at = date_create($li['license_key']['expires_at']);
        }

        if($expires_at instanceof DateTime) {
          $timestamp = $expires_at->getTimestamp();
          $date = wp_date('F j, Y', $timestamp);

          echo esc_html(
            sprintf(
              __('License activated until %s', 'pretty-link'),
              $date
            )
          );
        }
        else {
          esc_html_e('License activated', 'pretty-link');
        }
      ?>
    </div>
    <div class="prli-wizard-license-details">
      <div>
        <div class="prli-wizard-license-label">
          <?php esc_html_e('Account email', 'pretty-link'); ?>
        </div>
        <div class="prli-wizard-license-value">
          <?php echo esc_html(!empty($li['user']['email']) ? $li['user']['email'] : __('Unknown', 'pretty-link')); ?>
        </div>
      </div>
      <div>
        <div class="prli-wizard-license-label">
          <?php esc_html_e('Product', 'pretty-link'); ?>
        </div>
        <div class="prli-wizard-license-value">
          <?php echo esc_html($li['product_name']); ?>
        </div>
      </div>
      <div>
        <div class="prli-wizard-license-label">
          <?php esc_html_e('Activations', 'pretty-link'); ?>
        </div>
        <div class="prli-wizard-license-value">
          <?php
            printf(
              // translators: %1$d: activation count, %2$d: max activations
              esc_html__('%1$d of %2$d sites have been activated with this license key', 'pretty-link'),
              esc_html($li['activation_count']),
              esc_html(ucwords($li['max_activations']))
            );
          ?>
        </div>
      </div>
    </div>
    <div class="prli-wizard-license-manage">
      <a href="https://prettylinks.com/account/" target="_blank"><?php esc_html_e('Manage activations', 'pretty-link'); ?></a>
    </div>
    <div class="prli-wizard-license-deactivate">
      <button type="button" id="prli-deactivate-license-key" class="prli-wizard-button-secondary"><?php esc_html_e('Deactivate License', 'pretty-link'); ?></button>
    </div>
  </div>
</div>
