<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');
?>
<div class="wrap">
  <?php PrliAppHelper::page_title(__('Groups', 'pretty-link')); ?>
  <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-groups&action=new')); ?>" class="page-title-action"><?php esc_html_e('Add Group', 'pretty-link'); ?></a>
  <hr class="wp-header-end">

  <?php if(empty($groups)): ?>
    <div class="updated notice notice-success is-dismissible"><p><?php echo $prli_message; ?></p></div>
  <?php endif; ?>

  <div id="search_pane" style="float: right;">
    <form class="form-fields" name="group_form" method="post" action="">
      <?php wp_nonce_field('prli-groups'); ?>
      <input type="hidden" name="sort" id="sort" value="<?php echo esc_attr($sort_str); ?>" />
      <input type="hidden" name="sdir" id="sort" value="<?php echo esc_attr($sdir_str); ?>" />
      <input type="text" name="search" id="search" value="<?php echo esc_attr($search_str); ?>" style="display:inline;"/>
      <div class="submit" style="display: inline;"><input class="button button-primary" type="submit" name="Submit" value="<?php esc_attr_e('Search', 'pretty-link'); ?>"/>
      <?php
      if(!empty($search_str)) {
        ?>
        &nbsp; <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups")); ?>" class="button"><?php esc_html_e('Reset', 'pretty-link'); ?></a>
        <?php
      }
      ?>
      </div>
    </form>
  </div>

<?php
  require(PRLI_VIEWS_PATH.'/shared/table-nav.php');
?>

  <table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
      <th class="manage-column" width="50%">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-groups&sort=name'.(($sort_str == 'name' && $sdir_str == 'asc')?'&sdir=desc':''))); ?>">
          <?php esc_html_e('Name', 'pretty-link'); echo (($sort_str == 'name')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
        </a>
      </th>
      <th class="manage-column" width="20%">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-groups&sort=link_count'.(($sort_str == 'link_count' && $sdir_str == 'asc')?'&sdir=desc':''))); ?>">
          <?php esc_html_e('Links', 'pretty-link'); echo (($sort_str == 'link_count')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
        </a>
      </th>
      <th class="manage-column" width="30%">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pretty-link-groups&sort=created_at'.(($sort_str == 'created_at' and $sdir_str == 'asc')?'&sdir=desc':''))); ?>">
          <?php esc_html_e('Created', 'pretty-link'); echo ((empty($sort_str) or $sort_str == 'created_at')?'&nbsp;&nbsp;&nbsp;<img src="'.esc_url(PRLI_IMAGES_URL.'/'.((empty($sort_str) or $sdir_str == 'desc')?'arrow_down.png':'arrow_up.png')).'"/>':'') ?>
        </a>
      </th>
    </tr>
    </thead>
  <?php

  if($record_count <= 0)
  {
      ?>
    <tr>
      <td colspan="5"><?php esc_html_e('No Pretty Link Groups were found', 'pretty-link'); ?></td>
    </tr>
    <?php
  }
  else
  {
    $row_index=0;
    foreach($groups as $group)
    {
      $alternate = ( $row_index++ % 2 ? '' : 'alternate' );
      ?>
      <tr id="record_<?php echo esc_attr($group->id); ?>" class="<?php echo $alternate; ?>">
        <td class="edit_group">
        <a class="group_name" href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups&action=edit&id={$group->id}")); ?>" title="<?php echo esc_attr(sprintf(__('Edit %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php echo esc_html(stripslashes($group->name)); ?></a>
          <br/>
          <div class="group_actions">
            <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups&action=edit&id={$group->id}")); ?>" title="<?php echo esc_attr(sprintf(__('Edit %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php esc_html_e('Edit', 'pretty-link'); ?></a>&nbsp;|
            <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-groups&action=destroy&id={$group->id}")); ?>"  onclick="return confirm('<?php echo esc_attr(sprintf(__('Are you sure you want to delete your %s Pretty Link Group?', 'pretty-link'), stripslashes($group->name))); ?>');" title="<?php echo esc_attr(sprintf(__('Delete %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php esc_html_e('Delete', 'pretty-link'); ?></a>&nbsp;|
            <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link&group={$group->id}")); ?>" title="<?php echo esc_attr(sprintf(__('View links in %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php esc_html_e('Links', 'pretty-link'); ?></a>&nbsp;|
            <a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link-clicks&group={$group->id}")); ?>" title="<?php echo esc_attr(sprintf(__('View hits in %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php esc_html_e('Clicks', 'pretty-link'); ?></a>
          </div>
        </td>
        <td><a href="<?php echo esc_url(admin_url("admin.php?page=pretty-link&group={$group->id}")); ?>" title="<?php echo esc_attr(sprintf(__('View links in %s', 'pretty-link'), stripslashes($group->name))); ?>"><?php echo esc_html($group->link_count); ?></a></td>
        <td><?php echo esc_html($group->created_at); ?></td>
      </tr>
      <?php
    }
  }
  ?>
    <tfoot>
    <tr>
      <th class="manage-column"><?php esc_html_e('Name', 'pretty-link'); ?></th>
      <th class="manage-column"><?php esc_html_e('Links', 'pretty-link'); ?></th>
      <th class="manage-column"><?php esc_html_e('Created', 'pretty-link'); ?></th>
    </tr>
    </tfoot>
</table>
<?php
  require(PRLI_VIEWS_PATH.'/shared/table-nav.php');
?>

</div>
