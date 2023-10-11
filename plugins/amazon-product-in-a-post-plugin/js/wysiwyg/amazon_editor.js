(function() {
   tinymce.create('tinymce.plugins.amazon_products', {
      init : function(ed, url) {
         ed.addButton('amazon_products', {
            title : 'Amazon Product In a Post',
            image : url+'/aicon.png',
            onclick : function() {
				var ASIN = prompt("Amazon ASIN(s) - separate multiple with a comma.", "");
				var shortcode_attrib = '';
				//var locale = prompt("Amazon Locale", "com");
				//var partner_id = prompt("Amazon Partner ID", "");
				//var public_key = prompt("Amazon Public Key for this Locale", "");
				//var private_key = prompt("Amazon Private Key for this Locale", "");
				//var extra_text = prompt("Extra Text", "");
	          	//if(locale !='' && locale != null){ shortcode_attrib =  shortcode_attrib +' locale="'+locale+'"';}
	          	//if(partner_id !='' && partner_id != null){ shortcode_attrib =  shortcode_attrib +' partner_id="'+partner_id+'"';}
	          	//if(public_key !='' && public_key != null){ shortcode_attrib =  shortcode_attrib +' public_key="'+public_key+'"';}
	          	//if(private_key !='' && private_key != null){ shortcode_attrib =  shortcode_attrib +' private_key="'+private_key+'"';}
				if( ASIN== null || ASIN ==''){
					alert('At least one Amazon ASIN is required to use this shortcode.');
				//}else if(extra_text != null && extra_text != ''){
					//ASIN = ASIN.replace(/^\s+|\s+$/g, '') ;
					//ed.execCommand('mceInsertContent', false, '[AMAZONPRODUCTS asin="'+ASIN+'"'+shortcode_attrib+']'+extra_text+'[/AMAZONPRODUCT]');
				}else{
					ASIN = ASIN.replace(/^\s+|\s+$/g, '') ;
					ed.execCommand('mceInsertContent', false, '[AMAZONPRODUCTS asin="'+ASIN+'"'+shortcode_attrib+']');
				}
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "Amazon Product In a Post",
            author : 'Don Fischer',
            authorurl : '',
            infourl : '',
            version : "1.0"
         };
      }
   });
   tinymce.PluginManager.add('amazon_products', tinymce.plugins.amazon_products);
})();