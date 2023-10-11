jQuery(function ($) {
  var $addonsContainer = $('#prli-addons-container');

  if ($addonsContainer.length) {
    if (window.List) {
      var list = new List($addonsContainer[0], {
        valueNames: ['prli-addon-name'],
        listClass: 'prli-addons'
      });

      $('#prli-addons-search').on('keyup', function () {
        list.search($(this).val());
      })
      .on('input', function () {
        // Used to detect click on HTML5 clear button
        if ($(this).val() === '') {
          list.search('');
        }
      });
    }

    if ($.fn.matchHeight) {
      $('.prli-addon .prli-addon-details').matchHeight({
        byRow: false
      });
    }

    var icons = {
      activate: '<i class="pl-icon pl-icon-toggle-on mp-flip-horizontal" aria-hidden="true"></i>',
      deactivate: '<i class="pl-icon pl-icon-toggle-on" aria-hidden="true"></i>',
      install: '<i class="pl-icon pl-icon-cloud-download" aria-hidden="true"></i>',
      spinner: '<i class="pl-icon pl-icon-spinner animate-spin" aria-hidden="true"></i>',
    };

    $(document).on('click', '.prli-addon-action button', function () {
      var $button = $(this),
        $addon = $button.closest('.prli-addon'),
        originalButtonHtml = $button.html(),
        originalButtonWidth = $button.width(),
        type = $button.data('type'),
        action,
        statusClass,
        statusText,
        buttonHtml,
        successText;

      if ($addon.hasClass('prli-addon-status-active')) {
        action = 'prli_addon_deactivate';
        statusClass = 'prli-addon-status-inactive';
        statusText = PrliAddons.inactive;
        buttonHtml = icons.activate + PrliAddons.activate;
      } else if ($addon.hasClass('prli-addon-status-inactive')) {
        action = 'prli_addon_activate';
        statusClass = 'prli-addon-status-active';
        statusText = PrliAddons.active;
        buttonHtml = icons.deactivate + PrliAddons.deactivate;
      } else if ($addon.hasClass('prli-addon-status-download')) {
        action = 'prli_addon_install';
        statusClass = 'prli-addon-status-active';
        statusText = PrliAddons.active;
        buttonHtml = icons.deactivate + PrliAddons.deactivate;
      } else {
        return;
      }

      $button.prop('disabled', true).html(icons.spinner).addClass('prli-loading').width(originalButtonWidth);

      var data = {
        action: action,
        _ajax_nonce: PrliAddons.nonce,
        plugin: $button.data('plugin'),
        type: type
      };

      var handleError = function (message) {
        $addon.find('.prli-addon-actions').append($('<div class="prli-addon-message prli-addon-message-error">').text(message));
        $button.html(originalButtonHtml);
      };

      $.ajax({
        type: 'POST',
        url: PrliAddons.ajax_url,
        dataType: 'json',
        data: data
      })
      .done(function (response) {
        if (!response || typeof response != 'object' || typeof response.success != 'boolean') {
          handleError(type === 'plugin' ? PrliAddons.plugin_install_failed : PrliAddons.install_failed);
        } else if (!response.success) {
          if (typeof response.data == 'object' && response.data[0] && response.data[0].code) {
            handleError(type === 'plugin' ? PrliAddons.plugin_install_failed : PrliAddons.install_failed);
          } else {
            handleError(response.data);
          }
        } else {
          if (action === 'prli_addon_install') {
            $button.data('plugin', response.data.basename);
            successText = response.data.message;

            if (!response.data.activated) {
              statusClass = 'prli-addon-status-inactive';
              statusText = PrliAddons.inactive;
              buttonHtml = icons.activate + PrliAddons.activate;
            }
          } else {
            successText = response.data;
          }

          $addon.find('.prli-addon-actions').append($('<div class="prli-addon-message prli-addon-message-success">').text(successText));

          $addon.removeClass('prli-addon-status-active prli-addon-status-inactive prli-addon-status-download')
                .addClass(statusClass);

          $addon.find('.prli-addon-status-label').text(statusText);

          $button.html(buttonHtml);
        }
      })
      .fail(function () {
        handleError(type === 'plugin' ? PrliAddons.plugin_install_failed : PrliAddons.install_failed);
      })
      .always(function () {
        $button.prop('disabled', false).removeClass('prli-loading').width('auto');

        // Automatically clear add-on messages after 3 seconds
        setTimeout(function() {
          $addon.find('.prli-addon-message').remove();
        }, 3000);
      });
    });
  }
});