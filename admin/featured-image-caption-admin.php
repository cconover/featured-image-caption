<?php

/**
 * Primary admin class for Featured Image Caption
 * @package cconover
 * @subpackage featured-image-caption
 */

 namespace cconover\FeaturedImageCaption;

class Admin extends FeaturedImageCaption {

    // Class constructor
    function __construct() {
        parent::__construct();

        // Admin initialization
        $this->admin_initialize();

        // Meta box hooks
        add_action( 'add_meta_boxes', array( $this, 'metabox') ); // Add meta box
        add_action( 'save_post', array( $this, 'save_metabox' ) ); // Save the caption when the post is saved

        // Plugin option hooks
        add_action( 'admin_menu', array( $this, 'create_options_menu' ) ); // Add menu entry to Settings menu
		add_action( 'admin_init', array( $this, 'options_init' ) ); // Initialize plugin options

        // Plugin management hooks
        register_activation_hook( $this->pluginfile, array( $this, 'activate' ) ); // Plugin activation
        register_deactivation_hook( $this->pluginfile, array( $this, 'deactivate' ) ); // Plugin deactivation
    }

    /*
    |---------------------------------------------------------------------------
    | Meta Box
    |---------------------------------------------------------------------------
    |
    | The meta box allows you to set the caption for the featured image of a
    | post or page.
    */

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
                array( $this, 'metabox_callback' ), // Callback function for the meta box to display it to the user
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

        echo '<label for="' . self::PREFIX . 'caption_text">Caption text</label><textarea style="width: 100%; max-width: 100%;" id="' . self::PREFIX . 'caption_text" name="' . self::PREFIX . 'caption_text">' . ( ! empty( $caption['caption_text'] ) ? esc_attr( $caption['caption_text'] ) : null ) . '</textarea>';
        echo '<br><br>';
        echo '<strong>Source Attribution</strong><br>';
        echo '<label for="' . self::PREFIX . 'source_text">Text</label><input type="text" style="width: 100%;" id="' . self::PREFIX . 'source_text" name="' . self::PREFIX . 'source_text" value="' . ( ! empty( $caption['source_text'] ) ? $caption['source_text'] : null ) . '">';
        echo '<label for="' . self::PREFIX . 'source_url">URL</label><input type="text" style="width: 100%;" id="' . self::PREFIX . 'source_url" name="' . self::PREFIX . 'source_url" value="' . ( ! empty( $caption['source_url'] ) ? $caption['source_url'] : null ) . '">';
        echo '<input type="checkbox" name="' . self::PREFIX . 'new_window" value="1"' . ( $this->new_window_checked( $caption ) ? " checked" : null ) . '><label for="' . self::PREFIX . 'new_window">Open in new window</label>';
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
            'caption_text'	=> wp_kses_post( $_POST[self::PREFIX . 'caption_text'] ),
            'source_text'	=> sanitize_text_field( $_POST[self::PREFIX . 'source_text'] ),
            'source_url'	=> esc_url_raw( $_POST[self::PREFIX . 'source_url'] ),
            'new_window'    => ( ! empty( $_POST[self::PREFIX . 'new_window'] ) ? true : false )
        );

        // Update the caption meta field
        update_post_meta( $post_id, self::METAPREFIX, $caption );

        // Update the user default for the "new window" checkbox
        update_user_option( get_current_user_id(), self::PREFIX . 'new_window', $caption['new_window'] );
    }

    /**
     * Whether the "new window" checkbox should be checked
     * @param   array   $caption    The caption data
     *
     * @return  boolean
     */
    private function new_window_checked( $caption ) {
        // If "new window" status is set for the caption data
        if ( ! empty( $caption['new_window'] ) ) {
            if ( $caption['new_window'] ) {
                return true;
            }
            else {
                return false;
            }
        }
        // If not set, look for the user option
        else {
            $new_window = get_user_option( self::PREFIX . 'new_window', get_current_user_id() );

            if ( $new_window ) {
                return true;
            }
            else {
                return false;
            }
        }
    }


    /*
    |---------------------------------------------------------------------------
    | Plugin Activation/Deactivation and Upgrade
    |---------------------------------------------------------------------------
    |
    | These methods manage the plugin's activation and deactivation.
    |
    | There is also a method to handle plugin upgrades, which must follow a
    | specific sequence. That sequence is documented inside the method.
    */

    /**
	 * Create the menu entry under the Settings menu
	 */
	function create_options_menu() {
		add_options_page(
			self::NAME, // Page title. This is displayed in the browser title bar.
			self::NAME, // Menu title. This is displayed in the Settings submenu.
			'manage_options', // Capability required to access the options page for this plugin
			self::ID, // Menu slug
			array( $this, 'options_page' ) // Function to render the options page
		);
	}

    /**
	 * Initialize plugin options
	 */
	function options_init() {
		// Register the plugin options call and the sanitation callback
		register_setting(
			self::PREFIX . 'options_fields', // The namespace for plugin options fields. This must match settings_fields() used when rendering the form.
			self::PREFIX . 'options', // The name of the plugin options entry in the database.
			array( $this, 'options_validate' ) // The callback method to validate plugin options
		);

		// Settings section for Post/Page options
		add_settings_section(
			'display', // Name of the section
			'Display', // Title of the section, displayed on the options page
			array( $this, 'display_callback' ), // Callback method to display plugin options
			self::ID // Page ID for the options page
		);

        // Section for plugin debugging
        add_settings_section(
            'debug',
            'Debug',
            array( $this, 'debug_callback' ),
            self::ID
        );

		// Automatically add the caption to the featured image
		add_settings_field(
			'auto_append', // Field ID
			'Automatically add the caption to the featured image', // Field title/label, displayed to the user
			array( $this, 'auto_append_callback' ), // Callback method to display the option field
			self::ID, // Page ID for the options page
			'display' // Settings section in which to display the field
		);

		// Add a container <div> to the caption HTML
		add_settings_field(
			'container', // Field ID
			'Add a container &lt;div&gt; to the caption HTML', // Field title/label, displayed to the user
			array( $this, 'container_callback' ), // Callback method to display the option field
			self::ID, // Page ID for the options page
			'display' // Settings section in which to display the field
		);
	}

    /**
     * Callback for Display settings section.
     */
    function display_callback() {
        echo '<p>Adjust the way the caption is displayed on your site.</p>';
    }

    /**
     * Callback for debugging.
     */
    function debug_callback() {
        echo '<p>Use the information below for debugging. If you are posting in the support forums or on a GitHub issue, please copy and paste everything shown below.</p>';

        // Versioning information
        echo '<strong>Version Information</strong><br>';
        echo 'Plugin: ' . self::VERSION . '<br>';
        echo 'WordPress: ' . get_bloginfo( 'version' ) . '<br>';
        echo 'PHP: ' . phpversion() . '<br>';

        // Theme information
        $theme = wp_get_theme();
        echo '<br><strong>Theme</strong><br>';
        echo 'Name: <a href="' . $theme->get( 'ThemeURI' ) . '" target="_blank">' . $theme->get( 'Name' ) . '</a><br>';
        echo 'Version: ' . $theme->get( 'Version' ) . '<br>';
    }

    /**
     * Callback for automatically appending caption.
     */
    function auto_append_callback() {
        $checked = ( ! empty( $this->options['auto_append'] ) ) ? ' checked' : null;

        echo '<input id="' . self::PREFIX . 'options[auto_append]" name="' . self::PREFIX . 'options[auto_append]" type="checkbox"' . $checked . '>';
        echo '<p class="description"><strong>Recommended.</strong> Automatically display the caption data you set for the featured image wherever the featured image is displayed. You do not have to make any modifications to your theme files. If you don\'t know what this means or why you wouldn\'t want this enabled, leave it checked.</p>';
    }

    /**
     * Callback for container <div>
     */
    function container_callback() {
        $checked = ( ! empty( $this->options['container'] ) ) ? ' checked' : null;

        echo '<input id="' . self::PREFIX . 'options[container]" name="' . self::PREFIX . 'options[container]" type="checkbox"' . $checked . '>';
        echo '<p class="description"><strong>Recommended.</strong> Put the entire HTML output of the caption information inside a &lt;div&gt; tag, to give you more control over styling the caption. If you do not know what this means, leave it checked.</p>';
    }

    /**
     * Options page
     */
    function options_page() {
        // Make sure the user has permissions to access the plugin options
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '<p>You do not have sufficient privileges to access this page.' );
		}
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo self::NAME; ?></h2>

            <form action="options.php" method="post">
                <?php
                settings_fields( self::PREFIX . 'options_fields' );
                do_settings_sections( self::ID );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Validate the options when saved.
     */
    function options_validate( $input ) {
        // Set local variable for plugin options stored in the database
        $options = $this->options;

        // Set the values to store in the database for each of the options
        $options['auto_append'] = ( ! empty( $input['auto_append'] ) ) ? true : false;
        $options['container'] = ( ! empty( $input['container'] ) ) ? true : false;

        return $options;
    }


    /*
    |---------------------------------------------------------------------------
    | Plugin Activation/Deactivation and Upgrade
    |---------------------------------------------------------------------------
    |
    | These methods manage the plugin's activation and deactivation.
    |
    | There is also a method to handle plugin upgrades, which must follow a
    | specific sequence. That sequence is documented inside the method.
    */

    /**
     * Admin initialization
     */
    function admin_initialize() {
        // Plugin upgrades
        $this->upgrade();
    }

    /**
    * Plugin activation
    */
    function activate() {
        // Check to make sure the version of WordPress being used is compatible with the plugin
        if ( version_compare( get_bloginfo( 'version' ), self::WPVER, '<' ) ) {
            wp_die( 'Your version of WordPress is too old to use this plugin. Please upgrade to the latest version of WordPress.' );
        }

        // Check to make sure the version of PHP being used is compatible
        if ( version_compare( phpversion(), self::PHPVER, '<' ) ) {
            wp_die( 'Your version of PHP is too old. Please upgrade to a newer version of PHP.' );
        }

        // Check that the current theme support featured images
        if ( ! current_theme_supports( 'post-thumbnails' ) ) {
            wp_die( 'Your current theme does not have support for post thumbnails (featured images), which is required to use this plugin. Please add support in your current theme, or activate a theme that already supports them.' );
        }

        // Default plugin options
        $options = array(
            'version'       => self::VERSION,   // Current plugin version
            'auto_append'   => true,            // Automatically append caption to featured image
            'container'     => true,            // Wrap the caption HTML in a container <div>
        );

        // Add options to database
        update_option( self::PREFIX . 'options', $options );
    }

    /**
    * Plugin deactivation
    */
    function deactivate() {
        // Remove the plugin options from the database
        delete_option( self::PREFIX . 'options' );
    }

    /**
     * Plugin upgrade
     */
    private function upgrade() {
        // If the database still has the legacy version entry
        if ( ! empty( $this->options['dbversion'] ) ) {
            // Set new version entry
            $this->options['version'] = $this->options['dbversion'];

            // Remove old entry
            unset( $this->options['dbversion'] );
        }

        // Check whether the database-stored plugin version number is less than the current plugin version number, or whether there is no plugin version saved in the database
        if ( ! empty( $this->options['version'] ) && version_compare( $this->options['version'], self::VERSION, '<' ) ) {
            /* FIRST STEP ALWAYS!!! Set local variable for options */
            $options = $this->options;

            /* === UPGRADE ACTIONS === (oldest to latest) */

            // Version 0.5.0
            if ( version_compare( $options['version'], '0.5.0', '<' ) ) {
                /*
                Add an option to automatically append caption to the featured
                image. Since this is an upgrade, we assume the user is already
                using the plugin's theme function(s), so we'll set this to false
                to avoid breakage.
                */
                $options['auto_append'] = false;

                // Wrap the caption HTML in a container <div>
                $options['container'] = true;
            }

            /* === END UPGRADE ACTIONS === */

            /* LAST STEPS ALWAYS!!! Update the plugin version saved in the database */
            // Set the value of the plugin version
            $options['version'] = self::VERSION;

            // Save to the database
            update_option( self::PREFIX . 'options', $options );
        }
    }

}
