<?php

namespace WordPress\DataLiberation\CSS;

use function WordPress\Encoding\codepoint_to_utf8_bytes;
use function WordPress\Encoding\compat\_wp_scan_utf8;
use function WordPress\Encoding\utf8_ord;
use function WordPress\Encoding\wp_scrub_utf8;

/**
 * Tokenizes CSS according to the CSS Syntax Level 3 specification.
 *
 * This class follows the algorithm in https://www.w3.org/TR/css-syntax-3/ and
 * exposes a pull-based API so callers can stream over large stylesheets without
 * allocating every token up front. Each call to next_token() advances the cursor
 * and fills in metadata (type, value, raw slice, byte offsets) that you can read
 * through the getter methods.
 *
 * ## Design choices
 *
 * ### On-the-fly normalization
 *
 * The CSS Spec requires the following normalization step:
 *
 * > Replace any U+000D CARRIAGE RETURN (CR) code points, U+000C FORM FEED (FF)
 * > code points, or pairs of U+000D CARRIAGE RETURN (CR) followed by U+000A LINE
 * > FEED (LF) in input by a single U+000A LINE FEED (LF) code point.
 * > Replace any U+0000 NULL or surrogate code points in input with U+FFFD REPLACEMENT
 * > CHARACTER (�).
 *
 * This processor delays normalization as much as possible. That keeps the raw byte
 * positions intact for accurate rewrites while still letting consumers ask for a
 * normalized token when they need one.
 * For example, url(a<NUL>b) is a URL token whose value is a�b, where <NUL>
 * means one zero byte in the source. That byte stays in the raw token, so later
 * edits still use byte offsets from the original input.
 *
 * ### No EOF token
 *
 * The EOF token is a CSS parsing concept, not CSS tokenization concept. Therefore,
 * this processor does not produce it.
 *
 * ### UTF-8 handling
 *
 * Only UTF-8 strings are supported. Invalid sequences are replaced with U+FFFD (�)
 * using the maximal subpart approach described in
 * https://www.unicode.org/versions/Unicode9.0.0/ch03.pdf, section 3.9 Best Practices
 * for Using U+FFFD.
 *
 * ## Usage
 *
 * Basic iteration:
 *
 * ```php
 * $css = 'width: 10px;';
 * $processor = CSSProcessor::create( $css );
 * while ( $processor->next_token() ) {
 *     echo $processor->get_normalized_token();
 * }
 * // Outputs:
 * // width: 10px;
 * ```
 *
 * Rewriting a URL while keeping the rest of the stylesheet intact:
 *
 * ```php
 * $css = 'background: url(old.jpg) center / cover;';
 * $processor = CSSProcessor::create( $css );
 * while ( $processor->next_token() ) {
 *     if ( CSSProcessor::TOKEN_URL === $processor->get_token_type() ) {
 *         $processor->set_value( 'uploads/new.jpg' );
 *     }
 * }
 * $result = $processor->get_updated_css();
 * // background: url(uploads/new.jpg) center / cover;
 * ```
 *
 * Gathering diagnostics with byte offsets:
 *
 * ```php
 * $css = "color: red;\ncolor: re\nd;";
 * $processor = CSSProcessor::create( $css );
 * $bad_strings = array();
 * while ( $processor->next_token() ) {
 *     if ( CSSProcessor::TOKEN_BAD_STRING === $processor->get_token_type() ) {
 *         $bad_strings[] = array(
 *             'start'  => $processor->get_token_start(),
 *             'length' => $processor->get_token_length(),
 *             'value'  => $processor->get_unnormalized_token(),
 *         );
 *     }
 * }
 * ```
 *
 * @see https://www.w3.org/TR/css-syntax-3/#tokenization
 */
class CSSProcessor {
	/**
	 * Token type constants matching the CSS Syntax Level 3 specification.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#tokenization
	 */
	public const TOKEN_WHITESPACE = 'whitespace-token';
	public const TOKEN_COMMENT    = 'comment';
	public const TOKEN_STRING     = 'string-token';

	/**
	 * BAD-STRING tokens occur when a string contains an unescaped newline.
	 *
	 * Valid strings: "hello", 'world', "line1\Aline2" (escaped newline)
	 * Invalid (produces bad-string): "hello
	 *                                 world"  (literal newline breaks the string)
	 *
	 * The processor stops at the newline and produces a bad-string token for error recovery.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-bad-string-token
	 */
	public const TOKEN_BAD_STRING    = 'bad-string-token';
	public const TOKEN_HASH          = 'hash-token';
	public const TOKEN_DELIM         = 'delim-token';
	public const TOKEN_NUMBER        = 'number-token';
	public const TOKEN_PERCENTAGE    = 'percentage-token';
	public const TOKEN_DIMENSION     = 'dimension-token';
	public const TOKEN_AT_KEYWORD    = 'at-keyword-token';
	public const TOKEN_COLON         = 'colon-token';
	public const TOKEN_SEMICOLON     = 'semicolon-token';
	public const TOKEN_COMMA         = 'comma-token';
	public const TOKEN_LEFT_PAREN    = '(-token';
	public const TOKEN_RIGHT_PAREN   = ')-token';
	public const TOKEN_LEFT_BRACKET  = '[-token';
	public const TOKEN_RIGHT_BRACKET = ']-token';
	public const TOKEN_LEFT_BRACE    = '{-token';
	public const TOKEN_RIGHT_BRACE   = '}-token';
	public const TOKEN_FUNCTION      = 'function-token';

	/**
	 * URL tokens represent unquoted URLs in url() notation.
	 *
	 * Valid: url(image.jpg), url(https://example.com)
	 * Quoted URLs are parsed as url( + string-token + ), not url-token.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-url-token
	 */
	public const TOKEN_URL = 'url-token';

	/**
	 * BAD-URL tokens occur when a URL contains invalid characters.
	 *
	 * Invalid characters: quotes ("), apostrophes ('), parentheses (()
	 * Example invalid: url(image(.jpg) or url(image".jpg)
	 *
	 * When detected, the processor consumes everything up to ) or EOF.
	 * This prevents the bad URL from breaking subsequent tokens.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-bad-url-token
	 */
	public const TOKEN_BAD_URL = 'bad-url-token';

	/**
	 * Identifier tokens, such as `color`, `margin-top`, `red`,
	 * `inherit`, `--my-var`, `\escaped`, `über` (Unicode), etc.
	 *
	 * They can contain: letters, digits, hyphens, underscores, non-ASCII, escapes
	 * and cannot start with a digit (unless preceded by a hyphen).
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-ident-token
	 */
	public const TOKEN_IDENT = 'ident-token';

	/**
	 * CDC (Comment Delimiter Close) token: -->
	 *
	 * Legacy token from when CSS was embedded in HTML <style> tags
	 * and needed to be hidden from old browsers using HTML comments:
	 *
	 *   <style>
	 *   <!--
	 *   body { color: red; }
	 *   -->
	 *   </style>
	 *
	 * Modern CSS no longer needs these, but they're preserved for compatibility.
	 * In stylesheets, they're typically treated like whitespace.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-CDC-token
	 */
	public const TOKEN_CDC = 'CDC-token';

	/**
	 * CDO (Comment Delimiter Open) token: <!--
	 *
	 * Legacy token from when CSS was embedded in HTML <style> tags.
	 * See TOKEN_CDC for full explanation of HTML comment compatibility.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#typedef-CDO-token
	 */
	public const TOKEN_CDO = 'CDO-token';

	/**
	 * @var string
	 */
	private $css;

	/**
	 * @var int
	 */
	private $length = 0;

	/**
	 * @var int
	 */
	private $at = 0;

	/**
	 * The type of the current token. One of the self::TOKEN_* constants.
	 *
	 * @var string|null
	 */
	private $token_type = null;

	/**
	 * The type flag for the current token, if any.
	 *
	 * Hash tokens carry an "id" or "unrestricted" flag. Per CSS Syntax Level 3,
	 * <number-token> and <dimension-token> have a type flag indicating whether
	 * the number was written as an integer or a number (with decimal point or
	 * exponent). <percentage-token> does not have a type flag.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-number
	 *
	 * @var string|null
	 * @phpstan-var 'id'|'unrestricted'|'integer'|'number'|null
	 */
	private $token_type_flag = null;

	/**
	 * The byte offset at which the current token starts.
	 *
	 * Example:
	 *
	 * background-image: url(https://example.com/image.jpg);
	 *                   ^ token_starts_at
	 *
	 * @var int|null
	 */
	private $token_starts_at = null;

	/**
	 * The byte length of the current token.
	 *
	 * Example:
	 *
	 * background-image: url(https://example.com/image.jpg);
	 *                   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 *                             token_length
	 *
	 * @var int|null
	 */
	private $token_length = null;

	/**
	 * The byte offset at which the value of the current token starts.
	 *
	 * It is used for STRING and URL tokens. For example:
	 *
	 * background-image: url(https://example.com/image.jpg);
	 *                       ^ token_value_starts_at
	 *
	 * @var int|null
	 */
	private $token_value_starts_at = null;

	/**
	 * The byte offset at which the value of the current token starts.
	 *
	 * It is relevant for STRING and URL tokens. For example:
	 *
	 * background-image: url(https://example.com/image.jpg);
	 *                       ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	 *                             token_value_length
	 *
	 * @var int|null
	 */
	private $token_value_length = null;

	/**
	 * A cache for the decoded and normalized token value.
	 *
	 * - `false` indicates the has not been computed.
	 * - `null` is used for token types without an associated value will have `null`: whitespace, bad-url, comment, punctuation, etc.
	 * - `string` is used for token types with an associated value: ident, string, function, url, etc.
	 *
	 * @var string|null|false
	 */
	private $token_value = false;

	/**
	 * The unit of the current token, e.g. "px", "em", "deg", etc.
	 *
	 * @var string|null
	 */
	private $token_unit = null;

	/**
	 * Lexical replacements to apply to input CSS document.
	 *
	 * Tracks modifications to be applied to the CSS, such as changing URL values.
	 * Each entry is an associative array with 'start', 'length', and 'text' keys.
	 *
	 * @var array[]
	 */
	private $lexical_updates = array();

	/** @var bool Whether an unfinished token may receive more source bytes. */
	private $expecting_more_input = false;

	/** @var bool Whether the last scan needs more bytes before it can return a token. */
	private $paused_at_incomplete_input = false;

	/** @var int Source bytes before the retained buffer, including bytes discarded by earlier processes. */
	private $input_bytes_forgotten = 0;

	/**
	 * Constructor for the CSS processor.
	 *
	 * Do not instantiate directly. Use CSSProcessor::create() instead.
	 *
	 * @param string $css         CSS source to tokenize.
	 */
	private function __construct( string $css ) {
		$this->css    = $css;
		$this->length = strlen( $css );
	}

	/**
	 * Creates a CSS processor for the given CSS string.
	 *
	 * Use this method to create a CSS processor instance.
	 *
	 * ## Current Support
	 *
	 * - The only supported document encoding is `UTF-8`, which is the default value.
	 *
	 * @param string $css      CSS source to tokenize.
	 * @param string $encoding Text encoding of the document; must be default of 'UTF-8'.
	 * @return static|null The created processor if successful, otherwise null.
	 */
	public static function create( string $css, string $encoding = 'UTF-8' ) {
		if ( 'UTF-8' !== $encoding ) {
			return null;
		}

		return new static( $css );
	}

	/**
	 * Opens a processor that accepts more CSS through append_bytes().
	 *
	 * To resume, supply source bytes starting at the saved token byte offset,
	 * together with the cursor. The cursor contains no CSS bytes or pending edits.
	 * A cursor saved on a token reads that token again, as XMLProcessor does.
	 *
	 * @param string      $css    Initial source bytes; may be empty.
	 * @param string|null $cursor Opaque state from get_reentrancy_cursor(), or null for a new stream.
	 * @return static
	 */
	public static function create_for_streaming( string $css = '', ?string $cursor = null ) {
		$processor                       = new static( $css );
		$processor->expecting_more_input = true;
		if ( null !== $cursor ) {
			$state = json_decode( $cursor, true );
			if ( ! is_array( $state ) || ! isset( $state['input_offset'], $state['expecting_more_input'] ) || ! is_int( $state['input_offset'] ) || $state['input_offset'] < 0 || ! is_bool( $state['expecting_more_input'] ) ) {
				throw new \InvalidArgumentException( 'The CSS cursor must contain a nonnegative source byte offset and whether more input is expected.' );
			}
			$processor->input_bytes_forgotten = $state['input_offset'];
			$processor->expecting_more_input  = $state['expecting_more_input'];
		}
		return $processor;
	}

	/**
	 * Adds source bytes and allows a paused scan to continue.
	 *
	 * Earlier bytes and edits remain available through get_updated_css(). Call
	 * flush_processed_css() separately to return and release completed output.
	 * An unfinished token stays in memory and is parsed again; its size is not capped.
	 *
	 * @param string $bytes Next source bytes.
	 */
	public function append_bytes( string $bytes ): void {
		if ( ! $this->expecting_more_input ) {
			throw new \LogicException( 'CSS input cannot be appended after the end of the stylesheet.' );
		}
		$this->css                       .= $bytes;
		$this->length                     = strlen( $this->css );
		$this->paused_at_incomplete_input = false;
	}

	/**
	 * Marks the actual end of the source, so the next scan uses CSS's EOF rules.
	 *
	 * A download stopping early is not EOF. Unlike XML, CSS can return an unclosed
	 * string or URL at EOF; input_finished() preserves those existing CSS rules.
	 */
	public function input_finished(): void {
		$this->expecting_more_input       = false;
		$this->paused_at_incomplete_input = false;
	}

	/** Returns whether more source bytes may be appended. */
	public function is_expecting_more_input(): bool {
		return $this->expecting_more_input;
	}

	/** Returns whether the last scan stopped for more bytes, including token lookahead. */
	public function is_paused_at_incomplete_input(): bool {
		return $this->paused_at_incomplete_input;
	}

	/** Returns whether input has ended and no token remains to be read or inspected. */
	public function is_finished(): bool {
		return ! $this->expecting_more_input && $this->at >= $this->length && null === $this->token_type;
	}

	/**
	 * Returns edited, completed input and keeps the unfinished token for another read.
	 *
	 * Existing whole-token setters work in streaming mode; their edits are applied
	 * only to the returned prefix. Flushing also clears the current token. Write and
	 * flush this output before saving a file-rewrite cursor and its source offset.
	 *
	 * @return string Processed CSS, including edits made with set_token_value().
	 */
	public function flush_processed_css(): string {
		if ( 0 === $this->at ) {
			return '';
		}
		$pending                      = substr( $this->css, $this->at );
		$updated                      = $this->get_updated_css();
		$output                       = substr( $updated, 0, strlen( $updated ) - strlen( $pending ) );
		$this->css                    = $pending;
		$this->length                 = strlen( $pending );
		$this->input_bytes_forgotten += $this->at;
		$this->at                     = 0;
		$this->lexical_updates        = array();
		$this->after_token();
		return $output;
	}

	/**
	 * Returns opaque parser state for a new processor at the current source position.
	 *
	 * Save get_token_byte_offset_in_the_input_stream() separately. Resume reads
	 * the original source again from that position; the cursor contains neither
	 * unfinished bytes nor edits. Do not depend on the string's internal format.
	 *
	 * @return string State accepted by create_for_streaming().
	 */
	public function get_reentrancy_cursor(): string {
		return json_encode(
			array(
				'input_offset' => $this->get_token_byte_offset_in_the_input_stream(),
				'expecting_more_input' => $this->expecting_more_input,
			)
		);
	}

	/**
	 * Returns the original source offset from which a saved cursor must resume.
	 *
	 * This is the current token's start, or the next unread position when no token
	 * is exposed. After flushing, it is the first source byte not yet returned.
	 *
	 * @return int Byte offset in the source, unaffected by replacement lengths.
	 */
	public function get_token_byte_offset_in_the_input_stream(): int {
		return $this->input_bytes_forgotten + ( $this->token_starts_at ?? $this->at );
	}

	/**
	 * Moves to the next token in the CSS stream.
	 *
	 * Implements the main tokenization loop, consuming the next token from the input stream.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-token
	 *
	 * @return bool Whether a complete token was found; false also means more input is needed.
	 */
	public function next_token(): bool {
		if ( $this->paused_at_incomplete_input ) {
			return false;
		}
		$start = $this->at;
		if ( ! $this->scan_next_token() ) {
			$this->paused_at_incomplete_input = $this->expecting_more_input;
			return false;
		}
		// A CSS escape needs at most six hex digits and CRLF after its backslash.
		// Ten bytes also cover UTF-8 and the lookahead used to classify a token.
		// A name may become a function, and a number may acquire a unit. Retry the
		// whole token when more bytes arrive instead of saving its internal state.
		if ( $this->expecting_more_input && $this->at > $this->length - 10 ) {
			$this->at = $start;
			$this->after_token();
			$this->paused_at_incomplete_input = true;
			return false;
		}
		return true;
	}

	/** Reads a whole token with the same CSS rules for complete and growing input. */
	private function scan_next_token(): bool {
		$this->after_token();

		// Bale out once we reach the end.
		if ( $this->at >= $this->length ) {
			return false;
		}

		/*
		 * CSS comments. They are not preserved as tokens in the specification, but we
		 * still track them.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#consume-comment
		 */
		if (
			$this->at + 1 < $this->length &&
			'/' === $this->css[ $this->at ] &&
			'*' === $this->css[ $this->at + 1 ]
		) {
			$this->token_type            = self::TOKEN_COMMENT;
			$this->token_starts_at       = $this->at;
			$this->token_value_starts_at = $this->at;

			$end                      = strpos( $this->css, '*/', $this->at + 2 );
			$this->at                 = false !== $end ? $end + 2 : $this->length;
			$this->token_length       = $this->at - $this->token_starts_at;
			$this->token_value_length = $this->token_length - 4;
			return true;
		}

		/*
		 * Whitespace tokens.
		 *
		 * We consider U+000A LINE FEED, U+0009 CHARACTER TABULATION, and U+0020 SPACE bytes covered by the spec.
		 * In addition, we also capture U+000D CARRIAGE RETURN and U+000C FORM FEED that are normally converted to
		 * U+000A LINE FEED during the preprocessing phase.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#newline
		 * @see https://www.w3.org/TR/css-syntax-3/#whitespace
		 */
		$whitespace_length = strspn( $this->css, "\t\n\f\r ", $this->at );
		if ( $whitespace_length > 0 ) {
			$this->token_type      = self::TOKEN_WHITESPACE;
			$this->token_length    = $whitespace_length;
			$this->token_starts_at = $this->at;
			$this->at             += $whitespace_length;
			return true;
		}

		/*
		 * String tokens with either " or ' as delimiters.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#consume-string-token
		 */
		if ( '"' === $this->css[ $this->at ] || "'" === $this->css[ $this->at ] ) {
			return $this->consume_string();
		}

		$char                  = $this->css[ $this->at ];
		$this->token_starts_at = $this->at;

		/*
		 * U+0023 NUMBER SIGN (#)
		 *
		 * A hash token is created when # is followed by an ident code point or valid escape.
		 * This is commonly used for hex colors (#fff) or ID selectors (#header).
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#consume-token
		 */
		if ( '#' === $char ) {
			if ( $this->at + 1 < $this->length ) {
				if (
					$this->consume_ident_codepoint( $this->at + 1 ) > 0 ||
					// The next two input code points are a valid escape.
					$this->is_valid_escape( $this->at + 1 )
				) {
					// Create a <hash-token>.
					++$this->at;

					$this->token_type_flag = $this->check_if_3_code_points_start_an_ident_sequence( $this->at )
						? 'id'
						: 'unrestricted';

					// Consume an ident sequence, and set the <hash-token>'s value to the returned string.
					$this->consume_ident_sequence();
					$this->token_type   = self::TOKEN_HASH;
					$this->token_length = $this->at - $this->token_starts_at;
					return true;
				}
			}
			// Otherwise, return a <delim-token> with its value set to the current input code point.
			++$this->at;
			$this->token_type   = self::TOKEN_DELIM;
			$this->token_length = 1;
			return true;
		}

		/*
		 * Simple single-byte tokens
		 *
		 * These characters form their own tokens when encountered.
		 * Note: ( tokens here are not function tokens - those are handled
		 * in consume_ident_like() when ( follows an identifier.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#tokenization
		 */
		$simple = array(
			'(' => self::TOKEN_LEFT_PAREN,
			')' => self::TOKEN_RIGHT_PAREN,
			',' => self::TOKEN_COMMA,
			':' => self::TOKEN_COLON,
			';' => self::TOKEN_SEMICOLON,
			'[' => self::TOKEN_LEFT_BRACKET,
			']' => self::TOKEN_RIGHT_BRACKET,
			'{' => self::TOKEN_LEFT_BRACE,
			'}' => self::TOKEN_RIGHT_BRACE,
		);
		if ( isset( $simple[ $char ] ) ) {
			++$this->at;
			$this->token_type   = $simple[ $char ];
			$this->token_length = 1;
			return true;
		}

		/*
		 * U+0040 COMMERCIAL AT (@)
		 *
		 * An at-keyword is @ followed by an identifier, used for at-rules like
		 * @media, @import, @keyframes, etc.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#consume-token
		 */
		if ( '@' === $char ) {
			++$this->at;
			// If the next 3 input code points after the @ would start an ident sequence,
			// consume an ident sequence, create an <at-keyword-token> with its value set to the returned value,
			// and return it.
			if ( $this->check_if_3_code_points_start_an_ident_sequence( $this->at ) ) {
				$this->consume_ident_sequence();
				$this->token_type   = self::TOKEN_AT_KEYWORD;
				$this->token_length = $this->at - $this->token_starts_at;
				return true;
			} else {
				// Otherwise, return a <delim-token> with its value set to the current input code point.
				$this->token_type   = self::TOKEN_DELIM;
				$this->token_length = 1;
				return true;
			}
		}

		/*
		 * Numbers start with digits, the plus sign, minus sign, and decimal point.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#starts-with-a-number
		 */
		if ( $this->would_next_3_code_points_start_a_number() ) {
			return $this->consume_numeric();
		}

		/*
		 * U+002D HYPHEN-MINUS (-)
		 */
		if ( '-' === $char ) {
			// This case is covered above:
			// > If the input stream starts with a number.

			/*
			 * If followed by another hyphen and >, this is a CDC token (-->)
			 *
			 * Comment Delimiter Close - legacy HTML comment syntax in CSS.
			 *
			 * @see https://www.w3.org/TR/css-syntax-3/#CDC-token-diagram
			 */
			if (
				$this->at + 2 < $this->length &&
				'-' === $this->css[ $this->at + 1 ] &&
				'>' === $this->css[ $this->at + 2 ]
			) {
				// Consume them and return a <CDC-token>.
				$this->at          += 3;
				$this->token_type   = self::TOKEN_CDC;
				$this->token_length = 3;
				return true;
			}

			// Otherwise, if the input stream starts with an ident sequence,
			// reconsume the current input code point, consume an ident-like
			// token, and return it.
			if ( $this->check_if_3_code_points_start_an_ident_sequence( $this->at ) ) {
				return $this->consume_ident_like();
			}

			// Otherwise, return a <delim-token> with its value set to the current input code point.
			++$this->at;
			$this->token_type   = self::TOKEN_DELIM;
			$this->token_length = 1;
			return true;
		}

		/*
		 * U+003C LESS-THAN SIGN (<)
		 * If followed by !--, this is a CDO token (<!--)
		 *
		 * Comment Delimiter Open - legacy HTML comment syntax in CSS.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#CDO-token-diagram
		 */
		if ( '<' === $char && $this->at + 3 < $this->length &&
			'!' === $this->css[ $this->at + 1 ] &&
			'-' === $this->css[ $this->at + 2 ] &&
			'-' === $this->css[ $this->at + 3 ] ) {
			// Consume them and return a <CDO-token>.
			$this->at          += 4;
			$this->token_type   = self::TOKEN_CDO;
			$this->token_length = 4;
			return true;
		}

		/*
		 * Ident-start code point
		 *
		 * If the input stream starts with an ident sequence, reconsume the current
		 * input code point, consume an ident-like token, and return it.
		 *
		 * Could be an identifier, function, or url() token.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#consume-ident-like-token
		 */
		if ( $this->check_if_3_code_points_start_an_ident_sequence( $this->at ) ) {
			return $this->consume_ident_like();
		}

		/*
		 * Delim token (delimiter)
		 *
		 * Any code point that doesn't match above rules becomes a delim token.
		 * Handle multi-byte UTF-8 characters properly.
		 *
		 * @see https://www.w3.org/TR/css-syntax-3/#delim-token-diagram
		 */
		if ( ord( $char ) >= 0x80 ) {
			$new_at         = $this->at;
			$invalid_length = 0;
			if ( 1 !== _wp_scan_utf8( $this->css, $new_at, $invalid_length, null, 1 ) ) {
				/**
				 * Trouble ahead!
				 * Bytes at $at are not a valid UTF-8 sequence.
				 *
				 * We'll move forward by $invalid_length bytes and continue processing.
				 * Later on, during the string decoding, we'll replace the invalid bytes with U+FFFD
				 * via maximal subpart”replacement.
				 */
				$matched_bytes = $invalid_length;
			} else {
				$matched_bytes = $new_at - $this->at;
			}

			$this->at          += $matched_bytes;
			$this->token_type   = self::TOKEN_DELIM;
			$this->token_length = $matched_bytes;
			return true;
		}

		// Single ASCII delim.
		++$this->at;
		$this->token_type   = self::TOKEN_DELIM;
		$this->token_length = 1;
		return true;
	}

	/**
	 * Gets the current token type.
	 *
	 * @return string|null
	 */
	public function get_token_type(): ?string {
		return $this->token_type;
	}

	/**
	 * Gets the current token type flag.
	 *
	 * Some token types have an additional flag:
	 * - Hash tokens have a flag that is either "id" or "unrestricted". The
	 *   following example uses an "id" hash token as the `#ident` ID selector and
	 *   an "unrestricted" hash token as the `#0f0` hex color:
	 *       #ident {
	 *         color: #0f0;
	 *       }
	 * - Number and dimension tokens have an "integer" flag when the number was
	 *   written without a decimal point or exponent (e.g. "42", "+7"), and a
	 *   "number" flag otherwise. Percentage tokens do not have a type flag.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-number
	 *
	 * @return string|null
	 * @phpstan-return 'id'|'unrestricted'|'integer'|'number'|null
	 */
	public function get_token_type_flag(): ?string {
		return $this->token_type_flag;
	}

	/**
	 * Gets the normalized token text from the CSS source.
	 *
	 * Returns the token with CSS normalization and escape decoding applied:
	 * - CSS escapes decoded (e.g., \6c → l, \2f → /, \A → newline)
	 * - \r\n, \r, \f → \n
	 * - \x00 → U+FFFD (�)
	 *
	 * This is different from get_token_value() which returns the semantic value
	 * (e.g., for strings: content without quotes; for numbers: numeric value).
	 *
	 * @return string|null
	 */
	public function get_normalized_token(): ?string {
		if ( null === $this->token_starts_at || null === $this->token_length ) {
			return null;
		}

		return $this->decode_range(
			$this->token_starts_at,
			$this->token_length,
			self::TOKEN_STRING === $this->token_type
		);
	}

	/**
	 * Gets the raw, unnormalized token text from the CSS source.
	 *
	 * Returns the exact bytes from the source without any normalization.
	 * This preserves original line endings (\r\n, \r, \f) and null bytes.
	 *
	 * @return string|null
	 */
	public function get_unnormalized_token(): ?string {
		if ( null === $this->token_starts_at || null === $this->token_length ) {
			return null;
		}
		return substr( $this->css, $this->token_starts_at, $this->token_length );
	}

	/**
	 * Gets the current token value as a normalized and decoded string. This is
	 * a slight divergence from the CSS Syntax Level 3 spec, where all the numberic
	 * values are parsed as numbers. This processor is only concerned with their
	 * textual representation.
	 *
	 * Returns the semantic value of the token per CSS Syntax Level 3 spec:
	 *
	 * - For delimiters: the single code point
	 * - For numbers/percentages: the string representation of the number
	 * - For dimensions: the string representation of the number (use get_token_unit() for the unit)
	 * - For identifiers/functions/hash/at-keywords: the decoded identifier string
	 * - For strings/URLs: the decoded string value
	 * - For other tokens: null
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#tokenization
	 * @return string|null
	 */
	public function get_token_value(): ?string {
		if ( false === $this->token_value ) {
			if ( null === $this->token_starts_at || null === $this->token_length ) {
				return null;
			}

			switch ( $this->token_type ) {
				case self::TOKEN_HASH:
					// Hash value starts after the # character.
					$this->token_value = $this->decode_range( $this->token_starts_at + 1, $this->token_length - 1 );
					break;

				case self::TOKEN_AT_KEYWORD:
					// At-keyword value starts after the @ character.
					$this->token_value = $this->decode_range( $this->token_starts_at + 1, $this->token_length - 1 );
					break;

				case self::TOKEN_FUNCTION:
					// Function name is everything except the final (.
					$this->token_value = $this->decode_range( $this->token_starts_at, $this->token_length - 1 );
					break;

				case self::TOKEN_IDENT:
					// Identifier is the entire token.
					$this->token_value = $this->decode_range( $this->token_starts_at, $this->token_length );
					break;

				case self::TOKEN_STRING:
					if ( null !== $this->token_value_starts_at && null !== $this->token_value_length ) {
						$this->token_value = $this->decode_range(
							$this->token_value_starts_at,
							$this->token_value_length,
							true
						);
					} else {
						$this->token_value = null;
					}
					break;

				case self::TOKEN_URL:
					if ( null !== $this->token_value_starts_at && null !== $this->token_value_length ) {
						$this->token_value = $this->decode_range(
							$this->token_value_starts_at,
							$this->token_value_length
						);
					} else {
						$this->token_value = null;
					}
					break;

				case self::TOKEN_DELIM:
					// Delim value is the single code point.
					$this->token_value = $this->decode_range( $this->token_starts_at, $this->token_length );
					break;

				case self::TOKEN_NUMBER:
					// Return the string representation of the number (not parsed to float).
					$this->token_value = substr( $this->css, $this->token_starts_at, $this->token_length );
					break;

				case self::TOKEN_PERCENTAGE:
					// Return the string representation of the number (without the %).
					$this->token_value = substr( $this->css, $this->token_starts_at, $this->token_length - 1 );
					break;

				case self::TOKEN_DIMENSION:
					// Return the string representation of the number (without the unit).
					$this->token_value = substr( $this->get_normalized_token(), 0, -strlen( $this->token_unit ) );
					break;

				default:
					$this->token_value = null;
					break;
			}
		}

		return $this->token_value;
	}

	/**
	 * Determines whether the current token is a data URI.
	 *
	 * Only meaningful for URL and STRING tokens. Returns false for all other token types.
	 *
	 * @return bool Whether the current token value starts with "data:" (case-insensitive).
	 */
	public function is_data_uri(): bool {
		if ( null === $this->token_value_starts_at || null === $this->token_value_length ) {
			return false;
		}

		if ( $this->token_value_length < 5 ) {
			return false;
		}

		$offset = $this->token_value_starts_at;
		return (
			( 'd' === $this->css[ $offset ] || 'D' === $this->css[ $offset ] ) &&
			( 'a' === $this->css[ $offset + 1 ] || 'A' === $this->css[ $offset + 1 ] ) &&
			( 't' === $this->css[ $offset + 2 ] || 'T' === $this->css[ $offset + 2 ] ) &&
			( 'a' === $this->css[ $offset + 3 ] || 'A' === $this->css[ $offset + 3 ] ) &&
			':' === $this->css[ $offset + 4 ]
		);
	}

	/**
	 * Gets the token start at.
	 *
	 * @return int|null
	 */
	public function get_token_start(): ?int {
		return $this->token_starts_at;
	}

	/**
	 * Gets the token length.
	 *
	 * @return int|null
	 */
	public function get_token_length(): ?int {
		return $this->token_length;
	}

	/**
	 * Gets the unit for dimension tokens.
	 *
	 * @return string|null
	 */
	public function get_token_unit(): ?string {
		return $this->token_unit;
	}

	/**
	 * Gets the byte at where the token value starts (for STRING and URL tokens).
	 *
	 * @return int|null
	 */
	public function get_token_value_start(): ?int {
		return $this->token_value_starts_at;
	}

	/**
	 * Gets the byte length of the token value (for STRING and URL tokens).
	 *
	 * @return int|null
	 */
	public function get_token_value_length(): ?int {
		return $this->token_value_length;
	}

	/**
	 * Sets the value of the current URL token.
	 *
	 * This method allows modifying the URL value in url() tokens. The new value
	 * will be properly escaped according to CSS URL syntax rules.
	 *
	 * Currently only URL tokens are supported. Attempting to set the value on
	 * other token types will return false.
	 *
	 * Example:
	 *
	 *     $css = 'background: url(old.jpg);';
	 *     $processor = CSSProcessor::create( $css );
	 *     while ( $processor->next_token() ) {
	 *         if ( CSSProcessor::TOKEN_URL === $processor->get_token_type() ) {
	 *             $processor->set_token_value( 'new.jpg' );
	 *         }
	 *     }
	 *     echo $processor->get_updated_css();
	 *     // Outputs: background: url(new.jpg);
	 *
	 * @param string $new_value The new URL value (should not include url() wrapper).
	 * @return bool Whether the value was successfully updated.
	 */
	public function set_token_value( string $new_value ): bool {
		// Only URL and string tokens are currently supported.
		switch ( $this->token_type ) {
			case self::TOKEN_URL:
				$this->lexical_updates[] = array(
					'start'  => $this->token_value_starts_at,
					'length' => $this->token_value_length,
					'text'   => self::escape_url_value( $new_value ),
				);
				return true;
			case self::TOKEN_STRING:
				$this->lexical_updates[] = array(
					'start'  => $this->token_starts_at,
					'length' => $this->token_length,
					'text'   => self::escape_url_value( $new_value ),
				);
				return true;
			default:
				_doing_it_wrong( __METHOD__, 'set_token_value() only supports URL and string tokens. Got token type: ' . $this->token_type, '1.0.0' );
				return false;
		}
	}

	/**
	 * Measures where a matched prefix ends in the exact CSS text we received.
	 *
	 * These spellings all decode to the same 19-byte prefix, "https://old.example":
	 *
	 *     Actual CSS spelling          Source bytes to replace
	 *     https://old.example          19
	 *     https://\6f ld.example       22
	 *     https://\00006fld.example    25
	 *
	 * "\6f " and "\00006f" both mean "o", but occupy four and seven source
	 * bytes respectively. The space in "\6f " is part of that CSS escape.
	 *
	 * The method receives both the actual CSS text ($raw_value) and the
	 * matched prefix's decoded byte length ($decoded_bytes). It:
	 *
	 * 1. Reads that particular CSS spelling.
	 * 2. Decodes it until it has accounted for the requested decoded bytes.
	 * 3. Returns how many original bytes it consumed.
	 *
	 * The number 19 alone cannot determine the answer. The actual CSS source
	 * determines whether the result is 19, 22, or 25 in the examples above.
	 * The caller uses that result to cut off the old host without touching
	 * the filename. It has already checked that the decoded URL matches the
	 * prefix; this method only counts bytes and does not change the CSS.
	 * Escape and UTF-8 decoding follow the same rules as token values.
	 *
	 * Examples from unquoted url(...) values ($is_string = false):
	 *
	 *     $decoded_prefix = 'https://old.example';
	 *     $decoded_bytes  = strlen( $decoded_prefix ); // 19.
	 *
	 *     // No CSS escapes: replace 19 source bytes for the 19-byte prefix.
	 *     CSSProcessor::measure_value_prefix( 'https://old.example/photo.png', $decoded_bytes, false ); // 19.
	 *
	 *     // A seven-byte spelling of "o" makes this prefix 25 source bytes long.
	 *     CSSProcessor::measure_value_prefix( 'https://\00006fld.example/photo.png', $decoded_bytes, false ); // 25.
	 *
	 *     // Escaped "o": replace 22 source bytes for the same 19-byte prefix.
	 *     $raw_value    = 'https://\6f ld.example/photo\2e png';
	 *     $source_bytes = CSSProcessor::measure_value_prefix( $raw_value, $decoded_bytes, false ); // 22.
	 *     $updated      = substr_replace( $raw_value, 'https://new.example', 0, $source_bytes );
	 *     // Result: https://new.example/photo\2e png
	 *
	 * The filename's "\2e " escape stays exactly as written. Using 19 instead
	 * of 22 in substr_replace() would leave "ple" from the old host and produce:
	 * https://new.exampleple/photo\2e png
	 *
	 * @param string $raw_value     Original CSS value bytes, without quotes or the url() wrapper.
	 * @param int    $decoded_bytes strlen() of the prefix already matched against the decoded value.
	 * @param bool   $is_string     True for a quoted CSS string: backslash-newline sequences occupy
	 *                             source bytes but add no decoded bytes. False for an unquoted URL.
	 * @return int Number of bytes to replace at the start of $raw_value, leaving the suffix untouched.
	 */
	public static function measure_value_prefix( string $raw_value, int $decoded_bytes, bool $is_string ): int {
		$processor = new static( $raw_value );
		$at        = 0;
		$decoded   = 0;
		while ( $decoded < $decoded_bytes && $at < $processor->length ) {
			// Ordinary URL bytes need no decoding. Stop the native scan at the prefix boundary.
			$plain_bytes = strspn( $raw_value, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._~:/?#[]@!$&*+,;=%', $at, $decoded_bytes - $decoded );
			if ( $plain_bytes > 0 ) {
				$at      += $plain_bytes;
				$decoded += $plain_bytes;
				continue;
			}
			$char = $raw_value[ $at ];
			if ( '\\' === $char && $is_string && $at + 1 < $processor->length && false !== strpos( "\r\n\f", $raw_value[ $at + 1 ] ) ) {
				$at += "\r" === $raw_value[ $at + 1 ] && "\n" === substr( $raw_value, $at + 2, 1 ) ? 3 : 2;
			} elseif ( '\\' === $char && $processor->is_valid_escape( $at ) ) {
				++$at;
				$decoded += strlen( $processor->decode_escape_at( $at, $consumed ) );
				$at      += $consumed;
			} else {
				$next    = $at;
				$invalid = 0;
				if ( 1 === _wp_scan_utf8( $raw_value, $next, $invalid, null, 1 ) ) {
					$decoded += "\x00" === $char ? 3 : $next - $at;
					$at       = $next;
				} else {
					$decoded += 3;
					$at      += $invalid;
				}
			}
		}
		return $at;
	}

	/**
	 * Escapes a replacement prefix for either quoted or unquoted CSS URL syntax.
	 *
	 * Keep the existing quotes or url() delimiters outside the replacement so
	 * the unmatched suffix can keep its original source spelling.
	 *
	 * @param string $value Decoded replacement URL base.
	 * @return string CSS value bytes without surrounding quotes.
	 */
	public static function escape_value_prefix( string $value ): string {
		return self::escape_url_value( $value, false );
	}

	/**
	 * Escapes a URL value for use in quoted url() syntax.
	 *
	 * Whole-value replacements use quoted URL strings because they are easier
	 * to escape. Quoted URLs are consumed using the string token
	 * rules, and the only values we need to escape in strings, are:
	 *
	 * * Trailing quote.
	 * * Newlines. That amounts to \n, \r, \f, \r\n when preprocessing is considered.
	 * * U+005C REVERSE SOLIDUS (\)
	 *
	 * Prefix replacements keep the surrounding syntax and also escape spaces,
	 * controls, apostrophes, and parentheses to work in unquoted URLs.
	 *
	 * @param string $unescaped Decoded URL value or prefix.
	 * @param bool   $quote Whether to wrap a complete replacement in quotes.
	 * @return string Escaped CSS value bytes.
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-url-token
	 */
	private static function escape_url_value( string $unescaped, bool $quote = true ): string {
		$unsafe = $quote ? "\n\r\f\\\"" : "\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b\f\r\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f \x7f\\\"'()";
		// Scanning once is cheaper than a replacement lookup for long URLs with nothing to escape.
		if ( strcspn( $unescaped, $unsafe ) === strlen( $unescaped ) ) {
			return $quote ? '"' . $unescaped . '"' : $unescaped;
		}

		/**
		 * Add a trailing space to prevent accidentally creating a
		 * wrong escape sequence. This is a valid CSS syntax and
		 * CSS parsers will ignore that whitespace.
		 *
		 * Without the space, "carriage\return" would be encoded as "carriage\aeturn",
		 * making `e` a part of the escape sequence `\ae` which is not
		 * what the caller intended.
		 */
		$escapes = array(
			"\r\n" => '\a ',
			"\r"   => '\a ',
			"\n"   => '\a ',
			"\f"   => '\a ',
			'\\'   => '\5C ',
			'"'    => '\22 ',
		);
		if ( ! $quote ) {
			$escapes += array(
				"\x00" => '\0 ',
				"\x01" => '\1 ',
				"\x02" => '\2 ',
				"\x03" => '\3 ',
				"\x04" => '\4 ',
				"\x05" => '\5 ',
				"\x06" => '\6 ',
				"\x07" => '\7 ',
				"\x08" => '\8 ',
				"\x09" => '\9 ',
				"\x0b" => '\b ',
				"\x0e" => '\e ',
				"\x0f" => '\f ',
				"\x10" => '\10 ',
				"\x11" => '\11 ',
				"\x12" => '\12 ',
				"\x13" => '\13 ',
				"\x14" => '\14 ',
				"\x15" => '\15 ',
				"\x16" => '\16 ',
				"\x17" => '\17 ',
				"\x18" => '\18 ',
				"\x19" => '\19 ',
				"\x1a" => '\1a ',
				"\x1b" => '\1b ',
				"\x1c" => '\1c ',
				"\x1d" => '\1d ',
				"\x1e" => '\1e ',
				"\x1f" => '\1f ',
				' '    => '\20 ',
				"\x7f" => '\7f ',
				"'"    => '\27 ',
				'('    => '\28 ',
				')'    => '\29 ',
			);
		}
		// strtr() matches CRLF before CR and does not escape the spaces or backslashes it inserts.
		$escaped = strtr( $unescaped, $escapes );
		return $quote ? '"' . $escaped . '"' : $escaped;
	}

	/**
	 * Returns the CSS with all modifications applied.
	 *
	 * This method applies all queued lexical updates and returns the modified CSS.
	 * If no modifications were made, returns the original CSS.
	 *
	 * Example:
	 *
	 *     $css = 'background: url(old.jpg);';
	 *     $processor = CSSProcessor::create( $css );
	 *     while ( $processor->next_token() ) {
	 *         if ( CSSProcessor::TOKEN_URL === $processor->get_token_type() ) {
	 *             $processor->set_token_value( 'new.jpg' );
	 *         }
	 *     }
	 *     echo $processor->get_updated_css();
	 *     // Outputs: background: url(new.jpg);
	 *
	 * @return string The modified CSS.
	 */
	public function get_updated_css(): string {
		if ( empty( $this->lexical_updates ) ) {
			return $this->css;
		}

		// Sort updates by start position in ascending order.
		usort(
			$this->lexical_updates,
			function ( $a, $b ) {
				return $a['start'] - $b['start'];
			}
		);

		// Build the output by concatenating original CSS fragments with replacements.
		$bytes_already_copied = 0;
		$output               = '';

		foreach ( $this->lexical_updates as $update ) {
			$output              .= substr( $this->css, $bytes_already_copied, $update['start'] - $bytes_already_copied );
			$output              .= $update['text'];
			$bytes_already_copied = $update['start'] + $update['length'];
		}

		// Copy remaining CSS after last update.
		$output .= substr( $this->css, $bytes_already_copied );

		return $output;
	}

	/**
	 * Clears token state between tokens.
	 */
	private function after_token(): void {
		$this->token_type            = null;
		$this->token_type_flag       = null;
		$this->token_starts_at       = null;
		$this->token_length          = null;
		$this->token_value           = false;
		$this->token_unit            = null;
		$this->token_value_starts_at = null;
		$this->token_value_length    = null;
	}

	/**
	 * Consumes a string token.
	 *
	 * Strings are quoted with either " or ' and can contain escape sequences.
	 * Newlines inside strings (without escaping) make the string invalid.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-string-token
	 *
	 * @return bool
	 */
	private function consume_string(): bool {
		// Initially create a <string-token> with its value set to the empty string.
		$this->token_starts_at = $this->at;
		$ending_char           = $this->css[ $this->at ];

		// Skip past the opening quote.
		++$this->at;
		$value_starts_at = $this->at;

		// Characters that need special handling: the ending quote, newlines, backslashes.
		$special_chars = "'" === $ending_char ? "'\n\f\r\\" : "\"\n\f\r\\";

		while ( $this->at < $this->length ) {
			// Consume normal characters until we hit a special character.
			$normal_len = strcspn( $this->css, $special_chars, $this->at );
			if ( $normal_len > 0 ) {
				$this->at += $normal_len;
			}

			if ( $this->at >= $this->length ) {
				break; // EOF.
			}

			$char = $this->css[ $this->at ];
			switch ( $char ) {
				case $ending_char:
					// Ending quote.
					// Return the <string-token>.
					++$this->at;
					$this->token_type            = self::TOKEN_STRING;
					$this->token_length          = $this->at - $this->token_starts_at;
					$this->token_value_starts_at = $value_starts_at;
					$this->token_value_length    = $this->at - $value_starts_at - 1;
					return true;

				case "\n":
				case "\f":
				case "\r":
					/*
					 * Newline.
					 *
					 * This is a parse error. Reconsume the current input code point,
					 * create a <bad-string-token>, and return it.
					 *
					 * Unescaped newlines are not allowed in strings. To include a newline,
					 * it must be escaped as \A or the string must end and a new one begin.
					 *
					 * @see https://www.w3.org/TR/css-syntax-3/#consume-string-token
					 */
					$this->token_type            = self::TOKEN_BAD_STRING;
					$this->token_length          = $this->at - $this->token_starts_at;
					$this->token_value_starts_at = $value_starts_at;
					$this->token_value_length    = $this->at - $value_starts_at;
					return true;

				case '\\':
					// U+005C REVERSE SOLIDUS (\)
					// If the next input code point is EOF, do nothing.
					++$this->at;
					if ( $this->at >= $this->length ) {
						// Backslash-EOF: do nothing, just consume the backslash.
						continue 2;
					}

					// Otherwise, if the next input code point is a newline, consume it.
					$next = $this->css[ $this->at ];
					if ( "\n" === $next || "\f" === $next ) {
						++$this->at;
						continue 2;
					} elseif ( "\r" === $next ) {
						++$this->at;
						// Handle \r\n as a single newline.
						if ( $this->at < $this->length && "\n" === $this->css[ $this->at ] ) {
							++$this->at;
						}
						continue 2;
					}

					// Otherwise, (the stream starts with a valid escape) consume an escaped
					// code point (just to advance position, don't store the result).
					$this->decode_escape_at( $this->at, $matched_bytes );
					$this->at += $matched_bytes;
					continue 2;

				default:
					_doing_it_wrong( __METHOD__, 'Unexpected character in string: ' . $char, '1.0.0' );
					break;
			}
		}

		// EOF
		// This is a parse error. Return the <string-token>.
		$this->token_type            = self::TOKEN_STRING;
		$this->token_length          = $this->at - $this->token_starts_at;
		$this->token_value_starts_at = $value_starts_at;
		$this->token_value_length    = $this->at - $value_starts_at;
		return true;
	}

	/**
	 * Consumes a numeric token (number, percentage, dimension).
	 *
	 * Numbers can be integers or decimals, with optional sign and exponent.
	 * They can be followed by % (percentage) or an identifier (dimension).
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-numeric-token
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-number
	 *
	 * @return bool
	 */
	private function consume_numeric(): bool {
		// Consume a number and let number be the result.
		// The type flag defaults to "integer".
		$number_type = 'integer';

		// If the next input code point is U+002B PLUS SIGN (+) or U+002D HYPHEN-MINUS (-),
		// consume it and append it to repr.
		if ( '+' === $this->css[ $this->at ] || '-' === $this->css[ $this->at ] ) {
			++$this->at;
		}

		// While the next input code point is a digit, consume it and append it to repr.
		$digits = strspn( $this->css, '0123456789', $this->at );
		if ( $digits > 0 ) {
			$this->at += $digits;
		}

		// If the next 2 input code points are U+002E FULL STOP (.) followed by a digit, then.
		if (
			$this->at + 1 < $this->length &&
			'.' === $this->css[ $this->at ] &&
			$this->css[ $this->at + 1 ] >= '0' &&
			$this->css[ $this->at + 1 ] <= '9'
		) {
			// Consume them.
			++$this->at;
			// Set type to "number".
			$number_type = 'number';
			// While the next input code point is a digit, consume it and append it to repr.
			$digits = strspn( $this->css, '0123456789', $this->at );
			if ( $digits > 0 ) {
				$this->at += $digits;
			}
		}

		// If the next 2 or 3 input code points are U+0045 LATIN CAPITAL LETTER E (E)
		// or U+0065 LATIN SMALL LETTER E (e), optionally followed by U+002D HYPHEN-MINUS (-)
		// or U+002B PLUS SIGN (+), followed by a digit, then.
		if ( $this->at < $this->length ) {
			$e = $this->css[ $this->at ];
			if ( 'e' === $e || 'E' === $e ) {
				$save_pos = $this->at;
				++$this->at;
				$has_exp = false;

				if ( $this->at < $this->length ) {
					$next = $this->css[ $this->at ];
					if ( ( '+' === $next || '-' === $next ) && $this->at + 1 < $this->length &&
						$this->css[ $this->at + 1 ] >= '0' && $this->css[ $this->at + 1 ] <= '9' ) {
						// Consume them.
						++$this->at;
						$has_exp = true;
					} elseif ( $next >= '0' && $next <= '9' ) {
						$has_exp = true;
					}
				}

				if ( $has_exp ) {
					// Set type to "number".
					$number_type = 'number';
					// While the next input code point is a digit, consume it and append it to repr.
					$digits = strspn( $this->css, '0123456789', $this->at );
					if ( $digits > 0 ) {
						$this->at += $digits;
					}
				} else {
					$this->at = $save_pos;
				}
			}
		}

		/**
		 * This is the end of spec section 4.3.12. Consume a number.
		 * We still have some work to do as specified in section 4.3.3. Consume a numeric token:
		 * https://www.w3.org/TR/css-syntax-3/#consume-numeric-token
		 */

		// If the next 3 input code points would start an ident sequence, then.
		if ( $this->check_if_3_code_points_start_an_ident_sequence( $this->at ) ) {
			// Create a <dimension-token> with the same value and type flag as number,
			// and a unit set initially to the empty string.
			// Consume an ident sequence. Set the <dimension-token>'s unit to the returned value.
			$unit_starts_at = $this->at;
			$this->consume_ident_sequence();
			$this->token_unit      = $this->decode_range( $unit_starts_at, $this->at - $unit_starts_at );
			$this->token_type      = self::TOKEN_DIMENSION;
			$this->token_type_flag = $number_type;
			$this->token_length    = $this->at - $this->token_starts_at;
			return true;
		}

		// Otherwise, if the next input code point is U+0025 PERCENTAGE SIGN (%), consume it.
		// Create a <percentage-token> with the same value as number, and return it.
		// Note: percentage tokens do not have a type flag per spec.
		if ( $this->at < $this->length && '%' === $this->css[ $this->at ] ) {
			++$this->at;
			$this->token_type   = self::TOKEN_PERCENTAGE;
			$this->token_length = $this->at - $this->token_starts_at;
			return true;
		}

		// Otherwise, create a <number-token> with the same value and type flag as number, and return it.
		$this->token_type      = self::TOKEN_NUMBER;
		$this->token_type_flag = $number_type;
		$this->token_length    = $this->at - $this->token_starts_at;
		return true;
	}

	/**
	 * Consumes an ident-like token (function, url, ident).
	 *
	 * After consuming an identifier, checks if it's followed by '(' to determine
	 * if it's a function or url() token, otherwise it's a plain identifier.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-ident-like-token
	 *
	 * @return bool
	 */
	private function consume_ident_like(): bool {
		// Consume an ident sequence, and let string be the result.
		$ident_start = $this->at;
		$decoded     = $this->consume_ident_sequence();
		$string      = $decoded ?? $this->decode_range( $ident_start, $this->at - $ident_start );

		// If string's value is an ASCII case-insensitive match for "url",
		// and the next input code point is U+0028 LEFT PARENTHESIS (().
		if ( 0 === strcasecmp( $string, 'url' ) && $this->at < $this->length && '(' === $this->css[ $this->at ] ) {
			// Consume it.
			++$this->at;

			// While the next two input code points are whitespace, consume the next input code point.
			$ws_len = strspn( $this->css, "\t\n\f\r ", $this->at );

			// If the next one or two input code points are U+0022 QUOTATION MARK ("),
			// U+0027 APOSTROPHE ('), or whitespace followed by U+0022 QUOTATION MARK (")
			// or U+0027 APOSTROPHE (').
			if ( $this->at + $ws_len < $this->length ) {
				$next = $this->css[ $this->at + $ws_len ];
				if ( '"' === $next || "'" === $next ) {
					// then create a <function-token> with its value set to string and return it.
					if ( null !== $decoded ) {
						$this->token_value = $decoded;
					}
					$this->token_type   = self::TOKEN_FUNCTION;
					$this->token_length = $this->at - $this->token_starts_at;
					return true;
				}
			}

			// Otherwise, consume a url token, and return it.
			$this->at += $ws_len;
			return $this->consume_url();
		}

		// Otherwise, if the next input code point is U+0028 LEFT PARENTHESIS (().
		if ( $this->at < $this->length && '(' === $this->css[ $this->at ] ) {
			// Consume it.
			++$this->at;
			// Create a <function-token> with its value set to string and return it.
			if ( null !== $decoded ) {
				$this->token_value = $decoded;
			}
			$this->token_type   = self::TOKEN_FUNCTION;
			$this->token_length = $this->at - $this->token_starts_at;
			return true;
		}

		// Otherwise, create an <ident-token> with its value set to string and return it.
		if ( null !== $decoded ) {
			$this->token_value = $decoded;
		}
		$this->token_type   = self::TOKEN_IDENT;
		$this->token_length = $this->at - $this->token_starts_at;
		return true;
	}

	/**
	 * Consumes a url token.
	 *
	 * URL tokens can contain unquoted URLs with escape sequences but not quotes,
	 * parentheses, or certain control characters. Invalid characters create a
	 * bad-url token.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-url-token
	 *
	 * @return bool
	 */
	private function consume_url(): bool {
		// Initially create a <url-token> with its value set to the empty string.
		// Consume as much whitespace as possible.
		$this->at += strspn( $this->css, "\t\n\f\r ", $this->at );

		$value_starts_at = $this->at;

		// Repeatedly consume the next input code point from the stream.
		while ( $this->at < $this->length ) {
			// Scan runs such as https://example.com/photo.png in native code rather
			// than one PHP iteration per byte. These ASCII bytes need no special
			// handling in an unquoted URL. Quotes, parentheses, whitespace, escapes,
			// and non-ASCII bytes fall through to the rules below.
			// Only the cursor advances; source bytes stay unchanged. This also makes
			// reparsing a long unfinished URL cheaper when another chunk arrives.
			// This set only selects the fast path; it does not validate the URL or
			// reject other bytes, which still reach the CSS token rules below.
			$plain = strspn( $this->css, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._~:/?#[]@!$&*+,;=%', $this->at );
			if ( $plain > 0 ) {
				$this->at += $plain;
				continue;
			}
			// U+0029 RIGHT PARENTHESIS ())
			// Return the <url-token>.
			if ( ')' === $this->css[ $this->at ] ) {
				++$this->at;
				$this->token_type            = self::TOKEN_URL;
				$this->token_length          = $this->at - $this->token_starts_at;
				$this->token_value_starts_at = $value_starts_at;
				$this->token_value_length    = $this->at - $value_starts_at - 1;
				return true;
			}

			// whitespace
			// Consume as much whitespace as possible. If the next input code point is
			// U+0029 RIGHT PARENTHESIS ()) or EOF, consume it and return the <url-token>
			// (if EOF was encountered, this is a parse error); otherwise, consume the
			// remnants of a bad url, create a <bad-url-token>, and return it.
			$ws_len = strspn( $this->css, "\t\n\f\r ", $this->at );
			if ( $ws_len > 0 ) {
				$value_ends_at = $this->at;
				$this->at     += $ws_len;
				// Accept either ) or EOF after whitespace.
				if ( $this->at >= $this->length ) {
					// EOF is a parse error, but we return the <url-token> anyway.
					$this->token_type            = self::TOKEN_URL;
					$this->token_length          = $this->at - $this->token_starts_at;
					$this->token_value_starts_at = $value_starts_at;
					$this->token_value_length    = $value_ends_at - $value_starts_at;
					return true;
				}

				if ( ')' === $this->css[ $this->at ] ) {
					// Skip the closing parenthesis and return the <url-token>.
					++$this->at;
					$this->token_type            = self::TOKEN_URL;
					$this->token_length          = $this->at - $this->token_starts_at;
					$this->token_value_starts_at = $value_starts_at;
					$this->token_value_length    = $value_ends_at - $value_starts_at;
					return true;
				}

				return $this->consume_remnants_of_bad_url();
			}

			// These codepoints trigger a parse error.
			$byte = ord( $this->css[ $this->at ] );
			if (
				'"' === $this->css[ $this->at ] ||
				"'" === $this->css[ $this->at ] ||
				'(' === $this->css[ $this->at ] ||

				// NUL becomes U+FFFD during CSS preprocessing, so it is valid here.
				// Keep its source byte for offsets; decode_range() replaces it when
				// the caller reads the value. Other non-printable controls stay invalid.
				( $byte >= 0x01 && $byte <= 0x08 ) ||

				// Line Tabulation.
				0x0B === $byte ||

				// Control characters.
				( $byte >= 0x000E && $byte <= 0x001F ) ||

				// Delete.
				0x7F === $byte
			) {
				// Consume the remnants of a bad url,
				// create a <bad-url-token>, and return it.
				return $this->consume_remnants_of_bad_url();
			}

			// U+005C REVERSE SOLIDUS (\)
			// If the stream starts with a valid escape, consume an escaped code point.
			if ( '\\' === $this->css[ $this->at ] ) {
				if ( $this->is_valid_escape( $this->at ) ) {
					++$this->at;
					$this->decode_escape_at( $this->at, $matched_bytes );
					$this->at += $matched_bytes;
					continue;
				}
				// Otherwise, this is a parse error. Consume the remnants of a bad url,
				// create a <bad-url-token>, and return it.
				return $this->consume_remnants_of_bad_url();
			}

			$at             = $this->at;
			$invalid_length = 0;
			if ( 1 !== _wp_scan_utf8( $this->css, $at, $invalid_length, null, 1 ) ) {
				/**
				 * Trouble ahead!
				 * Bytes at $at are not a valid UTF-8 sequence.
				 *
				 * We'll move forward by $invalid_length bytes and continue processing.
				 * Later on, during the string decoding, we'll replace the invalid bytes with U+FFFD
				 * via maximal subpart”replacement.
				 */
				$this->at += $invalid_length;
			} else {
				$this->at = $at;
			}
		}

		// EOF
		// This is a parse error. Return the <url-token>.
		$this->token_type            = self::TOKEN_URL;
		$this->token_length          = $this->at - $this->token_starts_at;
		$this->token_value_starts_at = $value_starts_at;
		$this->token_value_length    = $this->at - $value_starts_at;
		return true;
	}

	/**
	 * Finishes a bad url token by consuming remnants.
	 *
	 * When an invalid character is encountered in a URL, we must consume
	 * the remainder of the URL up to the closing ) or EOF.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-remnants-of-bad-url
	 *
	 * @return bool
	 */
	private function consume_remnants_of_bad_url(): bool {
		while ( $this->at < $this->length ) {
			$this->at += strcspn( $this->css, ')\\', $this->at );

			if ( $this->at >= $this->length ) {
				break;
			}

			if ( '\\' === $this->css[ $this->at ] ) {
				++$this->at;
				if ( $this->is_valid_escape( $this->at - 1 ) ) {
					$this->decode_escape_at( $this->at, $matched_bytes );
					$this->at += $matched_bytes;
					continue;
				}
			} elseif ( ')' === $this->css[ $this->at ] ) {
				++$this->at;
				break;
			}
		}

		$this->token_type   = self::TOKEN_BAD_URL;
		$this->token_length = $this->at - $this->token_starts_at;
		return true;
	}

	/**
	 * Consumes an identifier sequence.
	 *
	 * Identifiers can contain letters, digits, hyphens, underscores, non-ASCII
	 * characters, and escape sequences. Null bytes are replaced with U+FFFD.
	 *
	 * Returns the decoded identifier string if escapes were encountered,
	 * or null if no decoding was needed (can use raw substring).
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-name
	 */
	private function consume_ident_sequence() {
		while ( $this->at < $this->length ) {
			// Scan ASCII name characters, as in margin-top or item_2, in native
			// code rather than one PHP iteration per byte. Letters, digits, "-",
			// and "_" can continue a name; deciding whether an identifier may
			// start here is a separate check, not the purpose of this byte list.
			// Non-ASCII bytes, NULL, and escapes use the rules below. Advancing
			// only the cursor preserves the source spelling for later decoding.
			$plain = strspn( $this->css, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_', $this->at );
			if ( $plain > 0 ) {
				$this->at += $plain;
				continue;
			}
			$codepoint_bytes = $this->consume_ident_codepoint( $this->at );
			if ( $codepoint_bytes > 0 ) {
				$this->at += $codepoint_bytes;
				continue;
			}

			if ( $this->is_valid_escape( $this->at ) ) {
				++$this->at;

				$this->decode_escape_at( $this->at, $matched_bytes );
				$this->at += $matched_bytes;
				continue;
			}

			break;
		}
	}

	/**
	 * Ident-start code point
	 *     A letter, a non-ASCII code point, or U+005F LOW LINE (_).
	 *
	 * Ident code point
	 *     An ident-start code point, a digit, or U+002D HYPHEN-MINUS (-).
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#ident-start-code-point
	 * @return int The number of bytes consumed.
	 */
	private function consume_ident_codepoint( $at ): int {
		// ident code points.
		if ( ( $this->css[ $at ] >= '0' && $this->css[ $at ] <= '9' ) ||
			'-' === $this->css[ $at ] ) {
			return 1;
		}

		return $this->consume_ident_start_codepoint( $at );
	}


	/**
	 * Ident-start code point
	 *     A letter, a non-ASCII code point, or U+005F LOW LINE (_).
	 *
	 * Ident code point
	 *     An ident-start code point, a digit, or U+002D HYPHEN-MINUS (-).
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#ident-start-code-point
	 * @return int The number of bytes consumed.
	 */
	private function consume_ident_start_codepoint( $at ): int {
		if ( $at >= $this->length ) {
			return 0;
		}

		// ASCII codepoints.
		if ( ( $this->css[ $at ] >= 'A' && $this->css[ $at ] <= 'Z' ) ||
			( $this->css[ $at ] >= 'a' && $this->css[ $at ] <= 'z' ) ||
			'_' === $this->css[ $at ] ) {
			return 1;
		}

		// Special case for null bytes – they are replaced with U+FFFD during preprocessing.
		if ( "\x00" === $this->css[ $at ] ) {
			return 1;
		}

		$new_at         = $at;
		$invalid_length = 0;
		if ( 1 !== _wp_scan_utf8( $this->css, $new_at, $invalid_length, null, 1 ) ) {
			/**
			 * Trouble ahead!
			 * Bytes at $at are not a valid UTF-8 sequence.
			 *
			 * We'll move forward by $invalid_length bytes and continue processing.
			 * Later on, during the string decoding, we'll replace the invalid bytes with U+FFFD
			 * via maximal subpart”replacement.
			 */
			return $invalid_length;
		}

		$codepoint_byte_length = $new_at - $at;
		$codepoint             = utf8_ord( substr( $this->css, $at, $codepoint_byte_length ) );
		if ( null !== $codepoint && $codepoint >= 0x80 ) {
			return $codepoint_byte_length;
		}
		return 0;
	}

	/**
	 * Decodes and normalizes ident-like or string CSS values from a byte range.
	 *
	 * For example:
	 * ┌──────────────┬────────┐
	 * │ Input        │ Output │
	 * ├──────────────┼────────┤
	 * │ 'xyz'        │ 'xyz'  │
	 * │ '\x\y\z'     │ 'xyz'  │
	 * │ 'x\79z'      │ 'xyz'  │
	 * │ 'x\000079 z' │ 'xyz'  │
	 * │ 'a\r\nb'     │ 'a\nb' │
	 * │ 'a\0b'       │ 'a�b'  │
	 * └──────────────┴────────┘
	 *
	 * @param int  $start           Start byte offset.
	 * @param int  $length          Length of the substring to decode.
	 * @param bool $string_escapes  Optional, default false. When true, apply additional escape
	 *                              rules that apply only to string tokens.
	 * @return string Decoded and normalized string.
	 */
	private function decode_range( int $start, int $length, bool $string_escapes = false ): string {
		// Fast path: check if any processing is needed.
		$slice         = wp_scrub_utf8( substr( $this->css, $start, $length ) );
		$special_chars = "\\\r\f\x00";
		if ( false === strpbrk( $slice, $special_chars ) ) {
			// No special chars - return raw substring (almost zero allocations).
			return $slice;
		}

		// Slow path: build decoded string (one allocation).
		$decoded = '';
		$at      = $start;
		$end     = $start + $length;

		while ( $at < $end ) {
			// Find next special character within the token boundary.
			$normal_len = strcspn( $this->css, $special_chars, $at, $end - $at );
			if ( $normal_len > 0 ) {
				$decoded .= wp_scrub_utf8( substr( $this->css, $at, $normal_len ) );
				$at      += $normal_len;
			}

			if ( $at >= $end ) {
				break;
			}

			$char = $this->css[ $at ];

			// Handle escapes.
			if ( '\\' === $char ) {
				/**
				 * String tokens have special escape rules:
				 * - 0x5C (backslash) at EOF: consume the backslash, produce no value.
				 * - 0x5C (backslash) followed by 0x0A (LF), 0x0C (FF), or 0x0D (CR):
				 *   consume both characters as a line continuation, produce no value.
				 * - 0x5C (backslash) followed by 0x0D 0x0A (CRLF):
				 *   consume all three characters as a line continuation, produce no value.
				 * These must be checked before the general escape path.
				 *
				 * @see https://www.w3.org/TR/css-syntax-3/#consume-string-token
				 */
				if ( $string_escapes ) {
					if ( $at + 1 >= $end ) {
						// 0x5C at EOF: consume the backslash and stop.
						++$at;
						continue;
					}
					$next = $this->css[ $at + 1 ];
					if ( "\n" === $next || "\f" === $next ) {
						// 0x5C followed by 0x0A (LF) or 0x0C (FF): line continuation.
						$at += 2;
						continue;
					}
					if ( "\r" === $next ) {
						// 0x5C followed by 0x0D (CR): line continuation; 0x0D 0x0A counts as one newline.
						$at += 2;
						if ( $at < $end && "\n" === $this->css[ $at ] ) {
							++$at;
						}
						continue;
					}
				}

				if ( $this->is_valid_escape( $at ) ) {
					++$at;
					$decoded .= $this->decode_escape_at( $at, $bytes_consumed );
					$at      += $bytes_consumed;
					continue;
				}
				// Invalid escape - consume the backslash and keep going.
				$decoded .= '\\';
				++$at;
				continue;
			}

			// CSS normalization: \r\n, \r, and \f all become \n.
			if ( "\r" === $char ) {
				$decoded .= "\n";
				++$at;
				// Handle \r\n as single newline.
				if ( $at < $end && "\n" === $this->css[ $at ] ) {
					++$at;
				}
				continue;
			}

			if ( "\f" === $char ) {
				$decoded .= "\n";
				++$at;
				continue;
			}

			// Null bytes become U+FFFD.
			if ( "\x00" === $char ) {
				$decoded .= "\u{FFFD}";
				++$at;
				continue;
			}
		}

		return $decoded;
	}

	/**
	 * Decodes an escape sequence starting at the given offset without
	 * modifying $this->at.
	 *
	 * Escape sequences are backslash followed by 1-6 hex digits (with optional
	 * trailing whitespace) or any other character. Invalid code points are
	 * replaced with U+FFFD.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#consume-escaped-code-point
	 *
	 * @param int $offset Byte offset (should point to the character after the backslash).
	 * @param int &$bytes_consumed Output parameter: number of bytes consumed.
	 * @return string The decoded character(s).
	 */
	private function decode_escape_at( int $offset, &$bytes_consumed ): string {
		// This method assumes the U+005C REVERSE SOLIDUS (\) has already been consumed
		// and the next input code point has already been verified to be part of a valid
		// escape sequence.
		$at = $offset;

		// EOF.
		if ( $at >= $this->length ) {
			// This is a parse error. Return U+FFFD REPLACEMENT CHARACTER (�).
			$bytes_consumed = 0;
			return "\u{FFFD}";
		}

		// Hex digits (CSS spec allows at most 6).
		$hex_len = strspn( $this->css, '0123456789ABCDEFabcdef', $at, 6 );
		if ( $hex_len > 0 ) {
			$hex = substr( $this->css, $at, $hex_len );
			$at += $hex_len;

			// If the next input code point is whitespace, consume it as well.
			if ( $at < $this->length ) {
				$next = $this->css[ $at ];
				if ( "\t" === $next || "\n" === $next || "\f" === $next || ' ' === $next ) {
					++$at;
				} elseif ( "\r" === $next ) {
					++$at;
					// Handle \r\n as a single whitespace – the preprocessing phase would replace \r\n with \n.
					if ( $at < $this->length && "\n" === $this->css[ $at ] ) {
						++$at;
					}
				}
			}

			$bytes_consumed = $at - $offset;
			// Convert the hex digits to a UTF-8 string.
			return codepoint_to_utf8_bytes( hexdec( $hex ) );
		}

		// Anything else.
		// Return the current input code point.
		// Null bytes are replaced with U+FFFD during preprocessing.
		if ( "\x00" === $this->css[ $at ] ) {
			$bytes_consumed = 1;
			return "\u{FFFD}";
		}

		$new_at         = $at;
		$invalid_length = 0;
		if ( 1 !== _wp_scan_utf8( $this->css, $new_at, $invalid_length, null, 1 ) ) {
			// Bytes at $at are not a valid UTF-8 sequence. Consume the maximal
			// invalid subpart and return U+FFFD per the CSS spec.
			$bytes_consumed = $invalid_length;
			return "\u{FFFD}";
		}

		$bytes_consumed = $new_at - $at;
		return substr( $this->css, $at, $bytes_consumed );
	}

	/**
	 * Checks if current position starts a valid escape sequence.
	 *
	 * A valid escape is a backslash not followed by a newline or EOF.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#starts-with-a-valid-escape
	 *
	 * @param int $offset Byte offset.
	 * @return bool
	 */
	private function is_valid_escape( int $offset ): bool {
		// If the first code point is not U+005C REVERSE SOLIDUS (\), return false.
		if ( $offset >= $this->length || '\\' !== $this->css[ $offset ] ) {
			return false;
		}
		// Otherwise, if the second code point is a newline, return false.
		if ( $offset + 1 >= $this->length ) {
			// Second code point is EOF - this is a valid escape per spec (weird!)
			// Are we sure we're interpreting the spec correctly?
			return true;
		}

		// Otherwise, if the second code point is not a newline, return true.
		return (
			"\n" !== $this->css[ $offset + 1 ] &&

			// Form feed is normalized to newline during preprocessing.
			"\f" !== $this->css[ $offset + 1 ] &&

			// Carriage return is normalized to newline during preprocessing.
			"\r" !== $this->css[ $offset + 1 ]

			// We don't need to check for \r\n separately here. The \r check alone covers
			// that scenario.
		);
	}

	/**
	 * Checks if the next 3 code points would start a number.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#starts-with-a-number
	 *
	 * @return bool
	 */
	private function would_next_3_code_points_start_a_number(): bool {
		if ( $this->at >= $this->length ) {
			return false;
		}

		// Look at the first code point.

		// U+002B PLUS SIGN (+) or U+002D HYPHEN-MINUS (-).
		if ( '+' === $this->css[ $this->at ] || '-' === $this->css[ $this->at ] ) {
			if ( $this->at + 1 >= $this->length ) {
				return false;
			}
			// If the second code point is a digit, return true.
			if ( $this->css[ $this->at + 1 ] >= '0' && $this->css[ $this->at + 1 ] <= '9' ) {
				return true;
			}
			// Otherwise, the second code point must be a full stop (.) and the third code point must be a digit.
			if ( '.' === $this->css[ $this->at + 1 ] && $this->at + 2 < $this->length ) {
				return $this->css[ $this->at + 2 ] >= '0' && $this->css[ $this->at + 2 ] <= '9';
			}

			// Otherwise, return false.
			return false;
		}

		// U+002E FULL STOP (.).
		if ( '.' === $this->css[ $this->at ] ) {
			if ( $this->at + 1 >= $this->length ) {
				return false;
			}
			return $this->css[ $this->at + 1 ] >= '0' && $this->css[ $this->at + 1 ] <= '9';
		}

		// Digit.
		if ( $this->css[ $this->at ] >= '0' && $this->css[ $this->at ] <= '9' ) {
			return true;
		}

		// Anything else – return false.
		return false;
	}

	/**
	 * Checks if three code points would start an identifier sequence.
	 *
	 * This implements the CSS spec's "Check if three code points would start an ident sequence"
	 * algorithm, which checks the code point at $offset and the following two code points.
	 *
	 * NOTE: "Three code points" means three Unicode code points, not three bytes.
	 * Multi-byte UTF-8 sequences count as single code points.
	 *
	 * @see https://www.w3.org/TR/css-syntax-3/#would-start-an-identifier
	 *
	 * @param int $offset Byte offset of the first code point to check.
	 * @return bool
	 */
	private function check_if_3_code_points_start_an_ident_sequence( int $offset ): bool {
		if ( $offset >= $this->length ) {
			return false;
		}

		if ( '-' === $this->css[ $offset ] ) {
			// If the second code point is a U+002D HYPHEN-MINUS (-), return true.
			// e.g. --custom-property.
			if ( $offset + 1 < $this->length && '-' === $this->css[ $offset + 1 ] ) {
				return true;
			}
			// Otherwise, check if the second code point is an ident-START code point or valid escape.
			// Note: After a hyphen, only ident-START code points are valid, NOT digits or hyphens.
			++$offset;
		}

		return $this->consume_ident_start_codepoint( $offset ) > 0 || $this->is_valid_escape( $offset );
	}
}
