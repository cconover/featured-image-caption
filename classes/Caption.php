<?php

/**
 * Caption data.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Caption {
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
     * Access and format the caption data.
     *
     * @param bool $html Whether the result should be fully-formed HTML.
     *                   True: create HTML. False: return raw data array.
     * @param int $post_id The ID of the post for which caption data should
     *                     be retrieved.
     *
     * @return array|null|string If successful, returns the requested result. If unsuccessful, returns null.
     */
    public function caption( $html = true, $post_id = null ) {
        // Set the post ID
        if ( empty( $post_id ) ) {
            global $post;
            $post_id = $post->ID;
        }

        // Get the caption data
        $captiondata = $this->caption_data( $post_id );

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

        return $caption;
    }

    /**
     * Retrieve the caption data.
     *
     * @param int $id The ID of the post or page for which we need the featured image caption.
     *
     * @return bool|array $caption If successful, the array of caption data. If unsuccessful, return false.
     */
    public function caption_data( $id ) {
        // Get the caption data from the post meta
        $caption = get_post_meta( $id, '_' . CCFIC_KEY, true );

        // If caption data is not present, return null
        if ( empty( $caption ) ) {
            return;
        }

        // Legacy support: if caption is a string, convert it to an array
        if ( is_string( $caption ) ) {
            $string = $caption;
            $caption = array(
                'caption_text' => $string
           );
        }

        // Unescape HTML characters
        if ( ! empty( $caption['caption_text'] ) ) {
            wp_kses_decode_entities( $caption['caption_text'] );
        }
        if ( ! empty( $caption['source_text'] ) ) {
            wp_kses_decode_entities( $caption['source_text'] );
        }

        return $caption;
    }

    /**
     * Assemble the caption HTML.
     *
     * @param array $captiondata The caption data for the post retrieved from the database.
     * @param array $atts        The shortcode attributes, from which we determine which caption elements to include.
     *
     * @return string $caption        The fully assembled caption HTML.
     */
    public function html( $captiondata, $atts = array() ) {
        // Initialize the caption HTML
        if (! empty($this->options->container)) {
            // Start with the container <div>
            $caption = '<div class="'.CCFIC_ID.'">';
        } else {
            // Start with an empty string
            $caption = '';
        }

        // Get an instance of the shortcode class
        $shortcode = new Shortcode();

        // Caption text
        if ($shortcode->has_flag('caption-text', $atts) && ! empty($captiondata['caption_text'])) {
            $caption .= '<span class="'.CCFIC_ID.'-text">'.$captiondata['caption_text'].'</span>';
        }

        /* Source attribution */
        // Only move forward if we have source text. Without that, nothing else is useful.
        if (! empty($captiondata['source_text'])) {
            // Source link
            if ($shortcode->has_flag('source-link', $atts) && ! empty($captiondata['source_url'])) {
                // Whether the link should open in a new window
                $new_window = ! empty($captiondata['new_window']) ? ' target="_blank"' : '';

                // Source link HTML
                $caption .= ' <span class="'.CCFIC_ID.'-source"><a href="'.$captiondata['source_url'].'"'.$new_window.'>'.$captiondata['source_text'].'</a></span>';
            } elseif ($shortcode->has_flag('source-text', $atts)) {
                // Caption text, no link
                $caption .= ' <span class="'.CCFIC_ID.'-source">'.$captiondata['source_text'].'</span>';
            }
        }

        // Close the HTML if necessary
        if (! empty($this->options->container)) {
            $caption .= '</div>';
        }

        return $caption;
    }

    /**
     * Assemble the caption as plain text.
     *
     * @param array $captiondata The caption data for the post retrieved from the database.
     * @param array $atts        The shortcode attributes, from which we determine which caption elements to include.
     *
     * @return string $caption        The caption plain text.
     */
    public function plaintext($captiondata, $atts = array())
    {
        // Start with an empty string
        $caption = '';

        // Get an instance of the shortcode class
        $shortcode = new Shortcode();

        // Since the source URL can't be combined with any other caption data in plain text, we'll start with that.
        if ($shortcode->has_flag('source-url', $atts, false) && ! empty($captiondata['source_url'])) {
            return $captiondata['source_url'];
        }

        // Caption text
        if ($shortcode->has_flag('caption-text', $atts) && ! empty($captiondata['caption_text'])) {
            $caption .= $captiondata['caption_text'];
        }

        // Source text
        if ($shortcode->has_flag('source-text', $atts) && ! empty($captiondata['source_text'])) {
            // If the caption text is already part of the caption, we need to put a space before the source text
            if ($caption == $captiondata['caption_text']) {
                $caption .= ' '.$captiondata['source_text'];
            } else {
                $caption .= $captiondata['source_text'];
            }
        }

        return $caption;
    }

    /**
     * Filter the post thumbnail to add our caption data.
     *
     * @param string $html The HTML containing the post thumbnail.
     *
     * @return string $html The updated post thumbnail HTML.
     */
    public function post_thumbnail_filter( $html )
    {
        // If automatic caption append is not enabled or we're not in The Loop, return the HTML unchanged
        if ( empty( $this->options->auto_append ) || ! in_the_loop() ) {
            return $html;
        }

        // If auto-append is enabled and should only be done on single posts
        if ( ! empty( $this->options->auto_append ) && ! empty( $this->options->only_single ) ) {
            // If we're not on a single post
            if ( ! is_single() ) {
                return $html;
            }
        }

        // Get the caption HTML
        $caption = $this->caption();

        // Assemble our HTML to append to the post thumbnail HTML
        $html .= $caption;

        return $html;
    }

    /**
     * Check whether automatic caption appending is enabled.
     *
     * @return bool $enabled    Whether the option is enabled.
     */
    public function auto_append()
    {
        if (! empty($this->options->auto_append)) {
            return true;
        } else {
            return false;
        }
    }
}
