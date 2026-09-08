<?php
/** Replaces each complete URL value through the existing setter, which adds CSS quotes. */

use WordPress\DataLiberation\URL\CSSURLProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

// CSSPrefixEditProcessTest passes a directory containing these two input files.
$directory = $argv[1];
$input_css = file_get_contents( $directory . '/source.css' );
$replacement_url = file_get_contents( $directory . '/replacement.txt' );
$processor = new CSSURLProcessor( $input_css );
while ( $processor->next_url() ) {
	$processor->set_raw_url( $replacement_url );
}
file_put_contents( $directory . '/target.css', $processor->get_updated_css() );
