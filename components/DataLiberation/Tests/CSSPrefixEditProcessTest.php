<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\CSS\CSSProcessor;
use WordPress\DataLiberation\URL\CSSURLProcessor;

/** Rewrites real CSS files in a separate PHP process and checks their resulting contents. */
class CSSPrefixEditProcessTest extends TestCase {
	/** @var string Temporary directory containing this test's input, replacement, output, and log. */
	private $directory;

	/** @before Gives each test its own files; no replacement is selected implicitly. */
	public function create_directory() {
		$this->directory = sys_get_temp_dir() . '/css-prefix-' . bin2hex( random_bytes( 8 ) );
		mkdir( $this->directory );
	}

	/** @after Removes only this test's files. */
	public function remove_directory() {
		foreach ( glob( $this->directory . '/*' ) as $path ) {
			unlink( $path );
		}
		rmdir( $this->directory );
	}

	/** Only the host changes; the three quoting styles and escaped filename stay as written. */
	public function test_prefix_replacement_preserves_quotes_and_escaped_filenames() {
		// In CSS, \6f decodes to "o", \/ to "/", and \2e to ".".
		$input_css = <<<'CSS'
a{src:url(https://\6f ld.example\/photo\2e png)}
b{src:url("https://\6f ld.example\/photo\2e png")}
c{src:url('https://\6f ld.example\/photo\2e png')}
CSS;
		$expected_css = <<<'CSS'
a{src:url(https://new.example\/photo\2e png)}
b{src:url("https://new.example\/photo\2e png")}
c{src:url('https://new.example\/photo\2e png')}
CSS;

		$actual_css = $this->replace_url_prefix_in_file( $input_css, 'https://new.example' );

		$this->assertSame( $expected_css, $actual_css );
	}

	/** Quotes, parentheses, spaces, and backslashes in the new prefix must remain URL data. */
	public function test_replacement_characters_cannot_close_a_quote_or_url_function() {
		$input_css = <<<'CSS'
a{src:url(https://old.example/photo.png)}
b{src:url("https://old.example/photo.png")}
c{src:url('https://old.example/photo.png')}
CSS;
		$replacement_prefix = <<<'URL'
https://new.example/(a)'" \
URL;
		// CSS hex escapes: ( = \28, ) = \29, ' = \27, " = \22, space = \20, \ = \5C.
		// The space after each hex escape ends that escape; it is not part of the URL.
		$expected_css = <<<'CSS'
a{src:url(https://new.example/\28 a\29 \27 \22 \20 \5C /photo.png)}
b{src:url("https://new.example/\28 a\29 \27 \22 \20 \5C /photo.png")}
c{src:url('https://new.example/\28 a\29 \27 \22 \20 \5C /photo.png')}
CSS;
		$expected_decoded_url = <<<'URL'
https://new.example/(a)'" \/photo.png
URL;

		$actual_css = $this->replace_url_prefix_in_file( $input_css, $replacement_prefix );

		$this->assertSame( $expected_css, $actual_css );
		$url_reader = new CSSURLProcessor( $actual_css );
		foreach ( array( 'unquoted', 'double quoted', 'single quoted' ) as $quoting_style ) {
			$this->assertTrue( $url_reader->next_url(), $quoting_style );
			$this->assertSame( $expected_decoded_url, $url_reader->get_raw_url(), $quoting_style );
		}
		$this->assertFalse( $url_reader->next_url(), 'Escaping must not introduce another URL.' );
	}

	/** An unescaped opening parenthesis makes an unquoted URL invalid. Leave it untouched. */
	public function test_invalid_unquoted_url_is_copied_unchanged() {
		$input_css = 'a{src:url(https://old.example/bad(url)}';

		$actual_css = $this->replace_url_prefix_in_file( $input_css, 'https://new.example' );

		$this->assertSame( $input_css, $actual_css );
	}

	/** A string can split the hostname across lines without adding a newline to its URL. */
	public function test_prefix_replacement_skips_string_line_continuations() {
		// Backslash followed by a newline joins "o" and "ld.example" into "old.example".
		$input_css = <<<'CSS'
a{src:url("https://o\
ld.example\/photo\2e png")}
b{src:url('https://o\
ld.example\/photo\2e png')}
CSS;
		$expected_css = <<<'CSS'
a{src:url("https://new.example\/photo\2e png")}
b{src:url('https://new.example\/photo\2e png')}
CSS;

		$actual_css = $this->replace_url_prefix_in_file( $input_css, 'https://new.example' );

		$this->assertSame( $expected_css, $actual_css );
	}

	/** A CRLF in the new prefix must become one escape without escaping its inserted space again. */
	public function test_prefix_replacement_encodes_crlf_once_and_keeps_the_suffix() {
		$input_css = 'a{src:url(https://old.example/photo.png)}';
		$replacement_prefix = "https://new.example/first\r\nsecond";
		$expected_css = 'a{src:url(https://new.example/first\a second/photo.png)}';

		$actual_css = $this->replace_url_prefix_in_file( $input_css, $replacement_prefix );

		$this->assertSame( $expected_css, $actual_css );
	}

	/** A literal newline ends this string before a closing quote. Do not rewrite its prefix. */
	public function test_string_with_an_unescaped_newline_is_copied_unchanged() {
		$input_css = <<<'CSS'
@import "https://old.example/bad
next-line;
CSS;

		$actual_css = $this->replace_url_prefix_in_file( $input_css, 'https://new.example' );

		$this->assertSame( $input_css, $actual_css );
	}

	/** A Windows-style CRLF line ending becomes one CSS newline escape, not two. */
	public function test_whole_url_replacement_encodes_crlf_as_one_newline() {
		$input_css = 'a{src:url(https://old.example/photo.png)}';
		$replacement_url = "https://new.example/first\r\nsecond";
		$expected_css = 'a{src:url("https://new.example/first\a second")}';

		$actual_css = $this->replace_whole_url_in_file( $input_css, $replacement_url );

		$this->assertSame( $expected_css, $actual_css );
	}

	/** The X after a lone carriage return must survive; the following LF is a separate newline. */
	public function test_whole_url_replacement_keeps_text_after_a_lone_carriage_return() {
		$input_css = 'a{src:url(https://old.example/photo.png)}';
		$replacement_url = "https://new.example/first\rX\nlast";
		$expected_css = 'a{src:url("https://new.example/first\a X\a last")}';

		$actual_css = $this->replace_whole_url_in_file( $input_css, $replacement_url );

		$this->assertSame( $expected_css, $actual_css );
	}

	/** Control byte 0x01 is escaped, and CR/LF normalize to newlines in every quoting style. */
	public function test_escaped_control_bytes_decode_to_the_expected_url() {
		$url_prefix = "https://new.example/\x01\rX\n";
		$expected_escaped_prefix = 'https://new.example/\1 \a X\a ';
		$expected_decoded_url = "https://new.example/\x01\nX\n";

		$escaped_prefix = CSSProcessor::escape_value_prefix( $url_prefix );

		$this->assertSame( $expected_escaped_prefix, $escaped_prefix );
		$css_values = array(
			'unquoted' => 'url(' . $escaped_prefix . ')',
			'double quoted' => 'url("' . $escaped_prefix . '")',
			'single quoted' => "url('" . $escaped_prefix . "')",
		);
		foreach ( $css_values as $quoting_style => $css ) {
			$processor = new CSSURLProcessor( $css );
			$this->assertTrue( $processor->next_url(), $quoting_style );
			$this->assertSame( $expected_decoded_url, $processor->get_raw_url(), $quoting_style );
			$this->assertFalse( $processor->next_url(), 'Escaping must not introduce another URL.' );
		}
	}

	/** Replaces https://old.example in a real CSS file; returns CSS text with the suffix intact. */
	private function replace_url_prefix_in_file( string $input_css, string $replacement_prefix ): string {
		return $this->rewrite_file_in_separate_php_process( 'replace-url-prefix.php', $input_css, $replacement_prefix );
	}

	/** Replaces the complete url() value in a real CSS file; returns CSS text with a quoted value. */
	private function replace_whole_url_in_file( string $input_css, string $replacement_url ): string {
		return $this->rewrite_file_in_separate_php_process( 'replace-whole-url.php', $input_css, $replacement_url );
	}

	/**
	 * Runs one fixture script against real files and returns the written CSS, not an exit code.
	 *
	 * Both scripts read source.css and replacement.txt from the directory passed
	 * on the command line, and write target.css. A failed process fails the test
	 * here with its log, before the test compares the output CSS.
	 *
	 * @param string $fixture_script PHP filename under fixtures/css-prefix/.
	 * @param string $input_css CSS to write into source.css.
	 * @param string $replacement_url Decoded URL or prefix to write into replacement.txt.
	 * @return string Contents of target.css after a successful process exit.
	 */
	private function rewrite_file_in_separate_php_process( string $fixture_script, string $input_css, string $replacement_url ): string {
		file_put_contents( $this->directory . '/source.css', $input_css );
		file_put_contents( $this->directory . '/replacement.txt', $replacement_url );
		$arguments = array( PHP_BINARY, __DIR__ . '/fixtures/css-prefix/' . $fixture_script, $this->directory );
		$command = implode( ' ', array_map( 'escapeshellarg', $arguments ) );
		$log_path = $this->directory . '/rewrite.log';
		// Windows cmd.exe strips quotes from this command. Launch PHP directly;
		// the command stays a string for PHP 7.2, which cannot accept an argument array.
		$process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'file', $log_path, 'w' ), 2 => array( 'file', $log_path, 'a' ) ), $pipes, null, null, array( 'bypass_shell' => true ) );
		$this->assertIsResource( $process );
		fclose( $pipes[0] );
		$exit_code = proc_close( $process );
		$this->assertSame( 0, $exit_code, "The PHP file rewrite should exit successfully. Output:\n" . file_get_contents( $log_path ) );
		$this->assertSame( $input_css, file_get_contents( $this->directory . '/source.css' ), 'Rewriting the output must not change the source file.' );
		return file_get_contents( $this->directory . '/target.css' );
	}
}
