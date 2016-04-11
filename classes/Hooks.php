<?php
/**
 * WordPress hooks and filters for the plugin.
 *
 * @filesource
 */

namespace cconover\FeaturedImageCaption;

/**
 * WordPress hooks and filters for the plugin.
 *
 * The plugin depends on WordPress action and filter hooks. To make management
 * of the hooks easier, they are organized inside this class. Each method inside
 * this class handles the hooks for another class in this plugin.
 *
 * @since 0.7.0
 */
class Hooks {
    /**
     * Call the hooks.
     *
     * @api
     *
     * @since 0.7.0
     */
    public function call() {
        // Admin
        if ( is_admin() ) {
            // Plugin upgrades
            $this->upgrade();

            // Post meta box
            $this->metabox();

            // Plugin options
            $this->options();
        }

        // Caption data
        $this->caption();

        // REST API (if supported)
        if ( class_exists( 'WP_Rest_Controller' ) ) {
            $this->rest_api();
        }

        // Shortcode
        $this->shortcode();
    }

    /**
     * Caption data.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function caption() {
        $caption = new Caption();

        // Hook into post thumbnail
        add_filter( 'post_thumbnail_html', array( $caption, 'post_thumbnail_filter' ) );
    }

    /**
     * Post meta box.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function metabox() {
        $metabox = new MetaBox();

        // Add meta box
        add_action('add_meta_boxes', array($metabox, 'metabox'));

        // Save the caption when the post is saved
        add_action('save_post', array($metabox, 'save_metabox'));
    }

    /**
     * Plugin options.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function options() {
        $option = new Option();

        // Add menu entry to Settings menu
        add_action('admin_menu', array($option, 'create_options_menu'));

        // Initialize plugin options
        add_action('admin_init', array($option, 'options_init'));
    }

    /**
     * REST API support
     *
     * @internal
     *
     * @since 0.8.3
     */
    private function rest_api() {
        $rest_api = new RestApi();

        // Register the fields
        add_action( 'rest_api_init', array( $rest_api, 'register_fields' ) );
    }

    /**
     * Shortcode for the caption data.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function shortcode() {
        $shortcode = new Shortcode();

        // Register the shortcode
        add_shortcode( 'ccfic', array( $shortcode, 'shortcode' ) );

        // DEPRECATED shortcode because hyphens are to be avoided.
        add_shortcode( 'cc-featured-image-caption', array( $shortcode, 'shortcode' ) );
    }

    /**
     * Plugin upgrades.
     *
     * @internal
     *
     * @since 0.8.2
     */
    private function upgrade() {
        $upgrade = new Upgrade();

        // Run plugin upgrades
        $upgrade->upgrades();
    }
}
