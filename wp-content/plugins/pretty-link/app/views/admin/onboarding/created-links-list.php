<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div id="prli-wizard-links-list-container">
  <?php
    $total_links = count($pretty_links);
    $links_per_page = 15;
    $total_pages = ceil($total_links / $links_per_page);

    $offset = ($current_page - 1) * $links_per_page;

    $paged_links = array_slice($pretty_links, $offset, $links_per_page);
  ?>

  <?php if(count($paged_links)): ?>
    <div id="prli-wizard-selected-content" class="prli-wizard-created-links">
      <div>
        <h2 class="prli-wizard-step-title"><?php esc_html_e('Your Pretty Links', 'pretty-link'); ?></h2>
      </div>

      <?php foreach($paged_links as $link): ?>
        <div class="prli-wizard-selected-content" id="prli-wizard-selected-content-<?php echo esc_attr($link->link_cpt_id); ?>">
          <div>
            <div class="prli-wizard-selected-content-heading"><?php esc_html_e('Link Name', 'pretty-link'); ?></div>
            <div class="prli-wizard-selected-content-name"><?php echo esc_html($link->name); ?></div>
          </div>
          <div>
            <div class="prli-wizard-selected-content-expand-menu" data-id="prli-wizard-selected-content-menu-<?php echo esc_attr($link->link_cpt_id); ?>">
              <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/onboarding/expand-menu.svg'); ?>">
            </div>
            <div id="prli-wizard-selected-content-menu-<?php echo esc_attr($link->link_cpt_id); ?>" class="prli-wizard-selected-content-menu prli-hidden">
              <div class="prli-wizard-selected-content-delete" data-link-id="<?php echo esc_attr($link->link_cpt_id); ?>"><?php esc_html_e('Remove', 'pretty-link'); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="prli-wizard-links-pagination">
      <?php if($total_pages > 1): ?>
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <?php $active_class = ($i === $current_page) ? 'active' : ''; ?>

          <a href="#" class="prli-wizard-links-pagination-page <?php echo $active_class; ?>" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>