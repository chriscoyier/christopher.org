<?php
/**
 * One-click updater for Luminate
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the theme updater
if ( ! class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

// Loads the updater classes
$updater = new EDD_Theme_Updater_Admin(

	// Config settings
	$config = array(
		'remote_api_url' => 'https://devpress.com',
		'item_name' => 'Luminate',
		'theme_slug' => 'devpress-luminate',
		'version' => LUMINATE_VERSION,
		'author' => 'DevPress',
	),

	// Strings
	$strings = array(
		'theme-license' => __( 'Theme License', 'luminate' ),
		'enter-key' => __( 'Enter your theme license key.', 'luminate' ),
		'license-key' => __( 'License Key', 'luminate' ),
		'license-action' => __( 'License Action', 'luminate' ),
		'deactivate-license' => __( 'Deactivate License', 'luminate' ),
		'activate-license' => __( 'Activate License', 'luminate' ),
		'status-unknown' => __( 'License status is unknown.', 'luminate' ),
		'renew' => __( 'Renew?', 'luminate' ),
		'unlimited' => __( 'unlimited', 'luminate' ),
		'license-key-is-active' => __( 'License key is active.', 'luminate' ),
		'expires%s' => __( 'Expires %s.', 'luminate' ),
		'%1$s/%2$-sites' => __( 'You have %1$s / %2$s sites activated.', 'luminate' ),
		'license-key-expired-%s' => __( 'License key expired %s.', 'luminate' ),
		'license-key-expired' => __( 'License key has expired.', 'luminate' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'luminate' ),
		'license-is-inactive' => __( 'License is inactive.', 'luminate' ),
		'license-key-is-disabled' => __( 'License key is disabled.', 'luminate' ),
		'site-is-inactive' => __( 'Site is inactive.', 'luminate' ),
		'license-status-unknown' => __( 'License status is unknown.', 'luminate' ),
		'update-notice' => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'luminate' ),
		'update-available' => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'luminate' )
	)

);