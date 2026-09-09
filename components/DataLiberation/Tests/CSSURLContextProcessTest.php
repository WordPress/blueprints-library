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
