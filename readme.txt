=== Featured Image Caption ===
Contributors: cconover
Donate link: https://christiaanconover.com/code/wp-featured-image-caption#donate
Tags: buffer, bufferapp, sharing, social, twitter, facebook, linkedin
Requires at least: 2.7
Tested up to: 3.9.2
Stable tag: 0.2.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom post meta field and meta box to set a caption for the featured image of a post

== Description ==

Featured Image Caption is a WordPress plugin that creates a custom post meta field and meta box to set a caption for the featured image of a post. This data is saved to the post meta, and can be added to your theme using a function.

== Installation ==

1. Upload the `featured-image-caption` directory to your site's `plugins` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Add the following line to your theme files (within The Loop) to display the caption: `<?php cc_featured_image_caption(); ?>`

== Frequently Asked Questions ==

= How do I set the caption for the featured image? =
The meta box is added in the side column of the Edit Post or Edit Page screen. Add your caption inside the text area within the meta box.

= Are there any options for this plugin? =
There aren't any options for this plugin. When you activate it, the meta box and theme function are added.

= How do I customize the formatting of the caption? =
The caption is contained inside a `<span>` tag with the class `cc-featured-image-caption` which you can use to apply CSS to the caption.

Note: if you add the argument to return the caption, only the caption text is returned and is not encapsulated by a `<span>` tag.

= How do I return the value of the caption without displaying it? =
The `cc_featured_image_caption()` function accepts an argument to determine whether the result is displayed or returned.

To return the value, use the following syntax: `cc_featured_image_caption( false );`

= Is there an easy way for me to check whether a caption has been set in my theme code? =
Use the function `cc_has_featured_image_caption()` to find out whether a caption is set. This function returns `true` if one is set, and `false` if no caption is set.

== Screenshots ==

1. Featured Image Caption meta box below the Featured Image meta box

== Upgrade Notice ==

= 0.2.0 =
SYNTAX CHANGE: The function cc_featured_image_caption() now displays the formatted by default, and optionally can return the text of the caption instead, see the plugin FAQ for details. HTML tags are now supported inside the caption. A new function has been added to check whether a caption is set.

= 0.1.3 =
Fixed check in theme function for whether a caption is set, and how the function handles that information.

= 0.1.0 =
Initial release.

== Changelog ==

= 0.2.0 =
* SYNTAX CHANGE: The theme function cc_featured_image_caption() no longer needs to be used with `echo` but instead defaults to echo. If you'd like to return the result, add `false` as an argument for the function like so: `cc_featured_image_caption( false );`
* Allow HTML tags in the caption. Only permits tags that WordPress allows in post content.
* A new function has been added to check whether a caption is set for the post. Use the function `cc_has_featured_image_caption()` which returns `true` if a caption is set, and `false` if a caption is not set.

= 0.1.3 =
Theme function checks whether the caption data returned to it is false. If false, the theme function also returns false. If not false, the theme function returns a formatted caption string.

= 0.1.2 =
Fixed bug in declaring <span> class.

= 0.1.1 =
Added <span> around the rendered caption to allow for CSS formatting.

= 0.1.0 =
Initial release.
