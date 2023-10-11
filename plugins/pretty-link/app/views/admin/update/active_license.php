<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
  if(!isset($editions)) {
    $editions = PrliUtils::is_incorrect_edition_installed();
  }

  if(is_array($editions)) {
    printf(
      '<div class="notice notice-warning inline"><p>%1$s<img id="prli-install-license-edition-loading" class="prli-hidden" src="%2$s" alt="%3$s" /></p></div>',
      sprintf(
        /* translators: %1$s: the license edition, %2$s: the installed edition, %3$s: open link tag, %4$s: close link tag */
        esc_html__('This License Key is for %1$s, but %2$s is installed. %3$sClick here%4$s to install the correct edition for the license (%1$s).', 'pretty-link'),
        '<strong>' . esc_html($editions['license']['name']) . '</strong>',
        '<strong>' . esc_html($editions['installed']['name']) . '</strong>',
        '<a id="prli-install-license-edition" href="#">',
        '</a>'
      ),
      esc_url(PRLI_IMAGES_URL . '/square-loader.gif'),
      esc_html__('Loading...', 'pretty-link')
    );
  }
?>
<div class="prli-license-active">
  <div><h4><?php esc_html_e('Active License Key Information:', 'pretty-link'); ?></h4></div>
  <table>
    <tr>
      <td><?php esc_html_e('License Key:', 'pretty-link'); ?></td>
      <td>********-****-****-****-<?php echo esc_html(substr($li['license_key']['license'], -12)); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Status:', 'pretty-link'); ?></td>
      <td><b><?php echo esc_html(sprintf(__('Active on %s', 'pretty-link'), PrliUtils::site_domain())); ?></b></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Product:', 'pretty-link'); ?></td>
      <td><?php echo esc_html($li['product_name']); ?></td>
    </tr>
    <tr>
      <td><?php esc_html_e('Activations:', 'pretty-link'); ?></td>
      <td>
        <?php
          printf(
            /* translators: %1$s: open b tag, %2$s: close b tag, %3$d: current activation count, %4$s: max activations */
            esc_html__('%1$s%3$d of %4$s%2$s sites have been activated with this license key', 'pretty-link'),
            '<b>',
            '</b>',
            esc_html($li['activation_count']),
            esc_html(ucwords($li['max_activations']))
          );
        ?>
      </td>
    </tr>
  </table>
  <div class="prli-deactivate-button">
    <button type="button" id="prli-deactivate-license-key" class="button button-primary"><?php echo esc_html(sprintf(__('Deactivate License Key on %s', 'pretty-link'), PrliUtils::site_domain())); ?></button>
  </div>
</div>
<?php if(!$this->is_installed()): ?>
  <div><a href="<?php echo esc_url($this->update_plugin_url()); ?>" class="button button-primary"><?php esc_html_e('Upgrade plugin to Pro', 'pretty-link'); ?></a></div>
  <div>&nbsp;</div>
<?php endif; ?>
<?php require PRLI_VIEWS_PATH . '/admin/update/edge_updates.php'; ?>
<br/>
<div id="prli-version-string"><?php printf(esc_html__("You're currently running version %s of Pretty Links Pro", 'pretty-link'), '<b>'.esc_html(PRLI_VERSION).'</b>'); ?></div>
