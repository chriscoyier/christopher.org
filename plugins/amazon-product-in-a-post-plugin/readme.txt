=== Amazon Product in a Post Plugin ===
Plugin Name: Amazon Product in a Post
Contributors: flowdee, kryptonitewp
Tags: Amazon, Amazon Associate, Amazon product, Amazon Affiliate
Donate link: https://donate.flowdee.de/
Requires at least: 5.0
Tested up to: 5.6.1
Requires PHP: 5.6.0
Stable tag: 5.2.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add formatted Amazon Products to any page or post using the Amazon Product Advertising API.

== Description ==
The Amazon Product In a Post plugin can be used to quickly add formatted Amazon Products/Items to any page or post by using just the Amazon product ASIN.

## NOTICE ##
**Uses the New Amazon Product Advertising API V5**
As of March 9th 2020, Amazon requires the API version 5.0 to be used. This version has a more streamlined response so some data is no longer available. **Most noticeably the Product Description and Customer Reviews.**

**If you have an Affiliate account already, you will need to Migrate your API Keys or generate a new set of keys before the plugin will work correctly.**
This will also require you to add the new keys in the plugin settings.

Amazon also requires you have your affiliate account fully approved before they will grant you access to the Amazon Product Advertising API. This means that you many not be able to use the plugin immediately until you receive access to the API.

If you do not have an Amazon Affiliate Account or Amazon Product Advertising API Keys already, it is free and not too extremely difficult to set up (takes about 15 min. total for both). Once you have an account, install the plugin, enter your Amazon Associate ID and API keys in the Plugin Settings page and you are ready to start adding products to your website!

## How it Works: ##
The plugin uses the Amazon Product Advertising API to get product information from Amazon to display on your site. To use the plugin, you must have:
1. An Amazon Affiliate Account
2. Amazon Product Advertising API keys (generated after March 2019).

**Amazon's Product Advertising API Terms of Use requires you have an AWS Access Key ID and Secret Access Key of your own to use.** See **Plugin FAQs** or **Getting Started** page for links to sign up.

## With this plugin you can: ##
* Add any Amazon product or item to an existing Post (or Page) or custom Post Type.
* Monetize your blog posts with custom Amazon Product and add your own Reviews, descriptions or any other thing you would normally want to add to a post — and still have the Amazon product there.
* Easily add only the items that are right for your site.
* Add the product to the TOP of the post content, the BOTTOM of the post content, or make the post content become part of the product layout (see screenshots for examples)
* You can add as many products as you want to any existing page/post content using Shortcodes — see Installation or FAQ page for details.

## Features: ##
* Preformmated display of Amazon Products for easy integration (with various settings)
* Shortcodes for Adding Products, Product Elements, Product Grids, etc.
* Add new Page/Post at the same time with "New Amazon Post" feature
* New Gutenberg Blocks for Amazon Products, Element, Searches and Grid Layouts.
* You can add Product Grid Layouts, Single Product Layouts or Individual Product Elements to Pages/Posts
* Option to create multiple Pages/Posts from ASINs with Amazon product data auto populated (no need to add separate products)
* Products can use the Standard product page URL or "Add to Cart" URL (i.e., 90 day Cookie Conversions)
* Links can be set to open in a new window/tab
* Custom styling options (via CSS in the settings)
* Lightbox functionality for larger image popups and additional images (can be disabled).
* Adjustable Caching of product data to limit API Calls
* Test feature for verifying Amazon settings are properly set up
* Debugging Information for troubleshooting issues

## Known Issues: ##
* If you just applied for Amazon Product Advertising API Usage and were approved, it can still take up to several weeks before you have access to the API.
* The more products you add, the more overhead there is for API requests. The caching system tries to optimize the number of requests by grouping them, instead of doing them individually.
* **Amazon OneLink** scripts may cause some links not to work correctly by over-riding the standard product link. If you use OneLink scripts on your site and still want to add products, try to limit the Amazon OneLink scripts to pages where you will not include products.
* **Amazon Ads** may also cause some links not to works correctly. This is the same as for Amazon OneLink.
* **Some Products or Product data is not available via the Amazon Product Advertising API.** When this happens, the product will not be displayed, or the requested element will not be displayed in the product output.
* You must have at least twp referral sales every 30 days or you will lose your Amazon Product Advertising Account. If this happens, Amazon will deactivate your Amazon Product Advertising Account and the plugin will no longer display products. You can simply re-sign up for access and change your Amazon Keys in the settings, and they will return (products shortcodes and settings are not deleted, they just cannot be displayed).

## Support ##
* Browse [issue tracker](https://github.com/flowdee/amazon-product-in-a-post-plugin/issues) on GitHub
* [Follow us on Twitter](https://twitter.com/kryptonitewp) to stay in contact and informed about updates

== Frequently Asked Questions ==
See the Installation Page for details on setting up the Products. There is a dynamic FAQs feed in the plugin that will allow for adding new FAQs as they come up. More detailed FAQs will come as questions and solutions arise.

= Does this plugin use the New v5 of the Product Advertising API? =
* Yes — As of the plugin version 5.0, the plugin uses version 5.0 of the PA API.

= Do I have to use the version 5 API? Can't I just still use version 4?? =
* Amazon has officially retired the PA API Version 4, so it cannot be used.

= Will you support Blocks for New Gutenberg Editor? =
* Yes! We are finalizing them now and hope to roll them out prior to WordPress 5.0 Rollout.
* Our goal is to have blocks for all shortcodes (amazon-grid, amazon-elements, amazon-search and amazonproduct) and the main product (non-shortcode, currently the 'meta-box' default option) with the ability to make easy customization to the product layout/elements. Our early tests are looking/working great!
* Anyone still wanting to use the Classic Editor will be able to still use shortcodes like they currently do, and you can use shortcodes in Gutenberg by using the core 'shortcode' block that is included with the editor.

= What do I need to do to get started? =
* You need to have an Amazon Affiliate Account and you have to register for the Amazon Product Advertising API to get a set of access Keys to put in the plugin. That allows the plugin to make calls to Amazon to get Product Data and pricing.
* Version 3.5.1+ has a Getting Started page that helps you through the Amazon Signup for your Amazon Access Keys. Install the plugin and go to the Getting Started Page in the plugin menu for more information.

= Great Plugin! How do I donate to the cause? =
* Excellent question! The plugin is provided free to the public — you can use it however you like — where ever you like — you can even change it however you like. Should you decide that the plugin has been a great help and want to donate to our plugin development fund, you may do so [here](https://donate.flowdee.de/ "here").

== Installation ==
After you install the plugin, you need to set up your Amazon Affiliate/Associate ID in the Options panel located in the AMAZON PRODUCT menu under PLUGIN SETTING.

An AWS Access Key ID and Secret Access Key REQUIRED. You MUST use the ROOT keys and not the User or Group Keys that Amazon recommends. See the plugin **Getting Started** page for additional setup instructions.

No additional adjustments are needed unless you want to configure your own CSS styles. Styles can be adjusted or removed in the Options Panel as well.

== Screenshots ==
1. Amazon Products displayed on a page with the Post Content as part of the product (option 3 when setting up product)
2. Custom Product Layout. Using amazon-elements shortcode for custom look. (with adjusted default plugin styles)
3. Sample Basic Shortcode Usage Layout. Note Kindle Products Message. (Amazon does not add Kindle prices in the API)
4. Amazon Quick Product Post option. Adds basic information needed for a product. Automatically creates corresponding Post/Page/Post Type for product
5. Plugin Menu (updated and renamed in 3.5.1). (Previously called Amazon PIP)
6. Shortcodes to allow multiple products in content — also can be used to add any product data if you want to layout your own page.
7. Plugin Options Panel part 1.
8. Plugin Options Panel part 2.
9. Admin post/page edit box used for adding a product.
10. Shortcode Usage Page. Outlines how to use the shortcodes for different setups.
11. Getting Started Page. Walks you through how to get and set up the Amazon Product API keys for the plugin.
12. FAQs and Help Page. FAQs are on a feed to make it easy for us to add new FAQs if there are common problems.
13. Amazon Cache Page. Allows you to see and delete cached product data.

== Changelog ==

= 5.2.2 (22th February 2021) =
* Tweak: Set product title as default ALT tag for images
* Fix: `hide_image` and `hide_lg_img_text` didn't work properly
* WordPress v5.6.1 compatibility

= 5.2.1 (20th September 2020) =
* Fix: Plugin settings didn't update properly

= 5.2.0 (4th September 2020) =
* New: Added support for the following Amazon stores: Japan, Mexico, Netherlands, Saudi Arabia, Singapore & United Arab Emirates
* Fix: Javascript error "Uncaught TypeError: $(...).live is not a function"
* WordPress v5.5.1 compatibility

= 5.1.1 (2nd June 2020) =
* Tweak: Optimized database table character set / collation for plugin installation
* Fix: Button image didn't load properly in some cases
* Fix: PHP notice "Undefined variable: $aws_plugin_version"
* Updated plugin author information

= 5.1.0 (26th May 2020) =
* **Added:** Added defined constants to make updating developer information easier. (5/23/2020)
* **Updated:** Added Flowdee as contributor as he will be taking over future updates. (5/25/2020)
* **Updated:** Updated FAQs feed URL to a new Feed URL location. (5/25/2020)
* **Updated:** Change to default styles to fix WooCommerce 'product' layout issues (a few styles had the same names and were conflicting). (5/20/2020)
* **bug fix:** Fixed some style parameters that were incorrect (i.e., In Stock color). (5/14/2020)
* **bug fix:** Fixed some undefined variable notices in the Test functions. (5/20/2020)
* **bug fix:** Fixed cart URL not working on Amazon product shortcode and default page product. (5/22/2020)
* **bug fix:** Fixed templates "cart URL" for same reason as above. (5/22/2020)
* **bug fix:** Fixed debug output when sending to Developers. If the admin email address was not a real address, we could not respond to the user. Changed reply to email to current user who sends debug request. (5/25/2020)
* **Translations:** Updated Translations to the latest version. (05/25/2020)

= 5.0.9 =
* **Added:** Added and edit and delete option for custom buttons. (4/21/2020)
* **Updated:** Added 'columns' parameter to search shortcode/block for when 'grid' template is used. (4/20/2020)
* **Updated:** Changed deprecated 'contextual_help' filter to use 'current_screen' filter. (4/20/2020)
* **Updated:** Updated Custom Button feature to require Dropdown Field name so it shows in list (cannot be empty). (4/20/2020)
* **bug fix:** Fixed products showing "0" price when products was out of stock. (04/18/2020)
* **bug fix:** Fixed "Add to cart" functionality not working in Search shortcode. (04/18/2020)
* **bug fix:** Fixed Custom Button Styles output CSS (04/19/2020)
* **bug fix:** Fixed some unknown index errors for non-defined array index calls. (04/19/2020)
* **bug fix:** Fixed repeated looping output of Custom Styles. This was outputting the CSS for each shotcode because it was enquing styles in the shortcode class. Added a check to see if already enqueued.  (04/21/2020)
* **bug fix:** Fixed ASIN API calls error when there were line breaks in a set of ASINs. (04/14/2020)

= 5.0.8 =
* **Updated:** Fix Price display in amazonproducts plugin to show by default. (3/30/2020)

= 5.0.7 =
* **Updated:** Registered meta fields for when Gutenberg Editor is in use (was not updating meta fields with blocks). (3/26/2020)
* **Added:** Added title replace option to Amazon Grid and Amazon Elements shortcodes (used just like Amazon Product shortcode title replace feature). (3/28/2020)
* **Added:** Added Template option to main Amazon Product Meta add screen. (3/28/2020)
* **Added:** Added Error display to Cache page to show easily when there is an error in a request to Amazon. (3/27/2020)
* **Added:** Added new Custom Button functionality to add new your own button layouts. (3/28/2020)
* **bug fix:** Fixed '%' turning into long encoded string — changed $wpdb-&gt;_escape to $wpdb-&gt;esc_like to prevent '%' &amp; '_' encoding in the Amazon Responses. (3/26/2020)
* **bug fix:** Fixed caching function that was not displaying titles after error in JSON. (3/29/2020)

= 5.0.6 =
* **bug fix:** Fixed Amazon Elements output and button URLs. (3/20/2020)

= 5.0.5 =
* **bug fix:** Fixed Templates Not displaying Correctly. (3/18/2020)
* **Added:** Added new "Amazon Layout" template for Products block (displays like Amazon Scratchpad layout). (3/18/2020)

= 5.0.4 =
* **bug fix:** Fixed HiRes image result (PA API 5 does not have HiRes Image, so replaced with Large Image of present). (3/16/2020)
* **bug fix:** Fixed Search results block issues that showed the same content in multiple search blocks on the same page. (3/15/2020)
* **bug fix:** Adjustments to block items to fix wrong type errors. (3/15/2020)
* **bug fix:** Error messages in API response updated. (3/14/2020)
* **bug fix:** Fixed Fatal Error in non static class call. (3/12/2020)
* **bug fix:** Fixed Undefined Variable/Notice Warnings — 67 warnings fixed. (3/14/2020)

= 5.0.3 =
* **bug fix:** Fixed Warning — Illegal string offset 'Content'. (3/12/2020)

= 5.0.2 =
* **bug fix:** Fixed Misspelled option 'detele_option' which caused a fatal error. (3/11/2020)

= 5.0.1 =
* **bug fix:** Fixed "Features" parameter from displaying "Array". (3/10/2020)
* **bug fix:** Changed so 'gallery' field can also use 'imagesets' as it is supposed to. (3/10/2020)
* **bug fix:** Fixed title not displaying correctly when "hide binding" not on it the settings. (3/10/2020)


= 5.0.0 =
* **Release Notes:** Major Update to API &amp; Core functionality.
* **TODO:** More to come... we will update the change log with the complete list of changes in the next few days. (3/10/2020)
* **TODO:** We will be updating help text and usage information soon. We needed to get the new API out ASAP, so some items are still not updated. (3/10/2020)
* **New:** Added PA API Version 5 API functionality. (3/2/2020)
* **Added:** Removed many Response Fields to be compliant with new v5 data responses. (2/18/2020)
* **Added:** Added Gutenberg Blocks for Search, Grid, Products and Elements. (2/18/2020)
* **Added:** Added new tab under Shortcode Usage menu for Gutenberg Blocks. (2/18/2020)
* **Added:** Added Database table creation check for instances when the table was not created, it will try to create it when accessing the settings page.(12/05/2019)
* **Update:** Re-Mapped API Response data to new V5 API data responses. (2/7/2020)
* **Update:** Moved all API calls into one class call to streamline the process. (2/7/2020)
* **Update:** Updated Menu label to be "Shortcodes/Blocks" from "Shortcode Usage". (2/18/2020)
* **Update:** Updated options list for removing all options on Plugin uninstall. (2/18/2020)
* **Update:** Updated shortcode parameters for most shortcodes to make use of new Gutenberg block attributes. (2/18/2020)
* **Update:** Updated Test functionality on the settings page to be more accurate and display more concise messages. (2/18/2020)
* **Bug Fix:** Fixed Improper cache save on some API calls. (12/05/2019)
* **Bug Fix:** Fixed shortcode parameters filter in all shortcodes. (12/05/2019)
* **Bug Fix:** Database amazon cache table index too long error prevented table creation in some older MySQL versions. (12/02/2019)
* **Bug Fix:** Fixed "Show On Single Page" bug where products were still showing on list/archive pages even though selected.  (12/02/2019)

= 4.0.3.4 =
* **Added:** Added some additional code documentation for some functions/variable (10/03/2018)
* **Update:** Fixed No Image URL in some shortcodes when image is not provided. (10/10/2018)
* **Bug Fix:** Database creation fix for some versions of MariaDB where index column length was too long. (10/05/2018)
* **Bug Fix:** Fixed Item Search Cache String loading.  (10/06/2018)

= 4.0.3.3 =
* **Release Notes:** This is more of a 'clean-up' update to fix little things, removed unused items and add helper items in anticipation of Gutenberg update.
* **Removed:** Removed old styles that were no longer in use. (9/5/2018)
* **Removed:** Removed old scripts in amazon-admin.js that were no longer in use. (9/5/2018)
* **Removed:** Removed Default styles option from database — no longer needed. (9/5/2018)
* **Added:** Added new styles for HTML buttons and grid items. (9/7/2018)
* **Added:** Added default button style to amazon-grid.css. (9/7/2018)
* **Added:** Added HiResImage to result array for access to hires images if available. (9/8/2018)
* **Added:** Added Filters to result array images: 'amazon-product-main-image-sm', 'amazon-product-main-image-md', 'amazon-product-main-image-lg', 'amazon-product-main-image-hi'. (9/8/2018)
* **Added:** Added libxml_use_internal_errors(true) to XML parsing for better error handling. (9/8/2018)
* **Added:** Added new HTML button replacement to all shortcodes. See Button Settings in plugin settings for usage. (9/10/2018)
* **Added:** Added some missing options to remove on uninstall when 'remove all traces' is checked. (9/10/2018)
* **Update:** Updated Test API call with defined constants instead of option calls. (9/7/2018)
* **Update:** Updated Test API call keywords and call to random product and page for different results. (9/7/2018)
* **Update:** Updated Test API call Response Group because Large was not needed. (9/7/2018)
* **Update:** Updated Test API call Debug checks and Error notice outputs on failure. (9/7/2018)
* **Update:** Updated script enqueue order so custom styles load after other plugin styles. (9/9/2018)
* **Bug Fix:** Fixed internal use filters for adding shortcode tabs and content to Shortcode Usage Page. (9/10/2018)
* **Bug Fix:** Fixed shortcode product filters — were filtering entire result array each time instead of just current product.  (9/10/2018)

= 4.0.3.2 =
* **Bug Fix:** Fixed some translations that were not correctly set up. (9/2/2018)
* **Bug Fix:** Fixed double filter application on some product label elements. (9/2/2018)
* **Bug Fix:** Fixed admin style/js enqueue on translation loading. (9/2/2018)
* **Bug Fix:** Fixed translations loading issue that was preventing languages from loading. (9/2/2018)
* **Removed:** Removed some JavaScript debug console logging that was still present but not needed. (9/2/2018)
* **Admin Style Change:** Fixed CSS layout on shortcode help pages. (9/2/2018)

= 4.0.3.1 =
* **Update:** Removed debug code that made it into production version. (8/29/2018)

= 4.0.3 =
* **Bug Fix:** Fixed many Undefined Variables and Undefined Index Warnings/Notices. (8/29/2018)
* **Bug Fix:** Translation file was not being loaded correctly. Made adjustment to hopefully fix issues. (8/22/2018)
* **Bug Fix:** Fix "Empty Needle" Warning in amazon-product-in-a-post-aws-signed-request.php when string check was blank. (08/20/2018)
* **Bug Fix:** Fix to Cache Ahead functionality. Was calling additional requests in some cases when it should have always used available cache. (08/20/2018)
* **Bug Fix:** Fix to shortcode locale parameter usage. Was not changing when a different locale was added via shortcode. (08/20/2018)
* **Update:** Translation files Updated. (08/30/2018)
* **Update:** Change to add alt text parameter to Main Image. Also includes new 'appip_alt_text_main_img' filter to change it if you do not want the default of 'Buy Now'. (08/22/2018)
* **Update:** Change to add alt text parameter to Additional Images. Also includes new 'appip_alt_text_gallery_img' filter to change it if you do not want the default of 'Img - [ASIN]'. (08/22/2018)
* **Update:** Change to add alt text parameter to button image and Additional Images. Also includes new 'appip_amazon_button_alt_text' filter to change it if you do not want the default of 'buy now'. (08/22/2018)
* **Addition:** Added Subscription Price/length for amazon-grid shortcode when item is a Magazine or other subscription (i.e., Kindle Subscriptions, etc.). Todo — add to other shortcodes. (08/30/2018)
* **Addition:** Added some new elements for Gutenberg (not yet active) in prep from WP 5.0 release. (08/23/2018)
* **Addition:** Added new filters — 'appip_metabox_context', 'appip_metabox_priority', 'appip_meta_posttypes_support' in prep for Gutenberg. See filters-and-hooks.txt for more info. (08/26/2018)

= 4.0.2 =
* **Bug Fix:** Fix to undefined variable '$appip_running_excerpt'. (07/25/2018)
* **Bug Fix:** Fix to uppercase shortcodes 'AMAZONPRODUCT' and 'AMAZONPRODUCTS' not being rendered correctly. (07/25/2018)
* **Bug Fix:** Fix to WP Error, fatal error response in request when transport fails and returns WP_Error object. (08/01/2018)

= 4.0.1 =
* **Bug Fix:** Fix Open in a New Window functionality — not working for amazonproducts shortcode. (07/18/2018)
* **Bug Fix:** Fix to 'nofollow' property in amazon-grid shortcode links. (07/18/2018)
* **Update:** Change to AMP styles — TODO: Add option to remove if desired. (07/19/2018)
* **Update:** WordPress 4.9.7 Compatibility (07/19/2018)

= 4.0.0 =
* **RELEASE:** This is the first Official Release Update since version 3.6.4. (06/01/2018)
* **Removed:** Temporarily Removed Shortcode Editor Button — preparing for blocks with Gutenberg Editor and new Classic Editor button. (05/30/2018)
* **Feature Addition:** Added Setting for Future Addition of Amazon Mobile Popover. Will be fleshed out in upcoming version. (05/29/2018)
* **Feature Addition:** Amazon Featured Image Integration. This is for creating Amazon Products using the quick create method. Documentation to come.
* **Feature Addition:** Added SSL image Support. Should detect https automatically, but to force SSL images, use the option in the advanced settings.
* **Update:** Updated options to allow Amazon Featured Image to be turned on or off. (05/28/2018)
* **Update:** Re-wrote the Debugging features to include more info about user install of WordPres and to allow sending debug info via email directly from settings page. (05/28/2018)
* **Update:** Renamed "Amazon PIP Options" to "Plugin Settings" in menu. (05/31/2018)
* **Update:** Fixed Instances of Developer URL to be https. (05/29/2018)
* **Update:** PHP7 Compatibility Update (04/30/2018)
* **Update:** Changed API Call to use `wp_remote_request` — works more consistently than other methods.
* **Update:** Changed caching mechanism to better make use of 'cache ahead' functions. Reads all ASINs on the page load object prior to trying to load any individual calls so items are cached prior to load in blocks.
* **Update:** WordPress 4.9.4 Compatibility
* **Bug Fix:** Fix issue with Debugging System. (05/28/2018)
* **Bug Fix:** Fix issue with wp_remote call. (04/22/2018)
* **Bug Fix/Update:** Fixed image calls. No longer need old method to get images — does not work well with SSL images anyway.
* **Bug Fix:** Fix Content and Title creation on Amazon Post creation. Should work better and more consistently.
* **Bug Fix:** Fixed CLEAN shortcode field parameter calls (i.e., 'title_clean') so they are more consistant.
* **Bug Fix:** Fix call to CURL function in some cases CURL lib has an SSL bug and needs additional settings.
* **Translations:** Updated English Translations and added a few settings related translations to French and Spanish. (05/29/2018)

= 3.8.2 =
* **Bug Fix:** Fix issue with wp_remote call. (04/22/2018)
* **Update:** PHP7.2 Compatibility Update (04/30/2018)

= 3.8.1 =
* **Feature Addition:** Amazon Featured Image Integration. This is for creating Amazon Products using the quick create method. Documentation to come.
* **Feature Addition:** Added SSL image Support. Should detect HTTPS automatically, but to force SSL images, use the option in the advanced settings.
* **Update:** Changed API Call to use `wp_remote_request` — works more consistently than other methods.
* **Update:** Changed caching mechanism to better make use of 'cache ahead' functions. Reads all ASINs on the page load object prior to trying to load any individual calls so items are cached prior to load in blocks.
* **Bug Fix/Update:** Fixed image calls. No longer need old method to get images — does not work well with SSL images anyway.
* **Bug Fix:** Fix Content and Title creation on Amazon Post creation. Should work better and more consistently.
* **Bug Fix:** Fixed CLEAN shortcode field parameter calls (i.e., 'title_clean') so they are more consistent.

= 3.8.0 =
* **Bug Fix:** Fix call to CURL function in some cases CURL lib has an SSL bug and needs additional settings.

= 3.7.0 =
* **Update:** WordPress 4.9.4 Compatibility

= 3.6.4 =
* **Bug Fix:** Fix call to CURL function.

= 3.6.3 =
* **Bug Fix:** Fix bugs in Use Cart URL for shortcodes.
* **Bug Fix:** Fix Displaying "blank" products when no product data is returned.
* **Bug Fix:** Fix debugging data to be more accurate when there is an error returned.
* **Update:** plugin now clears product cache whenever the options are updated for the plugin — to prevent old data from being mis-displayed after update.
* **Update:** updated the plugin styles to make element wrapper a block element.
* **Update:** modified the `amazon-element(s)` shortcode to allow some 'clean' fields that are not wrapped or styled. For example — 'title_clean' will return just title name and not the fully tagged link and title name.
The current clean fields are:
Returns Plain Text Only: 'title_clean', 'desc_clean', 'description_clean', 'price_clean', 'new-price_clean', 'features_clean'.
Return URL only: 'image_clean', 'med-image_clean', 'sm-image_clean', 'lg-image_clean', 'full-image_clean', 'large-image-link_clean', 'link_clean', 'customerreviews_clean':

= 3.6.2 =
* **Feature Addition:** No longer limited to 10 ASINs per shortcode or main product. Amazon still limits 10 per request, but the plugin will now split them out into blocks of 10 and perform the requests.
* **Feature Addition:** Added Tabbed options to the Settings page to make it easier to enter settings.
* **Feature Addition:** Added 'Split ASINs' option to 'New Amazon Post' creation page. You can add up to 10 ASINs (comma separated) and if checked, will create corresponding posts for each ASIN so you do not need to create individual ones.
* **Feature Addition:** Added 'amazon-product-search' shortcode to show search results. Takes parameters for 'keywords', 'search_index', and 'item_page' as well some other parameters of 'amazon-elements' shortcode.
Example `[amazon-product-search keywords='disney' search_index='All' item_page='1']`. Will write up more on usage soon.
* **Feature Addition:** Added 'use Add to Cart URL' which changes links to the cart add links to help with 90 day cookie conversion. Select option on settings page.
* **Update:** Updated Menu icon.
* **Update:** Modified Cache functions to include caching for post list pages (e.g., Archive pages, home posts page, etc.). Select 'Cache Ahead?' option on settings page.
* **Bug Fix:** Fixed some issues with the wrong locale URL codes for Brazil and Japan locale API requests.
* **Bug Fix:** Fixed shortcode requests for new Caching System.
* **Bug Fix:** Fix bugs in caching functions that caused Shortcodes and Multiple products to not display for some users.

= 3.6.1 =
* **Feature Addition:** Add Test Setting Feature to the Options Page.
* **Update:** Adjustment to the Signing Method to make sure it was compliant with Amazon change.
* **Update:** Change buttons to comply with Amazon Terms (remove logo, changed out with text instead).
* **Update:** Made some changes to caching function. Still needs some modification to allow multi-post caching for posts pages.

= 3.6.0 =
* Security Vulnerability Fix (for deleting cache and updating Options)
* Set limits on number of images for image set on Variants to 10.

= 3.5.5 =
* Fix Styles not loading (dynamic styles did not have correct content type).
* Added additional Shortcode parameter aliases (show_used, show_list, etc.)
* Fixed 'used_price' shortcode parameter (was not working when used)
* Fixed 'container' and 'container_class' parameters to have default values [for amazon-elements shortcode only] (makes styling easier)
* Added better support for Products with Variant products linked to parent ASIN.
* Updated styles to version 2.0 (from 1.9) to add better styles for responsiveness.
* Added new API KEY Signup instructions — old ones were outdated.

= 3.5.4 =
* Fix "New Amazon Post" functionality dues to misnamed functions between versions.
* Added Options to turn certain features on and off in the MetaBox options.
* Fixed jQuery for admin — add "New Amazon Post" functionality.
* Fixed German Limited Translations issue.
* Updated Caching to not poll the API for any product that is already in the cache — even if it is in a separate call not related to that call.

= 3.5.3 =
* Security fix to prevent SQL injection. Thanks to jamie@jamieakhtar.com for pointing out the vulnerability.
* Changed style loading to be dynamic via ajax call.
* Made styles loadable though theme file if desired by user (so no dynamic load is required).
* Added framework for Contextual Help (help text coming soon)

= 3.5.2 =
* Fix to issue with Add New Amazon Post page not loading.
* Fix to the Shortcode for amazon-elements.
* Added a container parameter and container class parameter to the amazon-elements shortcode to make styling multiple ASINs easier.
* Fixed Readme.txt file for screenshots for WordPress repository.

= 3.5.1 =
* Basic template integration (for future use — or if you are good at hooks and filters and can figure it out on your own — go ahead — the structure is there!)
* Removed traces of Developer Keys at Amazon's request.
* Added Amazon Elements shortcode to add bits and pieces of a product to a post — very handy for making a custom layout.
* Added Amazon Cache Viewer — allows you to manually delete a product cache to initiate a new amazon Call. Caches are stored for 60 minutes and updated as the calls are needed.
* Added Getting Started page to help users set up affiliate and API Key signup. This is becoming more and more complex, so a little help was needed.
* Added Shortcode Help Page to give examples of how to use the shortcodes effectively.
* Added feed driven FAQs page — easier for me to update FAQs on the fly that way.
* Added several Filters and Hooks — will lay them all out in next revision.

= 3.1 to 3.5.0 =
* development versions.

= 3.0 =
* Added New Shortcode [AMAZONPRODUCTS] (instead of [AMAZONPRODUCT=B0084IG8TM]) — old method will still work
* Added Bulk API Call to limit number of calls to API (can use up to 10 ASINs at one time)
* Updated the deprecated function calls
* Increased API return values for use in theme — puts all items in the array now
* Updated styles to include some new elements
* Updated database field for amazoncache table to allow for larger data size of cached XML body (as API can now return up to 10 items at a time)
* Updated aws_request function
* Wrapped generic helper functions in !function_exists wrapper to eliminate conflicts with some other Amazon plugins.
* Updated Install function with styles and database upgrade
* Added amazon icon button to editor to easily add shortcode.
* Added new parameters to shortcode to allow custom additions to any post/page:
	— asin — this is the ASIN or ASINs up to 10 comma separated
	— locale — this is the Amazon locale you want to get the product from, i.e., com, co.uk, fr, etc. default is your plugin setting
	— desc — using 1 shows Amazon description (if available) and 0 hides it — default is 0.
	— features — using 1 shows Amazon Features (if available) and 0 hides it — default is 0.
	— listprice — using 1 shows the list price and 0 hides it — default is 1.
	— partner_id — allows you to add a different parent ID if different for other locale — default is ID in settings.
	— private_key — allows you to add different private key for locale if different — default is private key in settings.
	— public_key — allows you to add a different private key for locale if different — default is public key in settings.
* New Shortcode would be used like this:
	— If you want to add a.com item and you have the same partner id, public key, private key and want the features showing:
	`[AMAZONPRODUCTS asin="B0084IG8TM" features="1" locale="com"]`
	— If you want to add a.com item and you have a different partner id, public key, private key and want the description showing but features not showing:
	`[AMAZONPRODUCTS asin="B0084IG8TM,B005LAIHPE" locale="com" public_key="AKDAJDANJ6OU527HKGXQ" private_key="Ns3FXyeVysc5yjcZwrIV3bhDti/OGyRHEYOWO005" partner_id="wordseen-20"]`
	-If you just want to use your same locale but want 2 items with no list price and features showing:
	`[AMAZONPRODUCTS asin="B0084IG8TM,B005LAIHPE" features="1" listprice="0"]`
	-If you just want 2 products with regular settings:
	`[AMAZONPRODUCTS asin="B0084IG8TM,B005LAIHPE"]`
	-If you want to add text to a product:
	`[AMAZONPRODUCTS asin="B0084IG8TM"]your text can go here![/AMAZONPRODUCTS]`

= 2.0 =
* Added Database for caching API calls (10/20/2010)
* Added Options for Private and Public Keys in the options panel. (10/22/2010)
* Added Options for Complete Removal and Partial Removal of Plugin on Deactivate. (10/24/2010)
* Added new error checks to comply with API changes.
* Added new Display checks to not display anything on error (except an HTML Comment in the code) (10/24/2010)
* Fixed option naming convention to resolve a few issues with previous versions (10/24/2010)
* Fixed some code to resolve headers sent issues. (10/23/2010)
* Modified Style calls to fix issues with earlier versions upgrading to newer version (10/23/2010)
* Updated FAQs (10/24/2010)

= 1.9.1 =
* Fix to WordPress Core location assumption. Caused Problem when WP core was located outside root. (1/3/2010)
* Added German Language. (special thanks to Henri Sequeira for translations). (1/3/2010)

= 1.9 =
* fix to not defined function error. (12/28/2009)

= 1.8 =
* Added Fix for users without encoding functions in PHP4 to be able to use. This may have caused some problems with users on 1.7. (12/21/2009)
* Added Options for Language selection. (12/21/2009)
* Added French Language and buttons (special thanks to Henri Sequeira for translations). (12/21/2009)
* Added Lightbox type effect for "View Larger Image" function.(12/22/2009)
* Modified Style Call to use a more WP friendly method to not rely on a "guess" as to where the core WP files are located. (12/22/2009)
* Fixed Open in new window functionality — was not working 100% of the time. (12/22/2009)

= 1.7 =
* Add Curl option for users that can't use file_get_contents() for some reason or another. (12/1/2009)
* Added Show on Single Page Only option to Options Page.(11/30/2009)
* Added a way to change encoding display of prices from API if funny characters are showing.(12/1/2009)

= 1.6 =
* Added Options to let admin choose if they want to "Hook" into the_excerpt and the_content hooks in Main Options with override on each post/page.(10/3/2009)
* Added Open in a New Window Option (for Amazon button) in Main Options with override on each page/post.(10/3/2009)
* Added "Show Only on Single Page" option to individual post/page options.(10/4/2009)
* Added Shortcode functionality to allow addition of unlimited products in the post/page content.(10/4/2009)
* Added "Quick Fix — Hide Warnings" option in Main Options. Adds ini_set("display_errors", 0) to code to help some admins hide any Warnings if their PHP settings are set to show them.(10/3/2009)
* Fixed Array Merge Warning when item is not an array.(10/3/2009)
* Fixed "This Item not available in your locale" message as to when it actually displays and spelling.(10/3/2009)
* Added Options to let admin add their own Error Messages for Item Not available and Amazon Hidden Price notification.(10/3/2009)
* Updated Default CSS styles to include in Stock and Out of Stock classes and made adjustments to other improper styles. (10/3/2009)

= 1.5 =
* Remove hook to the_excerpt because it could cause problems in themes that only want to show text. (9/17/2009)

= 1.4 =
* Added method to restore default CSS if needed — by deleting all CSS in options panel and saving — default CSS will re-appear in box. (9/16/2009)

= 1.3 =
* Added new feature to be able to adjust or add your own styles. (9/16/2009)

= 1.2 =
* Fix to image call procedure to help with "no image available" issue. (9/15/2009)

= 1.1 =
* Minor Fixes/Spelling Error corrections &amp; code cleanup to prep for WordPress hosting of Plugin. (9/14/2009)

= 1.0 =
* Plugin Release (9/12/2009)

== Upgrade Notice ==

= 5.2.1 (20th September 2020) =
* Plugin settings didn't update properly

= 5.1.1 (2nd June 2020) =
* You can now advertise the following Amazon stores: Japan, Mexico, Netherlands, Saudi Arabia, Singapore & United Arab Emirates

= 5.1.1 (2nd June 2020) =
* Fixed 2 bugs, optimized database table creation and updated plugin author information.

= 5.1.0 =
* 5.1.0 Fixed 4 bugs and updated some functionality.

= 5.0.9 =
* 5.0.9 Fixed 6 bugs and enhanced some previous functionality.

= 5.0.8 =
* 5.0.8 Fixed amazon product shortcode price display.

= 5.0.7 =
* 5.0.7 Fixed Caching, Product Meta saving. Added several features.

= 5.0.6 =
* 5.0.6 Fixed Amazon Elements output and button URLs.

= 5.0.5 =
* 5.0.5 Fixed Templates Not displaying Correctly.

= 5.0.2 =
* 5.0.2 — Fixed Misspelled option 'detele_option' which caused a fatal error.

= 5.0.1 =
* 5.0.1 — Fixed title not displaying correctly and Features Array.

= 4.0.3.4 =
* 4.0.3.4 — Fixed some Search Caching issues and Database table creation code.

= 4.0.3.3 =
* 4.0.3.3 — A 'clean-up' update to fix little things, removed unused items and add helper items in anticipation of Gutenberg update.

= 4.0.3.2 =
* 4.0.3.2 — bug fixes for Translations and other minor fixes.

= 4.0.3.1 =
* 4.0.3.1 — Removed debug code that made it into production version.

= 4.0.3 =
* 4.0.3 - 5 bug fixes including "Undefined Variable" Notices, 4 Updates including Alt text for images, 3 Additions including Gutenberg pre-rollout blocks code (Block should be available in version 4.0.4).
