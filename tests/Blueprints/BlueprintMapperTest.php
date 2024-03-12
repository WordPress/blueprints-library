<?php

namespace Blueprints;

use WordPress\Blueprints\BlueprintMapper;
use PHPUnit\Framework\TestCase;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\JsonMapper\JsonMapper;

class BlueprintMapperTest extends TestCase {

	/**
	 * @var BlueprintMapper
	 */
	private $blueprint_mapper;

	/**
	 * @before
	 */
	public function before() {
		$this->blueprint_mapper = new BlueprintMapper();
	}

	public function testMapsEmptyBlueprint() {
		$raw_json = '{}';

		$parsed_json = json_decode( $raw_json, false );

		$blueprint = $this->blueprint_mapper->map( $parsed_json );

		$expected = BlueprintBuilder::create()
			->toBlueprint();

		$this->assertEquals( $expected, $blueprint );
	}

	public function testMapsWordPressVersion() {
		$raw_json =
			'{
				"WordPressVersion":"https://wordpress.org/latest.zip"
			}';

		$parsed_json = json_decode( $raw_json, false );

		$blueprint = $this->blueprint_mapper->map( $parsed_json );

		$expected = BlueprintBuilder::create()
			->withWordPressVersion( 'https://wordpress.org/latest.zip' )
			->toBlueprint();

		$this->assertEquals( $expected, $blueprint );
	}

	public function testMapsMultiplePlugins() {
		$raw_json =
			'{
				"plugins":
					[
						"https://downloads.wordpress.org/plugin/wordpress-importer.zip",
						"https://downloads.wordpress.org/plugin/hello-dolly.zip",
						"https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip"
                    ]
			}';

		$parsed_json = json_decode( $raw_json, false );

		$blueprint = $this->blueprint_mapper->map( $parsed_json );

		$expected = BlueprintBuilder::create()
			->withPlugins(
				array(
					'https://downloads.wordpress.org/plugin/wordpress-importer.zip',
					'https://downloads.wordpress.org/plugin/hello-dolly.zip',
					'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip',
				)
			)
			->toBlueprint();

		$this->assertEquals( $expected, $blueprint );
	}

	public function testMapsWhenSpecificStepAppearsTwice() {
		$raw_json =
			'{
				"steps":
					[
						{"step":"mkdir","path":"dir1"},
						{"step":"rm","path":"dir1"},
						{"step":"mkdir","path":"dir2"}
					]
			}';

		$parsed_json = json_decode( $raw_json, false );

		$blueprint = $this->blueprint_mapper->map( $parsed_json );

		$expected = BlueprintBuilder::create()
			->makeDirectory( 'dir1' )
			->remove( 'dir1' )
			->makeDirectory( 'dir2' )
			->toBlueprint();

		$this->assertEquals( $expected, $blueprint );
	}
}
