jQuery(document).ready(function($) {
  $('.prli-color-picker').wpColorPicker();

  $('.prlipro-chip').on('click', function() {
    $(this).toggleClass('selected');
    var $checkbox = $(this).find('input[type="checkbox"]');
    $checkbox.attr('checked', !$checkbox.attr('checked'));
  });

  if($.fn.select2) {
    $('#prli_prettypay_default_currency').select2({
      theme: 'prli',
      width: '100%'
    });
  }

  var $configure_button = $('#prli-stripe-configure-customer-portal');

  $configure_button.on('click', function () {
    var $sub_box = $('#prli-customer-portal-sub-box'),
        show = $sub_box.is(':hidden');

    if (show) {
      $configure_button.find('i').removeClass('pl-icon-right-open').addClass('pl-icon-down-open');
      $sub_box.slideDown();
    } else {
      $configure_button.find('i').removeClass('pl-icon-down-open').addClass('pl-icon-right-open');
      $sub_box.slideUp();
    }
  });

  $('#prli-portal-customer-update, #prli-portal-subscription-cancel').on('change', function () {
    $(this).closest('td').find('.prli-portal-sub-options')[this.checked ? 'show' : 'hide']();
  });

  if (window.location.href.indexOf('&configure_customer_portal=1') !== -1) {
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href.replace('&configure_customer_portal=1', ''));
    }

    if ($configure_button.length) {
      $configure_button.find('i').removeClass('pl-icon-right-open').addClass('pl-icon-down-open');
      $('#prli-customer-portal-sub-box').show();

      setTimeout(function () {
        window.scrollTo({
          top: $configure_button.offset().top - 82,
          behavior: 'smooth'
        });
      }, 100);
    }
  }
});

