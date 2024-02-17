<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Resource\Resource;

abstract class BaseStepInput {

	static public function getResources() {
		//     $selfProps = get_class_vars(static::class);
		//     $resources = [];
		//     foreach(get_object_vars($this) as $key => $value) {
		//         if($value instanceof Resource) {
		//             $resources[$key] = $value;
		//         }
		//     }
		//     return $resources;
	}

}
