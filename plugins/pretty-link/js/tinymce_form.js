(function() {
  tinymce.create('tinymce.plugins.prli', {
    init : function(ed, url) {
      ed.addCommand('prli_tinymce_form', function() {
        ed.windowManager.open({
          file : ajaxurl + '?action=prli_tinymce_form', // file that contains HTML for our modal window
          width : 600 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
          height : 500 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
          inline : 1
        }, {
          plugin_url : url
        });
      });
      ed.addButton('prli_tinymce_form', {title : 'Insert Pretty Link', cmd : 'prli_tinymce_form', image: url + '/../images/tinymce_form_popup.png'});
    },

    getInfo : function() {
      return {
        longname : 'PrliTinyMCE',
        author : 'Caseproof, LLC',
        authorurl : 'http://www.caseproof.com',
        infourl : 'http://www.caseproof.com',
        version : tinymce.majorVersion + "." + tinymce.minorVersion
      };
    }
  });

  // Register plugin
  // first parameter is the button ID and must match ID elsewhere
  // second parameter must match the first parameter of the tinymce.create() function above
  tinymce.PluginManager.add('PrliTinyMCE', tinymce.plugins.prli);
})();
