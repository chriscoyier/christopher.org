=== Smart Code Escape ===
Contributors: danielpataki
Donate link: https://gratipay.com/danielpataki/
Tags: content, code, escaping
Requires at least: 1.2.1
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A tiny plugin for WordPress which converts greater than signs, smaller than signs and ampersands to html entities within pre tags.

== Description ==

A tiny plugin for WordPress which converts greater than signs, smaller than signs and ampersands to html entities within pre tags. It supports semantic syntax you would use when using the Prism highlighter for example. If a code tag sits immediately within a pre tag it will not be escaped.

Code is escaped before it is displayed which gives you easy editing access in the backend as you will always see the unescaped version.

== Installation ==

Since this a WordPress plugin just search for "Smart Code Escape" in the plugins section in the admin and install it from there. If you would rather install it manually you can do the following:

1. Download and extract the plugin
3. Copy the resulting directory into your WordPress plugins directory
4. Go to your WordPress admin and activate the plugin.

== Frequently Asked Questions ==

= Where are the settings? =

There are no settings at the moment. The plugin will escape your code for you within pre tags, not setup needed.

= How should I format my code? =

The whole reason for the plugin is so you don't need to add any special formatting. You can type any characters you like, they will show up as you'd expect in the admin and will be escaped on the front-end.

= Does the plugin work with syntax highlighters =

I have only tested it with Prism but I imagine it should work with a number of others as well. Prism requires you to create a semantically correct markup. The plugin does not escape a code tag if it is directly within a pre tag for this reason.

== Upgrade Notice ==

Nothing to upgrade just yet!

== Screenshots ==

1. Some code written in the admin. Notice, no need to encode entities
2. This is how it is shown on the front-end. The initial code tag is not encoded for use in Prism.

== Changelog ==

= 1.1.0 (2015-04-20) =
* WordPress 4.2 compatibility check

= 1.0.1 =
* Added Smaller Icon

= 1.0 =
* Initial version
