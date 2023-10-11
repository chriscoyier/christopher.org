<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
} ?>

<?php
global $plp_update;

if ($plp_update->is_installed()) {
  $id = isset($id) ? $id : false;
  do_action('prli_product_display_fields', $id);
} else {
?>

  <div class="pretty-link-blur-wrap">
    <div class="pretty-link-blur">
      <div class="prli-pd-head">
        <h2><?php esc_html_e('Product Display', 'pretty-link'); ?></h2>
        <button type="button" id="prli-pd-clipboard-btn"><?php esc_html_e('Copy Shortcode', 'pretty-link'); ?></button>
      </div>

      <table class="form-table prli-pd-settings">
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Theme', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-theme',
              esc_html__('Theme', 'pretty-link'),
              esc_html__('Theme to use for the display.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <select disabled>
              <option value="basic"><?php esc_html_e('Basic', 'pretty-link'); ?></option>
              <option value="boxed"><?php esc_html_e('Boxed', 'pretty-link'); ?></option>
              <option value="sharp"><?php esc_html_e('Sharp', 'pretty-link'); ?></option>
            </select>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Product Image', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-product-image',
              esc_html__('Product Image', 'pretty-link'),
              esc_html__('Upload an image associated with this product.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <div>
              <button type="button" class="button"><?php esc_html_e('Update Image', 'pretty-link'); ?></button>
            </div>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Description', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-description',
              esc_html__('Description', 'pretty-link'),
              esc_html__('Enter a short description for this product.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <?php
              $description_settings = array(
                'media_buttons' => false,
                'teeny' => true,
                'quicktags' => false,
                'tinymce' => array('toolbar1' => 'bold italic underline link fullscreen')
              );
              wp_editor('', 'prli_pd_description', $description_settings);
            ?>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Price', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-price',
              esc_html__('Price', 'pretty-link'),
              esc_html__('Enter the price for this product.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <input type="text" disabled>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Badge Text', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-badge-text',
              esc_html__('Badge Text', 'pretty-link'),
              esc_html__('Text shown as a badge at the top of the display.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <input type="text" disabled>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Primary Button Text', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-brimary-button-text',
              esc_html__('Primary Button Text', 'pretty-link'),
              esc_html__('Used to change the button text displayed for the target URL set up for the Pretty Link.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <input type="text" disabled>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Review URL', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-review-url',
              esc_html__('Review URL', 'pretty-link'),
              esc_html__('URL for the product review.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <input type="text" disabled>
          </td>
        </tr>
        <tr class="prli-pro-only">
          <th scope="row">
            <?php esc_html_e('Review Button Text', 'pretty-link'); ?>
            <?php PrliAppHelper::info_tooltip(
              'prli-pd-review-button-text',
              esc_html__('Review Button Text', 'pretty-link'),
              esc_html__('Used to change the button text displayed for the review URL.', 'pretty-link')
            ); ?>
          </th>
          <td>
            <input type="text" disabled>
          </td>
        </tr>
      </table>
    </div>

    <?php
      $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?product-display-settings';
      $section_title = '';
      include PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
    ?>
  </div>
<?php } ?>