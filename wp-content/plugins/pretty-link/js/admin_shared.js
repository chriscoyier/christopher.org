function toggle_iphone_instructions() {
  jQuery('.iphone_instructions').slideToggle();
}

jQuery(document).ready(function($) {
  $(".prli-social-button-checkboxes").sortable();

  if ($.fn.magnificPopup) {
    $('.prli-update').magnificPopup({
      delegate: '.prli-image-popup',
      type: 'image',
      mainClass: 'mfp-prli',
      gallery: {
        enabled: true
      }
    });
  }
});

jQuery(document).ready(function($) {
  // Flyout Menu Elements.
  var $flyoutMenu    = $( '#caseproof-flyout' );

  if ( $flyoutMenu.length === 0 ) {
    return;
  }

  var $head   = $flyoutMenu.find( '.caseproof-flyout-head' ),
    $sullie = $head.find( 'img' ),
    menu    = {
      state: 'inactive',
      srcInactive: $sullie.attr( 'src' ),
      srcActive: $sullie.data( 'active' ),
    };

  // Click on the menu head icon.
  $head.on( 'click', function( e ) {

    e.preventDefault();

    if ( menu.state === 'active' ) {
      $flyoutMenu.removeClass( 'opened' );
      $sullie.attr( 'src', menu.srcInactive );
      menu.state = 'inactive';
    } else {
      $flyoutMenu.addClass( 'opened' );
      $sullie.attr( 'src', menu.srcActive );
      menu.state = 'active';
    }
  } );

  // Page elements and other values.
  var $wpfooter = $( '#wpfooter' );

  if ( $wpfooter.length === 0 ) {
    return;
  }

  var $overlap       = $( '#caseproof-overview, #caseproof-entries-list, #caseproof-tools.caseproof-tools-tab-action-scheduler' ),
    wpfooterTop    = $wpfooter.offset().top,
    wpfooterBottom = wpfooterTop + $wpfooter.height(),
    overlapBottom  = $overlap.length > 0 ? $overlap.offset().top + $overlap.height() + 85 : 0;

  // Hide menu if scrolled down to the bottom of the page.
  $( window ).on( 'resize scroll', function( e ) {

    var viewTop = $( window ).scrollTop(),
      viewBottom = viewTop + $( window ).height();

    if ( wpfooterBottom <= viewBottom && wpfooterTop >= viewTop && overlapBottom > viewBottom ) {
      $flyoutMenu.addClass( 'out' );
    } else {
      $flyoutMenu.removeClass( 'out' );
    }
  } );

  // $( window ).trigger( 'scroll' );
});

jQuery(document).ready(function($) {
  var checkboxes = $('#screen-options-wrap input[type=checkbox]');
  $.each(checkboxes, function(index, el) {
    var label = $(el).closest('label');
    if ( el.name.includes('pro-pretty-link') || label.html().includes('Keywords Pro') ) {
      var labelHtml = label.html();
      var newLabel = labelHtml.replace(' Pro', '');
      label.html(newLabel);
    }
  });
});
