jQuery(function ($) {
  var $error = $('#prli-quick-create-error'),
    $loading = $('#prli-quick-create-loading'),
    showError = function (message) {
      $error.html(
        $('<div class="notice notice-error">').append(
          $('<p>').text(message)
        )
      ).show();
    };

  $('#prli-quick-create').on('submit', function (e) {
    e.preventDefault();

    $error.hide();
    $loading.show();

    $.ajax({
      type: 'POST',
      url: PrliQuickCreate.ajaxUrl,
      data: {
        action: 'prli_quick_create',
        _ajax_nonce: PrliQuickCreate.nonce,
        url: $('#prli-quick-create-url').val(),
        slug: $('#prli-quick-create-slug').val()
      },
      dataType: 'json'
    }).done(function (response) {
      if (response && typeof response === 'object') {
        if (response.success) {
          window.location = response.data.redirect;
        } else {
          showError(response.data.message);
        }
      } else {
        showError(PrliQuickCreate.invalidServerResponse);
      }
    }).fail(function () {
      showError(PrliQuickCreate.ajaxError);
    }).always(function () {
      $loading.hide();
    });
  });
});
