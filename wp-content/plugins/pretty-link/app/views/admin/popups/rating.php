<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php $plp_update = new PrliUpdateController(); ?>

<div id="prli-rating-popup" class="mfp-hide prli-popup prli-auto-open">
  <p><img src="<?php echo PRLI_IMAGES_URL . '/pl-logo-horiz-RGB.svg'; ?>" width="200" height="32" /></p>
  <div id="prli-rating-stage-one">
    <center><h2><?php esc_html_e('Are you enjoying using Pretty Links?', 'pretty-link'); ?></h2></center>
    <div>&nbsp;</div>

    <center>
      <button data-popup="rating" class="prli-rating-enjoy-yes-popup button-primary"><?php esc_html_e('Yes', 'pretty-link'); ?></button>
      <button data-popup="rating" class="prli-rating-enjoy-no-popup button"><?php esc_html_e('No', 'pretty-link'); ?></button>
    </center>
  </div>
  <div id="prli-rating-stage-two-yes" class="prli-hidden">
    <h2><?php esc_html_e('Rate Pretty Links', 'pretty-link'); ?></h2>
    <p><?php esc_html_e('If you enjoy using Pretty Links would you mind taking a moment to rate it on WordPress.org? It won\'t take more than a minute.', 'pretty-link'); ?></p>
    <p><?php esc_html_e('Thanks for your support!', 'pretty-link'); ?></p>
    <div>&nbsp;</div>
    <div>&nbsp;</div>

    <center>
      <button data-popup="rating" class="prli-delay-popup button"><?php esc_html_e('Remind Me Later', 'pretty-link'); ?></button>
      <button data-popup="rating" data-href="https://prettylinks.com/pl/footer/review" class="prli-stop-popup button-primary"><?php esc_html_e('Review Pretty Links', 'pretty-link'); ?></button>
    </center>
  </div>
  <div id="prli-rating-stage-two-no" class="prli-hidden">
    <h2><?php esc_html_e('Leave Feedback', 'pretty-link'); ?></h2>
    <p><?php esc_html_e('To help us improve Pretty Links, would you mind taking a moment to leave feedback?', 'pretty-link'); ?></p>
    <p><?php esc_html_e('Thanks for your support!', 'pretty-link'); ?></p>
    <div>&nbsp;</div>
    <div>&nbsp;</div>

    <center>
      <button data-popup="rating" class="prli-delay-popup button"><?php esc_html_e('Remind Me Later', 'pretty-link'); ?></button>
      <a href="https://prettylinks.com/feedback/" target="_blank" class="prli-leave-feedback button-primary"><?php esc_html_e('Leave Feedback', 'pretty-link'); ?></a>
      <?php if($plp_update->is_installed()): ?>
        <div>&nbsp;</div>
        <a href="" data-popup="rating" class="prli-stop-popup"><?php esc_html_e('Never Remind Me Again', 'pretty-link'); ?></a>
      <?php endif; ?>
    </center>
  </div>

</div>

