<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliStripeHelper {
  public static function render_line_item(array $line_item) {
    $price = $line_item['price'];

    ob_start();
    ?>
    <div class="prli-stripe-line-item-box" data-line-item="<?php echo esc_attr(wp_json_encode($line_item)); ?>">
      <div class="prli-stripe-price-image">
        <?php if(isset($price['product']['images'][0])) : ?>
          <div class="prli-stripe-price-image-url" style="background-image: url('<?php echo esc_url_raw($price['product']['images'][0]); ?>');"></div>
        <?php else : ?>
          <div class="prli-stripe-price-image-default">
            <svg aria-hidden="true" height="16" width="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M13.788 3.119a1.04 1.04 0 0 1-.31.283L8.5 6.362a.97.97 0 0 1-.998 0l-4.98-2.96a1.04 1.04 0 0 1-.309-.283L6.99.279a1.97 1.97 0 0 1 2.02 0zm1.194 1.647c.012.09.018.182.018.274v5.92c0 .743-.385 1.43-1.01 1.802l-4.98 2.96a1.97 1.97 0 0 1-2.02 0l-4.98-2.96A2.092 2.092 0 0 1 1 10.96V5.04c0-.092.006-.184.018-.274.147.133.308.252.481.355l4.98 2.96a2.97 2.97 0 0 0 3.042 0l4.98-2.96c.173-.103.334-.222.481-.355z" fill-rule="evenodd"></path></svg>
          </div>
        <?php endif; ?>
      </div>
      <div class="prli-stripe-product-name-price">
        <div class="prli-stripe-product-name"><?php echo esc_html($price['product']['name']); ?></div>
        <div class="prli-stripe-product-price"><?php echo esc_html(self::format_price($price)); ?></div>
      </div>
      <div class="prli-stripe-line-item-x">
        <svg aria-hidden="true" height="12" width="12" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m8 6.585 4.593-4.592a1 1 0 0 1 1.415 1.416L9.417 8l4.591 4.591a1 1 0 0 1-1.415 1.416L8 9.415l-4.592 4.592a1 1 0 0 1-1.416-1.416L6.584 8l-4.59-4.591a1 1 0 1 1 1.415-1.416z" fill-rule="evenodd"></path></svg>
      </div>
    </div>
    <?php

    return ob_get_clean();
  }

  public static function format_price($price) {
    $interval = '';

    if($price['recurring']) {
      $interval = $price['recurring']['interval'];

      if((int) $price['recurring']['interval_count'] !== 1) {
        $interval = sprintf('%d %ss', $price['recurring']['interval_count'], $interval);
      }

      $interval = ' / ' . $interval;
    }

    return sprintf(
      '%s %s%s',
      strtoupper($price['currency']),
      self::format_unit_amount($price['unit_amount'], $price['currency']),
      $interval
    );
  }

  public static function format_unit_amount($amount, $currency) {
    $amount = (float) $amount;

    if(self::is_zero_decimal_currency($currency)) {
      return number_format_i18n($amount);
    }

    return number_format_i18n($amount / 100, 2);
  }

  public static function is_zero_decimal_currency($currency) {
    $zero_decimal_currencies = apply_filters('prli_stripe_zero_decimal_currencies', array(
      'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
    ));

    return in_array(strtoupper($currency), $zero_decimal_currencies, true);
  }

  public static function format_currency($amount, $currency) {
    return sprintf(
      '%s %s',
      strtoupper($currency),
      self::format_unit_amount($amount, $currency)
    );
  }

  public static function shipping_countries() {
    $countries = PrliUtils::countries();
    $unsupported = array('AS', 'CX', 'CC', 'CU', 'HM', 'IR', 'KP', 'MH', 'FM', 'NF', 'MP', 'PW', 'SD', 'SY', 'UM', 'VI');

    foreach($unsupported as $key) {
      unset($countries[$key]);
    }

    return apply_filters('prli_stripe_shipping_countries', $countries);
  }

  public static function to_zero_decimal_amount($amount, $currency) {
    if(self::is_zero_decimal_currency($currency)) {
      return (int) $amount;
    }

    return (int) ($amount * 100);
  }

  public static function get_portal_config_value($key) {
    $portal = get_option('prli_stripe_customer_portal');

    $defaults = array(
      'business_profile' => array(
        'headline' => __('Manage your Payments', 'pretty-link'),
        'privacy_policy_url' => '',
        'terms_of_service_url' => '',
      ),
      'default_return_url' => home_url('/'),
      'features' => array(
        'customer_update' => array(
          'allowed_updates' => array('name', 'email', 'address', 'shipping', 'phone'),
          'enabled' => true
        ),
        'invoice_history' => array(
          'enabled' => true
        ),
        'payment_method_update' => array(
          'enabled' => true
        ),
        'subscription_cancel' => array(
          'enabled' => true
        )
      )
    );

    $default = PrliUtils::array_get($defaults, $key);

    if(is_array($portal)) {
      return PrliUtils::array_get($portal, $key, $default);
    }

    return $default;
  }

  /**
   * Get the name of the page that will redirect to the customer portal
   *
   * @return string
   */
  public static function get_customer_portal_page_name() {
    return apply_filters('pl_customer_portal_page_name', 'pl-customer-portal');
  }

  /**
   * Is the connection to Stripe active?
   *
   * @return bool
   */
  public static function is_connection_active() {
    if(defined('PRLI_STRIPE_TEST_MODE') && PRLI_STRIPE_TEST_MODE) {
      return !empty(get_option('prli_stripe_test_secret_key'));
    }

    return !empty(get_option('prli_stripe_live_secret_key'));
  }
}
