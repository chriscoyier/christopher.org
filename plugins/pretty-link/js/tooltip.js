(function($) {
  $(document).ready(function() {
    $('body').on('click', '.prli-tooltip', function() {
      var tooltip_title = $(this).find('.prli-data-title').html();
      var tooltip_info = $(this).find('.prli-data-info').html();
      $(this).pointer({ 'content':  '<h3>' + tooltip_title + '</h3><p>' + tooltip_info + '</p>',
                        'position': {'edge':'left','align':'center'},
                        //'buttons': function() {
                        //  // intentionally left blank to eliminate 'dismiss' button
                        //}
                      })
      .pointer('open');
    });

    //$('body').on('mouseout', '.prli-tooltip', function() {
    //  $(this).pointer('close');
    //});

    if( PrliTooltip.show_about_notice ) {
      var prli_about_pointer_id = 'prli-about-info';

      var prli_setup_about_pointer = function() {
        $('#'+prli_about_pointer_id).pointer({
          content: PrliTooltip.about_notice,
          position: {'edge':'bottom','align':'left'},
          close: function() {
            var args = { action: 'prli_close_about_notice' };
            $.post( ajaxurl, args );
          }
        }).pointer('open');
      };

      $('.toplevel_page_pretty-link .wp-menu-name').attr( 'id', prli_about_pointer_id );
      prli_setup_about_pointer();
    }
  });
})(jQuery);
