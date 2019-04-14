<?php

/*
 * Test the methods in the Caption class.
 */

class CaptionTest extends WP_UnitTestCase
{
    /**
     * Instance of the Caption class.
     */
    private $caption;

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
    private function set_test_data()
    {
        // Instantiate the Caption class instance
        $this->caption = new \cconover\FeaturedImageCaption\Caption();

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
     * caption() method, no arguments.
     */
    public function test_caption()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Add the caption data to the post meta
        add_post_meta($post->ID, '_cc_featured_image_caption', $this->postmeta);

        // Get the output from the method
        $test = $this->caption->caption();

        $this->assertEquals($this->string->html, $test);
    }

    /**
     * caption() method, return array.
     */
    public function test_caption_array()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Add the caption data to the post meta
        add_post_meta($post->ID, '_cc_featured_image_caption', $this->postmeta);

        // Get the output from the method
        $test = $this->caption->caption(false);

        // Array keys are all present
        $this->assertArrayHasKey('caption_text', $test);
        $this->assertArrayHasKey('source_text', $test);
        $this->assertArrayHasKey('source_url', $test);
        $this->assertArrayHasKey('new_window', $test);

        // Contents of each array element
        $this->assertEquals($this->postmeta['caption_text'], $test['caption_text']);
        $this->assertEquals($this->postmeta['source_text'], $test['source_text']);
        $this->assertEquals($this->postmeta['source_url'], $test['source_url']);
        $this->assertTrue($test['new_window']);
    }

    /**
     * caption() method, no data.
     */
    public function test_caption_nodata()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Get the output from the method
        $test = $this->caption->caption();

        $this->assertNull($test);
    }

    /**
     * caption() method, no data, return array.
     */
    public function test_caption_nodata_array()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Get the output from the method
        $test = $this->caption->caption(false);

        $this->assertNull($test);
    }

    /**
     * caption_data() method
     */
    public function test_caption_data()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Add the caption data to the post meta
        add_post_meta($post->ID, '_cc_featured_image_caption', $this->postmeta);

        // Get the output of the method
        $test = $this->caption->caption_data($post->ID);

        // Array keys are all present
        $this->assertArrayHasKey('caption_text', $test);
        $this->assertArrayHasKey('source_text', $test);
        $this->assertArrayHasKey('source_url', $test);
        $this->assertArrayHasKey('new_window', $test);

        // Contents of each array element
        $this->assertEquals($this->postmeta['caption_text'], $test['caption_text']);
        $this->assertEquals($this->postmeta['source_text'], $test['source_text']);
        $this->assertEquals($this->postmeta['source_url'], $test['source_url']);
        $this->assertTrue($test['new_window']);
    }

    /**
     * caption_data() method, no data.
     */
    public function test_caption_data_nodata()
    {
        // Set up test data
        $this->set_test_data();

        global $post;

        // Create a new post
        $post = $this->factory->post->create_and_get();
        setup_postdata($post);

        // Get the output from the method
        $test = $this->caption->caption_data($post->ID);

        $this->assertNull($test);
    }

    /**
     * html() method, no shortcode attributes.
     */
    public function test_html()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta);

        $this->assertEquals($this->string->html, $test);
    }

    /**
     * html() method, 'caption-text' shortcode attribute.
     */
    public function test_html_caption_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'caption-text' ));

        // Expected output
        $string = '<div class="ccfic"><span class="ccfic-text">' . $this->postmeta['caption_text'] . '</span></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * html() method, 'source-text' shortcode attribute.
     */
    public function test_html_source_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'source-text' ));

        // Expected output
        $string = '<div class="ccfic"> <span class="ccfic-source">' . $this->postmeta['source_text'] . '</span></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * html() method, 'caption-text' and 'source-text' shortcode attributes.
     */
    public function test_html_caption_text_source_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'caption-text', 'source-text' ));

        // Expected output
        $string = '<div class="ccfic"><span class="ccfic-text">' . $this->postmeta['caption_text'] . '</span> <span class="ccfic-source">' . $this->postmeta['source_text'] . '</span></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * html() method, just 'source-link' shortcode attribute.
     */
    public function test_html_source_link()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'source-link' ));

        // Expected output
        $string = '<div class="ccfic"> <span class="ccfic-source"><a href="' . $this->postmeta['source_url'] . '" target="_blank">' . $this->postmeta['source_text'] . '</a></span></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * html() method, all shortcode attributes.
     */
    public function test_html_all_shortcode_atts()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'caption-text', 'source-text', 'source-link' ));

        $this->assertEquals($this->string->html, $test);
    }

    /**
     * html() method, gibberish shortcode attribute.
     */
    public function test_html_gibberish_shortcode_att()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'faw3aardvwasfeasdfasdf' ));

        // Expected output
        $string = '<div class="ccfic"></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * html() method, gibberish shortcode attribute combined with real attribute.
     */
    public function test_html_gibberish_combo_shortcode_atts()
    {
        // Set up test data
        $this->set_test_data();

        // Call the html() method
        $test = $this->caption->html($this->postmeta, array( 'faw3aardvwasfeasdfasdf', 'caption-text' ));

        // Expected output
        $string = '<div class="ccfic"><span class="ccfic-text">' . $this->postmeta['caption_text'] . '</span></div>';

        $this->assertEquals($string, $test);
    }

    /**
     * plaintext() method, no shortcode attributes.
     */
    public function test_plaintext()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta);

        $this->assertEquals($this->string->plaintext, $test);
    }

    /**
     * plaintext() method, 'caption-text' shortcode attribute.
     */
    public function test_plaintext_caption_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( 'caption-text' ));

        $this->assertEquals($this->postmeta['caption_text'], $test);
    }

    /**
     * plaintext() method, 'source-text' shortcode attribute.
     */
    public function test_plaintext_source_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( 'source-text' ));

        $this->assertEquals($this->postmeta['source_text'], $test);
    }

    /**
     * plaintext() method, 'caption-text' and 'source-text' shortcode attributes.
     */
    public function test_plaintext_caption_text_source_text()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( 'caption-text', 'source-text' ));

        // Expected output
        $string = $this->postmeta['caption_text'] . ' ' . $this->postmeta['source_text'];

        $this->assertEquals($string, $test);
    }

    /**
     * plaintext() method, just 'source-url' shortcode attribute.
     */
    public function test_plaintext_source_url()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( 'source-url' ));

        $this->assertEquals($this->postmeta['source_url'], $test);
    }

    /**
     * plaintext() method, all shortcode attributes.
     */
    public function test_plaintext_all_shortcode_atts()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( 'caption-text', 'source-text', 'source-url' ));

        $this->assertEquals($this->postmeta['source_url'], $test);
    }

    /**
     * plaintext() method, gibberish shortcode attribute.
     */
    public function test_plaintext_gibberish_shortcode_att()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( '65rgtfythdrgs' ));

        $this->assertEmpty($test);
    }

    /**
     * plaintext() method, gibberish shortcode attribute combined with real attribute.
     */
    public function test_plaintext_gibberish_combo_shortcode_atts()
    {
        // Set up test data
        $this->set_test_data();

        // Call the plaintext() method
        $test = $this->caption->plaintext($this->postmeta, array( '65rgtfythdrgs', 'caption-text' ));

        $this->assertEquals($this->postmeta['caption_text'], $test);
    }

    /**
     * auto_append() method
     */
    public function test_auto_append()
    {
        // Set up test data
        $this->set_test_data();

        $test = $this->caption->auto_append();

        $this->assertTrue($test);
    }
}
