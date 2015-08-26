<?php

/**
 * Manage plugin options.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Option {
    /**
     * Plugin options.
     */
    private $options;

    /**
     * Class constructor.
     */
    public function __construct() {
        // Get plugin options
        $this->options = get_option( CCFIC_ID . '_options' );
    }

    /**
     * Create the menu entry under the Settings menu.
     */
    public function create_options_menu()
    {
        add_options_page(
            CCFIC_NAME, // Page title. This is displayed in the browser title bar.
            CCFIC_NAME, // Menu title. This is displayed in the Settings submenu.
            'manage_options', // Capability required to access the options page for this plugin
            CCFIC_ID, // Menu slug
            array($this, 'options_page') // Function to render the options page
        );
    }

    /**
     * Initialize plugin options.
     */
    public function options_init()
    {
        // Register the plugin options call and the sanitation callback
        register_setting(
            CCFIC_ID.'_options_fields', // The namespace for plugin options fields. This must match settings_fields() used when rendering the form.
            CCFIC_ID.'_options', // The name of the plugin options entry in the database.
            array($this, 'options_validate') // The callback method to validate plugin options
        );

        // Settings section for Post/Page options
        add_settings_section(
            'display', // Name of the section
            'Display', // Title of the section, displayed on the options page
            array($this, 'display_callback'), // Callback method to display plugin options
            CCFIC_ID // Page ID for the options page
        );

        // Section for plugin debugging
        add_settings_section(
            'debug',
            'Debug',
            array($this, 'debug_callback'),
            CCFIC_ID
        );

        // Settings fields
        $this->settings_fields();
    }

    /**
     * Callback for Display settings section.
     */
    public function display_callback()
    {
        echo '<p>Adjust the way the caption is displayed on your site.</p>';
    }

    /**
     * Callback for debugging.
     */
    public function debug_callback()
    {
        //echo '<button id="cc-featured-image-caption-debug-toggle" class="button">Show Debug Info</button>';

        echo '<div id="cc-featured-image-caption-debug-info">';

        echo '<p>Use the information below for debugging. If you are posting in the support forums or on a GitHub issue, please copy and paste everything shown below.</p>';

        // Versioning information
        echo '<strong>Version Information</strong><br>';
        echo 'Plugin: '.CCFIC_VERSION.'<br>';
        echo 'WordPress: '.get_bloginfo('version').'<br>';
        echo 'PHP: '.phpversion().'<br>';

        // Theme information
        $theme = wp_get_theme();
        echo '<br><strong>Theme</strong><br>';
        echo 'Name: <a href="'.$theme->get('ThemeURI').'" target="_blank">'.$theme->get('Name').'</a><br>';
        echo 'Version: '.$theme->get('Version').'<br>';

        echo '</div>';
    }

    /**
     * Callback for automatically appending caption.
     */
    public function auto_append_callback()
    {
        $checked = ( ! empty( $this->options->auto_append ) ) ? ' checked' : null;

        echo '<input id="' . CCFIC_ID . '_options_auto_append" name="' . CCFIC_ID . '_options[auto_append]" type="checkbox"' . $checked . '>';
        echo '<p class="description"><strong>Recommended.</strong> Automatically display the caption data you set for the featured image wherever the featured image is displayed. You do not have to make any modifications to your theme files. If you don\'t know what this means or why you wouldn\'t want this enabled, leave it checked.</p>';
    }

    /**
     * Callback for the 'only on single posts' option.
     *
     * @since 0.8.2
     */
    public function only_single_callback() {
        // Determine whether the checkbox should be checked
        $checked = ( ! empty( $this->options->only_single ) ? ' checked' : null );

        // Display the checkbox and additional option information
        echo '<input id="' . CCFIC_ID . '_options_only_single" name="' . CCFIC_ID . '_options[only_single]" type="checkbox"' . $checked . '>';
        echo '<p class="description">If, when auto appending is enabled, you only want the caption to be appended when viewing a single post, enable this option.</p>';
    }

    /**
     * Callback for container div.
     */
    public function container_callback()
    {
        $checked = ( ! empty( $this->options->container ) ) ? ' checked' : null;

        echo '<input id="' . CCFIC_ID . '_options_container" name="' . CCFIC_ID . '_options[container]" type="checkbox"' . $checked . '>';
        echo '<p class="description"><strong>Recommended.</strong> Put the entire HTML output of the caption information inside a &lt;div&gt; tag, to give you more control over styling the caption. If you do not know what this means, leave it checked.</p>';
    }

    /**
     * Options page.
     */
    public function options_page()
    {
        // Make sure the user has permissions to access the plugin options
        if (! current_user_can('manage_options')) {
            wp_die('<p>You do not have sufficient privileges to access this page.');
        }
        ?>
        <div class="wrap">
            <?php screen_icon();
        ?>
            <h2><?php echo CCFIC_NAME;
        ?></h2>

            <form action="options.php" method="post">
                <?php
                settings_fields(CCFIC_ID.'_options_fields');
        do_settings_sections(CCFIC_ID);
        submit_button();
        ?>
            </form>
        </div>
        <?php

    }

    /**
     * Validate the options when saved.
     */
    public function options_validate($input)
    {
        // Set local variable for plugin options stored in the database
        $options = $this->options;

        // If the options are stored as an array, convert them to an object
        if ( is_array( $options ) ) {
            $options_obj = new \stdClass();
            foreach ( $options as $key => $value ) {
                $options_obj->$key = $value;
            }

            $options = $options_obj;
        }

        // Set the values to store in the database for each of the options
        $options->auto_append = ( ! empty( $input['auto_append'] ) ) ? true : false;
        $options->only_single = ( ! empty( $input['only_single'] ) ) ? true : false;
        $options->container = ( ! empty( $input['container'] ) ) ? true : false;

        return $options;
    }

    /**
     * Settings fields.
     *
     * Add settings fields to the options page.
     *
     * @since 0.8.2
     */
    private function settings_fields() {
        // Automatically add the caption to the featured image
        add_settings_field(
            'auto_append',
            'Automatically add the caption to the featured image',
            array($this, 'auto_append_callback'),
            CCFIC_ID,
            'display'
        );

        // Only show the caption when viewing a single post
        add_settings_field(
            'only_single',
            'Only automatically append on single posts',
            array( $this, 'only_single_callback' ),
            CCFIC_ID,
            'display'
        );

        // Add a container <div> to the caption HTML
        add_settings_field(
            'container',
            'Add a container &lt;div&gt; to the caption HTML',
            array($this, 'container_callback'),
            CCFIC_ID,
            'display'
        );
    }
}
