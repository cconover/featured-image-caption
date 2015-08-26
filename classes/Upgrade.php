<?php

/**
 * Handle all plugin upgrades.
 *
 * @since 0.8.2
 */

namespace cconover\FeaturedImageCaption;

class Upgrade {
    /**
     * @var object $env Plugin environment data.
     */
    private $env;

    /**
     * @var object $options Plugin options.
     */
    private $options;

    /**
     * @var string $version Plugin version.
     */
    private $version;

    /**
     * Class contructor.
     *
     * @since 0.8.2
     */
    public function __construct() {
        // Get plugin data from the database
        $this->get_data();

        // Get the plugin version
        $this->version();
    }

    /**
     * Run plugin upgrades. Upgrades are run from oldest to latest.
     *
     * @api
     *
     * @since 0.8.2
     */
    public function upgrades() {
        // 0.5.0
        $this->v0_5_0();

        // 0.7.0
        $this->v0_7_0();

        // 0.7.2
        $this->v0_7_2();

        // 0.8.2
        $this->v0_8_2();


        /* === ALWAYS THE LAST STEPS === */
        // Update plugin options in the database
        update_option( CCFIC_ID . '_options', $this->options );

        // Update the saved plugin version
        $this->env->version = CCFIC_VERSION;

        // Update the plugin environment data in the database
        update_option( CCFIC_ID . '_env', $this->env );
    }

    /*
	|---------------------------------------------------------------------------
	| Upgrades
	|---------------------------------------------------------------------------
	|
	| Each version's upgrades are contained in a method for that version.
	|
	*/

    /**
     * Version 0.5.0
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return $this
     */
    private function v0_5_0() {
        // Set a local variable for options
        $options = $this->options;

        if ( version_compare( $this->version, '0.5.0', '<' ) ) {
            /*
            Add an option to automatically append caption to the featured
            image. Since this is an upgrade, we assume the user is already
            using the plugin's theme function(s), so we'll set this to false
            to avoid breakage.
            */
            $options['auto_append'] = false;

            // Wrap the caption HTML in a container <div>
            $options['container'] = true;

            // Change the key that contains the plugin version
            $options['version'] = $options['dbversion'];
            unset( $options['dbversion'] );

            // Update the class property
            $this->options = $options;
        }

        return $this;
    }

    /**
     * Version 0.7.0
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return $this
     */
    private function v0_7_0() {
        // Set a local variable for plugin options
        $options = $this->options;

        if ( version_compare( $this->version, '0.7.0', '<' ) ) {
            // Convert the stored plugin options from an array to an object
            if ( is_array( $options ) ) {
                $options_obj = new \stdClass();
                foreach ( $options as $key => $value ) {
                    $options_obj->$key = $value;
                }

                $options = $options_obj;
            }

            // Update the class property
            $this->options = $options;
        }

        return $this;
    }

    /**
     * Version 0.7.2
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return $this
     */
    private function v0_7_2() {
        // Set a local variable for options
        $options = $this->options;

        // If no version is set, use 0.7.0
        if ( empty( $this->version ) ) {
            $this->version = '0.7.0';
        }

        if ( version_compare( $this->version, '0.7.2', '<' ) ) {
            // If options are still stored as an array, convert to an object
            if ( is_array( $options ) ) {
                $options_obj = new \stdClass();
                foreach ( $options as $key => $value ) {
                    $options_obj->$key = $value;
                }

                $options = $options_obj;
            }

            // Add the version number
            $options->version = $this->version;

            // Update the class property
            $this->options = $options;
        }

        return $this;
    }

    /**
     * Version 0.8.2
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return $this
     */
    private function v0_8_2() {
        // Set a local variable for options
        $options = $this->options;

        if ( version_compare( $this->version, '0.8.2', '<' ) ) {
            // Create an object for environment data
            $env = new \stdClass();

            // Store the plugin version
            $env->version = $this->version;

            // Remove the plugin version from options data
            unset( $options->version );

            // Add option value for restricting auto-append to single posts
            $options->only_single = false;

            // Rename the plugin options in the database
            delete_option( CCFIC_KEY . '_options' );
            add_option( CCFIC_ID . '_options', $options );

            // Update the class properties
            $this->env = $env;
            $this->options = $options;
        }

        return $this;
    }

    /*
	|---------------------------------------------------------------------------
	| Upgrade Environment
	|---------------------------------------------------------------------------
	|
	| In order to perform upgrades, the plugin environment and other
    | configuration parameters must be retrieved.
	|
	*/

    /**
     * Get plugin data from the database.
     *
     * @internal
     *
     * @since 0.8.2
     */
    private function get_data() {
        // Get plugin environment data
        $this->env = get_option( CCFIC_ID . '_env' );

        // Get plugin options
        $this->options = get_option( CCFIC_ID . '_options' );
        if ( ! $this->options ) {
            // If the option data does not exist, try the legacy entry name
            $this->options = get_option( CCFIC_KEY . '_options' );

            if ( ! $this->options ) {
                // If there is still no option data, return
                return;
            }
        }
    }

    /**
     * Get the plugin version.
     *
     * @internal
     *
     * @since 0.8.2
     *
     * @return this
     */
    private function version() {
        if ( is_array( $this->options ) ) {
            // If the 'version' array key is set, use it for the version
            if ( ! empty( $this->options['version'] ) ) {
                $this->version = $this->options['version'];
            } elseif ( ! empty( $this->options['dbversion'] ) ) {
                // If the 'dbversion' key is set, use it for the version
                $this->version = $this->options['dbversion'];
            }
        } elseif ( is_object( $this->options ) ) {
            // If the version is saved in plugin options, use it
            if ( ! empty( $this->options->version ) ) {
                $this->version = $this->options->version;
            } elseif ( ! empty( $this->env ) && ! empty( $this->env->version ) ) {
                // If the version is in environment data, use it
                $this->version = $this->env->version;
            }
        }

        return $this;
    }
}
