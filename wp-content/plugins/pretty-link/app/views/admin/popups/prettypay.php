<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wrap pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <h1 class="wp-heading-inline">PrettyPay™ Links</h1>
    <a href="#" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    <ul class="subsubsub">
      <li class="all"><a href="#" class="current" aria-current="page">All <span class="count">(10)</span></a></li>
    </ul>
    <p class="search-box">
      <label class="screen-reader-text" for="post-search-input">Search PrettyPay™ Links:</label>
      <input type="search" id="post-search-input" name="s" value="">
      <input type="submit" id="search-submit" class="button" value="Search PrettyPay™ Links">
    </p>
    <div class="tablenav top">
      <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
          <option value="-1">Bulk actions</option>
          <option value="edit" class="hide-if-no-js">Edit</option>
          <option value="trash">Move to Trash</option>
        </select>
        <input type="submit" id="doaction" class="button action" value="Apply">
      </div>
      <div class="alignleft actions">
        <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
        <select name="m" id="filter-by-date">
          <option selected="selected" value="0">All dates</option>
          <option value="202309">September 2023</option>
        </select>
        <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">    </div>
      <div class="tablenav-pages one-page"><span class="displaying-num">10 items</span>
        <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
        <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span>
      </div>
      <br class="clear">
    </div>
    <table class="widefat post fixed" cellspacing="0">
      <thead>
      <tr>
        <th class="manage-column" width="15%">
          <a href="#">
            <?php esc_html_e('Settings', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="15%">
          <a href="#">
            <?php esc_html_e('Link Title', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="15%">
          <a href="#">
            <?php esc_html_e('Categories', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="10%">
          <a href="#">
            <?php esc_html_e('Tags', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="10%">
          <a href="#">
            <?php esc_html_e('Keywords', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="10%">
          <a href="#">
            <?php esc_html_e('Clicks', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="10%">
          <a href="#">
            <?php esc_html_e('Date', 'pretty-link'); ?>
          </a>
        </th>
        <th class="manage-column" width="15%">
          <a href="#">
            <?php esc_html_e('Pretty Links', 'pretty-link'); ?>
          </a>
        </th>
      </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Google Link
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              0/0
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/08/16 at 10:08 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/vw09">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Facebook Link
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            <a href="#">facebook</a>
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              0/0
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/08/10 at 15:15 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/ga04">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Pro plans
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            <a href="#">Basic</a>
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              4/20
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/08/09 at 10:38 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/jw06">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            BF Link
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              23/100
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/08/03 at 11:12 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/vw04">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Birthday Promo
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            <a href="#">Pro</a>
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            <a href="#">facebook</a>
          </td>
          <td>facebook</td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              2/4
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/08/01 at 20:08 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/tp03">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            TP Promo
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              0/5
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/07/29 at 17:05 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/vg09">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Pro Campaign
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            <a href="#">pro</a>
          </td>
          <td>pro</td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              3/10
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/07/25 at 10:50 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/fu02">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            Notifications
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td></td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              0/5
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/07/22 at 15:18 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/bv20">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            BF Campaign
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            <a href="#">google</a>
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            <a href="#">Plus</a>
          </td>
          <td>plus</td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              0/0
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/07/20 at 14:22 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/tp02">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
        <tr>
          <td>
            <i title="PrettyPay™ Link" class="pl-icon-basket pl-list-icon"></i>
          </td>
          <td class="title column-title has-row-actions column-primary page-title" data-colname="Link Title">
            PE Promo
          </td>
          <td class="taxonomy-pretty-link-category column-taxonomy-pretty-link-category" data-colname="Categories">
            —
          </td>
          <td class="taxonomy-pretty-link-tag column-taxonomy-pretty-link-tag" data-colname="Tags">
            —
          </td>
          <td>campaign</td>
          <td class="clicks column-clicks" data-colname="Clicks">
            <a href="#" id="link_clicks_1" title="0 Clicks / 0 Uniques">
              5/10
            </a>
          </td>
          <td class="date column-date" data-colname="Date">
            Published<br>2023/07/20 at 13:33 am
          </td>
          <td class="links column-links" data-colname="Pretty Links">
            <input type="text" readonly="true" style="width: 65%;" value="/kh03">
            <span class="list-clipboard prli-clipboard">
              <i class="pl-icon-clipboard pl-list-icon icon-clipboardjs tooltipstered" data-clipboard-text=""></i>
            </span>
          </td>
        </tr>
      </tbody>
      <tfoot>
      <tr>
        <th class="manage-column"><?php esc_html_e('Settings', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Link Title', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Categories', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Tags', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Keywords', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Clicks', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Date', 'pretty-link'); ?></th>
        <th class="manage-column"><?php esc_html_e('Pretty Links', 'pretty-link'); ?></th>
      </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="pretty-link-popup pretty-link-popup-long">
  <div class="pretty-link-popup-wrap">
    <div class="pretty-link-popup-content">
      <div class="pretty-link-popup-logo">
        <img src="<?php echo PRLI_IMAGES_URL . '/stripe.svg'; ?>" alt="">
      </div>
      <h2>Elevate Your Earnings with Stripe!</h2>
      <p><em>Why wait around for your money?</em> Connect your site to Stripe and get paid the second customers click on your PrettyPay™ links. Make your cash flow as smooth as your customer journey.</p>
      <h4>Boost Your Bucks with Stripe's Epic Extras</h4>
      <ul class="features">
        <li>Seamless & Secure Payment Processing</li>
        <li>Global Currency Support</li>
        <li>Multi-Channel Sales</li>
        <li>Immediate Access to Funds</li>
        <li>Enhanced Tracking & Analytics</li>
        <li>No Coding Required</li>
      </ul>
      <p>Integrate with Stripe today and watch your revenue soar, all while giving your customers an exceptional shopping experience.</p>
    </div>
    <div class="pretty-link-popup-cta">
      <a href="<?php echo esc_url($stripe_connect_url); ?>" id="pretty_link_cta_upgrade_link" class="pretty-link-cta-button">Connect to Stripe</a>
    </div>
  </div>
</div>
