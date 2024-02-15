<?php

namespace WordPress\Blueprints\Dependency;

class ResourceMeta {

	public function __construct(
		public readonly string $slug,
		public readonly string $dataClass,
	) {
	}

}
