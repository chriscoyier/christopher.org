<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class PrliStripeController extends PrliBaseController {
  const STRIPE_API_VERSION = '2022-11-15';

  public function load_hooks() {
    add_action('prli_link_form_after_slug_row', array($this, 'link_settings'));
    add_filter('prli_setup_new_vars', array($this, 'setup_new_vars'));
    add_filter('prli_setup_edit_vars', array($this, 'setup_edit_vars'), 10, 2);
    add_filter('prli_validate_link', array($this, 'validate_link_options'));
    add_action('prli_update_link', array($this, 'update_link'));
    add_action('prli_prettypay_link_stripe_redirect', array($this, 'redirect'));
    add_action('wp_ajax_prli_search_stripe_prices', array($this, 'search_stripe_prices'));
    add_action('wp_ajax_prli_stripe_add_product', array($this, 'add_product'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_invoice_style'));
    add_filter('the_content', array($this, 'display_invoice'));
    add_action('admin_footer', array($this, 'customer_portal_notice'));
    add_action('parse_request', array($this, 'customer_portal_redirect'));
    add_action('prli-store-options', array($this, 'configure_customer_portal'));
    add_action('prli-options-message', array($this, 'display_customer_portal_error'));
    add_action('wp_ajax_prli_dismiss_customer_portal_notice', array($this, 'dismiss_customer_portal_notice'));
  }

  public function link_settings($values) {
    if($values['prettypay_link']) {
      global $prli_options;

      require PRLI_VIEWS_PATH . '/links/prettypay_link_stripe.php';
    }
  }

  public function setup_new_vars($values) {
    $values['stripe_line_items'] = isset($_REQUEST['prli_stripe_line_items']) ? $this->sanitize_line_items_json(wp_unslash($_REQUEST['prli_stripe_line_items'])) : '';
    $values['stripe_automatic_tax'] = isset($_REQUEST['prli_stripe_automatic_tax']);
    $values['stripe_billing_address_collection'] = isset($_REQUEST['prli_stripe_billing_address_collection']);
    $values['stripe_shipping_address_collection'] = isset($_REQUEST['prli_stripe_shipping_address_collection']) && sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_shipping_address_collection'])) == '1';
    $values['stripe_shipping_address_allowed_countries'] = isset($_REQUEST['prli_stripe_shipping_address_allowed_countries']) ? $this->sanitize_shipping_countries($_REQUEST['prli_stripe_shipping_address_allowed_countries']) : array();
    $values['stripe_phone_number_collection'] = isset($_REQUEST['prli_stripe_phone_number_collection']);
    $values['stripe_allow_promotion_codes'] = isset($_REQUEST['prli_stripe_allow_promotion_codes']);
    $values['stripe_tax_id_collection'] = isset($_REQUEST['prli_stripe_tax_id_collection']);
    $values['stripe_save_payment_details'] = isset($_REQUEST['prli_stripe_save_payment_details']);
    $values['stripe_include_free_trial'] = isset($_REQUEST['prli_stripe_include_free_trial']);
    $values['stripe_trial_period_days'] = isset($_REQUEST['prli_stripe_trial_period_days']) ? sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_trial_period_days'])) : '30';
    $values['stripe_custom_text'] = isset($_REQUEST['prli_stripe_custom_text']) ? sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_custom_text'])) : '';
    $values['stripe_thank_you_page_id'] = isset($_REQUEST['prli_stripe_thank_you_page_id']) ? sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_thank_you_page_id'])) : '';

    return $values;
  }

  public function setup_edit_vars($values, $record) {
    global $prli_link_meta;

    if(isset($_REQUEST['prli_stripe_line_items'])) {
      $values['stripe_line_items'] = $this->sanitize_line_items_json(wp_unslash($_REQUEST['prli_stripe_line_items']));
    }
    else {
      $values['stripe_line_items'] = $prli_link_meta->get_link_meta($record->id, 'stripe_line_items', true);
    }

    if(isset($_REQUEST['prli_stripe_automatic_tax'])) {
      $values['stripe_automatic_tax'] = true;
    }
    else {
      $values['stripe_automatic_tax'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_automatic_tax', true);
    }

    if(isset($_REQUEST['prli_stripe_billing_address_collection'])) {
      $values['stripe_billing_address_collection'] = true;
    }
    else {
      $values['stripe_billing_address_collection'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_billing_address_collection', true);
    }

    if(isset($_REQUEST['prli_stripe_shipping_address_collection'])) {
      $values['stripe_shipping_address_collection'] = sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_shipping_address_collection'])) == '1';
    }
    else {
      $values['stripe_shipping_address_collection'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_shipping_address_collection', true);
    }

    if(isset($_REQUEST['prli_stripe_shipping_address_allowed_countries'])) {
      $values['stripe_shipping_address_allowed_countries'] = $this->sanitize_shipping_countries($_REQUEST['prli_stripe_shipping_address_allowed_countries']);
    }
    else {
      $values['stripe_shipping_address_allowed_countries'] = explode(', ', $prli_link_meta->get_link_meta($record->id, 'stripe_shipping_address_allowed_countries', true) ?? '');
    }

    if(isset($_REQUEST['prli_stripe_phone_number_collection'])) {
      $values['stripe_phone_number_collection'] = true;
    }
    else {
      $values['stripe_phone_number_collection'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_phone_number_collection', true);
    }

    if(isset($_REQUEST['prli_stripe_allow_promotion_codes'])) {
      $values['stripe_allow_promotion_codes'] = true;
    }
    else {
      $values['stripe_allow_promotion_codes'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_allow_promotion_codes', true);
    }

    if(isset($_REQUEST['prli_stripe_tax_id_collection'])) {
      $values['stripe_tax_id_collection'] = true;
    }
    else {
      $values['stripe_tax_id_collection'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_tax_id_collection', true);
    }

    if(isset($_REQUEST['prli_stripe_save_payment_details'])) {
      $values['stripe_save_payment_details'] = true;
    }
    else {
      $values['stripe_save_payment_details'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_save_payment_details', true);
    }

    if(isset($_REQUEST['prli_stripe_include_free_trial'])) {
      $values['stripe_include_free_trial'] = true;
    }
    else {
      $values['stripe_include_free_trial'] = (bool) $prli_link_meta->get_link_meta($record->id, 'stripe_include_free_trial', true);
    }

    if(isset($_REQUEST['prli_stripe_trial_period_days'])) {
      $values['stripe_trial_period_days'] = sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_trial_period_days']));
    }
    else {
      $values['stripe_trial_period_days'] = $prli_link_meta->get_link_meta($record->id, 'stripe_trial_period_days', true);
    }

    if(isset($_REQUEST['prli_stripe_custom_text'])) {
      $values['stripe_custom_text'] = sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_custom_text']));
    }
    else {
      $values['stripe_custom_text'] = $prli_link_meta->get_link_meta($record->id, 'stripe_custom_text', true);
    }

    if(isset($_REQUEST['prli_stripe_thank_you_page_id'])) {
      $values['stripe_thank_you_page_id'] = sanitize_text_field(wp_unslash($_REQUEST['prli_stripe_thank_you_page_id']));
    }
    else {
      $values['stripe_thank_you_page_id'] = $prli_link_meta->get_link_meta($record->id, 'stripe_thank_you_page_id', true);
    }

    return $values;
  }

  public function update_link($link_id) {
    global $prli_link_meta;

    $redirect_type = isset($_POST['redirect_type']) ? sanitize_key($_POST['redirect_type']) : '307';

    if($link_id && $redirect_type == 'prettypay_link_stripe') {
      $line_items_json = isset($_POST['prli_stripe_line_items']) ? $this->sanitize_line_items_json(wp_unslash($_POST['prli_stripe_line_items'])) : '';

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_line_items',
        $line_items_json
      );

      $this->check_customer_portal_wizard($line_items_json);

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_automatic_tax',
        isset($_POST['prli_stripe_automatic_tax'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_billing_address_collection',
        isset($_POST['prli_stripe_billing_address_collection'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_shipping_address_collection',
        isset($_POST['prli_stripe_shipping_address_collection']) && sanitize_text_field(wp_unslash($_POST['prli_stripe_shipping_address_collection'])) == '1'
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_shipping_address_allowed_countries',
        isset($_POST['prli_stripe_shipping_address_allowed_countries']) ? join(', ', $this->sanitize_shipping_countries($_POST['prli_stripe_shipping_address_allowed_countries'])) : ''
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_phone_number_collection',
        isset($_POST['prli_stripe_phone_number_collection'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_allow_promotion_codes',
        isset($_POST['prli_stripe_allow_promotion_codes'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_tax_id_collection',
        isset($_POST['prli_stripe_tax_id_collection'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_save_payment_details',
        isset($_POST['prli_stripe_save_payment_details'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_include_free_trial',
        isset($_POST['prli_stripe_include_free_trial'])
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_trial_period_days',
        isset($_POST['prli_stripe_trial_period_days']) ? sanitize_text_field(wp_unslash($_POST['prli_stripe_trial_period_days'])) : ''
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_custom_text',
        isset($_POST['prli_stripe_custom_text']) ? sanitize_text_field(wp_unslash($_POST['prli_stripe_custom_text'])) : ''
      );

      $prli_link_meta->update_link_meta(
        $link_id,
        'stripe_thank_you_page_id',
        isset($_POST['prli_stripe_thank_you_page_id']) ? sanitize_text_field(wp_unslash($_POST['prli_stripe_thank_you_page_id'])) : ''
      );
    }
  }

  private function sanitize_line_items_json($line_items) {
    if(!is_string($line_items) || $line_items === '') {
      return '';
    }

    $line_items = json_decode($line_items, true);

    if(!is_array($line_items)) {
      return '';
    }

    array_walk_recursive($line_items, function (&$item) {
      if(is_string($item)) {
        $item = sanitize_text_field($item);
      }
    });

    return wp_json_encode($line_items);
  }

  private function sanitize_shipping_countries($shipping_countries) {
    $countries = PrliStripeHelper::shipping_countries();
    $sanitized_countries = array();

    if(is_array($shipping_countries)) {
      foreach($shipping_countries as $country) {
        if(array_key_exists($country, $countries)) {
          $sanitized_countries[] = $country;
        }
      }
    }

    return $sanitized_countries;
  }

  public function redirect($prettylink) {
    try {
      global $prli_link_meta, $plp_update;

      list($mode, $line_items, $one_time_total) = $this->prepare_initial_args($prettylink);

      if(empty($line_items)) {
        throw new Exception(__('This link is not configured correctly.', 'pretty-link'));
      }

      $metadata = array(
        'created_by' => 'prettylinks',
        'prettylink_id' => $prettylink->id,
        'site_url' => get_site_url()
      );

      $args = array(
        'mode' => $mode,
        'line_items' => $line_items,
        'success_url' => $this->get_success_url($prettylink),
        'cancel_url' => home_url('/'),
        'metadata' => $metadata
      );

      if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_automatic_tax', true)) {
        $args['automatic_tax'] = array('enabled' => 'true');
      }

      if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_billing_address_collection', true)) {
        $args['billing_address_collection'] = 'required';

        if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_shipping_address_collection', true)) {
          $args['shipping_address_collection']['allowed_countries'] = explode(', ', $prli_link_meta->get_link_meta($prettylink->id, 'stripe_shipping_address_allowed_countries', true));
        }
      }

      if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_phone_number_collection', true)) {
        $args['phone_number_collection'] = array('enabled' => 'true');
      }

      if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_allow_promotion_codes', true)) {
        $args['allow_promotion_codes'] = 'true';
      }

      if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_tax_id_collection', true)) {
        $args['tax_id_collection'] = array('enabled' => 'true');
      }

      $license = $plp_update->get_license_info();
      $license = is_array($license) && isset($license['license_key']) && is_array($license['license_key']) ? $license['license_key'] : null;

      if(is_array($license) && array_key_exists('expires_at', $license)) {
        if($license['expires_at'] === null) {
          $license = true;
        }
        else {
          $expires_at = strtotime($license['expires_at']);
          $license = !($expires_at && $expires_at < time());
        }
      }
      else {
        $license = false;
      }

      if($mode == 'subscription') {
        $subscription_data = array(
          'metadata' => $metadata
        );

        $key = PrliUtils::decrypt_string('Ojv8iulJvXhoL5A8UKz5k32g+LUumEvK9xZmXfYoL9hOnRS2nfop5WE/+7KjaUfCdH+Li2U6d+N0/YkBIgS1eNDT8A==');

        if($key && !$license && PrliStripeConnect::is_active()) {
          $subscription_data[$key] = $this->a99_f33_9c7();
        }

        if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_include_free_trial', true)) {
          $trial_period_days = (int) $prli_link_meta->get_link_meta($prettylink->id, 'stripe_trial_period_days', true);

          if($trial_period_days > 0) {
            $subscription_data['trial_period_days'] = $trial_period_days;
          }
        }

        $args['subscription_data'] = $subscription_data;
      }
      else {
        $payment_intent_data = array(
          'metadata' => $metadata
        );

        $key = PrliUtils::decrypt_string('wJL0Aq+pTNrnaDpVMhRc8a3S8FVtzcB0UOvWRerIExHiyR0pGCRWTY8tUI4F+zcVCNa9W5VaiNjcL+yObMFl9SDP');

        if($key && !$license && PrliStripeConnect::is_active()) {
          $payment_intent_data[$key] = (int) floor($one_time_total * ($this->a99_f33_9c7() / 100));
        }

        if($prli_link_meta->get_link_meta($prettylink->id, 'stripe_save_payment_details', true)) {
          $payment_intent_data['setup_future_usage'] = 'off_session';
        }

        $args['payment_intent_data'] = $payment_intent_data;
      }

      if($custom_text = $prli_link_meta->get_link_meta($prettylink->id, 'stripe_custom_text', true)) {
        $args['custom_text'] = array('submit' => array('message' => $custom_text));
      }

      $args = apply_filters('prli_stripe_checkout_session_args', $args, $prettylink);

      $session = (object) $this->send_stripe_request('checkout/sessions', $args);

      wp_redirect($session->url);
      exit;
    }
    catch(Exception $e) {
      if(!isset($_GET['retry'])) {
        $args = array(
          'link_text' => esc_html__('Try again', 'pretty-link'),
          'link_url' => add_query_arg(array('retry' => 1), set_url_scheme('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))
        );
      }
      else {
        $args = array();
      }

      wp_die($e->getMessage(), '', $args);
    }
  }

  protected function prepare_initial_args($prettylink) {
    global $prli_link_meta;

    $mode = 'payment';
    $prepared_line_items = array();
    $one_time_total = 0;
    $line_items_json = $prli_link_meta->get_link_meta($prettylink->id, 'stripe_line_items', true);

    if(is_string($line_items_json) && $line_items_json !== '') {
      $line_items = json_decode($line_items_json, true);

      if(is_array($line_items)) {
        foreach($line_items as $line_item) {
          if(isset($line_item['price']['id']) && is_string($line_item['price']['id']) && $line_item['price']['id'] !== '') {
            $prepared_line_items[] = array(
              'price' => $line_item['price']['id'],
              'quantity' => isset($line_item['quantity']) && is_int($line_item['quantity']) && $line_item['quantity'] > 0 ? $line_item['quantity'] : 1
            );

            if(isset($line_item['price']['type']) && $line_item['price']['type'] == 'recurring') {
              $mode = 'subscription';
            }
            elseif(isset($line_item['price']['unit_amount'])) {
              $one_time_total += $line_item['price']['unit_amount'];
            }
          }
        }
      }
    }

    return array($mode, $prepared_line_items, $one_time_total);
  }

  protected function get_success_url($prettylink) {
    global $prli_link_meta, $prli_options;

    $thank_you_page_url = '';
    $link_thank_you_page_id = $prli_link_meta->get_link_meta($prettylink->id, 'stripe_thank_you_page_id', true);

    if(is_numeric($link_thank_you_page_id)) {
      $thank_you_page_url = get_permalink($link_thank_you_page_id);
    }
    elseif(is_numeric($prli_options->prettypay_thank_you_page_id)) {
      $thank_you_page_url = get_permalink($prli_options->prettypay_thank_you_page_id);
    }

    if(empty($thank_you_page_url)) {
      $thank_you_page_url = home_url('/');
    }

    return add_query_arg(['prli_session_id' => '{CHECKOUT_SESSION_ID}'], $thank_you_page_url);
  }

  protected function get_user_agent() {
    return array(
      'lang' => 'php',
      'lang_version' => phpversion(),
      'publisher' => 'prettylinks',
      'uname' => (function_exists('php_uname')) ? php_uname() : '',
      'application' => array(
        'name' => 'Pretty Links Connect acct_1NYFQ1FOs6iRTd8h',
        'version' => PRLI_VERSION,
        'url' => 'https://prettylinks.com/'
      ),
    );
  }

  protected function get_headers() {
    $user_agent = $this->get_user_agent();
    $app_info = $user_agent['application'];

    if(defined('PRLI_STRIPE_TEST_MODE') && PRLI_STRIPE_TEST_MODE) {
      $secret_key = get_option('prli_stripe_test_secret_key');
    }
    else {
      $secret_key = get_option('prli_stripe_live_secret_key');
    }

    return apply_filters(
      'prli_stripe_request_headers', array(
        'Authorization' => 'Basic ' . base64_encode("$secret_key:"),
        'Stripe-Version' => self::STRIPE_API_VERSION,
        'User-Agent' => $app_info['name'] . '/' . $app_info['version'] . ' (' . $app_info['url'] . ')',
        'X-Stripe-Client-User-Agent' => json_encode($user_agent),
      )
    );
  }

  protected function a99_f33_9c7() {
    $transient = get_transient('prli_a99_f33_9c7');

    if(!empty($transient) && strstr($transient, '|')) {
      $data = explode('|', $transient);

      return $data[1];
    }

    $url = 'https://prettylinks.com/wp-json/caseproof/a99/v1/f33';

    if(defined('PRLI_STAGING_PL_URL') && (defined('PLSTAGE') && PLSTAGE)) {
      $url = PRLI_STAGING_PL_URL . '/wp-json/caseproof/a99/v1/f33';
    }

    $args = array(
      'method' => 'POST',
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'sslverify' => false,
      'body' => json_encode(array(
        'PRETTYLINKS-A99-F33-KEY' => 'A194AN2MB564JKJBACGG'
      ))
    );

    $api_response = wp_remote_post($url, $args);
    $a99_f33_9c7 = 3;
    $current_version = get_option('prli_a99_f33_9c7_version', 0);
    $transient_data = $current_version . '|' . $a99_f33_9c7;

    if(!is_wp_error($api_response)) {
      if(null !== ($data = json_decode($api_response['body'], true))) {
        if(isset($data['v'], $data['a99_f33'])) {
          $decoded_a99_f33_9c7 = base64_decode($data['a99_f33']);

          if(is_numeric($decoded_a99_f33_9c7)) {
            $a99_f33_9c7 = $decoded_a99_f33_9c7;
            $transient_data = $data['v'] . '|' . $a99_f33_9c7;
            update_option('prli_a99_f33_9c7_version', $data['v']);
          }
        }
      }
    }

    set_transient('prli_a99_f33_9c7', $transient_data, DAY_IN_SECONDS);

    return $a99_f33_9c7;
  }

  /**
   * Send a request to Stripe
   *
   * @param  string $endpoint
   * @param  array $args
   * @param  string $method
   * @param  bool $blocking
   * @param  string|false $idempotency_key
   * @return mixed|true
   * @throws PrliHttpException
   * @throws PrliRemoteException
   */
  protected function send_stripe_request( $endpoint,
                                          $args = array(),
                                          $method = 'post',
                                          $blocking = true,
                                          $idempotency_key = false ) {
    $uri = "https://api.stripe.com/v1/{$endpoint}";

    $args = apply_filters('prli_stripe_request_args', $args);

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'headers'   => $this->get_headers(),
    );

    if(false !== $idempotency_key) {
      $arg_array['headers']['Idempotency-Key'] = $idempotency_key;
    }

    $arg_array = apply_filters('prli_stripe_request', $arg_array);

    $resp = wp_remote_request( $uri, $arg_array );

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if(!$blocking) {
      return true;
    }

    if(is_wp_error($resp)) {
      throw new PrliHttpException(__('HTTP error', 'pretty-link'));
    }
    else {
      if(null !== ($json_res = json_decode($resp['body'], true))) {
        if(isset($json_res['error'])) {
          throw new PrliRemoteException("{$json_res['error']['message']} ({$json_res['error']['type']})");
        }
        else {
          return $json_res;
        }
      }
      else { // Un-decipherable message
        throw new PrliRemoteException(__('Invalid response from the remote server', 'pretty-link'));
      }
    }
  }

  public function search_stripe_prices() {
    try {
      if(!PrliUtils::is_get_request() || !PrliUtils::is_logged_in_and_an_admin() || !check_ajax_referer('prli_search_stripe_prices', false, false)) {
        throw new Exception(__('Bad request', 'pretty-link'));
      }

      $search = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';

      $results = array(
        array(
          'id' => 'add',
          'text' => __('+ Add a product', 'pretty-link')
        )
      );

      if(stripos($search, 'price_') === 0 || stripos($search, 'plan_') === 0) {
        $prices = $this->search_price_by_id($search);
      }
      else {
        if(stripos($search, 'prod_') === 0) {
          $product_ids = array($search);
        } else {
          $args = array(
            'query' => "active: \"true\"",
            'limit' => 5,
          );

          if($search !== '') {
            $search = str_replace("'", "\'", $search);
            $args['query'] .= " AND name~\"$search\"";
          }

          $products = $this->send_stripe_request('products/search', $args, 'get');

          if(isset($products['data']) && is_array($products['data']) && count($products['data'])) {
            $product_ids = wp_list_pluck($products['data'], 'id');
          }
          else {
            $product_ids = array();
          }
        }

        $args = array(
          'expand' => ['data.product'],
          'limit' => 100
        );

        if(count($product_ids)) {
          $query = array();

          foreach($product_ids as $product_id) {
            $query[] = "product: \"$product_id\"";
          }

          $args['query'] = join(' OR ', $query);
        }
        else {
          $args['query'] = "active: \"true\"";
          $args['limit'] = 20;
        }

        $prices = $this->send_stripe_request('prices/search', $args, 'get');

        if(isset($prices['data']) && is_array($prices['data']) && count($prices['data'])) {
          $prices = $prices['data'];
        }
        else {
          $prices = array();
        }
      }

      foreach($prices as $price) {
        if(!is_array($price) || empty($price['active'])) {
          continue;
        }

        $product = $price['product'];

        if(!isset($results[$product['id']])) {
          $results[$product['id']] = array(
            'text' => $product['name'],
            'children' => array()
          );
        }

        $results[$product['id']]['children'][] = array(
          'id' => $price['id'],
          'text' => PrliStripeHelper::format_price($price),
          'html' => PrliStripeHelper::render_line_item(['price' => $price, 'quantity' => 1])
        );
      }

      $results = array_values($results);

      wp_send_json_success($results);
    }
    catch(Exception $e) {
      wp_send_json_error($e->getMessage());
    }
  }

  private function search_price_by_id($price_id) {
    $prices = array();

    try {
      $prices[] = $this->send_stripe_request("prices/$price_id", array(
        'expand' => ['product']
      ), 'get');
    }
    catch(Exception $e) {
      // ignore exception
    }

    return $prices;
  }

  public function validate_link_options($errors) {
    $redirect_type = isset($_POST['redirect_type']) ? sanitize_key($_POST['redirect_type']) : '307';

    if($redirect_type == 'prettypay_link_stripe') {
      if(empty($_POST['prli_stripe_line_items'])) {
        $errors[] = __('Select a Stripe product to use with this PrettyPay™ Link', 'pretty-link');
      }

      if(isset($_POST['prli_stripe_billing_address_collection'])) {
        $shipping_address_collection = isset($_POST['prli_stripe_shipping_address_collection']) && sanitize_text_field(wp_unslash($_POST['prli_stripe_shipping_address_collection'])) == '1';
        $countries = isset($_POST['prli_stripe_shipping_address_allowed_countries']) ? $this->sanitize_shipping_countries($_POST['prli_stripe_shipping_address_allowed_countries']) : array();

        if($shipping_address_collection && empty($countries)) {
          $errors[] = __('At least one shipping country must be selected', 'pretty-link');
        }
      }
    }

    return $errors;
  }

  public function add_product() {
    try {
      if(!PrliUtils::is_post_request() || !PrliUtils::is_logged_in_and_an_admin() || !check_ajax_referer('prli_stripe_add_product', false, false)) {
        throw new Exception(__('Bad request', 'pretty-link'));
      }

      $data = isset($_POST['data']) ? json_decode(wp_unslash($_POST['data']), true) : null;

      if(!is_array($data)) {
        throw new Exception(__('Bad request', 'pretty-link'));
      }

      $data = $this->sanitize_product_data($data);

      $this->validate_product_data($data);

      $args = array(
        'currency' => strtolower($data['currency']),
        'unit_amount' => PrliStripeHelper::to_zero_decimal_amount($data['price'], $data['currency']),
        'product_data' => array(
          'name' => $data['name']
        ),
        'tax_behavior' => $data['tax_behavior'],
        'expand' => ['product']
      );

      if($data['type'] == 'recurring') {
        $args['recurring'] = $this->get_recurring_data($data);
      }

      $price = $this->send_stripe_request('prices', $args);

      wp_send_json_success( PrliStripeHelper::render_line_item(['price' => $price, 'quantity' => 1]));
    }
    catch(Exception $e) {
      wp_send_json_error($e->getMessage());
    }
  }

  /**
   * Sanitizes the given product data
   *
   * @param array $data
   * @return string[]
   */
  private function sanitize_product_data(array $data) {
    return array(
      'name' => isset($data['name']) ? sanitize_text_field($data['name']) : '',
      'price' => isset($data['price']) ? sanitize_text_field($data['price']) : '',
      'currency' => isset($data['currency']) ? sanitize_text_field($data['currency']) : '',
      'type' => isset($data['type']) ? sanitize_text_field($data['type']) : '',
      'billing_period' => isset($data['billing_period']) ? sanitize_text_field($data['billing_period']) : '',
      'interval' => isset($data['interval']) ? sanitize_text_field($data['interval']) : '',
      'interval_count' => isset($data['interval_count']) ? sanitize_text_field($data['interval_count']) : '',
      'tax_behavior' => isset($data['tax_behavior']) ? sanitize_text_field($data['tax_behavior']) : '',
    );
  }

  /**
   * Validates the given product data
   *
   * @param string[] $data
   * @throws Exception If a value fails validation
   */
  private function validate_product_data(array $data) {
    if($data['name'] === '') {
      throw new Exception(__('A product name is required', 'pretty-link'));
    }

    if($data['price'] === '') {
      throw new Exception(__('A product price is required', 'pretty-link'));
    }
    elseif(!is_numeric($data['price']) || $data['price'] <= 0) {
      throw new Exception(__('The product price must be numeric and greater than zero', 'pretty-link'));
    }

    if($data['currency'] === '') {
      throw new Exception(__('A product currency is required', 'pretty-link'));
    }
    else {
      $currencies = PrliUtils::currencies();

      if(!array_key_exists($data['currency'], $currencies)) {
        throw new Exception(__('The given currency is not supported', 'pretty-link'));
      }
    }

    if(!in_array($data['type'], array('recurring', 'one_time'), true)) {
      throw new Exception(__('The given product type is not supported', 'pretty-link'));
    }

    if(!in_array($data['billing_period'], array('day', 'week', 'month', 'quarter', 'semiannual', 'year', 'custom'), true)) {
      throw new Exception(__('The given billing period is not supported', 'pretty-link'));
    }
    elseif($data['billing_period'] == 'custom') {
      if(!is_numeric($data['interval_count']) || $data['interval_count'] <= 0) {
        throw new Exception(__('The given custom billing interval count must be numeric and greater than zero', 'pretty-link'));
      }

      if(!in_array($data['interval'], array('day', 'week', 'month'), true)) {
        throw new Exception(__('The given custom billing interval is not supported', 'pretty-link'));
      }
    }

    if(!in_array($data['tax_behavior'], array('unspecified', 'inclusive', 'exclusive'), true)) {
      throw new Exception(__('The given tax behavior is not supported', 'pretty-link'));
    }
  }

  private function get_recurring_data(array $data) {
    switch($data['billing_period']) {
      case 'day':
      case 'week':
      case 'month':
      case 'year':
        $interval = $data['billing_period'];
        $interval_count = 1;
        break;
      case 'quarter':
        $interval = 'month';
        $interval_count = 3;
        break;
      case 'semiannual':
        $interval = 'month';
        $interval_count = 6;
        break;
      default:
        $interval = $data['interval'];
        $interval_count = $data['interval_count'];
    }

    return array(
      'interval' => $interval,
      'interval_count' => $interval_count
    );
  }

  public function enqueue_invoice_style() {
    $session_id = isset($_GET['prli_session_id']) ? sanitize_text_field(wp_unslash($_GET['prli_session_id'])) : '';

    if($session_id && strpos($session_id, 'cs_') === 0 && !apply_filters('prli_disable_prettypay_invoice', false)) {
      wp_enqueue_style('prli-prettypay-invoice', PRLI_CSS_URL . '/prettypay-invoice.css', array(), PRLI_VERSION);
    }
  }

  public function display_invoice($content) {
    $session_id = isset($_GET['prli_session_id']) ? sanitize_text_field(wp_unslash($_GET['prli_session_id'])) : '';

    if(!$session_id || strpos($session_id, 'cs_') !== 0 || apply_filters('prli_disable_prettypay_invoice', false)) {
      return $content;
    }

    static $already_run = false;

    if($already_run) {
      return $content;
    }

    $already_run = true;

    try {
      $session = $this->send_stripe_request("checkout/sessions/$session_id", array(
        'expand' => [
          'line_items.data.price.product',
          'subscription.latest_invoice'
        ]
      ), 'get');

      $line_items = array();

      if(isset($session['line_items']['data']) && is_array($session['line_items']['data'])) {
        foreach($session['line_items']['data'] as $line_item) {
          $line_items[] = array(
            'description' => $line_item['description'],
            'price' => PrliStripeHelper::format_currency($line_item['amount_subtotal'], $line_item['currency']),
            'image' => !empty($line_item['price']['product']['images'][0]) ? $line_item['price']['product']['images'][0] : ''
          );
        }
      }

      $order_id = '';

      if($session['mode'] == 'payment') {
        if(isset($session['payment_intent'])) {
          $order_id = $session['payment_intent'];
        }
      }
      elseif($session['mode'] == 'subscription') {
        if(isset($session['subscription']['latest_invoice']['payment_intent'])) {
          $order_id = $session['subscription']['latest_invoice']['payment_intent'];
        }
      }

      $currency = $session['currency'];
      $subtotal = PrliStripeHelper::format_currency($session['amount_subtotal'], $session['currency']);
      $discount = $session['total_details']['amount_discount'] > 0 ? PrliStripeHelper::format_currency($session['total_details']['amount_discount'], $session['currency']) : 0;
      $tax = $session['total_details']['amount_tax'] > 0 ? PrliStripeHelper::format_currency($session['total_details']['amount_tax'], $session['currency']) : 0;
      $total = PrliStripeHelper::format_currency($session['amount_total'], $session['currency']);
      $payment_status = $session['payment_status'] == 'unpaid' ? __('Payment Pending', 'pretty-link') : __('Payment Successful', 'pretty-link');
      $customer_portal_url = '';

      if($session['mode'] == 'subscription') {
        $customer_portal = get_option('prli_stripe_customer_portal');
        $customer_portal_url = is_array($customer_portal) && isset($customer_portal['login_page']['url']) ? home_url(PrliStripeHelper::get_customer_portal_page_name()) : '';
      }

      ob_start();
      include PRLI_VIEWS_PATH . '/prettypay/invoice.php';
      $content .= apply_filters('prli_prettypay_invoice_html', ob_get_clean());
    }
    catch(Exception $e) {
      // fail silently
    }

    return $content;
  }

  protected function check_customer_portal_wizard($line_items_json) {
    $line_items = json_decode($line_items_json, true);

    if(is_array($line_items)) {
      foreach($line_items as $line_item) {
        if(isset($line_item['price']['type']) && $line_item['price']['type'] == 'recurring') {
          update_option('prli_has_recurring_prettypay_link', true);
        }
      }
    }
  }

  public function customer_portal_notice() {
    $screen = get_current_screen();

    if(
      PrliUtils::is_get_request() &&
      PrliUtils::is_logged_in_and_an_admin() &&
      $screen instanceof WP_Screen &&
      $screen->id == 'edit-pretty-link' &&
      isset($_GET['prettypay']) &&
      $_GET['prettypay'] == 1 &&
      get_option('prli_has_recurring_prettypay_link') &&
      PrliStripeHelper::is_connection_active() &&
      !get_option('prli_customer_portal_notice_dismissed') &&
      !get_option('prli_stripe_customer_portal')
    ) {
      include PRLI_VIEWS_PATH . '/admin/payments/customer_portal_notice.php';
    }
  }

  public function customer_portal_redirect() {
    global $wp;

    if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == PrliStripeHelper::get_customer_portal_page_name() || isset($wp->query_vars['name']) && $wp->query_vars['name'] == PrliStripeHelper::get_customer_portal_page_name()) {
      $portal = get_option('prli_stripe_customer_portal');

      if(!empty($portal['login_page']['url'])) {
        wp_redirect(esc_url_raw($portal['login_page']['url']));
        exit;
      }

      $configure_link = '';

      if(PrliUtils::is_logged_in_and_an_admin()) {
        $configure_link = sprintf(
          '<br><br><a href="%1$s" class="button button-large">%2$s</a>',
          esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-options&nav_action=payments&configure_customer_portal=1')),
          esc_html__('Configure Customer Portal', 'pretty-link')
        );
      }

      wp_die(__('The Customer Portal is not yet configured.', 'pretty-link') . $configure_link);
    }
  }

  public function configure_customer_portal() {
    delete_transient('prli_stripe_customer_portal_error');

    if(!PrliStripeHelper::is_connection_active()) {
      return;
    }

    $data = array(
      'headline' => isset($_POST['prli_portal_headline']) ? mb_substr(sanitize_text_field(wp_unslash($_POST['prli_portal_headline'])), 0, 60) : '',
      'privacy_policy_url' => !empty($_POST['prli_portal_privacy_policy_url']) ? sanitize_text_field(wp_unslash($_POST['prli_portal_privacy_policy_url'])) : null,
      'terms_of_service_url' => !empty($_POST['prli_portal_terms_of_service_url']) ? sanitize_text_field(wp_unslash($_POST['prli_portal_terms_of_service_url'])) : null,
      'customer_update_enabled' => !empty($_POST['prli_portal_customer_update_enabled']),
      'allowed_updates' => isset($_POST['prli_portal_allowed_updates']) && is_array($_POST['prli_portal_allowed_updates']) ? array_map('sanitize_text_field', $_POST['prli_portal_allowed_updates']) : array(),
      'payment_method_update_enabled' => !empty($_POST['prli_portal_payment_method_update_enabled']),
      'subscription_cancel_enabled' => !empty($_POST['prli_portal_subscription_cancel_enabled']),
      'subscription_cancel_mode' => isset($_POST['prli_portal_subscription_cancel_mode']) && $_POST['prli_portal_subscription_cancel_mode'] == 'immediately' ? 'immediately' : 'at_period_end',
      'invoice_history_enabled' => !empty($_POST['prli_portal_invoice_history_enabled'])
    );

    $portal = get_option('prli_stripe_customer_portal');
    $portal_id = null;

    if(is_array($portal) && isset($portal['id'])) {
      $portal_id = $portal['id'];

      $old_data = array(
        'headline' => PrliUtils::array_get($portal, 'business_profile.headline'),
        'privacy_policy_url' => PrliUtils::array_get($portal, 'business_profile.privacy_policy_url'),
        'terms_of_service_url' => PrliUtils::array_get($portal, 'business_profile.terms_of_service_url'),
        'customer_update_enabled' => PrliUtils::array_get($portal, 'features.customer_update.enabled'),
        'allowed_updates' => PrliUtils::array_get($portal, 'features.customer_update.allowed_updates'),
        'payment_method_update_enabled' => PrliUtils::array_get($portal, 'features.payment_method_update.enabled'),
        'subscription_cancel_enabled' => PrliUtils::array_get($portal, 'features.subscription_cancel.enabled'),
        'subscription_cancel_mode' => PrliUtils::array_get($portal, 'features.subscription_cancel.mode'),
        'invoice_history_enabled' => PrliUtils::array_get($portal, 'features.invoice_history.enabled')
      );

      if($data == $old_data) {
        // Nothing has changed
        return;
      }
    }

    try {
      $args = array(
        'business_profile' => array(
          'headline' => $data['headline']
        ),
        'features' => array(
          'customer_update' => array(
            'enabled' => $data['customer_update_enabled'] ? 'true' : 'false',
            'allowed_updates' => count($data['allowed_updates']) ? $data['allowed_updates'] : ''
          ),
          'invoice_history' => array(
            'enabled' => $data['invoice_history_enabled'] ? 'true' : 'false',
          ),
          'payment_method_update' => array(
            'enabled' => $data['payment_method_update_enabled'] ? 'true' : 'false',
          ),
          'subscription_cancel' => array(
            'enabled' => $data['subscription_cancel_enabled'] ? 'true' : 'false',
            'mode' => $data['subscription_cancel_mode'] == 'immediately' ? 'immediately' : 'at_period_end',
            'proration_behavior' => 'none'
          )
        ),
        'default_return_url' => home_url('/'),
        'login_page' => array(
          'enabled' => 'true'
        ),
        'metadata' => array(
          'created_by' => 'prettylinks'
        )
      );

      if($data['privacy_policy_url']) {
        $args['business_profile']['privacy_policy_url'] = $data['privacy_policy_url'];
      }

      if($data['terms_of_service_url']) {
        $args['business_profile']['terms_of_service_url'] = $data['terms_of_service_url'];
      }

      if($portal_id) {
        $endpoint = "billing_portal/configurations/$portal_id";
        $args['active'] = 'true';
      }
      else {
        $endpoint = 'billing_portal/configurations';
      }

      $configuration = $this->send_stripe_request($endpoint, $args);

      if($configuration['is_default']) {
        // The default configuration cannot be updated, we'll create a new one to use for PL
        $configuration = $this->send_stripe_request('billing_portal/configurations', $args);
      }

      update_option('prli_stripe_customer_portal', $configuration);
      update_option('prli_customer_portal_notice_dismissed', true);
    }
    catch(Exception $e) {
      set_transient('prli_stripe_customer_portal_error', $e->getMessage(), HOUR_IN_SECONDS);
      PrliUtils::debug_log($e->getMessage());
    }
  }

  public function display_customer_portal_error() {
    $customer_portal_error = get_transient('prli_stripe_customer_portal_error');

    delete_transient('prli_stripe_customer_portal_error');

    if($customer_portal_error) : ?>
      <div class="notice notice-error">
        <p>
          <?php
            printf(
              /* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: the error message */
              esc_html__('%1$sError saving Customer Portal:%2$s %3$s', 'pretty-link'),
              '<strong>',
              '</strong>',
              esc_html($customer_portal_error)
            );
          ?>
        </p>
      </div>
    <?php endif;
  }

  public function dismiss_customer_portal_notice() {
    if(!PrliUtils::is_post_request() || !PrliUtils::is_logged_in_and_an_admin() || !check_ajax_referer('prli_dismiss_customer_portal_notice', false, false)) {
      wp_send_json_error(__('Bad request', 'pretty-link'));
    }

    update_option('prli_customer_portal_notice_dismissed', true);

    wp_send_json_success();
  }
}
