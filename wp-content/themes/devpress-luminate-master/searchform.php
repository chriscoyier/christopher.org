<?php
/**
 * Search form template
 *
 * @package Luminate
 */
?>

<form role="search" method="get" class="search-form clearfix" action="<?php echo home_url( '/' ); ?>">
	<label>
		<span class="screen-reader-text"><?php _e( 'Search for:', 'luminate' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php _e( 'Search...', 'luminate' ); ?>" value="" name="s" title="<?php _e( 'Search for:', 'luminate' ); ?>" />
	</label>
	<button type="submit" class="search-submit">
		<div class="luminate-icon-search"></div><span class="screen-reader-text"><?php _e( 'Search...', 'luminate' ); ?></span>
	</button>
</form>
