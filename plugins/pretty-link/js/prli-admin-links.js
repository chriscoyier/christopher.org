function prli_toggle_link_options() {
  if( jQuery('#redirect_type').val() == 'metarefresh' ||
      jQuery('#redirect_type').val() == 'javascript' )
    jQuery('#prli_time_delay').show();
  else
    jQuery('#prli_time_delay').hide();

  if( jQuery('#redirect_type').val() == 'cloak' ||
      jQuery('#redirect_type').val() == 'prettybar' ||
      jQuery('#redirect_type').val() == 'metarefresh' ||
      jQuery('#redirect_type').val() == 'javascript' )
    jQuery('#prli_google_analytics').show();
  else
    jQuery('#prli_google_analytics').hide();

  if( jQuery('#redirect_type').val() == 'pixel' )
    jQuery('#prli_target_url').hide();
  else
    jQuery('#prli_target_url').show();

  if( jQuery('.prlipro-enable-split-test').prop('checked') )
    jQuery('.prlipro-split-test-goal-link').show();
  else
    jQuery('.prlipro-split-test-goal-link').hide();
}

(function($) {
  $(document).ready(function(e) {
    prli_toggle_link_options();

    $('#redirect_type').change(function() {
      prli_toggle_link_options();
    });

    $('#param_forwarding').click(function() {
      prli_toggle_link_options();
    });

    $('.prlipro-enable-split-test').click(function() {
      prli_toggle_link_options();
    });

    // tab swapping
    $('.nav-tab').click(function() {

      // tab is already active. don't do anything
      if( $(this).hasClass( 'nav-tab-active' ) )
        return false;

      $('.nav-tab-active').removeClass( 'nav-tab-active' );
      $(this).addClass( 'nav-tab-active' );

      if( $(this).attr( 'href' ) == '#options-table' ) {
        $('#options-table').show();
        $('#pro-options-table').hide();
      }
      else {
        $('#options-table').hide();
        $('#pro-options-table').show();
      }

      return false;
    });

    $(".defaultText").focus(function(srcc) {
      if ($(this).val() == $(this)[0].title) {
        $(this).removeClass("defaultTextActive");
        $(this).val('');
      }
    });

    $(".defaultText").blur(function() {
      if ($(this).val() == "")
      {
        $(this).addClass("defaultTextActive");
        $(this).val($(this)[0].title);
      }
    });

    $(".defaultText").blur();


    $('#prli_add_link_rotation').on('click', function(e) {
      e.preventDefault();
      $('#prli_link_rotations').append(PlpLink.rotation_row_html);
    });

    $('.prli_reset_pretty_link').on('click', function(e) {
      e.preventDefault();

      if(confirm(PrliLinkList.reset_str)) {
        var args = {
          'action' : 'reset_pretty_link',
          'id' : $(this).data('id'),
          'security' : PrliLinkList.reset_security
        };

        $.post( ajaxurl, args, function(data) {
          $('.clicks.column-clicks a#link_clicks_' + args.id).html('0/0');

          // show message coming from the server indicating
          // whether or not the reset was successful or not
          alert(data.message);
        }, 'json' );
      }
    });

  });
})(jQuery);
