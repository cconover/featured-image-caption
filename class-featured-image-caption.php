<?php

/**
 * Main plugin class.
 */

namespace cconover\FeaturedImageCaption;

class FeaturedImageCaption
{
    // Plugin constants
    const ID            = 'cc-featured-image-caption'; // Plugin ID
    const NAME          = 'Featured Image Caption'; // Plugin name
    const VERSION       = '0.6.2'; // Plugin version
    const WPVER         = '3.5'; // Minimum version of WordPress required for this plugin
    const PHPVER        = '5.2.4'; // Minimum required version of PHP
    const PREFIX        = 'cc_featured_image_caption_'; // Plugin database prefix
    const METAPREFIX    = '_cc_featured_image_caption'; // Post meta database prefix

    // Class properties
    protected $options; // Plugin options and settings
    protected $pluginpath; // Directory path to the plugin root
    protected $pluginurl; // URL to the plugin directory
    protected $pluginfile; // File path to the main plugin file

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Get plugin options from database
        $this->options = get_option(self::PREFIX.'options');

        // Declare properties
        $this->pluginpath = plugin_dir_path(__FILE__);
        $this->pluginurl  = plugin_dir_url(__FILE__);
        $this->pluginfile = $this->pluginpath.'featured-image-caption.php';

        // Hook into post thumbnail
        add_filter('post_thumbnail_html', array($this, 'post_thumbnail_filter'));

        // Shortcode
        add_shortcode('cc-featured-image-caption', array($this, 'shortcode'));
    }

    /**
     * Filter the post thumbnail to add our caption data.
     *
     * @param string $html The HTML containing the post thumbnail.
     *
     * @return string $html The updated post thumbnail HTML.
     */
    public function post_thumbnail_filter($html)
    {
        // If automatic caption append is not enabled, return the HTML unchanged
        if (empty($this->options['auto_append'])) {
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
     * @param array $atts Shortcode attributes.
     *
     * @return string $caption The output for the shortcode to display.
     */
    public function shortcode($atts)
    {
        // Set up the attributes for use. Since all attributes are boolean by declaration, no defaults are set.
        $a = shortcode_atts(
            array(
                'format'    => 'html',
            ),
            $atts
        );

        // Get the caption data
        global $post;
        $captiondata = $this->caption_data($post->ID);

        var_dump($a['format']);

        // Format-specific flags to force format, ordered by presedence
        do {
            // Source link
            if ($this->has_flag('source-link', $atts, false)) {
                $a['format'] = 'html';
                break;
            }

            // Source URL
            if ($this->has_flag('source-url', $atts, false)) {
                $a['format'] = 'plaintext';
                break;
            }
        } while (0);

        var_dump($a['format']);

        // Select the output format
        switch ($a['format']) {
            case 'plaintext':
                $caption = $this->plaintext($captiondata, $atts);
                break;
            default:
                $caption = $this->html($captiondata, $atts);
        }

        return $caption;
    }

    /**
     * Access and format the caption data.
     *
     * @param bool $html Whether the result should be fully-formed HTML.
     *                   True: create HTML. False: return raw data array.
     *
     * @return bool|string $caption If successful, returns the requested result. If unsuccessful, returns false.
     */
    public function caption($html = true)
    {
        // Get the caption data
        global $post;
        $captiondata = $this->caption_data($post->ID);

        // If there is no caption data, return empty.
        if (empty($captiondata)) {
            return;
        }

        // If HTML is not desired, return the raw array
        if (empty($html)) {
            return $captiondata;
        }

        // Get the HTML
        $caption = $this->html($captiondata);

        return $caption;
    }

    /**
     * Retrieve the caption data.
     *
     * @param int $id The ID of the post or page for which we need the featured image caption.
     *
     * @return bool|array $caption If successful, the array of caption data. If unsuccessful, return false.
     */
    private function caption_data($id)
    {
        // Get the caption data from the post meta
        $caption = get_post_meta($id, self::METAPREFIX, true);

        // If caption data is not present, return false
        if (empty($caption)) {
            return false;
        }

        // Legacy support: if caption is a string, convert it to an array
        if (is_string($caption)) {
            $string = $caption;
            $caption = array(
                'caption_text'    => $string,
           );
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
    private function html($captiondata, $atts = array())
    {
        // Initialize the caption HTML
        if (! empty($this->options['container'])) {
            // Start with the container <div>
            $caption = '<div class="'.self::ID.'">';
        } else {
            // Start with an empty string
            $caption = '';
        }

        // Caption text
        if ($this->has_flag('caption-text', $atts) && ! empty($captiondata['caption_text'])) {
            $caption .= '<span class="'.self::ID.'-text">'.$captiondata['caption_text'].'</span>';
        }

        /* Source attribution */
        // Only move forward if we have source text. Without that, nothing else is useful.
        if (! empty($captiondata['source_text'])) {
            // Source link
            if ($this->has_flag('source-link', $atts) && ! empty($captiondata['source_url'])) {
                // Whether the link should open in a new window
                $new_window = ! empty($captiondata['new_window']) ? ' target="_blank"' : '';

                // Source link HTML
                $caption .= ' <span class="'.self::ID.'-source"><a href="'.$captiondata['source_url'].'"'.$new_window.'>'.$captiondata['source_text'].'</a></span>';
            } elseif ($this->has_flag('source-text', $atts)) {
                // Caption text, no link
                $caption .= ' <span class="'.self::ID.'-source">'.$captiondata['source_text'].'</span>';
            }
        }

        // Close the HTML if necessary
        if (! empty($this->options['container'])) {
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
    private function plaintext($captiondata, $atts = array())
    {
        // Start with an empty string
        $caption = '';

        // Since the source URL can't be combined with any other caption data in plain text, we'll start with that.
        if ($this->has_flag('source-url', $atts, false) && ! empty($captiondata['source_url'])) {
            return $captiondata['source_url'];
        }

        // Caption text
        if ($this->has_flag('caption-text', $atts) && ! empty($captiondata['caption_text'])) {
            $caption .= $captiondata['caption_text'];
        }

        // Source text
        if ($this->has_flag('source-text', $atts) && ! empty($captiondata['source_text'])) {
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
     * Check whether the given flag is set in the shortcode attributes.
     *
     * @param string $flag The flag to be checked in the attributes.
     * @param array  $atts The shortcode attributes.
     * @param bool   $all  Whether to return true if $atts is empty, causing the flag to be assumed. Default: true.
     *
     * @return bool $result True if attribute is set or assumed. False if attribute is not set or assumed.
     */
    private function has_flag($flag, $atts, $all = true)
    {
        // If 'format' is set in attributes, remove it
        if (! empty($atts['format'])) {
            unset($atts['format']);
        }

        // If no flags are set and $all is true, return true
        if ($all && empty($atts)) {
            return true;
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

    /**
     * Check whether automatic caption appending is enabled.
     *
     * @return bool $enabled    Whether the option is enabled.
     */
    public function auto_append()
    {
        if (! empty($this->options['auto_append'])) {
            return true;
        } else {
            return false;
        }
    }
}
