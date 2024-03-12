<?php

namespace JsonMapper;

use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\JsonMapper\JsonMapper;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase {

	/**
	 * @var JsonMapper
	 */
	private $json_mapper;

	/**
	 * @before
	 */
	public function before() {
		$this->json_mapper = new JsonMapper();
	}

	public function testMapsEmptyBlueprint() {
		$raw_json = '{}';

		$parsed_json = json_decode( $raw_json, false );

		$blueprint = $this->json_mapper->hydrate( $parsed_json, Blueprint::class );

		$expected = BlueprintBuilder::create()
			->toBlueprint();

		$this->assertEquals( $expected, $blueprint );
	}
}
