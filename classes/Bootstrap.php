<?php

/**
 * Plugin loader.
 *
 * @filesource
 */

namespace cconover\FeaturedImageCaption;

/**
 * Plugin loader
 *
 * When the plugin is loaded, the environment needs to be set up. This class
 * bootstraps the plugin's classes and hooks.
 *
 * @since 0.7.0
 */
class Bootstrap {
    /**
     * Load the bootstrap processes.
     *
     * @api
     *
     * @since 0.7.0
     */
    public function load() {
        // Hooks and filters
        $hooks = new Hooks();
        $hooks->call();
    }
}
