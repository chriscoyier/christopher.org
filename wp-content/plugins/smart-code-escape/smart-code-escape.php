<?php
/*
 * Plugin Name: Smart Code Escape
 * Plugin URI: https://github.com/danielpataki/Smart-Code-Escape
 * Description: Converts less than, greater than and ampersand characters to their HTML
entities within pre tags before they are output on the page. You will always see the
non-escaped version in the editor, making code easy to modify. It Will not convert code
tags directly within pre tags to support Prism-style highlighting.
 * Version: 1.1
 * Author: Daniel Pataki
 * Author URI: http://danielpataki.com
 * License: GPL v2
 * Licence URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


/* Escape Tags
 *
 * This function escapes code within pre tags. It leaves a code tag in-tact if it is the direct child of the pre tag.
 *
 * @param array $data The matched content from the preg replace
 * @return string The escaped string
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function smart_code_escape_pre( $data ) {
	preg_match('@(<code.*>)(.*)(<\/code>)@isU', $data[2], $matches );
	if( !empty( $matches ) ) {
		return $data[1] . $matches[1] . str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&gt;' ), $matches[2] ) . $matches[3] . $data[3];
	}
	else {
		return $data[1] . str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&gt;' ), $data[2] ) . $data[3];
	}
}



add_filter( 'the_content', 'smart_code_escape_content', 9 );
/* Escape Content
 *
 * This filter is hooked into the_content and initiates the escaping logic
 *
 * @param string $content The original post content
 * @return string The escaped post content
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */
function smart_code_escape_content( $content ) {
	$content = preg_replace_callback('@(<pre.*>)(.*)(<\/pre>)@isU', 'smart_code_escape_pre', $content );
	return $content;
}
