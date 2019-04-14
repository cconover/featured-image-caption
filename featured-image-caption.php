<?php
/*
Plugin Name: Featured Image Caption
Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?utm_source=wp-featured-image-caption
Description: Set a caption for the featured image of a post that can be displayed on your site.
Version: 0.8.9
Author: Christiaan Conover
Author URI: https://christiaanconover.com?utm_source=wp-featured-image-caption-author
License: GPLv2.
Text Domain: ccfic
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    die('You cannot access this resource directly.');
}

/* Define plugin constants */
define('CCFIC_ID', 'ccfic'); // Plugin ID
define('CCFIC_NAME', 'Featured Image Caption'); // Plugin name
define('CCFIC_VERSION', '0.8.9'); // Plugin version
define('CCFIC_WPVER', '3.5'); // Minimum required version of WordPress
define('CCFIC_PHPVER', '5.6.20'); // Minimum required version of PHP
define('CCFIC_KEY', 'cc_featured_image_caption'); // Database key (legacy support, ID now used)
define('CCFIC_PATH', __FILE__); // Path to the primary plugin file

// Check that the version of PHP is sufficient
if (version_compare(PHP_VERSION, CCFIC_PHPVER, '<')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins(plugin_basename(CCFIC_PATH));
    add_action('admin_notices', 'ccfic_unsupported_php_notice');
    return;
}

if (is_admin()) {
    // Plugin activation
    register_activation_hook(__FILE__, 'ccfic_activate');
}

/**
 * Class autoloader
 *
 * Auto-loads classes for the plugin.
 *
 * @param string $class The fully-qualified class name.
 *
 * @return void
 */
spl_autoload_register(
    function ($class) {
        // project-specific namespace prefix
        $prefix = 'cconover\\FeaturedImageCaption\\';

        // base directory for the namespace prefix
        $base_dir = __DIR__ . '/classes/';

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            include $file;
        }
    }
);

/**
 * Plugin loader hook.
 *
 * @return void
 */
function cc_featured_image_caption_loader()
{
    // Instantiate the plugin
    $bootstrap = new \cconover\FeaturedImageCaption\Bootstrap();
    $bootstrap->load();
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
function cc_featured_image_caption($echo = true, $html = true)
{
    // Call the caption data using the shortcode
    $format = $html ? '' : ' format="plaintext"';
    $caption = do_shortcode('[ccfic' . $format . ']');

    // If the result should be printed to the screen.
    if ($echo) {
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
function cc_has_featured_image_caption()
{
    return true;
}

/**
 * Plugin activation.
 *
 * @since 0.8.1
 *
 * @return void
 */
function ccfic_activate()
{
    // Check to make sure the version of WordPress is compatible
    if (version_compare($GLOBALS['wp_version'], CCFIC_WPVER, '<')) {
        deactivate_plugins(plugin_basename(CCFIC_PATH));
        add_action('admin_notices', 'ccfic_unsupported_wp_notice');
        return;
    }

    // Plugin environment data
    $env = new \stdClass();
    $env->version = CCFIC_VERSION;

    // Add environment data to the database
    add_option(CCFIC_ID . '_env', $env);

    // Default plugin options
    $options = new \stdClass();
    $options->auto_append = true; // Automatically append caption to featured image
    $options->only_single = false; // Restrict automatic caption appending to single posts
    $options->container = true; // Wrap the caption HTML in a container div

    // Add options to database
    add_option(CCFIC_ID . '_options', $options);
}

/**
 * Notice of unsupported PHP version.
 *
 * @since 0.8.9
 *
 * @return void
 */
function ccfic_unsupported_php_notice()
{
    ?>
    <div class="notice notice-error is-dismissable">
        <p><?php esc_html_e(sprintf('%s requires PHP %s or newer. The plugin has been deactivated.', CCFIC_NAME, CCFIC_PHPVER), CCFIC_ID); ?></p>
    </div>
    <?php
}

/**
 * Notice of unsupported WordPress version.
 *
 * @since 0.8.9
 *
 * @return void
 */
function ccfic_unsupported_wp_notice()
{
    ?>
    <div class="notice notice-error is-dismissable">
        <p><?php esc_html_e(sprintf('%s requires WordPress version %s or newer. Please upgrade to the latest version of WordPress. The plugin has been deactivated.', CCFIC_NAME, CCFIC_WPVER), CCFIC_ID); ?></p>
    </div>
    <?php
}
