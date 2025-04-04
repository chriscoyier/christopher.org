<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
$youtube_video_hash = md5($youtube_video_id);
?>

<div class="prli-wizard-onboarding-video-wrapper prli-wizard-onboarding-video-<?php echo esc_attr($step); ?>" id="wrapper_<?php echo $youtube_video_hash; ?>" >
   <div  class="prli-wizard-onboarding-video-expand" id="expand_<?php echo $youtube_video_hash; ?>" data-id="<?php echo $youtube_video_hash; ?>">
  <img src="<?php echo PRLI_IMAGES_URL . '/onboarding/expand.png'; ?>" class="prli-animation-shaking" />
</div>
  <div class="prli-video-wrapper" id="inner_<?php echo $youtube_video_hash; ?>">

    <div class='prli-video-holder' id="holder_<?php echo $youtube_video_hash; ?>">
         <a href='#' class='prli-video-play-button' id="prli_play_<?php echo $youtube_video_hash; ?>" data-hash="<?php echo $youtube_video_hash; ?>"  data-holder-id="holder_<?php echo $youtube_video_hash; ?>" data-id='<?php echo esc_attr($youtube_video_id); ?>'></a>
    </div>
  </div>
  <div class="prli-wizard-onboarding-video-collapse" data-id="<?php echo $youtube_video_hash; ?>" data-id="<?php echo $youtube_video_hash; ?>">
    <img src="<?php echo PRLI_IMAGES_URL . '/onboarding/collapse.png'; ?>" />
  </div>
</div>