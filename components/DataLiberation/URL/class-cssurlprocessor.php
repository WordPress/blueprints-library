<?php

namespace WordPress\DataLiberation\URL;

use WordPress\DataLiberation\CSS\CSSProcessor;

/**
 * Provides URL specific helpers on top of the CSSProcessor tokenizer.
 */
class CSSURLProcessor {
	/** A URL base can contain arbitrarily many zero-width CSS line continuations. */
	private const MAX_BASE_BYTES = 1048576;

	/**
	 * @var CSSProcessor
	 */
	private $processor;

	/** @var array URL syntax context shared by whole-string and streaming callers. */
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

	/** @var array|null State for chunked rewriting; null for the whole-string iterator. */
	private $stream;

	/** @var array Compiled source bases, ordered from longest path to shortest. */
	private $mappings = array();

	/** @var bool Whether the caller still has output chunks to consume for this input. */
	private $input_open = false;

	/** @var string Binds resumed prefix decisions to the same compiled mapping. */
	private $mapping_hash;

	/**
	 * Opens URL rewriting for a CSS file without retaining completed input.
	 *
	 * @param array<string,string> $url_mapping Source HTTP(S) bases mapped to target HTTP(S) bases.
	 * @param array|null           $cursor {
	 *               Optional state returned by get_reentrancy_cursor().
	 *     @type array $css CSS tokenizer cursor.
	 *     @type array $urls Undecided URL prefix and EOF state.
	 *     @type array $context Function depth and expected URL syntax.
	 *     @type string $mapping_hash Hash of the compiled mapping.
	 * }
	 * @return static
	 */
	public static function create_for_streaming( array $url_mapping, ?array $cursor = null ) {
		if ( null !== $cursor && ( ! isset( $cursor['css'], $cursor['urls'], $cursor['context'], $cursor['mapping_hash'] ) || ! is_array( $cursor['css'] ) || ! is_array( $cursor['urls'] ) || ! is_array( $cursor['context'] ) ) ) {
			throw new \InvalidArgumentException( 'The CSS URL cursor must contain CSS lexer state and URL context. Restart downloads created by another CSS rewriter.' );
		}
		$processor            = new static( '' );
		$processor->processor = CSSProcessor::create_for_streaming( $cursor['css'] ?? null );
		$processor->stream    = array(
			'pending' => null,
			'finished' => false,
		);
		if ( null !== $cursor ) {
			$processor->stream  = $cursor['urls'];
			$processor->context = $cursor['context'];
			if ( null !== $processor->stream['pending'] ) {
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Cursor JSON must preserve arbitrary source bytes.
				$processor->stream['pending']['raw'] = base64_decode( $processor->stream['pending']['raw'], true );
				if ( false === $processor->stream['pending']['raw'] ) {
					throw new \InvalidArgumentException( 'The CSS URL cursor contains an invalid base64 URL prefix.' );
				}
			}
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
				$entry                 = end( $processor->mappings );
				if ( strlen( $entry['prefix'] ) > self::MAX_BASE_BYTES || strlen( $entry['target'] ) > self::MAX_BASE_BYTES ) {
					throw new \InvalidArgumentException( 'CSS URL bases must not exceed ' . self::MAX_BASE_BYTES . ' bytes; the source has ' . strlen( $entry['prefix'] ) . ' bytes and the escaped target has ' . strlen( $entry['target'] ) . ' bytes.' );
				}
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
	 * Rewrites one supplied chunk and yields bytes ready to write.
	 *
	 * Only URL base bytes change. CSS delimiters and the unmatched URL suffix keep
	 * their source spelling. Comments and displayed strings are never URL contexts.
	 * The caller writes every yielded chunk before saving its source/output offsets
	 * and this processor cursor together at the input boundary. If writing fails or
	 * the generator is abandoned, reopen from the last saved cursor. A cursor covers
	 * all supplied source bytes, including the small undecided tail kept inside it.
	 *
	 * @param string $chunk   Next source bytes.
	 * @param bool   $is_last Whether this is the actual end of the stylesheet.
	 * @return \Generator<string> Output chunks of at most 64 KiB; drain them before saving the cursor.
	 */
	public function rewrite_chunk( string $chunk, bool $is_last ): \Generator {
		if ( null === $this->stream ) {
			throw new \LogicException( 'Chunked CSS rewriting requires create_for_streaming().' );
		}
		if ( $this->input_open ) {
			throw new \LogicException( 'Consume all CSS output chunks before supplying another input chunk.' );
		}
		if ( $this->stream['finished'] ) {
			if ( '' === $chunk ) {
				return;
			}
			throw new \LogicException( 'Cannot append CSS bytes after the end of the stylesheet.' );
		}
		$this->input_open = true;
		$output           = '';
		$length           = strlen( $chunk );
		$offset           = 0;
		do {
			$bytes   = substr( $chunk, $offset, 65536 );
			$offset += strlen( $bytes );
			$this->processor->append_bytes( $bytes, $is_last && $offset === $length );
			// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition -- Read each fragment once and stop at the input boundary.
			while ( null !== ( $fragment = $this->processor->next_token_fragment() ) ) {
				$type     = $fragment['type'];
				$is_value = in_array( $type, array( CSSProcessor::TOKEN_STRING, CSSProcessor::TOKEN_URL, CSSProcessor::TOKEN_BAD_STRING, CSSProcessor::TOKEN_BAD_URL ), true );
				$is_url   = false;
				if ( ! $is_value || $fragment['first'] ) {
					$is_url = $this->inspect_url_context( $type, $fragment['name'] );
				}
				$piece = $fragment['text'];
				if ( $is_value ) {
					if ( $fragment['first'] ) {
						$this->stream['pending'] = $is_url ? array(
							'raw' => '',
							'decoded' => '',
							'string' => ! in_array( $type, array( CSSProcessor::TOKEN_URL, CSSProcessor::TOKEN_BAD_URL ), true ),
						) : null;
					}
					$piece = $fragment['before'];
					if ( null !== $this->stream['pending'] ) {
						$this->stream['pending']['raw']     .= $fragment['raw_value'];
						$this->stream['pending']['decoded'] .= $fragment['value'];
						$piece                              .= $this->rewrite_pending_prefix( $fragment['last'] );
					} else {
						$piece .= $fragment['raw_value'];
					}
					$piece .= $fragment['after'];
					if ( $fragment['closes_url'] ) {
						$this->inspect_url_context( CSSProcessor::TOKEN_RIGHT_PAREN, '' );
					}
				}
				$output .= $piece;
				if ( strlen( $output ) >= 65536 ) {
					// Write expanded replacements before reading more source tokens.
					yield from $this->split_output_chunks( $output );
					$output = '';
				}
			}
		} while ( $offset < $length );
		if ( $is_last ) {
			$output                  .= $this->rewrite_pending_prefix( true );
			$this->stream['finished'] = true;
		}
		yield from $this->split_output_chunks( $output );
		$this->input_open = false;
	}

	/**
	 * Returns state to save beside the source and destination byte offsets.
	 *
	 * @return array {
	 *     @type array $css  CSS tokenizer cursor.
	 *     @type array $urls Any undecided base prefix and EOF state; raw bytes use base64.
	 *     @type array $context Function depth and expected URL syntax.
	 *     @type string $mapping_hash Hash of the compiled mapping; resume requires the same mapping.
	 * }
	 */
	public function get_reentrancy_cursor(): array {
		if ( null === $this->stream ) {
			throw new \LogicException( 'CSS URL cursors require create_for_streaming().' );
		}
		if ( $this->input_open ) {
			throw new \LogicException( 'Consume all CSS output chunks before saving a cursor.' );
		}
		$urls = $this->stream;
		if ( null !== $urls['pending'] ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Cursor JSON must preserve arbitrary source bytes.
			$urls['pending']['raw'] = base64_encode( $urls['pending']['raw'] );
		}
		return array(
			'css' => $this->processor->get_reentrancy_cursor(),
			'urls' => $urls,
			'context' => $this->context,
			'mapping_hash' => $this->mapping_hash,
		);
	}

	/**
	 * Splits expanded URL replacements without holding the whole rewritten input.
	 *
	 * @param string $bytes Current output buffer.
	 * @return \Generator<string> Bounded output chunks.
	 */
	private function split_output_chunks( string $bytes ): \Generator {
		$length = strlen( $bytes );
		for ( $offset = 0; $offset < $length; $offset += 65536 ) {
			yield substr( $bytes, $offset, 65536 );
		}
	}

	/** Replaces one decoded source base, after its following byte rules out a host/path prefix collision. */
	private function rewrite_pending_prefix( bool $last ): string {
		if ( null === $this->stream['pending'] ) {
			return '';
		}
		$pending = $this->stream['pending'];
		$decoded = $pending['decoded'];
		foreach ( $this->mappings as $mapping ) {
			$prefix               = $mapping['prefix'];
			$origin_bytes         = strlen( $mapping['origin'] );
			$compare_bytes        = min( strlen( $decoded ), strlen( $prefix ) );
			$origin_compare_bytes = min( $compare_bytes, $origin_bytes );
			if ( 0 !== strncasecmp( $decoded, $prefix, $origin_compare_bytes ) ||
				( $compare_bytes > $origin_bytes && substr( $decoded, $origin_bytes, $compare_bytes - $origin_bytes ) !== substr( $prefix, $origin_bytes, $compare_bytes - $origin_bytes ) ) ) {
				continue;
			}
			if ( strlen( $decoded ) < strlen( $prefix ) || ( strlen( $decoded ) === strlen( $prefix ) && ! $last ) ) {
				if ( ! $last ) {
					if ( strlen( $pending['raw'] ) > self::MAX_BASE_BYTES ) {
						throw new \RuntimeException( 'An undecided CSS URL base has ' . strlen( $pending['raw'] ) . ' source bytes, exceeding ' . self::MAX_BASE_BYTES . '; shorten the escaped URL prefix before migrating this stylesheet.' );
					}
					return '';
				}
				continue;
			}
			if ( strlen( $decoded ) > strlen( $prefix ) && false === strpos( '/?#', $decoded[ strlen( $prefix ) ] ) ) {
				continue;
			}
			$raw_prefix_bytes        = CSSProcessor::measure_value_prefix( $pending['raw'], strlen( $prefix ), $pending['string'] );
			$this->stream['pending'] = null;
			return $mapping['target'] . substr( $pending['raw'], $raw_prefix_bytes );
		}
		$this->stream['pending'] = null;
		return $pending['raw'];
	}

	/**
	 * Moves the cursor to the next URL token, if available.
	 *
	 * @return bool
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
	 * Recognizes URL values without treating comments or displayed text as URLs.
	 *
	 * The url() function expects a STRING token after whitespace. Bare @import and
	 * image-set strings use the same lookahead; another token clears that expectation.
	 * Direct unquoted URL tokens need no preceding function token in whole-string mode.
	 *
	 * @param string $type CSS token type.
	 * @param string $name Decoded function or at-keyword name, otherwise empty.
	 * @return bool Whether this begins a URL value.
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
