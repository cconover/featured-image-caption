<?php

namespace cconover\FeaturedImageCaption;

/**
 * Add support for the WordPress REST API
 *
 * Add support for the WordPress REST API to the plugin. This adds the plugin
 * data fields to the `posts` response for the REST API core endpoint.
 *
 * @since 0.8.3
 */
class RestApi {
    /**
     * Register the API fields
     *
     * Register the plugin data fields with the REST API `posts` endpoint.
     *
     * @since 0.8.3
     */
    public function register_fields() {
        // Featured image caption data
        register_api_field(
            'post',
            CCFIC_KEY,
            array(
                'get_callback' => array($this, 'rest_caption'),
                'update_callback' => null,
                'schema' => null
            )
        );
    }

    /**
     * Assemble the caption data for the REST API
     *
     * @since 0.8.3
     *
     * @param array $object Details of current post.
     * @param string $field_name Name of field.
     * @param WP_REST_Request $request Current request.
     *
     * @return mixed
     */
    public function rest_caption( $object, $field_name, $request ) {
        // Get the post ID
        $post_id = (int) $object['id'];

        // Instatiate the Caption class
        $caption = new Caption();

        // Get the caption data as non-HTML, raw output
        $data = $caption->caption( false, $post_id );

        // Assemble the caption data into an object
        $response = new \stdClass();

        // Sanitize the data for use with REST
        $sanitized = array();
        foreach ( $data as $key => $value ) {
            // If the field does not have a value, set the value to false
            if ( empty( $value ) ) {
                $value = false;
            }

            // Add to the sanitized array
            $sanitized[$key] = $value;
        }

        $response->caption_text = $sanitized['caption_text'];
        $response->source_text = $sanitized['source_text'];
        $response->source_url = $sanitized['source_url'];

        return $response;
    }
}
