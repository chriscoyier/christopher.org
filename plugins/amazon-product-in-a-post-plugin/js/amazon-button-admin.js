(function ($) {
	"use strict";
	$(document).ready(function () {
		/*$(document).on("click", ".upload_image_button", function() {
			//console.log('clicked');
			open_media_uploader_image();
			$.data(document.body, 'prevElement', $(this).prev());
			window.send_to_editor = function(html) {
				var imgurl = $('img',html).attr('src');
				var inputText = $.data(document.body, 'prevElement');
				if(inputText !== undefined && inputText !== ''){
					inputText.val(imgurl);
				}
				tb_remove();
			};
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			return false;
			*
		});*/

		//console.log('loaded');

		var frame,
			metaBox = $('#amz_button_form'),
			addImgLink = metaBox.find('.upload_image_button'),
			delImgLink = metaBox.find('.delete_image_button'),
			imgContainer = metaBox.find('#selected-image'),
			imgIdInput = metaBox.find('#amz-button-image');

		addImgLink.on('click', function (event) {
			event.preventDefault();
			//console.log('add-clicked');
			if (frame) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Select or Upload Button Image',
				button: {
					text: 'Use this button'
				},
				multiple: false
			});
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				imgContainer.append('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');
				imgIdInput.val(attachment.url);
				addImgLink.addClass('hidden');
				delImgLink.removeClass('hidden');
			});
			frame.open();
		});
		delImgLink.on('click', function (event) {
			//console.log('delete-clicked');
			event.preventDefault();
			imgContainer.html('');
			addImgLink.removeClass('hidden');
			delImgLink.addClass('hidden');
			imgIdInput.val('');
		});
	});
})(jQuery);
