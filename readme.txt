=== Featured Image Caption ===
Contributors: cconover
Donate link: https://christiaanconover.com/code/wp-featured-image-caption?ref=plugin-readme
Tags: image, caption, featured image, shortcode
Requires at least: 3.5
Tested up to: 4.7
Stable tag: 0.8.6
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily add and display a caption for the featured image of a post.

== Description ==

Featured Image Caption makes it simple to show a caption with the featured image of a post or page. It works seamlessly with most themes, with no coding required. If you like to mess about in the code, it supports that too.

For full details about the plugin and usage documentation, [check out the plugin wiki on GitHub](https://github.com/cconover/wp-featured-image-caption/wiki).

== Installation ==

1. Upload the `featured-image-caption` directory to your site's `plugins` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Review the plugin options found in `Settings` > `Featured Image Caption`.

== Frequently Asked Questions ==

= Where do I find the documentation for this plugin? =
Documentation is maintained on the plugin's [GitHub wiki](https://github.com/cconover/wp-featured-image-caption/wiki). I don't want to maintain two separate sets of documentation and run the risk of conflicting/outdated information, and all of the plugin development happens on GitHub.

== Screenshots ==

1. Featured Image Caption meta box below the Featured Image meta box.

== Upgrade Notice ==

= 0.8.6 =
Fix an error thrown due to a deprecated function call for the WP REST API.

= 0.8.5 =
Fix an issue with empty caption data causing Undefined Index errors for the REST API.

= 0.8.4 =
Fix a bug with the REST API throwing errors for posts with no caption data.

= 0.8.3 =
Add support for the WordPress REST API.

= 0.8.2 =
When automatic caption appending is enabled, the plugin can optionally only append the caption when viewing a single post.

= 0.8.0 =
NOTICE: Changes the shortcode name and fixes bugs in the shortcode. PLEASE check the changelog for this version to see how this affects you.

= 0.7.2 =
Fixes a bug in upgrading plugin options.

= 0.7.1 =
Fixes missing dependency.

= 0.7.0 =
Fixes bug in handling HTML tags in caption data that was introduced in the last release. Bug fixes related to activation.

= 0.6.3 =
Adds support for HTML tags to be used in caption and source text.

= 0.6.2 =
Fixes issue with incorrectly escaped characters for the meta box fields.

= 0.6.1 =
Fixes a bug introduced in 0.6.0 in the theme function.

= 0.6.0 =
A shortcode has been added to allow for easy insertion of caption information anywhere in a post.

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

= 0.8.6 =
Fix an error thrown due to a deprecated function call for the WP REST API.

= 0.8.5 =
Fix an issue with [empty caption data causing `Undefined Index` errors for the REST API](https://github.com/cconover/featured-image-caption/pull/56).
Credit: [github/brockpetrie](https://github.com/brockpetrie)

= 0.8.4 =
Fix a bug with the REST API throwing errors for posts with no caption data.

= 0.8.3 =
Add support for the WordPress REST API. The caption fields are added as properties in the `posts` response. See [plugin documentation for the REST API](https://github.com/cconover/featured-image-caption/wiki/REST-API) for usage information.

= 0.8.2 =
* When automatic caption appending is enabled, the plugin can optionally only append the caption when viewing a single post.
* Improved validation of supported PHP version. If PHP is too old, the plugin is deactivated and a warning is displayed.
* Bug fixes and improvements.

= 0.8.1 =
Restricts the automatic caption insertion to only occur in The Loop.

= 0.8.0 =
* Changes the name of the shortcode from `cc-featured-image-caption` to `ccfic`. This was done for a few reasons. First, WordPress documentation advises against hyphens in shortcode names, so in order to follow best practices the hyphens have been removed. Second, it's easier to type the abbreviation when using the shortcode. Please note, the old shortcode name still works for now, but you should expect that it will be removed entirely by the time this plugin reaches its 1.0.0 release. As such, please update all the locations that you use the shortcode. Please see the [shortcode documentation](https://github.com/cconover/wp-featured-image-caption/wiki/Shortcode) for usage information.
* Fixes the activation process so that initial configuration is saved properly in the database.

= 0.7.2 =
Fixes a bug in upgrading plugin options.

= 0.7.1 =
Fixes missing dependency.

= 0.7.0 =
Fixes bug in handling HTML tags in caption data that was introduced in the last release. Bug fixes related to activation.

= 0.6.3 =
Adds support for HTML tags to be used in caption and source text. HTML tags are filtered, and only tags allowed in post content are allowed in caption and source text.

= 0.6.2 =
Fixes issue with incorrectly escaped characters for the meta box fields.

= 0.6.1 =
Fixes a bug introduced in 0.6.0 in the theme function.

= 0.6.0 =
A shortcode has been added for easy insertion of caption information into a post or page. Debugging information is now displayed on the plugin settings page. Minor bug fixes.

= 0.5.0 =
** Major Release / Breaking Changes **

Plugin can automatically add the caption after the featured image, removing the need to modify theme files. If you are upgrading from a previous option this will be disabled by default, otherwise it is enabled by default. Theme function `cc_featured_image_caption()` is not needed if you have automatic caption insertion turned on. If this option is enabled, the function won't display anything. You can, however, still use it to `return` the caption data. CSS classes have changed. See the FAQ for information about the new markup and CSS classes.

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
