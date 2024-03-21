<?php

namespace unit\blueprint;

use ArrayObject;
use PHPUnitTestCase;
use WordPress\Blueprints\Model\DataClass\MkdirStep;
use WordPress\Blueprints\Model\DataClass\RmStep;
use WordPress\Blueprints\BlueprintMapper;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\UrlResource;
use WordPress\JsonMapper\JsonMapperException;

class BlueprintMapperTest extends PHPUnitTestCase {

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
		$raw_blueprint = '{}';

		$parsed_json = json_decode($raw_blueprint, false);

		$result = $this->blueprint_mapper->map($parsed_json);

		$expected = new Blueprint();

		$this->assertEquals($expected, $result);
	}

	public function testMapsWordPressVersion() {
		$raw_blueprint =
			'{
				"WordPressVersion":"https://wordpress.org/latest.zip"
			}';

		$parsed_json = json_decode($raw_blueprint, false);

		$result = $this->blueprint_mapper->map($parsed_json);

		$expected = new Blueprint();
		$expected->WordPressVersion = 'https://wordpress.org/latest.zip';

		$this->assertEquals($expected, $result);
	}

	public function testMapsMultiplePlugins() {
		$raw_blueprint =
			'{
				"plugins":
					[
						"https://downloads.wordpress.org/plugin/wordpress-importer.zip",
						"https://downloads.wordpress.org/plugin/hello-dolly.zip",
						"https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip"
					]
			}';

		$parsed_json = json_decode($raw_blueprint, false);

		$result = $this->blueprint_mapper->map($parsed_json);

		$expected = new Blueprint();
		$expected->plugins = [
			'https://downloads.wordpress.org/plugin/wordpress-importer.zip',
			'https://downloads.wordpress.org/plugin/hello-dolly.zip',
			'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip',
		];

		$this->assertEquals($expected, $result);
	}

	public function testMapsPluginsWithDifferentDataTypes() {
		$raw_blueprint =
			'{
				"plugins": [
					"https://downloads.wordpress.org/plugin/wordpress-importer.zip",
					{ "resource": "url", "url": "https://mysite.com" }
				]
			}';

		$parsed_json = json_decode($raw_blueprint, false);

		$result = $this->blueprint_mapper->map($parsed_json);

		$expected = new Blueprint();
		$expected->plugins = [
			'https://downloads.wordpress.org/plugin/wordpress-importer.zip',
			(new UrlResource())->setUrl('https://mysite.com'),
		];

		$this->assertEquals($expected, $result);
	}

	public function testFailsWhenPluginsWithInvalidDataTypes() {
		$raw_blueprint =
			'{
				"plugins": [
					"https://downloads.wordpress.org/plugin/wordpress-importer.zip",
					123
        		]
			}';

		$parsed_json = json_decode( $raw_blueprint, false );

		$this->expectException( JsonMapperException::class );
		$this->blueprint_mapper->map( $parsed_json );
	}

	public function testMapsWhenSpecificStepAppearsTwice() {
		$raw_blueprint =
			'{
				"steps":
					[
						{"step":"mkdir","path":"dir1"},
						{"step":"rm","path":"dir1"},
						{"step":"mkdir","path":"dir2"}
					]
			}';

		$parsed_json = json_decode( $raw_blueprint, false );

		$result = $this->blueprint_mapper->map( $parsed_json );

		$expected        = new Blueprint();
		$expected->steps = array(
			0 => ( new MkdirStep() )->setPath( 'dir1' ),
			1 => ( new RmStep() )->setPath( 'dir1' ),
			2 => ( new MkdirStep() )->setPath( 'dir2' ),
		);

		$this->assertEquals( $expected, $result );
	}

	public function testMapsWpConfigConstants() {
		$raw_blueprint =
			'{
				"constants": {
        			"WP_DEBUG": true,
					"WP_DEBUG_LOG": true,
					"WP_DEBUG_DISPLAY": true,
					"WP_CACHE": true
      			}
			}';

		$parsed_json = json_decode( $raw_blueprint, false );

		$result = $this->blueprint_mapper->map( $parsed_json );

		$expected            = new Blueprint();
		$expected->constants = new ArrayObject(
			array(
				'WP_DEBUG'         => true,
				'WP_DEBUG_LOG'     => true,
				'WP_DEBUG_DISPLAY' => true,
				'WP_CACHE'         => true,
			)
		);

		$this->assertEquals( $expected, $result );
	}
	public function testMapsSiteOptions() {
		$raw_blueprint =
			'{
				"siteOptions": {
					"blogname": "My Blog",
                	"blogdescription": "A great blog"
      			}
			}';

		$parsed_json = json_decode( $raw_blueprint, false );

		$result = $this->blueprint_mapper->map( $parsed_json );

		$expected              = new Blueprint();
		$expected->siteOptions = new \ArrayObject(
			array(
				'blogname'        => 'My Blog',
				'blogdescription' => 'A great blog',
			)
		);

		$this->assertEquals( $expected, $result );
	}
}
