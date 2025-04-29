<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="prli-prettypay-invoice">
  <p>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 prli-check">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  </p>
  <p>
    <?php echo esc_html($payment_status); ?>
    <?php if($order_id) : ?>
      <br>
      <?php echo esc_html(sprintf(__('Order: %s', 'pretty-link'), $order_id)); ?>
    <?php endif; ?>
  </p>
  <p class="prli-invoice-amount"><?php echo esc_html($total); ?></p>
  <table class="prli-prettypay-invoice-table">
    <tbody>
      <?php foreach($line_items as $line_item) : ?>
        <tr class="prli-line-item-row">
          <td>
            <?php if($line_item['image']) : ?>
              <div class="prli-line-item-image-url" style="background-image: url('<?php echo esc_url_raw($line_item['image']); ?>');"></div>
            <?php else : ?>
              <div class="prli-line-item-image-default">
                <svg aria-hidden="true" height="16" width="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M13.788 3.119a1.04 1.04 0 0 1-.31.283L8.5 6.362a.97.97 0 0 1-.998 0l-4.98-2.96a1.04 1.04 0 0 1-.309-.283L6.99.279a1.97 1.97 0 0 1 2.02 0zm1.194 1.647c.012.09.018.182.018.274v5.92c0 .743-.385 1.43-1.01 1.802l-4.98 2.96a1.97 1.97 0 0 1-2.02 0l-4.98-2.96A2.092 2.092 0 0 1 1 10.96V5.04c0-.092.006-.184.018-.274.147.133.308.252.481.355l4.98 2.96a2.97 2.97 0 0 0 3.042 0l4.98-2.96c.173-.103.334-.222.481-.355z" fill-rule="evenodd"></path></svg>
              </div>
            <?php endif; ?>
          </td>
          <td><?php echo esc_html($line_item['description']); ?></td>
          <td><?php echo esc_html($line_item['price']); ?></td>
        </tr>
      <?php endforeach; ?>
      <tr class="prli-subtotal-row">
          <td>&nbsp;</td>
        <td><?php esc_html_e('Subtotal', 'pretty-link'); ?></td>
        <td><?php echo esc_html($subtotal); ?></td>
      </tr>
      <?php if($discount) : ?>
        <tr class="prli-discount-row">
          <td>&nbsp;</td>
          <td><?php esc_html_e('Discount', 'pretty-link'); ?></td>
          <td>-<?php echo esc_html($discount); ?></td>
        </tr>
      <?php endif; ?>
      <?php if($tax) : ?>
        <tr class="prli-tax-row">
          <td>&nbsp;</td>
          <td><?php esc_html_e('Tax', 'pretty-link'); ?></td>
          <td><?php echo esc_html($tax); ?></td>
        </tr>
      <?php endif; ?>
      <tr class="prli-total-row">
          <td>&nbsp;</td>
        <td><?php esc_html_e('Total', 'pretty-link'); ?></td>
        <td class="prli-font-weight-bold"><?php echo esc_html($total); ?></td>
      </tr>
    </tbody>
  </table>
  <?php if($customer_portal_url && apply_filters('prli_display_invoice_portal_link', true)) : ?>
    <p class="prli-invoice-manage-subscription">
      <a href="<?php echo esc_url($customer_portal_url); ?>"><?php esc_html_e('Manage Subscription', 'pretty-link'); ?></a>
    </p>
  <?php endif; ?>
</div>
