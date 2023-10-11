jQuery(document).ready(function($) {
  $('.prli-color-picker').wpColorPicker();

  $('.prlipro-chip').on('click', function() {
    $(this).toggleClass('selected');
    var $checkbox = $(this).find('input[type="checkbox"]');
    $checkbox.attr('checked', !$checkbox.attr('checked'));
  });
});

