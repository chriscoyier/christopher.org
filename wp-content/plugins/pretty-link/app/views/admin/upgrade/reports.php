<div class="pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <table class="widefat post fixed" cellspacing="0">
      <thead>
        <tr>
          <th class="manage-column" width="35%">
            <a href="#">
              <?php esc_html_e('Name', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column" width="35%">
            <a href="#">
              <?php esc_html_e('Goal Link', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column" width="10%">
            <a href="#">
              <?php esc_html_e('Links', 'pretty-link'); ?>
            </a>
          </th>
          <th class="manage-column" width="20%">
            <a href="#">
              <?php esc_html_e('Created', 'pretty-link'); ?>
            </a>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="4"><?php esc_html_e('No Pretty Link Reports were found', 'pretty-link'); ?></td>
        </tr>
      </tbody>
        <tfoot>
        <tr>
          <th class="manage-column"><?php esc_html_e('Name', 'pretty-link'); ?></th>
          <th class="manage-column"><?php esc_html_e('Goal Link', 'pretty-link'); ?></th>
          <th class="manage-column"><?php esc_html_e('Links', 'pretty-link'); ?></th>
          <th class="manage-column"><?php esc_html_e('Created', 'pretty-link'); ?></th>
        </tr>
        </tfoot>
    </table>
  </div>
  <?php
  $section_title = 'Link Reports';
  $upgrade_link = 'https://prettylinks.com/pl/pro-feature-indicator/upgrade?link-reports';
  include_once PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php";
  ?>
</div>