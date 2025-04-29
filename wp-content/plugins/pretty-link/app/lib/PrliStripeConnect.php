<?php

class PrliStripeConnect
{
  private static $notifiers = array(
      'whk' => 'listener',
      'stripe-service-whk' => 'service_listener',
      'update-billing.html' => 'churn_buster'
    );

  public static function get_notifiers() {
    return self::$notifiers;
  }

  public static function get_method_id() {
    return 'prli7tr1pe';
  }

  /**
  * Assembles the URL for redirecting to Stripe Connect
  *
  * @param  string $id         Payment ID
  * @param  bool   $onboarding True if we are onboarding
  * @return string
  */
  public static function get_stripe_connect_url($method_id = '') {

    if( '' === $method_id ) {
      $method_id = self::get_method_id();
    }

    $args = array(
      'action' => 'prli_stripe_connect_update_creds',
      '_wpnonce' => wp_create_nonce( 'stripe-update-creds' )
    );

    $base_return_url = add_query_arg( $args, admin_url( 'admin-ajax.php' ) );

    $error_url = add_query_arg( array(
      'prli-action' => 'error'
    ), $base_return_url );

    $site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );

    if ( empty( $site_uuid ) ) {
      return false;
    }

    $payload = array(
      'method_id' => $method_id,
      'site_uuid' => $site_uuid,
      'user_uuid' => get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_USER_UUID ),
      'return_url'=> $base_return_url,
      'error_url' => $error_url,
      'webhook_url'  => self::notify_url( $method_id, 'whk' ),
      'service_webhook_url' => self::notify_url( $method_id, 'stripe-service-whk' ),
      'mp_version' => PRLI_VERSION
    );

    $jwt = PrliAuthenticatorController::generate_jwt( $payload );
    return PRLI_STRIPE_SERVICE_URL  . "/connect/{$site_uuid}/{$method_id}/{$jwt}";
  }

  /** Returns the url of a given notifier for the current gateway */
  public static function notify_url($method_id, $action, $force_ssl=false) {
    if(isset(self::$notifiers[$action])) {
      $permalink_structure = get_option('permalink_structure');
      $force_ugly_urls = get_option('prli_force_ugly_gateway_notify_urls');

      if($force_ugly_urls || empty($permalink_structure)) {
        $url = PRLI_SCRIPT_URL."&pmt={$method_id}&action={$action}";
      }
      else {
        $notify_url = preg_replace('!%gatewayid%!', $method_id, PrliGatewayHelper::gateway_notify_url_structure());
        $notify_url = preg_replace('!%action%!', $action, $notify_url);

        $url = site_url($notify_url);
      }

      if($force_ssl) {
        $url = preg_replace('/^http:/','https:',$url);
      }

      $slug = self::get_method_id();
      $url = apply_filters('prli_gateway_notify_url', $url, $slug, $action, $method_id);
      return apply_filters("prli_gateway_{$slug}_{$action}_notify_url", $url, $method_id);
    }

    return false;
  }

  public static function stripe_connect_status() {
    return get_option( 'prli_stripe_connect_status', 'not-connected' );
  }

  public static function is_active() {
    return get_option( 'prli_stripe_status', 0 );
  }

  public static function has_method_with_connect_status( $target_status ) {
    $status = self::stripe_connect_status();
    if( $target_status === $status ) {
      return true;
    } else {
      return false;
    }
  }
}
