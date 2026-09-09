<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;

/** Checks CSS preprocessing without replacing the source bytes used for edits. */
class CSSPreprocessingTest extends TestCase {
	/**
	 * NUL has the value U+FFFD during parsing, but still occupies one source byte.
	 * Other forbidden control bytes must continue to produce bad URL tokens.
	 *
	 * @dataProvider preprocessing_cases
	 */
	public function test_preprocessed_token_keeps_source_bytes( $input, $type, $value ) {
		$processor = CSSProcessor::create( $input );
		$this->assertTrue( $processor->next_token() );
		$this->assertSame( $type, $processor->get_token_type() );
		$this->assertSame( $value, $processor->get_token_value() );
		$this->assertSame( $input, $processor->get_unnormalized_token() );
		$this->assertSame( 0, $processor->get_token_start() );
		$this->assertSame( strlen( $input ), $processor->get_token_length() );
		$this->assertFalse( $processor->next_token() );
		$this->assertSame( $input, $processor->get_updated_css() );
	}

	/**
	 * Restores JSON state at every byte split, including beside NUL and inside CRLF or an escape.
	 * The expected token is specified by the test, not copied from a whole-string parse.
	 *
	 * @dataProvider preprocessing_cases
	 */
	public function test_preprocessing_survives_split_input_and_resume( $input, $type, $value ) {
		for ( $split = 0; $split <= strlen( $input ); ++$split ) {
			$processor = CSSProcessor::create_for_streaming();
			$output = '';
			$tokens = array();
			foreach ( array( substr( $input, 0, $split ), substr( $input, $split ) ) as $index => $chunk ) {
				$processor->append_bytes( $chunk, 1 === $index );
				while ( $processor->next_token() ) {
					$tokens[] = array( $processor->get_token_type(), $processor->get_token_value(), $processor->get_unnormalized_token() );
				}
				$output .= $processor->flush_processed_css();
				$cursor = json_decode( json_encode( $processor->get_reentrancy_cursor() ), true );
				$processor = CSSProcessor::create_for_streaming( $cursor );
			}
			$this->assertSame( array( array( $type, $value, $input ) ), $tokens, 'Split at byte ' . $split );
			$this->assertSame( $input, $output, 'Split at byte ' . $split );
		}
	}

	/** Supplies valid NUL values and nearby cases that must remain invalid CSS URLs. */
	public static function preprocessing_cases() {
		yield 'NUL as the URL' => array( "url(\x00)", CSSProcessor::TOKEN_URL, "\u{FFFD}" );
		yield 'NUL before text' => array( "url(\x00a)", CSSProcessor::TOKEN_URL, "\u{FFFD}a" );
		yield 'NUL inside text' => array( "url(a\x00b)", CSSProcessor::TOKEN_URL, "a\u{FFFD}b" );
		yield 'NUL before closing parenthesis' => array( "url(a\x00)", CSSProcessor::TOKEN_URL, "a\u{FFFD}" );
		yield 'NUL before EOF' => array( "url(a\x00", CSSProcessor::TOKEN_URL, "a\u{FFFD}" );
		yield 'consecutive NUL bytes' => array( "url(a\x00\x00b)", CSSProcessor::TOKEN_URL, "a\u{FFFD}\u{FFFD}b" );
		yield 'escaped literal NUL' => array( "url(a\\\x00b)", CSSProcessor::TOKEN_URL, "a\u{FFFD}b" );
		yield 'zero escape' => array( 'url(a\0 b)', CSSProcessor::TOKEN_URL, "a\u{FFFD}b" );
		yield 'zero escape followed by CRLF' => array( "url(a\\0\r\nb)", CSSProcessor::TOKEN_URL, "a\u{FFFD}b" );
		yield 'NUL followed by trailing CRLF' => array( "url(a\x00\r\n)", CSSProcessor::TOKEN_URL, "a\u{FFFD}" );
		yield 'NUL in a string' => array( "\"a\x00b\"", CSSProcessor::TOKEN_STRING, "a\u{FFFD}b" );
		yield 'NUL in a name' => array( "a\x00b", CSSProcessor::TOKEN_IDENT, "a\u{FFFD}b" );
		yield 'NUL after trailing whitespace' => array( "url(a \x00)", CSSProcessor::TOKEN_BAD_URL, null );
		yield 'NUL after a forbidden control' => array( "url(a\x01\x00b)", CSSProcessor::TOKEN_BAD_URL, null );
		foreach ( array( "\t", "\n", "\r", "\f", "\r\n" ) as $whitespace ) {
			yield 'internal whitespace ' . bin2hex( $whitespace ) => array( 'url(a' . $whitespace . 'b)', CSSProcessor::TOKEN_BAD_URL, null );
		}
		foreach ( array_merge( range( 1, 8 ), array( 0x0B ), range( 0x0E, 0x1F ), array( 0x7F ) ) as $byte ) {
			yield 'forbidden control ' . dechex( $byte ) => array( 'url(a' . chr( $byte ) . 'b)', CSSProcessor::TOKEN_BAD_URL, null );
		}
	}
}
