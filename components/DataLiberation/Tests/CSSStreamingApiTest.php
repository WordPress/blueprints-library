<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Both CSS processors accept more input without changing their scan-and-edit methods. */
class CSSStreamingApiTest extends TestCase {
	/** @dataProvider processors */
	public function test_initial_input_pauses_then_uses_the_existing_setter( $class, $scan, $get_value, $set_value ) {
		$processor = $class::create_for_streaming( 'url(https://old.exa' );
		$this->assertTrue( $processor->is_expecting_more_input() );
		$this->assertFalse( $processor->$scan() );
		$this->assertTrue( $processor->is_paused_at_incomplete_input() );
		$this->assertFalse( $processor->$scan() );
		$this->assertTrue( $processor->is_paused_at_incomplete_input() );
		$this->assertFalse( $processor->is_finished() );
		$processor->append_bytes( 'mple/a)' );
		$this->assertFalse( $processor->is_paused_at_incomplete_input() );
		$processor->input_finished();
		$this->assertFalse( $processor->is_expecting_more_input() );
		$this->assertTrue( $processor->$scan() );
		$this->assertSame( 'https://old.example/a', $processor->$get_value() );
		$this->assertTrue( $processor->$set_value( 'https://new.example/a' ) );
		$this->assertSame( 'url("https://new.example/a")', $processor->get_updated_css() );
		$this->assertFalse( $processor->$scan() );
		$this->assertTrue( $processor->is_finished() );
		$this->assertFalse( $processor->is_paused_at_incomplete_input() );
		$this->assertFalse( $processor->$scan() );
	}

	/** Appending must not discard earlier edits or require the caller to flush them first. @dataProvider processors */
	public function test_append_keeps_unflushed_edits( $class, $scan, $get_value, $set_value ) {
		$processor = $class::create_for_streaming( 'url(first.png)            ' );
		$this->assertTrue( $processor->$scan() );
		$this->assertTrue( $processor->$set_value( 'changed.png' ) );
		$processor->append_bytes( 'url(second.png)' );
		$processor->input_finished();
		$values = array();
		while ( $processor->$scan() ) {
			if ( null !== $processor->$get_value() ) {
				$values[] = $processor->$get_value();
			}
		}
		$this->assertSame( array( 'second.png' ), $values );
		$this->assertSame( 'url("changed.png")            url(second.png)', $processor->get_updated_css() );
	}

	/** Like XML, a cursor at a token replays that token from the original source. @dataProvider processors */
	public function test_resume_replays_the_current_token_without_saving_edits( $class, $scan, $get_value, $set_value ) {
		$input = 'url(first.png) url(second.png)';
		$processor = $class::create_for_streaming( $input );
		$processor->input_finished();
		$this->assertTrue( $processor->$scan() );
		$this->assertTrue( $processor->$set_value( 'changed.png' ) );
		$offset = $processor->get_token_byte_offset_in_the_input_stream();
		$cursor = $processor->get_reentrancy_cursor();
		$this->assertSame( 0, $offset );
		$this->assertIsString( $cursor );
		$resumed = $class::create_for_streaming( substr( $input, $offset ), $cursor );
		$this->assertTrue( $resumed->$scan() );
		$this->assertSame( 'first.png', $resumed->$get_value() );
		$this->assertSame( $input, $resumed->get_updated_css() );
	}

	/** Unfinished bytes are reread from the source, rather than copied into the cursor. @dataProvider processors */
	public function test_resume_after_flushing_rereads_the_unfinished_token( $class, $scan, $get_value, $set_value ) {
		$prefix = 'url(first.png) ';
		$pending = '/*' . str_repeat( 'a', 131072 );
		$processor = $class::create_for_streaming( $prefix . $pending );
		while ( $processor->$scan() ) {}
		$this->assertTrue( $processor->is_paused_at_incomplete_input() );
		$this->assertSame( $prefix, $processor->flush_processed_css() );
		$offset = $processor->get_token_byte_offset_in_the_input_stream();
		$cursor = $processor->get_reentrancy_cursor();
		$this->assertSame( strlen( $prefix ), $offset );
		$this->assertIsString( $cursor );
		$this->assertLessThan( 512, strlen( $cursor ) );
		$input = $prefix . $pending . '*/url(second.png)';
		$resumed = $class::create_for_streaming( substr( $input, $offset ), $cursor );
		$resumed->input_finished();
		$values = array();
		while ( $resumed->$scan() ) {
			if ( null !== $resumed->$get_value() ) {
				$values[] = $resumed->$get_value();
			}
		}
		$this->assertSame( array( 'second.png' ), $values );
		$this->assertSame( substr( $input, $offset ), $resumed->flush_processed_css() );
		$this->assertSame( strlen( $input ), $resumed->get_token_byte_offset_in_the_input_stream() );
		$this->assertTrue( $resumed->is_finished() );
	}

	/** Marking EOF twice is harmless; supplying more input afterwards is an error. @dataProvider processors */
	public function test_cannot_append_after_input_finished( $class, $scan, $get_value, $set_value ) {
		$processor = $class::create_for_streaming();
		$processor->input_finished();
		$processor->input_finished();
		$this->assertFalse( $processor->$scan() );
		$this->assertTrue( $processor->is_finished() );
		$this->expectException( LogicException::class );
		$this->expectExceptionMessage( 'after the end of the stylesheet' );
		$processor->append_bytes( 'url(late.png)' );
	}

	/** Malformed cursor input must not silently start a new parse. @dataProvider processors */
	public function test_rejects_corrupt_cursor( $class, $scan, $get_value, $set_value ) {
		$this->expectException( InvalidArgumentException::class );
		$class::create_for_streaming( '', 'not a cursor' );
	}

	/**
	 * Restoring on a string must keep the URL meaning given by its preceding syntax.
	 *
	 * @dataProvider url_contexts
	 */
	public function test_url_resume_replays_the_current_string( $input ) {
		$processor = CSSURLProcessor::create_for_streaming( $input );
		$processor->input_finished();
		$this->assertTrue( $processor->next_url() );
		$offset = $processor->get_token_byte_offset_in_the_input_stream();
		$resumed = CSSURLProcessor::create_for_streaming( substr( $input, $offset ), $processor->get_reentrancy_cursor() );
		$this->assertTrue( $resumed->next_url() );
		$this->assertSame( 'first.png', $resumed->get_raw_url() );
		$this->assertTrue( $resumed->next_url() );
		$this->assertSame( 'second.png', $resumed->get_raw_url() );
		$this->assertFalse( $resumed->next_url() );
	}

	/** These strings are URLs only because of syntax before the saved byte offset. */
	public static function url_contexts() {
		return array(
			'import' => array( '@import "first.png"; @import "second.png";' ),
			'quoted url' => array( 'a{src:url("first.png"),url("second.png")}' ),
			'image set' => array( 'a{src:image-set("first.png" type("image/png"),"second.png" 2x)}' ),
		);
	}

	/** The APIs differ only in whether the caller scans all tokens or URL values. */
	public static function processors() {
		return array(
			'tokens' => array( CSSProcessor::class, 'next_token', 'get_token_value', 'set_token_value' ),
			'URLs' => array( CSSURLProcessor::class, 'next_url', 'get_raw_url', 'set_raw_url' ),
		);
	}
}
