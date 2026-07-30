<?php

namespace WordPress\DataLiberation\Shortcode;

/**
 * Tokenizes and minimally rewrites shortcode-like markup.
 *
 * This processor follows the pull-based, single-pass model of
 * WP_HTML_Tag_Processor. It walks a source string as a stream of `#shortcode`
 * and `#text` tokens, records byte offsets instead of building a document
 * tree, and defers lexical updates until get_updated_text() is called.
 *
 * It is deliberately a tokenizer, not a shortcode renderer. It does not
 * require registered WordPress shortcode callbacks and does not attempt to
 * reproduce do_shortcode(). Opening and closing tokens are reported
 * independently, which allows consumers to handle same-name nesting and
 * builder-specific structures without the limitations of the regular
 * expression used by WordPress Core.
 *
 * Attribute parsing is tolerant of builder data stored inside quoted values,
 * including CSS, HTML, JSON, and square brackets. Getters return the exact
 * source bytes between the attribute delimiters. Updates replace only the
 * corresponding lexical span and preserve all unrelated whitespace, quotes,
 * text, and markup.
 *
 * Square-bracket syntax is ambiguous without information from the surrounding
 * storage format. For example, `[hidden]` may be a shortcode or a CSS attribute
 * selector. Callers should first isolate a shortcode-bearing region and, where
 * possible, query for registered tag names or known builder prefixes.
 */
class ShortcodeProcessor {

	const TOKEN_SHORTCODE = '#shortcode';
	const TOKEN_TEXT      = '#text';

	/**
	 * Source text being processed.
	 *
	 * @var string
	 */
	private $text;

	/**
	 * Source text length in bytes.
	 *
	 * @var int
	 */
	private $length;

	/**
	 * Byte offset at which the next token scan begins.
	 *
	 * @var int
	 */
	private $at = 0;

	/**
	 * Current token type.
	 *
	 * @var string|null
	 */
	private $token_type = null;

	/**
	 * Byte offset at which the current token starts.
	 *
	 * @var int|null
	 */
	private $token_starts_at = null;

	/**
	 * Current token length in bytes.
	 *
	 * @var int|null
	 */
	private $token_length = null;

	/**
	 * Byte offset at which the current shortcode tag name starts.
	 *
	 * @var int|null
	 */
	private $tag_name_starts_at = null;

	/**
	 * Current shortcode tag name length in bytes.
	 *
	 * @var int|null
	 */
	private $tag_name_length = null;

	/**
	 * Whether the current shortcode token is a closing token.
	 *
	 * @var bool
	 */
	private $is_closing_tag = false;

	/**
	 * Whether the current shortcode token has a self-closing solidus.
	 *
	 * @var bool
	 */
	private $has_self_closing_flag = false;

	/**
	 * Whether the current shortcode token has two opening square brackets.
	 *
	 * @var bool
	 */
	private $has_escaped_opening_bracket = false;

	/**
	 * Whether the current shortcode token has two closing square brackets.
	 *
	 * @var bool
	 */
	private $has_escaped_closing_bracket = false;

	/**
	 * Parsed attributes on the current shortcode opener.
	 *
	 * Each attribute records source byte spans instead of allocating a
	 * normalized copy of the complete token.
	 *
	 * @var array[]
	 * @phpstan-var array<int, array{
	 *     name: string|null,
	 *     comparable_name: string|null,
	 *     start: int,
	 *     length: int,
	 *     value_start: int,
	 *     value_length: int,
	 *     quote: string|null
	 * }>
	 */
	private $attributes = array();

	/**
	 * Index of the current attribute.
	 *
	 * @var int
	 */
	private $attribute_index = -1;

	/**
	 * Lexical replacements keyed by the token and field that owns the update.
	 *
	 * @var array[]
	 * @phpstan-var array<string, array{
	 *     start: int,
	 *     length: int,
	 *     text: string,
	 *     value?: string
	 * }>
	 */
	private $lexical_updates = array();

	/**
	 * Constructor.
	 *
	 * @param string $text Text containing possible shortcode markup.
	 */
	public function __construct( string $text ) {
		$this->text   = $text;
		$this->length = strlen( $text );
	}

	/**
	 * Finds the next shortcode matching an optional query.
	 *
	 * A string query matches an exact, case-sensitive shortcode tag name.
	 * An array query may contain:
	 *
	 * - `tag_name`: exact, case-sensitive tag name.
	 * - `tag_prefix`: case-sensitive tag-name prefix.
	 * - `tag_closers`: `visit` (default) or `skip`.
	 * - `match_offset`: one-based index of the matching token to return.
	 * - `escaped`: whether the token must use the complete `[[tag]]` form.
	 *
	 * @param array|string|null $query Optional shortcode query.
	 * @return bool Whether a matching shortcode token was found.
	 */
	public function next_shortcode( $query = null ): bool {
		$tag_name     = null;
		$tag_prefix   = null;
		$tag_closers  = 'visit';
		$match_offset = 1;
		$escaped      = null;

		if ( is_string( $query ) ) {
			$tag_name = $query;
		} elseif ( is_array( $query ) ) {
			$tag_name     = isset( $query['tag_name'] ) ? (string) $query['tag_name'] : null;
			$tag_prefix   = isset( $query['tag_prefix'] ) ? (string) $query['tag_prefix'] : null;
			$tag_closers  = isset( $query['tag_closers'] ) ? (string) $query['tag_closers'] : 'visit';
			$match_offset = isset( $query['match_offset'] ) ? max( 1, (int) $query['match_offset'] ) : 1;
			$escaped      = isset( $query['escaped'] ) ? (bool) $query['escaped'] : null;
		}

		$matches = 0;
		while ( $this->next_token() ) {
			if ( self::TOKEN_SHORTCODE !== $this->token_type ) {
				continue;
			}

			if ( 'skip' === $tag_closers && $this->is_closing_tag ) {
				continue;
			}

			$current_tag = $this->get_tag();
			if ( null !== $tag_name && $current_tag !== $tag_name ) {
				continue;
			}

			if (
				null !== $tag_prefix &&
				0 !== strncmp( $current_tag, $tag_prefix, strlen( $tag_prefix ) )
			) {
				continue;
			}

			if ( null !== $escaped && $this->is_escaped() !== $escaped ) {
				continue;
			}

			++$matches;
			if ( $matches >= $match_offset ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Advances to the next shortcode or text token.
	 *
	 * The processor searches forward for a complete shortcode candidate.
	 * Invalid or truncated candidates remain part of a text token.
	 *
	 * @return bool Whether another token was found.
	 */
	public function next_token(): bool {
		$this->after_token();

		if ( $this->at >= $this->length ) {
			return false;
		}

		$scan_at = $this->at;
		while ( $scan_at < $this->length ) {
			$candidate_at = strpos( $this->text, '[', $scan_at );
			if ( false === $candidate_at ) {
				break;
			}

			$shortcode = $this->scan_shortcode_at( $candidate_at );
			if ( false === $shortcode ) {
				$scan_at = $candidate_at + 1;
				continue;
			}

			if ( $candidate_at > $this->at ) {
				$this->token_type      = self::TOKEN_TEXT;
				$this->token_starts_at = $this->at;
				$this->token_length    = $candidate_at - $this->at;
				$this->at              = $candidate_at;

				return true;
			}

			$this->set_shortcode_token( $shortcode );
			$this->at = $candidate_at + $shortcode['length'];

			return true;
		}

		$this->token_type      = self::TOKEN_TEXT;
		$this->token_starts_at = $this->at;
		$this->token_length    = $this->length - $this->at;
		$this->at              = $this->length;

		return true;
	}

	/**
	 * Returns the current token type.
	 *
	 * @return string|null `#shortcode`, `#text`, or null when not on a token.
	 */
	public function get_token_type(): ?string {
		return $this->token_type;
	}

	/**
	 * Returns the exact source bytes of the current token.
	 *
	 * @return string|null Current token text, or null when not on a token.
	 */
	public function get_token_text(): ?string {
		if ( null === $this->token_starts_at || null === $this->token_length ) {
			return null;
		}

		return substr( $this->text, $this->token_starts_at, $this->token_length );
	}

	/**
	 * Returns the current shortcode tag name exactly as written.
	 *
	 * Shortcode tag names are case-sensitive.
	 *
	 * @return string|null Current shortcode tag name, or null on a text token.
	 */
	public function get_tag(): ?string {
		if (
			self::TOKEN_SHORTCODE !== $this->token_type ||
			null === $this->tag_name_starts_at ||
			null === $this->tag_name_length
		) {
			return null;
		}

		return substr( $this->text, $this->tag_name_starts_at, $this->tag_name_length );
	}

	/**
	 * Indicates whether the current shortcode token is a closing token.
	 *
	 * @return bool Whether the current token is a shortcode closer.
	 */
	public function is_tag_closer(): bool {
		return self::TOKEN_SHORTCODE === $this->token_type && $this->is_closing_tag;
	}

	/**
	 * Indicates whether the current shortcode token has a self-closing flag.
	 *
	 * @return bool Whether the current token is self-closing.
	 */
	public function has_self_closing_flag(): bool {
		return (
			self::TOKEN_SHORTCODE === $this->token_type &&
			$this->has_self_closing_flag
		);
	}

	/**
	 * Indicates whether the current shortcode uses complete `[[tag]]` escaping.
	 *
	 * @return bool Whether both the opening and closing brackets are doubled.
	 */
	public function is_escaped(): bool {
		return (
			self::TOKEN_SHORTCODE === $this->token_type &&
			$this->has_escaped_opening_bracket &&
			$this->has_escaped_closing_bracket
		);
	}

	/**
	 * Returns the current token's byte offset.
	 *
	 * @return int|null Current token start, or null when not on a token.
	 */
	public function get_token_start(): ?int {
		return $this->token_starts_at;
	}

	/**
	 * Returns the current token's byte length.
	 *
	 * @return int|null Current token length, or null when not on a token.
	 */
	public function get_token_length(): ?int {
		return $this->token_length;
	}

	/**
	 * Advances to the next attribute on the current shortcode opener.
	 *
	 * Named and positional attributes are both reported. Positional attributes
	 * return null from get_attribute_name().
	 *
	 * @return bool Whether another attribute was found.
	 */
	public function next_attribute(): bool {
		if (
			self::TOKEN_SHORTCODE !== $this->token_type ||
			$this->is_closing_tag
		) {
			return false;
		}

		if ( $this->attribute_index + 1 >= count( $this->attributes ) ) {
			return false;
		}

		++$this->attribute_index;
		return true;
	}

	/**
	 * Returns the current attribute name exactly as written.
	 *
	 * @return string|null Attribute name, or null for positional attributes and
	 *                     when not positioned on an attribute.
	 */
	public function get_attribute_name(): ?string {
		$attribute = $this->get_current_attribute();
		return null === $attribute ? null : $attribute['name'];
	}

	/**
	 * Returns the current attribute value without surrounding quotes.
	 *
	 * The value is returned as exact source bytes. Unlike shortcode_parse_atts(),
	 * this method does not call stripcslashes() or normalize whitespace.
	 *
	 * @return string|null Current attribute value.
	 */
	public function get_attribute_value(): ?string {
		$attribute = $this->get_current_attribute();
		if ( null === $attribute ) {
			return null;
		}

		$update_key = $this->get_attribute_update_key( $this->attribute_index );
		if ( isset( $this->lexical_updates[ $update_key ]['value'] ) ) {
			return $this->lexical_updates[ $update_key ]['value'];
		}

		return substr(
			$this->text,
			$attribute['value_start'],
			$attribute['value_length']
		);
	}

	/**
	 * Returns the quote delimiting the current attribute value.
	 *
	 * @return string|null Single quote, double quote, or null for an unquoted
	 *                     value and when not positioned on an attribute.
	 */
	public function get_attribute_quote(): ?string {
		$attribute = $this->get_current_attribute();
		return null === $attribute ? null : $attribute['quote'];
	}

	/**
	 * Returns the current attribute's byte offset.
	 *
	 * @return int|null Current attribute start.
	 */
	public function get_attribute_start(): ?int {
		$attribute = $this->get_current_attribute();
		return null === $attribute ? null : $attribute['start'];
	}

	/**
	 * Returns the current attribute's byte length.
	 *
	 * @return int|null Current attribute length.
	 */
	public function get_attribute_length(): ?int {
		$attribute = $this->get_current_attribute();
		return null === $attribute ? null : $attribute['length'];
	}

	/**
	 * Returns the last value of a named shortcode attribute.
	 *
	 * Attribute names are compared ASCII-case-insensitively, matching
	 * shortcode_parse_atts(). When an attribute is repeated, the last value
	 * wins.
	 *
	 * @param string $name Attribute name.
	 * @return string|null Attribute value, or null if not present.
	 */
	public function get_attribute( string $name ): ?string {
		if (
			self::TOKEN_SHORTCODE !== $this->token_type ||
			$this->is_closing_tag
		) {
			return null;
		}

		$comparable_name = strtolower( $name );
		for ( $i = count( $this->attributes ) - 1; $i >= 0; --$i ) {
			if ( $this->attributes[ $i ]['comparable_name'] !== $comparable_name ) {
				continue;
			}

			$update_key = $this->get_attribute_update_key( $i );
			if ( isset( $this->lexical_updates[ $update_key ]['value'] ) ) {
				return $this->lexical_updates[ $update_key ]['value'];
			}

			return substr(
				$this->text,
				$this->attributes[ $i ]['value_start'],
				$this->attributes[ $i ]['value_length']
			);
		}

		return null;
	}

	/**
	 * Returns unique lowercase attribute names matching a prefix.
	 *
	 * @param string $prefix Attribute-name prefix.
	 * @return array|null Matching names, or null when not on a shortcode opener.
	 */
	public function get_attribute_names_with_prefix( string $prefix ): ?array {
		if (
			self::TOKEN_SHORTCODE !== $this->token_type ||
			$this->is_closing_tag
		) {
			return null;
		}

		$prefix  = strtolower( $prefix );
		$matches = array();
		foreach ( $this->attributes as $attribute ) {
			$name = $attribute['comparable_name'];
			if (
				null !== $name &&
				0 === strncmp( $name, $prefix, strlen( $prefix ) )
			) {
				$matches[ $name ] = true;
			}
		}

		return array_keys( $matches );
	}

	/**
	 * Replaces the current attribute value with minimal lexical changes.
	 *
	 * Existing quotes are retained when possible. An unquoted value gains
	 * quotes only when the replacement cannot be represented unquoted. The
	 * method refuses values containing both quote delimiters when quoting is
	 * required, because WordPress shortcode syntax has no reliable quote escape.
	 *
	 * @param string $value Replacement attribute value.
	 * @return bool Whether the update was enqueued.
	 */
	public function set_attribute_value( string $value ): bool {
		$attribute = $this->get_current_attribute();
		if ( null === $attribute ) {
			return false;
		}

		$start  = $attribute['value_start'];
		$length = $attribute['value_length'];
		$text   = $value;
		$quote  = $attribute['quote'];

		if ( null !== $quote ) {
			if ( false !== strpos( $value, $quote ) ) {
				$alternate_quote = '"' === $quote ? "'" : '"';
				if ( false !== strpos( $value, $alternate_quote ) ) {
					return false;
				}

				$start  = $attribute['value_start'] - 1;
				$length = $attribute['value_length'] + 2;
				$text   = $alternate_quote . $value . $alternate_quote;
			}
		} elseif ( ! $this->can_use_unquoted_attribute_value( $value ) ) {
			if ( false === strpos( $value, '"' ) ) {
				$text = '"' . $value . '"';
			} elseif ( false === strpos( $value, "'" ) ) {
				$text = "'" . $value . "'";
			} else {
				return false;
			}
		}

		$this->lexical_updates[ $this->get_attribute_update_key( $this->attribute_index ) ] = array(
			'start'  => $start,
			'length' => $length,
			'text'   => $text,
			'value'  => $value,
		);

		return true;
	}

	/**
	 * Returns the exact text represented by the current text token.
	 *
	 * @return string|null Current text, or null on a shortcode token.
	 */
	public function get_modifiable_text(): ?string {
		if ( self::TOKEN_TEXT !== $this->token_type ) {
			return null;
		}

		$update_key = $this->get_text_update_key();
		if ( isset( $this->lexical_updates[ $update_key ] ) ) {
			return $this->lexical_updates[ $update_key ]['text'];
		}

		return $this->get_token_text();
	}

	/**
	 * Replaces the current text token without applying HTML escaping.
	 *
	 * Text regions may contain CSS, HTML, block markup, another structured
	 * language, or ordinary prose. The caller owns that nested grammar, so this
	 * method records the supplied bytes exactly.
	 *
	 * @param string $text Replacement text.
	 * @return bool Whether the update was enqueued.
	 */
	public function set_modifiable_text( string $text ): bool {
		if (
			self::TOKEN_TEXT !== $this->token_type ||
			null === $this->token_starts_at ||
			null === $this->token_length
		) {
			return false;
		}

		$this->lexical_updates[ $this->get_text_update_key() ] = array(
			'start'  => $this->token_starts_at,
			'length' => $this->token_length,
			'text'   => $text,
		);

		return true;
	}

	/**
	 * Returns the source text with all enqueued lexical updates applied.
	 *
	 * @return string Updated text.
	 */
	public function get_updated_text(): string {
		if ( empty( $this->lexical_updates ) ) {
			return $this->text;
		}

		$updates = array_values( $this->lexical_updates );
		usort(
			$updates,
			static function ( array $a, array $b ): int {
				return $a['start'] - $b['start'];
			}
		);

		$output               = '';
		$bytes_already_copied = 0;
		foreach ( $updates as $update ) {
			if ( $update['start'] < $bytes_already_copied ) {
				continue;
			}

			$output .= substr(
				$this->text,
				$bytes_already_copied,
				$update['start'] - $bytes_already_copied
			);
			$output .= $update['text'];

			$bytes_already_copied = $update['start'] + $update['length'];
		}

		$output .= substr( $this->text, $bytes_already_copied );

		return $output;
	}

	/**
	 * Returns the updated source text.
	 *
	 * @return string Updated text.
	 */
	public function __toString(): string {
		return $this->get_updated_text();
	}

	/**
	 * Scans a shortcode candidate at a given byte offset.
	 *
	 * @param int $start Candidate start offset.
	 * @return array|false Parsed token metadata, or false for plain text.
	 * @phpstan-return array{
	 *     start: int,
	 *     length: int,
	 *     tag_name_start: int,
	 *     tag_name_length: int,
	 *     is_closing: bool,
	 *     self_closing: bool,
	 *     escaped_opening: bool,
	 *     escaped_closing: bool,
	 *     attributes: array
	 * }|false
	 */
	private function scan_shortcode_at( int $start ) {
		$at = $start + 1;
		if ( $at >= $this->length ) {
			return false;
		}

		$escaped_opening = false;
		if ( '[' === $this->text[ $at ] ) {
			$escaped_opening = true;
			++$at;
		}

		$is_closing = false;
		if ( $at < $this->length && '/' === $this->text[ $at ] ) {
			$is_closing = true;
			++$at;
		}

		$tag_name_start = $at;
		while (
			$at < $this->length &&
			$this->is_shortcode_tag_name_byte( $this->text[ $at ] )
		) {
			++$at;
		}

		$tag_name_length = $at - $tag_name_start;
		if ( 0 === $tag_name_length ) {
			return false;
		}

		if (
			$at < $this->length &&
			! $this->is_shortcode_whitespace_at( $at ) &&
			'"' !== $this->text[ $at ] &&
			"'" !== $this->text[ $at ] &&
			'/' !== $this->text[ $at ] &&
			']' !== $this->text[ $at ]
		) {
			return false;
		}

		if ( $is_closing ) {
			$at = $this->skip_shortcode_whitespace( $at, $this->length );
			if ( $at >= $this->length || ']' !== $this->text[ $at ] ) {
				return false;
			}

			++$at;
			$escaped_closing = $at < $this->length && ']' === $this->text[ $at ];
			if ( $escaped_closing ) {
				++$at;
			}

			return array(
				'start'           => $start,
				'length'          => $at - $start,
				'tag_name_start'  => $tag_name_start,
				'tag_name_length' => $tag_name_length,
				'is_closing'      => true,
				'self_closing'    => false,
				'escaped_opening' => $escaped_opening,
				'escaped_closing' => $escaped_closing,
				'attributes'      => array(),
			);
		}

		$attributes_start = $at;
		$quote            = null;
		while ( $at < $this->length ) {
			$byte = $this->text[ $at ];

			if ( null !== $quote ) {
				if ( '\\' === $byte && $at + 1 < $this->length ) {
					$at += 2;
					continue;
				}

				if ( $byte === $quote ) {
					$quote = null;
				}

				++$at;
				continue;
			}

			if ( '"' === $byte || "'" === $byte ) {
				$quote = $byte;
				++$at;
				continue;
			}

			if ( ']' !== $byte ) {
				++$at;
				continue;
			}

			$attributes_end = $at;
			$tail           = $this->previous_non_whitespace_offset( $attributes_end );
			$self_closing   = $tail >= $attributes_start && '/' === $this->text[ $tail ];
			if ( $self_closing ) {
				$attributes_end = $tail;
			}

			++$at;
			$escaped_closing = $at < $this->length && ']' === $this->text[ $at ];
			if ( $escaped_closing ) {
				++$at;
			}

			return array(
				'start'           => $start,
				'length'          => $at - $start,
				'tag_name_start'  => $tag_name_start,
				'tag_name_length' => $tag_name_length,
				'is_closing'      => false,
				'self_closing'    => $self_closing,
				'escaped_opening' => $escaped_opening,
				'escaped_closing' => $escaped_closing,
				'attributes'      => $this->parse_attributes(
					$attributes_start,
					$attributes_end
				),
			);
		}

		return false;
	}

	/**
	 * Parses attributes from a complete shortcode opener.
	 *
	 * @param int $start Attribute-list start offset.
	 * @param int $end   Attribute-list end offset.
	 * @return array Parsed attribute metadata.
	 */
	private function parse_attributes( int $start, int $end ): array {
		$attributes = array();
		$at         = $start;

		while ( $at < $end ) {
			$at = $this->skip_shortcode_whitespace( $at, $end );
			if ( $at >= $end ) {
				break;
			}

			$attribute_start = $at;
			$quote           = $this->text[ $at ];
			if ( '"' === $quote || "'" === $quote ) {
				$value_start = $at + 1;
				$value_end   = $this->find_quote_end( $value_start, $end, $quote );
				if ( false === $value_end ) {
					break;
				}

				$at           = $value_end + 1;
				$attributes[] = array(
					'name'            => null,
					'comparable_name' => null,
					'start'           => $attribute_start,
					'length'          => $at - $attribute_start,
					'value_start'     => $value_start,
					'value_length'    => $value_end - $value_start,
					'quote'           => $quote,
				);
				continue;
			}

			$word_start = $at;
			while (
				$at < $end &&
				! $this->is_shortcode_whitespace_at( $at ) &&
				'=' !== $this->text[ $at ] &&
				'"' !== $this->text[ $at ] &&
				"'" !== $this->text[ $at ]
			) {
				++$at;
			}

			$word_end = $at;
			if ( $word_start === $word_end ) {
				++$at;
				continue;
			}

			$after_word = $this->skip_shortcode_whitespace( $at, $end );
			if ( $after_word >= $end || '=' !== $this->text[ $after_word ] ) {
				$attributes[] = array(
					'name'            => null,
					'comparable_name' => null,
					'start'           => $attribute_start,
					'length'          => $word_end - $attribute_start,
					'value_start'     => $word_start,
					'value_length'    => $word_end - $word_start,
					'quote'           => null,
				);

				$at = $word_end;
				continue;
			}

			$name = substr( $this->text, $word_start, $word_end - $word_start );
			$at   = $this->skip_shortcode_whitespace( $after_word + 1, $end );

			if ( $at < $end && ( '"' === $this->text[ $at ] || "'" === $this->text[ $at ] ) ) {
				$quote       = $this->text[ $at ];
				$value_start = $at + 1;
				$value_end   = $this->find_quote_end( $value_start, $end, $quote );
				if ( false === $value_end ) {
					break;
				}
				$at = $value_end + 1;
			} else {
				$quote       = null;
				$value_start = $at;
				while ( $at < $end && ! $this->is_shortcode_whitespace_at( $at ) ) {
					++$at;
				}
				$value_end = $at;
			}

			$attributes[] = array(
				'name'            => $name,
				'comparable_name' => strtolower( $name ),
				'start'           => $attribute_start,
				'length'          => $at - $attribute_start,
				'value_start'     => $value_start,
				'value_length'    => $value_end - $value_start,
				'quote'           => $quote,
			);
		}

		return $attributes;
	}

	/**
	 * Finds an attribute's closing quote.
	 *
	 * Backslash-quoted values are accepted as a builder-tolerant extension. The
	 * tokenizer does not decode them.
	 *
	 * @param int    $start Value start offset.
	 * @param int    $end   Attribute-list end offset.
	 * @param string $quote Quote delimiter.
	 * @return int|false Closing quote offset, or false.
	 */
	private function find_quote_end( int $start, int $end, string $quote ) {
		$at = $start;
		while ( $at < $end ) {
			if ( '\\' === $this->text[ $at ] && $at + 1 < $end ) {
				$at += 2;
				continue;
			}

			if ( $quote === $this->text[ $at ] ) {
				return $at;
			}

			++$at;
		}

		return false;
	}

	/**
	 * Applies parsed shortcode metadata as the current token.
	 *
	 * @param array $shortcode Parsed shortcode metadata.
	 */
	private function set_shortcode_token( array $shortcode ): void {
		$this->token_type                  = self::TOKEN_SHORTCODE;
		$this->token_starts_at             = $shortcode['start'];
		$this->token_length                = $shortcode['length'];
		$this->tag_name_starts_at          = $shortcode['tag_name_start'];
		$this->tag_name_length             = $shortcode['tag_name_length'];
		$this->is_closing_tag              = $shortcode['is_closing'];
		$this->has_self_closing_flag       = $shortcode['self_closing'];
		$this->has_escaped_opening_bracket = $shortcode['escaped_opening'];
		$this->has_escaped_closing_bracket = $shortcode['escaped_closing'];
		$this->attributes                  = $shortcode['attributes'];
	}

	/**
	 * Clears state belonging to the previous token.
	 */
	private function after_token(): void {
		$this->token_type                  = null;
		$this->token_starts_at             = null;
		$this->token_length                = null;
		$this->tag_name_starts_at          = null;
		$this->tag_name_length             = null;
		$this->is_closing_tag              = false;
		$this->has_self_closing_flag       = false;
		$this->has_escaped_opening_bracket = false;
		$this->has_escaped_closing_bracket = false;
		$this->attributes                  = array();
		$this->attribute_index             = -1;
	}

	/**
	 * Returns the current attribute metadata.
	 *
	 * @return array|null Current attribute metadata.
	 */
	private function get_current_attribute(): ?array {
		if (
			$this->attribute_index < 0 ||
			$this->attribute_index >= count( $this->attributes )
		) {
			return null;
		}

		return $this->attributes[ $this->attribute_index ];
	}

	/**
	 * Returns a stable lexical-update key for an attribute.
	 *
	 * @param int $attribute_index Attribute index in the current token.
	 * @return string Update key.
	 */
	private function get_attribute_update_key( int $attribute_index ): string {
		return 'attribute:' . $this->token_starts_at . ':' . $attribute_index;
	}

	/**
	 * Returns a stable lexical-update key for the current text token.
	 *
	 * @return string Update key.
	 */
	private function get_text_update_key(): string {
		return 'text:' . $this->token_starts_at;
	}

	/**
	 * Checks whether a replacement can remain unquoted.
	 *
	 * @param string $value Replacement value.
	 * @return bool Whether the value can remain unquoted.
	 */
	private function can_use_unquoted_attribute_value( string $value ): bool {
		if ( false !== strpbrk( $value, " \t\f\r\n\"'[]" ) ) {
			return false;
		}

		return (
			false === strpos( $value, "\u{00A0}" ) &&
			false === strpos( $value, "\u{200B}" )
		);
	}

	/**
	 * Checks whether a byte may appear in a practical shortcode tag name.
	 *
	 * Builder and WordPress shortcode tags conventionally use ASCII letters,
	 * digits, underscores, hyphens, colons, and periods. Non-ASCII bytes are
	 * accepted so the tokenizer does not split UTF-8 names. Restricting the
	 * ASCII punctuation prevents CSS attribute selectors such as `[href^=]`
	 * from being reported as shortcode tokens.
	 *
	 * @param string $byte Byte to inspect.
	 * @return bool Whether the byte can occur in a tag name.
	 */
	private function is_shortcode_tag_name_byte( string $byte ): bool {
		$ord = ord( $byte );
		if ( $ord >= 0x80 ) {
			return true;
		}

		return (
			( $ord >= 0x30 && $ord <= 0x39 ) ||
			( $ord >= 0x41 && $ord <= 0x5A ) ||
			( $ord >= 0x61 && $ord <= 0x7A ) ||
			'_' === $byte ||
			'-' === $byte ||
			':' === $byte ||
			'.' === $byte
		);
	}

	/**
	 * Checks for shortcode whitespace at a byte offset.
	 *
	 * The shortcode_parse_atts() function normalizes U+00A0 NO-BREAK SPACE and U+200B
	 * ZERO WIDTH SPACE to ordinary spaces before parsing. Treating them as
	 * separators here provides the same attribute boundaries without changing
	 * the source bytes.
	 *
	 * @param int $at Byte offset.
	 * @return bool Whether whitespace begins at the offset.
	 */
	private function is_shortcode_whitespace_at( int $at ): bool {
		if ( $at >= $this->length ) {
			return false;
		}

		if ( false !== strpos( " \t\f\r\n", $this->text[ $at ] ) ) {
			return true;
		}

		return (
			0 === substr_compare( $this->text, "\u{00A0}", $at, 2 ) ||
			0 === substr_compare( $this->text, "\u{200B}", $at, 3 )
		);
	}

	/**
	 * Advances past shortcode whitespace.
	 *
	 * @param int $at  Starting byte offset.
	 * @param int $end Exclusive end offset.
	 * @return int First non-whitespace byte offset.
	 */
	private function skip_shortcode_whitespace( int $at, int $end ): int {
		while ( $at < $end ) {
			$ascii_length = strspn( $this->text, " \t\f\r\n", $at, $end - $at );
			if ( $ascii_length > 0 ) {
				$at += $ascii_length;
				continue;
			}

			if ( 0 === substr_compare( $this->text, "\u{00A0}", $at, 2 ) ) {
				$at += 2;
				continue;
			}

			if ( 0 === substr_compare( $this->text, "\u{200B}", $at, 3 ) ) {
				$at += 3;
				continue;
			}

			break;
		}

		return $at;
	}

	/**
	 * Finds the previous non-whitespace byte before an exclusive offset.
	 *
	 * @param int $before Exclusive byte offset.
	 * @return int Previous non-whitespace offset, or -1.
	 */
	private function previous_non_whitespace_offset( int $before ): int {
		$at = $before - 1;
		while ( $at >= 0 ) {
			if ( false !== strpos( " \t\f\r\n", $this->text[ $at ] ) ) {
				--$at;
				continue;
			}

			if (
				$at >= 1 &&
				0 === substr_compare( $this->text, "\u{00A0}", $at - 1, 2 )
			) {
				$at -= 2;
				continue;
			}

			if (
				$at >= 2 &&
				0 === substr_compare( $this->text, "\u{200B}", $at - 2, 3 )
			) {
				$at -= 3;
				continue;
			}

			break;
		}

		return $at;
	}
}
