<?php

class Amazon_Product_Add_Custom_MCE {

	function __construct(){
		add_action( 'admin_head', array( $this, 'custom_add_tmce_button' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'custom_tmce_css' ) );
	}

	function custom_add_tmce_button() {
		global $typenow;
		$stop = true;
		if ( $typenow == 'page' &&  current_user_can( 'edit_pages' )){
			$stop = false;
		} elseif (  $typenow == 'post' && current_user_can( 'edit_posts' ) ){
			$stop = false;
		}
		$stop = apply_filters('amazon-product-show-tmce-button', $stop, $typenow);
		if ( get_user_option('rich_editing') == 'true' && $stop == false) {
			add_filter( 'mce_external_plugins', array( $this, 'custom_add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'custom_register_tmce_button' ) );
		}
		return;
	}
	
	function custom_add_tinymce_plugin($plugin_array) {
		$plugin_array['amazon_product_tmce_button'] = plugins_url( '/js/amazon-tmce-button.js', dirname( __FILE__ ) );
		return $plugin_array;
	}
	
	function custom_register_tmce_button($buttons) {
		$toggle = array_search('wp_adv', $buttons);
		if((int)$toggle > 0 ){
			$buttonsNew = array_chunk ($buttons , (int)$toggle ,false );
			array_push($buttonsNew[0], 'amazon_product_tmce_button');
			$buttons = array_merge($buttonsNew[0], $buttonsNew[1]);
		}else{
			array_push($buttons, 'amazon_product_tmce_button');
		}
		return $buttons;
	}
	
	function custom_tmce_css() {
		wp_enqueue_style('amazon-product-in-a-post-custom-tmce', plugins_url('/css/tmce-styles.css', dirname( __FILE__ )));
	}

}

new Amazon_Product_Add_Custom_MCE();