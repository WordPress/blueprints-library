<?php

require dirname( __DIR__, 5 ) . '/bootstrap.php';

use WordPress\DataLiberation\URL\CSSURLProcessor;

// A one-character UTF-8 check must not scan the rest of a 1 MiB chunk.
// Ten seconds leaves room for slow CI workers, but stops the old repeated
// suffix scans before they hold up the rest of the test suite.
set_time_limit( 10 );
$input = fopen( $argv[1], 'rb' );
$output = fopen( $argv[2], 'wb' );
$processor = CSSURLProcessor::create_for_streaming();
while ( ! feof( $input ) ) {
	$processor->append_bytes( fread( $input, 1048576 ) );
	if ( feof( $input ) ) {
		$processor->input_finished();
	}
	while ( $processor->next_url() ) {
		$processor->set_raw_url( 'https://new.example/photo.png' );
		fwrite( $output, $processor->flush_processed_css() );
	}
	fwrite( $output, $processor->flush_processed_css() );
}
fclose( $input );
fclose( $output );
