<div class="pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <table class="widefat post fixed" cellspacing="0">
      <thead>
        <tr>
          <th class="manage-column" width="55%">
            <a href="#">
              <?php esc_html_e('Title', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column" width="35%">
            <a href="#">
              <?php esc_html_e('Date', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column" width="10%">
            <?php esc_html_e('Links', 'pretty-link'); ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="3"><?php esc_html_e('No Display Groups found', 'pretty-link'); ?></td>
        </tr>
      </tbody>
        <tfoot>
        <tr>
          <th class="manage-column">
            <a href="#">
              <?php esc_html_e('Title', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column">
            <a href="#">
              <?php esc_html_e('Date', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column">
            <?php esc_html_e('Links', 'pretty-link'); ?>
          </th>
        </tr>
        </tfoot>
    </table>
  </div>
  <?php include_once PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php"; ?>
</div>