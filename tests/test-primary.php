<?php
/*
 * Test functions in the primary plugin file.
 */

class PrimaryTest extends WP_UnitTestCase {
    /**
     * Caption data
     */
    private $postmeta;

    /**
     * Caption output strings.
     */
    private $string;

    /**
     * Class constructor.
     */
    public function __construct() {
        // Create the caption data
        $this->postmeta = array(
            'caption_text' => 'Here is caption text.',
            'source_text' => 'Here is source text.',
            'source_url' => 'http://example.com/',
            'new_window' => true
        );

        // Create object for strings
        $this->string = new \stdClass();

        // HTML string
        $this->string->html = '<div class="cc-featured-image-caption"><span class="cc-featured-image-caption-text">' . $this->postmeta['caption_text'] . '</span> <span class="cc-featured-image-caption-source"><a href="' . $this->postmeta['source_url'] . '" target="_blank">' . $this->postmeta['source_text'] . '</a></span></div>';

        // Plaintext string
        $this->string->plaintext = $this->postmeta['caption_text'] . ' ' . $this->postmeta['source_text'];
    }

    /**
     * Test the theme function with no arguments.
     */
    function test_theme_function_noargs() {
        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata( $post );

        // Add the caption data to the post meta
        add_post_meta( $post->ID, '_cc_featured_image_caption', $this->postmeta );

        $this->expectOutputString( $this->string->html );

        cc_featured_image_caption();
    }

    /**
     * Theme function, echo is true, html is false
     */
    function test_theme_function_plaintext() {
        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata( $post );

        // Add the caption data to the post meta
        add_post_meta( $post->ID, '_cc_featured_image_caption', $this->postmeta );

        $this->expectOutputString( $this->string->plaintext );

        cc_featured_image_caption( true, false );
    }

    /**
     * Theme function, echo is false, html is true
     */
    function test_theme_function_noecho() {
        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata( $post );

        // Add the caption data to the post meta
        add_post_meta( $post->ID, '_cc_featured_image_caption', $this->postmeta );

        $test = cc_featured_image_caption( false );

        $this->assertEquals( $this->string->html, $test );
    }

    /**
     * Theme function, echo is false, html is false
     */
    function test_theme_function_noecho_plaintext() {
        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata( $post );

        // Add the caption data to the post meta
        add_post_meta( $post->ID, '_cc_featured_image_caption', $this->postmeta );

        $test = cc_featured_image_caption( false, false );

        $this->assertEquals( $this->string->plaintext, $test );
    }

    /**
     * Caption set check
     * Should always return true, as this function is deprecated
     */
    function test_caption_set_check() {
        $test = cc_has_featured_image_caption();

        $this->assertTrue( $test );
    }
}
