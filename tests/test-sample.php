<?php

class SampleTest extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_string_value() {
		$string = 'Unit tests are sweet';

		$this->assertEquals( 'Unit tests are sweet', $string );
	}
}
