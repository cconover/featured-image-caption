<?php

/**
 * Plugin loader.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Loader {
    /**
     * Class constructor.
     *
     * @since 0.7.0
     */
    public function __construct() {
        /**
         * Plugin constants.
         *
         * @since 0.7.0
         */
        // Plugin ID
        if ( ! defined( 'CCFAC_ID' ) ) {
            define( 'CCFAC_ID', 'cc-featured-image-caption' );
        }
        // Plugin name
        if ( ! defined( 'CCFAC_NAME' ) ) {
            define( 'CCFAC_NAME', 'Featured Image Caption' );
        }
        // Plugin version
        if ( ! defined( 'CCFAC_VERSION' ) ) {
            define( 'CCFAC_VERSION', '0.7.2' );
        }
        // Minimum required version of WordPress
        if ( ! defined( 'CCFAC_WPVER' ) ) {
            define( 'CCFAC_WPVER', '3.5' );
        }
        // Database prefix
        if ( ! defined( 'CCFAC_PREFIX' ) ) {
            define( 'CCFAC_PREFIX', 'cc_featured_image_caption_' );
        }
        // Post meta database prefix
        if ( ! defined( 'CCFAC_METAPREFIX' ) ) {
            define( 'CCFAC_METAPREFIX', '_cc_featured_image_caption' );
        }

        // Hooks and filters
        new Hooks();
    }
}
