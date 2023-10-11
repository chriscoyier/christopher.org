jQuery(document).ready(function($) {
  $('table.prli-settings-table td.prli-settings-table-nav ul li').each( function() {
    var page_id = $(this).find('a').data('id');

    $(this).find('a').attr('id', 'prli-nav-'+page_id);
    $(this).find('a').attr('href', '#'+page_id);
  });

  var prliSetPage = function (hash) {
    // IF ON INITIAL PAGE
    var page = 'table.prli-settings-table td.prli-settings-table-pages .prli-page:first-child';
    var nav = 'table.prli-settings-table td.prli-settings-table-nav ul li:first-child a';

    if(!hash) { hash = window.location.hash; }

    var url = window.location.href.replace(/#.*$/,'');

    // Open correct page based on the hash
    var trypage = 'table.prli-settings-table td.prli-settings-table-pages .prli-page' + hash;
    if ((hash != '') && ($(trypage).length > 0)) {
      page = trypage;
      nav = 'table.prli-settings-table td.prli-settings-table-nav ul li a#prli-nav-' + hash.replace(/\#/,'');

      var href = url + hash;
      $( 'table.prli-settings-table' ).trigger( 'prli-settings-url', [ href, hash, url ] );

      // Don't do this for now ... it will make the page bump around when using anchors
      //window.location.href = href;
    }

    $('table.prli-settings-table td.prli-settings-table-nav ul li a').removeClass('prli-active');
    $(nav).addClass('prli-active');

    $('.prli-page').hide();
    $(page).show();

    // Auto hide the menu in mobile mode when the button is clicked
    if($(window).width() <= 782) {
      $('td.prli-settings-table-nav').hide();
    }
  };

  prliSetPage();

  $('table.prli-settings-table').on( 'click', 'td.prli-settings-table-nav ul li a', function (e) {
    e.preventDefault();
    prliSetPage($(this).attr('href'));
  });

  $('tr.prli-mobile-nav a.prli-toggle-nav').on('click', function(e) {
    e.preventDefault();
    $('td.prli-settings-table-nav').toggle();
  });

  // This is in place so the settings table doesn't get screwed up when resizing
  // up to desktop mode from mobile ... not that that would ever happen of course
  $(window).on('resize', function(e) {
    if($(this).width() > 782) {
      $('td.prli-settings-table-nav').css('display','');
    }
  });

  var prli_show_box = function(box,animate) {
    $(box).trigger('prli_show_box');
    animate ? $(box).slideDown() : $(box).show();
  };

  var prli_hide_box = function(box,animate) {
    $(box).trigger('prli_hide_box');
    animate ? $(box).slideUp() : $(box).hide();
  };

  // Toggle Box from Checkbox
  var prli_toggle_checkbox_box = function(checkbox, box, animate, reverse) {
    if ($(checkbox).is(':checked')) {
      reverse ? prli_hide_box(box,animate) : prli_show_box(box,animate);
    }
    else {
      reverse ? prli_show_box(box,animate) : prli_hide_box(box,animate);
    }
  };

  // Toggle Box from Link
  var prli_toggle_link_box = function(link, box, animate) {
    if ($(box).is(':visible')) {
      prli_hide_box(box,animate);
    }
    else {
      prli_show_box(box,animate);
    }
  };

  // Toggle Box from Link
  var prli_toggle_select_box = function(select, boxes, animate) {
    var box = '';

    $.each(boxes, function(k,v) {
      box = '.'+v;
      prli_hide_box(box,animate);
    });

    if (typeof boxes[$(select).val()] !== undefined) {
      box = '.'+boxes[$(select).val()];
      prli_show_box(box,animate);
    }
  };

  // Setup all option toggle boxes
  var prli_toggle_boxes = function() {
    $('.prli-toggle-checkbox').each(function() {
      var box = '.'+$(this).data('box');
      var reverse  = (typeof $(this).data('reverse') !== 'undefined');

      prli_toggle_checkbox_box(this, box, false, reverse);

      $(this).on('click', function() {
        prli_toggle_checkbox_box(this, box, true, reverse);
      });
    });

    $('.prli-toggle-link').each(function() {
      var box = '.'+$(this).data('box');
      var reverse = (typeof $(this).data('reverse') !== 'undefined');

      reverse ? prli_show_box(box, false) : prli_hide_box(box, false);

      $(this).on('click', function(e) {
        e.preventDefault();
        prli_toggle_link_box(this, box, true);
      });
    });

    $('.prli-toggle-select').each(function() {
      var boxes = {};
      var select = this;

      $(this).find('option').each(function() {
        var boxname = $(this).val()+'-box';
        if (typeof $(select).data(boxname) !== 'undefined') {
          boxes[$(this).val()] = $(select).data(boxname);
        }
      });

      prli_toggle_select_box(this, boxes, false);

      $(this).on('change', function(e) {
        prli_toggle_select_box(this, boxes, true);
      });
    });
  };

  prli_toggle_boxes();

  // Adjust the action url so we can stay on the same settings page on update
  $('table.prli-settings-table').on('prli-settings-url', function( e, href, hash, url ) {
    $('form#prli-options').attr('action',href);
  });
});

