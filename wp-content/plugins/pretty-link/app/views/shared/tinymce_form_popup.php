<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title><?php esc_html_e('Insert Pretty Link', 'pretty-link'); ?></title>
    <?php wp_print_styles('prli-tinymce-popup-form'); ?>
  </head>
  <body>
    <div id="errors"></div>
    <div id="prli_accordion">
      <h3><?php esc_html_e('Create New Pretty Link', 'pretty-link'); ?></h3>
      <div class="prlitinymce-options">
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Target URL', 'pretty-link'); ?>:</label>
          <input type="text" name="prli_insert_link_target" id="prli_insert_link_target" value="" />
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Slug', 'pretty-link'); ?>:</label>
          <input type="text" name="prli_insert_link_slug" id="prli_insert_link_slug" value="<?php echo esc_attr($random_slug); ?>" />
          <span id="prlitinymce-thinking" class="prlitinymce-hidden"><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" /></span>
          <span id="prlitinymce-good-slug" class="prlitinymce-hidden"><small><?php esc_html_e('valid', 'pretty-link'); ?></small></span>
          <span id="prlitinymce-bad-slug" class="prlitinymce-hidden"><small><?php esc_html_e('invalid', 'pretty-link'); ?></small></span>
          <input type="hidden" name="prli_is_valid_slug" id="prli_is_valid_slug" value="good" />
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Link Text', 'pretty-link'); ?>:</label>
          <input type="text" name="prli_insert_link_link_text" id="prli_insert_link_link_text" value="" />
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Redirect Type', 'pretty-link'); ?>:</label>
          <select name="prli_insert_link_redirect" id="prli_insert_link_redirect">
            <option value="default"><?php esc_html_e('Default', 'pretty-link'); ?></option>
            <option value="307"><?php esc_html_e('307 (Temporary)', 'pretty-link'); ?></option>
            <option value="302"><?php esc_html_e('302 (Temporary)', 'pretty-link'); ?></option>
            <option value="301"><?php esc_html_e('301 (Permanent)', 'pretty-link'); ?></option>
            <?php global $plp_update; ?>
            <?php if($plp_update->is_installed()): ?>
              <option value="prettybar"><?php esc_html_e('Pretty Bar', 'pretty-link'); ?></option>
              <option value="cloak"><?php esc_html_e('Cloaked', 'pretty-link'); ?></option>
              <option value="pixel"><?php esc_html_e('Pixel', 'pretty-link'); ?></option>
              <option value="metarefresh"><?php esc_html_e('Meta Refresh', 'pretty-link'); ?></option>
              <option value="javascript"><?php esc_html_e('Javascript', 'pretty-link'); ?></option>
            <?php endif; ?>
          </select>
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Nofollow', 'pretty-link'); ?>:</label>
          <select name="prli_insert_link_nofollow" id="prli_insert_link_nofollow">
            <option value="default"><?php esc_html_e('Default', 'pretty-link'); ?></option>
            <option value="enabled"><?php esc_html_e('Enabled', 'pretty-link'); ?></option>
            <option value="disabled"><?php esc_html_e('Disabled', 'pretty-link'); ?></option>
          </select>
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Sponsored', 'pretty-link'); ?>:</label>
          <select name="prli_insert_link_sponsored" id="prli_insert_link_sponsored">
            <option value="default"><?php esc_html_e('Default', 'pretty-link'); ?></option>
            <option value="enabled"><?php esc_html_e('Enabled', 'pretty-link'); ?></option>
            <option value="disabled"><?php esc_html_e('Disabled', 'pretty-link'); ?></option>
          </select>
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Tracking', 'pretty-link'); ?>:</label>
          <select name="prli_insert_link_tracking" id="prli_insert_link_tracking">
            <option value="default"><?php esc_html_e('Default', 'pretty-link'); ?></option>
            <option value="enabled"><?php esc_html_e('Enabled', 'pretty-link'); ?></option>
            <option value="disabled"><?php esc_html_e('Disabled', 'pretty-link'); ?></option>
          </select>
        </div>
        <div class="prlitinymce-options-row">
          <label>&nbsp;</label>
          <input type="checkbox" name="prli_insert_link_new_tab" id="prli_insert_link_new_tab" /> <?php esc_html_e('Open this Pretty Link in a new window/tab', 'pretty-link'); ?>
        </div>
        <div class="prlitinymce-options-row" id="prlitinymce-insert">
          <a href="javascript:PrliPopUpHandler.insert_new()" class="prli_button"><?php esc_html_e('Insert New Pretty Link', 'pretty-link'); ?></a>
          <span id="insert_loading" class="prlitinymce-hidden"><img src="<?php echo esc_url(includes_url('/js/thickbox/loadingAnimation.gif')); ?>" width="150" /></span>
        </div>
      </div>
      <h3><?php esc_html_e("Use Existing Pretty Link", 'pretty-link'); ?></h3>
      <div id="prlitinymce-search-area" class="prlitinymce-options">
        <input type="text" name="prli_search_box" id="prli_search_box" value="" placeholder="<?php esc_attr_e('Search by Slug, Title, or Target URL...', 'pretty-link'); ?>" />
        <div class="prlitinymce-options-row">
          <label class="lefty"><?php esc_html_e('Target URL', 'pretty-link'); ?>:</label>
          <small id="existing_link_target" class="righty"><?php esc_html_e('None', 'pretty-link'); ?></small>
        </div>
        <div class="prlitinymce-options-row">
          <label class="lefty"><?php esc_html_e('Pretty Link', 'pretty-link'); ?>:</label>
          <small id="existing_link_slug" class="righty"><?php esc_html_e('None', 'pretty-link'); ?></small>
        </div>
        <div class="prlitinymce-options-row">
          <label><?php esc_html_e('Link Text', 'pretty-link'); ?>:</label>
          <input type="text" name="existing_link_link_text" id="existing_link_link_text" value="" />
        </div>
        <div class="prlitinymce-options-row">
          <label>&nbsp;</label>
          <input type="checkbox" name="existing_link_new_tab" id="existing_link_new_tab" /> <?php esc_html_e('Open this Pretty Link in a new window/tab', 'pretty-link'); ?>
        </div>
        <div class="prlitinymce-options-row" id="existing_link_insert">
          <input type="hidden" name="existing_link_nofollow" id="existing_link_nofollow" value="0" />
          <input type="hidden" name="existing_link_sponsored" id="existing_link_sponsored" value="0" />
          <a href="javascript:PrliPopUpHandler.insert_existing()" class="prli_button"><?php esc_html_e('Insert Existing Pretty Link', 'pretty-link'); ?></a>
        </div>
      </div>
    </div>
    <?php wp_print_scripts('prli-tinymce-popup-form'); ?>
  </body>
</html>
