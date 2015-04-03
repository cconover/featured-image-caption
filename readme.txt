=== Featured Image Caption ===
Contributors: cconover
Donate link: https://christiaanconover.com/code/wp-featured-image-caption?ref=plugin-readme
Tags: image, caption, featured image
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 0.5.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily add and display a caption to the featured image of a post.

== Description ==

Featured Image Caption is a WordPress plugin that creates a custom post meta field and meta box to set a caption for the featured image of a post. This data is saved to the post meta, and can be added to your theme either automatically or using the provided function.

== Installation ==

1. Upload the `featured-image-caption` directory to your site's `plugins` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Review the plugin options found in `Settings` -> `Featured Image Caption`.

== Frequently Asked Questions ==

= How do I set the caption for the featured image? =
The meta box is added in the side column of the Edit Post or Edit Page screen. Add your caption inside the text area within the meta box.

= How do I customize the formatting of the caption? =
By default, the entire caption HTML is wrapped in a `<div>` tag, but this can be toggled in plugin options. If this is enabled, the caption HTML will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
    </div>

If you have source attribution text but no URL, it will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
        <span class="cc-featured-image-caption-source">Attribution text</span>
    </div>

If you have a source attribution URL also, it will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
        <span class="cc-featured-image-caption-source"><a href="http://example.com/">Attribution text</a></span>
    </div>

If you have disabled the `<div>` container, everything inside the `<div>` will be the same but without the `<div>` around it.

= Can I customize where the caption appears on my site? =
By default, the plugin automatically adds the caption immediately after the featured image. You can change this in plugin options.

If you need to customize the placement of the caption, you can disable this option in plugin options, and place the following function in your theme where you would like the caption to appear:

    <?php cc_featured_image_caption(); ?>

= How do I return the value of the caption without displaying it? =
The `cc_featured_image_caption()` function accepts an argument to determine whether the result is displayed or returned.

To return the value, use the following syntax:

    <?php cc_featured_image_caption( false ); ?>

By default, the returned data will be the fully formatted HTML of the caption. If you want the raw array of the caption data, use the following syntax:

    <?php cc_featured_image_caption( false, false ); ?>

== Screenshots ==

1. Featured Image Caption meta box below the Featured Image meta box.

== Upgrade Notice ==

= 0.5.0 =
*** BREAKING CHANGES - READ BEFORE UPGRADING *** The plugin has been completely rewritten to allow the caption to be automatically inserted with the featured image, no theme modifications required. The CSS selectors have changed. PLEASE PLEASE PLEASE read the updated changelog and documentation before upgrading.

= 0.4.1 =
Added the option to have the source link open in a new window.

= 0.4.0 =
Restructured the plugin for performance. Fixed security vulnerability.

= 0.3.3 =
Added support for custom post types.

= 0.3.2 =
Removed image source attribution pre-text.

= 0.3.1 =
Added dedicated fields for image source attribution.

= 0.3.0 =
Added dedicated fields for image source attribution.

= 0.2.0 =
SYNTAX CHANGE: The function cc_featured_image_caption() now displays the formatted caption by default, and optionally can return the text of the caption instead, see the plugin FAQ for details. HTML tags are now supported inside the caption. A new function has been added to check whether a caption is set.

= 0.1.3 =
Fixed check in theme function for whether a caption is set, and how the function handles that information.

= 0.1.0 =
Initial release.

== Changelog ==

= 0.5.0 =
** Major Release / Breaking Changes **
* Plugin can automatically add the caption after the featured image, removing the need to modify theme files. If you are upgrading from a previous option this will be disabled by default, otherwise it is enabled by default.
* Theme function `cc_featured_image_caption()` is not needed if you have automatic caption insertion turned on. If this option is enabled, the function won't display anything. You can, however, still use it to `return` the caption data.
* CSS classes have changed. See the FAQ for information about the new markup and CSS classes.

= 0.4.1 =
* Added the option to have the source link open in a new window. This option is on a post-by-post basis. Thanks to [avluis](https://github.com/cconover/wp-featured-image-caption/issues/9) for the suggestion.
* Every time you save a post, the "new window" setting is saved to your user options as the default for your user for that setting.

= 0.4.0 =
* Restructured the plugin to improve performance.
* Fixed a bug with nonce verification when saving the caption data.

= 0.3.3 =
Added support for custom post types. Thanks to [anlutro](https://github.com/cconover/wp-featured-image-caption/pull/6) for the contribution.

= 0.3.2 =
Removed image source attribution pre-text.

= 0.3.1 =
Only display caption text data if it has been set.

= 0.3.0 =
Dedicated fields for image source attribution. When formatted text is requested, the source information has its own CSS class.

= 0.2.0 =
* SYNTAX CHANGE: The theme function `cc_featured_image_caption()` no longer needs to be used with `echo` but instead defaults to echo. If you'd like to return the result, add `false` as an argument for the function like so: `cc_featured_image_caption( false );`
* Allow HTML tags in the caption. Only permits tags that WordPress allows in post content.
* A new function has been added to check whether a caption is set for the post. Use the function `cc_has_featured_image_caption()` which returns `true` if a caption is set, and `false` if a caption is not set.

= 0.1.3 =
Theme function checks whether the caption data returned to it is false. If false, the theme function also returns false. If not false, the theme function returns a formatted caption string.

= 0.1.2 =
Fixed bug in declaring `<span>` class.

= 0.1.1 =
Added `<span>` around the rendered caption to allow for CSS formatting.

= 0.1.0 =
Initial release.
