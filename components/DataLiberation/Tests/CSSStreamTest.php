<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;

/** The CSS lexer must keep every source byte while forgetting completed input. */
class CSSStreamTest extends TestCase {
	/** @dataProvider corpus */
	public function test_source_bytes_survive_one_byte_input_and_resume( $input ) {
		$processor = CSSProcessor::create_for_streaming();
		$output = '';
		$tokens = array();
		$whole_tokens = array();
		$whole = CSSProcessor::create( $input );
		while ( $whole->next_token() ) {
			$whole_tokens[] = array( $whole->get_token_type(), $whole->get_token_value(), $whole->get_unnormalized_token() );
		}
		for ( $offset = 0; $offset <= strlen( $input ); ++$offset ) {
			$processor->append_bytes( substr( $input, $offset, 1 ) );
			if ( strlen( $input ) === $offset ) {
				$processor->input_finished();
			}
			$steps = 0;
			while ( $processor->next_token() ) {
				$tokens[] = array( $processor->get_token_type(), $processor->get_token_value(), $processor->get_unnormalized_token() );
				$this->assertLessThan( 64, ++$steps, 'The lexer must consume input or finish its current token.' );
			}
			$output .= $processor->flush_processed_css();
			$resume_offset = $processor->get_token_byte_offset_in_the_input_stream();
			$processor = CSSProcessor::create_for_streaming( substr( $input, $resume_offset, min( $offset + 1, strlen( $input ) ) - $resume_offset ), $processor->get_reentrancy_cursor() );
		}
		$this->assertSame( $input, $output );
		$this->assertSame( $whole_tokens, $tokens );
	}

	/** Supplies the CSS corpus plus byte sequences that cross UTF-8 and escape boundaries. */
	public static function corpus() {
		$cases = json_decode( file_get_contents( __DIR__ . '/css-test-cases.json' ), true );
		foreach ( $cases as $name => $case ) {
			yield $name => array( $case['css'] );
		}
		yield 'invalid UTF-8' => array( "a{content:\"bad\xff\xc3x\xe2\x82\x80\"}" );
		yield 'UTF-8 at every position' => array( 'a{content:"aé東京😀\1f600 \0000e9 b"}' );
	}

	/** A buffered string uses the same UTF-8 and escape rules as whole-string input. */
	public function test_buffered_strings_match_the_whole_token() {
		foreach ( array( '"é東京😀\1f600 \0000e9 b"', "\"before\\\r\nafter\\\nend\\\ftail\"", "\"\xc3x\xe2\x82\x80\xff\x00\"" ) as $input ) {
			$whole = CSSProcessor::create( $input );
			$this->assertTrue( $whole->next_token() );
			$expected = $whole->get_token_value();
			$processor = CSSProcessor::create_for_streaming();
			$decoded = '';
			for ( $offset = 0; $offset <= strlen( $input ); ++$offset ) {
				$processor->append_bytes( substr( $input, $offset, 1 ) );
				if ( strlen( $input ) === $offset ) {
					$processor->input_finished();
				}
				while ( $processor->next_token() ) {
					$decoded .= $processor->get_token_value();
				}
				$processor->flush_processed_css();
				$resume_offset = $processor->get_token_byte_offset_in_the_input_stream();
				$processor = CSSProcessor::create_for_streaming( substr( $input, $resume_offset, min( $offset + 1, strlen( $input ) ) - $resume_offset ), $processor->get_reentrancy_cursor() );
			}
			$this->assertSame( $expected, $decoded );
		}
	}

	/** The existing whole-token setter must survive flushing and a fresh processor. */
	public function test_buffered_tokens_use_the_existing_value_setter() {
		$processor = CSSProcessor::create_for_streaming();
		$output = '';
		$source = '';
		foreach ( array( 'a{src:url("https://old.exa', 'mple/a");color:red}            ' ) as $input ) {
			$source .= $input;
			$processor->append_bytes( $input );
			while ( $processor->next_token() ) {
				if ( 'https://old.example/a' === $processor->get_token_value() ) {
					$this->assertTrue( $processor->set_token_value( 'https://new.example/moved/a' ) );
				}
			}
			$output .= $processor->flush_processed_css();
			$processor = CSSProcessor::create_for_streaming( substr( $source, $processor->get_token_byte_offset_in_the_input_stream() ), $processor->get_reentrancy_cursor() );
		}
		$processor->input_finished();
		while ( $processor->next_token() ) {}
		$output .= $processor->flush_processed_css();
		$this->assertSame( 'a{src:url("https://new.example/moved/a");color:red}            ', $output );
	}

}
