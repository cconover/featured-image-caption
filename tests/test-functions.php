<?php
/*
 * Test global functions of the plugin.
 */

class FunctionsTest extends WP_UnitTestCase {
    /**
     * Caption set check
     * Should always return true, as this function is deprecated
     */
    function test_caption_set_check() {
        $test = cc_has_featured_image_caption();

        $this->assertTrue( $test );
    }
}
