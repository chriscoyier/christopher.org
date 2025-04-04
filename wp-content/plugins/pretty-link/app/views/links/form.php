<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<?php
  global $plp_update;

  if(isset($values['link_id']) && $values['link_id'] > 0) {
    ?>
      <input type="hidden" name="link_id" value="<?php echo esc_attr($values['link_id']); ?>" />
    <?php
  }

  $link_nonce = wp_create_nonce(PrliLink::$nonce_str . wp_salt());
?>

<input type="hidden" name="<?php echo esc_attr(PrliLink::$nonce_str); ?>" value="<?php echo esc_attr($link_nonce); ?>" />

<div id="pretty_link_errors" class="prli-hidden">
  <p><!-- This is where our errors will show up --></p>
</div>

<table class="prli-settings-table">
  <tr class="prli-mobile-nav">
    <td colspan="2">
      <a href="" class="prli-toggle-nav"><i class="pl-icon-menu"> </i></a>
    </td>
  </tr>
  <tr>
    <td class="prli-settings-table-nav">
      <ul class="prli-sidebar-nav">
        <li><a data-id="basic"><?php esc_html_e('Basic', 'pretty-link'); ?></a></li>
        <li><a data-id="advanced"><?php esc_html_e('Advanced', 'pretty-link'); ?></a></li>
        <li><a data-id="pro"><?php esc_html_e('Pro', 'pretty-link'); ?></a></li>
        <?php if(!$plp_update->is_installed() || $plp_update->is_installed() && is_plugin_active('pretty-link-product-displays/pretty-link-product-displays.php')): ?>
          <li><a data-id="product-display"><?php esc_html_e('Product Display', 'pretty-link'); ?></a></li>
        <?php endif; ?>
        <?php do_action('prli_admin_link_nav'); ?>
      </ul>
    </td>
    <td class="prli-settings-table-pages">
      <div class="prli-page" id="basic">
        <?php require(PRLI_VIEWS_PATH . '/links/form_basic.php'); ?>
      </div>
      <div class="prli-page" id="advanced">
        <?php require(PRLI_VIEWS_PATH . '/links/form_advanced.php'); ?>
      </div>
      <div class="prli-page" id="pro">
        <?php require(PRLI_VIEWS_PATH . '/links/form_pro.php'); ?>
      </div>
      <?php if(!$plp_update->is_installed() || $plp_update->is_installed() && is_plugin_active('pretty-link-product-displays/pretty-link-product-displays.php')): ?>
        <div class="prli-page" id="product-display">
          <?php require(PRLI_VIEWS_PATH . '/links/form_product_display.php'); ?>
        </div>
      <?php endif; ?>
      <?php do_action('prli_admin_link_nav_body'); ?>
    </td>
  </tr>
</table>

<?php do_action('prli_admin_after_link_form'); ?>

