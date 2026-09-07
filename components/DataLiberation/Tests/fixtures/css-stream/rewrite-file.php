<?php

use WordPress\DataLiberation\URL\CSSURLProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

$input_path = $argv[1];
$output_path = $argv[2];
$state_path = $argv[3];
$stop = $argv[4];
$state = file_exists( $state_path ) ? json_decode( file_get_contents( $state_path ), true ) : array( 'source_bytes' => 0, 'output_bytes' => 0, 'css' => null );
$input = fopen( $input_path, 'rb' );
$output = fopen( $output_path, 'c+b' );
fseek( $input, $state['source_bytes'] );
// Bytes after the last saved boundary were written by an interrupted process.
// Discard them before replaying the corresponding source chunk.
ftruncate( $output, $state['output_bytes'] );
fseek( $output, $state['output_bytes'] );
$processor = CSSURLProcessor::create_for_streaming( array( 'https://old.example' => 'https://old.example/moved' ), $state['css'] );
$chunks = 0;
while ( ! feof( $input ) ) {
	$chunk = fread( $input, 32768 );
	foreach ( $processor->rewrite_chunk( $chunk, feof( $input ) ) as $rewritten ) {
		$written = fwrite( $output, $rewritten );
		if ( strlen( $rewritten ) !== $written ) {
			throw new RuntimeException( 'CSS output wrote ' . $written . ' of ' . strlen( $rewritten ) . ' bytes.' );
		}
	}
	fflush( $output );
	++$chunks;
	if ( 'before' === $stop && 2 === $chunks ) {
		exit( 99 );
	}
	$state = array( 'source_bytes' => ftell( $input ), 'output_bytes' => ftell( $output ), 'css' => $processor->get_reentrancy_cursor() );
	file_put_contents( $state_path . '.tmp', json_encode( $state ) );
	rename( $state_path . '.tmp', $state_path );
	if ( 'after' === $stop && 2 === $chunks ) {
		exit( 99 );
	}
}
fclose( $input );
fclose( $output );
