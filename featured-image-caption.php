<?php

/*
 * Plugin Name: Featured Image Caption
 * Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?utm_source=wp-featured-image-caption
 * Description: Set a caption for the featured image of a post that can be displayed on your site.
 * Version: 0.8.0
 * Author: Christiaan Conover
 * Author URI: https://christiaanconover.com?utm_source=wp-featured-image-caption-author
 * License: GPLv2.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    die( 'You cannot access this resource directly.' );
}

// Check that the version of PHP is sufficient
if( version_compare( phpversion(), '5.3', '<' ) ) {
    trigger_error( 'PHP version ' . phpversion() . ' is unsupported, must be version 5.3 or newer', E_USER_ERROR );

    return;
}

/* Define plugin constants */
define( 'CCFIC_ID', 'cc-featured-image-caption' ); // Plugin ID
define( 'CCFIC_NAME', 'Featured Image Caption' ); // Plugin name
define( 'CCFIC_VERSION', '0.8.0' ); // Plugin version
define( 'CCFIC_WPVER', '3.5' ); // Minimum required version of WordPress
define( 'CCFIC_KEY', 'cc_featured_image_caption' ); // Database key


// Plugin activation
if( is_admin() ) {
    require_once 'classes/Manage.php';
    // Plugin activation
    register_activation_hook( __FILE__, array( '\cconover\FeaturedImageCaption\Manage', 'activate' ) );
}

/**
 * Plugin loader hook.
 */
function cc_featured_image_caption_loader() {
    // Define the path to this file
    if ( ! defined( 'CCFIC_PATH' ) ) {
        define( 'CCFIC_PATH', __FILE__ );
    }

    // Composer autoloader
    require_once 'vendor/autoload.php';

    // Instantiate the plugin
    new \cconover\FeaturedImageCaption\Bootstrap();
}
add_action('plugins_loaded', 'cc_featured_image_caption_loader');

/**
 * Use this function to retrieve the caption for the featured image.
 * This function must be used within The Loop.
 *
 * As of version 0.5.0, this function is no longer required, and is retained for
 * legacy support and to allow theme developers more control over placement.
 *
 * @param bool $echo Whether to print the results true or return them false (default: true)
 * @param bool $html Whether the result should be formatted HTML. True: HTML. False: array of caption data.
 *
 * @return string The formatted caption.
 */
function cc_featured_image_caption( $echo = true, $html = true ) {
    // Call the caption data using the shortcode
    $format = $html ? '' : ' format="plaintext"';
    $caption = do_shortcode( '[ccfic'.$format.']');

    // If the result should be printed to the screen.
    if ( $echo ) {
        echo $caption;
    } else {
        return $caption;
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
function cc_has_featured_image_caption() {
    return true;
}
