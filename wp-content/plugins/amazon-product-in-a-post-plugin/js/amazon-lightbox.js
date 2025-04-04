(function($){
	// Updated 06/18/18
	$(document).ready(function() {
	  appiplightbox();
	});
	
	function appiplightbox() {
		var newLinks 		= [];
		var overlay 		= $($('<div id="overlay" style="display: none"></div>'));
		var container 		= $($('<div id="appiplightbox" style="display: none"></div>'));
		var close 			= $($('<a href="#close" class="close">&times; Close</a>'));
		var target 			= $($('<div class="target"></div>'));
		var prev 			= $($('<a href="#prev" class="prev">&laquo;</a>'));
		var next 			= $($('<a href="#next" class="next">&raquo;</a>'));
		$('a[rel^=appiplightbox]').each(function () {
			if($.inArray($(this).attr('rel'), newLinks)=== -1){
				newLinks.push($(this).attr('rel'));
			}
		});
		//console.log(newLinks);
		var itemapp = '';
		var itemtext = '';
		var i;
		var len = newLinks.length;
		//console.log(len);
		for( i = len; i >= 0; i-- ){
			itemtext = 'a[rel='+newLinks[i]+']';
			$(itemtext).each( function(index) {
				itemapp = $(this);
				//console.log(itemapp);
				itemapp.on('click',function(c) {
					//console.log(itemapp.data('appiplg'));
					c.preventDefault();
					$('a[target="amazonwin"]').removeClass('selected');
					open($(this).data('appiplg'));
					$(this).addClass('selected');
				});
				itemapp.attr({'lb-position': index});
			});
		}
		$('body').append(overlay).append(container);
		container.append(close).append(target).append(prev).append(next);
		container.show().css({'top': Math.round((($(window).height() > window.innerHeight ? window.innerHeight : $(window).height()) - container.outerHeight()) / 2) + 'px', 'left': Math.round(($(window).width() - container.outerWidth()) / 2) + 'px', 'margin-top': 0, 'margin-left': 0}).hide();
		prev.add(next).on('click', function(c) {
			c.preventDefault();
			var tempo 		= $('a[rel*="appiplightbox-"].selected');
			var itemgr 		= tempo.attr('rel');
			var itemgb 		= tempo.attr('lb-position');
			var mgsize 		= $('a[rel='+itemgr+']').size();
			//var maingroup 	= $(itemgr);
			var current 	= parseInt(itemgb,10);
			var to			= null;
			tempo.removeClass('selected');
			if($(this).is('.prev')){
				to = $('[rel='+itemgr+'][lb-position="'+(current - 1)+'"]');
				if(current === 0){
					$('[rel='+itemgr+'][lb-position="'+(mgsize - 1)+'"]').addClass('selected');
				}else{
					$('[rel='+itemgr+'][lb-position="'+(itemgb + 1)+'"]').addClass('selected');
				}
				if(current === 0 && mgsize === 1){
					to = $('[rel='+itemgr+'][lb-position="0"]');
				}else if(current === 0){
					to = $('[rel='+itemgr+'][lb-position="'+(mgsize-1)+'"]');
				}
			}else{
				to = $('[rel='+itemgr+'][lb-position="'+(current + 1)+'"]');
				if(itemgb === (mgsize - 1) ){
					$('[rel='+itemgr+'][lb-position="'+((itemgb + 1 > mgsize ? 0 : itemgb + 1))+'"]').addClass('selected');
				}else{
					$('[rel='+itemgr+'][lb-position="'+(itemgb + 1)+'"]').addClass('selected');
				}
				if(current === 0 && mgsize === 1){
					to = $('[rel='+itemgr+'][lb-position="0"]');
				}else if(current === (mgsize - 1)){
					to = $('[rel='+itemgr+'][lb-position="0"]');
				}
			}
			if(!to.size()){
			  to = $(this).is('.prev') ? $('[rel='+itemgr+'][lb-position='+(mgsize-1)+']') : $('[rel='+itemgr+'][lb-position="0"]');
			}else{
			  to.click();
			}
		});
		close.add(overlay).on('click',function(c) {
			c.preventDefault();
			overlay.add(container).fadeOut('normal');
		});
	
		var open;
		open = function (url) {
			if (container.is(':visible')) {
				target.children().fadeOut('normal', function () {
					target.children().remove();
					loadImage(url);
				});
			} else {
				target.children().remove();
				overlay.add(container).fadeIn('normal', function () {
					loadImage(url);
				});
			}
		};
	
		var loadImage;
		loadImage = function (url) {
			if (container.is('.loading')) {
				return;
			}
			container.addClass('loading');
			var img = new Image();
			img.onload = function () {
				img.style.display = 'none';
				var maxWidth = ($(window).width() - parseInt(container.css('padding-left'), 10) - parseInt(container.css('padding-right'), 10)) - 100;
				var maxHeight = (($(window).height() > window.innerHeight ? window.innerHeight : $(window).height()) - parseInt(container.css('padding-top'), 10) - parseInt(container.css('padding-bottom'), 10)) - 100;
				if (img.width > maxWidth || img.height > maxHeight) { // One of these is larger than the window
					var ratio = img.width / img.height;
					if (img.height >= maxHeight) {
						img.height = maxHeight;
						img.width = maxHeight * ratio;
					} else {
						img.width = maxWidth;
						img.height = maxWidth * ratio;
					}
				}
				container.animate({
					'width': img.width,
					'height': img.height,
					'top': Math.round((($(window).height() > window.innerHeight ? window.innerHeight : $(window).height()) - img.height - parseInt(container.css('padding-top'), 10) - parseInt(container.css('padding-bottom'), 10)) / 2) + 'px',
					'left': Math.round(($(window).width() - img.width - parseInt(container.css('padding-left'), 10) - parseInt(container.css('padding-right'), 10)) / 2) + 'px'
				}, 'normal', function () {
					target.append(img);
					$(img).fadeIn('normal', function () {
						container.removeClass('loading');
					});
				});
			};
			img.src = url;
		};
	}
})(jQuery);