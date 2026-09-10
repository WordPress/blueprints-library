<?php

/**
 * Test caller that rewrites a CSS file and saves progress after each source chunk.
 *
 * Arguments are the source path, output path, state path, and stop mode.
 * 'before' and 'after' exit around the second state save. 'eof' exits after
 * marking source EOF but before editing the last tokens. 'none' runs to the end.
 * Run again with the same paths and 'none' to resume from the state file.
 */

use WordPress\DataLiberation\URL\CSSURLProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

$input_path = $argv[1];
$output_path = $argv[2];
$state_path = $argv[3];
$stop = $argv[4];
// The two offsets say where to resume reading and writing. The source offset
// points before unfinished bytes, which the new process reads again.
$state = file_exists( $state_path ) ? json_decode( file_get_contents( $state_path ), true ) : array( 'source_bytes' => 0, 'output_bytes' => 0, 'css' => null );
$input = fopen( $input_path, 'rb' );
$output = fopen( $output_path, 'c+b' );
fseek( $input, $state['source_bytes'] );
// A process can stop after writing output but before saving the new offsets.
// The next run reads that source again. Remove the extra output bytes first
// so that the repeated read does not append a second copy of the same CSS.
ftruncate( $output, $state['output_bytes'] );
fseek( $output, $state['output_bytes'] );
// The target still starts with the source base. Rewriting a URL twice would
// add '/moved' twice, which makes repeated replacements visible in the output.
$processor = CSSURLProcessor::create_for_streaming( '', $state['css'] );
$chunks = 0;
while ( ! $processor->is_finished() ) {
	$chunk = fread( $input, 32768 );
	$processor->append_bytes( $chunk );
	if ( feof( $input ) ) {
		$processor->input_finished();
		if ( 'eof' === $stop ) {
			exit( 99 );
		}
	}
	while ( $processor->next_url() ) {
		$processor->set_raw_url( str_replace( 'https://old.example/', 'https://old.example/moved/', $processor->get_raw_url() ) );
	}
	$rewritten = $processor->flush_processed_css();
	$written = fwrite( $output, $rewritten );
	if ( strlen( $rewritten ) !== $written ) {
		throw new RuntimeException( 'CSS output wrote ' . $written . ' of ' . strlen( $rewritten ) . ' bytes.' );
	}
	// Save offsets only after all output for this source chunk has been written.
	// A stopped process must not leave saved state ahead of the output file.
	fflush( $output );
	++$chunks;
	if ( 'before' === $stop && 2 === $chunks ) {
		exit( 99 );
	}
	// The cursor contains no CSS bytes. Resume must reread unfinished input
	// from the processor's source offset, which can be earlier than ftell($input).
	$state = array( 'source_bytes' => $processor->get_token_byte_offset_in_the_input_stream(), 'output_bytes' => ftell( $output ), 'css' => $processor->get_reentrancy_cursor() );
	// Replace the complete state file in one rename. A stop during the temporary
	// write leaves the previous saved offsets and parser state together.
	file_put_contents( $state_path . '.tmp', json_encode( $state ) );
	rename( $state_path . '.tmp', $state_path );
	if ( 'after' === $stop && 2 === $chunks ) {
		exit( 99 );
	}
}
fclose( $input );
fclose( $output );
