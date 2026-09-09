<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Uses the URL iterator in a separate PHP process to rewrite real CSS files. */
class CSSURLContextProcessTest extends TestCase {
	/** @var string */
	private $directory;

	/** @before Creates source, output, and log paths for one caller. */
	public function create_directory() {
		$this->directory = sys_get_temp_dir() . '/css-context-' . bin2hex( random_bytes( 8 ) );
		mkdir( $this->directory );
	}

	/** @after Removes only this test's files. */
	public function remove_directory() {
		foreach ( glob( $this->directory . '/*' ) as $path ) {
			unlink( $path );
		}
		rmdir( $this->directory );
	}

	/** Import and image-set strings are URLs, but comments, MIME types, and displayed text are not. */
	public function test_file_rewrite_finds_only_url_contexts() {
		$input = '/* https://old.example/comment */ @import "https://old.example/theme.css";a{src:image-set("https://old.example/a" 1x,url(https://old.example/b) 2x,"https://old.example/c" type("https://old.example/mime"));content:"https://old.example/text"}';
		$expected = '/* https://old.example/comment */ @import "https://new.example/theme.css";a{src:image-set("https://new.example/a" 1x,url("https://new.example/b") 2x,"https://new.example/c" type("https://old.example/mime"));content:"https://old.example/text"}';
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 0, $this->run_worker(), file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( $expected, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** A malformed import consumes its URL position; a later valid URL must still be found. */
	public function test_file_rewrite_skips_malformed_urls_and_following_text() {
		$input = "@import \"https://old.example/bad\n"
			. '"https://old.example/text";a{src:url(https://old.example/bad(image),url(https://old.example/good)}'
			. '@import/**/"https://old.example/theme.css";';
		$expected = "@import \"https://old.example/bad\n"
			. '"https://old.example/text";a{src:url(https://old.example/bad(image),url("https://new.example/good")}'
			. '@import/**/"https://new.example/theme.css";';
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 0, $this->run_worker(), file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( $expected, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** NUL-containing URLs can be rewritten; other controls still make a URL invalid. */
	public function test_file_rewrite_applies_nul_preprocessing_before_url_validation() {
		$comment = "/* Keep this NUL: \x00 and these line endings: \r\n\f */";
		$bad_url = "a{src:url(https://old.example/bad\x01.png)}";
		$input = $comment . "a{src:url(https://old.example/a\x00b.png)}" . $bad_url
			. 'a{src:url(https://old.example/last.png)}';
		$expected = $comment . "a{src:url(\"https://new.example/a\u{FFFD}b.png\")}" . $bad_url
			. 'a{src:url("https://new.example/last.png")}';
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 0, $this->run_worker(), file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( $expected, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** The caller must not publish a stylesheet after URL-context tracking rejects excessive nesting. */
	public function test_nesting_failure_leaves_the_existing_output_untouched() {
		$input = 'a{src:' . str_repeat( 'image-set(', 129 ) . '"https://old.example/a"' . str_repeat( ')', 129 ) . '}';
		file_put_contents( $this->directory . '/source.css', $input );
		file_put_contents( $this->directory . '/target.css', 'previous output' );
		$this->assertNotSame( 0, $this->run_worker() );
		$this->assertStringContainsString( 'nesting exceeds 128', file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( 'previous output', file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** The existing iterator recognizes import and image-set URLs without matching displayed text. */
	public function test_whole_string_finder_recognizes_import_and_image_set_urls() {
		$css = '@import "https://old.example/theme.css";a{src:image-set("https://old.example/a" 1x,url(https://old.example/b) 2x,"https://old.example/c" 3x);content:"https://old.example/text"}';
		$processor = new CSSURLProcessor( $css );
		$urls = array();
		while ( $processor->next_url() ) {
			$urls[] = $processor->get_raw_url();
		}
		$this->assertSame( array( 'https://old.example/theme.css', 'https://old.example/a', 'https://old.example/b', 'https://old.example/c' ), $urls );
	}

	/** Reading a URL twice must not consume another token or lose an empty URL. */
	public function test_url_reads_leave_the_iterator_on_the_current_url() {
		$css = '@import/**/"theme.css";a{content:"text";src:url(""),url(a.png),image-set("b.png" type("image/png"),"c.png" 2x)}';
		$processor = new CSSURLProcessor( $css );
		foreach ( array( 'theme.css', '', 'a.png', 'b.png', 'c.png' ) as $url ) {
			$this->assertTrue( $processor->next_url() );
			$this->assertSame( $url, $processor->get_raw_url() );
			$this->assertSame( $url, $processor->get_raw_url() );
		}
		$this->assertFalse( $processor->next_url() );
		$this->assertFalse( $processor->next_url() );
	}

	/** Runs a whole-file caller without streamed input or a saved cursor. */
	private function run_worker() {
		$arguments = array( PHP_BINARY, __DIR__ . '/fixtures/css-context/rewrite-file.php', $this->directory );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );
		// Windows cmd.exe strips quotes from this command. Launch PHP directly;
		// the command stays a string for PHP 7.2, which cannot accept an argument array.
		$process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $this->directory . '/worker.log', 'w' ), 2 => array( 'file', $this->directory . '/worker.log', 'a' ) ), $pipes, null, null, array( 'bypass_shell' => true ) );
		$this->assertIsResource( $process );
		fclose( $pipes[0] );
		return proc_close( $process );
	}
}
