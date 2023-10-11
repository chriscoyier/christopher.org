<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<div class="prli-wizard">
  <div class="prli-wizard-inner">
    <div class="prli-onboarding-logo">
      <img src="<?php echo esc_url(PRLI_IMAGES_URL . '/pl-logo-horiz-RGB.svg'); ?>" alt="">
    </div>
    <div class="prli-wizard-steps">
      <?php
        $onboarding_steps_completed = PrliOnboardingHelper::get_steps_completed();
        $next_applicable_step = $onboarding_steps_completed + 1;

        foreach($steps as $key => $step) {
          printf('<div class="prli-wizard-step prli-wizard-step-%s">', $key + 1);
          echo '<div class="prli-wizard-progress-steps">';

          foreach($steps as $progress_key => $progress_step) {
            $link_step = $progress_step['step'];

            $skipped_steps = PrliOnboardingHelper::get_skipped_steps();
            $css_class = '';

            if($progress_key == $key){
               $css_class .= ' prli-wizard-current-step';
            }

            if(in_array($link_step, $skipped_steps) && $progress_key != $key){
              $css_class .= ' prli-wizard-current-step-skipped';
            }

            printf(
              '<div class="prli_onboarding_step_%s prli-wizard-progress-step%s"><span></span><a href="%s">%s</a></div>',
              $link_step,
              $css_class,
              admin_url('admin.php?page=pretty-link-onboarding&step='.(int)$link_step),
              esc_html($progress_step['title'])
            );

          }

          echo '</div>';
          if(file_exists($step['content'])){
            require $step['content'];
          }
          echo '</div>';
        }
      ?>
    </div>
  </div>
  <div class="prli-wizard-nav">
    <?php
      foreach($steps as $key => $step) {
        printf(
          '<div class="prli-wizard-nav-step prli-wizard-nav-step-%1$s">',
          $key + 1
        );

        if(file_exists($step['nav'])) {
          require $step['nav'];
        }

        echo '</div>';
      }
    ?>
  </div>
</div>
