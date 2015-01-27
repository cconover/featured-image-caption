<?php

/**
 * Primary admin class for Featured Image Caption
 * @package cconover
 * @subpackage featured-image-caption
 */

namespace cconover;

class Admin extends FeaturedImageCaption {

    // Class constructor
    function __construct() {
        // Initialize admin
        $this->admin_initialize();

        // Hooks and filters
        add_action( 'add_meta_boxes', array( &$this, 'metabox') ); // Add meta box
        add_action( 'save_post', array( &$this, 'save_metabox' ) ); // Save the caption when the post is saved
        register_activation_hook( __FILE__, array( &$this, 'activate' ) ); // Plugin activation
        register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) ); // Plugin deactivation
    }

    /**
    * Create the meta box
    */
    function metabox() {
        // Specify the screens where the meta box should be available
        $screens = apply_filters( 'cc_featured_image_caption_screens', array( 'post', 'page' ) );

        // Iterate through the specified screens to add the meta box
        foreach ( $screens as $screen ) {
            add_meta_box(
                self::ID, // HTML ID for the meta box
                self::NAME, // Title of the meta box displayed to the us
                array( &$this, 'metabox_callback' ), // Callback function for the meta box to display it to the user
                $screen, // Locations where the meta box should be shown
                'side' // Location where the meta box should be shown. This one is placed on the side.
            );
        }
    }

    /**
    * Featured image caption meta box callback
    */
    function metabox_callback( $post ) {
        // Add a nonce field to verify data submissions came from our site
        wp_nonce_field( self::ID, self::PREFIX . 'nonce' );

        // Retrieve the current caption as a string, if set
        $caption = get_post_meta( $post->ID, self::METAPREFIX, true );

        // If the data is a string, convert it to an array (legacy data support)
        if ( is_string( $caption ) ) {
            $caption = array(
                'caption_text' => $caption
            );
        }

        echo 'Caption text <textarea style="width: 100%; max-width: 100%;" id="' . self::PREFIX . '_caption_text" name="' . self::PREFIX . '_caption_text">' . ( ! empty( $caption['caption_text'] ) ? esc_attr( $caption['caption_text'] ) : null ) . '</textarea>';
        echo '<br><br>';
        echo '<strong>Source Attribution</strong><br>';
        echo 'Text <input style="width: 100%;" id="' . self::PREFIX . '_source_text" name="' . self::PREFIX . '_source_text" value="' . ( ! empty( $caption['source_text'] ) ? $caption['source_text'] : null ) . '">';
        echo 'URL <input style="width: 100%;" id="' . self::PREFIX . '_source_url" name="' . self::PREFIX . '_source_url" value="' . ( ! empty( $caption['source_url'] ) ? $caption['source_url'] : null ) . '">';
    }

    /**
    * Save the meta box data
    */
    function save_metabox( $post_id ) {
        /*
        Verify using the nonce that the data was submitted from our meta box on our site.
        If it wasn't, return the post ID and be on our way.
        */
        // If no nonce was provided or the nonce does not match
        if ( ! isset( $_POST[self::PREFIX . 'nonce'] ) || ! wp_verify_nonce( $_POST[self::PREFIX . 'nonce'], self::ID ) ) {
            return $post_id;
        }

        // Make sure the user has valid permissions
        // If we're editing a page and the user isn't allowed to do that, return the post ID
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        }
        // If we're editing any other post type and the user isn't allowed to do that, return the post ID
        else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        // Now that we've validated nonce and permissions, let's save the caption data
        // Sanitize the caption
        $caption = array(
        'caption_text'	=> wp_kses_post( $_POST[self::PREFIX . '_caption_text'] ),
        'source_text'	=> sanitize_text_field( $_POST[self::PREFIX . '_source_text'] ),
        'source_url'	=> esc_url_raw( $_POST[self::PREFIX . '_source_url'] )
        );

        // Update the caption meta field
        update_post_meta( $post_id, self::METAPREFIX, $caption );
    }

    /**
     * Admin initialization
     */
    private function admin_initialize() {
        // Run upgrade process
        $this->upgrade();
    }

    /**
     * Plugin upgrade
     */
    private function upgrade() {
        // Check whether the database-stored plugin version number is less than the current plugin version number, or whether there is no plugin version saved in the database
        if ( ! empty( $this->options['dbversion'] ) && version_compare( $this->options['dbversion'], self::VERSION, '<' ) ) {
            /* FIRST STEP ALWAYS!!! Set local variable for options */
            $options = $this->options;

            /* UPGRADE ACTIONS */

            /* END UPGRADE ACTIONS */

            /* LAST STEPS ALWAYS!!! Update the plugin version saved in the database */
            // Set the value of the plugin version
            $options['dbversion'] = self::VERSION;

            // Save to the database
            update_option( self::PREFIX . 'options', $options );
        }
    }

    /**
    * Plugin activation
    */
    function activate() {
        // Check to make sure the version of WordPress being used is compatible with the plugin
        if ( version_compare( get_bloginfo( 'version' ), self::WPVER, '<' ) ) {
            wp_die( 'Your version of WordPress is too old to use this plugin. Please upgrade to the latest version of WordPress.' );
        }

        // Default plugin options
        $options = array(
            'dbversion'     => self::VERSION, // Current plugin version
        );

        // Add options to database
        add_option( self::PREFIX . 'options', $options );
    }

    /**
    * Plugin deactivation
    */
    function deactivate() {
        // Remove the plugin options from the database
        delete_option( self::PREFIX . 'options' );
    }

}

?>
