<?php
	class Amazon_Product_Featured_Image{

		function __construct(){
			add_action( 'init',                               array( $this, 'filter_override_thumbnail_id') );
			add_filter( 'wp_get_attachment_image_src',        array( $this, 'get_attachment_image_src'),10, 4); 
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'attachment_image_attributes'), 10,3); 
			add_action( 'add_meta_boxes',                     array( $this, 'add_featured_url_metabox') );
			add_action( 'save_post',                          array( $this, 'save_featured_url') );
			add_filter( 'wp_get_attachment_url',              array( $this, 'get_attachment_url'), 10, 2); 
		}

		function get_attachment_url( $url, $postid){
			//This is used for getting attachment when called directly (needed to return External URL)
			global $post;
			if (!isset( $post->ID))
				return $url;
			if ( $this->uses_amazon_featured( $post->ID ) === false )
				return $url;
			$url = $this->get_featured_thumbnail_src( $post->ID );
			return $url;
		}

		function get_attachment_image_src( $image, $attachment_id, $size, $icon ){
			global $post;
			$id = $post->ID;
			if( $id == 0 )
				return $image;
			if ( $this->uses_amazon_featured( $id ) === false )
				return $image;
			$image_url = $this->get_featured_thumbnail_src( $id );
			$image = array( $image_url, 0, 0 );
			return $image;
			
		}

		function add_featured_url_metabox() {
			$excluded_post_types = array('attachment', 'revision', 'nav_menu_item', 'wpcf7_contact_form',);
			foreach ( get_post_types( '', 'names' ) as $post_type ) {
				if ( in_array( $post_type, $excluded_post_types ) )
					continue;
				add_meta_box('amazon_featured_url_metabox','Amazon Featured Image', array( $this, 'featured_url_metabox'), $post_type,'side','default');
			}
		}

		function featured_url_metabox( $post ) {
			$amazon_featured_url = get_post_meta( $post->ID, $this->featured_url(), true );
			$amazon_featured_alt = get_post_meta( $post->ID, '_amazon_featured_alt', true );
			$has_img = strlen( $amazon_featured_url ) > 0;
			$show_if_img = '';
			$hide_if_img = '';
			if ( $has_img )
				$hide_if_img = 'display:none;';
			else
				$show_if_img = 'display:none;';
			?>
			<input type="text" placeholder="ALT text" style="width:100%;margin-top:10px;<?php echo $show_if_img; ?>" id="amazon_featured_alt" name="amazon_featured_alt" value="<?php echo esc_attr( $amazon_featured_alt ); ?>" /><?php
			if ( $has_img ) { ?>
			<div id="amazon_featured_preview_block"><?php
			} else { ?>
			<div id="amazon_featured_preview_block" style="display:none;"><?php
			} ?>
				<div id="amazon_featured_image_wrapper" style="<?php echo ('width:100%;max-width:300px;height:200px;margin-top:10px;background:url(' . $amazon_featured_url . ') no-repeat center center; -webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;' );?>"></div>

			<a id="amazon_featured_remove_button" href="#" onClick="amazon_remove_featured();" style="<?php echo $show_if_img; ?>">Remove featured image</a>

			<script>

                function amazon_remove_featured() {
                    jQuery("#amazon_featured_preview_block").hide();
                    jQuery("#amazon_featured_image_wrapper").hide();
                    jQuery("#amazon_featured_remove_button").hide();
                    jQuery("#amazon_featured_alt").hide();
                    jQuery("#amazon_featured_alt").val('');
                    jQuery("#amazon_featured_url").val('');
                    jQuery("#amazon_featured_url").show();
                    jQuery("#amazon_featured_preview_button").parent().show();
                }

                function amazon_preview_featured() {
                    jQuery("#amazon_featured_preview_block").show();
                    jQuery("#amazon_featured_image_wrapper").css('background-image', "url('" + jQuery("#amazon_featured_url").val() + "')" );
                    jQuery("#amazon_featured_image_wrapper").show();
                    jQuery("#amazon_featured_remove_button").show();
                    jQuery("#amazon_featured_alt").show();
                    jQuery("#amazon_featured_url").hide();
                    jQuery("#amazon_featured_preview_button").parent().hide();
                }

			</script>

			</div>
			<input type="text" placeholder="Image URL" style="width:100%;margin-top:10px;<?php echo $hide_if_img; ?>" id="amazon_featured_url" name="amazon_featured_url" value="<?php echo esc_attr( $amazon_featured_url ); ?>" />
			<div style="text-align:right;margin-top:10px;<?php echo $hide_if_img; ?>"><a class="button" id="amazon_featured_preview_button" onClick="amazon_preview_featured();">Preview</a></div>
			<?php
		}

		function save_featured_url( $post_ID ) {
			if ( isset( $_POST['amazon_featured_url'] ) ) {
				$url = strip_tags( $_POST['amazon_featured_url'] );
				update_post_meta( $post_ID, $this->featured_url(), $url );
			}
			if ( isset( $_POST['amazon_featured_alt'] ) )
				update_post_meta( $post_ID, '_amazon_featured_alt', strip_tags( $_POST['amazon_featured_alt'] ) );
		}

		function featured_url() {
			return apply_filters( 'amazon_featured_post_meta_key', '_amazon_featured_url' );
		}

		function uses_amazon_featured( $id ) {
			$image_url = $this->get_featured_thumbnail_src( $id );
			if ( $image_url === false )
				return false;
			else
				return true;
		}

		function attachment_image_attributes ($attr = array(), $attachment = array(), $size = false ){
			global $post;
			$id = $post->ID;
			if( $id == 0 )
				return $attr;
			if ( $this->uses_amazon_featured( $id ) === false )
				return $attr;
			unset($attr['srcset'],$attr['sizes']);
			$image_url = $this->get_featured_thumbnail_src( $id );
			$alt = get_post_meta( $id, '_amazon_featured_alt', true );
			$alt = $alt == '' && isset( $attr['alt'] ) ? $attr['alt'] : $alt ;
			$attr['src'] 	= $image_url;
			$attr['class'] 	= $attr['class'].' amazon-featured-image';
			$attr['alt']	= $alt;
			$attr['style']	= 'width: auto;';
			
			return $attr;
		}

		function get_featured_thumbnail_src( $id, $called_on_save = false ) {
			if(is_admin())
				return false;
			$this->unfilter_override_thumbnail_id();
			$regular_feat_image = get_post_meta( $id, '_thumbnail_id', true );
			$this->filter_override_thumbnail_id();
			if ( isset( $regular_feat_image ) && $regular_feat_image > 0 )
				return false;
			$image_url = get_post_meta( $id, $this->featured_url(), true );
			if($image_url != '')
				return $image_url;
			return false;
		}

		function override_thumbnail_check( $null, $object_id, $meta_key ) {
			$result = null;
			if ( '_thumbnail_id' === $meta_key ) {
				if ( $this->uses_amazon_featured( $object_id ) ){
					$tempid = get_option('amazon_product_dummy_featured_image_ID', '-1' );
					//need to get an attachment ID that is valid to fake it out.
					if( $tempid == '-1' || !get_post_status($tempid) ){
						$rndid = -1;
						$postschk = new WP_Query( array('post_type' => 'attachment', 'posts_per_page' => 1, 'post_status' => 'inherit' ) );
						if(is_object($postschk) && !empty($postschk->posts)){
							$rndid = $postschk->posts[0]->ID;
							update_option('amazon_product_dummy_featured_image_ID', $rndid  );
						}
						$result = $rndid ;
					}else{
						$result = $tempid;
					}
				}
			}
			return $result;
		}

		function filter_override_thumbnail_id() {
			foreach ( get_post_types() as $post_type ) {
				add_filter( "get_${post_type}_metadata", array( $this, 'override_thumbnail_check'), 10, 3 );
			}
		}
		function unfilter_override_thumbnail_id() {
			foreach ( get_post_types() as $post_type ) {
				remove_filter( "get_${post_type}_metadata", array( $this, 'override_thumbnail_check'), 10 );
			}
		}
	}

if( (bool) get_option('apipp_product_featured_image', false) === true){
	new Amazon_Product_Featured_Image();
}