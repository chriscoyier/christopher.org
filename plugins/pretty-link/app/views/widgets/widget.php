<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="wrap">
  <p style="text-align: left;">
    <a href="https://prettylinks.com/pl/widget/buy"><img style="border: 0px;" src="<?php echo esc_url(PRLI_IMAGES_URL . '/pl-logo-horiz-RGB.svg'); ?>" width="75%" /></a>
  </p>

  <form id="prli-quick-create" method="post">
    <div id="prli-quick-create-error" class="hidden"></div>
    <table class="form-table">
      <tr class="form-field">
        <td valign="top"><?php esc_html_e("Target URL", 'pretty-link'); ?></td>
        <td><input type="text" id="prli-quick-create-url" name="url" value="" size="75">
      </tr>
      <tr>
        <td valign="top"><?php esc_html_e("Pretty Link", 'pretty-link'); ?></td>
        <td><strong><?php echo esc_html($prli_blogurl); ?></strong>/<input type="text" id="prli-quick-create-slug" name="slug" value="<?php echo esc_attr($prli_link->generateValidSlug()); ?>">
      </tr>
    </table>

    <p class="submit">
      <input type="submit" name="Submit" value="<?php esc_attr_e('Create', 'pretty-link'); ?>" class="button button-primary" />
      <span id="prli-quick-create-loading" class="hidden"><img src="<?php echo PRLI_IMAGES_URL . '/square-loader.gif'; ?>" alt="<?php esc_attr_e('Loading...', 'pretty-link'); ?>"></span>
    </p>
  </form>
</div>
