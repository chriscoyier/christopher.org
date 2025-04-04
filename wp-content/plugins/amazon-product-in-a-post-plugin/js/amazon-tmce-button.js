(function() {
    tinymce.PluginManager.add('amazon_product_tmce_button', function( editor, url ) {
		
		var sh_tag 		= 'amazonproducts',
			sh_tag2		= 'amazon-elements',
			sh_tag3		= 'amazon-product-search',
			touchStart 	= false,
			tapped 		= false;

		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};

		function toTitleCase( str ){
    		return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
		}

		function getMatches( string, regex, index ) {
			index || (index = 1); // default to the first capturing group
			var matches = [],
				tmatch;
			while (tmatch = regex.exec(string)) {
				matches.push(tmatch[index]);
			}
			return matches;
		}

		function html( cls, data ,con) {
			var tempurl = url.slice(0, -3);
			var placeholder = tempurl + '/images/amazon-products-placeholder.png';
			if(cls == sh_tag2 + '_shortcode'){
				placeholder = tempurl + '/images/viewcart-placeholder.gif';
				return '<img style="display:block;max-width:100%;width:107px;height:auto;cursor:pointer;margin:5px;padding:2px;outline: none;border: 1px dashed rgba(0, 0, 0, 0.13);" src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="" data-sh-id="amazon-elements" data-mce-resize="false" data-mce-placeholder="1" />';
			}else if( cls == sh_tag + '_shortcode'){
				data 	= window.encodeURIComponent( data );
				var temp = '<img style="max-width:700px;width:100%;height:auto;cursor:pointer;outline: none;border: 1px dashed rgba(0, 0, 0, 0.13);" src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-sh-id="amazonproducts" data-mce-resize="false" data-mce-placeholder="1" />';
				return  temp;
			}
			return con;
		}

		function replaceShortcodes( content ) {
			var scOpened = new RegExp("\\[" + sh_tag + "([^\\]]*)\\]","g"),
				scCart 	 = new RegExp("\\[" + sh_tag2 + "([^\\]]*)\\]","g"),
				scHasClose  = content.indexOf("[\/" + sh_tag + "]") >= 0 ? true : false;
			content = content.replace( scOpened, function( all,attr,con) {
					return html(  sh_tag + '_shortcode', attr , window.decodeURIComponent(con));
			});
			content = content.replace( scCart, function( all,attr,con) {
				return html( sh_tag2 + '_shortcode', attr , window.decodeURIComponent(con));
			});			
			return content;
		}

		function restoreShortcodes( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data 		= getAttr( image, 'data-sh-attr' );
				var tid			= getAttr( image, 'data-sh-id' );
				if ( data ) {
					if( tid == 'amazon-elements' ){
						return '[' + sh_tag2 + data + ']';
					}else if( tid == 'amazon-product-search' ){
						return '[' + sh_tag3 + data + ']';
					}else if( tid == 'amazonproducts' ){
						return '[' + sh_tag + data + ']';
					}
				}
				return match;
			});
		}
		
		function openEditorPayPalPromo(e) {
			var cls  = e.target.className.indexOf( sh_tag + '_shortcode');
			var cls2  = e.target.className.indexOf( sh_tag2 + '_shortcode');
			if ( e.target.nodeName == 'IMG' && cls > -1 ) {
				var popupTitle = e.target.attributes['data-sh-attr'].value;
				popupTitle 	= window.decodeURIComponent(popupTitle);
				editor.execCommand(sh_tag + '_shortcode_popup','',{
					asins 		: getAttr(popupTitle,'asin') ,
					gallery 	: getAttr(popupTitle,'gallery') ,
					align   	: getAttr(popupTitle,'align')
				});
			}else if( e.target.nodeName == 'IMG' && cls2 > -1 ){

			}
		}

		editor.addCommand( sh_tag + '_shortcode_popup', function(ui, v) {
			//console.log(ui);
			if (!String.prototype.trim) {String.prototype.trim = function() {return this.replace(/^\s+|\s+$/g,'');}}
			var shortcode_str = '[' + sh_tag ,
				align 		= v.align ? v.align : '',
				asins 		= v.asins ? v.asins : '',
				gallery		= v.gallery ? v.gallery : 0;
			editor.windowManager.open( {
				title: 'Insert Amazon Product',
				minWidth: 200,
				body: [
				{
					type: 'textbox', 
					name: 'asins', 
					label: 'ASIN(s)', 
					value: asins,
					minWidth: 150,
				},
				{	
					type: 'checkbox',
					name: 'gallery',
					label: 'Gallery/Extra Images',
					value: 1,
					checked: gallery == 1 ? true : false
				},
				{
					type: 'listbox', 
					name: 'align', 
					label: 'Align', 
					values: [
						{ text: 'none', value: '' },
						{ text: 'Align Left', value: 'alignleft' },
						{ text: 'Align Right', value: 'alignright' },
						{ text: 'Align Center', value: 'aligncenter' }
					],
					value: align,
					minWidth: 150,
				}
				],
				onsubmit: function( e ) {
					//var content =  typeof e.data.content != 'undefined' ? e.data.content : '';
					//console.log(e.data);
					if (typeof e.data.asins !== 'undefined' && e.data.asins.length ){
						shortcode_str += ' asin="' + e.data.asins + '"';
					}
					if (typeof e.data.gallery !== 'undefined' && e.data.gallery  ){
						shortcode_str += ' gallery="1"';
					}
					if (typeof e.data.align !== 'undefined' && e.data.align.length ){
						shortcode_str += ' align="' + e.data.align + '"';						
					}

					shortcode_str += ']';
					editor.insertContent( shortcode_str );
				}
			});
		});

        editor.addButton( 'amazon_product_tmce_button', {
            title: 'Insert Amazon Shortcodes',
            type: 'menubutton',
            icon: 'icon dashicons-amazon',
            menu: [
                {
                    text: 'Add Amazon Product(s)',
                    onclick: function() {
						editor.execCommand( sh_tag + '_shortcode_popup','amz-prod',{
							asins 		: '',
							gallery		: '0',
							align   	: ''
						});
                    }
                },
                {
                    text: 'Add Amazon Search Result',
                    onclick: function() {
						editor.execCommand( sh_tag + '_shortcode_popup','amz-search',{
							asins 		: '',
							gallery		: '0',
							align   	: ''
						});
                    }
                },
                 {
                    text: 'Add Amazon Element',
                    onclick: function(e) {
						//editor.insertContent( '[pppviewcart]');
						editor.execCommand( sh_tag + '_shortcode_popup','amz-elem',{
							asins 		: '',
							gallery		: '0',
							align   	: ''
						});
                    }
                },
          ]
        });
		
		//replace from shortcode to an image placeholder
		editor.on('BeforeSetcontent', function(e){ 
			e.content = replaceShortcodes( e.content );
		});

		//replace from image placeholder to shortcode
		editor.on('GetContent', function(e){
			e.content = restoreShortcodes(e.content);
		});
		
		editor.on('touchstart',function(e) {
			
			e.preventDefault(); 
			touchStart	= true;
			if( !tapped ){
				tapped = setTimeout(function (){tapped = null;},300);
			}else{
				openEditorPayPalPromo(e);
				clearTimeout(tapped);
				tapped = null;
			}
		});
		
		editor.on('touchend touchcancel',function (e){
			e.preventDefault();
			touchStart = false;
		});
		
		//open popup on placeholder double click (for non Touch);
		editor.on( 'DblClick', function (e){
			openEditorPayPalPromo(e);
		});
    });
})();