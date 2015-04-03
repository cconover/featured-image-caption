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

        // Shortcode
        add_shortcode( 'cc-featured-image-caption', array( &$this, 'shortcode' ) );
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
     * Shortcode for displaying caption data.
     *
     * @param   array   $atts   Shortcode attributes.
     *
     * @return  string  $output The output for the shortcode to display.
     */
    public function shortcode( $atts ) {
        // Set up the attributes for use. Since all attributes are boolean by declaration, no defaults are set.
        $a = shortcode_atts( array(), $atts );

        // Set local variables for each of the attributes
        $caption_text = ( ! empty( $a['caption-text'] ) ) ? true : null;
        $source_text = ( ! empty( $a['source-text'] ) ) ? true : null;
        $source_url = ( ! empty( $a['caption-url'] ) ) ? true : null;
        $plaintext = ( ! empty( $a['plaintext'] ) ) ? true : null;

        // Get access to the $post object
        global $post;

        // Get the caption data
        $captiondata = $this->caption_data( $post->ID );

        // If plain text is set
        if ( ! empty( $plaintext ) ) {
            $caption = $this->plaintext( $captiondata, $caption_text, $source_text, $source_url );
        }

        // HTML
        $caption = $this->html( $captiondata, $caption_text, $source_text, $source_url );

        return $caption;
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

        // Get the HTML
        $caption = $this->html( $captiondata );

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
     * Assemble the caption HTML.
     *
     * @param   array       $captiondata    The caption data for the post retrieved from the database.
     * @param   boolean     $caption_text   Whether to include the caption text. Default: true.
     * @param   boolean     $source_text    Whether to include the source text. Default: true. If false, $source_url is false since we can't have the link without the source text.
     * @param   boolean     $source_url     Whether to include the source URL. Default: true. If true, $source_text is true since we can't have the link without the source text.
     *
     * @return  string      $caption        The fully assembled caption HTML.
     */
    private function html( $captiondata, $caption_text = true, $source_text = true, $source_url = true ) {
        // Initialize the caption HTML
        if ( ! empty( $this->options['container'] ) ) {
            // Start with the container <div>
            $caption = '<div class="' . self::ID . '">';
        } else {
            // Start with an empty string
            $caption = '';
        }

        // Caption text
        if ( ! empty( $caption_text ) && ! empty( $captiondata['caption_text'] ) ) {
            $caption .= '<span class="' . self::ID . '-text">' . $captiondata['caption_text'] . '</span>';
        }

        /* Source attribution */
        // Only move forward if we have source text. Without that, nothing else is useful.
        if ( ! empty( $captiondata['source_text'] ) ) {
            // Source link
            if ( ! empty( $source_url ) && ! empty( $captiondata['source_url'] ) ) {
                // Whether the link should open in a new window
                $new_window = ! empty( $captiondata['new_window'] ) ? ' target="_blank"' : '';

                // Source link HTML
                $caption .= ' <span class="' . self::ID . '-source"><a href="' . $captiondata['source_url'] . '"' . $new_window . '>' . $captiondata['source_text'] . '</a></span>';
            } elseif ( ! empty( $source_text ) ) {
                // Caption text, no link
                $caption .= ' <span class="' . self::ID . '-source">' . $captiondata['source_text'] . '</span>';
            }
        }

        // Close the HTML if necessary
        if ( ! empty( $this->options['container'] ) ) {
            $caption .= '</div>';
        }

        return $caption;
    }

    /**
     * Assemble the caption as plain text.
     *
     * @param   array       $captiondata    The caption data for the post retrieved from the database.
     * @param   boolean     $caption_text   Whether to include the caption text. Default: true.
     * @param   boolean     $source_text    Whether to include the source text. Default: true.
     * @param   boolean     $source_url     Whether to include the source URL. Default: true. If true, other caption filtering parameters are ignored since the source URL can't be returned as plain text with any other elements.
     *
     * @return  string      $caption        The fully assembled caption HTML.
     */
    private function plaintext( $captiondata, $caption_text = true, $source_text = true, $source_url = true ) {
        // Start with an empty string
        $caption = '';

        // Since the source URL can't go as plain text with any other caption elements, we'll start with that
        if ( ! empty( $source_url ) ) {
            // If a source URL is set, we'll use it. Otherwise the caption string will be returned empty.
            if ( ! empty( $captiondata['source_url'] ) ) {
                $caption .= $captiondata['source_url'];
            }

            return $caption;
        }

        // Caption text
        if ( ! empty( $caption_text ) && ! empty( $captiondata['caption_text'] ) ) {
            $caption .= $captiondata['caption_text'];
        }

        // Source text
        if ( ! empty( $source_text ) && ! empty( $captiondata['source_text'] ) ) {
            // If the caption text is already part of the caption, we need to put a space before the source text
            if ( $caption == $captiondata['caption_text'] ) {
                $caption .= ' ' . $captiondata['source_text'];
            } else {
                $caption .= $captiondata['source_text'];
            }
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
