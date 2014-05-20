=== Featured Image Caption ===
Contributors: cconover
Donate link: https://christiaanconover.com/code/wp-featured-image-caption#donate
Tags: buffer, bufferapp, sharing, social, twitter, facebook, linkedin
Requires at least: 2.7
Tested up to: 3.9
Stable tag: 0.1.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom post meta field and meta box to set a caption for the featured image of a post

== Description ==

Featured Image Caption is a WordPress plugin that creates a custom post meta field and meta box to set a caption for the featured image of a post. This data is saved to the post meta, and can be added to your theme using a function.

== Installation ==

1. Upload the `featured-image-caption` directory to your site's `plugins` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Add the following line to your theme files (within The Loop) to display the caption: `<?php echo cc_featured_image_caption(); ?>`

== Frequently Asked Questions ==

= How do I set the caption for the featured image? =
The meta box is added in the side column of the Edit Post or Edit Page screen. Add your caption inside the text area within the meta box.

= Are there any options for this plugin? =
There aren't any options for this plugin. When you activate it, the meta box and theme function are added.

= How do I customize the formatting of the caption? =
The caption is contained inside a <span> tag with the class "cc-featured-image-caption" which you can use to apply CSS to the caption.

== Screenshots ==

1. Featured Image Caption meta box below the Featured Image meta box

== Upgrade Notice ==

= 0.1.0 =
Initial release.

== Changelog ==

= 0.1.1 =
Added <span> around the rendered caption to allow for CSS formatting.

= 0.1.0 =
Initial release.
