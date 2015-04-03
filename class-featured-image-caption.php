<?php

/**
 * Main plugin class
 * @package cconover
 * @subpackage featured-image-caption
 */

namespace cconover\FeaturedImageCaption;

class FeaturedImageCaption {
    // Plugin constants
    const ID			= 'cc-featured-image-caption';	// Plugin ID
    const NAME			= 'Featured Image Caption';		// Plugin name
    const VERSION		= '0.5.0';						// Plugin version
    const WPVER			= '3.5';						// Minimum version of WordPress required for this plugin
    const PREFIX		= 'cc_featured_image_caption_';	// Plugin database prefix
    const METAPREFIX	= '_cc_featured_image_caption';	// Post meta database prefix

    // Class properties
    protected $options; // Plugin options and settings
    protected $pluginpath; // Directory path to the plugin root
    protected $pluginurl; // URL to the plugin directory
    protected $pluginfile; // File path to the main plugin file

    /**
    * Class constructor
    */
    function __construct() {
        // Get plugin options from database
        $this->options = get_option( self::PREFIX . 'options' );

        // Declare properties
        $this->pluginpath = plugin_dir_path( __FILE__ );
        $this->pluginurl  = plugin_dir_url( __FILE__ );
        $this->pluginfile = $this->pluginpath . 'featured-image-caption.php';

        // Hook into post thumbnail
        add_filter( 'post_thumbnail_html', array( &$this, 'post_thumbnail_filter' ) );
    }

    /**
     * Filter the post thumbnail to add our caption data.
     */
    public function post_thumbnail_filter( $html ) {
        // If automatic caption append is not enabled, return the HTML unchanged
        if ( empty( $this->options['auto_append'] ) ) {
            return $html;
        }

        // Get the caption HTML
        $caption = $this->caption();

        // Assemble our HTML to append to the post thumbnail HTML
        $html .= $caption;

        return $html;
    }

    /**
     * Access and format the caption data.
     *
     * @param   boolean             $echo       Whether to print the result to the screen. True: print the result. False: return the result. Defaults to false.
     * @param   boolean             $html       Whether the result should be fully-formed HTML. True: create HTML. False: return raw data array.
     *
     * @return  boolean|string      $caption    If successful, returns the requested result. If unsuccessful, returns false.
     */
    public function caption( $echo = false, $html = true ) {
        // Get access to the $post object
        global $post;

        // Get the caption data
        $captiondata = $this->caption_data( $post->ID );

        // If there is no caption data, return empty.
        if ( empty( $captiondata ) ) {
            return;
        }

        // If HTML is not desired, return the raw array
        if ( empty( $html ) ) {
            return $captiondata;
        }

        // Assemble the HTML
        $caption = '<span class="' . self::ID . '-text">' . $captiondata['caption_text'] . '</span>';

        // If the source URL is set, attribution is rendered as a link
        if ( ! empty( $captiondata['source_url'] ) ) {
            $new_window = ! empty( $captiondata['new_window'] ) ? ' target="_blank"' : '';
            $caption .= ' <span class="' . self::ID . '-source"><a href="' . $captiondata['source_url'] . '"' . $new_window . '>' . $captiondata['source_text'] . '</a></span>';
        } else {
            $caption .= ' <span class="' . self::ID . '-source">' . $captiondata['source_text'] . '</span>';
        }

        // If the container <div> is enabled
        if ( ! empty( $this->options['container'] ) ) {
            $caption = '<div class="' . self::ID . '">' . $caption . '</div>';
        }

        // If we don't want to print the result, return it
        if ( empty( $echo ) ) {
            return $caption;
        } else {
            echo $caption;
        }
    }

    /**
    * Retrieve the caption data
    *
    * @param    integer         $id         The ID of the post or page for which we need the featured image caption
    *
    * @return   boolean|array   $caption    If successful, the array of caption data. If unsuccessful, return false.
    */
    private function caption_data( $id ) {
        // Get the caption data from the post meta
        $caption = get_post_meta( $id, self::METAPREFIX, true );

        // If caption data is not present, return false
        if ( empty( $caption ) ) {
            return false;
        }

        // Legacy support: if caption is a string, convert it to an array
        if ( is_string( $caption ) ) {
            $string = $caption;
            $caption = array(
                'caption_text'	=> $string
            );
        }

        return $caption;
    }

    /**
     * Check whether automatic caption appending is enabled.
     *
     * @return  boolean     $enabled    Whether the option is enabled.
     */
    public function auto_append() {
        if ( ! empty( $this->options['auto_append'] ) ) {
            return true;
        } else {
            return false;
        }
    }

}

?>
