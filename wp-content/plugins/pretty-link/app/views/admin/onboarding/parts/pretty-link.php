<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
  global $plp_update;
  $features_enabled = PrliOnboardingHelper::get_selected_features(get_current_user_id());
  $import_export_selected = in_array('pretty-link-import-export', $features_enabled);
  $has_imported_links = PrliOnboardingHelper::get_has_imported_links();
?>

<div id="prli-wizard-create-select-link">
  <h2 class="prli-wizard-step-title"><?php esc_html_e("Create Your Pretty Link", 'pretty-link'); ?></h2>
  <p class="prli-wizard-step-description"><?php esc_html_e("Whether you have one, two, or even thousands of ugly-looking links, we're here to help you get them cleaned up and click-worthy.", 'pretty-link'); ?></p>
  <p class="prli-wizard-step-description"><?php esc_html_e("Click the \"Create New Link\" button below and we'll guide you through creating your first pretty link!", 'pretty-link'); ?></p>

  <?php if($import_export_selected): ?>
    <p class="prli-wizard-step-description"><?php _e("<i>Already have a long list of links you'd like to migrate over into Pretty Links?</i> Click \"Import Existing Links\" and have them automatically converted to pretty links.", 'pretty-link'); ?></p>
  <?php endif; ?>

  <div class="prli-wizard-button-group">
    <button type="button" id="prli-wizard-create-new-link" class="prli-wizard-button-blue"><?php esc_html_e('Create Your First Pretty Link', 'pretty-link'); ?></button>

    <?php if($import_export_selected): ?>
      <button type="button" id="prli-wizard-import-links" class="prli-wizard-button-link"><span><?php esc_html_e('Import Existing Links', 'pretty-link'); ?></span></button>
    <?php endif; ?>
  </div>
</div>

<div id="prli-wizard-create-new-link-popup" class="prli-wizard-popup mfp-hide"></div>

<div id="prli-wizard-import-links-popup" class="prli-wizard-popup mfp-hide">
  <h2><?php esc_html_e('Import Your Links', 'pretty-link'); ?></h2>

  <?php if($plp_update->is_installed_and_activated()): ?>
    <p><?php esc_html_e('To convert your existing links into pretty links, you\'ll need a CSV file that\'s compatible with the plugin.', 'pretty-link'); ?></p>
    <p><?php esc_html_e('For an example of a properly formatted Pretty Links CSV file, please click the "Download Sample CSV" button below.', 'pretty-link'); ?></p>

    <?php printf(
            __('<a class="prli-wizard-button-secondary" href="%s">Download Sample CSV</a>', 'pretty-link'),
            esc_url(PLP_URL . '/csv_sample/link_sample.csv')
          );
    ?>

    <p>
      <?php printf(
            __('For more detailed instructions on how to configure your CSV file, check out the instructional video on the left-hand side of the page or refer to the <a href="%s" class="prli-wizard-kb-link" target="_blank">Importing and Exporting Links</a> article in our knowledge base.', 'pretty-link'),
            esc_url('https://prettylinks.com/docs/importing-and-exporting-your-links/')
          );
      ?>
    </p>

    <div class="prli-wizard-popup-field">
      <input type="file" id="prli-wizard-import-file" name="importedfile">
    </div>

    <div id="prli-wizard-import-links-popup-info" class="prli-hidden">
      <h3><?php esc_html_e('Import Information', 'pretty-link'); ?></h3>

      <div><?php esc_html_e('Links Created: ', 'pretty-link'); ?><span id="prli-wizard-import-created-count">0</span></div>
      <div><?php esc_html_e('Links Updated: ', 'pretty-link'); ?><span id="prli-wizard-import-updated-count">0</span></div>
      <div id="prli-wizard-import-failed-create" class="prli-hidden"><?php esc_html_e('Links Unable to be Created: ', 'pretty-link'); ?><span id="prli-wizard-import-failed-create-count">0</span></div>
      <div id="prli-wizard-import-failed-update" class="prli-hidden"><?php esc_html_e('Links Unable to be Updated: ', 'pretty-link'); ?><span id="prli-wizard-import-failed-update-count">0</span></div>

      <div id="prli-wizard-import-failed-rows-container" class="prli-hidden">
        <p class="prli-wizard-import-links-failed-rows-text"><?php esc_html_e('Failed Rows:', 'pretty-link'); ?></p>
        <textarea id="prli-wizard-import-failed-rows"></textarea>
      </div>
    </div>

    <div class="prli-wizard-popup-button-row">
      <button type="button" id="prli-wizard-import-links-save" class="prli-wizard-button-blue"><?php esc_html_e('Import', 'pretty-link'); ?></button>
    </div>
  <?php else: ?>
    <p><?php _e('<i>Oops!</i> Pretty Links Lite cannot access the Importer feature. Upgrade to Pretty Links Pro to unlock this awesome functionality and enjoy all its benefits.', 'pretty-link'); ?></p>

    <p><?php esc_html_e('Don\'t want to upgrade yet? Click the "Create New Pretty Link" button to get started with creating your first pretty link.', 'pretty-link'); ?></p>

    <?php printf(
            __('<a class="prli-wizard-button-blue" href="%s">Upgrade to Pretty Links Pro Now</a>', 'pretty-link'),
            esc_url(PrliOnboardingHelper::get_upgrade_pricing_url())
          );
    ?>
  <?php endif; ?>
</div>

<?php if($has_imported_links): ?>
  <div id="prli-wizard-choose-link-results">
    <?php echo PrliOnboardingController::render_links_list(); ?>
  </div>
<?php else: ?>
  <div id="prli-wizard-selected-content" class="prli-hidden">
    <h2 class="prli-wizard-step-title"><?php esc_html_e('Your Pretty Link', 'pretty-link'); ?></h2>
    <div class="prli-wizard-selected-content">
      <div>
        <div class="prli-wizard-selected-content-heading"></div>
        <div class="prli-wizard-selected-content-name"></div>
      </div>
      <div>
        <div class="prli-wizard-selected-content-expand-menu" data-id="prli-wizard-selected-content-menu">
          <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/expand-menu.svg'); ?>" alt="">
        </div>
        <div id="prli-wizard-selected-content-menu" class="prli-wizard-selected-content-menu prli-hidden">
          <div class="prli-wizard-selected-content-delete"><?php esc_html_e('Remove', 'pretty-link'); ?></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>