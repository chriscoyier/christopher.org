<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="prli-admin-addons" class="wrap">

  <h2><?php esc_html_e('Pretty Links Add-ons', 'pretty-link'); ?><a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-addons&refresh=true')); ?>" class="add-new-h2 prli-addons-refresh"><?php esc_html_e('Refresh Add-ons', 'pretty-link'); ?></a><input type="search" id="prli-addons-search" placeholder="<?php esc_attr_e('Search add-ons', 'pretty-link'); ?>"></h2>

  <p>
    <?php
      printf(
        // translators: %1$s: open link tag, %2$s: close link tag
        esc_html__('Improve your links with our premium add-ons. Missing an add-on that you think you should be able to see? Click the %1$sRefresh Add-ons%2$s button above.', 'pretty-link'),
        sprintf('<a href="%s">', esc_url(admin_url('admin.php?page=pretty-link-addons&refresh=true'))),
        '</a>'
      );
    ?>
  </p>

  <h4><?php esc_html_e('Available Add-ons', 'pretty-link'); ?></h4>

  <?php if(!empty($addons)): ?>

    <div id="prli-addons-container">

      <div class="prli-addons prli-clearfix">

        <?php
          foreach($addons as $slug => $info):
            $info = (object) $info;
            $status_label = '';
            $action_class = 'prli-addon-action';

            $installed = isset($info->extra_info->directory) && is_dir(WP_PLUGIN_DIR . '/' . $info->extra_info->directory);
            $active = isset($info->extra_info->main_file) && is_plugin_active($info->extra_info->main_file);

            if($installed && $active) {
              $status = 'active';
              $status_label = esc_html__('Active', 'pretty-link');
            } elseif(!$installed && $info->installable) {
              $status = 'download';
              $status_label = esc_html__('Not Installed', 'pretty-link');
            } elseif($installed && !$active) {
              $status = 'inactive';
              $status_label = esc_html__('Inactive', 'pretty-link');
            } else {
              $status = 'upgrade';
            }
          ?>
          <div class="prli-addon prli-addon-status-<?php echo esc_attr($status); ?>">
            <div class="prli-addon-inner">

              <div class="prli-addon-details">
                <img src="<?php echo esc_url($info->extra_info->cover_image); ?>" alt="<?php echo esc_attr($info->product_name); ?>">
                <h5 class="prli-addon-name"><?php echo esc_html(isset($info->extra_info->list_title) ? $info->extra_info->list_title : $info->product_name); ?></h5>
                <p><?php echo $info->extra_info->description; ?></p>
              </div>

              <div class="prli-addon-actions prli-clearfix">

                <?php if($status != 'upgrade'): ?>

                  <div class="prli-addon-status">
                    <strong>
                      <?php
                        printf(
                          // translators: %s: add-on status label
                          esc_html__('Status: %s', 'pretty-link'),
                          sprintf(
                            '<span class="prli-addon-status-label">%s</span>',
                            $status_label
                          )
                        );
                      ?>
                    </strong>
                  </div>

                <?php else:  ?>

                  <?php $action_class .= ' prli-addon-action-upgrade'; ?>

                <?php endif; ?>

                <div class="<?php echo esc_attr($action_class); ?>">

                  <?php if($status == 'active'): ?>

                    <button type="button" data-plugin="<?php echo esc_attr($info->extra_info->main_file); ?>" data-type="add-on"><i class="pl-icon pl-icon-toggle-on"></i><?php esc_html_e('Deactivate', 'pretty-link'); ?></button>

                  <?php elseif($status == 'inactive'): ?>

                    <button type="button" data-plugin="<?php echo esc_attr($info->extra_info->main_file); ?>" data-type="add-on"><i class="pl-icon pl-icon-toggle-on mp-flip-horizontal"></i><?php esc_html_e('Activate', 'pretty-link'); ?></button>

                  <?php elseif($status == 'download'): ?>

                    <button type="button" data-plugin="<?php echo esc_attr($info->url); ?>" data-type="add-on"><i class="pl-icon pl-icon-download-cloud"></i><?php esc_html_e('Install Add-on', 'pretty-link'); ?></button>

                  <?php else: ?>

                    <a href="https://prettylinks.com/login/?redirect_to=%2Fpricing%2F" target="_blank"><?php esc_html_e('Upgrade Now', 'pretty-links', 'pretty-link'); ?></a>

                  <?php endif; ?>

                </div>

              </div>
            </div>
          </div>

        <?php endforeach; ?>

      </div>
    </div>

  <?php else: ?>

    <h3><?php esc_html_e('There were no Add-ons found for your license or lack thereof...', 'pretty-link'); ?></h3>

  <?php endif; ?>

</div>