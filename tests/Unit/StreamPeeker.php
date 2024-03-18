<?php

use WordPress\Streams\StreamPeekerData;
use WordPress\Streams\StreamPeekerWrapper;

it( 'can peek at the stream', function () {
	// Prepare the stream and mapper function
	$originalStream = fopen( 'php://temp', 'r+' );
	fwrite( $originalStream, 'This is some test data.' );
	rewind( $originalStream );

	$loggedData = '';
	$handle = StreamPeekerWrapper::create_resource(
		new StreamPeekerData(
			$originalStream,
			function ( $data ) use ( &$loggedData ) {
				$loggedData .= $data;
			}
		)
	);

	expect( stream_get_contents( $handle ) )->toBe( 'This is some test data.' );
	expect( $loggedData )->toBe( 'This is some test data.' );
	fclose( $handle );
} );
