<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Splitting input and restoring a cursor must preserve the whole-string scan-and-edit behavior. */
class CSSURLStreamTest extends TestCase {
	/**
	 * Replays unfinished source at every split and compares with the existing whole-string API.
	 *
	 * @dataProvider stylesheets
	 */
	public function test_rewrites_and_resumes_at_every_byte( $input ) {
		$whole = new CSSURLProcessor( $input );
		$this->replace_urls( $whole );
		$expected = $whole->get_updated_css();
		for ( $split = 0; $split <= strlen( $input ); ++$split ) {
			$processor = CSSURLProcessor::create_for_streaming( substr( $input, 0, $split ) );
			$this->replace_urls( $processor );
			$output = $processor->flush_processed_css();
			$offset = $processor->get_token_byte_offset_in_the_input_stream();
			$processor = CSSURLProcessor::create_for_streaming( substr( $input, $offset ), $processor->get_reentrancy_cursor() );
			$processor->input_finished();
			$this->replace_urls( $processor );
			$output .= $processor->flush_processed_css();
			$this->assertSame( $expected, $output, 'Split at byte ' . $split );
			$this->assertTrue( $processor->is_finished() );
		}
	}

	/** Covers escapes, malformed tokens, URL context, and tokens accepted only at actual EOF. */
	public static function stylesheets() {
		return array(
			'trailing URL spaces' => array( 'a{src:url(https://old.example                       )}' ),
			'leading URL spaces' => array( 'a{src:url(                              "https://old.example/a")}' ),
			'bad URL keeps its syntax' => array( 'a{src:url(https://old.example/a(broken)}' ),
			'bad string keeps its syntax' => array( "@import \"https://old.example/a\n" ),
			'bad string consumes import position' => array( "@import \"https://old.example/bad\n" . '"https://old.example/text";@import/**/"https://old.example/theme.css";' ),
			'URL position does not carry into text or a bad URL' => array( 'a{src:url(https://old.example/a);content:"https://old.example/text";src:url(https://old.example/bad(image),url(https://old.example/b)}' ),
			'not a url function' => array( 'a{src:10url("https://old.example/a"),noturl("https://old.example/a")}' ),
			'nested image set' => array( 'a{src:image-set(image-set("https://old.example/a" 1x) 1x,"https://old.example/b" type("https://old.example/mime"))}' ),
			'parenthesized resolution' => array( 'a{src:image-set("https://old.example/a" calc(1x * (2 + 3)), "https://old.example/b" 2x)}' ),
			'long non-url names' => array( str_repeat( 'x', 70 ) . 'url("https://old.example/a")' ),
			'long numeric dimension' => array( str_repeat( '1', 70 ) . '.23e+45url("https://old.example/a")' ),
			'quoted' => array( 'a{background:url("https://old.example/a.png")}' ),
			'unquoted' => array( 'a{background:url(https://old.example/a.png)}' ),
			'hex host and function' => array( 'a{background:\\75rl(https://\\6f ld.example/a.png)}' ),
			'slash escapes' => array( 'a{background:url(https:\\/\\/old.example\\/a.png)}' ),
			'protocol relative' => array( 'a{src:url(//old.example/font.woff2)}' ),
			'import string' => array( '@import "https://old.example/a.css" screen;' ),
			'image set' => array( 'a{background:image-set("https://old.example/a.png" 1x, url(https://old.example/b.png) 2x)}' ),
			'comment and displayed text' => array( '/* url(https://old.example/a) */ a{content:"url(https://old.example/b)"}' ),
			'unrelated and relative' => array( 'a{src:url(../a),url(data:image/png;base64,AAAA),url(https://old.example:8080/a),url(https://old.example.org/a)}' ),
			'EOF string' => array( 'a{src:url("https://old.example/a' ),
			'EOF URL' => array( 'a{src:url(https://old.example/a' ),
			'escaped line continuation' => array( "a{src:url(\"https://old.exa\\\r\nmple/a\")}" ),
		);
	}

	/** Restores after every byte, including bytes inside CSS escapes and image-set() strings. */
	public function test_one_byte_chunks_match_whole_string_output() {
		foreach ( self::stylesheets() as $case ) {
			$input = $case[0];
			$whole = new CSSURLProcessor( $input );
			$this->replace_urls( $whole );
			$processor = CSSURLProcessor::create_for_streaming();
			$output = '';
			for ( $at = 0; $at < strlen( $input ); ++$at ) {
				$processor->append_bytes( $input[ $at ] );
				$this->replace_urls( $processor );
				$output .= $processor->flush_processed_css();
				$offset = $processor->get_token_byte_offset_in_the_input_stream();
				$processor = CSSURLProcessor::create_for_streaming( substr( $input, $offset, $at + 1 - $offset ), $processor->get_reentrancy_cursor() );
			}
			$processor->input_finished();
			$this->replace_urls( $processor );
			$output .= $processor->flush_processed_css();
			$this->assertSame( $whole->get_updated_css(), $output );
		}
	}

	/**
	 * Sends one comment, string, URL, or name across 128 reads of 32 KiB each.
	 * The input buffer must grow until that item ends, but the cursor must not copy it.
	 */
	public function test_large_tokens_are_retained_until_complete_then_released() {
		foreach ( array( array( '/*', '*/' ), array( 'a{content:"', '"}' ), array( 'a{src:url(data:image/png;base64,', ')}' ), array( 'a{src:url(https://old.example/', ')}' ), array( '.long', '{}' ) ) as $token ) {
			$input = $token[0] . str_repeat( 'a', 128 * 32768 ) . $token[1];
			$whole = new CSSURLProcessor( $input );
			$this->replace_urls( $whole );
			$expected = hash( 'sha256', $whole->get_updated_css() );
			$processor = CSSURLProcessor::create_for_streaming( $token[0] );
			$this->replace_urls( $processor );
			$output_hash = hash_init( 'sha256' );
			hash_update( $output_hash, $processor->flush_processed_css() );
			$retained_bytes = 0;
			for ( $chunk = 0; $chunk < 128; ++$chunk ) {
				$processor->append_bytes( str_repeat( 'a', 32768 ) );
				$this->replace_urls( $processor );
				hash_update( $output_hash, $processor->flush_processed_css() );
				$this->assertGreaterThan( $retained_bytes, strlen( $processor->get_updated_css() ) );
				$retained_bytes = strlen( $processor->get_updated_css() );
				$cursor = $processor->get_reentrancy_cursor();
				$this->assertLessThan( 512, strlen( $cursor ) );
				$offset = $processor->get_token_byte_offset_in_the_input_stream();
				$processor = CSSURLProcessor::create_for_streaming( substr( $input, $offset, strlen( $token[0] ) + ( $chunk + 1 ) * 32768 - $offset ), $cursor );
			}
			$processor->append_bytes( $token[1] );
			$processor->input_finished();
			$this->replace_urls( $processor );
			hash_update( $output_hash, $processor->flush_processed_css() );
			$this->assertSame( $expected, hash_final( $output_hash ) );
			$this->assertSame( '', $processor->get_updated_css() );
			$this->assertSame( strlen( $input ), $processor->get_token_byte_offset_in_the_input_stream() );
		}
	}

	/**
	 * Expands 1,500 short URLs into over 5 MiB of output.
	 * The caller flushes each edit instead of retaining all replacements until EOF.
	 */
	public function test_caller_can_flush_each_edit_to_bound_expanded_output() {
		$target = 'https://new.example/' . str_repeat( 'a', 4096 );
		$input = str_repeat( 'a{src:url(https://old.example/a)}', 1500 );
		$processor = CSSURLProcessor::create_for_streaming( $input );
		$processor->input_finished();
		$expected = hash_init( 'sha256' );
		for ( $index = 0; $index < 1500; ++$index ) {
			hash_update( $expected, 'a{src:url("' . $target . '/a")}' );
		}
		$actual = hash_init( 'sha256' );
		$bytes = 0;
		$memory = memory_get_usage();
		while ( $processor->next_url() ) {
			$processor->set_raw_url( $target . '/a' );
			$output = $processor->flush_processed_css();
			$this->assertLessThan( 2 * 1024 * 1024, memory_get_usage() - $memory );
			$bytes += strlen( $output );
			hash_update( $actual, $output );
		}
		$output = $processor->flush_processed_css();
		hash_update( $actual, $output );
		$bytes += strlen( $output );
		$this->assertGreaterThan( 5 * 1024 * 1024, $bytes );
		$this->assertSame( hash_final( $expected ), hash_final( $actual ) );
	}

	/** Flushing a large URL returns it whole, with no fixed-size output slices. */
	public function test_large_url_is_flushed_without_splitting() {
		$path = str_repeat( 'a', 131072 );
		$processor = CSSURLProcessor::create_for_streaming( 'url(https://old.example/' . $path . ')' );
		$processor->input_finished();
		$this->assertTrue( $processor->next_url() );
		$processor->set_raw_url( 'https://new.example/' . $path );
		$this->assertSame( 'url("https://new.example/' . $path . '")', $processor->flush_processed_css() );
		$this->assertSame( '', $processor->flush_processed_css() );
	}

	/**
	 * Inserts over 1 MiB of backslash-newline pairs between the 'h' and 'ttps://' of a short URL.
	 * Those pairs decode to no characters, but must be retained until the quoted URL ends.
	 */
	public function test_long_escaped_url_is_rewritten_after_its_closing_quote() {
		$processor = CSSURLProcessor::create_for_streaming( 'a{src:url("h' );
		$this->replace_urls( $processor );
		$output = $processor->flush_processed_css();
		for ( $index = 0; $index < 34; ++$index ) {
			$processor->append_bytes( str_repeat( "\\\n", 16384 ) );
			$this->replace_urls( $processor );
			$output .= $processor->flush_processed_css();
		}
		$processor->append_bytes( 'ttps://old.example/a")}' );
		$processor->input_finished();
		$this->replace_urls( $processor );
		$output .= $processor->flush_processed_css();
		$this->assertSame( 'a{src:url("https://new.example/a")}', $output );
	}

	/** An unfinished URL cannot be written yet; resume must read it again from its start. */
	public function test_unfinished_url_is_not_written_before_its_end() {
		$input = 'a{src:url(https://old.example/' . str_repeat( 'a', 65536 );
		$processor = CSSURLProcessor::create_for_streaming( $input );
		$this->replace_urls( $processor );
		$output = $processor->flush_processed_css();
		$this->assertSame( 'a{src:', $output );
		$offset = $processor->get_token_byte_offset_in_the_input_stream();
		$processor = CSSURLProcessor::create_for_streaming( substr( $input, $offset ) . ')}', $processor->get_reentrancy_cursor() );
		$processor->input_finished();
		$this->replace_urls( $processor );
		$output .= $processor->flush_processed_css();
		$this->assertSame( 'a{src:url("https://new.example/' . str_repeat( 'a', 65536 ) . '")}', $output );
	}

	/** Uses the same caller-selected replacement for whole-string and streamed input. */
	private function replace_urls( CSSURLProcessor $processor ): void {
		while ( $processor->next_url() ) {
			$url = $processor->get_raw_url();
			$replacement = str_replace( 'https://old.example/', 'https://new.example/', $url );
			if ( $url !== $replacement ) {
				$processor->set_raw_url( $replacement );
			}
		}
	}
}
