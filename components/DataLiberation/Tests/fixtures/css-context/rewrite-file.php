<?php

use WordPress\DataLiberation\URL\CSSURLProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

$directory = $argv[1];
$processor = new CSSURLProcessor( file_get_contents( $directory . '/source.css' ) );
while ( $processor->next_url() ) {
	$processor->set_raw_url( str_replace( 'https://old.example/', 'https://new.example/', $processor->get_raw_url() ) );
}
// A failed parse must leave any previous output intact.
file_put_contents( $directory . '/target.css', $processor->get_updated_css() );
