<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/**
 * PrliNotifications.
 *
 * Class for logging in-plugin notifications.
 * Includes:
 *     Notifications from our remote feed
 *     Plugin-related notifications (e.g. recent sales performances)
 */
class PrliNotifications {
  /**
   * Check if user has access and is enabled.
   *
   * @return bool
   */
  public static function has_access() {

    $access = false;

    if (
      current_user_can( 'manage_options' )
      && ! get_option( 'prli_hide_announcements' )
    ) {
      $access = true;
    }

    return apply_filters( 'prli_admin_notifications_has_access', $access );
  }
}