<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="prli-wizard-create-select-category">
  <h2 class="prli-wizard-step-title"><?php esc_html_e("Let's Get Organized!", 'pretty-link'); ?></h2>
  <p class="prli-wizard-step-description"><?php esc_html_e("By categorizing your links, you'll have a neat and tidy system in place, allowing you to find what you need in a snap as you continue adding more links to your site.", 'pretty-link'); ?></p>

  <div class="prli-wizard-button-group">
    <button type="button" id="prli-wizard-create-new-category" class="prli-wizard-button-blue"><?php esc_html_e('Create New Category', 'pretty-link'); ?></button>
  </div>
</div>

<div id="prli-wizard-selected-category" class="prli-hidden">
  <h2 class="prli-wizard-step-title"><?php esc_html_e('Your category', 'pretty-link'); ?></h2>
  <div class="prli-wizard-selected-content prli-wizard-selected-content-full-scape">
    <div class="prli-wizard-selected-content-column">
      <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Category Name', 'pretty-link'); ?></div>
      <div class="prli-wizard-selected-content-name" id="prli-selected-category-name"></div>
    </div>
    <hr>
    <div class="prli-wizard-selected-content-column">
      <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Category Slug', 'pretty-link'); ?></div>
      <div class="prli-wizard-selected-content-name"  id="prli-selected-category-slug"></div>
    </div>
    <hr>
    <div class="prli-wizard-selected-content-column">
      <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Count','pretty-link'); ?></div>
      <div class="prli-wizard-selected-content-name"  id="prli-selected-category-count"></div>
    </div>
      <div class="prli-wizard-selected-content-expand-menu" data-id="prli-wizard-selected-category-menu">
        <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/expand-menu.svg'); ?>" alt="">
      </div>
      <div id="prli-wizard-selected-category-menu" class="prli-wizard-selected-content-menu prli-hidden">
        <div id="prli-wizard-selected-category-delete"><?php esc_html_e('Remove', 'pretty-link'); ?></div>
      </div>
  </div>
</div>

<div id="prli-wizard-create-new-category-popup" class="prli-wizard-popup prli-wizard-popup-create-category mfp-hide">
  <form id="prli-wizard-create-new-category-form">
    <h2><?php esc_html_e('Create Category', 'pretty-link'); ?></h2>

    <div class="prli-wizard-popup-field">
      <label for="prli-wizard-create-category-name"><?php esc_html_e('Category Name', 'pretty-link'); ?></label>
      <input type="text" id="prli-wizard-create-category-name" placeholder="<?php esc_attr_e('Enter the name of your category.', 'pretty-link'); ?>">
    </div>

    <?php if(PrliOnboardingHelper::get_has_imported_links()): ?>
      <div class="prli-wizard-popup-field">
        <label for="prli-wizard-create-category-links"><?php esc_html_e('Links', 'pretty-link'); ?></label>
        <div class="prli-wizard-search-bar">
          <input type="text" id="prli-wizard-create-category-links" placeholder="<?php esc_attr_e('Search for the links you want to assign to this category.', 'pretty-link'); ?>">
          <span id="prli-wizard-search-spinner"><img src="<?php echo admin_url('images/wpspin_light.gif'); ?>"></span>
        </div>

        <ul id="prli-wizard-links-suggestions-list" class="prli-hidden"></ul>
      </div>

      <ul id="prli-wizard-selected-links"></ul>
    <?php endif; ?>

    <div class="prli-wizard-popup-button-row">
      <button type="button" id="prli-wizard-create-new-category-save" class="prli-wizard-button-blue"><?php esc_html_e('Save', 'pretty-link'); ?></button>
    </div>
  </form>
</div>