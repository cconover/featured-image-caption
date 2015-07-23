<?php

/**
 * Composer PHP interface for the plugin.
 *
 * @since 0.8.2
 */

namespace cconover\FeaturedImageCaption;

class Composer {
    /**
     * @var bool $available Whether the Composer executable is available.
     */
    public $available;

    /**
     * Class constructor.
     *
     * @since 0.8.2
     */
    public function __construct() {
        // Check for cURL
        if ( ! function_exists( 'curl_version' ) ) {
            throw new \Exception( 'cURL is not available.' );
        }

        // Composer executable availability
        $get = $this->get();
        if ( is_wp_error( $get ) ) {
            $this->available = false;
        } else {
            $this->available = true;
        }
    }

    /**
     * Install Composer dependencies.
     *
     * @api
     *
     * @since 0.8.2
     *
     * @return bool|object If successful, return true. If not, return WP_Error.
     */
    public function install() {
        // If the Composer executable is not available, throw an error
        if ( ! $this->available ) {
            return new \WP_Error(
                'composer-not-available',
                __( 'The Composer executable is not available.', CCFIC_ID )
            );
        }

        // If WP_DEBUG is false, add '--no-dev' to the command
        if ( ! WP_DEBUG ) {
            $dev = ' --no-dev';
        } else {
            $dev = null;
        }

        // Install Composer dependencies
        $install = shell_exec( 'composer install -o' . $dev );

        if ( 'Generating optimized autoload files' == $install ) {
            return true;
        } else {
            return new \WP_Error(
                'composer-install-failed',
                __( 'Composer could not install the dependencies. Output: ' . $install, CCFIC_ID )
            );
        }
    }

    /**
     * Get the Composer executable.
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return bool|object If successful, return true. If not, WP_Error.
     */
    private function get() {
        // If Composer is not already available on the system, get it
        if ( ! shell_exec( 'composer' ) && ! shell_exec( plugin_dir_path( CCFIC_PATH ) . 'bin/composer' ) ) {
            // Get the plugin's 'bin' directory path
            $bin = plugin_dir_path( CCFIC_PATH ) . 'bin';

            // Execute the command to download and install Composer
            $curl = shell_exec( 'curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=' . $bin );
            if ( 'Use it: php ' . $bin . '/composer' != $curl ) {
                return new \WP_Error(
                    'composer-get-failed',
                    __( 'The Composer executable could not be installed. Output: ' . $curl, CCFIC_ID )
                );
            }

            // Give the Composer executable the proper permissions
            $chmod = shell_exec( 'chmod +x ' . $bin . '/composer' );
            if ( null != $chmod ) {
                return new \WP_Error(
                    'composer-chmod-failed',
                    __( 'The Composer executable permissions could not be set. Output: ' . $curl, CCFIC_ID )
                );
            }
        }

        return true;
    }
}
