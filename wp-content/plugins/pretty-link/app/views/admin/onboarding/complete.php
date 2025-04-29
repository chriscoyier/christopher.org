<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h2 class="prli-wizard-finished"><?php esc_html_e("That’s It! You're Ready to Roll", 'pretty-link'); ?></h2>
<div id="prli-wizard-completed">
  <div id="prli-wizard-content-section"><?php echo PrliOnboardingHelper::get_completed_step_urls_html(); ?></div>

  <h2 class="prli-wizard-step-title"><?php esc_html_e('Your next steps...', 'pretty-link'); ?></h2>
  <div class="prli-wizard-selected-content prli-wizard-selected-content-full-scape">
    <div class="prli-wizard-selected-content-column">
      <a href="https://prettylinks.com/blog/how-to-use-pretty-links-plugin/">
        <div class="prli-wizard-selected-content-image-box">
          <div class="prli-wizard-selected-content-image-thumbnail">
            <img src="<?php echo PRLI_URL; ?>/images/onboarding/how-to-use-pretty-links.jpg" alt="<?php esc_html_e('Getting Started with Pretty Links','pretty-link'); ?>" />
          </div>
          <div class="prli-wizard-selected-content-image-description">
             <a href="https://prettylinks.com/blog/how-to-use-pretty-links-plugin/" target="_blank">
              <h4 class="prli-image-title"><?php esc_html_e('Getting Started with Pretty Links','pretty-link'); ?></h4>
              <p class="prli-image-desc"><?php esc_html_e('Now that you\'ve configured Pretty Links, it\'s time to learn the basics of the plugin so you can start monetizing your content!','pretty-link'); ?></p>
            </a>
          </div>
        </div>
    </div>
  </div>
  <div class="prli-wizard-selected-content prli-wizard-selected-content-full-scape">
    <div class="prli-wizard-selected-content-column">
      <a href="https://prettylinks.com/blog/prettypay/">
        <div class="prli-wizard-selected-content-image-box">
          <div class="prli-wizard-selected-content-image-thumbnail">
            <img src="<?php echo PRLI_URL; ?>/images/onboarding/PrettyPay_Pretty-Links.webp" alt="<?php esc_html_e('Start Selling with PrettyPay™','pretty-link'); ?>" />
          </div>
          <div class="prli-wizard-selected-content-image-description">
            <a href="https://prettylinks.com/blog/prettypay/" target="_blank">
              <h4 class="prli-image-title"><?php esc_html_e('Start Selling with PrettyPay™','pretty-link'); ?></h4>
              <p class="prli-image-desc"><?php esc_html_e('Experience the simplicity of selling YOUR creations with customized payment links, designed to make every transaction smooth and personal.','pretty-link'); ?></p>
            </a>
          </div>
        </div>
    </div>
  </div>

  <h2 class="prli-wizard-step-title"><?php esc_html_e('Stay in the loop an all things Pretty Links...', 'pretty-link'); ?></h2>
  <div class="prli-wizard-selected-content prli-wizard-selected-content-full-scape">
    <div class="prli-wizard-selected-content-column">
        <div class="prli-wizard-selected-content-image-box">
          <div class="prli-wizard-selected-content-image-thumbnail">
            <img src="<?php echo PRLI_URL; ?>/images/onboarding/pretty-links-blog-screenshot.jpg" alt="<?php esc_html_e('Pretty Links Blog','pretty-link'); ?>" />
          </div>
          <div class="prli-wizard-selected-content-image-description">
            <a href="https://prettylinks.com/blog/" target="_blank">
              <h4 class="prli-image-title"><?php esc_html_e('Pretty Links Blog','pretty-link'); ?></h4>
              <p class="prli-image-desc"><?php esc_html_e('Sign up for tips, tricks, and industry updates from top affiliate marketers and influencers.','pretty-link'); ?></p>
            </a>
          </div>
        </div>
    </div>
  </div>
</div>
