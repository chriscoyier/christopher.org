<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<tr>
  <th scope="row">
    <?php esc_html_e('Product*', 'pretty-link'); ?>
  </th>
  <td>
    <div id="prli-stripe-line-items">
      <?php
        $hide_product_selector = false;

        if(!empty($values['stripe_line_items'])) {
          $line_items = json_decode($values['stripe_line_items'], true);

          if(is_array($line_items)) {
            foreach($line_items as $line_item) {
              if(is_array($line_item)) {
                echo PrliStripeHelper::render_line_item($line_item);
                $hide_product_selector = true;
              }
            }
          }
        }
      ?>
    </div>
    <div id="prli-stripe-product-selector" class="prli-width-500<?php echo $hide_product_selector ? ' prli-hidden' : ''; ?>">
      <select id="prli-stripe-product-select"></select>
    </div>
    <textarea class="prli-hidden" name="prli_stripe_line_items"><?php echo esc_textarea($values['stripe_line_items']); ?></textarea>
    <div id="prli-stripe-add-product-popup" class="prli-popup mfp-hide">
      <div class="prli-stripe-add-product-title"><?php esc_html_e('Add a new product', 'pretty-link'); ?></div>
      <div class="prli-stripe-add-product-options">
        <div class="prli-stripe-add-product-option">
          <label for="prli_stripe_add_product_name"><?php esc_html_e('Name', 'pretty-link'); ?></label>
          <div class="prli-stripe-add-product-field">
            <input type="text" id="prli_stripe_add_product_name" placeholder="<?php esc_attr_e('Premium Plan, sunglasses, etc.', 'pretty-link'); ?>">
          </div>
        </div>
        <div class="prli-stripe-add-product-option">
          <label for="prli_stripe_add_product_price"><?php esc_html_e('Price', 'pretty-link'); ?></label>
          <div class="prli-stripe-add-product-field">
            <div class="prli-stripe-add-product-price">
              <input type="text" id="prli_stripe_add_product_price" placeholder="0.00">
              <select id="prli_stripe_add_product_currency" aria-label="<?php esc_html_e('Currency', 'pretty-link'); ?>">
                <?php foreach(PrliUtils::currencies() as $code => $name) : ?>
                  <option value="<?php echo esc_attr($code); ?>" <?php selected($prli_options->prettypay_default_currency, $code); ?>><?php echo esc_attr("$code - $name"); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="prli-stripe-add-product-option prli-stripe-recurring-tiles">
          <div>
            <input type="radio" id="prli_stripe_add_product_type_recurring" name="prli_stripe_recurring" value="recurring">
            <label for="prli_stripe_add_product_type_recurring"><?php esc_html_e('Recurring', 'pretty-link'); ?></label>
          </div>
          <div>
            <input type="radio" id="prli_stripe_add_product_type_one_time" name="prli_stripe_recurring" value="one_time" checked>
            <label for="prli_stripe_add_product_type_one_time"><?php esc_html_e('One time', 'pretty-link'); ?></label>
          </div>
        </div>
        <div id="prli-stripe-add-product-recurring-options" class="prli-stripe-add-product-option prli-hidden">
          <label for="prli_stripe_add_product_billing_period"><?php esc_html_e('Billing period', 'pretty-link'); ?></label>
          <div class="prli-stripe-add-product-field">
            <select id="prli_stripe_add_product_billing_period">
              <option value="day"><?php esc_html_e('Daily', 'pretty-link'); ?></option>
              <option value="week"><?php esc_html_e('Weekly', 'pretty-link'); ?></option>
              <option value="month" selected><?php esc_html_e('Monthly', 'pretty-link'); ?></option>
              <option value="quarter"><?php esc_html_e('Every 3 months', 'pretty-link'); ?></option>
              <option value="semiannual"><?php esc_html_e('Every 6 months', 'pretty-link'); ?></option>
              <option value="year"><?php esc_html_e('Yearly', 'pretty-link'); ?></option>
              <option value="custom"><?php esc_html_e('Custom', 'pretty-link'); ?></option>
            </select>
          </div>
          <div id="prli-stripe-add-product-recurring-custom" class="prli-stripe-add-product-field prli-hidden">
            <div class="prli-stripe-add-product-recurring-custom">
              <span><?php esc_html_e('every', 'pretty-link'); ?></span>
              <input type="text" id="prli_stripe_add_product_interval_count" class="small-text" value="2" aria-label="<?php esc_html_e('Interval count', 'pretty-link'); ?>">
              <select id="prli_stripe_add_product_interval" aria-label="<?php esc_html_e('Interval', 'pretty-link'); ?>">
                <option value="day"><?php esc_html_e('days', 'pretty-link'); ?></option>
                <option value="week"><?php esc_html_e('weeks', 'pretty-link'); ?></option>
                <option value="month" selected><?php esc_html_e('months', 'pretty-link'); ?></option>
              </select>
            </div>
          </div>
        </div>
        <div class="prli-stripe-add-product-option">
          <label for="prli_stripe_add_product_tax_behavior"><?php esc_html_e('Include tax in price', 'pretty-link'); ?></label>
          <div class="prli-stripe-add-product-field">
            <select id="prli_stripe_add_product_tax_behavior">
              <option value="unspecified"><?php esc_html_e('Default', 'pretty-link'); ?></option>
              <option value="inclusive"><?php esc_html_e('Price is inclusive of tax', 'pretty-link'); ?></option>
              <option value="exclusive"><?php esc_html_e('Price is exclusive of tax', 'pretty-link'); ?></option>
            </select>
          </div>
        </div>
        <div class="prli-stripe-add-product-buttons">
          <button type="button" id="prli-stripe-add-product-cancel" class="button button-secondary"><?php esc_html_e('Cancel', 'pretty-link'); ?></button>
          <button type="button" id="prli-stripe-add-product-save" class="button button-primary"><?php esc_html_e('Add product', 'pretty-link'); ?></button>
        </div>
      </div>
    </div>
  </td>
</tr>
<tr>
  <th scope="row">
    <?php esc_html_e('Options', 'pretty-link'); ?>
  </th>
  <td>
    <div id="prli-stripe-prettypay-link-options">
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" name="prli_stripe_automatic_tax" <?php checked($values['stripe_automatic_tax']); ?>>
          <?php esc_html_e('Collect tax automatically', 'pretty-link'); ?>
        </label>
        <?php
          PrliAppHelper::info_tooltip(
            'prli-stripe-automatic-tax',
            esc_html__('Collect tax automatically', 'pretty-link'),
            sprintf(
              /* translators: %s: link to Stripe Tax setup docs */
              esc_html__('Tax will be calculated automatically based on the address provided by the customer. Stripe Tax must be enabled on the connected Stripe account. Additional charges may apply. Please visit %s to get started.', 'pretty-link'),
              sprintf('<a href="%1$s" target="_blank">%1$s</a>', 'https://stripe.com/docs/tax/set-up')
            )
          );
        ?>
      </div>
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" id="prli_stripe_billing_address_collection" name="prli_stripe_billing_address_collection" <?php checked($values['stripe_billing_address_collection']); ?>>
          <?php esc_html_e('Collect customers\' addresses', 'pretty-link'); ?>
        </label>
        <div class="prli-stripe-prettypay-link-sub-options<?php echo !$values['stripe_billing_address_collection'] ? ' prli-hidden' : ''; ?>">
          <div class="prli-stripe-prettypay-link-sub-option">
            <label>
              <input type="radio" name="prli_stripe_shipping_address_collection" value="0" <?php checked(!$values['stripe_shipping_address_collection']); ?>>
              <?php esc_html_e('Billing addresses only', 'pretty-link'); ?>
            </label>
          </div>
          <div class="prli-stripe-prettypay-link-sub-option">
            <label>
              <input type="radio" name="prli_stripe_shipping_address_collection" value="1" <?php checked($values['stripe_shipping_address_collection']); ?>>
              <?php esc_html_e('Billing and shipping addresses', 'pretty-link'); ?>
            </label>
            <div class="prli-stripe-prettypay-link-sub-option prli-width-500<?php echo !$values['stripe_shipping_address_collection'] ? ' prli-hidden' : ''; ?>">
              <select multiple id="prli_stripe_shipping_address_allowed_countries" name="prli_stripe_shipping_address_allowed_countries[]">
                <?php foreach(PrliStripeHelper::shipping_countries() as $code => $name) : ?>
                  <option value="<?php echo esc_attr($code); ?>"<?php selected(in_array($code, $values['stripe_shipping_address_allowed_countries'], true)); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" name="prli_stripe_phone_number_collection" <?php checked($values['stripe_phone_number_collection']); ?>>
          <?php esc_html_e('Require customers to provide a phone number', 'pretty-link'); ?>
        </label>
      </div>
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" name="prli_stripe_allow_promotion_codes" <?php checked($values['stripe_allow_promotion_codes']); ?>>
          <?php esc_html_e('Allow promotion codes', 'pretty-link'); ?>
        </label>
      </div>
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" name="prli_stripe_tax_id_collection" <?php checked($values['stripe_tax_id_collection']); ?>>
          <?php esc_html_e('Allow business customers to provide tax IDs', 'pretty-link'); ?>
        </label>
        <?php
          PrliAppHelper::info_tooltip(
            'prli-stripe-tax-id-collection',
            esc_html__('Allow business customers to provide tax IDs', 'pretty-link'),
            sprintf(
              /* translators: %s: link to Stripe Tax ID docs */
              esc_html__('This displays an additional field for businesses to provide their VAT or other tax ID. Stripe only displays this field to customers in certain countries. %s', 'pretty-link'),
              sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://stripe.com/docs/billing/customer/tax-ids', esc_html__('View docs', 'pretty-link'))
            )
          );
        ?>
      </div>
      <div class="prli-stripe-prettypay-link-option">
        <label>
          <input type="checkbox" name="prli_stripe_save_payment_details" <?php checked($values['stripe_save_payment_details']); ?>>
          <?php esc_html_e('Save payment details for future use', 'pretty-link'); ?>
        </label>
      </div>
      <div class="prli-stripe-prettypay-link-option prli-stripe-recurring-only">
        <label>
          <input type="checkbox" id="prli_stripe_include_free_trial" name="prli_stripe_include_free_trial" <?php checked($values['stripe_include_free_trial']); ?>>
          <?php esc_html_e('Include a free trial', 'pretty-link'); ?>
        </label>
        <div class="prli-stripe-prettypay-link-sub-options<?php echo !$values['stripe_include_free_trial'] ? ' prli-hidden' : ''; ?>">
          <div class="prli-stripe-prettypay-link-sub-option">
            <label>
              <input type="text" name="prli_stripe_trial_period_days" class="small-text" value="<?php echo esc_attr($values['stripe_trial_period_days']); ?>">
              <?php esc_html_e('days', 'pretty-link'); ?>
            </label>
          </div>
        </div>
      </div>
    </div>
  </td>
</tr>
<tr>
  <th scope="row">
    <label for="prli_stripe_custom_text"><?php esc_html_e('Checkout Message', 'pretty-link'); ?></label>
    <?php
      PrliAppHelper::info_tooltip(
        'prli-stripe-custom-text',
        esc_html__('Checkout Message', 'pretty-link'),
        esc_html__('Custom text that should be displayed alongside the payment confirmation button (up to 1000 characters).', 'pretty-link')
      );
    ?>
  </th>
  <td>
    <input type="text" id="prli_stripe_custom_text" name="prli_stripe_custom_text" class="large-text" maxlength="1000" value="<?php echo esc_attr($values['stripe_custom_text']); ?>">
  </td>
</tr>
<tr>
  <th scope="row">
    <?php esc_html_e('Thank You Page', 'pretty-link'); ?>
  </th>
  <td>
    <?php
      global $prli_options;
      $empty_option = __('Default (Homepage)', 'pretty-link');
      $thank_you_page = is_numeric($prli_options->prettypay_thank_you_page_id) ? get_post($prli_options->prettypay_thank_you_page_id) : null;

      if($thank_you_page instanceof WP_Post) {
        $empty_option = sprintf(
          /* translators: %s: thank you page title */
          __('Default (%s)', 'pretty-link'),
          $thank_you_page->post_title
        );
      }

      PrliAppHelper::wp_pages_dropdown(
        'prli_stripe_thank_you_page_id',
        $values['stripe_thank_you_page_id'],
        false,
        $empty_option
      );

      if(!$thank_you_page instanceof WP_Post && empty($values['stripe_thank_you_page_id'])) {
        printf(
          '<a class="prli-ty-page-settings" href="%1$s">%2$s</a>',
          esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options&nav_action=payments')),
          esc_html__('Settings', 'pretty-link')
        );
      }
    ?>
  </td>
</tr>
