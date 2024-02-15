<?php

namespace WordPress\Blueprints\Dependency;

class StepMeta {

	public function __construct(
		public readonly string $slug,
		public readonly string $class,
		public readonly string $inputClass,
	) {
	}

}
