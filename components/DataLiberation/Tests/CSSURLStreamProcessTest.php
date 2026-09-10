<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Rewrites real CSS files in child PHP processes to check output and saved state after a stop. */
class CSSURLStreamProcessTest extends TestCase {
	/** @var string Temporary directory for one test's source, output, saved state, and process log. */
	private $directory;

	/** @before */
	public function create_directory() {
		$this->directory = sys_get_temp_dir() . '/css-stream-' . bin2hex( random_bytes( 8 ) );
		mkdir( $this->directory );
	}

	/** @after */
	public function remove_directory() {
		foreach ( glob( $this->directory . '/*' ) as $path ) {
			unlink( $path );
		}
		rmdir( $this->directory );
	}

	/** A multi-chunk stylesheet with few URLs must finish within the worker's execution budget. */
	public function test_large_stylesheet_rewrite_does_not_rescan_ascii_suffixes() {
		$rules = str_repeat( '.card{color:red;margin:10px}', 100000 );
		$input = $rules . 'a{src:url(https://old.example/photo.png)}';
		$expected = $rules . 'a{src:url("https://new.example/photo.png")}';
		file_put_contents( $this->directory . '/source.css', $input );
		$arguments = array( PHP_BINARY, __DIR__ . '/fixtures/css-stream/rewrite-large-file.php', $this->directory . '/source.css', $this->directory . '/target.css' );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );
		$process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $this->directory . '/worker.log', 'w' ), 2 => array( 'file', $this->directory . '/worker.log', 'a' ) ), $pipes, null, null, array( 'bypass_shell' => true ) );
		$this->assertIsResource( $process );
		fclose( $pipes[0] );
		$this->assertSame( 0, proc_close( $process ), file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( hash( 'sha256', $expected ), hash_file( 'sha256', $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/**
	 * Checks an uninterrupted rewrite and two runs that exit before or after saving state.
	 * A new process must finish each stopped run with the exact expected file bytes.
	 *
	 * @dataProvider interruptions
	 */
	public function test_file_rewrite_resumes_after_process_death( $stop ) {
		// The worker reads 32 KiB at a time. Its first read ends after '\6',
		// inside the '\6f ' escape for 'o'. Its second read ends inside a comment.
		// Both saved positions require the next process to reread unfinished CSS.
		$prefix = '/*' . str_repeat( 'a', 32743 ) . '*/a{src:url("https://\\6f ld.example/a.png")}';
		$input = $prefix . '/*' . str_repeat( 'b', 32768 ) . '*/'
			. '@import "https://old.example/theme.css";'
			. 'a{src:url(https://old.example/' . str_repeat( 'c', 131072 ) . ')}';
		$this->assertSame( '\\6', substr( $input, 32766, 2 ) );
		$whole = new CSSURLProcessor( $input );
		while ( $whole->next_url() ) {
			$whole->set_raw_url( str_replace( 'https://old.example/', 'https://old.example/moved/', $whole->get_raw_url() ) );
		}
		$expected = $whole->get_updated_css();
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 'none' === $stop ? 0 : 99, $this->run_worker( $stop ), file_get_contents( $this->directory . '/worker.log' ) );
		if ( 'none' !== $stop ) {
			$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
			$this->assertGreaterThan( 0, $state['source_bytes'] );
			$this->assertLessThan( strlen( $input ), $state['source_bytes'] );
			$this->assertSame( 0, $this->run_worker( 'none' ), file_get_contents( $this->directory . '/worker.log' ) );
		}
		$this->assertSame( hash( 'sha256', $expected ), hash_file( 'sha256', $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
		$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
		$this->assertSame( strlen( $input ), $state['source_bytes'] );
		$this->assertSame( strlen( $expected ), $state['output_bytes'] );
	}

	/**
	 * Restores the URL position after @import, even when a comment spans the saved offsets.
	 *
	 * @dataProvider interruptions
	 */
	public function test_file_rewrite_resumes_between_import_keyword_and_url( $stop ) {
		// The comment ends at the second 32 KiB read. Both stop modes save a
		// position after @import but before the string that supplies its URL.
		$prefix = 'a{src:url(https://old.example/first)}@import';
		$comment = '/*' . str_repeat( ' ', 65536 - strlen( $prefix ) - 4 ) . '*/';
		$input = $prefix . $comment . '"https://old.example/theme.css";'
			. 'a{content:"https://old.example/text";src:url(https://old.example/bad(image),url(https://old.example/last)}';
		$expected = 'a{src:url("https://old.example/moved/first")}@import' . $comment . '"https://old.example/moved/theme.css";'
			. 'a{content:"https://old.example/text";src:url(https://old.example/bad(image),url("https://old.example/moved/last")}';
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 'none' === $stop ? 0 : 99, $this->run_worker( $stop ), file_get_contents( $this->directory . '/worker.log' ) );
		if ( 'none' !== $stop ) {
			$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
			$this->assertSame( strlen( $prefix ), $state['source_bytes'] );
			$this->assertLessThan( 512, strlen( $state['css'] ) );
			$this->assertSame( 0, $this->run_worker( 'none' ), file_get_contents( $this->directory . '/worker.log' ) );
		}
		$this->assertSame( $expected, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
		$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
		$this->assertSame( strlen( $input ), $state['source_bytes'] );
		$this->assertSame( strlen( $expected ), $state['output_bytes'] );
	}

	/**
	 * Saves part of the file, then reaches 129 nested image-set() calls and fails.
	 * A second process must report the same error and leave the saved state before the file end.
	 */
	public function test_file_rewrite_reports_a_nesting_limit_and_keeps_the_last_checkpoint() {
		$input = '/*' . str_repeat( 'a', 65536 ) . '*/a{src:' . str_repeat( 'image-set(', 129 ) . '"https://old.example/a"' . str_repeat( ')', 129 ) . '}';
		file_put_contents( $this->directory . '/source.css', $input );
		for ( $attempt = 0; $attempt < 2; ++$attempt ) {
			$this->assertNotSame( 0, $this->run_worker( 'none' ) );
			$this->assertStringContainsString( 'nesting exceeds 128', file_get_contents( $this->directory . '/worker.log' ) );
			$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
			$this->assertLessThan( strlen( $input ), $state['source_bytes'] );
			$this->assertTrue( CSSURLProcessor::create_for_streaming( '', $state['css'] )->is_expecting_more_input() );
			$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
		}
	}

	/** A stop after marking EOF must not skip a URL that has not yet been written. */
	public function test_resume_after_process_death_between_eof_and_final_edit() {
		$path = str_repeat( 'a', 65536 );
		$input = '@import "https://old.example/' . $path . '";';
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 99, $this->run_worker( 'eof' ), file_get_contents( $this->directory . '/worker.log' ) );
		$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
		$this->assertSame( strlen( '@import ' ), $state['source_bytes'] );
		$this->assertSame( '@import ', file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( 0, $this->run_worker( 'none' ), file_get_contents( $this->directory . '/worker.log' ) );
		$expected = '@import "https://old.example/moved/' . $path . '";';
		$this->assertSame( $expected, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
		$state = json_decode( file_get_contents( $this->directory . '/state.json' ), true );
		$this->assertSame( strlen( $input ), $state['source_bytes'] );
		$this->assertSame( strlen( $expected ), $state['output_bytes'] );
		$this->assertTrue( CSSURLProcessor::create_for_streaming( '', $state['css'] )->is_finished() );
	}

	/**
	 * Selects normal completion, or exit just before or after the second saved state.
	 * The worker has written the second chunk's output before either exit point.
	 */
	public static function interruptions() {
		return array( array( 'none' ), array( 'before' ), array( 'after' ) );
	}

	/**
	 * Starts the file-rewrite script and waits for its exit code.
	 * Code 0 means completion, 99 means a test stop, and other codes report failures.
	 * The script loads any saved state from the previous process before reading more source bytes.
	 */
	private function run_worker( $stop ) {
		$arguments = array( PHP_BINARY, __DIR__ . '/fixtures/css-stream/rewrite-file.php', $this->directory . '/source.css', $this->directory . '/target.css', $this->directory . '/state.json', $stop );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );
		// Bypass cmd.exe on Windows because it strips the quotes around these
		// paths. Keep a command string: proc_open() in PHP 7.2 cannot take an array.
		$process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $this->directory . '/worker.log', 'w' ), 2 => array( 'file', $this->directory . '/worker.log', 'a' ) ), $pipes, null, null, array( 'bypass_shell' => true ) );
		$this->assertIsResource( $process );
		fclose( $pipes[0] );
		return proc_close( $process );
	}
}
