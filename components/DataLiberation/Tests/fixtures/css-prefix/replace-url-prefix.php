<?php
/** Replaces the https://old.example prefix, preserving the remaining CSS source bytes. */

use WordPress\DataLiberation\CSS\CSSProcessor;

require dirname( __DIR__, 5 ) . '/bootstrap.php';

// CSSPrefixEditProcessTest passes a directory containing these two input files.
$directory = $argv[1];
$input_css = file_get_contents( $directory . '/source.css' );
$replacement_prefix = file_get_contents( $directory . '/replacement.txt' );
$source_prefix = 'https://old.example';
$processor = CSSProcessor::create( $input_css );
$output_css = '';
while ( $processor->next_token() ) {
	$token_type = $processor->get_token_type();
	$token_css = $processor->get_unnormalized_token();
	if ( in_array( $token_type, array( CSSProcessor::TOKEN_URL, CSSProcessor::TOKEN_STRING ), true ) && 0 === strpos( $processor->get_token_value(), $source_prefix ) ) {
		$value_offset_in_token = $processor->get_token_value_start() - $processor->get_token_start();
		$raw_value = substr( $token_css, $value_offset_in_token, $processor->get_token_value_length() );
		// The decoded prefix and its escaped CSS spelling can have different lengths.
		$source_prefix_bytes = CSSProcessor::measure_value_prefix( $raw_value, strlen( $source_prefix ), CSSProcessor::TOKEN_STRING === $token_type );
		$escaped_replacement = CSSProcessor::escape_value_prefix( $replacement_prefix );
		$token_css = substr_replace( $token_css, $escaped_replacement, $value_offset_in_token, $source_prefix_bytes );
	}
	$output_css .= $token_css;
}
file_put_contents( $directory . '/target.css', $output_css );
