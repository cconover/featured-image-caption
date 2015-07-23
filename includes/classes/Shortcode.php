<?php
/**
 * Shortcode to insert caption data.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class Shortcode {
    /**
     * Shortcode for displaying caption data.
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string $caption The output for the shortcode to display.
     */
    public function shortcode( $atts ) {
        global $post;

        /*
        Set up the attributes. Most attributes are boolean by declaration,
        so they are not given defaults.
        */
        $a = shortcode_atts(
            array(
                'format' => 'html',
            ),
            $atts
        );

        // Create a new instance of the Caption class
        $caption = new Caption();

        // Get the caption data
        $captiondata = $caption->caption_data( $post->ID );

        // Format-specific flags to force format, ordered by presedence
        do {
            // Source link
            if ( $this->has_flag( 'source-link', $atts, false ) ) {
                $a['format'] = 'html';
                break;
            }

            // Source URL
            if ( $this->has_flag( 'source-url', $atts, false ) ) {
                $a['format'] = 'plaintext';
                break;
            }
        } while ( 0 );

        // Select the output format
        switch ( $a['format'] ) {
            case 'plaintext':
                $output = $caption->plaintext( $captiondata, $atts );
                break;
            default:
                $output = $caption->html( $captiondata, $atts );
        }

        return $output;
    }

    /**
     * Check whether the given flag is set in the shortcode attributes.
     *
     * @param string $flag The flag to be checked in the attributes.
     * @param array  $atts The shortcode attributes.
     * @param bool   $all  Whether to return true if $atts is empty, causing the flag to be assumed. Default: true.
     *
     * @return bool $result True if attribute is set or assumed. False if attribute is not set or assumed.
     */
    public function has_flag($flag, $atts, $all = true)
    {
        // If 'format' is set in attributes, remove it
        if (! empty($atts['format'])) {
            unset($atts['format']);
        }

        // If no flags are set
        if (empty($atts)) {
            // If $all is set, which assumes all attributes
            if ( $all ) {
                return true;
            } else {
                return false;
            }
        }

        // Cycle through all attributes and return true when we find our flag
        foreach ($atts as $key => $value) {
            if (is_int($key) && $value == $flag) {
                return true;
            }
        }

        // If nothing has matched
        return false;
    }
}
