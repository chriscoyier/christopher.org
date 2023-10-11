jQuery(function ($) {
  var $license_container = $('#prli-license-container'),
    activating = false,
    license_error = function (message) {
      $license_container.prepend(
        $('<div class="notice notice-error">').append(
          $('<p>').html(message)
        )
      );
    };

  $('body').on('click', '#prli-activate-license-key', function () {
    var $button = $(this),
      button_width = $button.width(),
      button_html = $button.html(),
      key = $('#prli-license-key').val();

    if(activating || !key) {
      return;
    }

    activating = true;
    $button.width(button_width).html(PrliActivateL10n.loading_image);
    $license_container.find('> .notice').remove();

    $.ajax({
      url: PrliActivateL10n.ajax_url,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'prli_activate_license',
        _ajax_nonce: PrliActivateL10n.activate_license_nonce,
        key: key
      }
    })
    .done(function (response) {
      if(!response || typeof response != 'object' || typeof response.success != 'boolean') {
        license_error(PrliActivateL10n.activation_error.replace('%s', PrliActivateL10n.invalid_response));
      }
      else if(!response.success) {
        license_error(response.data);
      }
      else {
        if(response.data === true) {
          window.location.reload();
        }
        else {
          $license_container.html(response.data);
        }
      }
    })
    .fail(function () {
      license_error(PrliActivateL10n.activation_error.replace('%s', PrliActivateL10n.ajax_error));
    })
    .always(function () {
      activating = false;
      $button.html(button_html).width('auto');
    });
  });

  $('body').on('keypress', '#prli-license-key', function (e) {
    if(e.which === 13) {
      e.preventDefault();
      $('#prli-activate-license-key').trigger('click');
    }
  });

  var deactivating = false;

  $('body').on('click', '#prli-deactivate-license-key', function () {
    var $button = $(this),
      button_width = $button.width(),
      button_html = $button.html();

    if(deactivating || !confirm(PrliActivateL10n.deactivate_confirm)) {
      return;
    }

    deactivating = true;
    $button.width(button_width).html(PrliActivateL10n.loading_image);
    $license_container.find('> .notice').remove();

    $.ajax({
      url: PrliActivateL10n.ajax_url,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'prli_deactivate_license',
        _ajax_nonce: PrliActivateL10n.deactivate_license_nonce
      }
    })
    .done(function (response) {
      if(!response || typeof response != 'object' || typeof response.success != 'boolean') {
        license_error(PrliActivateL10n.deactivation_error.replace('%s', PrliActivateL10n.invalid_response));
      }
      else if(!response.success) {
        license_error(response.data);
      }
      else {
        $license_container.html(response.data);
      }
    })
    .fail(function () {
      license_error(PrliActivateL10n.deactivation_error.replace('%s', PrliActivateL10n.ajax_error));
    })
    .always(function () {
      deactivating = false;
      $button.html(button_html).width('auto');
    });
  });

  $('body').on('click', '#plp_edge_updates', function(e) {
    e.preventDefault();
    var wpnonce = $(this).attr('data-nonce');

    $('#plp_edge_updates-wrap .prli_loader').show();
    $(this).prop('disabled',true);

    var data = {
      action: 'plp_edge_updates',
      edge: $(this).is(':checked'),
      wpnonce: wpnonce
    };

    var bigthis = this;

    $.post(PrliActivateL10n.ajax_url, data, function(obj) {
      $('#plp_edge_updates-wrap .prli_loader').hide();
      $(bigthis).prop('disabled',false);

      if('error' in obj)
        alert(obj.error);
      else {
        $(bigthis).prop('checked',(obj.state=='true'));
      }
    }, 'json');
  });

  $('body').on('click', '#prli-install-license-edition', function (e) {
    e.preventDefault();

    $('#prli-install-license-edition-loading').css('display', 'inline-block');

    $.ajax({
      url: PrliActivateL10n.ajax_url,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'prli_install_license_edition',
        _ajax_nonce: PrliActivateL10n.install_license_edition_nonce
      }
    })
    .done(function (response) {
      if(response && typeof response.success === 'boolean') {
        alert(response.data);

        if(response.success) {
          window.location.reload();
        }
      }
      else {
        alert(PrliActivateL10n.error_installing_license_edition);
      }
    })
    .fail(function () {
      alert(PrliActivateL10n.error_installing_license_edition);
    })
    .always(function () {
      $('#prli-install-license-edition-loading').hide();
    });
  });

  $('body').on('click', '#prli-activate-new-license', function (e) {
    e.preventDefault();

    var license_key = $(this).data('license-key');

    setTimeout(function () {
      $('#prli-license-key').val(license_key);
      $('#prli-activate-license-key').trigger('click');
    }, 250);
  });
});
