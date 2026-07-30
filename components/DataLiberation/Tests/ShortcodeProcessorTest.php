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
