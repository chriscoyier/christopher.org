(function($) {
  $(document).ready(function() {
    function prli_objectify_form(formArray, allowed) {//serialize data function
      var returnArray = {};

      for (var i = 0; i < formArray.length; i++){
        var name = formArray[i]['name'],
            value = formArray[i]['value'];

        if(-1 !== $.inArray(name, allowed)) {
          if (name.slice(-2) === '[]') {
            if (typeof returnArray[name] === 'undefined') {
              returnArray[name] = [];
            }

            returnArray[name].push(value);
          } else {
            returnArray[name] = value;
          }
        }
      }

      return returnArray;
    }

    $('form#post').submit(function(e) {
      e.preventDefault();

      $('#pretty_link_errors').hide();
      $('.spinner').css('visibility','visible');

      var fields_to_validate = [
        'prli_url', 'redirect_type', 'slug', 'url_replacements', 'enable_expire', 'expire_type', 'expire_date',
        'expire_clicks', 'enable_expired_url', 'expired_url', 'dynamic_redirection', 'url_rotations[]',
        'target_url_weight', 'url_rotation_weights[]', 'prli_geo_url[]', 'prli_geo_countries[]', 'prli_tech_url[]',
        'prli_time_url[]', 'prli_time_start[]', 'prli_time_end[]', 'delay'
      ];

      var args = prli_objectify_form($(this).serializeArray(), fields_to_validate);
      args['id']       = PrliLinkValidation.args['id'];
      args['action']   = PrliLinkValidation.args['action'];
      args['security'] = PrliLinkValidation.args['security'];

      var form = this;

      $.post( ajaxurl, args, function(data) {
        if(data.valid) {
          $('#pretty_link_errors').hide();
          $(form).triggerHandler('submit.edit-post');

          // Trigger the correct actions and messages depending on whether we are publishing or updating a link
          if ($('#publish').attr('name') === 'save') {
            $(form).append('<input type="hidden" name="save" value="Update">');
          } else {
            $(form).append('<input type="hidden" name="publish" value="Publish">');
          }

          form.submit();
        }
        else {
          $('#pretty_link_errors p').html(data.message);
          $('#pretty_link_errors').show();

          $('.spinner').css('visibility','hidden');
        }
      }, 'json' );
    });

    if (window.adminpage === 'post-new-php' && window.typenow === 'pretty-link') {
      $('#publish').val(PrliLinkValidation.args.update);
    }

    // Disable "enter" key on the Target URL field
    $('#prli_url').on('keypress', function (e) {
      if (e.keyCode === 13) {
        e.preventDefault();
      }
    });

    if (window.ClipboardJS) {
      var $el = $('.prli-edit-link-clipboard'),
        copy_text = PrliLinkValidation.copy_text,
        copied_text = PrliLinkValidation.copied_text,
        copy_error_text = PrliLinkValidation.copy_error_text,
        clipboard = new ClipboardJS($el[0], {
          text: function () {
            return PrliLinkValidation.blogurl + PrliLinkValidation.permalink_pre_slug_uri + $('#prli_slug').val();
          }
        });

      if ($.fn.tooltipster) {
        try {
          var instance = $el.tooltipster({
            theme: 'tooltipster-borderless',
            content: copy_text,
            trigger: 'custom',
            triggerClose: {
              mouseleave: true,
              touchleave: true
            },
            triggerOpen: {
              mouseenter: true,
              touchstart: true
            }
          }).tooltipster('instance');

          clipboard
            .on('success', function(e) {
              instance
                .content(copied_text)
                .one('after', function(){
                  instance.content(copy_text);
              });
            })
            .on('error', function(e) {
              instance
                .content(copy_error_text)
                .one('after', function(){
                  instance.content(copy_text);
              });
            });
        } catch (e) {
          // With tooltipster <=3.3.0 an error will be caught here, just display a static tooltip
          $el.tooltipster('destroy').tooltipster({
            content: copy_text
          });
        }
      }
    }
  });
})(jQuery);
