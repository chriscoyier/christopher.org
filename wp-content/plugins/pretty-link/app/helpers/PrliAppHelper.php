<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAppHelper {
  public static function page_title($page_title) {
    require(PRLI_VIEWS_PATH . '/shared/title_text.php');
  }

  public static function info_tooltip($id, $title, $info) {
    ?>
    <span id="prli-tooltip-<?php echo esc_attr($id); ?>" class="prli-tooltip">
      <span><i class="pl-icon pl-icon-info-circled pl-16"></i></span>
      <span class="prli-data-title prli-hidden"><?php echo $title; ?></span>
      <span class="prli-data-info prli-hidden"><?php echo $info; ?></span>
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
}

