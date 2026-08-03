<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\BlockMarkup\BlockMarkupUrlProcessor;
use WordPress\DataLiberation\URL\WPURL;

class BlockMarkupUrlProcessorShortcodeTest extends TestCase {

	/**
	 * @dataProvider shortcode_css_url_provider
	 */
	public function test_rewrites_css_urls_in_shortcode_attributes_without_html_encoding(
		string $input,
		string $expected,
		int $expected_url_count
	): void {
		$processor = new BlockMarkupUrlProcessor( $input, 'https://old.example/' );

		$this->assertSame( $expected_url_count, $this->replace_old_base_url( $processor ) );
		$updated = $processor->get_updated_html();

		$this->assertSame( $expected, $updated );
		$this->assertStringNotContainsString( '&amp;', $updated );
		$this->assertStringNotContainsString( '&quot;', $updated );
		$this->assertStringNotContainsString( '&lt;', $updated );
		$this->assertStringNotContainsString( '&apos;', $updated );
	}

	public static function shortcode_css_url_provider(): array {
		return array(
			'Divi CSS preserves raw text bytes while updating multiple URLs' => array(
				<<<'HTML'
<!-- wp:shortcode -->
[et_pb_section custom_css_main_element='.hero::before { content: "<&>"; background: url(https://old.example/media/hero.jpg?width=1200&height=800) no-repeat; mask: url("https://old.example/media/hero.jpg?width=1200&height=800"); }']
[/et_pb_section]
<!-- /wp:shortcode -->
HTML
				,
				<<<'HTML'
<!-- wp:shortcode -->
[et_pb_section custom_css_main_element='.hero::before { content: "<&>"; background: url("https://new.example/media/hero.jpg?width=1200&height=800") no-repeat; mask: url("https://new.example/media/hero.jpg?width=1200&height=800"); }']
[/et_pb_section]
<!-- /wp:shortcode -->
HTML
				,
				2,
			),
			'WPBakery CSS switches delimiters instead of encoding quotes' => array(
				<<<'HTML'
<!-- wp:shortcode -->
[vc_row css=".vc_custom_1 { background-image: url(https://old.example/media/hero.jpg?width=1200&height=800); }"]
[/vc_row]
<!-- /wp:shortcode -->
HTML
				,
				<<<'HTML'
<!-- wp:shortcode -->
[vc_row css='.vc_custom_1 { background-image: url("https://new.example/media/hero.jpg?width=1200&height=800"); }']
[/vc_row]
<!-- /wp:shortcode -->
HTML
				,
				1,
			),
		);
	}

	public function test_rewrites_block_html_and_shortcode_urls_in_one_pass(): void {
		$input = <<<'HTML'
<!-- wp:image {"url":"https://old.example/block.jpg"} -->
<figure><img src="https://old.example/block.jpg"></figure>
<!-- /wp:image -->
<!-- wp:shortcode -->
[et_pb_image src="https://old.example/shortcode.jpg?width=800&height=600"]
<!-- /wp:shortcode -->
HTML;
		$processor = new BlockMarkupUrlProcessor( $input, 'https://old.example/' );

		$this->assertSame( 3, $this->replace_old_base_url( $processor ) );
		$this->assertSame(
			<<<'HTML'
<!-- wp:image {"url":"https:\/\/new.example\/block.jpg"} -->
<figure><img src="https://new.example/block.jpg"></figure>
<!-- /wp:image -->
<!-- wp:shortcode -->
[et_pb_image src="https://new.example/shortcode.jpg?width=800&height=600"]
<!-- /wp:shortcode -->
HTML
			,
			$processor->get_updated_html()
		);
	}

	public function test_rewrites_direct_shortcode_urls_from_multiple_site_builders(): void {
		$input = <<<'HTML'
[fusion_builder_container background_image="https://old.example/avada.jpg?width=1600&quality=80"]
[vc_video link="https://old.example/wpbakery.mp4?autoplay=1&muted=1"][/vc_video]
[themify_button link="https://old.example/themify?iframe=true&width=100%"]Button[/themify_button]
[x_image href="https://old.example/cornerstone?slide=1&from=builder"]
HTML;
		$processor = new BlockMarkupUrlProcessor( $input, 'https://old.example/' );

		$this->assertSame( 4, $this->replace_old_base_url( $processor ) );
		$this->assertSame(
			<<<'HTML'
[fusion_builder_container background_image="https://new.example/avada.jpg?width=1600&quality=80"]
[vc_video link="https://new.example/wpbakery.mp4?autoplay=1&muted=1"][/vc_video]
[themify_button link="https://new.example/themify?iframe=true&width=100%"]Button[/themify_button]
[x_image href="https://new.example/cornerstone?slide=1&from=builder"]
HTML
			,
			$processor->get_updated_html()
		);
	}

	public function test_preserves_text_node_span_across_incremental_shortcode_updates(): void {
		$input = <<<'HTML'
[vc_row css="
	background: url(https://old.example/first.jpg?x=1&y=2);
	mask: url(https://old.example/second.svg?x=3&y=4);
"]
HTML;
		$processor    = new BlockMarkupUrlProcessor( $input, 'https://old.example/' );
		$new_base_url = WPURL::parse( 'https://new-and-longer.example/migrated/' );

		$this->assertTrue( $processor->next_url() );
		$this->assertSame( 'https://old.example/first.jpg?x=1&y=2', $processor->get_raw_url() );
		$this->assertTrue( $processor->replace_base_url( $new_base_url ) );
		$this->assertSame(
			<<<'HTML'
[vc_row css='
	background: url("https://new-and-longer.example/migrated/first.jpg?x=1&y=2");
	mask: url(https://old.example/second.svg?x=3&y=4);
']
HTML
			,
			$processor->get_updated_html()
		);

		$this->assertTrue( $processor->next_url() );
		$this->assertSame( 'https://old.example/second.svg?x=3&y=4', $processor->get_raw_url() );
		$this->assertTrue( $processor->replace_base_url( $new_base_url ) );
		$this->assertSame(
			<<<'HTML'
[vc_row css='
	background: url("https://new-and-longer.example/migrated/first.jpg?x=1&y=2");
	mask: url("https://new-and-longer.example/migrated/second.svg?x=3&y=4");
']
HTML
			,
			$processor->get_updated_html()
		);
	}

	public function test_only_interprets_shortcodes_in_html_text_nodes(): void {
		$input = <<<'HTML'
<!-- wp:group {"metadata":{"pattern":"[button url=\"https://old.example/block-json\"]"}} -->
<div data-code="[button url='https://old.example/html-attribute']">
[[button url="https://old.example/escaped"]]
[button url="https://old.example/real?one=1&two=2"]
</div>
<!-- /wp:group -->
HTML;
		$processor = new BlockMarkupUrlProcessor( $input, 'https://old.example/' );

		$this->assertSame( 1, $this->replace_old_base_url( $processor ) );
		$this->assertSame(
			<<<'HTML'
<!-- wp:group {"metadata":{"pattern":"[button url=\"https://old.example/block-json\"]"}} -->
<div data-code="[button url='https://old.example/html-attribute']">
[[button url="https://old.example/escaped"]]
[button url="https://new.example/real?one=1&two=2"]
</div>
<!-- /wp:group -->
HTML
			,
			$processor->get_updated_html()
		);
	}

	private function replace_old_base_url( BlockMarkupUrlProcessor $processor ): int {
		$new_base_url = WPURL::parse( 'https://new.example/' );
		$updated      = 0;

		while ( $processor->next_url() ) {
			$this->assertTrue( $processor->replace_base_url( $new_base_url ) );
			++$updated;
		}

		return $updated;
	}
}
