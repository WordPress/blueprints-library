<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Checks that splitting CSS input and restoring saved state do not change URL rewrite results. */
class CSSURLStreamTest extends TestCase {
	/**
	 * Splits each example at every byte position and restores the parser between the two parts.
	 *
	 * @dataProvider stylesheets
	 */
	public function test_rewrites_and_resumes_at_every_byte( $input, $expected ) {
		$mapping = array( 'https://old.example' => 'http://new.example/local' );
		for ( $split = 0; $split <= strlen( $input ); ++$split ) {
			$processor = CSSURLProcessor::create_for_streaming( $mapping );
			$output = $this->rewrite_chunk( $processor,  substr( $input, 0, $split ), false );
			$cursor = json_decode( json_encode( $processor->get_reentrancy_cursor() ), true );
			$processor = CSSURLProcessor::create_for_streaming( $mapping, $cursor );
			$output .= $this->rewrite_chunk( $processor,  substr( $input, $split ), true );
			$this->assertSame( $expected, $output, 'Split at byte ' . $split );
		}
	}

	/**
	 * Supplies CSS and its expected output, including text that must stay unchanged.
	 * Escapes and malformed CSS use exact strings so unwanted byte changes fail the test.
	 */
	public static function stylesheets() {
		return array(
			'trailing URL spaces' => array( 'a{src:url(https://old.example                       )}', 'a{src:url(http://new.example/local                       )}' ),
			'leading URL spaces' => array( 'a{src:url(                              "https://old.example/a")}', 'a{src:url(                              "http://new.example/local/a")}' ),
			'bad URL keeps its syntax' => array( 'a{src:url(https://old.example/a(broken)}', 'a{src:url(https://old.example/a(broken)}' ),
			'bad string keeps its syntax' => array( "@import \"https://old.example/a\n", "@import \"https://old.example/a\n" ),
			'not a url function' => array( 'a{src:10url("https://old.example/a"),noturl("https://old.example/a")}', 'a{src:10url("https://old.example/a"),noturl("https://old.example/a")}' ),
			'nested image set' => array( 'a{src:image-set(image-set("https://old.example/a" 1x) 1x,"https://old.example/b" type("https://old.example/mime"))}', 'a{src:image-set(image-set("http://new.example/local/a" 1x) 1x,"http://new.example/local/b" type("https://old.example/mime"))}' ),
			'parenthesized resolution' => array( 'a{src:image-set("https://old.example/a" calc(1x * (2 + 3)), "https://old.example/b" 2x)}', 'a{src:image-set("http://new.example/local/a" calc(1x * (2 + 3)), "http://new.example/local/b" 2x)}' ),
			'long non-url names' => array( str_repeat( 'x', 70 ) . 'url("https://old.example/a")', str_repeat( 'x', 70 ) . 'url("https://old.example/a")' ),
			'long numeric dimension' => array( str_repeat( '1', 70 ) . '.23e+45url("https://old.example/a")', str_repeat( '1', 70 ) . '.23e+45url("https://old.example/a")' ),
			'quoted' => array( 'a{background:url("https://old.example/a.png")}', 'a{background:url("http://new.example/local/a.png")}' ),
			'unquoted' => array( 'a{background:url(https://old.example/a.png)}', 'a{background:url(http://new.example/local/a.png)}' ),
			'hex host and function' => array( 'a{background:\\75rl(https://\\6f ld.example/a.png)}', 'a{background:\\75rl(http://new.example/local/a.png)}' ),
			'slash escapes' => array( 'a{background:url(https:\\/\\/old.example\\/a.png)}', 'a{background:url(http://new.example/local\\/a.png)}' ),
			'protocol relative' => array( 'a{src:url(//old.example/font.woff2)}', 'a{src:url(//new.example/local/font.woff2)}' ),
			'import string' => array( '@import "https://old.example/a.css" screen;', '@import "http://new.example/local/a.css" screen;' ),
			'image set' => array( 'a{background:image-set("https://old.example/a.png" 1x, url(https://old.example/b.png) 2x)}', 'a{background:image-set("http://new.example/local/a.png" 1x, url(http://new.example/local/b.png) 2x)}' ),
			'comment and displayed text' => array( '/* url(https://old.example/a) */ a{content:"url(https://old.example/b)"}', '/* url(https://old.example/a) */ a{content:"url(https://old.example/b)"}' ),
			'unrelated and relative' => array( 'a{src:url(../a),url(data:image/png;base64,AAAA),url(https://old.example:8080/a),url(https://old.example.org/a)}', 'a{src:url(../a),url(data:image/png;base64,AAAA),url(https://old.example:8080/a),url(https://old.example.org/a)}' ),
			'EOF string' => array( 'a{src:url("https://old.example/a', 'a{src:url("http://new.example/local/a' ),
			'EOF URL' => array( 'a{src:url(https://old.example/a', 'a{src:url(http://new.example/local/a' ),
			'escaped line continuation' => array( "a{src:url(\"https://old.exa\\\r\nmple/a\")}", 'a{src:url("http://new.example/local/a")}' ),
		);
	}

	/** Sends one byte per call and restores the parser after each byte, including inside escapes. */
	public function test_one_byte_chunks_match_whole_chunk_output() {
		$mapping = array( 'https://old.example' => 'http://new.example/local' );
		foreach ( self::stylesheets() as $case ) {
			$processor = CSSURLProcessor::create_for_streaming( $mapping );
			$output = '';
			for ( $at = 0; $at < strlen( $case[0] ); ++$at ) {
				$output .= $this->rewrite_chunk( $processor,  $case[0][$at], false );
				$processor = CSSURLProcessor::create_for_streaming( $mapping, $processor->get_reentrancy_cursor() );
			}
			$output .= $this->rewrite_chunk( $processor,  '', true );
			$this->assertSame( $case[1], $output );
		}
	}

	/**
	 * Sends a single comment, string, URL, or name across many 32 KiB chunks.
	 * The saved unfinished input must grow until that item ends, then become empty.
	 */
	public function test_large_tokens_are_retained_until_complete_then_released() {
		$mapping = array( 'https://old.example' => 'https://old.example/moved' );
		foreach ( array( array( '/*', '*/' ), array( 'a{content:"', '"}' ), array( 'a{src:url(data:image/png;base64,', ')}' ), array( 'a{src:url(https://old.example/', ')}' ), array( '.long', '{}' ) ) as $token ) {
			$processor = CSSURLProcessor::create_for_streaming( $mapping );
			$input_hash = hash_init( 'sha256' );
			$output_hash = hash_init( 'sha256' );
			$expected_prefix = str_replace( 'https://old.example/', 'https://old.example/moved/', $token[0] );
			hash_update( $input_hash, $expected_prefix );
			hash_update( $output_hash, $this->rewrite_chunk( $processor,  $token[0], false ) );
			$retained_bytes = 0;
			for ( $chunk = 0; $chunk < 128; ++$chunk ) {
				$bytes = str_repeat( 'a', 32768 );
				hash_update( $input_hash, $bytes );
				hash_update( $output_hash, $this->rewrite_chunk( $processor,  $bytes, false ) );
				$cursor = $processor->get_reentrancy_cursor();
				$this->assertGreaterThan( $retained_bytes, strlen( $cursor['css']['pending_b64'] ) );
				$retained_bytes = strlen( $cursor['css']['pending_b64'] );
				$processor = CSSURLProcessor::create_for_streaming( $mapping, $cursor );
			}
			hash_update( $input_hash, $token[1] );
			hash_update( $output_hash, $this->rewrite_chunk( $processor,  $token[1], true ) );
			$this->assertSame( hash_final( $input_hash ), hash_final( $output_hash ) );
			$this->assertSame( '', $processor->get_reentrancy_cursor()['css']['pending_b64'] );
		}
	}
	/**
	 * Expands 1,500 short URLs into over 5 MiB of output.
	 * Each output piece must stay within 64 KiB without holding all replacements in memory.
	 */
	public function test_expanded_output_is_yielded_in_bounded_chunks() {
		$target = 'https://new.example/' . str_repeat( 'a', 4096 );
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => $target ) );
		$input = str_repeat( 'a{src:url(https://old.example/a)}', 1500 );
		$expected = hash_init( 'sha256' );
		for ( $index = 0; $index < 1500; ++$index ) {
			hash_update( $expected, 'a{src:url(' . $target . '/a)}' );
		}
		$actual = hash_init( 'sha256' );
		$bytes = 0;
		$memory = memory_get_usage();
		foreach ( $processor->rewrite_chunk( $input, true ) as $chunk ) {
			$this->assertLessThanOrEqual( 65536, strlen( $chunk ) );
			$this->assertLessThan( 2 * 1024 * 1024, memory_get_usage() - $memory );
			$bytes += strlen( $chunk );
			hash_update( $actual, $chunk );
		}
		$this->assertGreaterThan( 5 * 1024 * 1024, $bytes );
		$this->assertSame( hash_final( $expected ), hash_final( $actual ) );
	}

	/** Stops at the first output piece and checks that saving a cursor is rejected until iteration ends. */
	public function test_cannot_checkpoint_unconsumed_output() {
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://new.example' ) );
		$output = $processor->rewrite_chunk( 'a{src:url(https://old.example/a)}', true );
		$output->rewind();
		$this->expectException( LogicException::class );
		$this->expectExceptionMessage( 'Consume all CSS output chunks before saving a cursor.' );
		$processor->get_reentrancy_cursor();
	}

	/** Changes the target host after saving half a URL; resume must reject the changed rule. */
	public function test_changed_mapping_cannot_resume_an_open_url() {
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://new.example' ) );
		$this->rewrite_chunk( $processor, 'a{src:url("https://old.exa', false );
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'different URL mappings' );
		CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://other.example' ), $processor->get_reentrancy_cursor() );
	}

	/**
	 * Inserts over 1 MiB of backslash-newline pairs between the 'h' and 'ttps://' of a short URL.
	 * Those pairs decode to no characters, but must be retained until the quoted URL ends.
	 */
	public function test_long_escaped_url_is_rewritten_after_its_closing_quote() {
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://new.example' ) );
		$output = $this->rewrite_chunk( $processor, 'a{src:url("h', false );
		for ( $index = 0; $index < 34; ++$index ) {
			$output .= $this->rewrite_chunk( $processor, str_repeat( "\\\n", 16384 ), false );
		}
		$output .= $this->rewrite_chunk( $processor, 'ttps://old.example/a")}', true );
		$this->assertSame( 'a{src:url("https://new.example/a")}', $output );
	}

	/** Sends a URL without its closing ')'; only the preceding CSS may be returned before resume. */
	public function test_unfinished_url_is_not_written_before_its_end() {
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://new.example' ) );
		$output = $this->rewrite_chunk( $processor, 'a{src:url(https://old.example/' . str_repeat( 'a', 65536 ), false );
		$this->assertSame( 'a{src:', $output );
		$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://new.example' ), json_decode( json_encode( $processor->get_reentrancy_cursor() ), true ) );
		$output .= $this->rewrite_chunk( $processor, ')}', true );
		$this->assertSame( 'a{src:url(https://new.example/' . str_repeat( 'a', 65536 ) . ')}', $output );
	}

	/**
	 * Joins output pieces so tests can compare their exact bytes, while checking each piece's size.
	 * The large-output test reads the generator directly so this helper does not affect its memory check.
	 */
	private function rewrite_chunk( CSSURLProcessor $processor, string $input, bool $last ): string {
		$output = '';
		foreach ( $processor->rewrite_chunk( $input, $last ) as $chunk ) {
			$this->assertLessThanOrEqual( 65536, strlen( $chunk ) );
			$output .= $chunk;
		}
		return $output;
	}

}
