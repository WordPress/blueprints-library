<?php

namespace WordPress\Streams;

/**
 * Replace all occurrences of a string in a stream
 *
 * @example
 * ```php
 * $source = fopen( 'php://memory', 'w+' );
 * fwrite( $source, "Hello, world, and all the other places too!" );
 * rewind( $source );
 *
 * $generator = stream_str_replace( $source, "world", "universe" );
 * $chunks    = iterator_to_array( $generator );
 * $expected  = "Hello, universe, and all the other places too!";
 * ```
 */
function stream_str_replace( $source, $search, $replacement ) {
	if ( ! is_resource( $source ) ) {
		throw new \InvalidArgumentException( 'Source must be a valid stream resource' );
	}

	// The buffer will store the end of the chunk that might contain the start of the search string
	$searchLength = strlen( $search );
	$chunkSize    = 8196;
	$buffer       = '';

	while ( ! feof( $source ) ) {
		// Append the buffered text from the previous iteration
		$chunk = $buffer . fread( $source, $chunkSize + $searchLength );

		// Check if the search sequence is found in the current chunk
		while ( ( $pos = strpos( $chunk, $search ) ) !== false ) {
			// Perform the replacement
			$chunk = substr_replace( $chunk, $replacement, $pos, strlen( $search ) );
		}

		// Keep the last $searchLength characters in the buffer
		if ( strlen( $chunk ) > $searchLength ) {
			$buffer = substr( $chunk, - $searchLength );
			$chunk  = substr( $chunk, 0, strlen( $chunk ) - $searchLength );
		}

		// Yield the modified chunk
		yield $chunk;
	}

	// If there's any data left in the buffer after processing all chunks, it means this data does not contain the search string,
	// or it's the very end of the stream where the search string cannot span across chunks. We can safely yield it.
	if ( ! empty( $buffer ) ) {
		yield $buffer;
	}
}
