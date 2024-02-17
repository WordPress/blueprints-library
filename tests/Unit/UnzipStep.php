<?php

use WordPress\Blueprints\StepHandler\Implementation\UnzipStepHandler;
use WordPress\Blueprints\StepHandler\Unzip\UnzipStepInput;

it( "Should unzip a ZIP file", function () {
	try {
		$fp   = fopen( __DIR__ . "/test.zip", "rb" );
		$step = new UnzipStepHandler( new UnzipStepInput( $fp, __DIR__ . "/test" ) );
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
