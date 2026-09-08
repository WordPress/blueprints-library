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
	 * Direct unquoted URL tokens already include their url() wrapper.
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
