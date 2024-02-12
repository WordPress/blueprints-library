<?php

use WordPress\Zip\ZipStreamReader;

it( "Should decode ZIP files", function () {
	$fp   = fopen( __DIR__ . "/test.zip", "rb" );
	$file = ZipStreamReader::readEntry( $fp );
	expect( $file )->toBeArray();
	expect( $file['path'] )->toBe( 'TEST' );
	fclose( $fp );
} );
