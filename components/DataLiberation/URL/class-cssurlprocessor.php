<?php

namespace WordPress\DataLiberation\URL;

use WordPress\DataLiberation\CSS\CSSProcessor;

/**
 * Finds CSS URLs in a complete string or rewrites them as source chunks arrive.
 *
 * CSSProcessor reads one CSS item, called a token, at a time. A token can be
 * a quoted string, a comment, or an unquoted url(...). The surrounding syntax
 * tells whether a string holds a URL: @import "theme.css" does, but
 * content: "theme.css" does not.
 */
class CSSURLProcessor {
	/**
	 * @var CSSProcessor
	 */
	private $processor;

	/**
	 * Remembers where a quoted string can be a URL as tokens are read.
	 *
	 * Both next_url() and rewrite_chunk() update this state. Streaming callers
	 * save it in the cursor so a new process can continue inside an image-set().
	 *
	 * @var array {
	 *     @type int    $depth  Number of function or '(' tokens not yet closed by ')'.
	 *     @type int[]  $images Depth of each open image-set(), outermost first.
	 *     @type string $expect Why the next string can be a URL: 'url', 'import',
	 *                          or 'image'. Empty when no URL string is expected.
	 * }
	 */
	private $context = array(
		'depth' => 0,
		'images' => array(),
		'expect' => '',
	);

	/**
	 * @param string $css CSS source without wrapping braces.
	 */
	public function __construct( string $css ) {
		$this->processor = CSSProcessor::create( $css );
	}

	/**
	 * URL replacement rules prepared once by create_for_streaming().
	 *
	 * Each input rule produces two entries: one for https://old.example and one
	 * for //old.example, for example. Entries with longer source bases come first
	 * so a rule for /blog wins over a rule for the whole site.
	 *
	 * Each entry contains 'origin' (scheme and host, with any port), 'prefix'
	 * (origin and path), 'target' (replacement escaped for CSS), and 'position'
	 * (original entry order, used when two prefixes have the same length).
	 *
	 * @var array|null Null when constructed for next_url(), without streaming rules.
	 */
	private $mappings;

	/**
	 * Whether iteration over the current rewrite_chunk() result has started but not finished.
	 *
	 * While true, output may still be waiting in the generator. Reject new input
	 * and saved cursors until the caller finishes the foreach loop. Otherwise
	 * a saved cursor could advance past output that the caller has not received.
	 *
	 * @var bool
	 */
	private $input_open = false;

	/**
	 * Identifies the URL replacement rules used before a rewrite stopped.
	 *
	 * A file started with old.example -> new.example must not resume with
	 * old.example -> other.example. That could leave two target hosts in one
	 * output file. create_for_streaming() rejects a cursor if its saved hash
	 * differs from the hash of the rules supplied for the new run.
	 *
	 * @var string|null SHA-256 hash of $mappings, including match order; null before streaming.
	 */
	private $mapping_hash;

	/**
	 * Starts or resumes URL rewriting for CSS supplied in chunks.
	 *
	 * For example, array( 'https://old.example' => 'https://new.example/local' )
	 * changes https://old.example/photo.png to https://new.example/local/photo.png.
	 * Both bases must be HTTP(S) URLs without credentials, a query, or a fragment.
	 *
	 * Pass null for a new file. To resume, pass the cursor returned by
	 * get_reentrancy_cursor() and the same mapping in the same order. The cursor
	 * contains unfinished CSS bytes; supply only source bytes after the saved
	 * source offset. Completed source bytes are not kept by this processor.
	 *
	 * @param array<string,string> $url_mapping Source URL bases as keys, replacement bases as values.
	 * @param array|null           $cursor State from get_reentrancy_cursor(), or null to start. {
	 *     @type array  $css          CSSProcessor state, including unfinished source bytes.
	 *     @type array  $context      Saved $context: open parentheses, image sets, and expected strings.
	 *     @type string $mapping_hash Hash used to reject a resume with different replacement rules.
	 * }
	 * @return static Processor ready for rewrite_chunk().
	 */
	public static function create_for_streaming( array $url_mapping, ?array $cursor = null ) {
		if ( null !== $cursor && ( ! isset( $cursor['css'], $cursor['context'], $cursor['mapping_hash'] ) || ! is_array( $cursor['css'] ) || ! is_array( $cursor['context'] ) ) ) {
			throw new \InvalidArgumentException( 'The CSS URL cursor must contain buffered CSS input, URL context, and its mapping hash.' );
		}
		$processor            = new static( '' );
		$processor->processor = CSSProcessor::create_for_streaming( $cursor['css'] ?? null );
		$processor->mappings  = array();
		if ( null !== $cursor ) {
			$processor->context = $cursor['context'];
		}
		foreach ( $url_mapping as $source => $target ) {
			$source_url = WPURL::parse( $source );
			$target_url = WPURL::parse( $target );
			foreach ( array( $source_url, $target_url ) as $url ) {
				if ( false === $url || ! in_array( $url->protocol, array( 'http:', 'https:' ), true ) || '' !== $url->username || '' !== $url->password || '' !== $url->search || '' !== $url->hash ) {
					throw new \InvalidArgumentException( 'CSS URL bases must be HTTP(S) addresses without credentials, query, or fragment: ' . $source . ' => ' . $target );
				}
			}
			$path = rtrim( $source_url->pathname, '/' );
			foreach ( array( $source_url->protocol, '' ) as $scheme ) {
				$origin                = $scheme . '//' . $source_url->host;
				$processor->mappings[] = array(
					'origin' => $origin,
					'prefix' => $origin . $path,
					'target' => CSSProcessor::escape_value_prefix( ( '' === $scheme ? '' : $target_url->protocol ) . '//' . $target_url->host . rtrim( $target_url->pathname, '/' ) ),
					'position' => count( $processor->mappings ),
				);
			}
		}
		usort(
			$processor->mappings,
			static function ( $first, $second ) {
				return ( strlen( $second['prefix'] ) <=> strlen( $first['prefix'] ) ) ? ( strlen( $second['prefix'] ) <=> strlen( $first['prefix'] ) ) : ( $first['position'] <=> $second['position'] );
			}
		);
		$processor->mapping_hash = hash( 'sha256', json_encode( $processor->mappings ) );
		if ( null !== $cursor && ( $cursor['mapping_hash'] ?? null ) !== $processor->mapping_hash ) {
			throw new \InvalidArgumentException( 'Cannot resume CSS rewriting with different URL mappings. Start a new stylesheet rewrite.' );
		}
		return $processor;
	}

	/**
	 * Rewrites URLs in the next source chunk and returns output through a generator.
	 *
	 * A chunk can end inside `url(https://old.exa`. That URL stays in memory until
	 * later input completes it, or $is_last marks the actual end of the file.
	 * Only the matched URL base changes. Quotes, parentheses, and the remaining
	 * URL bytes keep their original spelling. Comments and displayed text stay
	 * unchanged. Malformed string and URL tokens also stay unchanged.
	 *
	 * Use foreach to write each output chunk. Finish that loop before supplying
	 * more input or calling get_reentrancy_cursor(). For files, flush the output,
	 * then save the cursor and both file offsets together. The source offset
	 * includes every supplied byte, even bytes still held in the cursor.
	 *
	 * If writing fails or the loop stops early, discard this processor. Resume
	 * from the last saved cursor and source offset. First remove output bytes
	 * after the saved output offset so the repeated input does not duplicate them.
	 *
	 * Output pieces are limited to 64 KiB, but unfinished tokens have no size
	 * limit. A large comment or URL can increase memory use and cursor size.
	 *
	 * @param string $chunk   Source bytes immediately after the previous chunk; may be empty.
	 * @param bool   $is_last True only at the actual end of the file, not when a download stops early.
	 * @return \Generator<string> Output pieces of at most 64 KiB, in source order.
	 */
	public function rewrite_chunk( string $chunk, bool $is_last ): \Generator {
		if ( null === $this->mappings ) {
			throw new \LogicException( 'Chunked CSS rewriting requires create_for_streaming().' );
		}
		if ( $this->input_open ) {
			throw new \LogicException( 'Consume all CSS output chunks before supplying another input chunk.' );
		}
		if ( ! $this->processor->is_expecting_more_input() ) {
			if ( '' === $chunk ) {
				return;
			}
			throw new \LogicException( 'Cannot append CSS bytes after the end of the stylesheet.' );
		}
		$this->input_open = true;
		$output           = '';
		$this->processor->append_bytes( $chunk, $is_last );
		while ( $this->processor->next_token() ) {
			$type   = $this->processor->get_token_type();
			$name   = in_array( $type, array( CSSProcessor::TOKEN_FUNCTION, CSSProcessor::TOKEN_AT_KEYWORD ), true ) ? $this->processor->get_token_value() : '';
			$is_url = $this->inspect_url_context( $type, $name );
			$piece  = $this->processor->get_unnormalized_token();
			if ( $is_url && in_array( $type, array( CSSProcessor::TOKEN_STRING, CSSProcessor::TOKEN_URL ), true ) ) {
				$decoded = $this->processor->get_token_value();
				foreach ( $this->mappings as $mapping ) {
					$prefix       = $mapping['prefix'];
					$origin_bytes = strlen( $mapping['origin'] );
					// Scheme and host ignore letter case; paths do not. Thus
					// OLD.EXAMPLE/blog can match old.example/blog, but /Blog cannot.
					if ( strlen( $decoded ) < strlen( $prefix ) || 0 !== strncasecmp( $decoded, $mapping['origin'], $origin_bytes ) ||
						substr( $decoded, $origin_bytes, strlen( $prefix ) - $origin_bytes ) !== substr( $prefix, $origin_bytes ) ) {
						continue;
					}
					// A base ending in /blog must not match /blogger. A slash, query,
					// fragment, or URL end must follow the matched base.
					if ( strlen( $decoded ) > strlen( $prefix ) && false === strpos( '/?#', $decoded[ strlen( $prefix ) ] ) ) {
						continue;
					}
					// Match decoded text, but edit the original bytes. For example,
					// an escaped letter such as \6f takes more source bytes than 'o'.
					// Measure only the base so escapes after it keep their spelling.
					$value_start      = $this->processor->get_token_value_start() - $this->processor->get_token_start();
					$raw_value        = substr( $piece, $value_start, $this->processor->get_token_value_length() );
					$raw_prefix_bytes = CSSProcessor::measure_value_prefix( $raw_value, strlen( $prefix ), CSSProcessor::TOKEN_STRING === $type );
					$piece            = substr_replace( $piece, $mapping['target'], $value_start, $raw_prefix_bytes );
					break;
				}
			}
			$output .= $piece;
			if ( strlen( $output ) >= 65536 ) {
				// Replacements can be much longer than the input URLs. Return
				// this output now so later replacements do not keep adding to it.
				yield from $this->split_output_chunks( $output );
				$output = '';
			}
		}
		// Completed tokens have been copied to output. Release their source
		// bytes, but keep any unfinished token for the next input chunk.
		$this->processor->flush_processed_css();
		yield from $this->split_output_chunks( $output );
		$this->input_open = false;
	}

	/**
	 * Returns the parser state needed to resume this rewrite in a new process.
	 *
	 * Call this after writing all output from rewrite_chunk(). The array can be
	 * encoded as JSON. Save it with the source and output byte offsets, then pass
	 * it to create_for_streaming() with the same URL mapping to resume. This method
	 * returns data only; the caller must save it and manage the two files.
	 *
	 * @return array State for create_for_streaming(). {
	 *     @type array  $css          CSSProcessor state, including unfinished source bytes.
	 *     @type array  $context      Saved $context, so a string after resume keeps its URL meaning.
	 *     @type string $mapping_hash Hash used to check that replacement rules did not change.
	 * }
	 */
	public function get_reentrancy_cursor(): array {
		if ( null === $this->mappings ) {
			throw new \LogicException( 'CSS URL cursors require create_for_streaming().' );
		}
		if ( $this->input_open ) {
			throw new \LogicException( 'Consume all CSS output chunks before saving a cursor.' );
		}
		return array(
			'css' => $this->processor->get_reentrancy_cursor(),
			'context' => $this->context,
			'mapping_hash' => $this->mapping_hash,
		);
	}

	/**
	 * Returns output in pieces of at most 64 KiB, even when one token is larger.
	 *
	 * The full string already exists here. Splitting it limits the size of each
	 * piece given to the caller; it does not limit the memory used by that string.
	 *
	 * @param string $bytes Rewritten CSS bytes waiting to be returned to the caller.
	 * @return \Generator<string> Consecutive pieces that together contain all of $bytes.
	 */
	private function split_output_chunks( string $bytes ): \Generator {
		$length = strlen( $bytes );
		for ( $offset = 0; $offset < $length; $offset += 65536 ) {
			yield substr( $bytes, $offset, 65536 );
		}
	}

	/**
	 * Finds the next URL in the complete CSS string passed to the constructor.
	 *
	 * Recognizes url(), @import strings, and image-set() image strings. Skips
	 * comments, displayed text, and malformed string or URL tokens.
	 *
	 * @return bool True when get_raw_url() can read the next URL; false when none remain.
	 */
	public function next_url(): bool {
		while ( $this->processor->next_token() ) {
			$type   = $this->processor->get_token_type();
			$name   = in_array( $type, array( CSSProcessor::TOKEN_FUNCTION, CSSProcessor::TOKEN_AT_KEYWORD ), true ) ? $this->processor->get_token_value() : '';
			$is_url = $this->inspect_url_context( $type, $name );
			if ( $is_url && in_array( $type, array( CSSProcessor::TOKEN_STRING, CSSProcessor::TOKEN_URL ), true ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Tracks open CSS functions and checks whether this token holds a URL.
	 *
	 * In @import "theme.css", the @import token makes the next string a URL.
	 * url("photo.png") and image-set("photo.png" 1x) use the same rule. Spaces
	 * and comments do not clear that expectation; any other token consumes it.
	 * An unquoted url(photo.png) is already one URL token, including its wrapper.
	 *
	 * Call once for every token, in order, so both whole-string and streaming
	 * callers track the same open functions and expected URL strings. A true
	 * result can include malformed URL or string tokens; callers skip those.
	 *
	 * @param string $type Token type returned by CSSProcessor::get_token_type().
	 * @param string $name Function or @-keyword name with CSS escapes decoded; otherwise ''.
	 * @return bool Whether this token is in a URL position, even if its syntax is malformed.
	 */
	private function inspect_url_context( string $type, string $name ): bool {
		if ( in_array( $type, array( CSSProcessor::TOKEN_WHITESPACE, CSSProcessor::TOKEN_COMMENT ), true ) ) {
			return false;
		}
		$expected                = $this->context['expect'];
		$this->context['expect'] = '';
		if ( CSSProcessor::TOKEN_FUNCTION === $type ) {
			++$this->context['depth'];
			$name                    = strtolower( $name );
			$this->context['expect'] = 'url' === $name ? 'url' : '';
			if ( in_array( $name, array( 'image-set', '-webkit-image-set' ), true ) ) {
				// In image-set("a.png" type("image/png"), "b.png" 2x), the comma
				// starts another image. A comma inside type(...) must not do so:
				// type() is one level deeper than the image-set() around it.
				// Save the depth of each open image-set() to tell them apart;
				// its closing ')' removes that depth from the list.
				// Keep at most 128 open image-set() calls, even in malformed CSS,
				// so repeated openings cannot grow the list without a limit.
				// This limits nesting, not images per set or separate sets in a file.
				if ( count( $this->context['images'] ) >= 128 ) {
					throw new \RuntimeException( 'CSS image-set nesting exceeds 128 open functions.' );
				}
				$this->context['images'][] = $this->context['depth'];
				$this->context['expect']   = 'image';
			}
		} elseif ( CSSProcessor::TOKEN_AT_KEYWORD === $type ) {
			$this->context['expect'] = 'import' === strtolower( $name ) ? 'import' : '';
		} elseif ( CSSProcessor::TOKEN_LEFT_PAREN === $type ) {
			++$this->context['depth'];
		} elseif ( CSSProcessor::TOKEN_RIGHT_PAREN === $type ) {
			if ( end( $this->context['images'] ) === $this->context['depth'] ) {
				array_pop( $this->context['images'] );
			}
			$this->context['depth'] = max( 0, $this->context['depth'] - 1 );
		} elseif ( CSSProcessor::TOKEN_COMMA === $type && end( $this->context['images'] ) === $this->context['depth'] ) {
			$this->context['expect'] = 'image';
		}
		return in_array( $type, array( CSSProcessor::TOKEN_URL, CSSProcessor::TOKEN_BAD_URL ), true ) ||
			( '' !== $expected && in_array( $type, array( CSSProcessor::TOKEN_STRING, CSSProcessor::TOKEN_BAD_STRING ), true ) );
	}

	/**
	 * Returns the raw (decoded) URL for the current match.
	 *
	 * @return string|false
	 */
	public function get_raw_url() {
		$value = $this->processor->get_token_value();
		return false !== $value ? $value : false;
	}

	/**
	 * Replaces the currently matched URL with a new value.
	 *
	 * @param string $new_url Replacement URL without quoting.
	 * @return bool
	 */
	public function set_raw_url( string $new_url ): bool {
		return $this->processor->set_token_value( $new_url );
	}

	/**
	 * Returns the updated CSS with all replacements applied.
	 *
	 * @return string
	 */
	public function get_updated_css(): string {
		return $this->processor->get_updated_css();
	}

	/**
	 * Determines whether the current URL is a data URI.
	 *
	 * @return bool
	 */
	public function is_data_uri(): bool {
		return $this->processor->is_data_uri();
	}
}
