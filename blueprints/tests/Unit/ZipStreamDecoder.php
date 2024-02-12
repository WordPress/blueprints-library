<?php

use WordPress\Zip\ZipStreamDecoder;

it( "Should decode ZIP files", function () {
	$fp      = fopen( __DIR__ . "/test.zip", "rb" );
	$decoder = new ZipStreamDecoder( $fp );
	$file    = $decoder->next();
	expect( $file )->toBeArray();
	expect( $file['path'] )->toBe( 'TEST' );
	fclose( $fp );
} );
