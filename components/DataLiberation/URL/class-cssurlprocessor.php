<?php

namespace WordPress\DataLiberation\URL;

use WordPress\DataLiberation\CSS\CSSProcessor;

/**
 * Provides URL specific helpers on top of the CSSProcessor tokenizer.
 */
class CSSURLProcessor {
	/**
	 * @var CSSProcessor
	 */
	private $processor;

	/** @var array URL syntax context used by the whole-string iterator. */
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
	 * Moves the cursor to the next URL token, if available.
	 *
	 * Recognizes URL values without treating comments or displayed text as URLs.
	 * The url() function expects a STRING token after whitespace. Bare @import and
	 * image-set strings use the same lookahead; another token clears that expectation.
	 * Direct unquoted URL tokens already include their url() wrapper.
	 *
	 * @return bool
	 */
	public function next_url(): bool {
		while ( $this->processor->next_token() ) {
			$type = $this->processor->get_token_type();
			$name = in_array( $type, array( CSSProcessor::TOKEN_FUNCTION, CSSProcessor::TOKEN_AT_KEYWORD ), true ) ? $this->processor->get_token_value() : '';
			if ( in_array( $type, array( CSSProcessor::TOKEN_WHITESPACE, CSSProcessor::TOKEN_COMMENT ), true ) ) {
				continue;
			}
			$expected                = $this->context['expect'];
			$this->context['expect'] = '';
			if ( CSSProcessor::TOKEN_FUNCTION === $type ) {
				++$this->context['depth'];
				$name                    = strtolower( $name );
				$this->context['expect'] = 'url' === $name ? 'url' : '';
				if ( in_array( $name, array( 'image-set', '-webkit-image-set' ), true ) ) {
					// In image-set("a.png" type("image/png"), "b.png" 2x),
					// type() is nested one level deeper than image-set(). Save each
					// open image-set's depth so only commas at that depth start images.
					// The matching ')' removes that entry. Cap the stack at 128 open
					// image-set() calls, even for malformed input. This does not cap
					// images within a set or separate image-set() calls in the file.
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
			if (
				CSSProcessor::TOKEN_URL === $type ||
				( '' !== $expected && CSSProcessor::TOKEN_STRING === $type )
			) {
				return true;
			}
		}
		return false;
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
