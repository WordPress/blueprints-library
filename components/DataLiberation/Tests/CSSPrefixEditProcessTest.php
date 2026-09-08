<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Edits real CSS files in a separate PHP process, then parses the written output. */
class CSSPrefixEditProcessTest extends TestCase {
	/** @var string */
	private $directory;

	/** @before Creates source, replacement, output, and log paths for one caller. */
	public function create_directory() {
		$this->directory = sys_get_temp_dir() . '/css-prefix-' . bin2hex( random_bytes( 8 ) );
		mkdir( $this->directory );
		file_put_contents( $this->directory . '/replacement.txt', 'https://new.example' );
	}

	/** @after Removes only this test's files. */
	public function remove_directory() {
		foreach ( glob( $this->directory . '/*' ) as $path ) {
			unlink( $path );
		}
		rmdir( $this->directory );
	}

	/** Prefix edits must preserve each wrapper and escaped suffix without adding CSS syntax. */
	public function test_prefix_file_edits_keep_quotes_and_raw_suffixes() {
		$source = 'https://\\6f ld.example';
		$suffix = '\\/photo\\2e png';
		$input = 'a{src:url(' . $source . $suffix . '),url("' . $source . $suffix . '"),url(\'' . $source . $suffix . '\')}';
		$replacement = "https://new.example/(a)'\" " . chr( 1 ) . '\\';
		file_put_contents( $this->directory . '/source.css', $input );
		file_put_contents( $this->directory . '/replacement.txt', $replacement );
		$this->assertSame( 0, $this->run_worker( 'prefix' ), file_get_contents( $this->directory . '/worker.log' ) );
		$output = file_get_contents( $this->directory . '/target.css' );
		$this->assertSame( 3, substr_count( $output, $suffix ) );
		$this->assertSame( 3, substr_count( $output, 'url(' ) );
		$this->assertSame( 2, substr_count( $output, '"' ) );
		$this->assertSame( 2, substr_count( $output, "'" ) );
		$processor = new CSSURLProcessor( $output );
		for ( $index = 0; $index < 3; ++$index ) {
			$this->assertTrue( $processor->next_url() );
			$this->assertSame( $replacement . '/photo.png', $processor->get_raw_url() );
		}
		$this->assertFalse( $processor->next_url() );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** Malformed URL and string tokens must not acquire a partially replaced prefix. */
	public function test_prefix_file_edit_leaves_malformed_tokens_unchanged() {
		$input = "a{src:url(https://old.example/bad(url)}\n@import \"https://old.example/bad\n";
		file_put_contents( $this->directory . '/source.css', $input );
		$this->assertSame( 0, $this->run_worker( 'prefix' ), file_get_contents( $this->directory . '/worker.log' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/target.css' ) );
		$this->assertSame( $input, file_get_contents( $this->directory . '/source.css' ) );
	}

	/** CRLF becomes one newline; a lone CR must not swallow the following character. */
	public function test_whole_value_file_edit_preserves_text_after_carriage_returns() {
		file_put_contents( $this->directory . '/source.css', 'a{src:url(https://old.example/a)}' );
		file_put_contents( $this->directory . '/replacement.txt', "https://new.example/a\r\nb\rX\nc" );
		$this->assertSame( 0, $this->run_worker( 'whole' ), file_get_contents( $this->directory . '/worker.log' ) );
		$processor = new CSSURLProcessor( file_get_contents( $this->directory . '/target.css' ) );
		$this->assertTrue( $processor->next_url() );
		$this->assertSame( "https://new.example/a\nb\nX\nc", $processor->get_raw_url() );
	}

	/** Replacement bytes must stay inside the value in all three URL quoting forms. */
	public function test_replacement_prefix_escapes_controls_and_delimiters() {
		$input = "https://new.example/" . chr( 1 ) . "\rX\n \"'()";
		$escaped = CSSProcessor::escape_value_prefix( $input );
		foreach ( array( 'url(' . $escaped . ')', 'url("' . $escaped . '")', "url('" . $escaped . "')" ) as $css ) {
			$processor = new CSSURLProcessor( $css );
			$this->assertTrue( $processor->next_url() );
			$this->assertSame( str_replace( "\r", "\n", $input ), $processor->get_raw_url() );
		}
	}

	/** Runs a whole-file caller without streamed input or a saved cursor. */
	private function run_worker( $mode ) {
		$arguments = array( PHP_BINARY, __DIR__ . '/fixtures/css-prefix/edit-file.php', $this->directory, $mode );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );
		// Windows cmd.exe strips quotes from this command. Launch PHP directly;
		// the command stays a string for PHP 7.2, which cannot accept an argument array.
		$process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $this->directory . '/worker.log', 'w' ), 2 => array( 'file', $this->directory . '/worker.log', 'a' ) ), $pipes, null, null, array( 'bypass_shell' => true ) );
		$this->assertIsResource( $process );
		fclose( $pipes[0] );
		return proc_close( $process );
	}
}
