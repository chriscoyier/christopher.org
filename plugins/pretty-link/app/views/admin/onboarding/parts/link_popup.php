<h2><?php esc_html_e('Create Pretty Link', 'pretty-link'); ?></h2>

<div id="prli-wizard-create-link-fields">
  <div class="prli-wizard-popup-field">
    <label for="prli-wizard-create-link-target-url"><?php esc_html_e('Target URL', 'pretty-link'); ?></label>
    <input type="text" id="prli-wizard-create-link-target-url" placeholder="<?php esc_attr_e('Enter the URL where your pretty link should redirect to.', 'pretty-link'); ?>">
  </div>

  <div class="prli-wizard-popup-field">
    <label for="prli-wizard-create-link-pretty-link"><?php esc_html_e('Pretty Link', 'pretty-link'); ?></label>
    <input type="text" id="prli-wizard-create-link-pretty-link" placeholder="<?php esc_attr_e('Enter the slug for your pretty link.', 'pretty-link'); ?>">
  </div>

  <div class="prli-wizard-popup-field">
    <label for="prli-wizard-create-link-redirection"><?php esc_html_e('Redirection', 'pretty-link'); ?></label>
    <select id="prli-wizard-create-link-redirection">
      <option value="302"><?php esc_html_e('302 (Temporary)', 'pretty-link'); ?></option>
      <option value="301"><?php esc_html_e('301 (Permanent)', 'pretty-link'); ?></option>
      <?php if($plp_update->is_installed_and_activated()): ?>
        <option value="cloak"><?php esc_html_e('Cloaked', 'pretty-link'); ?></option>
      <?php else: ?>
        <option disabled><?php esc_html_e('Cloaked (Pro)', 'pretty-link'); ?></option>
      <?php endif; ?>
    </select>
  </div>
</div>

<div class="prli-wizard-popup-button-row">
  <button type="button" id="prli-wizard-create-new-link-save" class="prli-wizard-button-blue"><?php esc_html_e('Save', 'pretty-link'); ?></button>
  <a target="_blank" class="prli-wizard-popuphelp" href="<?php echo admin_url('edit.php?post_type=pretty-link'); ?>">
    <?php
      printf(
        /* translators: %1$s: open underline tag, %2$s: close underline tag */
        esc_html__('More advanced options are available on the %1$sPretty Links%2$s page', 'pretty-link'),
        '<u>',
        '</u>'
      );
    ?>
  </a>
</div>