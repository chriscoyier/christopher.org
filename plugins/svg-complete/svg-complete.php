<?php
/**
 * Plugin Name: SVG Complete
 * Plugin URI: http://www.automatic-rock.nl/svg-zoom-pan-drag-wordpress/
 * Description: Upload SVG files to your WordPress and add them to your post or pages via the SVG button in your visual editor. Here you can even select to have zoom and pan (mouse drag) on your svg, useful for maps for example. You can also set image size, a fallback image and an alt text for SEO
 * Version: 1.0.2
 *
 * Author: Automatic Rock
 * Author URI: http://www.automatic-rock.nl
 *
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/*  2015 Benno de Wit  (email : bj@automatic-rock.nl)
 
    Thanks to:  http://www.jenskuerschner.de/svg-images-with-png-fallback-in-wordpress/
                https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
                https://github.com/tattivitorino/wp_custom_button_shortcode/blob/master/js/views-handler.js
                http://mikejolley.com/2013/12/sensible-script-enqueuing-shortcodes/
                http://www.petercollingridge.co.uk/interactive-svg-components/pan-and-zoom-control
                http://kov.com/Features/Schematics
                http://www.alessioatzeni.com/blog/simple-tooltip-with-jquery-only-text/
                http://generatewp.com/take-shortcodes-ultimate-level/
                and the nice people at http://stackoverflow.com/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die("No script kiddies please!");


// Add functionality to upload SVG files in WordPress
function svg_upload ($svg_mime) {
    $svg_mime['svg'] = 'image/svg+xml';
    return $svg_mime;
}
add_filter( 'upload_mimes', 'svg_upload' );

$svgcounter=0;

// Add a shortcode to implement them with a fallback solution in any frontend editor
function generate_svg_code($atts, $content = null) {
    $svga;
    $svgcode;
    $svgAlignPan;
    $svgAlignZoom;
    $svgcapclass;
    global $svgcounter;
    
    $svgcounter++;
    $svga = shortcode_atts( array(
    'width' => '',
    'height' => '',
    'svg_path' => '',
    'alt_path' => '',
    'alt' => '',
    'class' => '',
    'zoompan' => '', 
    'align' => '',
    ), $atts ); 
    
    if($content!='') $svgcapclass= 'wp-caption'; else $svgcapclass= 'svgnocaption';         // If svg-img has caption add default wordpress class
    if(empty($svga['height'])) $svga['height']=100;                             // Default height is 100px
    if(empty($svga['width'])) $svga['width']=100;                               // Default width is 100px
    
    if($svga['width']<620){                                                     // Rearange zoom and pan buttons for small images
      $svgAlignPan= 'margin-left: auto;margin-right: auto;display: block;';
      $svgAlignZoom= 'margin-left: auto;margin-right: auto;display: block;'; 
    } else {
      $svgAlignPan= 'float: left;';
      $svgAlignZoom= 'float: right;'; 
    }
    $svgcode= '<div class="'.$svga['align'].' '.$svga['class'].' '.$svgcapclass.'" style="width:'.$svga['width'].'px;">';       // Set classes and width of wrapping div
    if($svga['zoompan']== 'zoom_none') $svgcode.= '<div id="SVGile'.$svgcounter.'" class="svgNoZoomContainer">';
    else $svgcode.= '<div id="SVGfile'.$svgcounter.'" class="svgcontainer">';            
    if($svga['zoompan']== 'zoom_button') { 
       $svgcode.= '<div class="svgControl" style="text-align:center;">
                        <div class="svgControlLeft" style="'.$svgAlignPan.'">
                            <div style="display: inline-block;">
                                <input type="button" value="Pan Left" class="panLeftButton" >
                                <input type="button" value="Pan Right" class="panRightButton">
                            </div>
                            <div style="display: inline-block;">
                                <input type="button" value="Pan Up" class="panUpButton">
                                <input type="button" value="Pan Down" class="panDownButton">
                            </div>
                        </div>
                        <div class="svgControlRight" style="'.$svgAlignZoom.'"> 
                            <div style="display: inline-block;">
                                <input type="button" value="Zoom In" class="zoomInButton">
                                <input type="button" value="Zoom Out" class="zoomOutButton">
                            </div>
                            <div style="display: inline-block;">    
                                <input type="button" value="View All" class="viewAllButton">
                                <input type="button" value="Print" id="printButton" class="svgLeftButton" onclick="window.open(\''.htmlentities($svga['svg_path'], ENT_QUOTES).'\',\'_blank\')" />
                            </div>
                        </div>    
                    </div>';
    }
    $svgcode.= '<div class="svgimg SVGfile'.$svgcounter.'" style="text-align:center; overflow:hidden; width:100%;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$svga['width'].'" height="'.$svga['height'].'" viewBox="0 0 '.$svga['width'].' '.$svga['height'].'">
                        <g id="transmatrix" class="SVGfile'.$svgcounter.'" transform="matrix(1,0,0,1,0,0)">
                            <image xlink:href="'.htmlentities($svga['svg_path'], ENT_QUOTES).'" src="'.htmlentities($svga['alt_path'], ENT_QUOTES).'" width="'.$svga['width'].'" height="'.$svga['height'].'" alt="'.htmlentities($svga['alt'], ENT_QUOTES).'"  preserveAspectRatio="xMinYMin slice"/>
                        </g>
                    </svg>
                </div>';
          if($content!=''){
              $svgcode.= '<p class="wp-caption-text wp-caption svg-caption">'.$content.'</p>';
          }
       $svgcode.= '</div></div>';
    if($svga['zoompan']== 'zoom_mouse' || $svga['zoompan']== 'zoom_button') wp_enqueue_script( 'zoompan' ); // Only load zoompan script if needed
    return $svgcode;
    
 /*  */   
    
}
add_shortcode('svg-complete', 'generate_svg_code');


add_action('admin_head', 'svg_complete_add_tc_button');

function svg_complete_add_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "svg_complete_add_tinymce_plugin");
        add_filter('mce_buttons', 'svg_complete_register_tc_button');
    }
}

function svg_complete_add_tinymce_plugin($plugin_array) {
    $plugin_array['svg_complete_tc_button'] = plugins_url( '/svg-complete.js', __FILE__ ); 
    return $plugin_array;
}

function svg_complete_register_tc_button($buttons) {
   array_push($buttons, "svg_complete_tc_button");
   return $buttons;
}

add_action( 'wp_enqueue_scripts', 'register_zoompan_script' );
	
function register_zoompan_script() {
    wp_register_script( 'zoompan', plugins_url( '/zoompan.js' , __FILE__ ), array('jquery'), '1.0.0', true );
}

?>