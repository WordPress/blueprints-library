<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;

/** Checks source-byte boundaries and escapes used when replacing only a URL prefix. */
class CSSValuePrefixTest extends TestCase {
	/** The native ASCII scan must stop before the unmatched filename. */
	public function test_plain_prefix_stops_before_the_suffix() {
		$raw_value = 'https://old.example/photo.png';

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 19, false );

		$this->assertSame( 19, $source_bytes );
		$this->assertSame( '/photo.png', substr( $raw_value, $source_bytes ) );
	}

	/** A four-byte CSS escape represents one decoded byte between two ordinary ASCII spans. */
	public function test_hex_escape_between_plain_spans_counts_its_source_bytes() {
		$raw_value = 'https://\6f ld.example/photo.png';

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 19, false );

		$this->assertSame( 22, $source_bytes );
		$this->assertSame( '/photo.png', substr( $raw_value, $source_bytes ) );
	}

	/** The backslash and CRLF occupy three source bytes but add no decoded bytes. */
	public function test_string_line_continuation_does_not_count_toward_the_decoded_prefix() {
		$raw_value = "https://o\\\r\nld.example/photo.png";

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 19, true );

		$this->assertSame( 22, $source_bytes );
		$this->assertSame( '/photo.png', substr( $raw_value, $source_bytes ) );
	}

	/** The é occupies two UTF-8 bytes; the ASCII scan resumes at z, not inside the character. */
	public function test_unicode_between_plain_spans_keeps_the_suffix_boundary() {
		$raw_value = 'aéz/suffix';

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 4, true );

		$this->assertSame( 4, $source_bytes );
		$this->assertSame( '/suffix', substr( $raw_value, $source_bytes ) );
	}

	/** NUL becomes the three-byte replacement character when the CSS value is decoded. */
	public function test_null_byte_counts_as_three_decoded_bytes() {
		$raw_value = "a\x00z/suffix";

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 5, true );

		$this->assertSame( 3, $source_bytes );
		$this->assertSame( '/suffix', substr( $raw_value, $source_bytes ) );
	}

	/** One incomplete UTF-8 sequence becomes one three-byte replacement character. */
	public function test_invalid_utf8_counts_as_one_replacement_character() {
		$raw_value = "a\xe2\x82z/suffix";

		$source_bytes = CSSProcessor::measure_value_prefix( $raw_value, 5, true );

		$this->assertSame( 4, $source_bytes );
		$this->assertSame( '/suffix', substr( $raw_value, $source_bytes ) );
	}

	/** An empty prefix must not enter the native scan or consume any source bytes. */
	public function test_empty_prefix_consumes_no_source_bytes() {
		$this->assertSame( 0, CSSProcessor::measure_value_prefix( 'https://old.example', 0, false ) );
	}

	/**
	 * Every unsafe byte must trigger escaping even when no other unsafe byte is present.
	 *
	 * @dataProvider unsafe_prefix_bytes
	 * @param string $unsafe_byte The one byte appended to an otherwise plain URL.
	 * @param string $expected_escape Literal CSS bytes that must replace it.
	 */
	public function test_each_unsafe_byte_is_escaped( string $unsafe_byte, string $expected_escape ) {
		$url_prefix = 'https://new.example/' . $unsafe_byte;

		$escaped_prefix = CSSProcessor::escape_value_prefix( $url_prefix );

		$this->assertSame( 'https://new.example/' . $expected_escape, $escaped_prefix );
	}

	/** Pairs each unsafe byte with its literal CSS escape, including the terminating space. */
	public function unsafe_prefix_bytes() {
		return array(
			'NUL' => array( "\x00", '\0 ' ),
			'0x01' => array( "\x01", '\1 ' ),
			'0x02' => array( "\x02", '\2 ' ),
			'0x03' => array( "\x03", '\3 ' ),
			'0x04' => array( "\x04", '\4 ' ),
			'0x05' => array( "\x05", '\5 ' ),
			'0x06' => array( "\x06", '\6 ' ),
			'0x07' => array( "\x07", '\7 ' ),
			'0x08' => array( "\x08", '\8 ' ),
			'tab' => array( "\t", '\9 ' ),
			'line feed' => array( "\n", '\a ' ),
			'vertical tab' => array( "\x0b", '\b ' ),
			'form feed' => array( "\f", '\a ' ),
			'carriage return' => array( "\r", '\a ' ),
			'0x0e' => array( "\x0e", '\e ' ),
			'0x0f' => array( "\x0f", '\f ' ),
			'0x10' => array( "\x10", '\10 ' ),
			'0x11' => array( "\x11", '\11 ' ),
			'0x12' => array( "\x12", '\12 ' ),
			'0x13' => array( "\x13", '\13 ' ),
			'0x14' => array( "\x14", '\14 ' ),
			'0x15' => array( "\x15", '\15 ' ),
			'0x16' => array( "\x16", '\16 ' ),
			'0x17' => array( "\x17", '\17 ' ),
			'0x18' => array( "\x18", '\18 ' ),
			'0x19' => array( "\x19", '\19 ' ),
			'0x1a' => array( "\x1a", '\1a ' ),
			'0x1b' => array( "\x1b", '\1b ' ),
			'0x1c' => array( "\x1c", '\1c ' ),
			'0x1d' => array( "\x1d", '\1d ' ),
			'0x1e' => array( "\x1e", '\1e ' ),
			'0x1f' => array( "\x1f", '\1f ' ),
			'space' => array( ' ', '\20 ' ),
			'DEL' => array( "\x7f", '\7f ' ),
			'backslash' => array( '\\', '\5C ' ),
			'double quote' => array( '"', '\22 ' ),
			'apostrophe' => array( "'", '\27 ' ),
			'opening parenthesis' => array( '(', '\28 ' ),
			'closing parenthesis' => array( ')', '\29 ' ),
		);
	}
}
