<?php

namespace unit\json_mapper;

use ArrayObject;
use Bag;
use Item;
use PHPUnitTestCase;
use TestResourceClassComplexMapping;
use TestResourceClassSetValue;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\JsonMapperException;

class JsonMapperTest extends PHPUnitTestCase {

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

	public function testCustomFactory() {
		$mapper = new JsonMapper( array(
			Item::class => function ( $json ) {
				$item = new Item();
				$item->name = $json->name;

				return $item;
			},
		) );

		$result = $mapper->hydrate(
			json_decode( '{"name":"test","items":[{"name":"test"}]}' ),
			Bag::class
		);

		$expected = new Bag();
		$expected->name = 'test';
		$expected->items = [ new Item() ];
		$expected->items[0]->name = 'test';

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test checks if mapper works at all.
	 *
	 * @return void
	 */
	public function testMapsToArrayObject() {
		$raw_json = '{}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, ArrayObject::class );

		$expected = new ArrayObject();

		$this->assertEquals( $expected, $result );
	}

	public function testSetsPublicProperties() {
		$raw_json = '{"publicProperty":"test"}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassSetValue::class );

		$expected = new TestResourceClassSetValue();
		$expected->publicProperty = 'test';

		$this->assertEquals( $expected, $result );
	}

	public function testSetsPrivatePropertiesWithSetter() {
		$raw_json = '{"privateProperty":"test"}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassSetValue::class );

		$expected = new TestResourceClassSetValue();
		$expected->setPrivateProperty( 'test' );

		$this->assertEquals( $expected, $result );
	}

	public function testSetsProtectedPropertiesWithSetter() {
		$raw_json = '{"protectedProperty":"test"}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassSetValue::class );

		$expected = new TestResourceClassSetValue();
		$expected->setProtectedProperty( 'test' );

		$this->assertEquals( $expected, $result );
	}

	public function testFailsSettingPrivatePropertyWithNoSetter() {
		$raw_json = '{"setterlessPrivateProperty":"test"}';

		$parsed_json = json_decode( $raw_json );

		$this->expectException( JsonMapperException::class );
		$this->expectExceptionMessage( "Property: 'TestResourceClassSetValue::setterlessPrivateProperty' is non-public and no setter method was found." );
		$this->json_mapper->hydrate( $parsed_json, TestResourceClassSetValue::class );
	}

	public function testFailsSettingProtectedPropertyWithNoSetter() {
		$raw_json = '{"setterlessProtectedProperty":"test"}';

		$parsed_json = json_decode( $raw_json );

		$this->expectException( JsonMapperException::class );
		$this->expectExceptionMessage( "Property: 'TestResourceClassSetValue::setterlessProtectedProperty' is non-public and no setter method was found." );
		$this->json_mapper->hydrate( $parsed_json, TestResourceClassSetValue::class );
	}

	public function testMapsToDeepScalarArray() {
		$result = $this->json_mapper->hydrate(
			json_decode( '{"arrayOfStringArrays":[["test1","test2"],["test3","test4"]]}' ),
			TestResourceClassComplexMapping::class
		);

		$expected = new TestResourceClassComplexMapping();
		$expected->arrayOfStringArrays = array(
			array( 'test1', 'test2' ),
			array( 'test3', 'test4' ),
		);

		$this->assertEquals( $expected, $result );
	}

	public function testMapsToDeepMixedArray() {
		$raw_json = '{"arrayOfMixedArrays":[["test1", 42],["test3", true]]}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassComplexMapping::class );

		$expected = new TestResourceClassComplexMapping();
		$expected->arrayOfMixedArrays = array(
			array( 'test1', 42 ),
			array( 'test3', true ),
		);

		$this->assertEquals( $expected, $result );
	}

	public function testFailsWhenArrayWrongScalarType() {
		$raw_json = '{"arrayOfStringArrays":[["test1", 42],["test3", true]]}';

		$parsed_json = json_decode( $raw_json );

		$this->expectException( JsonMapperException::class );
		$this->expectExceptionMessage( "Unable to map [[\"test1\",42],[\"test3\",true]] to 'arrayOfStringArrays'." );
		$this->json_mapper->hydrate( $parsed_json, TestResourceClassComplexMapping::class );
	}

	public function testFailsWhenWrongScalarType() {
		$raw_json = '{"string":42}';

		$parsed_json = json_decode( $raw_json );

		$this->expectException( JsonMapperException::class );
		$this->expectExceptionMessage( "Unable to map 42 to 'string'." );
		$this->json_mapper->hydrate( $parsed_json, TestResourceClassComplexMapping::class );
	}

	public function testMapsToArrayOfArrays() {
		$raw_json = '{"arrayOfArrays":[["test1", 42],["test3", true]]}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassComplexMapping::class );

		$expected = new TestResourceClassComplexMapping();
		$expected->arrayOfArrays = array(
			array( 'test1', 42 ),
			array( 'test3', true ),
		);

		$this->assertEquals( $expected, $result );
	}

	public function testMapsToArray() {
		$raw_json = '{"array":["test1","test2"]}';

		$parsed_json = json_decode( $raw_json );

		$result = $this->json_mapper->hydrate( $parsed_json, TestResourceClassComplexMapping::class );

		$expected = new TestResourceClassComplexMapping();
		$expected->array = array( 'test1', 'test2' );

		$this->assertEquals( $expected, $result );
	}
}
