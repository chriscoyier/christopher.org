<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php
  $features = PrliOnboardingHelper::get_selected_features(get_current_user_id());
  $features_addons_selectable = PrliOnboardingHelper::features_addons_selectable_list();
?>
<h2 class="prli-wizard-step-title"><?php esc_html_e('What Features Do You Need?', 'pretty-link'); ?></h2>
<p class="prli-wizard-step-description"><?php esc_html_e('Pretty Links is packed with powerful link management and optimization features. Here are a few you can enable right from the start.', 'pretty-link'); ?></p>
<div class="prli-wizard-features">
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Link Tracking', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Monitor clicks, engagement, conversions, and more to make data-driven decisions and boost your marketing strategies.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-link-tracking" <?php checked(in_array('pretty-link-link-tracking', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('No Follow', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Protect your privacy by requesting search engine crawlers don\'t track your links. Please note: it’s up to the search engine to obey this request.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-no-follow" <?php checked(in_array('pretty-link-no-follow', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Sponsored', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Build trust with your audience and maintain transparency by adding a “sponsored” attribute to your affiliate links.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-sponsored" <?php checked(in_array('pretty-link-sponsored', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('QR Codes', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Create QR codes in seconds to engage your audience, boost brand visibility, and unlock new marketing opportunities.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-qr-codes" <?php checked(in_array('pretty-link-qr-codes', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Link Health', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Instantly receive notifications about broken links, so you can take immediate action, enhance user experience, and maintain a healthy website.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-link-health" <?php checked(in_array('pretty-link-link-health', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Replacements', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Seamlessly link keywords and phrases throughout your content, driving more traffic to relevant pages and increasing your click-through rate.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-replacements" <?php checked(in_array('pretty-link-replacements', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Import/Export Links', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Effortlessly import and export your pretty links with a simple click, streamlining your link management and saving valuable time and effort.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
      <input type="checkbox" class="prli-wizard-feature-input" value="pretty-link-import-export" <?php checked(in_array('pretty-link-import-export', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('Product Displays', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Transform plain ol’ text links into attention-grabbing showcases complete with captivating visuals, engaging descriptions, and irresistible CTAs.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
    <?php if($features_addons_selectable['pretty-link-product-displays']): ?>
      <input type="checkbox" class="prli-wizard-feature-input prli-wizard-plugin" value="pretty-link-product-displays" <?php checked(in_array('pretty-link-product-displays', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    <?php else: ?>
      <input type="hidden" class="prli-wizard-feature-input-active" value="pretty-link-product-displays">
      <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-disabled.svg'); ?>" alt="">
    <?php endif; ?>
    </div>
  </div>
  <div class="prli-wizard-feature">
    <div>
      <h3><?php esc_html_e('MonsterInsights', 'pretty-link'); ?></h3>
      <p><?php esc_html_e('Collect comprehensive data through Google Analytics, gaining deeper insights into link performance, user behavior, conversions, and more.', 'pretty-link'); ?></p>
    </div>
    <div class="prli-wizard-feature-right">
    <?php if($features_addons_selectable['monsterinsights']): ?>
      <input type="checkbox" class="prli-wizard-feature-input prli-wizard-plugin" value="monsterinsights" <?php checked(in_array('monsterinsights', $features, true)); ?>>
      <img class="prli-wizard-feature-checked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-checked.svg'); ?>" alt="">
      <img class="prli-wizard-feature-unchecked" src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-unchecked.svg'); ?>" alt="">
    <?php else: ?>
      <input type="hidden" class="prli-wizard-feature-input-active" value="monsterinsights">
      <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/checkbox-disabled.svg'); ?>" alt="">
    <?php endif; ?>
    </div>
  </div>
</div>
<p class="prli-wizard-plugins-to-install">
  <?php
    printf(
    __('If your subscription level allows, the following plugins will be installed automatically: %s', 'pretty-link'),
      '<span></span> <br /><br /><strong>Want a feature your membership level doesn’t support? No worries! You’ll get the chance to upgrade later in the onboarding wizard.</strong>'
    );
  ?>
</p>
