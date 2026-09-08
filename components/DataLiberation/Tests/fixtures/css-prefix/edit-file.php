<?php

use WordPress\DataLiberation\CSS\CSSProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

$directory = $argv[1];
$mode = $argv[2];
$input = file_get_contents( $directory . '/source.css' );
$replacement = file_get_contents( $directory . '/replacement.txt' );
$processor = CSSProcessor::create( $input );
$output = '';
while ( $processor->next_token() ) {
	$type = $processor->get_token_type();
	$piece = $processor->get_unnormalized_token();
	if ( in_array( $type, array( CSSProcessor::TOKEN_URL, CSSProcessor::TOKEN_STRING ), true ) && 0 === strpos( $processor->get_token_value(), 'https://old.example' ) ) {
		if ( 'whole' === $mode ) {
			$processor->set_token_value( $replacement );
		} else {
			$start = $processor->get_token_value_start() - $processor->get_token_start();
			$raw = substr( $piece, $start, $processor->get_token_value_length() );
			$length = CSSProcessor::measure_value_prefix( $raw, strlen( 'https://old.example' ), CSSProcessor::TOKEN_STRING === $type );
			$piece = substr_replace( $piece, CSSProcessor::escape_value_prefix( $replacement ), $start, $length );
		}
	}
	$output .= $piece;
}
file_put_contents( $directory . '/target.css', 'whole' === $mode ? $processor->get_updated_css() : $output );
