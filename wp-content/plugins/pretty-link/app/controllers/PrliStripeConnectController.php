<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliStripeConnectController extends PrliBaseController {
  /**
   * Associates methods with their proper hooks.
   *
   * @access public
   * @return void
   */
  public function load_hooks() {
    if(!defined('PRLI_STRIPE_SERVICE_DOMAIN')) {
      define('PRLI_STRIPE_SERVICE_DOMAIN', 'stripe.prettylinks.com');
    }

    define('PRLI_STRIPE_SERVICE_URL', 'https://' . PRLI_STRIPE_SERVICE_DOMAIN);

    add_action( 'admin_init', array( $this, 'persist_display_keys' ) );
    add_action( 'admin_notices', array( $this, 'enable_payment_links_notice' ), 9999 );
    add_action( 'update_option_home', array( $this, 'url_changed' ), 10, 3 );
    add_action( 'update_option_siteurl', array( $this, 'url_changed' ), 10, 3 );
    add_action( 'wp_ajax_prli_stripe_connect_update_creds', array( $this, 'process_update_creds' ) );
    add_action( 'wp_ajax_prli_stripe_connect_refresh', array( $this, 'process_refresh_tokens' ) );
    add_action( 'wp_ajax_prli_stripe_connect_disconnect', array( $this, 'process_disconnect' ) );
  }

  /**
   * When the ?display-keys query param is set, set a cookie to persist the "selection"
   *
   * @return void
   */
  public function persist_display_keys() {
    if ( isset($_GET['page']) && $_GET['page']=='pretty-link-options' && isset( $_GET['display-keys'] ) ) {
      setcookie( 'prli_stripe_display_keys', '1', time() + HOUR_IN_SECONDS, '/' );
    }
  }

  /**
   * Run the process for updating a webhook when a site's home or site URL changes
   *
   * @param  string   $old_url  Old setting (URL)
   * @param  string   $new_url  New setting
   * @param  string   $option   Option name
   *
   * @return string
   */
  public function url_changed( $old_url, $new_url, $option ) {
    if ( $new_url !== $old_url ) {
      $this->maybe_update_domain();
    }
  }

  /**
   * This checks if the current site's domain has changed from what we have stored on the Authentication service.
   * If the domain has changed, we need to update the site on the Auth service, and the connection on the Stripe Connect service.
   *
   * @return void
   */
  public function maybe_update_domain() {

    $old_site_url = get_option( 'prli_old_site_url',  get_site_url() );

    // Exit if the home URL hasn't changed
    if($old_site_url==get_site_url()) {
      return;
    }

    $site_uuid = get_option( 'prli_authenticator_site_uuid' );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $jwt = PrliAuthenticatorController::generate_jwt( $payload );
    $domain = parse_url( get_site_url(), PHP_URL_HOST );

    // Request to change the domain with the auth service (site.domain)
    $response = wp_remote_post( PRLI_AUTH_SERVICE_URL . "/api/domains/update", array(
      'sslverify' => false,
      'headers' => PrliUtils::jwt_header($jwt, PRLI_AUTH_SERVICE_DOMAIN),
      'body' => array(
        'domain' => $domain
      )
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    // Store for next time
    update_option( 'prli_old_site_url', get_site_url() );
  }

  /**
  * Process a request to retrieve credentials after a connection
  *
  * @return void
  */
  public function process_update_creds() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-update-creds' ) ) {
      wp_die(__('Sorry, updating your credentials failed. (security)', 'pretty-link'));
    }

    // Check for the existence of any errors passed back from the service
      if ( isset( $_GET['error'] ) ) {
        wp_die( sanitize_text_field( urldecode( $_GET['error'] ) ) );
      }

    // Make sure we have a method ID
    if ( ! isset( $_GET['pmt'] ) ) {
      wp_die(__('Sorry, updating your credentials failed. (pmt)', 'pretty-link'));
    }

    // Make sure the user is authorized
    if ( ! PrliUtils::is_authorized() ) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $this->update_connect_credentials();

    $stripe_action = ( ! empty( $_GET['stripe-action'] ) ? sanitize_text_field( $_GET['stripe-action'] ) : 'updated' );

    $redirect_url = add_query_arg( array(
        'stripe-action' => $stripe_action
      ), admin_url('edit.php?post_type=pretty-link&page=pretty-link-options') ) . '#payments';

    wp_redirect($redirect_url);
    exit;
  }

  /** Fetches the credentials from Stripe-Connect and updates them in the payment method. */
  private function update_connect_credentials() {

    $site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $method_id = PrliStripeConnect::get_method_id();
    $jwt = PrliAuthenticatorController::generate_jwt( $payload );

    // Make sure the request came from the Connect service
    $response = wp_remote_get( PRLI_STRIPE_SERVICE_URL . "/api/credentials/".$method_id, array(
      'headers' => PrliUtils::jwt_header($jwt, PRLI_STRIPE_SERVICE_DOMAIN)
    ) );

    $creds = json_decode( wp_remote_retrieve_body( $response ), true );

    update_option( 'prli_stripe_status', 1 );
    update_option( 'prli_stripe_test_secret_key', sanitize_text_field( $creds['test_secret_key'] ) );
    update_option( 'prli_stripe_test_publishable_key', sanitize_text_field( $creds['test_publishable_key'] ) );
    update_option( 'prli_stripe_live_secret_key', sanitize_text_field( $creds['live_secret_key'] ) );
    update_option( 'prli_stripe_live_publishable_key', sanitize_text_field( $creds['live_publishable_key'] ) );

    update_option( 'prli_stripe_connect_status', 'connected' );
    update_option( 'prli_stripe_service_account_id', sanitize_text_field( $creds['service_account_id'] ) );
    update_option( 'prli_stripe_service_account_name', sanitize_text_field( $creds['service_account_name'] ) );
  }

  /**
  * Process a request to refresh tokens
  *
  * @return void
  */
  public function process_refresh_tokens() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-refresh' ) ) {
      wp_die(__('Sorry, the refresh failed.', 'pretty-link'));
    }

    // Make sure we have a method ID
    if ( ! isset( $_GET['method-id'] ) ) {
      wp_die(__('Sorry, the refresh failed.', 'pretty-link'));
    }

    // Make sure the user is authorized
    if ( ! PrliUtils::is_authorized() ) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $method_id = PrliStripeConnect::get_method_id();
    $site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );

    $payload = array(
      'site_uuid' => $site_uuid
    );

    $jwt = PrliAuthenticatorController::generate_jwt( $payload );

    // Send request to Connect service
    $response = wp_remote_post( PRLI_STRIPE_SERVICE_URL . "/api/refresh/{$method_id}", array(
      'headers' => PrliUtils::jwt_header($jwt, PRLI_STRIPE_SERVICE_DOMAIN),
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['connect_status'] ) || 'refreshed' !== $body['connect_status'] ) {
      wp_die(__('Sorry, the refresh failed.', 'pretty-link'));
    }

    update_option( 'prli_stripe_status', 1 );
    update_option( 'prli_stripe_test_secret_key', sanitize_text_field( $body['test_secret_key'] ) );
    update_option( 'prli_stripe_test_publishable_key', sanitize_text_field( $body['test_publishable_key'] ) );
    update_option( 'prli_stripe_live_secret_key', sanitize_text_field( $body['live_secret_key'] ) );
    update_option( 'prli_stripe_live_publishable_key', sanitize_text_field( $body['live_publishable_key'] ) );

    update_option( 'prli_stripe_connect_status', 'connected' );
    update_option( 'prli_stripe_service_account_id', sanitize_text_field( $body['service_account_id'] ) );
    update_option( 'prli_stripe_service_account_name', sanitize_text_field( $body['service_account_name'] ) );

    $redirect_url = add_query_arg( array(
        'stripe-action' => 'refreshed'
      ), admin_url('edit.php?post_type=pretty-link&page=pretty-link-options') ) . '#payments';

    wp_redirect($redirect_url);
    exit;
  }

  /**
  * Process a request to disconnect
  *
  * @return void
  */
  public function process_disconnect() {

    // Security check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'stripe-disconnect' ) ) {
      wp_die(__('Sorry, the disconnect failed.', 'pretty-link'));
    }

    // Make sure the user is authorized
    if ( ! PrliUtils::is_authorized() ) {
      wp_die(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    $res = $this->disconnect();

    if(!$res) {
      wp_die(__('Sorry, the disconnect failed.', 'pretty-link'));
    }

    update_option( 'prli_stripe_connect_status', 'disconnected' );

    update_option( 'prli_stripe_status', 0 );

    $redirect_url = add_query_arg( array(
        'stripe-action' => 'disconnected'
      ), admin_url('edit.php?post_type=pretty-link&page=pretty-link-options') ) . '#payments';

    wp_redirect($redirect_url);
    exit;
  }

  private function disconnect() {

    $site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
    $method_id = PrliStripeConnect::get_method_id();

    // Attempt to disconnect at the service
    $payload = array(
      'method_id' => $method_id,
      'site_uuid' => $site_uuid
    );

    $jwt = PrliAuthenticatorController::generate_jwt( $payload );

    // Send request to Connect service
    $response = wp_remote_request( PRLI_STRIPE_SERVICE_URL . "/api/disconnect/{$method_id}", array(
      'method' => 'DELETE',
      'headers' => PrliUtils::jwt_header($jwt, PRLI_STRIPE_SERVICE_DOMAIN),
    ) );

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $body['connect_status'] ) || 'disconnected' !== $body['connect_status'] ) {
      return false;
    }

    return true;
  }

  /**
  * Display an admin notice for enabling PrettyPay links.
  *
  * @return void
  */
  public function enable_payment_links_notice() {
    if ( !PrliStripeHelper::is_connection_active() && !get_option('prli_dismiss_notice_prli_prettypay_stripe') ) {
      ?>
      <style>
      .prli-warning-notice-icon {
        color: #72aee6 !important;
        font-size: 32px !important;
        vertical-align: top !important;
      }

      .prli-warning-notice-title {
        vertical-align: top !important;
        margin-left: 18px !important;
        font-size: 18px !important;
        font-weight: bold !important;
        line-height: 32px !important;
      }
      </style>
      <div class="notice notice-info prli-notice is-dismissible prli-notice-dismiss-permanently" data-notice="prli_prettypay_stripe">
        <p style="margin-top: 12px;"><span class="dashicons dashicons-cart prli-warning-notice-icon"></span><strong class="prli-warning-notice-title"><?php esc_html_e( 'Get Paid the Pretty Links Way!', 'pretty-link' ); ?></strong></p>
        <p>
          <?php
            printf(
              esc_html__('Introducing %1$sNEW PrettyPay™ Links!%2$s Effortlessly accept payments right on your site with %1$sbrandable checkout links%2$s and watch your income soar. No detours, no distractions – just straightforward, secure transactions that make life easier for you and your customers.', 'pretty-link'),
              '<strong>',
              '</strong>'
            );
          ?>
        </p>
        <p style="margin-bottom: 12px;"><a href="<?php echo esc_url(admin_url('edit.php?post_type=pretty-link&page=prettypay-links')); ?>" class="button button-primary"><?php esc_html_e('Learn More', 'pretty-link'); ?></a></p>
      </div>
      <?php
    }
  }
}
