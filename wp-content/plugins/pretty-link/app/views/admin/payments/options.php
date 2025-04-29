<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php

$classes = '';
$show_keys = false;
if ( ! isset( $_GET['display-keys'] ) && ! isset( $_COOKIE['prli_stripe_display_keys'] ) && ! defined( 'PRLI_DISABLE_STRIPE_CONNECT' ) ) {
  $classes = 'class="prli-hidden"';
} else {
  $show_keys = true;
}

$account_email = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_EMAIL );
$secret = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SECRET );
$site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
$service_account_name = get_option( 'prli_stripe_service_account_name' );
$id = PrliStripeConnect::get_method_id();

// If we're authenticated then let's present a stripe url otherwise an authenticator url
if( $account_email && $secret && $site_uuid ) {
  $stripe_connect_url = PrliStripeConnect::get_stripe_connect_url();
} else {
  $return_url = admin_url('edit.php?post_type=pretty-link&page=pretty-link-options&nav_action=payments');
  $stripe_connect_url = PrliAuthenticatorController::get_auth_connect_url( $return_url, array(
    'stripe_connect' => 'true',
    'method_id' => $id
  ));
}

$connect_status = PrliStripeConnect::stripe_connect_status();
?>
<div class="prli-page" id="payments">
  <div class="prli-page-title"><?php esc_html_e('Payments', 'pretty-link'); ?></div>
  <table class="form-table prli-payment-stripe-connect">
    <tbody>
      <tr valign="top">
        <td colspan="2">
          <div class="prli-payment-option-prompt">
            <div><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Stripe_with_Tagline.svg" alt="Stripe logo"></div>
            <table class="stripe-feature-list" width="500px">
                <tr>
                  <td>
                    <ul class="stripe-features">
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "Accept all Major Credit Cards", 'pretty-link' ); ?></li>
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "Flexible subscriptions and billing terms", 'pretty-link' ); ?></li>
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "25+ ways to pay", 'pretty-link' ); ?></li>
                    </ul>
                  </td>
                  <td>
                    <ul class="stripe-features">
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "Accept Apple Pay", 'pretty-link' ); ?></li>
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "Accept Google Wallet", 'pretty-link' ); ?></li>
                      <li><img src="<?php echo PRLI_IMAGES_URL; ?>/payments/Check_Mark.svg"/><?php _e( "Fraud prevention tools", 'pretty-link' ); ?></li>
                    </ul>
                  </td>
                </tr>
            </table>

            <?php if ( 'connected' === $connect_status ) : ?>
              <?php
                $refresh_url = add_query_arg( array( 'action' => 'prli_stripe_connect_refresh', 'method-id' => $id, '_wpnonce' => wp_create_nonce('stripe-refresh') ), admin_url('admin-ajax.php') );
                $disconnect_url = add_query_arg( array( 'action' => 'prli_stripe_connect_disconnect', 'method-id' => $id, '_wpnonce' => wp_create_nonce('stripe-disconnect') ), admin_url('admin-ajax.php') );
                $disconnect_confirm_msg = __( 'Disconnecting from this Stripe Account will prevent PrettyPay™ links from working.', 'pretty-link' );
              ?>

              <div id="stripe-connected-actions" class="prli-payment-option-prompt connected">
                <span>
                  <?php if ( empty( $service_account_name ) ): ?>
                    <?php _e( 'Connected to Stripe', 'pretty-link' ); ?>
                  <?php else: ?>
                    <?php printf( __( 'Connected to: %1$s %2$s %3$s', 'pretty-link' ), '<strong>', $service_account_name, '</strong>' ); ?>
                  <?php endif; ?>
                </span>
                <span <?php echo $classes; ?>>
                  <a href="<?php echo $refresh_url; ?>"
                     class="stripe-btn prli_stripe_refresh_button button-secondary"><?php _e( 'Refresh Stripe Credentials', 'pretty-link' ); ?></a>
                 </span>
                 <a href="<?php echo $disconnect_url; ?>" class="stripe-btn prli_stripe_disconnect_button button-secondary"
                    data-disconnect-msg="<?php echo $disconnect_confirm_msg; ?>">
                   <?php _e( 'Disconnect', 'pretty-link' ); ?>
                 </a>
              </div>
            <?php endif; ?>

            <?php if ( 'disconnected' === $connect_status ) : ?>
              <div>
                <h4><strong><?php _e( 'Re-Connect to Stripe', 'pretty-link' ); ?></strong></h4>
                <p><?php _e( 'Stripe has been disconnected so it may stop working PrettyPay™ links at any time. To prevent this, re-connect your Stripe account by clicking the "Connect with Stripe" button below.', 'pretty-link' ); ?></p>
                <p>
                  <a href="<?php echo $stripe_connect_url; ?>"
                      data-id="<?php echo $id; ?>"
                      class="prli-stripe-connect-new">
                        <img src="<?php echo PRLI_IMAGES_URL . '/payments/stripe-connect.png'; ?>" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'pretty-link' ); ?>" width="200">
                    </a>
                </p>
              </div>
            <?php elseif ( 'connected' !== $connect_status ) : ?>
                <a data-id="<?php echo $id; ?>" href="<?php echo $stripe_connect_url; ?>" class="prli-stripe-connect-new">
                  <img src="<?php echo PRLI_IMAGES_URL . '/payments/stripe-connect.png'; ?>" alt="<?php esc_attr_e( '"Connect with Stripe" button', 'pretty-link' ); ?>" width="200">
              </a>
            <?php endif; ?>
          </div>

        </td>
      </tr>
    </tbody>
  </table>
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label for="<?php echo esc_attr($prettypay_thank_you_page_id); ?>"><?php esc_html_e('PrettyPay™ Thank You Page', 'pretty-link'); ?></label>
          <?php PrliAppHelper::info_tooltip('prli-options-prettypay-ty-page',
                                        esc_html__('PrettyPay™ Thank You Page', 'pretty-link'),
                                        esc_html__('The page that customers will be redirected to after making a payment using a PrettyPay™ Link, this can be overridden on individual PrettyPay™ Links.', 'pretty-link'));
          ?>
        </th>
        <td>
          <?php
            PrliAppHelper::wp_pages_dropdown(
              $prettypay_thank_you_page_id,
              $prli_options->prettypay_thank_you_page_id,
              true,
              __('Homepage', 'pretty-link')
            );
          ?>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label for="<?php echo esc_attr($prettypay_default_currency); ?>">
            <?php esc_html_e('Default Currency', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip('prli-options-default-currency',
                                          esc_html__('Default Currency', 'pretty-link'),
                                          esc_html__('Set the currency that is selected by default when creating products.', 'pretty-link'));
            ?>
          </label>
        </th>
        <td>
          <div class="prli-width-250">
            <select id="<?php echo esc_attr($prettypay_default_currency); ?>" name="<?php echo esc_attr($prettypay_default_currency); ?>" aria-label="<?php esc_html_e('Currency', 'pretty-link'); ?>">
              <?php foreach(PrliUtils::currencies() as $code => $name) : ?>
                <option value="<?php echo esc_attr($code); ?>" <?php selected($prli_options->prettypay_default_currency, $code); ?>><?php echo esc_attr("$code - $name"); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </td>
      </tr>
      <?php if(PrliStripeHelper::is_connection_active()) : ?>
        <tr>
          <th scope="row">
            <label>
              <?php esc_html_e('Customer Portal', 'pretty-link'); ?>
              <?php PrliAppHelper::info_tooltip('prli-options-customer-portal',
                                                esc_html__('Customer Portal', 'pretty-link'),
                                                esc_html__('Configure the customer portal where customers can manage their recurring PrettyPay™ Link subscriptions.', 'pretty-link'));
              ?>
            </label>
          </th>
          <td>
            <?php if(get_option('prli_stripe_customer_portal')) : ?>
              <div class="prli-stripe-portal-url">
                <input type="text" class="regular-text" value="<?php echo esc_url(home_url(PrliStripeHelper::get_customer_portal_page_name())); ?>" readonly>
                <span class="prli-clipboard">
                  <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs" data-clipboard-text="<?php echo esc_url(home_url(PrliStripeHelper::get_customer_portal_page_name())); ?>"></i>
                </span>
              </div>
            <?php endif; ?>
            <button type="button" id="prli-stripe-configure-customer-portal" class="button button-secondary"><?php esc_html_e('Configure Customer Portal', 'pretty-link'); ?><i class="pl-icon pl-icon-right-open"></i></button>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if(PrliStripeHelper::is_connection_active()) : ?>
    <div id="prli-customer-portal-sub-box" class="prli-sub-box">
      <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="prli-portal-headline">
                <?php esc_html_e('Portal Header', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-options-customer-portal-headline',
                                                esc_html__('Portal Header', 'pretty-link'),
                                                esc_html__('The messaging shown to customers in the portal.', 'pretty-link'));
                ?>
              </label>
            </th>
            <td>
              <input type="text" id="prli-portal-headline" name="prli_portal_headline" class="regular-text" maxlength="60" value="<?php echo esc_attr(PrliStripeHelper::get_portal_config_value('business_profile.headline')); ?>">
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-privacy-policy-url">
                <?php esc_html_e('Privacy Policy URL', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-options-customer-portal-privacy-policy',
                                                esc_html__('Privacy Policy URL', 'pretty-link'),
                                                esc_html__('A link to the business\'s publicly available privacy policy.', 'pretty-link'));
                ?>
              </label>
            </th>
            <td>
              <input type="text" id="prli-portal-privacy-policy-url" name="prli_portal_privacy_policy_url" class="regular-text" value="<?php echo esc_attr(PrliStripeHelper::get_portal_config_value('business_profile.privacy_policy_url')); ?>">
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-terms-of-service-url">
                <?php esc_html_e('Terms of Service URL', 'pretty-link'); ?>
                <?php PrliAppHelper::info_tooltip('prli-options-customer-portal-terms-of-service',
                                                esc_html__('Terms of Service URL', 'pretty-link'),
                                                esc_html__('A link to the business\'s publicly available terms of service.', 'pretty-link'));
                ?>
              </label>
            </th>
            <td>
              <input type="text" id="prli-portal-terms-of-service-url" name="prli_portal_terms_of_service_url" class="regular-text" value="<?php echo esc_attr(PrliStripeHelper::get_portal_config_value('business_profile.terms_of_service_url')); ?>">
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-customer-update"><?php esc_html_e('Customer Update', 'pretty-link'); ?></label>
            </th>
            <td>
              <label for="prli-portal-customer-update"><input type="checkbox" id="prli-portal-customer-update" name="prli_portal_customer_update_enabled" <?php checked(PrliStripeHelper::get_portal_config_value('features.customer_update.enabled')); ?>> <?php esc_html_e('Allow customers to update their data', 'pretty-link'); ?><span class="prli-recommended"><?php esc_html_e('Recommended', 'pretty-link'); ?></span></label>
              <div class="prli-portal-sub-options<?php echo !PrliStripeHelper::get_portal_config_value('features.customer_update.enabled') ? ' prli-hidden' : ''; ?>">
                <?php
                  $allowed_updates = array(
                    'name' => __('Allow updating names', 'pretty-link'),
                    'email' => __('Allow updating email addresses', 'pretty-link'),
                    'address' => __('Allow updating billing addresses', 'pretty-link'),
                    'shipping' => __('Allow updating shipping addresses', 'pretty-link'),
                    'phone' => __('Allow updating phone numbers', 'pretty-link'),
                    'tax_id' => __('Allow updating tax IDs', 'pretty-link')
                  );
                ?>
                <?php foreach($allowed_updates as $key => $label) : ?>
                  <div>
                    <label><input type="checkbox" name="prli_portal_allowed_updates[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, PrliStripeHelper::get_portal_config_value('features.customer_update.allowed_updates'), true)); ?>> <?php echo esc_html($label); ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-payment-method-update"><?php esc_html_e('Payment Method Update', 'pretty-link'); ?></label>
            </th>
            <td>
              <label for="prli-portal-payment-method-update"><input type="checkbox" id="prli-portal-payment-method-update" name="prli_portal_payment_method_update_enabled" <?php checked(PrliStripeHelper::get_portal_config_value('features.payment_method_update.enabled')); ?>> <?php esc_html_e('Allow customers to update their payment method', 'pretty-link'); ?><span class="prli-recommended"><?php esc_html_e('Recommended', 'pretty-link'); ?></span></label>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-subscription-cancel"><?php esc_html_e('Subscription Cancel', 'pretty-link'); ?></label>
            </th>
            <td>
              <label for="prli-portal-subscription-cancel"><input type="checkbox" id="prli-portal-subscription-cancel" name="prli_portal_subscription_cancel_enabled" <?php checked(PrliStripeHelper::get_portal_config_value('features.subscription_cancel.enabled')); ?>> <?php esc_html_e('Allow subscriptions to be cancelled', 'pretty-link'); ?><span class="prli-recommended"><?php esc_html_e('Recommended', 'pretty-link'); ?></span></label>
              <div class="prli-portal-sub-options<?php echo !PrliStripeHelper::get_portal_config_value('features.subscription_cancel.enabled') ? ' prli-hidden' : ''; ?>">
                <div>
                  <select id="prli-portal-subscription-cancel-mode" name="prli_portal_subscription_cancel_mode">
                    <option value="at_period_end" <?php selected(PrliStripeHelper::get_portal_config_value('features.subscription_cancel.mode'), 'at_period_end'); ?>><?php esc_html_e('Cancel at end of billing period (customer can renew until then)', 'pretty-link'); ?></option>
                    <option value="immediately" <?php selected(PrliStripeHelper::get_portal_config_value('features.subscription_cancel.mode'), 'immediately'); ?>><?php esc_html_e('Cancel subscriptions immediately', 'pretty-link'); ?></option>
                  </select>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="prli-portal-invoice-history"><?php esc_html_e('Invoice History', 'pretty-link'); ?></label>
            </th>
            <td>
              <label for="prli-portal-invoice-history"><input type="checkbox" id="prli-portal-invoice-history" name="prli_portal_invoice_history_enabled" <?php checked(PrliStripeHelper::get_portal_config_value('features.invoice_history.enabled')); ?>> <?php esc_html_e('Show a history of paid invoices', 'pretty-link'); ?><span class="prli-recommended"><?php esc_html_e('Recommended', 'pretty-link'); ?></span></label>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script>
  jQuery(document).ready(function($) {
    jQuery('body').on('click', '.prli_stripe_disconnect_button', function(e) {
      var proceed = confirm( jQuery(this).data('disconnect-msg') );
      if ( false === proceed ) {
        e.preventDefault();
      }
    });

    <?php if( isset($_GET['nav_action']) && 'payments' === $_GET['nav_action']) : ?>
      jQuery('#prli-nav-payments').trigger('click');
    <?php endif; ?>
  });
</script>
