(function() {
    tinymce.PluginManager.add('svg_complete_tc_button', function( editor, url ) {
               
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};

		function html( cls, data ,con) {
			var placeholder = getAttr(data,'alt_path');
                        if(placeholder=='') placeholder= url+'/placeholder.png';
                        var align = getAttr(data,'align');
                        var width = getAttr(data,'width');
                        var height = getAttr(data,'height');
                        data = window.encodeURIComponent( data );
			content = window.encodeURIComponent( con );
                        
                        var htmlplaceholder= '<div class="svgcomp ' + align + ' wp-caption"><img src="' + placeholder + '" class="svgcompimg mceItem ' + cls + '" ' + 'data-sh-attr="' + data + 
                                             '" data-sh-content="' + content + '" width="' + width + '" height="' + height + 
                                             '" data-mce-resize="false" data-mce-placeholder="1" />';
                        if(con!='') htmlplaceholder+= '<p class="wp-caption-text wp-caption-dd">' + con + '</p>';
                        htmlplaceholder+= '</div>';

			return htmlplaceholder;
		} 

		function replaceShortcodes( content ) {
			return content.replace( /\[svg-complete([^\]]*)\]([^\]]*)\[\/svg-complete\]/g, function( all,attr,con) {
				return html( 'svg_complete_tinymce_img', attr , con);
			});
		}

		function restoreShortcodes( content ) {
			return content.replace( /<div class(?:[^>\"']|\"[^\"]*\"|'[^']*')*>(.*?)<\/div>/g, function( match, image ) {
				var data = getAttr( image, 'data-sh-attr' );
				var con = getAttr( image, 'data-sh-content' );

				if ( data ) {
					return '[svg-complete' + data + ']' + con + '[/svg-complete]';
				}
				return match;
			});
		}
                
                //replace from shortcode to an image placeholder
		editor.on('BeforeSetcontent', function(event){ 
			event.content = replaceShortcodes( event.content );
		});

		//replace from image placeholder to shortcode
		editor.on('GetContent', function(event){
			event.content = restoreShortcodes(event.content);
		});
        
        
        editor.addButton( 'svg_complete_tc_button', {
            title: 'Insert a SVG image',
            image : url+'/svg.gif',
            onclick: function() {
                
                var svgdata;
                var imgdata;
                var ratio= 0;
                var timerSetHeight;
                var timerSetWidth;
                
                
                editor.windowManager.open( {
                    title: 'Insert svg image',
                    body: [
                        {
                            type:'button',
                            text:'Select svg file',
                            icon:'icon dashicons-admin-media',
                            name:'selectfile',
                            onclick:function(e){
                                var frame = wp.media({
                                  title:'Choose file',
                                  multiple:false,
                                  button:{ text:'Insert file..' }
                                });
                                frame.open();
                                frame.on('select', function(){
                                        svgdata = frame.state().get('selection').first().toJSON();
                                        jQuery('#svgurl').val(svgdata.url);
                                        jQuery('#svgcap').val(svgdata.caption.replace(/\n/g,"<br>"));
                                        jQuery('#alttext').val(svgdata.alt);
                                }); 
                                
                            }
                        },
                        {
                            type:'textbox',
                            name:'svgurl',
                            id:'svgurl',
                            label:'SVG url'
                        },
                        {
                            type:'button',
                            text:'Select alternative image file',
                            icon:'icon dashicons-admin-media',
                            name:'selectaltfile',
                            onclick:function(e){
                                var frame = wp.media({
                                  title:'Choose file',
                                  multiple:false,
                                  button:{ text:'Insert file..' }
                                });
                                frame.open();
                                frame.on('select', function(){
                                        imgdata = frame.state().get('selection').first().toJSON();
                                        ratio = imgdata.height/imgdata.width;
                                        jQuery('#imgurl').val(imgdata.sizes.medium.url);
                                        jQuery('#imgheight').val(imgdata.sizes.medium.height);
                                        jQuery('#imgwidth').val(imgdata.sizes.medium.width);
                                        if(jQuery('#svgcap').val()==''){
                                          jQuery('#svgcap').val(imgdata.caption);  
                                        }
                                        if(jQuery('#alttext').val()==''){
                                          jQuery('#alttext').val(imgdata.alt);  
                                        }
                                });
                            }
                        },
                        {
                            type:'listbox',
                            name:'imgsizes',
                            label:'Image size',
                            'values': [
                                {text: 'Medium', value: 'Medium',
                                    onclick: function(e) {
                                        if(imgdata){
                                            jQuery('#imgurl').val(imgdata.sizes.medium.url);
                                            jQuery('#imgheight').val(imgdata.sizes.medium.height);
                                            jQuery('#imgwidth').val(imgdata.sizes.medium.width);
                                        }
                                    } 
                                },
                                {text: 'Large',  value: 'Large',
                                    onclick: function(e) {
                                        if(imgdata){
                                            jQuery('#imgurl').val(imgdata.sizes.large.url);
                                            jQuery('#imgheight').val(imgdata.sizes.large.height);
                                            jQuery('#imgwidth').val(imgdata.sizes.large.width);
                                        }
                                    }
                                },
                                {text: 'Full', value: 'Full',
                                    onclick: function(e) {
                                        if(imgdata){
                                            jQuery('#imgurl').val(imgdata.url);
                                            jQuery('#imgheight').val(imgdata.height);
                                            jQuery('#imgwidth').val(imgdata.width);
                                        }
                                    }
                                }
                             ]
                        },
                        {
                            type:'textbox',
                            name:'imgurl',
                            id:'imgurl',
                            label:'Alternative image url'
                        },
                        {
                            type:'textbox',
                            name:'alttext',
                            id:'alttext',
                            label:'Alt. text'
                        },
                        {
                            type:'textbox',
                            name:'svgcap',
                            id:'svgcap',
                            label:'Caption'
                        },
                        {
                            type:'textbox',
                            name:'imgwidth',
                            id:'imgwidth',
                            label:'Width',
                            onclick: function(e){
                                clearTimeout(timerSetWidth);
                                var imgwidth;
                                var SetHeight = function() { 
                                    if(jQuery('#svgratio').attr('aria-checked')=='true'){
                                        imgwidth= jQuery('#imgwidth').val();
                                        jQuery('#imgheight').val( Math.round(imgwidth * ratio) ); 
                                    } else {
                                      clearTimeout(timerSetHeight);
                                      clearTimeout(timerSetWidth);
                                    }
                                };
                                timerSetHeight= setInterval(SetHeight, 300);
                            }
                        },
                        {
                            type:'textbox',
                            name:'imgheight',
                            id:'imgheight',
                            label:'Height',
                            onclick: function(e){
                                clearTimeout(timerSetHeight);
                                var imgheight;
                                var SetWidth = function() {
                                    if(jQuery('#svgratio').attr('aria-checked')=='true'){
                                        imgheight= jQuery('#imgheight').val();
                                        jQuery('#imgwidth').val( Math.round(imgheight / ratio) );
                                    } else {
                                      clearTimeout(timerSetHeight);
                                      clearTimeout(timerSetWidth);
                                    }
                                };
                                timerSetWidth= setInterval(SetWidth, 300);
                            }
                        },
                        {
                            type:'checkbox',
                            name:'svgratio',
                            id:'svgratio',
                            label:'Keep aspect ratio',
                            onclick: function(e){
                                if(ratio==0 || isNaN(ratio)) {
                                    ratio= jQuery('#imgheight').val() / jQuery('#imgwidth').val();
                                }
                            }
                        },
                        {
                            type:'textbox',
                            name:'svgcss',
                            id:'svgcss',
                            label:'CSS class'                            
                        },
                        {
                            type:'listbox',
                            name:'svgalign',
                            label:'Align',
                            'values': [
                                {text: 'Left', value: 'alignleft'},
                                {text: 'Center', value: 'aligncenter'},
                                {text: 'Right', value: 'alignright'},
                                {text: 'None', value: 'svgnoalign'}
                             ]
                        },
                        {
                            type:'listbox',
                            name:'zoompan',
                            label:'Image zoom and pan?',
                            'values': [
                                {text: 'None', value: 'zoom_none'},
                                {text: 'Mouse only', value: 'zoom_mouse'},
                                {text: 'Mouse and buttons', value: 'zoom_button'}
                             ]
                        }
                
                    ],
                    onsubmit: function( e ) {
                        clearTimeout(timerSetWidth);
                        clearTimeout(timerSetHeight);
                        editor.insertContent( '[svg-complete align="' + e.data.svgalign + '" class="' + e.data.svgcss + 
                                                 '" svg_path="' + e.data.svgurl + '" alt_path="' + e.data.imgurl + '" alt="' + e.data.alttext + 
                                                 '" width="' + e.data.imgwidth + '" height="' + e.data.imgheight + '" zoompan="' + e.data.zoompan + 
                                                 '"]' + e.data.svgcap + '[/svg-complete]');    //  e.data.level
                    }
                });
            }
            
        });
    });
})();