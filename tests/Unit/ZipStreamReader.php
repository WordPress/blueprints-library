<?php

use WordPress\Zip\ZipCentralDirectoryEntry;
use WordPress\Zip\ZipEndCentralDirectoryEntry;
use WordPress\Zip\ZipFileEntry;
use WordPress\Zip\ZipStreamReader;

it( "Should decode ZIP files", function () {
	$fp   = fopen( __DIR__ . "/test.zip", "rb" );
	$file = ZipStreamReader::readEntry( $fp );
	expect( $file )->toBeInstanceOf( ZipFileEntry::class );
	expect( $file->path )->toBe( 'TEST' );

	$file = ZipStreamReader::readEntry( $fp );
	expect( $file )->toBeInstanceOf( ZipCentralDirectoryEntry::class );
	expect( $file->path )->toBe( 'TEST' );

	$file = ZipStreamReader::readEntry( $fp );
	expect( $file )->toBeInstanceOf( ZipEndCentralDirectoryEntry::class );

	fclose( $fp );
} );
