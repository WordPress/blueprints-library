<?php

namespace WordPress\JsonMapper\Property;

use WordPress\JsonMapper\ObjectWrapper;

interface PropertyMapperInterface {
	public function map_properties( ObjectWrapper $object_wrapper, PropertyMap $property_map );
}