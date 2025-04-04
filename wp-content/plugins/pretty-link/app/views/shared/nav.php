<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

global $plp_update;

if($plp_update->is_installed()) {
  $support_link = "&nbsp;|&nbsp;<a href=\"https://prettylinks.com/pl/nav/um\" target=\"_blank\">" . esc_html__('Pro Manual', 'pretty-link') . '</a>';
}
else {
  $support_link = "&nbsp;|&nbsp;<a href=\"https://prettylinks.com/pl/nav/upgrade\" target=\"_blank\">" . esc_html__('Upgrade to Pro', 'pretty-link') . '</a>';
}

?>
<p class="prli-shared-header">
  <span><?php esc_html_e('Connect:', 'pretty-link'); ?></span>
  <a href="http://twitter.com/blairwilli"><img src="<?php echo PRLI_IMAGES_URL; ?>/twitter_32.png" class="prli-icon" /></a>
  <a href="http://www.facebook.com/pages/Pretty-Link/283252860401"><img src="<?php echo PRLI_IMAGES_URL; ?>/facebook_32.png" class="prli-icon" /></a>
  <br/>
  <?php esc_html_e('Get Help:', 'pretty-link'); ?>
  <a href="http://blairwilliams.com/xba" target="_blank"><?php esc_html_e('Tutorials', 'pretty-link'); ?></a>
  <?php echo $support_link; ?>
</p>

