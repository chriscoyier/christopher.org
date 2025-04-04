<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
$youtube_video_id = 'DEOJJ9BmSPE';
$step = 3;

require PRLI_VIEWS_PATH . '/admin/onboarding/video.php';
?>
<div id="prli-wizard-link-nav-skip">
  <button type="button" class="prli-wizard-button-link prli-wizard-go-to-step" data-step="5" data-context="skip"><span><?php esc_html_e('Skip', 'pretty-link'); ?></span></button>
</div>
<div id="prli-wizard-link-nav-continue" class="prli-hidden">
  <button type="button" class="prli-wizard-button-blue prli-wizard-go-to-step" data-step="4" data-context="continue"><?php esc_html_e('Continue', 'pretty-link'); ?></button>
</div>
