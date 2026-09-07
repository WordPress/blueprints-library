<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;

/** The CSS lexer must keep every source byte while forgetting completed input. */
class CSSStreamTest extends TestCase {
	/** @dataProvider corpus */
	public function test_source_bytes_survive_one_byte_input_and_resume( $input ) {
		$processor = CSSProcessor::create_for_streaming();
		$output = '';
		for ( $offset = 0; $offset <= strlen( $input ); ++$offset ) {
			$processor->append_bytes( substr( $input, $offset, 1 ), strlen( $input ) === $offset );
			$steps = 0;
			while ( null !== ( $fragment = $processor->next_token_fragment() ) ) {
				$output .= $fragment['text'];
				$this->assertLessThan( 64, ++$steps, 'The lexer must consume input or finish its current token.' );
			}
			$processor = CSSProcessor::create_for_streaming( $processor->get_reentrancy_cursor() );
		}
		$this->assertSame( $input, $output );
	}

	public static function corpus() {
		$cases = json_decode( file_get_contents( __DIR__ . '/css-test-cases.json' ), true );
		foreach ( $cases as $name => $case ) {
			yield $name => array( $case['css'] );
		}
		yield 'invalid UTF-8' => array( "a{content:\"bad\xff\xc3x\xe2\x82\x80\"}" );
		yield 'UTF-8 at every position' => array( 'a{content:"aé東京😀\1f600 \0000e9 b"}' );
	}

	/** The decoded fragments must use the same UTF-8 and escape rules as a whole value. */
	public function test_decoded_string_fragments_match_the_whole_token() {
		foreach ( array( '"é東京😀\1f600 \0000e9 b"', "\"before\\\r\nafter\\\nend\\\ftail\"", "\"\xc3x\xe2\x82\x80\xff\x00\"" ) as $input ) {
			$whole = CSSProcessor::create( $input );
			$this->assertTrue( $whole->next_token() );
			$expected = $whole->get_token_value();
			$processor = CSSProcessor::create_for_streaming();
			$decoded = '';
			for ( $offset = 0; $offset <= strlen( $input ); ++$offset ) {
				$processor->append_bytes( substr( $input, $offset, 1 ), strlen( $input ) === $offset );
				while ( null !== ( $fragment = $processor->next_token_fragment() ) ) {
					$decoded .= $fragment['value'];
				}
				$processor = CSSProcessor::create_for_streaming( $processor->get_reentrancy_cursor() );
			}
			$this->assertSame( $expected, $decoded );
		}
	}
}
