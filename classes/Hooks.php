<?php

/**
 * WordPress hooks and filters for the plugin.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Hooks {
    /**
     * Class constructor.
     *
     * @since 0.7.0
     */
    public function __construct() {
        // Admin
        if ( is_admin() ) {
            // Plugin management
            $this->manage();

            // Post meta box
            $this->metabox();

            // Plugin options
            $this->options();
        }

        // Caption data
        $this->caption();

        // Shortcode
        $this->shortcode();
    }

    /**
     * Plugin activation, deactivation, and other management tasks.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function manage() {
        $manage = new Manage();

        // Plugin activation
        register_activation_hook( CCFAC_PATH, array( $manage, 'activate' ) );

        // Plugin deactivation
        register_deactivation_hook( CCFAC_PATH, array( $manage, 'deactivate' ) );
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
     * Shortcode for the caption data.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function shortcode() {
        $shortcode = new Shortcode();

        // Register the shortcode
        add_shortcode( 'cc-featured-image-caption', array( $shortcode, 'shortcode' ) );
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
        add_filter('post_thumbnail_html', array($caption, 'post_thumbnail_filter'));
    }
}
