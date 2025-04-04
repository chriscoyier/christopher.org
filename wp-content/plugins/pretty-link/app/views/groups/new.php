<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

  $name = isset($_POST['name']) ? sanitize_text_field(stripslashes($_POST['name'])) : '';
  $description = isset($_POST['description']) ? sanitize_textarea_field(stripslashes($_POST['description'])) : '';
?>

<div class="wrap">
  <?php PrliAppHelper::page_title(__('New Group', 'pretty-link')); ?>

  <?php
    require(PRLI_VIEWS_PATH.'/shared/errors.php');
  ?>

  <form name="form1" method="post" action="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups")); ?>">
  <input type="hidden" name="action" value="create">
  <?php wp_nonce_field('update-options'); ?>

  <table class="form-table">
    <tr class="form-field">
      <td width="75px" valign="top"><?php esc_html_e('Name*:', 'pretty-link'); ?> </td>
      <td><input type="text" name="name" value="<?php echo esc_attr($name); ?>" size="75">
        <br/><span class="setting-description"><?php esc_html_e("This is how you'll identify your Group.", 'pretty-link'); ?></span></td>
    </tr>
    <tr class="form-field">
      <td valign="top"><?php esc_html_e('Description:', 'pretty-link'); ?> </td>
      <td><textarea style="height: 100px;" name="description"><?php echo esc_textarea($description) ?></textarea>
      <br/><span class="setting-description"><?php esc_html_e('A Description of this group.', 'pretty-link'); ?></span></td>
    </tr>
    <tr class="form-field" valign="top">
      <td valign="top"><?php esc_html_e('Links:', 'pretty-link'); ?> </td>
      <td valign="top">
        <div style="height: 400px; width: 95%; border: 1px solid #8cbdd5; overflow: auto;">
          <table width="100%" cellspacing="0">
            <thead style="background-color: #dedede; padding: 0px; margin: 0px; line-height: 8px; font-size: 14px;">
              <th width="50%" style="padding-left: 5px; margin: 0px;"><strong><?php esc_html_e('Name', 'pretty-link'); ?></strong></th>
              <th width="50%" style="padding-left: 5px; margin: 0px;"><strong><?php esc_html_e('Current Group', 'pretty-link'); ?></strong></th>
            </thead>
            <?php
            for($i = 0; $i < count($links); $i++) {
              $link = $links[$i];
              ?>
              <tr style="line-height: 15px; font-size: 12px;<?php echo (($i%2)?' background-color: #efefef;':''); ?>">
                <td style="min-width: 50%; width: 50%;">
                  <input type="checkbox" style="width: 15px;" name="link[<?php echo esc_attr($link->id); ?>]" <?php echo ((isset($_POST['link'][$link->id]) and $_POST['link'][$link->id] == 'on')?'checked="true"':''); ?>/>
                  <?php echo esc_html(stripslashes($link->name)) . " <strong>(" . esc_html(stripslashes($link->slug)) . ")</strong>"; ?>
                </td>
                <td style="min-width: 50%; width: 50%;"><?php echo esc_html(stripslashes($link->group_name)); ?></td>
              </tr>
              <?php
            }
            ?>
          </table>
        </div>
        <span class="setting-description">
          <?php
            printf(
              // translators: %1$s: open strong tag, %2$s close strong tag
              esc_html__('Select some links for this group. %1$sNote: each link can only be in one group at a time.%2$s', 'pretty-link'),
              '<strong>',
              '</strong>'
            );
          ?>
        </span>
      </td>
    </tr>
  </table>

  <p class="submit">
  <input type="submit" class="button button-primary" name="submit" value="<?php esc_attr_e('Create', 'pretty-link'); ?>" /> &nbsp; <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups")); ?>" class="button"><?php esc_html_e('Cancel', 'pretty-link'); ?></a>
  </p>

  </form>
</div>
