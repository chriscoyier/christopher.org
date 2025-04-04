<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="prli-wizard-create-category-upgrade">
  <div id="prli-wizard-create-select-membership">
    <h2 class="prli-wizard-step-title"><?php esc_html_e("Let's Get Organized!", 'pretty-link'); ?></h2>
    <p class="prli-wizard-step-description"><?php esc_html_e("By categorizing your links, you'll have a neat and tidy system in place, allowing you to find what you need in a snap as you continue adding more links to your site.", 'pretty-link'); ?></p>

    <p class="prli-wizard-step-description"><?php esc_html_e("Uh-oh! Pretty Links Lite doesn't have the superpower to support Categories. Upgrade to Pretty Links Pro for full control over organizing your links.", 'pretty-link'); ?></p>

    <?php printf(
            __('<a class="prli-wizard-button-blue" href="%s">Upgrade to Pretty Links Pro Now</a>', 'pretty-link'),
            esc_url(PrliOnboardingHelper::get_upgrade_pricing_url())
          );
    ?>
  </div>
</div>