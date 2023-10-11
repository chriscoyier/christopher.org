;(function($){
	$(document).ready(function(e) {
		if(!$('#apipp_product_styles_mine').prop('checked')){
				$('.apipp_product_styles-wrapper').addClass('disabled');
		}else{
				$('.apipp_product_styles-wrapper').removeClass('disabled');
		}
		if( $('.password-field').val() !== '' ){
        	$('.password-field').attr('type','password');
		}
		$('#apipp_amazon_publickey, #apipp_amazon_secretkey').change(function() {
            $('#form-changed-awsplugin').val( 'true' );
        });	
		$('#apipp_product_styles_mine').on('change', function(){
			if(!$(this).prop('checked')){
				$('.apipp_product_styles-wrapper').addClass('disabled');
			}else{
				$('.apipp_product_styles-wrapper').removeClass('disabled');
			}
		});
		$('#apipp_amazon_test_settings').on('click', function(){
			if( $('#form-changed-awsplugin').val( ) === 'true' ){
				e.preventDefault();
				e.stopPropagation();
				alert('Your keys may have changed.'+"\n\n"+'Please save the options before testing.');
				return false;	
			}
		});
		$('.password-field')
			.focus(function(){ $(this).attr('type','text');})
			.blur(function(){ $(this).attr('type','password');});
		$('.appip_debug_settings_click').on('click', function(){
			if($('.appip_debug_settings').hasClass('showing')){
				$('.appip_debug_settings').removeClass('showing');
				$(this).html('Show Debug Info');
			}else{	
				$('.appip_debug_settings').addClass('showing');
				$(this).html('Hide Debug Info');
			}
		});
		$('[name="appip_debug_submit"]').on('click', function(e){
			if($('[name="appip_debug_send_email"]').val() === ''){
				e.preventDefault();
				alert('Please enter an email address for Debug Info.');
				return false;
			}
			return true;
		});
		$('[name="appip_debug_submit_all"]').on('click', function(e){
			var t = '';
			if($('[name="appip_debug_send_email"]').val() === ''){
				t = t + '  - Enter Email Address for Debug Info to be sent to besides the developers.\n';
			}
			if($('[name="appip_debug_notes"]').val() === ''){
				e.preventDefault();
				t = t + '  - Enter Brief Notes about your Issues.';
			}
			if(t !== ''){
				e.preventDefault();
				t = 'Please Fix the Following:\n\n' + t;
				alert(t);
				return false;
			}
			return true;
		});
		$('[name="appip_debug_submit_dev"]').on('click', function(e){
			if($('[name="appip_debug_notes"]').val() === ''){
				e.preventDefault();
				alert('Please enter Brief Notes or Issues for the Developers.');
				return false;
			}
			return true;
		});
		$('#show-hide-ask').on('click', function(e){
			e.preventDefault();
			var $clkElem = $(this);
			var $shwid = $clkElem.data('pwid');
			var $curtxt = $clkElem.text();
			var $newtxt = $curtxt === 'show' ? 'hide' : 'show';
			if($shwid !== ''){
			//console.log($shwid+'-');
			//console.log($curtxt);
				if($curtxt === 'show'){
					$('#'+$shwid).attr('type','text');
				}else{
					$('#'+$shwid).attr('type','password');
				}
				$clkElem.text($newtxt);
			}
		});
    });
})(jQuery);