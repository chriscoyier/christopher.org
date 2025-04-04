//admin functions
;(function($){
	$( document ).ready(function($) {
		// based on modal.js Copyright 2014 Thomas Griffin.  http://thomasgriffin.io/
		$( document ).on('click.update-modal-holder','.tgm-plugin-update-modal', function(e){
			e.preventDefault();
			if ( ! $(this).hasClass('thickbox') ) {
				if ( typeof adminpage != 'undefined' && 'update-core-php' != adminpage )
					$('body').addClass('update-core-php');
				$(this).addClass('thickbox').click();
			}
			$('#TB_window').on('tb_unload', function(){
				if ( typeof adminpage != 'undefined' && 'update-core-php' != adminpage )
					$('body').removeClass('update-core-php');
				$('.tgm-plugin-update-modal').removeClass('thickbox');
			});
		});
		$( document ).on('click.createpost-wrapper', '.create-appip-product', function(e){
			if( $('[name="amazon-product-single-asin"]').val() == '' ){
				alert('Please complete the form before submitting.');
				e.preventDefault();
				return false;
			}
		});
		$('.appiptabs.nav-tab').on('click',function(e){
			e.preventDefault();	
			var thisID = '#'+$(this).attr('id')+'-content';
			var thisTab = $(this);
			var activeContent = '#'+$('.nav-tab-content.active').attr('id');
			//console.log(thisID+'|'+activeContent);
			$( activeContent ).fadeOut('fast',function(e){
				$('.appiptabs.nav-tab').removeClass('nav-tab-active');
				$(thisTab).addClass('nav-tab-active');
				$(thisID).addClass('active').fadeIn();
				$('#appip_current_tab').val($(thisTab).attr('id'));
				//doHashChange(activeContent);
			}).removeClass('active');
		});
		$('[href^="?page=apipp_plugin-shortcode&tab="]').on('click',function(e){
			e.preventDefault();	
			var thisID = '#'+$(this).attr('class')+'-content';
			var thisTab = '#'+$(this).attr('class');
			var activeContent = '#'+$('.nav-tab-content.active').attr('id');
			//console.log(thisID+'|'+activeContent);
			$( activeContent ).fadeOut('fast',function(){
				$('.appiptabs.nav-tab').removeClass('nav-tab-active');
				$(thisTab).addClass('nav-tab-active');
				$(thisID).addClass('active').fadeIn();
				//doHashChange(activeContent);
			}).removeClass('active');
		});
		function doHashChange(hash){
			if(history.pushState) {
				history.pushState(null, null, hash);
			}else {
				location.hash = hash;
			}
		}
		$( '#split_asins').on('click', function(e){
			var asins = $(this).is(':checked');
			if( asins ){
				$('[name="createpost_edit"]').css({'display':'none'});
			}else{
				$('[name="createpost_edit"]').css({'display':'inline-block'});
			}
		});
		$( "#appap-add-new-form" ).on( "click", '[name="createpost_edit"]', function(e) {
		  	var $act = $( "#appap-add-new-form" ).attr('action');
			//e.preventDefault();
			//$( "#appap-add-new-form" ).attr('action', $act + '&appip-do=edit');
			//$( "#appap-add-new-form" ).attr('action', $act + '&appip-do=edit');
		});
		$( ".appip-content-type" ).on('click', function() {
			var $imgbase = $('.appipexampleimg').attr('data');
			if ($(this).is(":checked")) {
				var group = "input:checkbox[name='" + $(this).attr("name") + "']";
				$(group).prop("checked", false);
				$(this).prop("checked", true);
				var srcval = $(this).val();
				$('.appipexampleimg').attr('src',$imgbase+'example-layout-'+srcval+'.png');
			} else {
				$(this).prop("checked", false);
				$('.appipexampleimg').attr('src',$imgbase+'example-blank.png');
			}
		});
		$('.taxonomy_block_post').show();
		$( document ).on('click', '.apip-ptypecb',function() {
			var apptypeval = $(this).val();
			$('.taxonomy_blocks').hide();				
			$('.taxonomy_block_'+apptypeval).show('slow');	
	
		});
		$( ".apip-ptypecb" ).each(function(){
			if($(this).prop("checked") == true){
				var apptypeval = $(this).val();
				$('.taxonomy_blocks').hide();				
				$('.taxonomy_block_'+apptypeval).show('slow');	
			}
		});
		$('#the-list').on( 'click', '.xml-show', function(e){
			e.preventDefault();
			var $showhidexml = $(this);
			if($showhidexml.hasClass( 'xml-hide' )){
				$showhidexml.next( 'textarea' ).css({ 'display' : 'none' });
				$showhidexml.removeClass( 'xml-hide' ).html( 'show JSON cache data' );
			}else{
				$showhidexml.next( 'textarea' ).css({ 'display' : 'block' });
				$showhidexml.addClass( 'xml-hide' ).html( 'hide JSON cache data' );
			}
		});
		$('.amazon-product_page_apipp-cache-page').delegate('.appip-cache-del', 'click', function(e){
			e.preventDefault();
			var r	= confirm( appipData.confirmDel );
			if (r == true){
				var buttonid = $(this).attr('id');
				var appbtnid = buttonid.replace( "appip-cache-", "" );
				$.post(appipData.ajaxURL, {'action' : "appip-cache-del", "appip-cache-id": appbtnid, "appip_nonce": appipData.appip_nonce }, function( data ){
					if(data == "deleted"){
						if(appbtnid == '0'){
							$( ".iedit" ).remove();
						}else{
							$( "." + buttonid + "-row" ).remove();
						}
					}else{
						alert( appipData.deleteMsgErr );
					}
					if( $( "#the-list tr" ).length == 0){
						$( "#the-list" ).html( '<tr class="alternate iedit appip-cache--row"><td colspan="4">'+appipData.noCacheMsg+'</td></tr>' );
					}
				});
			}
		});
	});
})(jQuery);