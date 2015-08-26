<?php

/**
 * Post meta box.
 *
 * @since 0.7.0
 */

namespace cconover\FeaturedImageCaption;

class MetaBox {
    /**
     * Create the meta box.
     *
     * @todo Support for custom post types.
     */
    public function metabox()
    {
        // Specify the screens where the meta box should be available
        $screens = apply_filters('cc_featured_image_caption_screens', array('post', 'page'));

        // Iterate through the specified screens to add the meta box
        foreach ($screens as $screen) {
            add_meta_box(
                CCFIC_ID, // HTML ID for the meta box
                CCFIC_NAME, // Title of the meta box displayed to the us
                array($this, 'metabox_callback'), // Callback function for the meta box to display it to the user
                $screen, // Locations where the meta box should be shown
                'side' // Location where the meta box should be shown. This one is placed on the side.
            );
        }
    }

    /**
     * Featured image caption meta box callback.
     */
    public function metabox_callback($post)
    {
        // Add a nonce field to verify data submissions came from our site
        wp_nonce_field(CCFIC_ID, CCFIC_KEY.'_nonce');

        // Retrieve the current caption as a string, if set
        $caption = get_post_meta($post->ID, '_'.CCFIC_KEY, true);

        // If the data is a string, convert it to an array (legacy data support)
        if (is_string($caption)) {
            $caption = array(
                'caption_text' => $caption,
            );
        }

        echo '<label for="'.CCFIC_KEY.'_caption_text">Caption text</label><textarea style="width: 100%; max-width: 100%;" id="'.CCFIC_KEY.'_caption_text" name="'.CCFIC_KEY.'_caption_text">'.(! empty($caption['caption_text']) ? esc_attr($caption['caption_text']) : null).'</textarea>';
        echo '<br><br>';
        echo '<strong>Source Attribution</strong><br>';
        echo '<label for="'.CCFIC_KEY.'_source_text">Text</label><input type="text" style="width: 100%;" id="'.CCFIC_KEY.'_source_text" name="'.CCFIC_KEY.'_source_text" value="'.(! empty($caption['source_text']) ? esc_attr($caption['source_text']) : null).'">';
        echo '<label for="'.CCFIC_KEY.'_source_url">URL</label><input type="text" style="width: 100%;" id="'.CCFIC_KEY.'_source_url" name="'.CCFIC_KEY.'_source_url" value="'.(! empty($caption['source_url']) ? $caption['source_url'] : null).'">';
        echo '<input type="checkbox" name="'.CCFIC_KEY.'_new_window" value="1"'.($this->new_window_checked($caption) ? ' checked' : null).'><label for="'.CCFIC_KEY.'_new_window">Open in new window</label>';
    }

    /**
     * Save the meta box data.
     */
    public function save_metabox($post_id)
    {
        /*
        Verify using the nonce that the data was submitted from our meta box on our site.
        If it wasn't, return the post ID and be on our way.
        */
        // If no nonce was provided or the nonce does not match
        if (! isset($_POST[CCFIC_KEY.'_nonce']) || ! wp_verify_nonce($_POST[CCFIC_KEY.'_nonce'], CCFIC_ID)) {
            return $post_id;
        }

        // Make sure the user has valid permissions
        // If we're editing a page and the user isn't allowed to do that, return the post ID
        if ('page' == $_POST['post_type']) {
            if (! current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        }
        // If we're editing any other post type and the user isn't allowed to do that, return the post ID
        else {
            if (! current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        // Now that we've validated nonce and permissions, let's save the caption data
        // Sanitize the caption
        $caption = array(
            'caption_text'    => $_POST[CCFIC_KEY.'_caption_text'],
            'source_text'    => $_POST[CCFIC_KEY.'_source_text'],
            'source_url'    => esc_url($_POST[CCFIC_KEY.'_source_url']),
            'new_window'    => (! empty($_POST[CCFIC_KEY.'_new_window']) ? true : false),
        );

        // Update the caption meta field
        update_post_meta($post_id, '_'.CCFIC_KEY, $caption);

        // Update the user default for the "new window" checkbox
        update_user_option(get_current_user_id(), CCFIC_KEY.'_new_window', $caption['new_window']);
    }

    /**
     * Whether the "new window" checkbox should be checked.
     *
     * @param array $caption The caption data
     *
     * @return bool
     */
    public function new_window_checked($caption)
    {
        // If "new window" status is set for the caption data
        if (! empty($caption['new_window'])) {
            if ($caption['new_window']) {
                return true;
            } else {
                return false;
            }
        }
        // If not set, look for the user option
        else {
            $new_window = get_user_option(CCFIC_KEY.'_new_window', get_current_user_id());

            if ($new_window) {
                return true;
            } else {
                return false;
            }
        }
    }
}
