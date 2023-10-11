var PrliOnboarding = (function($) {
  var onboarding;
  var working = false;
  var selected_content = null;
  var upgrade_wait_started;

  onboarding = {
    init: function () {
      if(!PrliOnboardingL10n.step) {
        return; // Skip JS on Welcome page
      }

      if(PrliOnboardingL10n.step > 1) {
        onboarding.go_to_step(PrliOnboardingL10n.step);
      }


      $('body').on('click','.prli-wizard-onboarding-video-collapse', function (e) {
        e.preventDefault();
        $('#inner_' + $(this).data('id')).hide();
        $('#wrapper_' + $(this).data('id')).removeClass('active');
        $('#expand_' + $(this).data('id')).show();
      });

      $('body').on('click','.prli-wizard-onboarding-video-expand', function (e) {
        e.preventDefault();
        $(this).hide();
        $('#wrapper_' + $(this).data('id')).show();
        $('#wrapper_' + $(this).data('id')).addClass('active');
        $('#inner_' + $(this).data('id')).show();
        $('#prli_play_' + $(this).data('id')).trigger('click');
      });

      $('body').on('click','.prli-video-play-button', function (e) {
        e.preventDefault();
        var prliPlayBtn = $(this);
        onboarding.load_video(prliPlayBtn, 1);
      });

      $('.prli-wizard-go-to-step').on('click', function () {
        var current_step = PrliOnboardingL10n.step;
        var context = $(this).data('context');
        onboarding.go_to_step($(this).data('step'));

        if(current_step == 3 || current_step == 4){
          if(context == 'skip'){
            $('.prli_onboarding_step_3').addClass('prli-wizard-current-step-skipped');
            $('.prli_onboarding_step_4').addClass('prli-wizard-current-step-skipped');
            $.ajax({
              method: 'POST',
              url: PrliOnboardingL10n.ajax_url,
              dataType: 'json',
              data: {
                action: 'prli_onboarding_mark_content_steps_skipped',
                _ajax_nonce: PrliOnboardingL10n.mark_content_steps_skipped_nonce,
                data: JSON.stringify({})
              }
            });
            return;
          }else{
            $('.prli_onboarding_step_3').removeClass('prli-wizard-current-step-skipped');
            $('.prli_onboarding_step_4').removeClass('prli-wizard-current-step-skipped');
          }
        }

        onboarding.mark_steps_complete(current_step);

      });


      $(window).on('resize', function(){

         if( $( window ).width() > 1440 ){
            $('.prli-wizard-onboarding-video-expand').each(function(){
              var _this = $(this);
              var obj_id = $(this).data('id');
              $('#expand_' + obj_id).trigger('click');
            });
         }
      });

      $(window).trigger('resize');

      $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;

        if(state && state.step) {
          onboarding.display_step(state.step);
        }
      });

      $('#prli-wizard-activate-license-key').on('click', onboarding.activate_license_key);

      $('.prli-wizard-feature').on('click', function () {
        onboarding.toggle_feature($(this));
      });

      onboarding.show_features_to_install();

      $('#prli-wizard-save-features').on('click', onboarding.save_features);

      $('body').on('click', 'a.prli-wizard-remove-selected-link', function(e) {
        e.preventDefault();
        $(this).closest('li').remove();
      })

      onboarding.setup_popups();

      $('body').on('click', '#prli-wizard-create-new-link-save', onboarding.create_new_link);
      $('body').on('click', '#prli-wizard-import-links-save', onboarding.import_links);

      $('.prli-wizard-selected-content-expand-menu').on('click', function (e) {
        e.stopPropagation();
        var element_id = $(this).data('id');
        $('#'+element_id).show();

        $(document.body).one('click', function () {
          $('#' + element_id).hide();
        });
      });

      $('#prli-wizard-selected-category-delete').on('click', function () {
        $('#prli-wizard-selected-category').hide();
        $('#prli-wizard-category-nav-continue').hide();
        $('#prli-wizard-category-nav-skip, #prli-wizard-create-select-category').show();

        var data = {
          category_id: PrliOnboardingL10n.category_id
        };

        $.ajax({
          method: 'POST',
          url: PrliOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'prli_onboarding_unset_category',
            _ajax_nonce: PrliOnboardingL10n.unset_category_nonce,
            data: JSON.stringify(data)
          }
        });
      });

      $('#prli-wizard-create-category-links').on('keyup', onboarding.debounce(onboarding.search_links, 250));

      $('#prli-wizard-choose-link-save').on('click', onboarding.select_existing_content);

      $('#prli-wizard-create-new-category-save').on('click', onboarding.create_new_category);

      if( PrliOnboardingL10n.step == 3 ) {
        if( PrliOnboardingL10n.content_id > 0 && PrliOnboardingL10n.has_imported_links == 0 ) {
          onboarding.select_existing_content();
        }

        if(PrliOnboardingL10n.has_imported_links == 1) {
          $('#prli-wizard-link-nav-skip').hide();
          $('#prli-wizard-link-nav-continue').show();
        }
      }

      $(document.body).on('click', '#prli-deactivate-license-key', onboarding.deactivate_license);

      if( PrliOnboardingL10n.step == 1 ){
          $('#prli-wizard-license-wrapper').removeClass('prli-hidden');
      }

      if( PrliOnboardingL10n.step == 4 ){
        if( PrliOnboardingL10n.category_id > 0 ){
          onboarding.fillin_category_data();
        }else{
          $('#prli-wizard-create-select-category').show();
        }
      }

      $('#prli-wizard-finish-onboarding').on('click', onboarding.finish);
    },

    load_video: function (o_this) {
      var video_id = o_this.data('id');

      if(o_this.hasClass('iframe_loaded')){
        return;
      }
      var video_holder_id = o_this.data('holder-id');
      var video_hash = o_this.data('hash');
      var iframe_id = 'prli_iframe' + video_hash;

      $('#'+ video_holder_id).html('<iframe id="'+iframe_id+'" width="100%" height="100%" src="https://www.youtube.com/embed/'+video_id+'?rel=0&autoplay=0&mute=1&enablejsapi=1" frameborder="0" allowfullscreen></iframe>')
      o_this.addClass('iframe_loaded');
    },

    mark_steps_complete: function (current_step) {
      $.ajax({
          method: 'POST',
          url: PrliOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'prli_onboarding_mark_steps_complete',
            _ajax_nonce: PrliOnboardingL10n.mark_steps_complete_nonce,
             data: JSON.stringify({step:current_step})
           }
      });
    },

    toggle_feature: function ($feature) {
      var $checkbox = $feature.find('input[type="checkbox"]');

      $checkbox.prop('checked', !$checkbox.prop('checked')).triggerHandler('change');
      onboarding.show_features_to_install();
    },

    show_features_to_install: function () {
      var plugins_to_install = [];
      var $plugins_to_install = $('.prli-wizard-plugins-to-install');

      $('.prli-wizard-plugin:checked').each(function () {
        var value = $(this).val();

        if(value && PrliOnboardingL10n.features.addons[value]) {
          plugins_to_install.push(PrliOnboardingL10n.features.addons[value]);
        }
      });

      $plugins_to_install.find('span').text(plugins_to_install.join(', '));
      $plugins_to_install[plugins_to_install.length ? 'show' : 'hide']();
    },

    save_features: function () {
      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');

      var features = [];

      $('.prli-wizard-feature-input:checked').each(function () {
        var value = $(this).val();

        if(value && (PrliOnboardingL10n.features.features[value] || PrliOnboardingL10n.features.addons[value])) {
          features.push(value);
        }
      });

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_save_features',
          _ajax_nonce: PrliOnboardingL10n.save_features_nonce,
          data: JSON.stringify(features)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            onboarding.go_to_step(3);
          }
          else {
            onboarding.save_features_error(response.data);
          }
        }
        else {
          onboarding.save_features_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.save_features_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },

    save_features_error: function (message) {
      alert(message || PrliOnboardingL10n.an_error_occurred);
    },

    go_to_step: function (step) {
      PrliOnboardingL10n.step = step;
      onboarding.display_step(step);

      var url = new URL(window.location);
      url.searchParams.set('step', step);
      window.history.pushState({ step: step }, '', url);

      if( step == 3 ) {
        onboarding.load_link_step_content();
      }

      if( step == 4 ){
        if( PrliOnboardingL10n.category_id > 0 ){
          onboarding.fillin_category_data();
        }else{
          $('#prli-wizard-create-select-category').show();
        }
      }

      if( step == 5 ){
        onboarding.load_finish_step();
      }

      if( step == 6 ){
        onboarding.load_complete_step();
      }

      if($('.prli-wizard-onboarding-video-'+step).length){
        var prliPlayBtn =  $('.prli-video-play-button', $('.prli-wizard-onboarding-video-'+step) );
        onboarding.load_video(prliPlayBtn);
      }
    },

    load_finish_step: function () {
      var edition = PrliOnboardingL10n.edition_url_param;
      var license = PrliOnboardingL10n.license_url_param;

      if(upgrade_wait_started && (Date.now() - upgrade_wait_started > 45000)) {
        edition = null;
      }

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_load_finish_step',
          _ajax_nonce: PrliOnboardingL10n.load_finish_step,
          data: JSON.stringify({
            step: 6,
            edition: edition,
            license: license
          })
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#prli-wizard-finish-step-container').html(response.data.html);

            if($('#prli-upgrade-wait-edition').length) {
              if(!upgrade_wait_started) {
                upgrade_wait_started = Date.now();
              }

              setTimeout(function () {
                onboarding.load_finish_step();
              }, 10000);

              return;
            }

            if($('#prli-finishing-setup-redirect').length) {
              setTimeout(function(){
                onboarding.mark_steps_complete(5);
                onboarding.go_to_step(6);
              }, 1500);
            }

            if($('#prli_wizard_finalize_setup').length) {
              if($('#prli_wizard_install_correct_edition').length) {
                onboarding.install_correct_edition();
              } else {
                if($('#start_addon_slug_installable').length) {
                  onboarding.install_addons($('#start_addon_slug_installable').val());
                }
                else {
                  $('#prli-wizard-finish-step-container').find('.prli-wizard-step-description').hide();
                }
              }
            }
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    load_complete_step: function () {
      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_load_complete_step',
          _ajax_nonce: PrliOnboardingL10n.load_complete_step,
           data: JSON.stringify({step:6})
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            var completed_step_urls = response.data.html;
            $('#prli-wizard-content-section').html(completed_step_urls);
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    display_step: function (step) {
      $('.prli-wizard-step').hide();
      $('.prli-wizard-step-' + step).show();
      $('.prli-wizard-nav-step').hide();
      $('.prli-wizard-nav-step-' + step).css('display', 'flex');
    },

    setup_popups: function () {
      if(!$.magnificPopup) {
        return;
      }

      $('#prli-wizard-create-new-category').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'prli-wizard-mfp',
          closeOnBgClick: false,
          items: {
            src: '#prli-wizard-create-new-category-popup',
            type: 'inline'
          }
        });
      });

      $('#prli-wizard-choose-content').on('click', function () {
        $.magnificPopup.open({
          mainClass: 'prli-wizard-mfp',
          items: {
            src: '#prli-wizard-choose-link-popup',
            type: 'inline'
          }
        });
      });
    },

    load_link_step_content: function() {
      var search_params = new URLSearchParams(window.location.href);

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_load_link_step_content',
          _ajax_nonce: PrliOnboardingL10n.load_link_step_content,
          data: JSON.stringify({step:3, link_page: search_params.get('link_page')})
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#prli-wizard-link-step-container').html(response.data.html);

            onboarding.register_link_step_listeners();

            if( PrliOnboardingL10n.content_id > 0 && PrliOnboardingL10n.has_imported_links == 0 ) {
              onboarding.select_existing_content();
            }

            if(PrliOnboardingL10n.has_imported_links == 1) {
              if(PrliOnboardingL10n.link_count > 0) {
                $('#prli-wizard-link-nav-skip').hide();
                $('#prli-wizard-link-nav-continue').show();
              } else {
                $('#prli-wizard-link-nav-skip').show();
                $('#prli-wizard-link-nav-continue').hide();
              }
            }
          }
        }
      })
      .fail(function () {

      })
      .always(function () {

      });
    },

    register_link_step_listeners: function() {
      // Unbind previous event listeners.
      $('#prli-wizard-create-new-link').off('click');
      $('#prli-wizard-import-links').off('click');
      $('.prli-wizard-selected-content-expand-menu').off('click');
      $('.prli-wizard-selected-content-delete').off('click');
      $('.prli-wizard-links-pagination-page').off('click');

      // Register new event listeners.
      $('#prli-wizard-create-new-link').on('click', function () {
        var o_this = $(this);
        o_this.attr('disabled','disabled');
        $.ajax({
          method: 'POST',
          url: PrliOnboardingL10n.ajax_url,
          dataType: 'json',
          data: {
            action: 'prli_onboarding_load_create_new_content',
            _ajax_nonce: PrliOnboardingL10n.load_create_new_content,
            data: JSON.stringify({step:3})
          }
        })
        .done(function (response) {
          o_this.removeAttr('disabled');
          if(response && typeof response.success === 'boolean') {
            if(response.success) {
              $('#prli-wizard-create-new-link-popup').html(response.data.html);
              $.magnificPopup.open({
                mainClass: 'prli-wizard-mfp',
                items: {
                  src: '#prli-wizard-create-new-link-popup',
                  type: 'inline'
                }
              });
            }
          }
        })
        .fail(function () {
          o_this.removeAttr('disabled');
        })
        .always(function () {
          o_this.removeAttr('disabled');
        });
      });

      $('#prli-wizard-import-links').on('click', function() {
        $.magnificPopup.open({
          mainClass: 'prli-wizard-mfp',
          closeOnBgClick: false,
          items: {
            src: '#prli-wizard-import-links-popup',
            type: 'inline'
          },
          callbacks: {
            close: function() {
              if(!PrliOnboardingL10n.is_pro_user) {
                return;
              }

              if(PrliOnboardingL10n.has_imported_links == 0) {
                window.location.reload();
              } else {
                onboarding.re_render_links_list();
              }
            }
          }
        });
      });

      $('.prli-wizard-selected-content-expand-menu').on('click', function (e) {
        e.stopPropagation();
        var element_id = $(this).data('id');
        $('#'+element_id).show();

        $(document.body).one('click', function () {
          $('#' + element_id).hide();
        });
      });

      $('.prli-wizard-selected-content-delete').on('click', onboarding.select_content_remove);

      $('.prli-wizard-selected-content-delete').on('click', function () {
        selected_content = null;

        if(PrliOnboardingL10n.has_imported_links == 0) {
          var $selected_content = $('#prli-wizard-selected-content');
        } else {
          var $selected_content = $('#prli-wizard-selected-content-' + $(this).data('link-id'));
        }

        $selected_content.find('.prli-wizard-selected-content-heading').text('');
        $selected_content.find('.prli-wizard-selected-content-name').text('');
        $selected_content.hide();

        if(PrliOnboardingL10n.has_imported_links == 0) {
          $('#prli-wizard-link-nav-continue').hide();
          $('#prli-wizard-create-select-link, #prli-wizard-link-nav-skip').show();
        }
      });

      $('.prli-wizard-links-pagination-page').on('click', function(e) {
        e.preventDefault();
        onboarding.re_render_links_list($(this).data('page'));
      });
    },

    re_render_links_list: function(page_id = 0) {
      $('#prli-wizard-links-list-container .pl-icon-spinner').show();

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_re_render_links_list',
          _ajax_nonce: PrliOnboardingL10n.re_render_links_list,
          data: JSON.stringify({step:3, page: page_id})
        }
      })
      .done(function(response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            $('#prli-wizard-links-list-container').html(response.data.html);

            onboarding.register_link_step_listeners();

            if( PrliOnboardingL10n.content_id > 0 && PrliOnboardingL10n.has_imported_links == 0 ) {
              onboarding.select_existing_content();
            }
          }
        }
      })
    },

    clear_import_data: function() {
      // Clear any inputs from previous import.
      $('#prli-wizard-import-file').val('');
      $('#prli-wizard-import-created-count').text('0');
      $('#prli-wizard-import-updated-count').text('0');
      $('#prli-wizard-import-failed-create-count').text('0');
      $('#prli-wizard-import-failed-update-count').text('0');
      $('#prli-wizard-import-failed-rows').empty();

      $('#prli-wizard-import-links-popup-info').hide();
      $('#prli-wizard-import-failed-create').hide();
      $('#prli-wizard-import-failed-update').hide();
      $('#prli-wizard-import-failed-rows-container').hide();
    },

    create_new_link: function () {
      $('#prli-wizard-create-new-link-popup').find('.prli-wizard-popup-field-error').removeClass('prli-wizard-popup-field-error');

      var $target_url = $('#prli-wizard-create-link-target-url');
      var $slug = $('#prli-wizard-create-link-pretty-link');
      var $redirection = $('#prli-wizard-create-link-redirection');

      var data = {
        target_url: $target_url.val(),
        slug: $slug.val(),
        redirection: $redirection.val()
      };

      if(!data.target_url) {
        $target_url.closest('.prli-wizard-popup-field').addClass('prli-wizard-popup-field-error');
        return;
      }

      if(!data.slug) {
        $slug.closest('.prli-wizard-popup-field').addClass('prli-wizard-popup-field-error');
        return;
      }

      if(!data.redirection) {
        $redirection.closest('.prli-wizard-popup-field').addClass('prli-wizard-popup-field-error');
        return;
      }

      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_save_new_link',
          _ajax_nonce: PrliOnboardingL10n.save_new_link_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.link;

            $('#prli-wizard-link-nav-continue').show();

            if(PrliOnboardingL10n.has_imported_links == 1) {
              $('#prli-wizard-link-nav-skip').hide();

              onboarding.re_render_links_list();
            } else {
              $('#prli-wizard-create-select-link, #prli-wizard-link-nav-skip').hide();

              var $selected_content = $('#prli-wizard-selected-content');
              $selected_content.find('.prli-wizard-selected-content-heading').text(response.data.heading);
              $selected_content.find('.prli-wizard-selected-content-name').text(response.data.link.name);
              $selected_content.show();
            }

            if($.magnificPopup) {
              $.magnificPopup.close();
            }

            PrliOnboardingL10n.content_id = response.data.link.link_cpt_id;
          }
          else {
            onboarding.wizard_prli_ajax_error(response.data);
          }
        }
        else {
          onboarding.wizard_prli_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_prli_ajax_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },

    import_links: function() {
      // Make sure we aren't running this when there's no file uploaded.
      if($('#prli-wizard-import-file').val() === '') {
        return;
      }

      var form_data = new FormData();
      var file_input = $('#prli-wizard-import-file')[0];
      var $import_info = $('#prli-wizard-import-links-popup-info');

      form_data.append('importedfile', file_input.files[0]);
      form_data.append('action', 'prli_onboarding_import_links');
      form_data.append('_ajax_nonce', PrliOnboardingL10n.import_links_nonce);

      if(working) {
        return;
      }

      working = true;

      onboarding.clear_import_data();

      $import_info.show();

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        contentType: false,
        data: form_data,
        processData: false
      })
      .done(function(response) {
        var res_data = response.data;

        $('#prli-wizard-link-nav-skip').hide();
        $('#prli-wizard-link-nav-continue').show();

        $('#prli-wizard-import-created-count').text(res_data.successful_create_count);
        $('#prli-wizard-import-update-count').text(res_data.successful_update_count);

        if(res_data.had_errors) {
          $('#prli-wizard-import-failed-rows-container').show();

          if(res_data.creation_errors.length > 0) {
            $('#prli-wizard-import-failed-create').show();
            $('#prli-wizard-import-failed-create-count').text(res_data.creation_errors.length);

            $.each(res_data.creation_errors, function(_, error) {
              $('#prli-wizard-import-failed-rows').append('Link: ' + error.slug + ' (' + error.errors[0] + ')' + '\n');
            });
          }

          if(res_data.update_errors.length > 0) {
            $('#prli-wizard-import-failed-update').show();
            $('#prli-wizard-import-failed-update-count').text(res_data.update_errors.length);

            $.each(res_data.update_errors, function(_, error) {
              $('#prli-wizard-import-failed-rows').append('Link: ' + error.slug + ' (' + error.errors[0] + ')' + '\n');
            });
          }
        }
      })
      .fail(function () {
        onboarding.wizard_prli_ajax_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },

    create_new_category: function () {
      $('#prli-wizard-create-new-category-popup').find('.prli-wizard-popup-field-error').removeClass('prli-wizard-popup-field-error');

      var $name = $('#prli-wizard-create-category-name');
      var $links = $('#prli-wizard-selected-links li');
      var link_ids = [];

      if($links && $links.length > 0) {
        $links.each(function() {
          link_ids.push($(this).data('id'));
        });
      }

      var data = {
        name: $name.val(),
        link_ids: link_ids
      };

      if(!data.name) {
        $name.closest('.prli-wizard-popup-field').addClass('prli-wizard-popup-field-error');
        return;
      }

      if(working) {
        return;
      }

      working = true;

      var $button = $(this),
        button_html = $button.html(),
        button_width = $button.width();

      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_save_new_category',
          _ajax_nonce: PrliOnboardingL10n.save_new_category_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.term;
            PrliOnboardingL10n.category_id = response.data.term.term_id;

            $('#prli-wizard-create-select-category, #prli-wizard-category-nav-skip').hide();
            $('#prli-wizard-category-nav-continue').show();

            var $selected_content = $('#prli-wizard-selected-category');
            $selected_content.find('#prli-selected-category-name').text(response.data.term.name);
            $selected_content.find('#prli-selected-category-slug').text(response.data.term.slug);
            $selected_content.find('#prli-selected-category-count').text(response.data.term.count);
            $selected_content.show();

            $('#prli-wizard-create-category-name').val('');
            $('#prli-wizard-selected-links').empty();

            if($.magnificPopup) {
              $.magnificPopup.close();
            }
          }
          else {
            onboarding.wizard_prli_ajax_error(response.data);
          }
        }
        else {
          onboarding.wizard_prli_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_prli_ajax_error('Request failed');
      })
      .always(function () {
        $button.html(button_html).width('auto');
        working = false;
      });
    },


    fillin_category_data: function () {

      var data = {
        category_id: PrliOnboardingL10n.category_id,
      };

      if(!data.category_id) {
        return;
      }

      if(working) {
        return;
      }

      working = true;

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_get_category',
          _ajax_nonce: PrliOnboardingL10n.get_category_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            selected_content = response.data.term;

            $('#prli-wizard-create-select-category, #prli-wizard-category-nav-skip').hide();
            $('#prli-wizard-category-nav-continue').show();

            var $selected_content = $('#prli-wizard-selected-category');
            $selected_content.find('#prli-selected-category-name').text(response.data.term.name);
            $selected_content.find('#prli-selected-category-slug').text(response.data.term.slug);
            $selected_content.find('#prli-selected-category-count').text(response.data.term.count);
            $selected_content.show();
          }
        }
      })
      .fail(function () {
        onboarding.wizard_prli_ajax_error('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    install_correct_edition: function () {
      if(working) {
        return;
      }

      working = true;

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_install_correct_edition',
          _ajax_nonce: PrliOnboardingL10n.install_correct_edition,
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            window.location.reload();
          } else {
            alert(response.data);
          }
        } else {
          onboarding.wizard_prli_ajax_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.wizard_prli_ajax_error('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    install_addons: function (addon_slug) {
      var data = {
        addon_slug: addon_slug,
      };

      if(!data.addon_slug) {
        return;
      }

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_install_addons',
          _ajax_nonce: PrliOnboardingL10n.install_addons,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if(response && typeof response.success === 'boolean') {
          if(response.success) {
            var _addon_slug = response.data.addon_slug;
            var status = response.data.status;
            var message = response.data.message;
            var o_div = jQuery('#prli-finish-step-addon-' + _addon_slug);
            var o_spinner = jQuery('#prli-wizard-finish-step-' + _addon_slug);

            if(o_div.length && 1 === status) {
              o_div.find('.prli-wizard-feature-activatedx').addClass('prli-wizard-feature-activated');
              o_spinner.hide();
            }

            if(0 === status) {
              o_spinner.hide();
              o_div.find('.prli-wizard-addon-text').addClass('error').html(message);
            }

            var next_addon = response.data.next_addon;

            if(next_addon !== '') {
              onboarding.install_addons(next_addon);
            }
            else {
              setTimeout(function(){
                onboarding.mark_steps_complete(5);
                onboarding.go_to_step(6);
              }, 1500);
            }
          }
          else {
            onboarding.install_addons_error(typeof response.data === 'string' ? response.data : null);
          }
        } else {
          onboarding.install_addons_error('Invalid response');
        }
      })
      .fail(function () {
        onboarding.install_addons_error('Request failed');
      });
    },

    install_addons_error: function (message) {
      $('#prli-wizard-finish-step-container .prli-wizard-step-description').text(PrliOnboardingL10n.error_installing_addon);
      $('#prli-wizard-finish-step-container .animate-spin').hide();
      onboarding.wizard_prli_ajax_error(message);
    },

    wizard_prli_ajax_error: function (message) {
      alert(message || PrliOnboardingL10n.an_error_occurred);
    },

    debounce: function (func, wait, immediate) {
      var timeout;

      return function() {
        var context = this,
          args = arguments;

        var later = function() {
          timeout = null;

          if (!immediate) {
            func.apply(context, args);
          }
        };

        var callNow = immediate && !timeout;

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);

        if (callNow) {
          func.apply(context, args);
        }
      };
    },

    search_links: function() {
      var $search = $(this);
      var $suggestions_list = $('#prli-wizard-links-suggestions-list');
      var added_suggestions = [];

      $('#prli-wizard-search-spinner').hide();

      if($search.val().length < 2) {
        $suggestions_list.hide();
        return;
      }

      $('#prli-wizard-search-spinner').show();

      $.ajax({
        method: 'GET',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_search_for_links',
          term: $search.val()
        }
      })
      .done(function(res) {
        $suggestions_list.show();
        $('#prli-wizard-search-spinner').hide();

        if(!res.length) {
          return;
        }

        $suggestions_list.empty();

        var suggestions = res.map(function(link) {
          return { title_with_slug: link.title + ' (' + link.slug + ')', title: link.title, id: link.id };
        });

        suggestions.forEach(function(suggestion) {
          if(!added_suggestions.includes(suggestion.id)) {
            $suggestions_list.append('<li class="prli-link-suggestion" tabindex="0" data-id="' + suggestion.id + '" data-title="' + suggestion.title + '">' + suggestion.title_with_slug + '</li>');
            added_suggestions.push(suggestion.id);
          }
        });

        $('.prli-link-suggestion').on('keydown', function(e) {
          if(e.keyCode === 13) {
            e.preventDefault();
            $(this).trigger('click');
          }
        });

        $('.prli-link-suggestion').on('click', function() {
          $search.val('');
          $suggestions_list.hide();

          var link_id = $(this).data('id');
          var link_title = $(this).data('title');

          $('#prli-wizard-selected-links').append(
            `<li class="prli-wizard-selected-link" data-id="${link_id}">
              ${link_title}
              <span class="prli-group-remove-link">
                <a href="" class="prli-wizard-remove-selected-link"><i class="pl-icon pl-icon-cancel-circled pl-18"></i></a>
              </span>
            </li>`);
        });
      })
    },

    select_content_remove: function () {
      if(PrliOnboardingL10n.has_imported_links == 0) {
        var $link_id = PrliOnboardingL10n.content_id;
      } else {
        var $link_id = $(this).data('link-id');
      }

      var data = {
        content_id: $link_id,
        imported_links: PrliOnboardingL10n.has_imported_links
      };

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_unset_content',
          _ajax_nonce: PrliOnboardingL10n.unset_content_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function(response) {
        if(response && typeof response.success === 'boolean') {
          if(response.data !== undefined && response.data.count !== undefined && PrliOnboardingL10n.has_imported_links) {
            if(response.data.count <= 0) {
              $('#prli-wizard-link-nav-continue').hide();
              $('#prli-wizard-create-select-link, #prli-wizard-link-nav-skip').show();
            }
          }
        }
        onboarding.re_render_links_list();
      });

      PrliOnboardingL10n.content_id = 0;
    },

    select_existing_content: function () {
      if(working){
        return;
      }

      working = true;

      var data = {
        content_id: PrliOnboardingL10n.content_id
      };

      $.ajax({
        method: 'POST',
        url: PrliOnboardingL10n.ajax_url,
        dataType: 'json',
        data: {
          action: 'prli_onboarding_set_content',
          _ajax_nonce: PrliOnboardingL10n.set_content_nonce,
          data: JSON.stringify(data)
        }
      })
      .done(function (response) {
        if($.magnificPopup) {
          $.magnificPopup.close();
        }

        if(response && typeof response.success === 'boolean') {
          $('#prli-wizard-create-select-link, #prli-wizard-link-nav-skip').hide();
          $('#prli-wizard-link-nav-continue').show();

          var $selected_content = $('#prli-wizard-selected-content');
          $selected_content.find('.prli-wizard-selected-content-heading').text(PrliOnboardingL10n.link_name);
          $selected_content.find('.prli-wizard-selected-content-name').text(response.data.link_data.name);
          $selected_content.show();
        }
      })
      .fail(function () {
        alert('Request failed');
      })
      .always(function () {
        working = false;
      });
    },

    activate_license_key: function () {
      var $button = $(this),
        button_width = $button.width(),
        button_html = $button.html(),
        key = $('#prli-wizard-license-key').val();

      if (working || !key) {
        return;
      }

      working = true;
      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');
      $('#prli-wizard-activate-license-container').find('> .notice').remove();

      $.ajax({
        url: PrliOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'prli_activate_license',
          _ajax_nonce: PrliOnboardingL10n.activate_license_nonce,
          key: key,
          onboarding: 1
        }
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          onboarding.activate_license_error('Request failed');
        } else if (!response.success) {
          onboarding.activate_license_error(response.data);
        } else if (response.data === true) {
          window.location.reload();
        } else {
          $('#prli-wizard-activate-license-container').html(response.data);
          $('#prli-wizard-license-nav-skip').addClass('prli-hidden');
          $('#prli-wizard-license-nav-continue').removeClass('prli-hidden');
        }
      })
      .fail(function () {
        onboarding.activate_license_error('Request failed');
      })
      .always(function () {
        working = false;
        $button.html(button_html).width('auto');
      });
    },

    activate_license_error: function (message) {
      $('#prli-wizard-activate-license-container').prepend(
        $('<div class="notice notice-error">').append(
          $('<p>').html(message)
        )
      );
    },

    deactivate_license: function () {
      var $button = $(this),
        button_width = $button.width(),
        button_html = $button.html();

      if (working || !confirm(PrliOnboardingL10n.deactivate_confirm)) {
        return;
      }

      working = true;
      $button.width(button_width).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');
      $('#prli-license-container').find('> .notice').remove();

      $.ajax({
        url: PrliOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'prli_deactivate_license',
          _ajax_nonce: PrliOnboardingL10n.deactivate_license_nonce
        }
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          onboarding.deactivate_license_error('Request failed');
        } else if (!response.success) {
          onboarding.deactivate_license_error(response.data);
        } else {
          window.location.reload();
        }
      })
      .fail(function () {
        onboarding.deactivate_license_error('Request failed');
      })
      .always(function () {
        working = false;
        $button.html(button_html).width('auto');
      });
    },

    deactivate_license_error: function (message) {
      $('#prli-license-container').prepend(
        $('<div class="notice notice-error">').append(
          $('<p>').html(message)
        )
      );
    },

    finish: function () {
      var $button = $(this);

      if (working) {
        return;
      }

      working = true;
      $button.width($button.width()).html('<i class="pl-icon pl-icon-spinner animate-spin"></i>');

      $.ajax({
        url: PrliOnboardingL10n.ajax_url,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'prli_onboarding_finish',
          _ajax_nonce: PrliOnboardingL10n.finish_nonce
        }
      })
      .always(function () {
        window.location = PrliOnboardingL10n.pretty_links_url;
      });
    }
  };

  $(onboarding.init);

  return onboarding;
})(jQuery);