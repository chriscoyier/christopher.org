<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAuthenticatorController extends PrliBaseController {
  /**
   * Associates methods with their proper hooks.
   *
   * @access public
   * @return void
   */
  public function load_hooks() {
    if(!defined('PRLI_AUTH_SERVICE_DOMAIN')) {
      define('PRLI_AUTH_SERVICE_DOMAIN', 'auth.caseproof.com');
    }
    define('PRLI_AUTH_SERVICE_URL', 'https://' . PRLI_AUTH_SERVICE_DOMAIN);

    add_action('init', array($this, 'process_connect'));
    add_action('init', array($this, 'process_disconnect'));
    add_action( 'admin_init', array( $this, 'delete_connection_data' ) );
  }

  /**
   * Validates the GET parameter and clears the saved connection data from the Authenticator.
   *
   * @access public
   * @return void
   */
  public static function delete_connection_data() {
    if ( isset( $_GET['prli-clear-connection-data'] ) ) {
      // Admins only
      if ( current_user_can( 'manage_options' ) ) {
        self::clear_connection_data();
      }
    }
  }

  /**
   * Clears the saved connection data from the Authenticator.
   *
   * @access public
   * @return void
   */
  public static function clear_connection_data() {
    delete_option('prli_authenticator_site_uuid');
    delete_option('prli_authenticator_account_email');
    delete_option('prli_authenticator_secret_token');
  }

  /**
   * Processes a connection to the Authenticator service.
   *
   * @access public
   * @return void
   */
  public function process_connect() {
    global $plp_update;

    // Make sure we've entered our Authenticator process.
    if(!isset($_GET['prli-connect']) || $_GET['prli-connect'] !== 'true') {
      return;
    }

    // Validate the nonce on the WP side of things.
    if(!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'prli-connect')) {
      return;
    }

    // Make sure the user is authorized.
    if(!PrliUtils::is_authorized()) {
      return;
    }

    $site_uuid = sanitize_text_field($_GET['site_uuid']);
    $auth_code = sanitize_text_field($_GET['auth_code']);

    // GET request to obtain token.
    $response = wp_remote_get(PRLI_AUTH_SERVICE_URL . "/api/tokens/{$site_uuid}", array(
      'sslverify' => false,
      'headers'   => array(
        'accept' => 'application/json'
      ),
      'body'      => array(
        'auth_code' => $auth_code
      )
    ));

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if(isset($body['account_email']) && !empty($body['account_email'])) {
      $email_saved = update_option('prli_authenticator_account_email', sanitize_text_field($body['account_email']));
    }

    if(isset($body['secret_token']) && !empty($body['secret_token'])) {
      $token_saved = update_option('prli_authenticator_secret_token', sanitize_text_field($body['secret_token']));
    }

    if(isset($body['user_uuid']) && !empty($body['user_uuid'])) {
      $user_uuid_saved = update_option('prli_authenticator_user_uuid', sanitize_text_field($body['user_uuid']));
    }

    if($site_uuid) {
      update_option('prli_authenticator_site_uuid', $site_uuid);
    }

    if ( isset( $_GET['stripe_connect'] ) && 'true' === $_GET['stripe_connect'] && isset( $_GET['method_id'] ) && ! empty( $_GET['method_id'] ) ) {
      wp_redirect( PrliStripeConnect::get_stripe_connect_url( $_GET['method_id'] ) );
      exit;
    }

    $redirect_url = remove_query_arg(array(
      'prli-connect',
      'nonce',
      'site_uuid',
      'user_uuid',
      'auth_code',
      'license_key'
    ));

    $license_key = isset($_GET['license_key']) ? sanitize_text_field(wp_unslash($_GET['license_key'])) : '';

    if(!empty($license_key)) {
      try {
        $plp_update->activate_license($license_key);
      } catch (Exception $e) {
        $redirect_url = add_query_arg('license_error', urlencode($e->getMessage()), $redirect_url);
      }
    }

    wp_redirect($redirect_url);
    exit;
  }

  /**
   * Processes a disconnect to the Authenticator service.
   *
   * @access public
   * @return void
   */
  public function process_disconnect() {
    // Make sure we've entered our Authenticator process.
    if(!isset($_GET['prli-disconnect']) || $_GET['prli-disconnect'] !== 'true') {
      return;
    }

    // Validate the nonce on the WP side of things.
    if(!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'prli-disconnect')) {
      return;
    }

    // Make sure the user is authorized.
    if(!PrliUtils::is_authorized()) {
      return;
    }

    $site_email = get_option('prli_authenticator_account_email');
    $site_uuid = get_option('prli_authenticator_site_uuid');

    do_action('prli_pretty_link_com_pre_disconnect', $site_uuid, $site_email);

    // Create token payload.
    $payload = array(
      'email'     => $site_email,
      'site_uuid' => $site_uuid
    );

    // Create JWT.
    $jwt = self::generate_jwt($payload);

    // DELETE request to obtain token.
    $response = wp_remote_request(PRLI_AUTH_SERVICE_URL . "/api/disconnect/prettylinks", array(
      'method'    => 'DELETE',
      'sslverify' => false,
      'headers'   => PrliUtils::jwt_header($jwt, PRLI_AUTH_SERVICE_DOMAIN)
    ));

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if(isset($body['disconnected']) && $body['disconnected'] === true) {
      delete_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_EMAIL );
      delete_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SECRET );
      delete_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
      delete_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_USER_UUID );
    }

    wp_redirect(remove_query_arg(array('prli-disconnect', 'nonce')));
    exit;
  }

  /**
   * Generates a JWT (JSON Web Token), signed by the stored secret token.
   *
   * @access public
   * @param array $payload Payload data.
   * @param string $secret Used to sign the JWT.
   * @return string The generated JWT.
   */
  public static function generate_jwt($payload, $secret = false) {
    if($secret === false) {
      $secret = get_option('prli_authenticator_secret_token');
    }

    // Create token header.
    $header = array(
      'typ' => 'JWT',
      'alg' => 'HS256'
    );

    $header = json_encode($header);
    $header = self::base64url_encode($header);

    // Create token payload.
    $payload = json_encode($payload);
    $payload = self::base64url_encode($payload);

    // Create Signature Hash.
    $signature = hash_hmac('sha256', "{$header}.{$payload}", $secret);
    $signature = json_encode($signature);
    $signature = self::base64url_encode($signature);

    // Create JWT.
    $jwt = "{$header}.{$payload}.{$signature}";
    return $jwt;
  }

  /**
   * Creates a Base64 encoded string so that it can be passed within URLs without any URL encoding.
   *
   * @access public
   * @param string $value The string to encode.
   * @return string The Base64 encoded string.
   */
  public static function base64url_encode($value) {
    return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' );
  }

  /**
   * Assembles a URL for connecting to our Authenticator service.
   *
   * @access public
   * @param string $return_url The URL to return back to after being successfully authenticated.
   * @param array $additional_params Extra parameters to include in the URL for authentication.
   * @return string The assembled URL.
   */
  public static function get_auth_connect_url($return_url, $additional_params = array()) {
    $connect_params = array(
      'return_url' => urlencode(add_query_arg('prli-connect', 'true', $return_url)),
      'nonce'      => wp_create_nonce('prli-connect')
    );

    $site_uuid = get_option('prli_authenticator_site_uuid');

    if($site_uuid) {
      $connect_params['site_uuid'] = $site_uuid;
    }

    if(!empty($additional_params)) {
      $connect_params = array_merge($connect_params, $additional_params);
    }

    return add_query_arg($connect_params, PRLI_AUTH_SERVICE_URL . '/connect/prettylinks');
  }
}