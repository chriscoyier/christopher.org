<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <?php PrliAppHelper::page_title(__('Tools', 'pretty-link')); ?>

  <?php if($update_message): ?>
    <div class="updated notice notice-success is-dismissible"><p><strong><?php echo esc_html($update_message); ?></strong></p></div>
  <?php endif; ?>

  <table class="prli-settings-table">
    <tr class="prli-mobile-nav">
      <td colspan="2">
        <a href="" class="prli-toggle-nav"><i class="pl-icon-menu"> </i></a>
      </td>
    </tr>
    <tr>
      <td class="prli-settings-table-nav">
        <ul class="prli-sidebar-nav">
          <li><a data-id="bookmarklet"><?php esc_html_e('Bookmarklet', 'pretty-link'); ?></a></li>
          <li><a data-id="trim"><?php esc_html_e('Trim Clicks', 'pretty-link'); ?></a></li>
          <?php do_action('prli_admin_tools_nav'); ?>
        </ul>
      </td>
      <td class="prli-settings-table-pages">
        <div class="prli-page" id="bookmarklet">
          <div class="prli-page-title"><?php esc_html_e('Bookmarklet', 'pretty-link'); ?></div>
          <strong><a class="button button-primary" href="<?php echo PrliLink::bookmarklet_link(); ?>" style="vertical-align:middle;"><?php esc_html_e('Get Pretty Link', 'pretty-link'); ?></a></strong>&nbsp;&nbsp;
          <?php PrliAppHelper::info_tooltip( 'prli-bookmarklet-instructions',
                                             esc_html__('Install Pretty Link Bookmarklet', 'pretty-link'),
                                             sprintf(
                                               // translators: %1$s: open link tag, %2$s close link tag
                                               esc_html__('Just drag this "Get PrettyLink" link to your toolbar to install the bookmarklet. As you browse the web, you can just click this bookmarklet to create a pretty link from the current url you\'re looking at. %1$s(more help)%2$s', 'pretty-link'),
                                               '<a href="http://blairwilliams.com/pretty-link-bookmarklet/">',
                                               '</a>'
                                             ));
          ?>
          <br/><br/><a href="javascript:toggle_iphone_instructions()"><strong><?php esc_html_e('Show iPhone Bookmarklet Instructions', 'pretty-link'); ?></strong></a>
          <br/><br/>
          <div class="prli-sub-box iphone_instructions" style="display: none">
            <div class="prli-arrow prli-gray prli-up prli-sub-box-arrow"> </div>
            <?php
              printf(
                // translators: %1$s: open strong tag, %2$s close strong tag
                esc_html__('%1$sNote:%2$s iPhone users can install this bookmarklet in their Safari to create Pretty Links with the following steps:', 'pretty-link'),
                '<strong>',
                '</strong>'
              );
            ?><br/>
            <ol>
              <li><?php esc_html_e('Copy this text:', 'pretty-link'); ?><br/><input type="text" value="<?php echo esc_attr(PrliLink::bookmarklet_link()); ?>" /></li>
              <li><?php esc_html_e('Tap the + button at the bottom of the screen', 'pretty-link'); ?></li>
              <li><?php esc_html_e('Choose to share the page, then click on "Bookmark". We recommend saving it in your Favorites folder. Rename your bookmark to "Get PrettyLink" (or whatever you want) and then "Save"', 'pretty-link'); ?></li>
              <li><?php esc_html_e('Navigate through your Bookmarks until you find the new bookmark and click "Edit"', 'pretty-link'); ?></li>
              <li><?php esc_html_e('Delete all the text from the address', 'pretty-link'); ?></li>
              <li><?php esc_html_e('Paste the text you copied in Step 1 into the address field', 'pretty-link'); ?></li>
              <li>
                <?php
                  printf(
                    // translators: %1$s: open strong tag, %2$s close strong tag
                    esc_html__('To save the changes hit "Bookmarks" and %1$syou\'re done!%2$s', 'pretty-link'),
                    '<strong>',
                    '</strong>'
                  );
                ?>
                <?php esc_html_e('Now when you find a page you want to save off as a Pretty Link, just click the "Bookmarks" icon at the bottom of the screen and select your "Get PrettyLink" bookmarklet.', 'pretty-link'); ?>
              </li>
            </ol>
          </div>
        </div>
        <div class="prli-page" id="trim">
          <div class="prli-page-title"><?php esc_html_e('Trim Clicks', 'pretty-link'); ?></div>
          <?php if($prli_options->auto_trim_clicks): ?>
            <p><em><?php esc_html_e('Pretty Link is already automatically trimming Clicks older than 90 days. Although not necessary, you can still use the buttons below to force click trimming.', 'pretty-link'); ?></em></p>
          <?php endif; ?>
          <?php if($prli_options->extended_tracking != 'count'): ?>
            <a class="button" href="<?php echo wp_nonce_url(admin_url('admin.php?page=pretty-link-tools&action=clear_30day_clicks'), 'prli-clear-clicks-30day'); ?>" onclick="return confirm('<?php esc_attr_e('***WARNING*** If you click OK you will delete ALL of the Click data that is older than 30 days. Your data will be gone forever -- no way to retreive it. Do not click OK unless you are absolutely sure you want to delete this data because there is no going back!', 'pretty-link'); ?>');"><?php esc_html_e('Delete Clicks older than 30 days', 'pretty-link'); ?></a>&nbsp;&nbsp;
            <?php PrliAppHelper::info_tooltip( 'prli-clear-clicks-30',
                                               esc_html__('Clear clicks 30 days or older', 'pretty-link'),
                                               esc_html__('This will clear all clicks in your database that are older than 30 days.', 'pretty-link') ); ?>
            <div>&nbsp;</div>
            <a class="button" href="<?php echo wp_nonce_url(admin_url('admin.php?page=pretty-link-tools&action=clear_90day_clicks'), 'prli-clear-clicks-90day'); ?>" onclick="return confirm('<?php esc_attr_e('***WARNING*** If you click OK you will delete ALL of the Click data that is older than 90 days. Your data will be gone forever -- no way to retreive it. Do not click OK unless you are absolutely sure you want to delete this data because there is no going back!', 'pretty-link'); ?>');"><?php esc_html_e('Delete Clicks older than 90 days', 'pretty-link'); ?></a>&nbsp;&nbsp;
            <?php PrliAppHelper::info_tooltip( 'prli-clear-clicks-90',
                                                esc_html__('Clear clicks 90 days or older', 'pretty-link'),
                                                esc_html__('This will clear all clicks in your database that are older than 90 days.', 'pretty-link') ); ?>
            <div>&nbsp;</div>
          <?php endif; ?>

          <a class="button button-primary" href="<?php echo wp_nonce_url(admin_url('admin.php?page=pretty-link-tools&action=clear_all_clicks'), 'prli-clear-clicks-all'); ?>" onclick="return confirm('<?php esc_attr_e('***WARNING*** If you click OK you will delete ALL of the Click data in your Database. Your data will be gone forever -- no way to retreive it. Do not click OK unless you are absolutely sure you want to delete all your data because there is no going back!', 'pretty-link'); ?>');"><?php esc_html_e('Delete All Clicks', 'pretty-link'); ?></a>&nbsp;&nbsp;
          <?php PrliAppHelper::info_tooltip( 'prli-clear-all-clicks',
                                              esc_html__('Clear all clicks', 'pretty-link'),
                                              esc_html__('Seriously, only click this link if you want to delete all the Click data in your database.', 'pretty-link') ); ?>
        </div>

        <?php do_action('prli_admin_tools_pages'); ?>
      </td>
    </tr>
  </table>
</div>
