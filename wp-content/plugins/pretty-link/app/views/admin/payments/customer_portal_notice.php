<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div id="prli-customer-portal-notice-popup" class="prli-popup mfp-hide" data-nonce="<?php echo esc_attr(wp_create_nonce('prli_dismiss_customer_portal_notice')); ?>">
  <h2><?php esc_html_e('Customer Portal', 'pretty-link'); ?></h2>
  <p>
    <?php
      printf(
        /* translators: %1$s: open strong tag, %2$s: close strong tag */
        esc_html__('Now that you have a recurring PrettyPay™ Link, did you know that you can let your customers manage their own subscriptions using the %1$sCustomer Portal%2$s?', 'pretty-link'),
        '<strong>',
        '</strong>'
      );
    ?>
  </p>
  <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/payments/stripe-customer-portal.jpg'); ?>" alt="">
  <p class="prli-customer-portal-notice-buttons">
    <a href="#" id="prli-customer-portal-notice-close"><?php esc_html_e('No thanks', 'pretty-link'); ?></a>
    <a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options&nav_action=payments&configure_customer_portal=1')); ?>" class="button button-primary"><?php esc_html_e('Configure Customer Portal', 'pretty-link'); ?></a>
  </p>
</div>
