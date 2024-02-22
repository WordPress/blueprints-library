<?php
//
//use function WordPress\Streams\stream_str_replace;
//
//it( "Replace a string in a stream", function () {
//	$source = fopen( 'php://memory', 'w+' );
//	fwrite( $source, "Hello, world, and all the other places too!" );
//	rewind( $source );
//
//	$generator = stream_str_replace( $source, "and all ", "" );
//	$chunks    = iterator_to_array( $generator );
//	$expected  = "Hello, world, the other places too!";
//
//	expect( implode( "", $chunks ) )->toBe( $expected );
//} );
//
//it( "Replace a URL in a longer string", function () {
//	$data = <<<EOT
//<!DOCTYPE html>
//<html>
//<head>
//	<title>Test</title>
//</head>
//<body>
//	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
//	<a href="https://example.com">Example</a>
//</body>
//EOT;
//
//	$source = fopen( 'php://memory', 'w+' );
//	fwrite( $source, $data );
//	rewind( $source );
//
//	$generator = stream_str_replace( $source, "https://example.com", "https://playground.internal" );
//	$chunks    = iterator_to_array( $generator );
//	$expected  = str_replace( "https://example.com", "https://playground.internal", $data );
//
//	expect( implode( "", $chunks ) )->toBe( $expected );
//} );
