<?php

$output_dir = $argv[2] ?? 'dist';
$phar_file  = $output_dir . '/playground.phar';

if ( file_exists( $phar_file ) ) {
	unlink( $phar_file );
}

$phar = new Phar( $phar_file );
$phar->startBuffering();

$phar->buildFromDirectory( './', '#^(\./)?(src/|vendor/)#' );
$phar->addFile( 'load.php' );

$stub = $phar->createDefaultStub( 'load.php' );
$phar->setStub( $stub );

$phar->stopBuffering();

echo "Phar created at $phar_file\n";

