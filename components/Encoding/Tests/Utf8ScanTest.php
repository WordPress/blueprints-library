<?php

use PHPUnit\Framework\TestCase;
use function WordPress\Encoding\compat\_wp_scan_utf8;

class Utf8ScanTest extends TestCase {

	/** @dataProvider scan_limits */
	public function test_scan_stops_at_the_requested_byte_or_character_limit( $bytes, $start, $max_bytes, $max_characters, $expected_count, $expected_offset, $expected_invalid ) {
		$at = $start;
		$invalid_length = 0;
		$count = _wp_scan_utf8( $bytes, $at, $invalid_length, $max_bytes, $max_characters );

		$this->assertSame( $expected_count, $count );
		$this->assertSame( $expected_offset, $at );
		$this->assertSame( $expected_invalid, $invalid_length );
	}

	public static function scan_limits() {
		return array(
			'one ASCII character' => array( 'abcdef', 0, null, 1, 1, 1, 0 ),
			'no characters' => array( 'abcdef', 0, null, 0, 0, 0, 0 ),
			'nonzero offset' => array( 'abcdef', 2, null, 2, 2, 4, 0 ),
			'byte limit first' => array( 'abcdef', 0, 2, 4, 2, 2, 0 ),
			'character limit first' => array( 'abcdef', 0, 4, 2, 2, 2, 0 ),
			'unlimited scan' => array( 'abcdef', 0, null, null, 6, 6, 0 ),
			'limit beyond input' => array( 'abcdef', 0, null, 10, 6, 6, 0 ),
			'multibyte then ASCII' => array( "\xc3\xb1abcd", 0, null, 2, 2, 3, 0 ),
			'stop after multibyte' => array( "\xc3\xb1abcd", 0, null, 1, 1, 2, 0 ),
			'ASCII then multibyte' => array( "a\xc3\xb1b", 0, null, 2, 2, 3, 0 ),
			'invalid byte beyond limit' => array( "ab\xff", 0, null, 2, 2, 2, 0 ),
			'invalid byte before limit' => array( "ab\xff", 0, null, 3, 2, 2, 1 ),
		);
	}
}
