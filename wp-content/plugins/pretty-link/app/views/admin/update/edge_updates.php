<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php global $plp_update; ?>
<div id="<?php echo esc_attr($plp_update->edge_updates_str); ?>-wrap">
  <input type="checkbox" id="<?php echo esc_attr($plp_update->edge_updates_str); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('wp-edge-updates')); ?>" <?php checked($plp_update->edge_updates); ?>/>&nbsp;<?php esc_html_e('Include Pretty Links Pro edge (development) releases in automatic updates (not recommended for production websites)', 'pretty-link'); ?> <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/square-loader.gif'); ?>" alt="<?php esc_attr_e('Loading...', 'pretty-link'); ?>" class="prli_loader" />
</div>

