<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\Shortcode\ShortcodeProcessor;
use WordPress\DataLiberation\URL\CSSURLProcessor;

class ShortcodeProcessorTest extends TestCase {

	public function test_tokenizes_text_and_shortcodes_in_a_single_pass(): void {
		$processor = new ShortcodeProcessor(
			'Before [gallery ids="1,2"] middle [/gallery] after.'
		);

		$this->assertSame(
			array(
				array( '#text', 'Before ', null, false, false ),
				array( '#shortcode', '[gallery ids="1,2"]', 'gallery', false, false ),
				array( '#text', ' middle ', null, false, false ),
				array( '#shortcode', '[/gallery]', 'gallery', true, false ),
				array( '#text', ' after.', null, false, false ),
			),
			$this->collect_tokens( $processor )
		);
	}

	public function test_reports_token_byte_offsets_for_utf8_content(): void {
		$input     = 'Zażółć [et_pb_image src="https://example.com/żółw.jpg"]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( '#text', $processor->get_token_type() );
		$this->assertSame( 'Zażółć ', $processor->get_token_text() );
		$this->assertSame( 0, $processor->get_token_start() );
		$this->assertSame( strlen( 'Zażółć ' ), $processor->get_token_length() );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( '#shortcode', $processor->get_token_type() );
		$this->assertSame( strlen( 'Zażółć ' ), $processor->get_token_start() );
		$this->assertSame(
			strlen( '[et_pb_image src="https://example.com/żółw.jpg"]' ),
			$processor->get_token_length()
		);
	}

	public function test_recognizes_opening_closing_self_closing_and_escaped_tokens(): void {
		$processor = new ShortcodeProcessor(
			'[one][/one][two /][three/][[four url="https://example.com"]]'
		);

		$tokens = array();
		while ( $processor->next_shortcode() ) {
			$tokens[] = array(
				$processor->get_tag(),
				$processor->is_tag_closer(),
				$processor->has_self_closing_flag(),
				$processor->is_escaped(),
			);
		}

		$this->assertSame(
			array(
				array( 'one', false, false, false ),
				array( 'one', true, false, false ),
				array( 'two', false, true, false ),
				array( 'three', false, true, false ),
				array( 'four', false, false, true ),
			),
			$tokens
		);
	}

	public function test_tokenizes_same_name_nesting_without_matching_entire_enclosing_macros(): void {
		$processor = new ShortcodeProcessor(
			'[row level="1"][row level="2"]Inner[/row][/row]'
		);

		$tokens = array();
		while ( $processor->next_shortcode( 'row' ) ) {
			$tokens[] = array(
				$processor->is_tag_closer(),
				$processor->get_attribute( 'level' ),
			);
		}

		$this->assertSame(
			array(
				array( false, '1' ),
				array( false, '2' ),
				array( true, null ),
				array( true, null ),
			),
			$tokens
		);
	}

	public function test_next_shortcode_filters_by_name_prefix_and_closer_policy(): void {
		$processor = new ShortcodeProcessor(
			'[gallery][et_pb_section][/et_pb_section][vc_row][/vc_row]'
		);

		$tags = array();
		while (
			$processor->next_shortcode(
				array(
					'tag_prefix'  => 'et_pb_',
					'tag_closers' => 'skip',
				)
			)
		) {
			$tags[] = $processor->get_tag();
		}

		$this->assertSame( array( 'et_pb_section' ), $tags );
	}

	public function test_next_shortcode_supports_match_offset(): void {
		$processor = new ShortcodeProcessor( '[item id="1"][item id="2"][item id="3"]' );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name'     => 'item',
					'match_offset' => 2,
				)
			)
		);
		$this->assertSame( '2', $processor->get_attribute( 'id' ) );
	}

	public function test_parses_named_positional_empty_duplicate_and_non_ascii_spaced_attributes(): void {
		$processor = new ShortcodeProcessor(
			"[demo one=\"a b\"\ttwo='c' three=3 empty=\"\" \"positional value\" bare"
			. "\u{00A0}duplicate=first\u{200B}DUPLICATE=last]"
		);

		$this->assertTrue( $processor->next_shortcode( 'demo' ) );

		$attributes = array();
		while ( $processor->next_attribute() ) {
			$attributes[] = array(
				'name'  => $processor->get_attribute_name(),
				'value' => $processor->get_attribute_value(),
				'quote' => $processor->get_attribute_quote(),
			);
		}

		$this->assertSame(
			array(
				array( 'name' => 'one', 'value' => 'a b', 'quote' => '"' ),
				array( 'name' => 'two', 'value' => 'c', 'quote' => "'" ),
				array( 'name' => 'three', 'value' => '3', 'quote' => null ),
				array( 'name' => 'empty', 'value' => '', 'quote' => '"' ),
				array( 'name' => null, 'value' => 'positional value', 'quote' => '"' ),
				array( 'name' => null, 'value' => 'bare', 'quote' => null ),
				array( 'name' => 'duplicate', 'value' => 'first', 'quote' => null ),
				array( 'name' => 'DUPLICATE', 'value' => 'last', 'quote' => null ),
			),
			$attributes
		);

		$this->assertSame( 'last', $processor->get_attribute( 'duplicate' ) );
		$this->assertSame(
			array( 'duplicate' ),
			$processor->get_attribute_names_with_prefix( 'dup' )
		);
	}

	public function test_quoted_attribute_may_contain_css_html_json_and_brackets(): void {
		$css = '.x[data-label="<"]::before {'
			. ' content: "[still-css]";'
			. ' background: url(https://old.example/zażółć%20gęślą.jpg);'
			. ' }';
		$json = '{"selector":"section-1","media":{"phone":["one","two"]}}';
		$input = "[et_pb_section custom_css_main_element='" . $css . "'"
			. " ct_options='" . $json . "']";

		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'et_pb_section' ) );
		$this->assertSame( $css, $processor->get_attribute( 'custom_css_main_element' ) );
		$this->assertSame( $json, $processor->get_attribute( 'ct_options' ) );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public function test_attribute_updates_preserve_unrelated_bytes_and_existing_quotes(): void {
		$input = "[et_pb_section  background_image = 'https://old.example/a.jpg'"
			. ' custom_css_main_element=".x::before { content: \'<\'; }" ]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'et_pb_section' ) );
		while ( $processor->next_attribute() ) {
			if ( 'background_image' === $processor->get_attribute_name() ) {
				$this->assertTrue(
					$processor->set_attribute_value( 'https://new.example/a.jpg' )
				);
			}
		}

		$this->assertSame(
			str_replace( 'https://old.example', 'https://new.example', $input ),
			$processor->get_updated_text()
		);
	}

	public function test_multiple_updates_are_applied_by_original_byte_offset(): void {
		$input = '[one url="https://old.example/one"][two url=https://old.example/two]';
		$processor = new ShortcodeProcessor( $input );

		while ( $processor->next_shortcode() ) {
			while ( $processor->next_attribute() ) {
				if ( 'url' === $processor->get_attribute_name() ) {
					$processor->set_attribute_value(
						str_replace(
							'https://old.example',
							'https://new.example',
							$processor->get_attribute_value()
						)
					);
				}
			}
		}

		$this->assertSame(
			str_replace( 'https://old.example', 'https://new.example', $input ),
			(string) $processor
		);
	}

	public function test_updating_unquoted_attribute_adds_quotes_only_when_required(): void {
		$processor = new ShortcodeProcessor( '[button url=https://old.example/a]' );

		$this->assertTrue( $processor->next_shortcode( 'button' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertTrue(
			$processor->set_attribute_value( 'https://new.example/a path' )
		);

		$this->assertSame(
			'[button url="https://new.example/a path"]',
			$processor->get_updated_text()
		);
	}

	public function test_attribute_update_refuses_value_that_cannot_be_quoted_safely(): void {
		$input     = '[demo value=original]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'demo' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertFalse( $processor->set_attribute_value( 'both " and \' with space' ) );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public function test_text_update_is_byte_preserving_and_does_not_apply_html_escaping(): void {
		$input = '.x::before {'
			. ' content: "<";'
			. ' background: url(https://old.example/a.jpg);'
			. ' }';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( '#text', $processor->get_token_type() );
		$this->assertTrue(
			$processor->set_modifiable_text(
				str_replace( 'https://old.example', 'https://new.example', $input )
			)
		);

		$this->assertSame(
			str_replace( 'https://old.example', 'https://new.example', $input ),
			$processor->get_updated_text()
		);
		$this->assertStringNotContainsString( '&quot;', $processor->get_updated_text() );
		$this->assertStringNotContainsString( '&lt;', $processor->get_updated_text() );
	}

	public function test_shortcode_updates_do_not_claim_neighboring_block_html_or_css_regions(): void {
		$shortcode = "[et_pb_section background_image='https://old.example/shortcode.jpg'"
			. " custom_css_main_element='.hero::before { content: \"<\";"
			. " background: url(https://old.example/attribute.jpg); }']";
		$input = '<!-- wp:paragraph -->'
			. '<p><a href="https://old.example/html">HTML</a></p>'
			. '<!-- /wp:paragraph -->'
			. '<!-- wp:shortcode -->'
			. $shortcode
			. '[/et_pb_section]'
			. '<!-- /wp:shortcode -->'
			. '<style>.footer { background: url(https://old.example/style.jpg); }</style>';
		$processor = new ShortcodeProcessor( $input );

		while ( $processor->next_shortcode() ) {
			while ( $processor->next_attribute() ) {
				$value = $processor->get_attribute_value();
				if ( false !== strpos( $value, 'https://old.example' ) ) {
					$processor->set_attribute_value(
						str_replace( 'https://old.example', 'https://new.example', $value )
					);
				}
			}
		}

		$expected = str_replace(
			$shortcode,
			str_replace( 'https://old.example', 'https://new.example', $shortcode ),
			$input
		);
		$this->assertSame( $expected, $processor->get_updated_text() );
		$this->assertStringContainsString(
			'href="https://old.example/html"',
			$processor->get_updated_text()
		);
		$this->assertStringContainsString(
			'url(https://old.example/style.jpg)',
			$processor->get_updated_text()
		);
		$this->assertStringContainsString( 'content: "<"', $processor->get_updated_text() );
	}

	/**
	 * @dataProvider builder_shortcode_provider
	 */
	public function test_tokenizes_real_builder_shapes(
		string $builder,
		string $input,
		array $expected_tags
	): void {
		$processor = new ShortcodeProcessor( $input );
		$actual    = array();

		while ( $processor->next_shortcode() ) {
			$actual[] = ( $processor->is_tag_closer() ? '/' : '' ) . $processor->get_tag();
		}

		$this->assertSame( $expected_tags, $actual, $builder );
	}

	public static function builder_shortcode_provider(): array {
		return array(
			'Divi 4 with HTML and a nested third-party shortcode' => array(
				'Divi 4',
				'[et_pb_section background_image="https://old.example/hero.jpg"'
					. ' custom_css_main_element=".x::before { content: \'<\'; }"]'
					. '[et_pb_row][et_pb_column type="4_4"]'
					. '[et_pb_text]<p>[contact-form-7 id="10"]</p>[/et_pb_text]'
					. '[/et_pb_column][/et_pb_row][/et_pb_section]',
				array(
					'et_pb_section',
					'et_pb_row',
					'et_pb_column',
					'et_pb_text',
					'contact-form-7',
					'/et_pb_text',
					'/et_pb_column',
					'/et_pb_row',
					'/et_pb_section',
				),
			),
			'Divi 5 block markup with a legacy Divi 4 region' => array(
				'Divi 5 legacy region',
				'<!-- wp:paragraph --><p>Before</p><!-- /wp:paragraph -->'
					. '<!-- wp:shortcode -->'
					. '[et_pb_section][et_pb_row][/et_pb_row][/et_pb_section]'
					. '<!-- /wp:shortcode -->',
				array(
					'et_pb_section',
					'et_pb_row',
					'/et_pb_row',
					'/et_pb_section',
				),
			),
			'Gutenberg Shortcode block' => array(
				'Gutenberg',
				'<!-- wp:paragraph --><p>Before</p><!-- /wp:paragraph -->'
					. '<!-- wp:shortcode -->[gallery ids="1,2"]<!-- /wp:shortcode -->',
				array( 'gallery' ),
			),
			'WPBakery nested layout with CSS and Base64 raw HTML' => array(
				'WPBakery',
				'[vc_row css=".vc_custom_1{background:url(https://old.example/a.jpg);}"]'
					. '[vc_column][vc_raw_html]JTNDcCUzRUhlbGxvJTNDJTJGcCUzRQ=='
					. '[/vc_raw_html][/vc_column][/vc_row]',
				array(
					'vc_row',
					'vc_column',
					'vc_raw_html',
					'/vc_raw_html',
					'/vc_column',
					'/vc_row',
				),
			),
			'Oxygen legacy shortcode tree with JSON options' => array(
				'Oxygen',
				"[ct_section ct_options='{\"selector\":\"section-1\",\"original\":{"
					. "\"background-image\":\"https://old.example/a.jpg\"}}']"
					. '[ct_code_block ct_options=\'{"code-css":"LmF7Y29sb3I6cmVkO30="}\']'
					. '[/ct_code_block][/ct_section]',
				array(
					'ct_section',
					'ct_code_block',
					'/ct_code_block',
					'/ct_section',
				),
			),
			'Avada Fusion hierarchy' => array(
				'Avada',
				'[fusion_builder_container background_image="https://old.example/a.jpg"]'
					. '[fusion_builder_row][fusion_builder_column type="1_1"]'
					. '[fusion_text]<p>Text</p>[/fusion_text]'
					. '[/fusion_builder_column][/fusion_builder_row]'
					. '[/fusion_builder_container]',
				array(
					'fusion_builder_container',
					'fusion_builder_row',
					'fusion_builder_column',
					'fusion_text',
					'/fusion_text',
					'/fusion_builder_column',
					'/fusion_builder_row',
					'/fusion_builder_container',
				),
			),
			'Themify nested columns' => array(
				'Themify',
				'[themify_col grid="2-1 first"]'
					. '[themify_button link="https://old.example/a?x=1&y=2"]Text[/themify_button]'
					. '[/themify_col]',
				array(
					'themify_col',
					'themify_button',
					'/themify_button',
					'/themify_col',
				),
			),
		);
	}

	/**
	 * @dataProvider nested_container_provider
	 */
	public function test_outer_non_shortcode_formats_are_left_for_their_own_processors(
		string $builder,
		string $input
	): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertFalse( $processor->next_shortcode(), $builder );
		$this->assertSame( $input, $processor->get_updated_text(), $builder );
	}

	public static function nested_container_provider(): array {
		return array(
			'Elementor JSON' => array(
				'Elementor',
				'{"id":"6a637978","elType":"widget","widgetType":"image",'
					. '"settings":{"url":"https://old.example/a.jpg"},"elements":[]}',
			),
			'Beaver Builder serialized PHP' => array(
				'Beaver Builder',
				'a:1:{s:3:"url";s:29:"https://old.example/image.jpg";}',
			),
			'SiteOrigin serialized PHP' => array(
				'SiteOrigin',
				'a:1:{s:7:"widgets";a:1:{i:0;a:1:{s:5:"image";'
					. 's:29:"https://old.example/image.jpg";}}}',
			),
			'Divi 5 block data without legacy shortcodes' => array(
				'Divi 5',
				'<!-- wp:divi/section {"backgroundImage":"https://old.example/a.jpg"} /-->',
			),
			'Oxygen JSON' => array(
				'Oxygen',
				'{"component":{"name":"ct_section","options":{"original":{'
					. '"background-image":"https://old.example/a.jpg"}}}}',
			),
		);
	}

	/**
	 * @dataProvider malformed_candidate_provider
	 */
	public function test_malformed_candidates_remain_plain_text( string $input ): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertFalse( $processor->next_shortcode() );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public static function malformed_candidate_provider(): array {
		return array(
			'empty brackets'             => array( 'Before [] after' ),
			'only a closer marker'       => array( 'Before [/] after' ),
			'truncated tag'              => array( 'Before [et_pb_section' ),
			'unclosed quoted attribute' => array(
				'Before [et_pb_section title="unterminated] after',
			),
			'HTML closing tag'           => array( 'Before </div> after' ),
			'CSS attribute selector'     => array(
				'.x[href^="https://old.example"] { color: red; }',
			),
		);
	}

	public function test_bracketed_identifiers_require_caller_context(): void {
		$css = '.x[hidden] { color: red; }';

		$unfiltered = new ShortcodeProcessor( $css );
		$this->assertTrue( $unfiltered->next_shortcode() );
		$this->assertSame( 'hidden', $unfiltered->get_tag() );

		$builder_filtered = new ShortcodeProcessor( $css );
		$this->assertFalse(
			$builder_filtered->next_shortcode(
				array(
					'tag_prefix' => 'et_pb_',
				)
			)
		);
		$this->assertSame( $css, $builder_filtered->get_updated_text() );
	}

	public function test_rewrites_url_inside_divi_css_attribute_without_html_encoding(): void {
		$old_url = 'https://old.example/%C5%BC%C3%B3%C5%82%C4%87-g%C4%99%C5%9Bl%C4%85.jpg';
		$new_url = 'https://new.example/%C5%BC%C3%B3%C5%82%C4%87-g%C4%99%C5%9Bl%C4%85.jpg';
		$css = '.x::before { content: "<"; background: url(' . $old_url
			. ') no-repeat center center fixed; }';
		$input = "[et_pb_section custom_css_main_element='" . $css . "']Hello[/et_pb_section]";
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'et_pb_section' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertSame( 'custom_css_main_element', $processor->get_attribute_name() );

		$css_processor = new CSSURLProcessor( $processor->get_attribute_value() );
		$this->assertTrue( $css_processor->next_url() );
		$this->assertSame( $old_url, $css_processor->get_raw_url() );
		$this->assertTrue( $css_processor->set_raw_url( $new_url ) );

		$updated_css = $css_processor->get_updated_css();
		$this->assertTrue( $processor->set_attribute_value( $updated_css ) );

		$updated = $processor->get_updated_text();
		$expected_css = str_replace(
			'url(' . $old_url . ')',
			'url("' . $new_url . '")',
			$css
		);

		$this->assertSame( str_replace( $css, $expected_css, $input ), $updated );
		// The CSS is not double-encoded the way an HTML text-node serializer would.
		$this->assertStringContainsString( 'content: "<"', $updated );
		$this->assertStringNotContainsString( '&quot;', $updated );
		$this->assertStringNotContainsString( '&lt;', $updated );
		$this->assertStringNotContainsString( '&apos;', $updated );
		// The closing quote is not slurped into the URL and percent-encoded.
		$this->assertStringNotContainsString( '%22', $updated );
		$this->assertStringContainsString(
			'") no-repeat center center fixed',
			$updated
		);
		$this->assertStringNotContainsString( $old_url, $updated );
	}

	public function test_shortcode_syntax_inside_a_quoted_attribute_is_not_a_separate_token(): void {
		$processor = new ShortcodeProcessor( '[outer inner="[gallery ids=\'1,2\']"]text[/outer]' );

		$tags = array();
		while ( $processor->next_shortcode() ) {
			$tags[] = ( $processor->is_tag_closer() ? '/' : '' ) . $processor->get_tag();
		}

		$this->assertSame( array( 'outer', '/outer' ), $tags );
	}


	/**
	 * @dataProvider attribute_update_round_trip_provider
	 */
	public function test_attribute_updates_round_trip_without_changing_token_semantics(
		string $input,
		string $attribute_name,
		string $replacement,
		string $expected
	): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$was_self_closing = $processor->has_self_closing_flag();
		$this->assertTrue( $this->move_to_attribute( $processor, $attribute_name ) );
		$this->assertTrue( $processor->set_attribute_value( $replacement ) );
		$this->assertSame( $expected, $processor->get_updated_text() );

		$reparsed = new ShortcodeProcessor( $processor->get_updated_text() );
		$this->assertTrue( $reparsed->next_shortcode( 'tag' ) );
		$this->assertSame( $was_self_closing, $reparsed->has_self_closing_flag() );
		$this->assertSame( $replacement, $reparsed->get_attribute( $attribute_name ) );
	}

	public static function attribute_update_round_trip_provider(): array {
		return array(
			'terminal slash on final attribute is quoted' => array(
				'[tag url=old]',
				'url',
				'https://example.com/',
				'[tag url="https://example.com/"]',
			),
			'terminal slash before another attribute remains unquoted' => array(
				'[tag url=old other=1]',
				'url',
				'https://example.com/',
				'[tag url=https://example.com/ other=1]',
			),
			'terminal slash before an existing self-closing flag remains unquoted' => array(
				'[tag url=old /]',
				'url',
				'https://example.com/',
				'[tag url=https://example.com/ /]',
			),
			'empty replacement is quoted' => array(
				'[tag value=old]',
				'value',
				'',
				'[tag value=""]',
			),
			'vertical tab requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\vtwo",
				"[tag value=\"one\vtwo\"]",
			),
			'ordinary whitespace requires quotes' => array(
				'[tag value=old]',
				'value',
				'two words',
				'[tag value="two words"]',
			),
			'tab requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\ttwo",
				"[tag value=\"one\ttwo\"]",
			),
			'form feed requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\ftwo",
				"[tag value=\"one\ftwo\"]",
			),
			'carriage return requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\rtwo",
				"[tag value=\"one\rtwo\"]",
			),
			'line feed requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\ntwo",
				"[tag value=\"one\ntwo\"]",
			),
			'opening bracket requires quotes' => array(
				'[tag value=old]',
				'value',
				'one[two',
				'[tag value="one[two"]',
			),
			'closing bracket requires quotes' => array(
				'[tag value=old]',
				'value',
				'one]two',
				'[tag value="one]two"]',
			),
			'double quote selects single-quoted representation' => array(
				'[tag value=old]',
				'value',
				'say "hi"',
				'[tag value=\'say "hi"\']',
			),
			'single quote selects double-quoted representation' => array(
				'[tag value=old]',
				'value',
				"it's",
				'[tag value="it\'s"]',
			),
			'non-breaking space requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\u{00A0}two",
				"[tag value=\"one\u{00A0}two\"]",
			),
			'zero-width space requires quotes' => array(
				'[tag value=old]',
				'value',
				"one\u{200B}two",
				"[tag value=\"one\u{200B}two\"]",
			),
		);
	}

	public function test_positional_terminal_slash_update_preserves_value_and_token_type(): void {
		$processor = new ShortcodeProcessor( '[tag old]' );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertNull( $processor->get_attribute_name() );
		$this->assertTrue( $processor->set_attribute_value( 'https://example.com/' ) );
		$this->assertSame( '[tag "https://example.com/"]', $processor->get_updated_text() );

		$reparsed = new ShortcodeProcessor( $processor->get_updated_text() );
		$this->assertTrue( $reparsed->next_shortcode( 'tag' ) );
		$this->assertFalse( $reparsed->has_self_closing_flag() );
		$this->assertTrue( $reparsed->next_attribute() );
		$this->assertNull( $reparsed->get_attribute_name() );
		$this->assertSame( 'https://example.com/', $reparsed->get_attribute_value() );
	}

	public function test_realistic_backslash_quoted_css_with_both_quote_delimiters_is_refused(): void {
		$input = '[tag css=".x::before { content: \\"it\'s\\";'
			. ' background: url(https://old.example/a.jpg); }"]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $this->move_to_attribute( $processor, 'css' ) );
		$updated_css = str_replace(
			'https://old.example',
			'https://new.example',
			$processor->get_attribute_value()
		);

		$this->assertFalse( $processor->set_attribute_value( $updated_css ) );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public function test_updated_attribute_value_is_logical_while_quote_and_spans_describe_source(): void {
		$input     = '[tag value="old"]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $processor->next_attribute() );
		$source_start  = $processor->get_attribute_start();
		$source_length = $processor->get_attribute_length();

		$this->assertTrue( $processor->set_attribute_value( 'say "hi"' ) );
		$this->assertSame( 'say "hi"', $processor->get_attribute_value() );
		$this->assertSame( 'say "hi"', $processor->get_attribute( 'value' ) );
		$this->assertSame( '"', $processor->get_attribute_quote() );
		$this->assertSame( $source_start, $processor->get_attribute_start() );
		$this->assertSame( $source_length, $processor->get_attribute_length() );
		$this->assertSame( $input, $processor->get_token_text() );
		$this->assertSame( '[tag value=\'say "hi"\']', $processor->get_updated_text() );
	}

	public function test_last_successful_update_wins_and_a_later_refusal_preserves_it(): void {
		$processor = new ShortcodeProcessor( '[tag value=old]' );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertTrue( $processor->set_attribute_value( 'first' ) );
		$this->assertTrue( $processor->set_attribute_value( 'second' ) );
		$this->assertSame( 'second', $processor->get_attribute_value() );
		$this->assertFalse( $processor->set_attribute_value( 'both " and \' with space' ) );
		$this->assertSame( 'second', $processor->get_attribute_value() );
		$this->assertSame( '[tag value=second]', $processor->get_updated_text() );
	}

	public function test_length_changing_text_and_attribute_updates_use_original_byte_offsets(): void {
		$input     = 'α[tag a=1 b="22"]MID[tag c=333]ω';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_token() );
		$this->assertTrue( $processor->set_modifiable_text( 'A-MUCH-LONGER-PREFIX' ) );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $this->move_to_attribute( $processor, 'a' ) );
		$this->assertTrue( $processor->set_attribute_value( '123456789' ) );
		$this->assertTrue( $this->move_to_attribute( $processor, 'b' ) );
		$this->assertTrue( $processor->set_attribute_value( '' ) );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( 'MID', $processor->get_modifiable_text() );
		$this->assertTrue( $processor->set_modifiable_text( '' ) );
		$this->assertSame(
			'A-MUCH-LONGER-PREFIX[tag a=123456789 b=""][tag c=333]ω',
			$processor->get_updated_text()
		);

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $this->move_to_attribute( $processor, 'c' ) );
		$this->assertTrue( $processor->set_attribute_value( 'two words' ) );

		$this->assertTrue( $processor->next_token() );
		$this->assertTrue( $processor->set_modifiable_text( 'TAIL' ) );

		$expected = 'A-MUCH-LONGER-PREFIX[tag a=123456789 b=""]'
			. '[tag c="two words"]TAIL';
		$this->assertSame( $expected, $processor->get_updated_text() );
		$this->assertSame( $expected, (string) $processor );
		$this->assertSame( $expected, $processor->get_updated_text() );
	}

	public function test_text_getters_distinguish_original_and_updated_bytes(): void {
		$processor = new ShortcodeProcessor( 'original' );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( 'original', $processor->get_token_text() );
		$this->assertSame( 'original', $processor->get_modifiable_text() );
		$this->assertTrue( $processor->set_modifiable_text( 'updated' ) );
		$this->assertSame( 'original', $processor->get_token_text() );
		$this->assertSame( 'updated', $processor->get_modifiable_text() );
		$this->assertSame( 'updated', $processor->get_updated_text() );
	}

	public function test_text_replacement_is_not_rescanned_in_the_current_pass(): void {
		$processor = new ShortcodeProcessor( 'plain text' );

		$this->assertTrue( $processor->next_token() );
		$this->assertTrue( $processor->set_modifiable_text( '[tag]' ) );
		$this->assertFalse( $processor->next_token() );
		$this->assertSame( '[tag]', $processor->get_updated_text() );

		$reparsed = new ShortcodeProcessor( $processor->get_updated_text() );
		$this->assertTrue( $reparsed->next_shortcode( 'tag' ) );
	}

	public function test_setters_reject_the_wrong_token_and_attribute_states(): void {
		$processor = new ShortcodeProcessor( 'text[tag value=1][/tag]' );

		$this->assertTrue( $processor->next_token() );
		$this->assertFalse( $processor->set_attribute_value( 'nope' ) );
		$this->assertTrue( $processor->set_modifiable_text( 'updated text' ) );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertNull( $processor->get_modifiable_text() );
		$this->assertFalse( $processor->set_modifiable_text( 'nope' ) );
		$this->assertFalse( $processor->set_attribute_value( 'nope' ) );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name'    => 'tag',
					'tag_closers' => 'visit',
				)
			)
		);
		$this->assertTrue( $processor->is_tag_closer() );
		$this->assertFalse( $processor->set_attribute_value( 'nope' ) );
		$this->assertFalse( $processor->set_modifiable_text( 'nope' ) );
	}

	public function test_public_state_is_null_before_scanning_and_after_exhaustion(): void {
		$processor = new ShortcodeProcessor( '' );

		$this->assertNull( $processor->get_token_type() );
		$this->assertNull( $processor->get_token_text() );
		$this->assertNull( $processor->get_tag() );
		$this->assertFalse( $processor->is_tag_closer() );
		$this->assertFalse( $processor->has_self_closing_flag() );
		$this->assertFalse( $processor->is_escaped() );
		$this->assertNull( $processor->get_token_start() );
		$this->assertNull( $processor->get_token_length() );
		$this->assertNull( $processor->get_attribute_name() );
		$this->assertNull( $processor->get_attribute_value() );
		$this->assertNull( $processor->get_attribute_quote() );
		$this->assertNull( $processor->get_attribute_start() );
		$this->assertNull( $processor->get_attribute_length() );
		$this->assertNull( $processor->get_modifiable_text() );
		$this->assertNull( $processor->get_attribute_names_with_prefix( '' ) );
		$this->assertFalse( $processor->next_attribute() );
		$this->assertFalse( $processor->set_attribute_value( 'nope' ) );
		$this->assertFalse( $processor->set_modifiable_text( 'nope' ) );
		$this->assertFalse( $processor->next_token() );

		$processor = new ShortcodeProcessor( '[tag]' );
		$this->assertTrue( $processor->next_token() );
		$this->assertFalse( $processor->set_modifiable_text( 'nope' ) );
		$this->assertFalse( $processor->next_token() );
		$this->assertNull( $processor->get_token_type() );
		$this->assertNull( $processor->get_token_text() );
		$this->assertNull( $processor->get_tag() );
		$this->assertNull( $processor->get_token_start() );
		$this->assertNull( $processor->get_token_length() );
	}

	public function test_failed_attribute_advancement_invalidates_the_attribute_cursor(): void {
		$processor = new ShortcodeProcessor( '[tag one=1 two=2]' );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertTrue( $processor->next_attribute() );
		$this->assertSame( 'two', $processor->get_attribute_name() );
		$this->assertFalse( $processor->next_attribute() );
		$this->assertNull( $processor->get_attribute_name() );
		$this->assertNull( $processor->get_attribute_value() );
		$this->assertNull( $processor->get_attribute_quote() );
		$this->assertNull( $processor->get_attribute_start() );
		$this->assertNull( $processor->get_attribute_length() );
		$this->assertFalse( $processor->set_attribute_value( 'nope' ) );
	}

	public function test_attribute_and_token_offsets_are_absolute_byte_spans(): void {
		$prefix    = "\xEF\xBB\xBFZażółć 😀";
		$shortcode = "[tag  a = 'żółw'  ]";
		$input     = $prefix . $shortcode . "\r\n";
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( strlen( $prefix ), $processor->get_token_start() );
		$this->assertSame( strlen( $shortcode ), $processor->get_token_length() );
		$this->assertSame( $shortcode, $processor->get_token_text() );

		$this->assertTrue( $processor->next_attribute() );
		$attribute_text = "a = 'żółw'";
		$attribute_at   = strpos( $input, $attribute_text );
		$this->assertSame( $attribute_at, $processor->get_attribute_start() );
		$this->assertSame( strlen( $attribute_text ), $processor->get_attribute_length() );
		$this->assertSame(
			$attribute_text,
			substr(
				$input,
				$processor->get_attribute_start(),
				$processor->get_attribute_length()
			)
		);
		$this->assertSame( 'żółw', $processor->get_attribute_value() );

		$this->assertTrue( $processor->next_token() );
		$this->assertSame( "\r\n", $processor->get_token_text() );
		$this->assertSame( strlen( $prefix . $shortcode ), $processor->get_token_start() );
	}

	public function test_attribute_name_prefix_queries_cover_empty_missing_duplicate_and_invalid_states(): void {
		$processor = new ShortcodeProcessor(
			'[tag DATA-One=1 data-two=2 data-one=3 bare][/tag]'
		);

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame(
			array( 'data-one', 'data-two' ),
			$processor->get_attribute_names_with_prefix( 'DATA-' )
		);
		$this->assertSame(
			array( 'data-one', 'data-two' ),
			$processor->get_attribute_names_with_prefix( '' )
		);
		$this->assertSame( array(), $processor->get_attribute_names_with_prefix( 'missing-' ) );
		$this->assertSame( '3', $processor->get_attribute( 'DATA-ONE' ) );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name'    => 'tag',
					'tag_closers' => 'visit',
				)
			)
		);
		$this->assertTrue( $processor->is_tag_closer() );
		$this->assertNull( $processor->get_attribute_names_with_prefix( '' ) );
	}

	public function test_query_filters_are_combined_before_applying_match_offset(): void {
		$processor = new ShortcodeProcessor(
			'[a id=1][/a][[a id=2]][a id=3][b id=4]'
		);

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name'     => 'a',
					'tag_prefix'   => 'a',
					'tag_closers'  => 'skip',
					'escaped'      => false,
					'match_offset' => 2,
				)
			)
		);
		$this->assertSame( '[a id=3]', $processor->get_token_text() );
		$this->assertSame( '3', $processor->get_attribute( 'id' ) );
	}

	public function test_escaped_query_selects_only_complete_escaped_tokens(): void {
		$processor = new ShortcodeProcessor( '[a][[a]][[a][a]]' );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name' => 'a',
					'escaped'  => true,
				)
			)
		);
		$this->assertSame( '[[a]]', $processor->get_token_text() );
		$this->assertTrue( $processor->is_escaped() );
		$this->assertFalse(
			$processor->next_shortcode(
				array(
					'tag_name' => 'a',
					'escaped'  => true,
				)
			)
		);
	}

	public function test_repeated_match_offset_queries_consume_each_skipped_match(): void {
		$processor = new ShortcodeProcessor(
			'[a id=1][a id=2][a id=3][a id=4][a id=5]'
		);
		$query     = array(
			'tag_name'     => 'a',
			'match_offset' => 2,
		);

		$this->assertTrue( $processor->next_shortcode( $query ) );
		$this->assertSame( '2', $processor->get_attribute( 'id' ) );
		$this->assertTrue( $processor->next_shortcode( $query ) );
		$this->assertSame( '4', $processor->get_attribute( 'id' ) );
		$this->assertFalse( $processor->next_shortcode( $query ) );
	}

	public function test_nonmatching_query_consumes_the_stream(): void {
		$processor = new ShortcodeProcessor( '[a][b]' );

		$this->assertFalse( $processor->next_shortcode( 'missing' ) );
		$this->assertFalse( $processor->next_shortcode( 'a' ) );
		$this->assertNull( $processor->get_token_type() );
	}

	public function test_invalid_query_type_applies_no_filter(): void {
		$processor = new ShortcodeProcessor( '[a]' );

		$this->assertTrue( $processor->next_shortcode( 123 ) );
		$this->assertSame( 'a', $processor->get_tag() );
	}

	/**
	 * @dataProvider match_offset_coercion_provider
	 */
	public function test_match_offset_coercion_is_explicit( $match_offset, string $expected_id ): void {
		$processor = new ShortcodeProcessor( '[a id=1][a id=2][a id=3]' );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_name'     => 'a',
					'match_offset' => $match_offset,
				)
			)
		);
		$this->assertSame( $expected_id, $processor->get_attribute( 'id' ) );
	}

	public static function match_offset_coercion_provider(): array {
		return array(
			'zero falls back to first'     => array( 0, '1' ),
			'negative falls back to first' => array( -2, '1' ),
			'numeric string is cast'       => array( '2', '2' ),
			'float is truncated'            => array( 2.9, '2' ),
		);
	}

	public function test_tag_queries_are_case_sensitive_and_name_prefix_constraints_intersect(): void {
		$processor = new ShortcodeProcessor( '[A][a][alpha]' );

		$this->assertTrue( $processor->next_shortcode( 'a' ) );
		$this->assertSame( '[a]', $processor->get_token_text() );

		$processor = new ShortcodeProcessor( '[a][alpha]' );
		$this->assertFalse(
			$processor->next_shortcode(
				array(
					'tag_name'   => 'a',
					'tag_prefix' => 'b',
				)
			)
		);
	}

	public function test_registry_dependent_period_and_colon_boundaries_use_the_longest_practical_name(): void {
		$processor = new ShortcodeProcessor( '[foo.bar][foo:bar]' );

		$this->assertFalse( $processor->next_shortcode( 'foo' ) );

		$processor = new ShortcodeProcessor( '[foo.bar][foo:bar]' );
		$this->assertTrue( $processor->next_shortcode( 'foo.bar' ) );
		$this->assertSame( '[foo.bar]', $processor->get_token_text() );
		$this->assertTrue( $processor->next_shortcode( 'foo:bar' ) );
		$this->assertSame( '[foo:bar]', $processor->get_token_text() );
	}

	public function test_unknown_closer_policy_uses_default_visit_behavior(): void {
		$processor = new ShortcodeProcessor( '[/a][a]' );

		$this->assertTrue(
			$processor->next_shortcode(
				array(
					'tag_closers' => 'unknown',
				)
			)
		);
		$this->assertTrue( $processor->is_tag_closer() );
	}

	/**
	 * @dataProvider escaped_token_provider
	 */
	public function test_bracket_runs_have_explicit_token_and_escape_semantics(
		string $input,
		array $expected
	): void {
		$this->assertSame(
			$expected,
			$this->collect_detailed_tokens( new ShortcodeProcessor( $input ) )
		);
	}

	public static function escaped_token_provider(): array {
		return array(
			'complete escaping' => array(
				'[[tag]]',
				array(
					array( '#shortcode', '[[tag]]', 'tag', false, false, true ),
				),
			),
			'triple brackets leave surrounding text' => array(
				'[[[tag]]]',
				array(
					array( '#text', '[', null, false, false, false ),
					array( '#shortcode', '[[tag]]', 'tag', false, false, true ),
					array( '#text', ']', null, false, false, false ),
				),
			),
			'opening bracket doubled only' => array(
				'[[tag]',
				array(
					array( '#shortcode', '[[tag]', 'tag', false, false, false ),
				),
			),
			'closing bracket doubled only' => array(
				'[tag]]',
				array(
					array( '#shortcode', '[tag]]', 'tag', false, false, false ),
				),
			),
			'escaped self-closing token' => array(
				'[[tag /]]',
				array(
					array( '#shortcode', '[[tag /]]', 'tag', false, true, true ),
				),
			),
			'escaped closing token' => array(
				'[[/tag]]',
				array(
					array( '#shortcode', '[[/tag]]', 'tag', true, false, true ),
				),
			),
		);
	}

	public function test_core_style_escaped_enclosing_shortcode_is_not_a_complete_escaped_token(): void {
		$input     = '[[outer url=old]content[/outer]]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertSame(
			array(
				array( '#shortcode', '[[outer url=old]', 'outer', false, false, false ),
				array( '#text', 'content', null, false, false, false ),
				array( '#shortcode', '[/outer]]', 'outer', true, false, false ),
			),
			$this->collect_detailed_tokens( $processor )
		);

		$escaped = new ShortcodeProcessor( $input );
		$this->assertFalse(
			$escaped->next_shortcode(
				array(
					'escaped' => true,
				)
			)
		);

		$active = new ShortcodeProcessor( $input );
		$this->assertTrue(
			$active->next_shortcode(
				array(
					'escaped' => false,
				)
			)
		);
		$this->assertSame( '[[outer url=old]', $active->get_token_text() );
	}

	/**
	 * @dataProvider practical_tag_name_provider
	 */
	public function test_practical_tag_name_subset_is_explicit(
		string $input,
		?string $expected_tag
	): void {
		$processor = new ShortcodeProcessor( $input );

		if ( null === $expected_tag ) {
			$this->assertFalse( $processor->next_shortcode() );
			$this->assertSame( $input, $processor->get_updated_text() );
			return;
		}

		$this->assertTrue( $processor->next_shortcode() );
		$this->assertSame( $expected_tag, $processor->get_tag() );
	}

	public static function practical_tag_name_provider(): array {
		return array(
			'plain ASCII'                     => array( '[plain]', 'plain' ),
			'numeric'                         => array( '[123]', '123' ),
			'colon'                           => array( '[namespace:tag]', 'namespace:tag' ),
			'period'                          => array( '[tag.name]', 'tag.name' ),
			'non-ASCII'                       => array( '[żółw]', 'żółw' ),
			'hyphen'                          => array( '[contact-form-7]', 'contact-form-7' ),
			'Core-valid exclamation rejected' => array( '[good!]', null ),
			'Core-valid punctuation rejected' => array(
				'[unreserved!#$%()*+,-.;?@^_{|}~chars]',
				null,
			),
			'legacy equals syntax rejected'   => array(
				'[tag=https://wordpress.org/]',
				null,
			),
		);
	}

	/**
	 * @dataProvider self_closing_syntax_provider
	 */
	public function test_self_closing_syntax_matches_the_immediate_solidus_rule(
		string $input,
		bool $expected_self_closing,
		array $expected_positional_attributes
	): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( $expected_self_closing, $processor->has_self_closing_flag() );

		$actual = array();
		while ( $processor->next_attribute() ) {
			if ( null === $processor->get_attribute_name() ) {
				$actual[] = $processor->get_attribute_value();
			}
		}
		$this->assertSame( $expected_positional_attributes, $actual );
	}

	public static function self_closing_syntax_provider(): array {
		return array(
			'no whitespace' => array( '[tag/]', true, array() ),
			'whitespace before solidus' => array( '[tag /]', true, array() ),
			'whitespace after solidus' => array( '[tag / ]', false, array( '/' ) ),
			'newline after solidus' => array( "[tag /\n]", false, array( '/' ) ),
			'non-breaking space after solidus' => array(
				"[tag /\u{00A0}]",
				false,
				array( '/' ),
			),
			'one positional solidus plus flag' => array( '[tag //]', true, array( '/' ) ),
			'solidus inside positional value' => array(
				'[tag /path]',
				false,
				array( '/path' ),
			),
		);
	}

	public function test_closing_tokens_tolerate_whitespace_but_reject_attributes(): void {
		$processor = new ShortcodeProcessor( "[/tag ][/tag\n][/tag attr=bad]" );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( '[/tag ]', $processor->get_token_text() );
		$this->assertTrue( $processor->is_tag_closer() );
		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( "[/tag\n]", $processor->get_token_text() );
		$this->assertTrue( $processor->is_tag_closer() );
		$this->assertFalse( $processor->next_shortcode( 'tag' ) );
	}

	public function test_all_shortcode_whitespace_bytes_separate_attributes(): void {
		$input = "[tag a=1 b=2\tc=3\fd=4\re=5\nf=6\vg=7"
			. "\u{00A0}h=8\u{200B}i=9]";
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		foreach ( range( 'a', 'i' ) as $index => $name ) {
			$this->assertSame( (string) ( $index + 1 ), $processor->get_attribute( $name ) );
		}
	}

	/**
	 * @dataProvider tolerant_attribute_contract_provider
	 */
	public function test_tolerant_attribute_contracts_are_explicit(
		string $input,
		array $expected
	): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$actual = array();
		while ( $processor->next_attribute() ) {
			$actual[] = array(
				$processor->get_attribute_name(),
				$processor->get_attribute_value(),
				$processor->get_attribute_quote(),
			);
		}
		$this->assertSame( $expected, $actual );
	}

	public static function tolerant_attribute_contract_provider(): array {
		return array(
			'hyphenated names' => array(
				'[tag -foo=x foo-=y foo--bar=z]',
				array(
					array( '-foo', 'x', null ),
					array( 'foo-', 'y', null ),
					array( 'foo--bar', 'z', null ),
				),
			),
			'zero name and values' => array(
				'[tag 0=x zero=0 0 "0"]',
				array(
					array( '0', 'x', null ),
					array( 'zero', '0', null ),
					array( null, '0', null ),
					array( null, '0', '"' ),
				),
			),
			'punctuation in attribute names' => array(
				'[tag foo.bar=x foo:bar=y @x=z]',
				array(
					array( 'foo.bar', 'x', null ),
					array( 'foo:bar', 'y', null ),
					array( '@x', 'z', null ),
				),
			),
			'adjacent quoted attributes' => array(
				'[tag a="1"b="2"]',
				array(
					array( 'a', '1', '"' ),
					array( 'b', '2', '"' ),
				),
			),
			'named empty value' => array(
				'[tag a=]',
				array(
					array( 'a', '', null ),
				),
			),
			'quoted named and positional empty values' => array(
				'[tag empty="" \'\' ""]',
				array(
					array( 'empty', '', '"' ),
					array( null, '', "'" ),
					array( null, '', '"' ),
				),
			),
		);
	}

	public function test_backslash_quoted_values_are_returned_without_decoding(): void {
		$input     = "[tag value=\"one\\\"two\\\\three\" other=3]";
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( "one\\\"two\\\\three", $processor->get_attribute( 'value' ) );
		$this->assertSame( '3', $processor->get_attribute( 'other' ) );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public function test_single_quoted_backslash_value_is_returned_without_decoding(): void {
		$input     = "[tag value='one\\'two\\\\three' other=3]";
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( "one\\'two\\\\three", $processor->get_attribute( 'value' ) );
		$this->assertSame( '3', $processor->get_attribute( 'other' ) );
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public function test_malformed_candidate_before_valid_shortcode_remains_in_text(): void {
		$input     = 'before [bad?] middle [good] after';
		$processor = new ShortcodeProcessor( $input );

		$this->assertSame(
			array(
				array( '#text', 'before [bad?] middle ', null, false, false, false ),
				array( '#shortcode', '[good]', 'good', false, false, false ),
				array( '#text', ' after', null, false, false, false ),
			),
			$this->collect_detailed_tokens( $processor )
		);
	}

	public function test_unclosed_quote_before_valid_shortcode_does_not_hide_the_later_token(): void {
		$input     = 'before [bad value="unterminated] middle [good] after';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'good' ) );
		$this->assertSame( '[good]', $processor->get_token_text() );
	}

	public function test_candidate_inside_failed_matched_quote_region_is_recovered(): void {
		$input     = 'before [bad value="[good]"';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'good' ) );
		$this->assertSame( '[good]', $processor->get_token_text() );
	}

	public function test_mismatched_unbalanced_and_raw_content_tokens_remain_independent(): void {
		$processor = new ShortcodeProcessor(
			'[a][b][/a][/c][raw]const flags = [true];[/raw]'
		);
		$actual    = array();

		while ( $processor->next_shortcode() ) {
			$actual[] = ( $processor->is_tag_closer() ? '/' : '' ) . $processor->get_tag();
		}

		$this->assertSame(
			array( 'a', 'b', '/a', '/c', 'raw', 'true', '/raw' ),
			$actual
		);
	}

	/**
	 * @dataProvider context_ambiguity_provider
	 */
	public function test_context_free_bracket_ambiguities_are_explicit(
		string $input,
		array $expected_tags
	): void {
		$processor = new ShortcodeProcessor( $input );
		$actual    = array();

		while ( $processor->next_shortcode() ) {
			$actual[] = ( $processor->is_tag_closer() ? '/' : '' ) . $processor->get_tag();
		}
		$this->assertSame( $expected_tags, $actual );

		$builder_filtered = new ShortcodeProcessor( $input );
		$this->assertFalse(
			$builder_filtered->next_shortcode(
				array(
					'tag_prefix' => 'et_pb_',
				)
			)
		);
		$this->assertSame( $input, $builder_filtered->get_updated_text() );
	}

	public static function context_ambiguity_provider(): array {
		return array(
			'CSS selector' => array(
				'.x[hidden] { color: red; }',
				array( 'hidden' ),
			),
			'CSS string' => array(
				'.x::before { content: "[gallery]"; }',
				array( 'gallery' ),
			),
			'JSON arrays and string' => array(
				'{"flags":[true],"count":[1],"label":"[gallery]"}',
				array( 'true', '1', 'gallery' ),
			),
			'JavaScript array' => array(
				'<script>const flags = [true];</script>',
				array( 'true' ),
			),
			'conditional HTML comment' => array(
				'<!--[if lt IE 9]><script></script><![endif]-->',
				array( 'if', 'endif' ),
			),
			'Markdown link' => array(
				'[label](https://example.com)',
				array( 'label' ),
			),
			'regular expression character class' => array(
				'/[a-z]/',
				array( 'a-z' ),
			),
			'serialized PHP string' => array(
				'a:1:{s:4:"text";s:9:"[gallery]";}',
				array( 'gallery' ),
			),
			'Gutenberg block JSON' => array(
				'<!-- wp:x {"label":"[gallery]"} /-->',
				array( 'gallery' ),
			),
			'HTML attribute' => array(
				'<a href="[url]">Link</a>',
				array( 'url' ),
			),
		);
	}

	/**
	 * @dataProvider builder_attribute_provider
	 */
	public function test_builder_attributes_can_be_rewritten_with_unequal_length_values(
		string $input,
		string $tag,
		string $attribute_name,
		string $old_value,
		string $new_value
	): void {
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( $tag ) );
		$this->assertSame( $old_value, $processor->get_attribute( $attribute_name ) );
		$this->assertTrue( $this->move_to_attribute( $processor, $attribute_name ) );
		$this->assertTrue( $processor->set_attribute_value( $new_value ) );

		$expected = str_replace( $old_value, $new_value, $input );
		$this->assertSame( $expected, $processor->get_updated_text() );

		$reparsed = new ShortcodeProcessor( $processor->get_updated_text() );
		$this->assertTrue( $reparsed->next_shortcode( $tag ) );
		$this->assertSame( $new_value, $reparsed->get_attribute( $attribute_name ) );
	}

	public static function builder_attribute_provider(): array {
		$old_url = 'https://old.example/a.jpg';
		$new_url = 'https://new-and-longer.example/assets/a.jpg';

		$divi_old = '.x[data-state="open"] { background:url(' . $old_url . '); }';
		$divi_new = str_replace( $old_url, $new_url, $divi_old );

		$wpbakery_old = ".x[data-state='open'] { background:url(" . $old_url . '); }';
		$wpbakery_new = str_replace( $old_url, $new_url, $wpbakery_old );

		$oxygen_old = '{"selector":"section-1","background-image":"' . $old_url . '"}';
		$oxygen_new = str_replace( $old_url, $new_url, $oxygen_old );

		return array(
			'Divi CSS attribute' => array(
				"[et_pb_section custom_css_main_element='" . $divi_old . "']",
				'et_pb_section',
				'custom_css_main_element',
				$divi_old,
				$divi_new,
			),
			'WPBakery CSS attribute' => array(
				'[vc_row css="' . $wpbakery_old . '"]',
				'vc_row',
				'css',
				$wpbakery_old,
				$wpbakery_new,
			),
			'Oxygen JSON attribute' => array(
				"[ct_section ct_options='" . $oxygen_old . "']",
				'ct_section',
				'ct_options',
				$oxygen_old,
				$oxygen_new,
			),
			'Avada URL attribute' => array(
				'[fusion_builder_container background_image="' . $old_url . '"]',
				'fusion_builder_container',
				'background_image',
				$old_url,
				$new_url,
			),
			'Themify URL attribute' => array(
				'[themify_button link="' . $old_url . '?x=1&y=2"]',
				'themify_button',
				'link',
				$old_url . '?x=1&y=2',
				$new_url . '?x=1&y=2',
			),
		);
	}

	/**
	 * @dataProvider token_stream_invariant_provider
	 */
	public function test_token_stream_spans_cover_every_original_byte( string $input ): void {
		$processor = new ShortcodeProcessor( $input );
		$at        = 0;
		$rebuilt   = '';

		while ( $processor->next_token() ) {
			$this->assertSame( $at, $processor->get_token_start() );
			$this->assertGreaterThan( 0, $processor->get_token_length() );
			$this->assertSame(
				substr( $input, $processor->get_token_start(), $processor->get_token_length() ),
				$processor->get_token_text()
			);
			$rebuilt .= $processor->get_token_text();
			$at      += $processor->get_token_length();
		}

		$this->assertSame( strlen( $input ), $at );
		$this->assertSame( $input, $rebuilt );
		$this->assertSame( $input, $processor->get_updated_text() );
		$this->assertNull( $processor->get_token_type() );
	}

	public static function token_stream_invariant_provider(): array {
		return array(
			'empty' => array( '' ),
			'plain text' => array( 'plain text' ),
			'mixed valid tokens' => array( 'α[tag a="żółw"]MID[/tag]ω' ),
			'malformed before valid' => array( '[bad?] before [good] after' ),
			'escaped and partial escaped' => array( '[[tag]][[tag][tag]]' ),
			'CRLF and Unicode whitespace' => array(
				"before\r\n[tag\u{00A0}a=1\u{200B}b=2]\r\nafter",
			),
			'binary bytes' => array( "\xFFbefore[tag value=\"\xFE\"]after\x00" ),
			'context ambiguity' => array( '{"flags":[true],"label":"[gallery]"}' ),
		);
	}

	public function test_short_fuzz_corpus_preserves_every_input_byte(): void {
		$alphabet = array( '[', ']', '/', '"', "'", '=', 'a', ' ', '\\', "\0", "\xFF" );

		foreach ( $alphabet as $first ) {
			foreach ( $alphabet as $second ) {
				foreach ( $alphabet as $third ) {
					$input     = $first . $second . $third;
					$processor = new ShortcodeProcessor( $input );
					$at        = 0;
					$rebuilt   = '';

					while ( $processor->next_token() ) {
						$this->assertSame( $at, $processor->get_token_start() );
						$rebuilt .= $processor->get_token_text();
						$at      += $processor->get_token_length();
					}

					$this->assertSame( strlen( $input ), $at );
					$this->assertSame( $input, $rebuilt );
					$this->assertSame( $input, $processor->get_updated_text() );
				}
			}
		}
	}

	public function test_large_quoted_attribute_with_many_brackets_is_one_linear_token(): void {
		$value     = str_repeat( '[not-a-token]', 5000 );
		$input     = '[tag value="' . $value . '"]';
		$processor = new ShortcodeProcessor( $input );

		$this->assertTrue( $processor->next_shortcode( 'tag' ) );
		$this->assertSame( $input, $processor->get_token_text() );
		$this->assertSame( $value, $processor->get_attribute( 'value' ) );
		$this->assertFalse( $processor->next_shortcode() );
	}

	/**
	 * @dataProvider malformed_candidate_storm_provider
	 */
	public function test_malformed_candidate_storm_does_not_rescan_the_remaining_suffix(
		string $input
	): void {
		$started   = microtime( true );
		$processor = new ShortcodeProcessor( $input );

		$this->assertFalse( $processor->next_shortcode() );
		$elapsed = microtime( true ) - $started;

		$this->assertLessThan(
			2.0,
			$elapsed,
			'Malformed candidates should be processed in approximately linear time.'
		);
		$this->assertSame( $input, $processor->get_updated_text() );
	}

	public static function malformed_candidate_storm_provider(): array {
		$prefix = str_repeat( '[a ', 12000 );

		return array(
			'no closing bracket' => array( $prefix ),
			'closing bracket inside an unmatched quote' => array( $prefix . '"]' ),
			'closing bracket inside a matched quoted region' => array( $prefix . '"]"' ),
		);
	}

	private function move_to_attribute(
		ShortcodeProcessor $processor,
		string $attribute_name
	): bool {
		while ( $processor->next_attribute() ) {
			if ( $attribute_name === $processor->get_attribute_name() ) {
				return true;
			}
		}

		return false;
	}

	private function collect_detailed_tokens( ShortcodeProcessor $processor ): array {
		$tokens = array();

		while ( $processor->next_token() ) {
			$tokens[] = array(
				$processor->get_token_type(),
				$processor->get_token_text(),
				$processor->get_tag(),
				$processor->is_tag_closer(),
				$processor->has_self_closing_flag(),
				$processor->is_escaped(),
			);
		}

		return $tokens;
	}
	private function collect_tokens( ShortcodeProcessor $processor ): array {
		$tokens = array();

		while ( $processor->next_token() ) {
			$tokens[] = array(
				$processor->get_token_type(),
				$processor->get_token_text(),
				$processor->get_tag(),
				$processor->is_tag_closer(),
				$processor->is_escaped(),
			);
		}

		return $tokens;
	}
}
