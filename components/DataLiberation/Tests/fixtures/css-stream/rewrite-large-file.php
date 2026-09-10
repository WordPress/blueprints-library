<?php

namespace WordPress\Encoding\compat {
	// PHP resolves the helper's unqualified strspn() call here in this worker
	// only. Run the real native scan, but count its returned ASCII bytes so
	// the test measures repeated work without depending on the CI runner's speed.
	function strspn( $bytes, $characters, $offset, $length ) {
		static $ascii_bytes_scanned = 0;
		$count = \strspn( $bytes, $characters, $offset, $length );
		$ascii_bytes_scanned += $count;
		if ( $ascii_bytes_scanned > ASCII_SCAN_BYTE_BUDGET ) {
			throw new \RuntimeException( 'UTF-8 ASCII scans read more than eight times the stylesheet size.' );
		}
		return $count;
	}
}

namespace {
	use WordPress\DataLiberation\URL\CSSURLProcessor;

	// CSS may inspect a character more than once to classify a token, but
	// scanning each remaining 1 MiB suffix would exceed this generous budget.
	define( 'WordPress\Encoding\compat\ASCII_SCAN_BYTE_BUDGET', 8 * filesize( $argv[1] ) );
	require dirname( __DIR__, 5 ) . '/bootstrap.php';

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
}
