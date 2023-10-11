<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
  <?php PrliAppHelper::page_title(__('Manage License', 'pretty-link')); ?>
  <?php require(PRLI_VIEWS_PATH.'/admin/errors.php'); ?>

  <div class="prli_spacer"></div>
  <table class="prli-settings-table">
    <tr class="prli-mobile-nav">
      <td colspan="2">
        <a href="" class="prli-toggle-nav"><i class="mp-icon-menu"> </i></a>
      </td>
    </tr>
    <tr>
      <td class="prli-settings-table-nav">
        <ul class="prli-sidebar-nav">
          <li><a data-id="license"><?php esc_html_e('License', 'pretty-link'); ?></a></li>
          <?php do_action('prli_updates_nav_items'); ?>
        </ul>
      </td>
      <td class="prli-settings-table-pages">
        <div class="prli-page" id="license">
          <div class="prli-page-title"><?php esc_html_e('Pretty Links Pro License', 'pretty-link'); ?></div>
          <div id="prli-license-container">
            <?php
              if(empty($li)) {
                require PRLI_VIEWS_PATH . '/admin/update/inactive_license.php';
              }
              else {
                require PRLI_VIEWS_PATH . '/admin/update/active_license.php';
              }
            ?>
          </div>
        </div>
        <?php do_action('prli_updates_pages'); ?>
      </td>
    </tr>
  </table>
</div>
