jQuery(function ($) {
  $('body').on('click', '.prli-notice-dismiss-permanently button.notice-dismiss', function () {
    $.ajax({
      url: PrliAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'prli_dismiss_notice',
        _ajax_nonce: PrliAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    })
  });

  $('body').on('click', '.prli-notice-dismiss-daily button.notice-dismiss', function() {
    $.ajax({
      url: PrliAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'prli_dismiss_daily_notice',
        _ajax_nonce: PrliAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    });
  });

  $('body').on('click', '.prli-notice-dismiss-monthly button.notice-dismiss', function (e) {
    $.ajax({
      url: PrliAdminGlobal.ajax_url,
      method: 'POST',
      data: {
        action: 'prli_dismiss_monthly_notice',
        _ajax_nonce: PrliAdminGlobal.dismiss_notice_nonce,
        notice: $(this).closest('.notice').data('notice')
      }
    });
  });
});
