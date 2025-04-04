<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="prli-admin-addons" class="wrap pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <h2><?php esc_html_e('Pretty Links Add-ons', 'pretty-link'); ?><a href="#" class="add-new-h2 prli-addons-refresh"><?php esc_html_e('Refresh Add-ons', 'pretty-link'); ?></a><input type="search" id="prli-addons-search" placeholder="<?php esc_attr_e('Search add-ons', 'pretty-link'); ?>" disabled></h2>

    <p>
      <?php
        printf(
          esc_html__('Improve your links with our premium add-ons. Missing an add-on that you think you should be able to see? Click the <a href="#">Refresh Add-ons</a> button above.', 'pretty-link')
        );
      ?>
    </p>

    <h4><?php esc_html_e('Available Add-ons', 'pretty-link'); ?></h4>

    <div id="prli-addons-container">
      <div class="prli-addons prli-clearfix">
        <div class="prli-addon prli-addon-status-inactive">
          <div class="prli-addon-details">
            <img src="https://prli-add-on-icons.s3.amazonaws.com/400x400/pl-display-icon_400x400.png" alt="<?php esc_attr_e('Pretty Links Product Displays', 'pretty-link'); ?>">
            <h5 class="prli-addon-name"><?php esc_html_e('Product Displays', 'pretty-link'); ?></h5>
            <p><?php esc_html_e('Add beautifully styled product displays to your site.', 'pretty-link'); ?></p>
          </div>
          <div class="prli-addon-actions prli-clearfix">
            <div class="prli-addon-status">
              <strong><?php esc_html_e('Status: ', 'pretty-link'); ?><span class="prli-addon-status-label"><?php esc_html_e('Inactive', 'pretty-link'); ?></span></strong>
            </div>
            <div class="prli-addon-action">
              <button type="button" disabled>
                <i class="pl-icon pl-icon-toggle-on mp-flip-horizontal"></i>
                <?php esc_html_e('Activate', 'pretty-link'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php"; ?>
</div>