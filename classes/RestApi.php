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
        register_rest_field(
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

        // If there's no caption data, set an empty array
        if ( empty( $data ) ) {
            $data = array();
        }

        // Assemble the caption data into an object
        $response = new \stdClass();
        $response->caption_text = isset( $data['caption_text'] ) ? $data['caption_text'] : false;
        $response->source_text = isset( $data['source_text'] ) ? $data['source_text'] : false;
        $response->source_url = isset( $data['source_url'] ) ? $data['source_url'] : false;

        return $response;
    }
}
