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
        // Hooks and filters
        new Hooks();
    }
}
