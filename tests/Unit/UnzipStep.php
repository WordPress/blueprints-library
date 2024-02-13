<?php

use \WordPress\Blueprints\Steps\Unzip\UnzipStep;
use WordPress\Blueprints\Steps\Unzip\UnzipStepInput;

it( "Should unzip a ZIP file", function () {
	try {
		$fp   = fopen( __DIR__ . "/test.zip", "rb" );
		$step = new UnzipStep( new UnzipStepInput( $fp, __DIR__ . "/test" ) );
		$step->execute();

		expect( file_exists( __DIR__ . "/test/TEST" ) )->toBeTrue();

		fclose( $fp );
	} finally {
		if ( file_exists( __DIR__ . "/test/TEST" ) ) {
			unlink( __DIR__ . "/test/TEST" );
		}
		if ( file_exists( __DIR__ . "/test" ) ) {
			rmdir( __DIR__ . "/test" );
		}
	}
} );
