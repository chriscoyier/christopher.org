jQuery(document).ready(function($) {
  // TOOLTIPS
  $('.icon-clipboardjs').each(function(i, el) {
    var $el = $(el),
        copy_text = 'Copy to Clipboard',
        copied_text = 'Copied!',
        copy_error_text = 'Oops, Copy Failed!',
        clipboard = new ClipboardJS(el);

    if ($.fn.tooltipster) {
      try {
        var instance = $el.tooltipster({
          theme: 'tooltipster-borderless',
          content: copy_text,
          trigger: 'custom',
          triggerClose: {
            mouseleave: true,
            touchleave: true
          },
          triggerOpen: {
            mouseenter: true,
            touchstart: true
          }
        }).tooltipster('instance');

        clipboard
          .on('success', function(e) {
            instance
              .content(copied_text)
              .one('after', function(){
                instance.content(copy_text);
              });
          })
          .on('error', function(e) {
            instance
              .content(copy_error_text)
              .one('after', function(){
                instance.content(copy_text);
              });
          });
      } catch (e) {
        // With tooltipster <=3.3.0 an error will be caught here, just display a static tooltip
        $el.tooltipster('destroy').tooltipster({
          content: copy_text
        });
      }
    }
  });

  // Hide Status from Link Bulk Edit
  if (window.pagenow === 'edit-pretty-link' && $('#bulk-edit').length) {
    $('#bulk-edit').find('select[name="_status"]').closest('.inline-edit-col').hide();
  }

  // Hide Date/Password/Status from Quick Edit
  if (window.pagenow === 'edit-pretty-link') {
    $('fieldset.inline-edit-date').hide();
    $('.inline-edit-password-input').parent().parent().parent().hide();
    $('label.inline-edit-status').parent().hide();
  }

  // BULK EDIT JS
  $(document).on('click', '#bulk_edit', function() {
    // Display spinner as ajax can take a while if there's a lot of posts to edit
    $('span#prli_bulk_edit_spinner img').fadeIn();
    var bulk_row = $('#bulk-edit');
    var post_ids = [];

    bulk_row.find('#bulk-titles-list li button').each(function() {
      post_ids.push($(this).attr('id').replace('_', ''));
    });

    var nofollow = bulk_row.find('select[name="prli_quick_edit_nofollow"]').val();
    var sponsored = bulk_row.find('select[name="prli_quick_edit_sponsored"]').val();
    var tracking = bulk_row.find('select[name="prli_quick_edit_tracking"]').val();

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      cache: false,
      data: {
        action: 'prli_links_list_save_bulk_edit',
        post_ids: post_ids,
        nofollow: nofollow,
        sponsored: sponsored,
        tracking: tracking
      }
    });
  });

  var $prli_broken_links = $('#prli-broken-links');

  $prli_broken_links.on('click', function(e) {
    e.preventDefault();
  });

  $prli_broken_links.tooltipster({
    maxWidth: 350,
    position: 'right',
    interactive: true,
    trigger: 'click',
    contentAsHTML: true,
    content: PrliLinkList.broken_link_text
  });
});
