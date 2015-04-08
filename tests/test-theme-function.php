<?php
// Test the outputs of this plugin

namespace cconover\FeaturedImageCaption;

// Theme function
class Theme_Function_Test extends \WP_UnitTestCase
{
    // No arguments, use function defaults
    public function test_no_args()
    {
        $output = cc_featured_image_caption();
        $this->expectOutputString($output);
    }

    // Return result
    public function test_return()
    {
        $output = cc_featured_image_caption(false);
    }

    // Echo is false, HTML is true. HTML should be overridden and return array.
    public function test_echo_html_override()
    {
        $output = cc_featured_image_caption(false, true);
    }
}
