<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>

<table class="form-table">
  <tr>
    <th scope="row">
      <?php esc_html_e('Redirection*', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-redirection-type',
              esc_html__('Redirection Type', 'pretty-link'),
              esc_html__('This is the method of redirection for your link.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <select id="redirect_type" name="redirect_type">
        <option value="307"<?php echo $values['redirect_type']['307']; ?>><?php esc_html_e("307 (Temporary)", 'pretty-link') ?>&nbsp;</option>
        <option value="302"<?php echo $values['redirect_type']['302']; ?>><?php esc_html_e("302 (Temporary)", 'pretty-link') ?>&nbsp;</option>
        <option value="301"<?php echo $values['redirect_type']['301']; ?>><?php esc_html_e("301 (Permanent)", 'pretty-link') ?>&nbsp;</option>
        <?php do_action('prli_redirection_types', $values, false); ?>
      </select>
      <?php
        global $plp_update;
        if(!$plp_update->is_installed()) {
          ?>
          <p class="description"><?php printf(esc_html__('Get cloaked redirects, Javascript redirects and more when you %1$sUpgrade to PRO%2$s', 'pretty-link'),'<a href="https://prettylinks.com/pl/link-form/upgrade" target="_blank">','</a>') ?></p>
          <?php
        }
      ?>
    </td>
  </tr>
  <tr id="prli_target_url">
    <th scope="row">
      <?php esc_html_e('Target URL*', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-target-url',
              esc_html__('Target URL', 'pretty-link'),
              esc_html__('This is the URL that your Pretty Link will redirect to.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <textarea id="prli_url" class="large-text" name="prli_url"><?php echo esc_textarea($values['url']); ?></textarea>
      <?php do_action('prli_link_form_after_target_url'); ?>
    </td>
  </tr>
  <tr>
    <th scope="row">
      <?php esc_html_e('Pretty Link*', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-slug',
              esc_html__('Pretty Link', 'pretty-link'),
              esc_html__('This is how your pretty link will appear. You can edit the Pretty Link slug here.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <strong><?php global $prli_blogurl; echo esc_html($prli_blogurl); ?></strong>/<input type="text" id="prli_slug" name="slug" class="regular-text" value="<?php echo esc_attr($values['slug']); ?>" />
      <span class="prli-clipboard prli-edit-link-clipboard">
        <i class="pl-icon-clipboard"></i>
      </span>
    </td>
  </tr>
  <tr>
    <th scope="row">
      <?php esc_html_e('Notes', 'pretty-link'); ?>
      <?php PrliAppHelper::info_tooltip(
              'prli-link-options-notes',
              esc_html__('Notes', 'pretty-link'),
              esc_html__('This is a field where you can enter notes about a particular link. This notes field is mainly for your own link management needs. It isn\'t currently used anywhere on the front end.', 'pretty-link')
            ); ?>
    </th>
    <td>
      <textarea class="large-text" name="prli_description"><?php echo esc_textarea($values['description']); ?></textarea>
    </td>
  </tr>
  <?php do_action('prli_link_form_basic'); ?>
</table>

