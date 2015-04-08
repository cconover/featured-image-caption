<?php
// Test the outputs of this plugin

// Theme function
class Theme_Function_Test extends WP_UnitTestCase {
    // No arguments, use function defaults
    function test_no_args() {
        $output = cc_featured_image_caption();
        $this->expectOutputString( $output );
    }
}
