<?php

/**
 * Plugin loader.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Bootstrap {
    /**
     * Class constructor.
     *
     * @since 0.7.0
     */
    public function __construct() {
        // Constants
        $this->constants();

        // Hooks and filters
        new Hooks();
    }

    /**
     * Class constructor.
     *
     * @internal
     *
     * @since 0.7.0
     */
    private function constants() {
        // Plugin ID
        if ( ! defined( 'CCFIC_ID' ) ) {
            define( 'CCFIC_ID', 'cc-featured-image-caption' );
        }
        // Plugin name
        if ( ! defined( 'CCFIC_NAME' ) ) {
            define( 'CCFIC_NAME', 'Featured Image Caption' );
        }
        // Plugin version
        if ( ! defined( 'CCFIC_VERSION' ) ) {
            define( 'CCFIC_VERSION', '0.8.0' );
        }
        // Minimum required version of WordPress
        if ( ! defined( 'CCFIC_WPVER' ) ) {
            define( 'CCFIC_WPVER', '3.5' );
        }
        // Database prefix
        if ( ! defined( 'CCFIC_PREFIX' ) ) {
            define( 'CCFIC_PREFIX', 'cc_featured_image_caption_' );
        }
        // Post meta database prefix
        if ( ! defined( 'CCFIC_METAPREFIX' ) ) {
            define( 'CCFIC_METAPREFIX', '_cc_featured_image_caption' );
        }
    }
}
