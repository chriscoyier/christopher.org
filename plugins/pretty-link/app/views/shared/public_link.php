<?php if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

global $plp_update;
$target_url_display = substr($target_url,0,50) . ((strlen($target_url)>50)?"...":'');

wp_register_style('tooltipster', PRLI_CSS_URL . '/tooltipster.bundle.min.css', array(), '4.2.6');
wp_register_style('tooltipster-sidetip', PRLI_CSS_URL . '/tooltipster-sideTip-borderless.min.css', array('tooltipster'), '4.2.6');
wp_register_style('prli-animation', PRLI_VENDOR_LIB_URL.'/fontello/css/animation.css', array(), PRLI_VERSION);
wp_register_style('prli-icons', PRLI_VENDOR_LIB_URL.'/fontello/css/pretty-link.css', array(), PRLI_VERSION);
wp_register_style('prli-social-buttons', PRLI_CSS_URL . '/social_buttons.css', array(), PRLI_VERSION);
wp_register_style('prli-public-link', PRLI_CSS_URL . '/public_link.css', array('tooltipster', 'tooltipster-sidetip', 'prli-animation', 'prli-icons', 'prli-social-buttons'), PRLI_VERSION);

wp_register_script('clipboard-js', PRLI_JS_URL . '/clipboard.min.js', array(), '2.0.0');
wp_register_script('tooltipster', PRLI_JS_URL . '/tooltipster.bundle.min.js', array('jquery'), '4.2.6');
wp_register_script('prli-admin-link-list', PRLI_JS_URL . '/admin_link_list.js', array('jquery', 'clipboard-js', 'tooltipster'), PRLI_VERSION);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">

    <title><?php esc_html_e('Here is your Pretty Link', 'pretty-link'); ?></title>

    <?php wp_print_styles('prli-public-link'); ?>
  </head>
  <body>
    <div class="prli-public-logo"><img src="<?php echo PRLI_IMAGES_URL; ?>/pl-logo-horiz-RGB.svg" /></div>

    <div class="prli-public-wrap">
      <div class="prli-public-label"><em><?php esc_html_e('Here\'s your pretty link for', 'pretty-link'); ?></em></div>
      <div class="prli-public-name"><?php echo esc_html($target_url_title); ?></div>
      <div class="prli-public-target">(<span title="<?php echo esc_url($target_url); ?>"><?php echo esc_html($target_url_display); ?></span>)</div>

      <div class="prli-public-pretty-link">
        <span class="prli-public-pretty-link-display"><a href="<?php echo esc_url($pretty_link); ?>"><?php echo esc_url($pretty_link); ?></a></span>
        <span class="prli-clipboardjs prli-public-pretty-link-copy"><i class="pl-icon-clipboard pl-list-icon icon-clipboardjs" data-clipboard-text="<?php echo esc_url($pretty_link); ?>"></i></span>
      </div>
    </div>

    <?php if( $plp_update->is_installed() ): ?>
      <div class="prli-public-social"><?php esc_html_e('send this link to:', 'pretty-link'); ?></div>
      <?php echo PlpSocialButtonsHelper::get_social_buttons_bar($pretty_link_id); ?>
    <?php endif; ?>

    <div class="prli-public-back"><a href="<?php echo esc_url($target_url); ?>">&laquo; <?php esc_html_e('back', 'pretty-link'); ?></a></div>

    <?php wp_print_scripts('prli-admin-link-list'); ?>
  </body>
</html>
