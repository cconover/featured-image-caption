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
     * Set up the test suite properties.
     */
    private function set_test_data() {
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
        $this->string->html = '<div class="ccfic"><span class="ccfic-text">' . $this->postmeta['caption_text'] . '</span> <span class="ccfic-source"><a href="' . $this->postmeta['source_url'] . '" target="_blank">' . $this->postmeta['source_text'] . '</a></span></div>';

        // Plaintext string
        $this->string->plaintext = $this->postmeta['caption_text'] . ' ' . $this->postmeta['source_text'];
    }

    /**
     * Test the theme function with no arguments.
     */
    public function test_theme_function_noargs() {
        // Set up test data
        $this->set_test_data();

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
    public function test_theme_function_plaintext() {
        // Set up test data
        $this->set_test_data();

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
    public function test_theme_function_noecho() {
        // Set up test data
        $this->set_test_data();

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
    public function test_theme_function_noecho_plaintext() {
        // Set up test data
        $this->set_test_data();

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
    public function test_caption_set_check() {
        // Set up test data
        $this->set_test_data();

        $test = cc_has_featured_image_caption();

        $this->assertTrue( $test );
    }
}
