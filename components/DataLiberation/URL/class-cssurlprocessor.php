<?php

namespace WordPress\DataLiberation\URL;

use WordPress\DataLiberation\CSS\CSSProcessor;

/**
 * Finds and edits CSS URLs in a complete string or as source bytes arrive.
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
	 * Whether the current token holds a URL, rather than a comment or displayed text.
	 *
	 * The string in @import "theme.css" holds a URL; the same string after
	 * content: does not. Malformed string and URL tokens give false.
	 * This checks the CSS token and its position, not full URL validity.
	 *
	 * The next_token() method records this before updating $context for the
	 * following token. It is not saved in the cursor: resume reads a new token
	 * and classifies it using the saved context.
	 *
	 * @var bool True also for an empty URL.
	 */
	private $current_token_is_url = false;

	/**
	 * Remembers where a quoted string can be a URL as tokens are read.
	 *
	 * The next_url() method advances through next_token(), which updates this
	 * state. Streaming callers save it in the cursor so a new process can
	 * continue inside an image-set().
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
	 * Context before the current URL was read, for replaying that URL on resume.
	 *
	 * Reading the string in @import "theme.css" clears the next-string expectation.
	 * A cursor saved on that string needs the earlier expectation to find it again.
	 *
	 * @var array|null Same keys as $context; null before the first URL.
	 */
	private $context_before_url;

	/**
	 * @param string $css CSS source without wrapping braces.
	 */
	public function __construct( string $css ) {
		$this->processor = CSSProcessor::create( $css );
	}

	/**
	 * Opens a URL iterator that accepts CSS through append_bytes().
	 *
	 * Use the same next_url(), get_raw_url(), and set_raw_url() calls as for a
	 * complete string. The caller chooses replacements; this processor does not
	 * accept URL mappings. Call input_finished() at the actual source EOF.
	 *
	 * To resume, supply source bytes starting at the saved token byte offset
	 * and the cursor. A cursor saved on a URL reads that URL again.
	 *
	 * @param string      $css    Initial source bytes; may be empty.
	 * @param string|null $cursor Opaque state from get_reentrancy_cursor(), or null for a new stream.
	 * @return static
	 */
	public static function create_for_streaming( string $css = '', ?string $cursor = null ) {
		$state = null;
		if ( null !== $cursor ) {
			$state = json_decode( $cursor, true );
			if ( ! is_array( $state ) || ! isset( $state['css'], $state['context'] ) || ! is_string( $state['css'] ) || ! is_array( $state['context'] ) ) {
				throw new \InvalidArgumentException( 'The CSS URL cursor must contain CSS parser state and URL context.' );
			}
		}
		$processor            = new static( '' );
		$processor->processor = CSSProcessor::create_for_streaming( $css, $state['css'] ?? null );
		if ( null !== $state ) {
			$processor->context = $state['context'];
		}
		return $processor;
	}

	/**
	 * Adds source bytes without discarding earlier bytes or edits.
	 *
	 * Continue with next_url(). Use flush_processed_css() separately when the
	 * caller is ready to write and release completed output.
	 *
	 * @param string $bytes Next source bytes.
	 */
	public function append_bytes( string $bytes ): void {
		$this->processor->append_bytes( $bytes );
	}

	/** Marks actual source EOF; a stopped download must not call this method. */
	public function input_finished(): void {
		$this->processor->input_finished();
	}

	/** Returns whether the caller may still supply source bytes. */
	public function is_expecting_more_input(): bool {
		return $this->processor->is_expecting_more_input();
	}

	/** Returns whether next_url() stopped because parsing needs more source bytes. */
	public function is_paused_at_incomplete_input(): bool {
		return $this->processor->is_paused_at_incomplete_input();
	}

	/** Returns whether input has ended and no token remains to be read or inspected. */
	public function is_finished(): bool {
		return $this->processor->is_finished();
	}

	/**
	 * Returns completed CSS with edits applied and releases those source bytes.
	 *
	 * An unfinished token stays in memory until more input arrives or the caller
	 * marks EOF. This clears the current URL, but keeps the surrounding syntax
	 * so next_url() can continue inside an image-set(). No output-size limit is
	 * imposed; callers can flush after each URL to avoid accumulating many edits.
	 *
	 * For files, write and flush this output before saving the parser cursor,
	 * source byte offset, and output byte offset together. If a write fails,
	 * discard this processor. Resume from the last saved cursor and source
	 * offset, first removing output bytes after the saved output offset so the
	 * repeated input does not duplicate them. Keep source and edit rules unchanged.
	 *
	 * @return string Completed output, possibly empty; unfinished tokens have no size cap.
	 */
	public function flush_processed_css(): string {
		$output                     = $this->processor->flush_processed_css();
		$this->current_token_is_url = false;
		return $output;
	}

	/**
	 * Returns opaque state for a new processor at the current source position.
	 *
	 * Save get_token_byte_offset_in_the_input_stream() beside this string and
	 * supply source bytes from that offset when resuming. The cursor contains
	 * parsing context, but no CSS bytes, pending edits, or replacement rules.
	 * Do not depend on its internal format.
	 *
	 * @return string State accepted by create_for_streaming().
	 */
	public function get_reentrancy_cursor(): string {
		return json_encode(
			array(
				'css' => $this->processor->get_reentrancy_cursor(),
				'context' => $this->current_token_is_url ? $this->context_before_url : $this->context,
			)
		);
	}

	/**
	 * Returns the source offset from which a saved cursor must resume.
	 *
	 * This is the current URL's token start, or the next unread position when
	 * next_url() has paused or finished. After flushing, it is the first source
	 * byte not yet returned, even when replacements changed the output length.
	 *
	 * @return int Byte offset in the original source.
	 */
	public function get_token_byte_offset_in_the_input_stream(): int {
		return $this->processor->get_token_byte_offset_in_the_input_stream();
	}

	/**
	 * Finds the next URL in the supplied CSS.
	 *
	 * Recognizes url(), @import strings, and image-set() image strings. Skips
	 * comments, displayed text, and malformed string or URL tokens.
	 *
	 * @return bool True when get_raw_url() can read the next URL; false at EOF or when more bytes are needed.
	 */
	public function next_url(): bool {
		while ( $this->next_token() ) {
			if ( $this->current_token_is_url ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Moves to the next CSS token and records whether it holds a URL.
	 *
	 * In @import "theme.css", the @import token makes the next string a URL.
	 * url("photo.png") and image-set("photo.png" 1x) use the same rule. Spaces
	 * and comments do not clear that expectation; any other token consumes it.
	 * An unquoted url(photo.png) is already one URL token, including its wrapper.
	 *
	 * Both whole-string and streaming callers advance here so they cannot skip
	 * the state changes needed to interpret later strings. A successful read can
	 * produce a comment or a malformed token. $current_token_is_url tells
	 * whether the current token holds a URL that the caller can read or replace.
	 *
	 * @return bool True when there is a current token; false at the end of input or when more bytes are needed.
	 */
	private function next_token(): bool {
		$this->current_token_is_url = false;
		if ( ! $this->processor->next_token() ) {
			return false;
		}
		$type = $this->processor->get_token_type();
		if ( in_array( $type, array( CSSProcessor::TOKEN_WHITESPACE, CSSProcessor::TOKEN_COMMENT ), true ) ) {
			return true;
		}
		// The expectation belongs to this token. For @import "theme.css", save
		// that the string is a URL before clearing the expectation for the next token.
		$this->current_token_is_url = CSSProcessor::TOKEN_URL === $type ||
			( '' !== $this->context['expect'] && CSSProcessor::TOKEN_STRING === $type );
		if ( $this->current_token_is_url ) {
			$this->context_before_url = $this->context;
		}
		$name                    = in_array( $type, array( CSSProcessor::TOKEN_FUNCTION, CSSProcessor::TOKEN_AT_KEYWORD ), true ) ? $this->processor->get_token_value() : '';
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
		return true;
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
