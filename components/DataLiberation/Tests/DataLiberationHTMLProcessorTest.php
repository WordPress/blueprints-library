<?php

use PHPUnit\Framework\TestCase;
use WordPress\DataLiberation\DataLiberationHTMLProcessor;
use WordPress\DataLiberation\Importer\ImportUtils;

class DataLiberationHTMLProcessorTest extends TestCase {

	public function test_get_inner_html_preserves_processor_position() {
		$processor = DataLiberationHTMLProcessor::create_fragment(
			'<h1>Title</h1><p>Body</p>'
		);

		$this->assertTrue( $processor->next_tag( 'H1' ) );
		$this->assertSame( 'Title', $processor->get_inner_html() );
		$this->assertSame( 'H1', $processor->get_tag() );
		$this->assertTrue( $processor->next_tag( 'P' ) );
		$this->assertSame( 'P', $processor->get_tag() );
	}

	public function test_import_h1_removal_preserves_remaining_markup() {
		$result = ImportUtils::remove_first_h1_block_from_block_markup(
			'<h1>Title</h1><!-- wp:paragraph --><p>Body</p>'
		);

		$this->assertSame(
			array(
				'h1_content'     => 'Title',
				'remaining_html' => '<p>Body</p>',
			),
			$result
		);
	}
}
