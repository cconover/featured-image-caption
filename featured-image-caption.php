<?php

/**
 * Plugin Name: Featured Image Caption
 * Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?utm_source=wp-featured-image-caption-plugin-data
 * Description: Set a caption for the featured image of a post that can be displayed on your site.
 * Version: 0.6.3
 * Author: Christiaan Conover
 * Author URI: https://christiaanconover.com?utm_source=wp-featured-image-caption-plugin-author-uri
 * License: GPLv2.
 **/

// Prevent direct access
if (! defined('ABSPATH')) {
    die('You cannot access this resource directly.');
}

// Create plugin object
function cc_featured_image_caption_loader()
{
    require_once plugin_dir_path(__FILE__).'class-featured-image-caption.php';
    new \cconover\FeaturedImageCaption\FeaturedImageCaption();

    // Admin
    if (is_admin()) {
        // Include the file containing the main Admin class and create an admin object
        require_once plugin_dir_path(__FILE__).'admin/featured-image-caption-admin.php';
        new \cconover\FeaturedImageCaption\Admin();
    }
}
add_action('plugins_loaded', 'cc_featured_image_caption_loader');

/**
 * As of version 0.5.0, this function is no longer required, and is retained for
 * legacy support and to allow theme developers more control over placement.
 *
 * Use this function to retrieve the caption for the featured image.
 * This function must be used within The Loop.
 *
 * @param bool $echo Whether to print the results true or return them false (default: true)
 * @param bool $html Whether the result should be formatted HTML. True: HTML. False: array of caption data.
 *
 * @return mixed
 */
function cc_featured_image_caption($echo = true, $html = true)
{
    $caption = new \cconover\FeaturedImageCaption\FeaturedImageCaption();

    // If the result should be printed to the screen. $echo and $html MUST both be true.
    if (! empty($echo) && ! empty($html)) {
        // If automatic caption appending is disabled
        if (! $caption->auto_append()) {
            echo $caption->caption();
        }
    } else {
        return $caption->caption($html);
    }
}

/**
 * === DEPRECATED ===
 * This function is no longer required, and is only retained for legacy support.
 * The plugin now automatically adds the caption data to the featured image. As
 * such, this function ALWAYS returns true to allow the plugin to proceed.
 *
 * Check whether a featured image caption is set.
 *
 * @return bool
 */
function cc_has_featured_image_caption()
{
    return true;
}
