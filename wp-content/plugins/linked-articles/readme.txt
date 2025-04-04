=== Linked Articles ===
Contributors: maximevalette
Donate link: http://maxime.sh/paypal
Tags: links, posts, articles, linked articles, linked posts, linked list, daring fireball, gruber
Requires at least: 3.0
Tested up to: 4.4
Stable tag: 1.2

Easily attach a link to a post. The post permalink is replaced with the shared link and a prefix is added.

== Description ==

Easily attach a link to a post. The post permalink is replaced with the shared link and a prefix is added.

You can custom everything: The title prefix, permalink text and custom field name used for the plugin.

I'm using it on my own blog and I don't have any problem. Please let me know about any bugs or requests.

== Installation ==

1. Upload the `/linked-articles/` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the Linked Articles settings page if you want to custom the settings
4. Edit your theme files to replace your post titles by `<?php linked_articles_title() ?>`

== Frequently Asked Questions ==

= Is the plugin compatible with my other title plugins? =

Of course. The `linked_articles_title()` function uses the final return of `the_title()` function so all the filters created by other extensions still apply.

== Changelog ==

= 1.2 =
* Added an option to have the "prefix" at the end of the title.

= 1.1 =
* Added an option to disable the RSS feed link change.

= 1.0 =
* Initial release

== Roadmap ==

Send your feedback or ideas to maxime@maximevalette.com.
