<?php

/**
 * Review admin notice.
 */
class PrliReviewNoticeController {

  public function load_hooks() {
    add_action( 'admin_notices', array( $this, 'review_notice' ) );
    add_action( 'wp_ajax_pl_dismiss_review_prompt', array( $this, 'dismiss_review_prompt' ) );
  }

  public function dismiss_review_prompt() {

    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pl_dismiss_review_prompt' ) ) {
      die('Failed');
    }

    if ( ! empty( $_POST['type'] ) ) {
      if ( 'remove' === $_POST['type'] ) {
        update_option( 'pl_review_prompt_removed', true );
        wp_send_json_success( array(
          'status' => 'removed'
        ) );
      } else if ( 'delay' === $_POST['type'] ) {
        update_option( 'pl_review_prompt_delay', array(
          'delayed_until' => time() + WEEK_IN_SECONDS
        ) );
        wp_send_json_success( array(
          'status' => 'delayed'
        ) );
      }
    }
  }

  public function review_notice() {

    // Only show to admins
    if ( ! current_user_can( 'manage_options' ) ) {
      return;
    }

    // Check for the constant to disable the prompt
    if ( defined( 'PL_DISABLE_REVIEW_PROMPT' ) && true == PL_DISABLE_REVIEW_PROMPT ) {
      return;
    }

    // Notice has been delayed
    $delayed_option = get_option( 'pl_review_prompt_delay' );
    if ( ! empty( $delayed_option['delayed_until'] ) && time() < $delayed_option['delayed_until'] ) {
      return;
    }

    // Notice has been removed
    if ( get_option( 'pl_review_prompt_removed' ) ) {
      return;
    }

    // Backwards compat
    if ( get_transient( 'pl_review_prompt_delay' ) ) {
      return;
    }

    // Don't bother if haven't been using long enough
    global $wpdb;
    $click_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}prli_clicks" );
    if ( empty( $click_count ) ) {
      return;
    }

    ?>
    <div class="notice notice-info is-dismissible prli-review-notice" id="prli_review_notice">
      <div id="prli_review_intro">
        <p><?php _e( 'Are you enjoying using Pretty Links?', 'pretty-link' ); ?></p>
        <p><a data-review-selection="yes" class="prli-review-selection" href="#">Yes, I love it</a> 🙂 | <a data-review-selection="no" class="prli-review-selection" href="#">Not really...</a></p>
      </div>
      <div id="prli_review_yes" style="display: none;">
        <p><?php _e( 'That\'s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'pretty-link' ); ?></p>
        <p style="font-weight: bold;">~ Blair Williams<br>Founder &amp; CEO of Pretty Links</p>
        <p>
          <a style="display: inline-block; margin-right: 10px;" href="https://wordpress.org/support/plugin/pretty-link/reviews/?filter=5#new-post" onclick="delayReviewPrompt(event, 'remove', true, true)" target="_blank"><?php esc_html_e( 'Okay, you deserve it', 'pretty-link' ); ?></a>
          <a style="display: inline-block; margin-right: 10px;" href="#" onclick="delayReviewPrompt(event, 'delay', true, false)"><?php esc_html_e( 'Nope, maybe later', 'pretty-link' ); ?></a>
          <a href="#" onclick="delayReviewPrompt(event, 'remove', true, false)"><?php esc_html_e( 'I already did', 'pretty-link' ); ?></a>
        </p>
      </div>
      <div id="prli_review_no" style="display: none;">
        <p><?php _e( 'We\'re sorry to hear you aren\'t enjoying Pretty Links. We would love a chance to improve. Could you take a minute and let us know what we can do better?', 'pretty-link' ); ?></p>
        <p>
          <a style="display: inline-block; margin-right: 10px;" href="https://prettylinks.com/feedback/?utm_source=plugin_admin&utm_medium=link&utm_campaign=in_plugin&utm_content=request_review" onclick="delayReviewPrompt(event, 'remove', true, true)" target="_blank"><?php esc_html_e( 'Give Feedback', 'pretty-link' ); ?></a>
          <a href="#" onclick="delayReviewPrompt(event, 'remove', true, false)"><?php esc_html_e( 'No thanks', 'pretty-link' ); ?></a>
        </p>
      </div>
    </div>
    <script>

      function delayReviewPrompt(event, type, triggerClick = true, openLink = false) {
        event.preventDefault();
        if ( triggerClick ) {
          jQuery('#prli_review_notice').fadeOut();
        }
        if ( openLink ) {
          var href = event.target.href;
          window.open(href, '_blank');
        }
        jQuery.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'pl_dismiss_review_prompt',
            nonce: "<?php echo wp_create_nonce( 'pl_dismiss_review_prompt' ) ?>",
            type: type
          },
        })
        .done(function(data) {

        });
      }

      jQuery(document).ready(function($) {
        $('.prli-review-selection').click(function(event) {
          event.preventDefault();
          var $this = $(this);
          var selection = $this.data('review-selection');
          $('#prli_review_intro').hide();
          $('#prli_review_' + selection).show();
        });
        $('body').on('click', '#prli_review_notice .notice-dismiss', function(event) {
          delayReviewPrompt(event, 'delay', false);
        });
      });
    </script>
    <?php
  }
}
