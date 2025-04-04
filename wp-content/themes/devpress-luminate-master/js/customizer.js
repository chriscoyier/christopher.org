/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	wp.customize( 'highlight-color', function( value ) {

		// Create an inline style in the head
		if ( null == document.getElementById( 'luminate-highlight-color' ) ) {
			$('<style id="luminate-highlight-color"></style>').appendTo( "head" );
		}

		// Write contents of inline style
		value.bind( function( to ) {
			$('#luminate-highlight-color').text(" .site-content a, .footer-widgets a { color : " + to + "; } .page-header, #content blockquote { border-left-color : " + to + "; } button, .button, input[type='button'], input[type='reset'], input[type='submit'] { background : " + to + "; }");
		} );
	} );

	wp.customize( 'highlight-hover', function( value ) {

		// Create an inline style in the head
		if ( null == document.getElementById( 'luminate-highlight-hover' ) ) {
			$('<style id="luminate-highlight-hover"></style>').appendTo( "head" );
		}

		// Write contents of inline style
		value.bind( function( to ) {
			$('#luminate-highlight-hover').text(" .site-content a:hover, .footer-widgets a:hover { color : " + to + "; }button:hover, .button, input[type='button']:hvoer, input[type='reset']:hover, input[type='submit']:hover { background : " + to + "; }");
		} );

	} );

} )( jQuery );
