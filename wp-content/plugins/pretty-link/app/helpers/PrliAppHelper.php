<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAppHelper {
  public static function page_title($page_title) {
    require(PRLI_VIEWS_PATH . '/shared/title_text.php');
  }

  public static function info_tooltip($id, $title, $info) {
    $content = "<strong>$title</strong><br>$info";
    ?>
    <span id="prli-tooltip-<?php echo esc_attr($id); ?>" class="prli-tooltip" data-title= "<?php echo esc_attr($content); ?>" >
      <span><i class="pl-icon pl-icon-info-circled pl-16" ></i></span>
    </span>
    <?php
  }

  public static function pro_only_feature_indicator($feature='', $label=null, $title=null) {
    $feature = esc_url_raw( empty($feature) ? '' : "?{$feature}" );
    $label = esc_html( is_null($label) ? __('Pro', 'pretty-link') : $label );
    $title = esc_attr( is_null($title) ? __('Upgrade to Pro to unlock this feature', 'pretty-link') : $title );

    return sprintf(
      '<span class="prli-pro-only-indicator" title="%1$s"><a href="https://prettylinks.com/pl/pro-feature-indicator/upgrade%2$s">%3$s</a></span>',
      $title,
      $feature,
      $label
    );
  }

  public static function wp_pages_dropdown($field_name, $page_id = 0, $auto_page = false, $empty_option = false) {
    $pages = PrliUtils::get_pages();
    $selected_page_id = isset($_POST[$field_name]) ? sanitize_text_field(wp_unslash($_POST[$field_name])) : $page_id;
    ?>
    <select name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>">
      <?php if($empty_option) : ?>
        <option value=""><?php echo esc_html($empty_option); ?></option>
      <?php endif ?>
      <?php if($auto_page) : ?>
        <option value="auto_create_page"><?php esc_html_e('- Auto Create New Page -', 'pretty-link'); ?></option>
      <?php endif; ?>
      <?php foreach($pages as $page) : ?>
        <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_page_id, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
      <?php endforeach; ?>
    </select>
    <?php if(is_numeric($selected_page_id)) : ?>
      <?php $permalink = get_permalink($selected_page_id); ?>
      <?php if($permalink) : ?>
        <a href="<?php echo esc_url(admin_url("post.php?post={$selected_page_id}&action=edit")); ?>" target="_blank" class="button"><?php esc_html_e('Edit', 'pretty-link'); ?></a>
        <a href="<?php echo esc_url($permalink); ?>" target="_blank" class="button"><?php esc_html_e('View', 'pretty-link'); ?></a>
      <?php endif; ?>
    <?php endif;
  }
}

